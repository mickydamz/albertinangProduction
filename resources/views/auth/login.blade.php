@extends('layouts/authlayout')

@section('title', 'Sign In — AlbertinaNG')

@section('content')

    <h4>Sign In 🚀</h4>
    <p class="subtitle">Welcome back! Please sign in to continue.</p>

    {{-- Error messages --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Oops! Something went wrong</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <label for="email">Email Address</label>
            <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                placeholder="you@example.com"
                value="{{ old('email') }}"
                required
                autocomplete="email"
            >
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label for="password">Password</label>
            <div class="pwd-wrap">
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                    autocomplete="current-password"
                >
                <button type="button" class="pwd-toggle" onclick="togglePassword()" aria-label="Toggle password">
                    <i class="fas fa-eye" id="pwdEyeIcon"></i>
                </button>
            </div>
        </div>

        {{-- Remember me + Forgot password --}}
        <div class="form-row">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember Me</label>
            </div>
            <a href="{{ route('password.request') }}" class="form-forgot">Forgot password?</a>
        </div>

        <button type="submit" class="btn-primary">Sign In</button>
    </form>

    <div class="auth-divider"><span>Don't have an account?</span></div>

    <p class="auth-card__foot">
        <a href="{{ route('register') }}">Create a free account →</a>
    </p>

@endsection