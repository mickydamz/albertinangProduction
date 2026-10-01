@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="col-12 mb-2">
                        <h2 class="content-header-title mb-0">Edit Payment Method</h2>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.payment-methods.index') }}">Payment Methods</a></li>
                                <li class="breadcrumb-item active">Edit</li>
                            </ol>
            </div>
        </div>
        <div class="content-body">
            <section id="edit-payment-method">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
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

                                <form method="POST" action="{{ route('admin.payment-methods.update', $paymentMethod->id) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-3">
                                        <label for="name" class="form-label">Payment Method Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $paymentMethod->name) }}" required>
                                    </div>

                                    <!-- <div class="mb-3">
                                        <label for="details" class="form-label">Details</label>
                                        <textarea class="form-control" id="details" name="details" required>{{ old('details', $paymentMethod->details) }}</textarea>
                                    </div> -->

                                    <button type="submit" class="btn btn-primary">Update</button>
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
