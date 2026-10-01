@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">

            {{-- Validation errors --}}
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong><i class="fas fa-exclamation-circle me-1"></i> Please fix the following:</strong>
                    <ul class="mb-0 mt-50">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Create New Banner</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Banner Type -->
                        <div class="form-group mt-2">
                            <label for="type" class="form-label">Banner Type <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="banner1" {{ old('type') === 'banner1' ? 'selected' : '' }}>Top (Banner 1)</option>
                                <option value="banner2" {{ old('type') === 'banner2' ? 'selected' : '' }}>Bottom (Banner 2)</option>
                                <option value="popup"   {{ old('type') === 'popup'   ? 'selected' : '' }}>Popup</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Title -->
                        <div class="form-group mt-2">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Link -->
                        <div class="form-group mt-2">
                            <label for="link" class="form-label">Banner Link</label>
                            <input type="text" name="link" id="link"
                                   class="form-control @error('link') is-invalid @enderror"
                                   value="{{ old('link') }}"
                                   placeholder="https://example.com/sale or /category/fridges">
                            <small class="text-muted">Optional — where the banner redirects when clicked.</small>
                            @error('link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Image -->
                        <div class="form-group mt-2">
                            <label for="image" class="form-label">Banner Image <span class="text-danger">*</span></label>
                            <input type="file" name="image" id="image" accept="image/*"
                                   class="form-control @error('image') is-invalid @enderror" required>
                            <small class="text-muted">JPG, PNG, GIF or WEBP — max 2 MB.</small>
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Live Preview -->
                        <div id="previewWrap" class="banner-preview mt-2 d-none">
                            <div class="banner-preview-label">
                                <i class="fas fa-eye me-50"></i> Preview
                            </div>
                            <img id="previewImg" src="" alt="Banner preview">
                        </div>

                        <!-- Status -->
                        <div class="form-group mt-2 mb-2">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="1" {{ old('status', '1') === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div
                        
                        <!-- Display Order -->
<div class="form-group mt-2 mb-2">
    <label for="sort_order" class="form-label">Display Order</label>
    <input type="number" name="sort_order" id="sort_order" min="0" max="999"
           class="form-control @error('sort_order') is-invalid @enderror"
           value="{{ old('sort_order') }}"
           placeholder="e.g. 1">
    <small class="text-muted">Lower numbers show first — 1 is the first slide. Leave empty to add it after the existing banners.</small>
    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

                        <div class="d-flex gap-1">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus me-50"></i> Create Banner
                            </button>
                            <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .banner-preview {
        border: 1px dashed #d8d6de;
        border-radius: 8px;
        padding: 0.75rem;
        background: #fafafa;
        text-align: center;
    }
    .banner-preview-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #b9b9c3;
        margin-bottom: 0.5rem;
        text-align: left;
    }
    .banner-preview img {
        max-width: 100%;
        max-height: 220px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,.08);
        object-fit: contain;
    }
</style>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('image');
    const wrap      = document.getElementById('previewWrap');
    const img       = document.getElementById('previewImg');

    if (fileInput) {
      fileInput.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (file && file.type.startsWith('image/')) {
          img.src = URL.createObjectURL(file);
          wrap.classList.remove('d-none');
        } else {
          img.src = '';
          wrap.classList.add('d-none');
        }
      });
    }
  });
</script>
@endpush
@endsection