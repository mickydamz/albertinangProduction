@extends('layouts.app')

@section('content')
<div class="app-content content ecommerce-application">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="content-header-left col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Distributor Details</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('distributors.index') }}">Distributors</a></li>
                                <li class="breadcrumb-item active">{{ $distributor->name }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-detached content-right">
            <div class="content-body">
                <h4 class="section-title mb-4">{{ $distributor->name }}</h4>
                <p><strong>Email:</strong> {{ $distributor->email }}</p>
                <p><strong>Role:</strong> {{ $distributor->role }}</p>
                <p><strong>Products:</strong></p>
                <ul>
                    @foreach($distributor->products as $product)
                        <li>{{ $product->name }}</li>
                    @endforeach
                </ul>
                <a href="{{ route('distributors.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
