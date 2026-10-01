<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $appStoreName ?? 'Albertina' }} — Admin</title>

    <link rel="icon" type="image/x-icon"       href="{{ asset('favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180"    href="{{ asset('favicon/apple-touch-icon.png') }}">

    <!-- Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap + Vuexy component CSS -->
    <link rel="stylesheet" href="{{ asset('/app-asset/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/app-asset/vendors/css/charts/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('/app-asset/vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/app-asset/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('/app-asset/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" href="{{ asset('/app-asset/css/colors.css') }}">
    <link rel="stylesheet" href="{{ asset('/app-asset/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('/app-asset/css/plugins/charts/chart-apex.css') }}">
    <link rel="stylesheet" href="{{ asset('/app-asset/css/plugins/extensions/ext-component-toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('/asset/css/style.css') }}">

    @livewireStyles

    <style>
        /* ══════════════════════════════════════════════════
           ALBERTINA ADMIN — Horizontal layout
        ══════════════════════════════════════════════════ */
        :root {
            --al-top:          60px;
            --al-bg:           #f9fafb;
            --al-surface:      #ffffff;
            --al-border:       #e5e7eb;
            --al-border-2:     #f3f4f6;
            --al-text:         #111827;
            --al-text-2:       #374151;
            --al-text-3:       #6b7280;
            --al-text-4:       #9ca3af;
            --al-primary:      #3d8012;
            --al-primary-dk:   #2a5a0a;
            --al-primary-lt:   #f0fce8;
            --al-radius:       10px;
            --al-shadow:       0 1px 3px rgba(0,0,0,.05), 0 1px 2px rgba(0,0,0,.04);
        }

        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            overflow-x: hidden !important;
            overflow-y: auto !important;
            height: auto !important;
        }
        html { -webkit-text-size-adjust: 100%; }

        body {
            margin: 0; padding: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            font-size: 14px; line-height: 1.5;
            color: var(--al-text-2);
            background: var(--al-bg);
            -webkit-font-smoothing: antialiased;
            background-image: none !important;
        }

        .content-overlay,
        .header-navbar-shadow,
        .sidenav-overlay,
        .drag-target {
            display: none !important;
            pointer-events: none !important;
            visibility: hidden !important;
        }

        section, table {
            background: none !important;
            background-image: none !important;
            height: auto !important;
            font-size: inherit !important;
            font-weight: inherit !important;
        }

        .app-content.content {
            padding: 0 !important;
            margin: 0 !important;
            min-height: unset !important;
            float: none !important;
        }
        .content-wrapper.container-xxl {
            max-width: 100% !important;
            padding: 0 !important;
        }
        .content-header {
            padding: 0 !important;
            margin-bottom: 20px !important;
        }
        .content-header-title {
            font-family: 'Inter', sans-serif !important;
            font-size: 20px !important;
            font-weight: 700 !important;
            color: var(--al-text) !important;
            letter-spacing: -.2px !important;
        }
        .breadcrumb { font-size: 12px !important; margin: 2px 0 0 !important; }
        .breadcrumb-item a { color: var(--al-text-4) !important; text-decoration: none !important; }
        .breadcrumb-item.active { color: var(--al-text-3) !important; }
        .breadcrumb-item + .breadcrumb-item::before { color: var(--al-text-4) !important; }

        /* ════════════════════════════════════════
           SHELL
        ════════════════════════════════════════ */
        .al-shell { display: flex; flex-direction: column; min-height: 100vh; }

        /* ════════════════════════════════════════
           TOPBAR
        ════════════════════════════════════════ */
        .al-topbar {
            height: var(--al-top);
            background: var(--al-surface);
            border-bottom: 1px solid var(--al-border);
            display: flex; align-items: center;
            padding: 0 20px; gap: 0;
            position: sticky; top: 0; z-index: 300;
        }

        .al-hamburger {
            display: none; width: 36px; height: 36px;
            align-items: center; justify-content: center;
            border: 1px solid var(--al-border); border-radius: 8px;
            background: none; cursor: pointer; color: var(--al-text-3);
            font-size: 16px; flex-shrink: 0; margin-right: 12px;
        }

        /* Logo */
        .al-logo {
            display: flex; align-items: center; flex-shrink: 0;
            margin-right: 16px; text-decoration: none; height: 100%;
        }
        .al-logo img { height: 34px; width: auto; max-width: 150px; object-fit: contain; display: block; }

        /* ════════════════════════════════════════
           HORIZONTAL NAV
        ════════════════════════════════════════ */
        .al-hnav {
            flex: 1; display: flex; align-items: center;
            height: 100%; gap: 2px; overflow: visible;
        }
        .al-hnav-group { position: relative; display: flex; align-items: center; height: 100%; }

        .al-hnav-item {
            display: flex; align-items: center; gap: 6px;
            padding: 0 10px; height: 36px; border-radius: 7px;
            font-size: 13.5px; font-weight: 500; font-family: 'Inter', sans-serif;
            color: var(--al-text-3); text-decoration: none;
            border: none; background: none; cursor: pointer; white-space: nowrap;
            transition: color .13s, background .13s;
        }
        .al-hnav-item:hover { color: var(--al-text); background: #f3f4f6; }
        .al-hnav-item.active { color: var(--al-primary); background: var(--al-primary-lt); font-weight: 600; }
        .al-hnav-chev { font-size: 9px; color: var(--al-text-4); transition: transform .18s; margin-left: 2px; }
        .al-hnav-group.open > .al-hnav-item .al-hnav-chev { transform: rotate(180deg); }

        /* Dropdowns */
        .al-hnav-dd {
            position: absolute; top: calc(100% + 6px); left: 0;
            min-width: 190px; background: var(--al-surface);
            border: 1px solid var(--al-border); border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,.09);
            padding: 4px; display: none; z-index: 600;
            animation: alFade .15s ease;
        }
        .al-hnav-dd.open { display: block; }
        .al-hnav-dd a {
            display: flex; align-items: center; gap: 8px;
            padding: 7px 12px; border-radius: 7px;
            font-size: 13px; color: var(--al-text-2);
            text-decoration: none; white-space: nowrap;
            transition: background .12s;
        }
        .al-hnav-dd a i { width: 15px; font-size: 12px; color: var(--al-text-4); text-align: center; flex-shrink: 0; transition: color .12s; }
        .al-hnav-dd a:hover { background: #f3f4f6; color: var(--al-text); }
        .al-hnav-dd a:hover i { color: var(--al-primary); }
        .al-hnav-dd a.al-active { color: var(--al-primary); background: var(--al-primary-lt); font-weight: 500; }
        .al-hnav-dd a.al-active i { color: var(--al-primary); }

        @keyframes alFade { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }

        /* Topbar right */
        .al-topbar-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; margin-left: 12px; }

        .al-topbar-link {
            display: flex; align-items: center; justify-content: center;
            width: 34px; height: 34px; border-radius: 8px;
            color: var(--al-text-3); text-decoration: none;
            border: 1px solid var(--al-border); background: var(--al-surface);
            font-size: 14px; transition: all .13s;
        }
        .al-topbar-link:hover { background: #f3f4f6; color: var(--al-text); border-color: #d1d5db; }

        /* User dropdown */
        .al-user { position: relative; }
        .al-user-btn {
            display: flex; align-items: center; gap: 8px;
            padding: 5px 10px 5px 5px; border-radius: 8px;
            border: 1px solid var(--al-border); background: var(--al-surface);
            cursor: pointer; transition: all .13s; white-space: nowrap;
        }
        .al-user-btn:hover { background: #f3f4f6; border-color: #d1d5db; }
        .al-avatar {
            width: 30px; height: 30px; border-radius: 7px;
            background: var(--al-primary); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; flex-shrink: 0; overflow: hidden;
        }
        .al-avatar img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .al-user-label { font-size: 13px; font-weight: 500; color: var(--al-text-2); max-width: 160px; overflow: hidden; text-overflow: ellipsis; }
        .al-user-chev { font-size: 9px; color: var(--al-text-4); transition: transform .18s; }
        .al-user-btn[aria-expanded="true"] .al-user-chev { transform: rotate(180deg); }
        .al-user-dd {
            position: absolute; top: calc(100% + 8px); right: 0;
            min-width: 200px; background: var(--al-surface);
            border: 1px solid var(--al-border); border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,.09);
            padding: 4px; display: none; z-index: 600;
            animation: alFade .15s ease;
        }
        .al-user-dd.open { display: block; }
        .al-user-dd-info { padding: 10px 12px 8px; border-bottom: 1px solid var(--al-border-2); margin-bottom: 4px; }
        .al-user-dd-email { font-size: 12px; color: var(--al-text-4); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .al-user-dd a, .al-user-dd button {
            display: flex; align-items: center; gap: 9px;
            padding: 7px 12px; border-radius: 7px;
            font-size: 13px; color: var(--al-text-2);
            text-decoration: none; transition: background .12s;
            width: 100%; border: none; background: none; cursor: pointer;
            text-align: left; font-family: 'Inter', sans-serif;
        }
        .al-user-dd a i, .al-user-dd button i { width: 14px; color: var(--al-text-4); font-size: 13px; text-align: center; }
        .al-user-dd a:hover, .al-user-dd button:hover { background: #f3f4f6; }
        .al-user-dd .al-logout { color: #dc2626 !important; }
        .al-user-dd .al-logout i { color: #dc2626 !important; }
        .al-user-dd .al-logout:hover { background: #fef2f2 !important; }
        .al-dd-divider { height: 1px; background: var(--al-border-2); margin: 4px 8px; }

        /* ════════════════════════════════════════
           CONTENT
        ════════════════════════════════════════ */
        .al-content { flex: 1; padding: 24px; }

        /* ════════════════════════════════════════
           MOBILE DRAWER
        ════════════════════════════════════════ */
        .al-drawer {
            position: fixed; top: 0; left: 0; bottom: 0; width: 260px;
            background: var(--al-surface); border-right: 1px solid var(--al-border);
            z-index: 400; transform: translateX(-100%);
            transition: transform .25s cubic-bezier(.4,0,.2,1);
            display: flex; flex-direction: column;
            overflow-y: auto; scrollbar-width: thin;
            scrollbar-color: var(--al-border) transparent;
        }
        .al-drawer.open { transform: translateX(0); box-shadow: 4px 0 24px rgba(0,0,0,.12); }
        .al-drawer::-webkit-scrollbar { width: 4px; }
        .al-drawer::-webkit-scrollbar-thumb { background: var(--al-border); border-radius: 4px; }

        .al-drawer-logo {
            display: flex; align-items: center; padding: 14px 16px;
            border-bottom: 1px solid var(--al-border-2);
            flex-shrink: 0; text-decoration: none;
        }
        .al-drawer-logo img { height: 34px; width: auto; max-width: 180px; }

        .al-dnav { flex: 1; padding: 8px; }
        .al-dnav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 10px; border-radius: 8px;
            font-size: 13.5px; font-weight: 500; color: var(--al-text-3);
            cursor: pointer; border: none; background: none;
            width: 100%; text-align: left; text-decoration: none;
            transition: background .13s, color .13s; margin-bottom: 2px;
        }
        .al-dnav-item:hover { background: #f3f4f6; color: var(--al-text); }
        .al-dnav-item.active { background: var(--al-primary-lt); color: var(--al-primary); font-weight: 600; }
        .al-dnav-item i.ni { width: 17px; font-size: 13.5px; text-align: center; flex-shrink: 0; }
        .al-dnav-chev { margin-left: auto; font-size: 9px; color: var(--al-text-4); transition: transform .2s; flex-shrink: 0; }
        .al-dgroup-toggle.open > .al-dnav-chev { transform: rotate(90deg); }

        .al-dsub { overflow: hidden; max-height: 0; transition: max-height .25s ease; }
        .al-dsub.open { max-height: 800px; }
        .al-dsub a {
            display: flex; align-items: center; gap: 9px;
            padding: 7px 10px 7px 28px; border-radius: 7px;
            font-size: 13px; font-weight: 400; color: var(--al-text-3);
            text-decoration: none; transition: background .12s, color .12s; margin-bottom: 1px;
        }
        .al-dsub a i { width: 15px; font-size: 12px; color: var(--al-text-4); text-align: center; flex-shrink: 0; transition: color .12s; }
        .al-dsub a:hover { background: #f3f4f6; color: var(--al-text); }
        .al-dsub a:hover i, .al-dsub a.al-active i { color: var(--al-primary); }
        .al-dsub a.al-active { color: var(--al-primary); font-weight: 600; }

        .al-drawer-foot { padding: 8px; border-top: 1px solid var(--al-border-2); flex-shrink: 0; }
        .al-drawer-foot a {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 10px; border-radius: 8px;
            font-size: 13px; font-weight: 500; color: var(--al-text-3);
            text-decoration: none; transition: background .12s, color .12s; margin-bottom: 2px;
        }
        .al-drawer-foot a i { width: 17px; font-size: 13px; text-align: center; flex-shrink: 0; }
        .al-drawer-foot a:hover { background: #f3f4f6; color: var(--al-text); }
        .al-drawer-foot .al-logout:hover { background: #fef2f2 !important; color: #dc2626 !important; }

        /* Overlay */
        .al-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 399; }
        .al-overlay.open { display: block; }

        /* ════════════════════════════════════════
           BOOTSTRAP COMPONENT OVERRIDES
        ════════════════════════════════════════ */

        /* Cards */
        .card {
            background: var(--al-surface) !important;
            border: 1px solid var(--al-border) !important;
            border-radius: var(--al-radius) !important;
            box-shadow: var(--al-shadow) !important;
            margin-bottom: 20px;
        }
        .card-header {
            background: var(--al-surface) !important;
            border-bottom: 1px solid var(--al-border-2) !important;
            padding: 14px 20px !important;
        }
        .card-title { font-size: 14px !important; font-weight: 600 !important; color: var(--al-text) !important; font-family: 'Inter', sans-serif !important; }
        .card-body { padding: 20px !important; }
        .card-footer { background: var(--al-surface) !important; border-top: 1px solid var(--al-border-2) !important; padding: 12px 20px !important; }

        /* Tables */
        .table { font-size: 13.5px; font-family: 'Inter', sans-serif; }
        .table thead th {
            font-size: 11px !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: .5px !important;
            color: var(--al-text-4) !important;
            border-bottom: 1px solid var(--al-border) !important;
            border-top: none !important;
            padding: 10px 14px !important;
            background: var(--al-surface) !important;
            white-space: nowrap;
        }
        .table tbody td {
            padding: 11px 14px !important;
            border-bottom: 1px solid var(--al-border-2) !important;
            border-top: none !important;
            color: var(--al-text-2);
            vertical-align: middle !important;
        }
        .table tbody tr:last-child td { border-bottom: none !important; }
        .table-hover tbody tr:hover > * { background: #fafafa !important; --bs-table-accent-bg: #fafafa; }
        .table-responsive { overflow-x: auto; border-radius: 0 0 var(--al-radius) var(--al-radius); }

        /* Buttons */
        .btn {
            font-family: 'Inter', sans-serif !important;
            border-radius: 8px !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            padding: 7px 15px !important;
            line-height: 1.4 !important;
            transition: all .13s !important;
        }
        .btn-sm { padding: 5px 10px !important; font-size: 12px !important; }
        .btn-lg { padding: 10px 22px !important; font-size: 15px !important; }
        .btn-primary  { background: var(--al-primary) !important; border-color: var(--al-primary) !important; color: #fff !important; }
        .btn-primary:hover  { background: var(--al-primary-dk) !important; border-color: var(--al-primary-dk) !important; }
        .btn-outline-primary { color: var(--al-primary) !important; border-color: var(--al-primary) !important; background: transparent !important; }
        .btn-outline-primary:hover { background: var(--al-primary-lt) !important; color: var(--al-primary) !important; }
        .btn-success  { background: #16a34a !important; border-color: #16a34a !important; color: #fff !important; }
        .btn-success:hover  { background: #15803d !important; border-color: #15803d !important; }
        .btn-danger   { background: #dc2626 !important; border-color: #dc2626 !important; color: #fff !important; }
        .btn-danger:hover   { background: #b91c1c !important; border-color: #b91c1c !important; }
        .btn-warning  { background: #d97706 !important; border-color: #d97706 !important; color: #fff !important; }
        .btn-warning:hover  { background: #b45309 !important; border-color: #b45309 !important; }
        .btn-secondary{ background: #6b7280 !important; border-color: #6b7280 !important; color: #fff !important; }
        .btn-light    { background: #f9fafb !important; border-color: var(--al-border) !important; color: var(--al-text-2) !important; }
        .btn-light:hover    { background: #f3f4f6 !important; }
        .btn-info     { background: #2563eb !important; border-color: #2563eb !important; color: #fff !important; }
        .btn-outline-danger  { color: #dc2626 !important; border-color: #dc2626 !important; }
        .btn-outline-danger:hover  { background: #fef2f2 !important; }
        .btn-outline-success { color: #16a34a !important; border-color: #16a34a !important; }
        .btn-outline-success:hover { background: #f0fdf4 !important; }
        .btn-outline-secondary { color: #6b7280 !important; border-color: #6b7280 !important; background: transparent !important; }
        .btn-outline-secondary:hover { background: #f3f4f6 !important; color: var(--al-text-2) !important; }
        .waves-effect { overflow: hidden; }

        /* Forms */
        .form-control, .form-select {
            font-family: 'Inter', sans-serif !important;
            border-radius: 8px !important;
            border: 1px solid #d1d5db !important;
            font-size: 13.5px !important;
            padding: 8px 12px !important;
            color: var(--al-text-2) !important;
            background: var(--al-surface) !important;
            transition: border-color .15s, box-shadow .15s !important;
            line-height: 1.4 !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--al-primary) !important;
            box-shadow: 0 0 0 3px rgba(61,128,18,.12) !important;
            outline: none !important;
        }
        .form-control::placeholder { color: var(--al-text-4) !important; }
        .form-label {
            font-family: 'Inter', sans-serif !important;
            font-size: 13px !important; font-weight: 500 !important;
            color: var(--al-text-2) !important; margin-bottom: 6px !important;
        }
        .form-text { font-size: 12px !important; color: var(--al-text-4) !important; }
        .form-check-input:checked { background-color: var(--al-primary) !important; border-color: var(--al-primary) !important; }

        /* Apple-style toggle — visual only; each layout keeps its own spacing */
        .form-switch .form-check-input {
            width: 2.4em !important; height: 1.35em !important;
            background-color: #e5e7eb; border: none !important;
            border-radius: 999px !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e") !important;
            background-position: left center; background-repeat: no-repeat;
            box-shadow: none !important; cursor: pointer;
            transition: background-color .2s ease, background-position .2s ease;
        }
        .form-switch .form-check-input:focus {
            border: none !important;
            box-shadow: 0 0 0 3px rgba(61,128,18,.15) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e") !important;
        }
        .form-switch .form-check-input:checked {
            background-color: var(--al-primary) !important;
            background-position: right center;
        }
        .form-check-label { font-size: 13.5px !important; color: var(--al-text-2) !important; }
        .input-group-text {
            border-radius: 8px !important; border-color: #d1d5db !important;
            background: #f9fafb !important; font-size: 13.5px !important; color: var(--al-text-3) !important;
        }
        .input-group > .form-control:not(:first-child),
        .input-group > .form-select:not(:first-child) { border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important; }
        .input-group > .form-control:not(:last-child),
        .input-group > .form-select:not(:last-child) { border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important; }
        textarea.form-control { min-height: 90px; resize: vertical; }
        .invalid-feedback { font-size: 12px !important; font-weight: 500 !important; }
        .is-invalid.form-control, .is-invalid.form-select { border-color: #dc2626 !important; }
        .is-invalid.form-control:focus, .is-invalid.form-select:focus { box-shadow: 0 0 0 3px rgba(220,38,38,.12) !important; }

        /* Badges */
        .badge {
            font-family: 'Inter', sans-serif !important;
            border-radius: 6px !important;
            font-size: 11px !important;
            font-weight: 500 !important;
            padding: 3px 8px !important;
            letter-spacing: .1px;
        }
        .bg-light-primary, .badge-light-primary { background: var(--al-primary-lt) !important; color: var(--al-primary) !important; }
        .bg-light-success  { background: #f0fdf4 !important; color: #16a34a !important; }
        .bg-light-danger   { background: #fef2f2 !important; color: #dc2626 !important; }
        .bg-light-warning  { background: #fffbeb !important; color: #d97706 !important; }
        .bg-light-info     { background: #eff6ff !important; color: #2563eb !important; }
        .bg-light-secondary{ background: #f3f4f6 !important; color: #6b7280 !important; }
        .text-primary { color: var(--al-primary) !important; }
        .bg-primary   { background: var(--al-primary) !important; }
        .text-success { color: #16a34a !important; }
        .text-danger  { color: #dc2626 !important; }
        .text-warning { color: #d97706 !important; }
        .text-info    { color: #2563eb !important; }
        .text-muted   { color: var(--al-text-4) !important; }

        /* Alerts */
        .alert {
            border-radius: var(--al-radius) !important;
            font-size: 13.5px !important;
            padding: 12px 16px !important;
            border: none !important;
            border-left: 4px solid !important;
            font-family: 'Inter', sans-serif !important;
        }
        .alert-success  { background: #f0fdf4 !important; border-left-color: #22c55e !important; color: #15803d !important; }
        .alert-danger   { background: #fef2f2 !important; border-left-color: #ef4444 !important; color: #b91c1c !important; }
        .alert-warning  { background: #fffbeb !important; border-left-color: #f59e0b !important; color: #92400e !important; }
        .alert-info     { background: #eff6ff !important; border-left-color: #60a5fa !important; color: #1e40af !important; }
        .alert .alert-body { color: inherit !important; }

        /* Pagination */
        .pagination { gap: 3px; }
        .page-link {
            border-radius: 7px !important;
            font-size: 13px !important;
            color: var(--al-text-2) !important;
            border-color: var(--al-border) !important;
            padding: 6px 11px !important;
            font-family: 'Inter', sans-serif !important;
        }
        .page-item.active .page-link { background: var(--al-primary) !important; border-color: var(--al-primary) !important; color: #fff !important; }
        .page-link:hover { background: #f3f4f6 !important; color: var(--al-text) !important; }

        /* Avatar */
        .avatar, .avatar .avatar-content { border-radius: 10px !important; }

        /* Dropdown menus */
        .dropdown-menu {
            border-radius: 10px !important; border: 1px solid var(--al-border) !important;
            box-shadow: 0 8px 24px rgba(0,0,0,.09) !important;
            padding: 4px !important; font-size: 13.5px !important;
            font-family: 'Inter', sans-serif !important;
        }
        .dropdown-item {
            border-radius: 7px !important; padding: 7px 12px !important;
            color: var(--al-text-2) !important; font-size: 13.5px !important;
            font-family: 'Inter', sans-serif !important;
        }
        .dropdown-item:hover { background: #f3f4f6 !important; color: var(--al-text) !important; }
        .dropdown-item.active, .dropdown-item:active { background: var(--al-primary-lt) !important; color: var(--al-primary) !important; }
        .dropdown-divider { border-color: var(--al-border-2) !important; }

        /* Nav tabs */
        .nav-tabs { border-bottom: 1px solid var(--al-border) !important; }
        .nav-tabs .nav-link {
            border: none !important; border-bottom: 2px solid transparent !important;
            border-radius: 0 !important; font-size: 13.5px !important; font-weight: 500 !important;
            color: var(--al-text-3) !important; padding: 10px 16px !important;
        }
        .nav-tabs .nav-link:hover { color: var(--al-text) !important; }
        .nav-tabs .nav-link.active { color: var(--al-primary) !important; border-bottom-color: var(--al-primary) !important; }
        .nav-pills .nav-link { border-radius: 8px !important; font-size: 13.5px !important; font-weight: 500 !important; }
        .nav-pills .nav-link.active { background: var(--al-primary) !important; }

        /* Progress */
        .progress { border-radius: 99px !important; height: 8px !important; background: #f3f4f6 !important; }
        .progress-bar { background: var(--al-primary) !important; border-radius: 99px !important; }

        /* Modal */
        .modal-content { border-radius: 14px !important; border: none !important; box-shadow: 0 20px 60px rgba(0,0,0,.18) !important; }
        .modal-header { border-bottom: 1px solid var(--al-border-2) !important; padding: 16px 20px !important; }
        .modal-title { font-size: 15px !important; font-weight: 600 !important; font-family: 'Inter', sans-serif !important; }
        .modal-body  { padding: 20px !important; }
        .modal-footer{ border-top: 1px solid var(--al-border-2) !important; padding: 12px 20px !important; gap: 8px; }

        /* Toast */
        .toast { border-radius: 10px !important; }

        /* Links */
        a { color: var(--al-primary); }
        a:hover { color: var(--al-primary-dk); }

        /* ════════════════════════════════════════
           RESPONSIVE
        ════════════════════════════════════════ */
        @media (max-width: 1024px) {
            .al-hnav { display: none; }
            .al-hamburger { display: flex; }
            .al-topbar { padding: 0 16px; }
            .al-topbar-right { margin-left: auto; }
            .al-content { padding: 16px; }
        }
    </style>

    @stack('styles')
</head>

<body>

@php
    // Catalog — everything that makes up a product listing
    $catOpen  = request()->routeIs('admin.products.*','admin.categories.*','admin.subcategories.*','admin.brands.index','admin.brands.create','admin.brands.edit','admin.tags.*','admin.colors.*','admin.sizes.*','admin.banners.*');
    // Sales — orders and the money that flows from them
    $salesOpen = request()->routeIs('admin.orders.*','admin.returns.*','admin.cancellations.*','admin.paystack-transactions.*');
    // Customers — people and their interactions
    $custOpen = request()->routeIs('admin.users.*','admin.brands.assign','admin.reviews.*','admin.tickets.*');
    // Delivery — how goods reach the customer
    $delOpen  = request()->routeIs('admin.states.*','admin.locations.*','admin.pickup-points.*','admin.store-locations.*','admin.shipping.*','admin.weight.*');
    // Marketing — promotions
    $mktOpen  = request()->routeIs('admin.coupons.*','admin.discount.*');
    // Content — public-facing pages
    $contentOpen = request()->routeIs('admin.about.*','admin.contact.*','admin.faqs.*');
    // Settings — store configuration & system
    $setOpen  = request()->routeIs('admin.markup.*','admin.currencies.*','admin.audit.*','admin.settings.*','admin.invoice.settings*');
@endphp

<div class="al-shell">

    {{-- ══════════════════════════════════════════
         TOPBAR
    ══════════════════════════════════════════ --}}
    <header class="al-topbar">

        {{-- Mobile hamburger --}}
        <button class="al-hamburger" id="alHamburger" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
        </button>

        {{-- Logo --}}
        <a href="{{ route('admin.dashboard') }}" class="al-logo">
            @if(!empty($appStoreLogo))
                <img src="{{ asset('storage/' . $appStoreLogo) }}" alt="{{ $appStoreName ?? 'AlbertinaNG' }}">
            @else
                <img src="{{ asset('image.png') }}" alt="{{ $appStoreName ?? 'AlbertinaNG' }}">
            @endif
        </a>

        {{-- Horizontal nav (desktop) --}}
        <nav class="al-hnav">

            {{-- Catalog --}}
            <div class="al-hnav-group">
                <button class="al-hnav-item {{ $catOpen ? 'active' : '' }}">
                    <i class="fas fa-box-open" style="font-size:12px;"></i> Catalog
                    <i class="fas fa-chevron-down al-hnav-chev"></i>
                </button>
                <div class="al-hnav-dd">
                    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'al-active' : '' }}"><i class="fas fa-box"></i> Products</a>
                    <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*','admin.subcategories.*') ? 'al-active' : '' }}"><i class="fas fa-folder-open"></i> Categories</a>
                    <a href="{{ route('admin.brands.index') }}" class="{{ request()->routeIs('admin.brands.index','admin.brands.create','admin.brands.edit') ? 'al-active' : '' }}"><i class="fas fa-copyright"></i> Brands</a>
                    <a href="{{ route('admin.tags.index') }}" class="{{ request()->routeIs('admin.tags.*') ? 'al-active' : '' }}"><i class="fas fa-hashtag"></i> Tags</a>
                    <a href="{{ route('admin.colors.index') }}" class="{{ request()->routeIs('admin.colors.*') ? 'al-active' : '' }}"><i class="fas fa-palette"></i> Colors</a>
                    <a href="{{ route('admin.sizes.index') }}" class="{{ request()->routeIs('admin.sizes.*') ? 'al-active' : '' }}"><i class="fas fa-ruler-combined"></i> Sizes</a>
                    <a href="{{ route('admin.banners.index') }}" class="{{ request()->routeIs('admin.banners.*') ? 'al-active' : '' }}"><i class="fas fa-images"></i> Banners</a>
                </div>
            </div>

            {{-- Sales --}}
            <div class="al-hnav-group">
                <button class="al-hnav-item {{ $salesOpen ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag" style="font-size:12px;"></i> Sales
                    <i class="fas fa-chevron-down al-hnav-chev"></i>
                </button>
                <div class="al-hnav-dd">
                    <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.index') ? 'al-active' : '' }}"><i class="fas fa-list-ul"></i> All Orders</a>
                    <a href="{{ route('admin.returns.index') }}" class="{{ request()->routeIs('admin.returns.*') ? 'al-active' : '' }}"><i class="fas fa-rotate-left"></i> Returns</a>
                    <a href="{{ route('admin.cancellations.index') }}" class="{{ request()->routeIs('admin.cancellations.*') ? 'al-active' : '' }}"><i class="fas fa-ban"></i> Cancellations</a>
                    <a href="{{ route('admin.paystack-transactions.index') }}" class="{{ request()->routeIs('admin.paystack-transactions.*') ? 'al-active' : '' }}"><i class="fas fa-credit-card"></i> Paystack Txns</a>
                </div>
            </div>

            {{-- Customers --}}
            <div class="al-hnav-group">
                <button class="al-hnav-item {{ $custOpen ? 'active' : '' }}">
                    <i class="fas fa-users" style="font-size:12px;"></i> Customers
                    <i class="fas fa-chevron-down al-hnav-chev"></i>
                </button>
                <div class="al-hnav-dd">
                    <a href="/admin/users" class="{{ request()->is('admin/users') ? 'al-active' : '' }}"><i class="fas fa-users"></i> All Users</a>
                    <a href="{{ route('admin.brands.assign') }}" class="{{ request()->routeIs('admin.brands.assign') ? 'al-active' : '' }}"><i class="fas fa-handshake"></i> Assign Brands</a>
                    <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'al-active' : '' }}"><i class="fas fa-star"></i> Reviews</a>
                    <a href="{{ route('admin.tickets.index') }}" class="{{ request()->routeIs('admin.tickets.*') ? 'al-active' : '' }}"><i class="fas fa-headset"></i> Support Tickets</a>
                </div>
            </div>

            {{-- Delivery --}}
            <div class="al-hnav-group">
                <button class="al-hnav-item {{ $delOpen ? 'active' : '' }}">
                    <i class="fas fa-truck" style="font-size:12px;"></i> Delivery
                    <i class="fas fa-chevron-down al-hnav-chev"></i>
                </button>
                <div class="al-hnav-dd">
                    <a href="{{ route('admin.states.index') }}" class="{{ request()->routeIs('admin.states.*') ? 'al-active' : '' }}"><i class="fas fa-map"></i> States</a>
                    <a href="{{ route('admin.locations.index') }}" class="{{ request()->routeIs('admin.locations.*') ? 'al-active' : '' }}"><i class="fas fa-location-dot"></i> Delivery Locations</a>
                    <a href="{{ route('admin.pickup-points.index') }}" class="{{ request()->routeIs('admin.pickup-points.*') ? 'al-active' : '' }}"><i class="fas fa-store"></i> Pickup Points</a>
                    <a href="{{ route('admin.store-locations.index') }}" class="{{ request()->routeIs('admin.store-locations.*') ? 'al-active' : '' }}"><i class="fas fa-map-marker-alt"></i> Store Locations</a>
                    <a href="{{ route('admin.shipping.index') }}" class="{{ request()->routeIs('admin.shipping.*') ? 'al-active' : '' }}"><i class="fas fa-truck"></i> Shipping</a>
                    <a href="{{ route('admin.weight.index') }}" class="{{ request()->routeIs('admin.weight.*') ? 'al-active' : '' }}"><i class="fas fa-weight-hanging"></i> Weight &amp; Delivery</a>
                </div>
            </div>

            {{-- Marketing --}}
            <div class="al-hnav-group">
                <button class="al-hnav-item {{ $mktOpen ? 'active' : '' }}">
                    <i class="fas fa-bullhorn" style="font-size:12px;"></i> Marketing
                    <i class="fas fa-chevron-down al-hnav-chev"></i>
                </button>
                <div class="al-hnav-dd">
                    <a href="{{ route('admin.coupons.index') }}" class="{{ request()->routeIs('admin.coupons.*') ? 'al-active' : '' }}"><i class="fas fa-ticket"></i> Coupons</a>
                    <a href="{{ route('admin.discount.index') }}" class="{{ request()->routeIs('admin.discount.*') ? 'al-active' : '' }}"><i class="fas fa-scissors"></i> Discounts</a>
                </div>
            </div>

            {{-- Settings --}}
            <div class="al-hnav-group">
                <button class="al-hnav-item {{ $setOpen ? 'active' : '' }}">
                    <i class="fas fa-gear" style="font-size:12px;"></i> Settings
                    <i class="fas fa-chevron-down al-hnav-chev"></i>
                </button>
                <div class="al-hnav-dd">
                    <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'al-active' : '' }}"><i class="fas fa-sliders"></i> Store Settings</a>
                    <a href="{{ route('admin.markup.index') }}" class="{{ request()->routeIs('admin.markup.*') ? 'al-active' : '' }}"><i class="fas fa-percent"></i> Markup</a>
                    <a href="{{ route('admin.currencies.index') }}" class="{{ request()->routeIs('admin.currencies.*') ? 'al-active' : '' }}"><i class="fas fa-coins"></i> Currencies</a>
                    <a href="{{ route('admin.audit.index') }}" class="{{ request()->routeIs('admin.audit.*') ? 'al-active' : '' }}"><i class="fas fa-clock-rotate-left"></i> Audit Trail</a>
                    <a href="{{ route('admin.invoice.settings') }}" class="{{ request()->routeIs('admin.invoice.settings*') ? 'al-active' : '' }}"><i class="fas fa-file-invoice"></i> Invoice Settings</a>
                </div>
            </div>

            {{-- Content --}}
            <div class="al-hnav-group">
                <button class="al-hnav-item {{ $contentOpen ? 'active' : '' }}">
                    <i class="fas fa-file-alt" style="font-size:12px;"></i> Content
                    <i class="fas fa-chevron-down al-hnav-chev"></i>
                </button>
                <div class="al-hnav-dd">
                    <a href="{{ route('admin.about.index') }}" class="{{ request()->routeIs('admin.about.*') ? 'al-active' : '' }}"><i class="fas fa-circle-info"></i> About Us</a>
                    <a href="{{ route('admin.contact.index') }}" class="{{ request()->routeIs('admin.contact.*') ? 'al-active' : '' }}"><i class="fas fa-address-book"></i> Contact Us</a>
                    <a href="{{ route('admin.faqs.index') }}" class="{{ request()->routeIs('admin.faqs.*') ? 'al-active' : '' }}"><i class="fas fa-circle-question"></i> FAQs</a>
                </div>
            </div>

        </nav>

        {{-- Topbar right --}}
        <div class="al-topbar-right">
            <div class="al-user">
                <button class="al-user-btn" id="alUserBtn" aria-expanded="false">
                    <div class="al-avatar">
                        @if(isset(auth()->user()->avatar) && auth()->user()->avatar)
                            <img src="{{ asset('storage/'.auth()->user()->avatar) }}" alt="Avatar">
                        @else
                            {{ strtoupper(substr(auth()->user()->email ?? 'A', 0, 1)) }}
                        @endif
                    </div>
                    <span class="al-user-label d-none d-md-inline">
                        {{ auth()->user()->name ?? auth()->user()->email ?? 'Admin' }}
                    </span>
                    <i class="fas fa-chevron-down al-user-chev"></i>
                </button>
                <div class="al-user-dd" id="alUserDD">
                    <div class="al-user-dd-info">
                        <div style="font-size:13px;font-weight:600;color:var(--al-text-2);margin-bottom:2px;">
                            {{ auth()->user()->name ?? 'Admin' }}
                        </div>
                        <div class="al-user-dd-email">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                    <a href="/admin/users"><i class="fas fa-users"></i> Manage Users</a>
                    <a href="{{ route('admin.settings.index') }}"><i class="fas fa-cog"></i> Settings</a>
                    <div class="al-dd-divider"></div>
                    <a href="{{ url('/') }}" target="_blank"><i class="fas fa-arrow-up-right-from-square"></i> View Store</a>
                    <div class="al-dd-divider"></div>
                    <a href="{{ url('/logout') }}" class="al-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>

    </header>

    {{-- ══════════════════════════════════════════
         MOBILE DRAWER
    ══════════════════════════════════════════ --}}
    <div class="al-drawer" id="alDrawer">

        <a href="{{ route('admin.dashboard') }}" class="al-drawer-logo">
            @if(!empty($appStoreLogo))
                <img src="{{ asset('storage/' . $appStoreLogo) }}" alt="{{ $appStoreName ?? 'AlbertinaNG' }}">
            @else
                <img src="{{ asset('image.png') }}" alt="{{ $appStoreName ?? 'AlbertinaNG' }}">
            @endif
        </a>

        <nav class="al-dnav">

            <button class="al-dnav-item al-dgroup-toggle {{ $catOpen ? 'open' : '' }}" data-target="dNavCatalog">
                <i class="fas fa-box-open ni"></i> Catalog
                <i class="fas fa-chevron-right al-dnav-chev"></i>
            </button>
            <div class="al-dsub {{ $catOpen ? 'open' : '' }}" id="dNavCatalog">
                <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'al-active' : '' }}"><i class="fas fa-box"></i> Products</a>
                <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*','admin.subcategories.*') ? 'al-active' : '' }}"><i class="fas fa-folder-open"></i> Categories</a>
                <a href="{{ route('admin.brands.index') }}" class="{{ request()->routeIs('admin.brands.index','admin.brands.create','admin.brands.edit') ? 'al-active' : '' }}"><i class="fas fa-copyright"></i> Brands</a>
                <a href="{{ route('admin.tags.index') }}" class="{{ request()->routeIs('admin.tags.*') ? 'al-active' : '' }}"><i class="fas fa-hashtag"></i> Tags</a>
                <a href="{{ route('admin.colors.index') }}" class="{{ request()->routeIs('admin.colors.*') ? 'al-active' : '' }}"><i class="fas fa-palette"></i> Colors</a>
                <a href="{{ route('admin.sizes.index') }}" class="{{ request()->routeIs('admin.sizes.*') ? 'al-active' : '' }}"><i class="fas fa-ruler-combined"></i> Sizes</a>
                <a href="{{ route('admin.banners.index') }}" class="{{ request()->routeIs('admin.banners.*') ? 'al-active' : '' }}"><i class="fas fa-images"></i> Banners</a>
            </div>

            <button class="al-dnav-item al-dgroup-toggle {{ $salesOpen ? 'open' : '' }}" data-target="dNavSales">
                <i class="fas fa-shopping-bag ni"></i> Sales
                <i class="fas fa-chevron-right al-dnav-chev"></i>
            </button>
            <div class="al-dsub {{ $salesOpen ? 'open' : '' }}" id="dNavSales">
                <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.index') ? 'al-active' : '' }}"><i class="fas fa-list-ul"></i> All Orders</a>
                <a href="{{ route('admin.returns.index') }}" class="{{ request()->routeIs('admin.returns.*') ? 'al-active' : '' }}"><i class="fas fa-rotate-left"></i> Returns</a>
                <a href="{{ route('admin.cancellations.index') }}" class="{{ request()->routeIs('admin.cancellations.*') ? 'al-active' : '' }}"><i class="fas fa-ban"></i> Cancellations</a>
                <a href="{{ route('admin.paystack-transactions.index') }}" class="{{ request()->routeIs('admin.paystack-transactions.*') ? 'al-active' : '' }}"><i class="fas fa-credit-card"></i> Paystack Txns</a>
            </div>

            <button class="al-dnav-item al-dgroup-toggle {{ $custOpen ? 'open' : '' }}" data-target="dNavCustomers">
                <i class="fas fa-users ni"></i> Customers
                <i class="fas fa-chevron-right al-dnav-chev"></i>
            </button>
            <div class="al-dsub {{ $custOpen ? 'open' : '' }}" id="dNavCustomers">
                <a href="/admin/users" class="{{ request()->is('admin/users') ? 'al-active' : '' }}"><i class="fas fa-users"></i> All Users</a>
                <a href="{{ route('admin.brands.assign') }}" class="{{ request()->routeIs('admin.brands.assign') ? 'al-active' : '' }}"><i class="fas fa-handshake"></i> Assign Brands</a>
                <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'al-active' : '' }}"><i class="fas fa-star"></i> Reviews</a>
                <a href="{{ route('admin.tickets.index') }}" class="{{ request()->routeIs('admin.tickets.*') ? 'al-active' : '' }}"><i class="fas fa-headset"></i> Support Tickets</a>
            </div>

            <button class="al-dnav-item al-dgroup-toggle {{ $delOpen ? 'open' : '' }}" data-target="dNavDelivery">
                <i class="fas fa-truck ni"></i> Delivery
                <i class="fas fa-chevron-right al-dnav-chev"></i>
            </button>
            <div class="al-dsub {{ $delOpen ? 'open' : '' }}" id="dNavDelivery">
                <a href="{{ route('admin.states.index') }}" class="{{ request()->routeIs('admin.states.*') ? 'al-active' : '' }}"><i class="fas fa-map"></i> States</a>
                <a href="{{ route('admin.locations.index') }}" class="{{ request()->routeIs('admin.locations.*') ? 'al-active' : '' }}"><i class="fas fa-location-dot"></i> Delivery Locations</a>
                <a href="{{ route('admin.pickup-points.index') }}" class="{{ request()->routeIs('admin.pickup-points.*') ? 'al-active' : '' }}"><i class="fas fa-store"></i> Pickup Points</a>
                <a href="{{ route('admin.store-locations.index') }}" class="{{ request()->routeIs('admin.store-locations.*') ? 'al-active' : '' }}"><i class="fas fa-map-marker-alt"></i> Store Locations</a>
                <a href="{{ route('admin.shipping.index') }}" class="{{ request()->routeIs('admin.shipping.*') ? 'al-active' : '' }}"><i class="fas fa-truck"></i> Shipping</a>
                <a href="{{ route('admin.weight.index') }}" class="{{ request()->routeIs('admin.weight.*') ? 'al-active' : '' }}"><i class="fas fa-weight-hanging"></i> Weight &amp; Delivery</a>
            </div>

            <button class="al-dnav-item al-dgroup-toggle {{ $mktOpen ? 'open' : '' }}" data-target="dNavMarketing">
                <i class="fas fa-bullhorn ni"></i> Marketing
                <i class="fas fa-chevron-right al-dnav-chev"></i>
            </button>
            <div class="al-dsub {{ $mktOpen ? 'open' : '' }}" id="dNavMarketing">
                <a href="{{ route('admin.coupons.index') }}" class="{{ request()->routeIs('admin.coupons.*') ? 'al-active' : '' }}"><i class="fas fa-ticket"></i> Coupons</a>
                <a href="{{ route('admin.discount.index') }}" class="{{ request()->routeIs('admin.discount.*') ? 'al-active' : '' }}"><i class="fas fa-scissors"></i> Discounts</a>
            </div>

            <button class="al-dnav-item al-dgroup-toggle {{ $setOpen ? 'open' : '' }}" data-target="dNavSettings">
                <i class="fas fa-gear ni"></i> Settings
                <i class="fas fa-chevron-right al-dnav-chev"></i>
            </button>
            <div class="al-dsub {{ $setOpen ? 'open' : '' }}" id="dNavSettings">
                <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'al-active' : '' }}"><i class="fas fa-sliders"></i> Store Settings</a>
                <a href="{{ route('admin.markup.index') }}" class="{{ request()->routeIs('admin.markup.*') ? 'al-active' : '' }}"><i class="fas fa-percent"></i> Markup</a>
                <a href="{{ route('admin.currencies.index') }}" class="{{ request()->routeIs('admin.currencies.*') ? 'al-active' : '' }}"><i class="fas fa-coins"></i> Currencies</a>
                <a href="{{ route('admin.audit.index') }}" class="{{ request()->routeIs('admin.audit.*') ? 'al-active' : '' }}"><i class="fas fa-clock-rotate-left"></i> Audit Trail</a>
                <a href="{{ route('admin.invoice.settings') }}" class="{{ request()->routeIs('admin.invoice.settings*') ? 'al-active' : '' }}"><i class="fas fa-file-invoice"></i> Invoice Settings</a>
            </div>

            <button class="al-dnav-item al-dgroup-toggle {{ $contentOpen ? 'open' : '' }}" data-target="dNavContent">
                <i class="fas fa-file-alt ni"></i> Content
                <i class="fas fa-chevron-right al-dnav-chev"></i>
            </button>
            <div class="al-dsub {{ $contentOpen ? 'open' : '' }}" id="dNavContent">
                <a href="{{ route('admin.about.index') }}" class="{{ request()->routeIs('admin.about.*') ? 'al-active' : '' }}"><i class="fas fa-circle-info"></i> About Us</a>
                <a href="{{ route('admin.contact.index') }}" class="{{ request()->routeIs('admin.contact.*') ? 'al-active' : '' }}"><i class="fas fa-address-book"></i> Contact Us</a>
                <a href="{{ route('admin.faqs.index') }}" class="{{ request()->routeIs('admin.faqs.*') ? 'al-active' : '' }}"><i class="fas fa-circle-question"></i> FAQs</a>
            </div>

        </nav>

        <div class="al-drawer-foot">
            <a href="{{ url('/') }}" target="_blank">
                <i class="fas fa-arrow-up-right-from-square"></i> View Store
            </a>
            <a href="{{ url('/logout') }}" class="al-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>

    </div>

    {{-- Mobile overlay --}}
    <div class="al-overlay" id="alOverlay"></div>

    {{-- ══════════════════════════════════════════
         PAGE CONTENT
    ══════════════════════════════════════════ --}}
    <main class="al-content">
        @yield('content')
    </main>

</div>{{-- /.al-shell --}}

{{-- Vendor JS --}}
<script src="{{ asset('/app-asset/vendors/js/vendors.min.js') }}"></script>
<script src="{{ asset('/app-asset/vendors/js/charts/apexcharts.min.js') }}"></script>
<script src="{{ asset('/app-asset/vendors/js/extensions/toastr.min.js') }}"></script>

<script>
if (typeof feather === 'undefined') { window.feather = { replace: function(){} }; }
</script>
<script src="{{ asset('/app-asset/js/core/app.js') }}"></script>
<script>
(function restoreScroll() {
    document.documentElement.style.setProperty('overflow-y', 'auto', 'important');
    document.body.style.setProperty('overflow-y', 'auto', 'important');
    document.documentElement.style.removeProperty('overflow');
    document.body.style.removeProperty('overflow');
    var blockers = document.querySelectorAll('.content-overlay,.sidenav-overlay,.drag-target');
    blockers.forEach(function(el) {
        el.style.setProperty('display', 'none', 'important');
        el.style.setProperty('pointer-events', 'none', 'important');
    });
})();
</script>

<script>
(function () {
    const overlay   = document.getElementById('alOverlay');
    const hamburger = document.getElementById('alHamburger');
    const drawer    = document.getElementById('alDrawer');
    const userBtn   = document.getElementById('alUserBtn');
    const userDD    = document.getElementById('alUserDD');

    // Mobile drawer
    if (hamburger) hamburger.addEventListener('click', function () {
        drawer.classList.toggle('open');
        overlay.classList.toggle('open');
    });
    if (overlay) overlay.addEventListener('click', function () {
        drawer.classList.remove('open');
        overlay.classList.remove('open');
    });

    // Horizontal nav dropdowns — click to open, click outside to close
    document.querySelectorAll('.al-hnav-group').forEach(function (grp) {
        const btn = grp.querySelector('.al-hnav-item');
        const dd  = grp.querySelector('.al-hnav-dd');
        if (!btn || !dd) return;
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var wasOpen = grp.classList.contains('open');
            document.querySelectorAll('.al-hnav-group').forEach(function (g) {
                g.classList.remove('open');
                var d = g.querySelector('.al-hnav-dd');
                if (d) d.classList.remove('open');
            });
            if (!wasOpen) {
                grp.classList.add('open');
                dd.classList.add('open');
            }
        });
    });
    document.addEventListener('click', function () {
        document.querySelectorAll('.al-hnav-group').forEach(function (g) {
            g.classList.remove('open');
            var d = g.querySelector('.al-hnav-dd');
            if (d) d.classList.remove('open');
        });
    });
    document.querySelectorAll('.al-hnav-dd').forEach(function (dd) {
        dd.addEventListener('click', function (e) { e.stopPropagation(); });
    });

    // Mobile drawer accordion
    document.querySelectorAll('.al-dgroup-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.getAttribute('data-target');
            var sub = document.getElementById(targetId);
            if (!sub) return;
            var wasOpen = sub.classList.contains('open');
            document.querySelectorAll('.al-dsub').forEach(function (s) { s.classList.remove('open'); });
            document.querySelectorAll('.al-dgroup-toggle').forEach(function (b) { b.classList.remove('open'); });
            if (!wasOpen) { sub.classList.add('open'); btn.classList.add('open'); }
        });
    });

    // User dropdown
    if (userBtn) {
        userBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var isOpen = userDD.classList.toggle('open');
            userBtn.setAttribute('aria-expanded', isOpen);
        });
        document.addEventListener('click', function () {
            userDD.classList.remove('open');
            userBtn.setAttribute('aria-expanded', 'false');
        });
        userDD.addEventListener('click', function (e) { e.stopPropagation(); });
    }

    // Feather
    $(window).on('load', function () {
        if (typeof feather !== 'undefined' && feather.replace) feather.replace({ width: 14, height: 14 });
    });
})();
</script>

@livewireScripts
@stack('scripts')
@include('partials.notify')
</body>
</html>
