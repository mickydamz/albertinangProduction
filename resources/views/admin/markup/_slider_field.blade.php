{{--
    Reusable slider field partial.
    Variables expected:
      $fieldId  — unique HTML id
      $current  — current markup_percent value (null = no override)
      $label    — optional label string (null to hide)
--}}
@php $val = $current ?? 0; @endphp

<div class="slider-wrap">
    @if($label)
        <label for="{{ $fieldId }}" class="form-label mb-0 me-1 small fw-semibold">{{ $label }}</label>
    @endif

    <input
        type="range"
        id="{{ $fieldId }}"
        name="markup_percent"
        min="0"
        max="200"
        step="0.5"
        value="{{ $val }}"
        oninput="document.getElementById('{{ $fieldId }}_display').textContent = this.value + '%'"
    >
    <span id="{{ $fieldId }}_display" class="slider-val">{{ $val }}%</span>

    {{-- Hidden fallback so the value always submits --}}
    <input type="hidden" name="markup_percent" id="{{ $fieldId }}_hidden" value="{{ $val }}">
</div>

<script>
(function() {
    var slider = document.getElementById('{{ $fieldId }}');
    var hidden = document.getElementById('{{ $fieldId }}_hidden');
    if (slider && hidden) {
        slider.addEventListener('input', function () {
            hidden.value = this.value;
        });
    }
})();
</script>