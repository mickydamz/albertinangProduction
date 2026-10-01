@extends('layouts.adminlayout')

@section('content')

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0" style="padding-top: 10px;">
        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Edit City</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.cities.index') }}">Cities</a></li>
                            <li class="breadcrumb-item active">Edit City</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.cities.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-50"></i> Back to Cities
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.cities.update', $city->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group mt-2 mb-2">
                    <label for="city_name">City Name</label>
                    <input type="text" name="name" id="city_name" class="form-control" value="{{ old('name', $city->name) }}" placeholder="Enter city name">
                    @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mt-2 mb-2">
                    <label for="country">Country</label>
                    <select name="country_id" id="country" class="form-control">
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ $city->country_id == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                        @endforeach
                    </select>
                    @error('country_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update City</button>
            </form>
        </div>
    </div>
</div>

@endsection
