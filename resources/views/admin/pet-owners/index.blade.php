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
</style>

<div class="top-bar">
    <div>
        <h3 style="margin: 0; font-family: 'Fredoka', sans-serif; font-size: 24px; color: var(--dark-text);">
            All Pet Owners
        </h3>
        <p style="margin: 5px 0 0 0; color: var(--light-text); font-size: 14px;">
            Total: {{ count($owners) }} owners
        </p>
    </div>
    <a href="{{ route('admin.pet-owners.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Owner
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if($owners->isEmpty())
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <h3>No Pet Owners Yet</h3>
        <p>Start by adding your first pet owner.</p>
        <a href="{{ route('admin.pet-owners.create') }}" class="btn btn-primary" style="margin-top: 20px;">
            <i class="fas fa-plus"></i> Add First Owner
        </a>
    </div>
@else
    <div class="owners-container">
        @foreach($owners as $owner)
            <div class="owner-card">
                <div class="owner-header">
                    <div class="owner-avatar">
                        {{ strtoupper(substr($owner->user->first_name, 0, 1)) }}{{ strtoupper(substr($owner->user->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="owner-name">{{ $owner->user->first_name }} {{ $owner->user->last_name }}</h4>
                        <p class="owner-contact">{{ $owner->user->email }}</p>
                    </div>
                </div>

                <div class="owner-info">
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-phone"></i> Contact:</span>
                        <span class="info-value">{{ $owner->user->contact_number }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-map"></i> Address:</span>
                        <span class="info-value">{{ Str::limit($owner->user->address, 30) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-paw"></i> Pets:</span>
                        <span class="info-value">{{ $owner->pets->count() }} pet(s)</span>
                    </div>
                    @if($owner->emergencyContacts->count() > 0)
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-phone-alt"></i> Emergency:</span>
                            <span class="info-value">{{ $owner->emergencyContacts->count() }} contact(s)</span>
                        </div>
                    @endif
                </div>

                <div class="card-actions">
                    <a href="{{ route('admin.pet-owners.show', $owner) }}" class="btn btn-secondary">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('admin.pet-owners.edit', $owner) }}" class="btn btn-secondary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('admin.pet-owners.destroy', $owner) }}" method="POST" style="flex: 1;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-secondary" style="width: 100%; background: #ff6b6b; color: white;" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
