@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Select Country</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.country.submit') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="country">Select Country</label>
                <select name="country" id="country" class="form-control" required>
                    <option value="">-- Select Country --</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Continue</button>
        </form>
    </div>
@endsection
