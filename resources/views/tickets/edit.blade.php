@extends('layouts.simslayout')

@section('content')
<div class="main-wrap">

    {{-- Breadcrumb --}}
    <nav style="margin-bottom:20px;font-size:13px;color:var(--ink3);">
        <a href="/" style="color:var(--ink3);">Home</a>
        <span style="margin:0 6px;">›</span>
        <a href="{{ route('tickets.index') }}" style="color:var(--ink3);">Support Tickets</a>
        <span style="margin:0 6px;">›</span>
        <a href="{{ route('tickets.show', $ticket->id) }}" style="color:var(--ink3);">#{{ $ticket->id }}</a>
        <span style="margin:0 6px;">›</span>
        <span style="color:var(--ink);">Edit</span>
    </nav>

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-family:var(--font-head);font-size:1.5rem;font-weight:700;color:var(--ink);margin-bottom:4px;">
                Edit Ticket
            </h1>
            <p style="font-size:13.5px;color:var(--ink3);">Update your ticket details below.</p>
        </div>
        <a href="{{ route('tickets.show', $ticket->id) }}"
           style="display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border:1px solid var(--border);border-radius:var(--radius);font-size:13.5px;color:var(--ink2);background:var(--surface);text-decoration:none;transition:all .2s;"
           onmouseover="this.style.borderColor='var(--g400)';this.style.color='var(--g600)';this.style.background='var(--g50)'"
           onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--ink2)';this.style.background='var(--surface)'">
            <i class="fas fa-arrow-left" style="font-size:11px;"></i> Back to Ticket
        </a>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div style="background:#fff5f5;border:1px solid #f5c6c6;border-radius:var(--radius);padding:14px 18px;margin-bottom:22px;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                <i class="fas fa-exclamation-circle" style="color:#c0392b;"></i>
                <span style="font-size:13.5px;font-weight:600;color:#c0392b;">Please fix the following errors:</span>
            </div>
            <ul style="margin:0;padding-left:20px;">
                @foreach($errors->all() as $error)
                    <li style="font-size:13px;color:#a93226;margin-bottom:3px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 280px;gap:24px;align-items:start;" class="ticket-edit-grid">

        {{-- Form Card --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:0;overflow:hidden;">
            <div style="padding:16px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:9px;background:var(--g50);border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-edit" style="color:var(--g600);font-size:15px;"></i>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:600;color:var(--ink);font-family:var(--font-head);">Ticket #{{ $ticket->id }}</div>
                    <div style="font-size:12px;color:var(--ink3);">Opened {{ $ticket->created_at->diffForHumans() }}</div>
                </div>
            </div>

            <div style="padding:24px 22px;">
                <form method="POST" action="{{ route('tickets.update', $ticket->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Subject --}}
                    <div style="margin-bottom:20px;">
                        <label for="subject" style="display:block;font-size:13px;font-weight:600;color:var(--ink2);margin-bottom:7px;">
                            Subject <span style="color:#c0392b;">*</span>
                        </label>
                        <input type="text" name="subject" id="subject"
                               value="{{ old('subject', $ticket->subject) }}"
                               required
                               style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:14px;font-family:var(--font-body);color:var(--ink);background:var(--surface);outline:none;transition:border-color .2s,box-shadow .2s;"
                               onfocus="this.style.borderColor='var(--g400)';this.style.boxShadow='0 0 0 3px rgba(90,171,31,.12)'"
                               onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'">
                        @error('subject')
                            <span style="font-size:12px;color:#e74c3c;margin-top:4px;display:block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Priority --}}
                    <div style="margin-bottom:20px;">
                        <label style="display:block;font-size:13px;font-weight:600;color:var(--ink2);margin-bottom:7px;">
                            Priority <span style="color:#c0392b;">*</span>
                        </label>
                        <div style="display:flex;gap:10px;">
                            @foreach(['low' => ['Low','#27ae60','#eafaf1','#a9dfbf'],
                                      'medium' => ['Medium','#e67e22','#fef9e7','#fad7a0'],
                                      'high' => ['High','#c0392b','#fff5f5','#f5c6c6']] as $val => $opt)
                                <label style="flex:1;cursor:pointer;">
                                    <input type="radio" name="priority" value="{{ $val }}"
                                           {{ old('priority', $ticket->priority) === $val ? 'checked' : '' }}
                                           style="display:none;" onchange="updatePriority()">
                                    <div class="priority-option" data-value="{{ $val }}"
                                         data-color="{{ $opt[1] }}" data-bg="{{ $opt[2] }}" data-border="{{ $opt[3] }}"
                                         style="text-align:center;padding:10px 8px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:13px;font-weight:500;color:var(--ink3);background:var(--surface);transition:all .2s;">
                                        {{ $opt[0] }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('priority')
                            <span style="font-size:12px;color:#e74c3c;margin-top:4px;display:block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div style="margin-bottom:24px;">
                        <label for="description" style="display:block;font-size:13px;font-weight:600;color:var(--ink2);margin-bottom:7px;">
                            Description <span style="color:#c0392b;">*</span>
                        </label>
                        <textarea name="description" id="description" rows="6" required
                                  style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:14px;font-family:var(--font-body);color:var(--ink);background:var(--surface);outline:none;resize:vertical;transition:border-color .2s,box-shadow .2s;line-height:1.55;"
                                  onfocus="this.style.borderColor='var(--g400)';this.style.boxShadow='0 0 0 3px rgba(90,171,31,.12)'"
                                  onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'">{{ old('description', $ticket->description) }}</textarea>
                        @error('description')
                            <span style="font-size:12px;color:#e74c3c;margin-top:4px;display:block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                        <button type="submit"
                                style="display:inline-flex;align-items:center;gap:8px;padding:11px 28px;background:var(--g500);color:#fff;border:none;border-radius:var(--radius);font-size:14px;font-weight:600;font-family:var(--font-body);cursor:pointer;transition:background .2s;"
                                onmouseover="this.style.background='var(--g600)'"
                                onmouseout="this.style.background='var(--g500)'">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="{{ route('tickets.show', $ticket->id) }}"
                           style="font-size:13.5px;color:var(--ink3);text-decoration:none;transition:color .2s;"
                           onmouseover="this.style.color='var(--ink)'"
                           onmouseout="this.style.color='var(--ink3)'">Cancel</a>
                    </div>

                </form>
            </div>
        </div>

        {{-- Sidebar --}}
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div style="background:#fff8e1;border:1px solid #fad7a0;border-radius:0;padding:18px;">
                <div style="font-size:13px;font-weight:600;color:#e67e22;margin-bottom:10px;display:flex;align-items:center;gap:7px;">
                    <i class="fas fa-exclamation-triangle"></i> Before editing
                </div>
                @foreach([
                    'Editing is only available while the ticket is open.',
                    'Changing priority may affect your response time.',
                    'Keep your description clear and detailed.',
                ] as $note)
                    <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:9px;font-size:13px;color:#7d6608;">
                        <i class="fas fa-dot-circle" style="font-size:9px;margin-top:4px;flex-shrink:0;color:#e67e22;"></i>
                        {{ $note }}
                    </div>
                @endforeach
            </div>

            <div style="background:var(--surface);border:1px solid var(--border);border-radius:0;padding:18px;">
                <div style="font-size:13px;font-weight:600;color:var(--ink);margin-bottom:12px;">Quick Info</div>
                @foreach([
                    ['fa-hashtag','Ticket ID','#'.$ticket->id],
                    ['fa-calendar','Created',$ticket->created_at->format('M d, Y')],
                    ['fa-comment','Replies', $replies->count() . ' reply(ies)'],
                ] as $qi)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:13px;">
                        <span style="color:var(--ink3);display:flex;align-items:center;gap:7px;">
                            <i class="fas {{ $qi[0] }}" style="font-size:11px;width:14px;"></i> {{ $qi[1] }}
                        </span>
                        <span style="font-weight:500;color:var(--ink);">{{ $qi[2] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function updatePriority() {
        document.querySelectorAll('input[name="priority"]').forEach(function (radio) {
            var div = document.querySelector('.priority-option[data-value="' + radio.value + '"]');
            if (!div) return;
            if (radio.checked) {
                div.style.borderColor = div.dataset.border;
                div.style.background  = div.dataset.bg;
                div.style.color       = div.dataset.color;
                div.style.fontWeight  = '600';
            } else {
                div.style.borderColor = 'var(--border)';
                div.style.background  = 'var(--surface)';
                div.style.color       = 'var(--ink3)';
                div.style.fontWeight  = '500';
            }
        });
    }
    window.updatePriority = updatePriority;
    document.querySelectorAll('input[name="priority"]').forEach(function (r) {
        r.addEventListener('change', updatePriority);
    });
    updatePriority();
});
</script>
@endpush

@push('styles')
<style>
@media (max-width: 768px) {
    .ticket-edit-grid { grid-template-columns: 1fr !important; }
}
</style>
@endpush

@endsection