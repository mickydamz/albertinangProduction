<section class="al-workspace" aria-labelledby="workspaceHeading">
    <h2 id="workspaceHeading" style="font-size:20px; margin-bottom:6px;">Your store workspace</h2>
    <p style="color:var(--al-text-3);">Start a daily task or browse the sections below. Use the menu search to find any admin page.</p>
    <div class="al-shortcuts" aria-label="Common tasks">
        <a href="{{ route('admin.orders.index') }}">Manage orders</a>
        <a href="{{ route('admin.products.create') }}">Add a product</a>
        <a href="{{ route('admin.tickets.index') }}">Customer support</a>
        <a href="{{ route('admin.coupons.index') }}">Manage coupons</a>
    </div>
    <details>
        <summary style="cursor:pointer; margin-bottom:14px; font-weight:600;">Browse all administration sections</summary>
        <div class="al-directory">
            @foreach(config('admin_navigation', []) as $group)
                <div class="al-directory-card">
                    <h3><i class="fas fa-{{ $group['icon'] }}" aria-hidden="true"></i> {{ $group['label'] }}</h3>
                    <p>{{ $group['description'] }}</p>
                    @foreach($group['items'] as $item)
                        <a href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                    @endforeach
                </div>
            @endforeach
        </div>
    </details>
</section>
