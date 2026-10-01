{{--
    Block Editor Field — include in create.blade.php and edit.blade.php
    Usage:
        @include('admin.partials.block-editor-field', [
            'blocks' => $product->description_blocks ?? null
        ])
--}}

<div class="mb-2">
    <label class="form-label fw-semibold">Rich Description (Block Editor)</label>
    <p class="text-muted" style="font-size:13px; margin-bottom:8px;">
        Build a rich product description using blocks — text, images, callouts, buttons, and more.
        The plain <strong>Description</strong> field above is still used as a short summary/fallback.
    </p>

    {{-- The editor mounts here --}}
    <div id="block-editor-mount"></div>

    {{-- Validation error --}}
    @error('description_blocks')
        <div class="text-danger mt-1" style="font-size:13px;">{{ $message }}</div>
    @enderror
</div>

{{-- Load the editor script (adjust path to wherever you put block-editor.js) --}}
<script src="{{ asset('js/block-editor.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var initial = @json($blocks ?? null);
        new BlockEditor('#block-editor-mount', initial, {
            hiddenName: 'description_blocks',
            htmlName:   'description_html',
            uploadUrl:  '{{ route("admin.upload-block-image") }}',
            csrfToken:  '{{ csrf_token() }}',
        });
    });
</script>