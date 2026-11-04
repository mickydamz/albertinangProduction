@if(session('success'))<div class="alert alert-success p-2" role="status">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-warning p-2" role="alert">{{ session('error') }}</div>@endif
@if($errors->any())<div class="alert alert-danger p-2" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
