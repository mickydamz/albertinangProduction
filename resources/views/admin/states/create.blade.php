@extends('layouts.adminlayout')

@section('content')

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Add New State</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.states.index') }}">States</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.states.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-50"></i> Back to States
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">New State</h4>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.states.store') }}" method="POST">
                        @csrf

                        <div class="mb-2">
                            <label class="form-label" for="name">State Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g. Lagos" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-1">
                                <i class="fas fa-save me-50"></i> Save State
                            </button>
                            <a href="{{ route('admin.states.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
