{{--
    Reusable product card partial
    Usage: @include('partials.product-card', ['product' => $product])
    Variables available: $product (Eloquent model)
--}}

@php
    $firstImage  = $product->images->first();
    $imageUrl    = $firstImage
        ? asset('storage/' . $firstImage->image_url)
        : 'https://placehold.co/160x110/eef3e8/3d8012?text=No+Image';

    $hasDiscount     = isset($product->old_price) && $product->old_price > $product->price;
    $discountPercent = $hasDiscount
        ? round((($product->old_price - $product->price) / $product->old_price) * 100)
        : 0;

    $rating      = $product->review_rating;        // live average, 0 when no real reviews
    $ratingCount = $product->review_rating_count;   // real review count (matches products.show)

    $badge      = null;
    $badgeClass = 'pcard__badge';
    try {
        if ($hasDiscount && $discountPercent >= 5) {
            $badge       = '-' . $discountPercent . '%';
            $badgeClass .= ' pcard__badge--sale';
        } elseif ($product->created_at && \Carbon\Carbon::parse($product->created_at)->diffInDays(now()) <= 14) {
            $badge = 'NEW';
        }
    } catch (\Exception $e) {
        // badge stays null
    }
@endphp
<div class="pcard"
     data-product-id="{{ $product->id }}"
     data-price-ngn="{{ $product->price }}"
     role="article"
     aria-label="{{ $product->name }}">

    <div class="pcard__img">
        @if($badge)
            <span class="{{ $badgeClass }}">{{ $badge }}</span>
        @endif
        <img src="{{ $imageUrl }}"
             alt="{{ $product->name }}"
             loading="lazy"
             onerror="this.src='https://placehold.co/160x110/eef3e8/3d8012?text=No+Image'">
    </div>

    <div class="pcard__body">
        <div class="pcard__name">
            <a href="{{ route('product.show', $product->id) }}">{{ $product->name }}</a>
        </div>

        <div>
            <span class="pcard__price" data-price-ngn="{{ $product->price }}">
                ₦{{ number_format($product->price, 0) }}
            </span>
            @if($hasDiscount)
                <span class="pcard__old">₦{{ number_format($product->old_price, 0) }}</span>
            @endif
        </div>

        @if($ratingCount > 0)
            <div class="pcard__stars" aria-label="Rating: {{ $rating }} out of 5">
                @include('partials.stars', ['rating' => $rating])
                <span>({{ $ratingCount }})</span>
            </div>
        @endif
    </div>

    <button class="pcard__atc" aria-label="Add {{ $product->name }} to cart">
        <i class="fas fa-shopping-bag" style="font-size:12px;"></i>
        Add to Cart
    </button>
</div>