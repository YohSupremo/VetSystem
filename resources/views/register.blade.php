@extends('layout.base')

@php($bodyClass = 'auth-body')

@section('content')
<div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
</div>

<div class="page-container">
    <div class="auth-card">
        <div class="logo-section">
            <div class="paw-icon">🐾</div>
            <h2>Join PawCare</h2>
            <p>Create your account to get started</p>
        </div>

        <form action="/register/create" method="POST">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label" for="first_name">First Name*</label>
                    <input
                        id="first_name"
                        type="text"
                        class="form-control @error('first_name') is-invalid @enderror"
                        name="first_name"
                        value="{{ old('first_name') }}"
                        placeholder="First name"
                    >
                    @error('first_name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-6">
                    <label class="form-label" for="last_name">Last Name*</label>
                    <input
                        id="last_name"
                        type="text"
                        class="form-control @error('last_name') is-invalid @enderror"
                        name="last_name"
                        value="{{ old('last_name') }}"
                        placeholder="Last name"
                    >
                    @error('last_name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label" for="address">Address*</label>
                    <textarea
                        id="address"
                        rows="2"
                        class="form-control @error('address') is-invalid @enderror"
                        name="address"
                        placeholder="Enter your address"
                    >{{ old('address') }}</textarea>
                    @error('address')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label" for="contact_number">Contact Number*</label>
                    <input
                        id="contact_number"
                        type="tel"
                        class="form-control @error('contact_number') is-invalid @enderror"
                        name="contact_number"
                        value="{{ old('contact_number') }}"
                        placeholder="Phone number"
                    >
                    @error('contact_number')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-6">
                    <label class="form-label" for="email">Email*</label>
                    <input
                        id="email"
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Email address"
                    >
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="username">Username*</label>
                <input
                    id="username"
                    type="text"
                    class="form-control @error('username') is-invalid @enderror"
                    name="username"
                    value="{{ old('username') }}"
                    placeholder="Choose a username"
                >
                @error('username')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label" for="password">Password*</label>
                <input
                    id="password"
                    type="password"
                    class="form-control @error('password') is-invalid @enderror"
                    name="password"
                    placeholder="Create a password"
                >
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="d-grid gap-3">
                <button type="submit" class="btn btn-primary">Register</button>
                <a href="/login" class="btn btn-secondary">Already have an account?</a>
            </div>
        </form>

        <div class="back-to-home">
            <a href="/">&larr; Back to Home</a>
        </div>
    </div>
</div>
@endsection
