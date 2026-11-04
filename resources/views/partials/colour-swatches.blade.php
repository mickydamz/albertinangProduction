@php($colourOptions=\App\Support\ProductColours::options($product))
@if(count($colourOptions))
<div class="product-colour-swatches" style="margin:12px 0;display:flex;gap:8px;align-items:center;flex-wrap:wrap" aria-label="Available colours">
@foreach($colourOptions as $colour)<a href="{{ route('product.show',$product->id) }}?colour={{ $colour['id'] }}" title="{{ $colour['name'] }}" aria-label="View {{ $product->name }} in {{ $colour['name'] }}" style="width:22px;height:22px;display:inline-block;border-radius:50%;border:1px solid #9ca3af;background:{{ $colour['swatch'] }};box-shadow:0 0 0 2px white" onclick="event.stopPropagation()"></a>@endforeach
<span style="font-size:11px;color:#53634c">{{ count($colourOptions) }} {{ count($colourOptions)===1 ? 'colour' : 'colours' }}</span></div>
@endif
