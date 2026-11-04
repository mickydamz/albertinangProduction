@if($revision->exists)
<div class="alert alert-info"><strong>Submission #{{ $revision->id }} · {{ ucwords(str_replace('_',' ',$revision->status)) }}</strong><p>The shop changes only after admin approval.</p>@if($revision->review_notes)<p>Admin feedback: {{ $revision->review_notes }}</p>@endif</div>
<div class="d-flex flex-wrap gap-1 mb-2">
@if($revision->status === 'pending')<form method="POST" action="{{ route('catalogue.withdraw',$revision) }}">@csrf<input type="hidden" name="version" value="{{ $revision->version }}"><button class="btn btn-outline-secondary">Withdraw to edit</button></form>@endif
@if(in_array($revision->status,['draft','changes_requested']))
<form method="POST" action="{{ route('catalogue.archive',$revision) }}">@csrf<input type="hidden" name="version" value="{{ $revision->version }}"><button class="btn btn-outline-secondary">Archive draft</button></form>
@if($revision->product_id)<form method="POST" action="{{ route('catalogue.refresh',$revision) }}" onsubmit="return confirm('Replace proposed edits with the current product? Earlier edits remain in history.')">@csrf<input type="hidden" name="version" value="{{ $revision->version }}"><button class="btn btn-outline-secondary">Refresh from current product</button></form>@endif
@endif</div>
<details class="mb-2"><summary>Saved details and change history</summary>@include('catalogue.details')@foreach($revision->events as $event)<div class="border-bottom py-1"><strong>{{ ucwords(str_replace('_',' ',$event->action)) }}</strong> · {{ $event->actor_name }} · {{ $event->created_at->format('d M Y H:i') }}<p>{{ $event->notes }}</p><details><summary>Saved version</summary>@include('catalogue.details',['draftData'=>$event->payload])</details></div>@endforeach</details>
@endif
