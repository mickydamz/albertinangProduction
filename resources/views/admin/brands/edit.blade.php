@extends('layouts.adminlayout')

@section('content')

<style>
    /* ── Upload Zone ── */
    .upload-drop-zone {
        border: 2px dashed #d8d6de;
        border-radius: 0.428rem;
        padding: 2rem 1rem;
        text-align: center;
        cursor: pointer;
        background: #f8f8f8;
        transition: border-color 0.2s ease, background 0.2s ease;
        /* NO position:relative, NO child file input inside */
    }
    .upload-drop-zone.dragover {
        border-color: #7367f0;
        background: #f0effe;
    }
    .upload-drop-zone.dragover .upload-drop-icon { background: #7367f0; color: #fff; }
    .upload-drop-zone.dragover .upload-drop-title { color: #7367f0; }
    .upload-drop-zone.dragover .upload-drop-sub   { color: #7367f0; }

    .upload-drop-icon {
        width: 56px; height: 56px;
        background: #ede9fe; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 0.75rem;
        color: #7367f0; font-size: 1.4rem;
        transition: background 0.2s, color 0.2s;
    }
    .upload-drop-zone:hover .upload-drop-icon { background: #7367f0; color: #fff; }
    .upload-drop-title { font-size: 0.95rem; font-weight: 600; color: #5e5873; margin-bottom: 0.2rem; }
    .upload-drop-title span { color: #7367f0; text-decoration: underline; cursor: pointer; }
    .upload-drop-sub { font-size: 0.8rem; color: #b9b9c3; }

    /* ── Current logo card ── */
    .current-logo-card {
        display: flex; align-items: center; gap: 1rem;
        padding: 0.85rem 1rem;
        border: 1px solid #ebe9f1; border-radius: 0.428rem;
        background: #f8f8f8; margin-bottom: 1rem;
        transition: opacity .2s;
    }
    .current-logo-card img {
        width: 72px; height: 72px; object-fit: contain;
        border: 1px solid #ebe9f1; border-radius: 0.357rem;
        background: #fff; padding: 4px;
    }
    .current-logo-card .logo-meta { flex: 1; }
    .current-logo-card .logo-meta strong { display: block; font-size: 0.85rem; color: #5e5873; }
    .current-logo-card .logo-meta small { color: #b9b9c3; font-size: 0.77rem; }
    .no-logo-card {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.75rem 1rem;
        border: 1px dashed #d8d6de; border-radius: 0.428rem;
        background: #f8f8f8; margin-bottom: 1rem; color: #b9b9c3;
        font-size: 0.85rem;
    }

    /* ── New upload preview ── */
    #logo-preview-wrap { margin-top: 0.85rem; display: none; }
    #logo-preview-img {
        width: 100px; height: 100px; object-fit: contain;
        border: 1px solid #ebe9f1; border-radius: 0.428rem;
        background: #f8f8f8; padding: 6px;
        display: block;
    }
    .logo-preview-label {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.5px; color: #b9b9c3; margin-bottom: 0.4rem;
    }

    /* ── File chip ── */
    .file-chip {
        display: none;
        margin-top: 0.6rem;
        font-size: 0.78rem;
        background: #ede9fe;
        color: #7367f0;
        border-radius: 20px;
        padding: 0.28rem 0.75rem;
        align-items: center;
        gap: 0.4rem;
        width: fit-content;
    }

    /* ── Section divider ── */
    .form-section-title {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.6px; color: #b9b9c3;
        border-bottom: 1px solid #ebe9f1;
        padding-bottom: 0.4rem; margin-bottom: 1rem; margin-top: 1.5rem;
    }

    /* ── Slug helper ── */
    #slug-preview { font-size: 0.78rem; color: #7367f0; margin-top: 0.3rem; min-height: 1.1rem; }

    /* ── Remove logo toggle ── */
    #remove-logo-wrap {
        padding: 0.6rem 0.85rem;
        border: 1px solid #ffd4d4; border-radius: 0.357rem;
        background: #fff5f5; margin-top: 0.65rem;
        display: flex; align-items: center; gap: 0.5rem;
        font-size: 0.85rem; color: #ea5455;
    }
    #remove-logo-wrap input[type="checkbox"] { accent-color: #ea5455; }
</style>

{{-- 
    FILE INPUT IS OUTSIDE THE DROP ZONE — hidden, triggered by JS.
    This is the key fix: when the input lives inside the zone it
    silently swallows drag events even with pointer-events:none.
    Moving it outside makes the zone div own every drag event cleanly.
--}}
<input type="file" name="logo" id="logo" style="display:none;"
       accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml,image/webp"
       form="brand-form">

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        {{-- Breadcrumb --}}
        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Edit Brand</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.brands.index') }}">Brands</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
                </div>
            </div>
        </div>

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                <div class="alert-body">
                    <i class="fas fa-circle-exclamation me-50"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-50">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="content-body">
            <section id="edit-brand">
                <div class="row justify-content-center">
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header">Edit Brand: <strong>{{ $brand->name }}</strong></div>
                            <div class="card-body">

                                <form id="brand-form"
                                      action="{{ route('admin.brands.update', $brand->id) }}"
                                      method="POST"
                                      enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <p class="form-section-title">Brand Information</p>

                                    {{-- Name --}}
                                    <div class="mb-1">
                                        <label for="name" class="form-label">
                                            Brand Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name" id="name"
                                               class="form-control @error('name') is-invalid @enderror"
                                               value="{{ old('name', $brand->name) }}"
                                               required autofocus>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    {{-- Slug --}}
                                    <div class="mb-1">
                                        <label for="slug" class="form-label">
                                            Slug
                                            <small class="text-muted ms-25">Leave blank to auto-generate from name</small>
                                        </label>
                                        <input type="text" name="slug" id="slug"
                                               class="form-control @error('slug') is-invalid @enderror"
                                               value="{{ old('slug', $brand->slug) }}">
                                        <div id="slug-preview"></div>
                                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    

                                    <p class="form-section-title">Brand Logo</p>

                                    {{-- Current logo --}}
                                    @if ($brand->logo)
                                        <div class="current-logo-card" id="current-logo-card">
                                            <img src="{{ Storage::url($brand->logo) }}" alt="{{ $brand->name }}">
                                            <div class="logo-meta">
                                                <strong>Current Logo</strong>
                                                <small>Upload a new file below to replace it, or use the remove option.</small>
                                            </div>
                                        </div>
                                        <div id="remove-logo-wrap">
                                            <input type="checkbox" name="remove_logo" id="remove_logo" value="1"
                                                   {{ old('remove_logo') ? 'checked' : '' }}>
                                            <label for="remove_logo" style="cursor:pointer;margin:0;">
                                                <i class="fas fa-trash-alt me-50"></i> Remove current logo
                                            </label>
                                        </div>
                                    @else
                                        <div class="no-logo-card">
                                            <i class="fas fa-image fa-lg"></i>
                                            <span>No logo uploaded yet.</span>
                                        </div>
                                    @endif

                                    {{-- Drop zone — no file input inside --}}
                                    <div class="upload-drop-zone mt-1" id="upload-zone">
                                        <div class="upload-drop-icon">
                                            <i class="fas fa-cloud-arrow-up"></i>
                                        </div>
                                        <div class="upload-drop-title">
                                            <span id="browse-trigger">Click to upload</span> or drag &amp; drop
                                        </div>
                                        <div class="upload-drop-sub">SVG, PNG, JPG, GIF or WEBP — max 2 MB</div>
                                    </div>

                                    {{-- File name chip --}}
                                    <div class="file-chip" id="file-chip">
                                        <i class="fas fa-file-image"></i>
                                        <span id="file-chip-name"></span>
                                    </div>

                                    {{-- Preview --}}
                                    <div id="logo-preview-wrap">
                                        <p class="logo-preview-label mt-75">New Logo Preview</p>
                                        <img id="logo-preview-img" src="" alt="Preview">
                                        <button type="button" id="clear-logo"
                                                class="btn btn-outline-danger btn-sm mt-75">
                                            <i class="fas fa-xmark me-50"></i> Remove Selection
                                        </button>
                                    </div>

                                    @error('logo')
                                        <p class="text-danger mt-25" style="font-size:.857rem;">{{ $message }}</p>
                                    @enderror

<div class="mb-1 mt-2">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
               {{ old('is_active', $brand->is_active) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Active</label>
    </div>
</div>

                                    {{-- Meta --}}
                                    <div class="mt-2 p-1"
                                         style="background:#f8f8f8;border-radius:0.357rem;border:1px solid #ebe9f1;font-size:.8rem;color:#b9b9c3;">
                                        <i class="fas fa-circle-info me-50"></i>
                                        Created {{ $brand->created_at->format('d M Y, H:i') }} &nbsp;·&nbsp;
                                        Last updated {{ $brand->updated_at->diffForHumans() }} &nbsp;·&nbsp;
                                        <strong style="color:#7367f0;">
                                            {{ $brand->products_count ?? $brand->products()->count() }}
                                        </strong> product(s) attached
                                    </div>

                                    {{-- Submit --}}
                                    <div class="d-flex gap-1 mt-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-50"></i> Update Brand
                                        </button>
                                        <a href="{{ route('admin.brands.index') }}"
                                           class="btn btn-outline-secondary">
                                            <i class="fas fa-xmark me-50"></i> Cancel
                                        </a>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Slug preview ── */
    const nameInp  = document.getElementById('name');
    const slugInp  = document.getElementById('slug');
    const slugPrev = document.getElementById('slug-preview');

    function toSlug(str) {
        return str.toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }
    nameInp.addEventListener('input', () => {
        if (!slugInp.value.trim()) {
            const g = toSlug(nameInp.value);
            slugPrev.textContent = g ? 'Will be saved as: ' + g : '';
        }
    });
    slugInp.addEventListener('input', () => {
        const c = slugInp.value.trim();
        slugPrev.textContent = c
            ? 'Will be saved as: ' + toSlug(c)
            : (nameInp.value.trim() ? 'Will be saved as: ' + toSlug(nameInp.value) : '');
    });

    /* ══════════════════════════════════════════════════════════════════
     * Drag-and-drop upload
     *
     * Key fix: the <input type="file"> lives OUTSIDE the drop zone in
     * the DOM. The zone div has no children that could intercept drag
     * events, so dragenter / dragover / drop are 100% reliable.
     *
     * Click-to-pick: clicking anywhere on the zone or the "Click to
     * upload" span programmatically opens the hidden file input.
     * ══════════════════════════════════════════════════════════════════ */
    const zone        = document.getElementById('upload-zone');
    const fileInput   = document.getElementById('logo');
    const prevWrap    = document.getElementById('logo-preview-wrap');
    const prevImg     = document.getElementById('logo-preview-img');
    const clearBtn    = document.getElementById('clear-logo');
    const chip        = document.getElementById('file-chip');
    const chipName    = document.getElementById('file-chip-name');
    const browseTrig  = document.getElementById('browse-trigger');

    /* Stop the browser from opening the file on accidental drops outside */
    ['dragenter','dragover','dragleave','drop'].forEach(evt => {
        document.addEventListener(evt, e => e.preventDefault());
    });

    /* Drag enters the zone */
    zone.addEventListener('dragenter', e => {
        e.preventDefault();
        e.stopPropagation();
        zone.classList.add('dragover');
    });

    /* Drag moves over the zone — must prevent default to allow drop */
    zone.addEventListener('dragover', e => {
        e.preventDefault();
        e.stopPropagation();
        zone.classList.add('dragover');
    });

    /* Drag leaves the zone */
    zone.addEventListener('dragleave', e => {
        e.preventDefault();
        e.stopPropagation();
        zone.classList.remove('dragover');
    });

    /* File dropped onto the zone */
    zone.addEventListener('drop', e => {
        e.preventDefault();
        e.stopPropagation();
        zone.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (!files || !files.length) return;

        /* Assign dropped file to the hidden input using DataTransfer */
        try {
            const dt = new DataTransfer();
            dt.items.add(files[0]);
            fileInput.files = dt.files;
        } catch (err) {
            /* Fallback: DataTransfer not supported (very old browsers) */
            console.warn('DataTransfer assign failed', err);
        }

        handleFile(files[0]);
    });

    /* Click on zone or "Click to upload" → open file picker */
    zone.addEventListener('click', () => fileInput.click());
    browseTrig.addEventListener('click', e => {
        e.stopPropagation(); /* zone click already fires, avoid double */
    });

    /* Gallery / file-picker selection */
    fileInput.addEventListener('change', function () {
        if (this.files && this.files.length) handleFile(this.files[0]);
    });

    /* Validate and show preview */
    const ALLOWED_TYPES = ['image/jpeg','image/png','image/jpg',
                           'image/gif','image/svg+xml','image/webp'];
    const MAX_BYTES = 2 * 1024 * 1024; // 2 MB

    function handleFile(file) {
        if (!ALLOWED_TYPES.includes(file.type)) {
            showZoneError('Please choose an image file (SVG, PNG, JPG, GIF or WEBP).');
            resetInput();
            return;
        }
        if (file.size > MAX_BYTES) {
            showZoneError('File is too large. Maximum size is 2 MB.');
            resetInput();
            return;
        }

        /* Preview */
        const reader = new FileReader();
        reader.onload = ev => {
            prevImg.src            = ev.target.result;
            prevWrap.style.display = 'block';
        };
        reader.readAsDataURL(file);

        /* File chip */
        chipName.textContent  = file.name;
        chip.style.display    = 'inline-flex';
    }

    function showZoneError(msg) {
        zone.style.borderColor = '#ea5455';
        zone.style.background  = '#fff5f5';
        setTimeout(() => {
            zone.style.borderColor = '';
            zone.style.background  = '';
        }, 2000);
        alert(msg);
    }

    /* Clear button */
    clearBtn.addEventListener('click', resetInput);

    function resetInput() {
        fileInput.value        = '';
        prevImg.src            = '';
        prevWrap.style.display = 'none';
        chip.style.display     = 'none';
        chipName.textContent   = '';
    }

    /* Remove-logo checkbox dims the current card */
    const removeChk   = document.getElementById('remove_logo');
    const currentCard = document.getElementById('current-logo-card');
    if (removeChk && currentCard) {
        const syncDim = () => {
            currentCard.style.opacity = removeChk.checked ? '0.35' : '1';
        };
        syncDim();
        removeChk.addEventListener('change', syncDim);
    }
});
</script>

@endsection