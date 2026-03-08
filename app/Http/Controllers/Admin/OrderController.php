<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderController extends BaseController
{
    /**
     * Display a listing of all customer orders.
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Order::class)
            ->with(['owner.user', 'pet', 'createdBy', 'items.inventoryItem', 'invoice.invoiceItems'])
            ->allowedFilters([
                AllowedFilter::callback('search', function ($builder, $value) {
                    $term = trim((string) $value);

                    if ($term === '') {
                        return;
                    }

                    $builder->where(function ($sub) use ($term) {
                        $sub->where('id', 'like', '%' . $term . '%')
                            ->orWhere('order_type', 'like', '%' . $term . '%')
                            ->orWhere('status', 'like', '%' . $term . '%')
                            ->orWhereHas('pet', function ($petQuery) use ($term) {
                                $petQuery->where('name', 'like', '%' . $term . '%');
                            })
                            ->orWhereHas('owner.user', function ($ownerUserQuery) use ($term) {
                                $ownerUserQuery->where('first_name', 'like', '%' . $term . '%')
                                    ->orWhere('last_name', 'like', '%' . $term . '%')
                                    ->orWhere('email', 'like', '%' . $term . '%')
                                    ->orWhere('username', 'like', '%' . $term . '%');
                            });
                    });
                }),
            ])
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
        $order = Order::with(['owner.user', 'pet', 'createdBy', 'items.inventoryItem', 'invoice.invoiceItems'])
            ->findOrFail($orderId);
        $statusOptions = ['draft', 'confirmed', 'shipped', 'fulfilled', 'cancelled'];

        return view('admin.orders.show', compact('order', 'statusOptions'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, NotificationService $notificationService, $orderId)
    {
        $data = $request->validate([
            'status' => 'required|in:draft,confirmed,shipped,fulfilled,cancelled',
        ]);

        $order = Order::with(['items', 'invoice.invoiceItems', 'invoice.payments', 'owner.user'])->findOrFail($orderId);
        $previousStatus = $order->status;
        
        // If transitioning to fulfilled and payment method is cash, create payment record
        if ($data['status'] === 'fulfilled' && stripos((string) $order->notes, 'Payment Method: Cash') !== false) {
            $invoice = $order->invoice;
            
            if ($invoice && $invoice->status !== 'paid') {
                $balance = max(0, (float) $invoice->total_amount - (float) $invoice->paid_amount);
                
                if ($balance > 0) {
                    \App\Models\Payment::create([
                        'invoice_id' => $invoice->id,
                        'payment_date' => now(),
                        'amount' => $balance,
                        'payment_method' => 'cash',
                        'reference_number' => 'CASH-' . $order->id . '-' . time(),
                        'received_by' => auth()->id(),
                        'notes' => 'Cash payment received for Order #' . $order->id,
                    ]);
                }

                $invoice->load('payments');
                $paidAmount = (float) $invoice->payments->sum('amount');
                $invoice->update([
                    'status' => $paidAmount >= (float) $invoice->total_amount ? 'paid' : 'partial',
                ]);
            }
        }
        
        $order->update([
            'status' => $data['status'],
        ]);

        if ($previousStatus !== $order->status) {
            $customer = $order->owner?->user;

            if ($customer) {
                $notificationService->send(
                    $customer,
                    Notification::TYPE_PAYMENT,
                    'Order Status Updated',
                    'Your order #' . $order->id . ' status changed from ' . ucfirst(str_replace('_', ' ', (string) $previousStatus)) . ' to ' . ucfirst(str_replace('_', ' ', (string) $order->status)) . '.',
                    [
                        'reference_type' => 'invoice',
                        'reference_id' => $order->id,
                        'action_url' => route('customer.billing.order-details', $order->id),
                    ]
                );
            }
        }

        return back()->with('success', 'Order status updated successfully.');
    }
}
