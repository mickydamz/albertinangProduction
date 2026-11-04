@extends('layouts.adminlayout')
@section('content')
<a href="{{ route('admin.catalogue.index') }}">Back to catalogue approvals</a><h2>Review submission #{{ $revision->id }}</h2><p>{{ $revision->editor?->name ?? 'Deleted account' }} · {{ ucwords(str_replace('_',' ',$revision->status)) }}</p>
@if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
<div class="card"><div class="card-body">@include('catalogue.details')
@if($revision->status==='pending')<form action="{{ route('admin.catalogue.review',$revision) }}" method="POST">@csrf<input type="hidden" name="version" value="{{ $revision->version }}"><label class="form-label" for="review-notes">Review notes (required)</label><textarea class="form-control mb-1" id="review-notes" name="notes" required minlength="3" maxlength="2000">{{ old('notes') }}</textarea><button class="btn btn-primary" name="decision" value="approve">Approve and apply</button> <button class="btn btn-outline-warning" name="decision" value="return">Request changes</button><p class="mt-1">Approval applies the proposed visibility. A publication request makes the product visible in the shop.</p></form>@else<p>Reviewed by {{ $revision->reviewer?->name ?? '—' }} · {{ $revision->review_notes }}</p>@endif
<h4 class="mt-2">Change history</h4>@foreach($revision->events as $event)<div class="border-bottom py-1">{{ ucwords(str_replace('_',' ',$event->action)) }} · {{ $event->actor_name }} · {{ $event->created_at->format('d M Y H:i') }}<p>{{ $event->notes }}</p><details><summary>Saved version</summary>@include('catalogue.details',['draftData'=>$event->payload])</details></div>@endforeach
</div></div>
@endsection
