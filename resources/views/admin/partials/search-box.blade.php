{{--
    Consistent, responsive admin search box.
    Params:
      $action      – form action URL (the index route)
      $value       – current search string (e.g. $search)
      $placeholder – input placeholder text
--}}
<form method="GET" action="{{ $action }}"
      class="d-flex flex-wrap gap-1 ms-md-auto"
      style="flex:1 1 300px; max-width:520px; min-width:0;">
    <input type="search" name="search" value="{{ $value ?? '' }}"
           class="form-control form-control-sm"
           style="flex:1 1 150px; min-width:0;"
           placeholder="{{ $placeholder ?? 'Search…' }}">
    <button type="submit" class="btn btn-sm btn-primary text-nowrap" style="min-width:92px;">
        <i class="fas fa-search me-1"></i> Search
    </button>
    @if(!empty($value))
        <a href="{{ $action }}" class="btn btn-sm btn-outline-secondary">Clear</a>
    @endif
</form>
