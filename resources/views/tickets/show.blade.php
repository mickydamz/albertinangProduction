@extends('layouts.simslayout')

@section('content')
<div class="main-wrap">

    {{-- Breadcrumb --}}
    <nav style="margin-bottom:20px;font-size:13px;color:var(--ink3);">
        <a href="/" style="color:var(--ink3);">Home</a>
        <span style="margin:0 6px;">›</span>
        <a href="{{ route('tickets.index') }}" style="color:var(--ink3);">Support Tickets</a>
        <span style="margin:0 6px;">›</span>
        <span style="color:var(--ink);">{{ \Illuminate\Support\Str::limit($ticket->subject, 40) }}</span>
    </nav>

    @if(session('status'))
        <div style="background:#eaf4fb;border:1px solid #aed6f1;border-radius:var(--radius);padding:13px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13.5px;color:#2980b9;">
            <i class="fas fa-info-circle"></i> {{ session('status') }}
        </div>
    @endif

    <div style="max-width:760px;">

        {{-- ── Ticket Card ───────────────────────────────────────────────────── --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-sm);margin-bottom:24px;">

            {{-- Header --}}
            <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div>
                    <h2 style="font-family:var(--font-head);font-size:1.15rem;font-weight:700;color:var(--ink);margin:0 0 8px;">
                        {{ $ticket->subject }}
                    </h2>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
                        @php
                            $pMap = [
                                'low'    => ['Low',    '#27ae60','#eafaf1','#a9dfbf','fa-arrow-down'],
                                'medium' => ['Medium', '#e67e22','#fef9e7','#fad7a0','fa-ellipsis-h'],
                                'high'   => ['High',   '#c0392b','#fff5f5','#f5c6c6','fa-exclamation-triangle'],
                            ];
                            $sMap = [
                                'open'    => ['Open',    '#2980b9','#eaf4fb','#aed6f1','fa-hourglass-half'],
                                'pending' => ['Pending', '#e67e22','#fef9e7','#fad7a0','fa-spinner'],
                                'closed'  => ['Closed',  '#27ae60','#eafaf1','#a9dfbf','fa-check-circle'],
                            ];
                            $p = $pMap[$ticket->priority] ?? ['?','#888','#f5f5f5','#ddd','fa-question'];
                            $s = $sMap[$ticket->status]   ?? ['?','#888','#f5f5f5','#ddd','fa-question'];
                        @endphp
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:600;background:{{ $s[2] }};border:1px solid {{ $s[3] }};color:{{ $s[1] }};">
                            <i class="fas {{ $s[4] }}" style="font-size:10px;"></i> {{ $s[0] }}
                        </span>
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:600;background:{{ $p[2] }};border:1px solid {{ $p[3] }};color:{{ $p[1] }};">
                            <i class="fas {{ $p[4] }}" style="font-size:10px;"></i> {{ $p[0] }} Priority
                        </span>
                        <span style="font-size:12px;color:var(--ink3);">
                            <i class="fas fa-clock" style="margin-right:3px;"></i>
                            Opened {{ $ticket->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div style="padding:22px 24px;">
                <p style="font-size:14px;color:var(--ink);line-height:1.7;white-space:pre-wrap;margin:0 0 20px;">{{ $ticket->description }}</p>

                {{-- Ticket Images --}}
                @if(!empty($ticket->images))
                    <div style="margin-top:4px;">
                        <p style="font-size:12px;font-weight:700;color:var(--ink3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">
                            <i class="fas fa-paperclip" style="margin-right:4px;"></i> Attachments ({{ count($ticket->images) }})
                        </p>
                        <div style="display:flex;flex-wrap:wrap;gap:10px;">
                            @foreach($ticket->images as $img)
                                <a href="{{ Storage::url($img) }}" target="_blank"
                                   style="display:block;width:110px;height:110px;border-radius:8px;overflow:hidden;border:1px solid var(--border);flex-shrink:0;box-shadow:var(--shadow-sm);">
                                    <img src="{{ Storage::url($img) }}"
                                         alt="Attachment"
                                         style="width:100%;height:100%;object-fit:cover;display:block;transition:opacity .2s;"
                                         onmouseover="this.style.opacity='.8'"
                                         onmouseout="this.style.opacity='1'">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── Replies ───────────────────────────────────────────────────────── --}}
        @if($replies->isNotEmpty())
            <div style="margin-bottom:24px;">
                <h3 style="font-size:13px;font-weight:700;color:var(--ink3);text-transform:uppercase;letter-spacing:.6px;margin-bottom:14px;">
                    <i class="fas fa-comments" style="margin-right:6px;color:var(--g500);"></i>
                    Conversation ({{ $replies->count() }})
                </h3>

                <div style="display:flex;flex-direction:column;gap:14px;">
                    @foreach($replies as $reply)
                        @php $isAdmin = isset($reply->user) && $reply->user->role === 'admin'; @endphp
                        <div style="background:var(--surface);border:1px solid {{ $isAdmin ? '#aed6f1' : 'var(--border)' }};border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-sm);{{ $isAdmin ? 'border-left:3px solid #2980b9;' : '' }}">

                            {{-- Reply header --}}
                            <div style="padding:12px 18px;border-bottom:1px solid var(--border);background:{{ $isAdmin ? '#f0f8ff' : 'var(--surf2)' }};display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:32px;height:32px;border-radius:50%;background:{{ $isAdmin ? '#2980b9' : 'var(--g500)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="fas {{ $isAdmin ? 'fa-headset' : 'fa-user' }}" style="color:#fff;font-size:13px;"></i>
                                    </div>
                                    <div>
                                        <span style="font-size:13px;font-weight:600;color:var(--ink);">
                                            {{ $isAdmin ? 'Support Team' : ($reply->user->name ?? 'You') }}
                                        </span>
                                        @if($isAdmin)
                                            <span style="display:inline-block;margin-left:6px;padding:1px 7px;border-radius:20px;font-size:10px;font-weight:700;background:#2980b9;color:#fff;vertical-align:middle;">Staff</span>
                                        @endif
                                    </div>
                                </div>
                                <span style="font-size:12px;color:var(--ink3);">
                                    <i class="fas fa-clock" style="margin-right:3px;font-size:10px;"></i>
                                    {{ $reply->created_at->diffForHumans() }}
                                </span>
                            </div>

                            {{-- Reply body --}}
                            <div style="padding:16px 18px;">
                                <p style="font-size:13.5px;color:var(--ink);line-height:1.7;white-space:pre-wrap;margin:0 0 {{ !empty($reply->images) ? '14px' : '0' }};">{{ $reply->message }}</p>

                                {{-- Reply images --}}
                                @if(!empty($reply->images))
                                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                        @foreach($reply->images as $img)
                                            <a href="{{ Storage::url($img) }}" target="_blank"
                                               style="display:block;width:90px;height:90px;border-radius:7px;overflow:hidden;border:1px solid var(--border);flex-shrink:0;">
                                                <img src="{{ Storage::url($img) }}"
                                                     alt="Attachment"
                                                     style="width:100%;height:100%;object-fit:cover;display:block;transition:opacity .2s;"
                                                     onmouseover="this.style.opacity='.8'"
                                                     onmouseout="this.style.opacity='1'">
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ── Reply Form ────────────────────────────────────────────────────── --}}
        @if($ticket->status !== 'closed')
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:0;overflow:hidden;">
                <div style="padding:14px 20px;border-bottom:1px solid var(--border);background:var(--surf2);">
                    <span style="font-size:13.5px;font-weight:600;color:var(--ink);font-family:var(--font-head);display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-reply" style="color:var(--g500);font-size:12px;"></i> Add a Reply
                    </span>
                </div>
                <div style="padding:20px 22px;">
                    <form action="{{ route('tickets.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <textarea name="message" rows="4"
                                  placeholder="Type your reply…"
                                  style="width:100%;box-sizing:border-box;padding:10px 13px;border:1px solid var(--border);border-radius:var(--radius);font-size:13.5px;color:var(--ink);background:var(--surface);outline:none;resize:vertical;font-family:var(--font-body);transition:border-color .2s;margin-bottom:14px;"
                                  onfocus="this.style.borderColor='var(--g400)'"
                                  onblur="this.style.borderColor='var(--border)'">{{ old('message') }}</textarea>
                        @error('message')
                            <p style="color:#e74c3c;font-size:12px;margin:-10px 0 10px;">{{ $message }}</p>
                        @enderror

                        {{-- Image upload for reply --}}
                        <div style="margin-bottom:18px;">
                            <label style="display:block;font-size:12px;font-weight:700;color:var(--ink3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">
                                <i class="fas fa-image" style="margin-right:4px;"></i>
                                Attach images <span style="font-weight:400;text-transform:none;letter-spacing:0;">(optional · up to 5)</span>
                            </label>

                            <div id="reply-drop-zone"
                                 style="border:2px dashed var(--border);border-radius:var(--radius);padding:18px 14px;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;"
                                 onclick="document.getElementById('reply-img-input').click()"
                                 ondragover="event.preventDefault();this.style.borderColor='var(--g400)';this.style.background='var(--g50)'"
                                 ondragleave="this.style.borderColor='var(--border)';this.style.background='transparent'"
                                 ondrop="handleReplyDrop(event)">
                                <i class="fas fa-cloud-upload-alt" style="font-size:20px;color:var(--g400);margin-bottom:5px;display:block;"></i>
                                <p style="font-size:13px;color:var(--ink3);margin:0;">Click or drag images here</p>
                            </div>

                            <input type="file" id="reply-img-input" name="images[]"
                                   accept="image/jpeg,image/png,image/gif,image/webp"
                                   multiple style="display:none;"
                                   onchange="previewReplyImages(this.files)">

                            <div id="reply-img-preview"
                                 style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;"></div>
                        </div>

                        <button type="submit"
                                style="display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:var(--g500);color:#fff;border:none;border-radius:var(--radius);font-size:13.5px;font-weight:600;font-family:var(--font-body);cursor:pointer;transition:background .2s;"
                                onmouseover="this.style.background='var(--g600)'"
                                onmouseout="this.style.background='var(--g500)'">
                            <i class="fas fa-paper-plane"></i> Send Reply
                        </button>

                    </form>
                </div>
            </div>
        @else
            <div style="background:var(--surf2);border:1px solid var(--border);border-radius:0;padding:18px 22px;text-align:center;font-size:13.5px;color:var(--ink3);">
                <i class="fas fa-lock" style="margin-right:6px;"></i>
                This ticket is closed. <a href="{{ route('tickets.index') }}" style="color:var(--g600);font-weight:600;">Open a new ticket</a> if you need further help.
            </div>
        @endif

    </div>
