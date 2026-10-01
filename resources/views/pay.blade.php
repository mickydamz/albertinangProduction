@php
$months = ['1' => 'Jan', '2' => 'Feb', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'Jun', '7' => 'July', '8' => 'Aug', '9' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'];
$prefilledData = [
    'owner' => 'James Justin',
    'cvv' => '123',
    'cardNumber' => '4111111111111111', // Example card number (Visa)
    'amount' => '100.00',
    'expiration-month' => '1', // Jan
    'expiration-year' => date('Y') + 2, // Set expiration year to 2 years ahead
];
@endphp

@extends('layouts.app')

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <!-- You can add your page header content here if needed -->
        </div>

        <div class="content-body">
            <div class="row mt-5 mb-5 justify-content-center">
                <div class="col-lg-6 col-md-8 col-sm-12">
                    <div class="card shadow-lg border-0 rounded">
                        <div class="card-body">
                            <div class="text-center">
                                <h2 class="font-weight-bold mb-4">Deposit</h2>
                            </div>

                            <!-- Success and Error messages -->
                            @if(session('success_msg'))
                                <div class="alert alert-success">{{ session('success_msg') }}</div>
                            @endif
                            @if(session('error_msg'))
                                <div class="alert alert-danger">{{ session('error_msg') }}</div>
                            @endif

                            <form method="post" action="{{ route('dopay.online') }}">
                                @csrf

                                <div class="form-group row">
                                    <label for="owner" class="col-md-4 col-form-label text-md-right">Owner</label>
                                    <div class="col-md-8">
                                        <input type="text" id="owner" name="owner" class="form-control" value="{{ $prefilledData['owner'] }}" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="cvv" class="col-md-4 col-form-label text-md-right">CVV</label>
                                    <div class="col-md-8">
                                        <input type="number" id="cvv" name="cvv" class="form-control" value="{{ $prefilledData['cvv'] }}" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="cardNumber" class="col-md-4 col-form-label text-md-right">Card Number</label>
                                    <div class="col-md-8">
                                        <input type="text" id="cardNumber" name="cardNumber" class="form-control" value="{{ $prefilledData['cardNumber'] }}" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="amount" class="col-md-4 col-form-label text-md-right">Amount</label>
                                    <div class="col-md-8">
                                        <input type="number" id="amount" name="amount" class="form-control" value="{{ $prefilledData['amount'] }}" required>
                                    </div>
                                </div>

                                <!-- Expiration Month Field -->
                                <div class="form-group row">
                                    <label for="expiration-month" class="col-md-4 col-form-label text-md-right">Exp Month</label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="expiration-month" name="expiration-month">
                                            @foreach($months as $k => $v)
                                                <option value="{{ $k }}" {{ $prefilledData['expiration-month'] == $k ? 'selected' : '' }}>{{ $v }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Expiration Year Field -->
                                <div class="form-group row">
                                    <label for="expiration-year" class="col-md-4 col-form-label text-md-right">Exp Year</label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="expiration-year" name="expiration-year">
                                            @for($i = date('Y'); $i <= (date('Y') + 15); $i++)
                                                <option value="{{ $i }}" {{ $prefilledData['expiration-year'] == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row justify-content-center">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-block">Make Payment</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
