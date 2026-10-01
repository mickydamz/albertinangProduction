@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="col-12 mb-2">
                        <h2 class="content-header-title mb-0">Add New Transaction</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <section id="add-transaction">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">Transaction Details</div>
                            <div class="card-body">
                                <form action="{{ route('admin.transactions.store') }}" method="POST">
                                    @csrf

                                    <div class="mb-1">
                                        <label for="user_id" class="form-label">User</label>
                                        <select name="user_id" class="form-select" required>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-1">
                                        <label for="total_amount" class="form-label">Total Amount</label>
                                        <input type="number" name="total_amount" class="form-control" step="0.01" required>
                                    </div>

                                    <div class="mb-1">
                                        <label for="escrow_amount" class="form-label">Escrow Amount</label>
                                        <input type="number" name="escrow_amount" class="form-control" step="0.01" required>
                                    </div>

                                    <div class="mb-1">
                                        <label for="status" class="form-label">Status</label>
                                        <select name="status" class="form-select" required>
                                            <option value="1">Complete</option>
                                            <option value="0">Pending</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Create Transaction</button>
                                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">Cancel</a>
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
