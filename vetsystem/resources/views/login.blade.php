@extends('layout.base')

@section('content')
<div class="page-bg">
    <div class="page-card">
        <h3>Login</h3>
        <form action="/login-success" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Username:</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror" name="username">
                @error('username')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Password:</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password">
                @error('password')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="d-grid">
                <button class="btn btn-primary">Login</button>
                <a href="/register" class="btn btn-secondary">Register</a>
            </div>
        </form>
    </div>
</div>
@endsection
