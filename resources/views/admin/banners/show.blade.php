@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Create New Banner</h4>
                </div>
                <div class="card-body">

                    <!-- Form starts here -->
                    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Banner Type dropdown -->
                        <div class="form-group mt-2">
                            <label for="type">Select Banner Type</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="banner1">Banner1</option>
                                <option value="banner2">Banner2</option>
                                <option value="popup">Popup</option>
                            </select>
                        </div>

                        <!-- Title input -->
                        <div class="form-group mt-2">
                            <label for="title">Title</label>
                            <input type="text" name="title" id="title" class="form-control">
                        </div>

                        <!-- Image input -->
                        <div class="form-group mt-2">
                            <label for="image">Banner Image</label>
                            <input type="file" name="image" id="image" accept="image/*" class="form-control-file" required>
                        </div>


                        <!-- Link input -->
                        <div class="form-group mt-2">
                            <label for="link">Link</label>
                            <input  name="link" id="link" class="form-control" placeholder="Enter the URL for the banner">
                        </div>

                        <!-- Status dropdown -->
                        <div class="form-group mt-2 mb-2">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Create Banner</button>
                    </form>
                    <!-- Form ends here -->

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
