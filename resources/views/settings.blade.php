@extends('layouts.app')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <!-- Header Section with Logout Button -->
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title text-primary float-start mb-0" style="color: #C68E17;">Account Settings</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row justify-content-center">
                <!-- Main Content Area -->
                <div class="col-md-8 col-12">
                    <!-- Content Area for Settings -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Account Settings</h4>
                        </div>
                        <div class="card-body">
                            <!-- Subtitle Section -->
                            <h5 class="subtitle">
                                Here you can update your settings and manage your preferences
                            </h5>

                            <div class="list-group">
                                <!-- General Settings Section -->
                                <div class="section">
                                    <h6 class="section-title">General Settings</h6>
                                    <a href="page-account-settings-account" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-3 py-2 bg-light rounded shadow-sm mb-2 hover-custom">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-cogs font-medium-3 me-2"></i>
                                            <span class="align-middle">General Settings</span>
                                        </div>
                                        <span class="badge badge-pill badge-light rounded-circle gold-arrow">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                                <path d="M9 18l6-6-6-6"></path>
                                            </svg>
                                        </span>
                                    </a>
                                    <a href="twofactor" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-3 py-2 bg-light rounded shadow-sm mb-2 hover-custom">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-lock font-medium-3 me-2"></i>
                                            <span class="align-middle">Enable 2FA</span>
                                        </div>
                                        <span class="badge badge-pill badge-light rounded-circle gold-arrow">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                                <path d="M9 18l6-6-6-6"></path>
                                            </svg>
                                        </span>
                                    </a>
                                    <a href="page-account-settings-security" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-3 py-2 bg-light rounded shadow-sm mb-2 hover-custom">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-eye font-medium-3 me-2"></i>
                                            <span class="align-middle"> Change password</span>
                                        </div>
                                        <span class="badge badge-pill badge-light rounded-circle gold-arrow">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                                <path d="M9 18l6-6-6-6"></path>
                                            </svg>
                                        </span>
                                    </a>
                                </div>

                                 <!--Account Settings Section -->
                                <div class="section">
    <h6 class="section-title">Support Tickets</h6>
    <a href="/tickets/create" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-3 py-2 bg-light rounded shadow-sm mb-2 hover-custom">
        <div class="d-flex align-items-center">
            <i class="fas fa-ticket-alt font-medium-3 me-2"></i>
            <span class="align-middle">Create Support Ticket</span>
        </div>
        <span class="badge badge-pill badge-light rounded-circle gold-arrow">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                <path d="M9 18l6-6-6-6"></path>
            </svg>
        </span>
    </a>
</div>


                                <!-- Orders Settings Section -->
                                <div class="section">
                                    <h6 class="section-title">My Transactions</h6>
                                    <a href="/transactions" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-3 py-2 bg-light rounded shadow-sm mb-2 hover-custom">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-box font-medium-3 me-2"></i>
                                            <span class="align-middle">View Transactions</span>
                                        </div>
                                        <span class="badge badge-pill badge-light rounded-circle gold-arrow">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                                <path d="M9 18l6-6-6-6"></path>
                                            </svg>
                                        </span>
                                    </a>
                                    <a href="/my-deposits" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-3 py-2 bg-light rounded shadow-sm mb-2 hover-custom">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-sync-alt font-medium-3 me-2"></i>
                                            <span class="align-middle">Deposit History</span>
                                        </div>
                                        <span class="badge badge-pill badge-light rounded-circle gold-arrow">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                                <path d="M9 18l6-6-6-6"></path>
                                            </svg>
                                        </span>
                                    </a>
                                </div>

                                <!-- Additional Settings can be added below -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .subtitle {
    font-size: 12px;
    font-weight: 200;
    color: #555;
    margin-bottom: 20px;
}

.section-title {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 15px;
}

.gold-arrow {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 4px; /* Reduced padding to make the circle smaller */
    border-radius: 50%;
    background-color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.gold-arrow svg {
    stroke: #C68E17;
    fill: none;
    width: 16px; /* Reduced the width of the arrow icon */
    height: 16px; /* Reduced the height of the arrow icon */
}

.gold-arrow:hover svg {
    stroke: #D9A31A;
}

.hover-custom:hover {
    background-color: #F9F9F9;
}


.list-group-item .badge {
    margin-left: auto;
}

</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


@endsection
