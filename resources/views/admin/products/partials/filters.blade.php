<div class="card-header border-bottom d-block">
 @if($errors->any())<div class="alert alert-danger" role="alert">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
 @php
   $filtersActive = filled($search) || filled($filterCategory) || filled($filterBrand)
       || ($filterStatus !== '' && $filterStatus !== null)
       || filled(request('inventory')) || filled(request('min_price')) || filled(request('max_price'));
 @endphp
 {{-- Mobile-only toggle: keeps the long filter form out of the way so the
      product list is visible right away. Hidden on md+ where filters always show. --}}
 <div class="d-flex d-md-none justify-content-between align-items-center">
  <span class="fw-semibold">Filters @if($filtersActive)<span class="badge bg-primary ms-1">on</span>@endif</span>
  <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#catalogueFilters" aria-expanded="{{ $filtersActive ? 'true' : 'false' }}" aria-controls="catalogueFilters">
   <i class="fas fa-sliders" aria-hidden="true"></i> Filters
  </button>
 </div>
 <form method="GET" action="{{ route('admin.products.index') }}" id="filterForm">
  <input type="hidden" name="sort" value="{{ $sortBy }}"><input type="hidden" name="dir" value="{{ $sortDir }}">
  <div class="collapse d-md-block {{ $filtersActive ? 'show' : '' }}" id="catalogueFilters">
   <div class="row g-2 pt-2 pt-md-0">
    <div class="col-md-4"><label for="catalogue-search" class="form-label">Search catalogue</label><input id="catalogue-search" type="search" name="search" class="form-control" value="{{ $search }}" placeholder="Name, brand, category or tag"></div>
    <div class="col-md-3"><label for="catalogue-category" class="form-label">Category</label><select id="catalogue-category" name="category" class="form-select"><option value="">All categories</option>@foreach($categories as $cat)<option value="{{ $cat->id }}" @selected($filterCategory == $cat->id)>{{ $cat->name }}</option>@endforeach</select></div>
    <div class="col-md-3"><label for="catalogue-brand" class="form-label">Brand</label><select id="catalogue-brand" name="brand" class="form-select"><option value="">All brands</option>@foreach($brands as $brand)<option value="{{ $brand->id }}" @selected($filterBrand == $brand->id)>{{ $brand->name }}</option>@endforeach</select></div>
    <div class="col-md-2"><label for="catalogue-status" class="form-label">Visibility</label><select id="catalogue-status" name="status" class="form-select"><option value="">All products</option><option value="1" @selected($filterStatus === '1')>Published</option><option value="0" @selected($filterStatus === '0')>Unpublished</option></select></div>
    <div class="col-md-3"><label for="catalogue-stock" class="form-label">Stock</label><select id="catalogue-stock" name="inventory" class="form-select"><option value="">Any stock level</option>@foreach(['in_stock'=>'In stock','low_stock'=>'Low stock (1–5)','out_of_stock'=>'Out of stock'] as $key=>$label)<option value="{{ $key }}" @selected(request('inventory') === $key)>{{ $label }}</option>@endforeach</select></div>
    <div class="col-md-3"><label for="catalogue-min" class="form-label">Minimum base price (₦)</label><input id="catalogue-min" type="number" min="0" step="0.01" name="min_price" class="form-control" value="{{ request('min_price') }}"></div>
    <div class="col-md-3"><label for="catalogue-max" class="form-label">Maximum base price (₦)</label><input id="catalogue-max" type="number" min="0" step="0.01" name="max_price" class="form-control" value="{{ request('max_price') }}"></div>
    <div class="col-md-3"><label for="catalogue-page" class="form-label">Products per page</label><select id="catalogue-page" name="per_page" class="form-select">@foreach([10,25,50,100] as $count)<option value="{{ $count }}" @selected(request('per_page',25) == $count)>{{ $count }}</option>@endforeach</select></div>
    <div class="col-12 d-flex gap-2 align-items-center mt-2 flex-wrap"><button class="btn btn-primary" type="submit">Apply filters</button><a class="btn btn-outline-secondary" href="{{ route('admin.products.index') }}">Clear filters</a><span role="status">{{ number_format($products->total()) }} products match</span></div>
   </div>
  </div>
 </form>
</div>
