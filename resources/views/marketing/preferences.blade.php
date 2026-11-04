@extends('layouts.simslayout')
@section('content')
<div class="container" style="padding:50px 20px;max-width:700px"><h1>Email preferences</h1>
@if(session('success'))<p role="status">{{ session('success') }}</p>@endif
<form method="POST" action="{{ route('marketing.preferences.update') }}">@csrf
<label><input type="checkbox" name="subscribe" value="1" @checked($subscribed)> Send me AlbertinaNG offers, sales and marketing emails.</label>
<p>You can unsubscribe at any time. Order and account emails are separate.</p><button class="btn btn-primary">Save preferences</button>
</form></div>
@endsection
