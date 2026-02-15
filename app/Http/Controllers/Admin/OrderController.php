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

        $order = Order::findOrFail($orderId);
        $order->update([
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Order status updated successfully.');
    }
}
