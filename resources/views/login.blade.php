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
            <h2>Welcome Back</h2>
            <p>Login to access your PawCare account</p>
        </div>

        <form action="/login-success" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label" for="username">Username</label>
                <input
                    id="username"
                    type="text"
                    name="username"
                    class="form-control @error('username') is-invalid @enderror"
                    placeholder="Enter your username"
                    value="{{ old('username') }}"
                >
                @error('username')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label" for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Enter your password"
                >
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="d-grid gap-3">
                <button type="submit" class="btn btn-primary">Login</button>
                <a href="/register" class="btn btn-secondary">Create Account</a>
            </div>
        </form>

        <div class="back-to-home">
            <a href="/">&larr; Back to Home</a>
        </div>
    </div>
</div>
@endsection
