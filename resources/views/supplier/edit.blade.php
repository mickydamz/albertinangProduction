@extends('layouts.app')

@section('content')
<div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
            </div>
            <div class="content-body">
<h1>Edit Product</h1>

<form action="{{ route('supplier.products.update', $product) }}" method="POST">
    @csrf
    @method('PUT')
    <div>
        <label for="name">Product Name:</label>
        <input type="text" name="name" id="name" value="{{ $product->name }}" required>
    </div>
    <div>
        <label for="description">Description:</label>
        <textarea name="description" id="description" required>{{ $product->description }}</textarea>
    </div>
    <div>
        <label for="price">Price:</label>
        <input type="number" name="price" id="price" value="{{ $product->price }}" required step="0.01">
    </div>
    <button type="submit">Update Product</button>
</form>

<a href="{{ route('supplier.dashboard') }}">Back to Dashboard</a>
@endsection
