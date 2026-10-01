@extends('layouts.app')

@section('content')
<div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
            </div>
            <div class="content-body">
<div class="ecommerce-application">

    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h1 class="page-title">Welcome, {{ auth()->user()->name }}</h1>
            </div>
            <div class="content-header-right col-md-3 d-md-block d-none text-end">
                <!-- Placeholder for future buttons or actions -->
            </div>
        </div>

        <style>
            /* Centralized row styling */
            .row {
                padding: 0 3rem;
            }

            /* Welcome Section Styles */
            .welcome-section {
                background-color: #f8f9fa;
                padding: 2rem;
                border-radius: 8px;
                text-align: center;
                margin-bottom: 2rem;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .welcome-section h1 {
                font-size: 2.5rem;
                color: #007bff;
            }

            /* Section Separator Styles */
            .section-separator {
                background-image: url('https://plus.unsplash.com/premium_photo-1670270203164-aa65468a9c67?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpbmtzfGVufDB8fDB8fHww');
                background-repeat: no-repeat;
                background-size: cover;
                background-position: center;
                padding: 2rem;
                border-radius: 8px;
                margin: 2rem 0;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                text-align: center;
                position: relative; /* Position for overlay */
                color: white; /* Text color for better contrast */
            }

            .section-separator::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5); /* Overlay for contrast */
                z-index: 1;
            }

            .section-separator .separator-content {
                position: relative; /* To bring the text above the overlay */
                z-index: 2; /* Ensures text is above the overlay */
            }

            /* Supplier Card Styles */
            .newcard {
                border: 1px solid #e0e0e0;
                border-radius: 10px;
                background-color: #fff;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                margin: 1rem;
            }

            .newcard:hover {
                transform: scale(1.05);
                box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
                border: 1px solid #007bff; /* Highlight on hover */
            }

            .newcard .card-header {
                display: flex;
                align-items: center;
            }

            .newcard .avatar {
                margin-left: auto; /* Align avatar to the right */
            }

            /* Optional: Adjust text color for better contrast */
            .newcard h2 {
                color: #333;
            }

            .newcard p {
                color: #555; /* Slightly lighter text */
            }

            /* Browse by section styles */
            .browse-by-section {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                margin-bottom: 2rem;
                background-color: #e0f7fa;
                padding: 1.5rem;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .category-card {
                width: 150px;
                text-align: center;
                margin: 1rem;
                transition: transform 0.3s ease;
            }

            .category-card:hover {
                transform: scale(1.1);
            }

            .category-card img {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                object-fit: cover;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }

            .category-name {
                font-weight: bold;
                margin-top: 0.5rem;
                color: #333;
            }

            /* E-commerce Card Styles */
            .ecommerce-card {
                border: 1px solid #e0e0e0;
                border-radius: 10px;
                background-color: #fff;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                margin: 1rem;
            }

            .ecommerce-card:hover {
                transform: scale(1.05);
                box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
                border: 1px solid #007bff;
            }

            .ecommerce-card .item-img img {
                width: 100%;
                height: auto;
                border-radius: 8px;
                object-fit: cover;
            }

            /* Button Styles */
            .btn-primary {
                background-color: #007bff;
                border: none;
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 5px;
                text-align: center;
            }

            .btn-primary:hover {
                background-color: #0056b3;
            }
        </style>

        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Welcome to Our Albertina</h1>
            <p>Explore a wide range of drinks tailored just for you.</p>
        </div>

        <!-- Supplier Section -->
        <div class="row mb-3">
            @foreach($suppliers as $supplier)
                <div class="col-lg-4 col-sm-6 col-12">
               
                    <div class="card newcard">
                    <a href="{{ route('supplier.profile', $supplier->id) }}">
                        <div class="card-header">
                            <div>
                                <h2 class="fw-bolder mb-0">{{ $supplier->name }}</h2>
                                <p class="card-text">{{ \Illuminate\Support\Str::limit($supplier->description, 50, '...') }}</p>
                            </div>
                            <div class="avatar bg-light-primary p-50 m-0">
                                <!-- <a href="{{ route('supplier.profile', $supplier->id) }}"> -->
                                    <img class="img-fluid rounded-circle" src="{{ asset('http://127.0.0.1:8000/storage/images/products/tXM8Gl4g5lbKx50nlLGudf2n70X1TiguZMgcYzc4.png') }}" alt="{{ $supplier->name }}" style="width: 50px; height: 50px; object-fit: cover;">
                                <!-- </a> -->
                            </div>
                        </div>
                        </a>
                    </div>
                </div>
              
            @endforeach
        </div>

        <!-- Section Separator -->
        <section class="section-separator">
            <div class="separator-content">
                <h2>Featured Products</h2>
                <p>Check out our best-selling items</p>
            </div>
        </section>

        <!-- E-commerce Products Section -->
        <section id="ecommerce-products" class="grid-view">
            @foreach($products as $product)
                <div class="card ecommerce-card">
                    <div class="item-img text-center">
                        <a href="{{ route('product.show', $product->id) }}">
                            <img class="img-fluid card-img-top" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="item-wrapper">
                            <div>
                                <h6 class="item-price">${{ number_format($product->price, 2) }}</h6>
                            </div>
                        </div>
                        <h6 class="item-name">
                            <a class="text-body" href="{{ route('product.show', $product->id) }}">{{ $product->name }}</a>
                            <span class="item-company">By <a href="#" class="company-name">{{ $product->company }}</a></span>
                        </h6>
                        <p class="card-text item-description">
                            {{ \Illuminate\Support\Str::limit($product->description, 100, '...') }}
                        </p>
                    </div>
                    <div class="item-options text-center">
                    @if($product->stock > 0)
        <a href="{{ route('product.show', $product->id) }}" class="btn btn-primary btn-cart">
            <i data-feather="shopping-cart"></i>
            <span class="add-to-cart">Add to cart</span>
        </a>
    @else
        <span class="btn btn-secondary out-of-stock" disabled>
            Out of stock
        </span>
    @endif
                    </div>
                </div>
            @endforeach
        </section>

        <!-- Browse by Section -->
        <div class="browse-by-section">
            @php
                $categories = [
                    ['name' => 'Beer', 'image' => 'dummy-beer.jpg'],
                    ['name' => 'Ready-to-Drink', 'image' => 'dummy-ready-to-drink.jpg'],
                    ['name' => 'Wine', 'image' => 'dummy-wine.jpg'],
                    ['name' => 'Spirits', 'image' => 'dummy-spirits.jpg'],
                    ['name' => 'Non-Alcoholic', 'image' => 'dummy-non-alcoholic.jpg']
                ];
            @endphp

            @foreach($categories as $category)
                <div class="category-card">
                    <a href="#">
                        <img src="{{ asset('https://plus.unsplash.com/premium_photo-1670270203164-aa65468a9c67?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpbmtzfGVufDB8fDB8fHww') }}" alt="{{ $category['name'] }}">
                        <p class="category-name">{{ $category['name'] }}</p>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Section Separator -->
        <section class="section-separator">
            <div class="separator-content">
                <h2>Discover Our Latest Collection</h2>
                <p>Handpicked products just for you</p>
            </div>
        </section>

    </div>
</div>

@endsection
