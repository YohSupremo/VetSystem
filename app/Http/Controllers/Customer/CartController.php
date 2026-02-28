<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\ClinicSetting;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PetOwner;
use App\Models\ShoppingCart;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    private function authenticateUser()
    {
        $username = session('username');
        if (!$username) {
            return redirect('/login')->with('error', 'Please login first');
        }

        $user = User::where('username', $username)->first();
      if (!$user || ($user->role !== 'pet_owner' && $user->role !== 'registered_user')) {
            return redirect('/login')->with('error', 'Access denied');
        }

        return $user;
    }

    private function getUserCart($user): ShoppingCart
    {
        return ShoppingCart::getOrCreateForUser($user->id);
    }

    /**
     * Display the shopping cart.
     */
    public function index(): View|RedirectResponse
    {
        $user = $this->authenticateUser();
        if ($user instanceof RedirectResponse) {
            return $user;
        }

        view()->share('user', $user);

        $cart = $this->getUserCart($user);
        $cart->load(['cartItems.inventoryItem']);

        // Remove items that are no longer available or out of stock
        $unavailableItems = $cart->cartItems->filter(function ($item) {
            return !$item->isAvailable();
        });

        if ($unavailableItems->isNotEmpty()) {
            foreach ($unavailableItems as $item) {
                $item->delete();
            }
            return redirect()->route('customer.cart.index')
                ->with('warning', 'Some items in your cart are no longer available and have been removed.');
        }

        $cartSubtotal = (float) $cart->cartItems->sum('total');
        $defaultTaxRate = ClinicSetting::defaultTaxRate();
        $cartTaxAmount = $cartSubtotal * ($defaultTaxRate / 100);
        $cartGrandTotal = $cartSubtotal + $cartTaxAmount;

        return view('customer.cart.index', compact(
            'cart',
            'defaultTaxRate',
            'cartSubtotal',
            'cartTaxAmount',
            'cartGrandTotal'
        ));
    }

    /**
     * Add an item to the cart.
     */
    public function add(Request $request, $productId): RedirectResponse
    {
        $user = $this->authenticateUser();
        if ($user instanceof RedirectResponse) {
            return $user;
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = InventoryItem::where('quantity', '>', 0)->findOrFail($productId);

        if ($product->requires_prescription) {
            return back()->with('error', 'This product requires a prescription. Please consult your veterinarian.');
        }

        if ($validated['quantity'] > $product->quantity) {
            return back()->with('error', 'Requested quantity exceeds available stock.');
        }

        $cart = $this->getUserCart($user);

        // Check if item already exists in cart
        $existingItem = $cart->cartItems()
            ->where('inventory_item_id', $product->id)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $validated['quantity'];
            
            if ($newQuantity > $product->quantity) {
                return back()->with('error', 'Cannot add more items than available stock.');
            }

            $existingItem->update([
                'quantity' => $newQuantity,
                'unit_price' => $product->unit_price,
            ]);

            return back()->with('success', 'Item quantity updated in cart!');
        }

        // Add new item to cart
        CartItem::create([
            'cart_id' => $cart->id,
            'inventory_item_id' => $product->id,
            'quantity' => $validated['quantity'],
            'unit_price' => $product->unit_price,
        ]);

        return back()->with('success', 'Item added to cart!');
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, $itemId): RedirectResponse
    {
        $user = $this->authenticateUser();
        if ($user instanceof RedirectResponse) {
            return $user;
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getUserCart($user);
        $cartItem = $cart->cartItems()->findOrFail($itemId);

        if (!$cartItem->canUpdateQuantity($validated['quantity'])) {
            return back()->with('error', 'Invalid quantity or insufficient stock.');
        }

        $cartItem->update([
            'quantity' => $validated['quantity'],
            'unit_price' => $cartItem->inventoryItem->unit_price,
        ]);

        return back()->with('success', 'Cart updated!');
    }

    /**
     * Remove an item from the cart.
     */
    public function remove($itemId): RedirectResponse
    {
        $user = $this->authenticateUser();
        if ($user instanceof RedirectResponse) {
            return $user;
        }

        $cart = $this->getUserCart($user);
        $cartItem = $cart->cartItems()->findOrFail($itemId);
        $cartItem->delete();

        return back()->with('success', 'Item removed from cart!');
    }

    /**
     * Clear the entire cart.
     */
    public function clear(): RedirectResponse
    {
        $user = $this->authenticateUser();
        if ($user instanceof RedirectResponse) {
            return $user;
        }

        $cart = $this->getUserCart($user);
        $cart->cartItems()->delete();

        return back()->with('success', 'Cart cleared!');
    }

    /**
     * Checkout - convert cart to order.
     */
    public function checkout(Request $request): RedirectResponse
    {
        $user = $this->authenticateUser();
        if ($user instanceof RedirectResponse) {
            return $user;
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer,mobile_payment',
        ], [
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Please select a valid payment method.',
            'notes.max' => 'Order notes cannot exceed 1000 characters.',
        ]);

        $cart = $this->getUserCart($user);
        $cart->load(['cartItems.inventoryItem']);

        if ($cart->isEmpty()) {
            return back()->with('error', 'Your cart is empty!');
        }

        // Validate stock availability before checkout
        foreach ($cart->cartItems as $cartItem) {
            if (!$cartItem->isAvailable() || $cartItem->quantity > $cartItem->inventoryItem->quantity) {
                return back()->with('error', "Item '{$cartItem->inventoryItem->name}' is no longer available or has insufficient stock.");
            }
        }

        // Ensure pet owner record exists
        $petOwner = PetOwner::firstOrCreate(
            ['user_id' => $user->id],
            ['notes' => null]
        );

        // Determine if this is online payment (not cash)
        $isOnlinePayment = $validated['payment_method'] !== 'cash';

        // Create order
        $order = Order::create([
            'appointment_id' => null,
            'pet_id' => null,
            'owner_id' => $petOwner->id,
            'created_by' => $user->id,
            'order_type' => 'online',
            'status' => 'confirmed',
            'order_date' => now(),
            'notes' => ($validated['notes'] ? $validated['notes'] . "\n" : '') . 'Payment Method: ' . ucfirst(str_replace('_', ' ', $validated['payment_method'])),
        ]);

        $subtotalAmount = 0;

        // Add order items and update stock
        foreach ($cart->cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'inventory_item_id' => $cartItem->inventory_item_id,
                'description' => $cartItem->inventoryItem->name,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->unit_price,
            ]);

            $subtotalAmount += $cartItem->quantity * $cartItem->unit_price;

            // Update inventory stock using the model helper
            $cartItem->inventoryItem->decrementStock($cartItem->quantity);

            // Create transaction record for audit (linking to the first available stock for reference, or generic)
            // Ideally transactions should be created per stock deduction in the decrementStock method, 
            // but for now we log a generic transaction for the order.
            $inventoryStock = \App\Models\InventoryStock::where('item_id', $cartItem->inventory_item_id)
                ->where('quantity', '>', 0)
                ->first();
            
            // Fallback to any stock if none has quantity (just for logging)
            if (!$inventoryStock) {
                $inventoryStock = \App\Models\InventoryStock::where('item_id', $cartItem->inventory_item_id)->first();
            }

            if ($inventoryStock) {
                \App\Models\InventoryTransaction::create([
                    'stock_id' => $inventoryStock->id,
                    'type' => 'out',
                    'quantity' => $cartItem->quantity,
                    'reference' => 'Order #' . $order->id,
                    'notes' => 'Sold to ' . $user->first_name . ' ' . $user->last_name,
                ]);
            }
        }

        // Generate invoice number
        $prefix = ClinicSetting::invoicePrefix();
        $defaultTaxRate = ClinicSetting::defaultTaxRate();
        $year = date('Y');
        $lastInvoice = \App\Models\Invoice::where('invoice_prefix', $prefix)
            ->whereYear('created_at', $year)
            ->orderBy('invoice_sequence', 'desc')
            ->first();
        $sequence = $lastInvoice ? $lastInvoice->invoice_sequence + 1 : 1;
        $invoiceNumber = sprintf('%s-%s-%06d', $prefix, $year, $sequence);

        // Create invoice
        $invoice = \App\Models\Invoice::create([
            'order_id' => $order->id,
            'owner_id' => $petOwner->id,
            'invoice_prefix' => $prefix,
            'invoice_sequence' => $sequence,
            'invoice_number' => $invoiceNumber,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'tax_rate' => $defaultTaxRate,
            'discount_amount' => 0,
            'status' => $isOnlinePayment ? 'paid' : 'pending',
            'notes' => 'Order #' . $order->id . ' - Payment Method: ' . ucfirst(str_replace('_', ' ', $validated['payment_method'])),
        ]);

        // Create invoice items
        foreach ($cart->cartItems as $cartItem) {
            \App\Models\InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_type' => 'product',
                'description' => $cartItem->inventoryItem->name,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->unit_price,
            ]);
        }

        $invoice->load('invoiceItems');
        $invoiceTotalAmount = (float) $invoice->total_amount;

        // If online payment, create payment record (auto-record as income)
        if ($isOnlinePayment) {
            \App\Models\Payment::create([
                'invoice_id' => $invoice->id,
                'payment_date' => now(),
                'amount' => $invoiceTotalAmount,
                'payment_method' => $validated['payment_method'],
                'reference_number' => 'ONLINE-' . $order->id . '-' . time(),
                'received_by' => null, // System generated
                'notes' => 'Online payment for Order #' . $order->id,
            ]);
        }

        // Clear cart after successful checkout
        $cart->cartItems()->delete();

        $successMessage = 'Order placed successfully!';
        if ($isOnlinePayment) {
            $successMessage .= ' Payment of ₱' . number_format($invoiceTotalAmount, 2) . ' has been recorded.';
        } else {
            $successMessage .= ' Please pay ₱' . number_format($invoiceTotalAmount, 2) . ' upon pickup.';
        }

        return redirect()->route('customer.billing.orders')
            ->with('success', $successMessage);
    }
}
