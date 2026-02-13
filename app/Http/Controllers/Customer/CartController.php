<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
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

        return view('customer.cart.index', compact('cart'));
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
            'total' => $product->unit_price * $validated['quantity'],
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

        // Create order
        $order = Order::create([
            'appointment_id' => null,
            'pet_id' => null,
            'owner_id' => $petOwner->id,
            'created_by' => $user->id,
            'order_type' => 'product',
            'status' => 'confirmed',
            'order_date' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Add order items and update stock
        foreach ($cart->cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'item_type' => 'inventory',
                'reference_id' => $cartItem->inventory_item_id,
                'description' => $cartItem->inventoryItem->name,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->unit_price,
                'total' => $cartItem->total,
            ]);

            // Update inventory stock
            $cartItem->inventoryItem->decrement('quantity', $cartItem->quantity);

            // Find corresponding inventory stock record and create transaction
            $inventoryStock = \App\Models\InventoryStock::where('item_id', $cartItem->inventory_item_id)->first();
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

        // Clear cart after successful checkout
        $cart->cartItems()->delete();

        return redirect()->route('customer.products.index')
            ->with('success', 'Order placed successfully! Your items have been reserved and stock updated.');
    }
}
