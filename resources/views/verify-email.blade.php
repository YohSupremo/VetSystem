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
            <div class="paw-icon">MAIL</div>
            <h2>Verify Your Email</h2>
            <p>Check your inbox and click the confirmation link to activate your account.</p>
        </div>

        @include('partials.flash-messages', [
            'containerClass' => 'app-flash-inline app-flash-themed',
        ])

        <div class="mb-3 text-center">
            @if(!empty($email))
                <small class="text-muted">Verification email sent to: <strong>{{ $email }}</strong></small>
            @else
                <small class="text-muted">If your account was created, a verification link has been sent.</small>
            @endif
        </div>

        <form action="{{ route('verification.send') }}" method="POST" class="d-grid gap-3 mb-3">
            @csrf
            <div class="text-start">
                <label for="email" class="form-label">Email Address</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $email) }}"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="Enter your registered email"
                    required
                >
            </div>
            <button type="submit" class="btn btn-primary">Resend Verification Email</button>
            <a href="{{ route('login') }}" class="btn btn-secondary">Go to Login</a>
        </form>

        @error('email')
            <div class="text-danger text-center mb-2">{{ $message }}</div>
        @enderror

        @if(session('verification_link'))
            <div class="alert alert-warning mt-2">
                <div class="mb-2"><strong>Mail fallback:</strong> use this secure link to verify now.</div>
                <a href="{{ session('verification_link') }}" class="btn btn-sm btn-outline-dark">Verify Now</a>
            </div>
        @endif

        <div class="back-to-home text-center">
            <a href="/">&larr; Back to Home</a>
        </div>
    </div>
</div>
@endsection
