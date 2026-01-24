@extends('admin.dashboard')

@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-cog"></i> System Settings</h1>
        <p>Configure clinic settings and preferences</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.settings.store') }}" method="POST">
            @csrf
            
            <div class="form-group mb-3">
                <label for="clinic_name">Clinic Name</label>
                <input type="text" class="form-control" id="clinic_name" name="clinic_name" placeholder="Enter clinic name">
            </div>

            <div class="form-group mb-3">
                <label for="clinic_email">Clinic Email</label>
                <input type="email" class="form-control" id="clinic_email" name="clinic_email" placeholder="Enter clinic email">
            </div>

            <div class="form-group mb-3">
                <label for="clinic_phone">Clinic Phone</label>
                <input type="text" class="form-control" id="clinic_phone" name="clinic_phone" placeholder="Enter clinic phone">
            </div>

            <div class="form-group mb-3">
                <label for="clinic_address">Clinic Address</label>
                <textarea class="form-control" id="clinic_address" name="clinic_address" rows="3" placeholder="Enter clinic address"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>
@endsection
