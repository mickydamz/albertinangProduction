@extends('layouts.supplierlayout')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row"></div>
        <div class="content-body">
            <h1 class="text-center my-1">Add New Product</h1>

            <div class="d-flex justify-content-center">
                <form action="{{ route('supplier.products.store') }}" method="POST" enctype="multipart/form-data" class="card p-4" style="width: 500px;">
                    @csrf

                    <!-- Product Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label font-weight-bold">Product Name:</label>
                        <input type="text" name="name" id="name" required class="form-control">
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label font-weight-bold">Description:</label>
                        <textarea name="description" id="description" required class="form-control"></textarea>
                    </div>

                    <!-- Price -->
                    <div class="mb-3">
                        <label for="price" class="form-label font-weight-bold">Price:</label>
                        <input type="number" name="price" id="price" required step="0.01" class="form-control">
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label for="category" class="form-label font-weight-bold">Category:</label>
                        <select name="category_id" id="category" required class="form-control">
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Stock Quantity -->
                    <div class="mb-3">
                        <label for="stock" class="form-label font-weight-bold">Stock Quantity:</label>
                        <input type="number" name="stock" id="stock" required min="0" class="form-control">
                    </div>

                  
                    

                    <!-- Product Images -->
                    <div class="mb-3">
                        <label for="images" class="form-label font-weight-bold">Product Images:</label>
                        <input type="file" name="images[]" id="images" accept="image/*" multiple class="form-control">
                    </div>

                    <!-- Display selected images -->
                    <div id="image-preview-container" class="mb-3">
                        <!-- Preview images will be shown here -->
                    </div>

                    <button type="submit" class="btn btn-success w-100">Add Product</button>
                </form>
            </div>
<!-- 
            <div class="text-center mt-4">
                <a href="{{ route('admin.products.index') }}" class="text-primary text-decoration-none">Back to Products</a>
            </div> -->
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form'); // Get the form element
        const imageInput = document.getElementById('images');
        const previewContainer = document.getElementById('image-preview-container');

        // Image preview logic
        imageInput.addEventListener('change', function (e) {
            previewContainer.innerHTML = ''; // Clear previous previews

            const files = e.target.files;
            if (files) {
                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        const imgElement = document.createElement('img');
                        imgElement.src = event.target.result;
                        imgElement.style = 'width: 100px; height: 100px; object-fit: cover; margin-right: 10px; border-radius: 8px;';
                        const deleteButton = document.createElement('button');
                        deleteButton.textContent = 'Remove';
                        deleteButton.classList.add('btn', 'btn-danger', 'btn-sm', 'mt-2');
                        deleteButton.onclick = function () {
                            // Remove the image preview from DOM and the file input
                            imgElement.remove();
                            deleteButton.remove();
                            imageInput.value = ''; // Clear the file input if the image is removed
                        };

                        previewContainer.appendChild(imgElement);
                        previewContainer.appendChild(deleteButton);
                    };

                    reader.readAsDataURL(file);
                });
            }
        });

        // Form submission validation
        form.addEventListener('submit', function (e) {
            // Check if there are no images selected
            if (imageInput.files.length === 0) {
                e.preventDefault(); // Prevent form submission
                alert('Please upload at least one image to create the product.');
            }
        });
    });
</script>


@endsection
