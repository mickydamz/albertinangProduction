{{--
    Params:
      $chartId    – canvas element id
      $title      – card heading text
      $icon       – Font Awesome icon class (e.g. fa-chart-line)
      $colorClass – Bootstrap color suffix (primary, success, danger, warning)
      $badgeId    – id for the top-right badge span
--}}
@php $loaderId = $chartId . 'Loader'; $emptyId = $chartId . 'Empty'; @endphp
<div class="card h-100">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between py-1">
        <h4 class="card-title mb-0" style="font-size:.95rem;">
            <i class="fas {{ $icon }} me-50 text-{{ $colorClass }}" style="font-size:15px;"></i>
            {{ $title }}
        </h4>
        <span class="badge bg-light-{{ $colorClass }} text-{{ $colorClass }}" id="{{ $badgeId }}">…</span>
    </div>
    <div class="card-body p-1" style="position:relative;min-height:220px;">
        <div id="{{ $loaderId }}" class="chart-loader">
            <div class="spinner-border text-{{ $colorClass }}" role="status" style="width:1.4rem;height:1.4rem;border-width:2px;"></div>
            <span class="ms-2 text-muted" style="font-size:12px;">Loading…</span>
        </div>
        <canvas id="{{ $chartId }}" style="display:none;max-height:240px;"></canvas>
        <div id="{{ $emptyId }}" class="chart-empty" style="display:none;">
            <i class="fas {{ $icon }}" style="font-size:30px;opacity:.25;"></i>
            <p class="text-muted mt-1 mb-0" style="font-size:12px;">No data yet.</p>
        </div>
    </div>
</div>
