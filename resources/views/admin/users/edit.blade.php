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
                        <h2 class="content-header-title mb-0">Edit User</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Edit User Form -->
            <section id="edit-user-form" style="margin-top: 20px;">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-12">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Edit User Status, Role, and Verification</h4>
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                 
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Form with enctype for file upload -->
                                <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <!-- Name Field -->
                                    <div class="mb-1">
                                        <label for="name" class="form-label">Name:</label>
                                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                    </div>

                                    <!-- Email Field -->
                                    <div class="mb-1">
                                        <label for="email" class="form-label">Email:</label>
                                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                    </div>

                                    <!-- Registered At Field -->
<div class="mb-1">
    <label for="created_at" class="form-label">Registered At:</label>
    <input type="text"
           id="created_at"
           class="form-control"
           value="{{ $user->created_at->format('D, d M Y  H:i:s') }}"
           disabled>
</div>
                                    <!-- Account Balance Field -->
                                    <div class="mb-1">
                                        <label for="account_balance" class="form-label">Account Balance:</label>
                                        <input type="number" name="account_balance" id="account_balance" class="form-control" value="{{ old('account_balance', $user->account_balance) }}" min="0" step="0.01">
                                    </div>

                                    <!-- Description Field -->
                                    <div class="mb-1">
                                        <label for="description" class="form-label">Description:</label>
                                        <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $user->description) }}</textarea>
                                    </div>

                                    <!-- Role Field -->
                                    <div class="mb-1">
                                        <label for="role" class="form-label">Role:</label>
                                        <select name="role" id="role" class="form-control" required>
                                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                           <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                            <option value="manager" {{ $user->role == 'manager' ? 'selected' : '' }}>Manager</option>
                                           
                                        </select>
                                    </div>

                                    <!-- Status Field -->
                                    <div class="mb-1">
                                        <label for="status" class="form-label">Status:</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="green" {{ $user->status == 'green' ? 'selected' : '' }}>Normal</option>
                                            <!--<option value="yellow" {{ $user->status == 'yellow' ? 'selected' : '' }}>Yellow</option>-->
                                            <option value="ban" {{ $user->status == 'ban' ? 'selected' : '' }}>Ban</option>
                                        </select>
                                    </div>

                                    <!-- Verified Field -->
                                    <div class="mb-1">
                                        <label for="verified" class="form-label">Verified:</label>
                                        <select name="verified" id="verified" class="form-control" required>
                                            <option value="1" {{ $user->verified == 1 ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ $user->verified == 0 ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>

                                    <!-- Avatar Upload Field -->
                                    <div class="mb-1">
                                        <label for="avatar" class="form-label">Avatar:</label>
                                        <input type="file" name="avatar" id="avatar" class="form-control">
                                    </div>

                                    <!-- Avatar Display (if available) -->
                                    @if($user->avatar)
                                        <div class="mb-1">
                                            <label class="form-label">Current Avatar:</label>
                                            <img src="{{ Storage::url($user->avatar) }}" alt="User Avatar" class="img-fluid" style="max-width: 150px;">
                                        </div>
                                    @endif

                                    <!-- is_hidden Field (New) -->
                                    <div class="mb-1">
                                        <label for="is_hidden" class="form-label">Hide Email:</label>
                                        <select name="is_hidden" id="is_hidden" class="form-control" required>
                                            <option value="0" {{ $user->is_hidden == 0 ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ $user->is_hidden == 1 ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>

                                        <!-- Postal Code Field -->
                                        <div class="mb-1">
                                        <label for="postal_code" class="form-label">Postal Code:</label>
                                        <input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ old('postal_code', $user->postal_code) }}">
                                    </div>

                                    <!-- Phone No Field -->
                                    <div class="mb-1">
                                        <label for="phone_no" class="form-label">Phone No:</label>
                                        <input type="text" name="phone_no" id="phone_no" class="form-control" value="{{ old('phone_no', $user->phone_no) }}">
                                    </div>

                                    <!-- Country Field -->
                                    <div class="mb-1">
                                        <label for="country" class="form-label">Country:</label>
                                        <input type="text" name="country" id="country" class="form-control" value="{{ old('country', $user->country) }}">
                                    </div>

                                    <!-- City Field -->
                                    <div class="mb-1">
                                        <label for="city" class="form-label">City:</label>
                                        <input type="text" name="city" id="city" class="form-control" value="{{ old('city', $user->city) }}">
                                    </div>

                                    <!-- Shipping Address Field -->
                                    <div class="mb-1">
                                        <label for="shipping_address" class="form-label">Shipping Address:</label>
                                        <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3">{{ old('shipping_address', $user->shipping_address) }}</textarea>
                                    </div>

                                    <div class="d-flex gap-2 mt-1">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Changes</button>
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--/ Edit User Form -->
        </div>
    </div>
</div>
@endsection
