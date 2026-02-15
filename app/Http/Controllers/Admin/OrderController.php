<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    /**
     * Display a listing of all customer orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['owner.user', 'pet', 'createdBy', 'items.inventoryItem'])
            ->orderByDesc('order_date')
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('owner')) {
            $ownerTerm = $request->owner;
            $query->whereHas('owner.user', function ($builder) use ($ownerTerm) {
                $builder->where('first_name', 'like', "%{$ownerTerm}%")
                    ->orWhere('last_name', 'like', "%{$ownerTerm}%")
                    ->orWhere('email', 'like', "%{$ownerTerm}%")
                    ->orWhere('username', 'like', "%{$ownerTerm}%");
            });
        }

        if ($request->filled('order_id')) {
            $query->where('id', $request->order_id);
        }

        $orders = $query->paginate(20)->appends($request->query());
        $statusOptions = ['draft', 'confirmed', 'shipped', 'fulfilled', 'cancelled'];

        return view('admin.orders.index', compact('orders', 'statusOptions'));
    }

    /**
     * Display a specific order.
     */
    public function show($orderId)
    {
        $order = Order::with(['owner.user', 'pet', 'createdBy', 'items.inventoryItem'])
            ->findOrFail($orderId);
        $statusOptions = ['draft', 'confirmed', 'shipped', 'fulfilled', 'cancelled'];

        return view('admin.orders.show', compact('order', 'statusOptions'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $orderId)
    {
        $data = $request->validate([
            'status' => 'required|in:draft,confirmed,shipped,fulfilled,cancelled',
        ]);

        $order = Order::with('items')->findOrFail($orderId);
        
        // If transitioning to fulfilled and payment method is cash, create payment record
        if ($data['status'] === 'fulfilled' && strpos($order->notes, 'Payment Method: Cash') !== false) {
            // Find the associated invoice
            $invoice = \App\Models\Invoice::where('order_id', $order->id)->first();
            
            if ($invoice && $invoice->status !== 'paid') {
                // Calculate total amount
                $totalAmount = $order->items->sum(function($item) {
                    return $item->quantity * $item->unit_price;
                });
                
                // Create payment record
                \App\Models\Payment::create([
                    'invoice_id' => $invoice->id,
                    'payment_date' => now(),
                    'amount' => $totalAmount,
                    'payment_method' => 'cash',
                    'reference_number' => 'CASH-' . $order->id . '-' . time(),
                    'received_by' => auth()->id(),
                    'notes' => 'Cash payment received for Order #' . $order->id,
                ]);
                
                // Mark invoice as paid
                $invoice->update(['status' => 'paid']);
            }
        }
        
        $order->update([
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Order status updated successfully.');
    }
}
