@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Select Bank Account</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.bank.submit') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="bank_account">Select Bank Account</label>
                <select name="bank_account_id" id="bank_account" class="form-control" required>
                    <option value="">-- Select Bank Account --</option>
                    @foreach ($bankAccounts as $bankAccount)
                        <option value="{{ $bankAccount->id }}">{{ $bankAccount->bank_name }} - {{ $bankAccount->account_number }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Complete Checkout</button>
        </form>
    </div>
@endsection
