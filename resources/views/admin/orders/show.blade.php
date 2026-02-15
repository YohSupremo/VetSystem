@extends('admin.dashboard')

@section('page-title', 'Order #'.$order->id)
@section('page-description', 'Review order details and update status')

@section('content')
<style>
    .order-detail {
        max-width: 1200px;
        margin: 0 auto;
    }

    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .back-link {
        text-decoration: none;
        font-weight: 600;
        color: var(--dark-text);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 12px;
        font-weight: 600;
    }

    .status-pill.draft { background: rgba(148, 163, 184, 0.2); color: #64748B; }
    .status-pill.confirmed { background: rgba(59, 130, 246, 0.15); color: #2563EB; }
    .status-pill.shipped { background: rgba(14, 116, 144, 0.18); color: #0E7490; }
    .status-pill.fulfilled { background: rgba(34, 197, 94, 0.18); color: #16A34A; }
    .status-pill.cancelled { background: rgba(239, 68, 68, 0.18); color: #DC2626; }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }

    .info-card {
        background: var(--white);
        border-radius: 16px;
        padding: 18px;
        box-shadow: var(--shadow-soft);
    }

    .info-card h4 {
        margin: 0 0 8px 0;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--light-text);
    }

    .info-card p {
        margin: 0;
        font-size: 15px;
        color: var(--dark-text);
        font-weight: 600;
    }

    .items-card {
        background: var(--white);
        border-radius: 18px;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
    }

    .items-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        font-weight: 700;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .items-table thead th {
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 12px;
        color: var(--light-text);
        padding: 12px 20px;
        text-align: left;
        background: #f8fafc;
    }

    .items-table tbody td {
        padding: 14px 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        font-size: 14px;
        color: var(--dark-text);
    }

    .items-table tbody tr:last-child td {
        border-bottom: none;
    }

    .status-form {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .status-form select {
        padding: 8px 10px;
        border-radius: 10px;
        border: 2px solid var(--soft-gray);
        font-size: 13px;
        background: var(--white);
    }

    .btn-update {
        padding: 8px 14px;
        border-radius: 10px;
        border: none;
        background: #111827;
        color: white;
        font-weight: 600;
        font-size: 12px;
    }

    .total-row {
        font-weight: 700;
        text-align: right;
    }
</style>

<div class="order-detail">
    <div class="header-actions">
        <a href="{{ route('admin.orders.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to orders
        </a>
        <span class="status-pill {{ $order->status }}">
            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
        </span>
    </div>

    @php
        $ownerUser = optional($order->owner)->user;
        $ownerName = $ownerUser ? trim(($ownerUser->first_name ?? '').' '.($ownerUser->last_name ?? '')) : 'Unknown';
        $orderTotal = $order->items->sum('total');
        $orderDate = $order->order_date ?? $order->created_at;
    @endphp

    <div class="info-grid">
        <div class="info-card">
            <h4>Owner</h4>
            <p>{{ $ownerName !== '' ? $ownerName : 'Unknown' }}</p>
            <p style="font-weight: 400; font-size: 13px; color: var(--light-text);">{{ optional($ownerUser)->email ?? 'No email' }}</p>
        </div>
        <div class="info-card">
            <h4>Pet</h4>
            <p>{{ optional($order->pet)->name ?? 'N/A' }}</p>
        </div>
        <div class="info-card">
            <h4>Order Type</h4>
            <p>{{ ucfirst(str_replace('_', ' ', $order->order_type ?? 'walk_in')) }}</p>
        </div>
        <div class="info-card">
            <h4>Order Date</h4>
            <p>{{ date('F d, Y h:i A', strtotime($orderDate)) }}</p>
        </div>
        <div class="info-card">
            <h4>Created By</h4>
            <p>{{ optional($order->createdBy)->username ?? 'System' }}</p>
        </div>
        <div class="info-card">
            <h4>Total</h4>
            <p>PHP {{ number_format((float) $orderTotal, 2) }}</p>
        </div>
    </div>

    <div class="items-card" style="margin-bottom: 24px;">
        <div class="items-card-header">
            <span>Order Items</span>
            <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}" class="status-form">
                @csrf
                <select name="status">
                    @foreach($statusOptions as $status)
                        <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn-update">Update Status</button>
            </form>
        </div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->inventoryItem->name ?? $item->description ?? 'Item' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>PHP {{ number_format((float) $item->unit_price, 2) }}</td>
                        <td>PHP {{ number_format((float) $item->total, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="total-row">Total</td>
                    <td class="total-row">PHP {{ number_format((float) $orderTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($order->notes)
        <div class="info-card">
            <h4>Notes</h4>
            <p style="font-weight: 400;">{{ $order->notes }}</p>
        </div>
    @endif
</div>
@endsection
