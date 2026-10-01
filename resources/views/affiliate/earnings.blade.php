<!-- resources/views/affiliate/earnings.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Your Earnings</h2>
        <p>You have earned: ${{ $earnings }}</p>
    </div>
@endsection
