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
use App\Models\Notification;
use App\Services\NotificationService;

class BillingController extends Controller
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
    
    public function index(Request $request)
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
        
        // Get invoices for customer (using owner_id) with filtering
        $query = Invoice::where('owner_id', $petOwner->id)
            ->with(['pet', 'payments', 'invoiceItems', 'appointment'])
            ->orderBy('issue_date', 'desc');
        
        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('issue_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('issue_date', '<=', $request->end_date);
        }
        
        $invoices = $query->get();
        $invoices->each(function ($invoice) {
            $invoice->source_category = $this->resolveInvoiceCategory($invoice);
        });

        $selectedCategory = $request->get('category', 'all');

        $categoryLabelMap = [
            'all' => 'All',
            'boarding' => 'Boarding',
            'consultation' => 'Consultation',
            'grooming' => 'Grooming',
            'surgery' => 'Surgery',
            'lab_test' => 'Lab Test',
            'vaccination' => 'Vaccination',
            'product' => 'Products',
            'service' => 'Service',
            'other' => 'Other',
            'mixed' => 'Mixed',
        ];

        $categoryCounts = ['all' => $invoices->count()];
        foreach ($invoices as $invoice) {
            $category = $invoice->source_category ?: 'other';
            $categoryCounts[$category] = ($categoryCounts[$category] ?? 0) + 1;
        }

        if ($selectedCategory !== 'all') {
            $invoices = $invoices
                ->where('source_category', $selectedCategory)
                ->values();
        }

        $availableCategories = collect($categoryCounts)
            ->keys()
            ->filter(function ($key) {
                return $key === 'all' || ($this->isKnownCategory($key));
            })
            ->values()
            ->all();
        
        // Calculate summary (show all stats regardless of filters)
        $allInvoices = Invoice::where('owner_id', $petOwner->id)->get();
        
        // Outstanding: unpaid invoices (pending, partial) - NOT cancelled
        $outstandingInvoices = $allInvoices->whereIn('status', ['pending', 'partial', 'overdue']);
        $outstandingAmount = $outstandingInvoices->sum(function ($invoice) {
            return $invoice->total_amount - $invoice->paid_amount;
        });
        
        // Cancelled invoices
        $cancelledInvoices = $allInvoices->where('status', 'cancelled');
        $cancelledAmount = $cancelledInvoices->sum('total_amount');
        
        // Paid amount from all non-cancelled invoices
        $paidAmount = $allInvoices->whereNotIn('status', ['cancelled'])->sum('paid_amount');
        
        $invoiceStats = [
            'total_invoices' => $allInvoices->count(),
            'outstanding_amount' => $outstandingAmount,
            'paid_amount' => $paidAmount,
            'cancelled_amount' => $cancelledAmount,
            'cancelled_count' => $cancelledInvoices->count(),
            'pending_invoices' => $allInvoices->whereIn('status', ['pending', 'partial'])->count(),
            'paid_invoices' => $allInvoices->where('status', 'paid')->count()
        ];
        
        return view('customer.billing.index', compact(
            'invoices',
            'invoiceStats',
            'selectedCategory',
            'categoryCounts',
            'categoryLabelMap',
            'availableCategories'
        ));
    }

    private function resolveInvoiceCategory($invoice): string
    {
        if (!$invoice instanceof Invoice) {
            return 'other';
        }

        $itemTypes = $invoice->invoiceItems
            ->pluck('item_type')
            ->filter()
            ->unique()
            ->values();

        if ($itemTypes->count() === 1) {
            return (string) $itemTypes->first();
        }

        if ($itemTypes->count() > 1) {
            return 'mixed';
        }

        if ($invoice->appointment && !empty($invoice->appointment->type)) {
            return (string) $invoice->appointment->type;
        }

        if (!empty($invoice->order_id)) {
            return 'product';
        }

        if (str_contains((string) ($invoice->notes ?? ''), '[BOARDING_ID:')) {
            return 'boarding';
        }

        return 'other';
    }

    private function isKnownCategory(string $category): bool
    {
        return in_array($category, [
            'all',
            'boarding',
            'consultation',
            'grooming',
            'surgery',
            'lab_test',
            'vaccination',
            'product',
            'service',
            'other',
            'mixed',
        ], true);
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
        
        $invoice = Invoice::where('owner_id', $petOwner->id)
            ->with(['pet', 'appointment', 'invoiceItems', 'payments'])
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
        
        $invoice = Invoice::where('owner_id', $petOwner->id)
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
    
    public function processPayment(Request $request, NotificationService $notificationService, $id)
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
        
        $invoice = Invoice::where('owner_id', $petOwner->id)
            ->whereIn('status', ['pending', 'partial'])
            ->findOrFail($id);
        
        // Normalize payment method to supported enum values
        $paymentMethod = $request->payment_method;
        if ($paymentMethod === 'ewallet' || $paymentMethod === 'e-wallet') {
            $paymentMethod = 'mobile_payment';
        }

        $amount = abs((float) $request->amount);
        if ($amount <= 0) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Payment amount must be greater than zero.');
        }

        // Create payment record
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'reference_number' => $request->reference_number,
            'notes' => $request->notes
        ]);
        
        // Update invoice status
        $totalPaid = $invoice->payments()->sum('amount');
        
        if ($totalPaid >= $invoice->total_amount) {
            $invoice->status = 'paid';
        } else {
            $invoice->status = 'partial';
        }
        
        $invoice->save();

        $notificationService->send(
            $user,
            Notification::TYPE_PAYMENT,
            'Payment Recorded',
            'Your payment for invoice ' . $invoice->invoice_number . ' was recorded. Current invoice status: ' . ucfirst($invoice->status) . '.',
            [
                'reference_type' => 'invoice',
                'reference_id' => $invoice->id,
                'action_url' => route('customer.billing.show', $invoice->id),
            ]
        );
        
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
        
        $payment = Payment::whereHas('invoice', function($query) use ($petOwner) {
            $query->where('owner_id', $petOwner->id);
        })->with(['invoice.pet', 'invoice.invoiceItems'])->findOrFail($paymentId);
        
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

            if ($orders->isNotEmpty()) {
                $orderIds = $orders->pluck('id')->all();

                $invoicesByOrder = Invoice::where('owner_id', $petOwner->id)
                    ->whereIn('order_id', $orderIds)
                    ->with('invoiceItems')
                    ->get()
                    ->keyBy('order_id');

                $orders->transform(function ($order) use ($invoicesByOrder) {
                    $invoice = $invoicesByOrder->get($order->id);
                    $order->billing_total = $invoice ? (float) $invoice->total_amount : (float) $order->items->sum('total');
                    return $order;
                });
            }
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

        $invoice = Invoice::where('owner_id', $petOwner->id)
            ->where('order_id', $order->id)
            ->with('invoiceItems')
            ->first();
            
        return view('customer.billing.order-details', compact('order', 'invoice'));
    }

    /**
     * Cancel an order.
     */
    public function cancelOrder(NotificationService $notificationService, $orderId)
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
            ->with('items.inventoryItem')
            ->firstOrFail();
            
        // Check if order can be cancelled
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Order is already cancelled.');
        }
        
        if ($order->status === 'fulfilled' || $order->status === 'shipped') {
            return back()->with('error', 'Cannot cancel order that is already shipped or fulfilled.');
        }
        
        // For online/product orders, restore stock
        if (in_array($order->order_type, ['online', 'product']) && $order->status === 'confirmed') {
            foreach ($order->items as $item) {
                if ($item->inventoryItem) {
                    // Restore stock to inventory_stock table
                    $inventoryStock = \App\Models\InventoryStock::where('item_id', $item->inventory_item_id)->first();
                    if ($inventoryStock) {
                        $inventoryStock->increment('quantity', $item->quantity);
                        
                        \App\Models\InventoryTransaction::create([
                            'stock_id' => $inventoryStock->id,
                            'type' => 'in',
                            'quantity' => $item->quantity,
                            'reference' => 'Cancelled Order #' . $order->id,
                            'performed_by' => $user->id,
                            'notes' => 'Stock restored from cancelled order',
                        ]);
                    }
                }
            }
        }
        
        // Find and handle associated invoice
        $invoice = \App\Models\Invoice::where('order_id', $order->id)->with('payments')->first();
        $refundedAmount = 0;
        
        if ($invoice) {
            // Get payments to be refunded
            $refundedAmount = $invoice->payments->sum('amount');
            
            // Delete all payments (revert income)
            \App\Models\Payment::where('invoice_id', $invoice->id)->delete();
            
            // Cancel the invoice
            $invoice->update([
                'status' => 'cancelled',
                'notes' => ($invoice->notes ?? '') . "\n\nCancelled due to order cancellation on " . now()->format('Y-m-d H:i:s'),
            ]);
        }
        
        // Cancel the order
        $order->update([
            'status' => 'cancelled',
            'notes' => ($order->notes ?? '') . "\n\nCancelled by customer on " . now()->format('Y-m-d H:i:s')
        ]);

        $notificationService->send(
            $user,
            Notification::TYPE_PAYMENT,
            'Order Cancelled',
            'Order #' . $order->id . ' has been cancelled successfully.',
            [
                'reference_type' => 'invoice',
                'reference_id' => $order->id,
                'action_url' => route('customer.billing.order-details', $order->id),
            ]
        );
        
        $message = 'Order cancelled successfully.';
        if ($refundedAmount > 0) {
            $message .= ' Payment of ₱' . number_format($refundedAmount, 2) . ' has been reversed.';
        }
        
        return redirect()->route('customer.billing.orders')
            ->with('success', $message);
    }
}
