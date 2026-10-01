@extends('layouts.app')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row"></div>
        <div class="content-body">
            <div class="container-fluid">
                <div class="supplier-profile">
                
                    <!-- Avatar and Supplier Info -->
                    <div class="avatar-container">
                        @if($supplier->avatar)
                            <img class="round" src="{{ Storage::url($supplier->avatar) }}" alt="avatar">
                        @else
                            <img class="round" src="\app-asset\images\portrait\small\e_avatar.png" alt="avatar">
                        @endif
                    </div>

                    <h1 class="supplier-name">
                        {{ $supplier->name }} 
                        @if ($supplier->status === 'green')
                            <i class="fas fa-check-circle verified-icon" title="Verified Supplier"></i>
                        @elseif ($supplier->status === 'banned')
                            <i class="fas fa-ban banned-icon" title="Banned"></i>
                        @endif
                    </h1>

                    <!-- Supplier Details -->
                    <div class="supplier-details">

                        @if(!$supplier->is_hidden)
    <p><strong>Email:</strong> {{ $supplier->email }}</p>
@endif

<p><strong>Location:</strong> {{ $supplier->city && $supplier->country ? $supplier->city . ', ' . $supplier->country : 'N/A' }}</p>
                    </div>

                    <div class="supplier-description">
                        <p ><strong></strong> {{ $supplier->description ?? 'No description provided.' }}</p>
                        </div>

                        <style>
                            /* Styling for Description Text */
.supplier-description {
    font-size: 1rem; /* Slightly larger text for readability */
    /* color: #444; Darker text for better contrast */
    line-height: 1.6; /* More spacing between lines for readability */
    margin-top: 1rem;
    padding: 10px; /* Padding to add space around the text */
    /* background-color: #f8f9fa; Light background to make it stand out */
    border-radius: 8px; /* Rounded corners */
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1); /* Light shadow for depth */
}

/* Desktop Adjustments for Description Only */
@media (min-width: 768px) {
    .supplier-description {
        max-width: 700px; /* Limit the width of the description text */
        margin: 0 auto; /* Center the description */
    }
}
</style>

                    

                    <!-- Action Buttons -->
                    <a href="{{ route('chat.show', ['supplierId' => $supplier->id]) }}" class="btn btn-primary mb-1 mt-1 ">Chat with Supplier</a>
                    <a href="{{ route('supplier.products.storefront', ['supplierId' => $supplier->id]) }}" class="btn btn-secondary ">Visit Distributor's Storefront</a>

                  
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* General Profile Styling */
.supplier-profile {
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    text-align: center;
}

.avatar-container {
    margin-bottom: 1.5rem;
}

.avatar-container img.round {
    border-radius: 50%;
    width: 80px;
    height: 80px;
}

.supplier-name {
    font-size: 2rem;
    color: #333;
    margin-bottom: .5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.verified-icon {
    color: #007bff;
    margin-left: 0.5rem;
    font-size: 1.2rem;
}

.banned-icon {
    color: red;
    margin-left: 0.5rem;
    font-size: 1.2rem;
}

.supplier-details p {
    font-size: 1rem;
    color: #555;
    margin: 0.5rem 0;
}

/* Reviews Section */
.reviews-section {
    margin-top: 2rem;
    text-align: left;
}

.section-title {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 1rem;
    text-align: center;
}

.no-reviews {
    color: #888;
    text-align: center;
}

.review-card {
    background-color: #f9f9f9;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.review-author {
    font-weight: bold;
    color: #333;
}

.review-date {
    color: #888;
    font-size: 0.875rem;
    margin-left: 0.5rem;
}

.review-content p {
    font-size: 1rem;
    color: #555;
    margin: 0.5rem 0;
}

.review-rating .fa-star {
    color: #ccc;
}

.review-rating .fa-star.filled {
    color: #FFD700;
}

@media (max-width: 768px) {
    .supplier-profile {
        padding: 1rem;
    }
    .avatar-container img {
        width: 70px;
        height: 70px;
    }
}
</style>

@endsection
