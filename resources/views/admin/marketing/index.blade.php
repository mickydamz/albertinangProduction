@extends('layouts.adminlayout')
@section('content')
<div class="app-content content"><div class="content-wrapper container-xxl">
<div class="promo-head"><div><p class="text-muted mb-1">MARKETING WORKSPACE</p><h1>Email campaigns</h1><p>Branded promotions, made for your customers.</p></div><div class="promo-brand"><strong><span data-subscriber-total>{{ $subscribedTotal }}</span> people opted in</strong><br><span data-subscriber-customers>{{ $audience }}</span> eligible customer accounts<br>AlbertinaNG · promotions@albertinang.com</div></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@if(!config('marketing.enabled'))<p class="alert alert-warning">Campaign sending is disabled. You can prepare drafts, pictures and previews.</p>@endif
<div class="card p-3 mb-3" id="subscribers"><div class="promo-head"><h2>People opted in</h2><a class="btn btn-outline-primary" href="{{ route('admin.marketing.index') }}#subscribers">Refresh subscriber list</a></div>
<p class="text-muted">All accounts that saved email consent appear here, including test and staff accounts. Only customer accounts receive customer campaigns. Counts refresh every 30 seconds; refresh the list to see new names.</p>
<div class="promo-scroll"><table class="table"><thead><tr><th>Name</th><th>Email</th><th>Campaign eligibility</th><th>Opted in</th></tr></thead><tbody>
@forelse($subscribers as $subscriber)<tr><td>{{ $subscriber->name }}</td><td>{{ $subscriber->email }}</td><td>{{ $subscriber->role === 'user' ? 'Eligible customer' : 'Staff account — excluded from customer campaigns' }}</td><td>{{ \Carbon\Carbon::parse($subscriber->marketing_subscribed_at)->format('d M Y H:i') }} UTC</td></tr>@empty<tr><td colspan="4">No accounts have opted in yet.</td></tr>@endforelse
</tbody></table></div>{{ $subscribers->fragment('subscribers')->links() }}</div>
<div class="card p-3"><h2>Create a promotion</h2><p class="text-muted">Choose a layout, add your message and pictures, then review the finished email.</p>@include('admin.marketing.form')</div>
<div class="promo-head"><h2>Campaign history</h2><form method="GET"><label for="status-filter">Campaign status</label><select name="status" id="status-filter" class="form-control" onchange="this.form.submit()"><option value="">All campaigns</option>@foreach(['draft','scheduled','queued','paused','completed','cancelled'] as $status)<option @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select></form></div>
<div class="promo-scroll"><table class="table"><thead><tr><th>Promotion</th><th>Status</th><th>Recipients</th><th>Created</th></tr></thead><tbody>
@forelse($campaigns as $campaign)<tr><td><a href="{{ route('admin.marketing.show',$campaign) }}">{{ $campaign->subject }}</a><br><small class="text-muted">{{ $campaign->template }}</small></td><td><span class="promo-label">{{ ucfirst($campaign->status) }}</span></td><td>{{ $campaign->deliveries_count }}</td><td>{{ $campaign->created_at->format('d M Y') }}</td></tr>@empty<tr><td colspan="4">Your saved campaigns will appear here.</td></tr>@endforelse
</tbody></table></div>{{ $campaigns->links() }}</div></div>
@include('admin.marketing.styles')
<script>
(function () {
    async function refreshCounts() {
        if (document.hidden) return;
        try {
            const response = await fetch(@json(route('admin.marketing.index', ['subscriber_counts' => 1])), {headers: {'Accept': 'application/json'}, cache: 'no-store'});
            if (!response.ok) return;
            const counts = await response.json();
            document.querySelector('[data-subscriber-total]').textContent = counts.total;
            document.querySelector('[data-subscriber-customers]').textContent = counts.customers;
        } catch (_) { /* Retain the last confirmed counts when offline. */ }
    }
    setInterval(refreshCounts, 30000);
    document.addEventListener('visibilitychange', refreshCounts);
})();
</script>
@endsection
