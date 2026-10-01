@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="col-12 mb-2">
                        <h2 class="content-header-title mb-0">Create Affiliate</h2>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.affiliates.index') }}">Affiliates</a></li>
                                <li class="breadcrumb-item active">Create Affiliate</li>
                            </ol>
            </div>
        </div>

        <div class="content-body">
            <h1>Create New Affiliate</h1>

            <form action="{{ route('admin.affiliates.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Affiliate Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="affiliate_code">Affiliate Code</label>
                    <input type="text" name="affiliate_code" id="affiliate_code" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="referred_by">Referred By (optional)</label>
                    <input type="text" name="referred_by" id="referred_by" class="form-control">
                </div>
                <!-- Add Password Fields -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Create Affiliate</button>
            </form>
        </div>
    </div>
</div>
@endsection
