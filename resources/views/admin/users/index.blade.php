@extends('admin.dashboard')

@section('page-title', 'Users')
@section('page-description', 'Manage system users')

@section('content')
<style>
    .users-content-area {
        max-width: 1400px;
        margin: 0 auto;
    }

    .users-content-area .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .users-content-area .top-bar h3 {
        margin: 0;
        font-family: 'Fredoka', sans-serif;
        font-size: 32px;
        font-weight: 700;
        color: #2D3748;
        background: linear-gradient(135deg, #FF8C42, #FF6B9D);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .users-content-area .top-bar p {
        margin: 8px 0 0 0;
        color: #718096;
        font-size: 15px;
    }

    .users-content-area .btn-add-user {
        background: linear-gradient(135deg, #FF8C42, #FF6B9D);
        color: white;
        padding: 14px 28px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
        border: none;
    }

    .users-content-area .btn-add-user:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 140, 66, 0.4);
        color: white;
        text-decoration: none;
    }

    .users-content-area .table-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid rgba(255, 140, 66, 0.08);
    }

    .users-content-area .table {
        margin-bottom: 0;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .users-content-area .table thead {
        background: linear-gradient(135deg, #F8F9FA, #F1F3F5);
    }

    .users-content-area .table thead th {
        padding: 20px 20px;
        font-family: 'Fredoka', sans-serif;
        font-size: 12px;
        font-weight: 700;
        color: #2D3748;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        border: none;
        white-space: nowrap;
    }

    .users-content-area .table thead th:first-child {
        border-top-left-radius: 16px;
        padding-left: 24px;
    }

    .users-content-area .table thead th:last-child {
        border-top-right-radius: 16px;
        padding-right: 24px;
    }

    .users-content-area .table tbody tr {
        border-bottom: 1px solid #F1F3F5;
        transition: all 0.2s ease;
    }

    .users-content-area .table tbody tr:last-child {
        border-bottom: none;
    }

    .users-content-area .table tbody tr:hover {
        background: linear-gradient(135deg, #FFF9F5, #FFF5F0);
    }

    .users-content-area .table tbody td {
        padding: 20px 20px;
        color: #718096;
        font-size: 14px;
        vertical-align: middle;
        border: none;
    }

    .users-content-area .table tbody td:first-child {
        color: #2D3748;
        font-weight: 700;
        padding-left: 24px;
    }

    .users-content-area .table tbody td:last-child {
        padding-right: 24px;
    }

    .users-content-area .user-name {
        font-weight: 600;
        color: #2D3748;
    }

    .users-content-area .role-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        gap: 6px;
    }

    .users-content-area .role-badge.admin {
        background: linear-gradient(135deg, #FEE2E2, #FECACA);
        color: #DC2626;
    }

    .users-content-area .role-badge.registered_user {
        background: linear-gradient(135deg, #28A745, #1E40AF);
        color: #FFFFFF;
    }

    .users-content-area .role-badge.pet_owner {
        background: linear-gradient(135deg, #FEF3C7, #FDE68A);
        color: #92400E;
    }

    .users-content-area .role-badge.staff {
        background: linear-gradient(135deg, #DBEAFE, #BFDBFE);
        color: #1E40AF;
    }

    .users-content-area .role-badge.vet {
        background: linear-gradient(135deg, #D1FAE5, #A7F3D0);
        color: #065F46;
    }

    .users-content-area .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .users-content-area .status-badge.active {
        background: linear-gradient(135deg, #D1FAE5, #A7F3D0);
        color: #065F46;
    }

    .users-content-area .status-badge.inactive {
        background: linear-gradient(135deg, #F1F3F5, #E8E8EA);
        color: #6B7280;
    }

    .users-content-area .status-badge i {
        font-size: 8px;
    }

    .users-content-area .date-text {
        color: #718096;
        font-size: 13px;
    }

    .users-content-area .pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 24px;
        padding: 20px;
    }

    .users-content-area .pagination .page-item {
        list-style: none;
    }

    .users-content-area .pagination .page-link {
        padding: 10px 16px;
        border-radius: 8px;
        border: 2px solid #E2E8F0;
        color: #2D3748;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .users-content-area .pagination .page-link:hover {
        background: linear-gradient(135deg, #FF8C42, #FF6B9D);
        color: white;
        border-color: transparent;
        transform: translateY(-2px);
    }

    .users-content-area .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #FF8C42, #FF6B9D);
        color: white;
        border-color: transparent;
    }

    .users-content-area .pagination .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .users-content-area .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: #718096;
    }

    .users-content-area .empty-state i {
        font-size: 64px;
        color: #E2E8F0;
        margin-bottom: 20px;
        display: block;
    }

    .users-content-area .empty-state h4 {
        font-family: 'Fredoka', sans-serif;
        font-size: 22px;
        color: #2D3748;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .users-content-area .empty-state p {
        font-size: 15px;
        color: #718096;
    }

    @media (max-width: 768px) {
        .users-content-area .top-bar {
            flex-direction: column;
            align-items: flex-start;
        }

        .users-content-area .btn-add-user {
            width: 100%;
            justify-content: center;
        }

        .users-content-area .table-card {
            overflow-x: auto;
        }
    }
</style>

<div class="users-content-area">
    <div class="top-bar">
        <div>
            <h3>All Users</h3>
            <p>Total: {{ $users->total() }} user{{ $users->total() !== 1 ? 's' : '' }}</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-add-user">
            <i class="fas fa-user-plus"></i>
            Add New User
        </a>
    </div>

    @if($users->count() > 0)
    <div class="table-card">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td class="user-name">{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="role-badge {{ $user->role }}">
                                @if($user->role === 'admin')
                                    <i class="fas fa-user-shield"></i>
                                @elseif($user->role === 'registered_user')
                                    <i class="fas fa-user"></i>
                                @elseif($user->role === 'vet')
                                    <i class="fas fa-user-md"></i>
                                @else
                                    <i class="fas fa-user"></i>
                                @endif
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>
                        <td>{{ $user->contact_number ?? '—' }}</td>
                        <td>
                            <span class="status-badge {{ $user->is_active ? 'active' : 'inactive' }}">
                                <i class="fas fa-circle"></i>
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="date-text">
                            {{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}
                        </td>
                        <td style="white-space:nowrap;">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm" title="View"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($users->hasPages())
        <div class="pagination">
            {{ $users->links() }}
        </div>
        @endif
    </div>
    @else
    <div class="table-card">
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <h4>No Users Yet</h4>
            <p>Get started by adding your first user to the system.</p>
        </div>
    </div>
    @endif
</div>

@endsection