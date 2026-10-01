@extends('layouts.app')

@section('content')
<!-- BEGIN: Content-->

<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row"></div>
        <div class="content-body">
            <div class="container-fluid">
                <h1 class="text-center my-1">Your Products</h1>

                <div class="text-center mb-4">
                    <a href="/supplier/products/create" class="btn btn-primary btn-lg">Add New Product</a>
                </div>

                @if($products->isEmpty())
                    <p class="text-center font-italic text-muted">You have no products listed.</p>
                @else
                    <div class="row match-height">
                        @foreach($products as $product)
                            <div class="col-lg-3 col-md-4 col-12 mb-4">
                                <div class="card h-100" style="border-radius: 10px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                    <div class="card-body text-start">
                                        <div class="text-center my-3">
                                            <!-- Carousel for Product Images -->
                                            @if($product->images->isNotEmpty())
                                                <div id="carousel{{ $product->id }}" class="carousel slide" data-ride="carousel" style="max-width: 100%; height: 10rem; object-fit: contain; border-radius: 8px;">
                                                    <div class="carousel-inner">
                                                        @foreach ($product->images as $index => $image)
                                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                                <img src="{{ asset('storage/' . $image->image_url) }}" class="d-block w-100" alt="Product Image {{ $index + 1 }}">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <a class="carousel-control-prev" href="#carousel{{ $product->id }}" role="button" data-slide="prev">
                                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                        <span class="sr-only">Previous</span>
                                                    </a>
                                                    <a class="carousel-control-next" href="#carousel{{ $product->id }}" role="button" data-slide="next">
                                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                        <span class="sr-only">Next</span>
                                                    </a>
                                                </div>
                                            @else
                                                <img src="http://127.0.0.1:8000/app-assets/images/illustration/product-placeholder.svg" alt="{{ $product->name }}" style="max-width: 100%; height: 10rem; object-fit: contain; border-radius: 8px;">
                                            @endif
                                        </div>
                                        <div class="row border-top mx-0">
                                            <div class="col-12 py-1">
                                                <h3 class="fw-bolder mb-2" style="font-size: 1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ \Illuminate\Support\Str::limit($product->name, 12) }}</h3>
                                                <h5 class="fw-bolder mb-2" style="font-size: 0.9rem;">{{ $product->category->name ?? 'Uncategorized' }}</h5>
                                                <h5 class="fw-bolder mb-0" style="font-size: 1rem;">${{ number_format($product->price, 2) }}</h5>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-footer d-flex justify-content-between align-items-center">
                                        <!-- View Details Button as Icon -->
                                        <a href="{{ route('supplier.products.show', $product) }}" class="btn btn-info btn-sm w-25 text-center">
                                            <i class="fas fa-eye"></i> <!-- Eye Icon -->
                                        </a>
                                        
                                        <!-- Edit Button as Icon -->
                                        <a href="{{ route('supplier.products.edit', $product) }}" class="btn btn-primary btn-sm w-25 text-center">
                                            <i class="fas fa-edit"></i> <!-- Edit Pencil Icon -->
                                        </a>

                                        <!-- Delete Form with Trash Icon -->
                                        <form action="{{ route('supplier.products.destroy', $product) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm w-25 text-center d-flex justify-content-center align-items-center" title="Delete">
                                                <i class="fas fa-trash-alt fa-lg"></i> <!-- Trash Icon with proper size -->
                                            </button>
                                        </form>

                                        <script>
                                            // Custom delete confirmation
                                            function confirmDelete() {
                                                return confirm('Are you sure you want to delete this product? This action cannot be undone.');
                                            }
                                        </script>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination Links (Laravel's Built-in) -->
                    <div class="pagination d-flex justify-content-center mt-4">
                        {{ $products->links('pagination::bootstrap-4') }} <!-- This will generate the pagination links including page numbers -->
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .carousel-control-prev-icon, 
    .carousel-control-next-icon {
        background-color:  #5aab1f; /* Set the background color to red */
        color: black;
    }

    .page-link.active {
        background-color: #5aab1f;
        color: #fff;
    }

    .page-link {
        padding: 0.5rem 1rem;
        margin: 0 0.25rem;
        background-color: #eaf3de;
        border-radius: 5px;
        text-decoration: none;
    }

    .page-link:hover {
        background-color: #ddd;
    }

    .page-link.disabled {
        pointer-events: none;
        opacity: 0.5;
    }

    .pagination {
        display: flex;
        justify-content: center;
    }

    .pagination .page-item {
        margin: 0 5px;
    }

    /* Styling for images to maintain proportional size */
    .carousel-inner img, 
    .card-body img {
        max-width: 100%; /* Ensure image takes full width */
        max-height: 10rem; /* Limit height to 10rem */
        object-fit: contain; /* Maintain aspect ratio */
    }

    /* Adjust the card to be smaller while maintaining layout */
    .card {
        max-width: 100%;
        border-radius: 10px; /* Slightly rounded corners for a smoother look */
        box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Soft shadow around the card */
    }

    .card-body {
        padding: 0.75rem;
    }

    /* Adjust text sizes within the card for proportionality */
    .card-body h3, .card-body h5 {
        font-size: 1rem;
    }
</style>
@endsection
