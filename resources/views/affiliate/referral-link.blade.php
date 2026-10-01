<!-- resources/views/affiliate/dashboard.blade.php -->

@extends('layouts.app')

@section('content')

      <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Account</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Account Settings </a>
                                    </li>
                                    <li class="breadcrumb-item active"> Account
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
    <div class="container">
        <h2>Generate Your Referral Link</h2>
        <p>Your unique referral link:</p>
        <a href="{{ $referralLink }}" target="_blank">{{ $referralLink }}</a>
    </div>
@endsection
