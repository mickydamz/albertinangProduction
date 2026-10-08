@extends('layouts.authlayout')

@section('title', 'Confirm Password — AlbertinaNG')

@section('content')

    <h4>Confirm Password 🔒</h4>
    <p class="subtitle">Please confirm your password before continuing.</p>

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

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

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
                    autofocus
                    autocomplete="current-password"
                >
                <button type="button" class="pwd-toggle" onclick="togglePassword()" aria-label="Toggle password">
                    <i class="fas fa-eye" id="pwdEyeIcon"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-primary">Confirm Password</button>
    </form>

    @if(Route::has('password.request'))
        <div class="auth-divider"><span>Trouble signing in?</span></div>
        <p class="auth-card__foot">
            <a href="{{ route('password.request') }}">Forgot your password? →</a>
        </p>
    @endif

@endsection