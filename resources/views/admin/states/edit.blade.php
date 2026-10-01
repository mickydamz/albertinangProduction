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
                        <h2 class="content-header-title mb-0">Edit State</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.states.index') }}">States</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.states.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-50"></i> Back to States
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="row">
                {{-- Edit form --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">{{ $state->name }}</h4>
                            <span class="badge {{ $state->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $state->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="card-body">

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                                </div>
                            @endif

                            <form action="{{ route('admin.states.update', $state->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-2">
                                    <label class="form-label" for="name">State Name <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $state->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active"
                                               name="is_active" value="1"
                                               {{ old('is_active', $state->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                    <small class="text-muted">Inactive states are hidden from the delivery dropdown at checkout.</small>
                                </div>

                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary me-1">
                                        <i class="fas fa-save me-50"></i> Update State
                                    </button>
                                    <a href="{{ route('admin.states.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                {{-- Locations in this state --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Delivery Locations in this State</h4>
                            <a href="{{ route('admin.locations.create', ['state_id' => $state->id, 'return' => route('admin.states.edit', $state->id)]) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-50"></i> Add Location
                            </a>
                        </div>
                        <div class="card-body p-0">
                            @if ($state->locations->count())
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Std Fee</th>
                                            <th>Truck Fee</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($state->locations as $loc)
                                            <tr>
                                                <td>{{ $loc->name }}</td>
                                                <td>₦{{ number_format($loc->shipping_cost, 0) }}</td>
                                                <td>₦{{ number_format($loc->truck_shipping_cost, 0) }}</td>
                                                <td>
                                                    <span class="badge {{ $loc->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $loc->is_active ? 'Active' : 'Off' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.locations.edit', ['location' => $loc->id, 'return' => route('admin.states.edit', $state->id)]) }}"
                                                       class="btn btn-xs btn-warning" style="font-size:11px;padding:2px 8px;">Edit</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="p-2 text-muted" style="font-size:13px;">
                                    No delivery locations yet for this state.
                                    <a href="{{ route('admin.locations.create', ['state_id' => $state->id, 'return' => route('admin.states.edit', $state->id)]) }}">Add one.</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
