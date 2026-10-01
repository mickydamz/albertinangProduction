@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Profile</h1>
    <form action="{{ route('user.profile.update') }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" required>
        </div>
        <div class="form-group">
            <label for="shipping_address">Shipping Address</label>
            <input type="text" name="shipping_address" id="shipping_address" class="form-control" value="{{ $user->shipping_address }}" required>
        </div>
        <div class="form-group">
            <label for="country">Country</label>
            <input type="text" name="country" id="country" class="form-control" value="{{ $user->country }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>
@endsection
