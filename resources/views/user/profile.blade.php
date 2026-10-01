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
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                  
                </div>
            </div>
            <div class="content-body">


         
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .progress-bar-container {
            width: 100%;
            background-color: #f3f3f3;
            border-radius: 25px;
            height: 25px;
            margin-top: 20px;
        }

        .progress-bar {
            height: 100%;
            width: 0%;
            background-color: #4CAF50;
            text-align: center;
            line-height: 25px;
            border-radius: 25px;
            color: white;
            font-weight: bold;
        }

        .completion-percentage-text {
            margin-top: 10px;
            font-weight: bold;
        }
    </style>




                <div class="row">
                    <div class="col-12">
                        <ul class="nav nav-pills mb-2">
                            <!-- account -->
                            <li class="nav-item">
                                <a class="nav-link active" href="page-account-settings-account">
                                    <i data-feather="user" class="font-medium-3 me-50"></i>
                                    <span class="fw-bold">Account</span>
                                </a>
                            </li>
                            <!-- security -->
                            <li class="nav-item">
                                <a class="nav-link" href="page-account-settings-security">
                                    <i data-feather="lock" class="font-medium-3 me-50"></i>
                                    <span class="fw-bold">Security</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="twofactor">
                                    <i data-feather="lock" class="font-medium-3 me-50"></i>
                                    <span class="fw-bold">2FA</span>
                                </a>
                            </li>
                          
                        </ul>

                        <!-- profile -->
                        <div class="card smart-bg">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Profile Details</h4>

                                 <!-- Display the profile completion progress -->
 <div class="progress-bar-container">
                        <div class="progress-bar" style="width: {{ $completionPercentage }}%">
                            {{ number_format($completionPercentage, 2) }}% Complete
                        </div>
                    </div>
                            </div>
                            <div class="card-body py-2 my-25">
                                <!-- header section -->
                                <div class="d-flex">
                                    <a href="#" class="me-25">
                                    @if(auth()->user()->avatar)
    <div class="mt-3">
        <!-- <label>Current Avatar:</label> -->
        <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="User Avatar" class="img-fluid" style="max-width: 150px; max-height: 150px;">
    </div>
@else
    <div class="mt-3">
<img src="\app-asset\images\portrait\small\e_avatar.png" alt="User Avatar" class="img-fluid" style="max-width: 150px; max-height: 150px;">
    </div>
@endif   
                                    </a>


                                    
                                    <!-- upload and reset button -->
                                    <livewire:user-avatar />
                                    <!--/ upload and reset button -->
                                </div>
                                <!--/ header section -->

                               <livewire:normal-user-update />
                            </div>
                        </div>

                        <!-- deactivate account  -->
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Delete Account</h4>
                            </div>
                            <div class="card-body py-2 my-25">
                                <div class="alert alert-warning">
                                    <h4 class="alert-heading">Are you sure you want to delete your account?</h4>
                                    <div class="alert-body fw-normal">
                                        Once you delete your account, there is no going back. Please be certain.
                                    </div>
                                </div>

                                <form id="formAccountDeactivation" class="validate-form" onsubmit="return false">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="accountActivation" id="accountActivation" data-msg="Please confirm you want to delete account" />
                                        <label class="form-check-label font-small-3" for="accountActivation">
                                            I confirm my account deactivation
                                        </label>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-danger deactivate-account mt-1">Deactivate Account</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!--/ profile -->
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END: Content-->

    
    <script src="{{ asset('/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('/app-assets/vendors/js/forms/cleave/cleave.min.js') }}"></script>
    <script src="{{ asset('/app-assets/vendors/js/forms/cleave/addons/cleave-phone.us.js') }}"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{ asset('/app-assets/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('/app-assets/js/core/app.js') }}"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="{{ asset('/app-assets/js/scripts/pages/page-account-settings-account.js') }}"></script>
    <!-- END: Page JS-->

    @endsection