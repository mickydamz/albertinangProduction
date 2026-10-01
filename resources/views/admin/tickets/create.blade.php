@extends('layouts.adminlayout')

@section('content')
<div class="app-content content ecommerce-application">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Create Ticket</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">Tickets</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg rounded-lg">
                    <div class="card-header text-white">
                        <h4 class="card-title mb-0">Create New Ticket</h4>
                    </div>
                    <div class="card-body bg-light">

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.tickets.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="subject" class="fw-bold">Subject</label>
                                <input type="text" class="form-control" name="subject" id="subject"
                                       value="{{ old('subject') }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="description" class="fw-bold">Description</label>
                                <textarea class="form-control" name="description" id="description"
                                          rows="5" required>{{ old('description') }}</textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label for="priority" class="fw-bold">Priority</label>
                                <select name="priority" id="priority" class="form-control" required>
                                    <option value="low"    {{ old('priority')=='low'    ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority','medium')=='medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high"   {{ old('priority')=='high'   ? 'selected' : '' }}>High</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="status" class="fw-bold">Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="open"    {{ old('status','open')=='open'    ? 'selected' : '' }}>Open</option>
                                    <option value="pending" {{ old('status')=='pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="closed"  {{ old('status')=='closed'  ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="user_id" class="fw-bold">Assign User</label>
                                <select name="user_id" id="user_id" class="form-control" required>
                                    <option value="">Select a User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id')==$user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Image upload --}}
                            <div class="form-group mb-4">
                                <label class="fw-bold d-block mb-1">
                                    Attachments
                                    <span class="text-muted fw-normal" style="font-size:12px;">(optional · up to 5 images · max 5 MB each)</span>
                                </label>

                                <div id="create-drop-zone"
                                     style="border:2px dashed #dee2e6;border-radius:8px;padding:24px;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;"
                                     onclick="document.getElementById('create-img-input').click()"
                                     ondragover="event.preventDefault();this.style.borderColor='#ffc107';this.style.background='#fffdf0'"
                                     ondragleave="this.style.borderColor='#dee2e6';this.style.background='transparent'"
                                     ondrop="handleCreateDrop(event)">
                                    <i class="fas fa-cloud-upload-alt" style="font-size:26px;color:#ffc107;display:block;margin-bottom:6px;"></i>
                                    <span style="font-size:13px;color:#6c757d;">Click to browse or drag & drop images here</span><br>
                                    <span style="font-size:11px;color:#adb5bd;">JPG, PNG, GIF, WebP</span>
                                </div>

                                <input type="file" id="create-img-input" name="images[]"
                                       accept="image/jpeg,image/png,image/gif,image/webp"
                                       multiple style="display:none;"
                                       onchange="previewCreateImages(this.files)">

                                <div id="create-img-preview" class="d-flex flex-wrap gap-2 mt-2"></div>
                            </div>

                            <div class="d-flex gap-2 mt-1">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Create Ticket</button>
                                <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
let createFiles = new DataTransfer();

function previewCreateImages(files) {
    for (const f of files) {
        if (createFiles.items.length >= 5) break;
        createFiles.items.add(f);
    }
    document.getElementById('create-img-input').files = createFiles.files;
    renderCreatePreviews();
}

function handleCreateDrop(e) {
    e.preventDefault();
    document.getElementById('create-drop-zone').style.borderColor = '#dee2e6';
    document.getElementById('create-drop-zone').style.background   = 'transparent';
    previewCreateImages(e.dataTransfer.files);
}

function removeCreateFile(index) {
    const newDT = new DataTransfer();
    const files = createFiles.files;
    for (let i = 0; i < files.length; i++) {
        if (i !== index) newDT.items.add(files[i]);
    }
    createFiles = newDT;
    document.getElementById('create-img-input').files = createFiles.files;
    renderCreatePreviews();
}

function renderCreatePreviews() {
    const grid  = document.getElementById('create-img-preview');
    grid.innerHTML = '';
    const files = createFiles.files;
    for (let i = 0; i < files.length; i++) {
        const url  = URL.createObjectURL(files[i]);
        const wrap = document.createElement('div');
        wrap.style.cssText = 'position:relative;width:80px;height:80px;border-radius:8px;overflow:hidden;border:1px solid #dee2e6;flex-shrink:0;';
        wrap.innerHTML = `
            <img src="${url}" style="width:100%;height:100%;object-fit:cover;display:block;">
            <button type="button" onclick="removeCreateFile(${i})"
                    style="position:absolute;top:3px;right:3px;width:20px;height:20px;border-radius:50%;background:rgba(0,0,0,.55);color:#fff;border:none;cursor:pointer;font-size:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-times"></i>
            </button>
            <div style="position:absolute;bottom:0;left:0;right:0;padding:3px 5px;background:rgba(0,0,0,.4);font-size:9px;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                ${files[i].name}
            </div>`;
        grid.appendChild(wrap);
    }
}
</script>
@endpush

@endsection