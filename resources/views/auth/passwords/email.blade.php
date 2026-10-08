@extends('layouts.authlayout')

@section('title', 'Forgot Password — AlbertinaNG')

@section('content')

    <h4>Forgot Password? 🔒</h4>
    <p class="subtitle">Enter your email and we'll send you a link to reset your password.</p>

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

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

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
                autofocus
                autocomplete="email"
            >
        </div>

        <button type="submit" class="btn-primary">Send Reset Link</button>
    </form>

    <div class="auth-divider"><span>Remembered it?</span></div>

    <p class="auth-card__foot">
        <a href="{{ route('login') }}">← Back to sign in</a>
    </p>

@endsection