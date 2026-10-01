@extends('layouts.app')

@section('content')
<div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
            </div>
            <div class="content-body">
<h1>Add New Product</h1>

<form action="{{ route('supplier.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div>
        <label for="name">Product Name:</label>
        <input type="text" name="name" id="name" required>
    </div>
    <div>
        <label for="description">Description:</label>
        <textarea name="description" id="description" required></textarea>
    </div>
    <div>
        <label for="price">Price:</label>
        <input type="number" name="price" id="price" required step="0.01">
    </div>
    <div>
        <label for="image">Product Image:</label>
        <input type="file" name="image" id="image" accept="image/*" required>
    </div>
    <button type="submit">Add Product</button>
</form>


<a href="{{ route('supplier.dashboard') }}">Back to Dashboard</a>
@endsection
