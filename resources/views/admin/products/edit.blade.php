@extends('layouts.adminlayout')

@section('content')

<style>
    /* ══════════════════════════════════════════
       LAYOUT — Sidebar + Content
    ══════════════════════════════════════════ */
    .pe-layout {
        display: grid;
        grid-template-columns: 240px 1fr;
        gap: 1.25rem;
        align-items: start;
    }
    @media (max-width: 991px) {
        .pe-layout { grid-template-columns: 1fr; }
    }

    /* ── Sticky Sidebar Nav ── */
    .pe-sidebar {
        position: sticky;
        top: 1rem;
        background: #fff;
        border: 1px solid #ebe9f1;
        border-radius: 0.428rem;
        padding: 0.65rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    @media (max-width: 991px) {
        /* ── Off-canvas right drawer (Dashlite-style) ── */
        .pe-sidebar {
            position: fixed; top: 0; right: 0; bottom: 0;
            width: 268px; max-width: 82vw; z-index: 1051;
            border: none; border-radius: 0;
            box-shadow: -6px 0 24px rgba(0,0,0,0.18);
            padding: 1rem 0.65rem;
            overflow-y: auto;
            transform: translateX(100%);
            transition: transform .28s cubic-bezier(.4,0,.2,1);
            display: block; flex-wrap: nowrap;
        }
        .pe-sidebar.open { transform: translateX(0); }
    }
    .pe-nav-title {
        font-size: 0.66rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.6px; color: #b9b9c3;
        padding: 0.35rem 0.65rem 0.55rem;
    }
    @media (max-width: 991px) { .pe-nav-title { display: none; } }
    .pe-nav-item {
        display: flex; align-items: center; gap: 0.6rem;
        padding: 0.55rem 0.7rem;
        border-radius: 0.357rem;
        font-size: 0.85rem; font-weight: 500; color: #6e6b7b;
        cursor: pointer; border: none; background: none; width: 100%;
        text-align: left; transition: background .15s, color .15s;
        position: relative;
    }
    .pe-nav-item .pe-nav-ico {
        width: 18px; text-align: center; font-size: 0.8rem; flex-shrink: 0;
        color: #b9b9c3; transition: color .15s;
    }
    .pe-nav-item:hover { background: #f8f8f8; color: #5e5873; }
    .pe-nav-item.active {
        background: #f0effe; color: #7367f0; font-weight: 600;
    }
    .pe-nav-item.active .pe-nav-ico { color: #7367f0; }
    .pe-nav-item .pe-nav-badge {
        margin-left: auto;
        font-size: 0.62rem; font-weight: 700;
        background: #f0effe; color: #7367f0; border: 1px solid #ddd9fb;
        padding: 0.05rem 0.4rem; border-radius: 999px;
        display: none;
    }
    .pe-nav-item .pe-nav-badge.show { display: inline-block; }
    @media (max-width: 991px) {
        .pe-nav-item { width: auto; }
        .pe-nav-item .pe-nav-badge { display: none !important; }
    }

    /* ── Drawer toggle / backdrop / close (mobile only) ── */
    .pe-drawer-toggle {
        display: none;                       /* shown only on mobile via media query */
        align-items: center; justify-content: center;
        flex-shrink: 0;
        width: 42px; height: 42px; padding: 0;
        background: #fff; color: #5e5873;
        border: 1px solid #ebe9f1; border-radius: 0.5rem;
        cursor: pointer; font-size: 1.05rem; line-height: 1;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        transition: background .15s, border-color .15s;
    }
    .pe-drawer-toggle:hover { background: #f8f8f8; border-color: #d8d6e0; }
    .pe-drawer-toggle:active { background: #f0effe; }
    .pe-drawer-backdrop {
        display: none;
        position: fixed; inset: 0; z-index: 1050;
        background: rgba(34,41,47,0.5);
        opacity: 0; transition: opacity .28s ease;
    }
    .pe-drawer-backdrop.show { opacity: 1; }
    .pe-drawer-head {
        display: none;
        align-items: center; justify-content: space-between;
        padding: 0 0.35rem 0.6rem; margin-bottom: 0.35rem;
        border-bottom: 1px solid #ebe9f1;
    }
    .pe-drawer-head span {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.6px; color: #b9b9c3;
    }
    .pe-drawer-close {
        background: #f8f8f8; border: none; border-radius: 0.357rem;
        width: 30px; height: 30px; cursor: pointer;
        color: #6e6b7b; font-size: 0.95rem; line-height: 1;
    }
    @media (max-width: 991px) {
        .pe-drawer-toggle { display: flex; }
        .pe-drawer-head   { display: flex; }
    }

    /* ── Panels ── */
    .pe-panel { display: none; animation: peFade .2s ease; }
    .pe-panel.active { display: block; }
    @keyframes peFade { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    .pe-panel-head {
        display: flex; align-items: center; gap: 0.65rem;
        margin-bottom: 1.25rem; padding-bottom: 0.85rem;
        border-bottom: 1px solid #ebe9f1;
    }
    .pe-panel-head .pe-panel-ico {
        width: 38px; height: 38px; flex-shrink: 0;
        background: #f0effe; border-radius: 0.428rem;
        display: grid; place-items: center; color: #7367f0; font-size: 0.95rem;
    }
    .pe-panel-head h4 { margin: 0; font-size: 1.05rem; font-weight: 600; color: #5e5873; }
    .pe-panel-head p { margin: 0; font-size: 0.78rem; color: #b9b9c3; }

    /* ── Footer nav (Prev / Next) ── */
    .pe-panel-foot {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 1.75rem; padding-top: 1rem; border-top: 1px solid #ebe9f1;
    }
    .pe-step-dots { display: flex; gap: 0.35rem; }
    .pe-step-dots span {
        width: 7px; height: 7px; border-radius: 50%;
        background: #e2e2e2; transition: background .15s, width .15s;
    }
    .pe-step-dots span.active { background: #7367f0; width: 18px; border-radius: 999px; }

    /* ── Image Previews ── */
    #image-previews {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(95px, 1fr));
        gap: 0.6rem;
        margin-top: 0.75rem;
    }
    .preview-card {
        position: relative; border-radius: 0.357rem; overflow: hidden;
        border: 1px solid #ebe9f1; aspect-ratio: 1;
        background: #f8f8f8; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .preview-card:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(0,0,0,0.1); }
    .preview-card img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .preview-card .preview-rm {
        position: absolute; top: 4px; right: 4px;
        width: 22px; height: 22px;
        background: rgba(234,84,85,0.85); border: none; border-radius: 50%;
        color: #fff; font-size: 9px; cursor: pointer;
        display: grid; place-items: center;
        opacity: 0; transition: opacity 0.15s, background 0.15s;
    }
    .preview-card:hover .preview-rm { opacity: 1; }
    .preview-card .preview-rm:hover { background: #ea5455; }

    /* ── Pill Input ── */
    .pill-box {
        border: 1px solid #d8d6de; border-radius: 0.357rem;
        padding: 0.4rem 0.75rem; background: #fff;
        display: flex; flex-wrap: wrap; gap: 0.3rem; align-items: center;
        min-height: 38px; cursor: text;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .pill-box:focus-within {
        border-color: #7367f0;
        box-shadow: 0 3px 10px 0 rgba(115,103,240,0.2);
    }
    .pill-box input {
        border: none; outline: none; background: transparent;
        font-size: 0.875rem; color: #6e6b7b; flex: 1; min-width: 110px; padding: 2px 0;
    }
    .pill-box input::placeholder { color: #b9b9c3; }
    .tag-pill {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 0.2rem 0.65rem;
        background: #f0effe; color: #7367f0;
        border: 1px solid #ddd9fb; border-radius: 999px;
        font-size: 0.775rem; font-weight: 600; white-space: nowrap;
    }
    .tag-pill .btn-close {
        font-size: 0.6rem; opacity: 0.6; cursor: pointer; padding: 0;
        background-size: 7px;
        filter: invert(0.3) sepia(1) saturate(4) hue-rotate(200deg);
    }
    .tag-pill .btn-close:hover { opacity: 1; }
    .btn-edit-opts {
        background: #e6fff8; color: #28c76f; border: 1px solid #c3f5de;
        border-radius: 999px; padding: 1px 6px;
        font-size: 0.67rem; font-weight: 700; cursor: pointer; transition: 0.15s;
    }
    .btn-edit-opts:hover { background: #28c76f; color: #fff; }
    .sugg-box {
        position: absolute; top: 100%; left: 0; right: 0;
        background: #fff; border: 1px solid #ebe9f1; border-top: none;
        border-radius: 0 0 0.357rem 0.357rem;
        max-height: 190px; overflow-y: auto;
        z-index: 1050; box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        display: none;
    }
    .sugg-item {
        padding: 0.5rem 0.85rem; font-size: 0.875rem;
        cursor: pointer; color: #6e6b7b; transition: background 0.1s;
    }
    .sugg-item:hover { background: #f0effe; color: #7367f0; }
    .avail-label {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.5px; color: #b9b9c3;
        margin-top: 0.6rem; margin-bottom: 0.35rem;
    }
    .avail-pills { display: flex; flex-wrap: wrap; gap: 0.35rem; }
    .avail-pill {
        display: inline-flex; align-items: center;
        padding: 0.18rem 0.6rem;
        background: #f8f8f8; border: 1px solid #ebe9f1; border-radius: 999px;
        font-size: 0.75rem; font-weight: 500; color: #6e6b7b; cursor: pointer;
        transition: all 0.13s;
    }
    .avail-pill:hover { background: #f0effe; border-color: #ddd9fb; color: #7367f0; }

    /* ── Stars ── */
    .star-wrap { display: flex; gap: 0.25rem; }
    .star-wrap .fa-star {
        font-size: 1.5rem; cursor: pointer; color: #e2e2e2;
        transition: color 0.12s, transform 0.12s;
    }
    .star-wrap .fa-star.lit { color: #ff9f43; }
    .star-wrap .fa-star:hover { transform: scale(1.15); }

    /* ── Installation Options ── */
    .install-opt-row {
        background: #f8f8f8;
        border: 1px solid #ebe9f1 !important;
        border-radius: 0.428rem;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .install-opt-row:hover {
        border-color: #d8d6de !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .btn-rm-install {
        width: 36px; height: 38px;
        border: 1px solid #ffd4d4; background: #fff5f5; color: #ea5455;
        border-radius: 0.357rem; display: grid; place-items: center;
        cursor: pointer; font-size: 0.8rem; transition: 0.15s;
    }
    .btn-rm-install:hover { background: #ea5455; color: #fff; border-color: #ea5455; }

    /* ── Tag Options Modal ── */
    .cp-backdrop {
        display: none; position: fixed; inset: 0;
        background: rgba(34,41,47,0.5); backdrop-filter: blur(3px);
        z-index: 9999; place-items: center;
    }
    .cp-backdrop.open { display: grid; }
    .cp-modal {
        background: #fff; border-radius: 0.428rem;
        width: 430px; max-width: 95vw;
        box-shadow: 0 5px 30px rgba(34,41,47,0.22); overflow: hidden;
        animation: mPop .2s ease;
    }
    @keyframes mPop {
        from { transform: scale(0.93) translateY(10px); opacity: 0; }
        to   { transform: scale(1) translateY(0);       opacity: 1; }
    }
    .cp-modal-hd {
        padding: 1rem 1.25rem; border-bottom: 1px solid #ebe9f1;
        display: flex; justify-content: space-between; align-items: center;
    }
    .cp-modal-hd h5 { margin: 0; font-size: 1rem; font-weight: 600; }
    .cp-modal-x {
        width: 28px; height: 28px; border: none;
        background: #f8f8f8; border-radius: 0.357rem;
        display: grid; place-items: center; cursor: pointer;
        color: #b9b9c3; font-size: 0.8rem; transition: 0.15s;
    }
    .cp-modal-x:hover { background: #ffdede; color: #ea5455; }
    .cp-modal-bd { padding: 1.25rem; }
    .cp-modal-ft {
        padding: 0.85rem 1.25rem; border-top: 1px solid #ebe9f1;
        display: flex; justify-content: flex-end; gap: 0.5rem;
    }

    /* ── Toast ── */
    .cp-toast {
        position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 99999;
        padding: 0.75rem 1rem; border-radius: 0.357rem;
        font-size: 0.875rem; font-weight: 500;
        display: flex; align-items: center; gap: 0.6rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        animation: tUp .22s ease; max-width: 310px;
    }
    @keyframes tUp { from { transform: translateY(14px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

    /* ── Grouped Attributes ── */
    .attr-group-card {
        border: 1px solid #ebe9f1; border-radius: 0.428rem;
        margin-bottom: 1rem; background: #fafafa; overflow: hidden; transition: box-shadow .15s;
    }
    .attr-group-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
    .attr-group-header {
        display: flex; align-items: center; gap: 0.6rem;
        padding: 0.65rem 0.85rem; background: #f3f2f7; border-bottom: 1px solid #ebe9f1;
    }
    .attr-group-header .group-name-inp {
        flex: 1; border: 1px solid #d8d6de; border-radius: 0.357rem;
        padding: 0.35rem 0.65rem; font-size: 0.875rem; font-weight: 600;
        color: #5e5873; background: #fff; outline: none;
        transition: border-color .15s, box-shadow .15s;
    }
    .attr-group-header .group-name-inp:focus {
        border-color: #7367f0; box-shadow: 0 3px 10px 0 rgba(115,103,240,0.2);
    }
    .attr-group-header .group-name-inp::placeholder { font-weight: 400; color: #b9b9c3; }
    .btn-rm-group {
        width: 32px; height: 32px; flex-shrink: 0;
        border: 1px solid #ffd4d4; background: #fff5f5; color: #ea5455;
        border-radius: 0.357rem; display: grid; place-items: center;
        cursor: pointer; font-size: 0.78rem; transition: 0.15s;
    }
    .btn-rm-group:hover { background: #ea5455; color: #fff; border-color: #ea5455; }
    .attr-group-body { padding: 0.75rem 0.85rem; }
    .attr-group-row {
        display: grid; grid-template-columns: 1fr 1fr auto;
        gap: 0.5rem; align-items: center; margin-bottom: 0.45rem;
    }
    .attr-group-row input {
        border: 1px solid #d8d6de; border-radius: 0.357rem;
        padding: 0.32rem 0.65rem; font-size: 0.82rem; color: #6e6b7b;
        background: #fff; outline: none; width: 100%;
        transition: border-color .15s, box-shadow .15s;
    }
    .attr-group-row input:focus {
        border-color: #7367f0; box-shadow: 0 3px 8px rgba(115,103,240,0.15);
    }
    .attr-group-row input::placeholder { color: #b9b9c3; }
    .btn-rm-attr-row {
        width: 30px; height: 30px; flex-shrink: 0;
        border: 1px solid #ffd4d4; background: #fff5f5; color: #ea5455;
        border-radius: 0.357rem; display: grid; place-items: center;
        cursor: pointer; font-size: 0.72rem; transition: 0.15s;
    }
    .btn-rm-attr-row:hover { background: #ea5455; color: #fff; border-color: #ea5455; }
    .btn-add-attr-row {
        font-size: 0.78rem; color: #7367f0; background: none; border: none;
        cursor: pointer; padding: 0; display: inline-flex; align-items: center; gap: 4px;
        margin-top: 0.25rem; font-weight: 600; transition: color .15s;
    }
    .btn-add-attr-row:hover { color: #5e50ee; }
    .attr-row-header {
        display: grid; grid-template-columns: 1fr 1fr auto;
        gap: 0.5rem; margin-bottom: 0.3rem;
    }
    .attr-row-header span {
        font-size: 0.7rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.4px; color: #b9b9c3;
    }

    /* ── JSON Paste Card ── */
    .json-paste-card { border: 1px solid #ebe9f1; border-radius: 0.428rem; overflow: hidden; margin-bottom: 1.25rem; }
    .json-paste-card-hd { display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1rem; background: #f8f7fa; border-bottom: 1px solid #ebe9f1; }
    .json-paste-card-hd .icon-wrap { width: 28px; height: 28px; flex-shrink: 0; background: #f0effe; border-radius: 6px; display: flex; align-items: center; justify-content: center; }
    .json-paste-card-hd .icon-wrap i { font-size: 0.75rem; color: #7367f0; }
    .json-paste-card-hd .hd-title { font-size: 0.85rem; font-weight: 600; color: #5e5873; flex: 1; }
    .badge-fmt { font-size: 0.62rem; font-weight: 700; background: #f0effe; color: #7367f0; border: 1px solid #ddd9fb; padding: 0.15rem 0.55rem; border-radius: 999px; letter-spacing: 0.2px; }
    .json-paste-card-bd { padding: 0.9rem 1rem; }
    .json-paste-hint { font-size: 0.75rem; color: #b9b9c3; margin-bottom: 0.85rem; line-height: 1.6; }
    .json-paste-hint code { font-size: 0.7rem; background: #f0effe; color: #7367f0; padding: 0.1rem 0.35rem; border-radius: 4px; font-family: 'Courier New', monospace; }
    .json-code-block { background: #f6f8fa; border: 1px solid #e1e4e8; border-radius: 0.357rem; padding: 0.75rem 0.9rem; margin-bottom: 0.85rem; position: relative; overflow-x: auto; }
    .json-code-block pre { margin: 0; font-family: 'Courier New', monospace; font-size: 0.72rem; color: #24292e; white-space: pre; line-height: 1.75; }
    .json-code-block .jk { color: #005cc5; }
    .json-code-block .js { color: #22863a; }
    .json-code-block .jp { color: #6a737d; }
    .btn-copy-json { position: absolute; top: 0.5rem; right: 0.5rem; background: #fff; border: 1px solid #d1d5da; color: #586069; border-radius: 5px; font-size: 0.65rem; font-weight: 600; padding: 0.2rem 0.6rem; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: 0.15s; }
    .btn-copy-json:hover { background: #f3f4f6; border-color: #b9b9c3; color: #24292e; }
    .json-input-row { display: flex; gap: 0.5rem; align-items: flex-start; }
    .json-paste-textarea { flex: 1; font-family: 'Courier New', monospace !important; font-size: 0.75rem !important; resize: vertical; border: 1px solid #d8d6de; border-radius: 0.357rem; padding: 0.5rem 0.75rem; color: #6e6b7b; background: #fff; min-height: 90px; outline: none; line-height: 1.6; transition: border-color .15s, box-shadow .15s; }
    .json-paste-textarea::placeholder { color: #c2c0cc; }
    .json-paste-textarea:focus { border-color: #7367f0; box-shadow: 0 3px 10px rgba(115,103,240,0.15); }
    .json-paste-textarea.is-valid-json { border-color: #28c76f; box-shadow: 0 3px 10px rgba(40,199,111,0.12); }
    .json-paste-textarea.is-invalid-json { border-color: #ea5455; box-shadow: 0 3px 10px rgba(234,84,85,0.12); }
    .btn-apply-json { flex-shrink: 0; white-space: nowrap; background: #7367f0; color: #fff; border: none; border-radius: 0.357rem; padding: 0.5rem 0.95rem; font-size: 0.78rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 4px 12px rgba(115,103,240,0.3); transition: background .15s, box-shadow .15s; }
    .btn-apply-json:hover { background: #6259d6; box-shadow: 0 4px 16px rgba(115,103,240,0.45); }
    .json-paste-error { font-size: 0.73rem; color: #ea5455; margin-top: 0.45rem; display: none; align-items: center; gap: 5px; }
    .json-paste-error i { font-size: 0.75rem; }

    /* ── Sticky action bar ── */
    .pe-actionbar {
        position: sticky; bottom: 0; z-index: 30;
        background: #fff; border-top: 1px solid #ebe9f1;
        margin: 1.5rem -1.5rem -1.5rem; padding: 1rem 1.5rem;
        display: flex; gap: 0.75rem; align-items: center;
        border-radius: 0 0 0.428rem 0.428rem;
    }
</style>

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        {{-- Breadcrumb --}}
        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div style="min-width:0; flex:1 1 auto;">
                        <div class="d-flex align-items-center gap-1">
                            <h2 class="content-header-title mb-0">Edit Product</h2>
                            <div style="width:1px; height:20px; background:#ebe9f1;"></div>
                            <nav>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                                    <li class="breadcrumb-item active">Edit</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1" style="flex-shrink:0;">
                        <a href="{{ route('admin.products.index') }}"
                           class="btn btn-outline-secondary waves-effect">
                            <i class="fas fa-arrow-left me-50"></i> Back
                        </a>
                        <button type="button" class="pe-drawer-toggle" id="pe-drawer-toggle" aria-label="Open sections">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <section id="edit-product">

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

                <form id="product-form"
                      action="{{ route('admin.products.update', $product->id) }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="pe-drawer-backdrop" id="pe-drawer-backdrop"></div>

                    <div class="pe-layout">

                        {{-- ═══════════════ SIDEBAR NAV ═══════════════ --}}
                        <aside class="pe-sidebar" id="pe-sidebar">
                            <div class="pe-drawer-head">
                                <span>Sections</span>
                                <button type="button" class="pe-drawer-close" id="pe-drawer-close" aria-label="Close">&times;</button>
                            </div>
                            <div class="pe-nav-title">Sections</div>
                            <button type="button" class="pe-nav-item active" data-target="pane-basic">
                                <span class="pe-nav-ico"><i class="fas fa-circle-info"></i></span> Basic Info
                            </button>
                            <button type="button" class="pe-nav-item" data-target="pane-pricing">
                                <span class="pe-nav-ico"><i class="fas fa-tag"></i></span> Pricing &amp; Stock
                            </button>
                            <button type="button" class="pe-nav-item" data-target="pane-org">
                                <span class="pe-nav-ico"><i class="fas fa-folder-tree"></i></span> Organization
                            </button>
                            <button type="button" class="pe-nav-item" data-target="pane-variants">
                                <span class="pe-nav-ico"><i class="fas fa-swatchbook"></i></span> Tags &amp; Variants
                            </button>
                            <button type="button" class="pe-nav-item" data-target="pane-images">
                                <span class="pe-nav-ico"><i class="fas fa-images"></i></span> Images
                                <span class="pe-nav-badge" id="badge-images"></span>
                            </button>
                            <button type="button" class="pe-nav-item" data-target="pane-specs">
                                <span class="pe-nav-ico"><i class="fas fa-list-check"></i></span> Specifications
                            </button>
                            <button type="button" class="pe-nav-item" data-target="pane-install">
                                <span class="pe-nav-ico"><i class="fas fa-screwdriver-wrench"></i></span> Installation
                            </button>
                            <button type="button" class="pe-nav-item" data-target="pane-visibility">
                                <span class="pe-nav-ico"><i class="fas fa-eye"></i></span> Visibility
                            </button>
                        </aside>

                        {{-- ═══════════════ CONTENT CARD ═══════════════ --}}
                        <div class="card mb-0">
                            <div class="card-body">

                                {{-- ━━━━━━━━━━━ BASIC INFO ━━━━━━━━━━━ --}}
                                <div class="pe-panel active" id="pane-basic">
                                    <div class="pe-panel-head">
                                        <div class="pe-panel-ico"><i class="fas fa-circle-info"></i></div>
                                        <div>
                                            <h4>Basic Information</h4>
                                            <p>Product name, identity and description</p>
                                        </div>
                                    </div>

                                    <div class="mb-1">
                                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name"
                                               class="form-control @error('name') is-invalid @enderror"
                                               value="{{ old('name', $product->name) }}" required>
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label for="moq" class="form-label">Minimum Order Quantity (MOQ)</label>
                                        <input type="number" name="moq" id="moq"
                                               class="form-control @error('moq') is-invalid @enderror"
                                               value="{{ old('moq', $product->moq) }}" min="1">
                                        @error('moq') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description"
                                                  class="form-control @error('description') is-invalid @enderror"
                                                  rows="6">{{ old('description', $product->description) }}</textarea>
                                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    @include('partials.block-editor-field', ['blocks' => $product->description_blocks ?? []])
                                </div>

                                {{-- ━━━━━━━━━━━ PRICING & STOCK ━━━━━━━━━━━ --}}
                                <div class="pe-panel" id="pane-pricing">
                                    <div class="pe-panel-head">
                                        <div class="pe-panel-ico"><i class="fas fa-tag"></i></div>
                                        <div>
                                            <h4>Pricing &amp; Stock</h4>
                                            <p>Price, markup, discount and inventory</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-1">
                                            <label for="price" class="form-label">Price (₦) <span class="text-danger">*</span></label>
                                            <input type="number" name="price" id="price"
                                                   class="form-control @error('price') is-invalid @enderror"
                                                   value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
                                            @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="stock" class="form-label">Stock Qty <span class="text-danger">*</span></label>
                                            <input type="number" name="stock" id="stock"
                                                   class="form-control @error('stock') is-invalid @enderror"
                                                   value="{{ old('stock', $product->stock) }}" min="0" required>
                                            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="mb-1">
                                        <label for="markup_percent" class="form-label">
                                            Markup Percentage (%)
                                            <small class="text-muted">Applied to product price</small>
                                        </label>
                                        <input type="number" name="markup_percent" id="markup_percent"
                                               class="form-control @error('markup_percent') is-invalid @enderror"
                                               value="{{ old('markup_percent', $product->markup_percent ?? '') }}"
                                               min="0" max="1000" step="0.01">
                                        @error('markup_percent') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label for="discount_percent" class="form-label">
                                            Discount Percentage (%)
                                            <small class="text-muted">Leave blank to inherit from subcategory / category</small>
                                        </label>
                                        <input type="number" name="discount_percent" id="discount_percent"
                                               class="form-control @error('discount_percent') is-invalid @enderror"
                                               value="{{ old('discount_percent', $product->discount_percent ?? '') }}"
                                               min="0" max="100" step="0.01">
                                        @error('discount_percent') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- ━━━━━━━━━━━ ORGANIZATION ━━━━━━━━━━━ --}}
                                <div class="pe-panel" id="pane-org">
                                    <div class="pe-panel-head">
                                        <div class="pe-panel-ico"><i class="fas fa-folder-tree"></i></div>
                                        <div>
                                            <h4>Organization</h4>
                                            <p>Category, subcategory and brand</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-1">
                                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                            <select name="category_id" id="category_id"
                                                    class="form-control @error('category_id') is-invalid @enderror" required>
                                                <option value="">Select a category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="subcategory_id" class="form-label">Subcategory</label>
                                            <select name="subcategory_id" id="subcategory_id"
                                                    class="form-control @error('subcategory_id') is-invalid @enderror">
                                                <option value="">Select a Subcategory</option>
                                            </select>
                                            @error('subcategory_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label">Brand <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <div class="pill-box" id="brand-box">
                                                <div id="sel-brand" style="display:contents;"></div>
                                                <input type="text" id="brand-inp" placeholder="Search or type a brand…">
                                            </div>
                                            <div class="sugg-box" id="brand-sugg"></div>
                                        </div>
                                        <p class="avail-label">Available Brands</p>
                                        <div id="avail-brands" style="display:flex;flex-wrap:wrap;gap:0.35rem;margin-top:0.4rem;"></div>
                                        @error('brand_id') <p class="text-danger mt-25" style="font-size:.857rem;">{{ $message }}</p> @enderror
                                        @error('brand')    <p class="text-danger mt-25" style="font-size:.857rem;">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- ━━━━━━━━━━━ TAGS & VARIANTS ━━━━━━━━━━━ --}}
                                <div class="pe-panel" id="pane-variants">
                                    <div class="pe-panel-head">
                                        <div class="pe-panel-ico"><i class="fas fa-swatchbook"></i></div>
                                        <div>
                                            <h4>Tags &amp; Variants</h4>
                                            <p>Tags, sizes, colors, locations and rating</p>
                                        </div>
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label">
                                            Tags
                                            <small class="text-muted ms-25">Type Name or Name:Option1,Option2 then Enter</small>
                                        </label>
                                        <div class="position-relative">
                                            <div class="pill-box" id="tag-box">
                                                <div id="sel-tags" style="display:contents;"></div>
                                                <input type="text" id="tag-inp" placeholder="Search or type a tag…">
                                            </div>
                                            <div class="sugg-box" id="tag-sugg"></div>
                                        </div>
                                        <p class="avail-label">Available Tags</p>
                                        <div class="avail-pills" id="avail-tags"></div>
                                        @error('tags') <p class="text-danger mt-25" style="font-size:.857rem;">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-1">
                                            <label class="form-label">Sizes</label>
                                            <div class="position-relative">
                                                <div class="pill-box">
                                                    <div id="sel-sizes" style="display:contents;"></div>
                                                    <input type="text" id="size-inp" placeholder="Search sizes…">
                                                </div>
                                                <div class="sugg-box" id="size-sugg"></div>
                                            </div>
                                            <p class="avail-label">Available</p>
                                            <div class="avail-pills" id="avail-sizes"></div>
                                            @error('sizes') <p class="text-danger mt-25" style="font-size:.857rem;">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label class="form-label">Colors</label>
                                            <div class="position-relative">
                                                <div class="pill-box">
                                                    <div id="sel-colors" style="display:contents;"></div>
                                                    <input type="text" id="color-inp" placeholder="Search colors…">
                                                </div>
                                                <div class="sugg-box" id="color-sugg"></div>
                                            </div>
                                            <p class="avail-label">Available</p>
                                            <div class="avail-pills" id="avail-colors"></div>
                                            @error('colors') <p class="text-danger mt-25" style="font-size:.857rem;">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label">Locations</label>
                                        <div class="position-relative">
                                            <div class="pill-box">
                                                <div id="sel-locs" style="display:contents;"></div>
                                                <input type="text" id="loc-inp" placeholder="Search locations…">
                                            </div>
                                            <div class="sugg-box" id="loc-sugg"></div>
                                        </div>
                                        <p class="avail-label">Available</p>
                                        <div class="avail-pills" id="avail-locs"></div>
                                        @error('locations') <p class="text-danger mt-25" style="font-size:.857rem;">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="mb-1">
                                        @php
                                            $pr  = $product->review_rating;        // live review average (0 when no real reviews)
                                            $prc = $product->review_rating_count;  // real review count (matches products.show)
                                        @endphp
                                        <label class="form-label">Customer Rating</label>
                                        <div class="star-wrap" style="color:#f5c518;">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($pr >= $i)
                                                    <span class="fas fa-star"></span>
                                                @elseif($pr >= $i - 0.5)
                                                    <span class="fas fa-star-half-alt"></span>
                                                @else
                                                    <span class="far fa-star" style="color:#d1d5db;"></span>
                                                @endif
                                            @endfor
                                        </div>
                                        <p class="text-muted mt-25" style="font-size:.8rem;">
                                            @if($prc > 0)
                                                {{ number_format($pr, 1) }} from {{ $prc }} customer review{{ $prc !== 1 ? 's' : '' }}.
                                                Updates automatically as new reviews come in.
                                            @else
                                                No customer reviews yet. The rating updates automatically once customers review this product.
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                {{-- ━━━━━━━━━━━ IMAGES ━━━━━━━━━━━ --}}
                                <div class="pe-panel" id="pane-images">
                                    <div class="pe-panel-head">
                                        <div class="pe-panel-ico"><i class="fas fa-images"></i></div>
                                        <div>
                                            <h4>Product Images</h4>
                                            <p>Manage existing images and upload new ones</p>
                                        </div>
                                    </div>

                                    <div class="mb-1">
                                        <label for="images" class="form-label">Upload New Images</label>
                                        <input type="file" name="images[]" id="images"
                                               class="form-control @error('images') is-invalid @enderror"
                                               multiple accept="image/jpeg,image/png,image/jpg,image/gif">
                                        @error('images') <div class="invalid-feedback">{{ $message }}</div> @enderror

                                        <div id="image-previews">
                                            @foreach ($product->images as $image)
                                                <div class="preview-card" data-image-id="{{ $image->id }}">
                                                    <img src="{{ Storage::url($image->image_url) }}" alt="Product Image">
                                                    <button type="button" class="preview-rm existing-rm"
                                                            data-image-id="{{ $image->id }}">
                                                        <i class="fas fa-xmark"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="removed_images" id="removed_images" value="">
                                    </div>
                                </div>

                                {{-- ━━━━━━━━━━━ SPECIFICATIONS ━━━━━━━━━━━ --}}
                                <div class="pe-panel" id="pane-specs">
                                    <div class="pe-panel-head">
                                        <div class="pe-panel-ico"><i class="fas fa-list-check"></i></div>
                                        <div>
                                            <h4>Grouped Specifications</h4>
                                            <p>Build groups manually or paste JSON</p>
                                        </div>
                                    </div>

                                    <div class="json-paste-card">
                                        <div class="json-paste-card-hd">
                                            <div class="icon-wrap"><i class="fas fa-code"></i></div>
                                            <span class="hd-title">Paste JSON to auto-fill groups</span>
                                            <span class="badge-fmt">Strict Format</span>
                                        </div>
                                        <div class="json-paste-card-bd">
                                            <p class="json-paste-hint">
                                                Must be an <code>array</code> of objects. Each needs <code>"group"</code> (string) and
                                                <code>"attrs"</code> (array of <code>{"key","value"}</code>).
                                                Applying JSON will <strong>replace</strong> all existing groups below.
                                            </p>
                                            <div class="json-code-block">
                                                <button type="button" class="btn-copy-json" id="btn-copy-json">
                                                    <i class="fas fa-copy"></i> Copy
                                                </button>
                                                <pre id="json-example-pre">[
  {
    <span class="jk">"group"</span><span class="jp">:</span> <span class="js">"Basic Info"</span><span class="jp">,</span>
    <span class="jk">"attrs"</span><span class="jp">:</span> <span class="jp">[</span>
      <span class="jp">{</span> <span class="jk">"key"</span><span class="jp">:</span> <span class="js">"Brand"</span><span class="jp">,</span>  <span class="jk">"value"</span><span class="jp">:</span> <span class="js">"Samsung"</span>    <span class="jp">},</span>
      <span class="jp">{</span> <span class="jk">"key"</span><span class="jp">:</span> <span class="js">"Model"</span><span class="jp">,</span>  <span class="jk">"value"</span><span class="jp">:</span> <span class="js">"Galaxy S24"</span> <span class="jp">}</span>
    <span class="jp">]</span>
  <span class="jp">},</span>
  <span class="jp">{</span>
    <span class="jk">"group"</span><span class="jp">:</span> <span class="js">"Electrical"</span><span class="jp">,</span>
    <span class="jk">"attrs"</span><span class="jp">:</span> <span class="jp">[</span>
      <span class="jp">{</span> <span class="jk">"key"</span><span class="jp">:</span> <span class="js">"Voltage"</span><span class="jp">,</span> <span class="jk">"value"</span><span class="jp">:</span> <span class="js">"240V"</span> <span class="jp">},</span>
      <span class="jp">{</span> <span class="jk">"key"</span><span class="jp">:</span> <span class="js">"Wattage"</span><span class="jp">,</span> <span class="jk">"value"</span><span class="jp">:</span> <span class="js">"65W"</span>  <span class="jp">}</span>
    <span class="jp">]</span>
  <span class="jp">}</span>
<span class="jp">]</span></pre>
                                            </div>
                                            <div class="json-input-row">
                                                <textarea id="json-paste-inp" class="json-paste-textarea" rows="4"
                                                    placeholder="Paste your JSON array here…" spellcheck="false"></textarea>
                                                <button type="button" id="json-paste-btn" class="btn-apply-json">
                                                    <i class="fas fa-wand-magic-sparkles"></i> Apply
                                                </button>
                                            </div>
                                            <div class="json-paste-error" id="json-paste-error">
                                                <i class="fas fa-circle-exclamation"></i>
                                                <span id="json-paste-error-msg"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="attr-groups-container"></div>
                                    <button type="button" id="add-group-btn" class="btn btn-outline-primary btn-sm mt-1">
                                        <i class="fas fa-folder-plus me-1"></i> Add Group
                                    </button>

                                    {{-- Preserve existing flat attributes silently on save --}}
                                    @if(empty($product->getAttributeGroups()) && !empty($customAttributes))
                                        <div class="alert alert-warning mb-2 mt-2" style="font-size:.82rem;">
                                            <i class="fas fa-info-circle me-1"></i>
                                            This product has <strong>{{ count($customAttributes) }}</strong> legacy flat attributes.
                                            Add groups above to migrate them — or they will be preserved as-is on save.
                                        </div>
                                        @foreach($customAttributes as $key => $value)
                                            @if($key === 'tag_options') @continue @endif
                                            <input type="hidden" name="options[{{ $loop->index }}][key]"   value="{{ $key }}">
                                            <input type="hidden" name="options[{{ $loop->index }}][value]" value="{{ is_array($value) ? implode(', ', $value) : $value }}">
                                        @endforeach
                                    @endif
                                </div>

                                {{-- ━━━━━━━━━━━ INSTALLATION ━━━━━━━━━━━ --}}
                                <div class="pe-panel" id="pane-install">
                                    <div class="pe-panel-head">
                                        <div class="pe-panel-ico"><i class="fas fa-screwdriver-wrench"></i></div>
                                        <div>
                                            <h4>Installation / Pricing Options</h4>
                                            <p>Add pricing tiers such as Basic Install, Premium Setup</p>
                                        </div>
                                    </div>

                                    <div id="install-opts-container">
                                        @php
                                            $existingInstallOpts = old('installation_options');
                                            if (is_null($existingInstallOpts)) {
                                                $existingInstallOpts = !empty($installationOptions)
                                                    ? $installationOptions
                                                    : [['label' => '', 'price' => '', 'description' => '']];
                                            }
                                        @endphp
                                        @foreach ($existingInstallOpts as $i => $iopt)
                                        <div class="install-opt-row p-2 mb-2">
                                            <div class="row g-2 align-items-end">
                                                <div class="col-md-4">
                                                    <label class="form-label form-label-sm mb-1">
                                                        Option Label <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                           name="installation_options[{{ $i }}][label]"
                                                           class="form-control form-control-sm @error('installation_options.'.$i.'.label') is-invalid @enderror"
                                                           placeholder="e.g. Basic Install"
                                                           value="{{ $iopt['label'] ?? '' }}">
                                                    @error('installation_options.'.$i.'.label')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label form-label-sm mb-1">
                                                        Price (₦) <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number"
                                                           name="installation_options[{{ $i }}][price]"
                                                           class="form-control form-control-sm @error('installation_options.'.$i.'.price') is-invalid @enderror"
                                                           placeholder="0.00" step="0.01" min="0"
                                                           value="{{ $iopt['price'] ?? '' }}">
                                                    @error('installation_options.'.$i.'.price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label form-label-sm mb-1">
                                                        Description <span class="text-muted">(optional)</span>
                                                    </label>
                                                    <input type="text"
                                                           name="installation_options[{{ $i }}][description]"
                                                           class="form-control form-control-sm"
                                                           placeholder="Short note about this tier"
                                                           value="{{ $iopt['description'] ?? '' }}">
                                                </div>
                                                <div class="col-md-1 d-flex align-items-end">
                                                    <button type="button" class="btn-rm-install w-100">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <button type="button" id="add-install-opt-btn" class="btn btn-outline-primary btn-sm mt-1">
                                        <i class="fas fa-plus me-1"></i> Add Option
                                    </button>
                                </div>

                                {{-- ━━━━━━━━━━━ VISIBILITY ━━━━━━━━━━━ --}}
                                <div class="pe-panel" id="pane-visibility">
                                    <div class="pe-panel-head">
                                        <div class="pe-panel-ico"><i class="fas fa-eye"></i></div>
                                        <div>
                                            <h4>Visibility</h4>
                                            <p>Control whether the product is shown to customers</p>
                                        </div>
                                    </div>

                                    <div class="mb-2 d-flex align-items-center gap-1">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                                   value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Active — product is visible to customers
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Truck delivery override --}}
                                    @php
                                        $rawTruck = $product->getRawOriginal('requires_truck');
                                        $truckOption = is_null($rawTruck) ? 'inherit' : ($rawTruck ? 'yes' : 'no');
                                        $truckOption = old('requires_truck_option', $truckOption);
                                    @endphp
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold">Truck Delivery</label>
                                        <div class="text-muted small mb-1">Leave as "Inherit" to follow the category setting.</div>
                                        <div class="d-flex gap-3 align-items-center flex-wrap">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="requires_truck_option" id="truck_inherit"
                                                       value="inherit" {{ $truckOption === 'inherit' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="truck_inherit">Inherit from category</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="requires_truck_option" id="truck_yes"
                                                       value="yes" {{ $truckOption === 'yes' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="truck_yes">Always requires truck</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="requires_truck_option" id="truck_no"
                                                       value="no" {{ $truckOption === 'no' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="truck_no">Never requires truck</label>
                                            </div>
                                        </div>
                                        {{-- Hidden fields that the controller reads --}}
                                        <input type="hidden" name="requires_truck_set" id="requires_truck_set" value="">
                                        <input type="hidden" name="requires_truck" id="requires_truck_val" value="">
                                    </div>

                                    {{-- Individual product weight --}}
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold">Product Weight (kg)</label>
                                        <div class="text-muted small mb-1">Leave blank to inherit from subcategory → category. Set only if this product differs from its subcategory's typical weight.</div>
                                        <input type="number" name="weight_kg" class="form-control"
                                               style="max-width:160px;"
                                               min="0" step="0.1" placeholder="e.g. 2.5"
                                               value="{{ old('weight_kg', $product->getRawOriginal('weight_kg')) }}">
                                    </div>
                                </div>

                                {{-- Shared footer: step nav + dots --}}
                                <div class="pe-panel-foot">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="pe-prev">
                                        <i class="fas fa-arrow-left me-50"></i> Previous
                                    </button>
                                    <div class="pe-step-dots" id="pe-dots"></div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="pe-next">
                                        Next <i class="fas fa-arrow-right ms-50"></i>
                                    </button>
                                </div>

                                {{-- Sticky action bar — submits the whole form --}}
                                <div class="pe-actionbar">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-50"></i> Update Product
                                    </button>
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-xmark me-50"></i> Cancel
                                    </a>
                                    <span class="ms-auto text-muted" style="font-size:.78rem;">
                                        <i class="fas fa-circle-info me-50"></i>Saving applies all sections at once.
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>

{{-- Tag Options Modal --}}
<div class="cp-backdrop" id="tag-modal">
    <div class="cp-modal">
        <div class="cp-modal-hd">
            <h5><i class="fas fa-pen-to-square me-50 text-primary"></i>Edit Tag Options</h5>
            <button class="cp-modal-x" id="m-close"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="cp-modal-bd">
            <div class="mb-1">
                <label class="form-label">Tag Name</label>
                <input type="text" id="m-tag-name" class="form-control" readonly>
            </div>
            <div class="mb-0">
                <label class="form-label">Options <small class="text-muted">(comma-separated)</small></label>
                <input type="text" id="m-tag-opts" class="form-control" placeholder="e.g. Red, Blue, Green">
            </div>
        </div>
        <div class="cp-modal-ft">
            <button class="btn btn-outline-secondary btn-sm" id="m-cancel">Cancel</button>
            <button class="btn btn-primary btn-sm" id="m-save">
                <i class="fas fa-check me-50"></i>Save Options
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ═══════════════ SECTION NAVIGATION ═══════════════ */
    const navItems = Array.from(document.querySelectorAll('.pe-nav-item'));
    const panels   = Array.from(document.querySelectorAll('.pe-panel'));
    const dotsWrap = document.getElementById('pe-dots');
    const prevBtn  = document.getElementById('pe-prev');
    const nextBtn  = document.getElementById('pe-next');
    let curIdx = 0;

    // build step dots
    navItems.forEach((_, i) => {
        const d = document.createElement('span');
        if (i === 0) d.classList.add('active');
        dotsWrap.appendChild(d);
    });
    const dots = Array.from(dotsWrap.children);

    function showPane(idx) {
        idx = Math.max(0, Math.min(navItems.length - 1, idx));
        curIdx = idx;
        const target = navItems[idx].dataset.target;
        navItems.forEach((n, i) => n.classList.toggle('active', i === idx));
        dots.forEach((d, i) => d.classList.toggle('active', i === idx));
        panels.forEach(p => p.classList.toggle('active', p.id === target));
        prevBtn.style.visibility = idx === 0 ? 'hidden' : 'visible';
        nextBtn.style.visibility = idx === navItems.length - 1 ? 'hidden' : 'visible';
        // scroll content into view on mobile
        if (window.innerWidth <= 991) {
            document.getElementById(target).scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    // ── Mobile drawer open/close ──
    const peSidebar  = document.getElementById('pe-sidebar');
    const peBackdrop = document.getElementById('pe-drawer-backdrop');
    const peToggle   = document.getElementById('pe-drawer-toggle');
    const peClose    = document.getElementById('pe-drawer-close');
    function openDrawer() {
        peSidebar.classList.add('open');
        peBackdrop.style.display = 'block';
        requestAnimationFrame(() => peBackdrop.classList.add('show'));
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
        peSidebar.classList.remove('open');
        peBackdrop.classList.remove('show');
        setTimeout(() => { peBackdrop.style.display = 'none'; }, 280);
        document.body.style.overflow = '';
    }
    if (peToggle)   peToggle.addEventListener('click', openDrawer);
    if (peClose)    peClose.addEventListener('click', closeDrawer);
    if (peBackdrop) peBackdrop.addEventListener('click', closeDrawer);

    navItems.forEach((n, i) => n.addEventListener('click', () => {
        showPane(i);
        if (window.innerWidth <= 991) closeDrawer();
    }));
    prevBtn.addEventListener('click', () => showPane(curIdx - 1));
    nextBtn.addEventListener('click', () => showPane(curIdx + 1));
    showPane(0);

    /* ─── Data from DB ─── */
    const categories   = @json($categories);
    const allTags      = @json($tags);
    const allSizes     = @json($sizes);
    const allColors    = @json($colors);
    const allLocations = @json($locations);
    const allBrands    = @json($brands->map(fn($b) => ['id' => $b->id, 'name' => $b->name]));

    /* ─── State — pre-fill from PHP ─── */
    let selTags = @json($selectedTags).map(tag => ({
        id:            tag.id,
        name:          tag.name,
        isNew:         tag.isNew,
        customOptions: tag.customOptions || [tag.name],
    }));
    let selSizes = new Set(@json($product->sizes->pluck('id')->values()));
    let selClrs  = new Set(@json($product->colors->pluck('id')->values()));
    let selLocs  = new Set(@json($product->locations->pluck('id')->values()));

    let selBrand = null;
    @if(old('brand_id') && old('brand'))
        selBrand = { id: {{ old('brand_id') }}, name: @json(old('brand')) };
    @elseif($product->brand_id && $product->brand)
        selBrand = { id: {{ $product->brand_id }}, name: @json($product->brand) };
    @elseif($product->brand)
        const _fb = allBrands.find(b => b.name === @json($product->brand));
        if (_fb) selBrand = _fb;
    @endif

    /* ─── Removed images ─── */
    const removedImagesInput = document.getElementById('removed_images');
    let removedImageIds = [];
    const imgBadge = document.getElementById('badge-images');

    function updateImgBadge() {
        const count = document.querySelectorAll('#image-previews .preview-card').length;
        if (count > 0) { imgBadge.textContent = count; imgBadge.classList.add('show'); }
        else { imgBadge.classList.remove('show'); }
    }

    document.getElementById('image-previews').addEventListener('click', function (e) {
        const btn = e.target.closest('.existing-rm');
        if (!btn) return;
        const id = parseInt(btn.dataset.imageId);
        removedImageIds.push(id);
        removedImagesInput.value = removedImageIds.join(',');
        btn.closest('.preview-card').remove();
        updateImgBadge();
    });

    const fileInput = document.getElementById('images');
    fileInput.addEventListener('change', function () {
        const grid = document.getElementById('image-previews');
        grid.querySelectorAll('.preview-card.new-file').forEach(c => c.remove());
        for (const f of this.files) {
            const card = document.createElement('div'); card.className = 'preview-card new-file';
            const img  = document.createElement('img');
            const rd   = new FileReader(); rd.onload = e => img.src = e.target.result; rd.readAsDataURL(f);
            card.appendChild(img);
            grid.appendChild(card);
        }
        updateImgBadge();
    });
    updateImgBadge();

    /* ─── Stars ─── */
    const starUi    = document.getElementById('star-ui');
    const starEls   = document.querySelectorAll('#star-ui .fa-star');
    const ratingHid = document.getElementById('rating-val');
    if (starUi && ratingHid) {

    function setStars(v) {
        ratingHid.value = v;
        starEls.forEach(s => s.classList.toggle('lit', +s.dataset.value <= v));
    }
    const initR = +ratingHid.value || 0;
    if (initR) setStars(initR);
    starEls.forEach(s => {
        s.addEventListener('click', () => setStars(+s.dataset.value));
        s.addEventListener('mouseenter', () =>
            starEls.forEach(x => x.classList.toggle('lit', +x.dataset.value <= +s.dataset.value))
        );
    });
    starUi.addEventListener('mouseleave', () => setStars(+ratingHid.value || 0));
    }

    /* ─── Category → Subcategory ─── */
    const catEl = document.getElementById('category_id');
    const subEl = document.getElementById('subcategory_id');
    catEl.addEventListener('change', function () {
        subEl.innerHTML = '<option value="">Select a Subcategory</option>';
        const c = categories.find(x => x.id == this.value);
        if (c && c.subcategories) c.subcategories.forEach(s => {
            const o = document.createElement('option');
            o.value = s.id; o.textContent = s.name;
            if (s.id == '{{ old('subcategory_id', $product->subcategory_id) }}') o.selected = true;
            subEl.appendChild(o);
        });
    });
    if (catEl.value) catEl.dispatchEvent(new Event('change'));

    /* ─── Brand ─── */
    const brandInp  = document.getElementById('brand-inp');
    const brandSugg = document.getElementById('brand-sugg');
    const brandCont = document.getElementById('sel-brand');
    const brandAvl  = document.getElementById('avail-brands');

    function renderBrand() {
        brandCont.querySelectorAll('.tag-pill, input[name="brand_id"], input[name="brand"]').forEach(el => el.remove());
        brandAvl.innerHTML = '';
        if (selBrand) {
            const pill = document.createElement('span'); pill.className = 'tag-pill';
            pill.innerHTML = `${selBrand.name} <button type="button" class="btn-close"></button>`;
            pill.querySelector('.btn-close').addEventListener('click', () => { selBrand = null; renderBrand(); });
            const hidId = document.createElement('input');
            hidId.type = 'hidden'; hidId.name = 'brand_id'; hidId.value = selBrand.id;
            const hidStr = document.createElement('input');
            hidStr.type = 'hidden'; hidStr.name = 'brand'; hidStr.value = selBrand.name;
            brandCont.appendChild(pill);
            brandCont.appendChild(hidId);
            brandCont.appendChild(hidStr);
        }
        allBrands.forEach(b => {
            if (selBrand && selBrand.id === b.id) return;
            const p = document.createElement('span'); p.className = 'avail-pill'; p.textContent = b.name;
            p.addEventListener('click', () => { selBrand = b; brandInp.value = ''; renderBrand(); });
            brandAvl.appendChild(p);
        });
    }
    brandInp.addEventListener('input', function () {
        const q = this.value.toLowerCase(); brandSugg.innerHTML = '';
        if (!q) { brandSugg.style.display = 'none'; return; }
        const hits = allBrands.filter(b => b.name.toLowerCase().includes(q) && b.id !== selBrand?.id).slice(0, 8);
        if (!hits.length) { brandSugg.style.display = 'none'; return; }
        hits.forEach(b => {
            const d = document.createElement('div'); d.className = 'sugg-item'; d.textContent = b.name;
            d.addEventListener('mousedown', e => {
                e.preventDefault(); selBrand = b; renderBrand();
                brandInp.value = ''; brandSugg.style.display = 'none';
            });
            brandSugg.appendChild(d);
        });
        brandSugg.style.display = 'block';
    });
    brandInp.addEventListener('blur', () => setTimeout(() => brandSugg.style.display = 'none', 150));
    renderBrand();

    /* ─── Tags ─── */
    const tagInp  = document.getElementById('tag-inp');
    const tagSugg = document.getElementById('tag-sugg');
    const tagCont = document.getElementById('sel-tags');
    const tagAvl  = document.getElementById('avail-tags');

    function renderTags() {
        tagCont.querySelectorAll('.tag-pill, input[name="tags[]"], input[name="new_tag_options_data[]"]').forEach(el => el.remove());
        tagAvl.innerHTML = '';
        selTags.forEach(tag => {
            const pill = document.createElement('span'); pill.className = 'tag-pill';
            let lbl = tag.name;
            if (tag.customOptions && tag.customOptions.length > 0 &&
                !(tag.customOptions.length === 1 && tag.customOptions[0].toLowerCase() === tag.name.toLowerCase()))
                lbl += `: ${tag.customOptions.join(', ')}`;
            pill.innerHTML = `${lbl}
                <button type="button" class="btn-edit-opts">Edit</button>
                <button type="button" class="btn-close"></button>`;
            pill.querySelector('.btn-close').addEventListener('click', () => { selTags = selTags.filter(t => t !== tag); renderTags(); });
            pill.querySelector('.btn-edit-opts').addEventListener('click', () => openTagModal(tag));
            const h1 = document.createElement('input'); h1.type = 'hidden'; h1.name = 'tags[]'; h1.value = tag.isNew ? tag.name : tag.id;
            tagCont.appendChild(pill); tagCont.appendChild(h1);
            if (tag.customOptions && tag.customOptions.length) {
                const h2 = document.createElement('input'); h2.type = 'hidden'; h2.name = 'new_tag_options_data[]';
                h2.value = JSON.stringify({ name: tag.name, options: tag.customOptions });
                tagCont.appendChild(h2);
            }
        });
        allTags.forEach(t => {
            if (selTags.some(s => (s.id && s.id === t.id) || s.name.toLowerCase() === t.name.toLowerCase())) return;
            const p = document.createElement('span'); p.className = 'avail-pill'; p.textContent = t.name;
            p.addEventListener('click', () => addTag({ id: t.id, name: t.name, isNew: false, customOptions: [t.name] }));
            tagAvl.appendChild(p);
        });
    }
    function addTag(obj) {
        if (!selTags.some(t => (t.id && t.id === obj.id) || t.name.toLowerCase() === obj.name.toLowerCase())) {
            selTags.push(obj); renderTags();
        }
        tagInp.value = ''; tagSugg.style.display = 'none';
    }
    tagInp.addEventListener('input', function () {
        const q = this.value.toLowerCase(); tagSugg.innerHTML = '';
        if (!q) { tagSugg.style.display = 'none'; return; }
        const hits = allTags.filter(t =>
            t.name.toLowerCase().includes(q) &&
            !selTags.some(s => s.id === t.id || s.name.toLowerCase() === t.name.toLowerCase())
        );
        if (!hits.length) { tagSugg.style.display = 'none'; return; }
        hits.forEach(t => {
            const d = document.createElement('div'); d.className = 'sugg-item'; d.textContent = t.name;
            d.addEventListener('mousedown', e => { e.preventDefault(); addTag({ id: t.id, name: t.name, isNew: false, customOptions: [t.name] }); });
            tagSugg.appendChild(d);
        });
        tagSugg.style.display = 'block';
    });
    tagInp.addEventListener('blur', () => setTimeout(() => tagSugg.style.display = 'none', 150));
    tagInp.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return; e.preventDefault();
        const val = this.value.trim(); if (!val) return;
        let name, opts;
        if (val.includes(':')) {
            const p = val.split(':', 2); name = p[0].trim();
            opts = p[1].split(',').map(o => o.trim()).filter(Boolean);
            if (!opts.length) opts = [name];
        } else { name = val; opts = [name]; }
        const ex = allTags.find(t => t.name.toLowerCase() === name.toLowerCase());
        ex ? addTag({ id: ex.id, name: ex.name, isNew: false, customOptions: opts })
           : addTag({ name, isNew: true, customOptions: opts });
    });
    renderTags();

    /* ─── Tag Modal ─── */
    const tModal   = document.getElementById('tag-modal');
    const mTagName = document.getElementById('m-tag-name');
    const mTagOpts = document.getElementById('m-tag-opts');
    function openTagModal(tag) {
        mTagName.value = tag.name;
        mTagOpts.value = (tag.customOptions || []).join(', ');
        tModal.classList.add('open');
        document.getElementById('m-save').onclick = () => {
            const o = mTagOpts.value.split(',').map(x => x.trim()).filter(Boolean);
            tag.customOptions = o.length ? o : [tag.name];
            renderTags(); tModal.classList.remove('open');
        };
    }
    document.getElementById('m-close').addEventListener('click',  () => tModal.classList.remove('open'));
    document.getElementById('m-cancel').addEventListener('click', () => tModal.classList.remove('open'));
    tModal.addEventListener('click', e => { if (e.target === tModal) tModal.classList.remove('open'); });

    /* ─── Generic pill selector (sizes / colors / locations) ─── */
    function mkSel({ inpId, suggId, selId, avlId, items, state, field }) {
        const inp = document.getElementById(inpId), sg = document.getElementById(suggId),
              sc  = document.getElementById(selId),  av = document.getElementById(avlId);
        function render() {
            sc.querySelectorAll(`.tag-pill, input[name="${field}"]`).forEach(el => el.remove());
            av.innerHTML = '';
            state.forEach(id => {
                const item = items.find(x => x.id === id); if (!item) return;
                const pill = document.createElement('span'); pill.className = 'tag-pill';
                pill.innerHTML = `${item.name} <button type="button" class="btn-close"></button>`;
                pill.querySelector('.btn-close').addEventListener('click', () => { state.delete(id); render(); });
                const h = document.createElement('input'); h.type = 'hidden'; h.name = field; h.value = id;
                sc.appendChild(pill); sc.appendChild(h);
            });
            items.forEach(item => {
                if (state.has(item.id)) return;
                const p = document.createElement('span'); p.className = 'avail-pill'; p.textContent = item.name;
                p.addEventListener('click', () => { state.add(item.id); inp.value = ''; render(); });
                av.appendChild(p);
            });
        }
        inp.addEventListener('input', function () {
            const q = this.value.toLowerCase(); sg.innerHTML = '';
            if (!q) { sg.style.display = 'none'; return; }
            const hits = items.filter(x => x.name.toLowerCase().includes(q) && !state.has(x.id));
            if (!hits.length) { sg.style.display = 'none'; return; }
            hits.forEach(x => {
                const d = document.createElement('div'); d.className = 'sugg-item'; d.textContent = x.name;
                d.addEventListener('mousedown', e => { e.preventDefault(); state.add(x.id); inp.value = ''; sg.style.display = 'none'; render(); });
                sg.appendChild(d);
            });
            sg.style.display = 'block';
        });
        inp.addEventListener('blur', () => setTimeout(() => sg.style.display = 'none', 150));
        render();
    }

    mkSel({ inpId: 'size-inp',  suggId: 'size-sugg',  selId: 'sel-sizes',  avlId: 'avail-sizes',  items: allSizes,     state: selSizes, field: 'sizes[]' });
    mkSel({ inpId: 'color-inp', suggId: 'color-sugg', selId: 'sel-colors', avlId: 'avail-colors', items: allColors,    state: selClrs,  field: 'colors[]' });
    mkSel({ inpId: 'loc-inp',   suggId: 'loc-sugg',   selId: 'sel-locs',   avlId: 'avail-locs',   items: allLocations, state: selLocs,  field: 'locations[]' });

    /* ─── Grouped Attributes ─── */
    const attrContainer = document.getElementById('attr-groups-container');

    @if(old('attr_groups'))
        @foreach(old('attr_groups') as $og)
            addGroup(
                @json($og['group'] ?? ''),
                @json(array_values(array_filter($og['attrs'] ?? [], fn($a) => !empty($a['key']))))
            );
        @endforeach
    @else
        @foreach($product->getAttributeGroups() as $eg)
            addGroup(
                @json($eg['group'] ?? ''),
                @json($eg['attrs'] ?? [])
            );
        @endforeach
    @endif

    document.getElementById('add-group-btn').addEventListener('click', () => addGroup());

    /* ─── JSON Paste Logic ─── */
    const jsonInp    = document.getElementById('json-paste-inp');
    const jsonErrEl  = document.getElementById('json-paste-error');
    const jsonErrMsg = document.getElementById('json-paste-error-msg');

    function validateStrictFormat(parsed) {
        if (!Array.isArray(parsed))
            return 'Must be a JSON array [ ... ]. Objects and other types are not accepted.';
        if (parsed.length === 0)
            return 'Array is empty — add at least one group object.';
        for (let i = 0; i < parsed.length; i++) {
            const g = parsed[i];
            if (typeof g !== 'object' || g === null || Array.isArray(g))
                return `Item at index ${i} must be an object with "group" and "attrs".`;
            if (typeof g.group !== 'string' || g.group.trim() === '')
                return `Item at index ${i} is missing a non-empty "group" string.`;
            if (!Array.isArray(g.attrs))
                return `Item at index ${i} ("${g.group}") must have an "attrs" array.`;
            for (let j = 0; j < g.attrs.length; j++) {
                const a = g.attrs[j];
                if (typeof a !== 'object' || a === null)
                    return `"${g.group}" → attrs[${j}] must be an object with "key" and "value".`;
                if (typeof a.key !== 'string')
                    return `"${g.group}" → attrs[${j}] is missing a "key" string.`;
                if (!('value' in a))
                    return `"${g.group}" → attrs[${j}] (key: "${a.key}") is missing a "value" field.`;
            }
        }
        return null;
    }

    function showJsonError(msg) {
        jsonErrMsg.textContent = msg;
        jsonErrEl.style.display = 'flex';
        jsonInp.classList.remove('is-valid-json');
        jsonInp.classList.add('is-invalid-json');
    }
    function clearJsonError() {
        jsonErrEl.style.display = 'none';
        jsonInp.classList.remove('is-invalid-json');
    }

    jsonInp.addEventListener('input', function () {
        const raw = this.value.trim();
        if (!raw) { clearJsonError(); jsonInp.classList.remove('is-valid-json'); return; }
        try {
            const parsed = JSON.parse(raw);
            const err = validateStrictFormat(parsed);
            if (err) showJsonError(err);
            else { clearJsonError(); jsonInp.classList.add('is-valid-json'); }
        } catch (e) {
            showJsonError('Invalid JSON syntax — check for missing commas, brackets, or quotes.');
        }
    });

    document.getElementById('json-paste-btn').addEventListener('click', function () {
        const raw = jsonInp.value.trim();
        clearJsonError();
        if (!raw) { showJsonError('Please paste your JSON before clicking Apply.'); return; }
        let parsed;
        try { parsed = JSON.parse(raw); }
        catch (e) { showJsonError('Invalid JSON syntax — check for missing commas, brackets, or quotes.'); return; }
        const err = validateStrictFormat(parsed);
        if (err) { showJsonError(err); return; }
        attrContainer.innerHTML = '';
        parsed.forEach(g => {
            const attrs = (g.attrs || []).filter(a => a.key && String(a.key).trim() !== '');
            addGroup(g.group.trim(), attrs.map(a => ({ key: String(a.key), value: String(a.value ?? '') })));
        });
        jsonInp.value = '';
        jsonInp.classList.remove('is-valid-json', 'is-invalid-json');
        clearJsonError();
        showToast(`${parsed.length} group${parsed.length !== 1 ? 's' : ''} loaded from JSON.`, 'success');
    });

    document.getElementById('btn-copy-json').addEventListener('click', function () {
        const exampleJson = JSON.stringify([
            { group: "Basic Info",  attrs: [{ key: "Brand", value: "Samsung" }, { key: "Model", value: "Galaxy S24" }] },
            { group: "Electrical", attrs: [{ key: "Voltage", value: "240V" }, { key: "Wattage", value: "65W" }] }
        ], null, 2);
        navigator.clipboard.writeText(exampleJson).then(() => {
            this.innerHTML = '<i class="fas fa-check"></i> Copied!';
            setTimeout(() => { this.innerHTML = '<i class="fas fa-copy"></i> Copy'; }, 2000);
        });
    });

    /* ─── addGroup / addAttrRow helpers ─── */
    function addGroup(groupName = '', attrs = []) {
        const gIdx = attrContainer.querySelectorAll('.attr-group-card').length;
        const card = document.createElement('div');
        card.className = 'attr-group-card';
        const header = document.createElement('div');
        header.className = 'attr-group-header';
        header.innerHTML = `
            <i class="fas fa-layer-group" style="color:#7367f0;font-size:0.8rem;flex-shrink:0;"></i>
            <input type="text"
                   class="group-name-inp"
                   name="attr_groups[${gIdx}][group]"
                   placeholder="Group name e.g. Basic Info, Electrical, Dimensions…"
                   value="${esc(groupName)}">
            <button type="button" class="btn-rm-group" title="Remove group">
                <i class="fas fa-trash-alt"></i>
            </button>`;
        header.querySelector('.btn-rm-group').addEventListener('click', () => { card.remove(); reIndexGroups(); });
        const body = document.createElement('div');
        body.className = 'attr-group-body';
        const colHeaders = document.createElement('div');
        colHeaders.className = 'attr-row-header';
        colHeaders.innerHTML = `<span>Key / Attribute</span><span>Value</span><span></span>`;
        body.appendChild(colHeaders);
        const rowsWrap = document.createElement('div');
        rowsWrap.className = 'attr-rows-wrap';
        const rowData = attrs.length ? attrs : [{ key: '', value: '' }];
        rowData.forEach(attr => addAttrRow(rowsWrap, gIdx, attr.key || '', attr.value || ''));
        const addRowBtn = document.createElement('button');
        addRowBtn.type = 'button';
        addRowBtn.className = 'btn-add-attr-row';
        addRowBtn.innerHTML = `<i class="fas fa-plus"></i> Add Row`;
        addRowBtn.addEventListener('click', () => { addAttrRow(rowsWrap, getGroupIndex(card)); });
        body.appendChild(rowsWrap);
        body.appendChild(addRowBtn);
        card.appendChild(header);
        card.appendChild(body);
        attrContainer.appendChild(card);
    }

    function addAttrRow(wrap, gIdx, key = '', value = '') {
        const rIdx = wrap.querySelectorAll('.attr-group-row').length;
        const row = document.createElement('div');
        row.className = 'attr-group-row';
        row.innerHTML = `
            <input type="text" name="attr_groups[${gIdx}][attrs][${rIdx}][key]"   placeholder="e.g. Weight" value="${esc(key)}">
            <input type="text" name="attr_groups[${gIdx}][attrs][${rIdx}][value]" placeholder="e.g. 2kg"    value="${esc(value)}">
            <button type="button" class="btn-rm-attr-row" title="Remove row"><i class="fas fa-times"></i></button>`;
        row.querySelector('.btn-rm-attr-row').addEventListener('click', () => {
            row.remove();
            reIndexRows(wrap, getGroupIndex(wrap.closest('.attr-group-card')));
        });
        wrap.appendChild(row);
    }

    function getGroupIndex(card) {
        return Array.from(attrContainer.querySelectorAll('.attr-group-card')).indexOf(card);
    }
    function reIndexGroups() {
        attrContainer.querySelectorAll('.attr-group-card').forEach((card, gIdx) => {
            card.querySelector('.group-name-inp').name = `attr_groups[${gIdx}][group]`;
            reIndexRows(card.querySelector('.attr-rows-wrap'), gIdx);
        });
    }
    function reIndexRows(wrap, gIdx) {
        if (!wrap) return;
        wrap.querySelectorAll('.attr-group-row').forEach((row, rIdx) => {
            const inputs = row.querySelectorAll('input');
            inputs[0].name = `attr_groups[${gIdx}][attrs][${rIdx}][key]`;
            inputs[1].name = `attr_groups[${gIdx}][attrs][${rIdx}][value]`;
        });
    }
    function esc(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    /* ─── Installation / Pricing Options ─── */
    const installCon = document.getElementById('install-opts-container');

    function reIndexInstall() {
        installCon.querySelectorAll('.install-opt-row').forEach((row, i) => {
            row.querySelectorAll('input').forEach(inp => {
                inp.name = inp.name.replace(/installation_options\[\d+\]/, `installation_options[${i}]`);
            });
        });
    }

    function addInstallRow(label = '', price = '', description = '') {
        const i = installCon.querySelectorAll('.install-opt-row').length;
        const row = document.createElement('div');
        row.className = 'install-opt-row p-2 mb-2';
        row.innerHTML = `
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label form-label-sm mb-1">Option Label <span class="text-danger">*</span></label>
                    <input type="text" name="installation_options[${i}][label]" class="form-control form-control-sm"
                           placeholder="e.g. Basic Install" value="${label}">
                </div>
                <div class="col-md-3">
                    <label class="form-label form-label-sm mb-1">Price (₦) <span class="text-danger">*</span></label>
                    <input type="number" name="installation_options[${i}][price]" class="form-control form-control-sm"
                           placeholder="0.00" step="0.01" min="0" value="${price}">
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-sm mb-1">Description <span class="text-muted">(optional)</span></label>
                    <input type="text" name="installation_options[${i}][description]" class="form-control form-control-sm"
                           placeholder="Short note about this tier" value="${description}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn-rm-install w-100"><i class="fas fa-trash-alt"></i></button>
                </div>
            </div>`;
        row.querySelector('.btn-rm-install').addEventListener('click', () => { row.remove(); reIndexInstall(); });
        installCon.appendChild(row);
    }

    document.getElementById('add-install-opt-btn').addEventListener('click', () => addInstallRow());
    installCon.querySelectorAll('.btn-rm-install').forEach(btn => {
        btn.addEventListener('click', () => { btn.closest('.install-opt-row').remove(); reIndexInstall(); });
    });

    /* ─── Submit validation — jump to the offending section ─── */
    document.getElementById('product-form').addEventListener('submit', function (e) {
        if (!selBrand) {
            e.preventDefault();
            // Organization pane is index 2
            const orgIdx = navItems.findIndex(n => n.dataset.target === 'pane-org');
            if (orgIdx >= 0) showPane(orgIdx);
            showToast('Please select a brand.', 'danger');
        }
    });

    /* ─── Toast ─── */
    function showToast(msg, type = 'danger') {
        const m = {
            danger:  { bg: '#ffeef0', bd: '#ffcdd2', c: '#c62828', ic: 'circle-exclamation' },
            warning: { bg: '#fff8e1', bd: '#ffecb3', c: '#f57f17', ic: 'triangle-exclamation' },
            success: { bg: '#e8f5e9', bd: '#c8e6c9', c: '#2e7d32', ic: 'check-circle' },
        };
        const s = m[type] || m.danger;
        const el = document.createElement('div'); el.className = 'cp-toast';
        el.style.cssText = `background:${s.bg};border:1px solid ${s.bd};color:${s.c};`;
        el.innerHTML = `<i class="fas fa-${s.ic}"></i>${msg}`;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 4000);
    }

    /* ═══════════════ TRUCK DELIVERY RADIO ═══════════════ */
    function syncTruckHiddens() {
        const selected = document.querySelector('input[name="requires_truck_option"]:checked');
        const setInput = document.getElementById('requires_truck_set');
        const valInput = document.getElementById('requires_truck_val');
        if (!selected || selected.value === 'inherit') {
            setInput.value = '';
            valInput.value = '';
        } else {
            setInput.value = '1';
            valInput.value = selected.value === 'yes' ? '1' : '0';
        }
    }
    document.querySelectorAll('input[name="requires_truck_option"]').forEach(function(r) {
        r.addEventListener('change', syncTruckHiddens);
    });
    syncTruckHiddens();
});
</script>

@endsection