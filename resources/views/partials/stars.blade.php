{{--
    Shared star renderer — the single source of truth for how a product rating
    is drawn, so every page (cards, category/search/brand listings, dashboard,
    product detail) shows the same number of stars for the same rating.

    Rounds to the nearest half-star:  frac >= .75 → next full, .25–.74 → half.
    Caller passes: ['rating' => $someRating]
--}}
@php
    $__r     = round((float) ($rating ?? 0) * 2) / 2;   // snap to nearest 0.5
    $__full  = (int) floor($__r);
    $__half  = ($__r - $__full) >= 0.5;
    $__empty = 5 - $__full - ($__half ? 1 : 0);
@endphp
@for($__i = 0; $__i < $__full; $__i++)<i class="fas fa-star"></i>@endfor
@if($__half)<i class="fas fa-star-half-alt"></i>@endif
@for($__i = 0; $__i < $__empty; $__i++)<i class="far fa-star"></i>@endfor
