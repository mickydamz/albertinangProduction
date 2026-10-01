@extends('layouts.authlayout')

@section('title', 'Two-Factor Verification — Albertina Nigeria')

@push('styles')
<style>
    /* ── OTP digit boxes ── */
    .otp-header {
        text-align: center;
        margin-bottom: 24px;
    }
    .otp-header__icon {
        width: 64px; height: 64px;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--g500), var(--g600));
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px;
        box-shadow: 0 8px 24px rgba(90,171,31,.28);
    }
    .otp-header__icon i { font-size: 26px; color: #fff; }
    .otp-header h4 {
        font-family: var(--font-head);
        font-size: 1.25rem; font-weight: 700; color: var(--ink);
        margin-bottom: 6px;
    }
    .otp-header p {
        font-size: 13.5px; color: var(--ink3); line-height: 1.6;
        margin-bottom: 0;
    }
    .otp-header p strong { color: var(--ink2); font-weight: 600; }

    /* boxes row */
    .otp-boxes {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin: 28px 0 22px;
    }
    .otp-box {
        width: 52px; height: 60px;
        border: 2px solid var(--border);
        border-radius: 12px;
        background: var(--surf2);
        font-family: var(--font-head);
        font-size: 1.7rem;
        font-weight: 700;
        color: var(--ink);
        text-align: center;
        outline: none;
        transition: border-color .18s, box-shadow .18s, background .18s;
        caret-color: transparent;
        -webkit-appearance: none;
    }
    .otp-box:focus {
        border-color: var(--g400);
        background: var(--surface);
        box-shadow: 0 0 0 4px rgba(90,171,31,.13);
    }
    .otp-box.filled {
        border-color: var(--g500);
        background: var(--g50);
        color: var(--g700);
    }
    .otp-box.error {
        border-color: #dc3545;
        background: #fff5f5;
        animation: shake .35s ease;
    }
    @keyframes shake {
        0%,100% { transform: translateX(0); }
        25%      { transform: translateX(-5px); }
        75%      { transform: translateX(5px); }
    }

    @media (max-width: 400px) {
        .otp-box { width: 44px; height: 54px; font-size: 1.5rem; border-radius: 10px; }
        .otp-boxes { gap: 7px; }
    }

    /* timer badge */
    .otp-timer {
        display: flex; align-items: center; justify-content: center;
        gap: 6px; font-size: 12.5px; color: var(--ink3);
        margin-bottom: 20px;
    }
    .otp-timer__badge {
        background: var(--surf3); border: 1px solid var(--border2);
        border-radius: 6px; padding: 3px 8px;
        font-family: var(--font-head); font-size: 12px; font-weight: 700;
        color: var(--g600); letter-spacing: .5px; min-width: 38px; text-align: center;
    }
    .otp-timer__badge.expired { color: #dc3545; border-color: #f5c6cb; background: #fff5f5; }
</style>
@endpush

@section('content')

    @if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)
            {{ $error }}
        @endforeach
    </div>
    @endif

    @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    {{-- Icon + heading --}}
    <div class="otp-header">
        <div class="otp-header__icon">
            <i class="fas fa-shield-halved"></i>
        </div>
        <h4>Two-Factor Verification</h4>
        <p>Enter the <strong>6-digit code</strong> sent to your email.<br>The code expires in 10 minutes.</p>
    </div>

    {{-- OTP form --}}
    <form method="POST" action="{{ route('2fa.verify') }}" id="otpForm">
        @csrf
        <input type="hidden" name="two_factor_code" id="two_factor_code">

        <div class="otp-boxes" id="otpBoxes">
            @for($i = 0; $i < 6; $i++)
            <input class="otp-box {{ $errors->any() ? 'error' : '' }}"
                   type="text"
                   inputmode="numeric"
                   maxlength="1"
                   pattern="[0-9]"
                   autocomplete="one-time-code"
                   data-index="{{ $i }}">
            @endfor
        </div>

        <div class="otp-timer">
            <i class="fas fa-clock" style="font-size:11px;"></i>
            Code expires in <span class="otp-timer__badge" id="otpTimer">10:00</span>
        </div>

        <button type="submit" class="btn-primary" id="otpSubmit" disabled style="opacity:.5;transition:opacity .2s;">
            <i class="fas fa-check-circle" style="margin-right:6px;"></i> Verify & Continue
        </button>
    </form>

    <div class="auth-divider"><span>Didn't receive a code?</span></div>

    <p class="auth-card__foot">
        <form action="{{ route('resend-2fa') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit"
                style="background:none;border:none;cursor:pointer;color:var(--g600);font-weight:600;font-size:13.5px;font-family:var(--font-body);"
                onmouseover="this.style.textDecoration='underline'"
                onmouseout="this.style.textDecoration='none'">
                <i class="fas fa-rotate-right" style="margin-right:4px;"></i> Resend code
            </button>
        </form>
    </p>

@endsection

@push('scripts')
<script>
(function () {
    const boxes    = Array.from(document.querySelectorAll('.otp-box'));
    const hidden   = document.getElementById('two_factor_code');
    const form     = document.getElementById('otpForm');
    const submitBtn = document.getElementById('otpSubmit');
    const timerEl  = document.getElementById('otpTimer');

    // ── focus first box on load ──
    if (boxes.length) boxes[0].focus();

    // ── input handling ──
    boxes.forEach((box, i) => {
        box.addEventListener('keydown', e => {
            if (e.key === 'Backspace') {
                e.preventDefault();
                if (box.value) {
                    box.value = '';
                    box.classList.remove('filled');
                } else if (i > 0) {
                    boxes[i - 1].value = '';
                    boxes[i - 1].classList.remove('filled');
                    boxes[i - 1].focus();
                }
                syncHidden();
                return;
            }
            if (e.key === 'ArrowLeft' && i > 0)  { e.preventDefault(); boxes[i-1].focus(); return; }
            if (e.key === 'ArrowRight' && i < 5) { e.preventDefault(); boxes[i+1].focus(); return; }
        });

        box.addEventListener('input', e => {
            const digit = box.value.replace(/\D/g, '').slice(-1);
            box.value = digit;
            if (digit) {
                box.classList.add('filled');
                if (i < boxes.length - 1) boxes[i + 1].focus();
            } else {
                box.classList.remove('filled');
            }
            syncHidden();
        });

        // allow re-clicking a filled box to clear it
        box.addEventListener('click', () => box.select());
    });

    // ── paste support ──
    boxes[0].addEventListener('paste', e => {
        e.preventDefault();
        const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
        text.split('').forEach((ch, idx) => {
            if (boxes[idx]) {
                boxes[idx].value = ch;
                boxes[idx].classList.add('filled');
            }
        });
        const next = Math.min(text.length, 5);
        boxes[next].focus();
        syncHidden();
    });

    // ── also catch paste on any box ──
    boxes.forEach(box => {
        box.addEventListener('paste', e => {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
            text.split('').forEach((ch, idx) => {
                if (boxes[idx]) { boxes[idx].value = ch; boxes[idx].classList.add('filled'); }
            });
            syncHidden();
        });
    });

    function syncHidden() {
        const code = boxes.map(b => b.value).join('');
        hidden.value = code;
        const complete = code.length === 6 && /^\d{6}$/.test(code);
        submitBtn.disabled = !complete;
        submitBtn.style.opacity = complete ? '1' : '.5';
        if (complete) {
            // brief delay so user sees the last digit fill
            setTimeout(() => form.submit(), 300);
        }
    }

    // ── countdown timer (10 min = 600s) ──
    let seconds = 600;
    function tick() {
        const m = String(Math.floor(seconds / 60)).padStart(2, '0');
        const s = String(seconds % 60).padStart(2, '0');
        timerEl.textContent = m + ':' + s;
        if (seconds <= 0) {
            timerEl.textContent = 'Expired';
            timerEl.classList.add('expired');
            submitBtn.disabled = true;
            submitBtn.style.opacity = '.5';
            clearInterval(timer);
        }
        seconds--;
    }
    tick();
    const timer = setInterval(tick, 1000);
})();
</script>
@endpush
