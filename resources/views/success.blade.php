@extends('layouts.app')

@section('content')
    <!-- Begin app-content wrapper for Vuexy layout -->
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <!-- You can add your page header content here if needed -->
            </div>

            <div class="content-body">
                <div class="row justify-content-center ">
                    <!-- Thank You Section -->
                    <div class="col-md-6 col-lg-4">
                        <div class="text-center">
                            <div class="thank-you-card p-4 rounded shadow-sm">
                                <!-- Header Section -->
                                <h1 class="thank-you-title text-success font-weight-bold">Thank You for Your Order!</h1>
                                <p class="thank-you-message lead text-muted mt-3">
                                    We truly appreciate your business and are excited to get your order to you. If you have any questions or need assistance, don't hesitate to reach out.
                                </p>
                                
                                <!-- Email Section -->
                                <p class="mt-4">
                                    For any inquiries, email us at: 
                                    <a href="mailto:support@Albertina.com" class="text-decoration-none text-primary">
                                        support@Albertina.com
                                    </a>
                                </p>

                                <!-- Optional CTA -->
                                <div class="mt-5">
                                    <a href="/products" class="btn btn-outline-primary btn-lg">
                                        Return to Shop
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End app-content wrapper -->
@endsection

@push('styles')
<style>
    /* Additional custom styles */
    .thank-you-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem; /* Adjust size for larger title */
    }

    .thank-you-message {
        font-family: 'Lato', sans-serif;
        font-size: 1.125rem; /* Slightly larger body text for better readability */
    }

    .thank-you-card {
        background-color: #ffffff;
        border: 1px solid #e0e0e0;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .text-success {
        color: #28a745 !important;
    }

    .text-muted {
        color: #6c757d !important;
    }

    .text-primary {
        color: #007bff !important;
    }

    .btn-outline-primary {
        border-color: #007bff;
        color: #007bff;
    }

    .btn-outline-primary:hover {
        background-color: #007bff;
        color: white;
    }
</style>
@endpush
