@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
<div class="content-overlay"></div>
<div class="header-navbar-shadow"></div>
<div class="content-wrapper container-xxl p-0">
    <div class="content-header row">
        <div class="col-12 mb-2">
                    <h2 class="content-header-title mb-0">Edit Transaction</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <section id="edit-transaction">
            <div class="row mt-3">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Transaction Details</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.transactions.update', $transaction->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- User Selection -->
                                <div class="mb-1">
                                    <label for="user_id" class="form-label">User</label>
                                    <select id="user_id" name="user_id" class="form-select" required>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $user->id == $transaction->user_id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Total Amount -->
                                <div class="mb-1">
                                    <label for="total_amount" class="form-label">Total Amount</label>
                                    <input type="number" id="total_amount" name="total_amount" class="form-control" value="{{ old('total_amount', $transaction->total_amount) }}" required>
                                </div>

                                <!-- Escrow Amount -->
                                <div class="mb-1">
                                    <label for="escrow_amount" class="form-label">Escrow Amount</label>
                                    <input type="number" id="escrow_amount" name="escrow_amount" class="form-control" value="{{ old('escrow_amount', $transaction->escrow_amount) }}" required>
                                </div>

                                <!-- Payment Method -->
                                <div class="mb-1">
                                    <label for="payment_method_id" class="form-label">Payment Method</label>
                                    <select id="payment_method_id" name="payment_method_id" class="form-select" required>
                                        <option value="1" {{ $transaction->payment_method_id == 1 ? 'selected' : '' }}>Full Payment</option>
                                        <option value="2" {{ $transaction->payment_method_id == 2 ? 'selected' : '' }}>Half Payment</option>
                                        <option value="3" {{ $transaction->payment_method_id == 3 ? 'selected' : '' }}>Escrow Payment</option>
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="mb-1">
                                    <label for="status" class="form-label">Status</label>
                                    <select id="status" name="status" class="form-select" required>
                                        <option value="1" {{ $transaction->status == 1 ? 'selected' : '' }}>Complete</option>
                                        <option value="0" {{ $transaction->status == 0 ? 'selected' : '' }}>Pending</option>
                                    </select>
                                </div>

                                <!-- Date -->
                                <div class="mb-1">
                                    <label for="created_at" class="form-label">Date</label>
                                    <input type="datetime-local" id="created_at" name="created_at" class="form-control" value="{{ $transaction->created_at->format('Y-m-d\TH:i') }}" required>
                                </div>

                                <!-- Buttons -->
                                <div class="mb-1">
                                    <button type="submit" class="btn btn-primary">Update Transaction</button>
                                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
</div>
@endsection
