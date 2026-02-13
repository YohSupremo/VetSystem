@extends('admin.dashboard')

@section('page-title', 'Pet Owners')
@section('page-description', 'Manage all pet owners and their information')

@section('content')
<style>
    .owners-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
    }

    .owner-card {
        background: var(--white);
        border-radius: 15px;
        padding: 20px;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s ease;
    }

    .owner-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .owner-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--soft-gray);
    }

    .owner-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-orange), var(--accent-pink));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 18px;
    }

    .owner-name {
        font-family: 'Fredoka', sans-serif;
        font-size: 18px;
        font-weight: 600;
        color: var(--dark-text);
        margin: 0;
    }

    .owner-contact {
        font-size: 13px;
        color: var(--light-text);
        margin: 5px 0 0 0;
    }

    .owner-info {
        margin-bottom: 15px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding: 8px;
        background: var(--soft-gray);
        border-radius: 8px;
        font-size: 13px;
    }

    .info-label {
        font-weight: 600;
        color: var(--dark-text);
    }

    .info-value {
        color: var(--light-text);
    }

    .card-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .card-actions .btn {
        flex: 1;
        padding: 8px 12px;
        font-size: 12px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--light-text);
    }

    .empty-state i {
        font-size: 64px;
        color: var(--soft-gray);
        margin-bottom: 20px;
        display: block;
    }

    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    /* ONLY TABLE ENHANCEMENTS - Won't affect navigation */
    .content-area .table-responsive {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .content-area .table {
        margin-bottom: 0;
    }

    .content-area .table thead th {
        padding: 16px 12px;
        background: linear-gradient(135deg, #F8F9FA, #F1F3F5);
        border-bottom: 2px solid #E9ECEF;
        font-weight: 700;
        font-size: 11px;
        color: #495057;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .content-area .table tbody td {
        padding: 18px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #F1F3F5;
        font-size: 14px;
    }

    .content-area .table tbody tr:last-child td {
        border-bottom: none;
    }

    .content-area .table tbody tr:hover {
        background: linear-gradient(135deg, #FFF9F5, #FFF5F0);
        transition: all 0.2s ease;
    }

    .content-area .table tbody td:first-child {
        font-weight: 700;
        color: #1F2937;
    }

    .content-area .table tbody td:nth-child(2) {
        font-weight: 600;
        color: #1F2937;
    }

    .contact-method-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        background: linear-gradient(135deg, #FEF3C7, #FDE68A);
        color: #92400E;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        gap: 5px;
    }

    .contact-method-badge i {
        font-size: 10px;
    }

    .pets-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        background: linear-gradient(135deg, #FF8A6B, #FF6B9D);
        color: white;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
        box-shadow: 0 2px 6px rgba(255, 138, 107, 0.3);
    }

    .emergency-name {
        font-weight: 600;
        color: #1F2937;
        display: block;
        margin-bottom: 2px;
    }

    .emergency-details {
        font-size: 13px;
        color: #6B7280;
    }

    /* Enhanced Action Buttons */
    .content-area .btn-sm {
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s ease;
        margin-right: 6px;
        border: none;
    }

    /* View Button - Blue (Eye/View theme) */
    .content-area .btn-sm.btn-outline-secondary {
        background: linear-gradient(135deg, #DBEAFE, #BFDBFE);
        color: #1E40AF;
        border: 2px solid #BFDBFE;
        padding: 8px 14px;
    }

    .content-area .btn-sm.btn-outline-secondary:hover {
        background: linear-gradient(135deg, #3B82F6, #2563EB);
        border-color: transparent;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    /* Add User Button - Orange theme */
    .content-area .btn-outline-secondary {
        background: linear-gradient(135deg, #FFF4ED, #FFE4D6);
        color: #EA580C;
        border: 2px solid #FDBA74;
        padding: 12px 24px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .content-area .btn-outline-secondary:hover {
        background: linear-gradient(135deg, #FF8C42, #FF6B9D);
        border-color: transparent;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
    }

    .content-area .btn-outline-primary {
        background: linear-gradient(135deg, #DBEAFE, #BFDBFE);
        color: #1E40AF;
        border: 1px solid #BFDBFE;
    }

    .content-area .btn-outline-primary:hover {
        background: linear-gradient(135deg, #BFDBFE, #93C5FD);
        border-color: #93C5FD;
        color: #1E3A8A;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(30, 64, 175, 0.2);
    }

    .content-area .btn-danger {
        background: linear-gradient(135deg, #FEE2E2, #FECACA);
        color: #DC2626;
        border: 1px solid #FECACA;
    }

    .content-area .btn-danger:hover {
        background: linear-gradient(135deg, #FECACA, #FCA5A5);
        border-color: #FCA5A5;
        color: #B91C1C;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.2);
    }
</style>

<div class="content-area">
<div class="top-bar">
    <div>
        <h3 style="margin: 0; font-family: 'Fredoka', sans-serif; font-size: 24px; color: var(--dark-text);">All Pet Owners</h3>
        <p style="margin: 5px 0 0 0; color: var(--light-text); font-size: 14px;">Total: {{ $owners->count() }} owners</p>
    </div>
    <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('admin.users.create') }}" class="btn btn-outline-secondary"><i class="fas fa-user-plus"></i> Add User</a>
        <a href="{{ route('admin.pet-owners.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Owner</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Preferred Contact</th>
                <th>Emergency Contact</th>
                <th>Notes</th>
                <th>Pets</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($owners as $owner)
                <tr>
                    <td>{{ $owner->id }}</td>
                    <td>{{ $owner->user->first_name }} {{ $owner->user->last_name }}</td>
                    <td>{{ $owner->user->email }}</td>
                    <td>{{ $owner->user->contact_number }}</td>
                    <td>
                        <span class="contact-method-badge">
                            <i class="fas fa-{{ $owner->preferred_contact_method === 'phone' ? 'phone' : 'envelope' }}"></i>
                            {{ ucfirst($owner->preferred_contact_method ?? 'email') }}
                        </span>
                    </td>
                    <td>
                        @if($owner->emergency_contact_name || $owner->emergency_contact_phone)
                            <div>
                                <span class="emergency-name">{{ $owner->emergency_contact_name }}</span>
                                <span class="emergency-details">
                                    {{ $owner->emergency_contact_phone }}
                                    @if($owner->emergency_contact_relationship)
                                        ({{ $owner->emergency_contact_relationship }})
                                    @endif
                                </span>
                            </div>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ Illuminate\Support\Str::limit($owner->notes ?? '-', 50) }}</td>
                    <td>
                        <span class="pets-count-badge">{{ $owner->pets->count() }}</span>
                    </td>
                    <td>{{ $owner->created_at ? $owner->created_at->format('M d, Y') : '-' }}</td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('admin.pet-owners.show', $owner) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        <a href="{{ route('admin.pet-owners.edit', $owner) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.pet-owners.destroy', $owner) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this owner?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
@endsection