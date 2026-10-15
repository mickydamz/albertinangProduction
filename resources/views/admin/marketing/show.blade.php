@extends('layouts.adminlayout')
@section('content')
<div class="app-content content"><div class="content-wrapper container-xxl"><a href="{{ route('admin.marketing.index') }}">All campaigns</a><h1>{{ $campaign->subject }}</h1>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<p>Status: {{ $campaign->status }}</p><div class="card p-3"><div style="white-space:pre-line">{{ $campaign->message }}</div>
@if($campaign->offer_url)<p><a href="{{ $campaign->offer_url }}">View offer</a></p>@endif</div>
<p><a class="btn btn-outline-primary" target="_blank" rel="noopener" href="{{ route('admin.marketing.preview', $campaign) }}">Preview email</a></p>
@if(!config('marketing.enabled'))<p class="alert alert-warning">Email sending is disabled in this environment. Drafts and previews are available.</p>
@else<form method="POST" action="{{ route('admin.marketing.test', $campaign) }}">@csrf<button class="btn btn-secondary">Send test to my admin email</button></form>
@if($campaign->status === 'draft')<form class="mt-3" method="POST" action="{{ route('admin.marketing.send', $campaign) }}">@csrf
<label><input type="checkbox" name="confirm" value="1" required> I have reviewed this promotion and want to send it to opted-in registered customers.</label><br><button class="btn btn-primary mt-2">Queue campaign</button></form>@endif
@endif
@if($campaign->status === 'draft')
<details class="card p-3 mt-3"><summary>Edit draft</summary><form method="POST" action="{{ route('admin.marketing.update', $campaign) }}">@csrf @method('PUT')
<label for="subject">Subject</label><input class="form-control mb-2" id="subject" name="subject" maxlength="150" value="{{ old('subject', $campaign->subject) }}" required>
<label for="message">Message</label><textarea class="form-control mb-2" id="message" name="message" rows="8" maxlength="20000" required>{{ old('message', $campaign->message) }}</textarea>
<label for="offer_url">Offer link (optional, HTTPS)</label><input class="form-control mb-2" type="url" id="offer_url" name="offer_url" value="{{ old('offer_url', $campaign->offer_url) }}">
<button class="btn btn-primary">Save changes</button></form></details>
@endif
<h2 class="mt-3">Delivery progress</h2><p>Sent means accepted by the mail transport; it does not confirm inbox delivery.</p>
@foreach(['pending', 'sending', 'sent', 'skipped', 'failed'] as $status)<p>{{ ucfirst($status) }}: {{ $counts[$status] ?? 0 }}</p>@endforeach
</div></div>
@endsection
