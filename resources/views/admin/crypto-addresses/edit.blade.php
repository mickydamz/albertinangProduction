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
                        <h2 class="content-header-title float-start mb-0">Edit Crypto Address</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">Edit Crypto Address</li>
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
            <section id="edit-crypto-address">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Edit Crypto Address Form</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.crypto-addresses.update', $cryptoAddress->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group mb-2 mt-2">
                                        <label for="currency">Currency</label>
                                        <input type="text" name="currency" id="currency" class="form-control" value="{{ $cryptoAddress->currency }}" required>
                                    </div>

                                

                                    <div class="form-group mb-2">
                                        <label for="address">Address</label>
                                        <input type="text" name="address" id="address" class="form-control" value="{{ $cryptoAddress->address }}" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Update Crypto Address</button>
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
