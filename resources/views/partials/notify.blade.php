{{-- ╔══════════════════════════════════════════════════════════════════════╗ --}}
{{-- ║  Progress-bar Notification System — Albertina Nigeria               ║ --}}
{{-- ║  Usage: showNotify(msg, type, duration)                             ║ --}}
{{-- ║         type: 'success' | 'error' | 'warning' | 'info'             ║ --}}
{{-- ╚══════════════════════════════════════════════════════════════════════╝ --}}

<style>
/* ── Notification wrap (stacking container) ─────────────────────────────── */
.pbn-wrap {
    position: fixed;
    top: 22px;
    right: 22px;
    z-index: 999999;
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: 340px;
    width: calc(100vw - 44px);
    pointer-events: none;
}

/* ── Individual notification card ─────────────────────────────────────── */
.pbn {
    background: #ffffff;
    border-radius: 14px;
    box-shadow:
        0 4px 6px -1px rgba(0,0,0,0.07),
        0 10px 30px -4px rgba(0,0,0,0.12),
        0 0 0 1px rgba(0,0,0,0.04);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    pointer-events: all;
    transform: translateX(calc(100% + 30px));
    opacity: 0;
    transition: transform 0.42s cubic-bezier(0.34, 1.32, 0.64, 1),
                opacity   0.24s ease;
    will-change: transform, opacity;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
}
.pbn.pbn-in  {
    transform: translateX(0);
    opacity: 1;
}
.pbn.pbn-out {
    transform: translateX(calc(100% + 30px));
    opacity: 0;
    transition: transform 0.28s cubic-bezier(0.55, 0, 1, 0.45),
                opacity   0.22s ease-in;
}

/* ── Card body ─────────────────────────────────────────────────────────── */
.pbn-body {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 15px 15px 13px 15px;
}

/* ── Icon bubble ───────────────────────────────────────────────────────── */
.pbn-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 16px;
}

/* ── Text area ─────────────────────────────────────────────────────────── */
.pbn-text { flex: 1; min-width: 0; padding-top: 1px; }

.pbn-title {
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.1px;
    margin-bottom: 3px;
    line-height: 1.3;
}
.pbn-msg {
    font-size: 12.5px;
    color: #6b7280;
    line-height: 1.5;
    word-break: break-word;
}

