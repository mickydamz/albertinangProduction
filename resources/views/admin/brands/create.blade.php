@extends('layouts.adminlayout')

@section('content')

<style>
    .upload-drop-zone {
        border: 2px dashed #d8d6de;
        border-radius: 0.428rem;
        padding: 2rem 1rem;
        text-align: center;
        cursor: pointer;
        background: #f8f8f8;
        transition: border-color 0.2s ease, background 0.2s ease;
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

    .form-section-title {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.6px; color: #b9b9c3;
        border-bottom: 1px solid #ebe9f1;
        padding-bottom: 0.4rem; margin-bottom: 1rem; margin-top: 1.5rem;
    }

    #slug-preview { font-size: 0.78rem; color: #7367f0; margin-top: 0.3rem; min-height: 1.1rem; }

    #logo-preview-wrap { margin-top: 0.85rem; display: none; }
    #logo-preview-img {
        width: 100px; height: 100px; object-fit: contain;
        border: 1px solid #ebe9f1; border-radius: 0.428rem;
        background: #f8f8f8; padding: 6px; display: block;
    }
    .logo-preview-label {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.5px; color: #b9b9c3; margin-bottom: 0.4rem;
    }
</style>

{{-- File input lives OUTSIDE the drop zone so drag events reach the zone reliably --}}
<input type="file" name="logo" id="logo" style="display:none;"
       accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml,image/webp"
       form="brand-form">

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Create Brand</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.brands.index') }}">Brands</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
                </div>
            </div>
        </div>

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
            <section id="create-brand">
                <div class="row justify-content-center">
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header">Create New Brand</div>
                            <div class="card-body">

                                <form id="brand-form"
                                      action="{{ route('admin.brands.store') }}"
                                      method="POST"
                                      enctype="multipart/form-data">
                                    @csrf

                                    <p class="form-section-title">Brand Information</p>

                                    <div class="mb-1">
                                        <label for="name" class="form-label">
                                            Brand Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name" id="name"
                                               class="form-control @error('name') is-invalid @enderror"
                                               value="{{ old('name') }}"
                                               placeholder="e.g. Samsung"
                                               required autofocus>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label for="slug" class="form-label">
                                            Slug
                                            <small class="text-muted ms-25">Leave blank to auto-generate from name</small>
                                        </label>
                                        <input type="text" name="slug" id="slug"
                                               class="form-control @error('slug') is-invalid @enderror"
                                               value="{{ old('slug') }}"
                                               placeholder="e.g. samsung">
                                        <div id="slug-preview"></div>
                                        @error('slug')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <p class="form-section-title">Brand Logo</p>

                                    {{-- Drop zone — no file input inside --}}
                                    <div class="upload-drop-zone" id="upload-zone">
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
                                        <p class="logo-preview-label mt-75">Preview</p>
                                        <img id="logo-preview-img" src="" alt="Preview">
                                        <button type="button" id="clear-logo"
                                                class="btn btn-outline-danger btn-sm mt-75">
                                            <i class="fas fa-xmark me-50"></i> Remove Selection
                                        </button>
                                    </div>

                                    @error('logo')
                                        <p class="text-danger mt-25" style="font-size:.857rem;">{{ $message }}</p>
                                    @enderror

                                    <div class="d-flex gap-1 mt-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-50"></i> Create Brand
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

    /* ── Drag-and-drop + click-to-pick ── */
    const zone       = document.getElementById('upload-zone');
    const fileInput  = document.getElementById('logo');
    const prevWrap   = document.getElementById('logo-preview-wrap');
    const prevImg    = document.getElementById('logo-preview-img');
    const clearBtn   = document.getElementById('clear-logo');
    const chip       = document.getElementById('file-chip');
    const chipName   = document.getElementById('file-chip-name');

    /* Stop browser navigating on accidental outside drops */
    ['dragenter','dragover','dragleave','drop'].forEach(evt => {
        document.addEventListener(evt, e => e.preventDefault());
    });

    zone.addEventListener('dragenter', e => { e.preventDefault(); e.stopPropagation(); zone.classList.add('dragover'); });
    zone.addEventListener('dragover',  e => { e.preventDefault(); e.stopPropagation(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', e => { e.preventDefault(); e.stopPropagation(); zone.classList.remove('dragover'); });

    zone.addEventListener('drop', e => {
        e.preventDefault();
        e.stopPropagation();
        zone.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (!files || !files.length) return;
        try {
            const dt = new DataTransfer();
            dt.items.add(files[0]);
            fileInput.files = dt.files;
        } catch (err) {
            console.warn('DataTransfer assign failed', err);
        }
        handleFile(files[0]);
    });

    zone.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', function () {
        if (this.files && this.files.length) handleFile(this.files[0]);
    });

    const ALLOWED_TYPES = ['image/jpeg','image/png','image/jpg','image/gif','image/svg+xml','image/webp'];
    const MAX_BYTES = 2 * 1024 * 1024;

    function handleFile(file) {
        if (!ALLOWED_TYPES.includes(file.type)) {
            showZoneError('Please choose an image file (SVG, PNG, JPG, GIF or WEBP).');
            resetInput(); return;
        }
        if (file.size > MAX_BYTES) {
            showZoneError('File is too large. Maximum size is 2 MB.');
            resetInput(); return;
        }
        const reader = new FileReader();
        reader.onload = ev => {
            prevImg.src            = ev.target.result;
            prevWrap.style.display = 'block';
        };
        reader.readAsDataURL(file);
        chipName.textContent = file.name;
        chip.style.display   = 'inline-flex';
    }

    function showZoneError(msg) {
        zone.style.borderColor = '#ea5455';
        zone.style.background  = '#fff5f5';
        setTimeout(() => { zone.style.borderColor = ''; zone.style.background = ''; }, 2000);
        alert(msg);
    }

    clearBtn.addEventListener('click', resetInput);

    function resetInput() {
        fileInput.value        = '';
        prevImg.src            = '';
        prevWrap.style.display = 'none';
        chip.style.display     = 'none';
        chipName.textContent   = '';
    }
});
</script>

@endsection