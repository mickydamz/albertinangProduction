@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Fund Account</h1>
    <form action="{{ route('user.fundAccount') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" name="amount" id="amount" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Fund Account</button>
    </form>
</div>
@endsection
