@extends('layouts.adminlayout')
@section('content')
<div class="app-content content"><div class="content-wrapper container-xxl"><h1>Email campaigns</h1>
<p>{{ $audience }} registered customers currently opted in.</p>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<div class="card p-3"><h2>Create a promotion</h2><form method="POST" action="{{ route('admin.marketing.store') }}">@csrf
<label for="subject">Subject</label><input class="form-control mb-2" id="subject" name="subject" maxlength="150" value="{{ old('subject') }}" required>
<label for="message">Message</label><textarea class="form-control mb-2" id="message" name="message" rows="8" maxlength="20000" required>{{ old('message') }}</textarea>
<label for="offer_url">Offer link (optional, HTTPS)</label><input class="form-control mb-2" type="url" id="offer_url" name="offer_url" value="{{ old('offer_url') }}">
<button class="btn btn-primary">Save draft &amp; preview</button></form></div>
<h2>Campaign history</h2><table class="table"><thead><tr><th>Subject</th><th>Status</th><th>Recipients</th></tr></thead><tbody>
@forelse($campaigns as $campaign)<tr><td><a href="{{ route('admin.marketing.show', $campaign) }}">{{ $campaign->subject }}</a></td><td>{{ $campaign->status }}</td><td>{{ $campaign->deliveries_count }}</td></tr>
@empty<tr><td colspan="3">No campaigns yet.</td></tr>@endforelse
</tbody></table>{{ $campaigns->links() }}</div></div>
@endsection
