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
                        <h2 class="content-header-title mb-0">Create User</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Create User Form -->
            <section id="create-user-form" style="margin-top: 20px;">
                <div class="row pt-5">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Create New User</h4>
                            </div>
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-1">
                                        <label for="name" class="form-label">Name:</label>
                                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                                    </div>

                                    <div class="mb-1">
                                        <label for="email" class="form-label">Email:</label>
                                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                                    </div>

                                    <div class="mb-1">
                                        <label for="password" class="form-label">Password:</label>
                                        <input type="password" name="password" id="password" class="form-control" required>
                                    </div>

                                    <div class="mb-1">
                                        <label for="password_confirmation" class="form-label">Confirm Password:</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                                    </div>

                                    <div class="mb-1">
                                        <label for="role" class="form-label">Role:</label>
                                        <select name="role" id="role" class="form-control" required>
                                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                                            <!-- <option value="affiliate" {{ old('role') == 'affiliate' ? 'selected' : '' }}>Affiliate</option> -->
                                        </select>
                                    </div>

                                    <div class="mb-1">
                                        <label for="status" class="form-label">Status:</label>
                                        <!-- Change value="ban" to value="banned" to match the DB enum -->
<select name="status" id="status" class="form-control" required>
    <option value="green"  {{ old('status') == 'green'  ? 'selected' : '' }}>Normal User</option>
    <!--<option value="yellow" {{ old('status') == 'yellow' ? 'selected' : '' }}>Yellow</option>-->
    <option value="banned" {{ old('status') == 'banned' ? 'selected' : '' }}>Banned</option>
</select>
                                    </div>

                                    <!-- Invisible Verified Field with default value 0 -->
                                    <input type="hidden" name="verified" value="0">

                                    <!-- Add the is_hidden field -->
                                    <div class="mb-1">
    <label for="is_hidden" class="form-label">Hide Email</label>
    <input type="checkbox" name="is_hidden" id="is_hidden" class="form-check-input" {{ old('is_hidden') ? 'checked' : '' }}>
    <!-- <span class="form-check-label">Hide Email</span> -->
</div>

                                    <div class="mb-1">
                                        <label for="description" class="form-label">Description:</label>
                                        <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                                    </div>

                                    <!-- Avatar Upload Field -->
                                    <div class="mb-1">
                                        <label for="avatar" class="form-label">Avatar:</label>
                                        <input type="file" name="avatar" id="avatar" class="form-control">
                                    </div>

                                    <!-- New Fields -->
                                 

                                    <!-- Postal Code Field -->
                                    <div class="mb-1">
                                        <label for="postal_code" class="form-label">Postal Code:</label>
                                        <input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ old('postal_code') }}">
                                    </div>

                                    <!-- Phone No Field -->
                                    <div class="mb-1">
                                        <label for="phone_no" class="form-label">Phone No:</label>
                                        <input type="text" name="phone_no" id="phone_no" class="form-control" value="{{ old('phone_no') }}">
                                    </div>

                                    <!-- Country Field -->
                                    <div class="mb-1">
                                        <label for="country" class="form-label">Country:</label>
                                        <input type="text" name="country" id="country" class="form-control" value="{{ old('country') }}">
                                    </div>

                                       <!-- City Field -->
                                       <div class="mb-1">
                                        <label for="city" class="form-label">City:</label>
                                        <input type="text" name="city" id="city" class="form-control" value="{{ old('city') }}">
                                    </div>

                                    <!-- Shipping Address Field -->
                                    <div class="mb-1">
                                        <label for="shipping_address" class="form-label">Shipping Address:</label>
                                        <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3">{{ old('shipping_address') }}</textarea>
                                    </div>

                                    

                                    <div class="d-flex gap-2 mt-1">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Create User</button>
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--/ Create User Form -->
        </div>
    </div>
</div>
@endsection
