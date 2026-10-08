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
                        <h2 class="content-header-title mb-0">{{ $tag->name }} Details</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.tags.index') }}">Tags</a></li>
                            <li class="breadcrumb-item active">{{ $tag->name }}</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.tags.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-50"></i> Back to Tags
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">
            <section id="tag-details-view">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-10 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Tag Information</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <p class="text-gray-700 text-lg mb-1">
                                        <strong class="font-semibold text-gray-800">Tag Name:</strong> {{ $tag->name }}
                                    </p>
                                    <p class="text-gray-700 text-sm mb-1">
                                        <strong class="font-semibold text-gray-800">ID:</strong> {{ $tag->id }}
                                    </p>
                                    <p class="text-gray-700 text-sm mb-1">
                                        <strong class="font-semibold text-gray-800">Created At:</strong> {{ $tag->created_at->format('M d, Y H:i A') }}
                                    </p>
                                    <p class="text-gray-700 text-sm mb-0">
                                        <strong class="font-semibold text-gray-800">Updated At:</strong> {{ $tag->updated_at->format('M d, Y H:i A') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h4 class="card-title">Tag Options</h4>
                            </div>
                            <div class="card-body">
                                @if ($tag->options && count($tag->options) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Key</th>
                                                    <th>Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tag->options as $option)
                                                    @if (is_array($option) && isset($option['key']) && isset($option['value']))
                                                        <tr>
                                                            <td><span class="font-medium text-gray-800">{{ $option['key'] }}</span></td>
                                                            <td>{{ $option['value'] }}</td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td colspan="2" class="text-danger">Invalid Option Format: {{ json_encode($option) }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-gray-600 text-center py-3">No specific options defined for this tag.</p>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <a href="{{ route('admin.tags.edit', $tag->id) }}" class="btn btn-primary me-1">
                                <i class="fas fa-edit me-50"></i> Edit Tag
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