</div>

{{-- Lightbox overlay --}}
<div id="lightbox"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:9999;align-items:center;justify-content:center;cursor:pointer;"
     onclick="this.style.display='none'">
    <img id="lightbox-img" src="" style="max-width:90vw;max-height:90vh;border-radius:8px;box-shadow:0 8px 40px rgba(0,0,0,.6);">
</div>

@push('scripts')
<script>
// Open images in lightbox
document.querySelectorAll('a[href$=".jpg"],a[href$=".jpeg"],a[href$=".png"],a[href$=".gif"],a[href$=".webp"]').forEach(a => {
    if (a.target === '_blank') {
        a.addEventListener('click', function(e) {
            e.preventDefault();
            const lb = document.getElementById('lightbox');
            document.getElementById('lightbox-img').src = this.href;
            lb.style.display = 'flex';
        });
    }
});

// Reply image preview
let replyFiles = new DataTransfer();

function previewReplyImages(files) {
    for (const f of files) {
        if (replyFiles.items.length >= 5) break;
        replyFiles.items.add(f);
    }
    document.getElementById('reply-img-input').files = replyFiles.files;
    renderReplyPreviews();
}

function handleReplyDrop(e) {
    e.preventDefault();
    document.getElementById('reply-drop-zone').style.borderColor = 'var(--border)';
    document.getElementById('reply-drop-zone').style.background   = 'transparent';
    previewReplyImages(e.dataTransfer.files);
}

