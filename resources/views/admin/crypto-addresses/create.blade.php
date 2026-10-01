@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Add New Crypto Address</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">Add Crypto Address</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <a href="{{ route('admin.crypto-addresses.index') }}" class="btn btn-primary">Back to List</a>
            </div>
        </div>
        <div class="content-body">
            <section id="add-crypto-address">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">New Crypto Address Form</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.crypto-addresses.store') }}" method="POST">
                                    @csrf

                                    <div class="form-group mb-2 mt-2">
                                        <label for="currency">Currency</label>
                                        <input type="text" name="currency" id="currency" class="form-control" required>
                                    </div>

                                    <!-- <div class="form-group mb-2">
                                        <label for="amount">Amount</label>
                                        <input type="number" name="amount" id="amount" class="form-control" required>
                                    </div> -->

                                    <div class="form-group mb-2">
                                        <label for="address">Address</label>
                                        <input type="text" name="address" id="address" class="form-control" required>
                                    </div>

                                    <button type="submit" class="btn btn-success">Create Crypto Address</button>
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
