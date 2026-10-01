<div>
    <div class="app-content content ecommerce-application">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
                <div class="content-header-left col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Products</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="supplier/product">Home</a></li>
                                    <li class="breadcrumb-item active">Products</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-detached content-right">
                <div class="content-body">
                    <!-- Search Bar -->
                    <section id="ecommerce-searchbar" class="ecommerce-searchbar">
                        <div class="row mt-1">
                            <div class="col-12">
                                <div class="input-group input-group-merge">
                                    <input 
                                        type="text" 
                                        class="form-control search-product" 
                                        id="shop-search" 
                                        placeholder="Search Product" 
                                        aria-label="Search..." 
                                        aria-describedby="shop-search" 
                                        wire:model="search" />
                                    <span class="input-group-text">
                                        <i data-feather="search" class="text-muted"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Filters Section (Visible only on Mobile) -->
                    <section id="filters-section-mobile" class="filters-section-mobile d-block d-md-none">
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="sidebar-shop">
                                    <h6 class="filter-heading">Filters</h6>
                                    <div class="card">
                                        <div class="card-body">
                                            <form>
                                                <!-- Category Filter -->
                                                <h6 class="filter-title">Category</h6>
                                                <select class="form-control" wire:model="categoryFilter">
                                                    <option value="">Select By Category</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>

                                                <!-- Price Filter -->
                                                <h6 class="filter-title mt-3">Price</h6>
                                                <div class="d-flex">
                                                    <input class="form-control" type="number" wire:model="minPrice" placeholder="Min Price">
                                                    <span class="mx-2">-</span>
                                                    <input class="form-control" type="number" wire:model="maxPrice" placeholder="Max Price">
                                                </div>

                                                <!-- Rating Filter -->
                                                <h6 class="filter-title mt-3">Rating</h6>
                                                <select class="form-control" wire:model="ratingFilter">
                                                    <option value="">Select Rating</option>
                                                    <option value="1">1 Star</option>
                                                    <option value="2">2 Stars</option>
                                                    <option value="3">3 Stars</option>
                                                    <option value="4">4 Stars</option>
                                                    <option value="5">5 Stars</option>
                                                </select>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Product List -->
                    <section id="product-list" class="product-list">
                        <div class="row">
                            <div class="col-12">
                                <h4 class="section-title mb-1">View All Products</h4>
                                @forelse($products as $product)
                                    <div class="product-card d-flex flex-column flex-md-row align-items-center">
                                        <div class="product-img">
                                            @if($product->images->count() > 0)
                                                <div id="carouselExampleIndicators{{ $product->id }}" class="carousel slide" data-bs-ride="carousel">
                                                    <div class="carousel-inner">
                                                        @foreach($product->images as $index => $image)
                                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                                <img src="{{ asset('storage/' . $image->image_url) }}" class="img-fluid" alt="Product Image {{ $index + 1 }}">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators{{ $product->id }}" data-bs-slide="prev">
                                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                        <span class="visually-hidden">Previous</span>
                                                    </button>
                                                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators{{ $product->id }}" data-bs-slide="next">
                                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                        <span class="visually-hidden">Next</span>
                                                    </button>
                                                </div>
                                            @else
                                                <img class="img-fluid" src="/app-assets/images/illustration/no-image.svg" alt="{{ $product->name }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                            @endif
                                        </div>
                                        <div class="product-content ms-md-3 mt-3 mt-md-0">
                                            <h5 class="product-name">{{ $product->name }}</h5>
                                            <p class="product-details">{{ \Illuminate\Support\Str::limit($product->description ?? 'No details available for now', 30) }}</p>

                                            <p class="product-price"><strong>Price:</strong> ${{ $product->price }}</p>
                                            <p class="product-price"><strong>MOQ:</strong> {{ $product->moq }}</p>

                                            <p class="product-rating">
                                                <strong>Rating:</strong>
                                                @php
                                                    $rating = round($product->review_rating); // live review average (0 when no real reviews)
                                                @endphp
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fa fa-star {{ $i <= $rating ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                            </p>
                                            <a href="{{ route('product.show', $product->id) }}" class="btn btn-primary">View Details</a>
                                        </div>
                                    </div>
                                @empty
                                    <p>No products found.</p>
                                @endforelse
                            </div>
                        </div>
                    </section>

                    <!-- Pagination -->
                    <section id="ecommerce-pagination">
                        <div class="row">
                            <div class="col-12">
                                {{ $products->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Sidebar (Visible only on Desktop) -->
            <div class="sidebar-detached sidebar-left d-none d-md-block">
                <div class="sidebar">
                    <div class="sidebar-shop">
                        <h6 class="filter-heading">Filters</h6>
                        <div class="card">
                            <div class="card-body">
                                <form>
                                    <!-- Category Filter -->
                                    <h6 class="filter-title">Category</h6>
                                    <select class="form-control" wire:model="categoryFilter">
                                        <option value="">Select By Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>

                                    <!-- Price Filter -->
                                    <h6 class="filter-title mt-3">Price</h6>
                                    <div class="d-flex">
                                        <input class="form-control" type="number" wire:model="minPrice" placeholder="Min Price">
                                        <span class="mx-2">-</span>
                                        <input class="form-control" type="number" wire:model="maxPrice" placeholder="Max Price">
                                    </div>

                                    <!-- Rating Filter -->
                                    <h6 class="filter-title mt-3">Rating</h6>
                                    <select class="form-control" wire:model="ratingFilter">
                                        <option value="">Select Rating</option>
                                        <option value="1">1 Star</option>
                                        <option value="2">2 Stars</option>
                                        <option value="3">3 Stars</option>
                                        <option value="4">4 Stars</option>
                                        <option value="5">5 Stars</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Styles -->
    <style>
        .product-list {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 0;
        }

        .product-card {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            align-items: center;
        }

        .product-img img {
            width: 100%;
            max-width: 150px;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
        }

        .product-content {
            flex: 1;
        }

        .product-name {
            font-size: 1.25rem;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .content-detached,
            .sidebar-detached {
                width: 100%;
            }

            .product-card {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 10px;
            }

            .sidebar-left {
                display: none;
            }

            .filters-section-mobile {
                padding: 15px;
                background-color: #fff;
                border-radius: 0;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
        }
    </style>

</div>
