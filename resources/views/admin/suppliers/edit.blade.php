@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="col-12 mb-2">
                        <h2 class="content-header-title mb-0">Edit Supplier Status</h2>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.suppliers.index') }}">Suppliers</a></li>
                                <li class="breadcrumb-item active">Edit Status</li>
                            </ol>
            </div>
        </div>
        <div class="content-body">
            <section id="edit-supplier-status">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">Edit Status for {{ $supplier->name }}</div>
                            <div class="card-body">

                                <form action="{{ route('admin.suppliers.update', $supplier->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status:</label>
                                        <select name="status" id="status" class="form-select" required>
                                            <option value="green" {{ $supplier->status == 'green' ? 'selected' : '' }}>Green</option>
                                            <option value="yellow" {{ $supplier->status == 'yellow' ? 'selected' : '' }}>Yellow</option>
                                            <option value="banned" {{ $supplier->status == 'banned' ? 'selected' : '' }}>Banned</option>
                                        </select>
                                    </div>

                                    <div class="mb-1">
                                        <label for="account_balance" class="form-label">Account Balance:</label>
                                        <input type="number" name="account_balance" id="account_balance" class="form-control" value="{{ old('account_balance', $supplier->account_balance) }}" min="0" step="0.01">
                                    </div>

                                    <button type="submit" class="btn btn-primary">Update Status</button>
                                </form>

                                <div class="mt-3">
                                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Back to Suppliers List</a>
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
