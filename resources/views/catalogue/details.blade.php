@php
    $proposed = $draftData ?? $revision->payload;
    $current = $revision->base_snapshot['attributes'] ?? [];
    $brandName = fn($id) => \App\Models\Brand::find($id)?->name ?? 'No brand';
    $categoryName = fn($id) => \App\Models\Category::find($id)?->name ?? 'Not selected';
    $typeName = fn($id) => \App\Models\Subcategory::find($id)?->name ?? 'Not selected';
    $rows = [
        'Product name'=>[$current['name'] ?? '—',$proposed['name'] ?? '—'],
        'Brand'=>[isset($current['brand_id']) ? $brandName($current['brand_id']) : '—',$brandName($proposed['brand_id'] ?? null)],
        'Category'=>[isset($current['category_id']) ? $categoryName($current['category_id']) : '—',$categoryName($proposed['category_id'] ?? null)],
        'Product type'=>[isset($current['subcategory_id']) ? $typeName($current['subcategory_id']) : '—',$typeName($proposed['subcategory_id'] ?? null)],
        'Base price (₦)'=>[isset($current['price']) ? number_format($current['price'],2) : '—',number_format($proposed['price'] ?? 0,2)],
        'Stock'=>[$current['stock'] ?? '—',$proposed['stock'] ?? 0],
        'Minimum order quantity'=>[$current['moq'] ?? '—',$proposed['moq'] ?? 1],
        'Weight (kg)'=>[$current['weight_kg'] ?? 'Inherit',$proposed['weight_kg'] ?? 'Inherit'],
        'Truck delivery'=>[!isset($current['requires_truck']) ? 'Inherit' : ($current['requires_truck'] ? 'Required' : 'Not required'),empty($proposed['requires_truck_set']) ? 'Inherit' : (!empty($proposed['requires_truck']) ? 'Required' : 'Not required')],
        'Shop visibility'=>[isset($current['is_active']) ? ($current['is_active'] ? 'Published' : 'Unpublished') : 'Not in shop',!empty($proposed['publish_after_approval']) ? 'Publish after approval' : 'Keep unpublished after approval'],
    ];
@endphp
<h4>{{ $proposed['name'] ?? 'Product' }}</h4>
<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Detail</th><th>Current product</th><th>Proposed</th></tr></thead><tbody>
@foreach($rows as $label=>$values)<tr><th>{{ $label }}</th><td>{{ $values[0] }}</td><td>{{ $values[1] }}</td></tr>@endforeach
</tbody></table></div>
<h5 class="mt-2">Description</h5><p style="white-space:pre-wrap">{{ $proposed['description'] ?? 'No plain description' }}</p>
@foreach(['tags'=>\App\Models\Tag::class,'sizes'=>\App\Models\Size::class,'colors'=>\App\Models\Color::class] as $relation=>$model)
<p><strong>{{ ucfirst($relation) }}:</strong> @forelse($proposed[$relation] ?? [] as $input) {{ ctype_digit((string)$input) ? ($model::find($input)?->name ?? 'Removed option') : $input }}{{ !$loop->last ? ', ' : '' }} @empty None @endforelse</p>
@endforeach
@foreach(($proposed['custom_attributes'] ?? [])['tag_options'] ?? [] as $name=>$options)<p><strong>{{ $name }} options:</strong> {{ implode(', ',$options) }}</p>@endforeach
<h5>Stores holding the product</h5>
<p><strong>Current:</strong> @foreach($revision->base_snapshot['pickup_points'] ?? [] as $pointId) {{ \App\Models\PickupPoint::find($pointId)?->name ?? 'Removed store' }}{{ !$loop->last ? ', ' : '' }} @endforeach</p>
<p><strong>Proposed:</strong> @forelse($proposed['pickup_points'] ?? [] as $pointId) @php($point=\App\Models\PickupPoint::with('state')->find($pointId)) {{ $point?->state?->name }} → {{ $point?->name ?? 'Removed store' }} — {{ $point?->address }}{{ !$loop->last ? '; ' : '' }} @empty Not assigned @endforelse</p>
<h5 class="mt-2">Specifications</h5>
@forelse($proposed['custom_attribute_groups'] ?? [] as $group)<h6>{{ $group['group'] }}</h6><dl>@foreach($group['attrs'] ?? [] as $attr)<dt>{{ $attr['key'] }}</dt><dd>{{ $attr['value'] }}</dd>@endforeach</dl>@empty<dl>@foreach($proposed['custom_attributes'] ?? [] as $key=>$value)@if($key !== 'tag_options' && is_scalar($value))<dt>{{ $key }}</dt><dd>{{ $value }}</dd>@endif
@endforeach</dl>@endforelse
<h5 class="mt-2">Installation options</h5>@forelse($proposed['installation_options'] ?? [] as $option)<p>{{ $option['label'] }} · ₦{{ number_format($option['price'] ?? 0,2) }}<br>{{ $option['description'] ?? '' }}</p>@empty<p>None</p>@endforelse
@if(!empty($proposed['description_blocks']))<h5 class="mt-2">Rich description preview</h5>@include('partials.block-renderer',['blocks'=>$proposed['description_blocks']])@endif
<h5 class="mt-2">Gallery images</h5><div class="d-flex flex-wrap gap-1">
@foreach($revision->base_snapshot['images'] ?? [] as $image)<figure><img style="width:120px;height:120px;object-fit:contain" src="{{ asset('storage/'.$image['image_url']) }}" alt="Current product image"><figcaption>{{ in_array($image['id'],$proposed['remove_images'] ?? []) ? 'Remove on approval' : 'Keep existing image' }}</figcaption></figure>@endforeach
@foreach($revision->uploads()->where('kind','gallery')->get() as $upload)<figure><img style="width:120px;height:120px;object-fit:contain" src="{{ asset('storage/'.$upload->path) }}" alt="Proposed product image"><figcaption>{{ in_array($upload->id,$proposed['remove_uploads'] ?? []) ? 'Excluded' : 'Add on approval' }}</figcaption></figure>@endforeach</div>
