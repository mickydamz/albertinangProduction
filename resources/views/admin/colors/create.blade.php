@extends('layouts.adminlayout')

@section('content')
{{-- Feather icons are typically loaded via a JS script in Vuexy, but including the CSS for general compatibility --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css">


<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Create Color</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.colors.index') }}">Colors</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.colors.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="color-create-form">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">New color Information</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.colors.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-1">
                                        <label class="form-label" for="name">color Name</label>
                                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Enter color name" value="{{ old('name') }}" required />
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                 
                                    
                                    <button type="submit" class="btn btn-primary me-1">
                                        {{-- Using Font Awesome for save icon, as feather icons require JS initialization --}}
                                        <i class="fas fa-save me-50"></i> Save color
                                    </button>
                                    <a href="{{ route('admin.colors.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