function removeReplyFile(index) {
    const newDT = new DataTransfer();
    const files = replyFiles.files;
    for (let i = 0; i < files.length; i++) {
        if (i !== index) newDT.items.add(files[i]);
    }
    replyFiles = newDT;
    document.getElementById('reply-img-input').files = replyFiles.files;
    renderReplyPreviews();
}

function renderReplyPreviews() {
    const grid = document.getElementById('reply-img-preview');
    grid.innerHTML = '';
    const files = replyFiles.files;
    for (let i = 0; i < files.length; i++) {
        const url  = URL.createObjectURL(files[i]);
        const wrap = document.createElement('div');
        wrap.style.cssText = 'position:relative;width:80px;height:80px;border-radius:7px;overflow:hidden;border:1px solid var(--border);flex-shrink:0;';
        wrap.innerHTML = `
            <img src="${url}" style="width:100%;height:100%;object-fit:cover;display:block;">
            <button type="button" onclick="removeReplyFile(${i})"
                    style="position:absolute;top:3px;right:3px;width:20px;height:20px;border-radius:50%;background:rgba(0,0,0,.55);color:#fff;border:none;cursor:pointer;font-size:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-times"></i>
            </button>`;
        grid.appendChild(wrap);
    }
}
</script>
@endpush

@endsection