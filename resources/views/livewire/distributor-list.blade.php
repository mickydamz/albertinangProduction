<div>
    <div class="app-content content ecommerce-application">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
                <div class="content-header-left col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Distributors</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="supplier/distributor">Home</a></li>
                                    <li class="breadcrumb-item active">Distributors</li>
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
                                        placeholder="Search Distributor" 
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

                    <!-- Filters Section (Mobile) -->
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

                                                <!-- Country Filter -->
                                                <h6 class="filter-title mt-3">Country</h6>
                                                <select wire:model="countryFilter" class="form-control">
                                                    <option value="">Select Country</option>
                                                    @foreach($countries as $country)
                                                        <option value="{{ $country->name }}">{{ $country->name }}</option>
                                                    @endforeach
                                                </select>

                                                <!-- City Filter -->
                                                <h6 class="filter-title mt-3">City</h6>
                                                <select wire:model="cityFilter" class="form-control">
                                                    <option value="">Select City</option>
                                                    @foreach($cities as $city)
                                                        <option value="{{ $city }}">{{ $city }}</option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Distributor List -->
                    <section id="distributor-list" class="distributor-list">
                        <div class="row">
                            <div class="col-12">
                                <h4 class="section-title mb-1">View All Distributors</h4>
                                @forelse($distributors as $distributor)
                                    <div class="distributor-card d-flex flex-column flex-md-row align-items-center">
                                        <div class="distributor-img">
                                            @if($distributor->avatar)
                                                <img class="img-fluid" src="{{ asset('storage/' . $distributor->avatar ) }}" alt="{{ $distributor->name }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                            @else
                                                <img class="img-fluid" src="/app-assets/images/illustration/badge.svg" alt="{{ $distributor->name }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                            @endif
                                        </div>
                                        <div class="distributor-content ms-md-3 mt-3 mt-md-0">
                                            <h5 class="distributor-name">{{ $distributor->name }}</h5>
                                            <p class="distributor-details">{{ \Illuminate\Support\Str::limit($distributor->description ?? 'No details available for now', 30) }}</p>
                                            <p class="distributor-location"><strong>Location:</strong> {{ $distributor->country && $distributor->city ? $distributor->country . ', ' . $distributor->city : 'N/A' }}</p>
                                            <a href="{{ route('supplier.profile', $distributor->id) }}" class="btn btn-primary">View Details</a>
                                        </div>
                                    </div>
                                @empty
                                    <p>No distributors found.</p>
                                @endforelse
                            </div>
                        </div>
                    </section>

                    <!-- Pagination -->
                    <section id="ecommerce-pagination">
                        <div class="row">
                            <div class="col-12">
                                {{ $distributors->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Sidebar (Desktop) -->
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

                                    <!-- Country Filter -->
                                    <h6 class="filter-title mt-3">Country</h6>
                                    <select wire:model="countryFilter" class="form-control">
                                        <option value="">Select Country</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->name }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>

                                    <!-- City Filter -->
                                    <h6 class="filter-title mt-3">City</h6>
                                    <select wire:model="cityFilter" class="form-control">
                                        <option value="">Select City</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city }}">{{ $city }}</option>
                                        @endforeach
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
        /* Maintain good padding on distributor list */
        .distributor-list {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }

        .distributor-card {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            align-items: center;
        }

        .distributor-img img {
            width: 100%;
            max-width: 150px;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
        }

        .distributor-content {
            flex: 1;
        }

        .distributor-name {
            font-size: 1.25rem;
            font-weight: 600;
        }

        /* Mobile-first responsive adjustments */
        @media (max-width: 768px) {
            .content-detached,
            .sidebar-detached {
                width: 100%;
            }

            .content-right,
            .sidebar-left {
                padding: 0;
            }

            .distributor-card {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 10px;
            }

            .breadcrumb-wrapper,
            .filter-heading {
                display: none;
            }

            .filters-section-mobile {
                padding: 15px;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .distributor-img img {
                max-width: 80px;
            }

            .sidebar-left {
                display: none;
            }
        }
    </style>
</div>
