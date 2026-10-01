@extends('layouts.adminlayout')

@section('content')

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        {{-- Page Header --}}
        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">View Location</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.locations.index') }}">Locations</a></li>
                            <li class="breadcrumb-item active">{{ $location->name }}</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.locations.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-50"></i> Back to Locations
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-body">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="alert-body">{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Location Detail Card --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Location Details</h4>
                    <div class="d-flex align-items-center gap-1">
                        {{-- Toggle Active/Inactive --}}
                        <form action="{{ route('admin.locations.toggleActive', $location->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $location->is_active ? 'btn-secondary' : 'btn-success' }}">
                                <i class="fas {{ $location->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }} me-50"></i>
                                {{ $location->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        <a href="{{ route('admin.locations.edit', $location->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit me-50"></i> Edit Location
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th style="width:160px; color:#6e6b7b;">ID</th>
                                        <td>{{ $location->id }}</td>
                                    </tr>
                                    <tr>
                                        <th style="color:#6e6b7b;">Name</th>
                                        <td><strong>{{ $location->name }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th style="color:#6e6b7b;">Status</th>
                                        <td>
                                            @if ($location->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="color:#6e6b7b;">Pickup Points</th>
                                        <td>
                                            <span class="badge bg-primary rounded-pill">
                                                {{ $location->pickupPoints->count() }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="color:#6e6b7b;">Created</th>
                                        <td>{{ $location->created_at->format('M d, Y H:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th style="color:#6e6b7b;">Last Updated</th>
                                        <td>{{ $location->updated_at->format('M d, Y H:i A') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pickup Points Card --}}
            <div class="card mt-1">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        Pickup Points
                        <span class="badge bg-light-primary ms-50">{{ $location->pickupPoints->count() }}</span>
                    </h4>
                    <a href="{{ route('admin.pickup-points.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-50"></i> Add Pickup Point
                    </a>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Opening Hours</th>
                                        <th>Added</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($location->pickupPoints as $point)
                                        <tr>
                                            <td>{{ $point->id }}</td>
                                            <td><strong>{{ $point->name }}</strong></td>
                                            <td>{{ $point->address }}</td>
                                            <td>
                                                @if ($point->hours)
                                                    <span class="badge bg-light-success">
                                                        <i class="fas fa-clock me-25" style="font-size:10px;"></i>
                                                        {{ $point->hours }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $point->created_at->format('M d, Y') }}</td>
                                            <td class="text-center">
                                                <div class="d-inline-flex align-items-center gap-50">
                                                    <a href="{{ route('admin.pickup-points.edit', $point->id) }}"
                                                       class="item-edit" title="Edit pickup point">
                                                        <i data-feather="edit" class="font-small-4"></i>
                                                    </a>
                                                    <form action="{{ route('admin.pickup-points.destroy', $point->id) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Delete \'{{ addslashes($point->name) }}\'? This cannot be undone.');"
                                                          class="d-inline-block mb-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-icon delete-record"
                                                                title="Delete pickup point">
                                                            <i data-feather="trash-2" class="font-small-4"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-2 text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                No pickup points added for <strong>{{ $location->name }}</strong> yet.
                                                <a href="{{ route('admin.pickup-points.create') }}" class="ms-50">Add one now</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="card mt-1 border-danger" style="border: 1px solid #ea5455;">
                <div class="card-header">
                    <h4 class="card-title text-danger mb-0">
                        <i class="fas fa-exclamation-triangle me-50"></i> Danger Zone
                    </h4>
                </div>
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-1">
                    <div>
                        <p class="mb-0 fw-bold">Delete this location</p>
                        <small class="text-muted">
                            This will permanently delete <strong>{{ $location->name }}</strong>
                            and all {{ $location->pickupPoints->count() }} associated pickup point(s).
                        </small>
                    </div>
                    <form action="{{ route('admin.locations.destroy', $location->id) }}"
                          method="POST"
                          onsubmit="return confirm('Permanently delete \'{{ addslashes($location->name) }}\' and all its pickup points? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt me-50"></i> Delete Location
                        </button>
                    </form>
                </div>
            </div>

        </div>{{-- /content-body --}}
    </div>
</div>
@endsection