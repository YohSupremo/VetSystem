@extends('admin.dashboard')

@push('styles')
<style>
    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .header-title h1 {
        font-size: 1.75rem;
        font-weight: 600;
        margin: 0 0 0.5rem;
        color: #2c3e50;
    }

    .header-title p {
        color: #6c757d;
        margin: 0;
    }

    .btn-primary {
        background: linear-gradient(135deg, #9c27b0 0%, #6a1b9a 100%);
        border: none;
        padding: 0.6rem 1.4rem;
        border-radius: 8px;
        font-weight: 500;
        color: #fff;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .content-section {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.5rem;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
    }

    .section-header h2 {
        font-size: 1.3rem;
        font-weight: 600;
        margin: 0;
        color: #2c3e50;
    }

    .section-header h2 i {
        margin-right: 0.5rem;
        color: #9c27b0;
    }

    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 6px;
    }

    .data-table thead th {
        padding: 0.75rem 1rem;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6c757d;
        border-bottom: 1px solid #e9ecef;
        text-align: left;
    }

    .data-table tbody tr {
        background: #f9fafb;
    }

    .data-table tbody td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
    }

    .actions {
        display: flex;
        gap: 6px;
    }

    .btn-icon {
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
        border-radius: 4px;
        color: var(--light-text);
    }

    .btn-icon:hover {
        background: var(--paw-medium);
        color: var(--primary-orange);
    }

    .btn-icon.text-danger:hover {
        color: #c82333;
    }

    .delete-form {
        display: inline;
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-spa"></i> Grooming Services</h1>
        <p>Manage available grooming services and pricing.</p>
    </div>
    <div>
        <a href="{{ route('admin.grooming-services.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Service
        </a>
    </div>
</div>

<div class="content-section">
    <div class="section-header">
        <h2><i class="fas fa-list"></i> Available Services</h2>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Service Name</th>
                    <th>Description</th>
                    <th>Duration</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                    <tr>
                        <td><strong>{{ $service->service_name }}</strong></td>
                        <td>{{ $service->description ?: '—' }}</td>
                        <td>{{ $service->duration_minutes ? $service->duration_minutes . ' min' : '—' }}</td>
                        <td>₱{{ number_format($service->price, 2) }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.grooming-services.show', $service->id) }}" class="btn-icon" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.grooming-services.edit', $service->id) }}" class="btn-icon" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.grooming-services.destroy', $service->id) }}" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon text-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No services found. <a href="{{ route('admin.grooming-services.create') }}">Add your first service</a></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var forms = document.querySelectorAll('.delete-form');
    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var ok = confirm('Are you sure you want to delete this service?');
            if (!ok) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush
@endsection
