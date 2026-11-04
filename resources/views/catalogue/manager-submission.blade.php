@if(isset($submission) && $submission)
<div class="alert alert-info"><strong>Publication review: {{ ucwords(str_replace('_',' ',$submission->status)) }}</strong>@if($submission->review_notes)<p>Admin feedback: {{ $submission->review_notes }}</p>@endif
@if($submission->status==='pending')<p>Editing the product while review is pending will require a fresh submission.</p>@endif</div>
@endif
@if(!$product->is_active && (!isset($submission) || !$submission || $submission->status !== 'pending'))
<form class="mb-2" method="POST" action="{{ route('manager.products.submit',$product) }}">@csrf<button class="btn btn-outline-primary">Send to admin for publication approval</button><p class="text-muted small mt-1">Save your product changes first. Only the administrator can publish.</p></form>
@endif
