@extends('layouts.adminlayout')

@section('content')
<div class="app-content content ecommerce-application">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Edit Ticket</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">Tickets</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
                </div>
            </div>
        </div>

        @if($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif


        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm rounded-lg">
                    <div class="card-header text-white">
                        <h4 class="card-title mb-0"> {{ $ticket->subject }}</h4>
                    </div>
                    <div class="card-body bg-light">
                        <form method="POST" action="{{ route('admin.tickets.update', $ticket->id) }}">
                            @csrf
                            @method('PUT')

                            <!-- Subject Field -->
                            <div class="form-group mb-3">
                                <label for="subject" class="fw-bold text-dark">Subject</label>
                                <input type="text" class="form-control" name="subject" id="subject" value="{{ old('subject', $ticket->subject) }}" required>
                            </div>

                            <!-- Description Field -->
                            <div class="form-group mb-3">
                                <label for="description" class="fw-bold text-dark">Description</label>
                                <textarea class="form-control" name="description" id="description" required>{{ old('description', $ticket->description) }}</textarea>
                            </div>

                            <!-- Status Dropdown -->
                            <div class="form-group mb-3">
                                <label for="status" class="fw-bold text-dark">Status</label>
                                <select name="status" id="status" class="form-control" required>
    <option value="open" {{ old('status', $ticket->status) == 'open' ? 'selected' : '' }}>Open</option>
    <option value="pending" {{ old('status', $ticket->status) == 'pending' ? 'selected' : '' }}>Pending</option>
    <option value="closed" {{ old('status', $ticket->status) == 'closed' ? 'selected' : '' }}>Closed</option>
</select>

                            </div>

                            <!-- Priority Dropdown -->
                            <div class="form-group mb-3">
                                <label for="priority" class="fw-bold text-dark">Priority</label>
                                <select name="priority" id="priority" class="form-control" required>
                                    <option value="low" {{ old('priority', $ticket->priority) == 'low' ? 'selected' : '' }} class="badge badge-light-success">Low</option>
                                    <option value="medium" {{ old('priority', $ticket->priority) == 'medium' ? 'selected' : '' }} class="badge badge-light-warning">Medium</option>
                                    <option value="high" {{ old('priority', $ticket->priority) == 'high' ? 'selected' : '' }} class="badge badge-light-danger">High</option>
                                </select>
                            </div>

                            <div class="d-flex gap-2 mt-1">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Ticket</button>
                                <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
