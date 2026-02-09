<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pet;
use App\Models\PetOwner;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Appointment;
use App\Models\Order;
use App\Models\OrderItem;

class BillingController extends Controller
{
    private function authenticateUser()
    {
        $username = session('username');
        if (!$username) {
            return redirect('/login')->with('error', 'Please login first');
        }
        
        $user = User::where('username', $username)->first();
        if (!$user || $user->role !== 'pet_owner') {
            return redirect('/login')->with('error', 'Access denied');
        }
        
        return $user;
    }
    
    public function index()
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        view()->share('user', $user);
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            $petOwner = PetOwner::create([
                'user_id' => $user->id,
                'notes' => null
            ]);
        }
        $petIds = $petOwner->pets()->pluck('id');
        
        // Get invoices for customer's pets
        $invoices = Invoice::whereIn('pet_id', $petIds)
            ->with(['pet', 'payments'])
            ->orderBy('issue_date', 'desc')
            ->get();
        
        // Calculate summary
        $totalAmount = $invoices->sum('total_amount');
        $paidAmount = $invoices->sum('paid_amount');
        $outstandingAmount = $totalAmount - $paidAmount;
        
        $invoiceStats = [
            'total_invoices' => $invoices->count(),
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'outstanding_amount' => $outstandingAmount,
            'pending_invoices' => $invoices->where('status', 'pending')->count(),
            'paid_invoices' => $invoices->where('status', 'paid')->count()
        ];
        
        return view('customer.billing.index', compact('invoices', 'invoiceStats'));
    }
    
    public function show($id)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        view()->share('user', $user);
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            $petOwner = PetOwner::create([
                'user_id' => $user->id,
                'notes' => null
            ]);
        }
        $petIds = $petOwner->pets()->pluck('id');
        
        $invoice = Invoice::whereIn('pet_id', $petIds)
            ->with(['pet', 'appointment', 'items', 'payments'])
            ->findOrFail($id);
        
        return view('customer.billing.show', compact('invoice'));
    }
    
    public function pay($id)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        view()->share('user', $user);
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            $petOwner = PetOwner::create([
                'user_id' => $user->id,
                'notes' => null
            ]);
        }
        $petIds = $petOwner->pets()->pluck('id');
        
        $invoice = Invoice::whereIn('pet_id', $petIds)
            ->whereIn('status', ['pending', 'partial'])
            ->findOrFail($id);
        
        $paymentMethods = [
            'cash' => 'Cash',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'bank_transfer' => 'Bank Transfer',
            'check' => 'Check',
            'insurance' => 'Insurance',
            'other' => 'Other'
        ];
        
        return view('customer.billing.pay', compact('invoice', 'paymentMethods'));
    }
    
    public function processPayment(Request $request, $id)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            $petOwner = PetOwner::create([
                'user_id' => $user->id,
                'notes' => null
            ]);
        }
        $petIds = $petOwner->pets()->pluck('id');
        
        $invoice = Invoice::whereIn('pet_id', $petIds)
            ->whereIn('status', ['pending', 'partial'])
            ->findOrFail($id);
        
        // Create payment record
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number,
            'notes' => $request->notes
        ]);
        
        // Update invoice status
        $totalPaid = $invoice->payments()->sum('amount');
        $invoice->paid_amount = $totalPaid;
        
        if ($totalPaid >= $invoice->total_amount) {
            $invoice->status = 'paid';
        } else {
            $invoice->status = 'partial';
        }
        
        $invoice->save();
        
        return redirect()->route('customer.billing.show', $invoice->id)
            ->with('success', 'Payment processed successfully!');
    }
    
    public function receipt($paymentId)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        view()->share('user', $user);
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            $petOwner = PetOwner::create([
                'user_id' => $user->id,
                'notes' => null
            ]);
        }
        $petIds = $petOwner->pets()->pluck('id');
        
        $payment = Payment::whereHas('invoice', function($query) use ($petIds) {
            $query->whereIn('pet_id', $petIds);
        })->with(['invoice.pet', 'invoice.items'])->findOrFail($paymentId);
        
        return view('customer.billing.receipt', compact('payment'));
    }

    /**
     * Display customer's orders.
     */
    public function orders()
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        view()->share('user', $user);
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            $orders = collect();
        } else {
            $orders = Order::where('owner_id', $petOwner->id)
                ->with(['items.inventoryItem', 'pet'])
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('customer.billing.orders', compact('orders'));
    }

    /**
     * Display order details.
     */
    public function orderDetails($orderId)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        view()->share('user', $user);
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            return back()->with('error', 'Order not found.');
        }
        
        $order = Order::where('owner_id', $petOwner->id)
            ->where('id', $orderId)
            ->with(['items.inventoryItem', 'pet', 'createdBy'])
            ->firstOrFail();
            
        return view('customer.billing.order-details', compact('order'));
    }

    /**
     * Cancel an order.
     */
    public function cancelOrder($orderId)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            return back()->with('error', 'Order not found.');
        }
        
        $order = Order::where('owner_id', $petOwner->id)
            ->where('id', $orderId)
            ->firstOrFail();
            
        // Check if order can be cancelled
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Order is already cancelled.');
        }
        
        if ($order->status === 'fulfilled') {
            return back()->with('error', 'Cannot cancel fulfilled order.');
        }
        
        // For product orders, restore stock if items were deducted
        if ($order->order_type === 'product' && $order->status === 'confirmed') {
            foreach ($order->items as $item) {
                if ($item->item_type === 'inventory' && $item->inventoryItem) {
                    $item->inventoryItem->increment('quantity', $item->quantity);
                    
                    // Create inventory transaction for stock restoration
                    $inventoryStock = \App\Models\InventoryStock::where('item_id', $item->reference_id)->first();
                    if ($inventoryStock) {
                        \App\Models\InventoryTransaction::create([
                            'stock_id' => $inventoryStock->id,
                            'type' => 'in',
                            'quantity' => $item->quantity,
                            'reference' => 'Cancelled Order #' . $order->id,
                            'notes' => 'Stock restored from cancelled order',
                        ]);
                    }
                }
            }
        }
        
        $order->update([
            'status' => 'cancelled',
            'notes' => ($order->notes ?? '') . "\n\nCancelled by customer on " . now()->format('Y-m-d H:i:s')
        ]);
        
        return redirect()->route('customer.billing.orders')
            ->with('success', 'Order cancelled successfully.');
    }
}
