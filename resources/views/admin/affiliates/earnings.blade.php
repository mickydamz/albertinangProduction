@extends('layouts.adminlayout')

@section('content')

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="col-12 mb-2">
                        <h2 class="content-header-title mb-0">Manage Affiliates</h2>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.affiliates.index') }}">Affiliates</a></li>
                                <li class="breadcrumb-item active">Manage Affiliates</li>
                            </ol>
            </div>
            
        </div>

    <h1>Earnings for Affiliate: {{ $affiliate->name }}</h1>

    <p>Total Earnings: ${{ $earnings }}</p>

    <a href="{{ route('admin.affiliates.index') }}" class="btn btn-primary">Back to All Affiliates</a>
@endsection
