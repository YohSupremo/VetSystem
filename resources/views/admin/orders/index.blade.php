@extends('admin.dashboard')

@section('page-title', 'Orders')
@section('page-description', 'Review and update customer orders')

@section('content')
<style>
    .orders-page {
        max-width: 1400px;
        margin: 0 auto;
    }

    .orders-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }

    .orders-toolbar h3 {
        margin: 0;
        font-family: 'Fredoka', sans-serif;
        font-size: 28px;
        color: var(--dark-text);
    }

    .orders-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }

    .orders-filters input,
    .orders-filters select {
        padding: 10px 12px;
        border-radius: 12px;
        border: 2px solid var(--soft-gray);
        background: var(--white);
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
        min-width: 160px;
    }

    .btn-filter {
        background: linear-gradient(135deg, #FF8C42, #FF6B9D);
        color: white;
        border: none;
        padding: 10px 18px;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-clear {
        border: 2px solid var(--soft-gray);
        padding: 9px 16px;
        border-radius: 12px;
        text-decoration: none;
        color: var(--dark-text);
        font-weight: 600;
        background: var(--white);
    }

    .orders-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
    }

    .orders-table thead th {
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 12px;
        color: var(--light-text);
        padding: 0 16px 8px 16px;
        text-align: left;
    }

    .orders-table tbody tr {
        background: var(--white);
        box-shadow: var(--shadow-soft);
        border-radius: 18px;
    }

    .orders-table tbody td {
        padding: 16px;
        font-size: 14px;
        color: var(--dark-text);
        vertical-align: middle;
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

    .order-meta {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .order-meta small {
        color: var(--light-text);
        font-size: 12px;
    }

    .status-form {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .status-form select {
        padding: 8px 10px;
        border-radius: 10px;
        border: 2px solid var(--soft-gray);
        font-size: 13px;
        background: var(--white);
    }

    .btn-update {
        padding: 8px 12px;
        border-radius: 10px;
        border: none;
        background: #111827;
        color: white;
        font-weight: 600;
        font-size: 12px;
    }

    .empty-card {
        background: var(--white);
        border-radius: 18px;
        padding: 36px;
        text-align: center;
        box-shadow: var(--shadow-soft);
    }

    .empty-card i {
        font-size: 36px;
        color: var(--light-text);
        margin-bottom: 12px;
    }
</style>

<div class="orders-page">
    <div class="orders-toolbar">
        <div>
            <h3>Orders</h3>
            <p class="text-muted">Track orders across all customers and update their status.</p>
        </div>
        <form class="orders-filters" method="GET" action="{{ route('admin.orders.index') }}">
            <input type="text" name="filter[search]" placeholder="Global search" value="{{ request('filter.search') }}">
            <input type="text" name="order_id" placeholder="Order ID" value="{{ request('order_id') }}">
            <input type="text" name="owner" placeholder="Owner name or email" value="{{ request('owner') }}">
            <select name="status">
                <option value="">All Statuses</option>
                @foreach($statusOptions as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-filter">Filter</button>
            <a href="{{ route('admin.orders.index') }}" class="btn-clear">Clear</a>
        </form>
    </div>

    @if($orders->isEmpty())
        <div class="empty-card">
            <i class="fas fa-shopping-bag"></i>
            <h4>No orders found</h4>
            <p class="text-muted">Try adjusting your filters to see more results.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Owner</th>
                        <th>Pet</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        @php
                            $ownerUser = optional($order->owner)->user;
                            $ownerName = $ownerUser ? trim(($ownerUser->first_name ?? '').' '.($ownerUser->last_name ?? '')) : 'Unknown';
                            $ownerEmail = optional($ownerUser)->email ?? 'No email';
                            $orderTotal = $order->invoice ? $order->invoice->total_amount : $order->items->sum('total');
                            $orderDate = $order->order_date ?? $order->created_at;
                        @endphp
                        <tr>
                            <td>
                                <div class="order-meta">
                                    <strong>#{{ $order->id }}</strong>
                                    <small>{{ $order->items->count() }} item{{ $order->items->count() !== 1 ? 's' : '' }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="order-meta">
                                    <strong>{{ $ownerName !== '' ? $ownerName : 'Unknown' }}</strong>
                                    <small>{{ $ownerEmail }}</small>
                                </div>
                            </td>
                            <td>{{ optional($order->pet)->name ?? 'N/A' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $order->order_type ?? 'walk_in')) }}</td>
                            <td>{{ date('M d, Y', strtotime($orderDate)) }}</td>
                            <td>PHP {{ number_format((float) $orderTotal, 2) }}</td>
                            <td>
                                <span class="status-pill {{ $order->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="order-meta" style="gap: 10px;">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-clear">View</a>
                                    <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}" class="status-form">
                                        @csrf
                                        <select name="status">
                                            @foreach($statusOptions as $status)
                                                <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn-update">Update</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
