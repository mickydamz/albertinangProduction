@extends('layouts.adminlayout')

@section('content')
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Edit Bank Account</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.bank.index') }}">Bank Accounts</a></li>
                                <li class="breadcrumb-item active">Edit Bank Account</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <a href="{{ route('admin.bank.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
        <div class="content-body">
            <!-- Edit Bank Account Form -->
            <section id="edit-bank-account-form" style="margin-top: 20px;">
                <div class="row pt-5">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Edit Bank Account</h4>
                            </div>
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ route('admin.bank.update', $bankAccount->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-3">
                                        <label for="name" class="form-label">Bank Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $bankAccount->name }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="account_number" class="form-label">Account Number</label>
                                        <input type="text" class="form-control" id="account_number" name="account_number" value="{{ $bankAccount->account_number }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="swift_code" class="form-label">SWIFT Code</label>
                                        <input type="text" class="form-control" id="swift_code" name="swift_code" value="{{ $bankAccount->swift_code }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="address" name="address" value="{{ $bankAccount->address }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="country_id" class="form-label">Country</label>
                                        <select class="form-select" id="country_id" name="country_id" required>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}" {{ $bankAccount->country_id == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="is_active" class="form-label">Is Active</label>
                                        <select class="form-select" id="is_active" name="is_active">
                                            <option value="1" {{ $bankAccount->is_active ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ !$bankAccount->is_active ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Update Bank Account</button>
                                    <a href="{{ route('admin.bank.index') }}" class="btn btn-secondary">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--/ Edit Bank Account Form -->
        </div>
    </div>
</div>
@endsection
