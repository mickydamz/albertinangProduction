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

    <h1 class="mb-2">Dashboard for Affiliate - {{ $affiliate->name }}</h1>

    <!-- Display referral count -->
    <p class="mb-2">People {{ $affiliate->name }} has personally referred: {{ $referrals->count() }} person</p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Date Joined</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($referrals as $referral)
                <tr>
                    <td>{{ $referral->name }}</td>
                    <td>{{ $referral->email }}</td>
                    <td>{{ $referral->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- <a href="{{ route('admin.affiliates.earnings', $affiliate->id) }}" class="btn btn-success">View Earnings</a> -->
@endsection
