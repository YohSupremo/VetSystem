@extends('admin.dashboard')

@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-file-invoice-dollar"></i> Billing & Invoices</h1>
        <p>Manage client invoices and billing information</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.billing.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Invoice
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Client</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="text-center text-muted">No invoices found</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
