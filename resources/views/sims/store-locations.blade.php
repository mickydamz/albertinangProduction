@extends('layouts.simslayout')

@section('title', 'Store Locations — ' . ($appStoreName ?? 'Albertina Nigeria'))

@section('content')

<style>
    .sl-hero {
        background: linear-gradient(135deg, var(--g700) 0%, var(--g600) 100%);
        padding: 52px var(--gutter) 40px;
        text-align: center;
        color: #fff;
    }
    .sl-hero h1 {
        font-family: var(--fh);
        font-size: clamp(1.75rem, 4vw, 2.5rem);
        font-weight: 800;
        margin: 0 0 8px;
        letter-spacing: -.5px;
    }
    .sl-hero p {
        font-size: 15px;
        color: rgba(255,255,255,.75);
        margin: 0;
    }
    .sl-breadcrumb {
        font-size: 13px;
        color: rgba(255,255,255,.55);
        margin-bottom: 16px;
        display: flex;
        justify-content: center;
        gap: 6px;
        align-items: center;
    }
    .sl-breadcrumb a { color: rgba(255,255,255,.7); text-decoration: none; }
    .sl-breadcrumb a:hover { color: #fff; }
    .sl-breadcrumb span { color: rgba(255,255,255,.35); }

    .sl-body {
        max-width: var(--max);
        margin: 0 auto;
        padding: 40px var(--gutter) 64px;
    }

    .sl-count {
        font-size: 13px;
        color: var(--ink3);
        margin-bottom: 24px;
    }

    .sl-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    .sl-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-lg);
        box-shadow: var(--sh-sm);
        overflow: hidden;
        transition: box-shadow .2s, transform .2s;
        display: flex;
        flex-direction: column;
    }
    .sl-card:hover {
        box-shadow: var(--sh-md);
        transform: translateY(-2px);
    }

    .sl-card__accent {
        height: 5px;
        background: linear-gradient(90deg, var(--g500), var(--g400));
    }

    .sl-card__body {
        padding: 20px 22px;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .sl-card__name {
        font-family: var(--fh);
        font-size: 17px;
        font-weight: 700;
        color: var(--ink);
        margin: 0 0 2px;
    }

    .sl-card__city {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: var(--g600);
        background: var(--g50);
        border-radius: 20px;
        padding: 3px 10px;
        width: fit-content;
        margin-bottom: 10px;
    }

    .sl-card__detail {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        font-size: 13.5px;
        color: var(--ink3);
        line-height: 1.4;
        padding: 6px 0;
        border-bottom: 1px solid var(--border);
    }
    .sl-card__detail:last-of-type { border-bottom: none; }
    .sl-card__detail i {
        width: 16px;
        color: var(--g500);
        flex-shrink: 0;
        margin-top: 2px;
        font-size: 12px;
    }

    .sl-card__footer {
        padding: 14px 22px 20px;
    }

    .sl-btn-directions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        width: 100%;
        padding: 10px 0;
        background: var(--g500);
        color: #fff;
        border-radius: var(--r);
        font-family: var(--fh);
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        transition: background .15s;
    }
    .sl-btn-directions:hover { background: var(--g600); color: #fff; }

    .sl-empty {
        text-align: center;
        padding: 80px 20px;
        color: var(--ink3);
    }
    .sl-empty i { font-size: 48px; color: var(--border2); margin-bottom: 16px; display: block; }
    .sl-empty h3 { font-family: var(--fh); color: var(--ink2); margin-bottom: 8px; }

    @media (max-width: 1024px) { .sl-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px)  { .sl-grid { grid-template-columns: 1fr; gap: 16px; } }
</style>

<section class="sl-hero">
    <div class="sl-breadcrumb">
        <a href="/">Home</a>
        <span>›</span>
        <span>Store Locations</span>
    </div>
    <h1>Our Store Locations</h1>
    <p>Visit any of our stores to experience AlbertinaNG products in person.</p>
</section>

<div class="sl-body">

    @if($locations->isEmpty())
        <div class="sl-empty">
            <i class="fas fa-store-slash"></i>
            <h3>No store locations listed yet</h3>
            <p>Check back soon — we're expanding our network.</p>
        </div>
    @else
        <p class="sl-count">{{ $locations->count() }} {{ Str::plural('location', $locations->count()) }} found</p>

        <div class="sl-grid">
            @foreach($locations as $loc)
                @php
                    $mapsQuery = urlencode(($loc->address ?? '') . ' ' . ($loc->location->name ?? '') . ' Nigeria');
                    $mapsUrl   = 'https://www.google.com/maps/search/?api=1&query=' . $mapsQuery;
                    $hours     = $loc->hours ?? 'Mon–Sat: 9am – 6pm';
                @endphp
                <div class="sl-card">
                    <div class="sl-card__accent"></div>
                    <div class="sl-card__body">
                        <h2 class="sl-card__name">{{ $loc->name }}</h2>

                        @if($loc->location)
                            <span class="sl-card__city">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $loc->location->name }}
                            </span>
                        @endif

                        @if($loc->address)
                            <div class="sl-card__detail">
                                <i class="fas fa-location-dot"></i>
                                <span>{{ $loc->address }}</span>
                            </div>
                        @endif

                        <div class="sl-card__detail">
                            <i class="far fa-clock"></i>
                            <span>{{ $hours }}</span>
                        </div>
                    </div>
                    <div class="sl-card__footer">
                        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" class="sl-btn-directions">
                            <i class="fas fa-route"></i> Get Directions
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

@endsection
