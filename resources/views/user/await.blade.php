@extends('layouts.app')

@section('title', __("Documents are being reviewed"))

@section('content')
<!-- Main content wrapper -->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">KYC Verification</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">dashboard</a></li>
                                <li class="breadcrumb-item active">Documents Review</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content body -->
        <div class="content-body mt-6">
            <div class="row">
                <div class="col-12">
                    <div class="card text-center">
                        <div class="card-body">
                            <!-- KYC Verification message -->
                            <div class="icon-box mb-4">
                                <i class="bx bx-check-circle font-large-3 text-warning"></i>
                            </div>
                            <h5 class="card-title">{{ __("Your Documents are being reviewed!") }}</h5>
                            <p class="card-text">{{ __("You will receive a verification text message or email once the documents have been reviewed. Thank you!") }}</p>

                            <!-- Additional actions or buttons can go here -->
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-primary me-2" onclick="window.location.href='{{ route('dashboard') }}'">
                                    <i class="bx bx-dashboard-alt"></i> Go to Dashboard
                                </button>
                                <!-- You can add other buttons here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-wrapper">
            <div class="container-fluid">
                <div class="footer-content d-flex justify-content-between">
                    <div class="footer-text">
                        &copy; 2022 {{ config('app.name') }}. All Rights Reserved.
                    </div>
                    <div class="footer-links">
                        <ul class="nav nav-sm">
                            <li class="nav-item dropup">
                                <a href="#" class="dropdown-toggle dropdown-indicator has-indicator nav-link" data-bs-toggle="dropdown" data-offset="0,10"><span>English</span></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="language-list">
                                        <li><a href="#" class="language-item">English</a></li>
                                        <li><a href="#" class="language-item">Español</a></li>
                                        <li><a href="#" class="language-item">Français</a></li>
                                        <li><a href="#" class="language-item">Türkçe</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#regionModal"><i class="bx bx-globe"></i><span class="ml-1">Select Region</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Select region modal -->
<div class="modal fade" id="regionModal" tabindex="-1" aria-labelledby="regionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <h5 class="modal-title" id="regionModalLabel">Select Your Country</h5>
                <ul class="list-unstyled country-list">
                    <li><a href="#" class="country-item"><img src="{{ asset('assets/images/flags/arg.png') }}" alt="Argentina" class="country-flag"> Argentina</a></li>
                    <li><a href="#" class="country-item"><img src="{{ asset('assets/images/flags/aus.png') }}" alt="Australia" class="country-flag"> Australia</a></li>
                    <li><a href="#" class="country-item"><img src="{{ asset('assets/images/flags/canada.png') }}" alt="Canada" class="country-flag"> Canada</a></li>
                    <li><a href="#" class="country-item"><img src="{{ asset('assets/images/flags/uk.png') }}" alt="United Kingdom" class="country-flag"> United Kingdom</a></li>
                    <li><a href="#" class="country-item"><img src="{{ asset('assets/images/flags/usa.png') }}" alt="United States" class="country-flag"> United States</a></li>
                    <!-- Add more countries as necessary -->
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
