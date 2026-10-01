@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>
  <div class="content-wrapper container-xxl p-0">

    {{-- Breadcrumb --}}
    <div class="content-header row">
      <div class="col-12 mb-2">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h2 class="content-header-title mb-0">
              Ticket&nbsp;<span class="text-primary">#{{ $ticket->id }}</span>
            </h2>
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">Tickets</a></li>
              <li class="breadcrumb-item active">#{{ $ticket->id }}</li>
            </ol>
          </div>
          <div class="d-flex gap-1">
            <a href="{{ route('admin.tickets.edit', $ticket->id) }}" class="btn btn-warning btn-sm">
              <i class="fas fa-edit me-50"></i> Edit
            </a>
            <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-arrow-left me-50"></i> Back
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="content-body">

      @if(session('status'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
          <div class="alert-body">{{ session('status') }}</div>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      {{-- Status banner --}}
      @php
        $statusColors  = ['open' => 'primary', 'pending' => 'warning', 'closed' => 'success'];
        $priorityColors = ['low' => 'success', 'medium' => 'warning', 'high' => 'danger'];
        $sColor = $statusColors[$ticket->status]    ?? 'secondary';
        $pColor = $priorityColors[$ticket->priority] ?? 'secondary';
      @endphp
      <div class="card mb-2">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2 py-2">
          <div class="d-flex align-items-center gap-2">
            <div class="avatar bg-light-{{ $sColor }} rounded">
              <div class="avatar-content"><i class="fas fa-ticket-alt"></i></div>
            </div>
            <div>
              <h5 class="mb-0">{{ $ticket->subject }}</h5>
              <small class="text-muted">Opened {{ $ticket->created_at->format('d M Y, h:i A') }}</small>
            </div>
          </div>
          <div class="text-md-end">
            <span class="badge bg-{{ $sColor }} me-1">{{ ucfirst($ticket->status) }}</span>
            <span class="badge bg-light-{{ $pColor }} text-{{ $pColor }}">{{ ucfirst($ticket->priority) }} Priority</span>
          </div>
        </div>
      </div>

      {{-- Top row: Ticket info + Customer --}}
      <div class="row">

        <div class="col-lg-4 col-12 mb-2">
          <div class="card h-100">
            <div class="card-header border-bottom">
              <h4 class="card-title"><i class="fas fa-info-circle text-primary me-50"></i>Ticket Info</h4>
            </div>
            <div class="card-body pt-1">
              <ul class="list-unstyled mb-0">
                <li class="d-flex justify-content-between py-50 border-bottom">
                  <span class="text-muted">Status</span>
                  <span class="badge bg-light-{{ $sColor }} text-{{ $sColor }}">{{ ucfirst($ticket->status) }}</span>
                </li>
                <li class="d-flex justify-content-between py-50 border-bottom">
                  <span class="text-muted">Priority</span>
                  <span class="badge bg-light-{{ $pColor }} text-{{ $pColor }}">{{ ucfirst($ticket->priority) }}</span>
                </li>
                <li class="d-flex justify-content-between py-50 border-bottom">
                  <span class="text-muted">Replies</span>
                  <strong>{{ $replies->count() }}</strong>
                </li>
                <li class="d-flex justify-content-between py-50 border-bottom">
                  <span class="text-muted">Opened</span>
                  <strong>{{ $ticket->created_at->format('d M Y, H:i') }}</strong>
                </li>
                <li class="d-flex justify-content-between py-50">
                  <span class="text-muted">Updated</span>
                  <strong>{{ $ticket->updated_at->format('d M Y, H:i') }}</strong>
                </li>
              </ul>

              <hr>

              {{-- Quick status update --}}
              <p class="text-muted mb-50" style="font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.04em;">Update Status</p>
              <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST" class="d-flex gap-1">
                @csrf
                @method('PUT')
                <select name="status" class="form-select form-select-sm">
                  <option value="open"    {{ $ticket->status === 'open'    ? 'selected' : '' }}>Open</option>
                  <option value="pending" {{ $ticket->status === 'pending' ? 'selected' : '' }}>Pending</option>
                  <option value="closed"  {{ $ticket->status === 'closed'  ? 'selected' : '' }}>Closed</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm waves-effect" style="flex-shrink:0;">Save</button>
              </form>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-12 mb-2">
          <div class="card h-100">
            <div class="card-header border-bottom">
              <h4 class="card-title"><i class="fas fa-user text-primary me-50"></i>Customer</h4>
            </div>
            <div class="card-body pt-1">
              @if($ticket->user)
                <div class="d-flex align-items-center mb-2">
                  @if($ticket->user->avatar)
                    <img src="{{ Storage::url($ticket->user->avatar) }}" alt="Avatar" class="rounded-circle me-2" style="width:54px;height:54px;object-fit:cover;">
                  @else
                    <img src="{{ asset('app-asset/images/portrait/small/e_avatar.png') }}" alt="Avatar" class="rounded-circle me-2" style="width:54px;height:54px;object-fit:cover;">
                  @endif
                  <div>
                    <h5 class="mb-0">{{ $ticket->user->name }}</h5>
                    <small class="text-muted text-capitalize">{{ $ticket->user->role ?? 'User' }}</small>
                  </div>
                </div>
                <ul class="list-unstyled mb-0">
                  <li class="d-flex justify-content-between py-50 border-bottom">
                    <span class="text-muted">Email</span>
                    <strong class="text-truncate ms-2" style="max-width:170px;">{{ $ticket->user->email }}</strong>
                  </li>
                  <li class="d-flex justify-content-between py-50">
                    <span class="text-muted">Member Since</span>
                    <strong>{{ $ticket->user->created_at->format('d M Y') }}</strong>
                  </li>
                </ul>
                <div class="mt-2">
                  <a href="{{ route('admin.users.edit', $ticket->user) }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="fas fa-external-link-alt me-50"></i>View User Profile
                  </a>
                </div>
              @else
                <div class="text-center text-muted py-3">
                  <i class="fas fa-user-slash fa-2x mb-2"></i>
                  <p class="mb-0">Deleted User</p>
                </div>
              @endif
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-12 mb-2">
          <div class="card h-100">
            <div class="card-header border-bottom">
              <h4 class="card-title"><i class="fas fa-align-left text-primary me-50"></i>Description</h4>
            </div>
            <div class="card-body pt-1">
              <p style="white-space:pre-wrap; font-size:14px; margin:0;">{{ $ticket->description }}</p>

              @if(!empty($ticket->images))
                <hr>
                <p class="text-muted mb-1" style="font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.04em;">
                  <i class="fas fa-paperclip me-1"></i>Attachments ({{ count($ticket->images) }})
                </p>
                <div class="d-flex flex-wrap gap-2">
                  @foreach($ticket->images as $img)
                    <a href="{{ Storage::url($img) }}" class="ticket-img-link" target="_blank">
                      <img src="{{ Storage::url($img) }}" alt="Attachment"
                           style="width:72px;height:72px;object-fit:cover;border-radius:6px;border:1px solid #dee2e6;transition:opacity .2s;"
                           onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                    </a>
                  @endforeach
                </div>
              @endif
            </div>
          </div>
        </div>

      </div>

      {{-- Replies thread --}}
      <div class="card mb-2">
        <div class="card-header border-bottom">
          <h4 class="card-title"><i class="fas fa-comments text-primary me-50"></i>Replies</h4>
        </div>
        <div class="card-body">

          @forelse($replies as $reply)
            @php $isAdmin = isset($reply->user) && $reply->user->role === 'admin'; @endphp
            <div class="d-flex gap-2 mb-2">
              <div style="flex-shrink:0;">
                @if($reply->user?->avatar)
                  <img src="{{ Storage::url($reply->user->avatar) }}" class="rounded-circle" style="width:38px;height:38px;object-fit:cover;">
                @else
                  <div class="avatar bg-light-{{ $isAdmin ? 'primary' : 'secondary' }} rounded-circle"
                       style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;">
                    <span style="font-size:14px;font-weight:600;color:{{ $isAdmin ? '#7367f0' : '#82868b' }};">
                      {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                    </span>
                  </div>
                @endif
              </div>
              <div style="flex:1;">
                <div class="card mb-0 {{ $isAdmin ? 'border-primary' : '' }}" style="{{ $isAdmin ? 'background:#f6f4ff;' : '' }}">
                  <div class="card-body py-1 px-2">
                    <div class="d-flex align-items-center justify-content-between mb-50">
                      <div>
                        <span class="fw-bolder" style="font-size:13px;">{{ $reply->user->name ?? 'Unknown' }}</span>
                        @if($isAdmin)
                          <span class="badge bg-light-primary text-primary ms-1" style="font-size:10px;">Support</span>
                        @endif
                      </div>
                      <small class="text-muted" style="font-size:11px;">{{ $reply->created_at->diffForHumans() }}</small>
                    </div>
                    <p style="white-space:pre-wrap;margin:0;font-size:13px;">{{ $reply->message }}</p>
                    @if(!empty($reply->images))
                      <div class="d-flex flex-wrap gap-1 mt-1">
                        @foreach($reply->images as $img)
                          <a href="{{ Storage::url($img) }}" class="ticket-img-link" target="_blank">
                            <img src="{{ Storage::url($img) }}" alt="Attachment"
                                 style="width:60px;height:60px;object-fit:cover;border-radius:5px;border:1px solid #dee2e6;">
                          </a>
                        @endforeach
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          @empty
            <p class="text-muted text-center py-2 mb-0">No replies yet. Be the first to respond.</p>
          @endforelse

        </div>
      </div>

      {{-- Reply form --}}
      @if($ticket->status !== 'closed')
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title"><i class="fas fa-pencil-alt text-primary me-50"></i>Add a Reply</h4>
        </div>
        <div class="card-body pt-2">
          <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-2">
              <textarea name="message" class="form-control" rows="4"
                        placeholder="Type your reply here…" required>{{ old('message') }}</textarea>
            </div>

            <div class="mb-2">
              <label class="form-label" style="font-size:13px;font-weight:500;">
                <i class="fas fa-image me-1"></i>Attach images
                <span class="text-muted fw-normal">(optional · up to 5 · max 5 MB each)</span>
              </label>
              <div id="admin-drop-zone"
                   style="border:2px dashed #ebe9f1;border-radius:8px;padding:20px;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;"
                   onclick="document.getElementById('admin-img-input').click()"
                   ondragover="event.preventDefault();this.style.borderColor='#7367f0';this.style.background='#f8f7ff';"
                   ondragleave="this.style.borderColor='#ebe9f1';this.style.background='transparent';"
                   ondrop="handleAdminDrop(event)">
                <i class="fas fa-cloud-upload-alt" style="font-size:24px;color:#7367f0;display:block;margin-bottom:6px;"></i>
                <span style="font-size:13px;color:#b9b9c3;">Click or drag images here</span>
              </div>
              <input type="file" id="admin-img-input" name="images[]"
                     accept="image/jpeg,image/png,image/gif,image/webp"
                     multiple style="display:none;" onchange="previewAdminImages(this.files)">
              <div id="admin-img-preview" class="d-flex flex-wrap gap-2 mt-1"></div>
            </div>

            <button type="submit" class="btn btn-primary waves-effect">
              <i class="fas fa-paper-plane me-50"></i>Send Reply
            </button>
          </form>
        </div>
      </div>
      @else
      <div class="alert alert-secondary">
        <i class="fas fa-lock me-1"></i>This ticket is closed. <a href="{{ route('admin.tickets.edit', $ticket->id) }}">Reopen it</a> to add a reply.
      </div>
      @endif

    </div>
  </div>
</div>

{{-- Lightbox --}}
<div id="lightbox"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:9999;align-items:center;justify-content:center;cursor:pointer;"
     onclick="this.style.display='none'">
  <img id="lightbox-img" src="" style="max-width:90vw;max-height:90vh;border-radius:8px;">
</div>

<style>
  .py-50 { padding-top: .5rem; padding-bottom: .5rem; }
  .mb-50 { margin-bottom: .5rem !important; }
  .ms-1  { margin-left: .25rem !important; }
  .me-50 { margin-right: .5rem !important; }
  .gap-1 { gap: .25rem !important; }
  .gap-2 { gap: .5rem  !important; }

  .avatar-content {
    display: flex; align-items: center; justify-content: center;
    width: 38px; height: 38px; font-size: 14px; font-weight: 600;
  }
  .avatar.bg-light-primary   .avatar-content { color: #7367f0; background: rgba(115,103,240,.12); }
  .avatar.bg-light-secondary .avatar-content { color: #82868b; background: rgba(130,134,139,.12); }
</style>

@push('scripts')
<script>
document.querySelectorAll('.ticket-img-link').forEach(a => {
  a.addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('lightbox-img').src = this.href;
    document.getElementById('lightbox').style.display = 'flex';
  });
});

let adminFiles = new DataTransfer();

function previewAdminImages(files) {
  for (const f of files) {
    if (adminFiles.items.length >= 5) break;
    adminFiles.items.add(f);
  }
  document.getElementById('admin-img-input').files = adminFiles.files;
  renderAdminPreviews();
}

function handleAdminDrop(e) {
  e.preventDefault();
  document.getElementById('admin-drop-zone').style.borderColor = '#ebe9f1';
  document.getElementById('admin-drop-zone').style.background   = 'transparent';
  previewAdminImages(e.dataTransfer.files);
}

function removeAdminFile(index) {
  const newDT = new DataTransfer();
  const files = adminFiles.files;
  for (let i = 0; i < files.length; i++) {
    if (i !== index) newDT.items.add(files[i]);
  }
  adminFiles = newDT;
  document.getElementById('admin-img-input').files = adminFiles.files;
  renderAdminPreviews();
}

function renderAdminPreviews() {
  const grid = document.getElementById('admin-img-preview');
  grid.innerHTML = '';
  const files = adminFiles.files;
  for (let i = 0; i < files.length; i++) {
    const url  = URL.createObjectURL(files[i]);
    const wrap = document.createElement('div');
    wrap.style.cssText = 'position:relative;width:72px;height:72px;border-radius:6px;overflow:hidden;border:1px solid #ebe9f1;flex-shrink:0;';
    wrap.innerHTML = `
      <img src="${url}" style="width:100%;height:100%;object-fit:cover;display:block;">
      <button type="button" onclick="removeAdminFile(${i})"
              style="position:absolute;top:3px;right:3px;width:20px;height:20px;border-radius:50%;background:rgba(0,0,0,.55);color:#fff;border:none;cursor:pointer;font-size:10px;display:flex;align-items:center;justify-content:center;">
        <i class="fas fa-times"></i>
      </button>`;
    grid.appendChild(wrap);
  }
}
</script>
@endpush

@endsection
