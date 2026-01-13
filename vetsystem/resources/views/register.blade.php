@extends('layout.base')

@section('content')
<div class="page-bg">
    <div class="page-card">
        <h3>Register</h3>
        <p>Vet Clinic Registration</p>

        <form action="/register/create" method="POST">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label">Last Name*</label>
                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}">
                    @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-6">
                    <label class="form-label">First Name*</label>
                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}">
                    @error('first_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label">Age*</label>
                    <input type="number" class="form-control @error('age') is-invalid @enderror" name="age" value="{{ old('age') }}">
                    @error('age') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-6">
                    <label class="form-label">Contact Number*</label>
                    <input type="tel" class="form-control @error('contact_number') is-invalid @enderror" name="contact_number" value="{{ old('contact_number') }}">
                    @error('contact_number') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Username*</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}">
                @error('username') <span class="text-danger">{{ $message }}</span> @enderror

            </div>
            
            <div class="mb-3">
                 <label class="form-label">Password*</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password">
            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
           

            <div class="d-grid mt-3">
                <button class="btn btn-primary">Register</button>
                <a href="/login" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>
@endsection