/* ── Close button ──────────────────────────────────────────────────────── */
.pbn-close {
    width: 24px;
    height: 24px;
    border: none;
    background: none;
    cursor: pointer;
    color: #c4cad4;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    flex-shrink: 0;
    margin-top: 1px;
    font-size: 12px;
    line-height: 1;
    transition: color .15s ease, background .15s ease;
}
.pbn-close:hover { color: #374151; background: rgba(0,0,0,0.06); }

/* ── Progress bar track ────────────────────────────────────────────────── */
.pbn-track {
    height: 4px;
    background: rgba(0,0,0,0.05);
    overflow: hidden;
    border-radius: 0 0 14px 14px;
}
.pbn-fill {
    height: 100%;
    width: 100%;
    transform-origin: left;
    transform: scaleX(1);
    will-change: transform;
    border-radius: 0 0 0 2px;
}

/* ── Type: success ─────────────────────────────────────────────────────── */
.pbn-success .pbn-icon  { background: #f0fdf4; color: #16a34a; }
.pbn-success .pbn-fill  { background: linear-gradient(90deg, #16a34a, #22c55e); }
.pbn-success .pbn-title { color: #14532d; }

/* ── Type: error ───────────────────────────────────────────────────────── */
.pbn-error   .pbn-icon  { background: #fef2f2; color: #dc2626; }
.pbn-error   .pbn-fill  { background: linear-gradient(90deg, #dc2626, #f87171); }
.pbn-error   .pbn-title { color: #7f1d1d; }

/* ── Type: warning ─────────────────────────────────────────────────────── */
.pbn-warning .pbn-icon  { background: #fffbeb; color: #d97706; }
.pbn-warning .pbn-fill  { background: linear-gradient(90deg, #d97706, #fbbf24); }
.pbn-warning .pbn-title { color: #78350f; }

/* ── Type: info ────────────────────────────────────────────────────────── */
.pbn-info    .pbn-icon  { background: #eff6ff; color: #2563eb; }
.pbn-info    .pbn-fill  { background: linear-gradient(90deg, #2563eb, #60a5fa); }
.pbn-info    .pbn-title { color: #1e3a8a; }

/* ── Mobile: slightly narrower ─────────────────────────────────────────── */
@media (max-width: 480px) {
    .pbn-wrap { top: 12px; right: 12px; left: 12px; max-width: none; width: auto; }
    .pbn { border-radius: 12px; }
}
</style>

<script>
(function () {
    'use strict';

    var _wrap = null;

    var ICONS = {
        success : 'fas fa-check-circle',
        error   : 'fas fa-times-circle',
        warning : 'fas fa-exclamation-circle',
        info    : 'fas fa-info-circle'
    };
    var TITLES = {
        success : 'Success',
        error   : 'Error',
        warning : 'Warning',
        info    : 'Information'
    };

    function getWrap() {
        if (!_wrap || !document.body.contains(_wrap)) {
            _wrap = document.createElement('div');
            _wrap.className = 'pbn-wrap';
            document.body.appendChild(_wrap);
        }
        return _wrap;
    }

    /**
     * showNotify(message, type, duration)
     * @param {string} msg      — plain text message
     * @param {string} type     — 'success' | 'error' | 'warning' | 'info'
     * @param {number} duration — ms before auto-dismiss (default 4500)
     */
    window.showNotify = function (msg, type, duration) {
        if (!msg) return;
        type     = TITLES[type] ? type : 'success';
        duration = typeof duration === 'number' ? duration : 4500;

        /* Limit stack to 5 */
        var w = getWrap();
        var all = w.querySelectorAll('.pbn');
        if (all.length >= 5) { all[0].querySelector('.pbn-close').click(); }

        /* Build element */
        var el = document.createElement('div');
        el.className = 'pbn pbn-' + type;
        el.innerHTML =
            '<div class="pbn-body">' +
                '<div class="pbn-icon"><i class="' + ICONS[type] + '"></i></div>' +
                '<div class="pbn-text">' +
                    '<div class="pbn-title">' + TITLES[type] + '</div>' +
                    '<div class="pbn-msg">'  + msg + '</div>' +
                '</div>' +
                '<button class="pbn-close" aria-label="Dismiss">&#215;</button>' +
            '</div>' +
            '<div class="pbn-track"><div class="pbn-fill"></div></div>';

        w.appendChild(el);

        var fill     = el.querySelector('.pbn-fill');
        var closeBtn = el.querySelector('.pbn-close');
        var timer    = null;
        var remain   = duration;
        var startTs  = null;
        var paused   = false;

        function dismiss() {
            clearTimeout(timer);
            if (el.classList.contains('pbn-out')) return;
            el.classList.add('pbn-out');
            el.addEventListener('transitionend', function h(e) {
                if (e.propertyName !== 'transform') return;
                el.removeEventListener('transitionend', h);
                el.remove();
            });
        }

        function startBar(ms) {
            startTs = performance.now();
            fill.style.transition = 'transform ' + ms + 'ms linear';
            /* Force reflow so transition fires from current scaleX */
            void fill.offsetWidth;
            fill.style.transform  = 'scaleX(0)';
            timer = setTimeout(dismiss, ms);
        }

        closeBtn.addEventListener('click', function () { dismiss(); });

        /* Pause on hover */
        el.addEventListener('mouseenter', function () {
            if (paused) return;
            paused = true;
            clearTimeout(timer);
            var elapsed = startTs ? performance.now() - startTs : 0;
            remain = Math.max(0, remain - elapsed);
            /* Freeze bar in place */
            var computed = window.getComputedStyle(fill);
            fill.style.transition = 'none';
            fill.style.transform  = computed.transform;
        });

        el.addEventListener('mouseleave', function () {
            if (!paused) return;
            paused  = false;
            if (remain <= 0) { dismiss(); return; }
            startBar(remain);
        });

        /* Entrance → then start bar after spring settles */
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                el.classList.add('pbn-in');
                setTimeout(function () { startBar(remain); }, 80);
            });
        });
    };

    /* ── Backward-compat: old showToast(msg, faIcon, duration) ─────────── */
    window.showToast = function (msg, icon, duration) {
        var type = 'success';
        if (typeof icon === 'string') {
            if (icon.indexOf('times')       > -1) type = 'error';
            else if (icon.indexOf('exclamation') > -1) type = 'warning';
            else if (icon.indexOf('info')   > -1) type = 'info';
        }
        window.showNotify(msg, type, duration || 4500);
    };

    /* ── Session flash auto-trigger ─────────────────────────────────────── */
    @if(session('success'))
    (function() {
        var msg = @json(session('success'));
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () { setTimeout(function(){ window.showNotify(msg, 'success'); }, 150); });
        } else {
            setTimeout(function(){ window.showNotify(msg, 'success'); }, 150);
        }
    })();
    @endif

    @if(session('error'))
    (function() {
        var msg = @json(session('error'));
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () { setTimeout(function(){ window.showNotify(msg, 'error'); }, 150); });
        } else {
            setTimeout(function(){ window.showNotify(msg, 'error'); }, 150);
        }
    })();
    @endif

    @if(session('warning'))
    (function() {
        var msg = @json(session('warning'));
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () { setTimeout(function(){ window.showNotify(msg, 'warning'); }, 150); });
        } else {
            setTimeout(function(){ window.showNotify(msg, 'warning'); }, 150);
        }
    })();
    @endif

    @if(session('info') || session('message'))
    (function() {
        var msg = @json(session('info') ?? session('message'));
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () { setTimeout(function(){ window.showNotify(msg, 'info'); }, 150); });
        } else {
            setTimeout(function(){ window.showNotify(msg, 'info'); }, 150);
        }
    })();
    @endif

})();
</script>
