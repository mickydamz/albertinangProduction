@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="col-12 mb-2">
                        <h2 class="content-header-title mb-0">Transaction Details</h2>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.transactions.index') }}">Transactions</a></li>
                                <li class="breadcrumb-item active">Details</li>
                            </ol>
            </div>
        </div>

        <div class="content-body">
            <section id="transaction-details">
                <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mb-0">Transaction Overview</h4>
                            </div>
                            <div class="card-body">
                                <h5>User: <span class="text-muted">{{ $transaction->user->name }}</span></h5>
                                <h5>Total Amount: <span class="text-success">${{ number_format($transaction->total_amount, 2) }}</span></h5>
                                <h5>Escrow Amount: <span class="text-success">${{ number_format($transaction->escrow_amount, 2) }}</span></h5>
                                <h5>Status: <span class="badge bg-{{ $transaction->status == 1 ? 'success' : 'warning' }}">
                                    {{ $transaction->status == 1 ? 'Complete' : 'Pending' }}
                                </span></h5>
                                <h5>Date: <span class="text-muted">{{ $transaction->created_at->format('Y-m-d H:i') }}</span></h5>

                                <h5 class="mt-4">Products</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transaction->products as $product)
                                                <tr>
                                                    <td>{{ $product->name }}</td>
                                                    <td>{{ $product->pivot->quantity }}</td>
                                                    <td>${{ number_format($product->pivot->price, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">Back to Transactions</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
