@extends('layouts.app')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row"></div>
        <div class="content-body">
            <div class="container-fluid">
                <h1 class="my-4 text-center text-muted">Edit Product: <span class="fw-bold">{{ $product->name }}</span></h1>

                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8 col-sm-10">
                        <form action="{{ route('supplier.products.update', $product) }}" method="POST" class="card p-4" id="product-form" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Display Global Errors -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Product Name Field -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name:</label>
                                <input type="text" id="name" name="name" class="form-control form-control-lg" value="{{ old('name', $product->name) }}" required>
                                @error('name') 
                                    <div class="text-danger">{{ $message }}</div> 
                                @enderror
                            </div>

                            <!-- Product Description Field -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Product Description:</label>
                                <textarea id="description" name="description" class="form-control form-control-lg" rows="4">{{ old('description', $product->description) }}</textarea>
                                @error('description') 
                                    <div class="text-danger">{{ $message }}</div> 
                                @enderror
                            </div>

                            <!-- Product Price Field -->
                            <div class="mb-3">
                                <label for="price" class="form-label">Price:</label>
                                <input type="number" id="price" name="price" class="form-control form-control-lg" value="{{ old('price', $product->price) }}" required>
                                @error('price') 
                                    <div class="text-danger">{{ $message }}</div> 
                                @enderror
                            </div>

                            <!-- Stock Quantity Field -->
                            <div class="mb-3">
                                <label for="stock" class="form-label font-weight-bold">Stock Quantity:</label>
                                <input type="number" value="{{ old('stock', $product->stock) }}" name="stock" id="stock" required min="0" class="form-control">
                                @error('stock') 
                                    <div class="text-danger">{{ $message }}</div> 
                                @enderror
                            </div>

                            <!-- Product Images Section -->
                            <div class="mb-2">
                                <h4 class="text-center text-muted">Product Images</h4>
                                <div class="row">
                                    @if($product->images->isNotEmpty())
                                        @foreach($product->images as $image)
                                            <div class="col-md-3 col-sm-4 col-6 text-center" id="image-card-{{ $image->id }}">
                                                <div class="card shadow-sm border-light">
                                                    <!-- Image Preview -->
                                                    <img src="{{ asset('storage/' . $image->image_url) }}" class="card-img-top" alt="Product Image" style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px;">
                                                    
                                                    <!-- Trash Button to Remove Image -->
                                                    <button type="button" class="btn btn-danger btn-sm remove-image" data-image-id="{{ $image->id }}" aria-label="Remove Image">
                                                        <i class="fas fa-trash"></i> <!-- Trash bin icon -->
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-center text-muted">No images available for this product.</p>
                                    @endif
                                </div>
                                @error('images') 
                                    <div class="text-danger">{{ $message }}</div> 
                                @enderror
                            </div>

                            <!-- Image Upload Section -->
                            <div class="mb-3">
                                <label for="images" class="form-label">Upload New Images:</label>
                                <input type="file" id="images" name="images[]" class="form-control" multiple onchange="previewImages()">
                                <small class="text-muted">You can upload multiple images (JPG, PNG, GIF).</small>
                            </div>

                            <!-- Preview Section for Selected Images -->
                            <div id="image-previews" class="mb-3 row"></div>

                            <!-- Hidden Input to Track Removed Images -->
                            <input type="hidden" id="removed_images" name="removed_images" value="">

                            <!-- Submit Button for Form -->
                            <button type="submit" class="btn btn-primary btn-lg w-100">Update Product</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
 document.addEventListener('DOMContentLoaded', function() {
    const removedImagesInput = document.getElementById('removed_images');
    const removeButtons = document.querySelectorAll('.remove-image');
    const imageCount = document.querySelectorAll('.remove-image').length;

    // Attach click event to remove buttons (trash icon)
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Check the number of images before allowing removal
            const currentImageCount = document.querySelectorAll('.remove-image').length;

            // If there are more than one image, allow removal
            if (currentImageCount > 1) {
                const imageId = this.getAttribute('data-image-id');
                const imageCard = document.getElementById('image-card-' + imageId);

                // Add image ID to the hidden input field
                let removedImages = removedImagesInput.value ? removedImagesInput.value.split(',') : [];
                if (!removedImages.includes(imageId)) {
                    removedImages.push(imageId);
                }

                removedImagesInput.value = removedImages.join(',');

                // Optionally hide the image card or remove it from the DOM
                imageCard.style.display = 'none';
            } else {
                alert('You cannot remove the last image. Please upload a new one before removing.');
            }
        });
    });

    // Form submission validation to ensure there is at least one image
    const form = document.getElementById('product-form');
    form.addEventListener('submit', function(event) {
        const imageFiles = document.getElementById('images').files;
        const existingImages = document.querySelectorAll('.remove-image').length;
        const removedImages = removedImagesInput.value.split(',').filter(Boolean).length;

        // If no new images are selected and no images are removed (i.e., no images to keep or remove)
        if (existingImages === 0 && imageFiles.length === 0) {
            event.preventDefault();  // Prevent form submission
            alert('Please upload at least one image for the product.');
        }
        // If no new images are selected, but the user has removed all the existing ones
        else if (existingImages === 0 && removedImages === 0 && imageFiles.length === 0) {
            event.preventDefault();
            alert('Please upload at least one image for the product.');
        }
    });
});

</script>

<script>
    // Function to preview selected images before upload
    function previewImages() {
        const previewContainer = document.getElementById('image-previews');
        previewContainer.innerHTML = ''; // Clear previous previews
        const files = document.getElementById('images').files;
        
        if (files) {
            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';
                    img.style.margin = '5px';

                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }
    }
</script>

<style>
    .remove-image {
        padding: 5px 8px; /* Adjust the padding to make the button smaller */
        border-radius: 50%; /* Make it round */
        font-size: 16px; /* Adjust the size of the icon */
        display: inline-flex; /* Make the button fit tightly around the icon */
        align-items: center;
        justify-content: center;
        width: 30px; /* Set the button size to match the icon */
        height: 30px; /* Set the button size to match the icon */
    }

    .remove-image i {
        color: #fff; /* Make the icon color white */
        font-size: 18px; /* Adjust the icon size */
    }

    .remove-image:hover {
        background-color: #d9534f; /* A slightly darker red on hover */
    }
</style>
@endsection
