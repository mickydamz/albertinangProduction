<div class="al-nav-tools">
    <div class="d-flex align-items-center justify-content-between">
        <label for="adminMenuSearch">Find an admin page</label>
        <button type="button" class="al-nav-close" id="adminNavClose" aria-label="Close navigation">Close</button>
    </div>
    <input type="search" id="adminMenuSearch" placeholder="Orders, coupons, invoices…" autocomplete="off">
    <small id="adminNavResults" role="status" aria-live="polite"></small>
</div>
<nav class="al-dnav" aria-label="Administration">
    <a href="{{ route('admin.dashboard') }}" class="al-dnav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif><i class="fas fa-home ni" aria-hidden="true"></i> Overview</a>
    @foreach($adminNavigation as $group)
        @php
            $groupActive = collect($group['items'])->contains(fn($item) => request()->routeIs(...$item['matches']));
        @endphp
        <details class="al-nav-group" data-admin-nav-group="{{ $group['label'] }}" @if($groupActive) open @endif>
            <summary><i class="fas fa-{{ $group['icon'] }}" aria-hidden="true"></i>{{ $group['label'] }}</summary>
            <div class="al-dsub">
                @foreach($group['items'] as $item)
                    @php $active = request()->routeIs(...$item['matches']); @endphp
                    <a href="{{ route($item['route']) }}" data-nav-search="{{ $item['label'] }} {{ $item['description'] }}" class="{{ $active ? 'al-active' : '' }}" @if($active) aria-current="page" @endif title="{{ $item['description'] }}"><i class="fas fa-{{ $item['icon'] }}" aria-hidden="true"></i>{{ $item['label'] }}</a>
                @endforeach
            </div>
        </details>
    @endforeach
    <p id="adminNavEmpty" hidden role="status" class="p-2">No pages found. Try orders, products or settings.</p>
</nav>
