@extends('layouts.authlayout')

@section('title', 'Reset Password — AlbertinaNG')

@section('content')

    <h4>Reset Password 🔒</h4>
    <p class="subtitle">Your new password must be different from previously used passwords.</p>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Oops! Something went wrong</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Email --}}
        <div class="form-group">
            <label for="email">Email Address</label>
            <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                placeholder="you@example.com"
                value="{{ $email ?? old('email') }}"
                required
                autofocus
                autocomplete="email"
            >
        </div>

        {{-- New Password --}}
        <div class="form-group">
            <label for="password">New Password</label>
            <div class="pwd-wrap">
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="Enter new password"
                    required
                    autocomplete="new-password"
                >
                <button type="button" class="pwd-toggle" onclick="togglePassword('password','pwdEyeIcon')" aria-label="Toggle password">
                    <i class="fas fa-eye" id="pwdEyeIcon"></i>
                </button>
            </div>
        </div>

        {{-- Confirm Password --}}
        <div class="form-group">
            <label for="password_confirmation">Confirm New Password</label>
            <div class="pwd-wrap">
                <input
                    type="password"
                    class="form-control"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Repeat new password"
                    required
                    autocomplete="new-password"
                >
                <button type="button" class="pwd-toggle" onclick="togglePassword('password_confirmation','pwdEyeIcon2')" aria-label="Toggle confirm password">
                    <i class="fas fa-eye" id="pwdEyeIcon2"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-primary">Set New Password</button>
    </form>

    <div class="auth-divider"><span>Back to sign in?</span></div>

    <p class="auth-card__foot">
        <a href="{{ route('login') }}">← Return to login</a>
    </p>

@endsection