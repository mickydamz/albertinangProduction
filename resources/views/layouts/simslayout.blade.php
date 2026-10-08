<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($appStoreName ?? 'AlbertinaNG') . ' – Premium Electronics')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="icon" type="image/x-icon"       href="{{ asset('favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16"  href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32"  href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180"     href="{{ asset('favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon/android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('favicon/android-chrome-512x512.png') }}">
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">

    <style>
        /* =====================================================
           AlbertinaNG — DESIGN SYSTEM v2
           Green-first · Plus Jakarta Sans + DM Sans
        ===================================================== */
        :root {
            /* Brand greens */
            --g50:  #f0fce8;
            --g100: #d5f5b8;
            --g200: #abeb73;
            --g400: #78c83a;
            --g500: #5aab1f;
            --g600: #3d8012;
            --g700: #2a5a0a;
            --g900: #162e05;

            /* Ink / surface */
            --ink:     #111310;
            --ink2:    #3a3f37;
            --ink3:    #6b7268;
            --surface: #ffffff;
            --surf2:   #f7f9f5;
            --surf3:   #eef3e8;
            --border:  #dde8d4;
            --border2: #c4d8b0;

            /* Radius */
            --r:   10px;
            --r-lg: 16px;
            --r-xl: 24px;

            /* Shadows */
            --sh-sm: 0 1px 3px rgba(0,0,0,.06);
            --sh-md: 0 4px 16px rgba(0,0,0,.09);
            --sh-lg: 0 12px 40px rgba(0,0,0,.14);

            /* Type */
            --fh: 'Plus Jakarta Sans', sans-serif;
            --fb: 'DM Sans', sans-serif;

            /* Layout */
            --max: 1200px;
            --gutter: 20px;

            /* Aliases — keep page-level stylesheets in sync */
            --font-head: var(--fh);
            --font-body: var(--fb);
            --radius:    var(--r);
            --radius-xs: 4px;
            --radius-sm: 6px;
            --radius-lg: var(--r-lg);
            --radius-xl: var(--r-xl);
            --shadow-xs: 0 1px 2px rgba(0,0,0,.05);
            --shadow-sm: var(--sh-sm);
            --shadow:    0 2px 10px rgba(0,0,0,.07);
            --shadow-md: var(--sh-md);
            --shadow-lg: var(--sh-lg);
            --g800: #1b400a;
            --ink4: #9ca3af;
            --surf: var(--surface);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }
        body {
            font-family: var(--fb);
            font-size: 15px;
            line-height: 1.55;
            color: var(--ink);
            background: #f5f5f5;
            -webkit-font-smoothing: antialiased;
        }
        a { text-decoration: none; color: inherit; }
        button { cursor: pointer; font-family: var(--fb); border: none; background: none; }
        img { display: block; max-width: 100%; height: auto; }
        ul { list-style: none; }
        input, select, textarea { font-family: var(--fb); }

        /* ── Skip link ── */
        .skip-link {
            position: absolute; top: -100px; left: 16px; z-index: 9999;
            background: var(--g500); color: #fff; padding: 8px 16px;
            border-radius: var(--r); font-size: 13px; font-weight: 600;
            transition: top .2s;
        }
        .skip-link:focus { top: 16px; }

        /* =====================================================
           TOP STRIP
        ===================================================== */
        .top-strip {
            background: var(--g700);
            color: var(--g100);
            font-size: 12px;
            padding: 6px 0;
        }
        .top-strip__inner {
            max-width: var(--max); margin: 0 auto; padding: 0 var(--gutter);
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 6px;
        }
        .top-strip__left { display: flex; align-items: center; gap: 16px; }
        .top-strip__left span { display: flex; align-items: center; gap: 5px; }
        .top-strip__left i { color: var(--g200); font-size: 11px; }
        .top-strip__links { display: flex; align-items: center; gap: 4px; }
        .top-strip__links a { color: var(--g200); margin-left: 14px; transition: color .2s; font-size: 12px; }
        .top-strip__links a:hover { color: #fff; }

        /* =====================================================
           ANNOUNCE BAR
        ===================================================== */
        .announce-bar {
            background: var(--g600);
            color: #fff;
            text-align: center;
            font-size: 13px;
            font-weight: 500;
            padding: 8px 20px;
            letter-spacing: .2px;
        }
        .announce-bar i { margin-right: 5px; color: rgba(255,255,255,.6); }
        .announce-bar strong { font-weight: 700; }

        /* =====================================================
           HEADER
        ===================================================== */
        .header {
            background: var(--surface);
            position: sticky; top: 0; z-index: 900;
            border-bottom: 1.5px solid var(--border);
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
            transition: box-shadow .2s;
        }
        .header.scrolled { box-shadow: 0 4px 24px rgba(0,0,0,.12); }
        .header__inner {
            max-width: var(--max); margin: 0 auto; padding: 0 var(--gutter);
            height: 72px; display: flex; align-items: center; gap: 14px;
        }

        /* Hamburger */
        .mobile-menu-btn {
            display: none; font-size: 20px; color: var(--ink2);
            padding: 6px; transition: color .2s; flex-shrink: 0;
        }
        .mobile-menu-btn:hover { color: var(--g600); }

        /* Logo */
        .logo-link {
            display: flex; align-items: center; flex-shrink: 0;
            transition: opacity .2s;
        }
        .logo-link:hover { opacity: .85; }
        .logo-link img { height: 36px; width: auto; object-fit: contain; display: block; }

        /* ── Categories button ── */
        .cat-dropdown-wrap { position: relative; flex-shrink: 0; }
        .cat-btn {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 16px;
            background: var(--g500); border: none;
            border-radius: var(--r); font-size: 13.5px; font-weight: 700;
            color: #fff; transition: all .2s; white-space: nowrap;
            font-family: var(--fh); letter-spacing: -.1px;
        }
        .cat-btn:hover { background: var(--g700); }
        .cat-btn .cat-grid-icon { font-size: 14px; }
        .cat-btn .cat-chev { font-size: 9px; opacity: .8; transition: transform .28s cubic-bezier(.25,0,.25,1); }
        .cat-btn[aria-expanded="true"] { background: var(--g700); }
        .cat-btn[aria-expanded="true"] .cat-chev { transform: rotate(180deg); }

        /* ── Cat dropdown — anchored to the button, grows with content ── */
        .cat-dropdown {
            position: absolute;
            top: calc(100% + 2px); left: 0;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 0 12px 12px 12px;
            box-shadow: 0 12px 40px rgba(0,0,0,.12), 0 2px 8px rgba(0,0,0,.06);
            z-index: 9000;
            display: flex; flex-direction: row;
            opacity: 0; visibility: hidden; pointer-events: none;
            transform: translateY(-6px);
            transition: opacity .18s ease,
                        transform .18s ease,
                        visibility 0s linear .18s;
        }
        .cat-dropdown.open {
            opacity: 1; visibility: visible; pointer-events: auto;
            transform: translateY(0);
            transition: opacity .18s ease,
                        transform .18s ease,
                        visibility 0s linear 0s;
        }

        /* Sub-container: collapses when empty, expands smoothly on hover */
        #catSubContainer {
            width: 0; overflow: hidden; flex-shrink: 0;
            display: flex; transition: width .22s cubic-bezier(.4,0,.2,1);
        }
        #catSubContainer.has-panel { width: 260px; }
        #catSubContainer.has-panel.wide { width: 500px; }
        #catSubContainer .cat-sub-panel { width: 260px; flex-shrink: 0; }
        #catSubContainer.wide .cat-sub-panel { width: 500px; }

        /* Left list */
        .cat-list {
            width: 220px; flex-shrink: 0;
            padding: 8px 0; overflow-y: auto;
            max-height: calc(100vh - 120px);
            border-right: 1px solid var(--border);
        }
        .cat-list::-webkit-scrollbar { width: 4px; }
        .cat-list::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 4px; }

        .cat-dropdown__item {
            padding: 11px 16px;
            min-height: unset;
            display: flex; align-items: center; gap: 11px;
            color: var(--ink2); font-size: 13.5px; font-weight: 500;
            transition: all .18s;
            border-left: 3px solid transparent;
            text-decoration: none; cursor: pointer;
            position: relative;
        }
        .cat-dropdown__item:hover,
        .cat-dropdown__item.active {
            background: var(--g50);
            color: var(--g700);
            border-left-color: var(--g500);
        }
        .cat-icon {
            width: 24px; height: 24px; border-radius: 6px;
            background: var(--surf2);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; transition: background .18s;
        }
        .cat-icon i { font-size: 11px; color: var(--g600); transition: color .18s; }
        .cat-dropdown__item:hover .cat-icon,
        .cat-dropdown__item.active .cat-icon { background: var(--g500); }
        .cat-dropdown__item:hover .cat-icon i,
        .cat-dropdown__item.active .cat-icon i { color: #fff; }
        .cat-dropdown__item .item-label { flex: 1; font-weight: 600; line-height: 1.25; }
        .cat-dropdown__item .item-sub {
            font-size: 11px; color: var(--ink4); font-weight: 400;
            display: block; margin-top: 1px;
        }
        .cat-dropdown__item .sub-arrow {
            font-size: 9px; color: var(--border2); flex-shrink: 0;
            transition: color .18s, transform .18s;
        }
        .cat-dropdown__item:hover .sub-arrow,
        .cat-dropdown__item.active .sub-arrow {
            color: var(--g500); transform: translateX(2px);
        }

        /* Middle sub-panel */
        .cat-sub-panel {
            width: 260px; flex-shrink: 0;
            background: #fff;
            display: none; flex-direction: column;
            overflow: hidden;
            border-right: 1px solid var(--border);
        }
        .cat-sub-panel.visible { display: flex; }

        .cat-sub-panel__title {
            font-size: 10.5px; font-weight: 800;
            letter-spacing: 1.2px; text-transform: uppercase;
            color: var(--ink3); padding: 10px 16px 8px;
            border-bottom: 1px solid var(--border); flex-shrink: 0;
            display: flex; align-items: center; gap: 7px; background: #fff;
        }
        .cat-sub-panel__title::before {
            content: ''; width: 3px; height: 14px; border-radius: 2px;
            background: var(--g500); flex-shrink: 0;
        }

        /* 2-column subcategory grid (the main change for Argos feel) */
        .sub-links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            align-content: start; gap: 1px;
            flex: 1; overflow-y: auto;
            padding: 6px 8px 4px;
        }
        .sub-links-grid::-webkit-scrollbar { width: 4px; }
        .sub-links-grid::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 4px; }
        .sub-links-grid a {
            display: flex; align-items: center; gap: 9px;
            padding: 8px 11px; font-size: 13px; font-weight: 500; color: var(--ink2);
            text-decoration: none; border-radius: 6px;
            transition: background .14s, color .14s; min-height: 36px;
        }
        .sub-links-grid a:hover { background: var(--g50); color: var(--g700); }

        .cat-sub-panel__viewall {
            display: flex; align-items: center; justify-content: center; gap: 7px;
            margin: 4px 12px 10px; padding: 10px 14px; font-size: 13px; font-weight: 700;
            color: #fff; background: var(--g500); border-radius: 8px;
            transition: background .18s; text-decoration: none; flex-shrink: 0;
            font-family: var(--fh);
        }
        .cat-sub-panel__viewall:hover { background: var(--g700); }

        /* ── Brands panel (3rd column — hidden until a category is hovered) ── */
        .cat-brands-panel {
            width: 0; overflow: hidden; flex-shrink: 0;
            border-left: 1px solid var(--border);
            background: #fff;
            display: flex; flex-direction: column;
            align-self: flex-start;
            transition: width .22s cubic-bezier(.4,0,.2,1);
        }
        /* Show brands only when a sub-panel is active */
        #catSubContainer.has-panel ~ .cat-brands-panel { width: 185px; }
        /* Wide brands: 2-column grid when many brands */
        #catSubContainer.has-panel ~ .cat-brands-panel.wide { width: 280px; }
        .cat-brands-panel.wide .cbp-brand-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
        .cbp-heading {
            font-size: 10px; font-weight: 800; letter-spacing: 1.2px;
            text-transform: uppercase; color: var(--ink3);
            padding: 13px 16px 10px; border-bottom: 1px solid var(--border);
            flex-shrink: 0; white-space: nowrap;
        }
        .cbp-brand-grid {
            display: flex; flex-direction: column;
            padding: 5px 0; max-height: 360px; overflow-y: auto;
        }
        .cbp-brand-grid::-webkit-scrollbar { width: 3px; }
        .cbp-brand-grid::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 3px; }
        .cbp-brand-link {
            display: block; padding: 6px 16px;
            font-size: 13px; font-weight: 500; color: var(--ink2);
            text-decoration: none; white-space: nowrap;
            overflow: hidden; text-overflow: ellipsis;
            border-left: 2px solid transparent;
            transition: color .13s, background .13s, border-color .13s;
        }
        .cbp-brand-link:hover {
            color: var(--g600); background: var(--g50);
            border-left-color: var(--g500);
        }
        .cbp-view-brands {
            display: flex; align-items: center; gap: 5px;
            padding: 10px 16px; font-size: 12px; font-weight: 600;
            color: var(--g600); text-decoration: none;
            border-top: 1px solid var(--border); flex-shrink: 0;
            transition: color .13s, background .13s;
        }
        .cbp-view-brands:hover { color: var(--g700); background: var(--g50); }
        .cbp-view-brands i { font-size: 9px; }

        /* ── Search ── */
        .search-wrap { flex: 1; max-width: 500px; position: relative; min-width: 0; }
        .search-form {
            display: flex; background: var(--surface);
            border: 1.5px solid var(--border); border-radius: var(--r);
            overflow: hidden; transition: border-color .2s, box-shadow .2s;
        }
        .search-form:focus-within {
            border-color: var(--g400);
            box-shadow: 0 0 0 3px rgba(90,171,31,.12);
        }
        .search-input {
            flex: 1; padding: 10px 14px; border: none; outline: none;
            font-size: 14px; background: transparent; color: var(--ink); min-width: 0;
        }
        .search-input::placeholder { color: var(--ink3); }
        .search-btn {
            background: var(--g500); color: #fff; padding: 0 18px;
            font-size: 13px; font-weight: 600; transition: background .2s;
            white-space: nowrap; flex-shrink: 0; display: flex; align-items: center; gap: 5px;
        }
        .search-btn:hover { background: var(--g600); }

        /* =====================================================
           SEARCH SUGGESTIONS
        ===================================================== */
        .search-suggestions {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,.08);
            z-index: 850;
            display: none;
            max-height: 400px;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .search-suggestions::-webkit-scrollbar { width: 4px; }
        .search-suggestions::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 4px; }
        .search-suggestions.open {
            display: block;
            animation: dropIn .2s cubic-bezier(.25,0,.25,1);
        }
        .search-suggestions__section-label {
            font-size: 10px; font-weight: 800; letter-spacing: 1.2px;
            text-transform: uppercase; color: var(--ink4);
            padding: 10px 16px 6px;
        }
        .search-suggestions__item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 16px;
            min-height: 48px;
            cursor: pointer;
            transition: background .15s;
            border-bottom: 1px solid var(--border);
        }
        .search-suggestions__item:last-of-type {
            border-bottom: none;
        }
        .search-suggestions__item:hover,
        .search-suggestions__item.active {
            background: var(--g50);
        }
        .sug-icon-box {
            width: 36px; height: 36px; border-radius: 9px;
            background: var(--surf3); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; transition: all .15s;
        }
        .sug-icon-box i { font-size: 13px; color: var(--ink3); transition: color .15s; }
        .search-suggestions__item:hover .sug-icon-box { background: var(--g50); border-color: var(--g200); }
        .search-suggestions__item:hover .sug-icon-box i { color: var(--g600); }
        .sug-img-box {
            width: 36px; height: 36px; border-radius: 9px;
            border: 1px solid var(--border); background: #f8f9fa;
            flex-shrink: 0; overflow: hidden;
        }
        .sug-img-box img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .search-suggestions__item .sug-text { flex: 1; min-width: 0; }
        .search-suggestions__item .sug-name {
            font-size: 13.5px;
            color: var(--ink);
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }
        .search-suggestions__item .sug-sub {
            font-size: 11.5px; color: var(--ink3);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            display: block; margin-top: 1px;
        }
        .search-suggestions__item .sug-type {
            font-size: 10px;
            color: var(--g700);
            background: var(--g50);
            border: 1px solid var(--border2);
            padding: 2px 8px;
            border-radius: 20px;
            flex-shrink: 0;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: .3px;
        }
        .search-suggestions__view-all {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 12px 16px;
            font-size: 13.5px;
            font-weight: 700;
            color: #fff;
            background: var(--g500);
            cursor: pointer;
            transition: background .15s;
            font-family: var(--fh);
        }
        .search-suggestions__view-all:hover {
            background: var(--g700);
        }
        .search-suggestions__loading {
            padding: 18px 16px;
            text-align: center;
            color: var(--ink3);
            font-size: 13px;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .search-suggestions__empty {
            padding: 20px 16px;
            text-align: center;
            color: var(--ink3);
            font-size: 13px;
        }

        /* ── Header actions ── */
        .header-actions { display: flex; align-items: center; gap: 5px; margin-left: auto; flex-shrink: 0; }

        .mobile-search-btn { display: none; font-size: 18px; color: var(--ink2); padding: 8px; transition: color .2s; }
        .mobile-search-btn:hover { color: var(--g600); }

        /* Icon button base */
        .icon-btn {
            display: flex; align-items: center; justify-content: center;
            width: 40px; height: 40px; border-radius: var(--r);
            border: 1.5px solid var(--border); background: var(--surface);
            color: var(--ink2); font-size: 17px; cursor: pointer;
            transition: all .2s; position: relative; flex-shrink: 0;
        }
        .icon-btn:hover { border-color: var(--g400); color: var(--g600); background: var(--g50); }

        /* ── Currency dropdown (header / desktop) ── */
        .currency-dropdown { position: relative; }
        .currency-btn-label {
            position: absolute; bottom: -3px; right: -3px;
            font-size: 8px; font-weight: 800; font-family: var(--fh);
            background: var(--g500); color: #fff;
            padding: 1px 4px; border-radius: 4px; line-height: 1;
            border: 1.5px solid var(--surface);
        }
        .currency-menu {
            position: absolute; top: calc(100% + 10px); right: 0;
            width: 205px; background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,.09);
            overflow: hidden;
            display: none; z-index: 850;
            animation: dropIn .2s cubic-bezier(.25,0,.25,1);
        }
        .currency-menu.open { display: block; }
        .currency-menu__title {
            font-size: 10.5px; font-weight: 800;
            letter-spacing: 1.2px; text-transform: uppercase;
            color: var(--ink3); padding: 13px 16px 10px;
            border-bottom: 1.5px solid var(--border);
            display: flex; align-items: center; gap: 7px;
        }
        .currency-menu__title::before {
            content: ''; width: 3px; height: 13px; border-radius: 2px;
            background: var(--g500);
        }
        .currency-menu__list { max-height: 320px; overflow-y: auto; padding: 4px 0; }
        .currency-menu__list::-webkit-scrollbar { width: 4px; }
        .currency-menu__list::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 4px; }
        .currency-menu button {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 16px; font-size: 13.5px; color: var(--ink2); font-weight: 500;
            transition: all .18s; border-bottom: 1px solid var(--border);
            width: 100%; text-align: left; background: none;
            font-family: var(--fb); cursor: pointer; min-height: 44px;
        }
        .currency-menu button:last-child { border-bottom: none; }
        .currency-menu button:hover { background: var(--g50); color: var(--g700); }
        .currency-menu button .cur-sym {
            width: 28px; height: 28px; border-radius: 8px;
            background: var(--surf3); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-weight: 800;
            font-family: var(--fh); color: var(--g700); font-size: 12px;
            transition: all .18s;
        }
        .currency-menu button:hover .cur-sym { background: var(--g500); border-color: var(--g500); color: #fff; }
        .currency-menu button .cur-name { flex: 1; }
        .currency-menu button.active { background: var(--g50); color: var(--g700); font-weight: 600; }
        .currency-menu button.active .cur-sym { background: var(--g500); border-color: var(--g500); color: #fff; }
        .currency-menu button.active::after {
            content: '\f00c'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
            font-size: 11px; color: var(--g500);
        }

        /* Cart */
        .cart-btn {
            display: flex; align-items: center; gap: 8px; padding: 9px 16px;
            border: none; border-radius: var(--r);
            font-size: 13.5px; color: #fff; transition: all .2s;
            white-space: nowrap; position: relative;
            background: var(--g600);
            font-weight: 700; font-family: var(--fh);
            letter-spacing: -.1px;
        }
        .cart-btn:hover { background: var(--g700); }
        .cart-btn i { font-size: 15px; }
        .cart-badge {
            background: #fff; color: var(--g700); border-radius: 50%;
            width: 20px; height: 20px; font-size: 10px; font-weight: 800;
            font-family: var(--fh);
            display: flex; align-items: center; justify-content: center;
            position: absolute; top: -7px; right: -7px; transition: transform .2s;
            border: 2px solid var(--g600);
        }
        .cart-badge.bump { animation: badgeBump .3s ease; }
        @keyframes badgeBump { 0%,100% { transform: scale(1); } 50% { transform: scale(1.4); } }

        /* Mini cart */
        .cart-wrap { position: relative; }
        .cart-wrap::after {
            content: '';
            position: absolute;
            top: 100%; right: 0;
            width: 100%; height: 14px;
            display: none;
        }
        .cart-wrap:hover::after { display: block; }
        .mini-cart {
            position: absolute; top: 100%; right: 0;
            margin-top: 10px;
            width: 340px; background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,.09);
            z-index: 800; overflow: hidden;
            display: none;
            animation: dropIn .2s cubic-bezier(.25,0,.25,1);
        }
        .cart-wrap:hover .mini-cart,
        .mini-cart:hover { display: block; }

        .mini-cart__head {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 18px 14px;
            border-bottom: 1.5px solid var(--border);
            background: var(--surf2);
        }
        .mini-cart__head h4 {
            font-family: var(--fh); font-size: 14.5px; font-weight: 800;
            color: var(--ink); letter-spacing: -.2px;
        }
        .mini-cart__head a {
            font-size: 12px; color: var(--g600); font-weight: 600;
            display: flex; align-items: center; gap: 4px;
            transition: color .15s;
        }
        .mini-cart__head a:hover { color: var(--g700); }

        .mini-cart__items { max-height: 280px; overflow-y: auto; padding: 6px 0; }
        .mini-cart__items::-webkit-scrollbar { width: 4px; }
        .mini-cart__items::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 4px; }

        .mini-cart__item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 18px; border-bottom: 1px solid var(--border);
            transition: background .15s;
        }
        .mini-cart__item:last-child { border-bottom: none; }
        .mini-cart__item:hover { background: var(--surf2); }
        .mini-cart__item img {
            width: 54px; height: 54px; object-fit: cover;
            border-radius: 10px; border: 1.5px solid var(--border);
            flex-shrink: 0; background: #f8f9fa;
        }
        .mini-cart__item-info { flex: 1; min-width: 0; }
        .mini-cart__item-name {
            font-size: 13px; font-weight: 600; line-height: 1.35;
            color: var(--ink); display: -webkit-box;
            -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .mini-cart__item-price {
            font-family: var(--fh); font-size: 13.5px; color: var(--ink);
            font-weight: 800; margin-top: 3px; letter-spacing: -.2px;
        }
        .mini-cart__item-qty { font-size: 11px; color: var(--ink4); margin-top: 1px; }

        .mini-cart__foot {
            padding: 14px 18px 16px;
            border-top: 1.5px solid var(--border);
            background: var(--surf2);
        }
        .mini-cart__foot-total {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 12px;
        }
        .mini-cart__foot-total span:first-child {
            font-size: 13px; font-weight: 600; color: var(--ink3);
        }
        .mini-cart__foot-total span:last-child {
            font-family: var(--fh); font-size: 18px; font-weight: 800;
            color: var(--ink); letter-spacing: -.4px;
        }
        .mini-cart__checkout {
            background: var(--g500); color: #fff; width: 100%;
            padding: 13px; border-radius: 10px;
            font-family: var(--fh); font-size: 14px;
            font-weight: 800; transition: all .2s;
            display: flex; align-items: center; justify-content: center; gap: 7px;
            letter-spacing: -.1px;
        }
        .mini-cart__checkout:hover { background: var(--g700); transform: translateY(-1px); }
        .empty-cart {
            text-align: center; color: var(--ink3); padding: 28px 18px 24px;
            font-size: 13px;
        }
        .empty-cart i { font-size: 28px; color: var(--border2); margin-bottom: 10px; display: block; }
        .empty-cart p { font-size: 13px; margin-bottom: 12px; }
        .empty-cart a {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 20px; background: var(--g500); color: #fff;
            border-radius: 8px; font-size: 13px; font-weight: 700;
            font-family: var(--fh); transition: background .2s;
        }
        .empty-cart a:hover { background: var(--g700); }

        /* Mobile search bar */
        .mobile-search-bar {
            display: none; padding: 10px var(--gutter);
            background: var(--surface); border-top: 1px solid var(--border);
            position: relative;
        }
        .header.search-open .mobile-search-bar { display: block; }

        /* ── User dropdown ── */
        .user-dropdown { position: relative; }
        .user-btn {
            display: flex; align-items: center; justify-content: center;
            width: 40px; height: 40px;
            border: 1.5px solid var(--border); border-radius: var(--r);
            background: var(--surface); color: var(--ink2);
            font-size: 17px; cursor: pointer; transition: all .2s; position: relative;
        }
        .user-btn:hover { border-color: var(--g400); color: var(--g600); background: var(--g50); }
        .user-btn--avatar {
            background: var(--g500); color: #fff;
            border: 2px solid var(--g400); border-radius: var(--r);
            font-family: var(--fh); font-size: 13px; font-weight: 800;
        }
        .user-btn--avatar:hover { background: var(--g700) !important; border-color: var(--g600) !important; color: #fff !important; }
        .user-menu {
            position: absolute; top: calc(100% + 10px); right: 0;
            width: 230px; background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 18px;
            box-shadow: 0 20px 60px rgba(0,0,0,.15), 0 4px 16px rgba(0,0,0,.07);
            overflow: hidden;
            z-index: 850;
            display: none;
            animation: dropIn .2s cubic-bezier(.25,0,.25,1);
        }
        .user-menu.open { display: block; }

        .user-menu__info {
            padding: 15px 16px 13px;
            border-bottom: 1.5px solid var(--border);
            background: var(--surf2);
            display: flex; align-items: center; gap: 11px;
        }
        .user-menu__avatar {
            width: 38px; height: 38px; border-radius: 10px;
            background: var(--g500); color: #fff;
            font-family: var(--fh); font-size: 14px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .user-menu__info-text { min-width: 0; flex: 1; }
        .user-menu__name {
            font-family: var(--fh); font-size: 13.5px; font-weight: 700;
            color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .user-menu__email {
            font-size: 11px; color: var(--ink3); margin-top: 1px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .user-menu__section { padding: 5px 0; border-bottom: 1px solid var(--border); }
        .user-menu__section:last-child { border-bottom: none; padding-bottom: 6px; }

        .user-menu a, .user-menu button {
            display: flex; align-items: center; gap: 11px;
            padding: 11px 16px; font-size: 13.5px; font-weight: 500; color: var(--ink2);
            transition: all .18s;
            width: 100%; text-align: left; background: none;
            border: none; font-family: var(--fb); cursor: pointer;
            min-height: 44px;
        }
        .user-menu a:hover, .user-menu button:hover {
            background: var(--g50); color: var(--g700); padding-left: 20px;
        }
        .user-menu__icon-box {
            width: 30px; height: 30px; border-radius: 8px;
            background: var(--surf3); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; transition: all .18s;
        }
        .user-menu__icon-box i { font-size: 12px; color: var(--ink3); transition: color .18s; }
        .user-menu a:hover .user-menu__icon-box,
        .user-menu button:hover .user-menu__icon-box {
            background: var(--g500); border-color: var(--g500);
        }
        .user-menu a:hover .user-menu__icon-box i,
        .user-menu button:hover .user-menu__icon-box i { color: #fff; }

        .user-menu__logout { color: #c0392b !important; }
        .user-menu__logout .user-menu__icon-box { background: #fff5f5 !important; border-color: #fecaca !important; }
        .user-menu__logout .user-menu__icon-box i { color: #c0392b !important; }
        .user-menu__logout:hover { background: #fff5f5 !important; color: #a93226 !important; padding-left: 20px !important; }
        .user-menu__logout:hover .user-menu__icon-box { background: #c0392b !important; border-color: #c0392b !important; }
        .user-menu__logout:hover .user-menu__icon-box i { color: #fff !important; }

        /* ── Info dropdown ── */
        .info-dropdown { position: relative; }
        .info-menu {
            position: absolute; top: calc(100% + 10px); right: 0;
            width: 215px; background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 18px;
            box-shadow: 0 20px 60px rgba(0,0,0,.15), 0 4px 16px rgba(0,0,0,.07);
            overflow: hidden;
            padding: 5px 0; display: none; z-index: 500;
            animation: dropIn .2s cubic-bezier(.25,0,.25,1);
        }
        .info-menu.open { display: block; }
        .info-menu a {
            display: flex; align-items: center; gap: 11px;
            padding: 11px 16px; font-size: 13.5px; font-weight: 500; color: var(--ink2);
            transition: all .18s; border-bottom: 1px solid var(--border);
            min-height: 44px;
        }
        .info-menu a:last-child { border-bottom: none; }
        .info-menu__icon-box {
            width: 30px; height: 30px; border-radius: 8px;
            background: var(--surf3); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; transition: all .18s;
        }
        .info-menu__icon-box i { font-size: 12px; color: var(--ink3); transition: color .18s; }
        .info-menu a:hover {
            background: var(--g50); color: var(--g700); padding-left: 20px;
        }
        .info-menu a:hover .info-menu__icon-box { background: var(--g500); border-color: var(--g500); }
        .info-menu a:hover .info-menu__icon-box i { color: #fff; }

        /* =====================================================
           CONTACT BAR
        ===================================================== */
        .contact-bar {
            /* background: var(--surface);  */
            border-bottom: 1px solid var(--border);
            padding: 8px 0; font-size: 12.5px; color: var(--ink3);
        }
        .contact-bar__inner {
            max-width: var(--max); margin: 0 auto; padding: 0 var(--gutter);
            display: flex; gap: 24px; align-items: center; flex-wrap: wrap;
        }
        .contact-bar i { margin-right: 5px; color: var(--g500); font-size: 12px; }
        .contact-bar a { color: var(--g600); font-weight: 500; }
        .contact-bar a:hover { text-decoration: underline; }

        /* =====================================================
           MOBILE MENU
        ===================================================== */
        .mobile-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.55);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            z-index: 1050; display: none; opacity: 0; transition: opacity .3s;
        }
        .mobile-overlay.active { display: block; opacity: 1; }

        .mobile-menu {
            position: fixed; top: 0; left: 0;
            width: 92%; max-width: 340px;
            height: 100dvh; background: var(--surface);
            box-shadow: 0 0 60px rgba(0,0,0,.28);
            z-index: 1100;
            display: flex; flex-direction: column;
            transform: translateX(-100%);
            transition: transform .32s cubic-bezier(.32,0,.67,0);
        }
        .mobile-menu.active {
            transform: translateX(0);
            transition: transform .34s cubic-bezier(.34,1.04,.64,1);
        }

        /* ── Single-row header ── */
        .mobile-menu__head {
            height: 58px; display: flex; align-items: center;
            padding: 0 14px;
            background: var(--g600);
            flex-shrink: 0;
        }
        .mobile-menu__title {
            flex: 1; display: flex; align-items: center; gap: 8px;
            font-family: var(--fh); font-size: 15px; font-weight: 800;
            color: #fff; letter-spacing: -.2px;
        }
        .mobile-menu__title i { font-size: 14px; color: rgba(255,255,255,.65); }
        .mobile-menu__close {
            width: 36px; height: 36px;
            background: rgba(255,255,255,.13); border: 1px solid rgba(255,255,255,.22);
            border-radius: 9px; color: #fff; font-size: 13px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all .2s; flex-shrink: 0;
        }
        .mobile-menu__close:hover { background: rgba(255,255,255,.28); transform: rotate(90deg); }

        /* ── Auth strip ── */
        .mobile-auth-strip {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px;
            background: var(--surf2);
            border-bottom: 1.5px solid var(--border);
            flex-shrink: 0;
        }
        .mobile-auth-strip__avatar {
            width: 32px; height: 32px; border-radius: 8px;
            background: var(--g50); border: 1.5px solid var(--g200);
            display: flex; align-items: center; justify-content: center;
            font-family: var(--fh); font-size: 12px; font-weight: 800; color: var(--g700);
            flex-shrink: 0;
        }
        .mobile-auth-strip__avatar i { font-size: 13px; color: var(--g600); }
        .mobile-auth-strip__text { flex: 1; min-width: 0; }
        .mobile-auth-strip__name {
            font-size: 13px; font-weight: 700; color: var(--ink);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2;
        }
        .mobile-auth-strip__sub { font-size: 11px; color: var(--ink3); font-weight: 400; margin-top: 1px; }
        .mobile-auth-strip__link {
            font-size: 12px; font-weight: 700; color: var(--g700);
            padding: 6px 11px; border-radius: 7px;
            background: var(--g50); border: 1.5px solid var(--g200);
            text-decoration: none; transition: all .18s; white-space: nowrap;
        }
        .mobile-auth-strip__link:hover { background: var(--g500); border-color: var(--g500); color: #fff; }

        /* ── Currency pill (inside header) ── */
        .mobile-cur-pill { position: relative; margin-right: 10px; }
        .mobile-cur-pill__btn {
            display: flex; align-items: center; gap: 5px;
            padding: 6px 10px; border-radius: 20px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.25);
            color: #fff; font-size: 11.5px; font-weight: 700;
            font-family: var(--fh); cursor: pointer; transition: background .18s;
        }
        .mobile-cur-pill__btn:hover { background: rgba(255,255,255,.26); }
        .mobile-cur-pill__btn .fa-globe { font-size: 10px; color: rgba(255,255,255,.65); }
        .mobile-cur-pill__chev { font-size: 7px; opacity: .7; transition: transform .25s; }
        .mobile-cur-pill__btn[aria-expanded="true"] .mobile-cur-pill__chev { transform: rotate(180deg); }
        .mobile-cur-pill__menu {
            position: absolute; top: calc(100% + 6px); right: 0;
            min-width: 162px; background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 16px 40px rgba(0,0,0,.2);
            overflow: hidden;
            display: none; z-index: 10;
            animation: dropIn .2s cubic-bezier(.25,0,.25,1);
        }
        .mobile-cur-pill__menu.open { display: block; }
        .mobile-cur-pill__menu button {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 14px; font-size: 13px; color: var(--ink2); font-weight: 500;
            border-bottom: 1px solid var(--border); width: 100%;
            text-align: left; background: none; font-family: var(--fb);
            cursor: pointer; transition: all .18s; min-height: 42px;
        }
        .mobile-cur-pill__menu button:last-child { border-bottom: none; }
        .mobile-cur-pill__menu button:hover { background: var(--g50); color: var(--g700); }
        .mobile-cur-pill__menu button .cur-sym {
            width: 26px; height: 26px; border-radius: 7px;
            background: var(--surf3); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-weight: 800; font-family: var(--fh);
            color: var(--g700); font-size: 11px; transition: all .18s;
        }
        .mobile-cur-pill__menu button:hover .cur-sym,
        .mobile-cur-pill__menu button.active .cur-sym { background: var(--g500); border-color: var(--g500); color: #fff; }
        .mobile-cur-pill__menu button.active { background: var(--g50); color: var(--g700); font-weight: 600; }
        .mobile-cur-pill__menu button.active::after {
            content: '\f00c'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
            font-size: 10px; color: var(--g500); margin-left: auto;
        }

        /* ── Scrollable body ── */
        .mobile-menu__body { flex: 1; overflow-y: auto; }
        .mobile-menu__body::-webkit-scrollbar { width: 3px; }
        .mobile-menu__body::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 3px; }

        /* Mobile sub-accordion */
        .mobile-cat-subs {
            max-height: 0; overflow: hidden;
            transition: max-height .28s cubic-bezier(.4,0,.2,1);
            background: var(--surf2);
        }
        .mobile-cat-subs.open { max-height: 3000px; }
        .mobile-cat-subs__viewall {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 20px 12px 18px;
            font-size: 13.5px; font-weight: 700; color: var(--g700);
            background: var(--g50); border-bottom: 1px solid var(--border);
            text-decoration: none; min-height: 46px; transition: background .15s;
        }
        .mobile-cat-subs__viewall:hover { background: var(--g100); }
        .mobile-cat-subs__viewall i.arr { font-size: 9px; margin-left: auto; color: var(--g500); }
        .mobile-cat-subs .mob-sub-link { padding-left: 28px; }

        /* Mobile brands accordion row (reuses subcategory link styles) */
        .mob-brands-grid { display: flex; flex-direction: column; }
        .mob-brand-item {
            display: flex; align-items: center;
            padding: 10px 16px 10px 28px;
            font-size: 13px; font-weight: 500; color: var(--ink2);
            text-decoration: none; border-bottom: 1px solid var(--surf3);
            transition: background .15s, color .15s;
        }
        .mob-brand-item:last-child { border-bottom: none; }
        .mob-brand-item:hover { background: var(--g50); color: var(--g700); }

        .mob-sub-link {
            display: flex; align-items: center; gap: 11px;
            padding: 12px 18px 12px 22px; font-size: 13.5px; font-weight: 500; color: var(--ink2);
            border-bottom: 1px solid var(--border); text-decoration: none;
            transition: background .15s, color .15s; min-height: 46px;
        }
        .mob-sub-link:hover { background: var(--g50); color: var(--g700); }

        /* ── Category row ── */
        .mobile-cat-row {
            display: flex; align-items: stretch;
            border-bottom: 1px solid var(--border);
        }
        .mobile-cat-link {
            flex: 1; display: flex; align-items: center; gap: 10px;
            padding: 0 10px 0 14px; color: var(--ink);
            font-weight: 600; font-size: 13.5px; text-decoration: none;
            transition: background .18s, color .18s;
            border-left: 3px solid transparent; min-height: 48px;
        }
        .mobile-cat-row.has-open .mobile-cat-link {
            background: var(--g50); color: var(--g700); border-left-color: var(--g500);
        }
        .mobile-cat__icon {
            width: 22px; height: 22px; border-radius: 6px;
            background: var(--surf2);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            transition: background .18s;
        }
        .mobile-cat__icon i { font-size: 10px; color: var(--g600); transition: color .18s; }
        .mobile-cat-row.has-open .mobile-cat__icon { background: var(--g500); }
        .mobile-cat-row.has-open .mobile-cat__icon i { color: #fff; }
        .mobile-cat-toggle {
            width: 46px; display: flex; align-items: center; justify-content: center;
            color: var(--ink3); background: none; border: none; cursor: pointer;
            border-left: 1px solid var(--border); flex-shrink: 0;
            font-size: 13px; transition: background .18s, color .18s;
        }
        .mobile-cat-toggle i { display: block; }
        .mobile-cat-row.has-open .mobile-cat-toggle { background: var(--g50); color: var(--g600); }

        /* accordion .mobile-subs removed — push navigation used instead */

        /* Footer */
        .mobile-menu__footer {
            border-top: 1.5px solid var(--border);
            background: var(--surf2);
        }
        .mobile-menu__footer-label {
            font-size: 9.5px; font-weight: 800; letter-spacing: 1.4px;
            text-transform: uppercase; color: var(--ink4); padding: 12px 18px 4px;
        }
        .mobile-menu__footer a {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 18px; font-size: 13.5px; color: var(--ink2); font-weight: 500;
            border-bottom: 1px solid var(--border); text-decoration: none;
            transition: background .15s, color .15s; min-height: 46px;
        }
        .mobile-menu__footer a:last-child { border-bottom: none; }
        .mob-foot-icon {
            width: 30px; height: 30px; border-radius: 8px;
            background: var(--surface); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; transition: all .18s;
        }
        .mob-foot-icon i { font-size: 12px; color: var(--ink3); transition: color .18s; }
        .mobile-menu__footer a:hover { background: var(--g50); color: var(--g700); }
        .mobile-menu__footer a:hover .mob-foot-icon { background: var(--g500); border-color: var(--g500); }
        .mobile-menu__footer a:hover .mob-foot-icon i { color: #fff; }

        /* =====================================================
           GLOBAL SELECT STYLE
        ===================================================== */
        select {
            -webkit-appearance: none; appearance: none;
            padding: 10px 38px 10px 14px;
            border: 1.5px solid var(--border); border-radius: 10px;
            font-size: 14px; font-weight: 500; font-family: var(--fb);
            color: var(--ink); background-color: var(--surface);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='none' stroke='%236b7280' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 13px center;
            cursor: pointer; transition: border-color .18s, box-shadow .18s;
            min-height: 44px; line-height: 1.2;
        }
        select:hover { border-color: var(--border2); }
        select:focus { outline: none; border-color: var(--g500); box-shadow: 0 0 0 3px rgba(90,171,31,.12); }
        select:disabled { background-color: var(--surf2); color: var(--ink4); cursor: not-allowed; opacity: .7; }

        /* =====================================================
           CUSTOM SELECT COMPONENT (.cs-*)
        ===================================================== */
        .cs-wrap { position: relative; display: block; }
        .cs-trigger {
            display: flex; align-items: center; justify-content: space-between; gap: 8px;
            width: 100%; min-height: 44px; padding: 10px 13px 10px 14px;
            border: 1.5px solid var(--border); border-radius: 10px;
            background: var(--surface); color: var(--ink);
            font-size: 14px; font-weight: 500; font-family: var(--fb);
            cursor: pointer; transition: border-color .18s, box-shadow .18s;
            text-align: left; line-height: 1.2;
        }
        .cs-trigger:hover { border-color: var(--border2); }
        .cs-wrap.cs-open .cs-trigger { border-color: var(--g500); box-shadow: 0 0 0 3px rgba(90,171,31,.12); }
        .cs-trigger:focus-visible { outline: none; border-color: var(--g500); box-shadow: 0 0 0 3px rgba(90,171,31,.12); }
        .cs-trigger[disabled],
        .cs-disabled .cs-trigger { background: var(--surf2); color: var(--ink4); cursor: not-allowed; opacity: .7; }
        .cs-value { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .cs-value--placeholder { color: var(--ink3); }
        .cs-chevron { flex-shrink: 0; display: flex; align-items: center; color: var(--ink3); transition: transform .2s; }
        .cs-wrap.cs-open .cs-chevron { transform: rotate(180deg); }
        .cs-dropdown {
            position: absolute; top: 100%; left: 0; right: 0; z-index: 9999;
            background: var(--surface); border: 1.5px solid var(--border); border-radius: 10px;
            box-shadow: 0 8px 28px rgba(0,0,0,.12); overflow-y: auto; max-height: 226px;
            display: none; animation: dropIn .14s ease;
        }
        .cs-wrap.cs-open .cs-dropdown { display: block; }
        .cs-option {
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            padding: 10px 14px; font-size: 13.5px; font-family: var(--fb);
            color: var(--ink); cursor: pointer; transition: background .1s, color .1s;
            border-bottom: 1px solid var(--border); line-height: 1.3;
        }
        .cs-option:last-child { border-bottom: none; }
        .cs-option:hover,
        .cs-option--focused { background: var(--g50); color: var(--g700); }
        .cs-option--selected { background: var(--g50); color: var(--g600); font-weight: 600; }
        .cs-option--selected::after { content: '✓'; flex-shrink: 0; color: var(--g500); font-size: 12px; }
        .cs-option--disabled { color: var(--ink4); cursor: not-allowed; }
        .cs-option--disabled:hover { background: none; color: var(--ink4); }

        /* =====================================================
           CUSTOM DATE PICKER COMPONENT (.cdp-*)
        ===================================================== */
        .cdp-wrap { position: relative; display: block; }
        .cdp-trigger {
            display: flex; align-items: center; gap: 10px;
            width: 100%; min-height: 44px; padding: 10px 13px 10px 14px;
            border: 1.5px solid var(--border); border-radius: 10px;
            background: var(--surface); color: var(--ink);
            font-size: 14px; font-weight: 500; font-family: var(--fb);
            cursor: pointer; transition: border-color .18s, box-shadow .18s;
            text-align: left; line-height: 1.2;
        }
        .cdp-trigger:hover { border-color: var(--border2); }
        .cdp-wrap.cdp-open .cdp-trigger { border-color: var(--g500); box-shadow: 0 0 0 3px rgba(90,171,31,.12); }
        .cdp-trigger__text { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .cdp-trigger__icon { flex-shrink: 0; color: var(--ink3); display: flex; align-items: center; }
        .cdp-panel {
            position: absolute; top: 100%; left: 0; z-index: 9999;
            background: var(--surface); border: 1.5px solid var(--border); border-radius: 12px;
            box-shadow: 0 8px 28px rgba(0,0,0,.13); padding: 14px 12px 12px;
            width: 272px; display: none; animation: dropIn .14s ease;
        }
        .cdp-wrap.cdp-open .cdp-panel { display: block; }
        .cdp-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        .cdp-nav {
            width: 30px; height: 30px; border: 1.5px solid var(--border); border-radius: 8px;
            background: var(--surface); cursor: pointer; display: flex; align-items: center;
            justify-content: center; color: var(--ink2); transition: border-color .15s, color .15s, background .15s;
        }
        .cdp-nav:hover { border-color: var(--g500); color: var(--g600); background: var(--g50); }
        .cdp-month-label {
            font-family: var(--fh); font-size: 14px; font-weight: 800;
            color: var(--ink); cursor: pointer; transition: color .15s;
        }
        .cdp-month-label:hover { color: var(--g600); }
        .cdp-dow-row { display: grid; grid-template-columns: repeat(7, 1fr); margin-bottom: 3px; }
        .cdp-dow-row span {
            text-align: center; font-size: 10.5px; font-weight: 700;
            color: var(--ink3); padding: 3px 0; text-transform: uppercase; letter-spacing: .03em;
        }
        .cdp-days { display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; }
        .cdp-day {
            aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
            border-radius: 7px; font-size: 12.5px; font-weight: 500; color: var(--ink);
            cursor: pointer; transition: background .12s, color .12s; border: none;
            background: none; font-family: var(--fb); line-height: 1;
        }
        .cdp-day:hover { background: var(--g50); color: var(--g700); }
        .cdp-day--selected { background: var(--g500) !important; color: #fff !important; font-weight: 700; }
        .cdp-day--today { box-shadow: inset 0 0 0 1.5px var(--g400); }
        .cdp-day--outside { color: var(--ink4); }
        .cdp-day--outside:hover { background: var(--surf2); color: var(--ink3); }
        .cdp-footer { display: flex; gap: 6px; margin-top: 10px; }
        .cdp-clear {
            flex: 1; padding: 7px; border: 1.5px solid var(--border); border-radius: 8px;
            background: var(--surface); color: var(--ink3); font-size: 12.5px; font-weight: 600;
            cursor: pointer; transition: border-color .15s, color .15s; font-family: var(--fb);
        }
        .cdp-clear:hover { border-color: var(--border2); color: var(--ink); }
        .cdp-today-btn {
            flex: 1; padding: 7px; border: 1.5px solid var(--g400); border-radius: 8px;
            background: var(--g50); color: var(--g700); font-size: 12.5px; font-weight: 600;
            cursor: pointer; transition: border-color .15s, background .15s; font-family: var(--fb);
        }
        .cdp-today-btn:hover { background: var(--g200); border-color: var(--g500); }

        /* =====================================================
           SHARED SECTION STYLES (for child pages)
        ===================================================== */
        .sec-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
        .sec-title { font-family: var(--fh); font-size: 1.15rem; font-weight: 700; color: var(--ink); display: flex; align-items: center; gap: 8px; }
        .sec-title i { color: var(--g500); font-size: 13px; }
        .sec-link { font-size: 12.5px; color: var(--g600); font-weight: 600; display: flex; align-items: center; gap: 5px; transition: gap .2s; }
        .sec-link:hover { gap: 8px; }
        .sec-arrows { display: flex; gap: 6px; }
        .sarrow { width: 34px; height: 34px; border-radius: 50%; border: 1px solid var(--border); background: var(--surface); color: var(--ink3); display: flex; align-items: center; justify-content: center; font-size: 13px; cursor: pointer; transition: all .2s; }
        .sarrow:hover { border-color: var(--ink2); color: var(--surface); background: var(--ink2); }
        .scroll-dots { display: flex; justify-content: center; gap: 7px; margin-top: 14px; }
        .scroll-dot { width: 8px; height: 8px; border-radius: 4px; background: var(--border2); cursor: pointer; transition: all .3s; }
        .scroll-dot.active { width: 22px; background: var(--g500); }

        /* Product card */
        .pcard {
            min-width: 200px; max-width: 200px;
            background: #fff;
            border: 1px solid #dde0e5;
            border-radius: 6px;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
            overflow: hidden; flex-shrink: 0; cursor: pointer;
            transition: box-shadow .24s cubic-bezier(.4,0,.2,1), transform .24s cubic-bezier(.4,0,.2,1), border-color .24s;
            position: relative; display: flex; flex-direction: column;
        }
        .pcard:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 44px rgba(0,0,0,.16), 0 6px 14px rgba(0,0,0,.07);
            border-color: #bec3ca;
        }
        .pcard__img {
            height: 192px; background: #fff;
            display: flex; align-items: center; justify-content: center;
            padding: 16px; position: relative; overflow: hidden;
            border-bottom: 1px solid #f2f3f5;
        }
        .pcard__img img {
            max-height: 158px; object-fit: contain; display: block;
            width: auto; max-width: 100%;
            transition: transform .4s cubic-bezier(.25,0,.25,1);
        }
        .pcard:hover .pcard__img img { transform: scale(1.06); }
        .pcard__badge {
            position: absolute; top: 9px; left: 9px;
            background: var(--g500); color: #fff;
            font-size: 9px; font-weight: 800; padding: 2px 7px; border-radius: 3px;
            letter-spacing: .5px; text-transform: uppercase; line-height: 1.5;
        }
        .pcard__badge--sale { background: #c00; }
        .pcard__body { padding: 10px 12px 58px; display: flex; flex-direction: column; gap: 3px; flex: 1; }
        /* Stars float above name — matches Argos visual order */
        .pcard__stars { order: -1; display: flex; align-items: center; gap: 1px; margin-bottom: 1px; }
        .pcard__stars i { font-size: 10px; color: #f5a623; }
        .pcard__stars span { font-size: 11px; color: #767676; margin-left: 4px; font-weight: 500; }
        .pcard__name {
            font-size: 13px; font-weight: 400; color: #1d1d1b; line-height: 1.42;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .pcard__name a { color: inherit; }
        .pcard__name a:hover { color: var(--g700); text-decoration: underline; }
        .pcard__price { font-size: 18px; font-weight: 800; color: #1a1a1a; font-family: var(--fh); letter-spacing: -.5px; display: inline; }
        .pcard__old { font-size: 12px; color: #888; text-decoration: line-through; margin-left: 5px; font-weight: 400; }
        .pcard__atc {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: var(--g500); color: #fff; border: none;
            padding: 14px 12px; font-size: 13.5px; font-weight: 700;
            display: flex; align-items: center; justify-content: center; gap: 7px;
            transition: background .15s; width: 100%; cursor: pointer;
            font-family: var(--fh); letter-spacing: -.1px;
        }
        .pcard__atc:hover { background: var(--g700) !important; }
        .products-row { display: flex; gap: 14px; overflow-x: auto; padding-bottom: 8px; scrollbar-width: none; cursor: grab; }
        .products-row::-webkit-scrollbar { display: none; }
        .products-row:active { cursor: grabbing; }

        /* Brand card */
        .brand-card { min-width: 120px; height: 64px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--r); display: flex; align-items: center; justify-content: center; padding: 10px 14px; flex-shrink: 0; transition: all .2s; cursor: pointer; }
        .brand-card:hover { border-color: var(--g400); box-shadow: var(--sh-sm); transform: translateY(-2px); }
        .brand-card img { max-width: 90px; max-height: 40px; object-fit: contain; filter: grayscale(1); transition: filter .2s; }
        .brand-card:hover img { filter: grayscale(0); }
        .brands-row { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 6px; scrollbar-width: none; }
        .brands-row::-webkit-scrollbar { display: none; }

        /* Value props */
        .vp-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; }
        .vp-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 20px 18px; display: flex; gap: 14px; align-items: flex-start; transition: all .2s; }
        .vp-card:hover { border-color: var(--g400); box-shadow: var(--sh-sm); }
        .vp-icon { width: 42px; height: 42px; border-radius: 10px; background: var(--g50); border: 1px solid var(--border2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .vp-icon i { font-size: 17px; color: var(--g600); }
        .vp-label { font-size: 13px; font-weight: 600; color: var(--ink); margin-bottom: 3px; }
        .vp-sub { font-size: 12px; color: var(--ink3); line-height: 1.4; }

        /* Deal cards */
        .deal-row { display: flex; gap: 14px; overflow-x: auto; padding-bottom: 6px; scrollbar-width: none; }
        .deal-row::-webkit-scrollbar { display: none; }
        .deal-card { min-width: 168px; background: var(--surface); border: 1px solid var(--border); border-top: 3px solid var(--g400); border-radius: var(--r-lg); padding: 24px 18px; text-align: center; flex-shrink: 0; cursor: pointer; transition: all .22s; }
        .deal-card:hover { transform: translateY(-3px); box-shadow: var(--sh-md); border-top-color: var(--g500); }
        .deal-icon { font-size: 28px; color: var(--g500); margin-bottom: 12px; }
        .deal-label { font-size: 13px; font-weight: 600; color: var(--ink); margin-bottom: 4px; }
        .deal-sub { font-size: 11.5px; color: var(--ink3); }

        /* Newsletter */
        .newsletter { background: var(--g700); border-radius: var(--r-xl); padding: 44px 48px; color: #fff; display: flex; align-items: center; gap: 40px; position: relative; overflow: hidden; }
        .newsletter::before { content: ''; position: absolute; right: -60px; top: -60px; width: 280px; height: 280px; border-radius: 50%; background: rgba(90,171,31,.15); pointer-events: none; }
        .newsletter::after { content: ''; position: absolute; right: 60px; bottom: -80px; width: 180px; height: 180px; border-radius: 50%; background: rgba(171,235,115,.08); pointer-events: none; }
        .newsletter__text { flex: 1; position: relative; z-index: 1; }
        .newsletter__text h2 { font-family: var(--fh); font-size: 1.5rem; font-weight: 700; margin-bottom: 8px; }
        .newsletter__text p { font-size: 13px; color: rgba(255,255,255,.65); line-height: 1.5; }
        .newsletter__form { display: flex; border-radius: var(--r); overflow: hidden; flex-shrink: 0; width: 340px; position: relative; z-index: 1; }
        .newsletter__input { flex: 1; padding: 12px 16px; border: none; outline: none; font-size: 13px; background: #fff; color: var(--ink); }
        .newsletter__btn { background: var(--g400); color: #fff; padding: 12px 20px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .2s; white-space: nowrap; flex-shrink: 0; }
        .newsletter__btn:hover { background: var(--g500); }

        /* =====================================================
           MAIN WRAP
        ===================================================== */
        .main-wrap { max-width: var(--max); margin: 0 auto; padding: 28px var(--gutter) 60px; }
        .section-block { margin-top: 40px; }

        /* =====================================================
           FOOTER
        ===================================================== */
        .footer { background: var(--g900); color: rgba(255,255,255,.65); padding: 52px 0 24px; }
        .footer__inner { max-width: var(--max); margin: 0 auto; padding: 0 var(--gutter); }
        .footer__grid { display: grid; grid-template-columns: repeat(5,1fr); gap: 28px; margin-bottom: 40px; }
        .footer__brand p { font-size: 12px; line-height: 1.6; color: rgba(255,255,255,.4); max-width: 200px; margin-top: 8px; }
        .footer__socials { display: flex; gap: 8px; margin-top: 18px; }
        .soc-btn { width: 36px; height: 36px; border-radius: 8px; background: rgba(255,255,255,.08); display: flex; align-items: center; justify-content: center; font-size: 15px; color: rgba(255,255,255,.5); transition: all .2s; }
        .soc-btn:hover { background: var(--g500); color: #fff; }
        .footer__col h4 { font-family: var(--fh); font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase; color: rgba(255,255,255,.28); margin-bottom: 14px; font-weight: 600; }
        .footer__col ul li { margin-bottom: 9px; }
        .footer__col ul li a { font-size: 13px; color: rgba(255,255,255,.5); transition: color .2s; }
        .footer__col ul li a:hover { color: var(--g200); }
        .footer__bottom {
            border-top: 1px solid rgba(255,255,255,.07); padding-top: 22px;
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 12px; font-size: 12px; color: rgba(255,255,255,.28);
        }
        .footer__bottom a { color: rgba(255,255,255,.35); }
        .footer__bottom a:hover { color: rgba(255,255,255,.65); }

        /* Toast — see partials/notify.blade.php */

        /* =====================================================
           RESPONSIVE
        ===================================================== */
        @media (max-width: 1024px) { .footer__grid { grid-template-columns: repeat(3,1fr); } }

        @media (max-width: 900px) {
            .vp-grid { grid-template-columns: repeat(2,1fr); }
            .footer__grid { grid-template-columns: repeat(2,1fr); }
            .newsletter { flex-direction: column; padding: 32px 28px; gap: 22px; }
            .newsletter__form { width: 100%; }
        }

        @media (max-width: 768px) {
            .cat-btn { display: none; }
            .search-wrap { display: none; }
            .mobile-menu-btn { display: block; }
            .mobile-search-btn { display: block; }
            .currency-dropdown { display: none; } /* lives in drawer */
            .info-dropdown { display: none; }     /* links live in footer on mobile */
            .header__inner { gap: 8px; }
            .header-actions { gap: 2px; }
            .logo-link img { max-width: 110px; height: auto; }
            .mini-cart { max-width: calc(100vw - 24px); }
            .pcard { min-width: 165px; max-width: 165px; }
            .pcard__img { height: 158px; }
            .vp-grid { grid-template-columns: 1fr; gap: 10px; }
            .footer__grid { grid-template-columns: 1fr 1fr; gap: 20px; }
            .newsletter { padding: 24px 20px; }
            .main-wrap { padding: 16px 14px 48px; }

            /* Collapse cart btn to icon-only on mobile */
            .cart-btn .cart-label { display: none; }
            .cart-btn { background: var(--g600); padding: 8px 10px; border-radius: 8px; }
            .cart-btn:hover { background: var(--g700); transform: none; }
            .user-btn { border: none; background: none; }
            .user-btn--avatar { border: 2px solid var(--g400) !important; background: var(--g500) !important; }

            .contact-bar { overflow-x: auto; scrollbar-width: none; }
            .contact-bar::-webkit-scrollbar { display: none; }
            .contact-bar__inner { flex-wrap: nowrap; white-space: nowrap; gap: 0; justify-content: flex-start; overflow-x: auto; scrollbar-width: none; }
            .contact-bar__inner::-webkit-scrollbar { display: none; }
            .contact-bar__inner span { font-size: 11.5px; padding: 0 12px; border-right: 1px solid var(--border); flex-shrink: 0; }
            .contact-bar__inner span:last-child { border-right: none; }
            .contact-bar__inner span:first-child { padding-left: 0; }

            .announce-bar { text-align: left; white-space: nowrap; overflow-x: auto; scrollbar-width: none; }
            .announce-bar::-webkit-scrollbar { display: none; }
        }

        @media (max-width: 480px) {
            .footer__grid { grid-template-columns: 1fr; }
            .pcard { min-width: 145px; max-width: 145px; }
            .pcard__img { height: 140px; }
            .newsletter__form { flex-direction: column; border-radius: var(--r); }
            .newsletter__input { border-radius: var(--r) var(--r) 0 0; }
            .newsletter__btn { border-radius: 0 0 var(--r) var(--r); }
        }

        /* ═══════════════════════════════════════════════
           Account section — shared shell
           Used by: /account, /account/orders, /account/change-password
        ═══════════════════════════════════════════════ */
        .acct-wrap { max-width: 1100px; margin: 0 auto; padding: 24px 16px 64px; }

        .acct-breadcrumb {
            list-style: none; display: flex; flex-wrap: wrap;
            align-items: center; gap: 2px;
            font-size: 12px; margin-bottom: 22px; color: var(--ink4);
        }
        .acct-breadcrumb li { display: flex; align-items: center; }
        .acct-breadcrumb li:not(:last-child)::after { content: '/'; margin: 0 7px; color: var(--border2); }
        .acct-breadcrumb a { color: var(--ink3); text-decoration: none; transition: color .15s; }
        .acct-breadcrumb a:hover { color: var(--g600); }
        .acct-breadcrumb li:last-child { color: var(--ink); font-weight: 600; }

        .acct-layout { display: grid; grid-template-columns: 200px 1fr; gap: 20px; align-items: start; }

        .acct-sidebar { position: sticky; top: 88px; }
        .acct-sidebar nav ul { list-style: none; padding: 0; }
        .acct-sidebar nav li a {
            display: flex; align-items: center; gap: 9px;
            padding: 10px 14px; font-size: 13px; font-weight: 500;
            color: var(--ink2); text-decoration: none;
            border-left: 3px solid transparent;
            transition: color .15s, border-color .15s;
        }
        .acct-sidebar nav li a i { color: var(--ink4); font-size: 12px; width: 14px; text-align: center; flex-shrink: 0; }
        .acct-sidebar nav li a:hover { color: var(--g600); }
        .acct-sidebar nav li a:hover i { color: var(--g500); }
        .acct-sidebar nav li a.active { border-left-color: var(--g500); color: var(--g700); font-weight: 700; }
        .acct-sidebar nav li a.active i { color: var(--g500); }

        .acct-main { min-width: 0; }

        .acct-header {
            padding: 0 0 14px 0; margin-bottom: 18px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            justify-content: space-between; gap: 12px; flex-wrap: wrap;
        }
        .acct-header__title {
            font-family: var(--font-head); font-size: 1.25rem;
            font-weight: 800; color: var(--ink); letter-spacing: -.3px;
            display: flex; align-items: center; gap: 8px;
        }
        .acct-header__title i { color: var(--g500); font-size: 15px; }

        @media (max-width: 900px) {
            .acct-layout  { display: block; }
            .acct-sidebar { display: none; }
        }
        @media (max-width: 640px) { .acct-wrap { padding: 14px 12px 48px; } }
    </style>

    @stack('styles')
</head>
<body>

<a href="#main-content" class="skip-link">Skip to main content</a>

{{-- ── TOP STRIP ── --}}
<div class="top-strip">
    <div class="top-strip__inner">
        <div class="top-strip__left">
            <span><i class="fas fa-shield-alt"></i> Trusted Since 1992</span>
            <span><i class="fas fa-headset"></i> 24/7 Support</span>
        </div>
        <div class="top-strip__links">
            <a href="{{ route('account.orders') }}"><i class="fas fa-box" style="margin-right:4px;"></i>Track Order</a>
            <a href="/"><i class="fas fa-store" style="margin-right:4px;"></i>Shop</a>
        </div>
    </div>
</div>

{{-- ── ANNOUNCE BAR ── --}}
<div class="announce-bar">
    <i class="fas fa-star"></i> 99% Positive Feedback
    &nbsp;·&nbsp;
    <i class="fas fa-tag"></i> New arrivals weekly
</div>

{{-- ── HEADER ── --}}
<header class="header" id="main-nav">
    <div class="header__inner">

        {{-- Hamburger --}}
        <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Open menu" aria-expanded="false">
            <i class="fas fa-bars"></i>
        </button>

        {{-- Logo --}}
        <a href="/" class="logo-link" aria-label="{{ $appStoreName ?? 'Home' }}">
            @if(!empty($appStoreLogo))
                <img src="{{ asset('storage/' . $appStoreLogo) }}" alt="{{ $appStoreName ?? 'Store' }}" height="36">
            @else
                <img src="{{ asset('image.png') }}" alt="{{ $appStoreName ?? 'Store' }}">
            @endif
        </a>

        {{-- Desktop Categories --}}
        <div class="cat-dropdown-wrap">
            <button class="cat-btn" id="catBtn" aria-expanded="false" aria-controls="catMenu">
                <i class="fas fa-th-large cat-grid-icon"></i>
                <span>Categories</span>
                <i class="fas fa-chevron-down cat-chev"></i>
            </button>

            <div class="cat-dropdown" id="catMenu" role="menu">
                <div class="cat-list" id="catList">
                    @php
                        $__usedIcons  = [];
                        $__catIconMap = [];
                        $__fallbacks  = [
                            'fa-box-open','fa-store','fa-cube','fa-layer-group',
                            'fa-swatchbook','fa-microchip','fa-display',
                            'fa-tablet-screen-button','fa-memory','fa-shapes',
                            'fa-bars-staggered','fa-circle-dot','fa-list-check',
                            'fa-grid-2','fa-star','fa-rectangle-list','fa-tag',
                        ];
                        foreach ($topLevelCategories as $__cat) {
                            $__n = strtolower($__cat->name);
                            $__c = [];
                            if (str_contains($__n,'sound') || str_contains($__n,'vision'))                                          $__c[]='fa-tv';
                            if (str_contains($__n,'air') && (str_contains($__n,'cool')||str_contains($__n,'condition')))            $__c[]='fa-wind';
                            if (str_contains($__n,'solar') || str_contains($__n,'power sol'))                                       $__c[]='fa-solar-panel';
                            if (str_contains($__n,'power stor') || str_contains($__n,'battery'))                                    $__c[]='fa-car-battery';
                            if (str_contains($__n,'cold stor') || str_contains($__n,'freez'))                                       $__c[]='fa-snowflake';
                            if (str_contains($__n,'fridge') || str_contains($__n,'refrig'))                                         $__c[]='fa-temperature-low';
                            if (str_contains($__n,'cooker') || str_contains($__n,'cook'))                                           $__c[]='fa-fire-burner';
                            if (str_contains($__n,'food proc') || str_contains($__n,'processor') || str_contains($__n,'juic'))      $__c[]='fa-blender';
                            if (str_contains($__n,'kitchen') || str_contains($__n,'blend'))                                         $__c[]='fa-kitchen-set';
                            if (str_contains($__n,'garment') || str_contains($__n,'iron') || str_contains($__n,'cloth'))            $__c[]='fa-shirt';
                            if (str_contains($__n,'wash') || str_contains($__n,'laundry'))                                          $__c[]='fa-jug-detergent';
                            if (str_contains($__n,'accessor') || str_contains($__n,'cable') || str_contains($__n,'plug'))           $__c[]='fa-plug';
                            if (str_contains($__n,'furniture') || str_contains($__n,'equip'))                                       $__c[]='fa-couch';
                            if (str_contains($__n,'tv') || str_contains($__n,'tele') || str_contains($__n,'screen'))                $__c[]='fa-desktop';
                            if (str_contains($__n,'inverter') || str_contains($__n,'gen'))                                          $__c[]='fa-bolt';
                            if (str_contains($__n,'power') || str_contains($__n,'electric'))                                        $__c[]='fa-plug-circle-bolt';
                            if (str_contains($__n,'phone') || str_contains($__n,'mobile') || str_contains($__n,'smart'))            $__c[]='fa-mobile-screen';
                            if (str_contains($__n,'laptop') || str_contains($__n,'computer') || str_contains($__n,'pc'))            $__c[]='fa-laptop';
                            if (str_contains($__n,'audio') || str_contains($__n,'speaker'))                                         $__c[]='fa-headphones';
                            if (str_contains($__n,'camera') || str_contains($__n,'photo'))                                          $__c[]='fa-camera';
                            if (str_contains($__n,'light') || str_contains($__n,'lamp') || str_contains($__n,'bulb'))               $__c[]='fa-lightbulb';
                            if (str_contains($__n,'water') || str_contains($__n,'pump') || str_contains($__n,'dispens'))            $__c[]='fa-droplet';
                            if (str_contains($__n,'fan'))                                                                            $__c[]='fa-fan';
                            if (str_contains($__n,'other'))                                                                          $__c[]='fa-ellipsis';
                            $__pool   = array_values(array_unique(array_merge($__c, $__fallbacks)));
                            $__chosen = 'fa-tag';
                            foreach ($__pool as $__ico) {
                                if (!in_array($__ico, $__usedIcons)) { $__chosen = $__ico; break; }
                            }
                            $__usedIcons[]              = $__chosen;
                            $__catIconMap[$__cat->id]   = $__chosen;
                        }
                    @endphp

                    @foreach($topLevelCategories as $i => $category)
                        @php $icon = $__catIconMap[$category->id] ?? 'fa-tag'; @endphp
                        <a href="/category/{{ rawurlencode($category->name) }}"
                           class="cat-dropdown__item"
                           @if($category->subcategories->isNotEmpty()) data-cat-id="{{ $i }}" @endif
                           role="menuitem">
                            <div class="cat-icon"><i class="fas {{ $icon }}"></i></div>
                            <span class="item-label">{{ $category->name }}</span>
                            @if($category->subcategories->isNotEmpty())
                                <i class="fas fa-chevron-right sub-arrow"></i>
                            @endif
                        </a>
                    @endforeach
                </div>

                <div id="catSubContainer"></div>

                {{-- Brands panel --}}
                @php $manyBrands = isset($navBrands) && $navBrands->count() > 8; @endphp
                <div class="cat-brands-panel{{ $manyBrands ? ' wide' : '' }}">
                    <div class="cbp-heading">Brands</div>
                    <div class="cbp-brand-grid">
                        @foreach($navBrands as $brand)
                            <a href="{{ route('brand.show', $brand->slug ?: \Str::slug($brand->name)) }}" class="cbp-brand-link">{{ $brand->name }}</a>
                        @endforeach
                    </div>
                    <a href="{{ route('products.index') }}" class="cbp-view-brands">
                        All brands <i class="fas fa-arrow-right i"></i>
                    </a>
                </div>

                {{-- Hidden sub-panel templates --}}
                @foreach($topLevelCategories as $i => $category)
                    @if($category->subcategories->isNotEmpty())
                        <div class="cat-sub-panel" id="subpanel-{{ $i }}" style="display:none;" aria-hidden="true">
                            <div class="cat-sub-panel__title">{{ $category->name }}</div>
                            <div class="sub-links-grid">
                                @foreach($category->subcategories as $sub)
                                    <a href="{{ route('category.show', \Str::slug(str_replace(['/', '&', '+'], ' ', $sub->name))) }}">{{ $sub->name }}</a>
                                @endforeach
                            </div>
                            <a href="{{ route('category.show', \Str::slug(str_replace(['/', '&', '+'], ' ', $category->name))) }}" class="cat-sub-panel__viewall">
                                View all {{ $category->name }} <i class="fas fa-arrow-right" style="font-size:11px;"></i>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Search (desktop) --}}
        <div class="search-wrap">
            <form class="search-form" action="{{ route('search') }}" method="GET" role="search" id="desktopSearchForm">
                <input type="text" name="searchTerm" class="search-input"
                    id="desktopSearchInput"
                    placeholder="Search products, brands, categories…"
                    value="{{ request('searchTerm') }}"
                    autocomplete="off" aria-label="Search products"
                    aria-expanded="false"
                    aria-controls="desktopSearchSuggestions">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>Search
                </button>
            </form>
            <div class="search-suggestions" id="desktopSearchSuggestions" role="listbox" aria-label="Search suggestions"></div>
        </div>

        {{-- Header actions --}}
        <div class="header-actions">

            {{-- Mobile search toggle --}}
            <button class="mobile-search-btn" id="mobileSearchBtn" aria-label="Search" aria-expanded="false">
                <i class="fas fa-search"></i>
            </button>

            {{-- Info dropdown --}}
            <div class="info-dropdown">
                <button class="icon-btn" id="infoBtn" aria-label="More info" aria-expanded="false" title="Info">
                    <i class="fas fa-info-circle"></i>
                </button>
                <div class="info-menu" id="infoMenu" role="menu">
                    <a href="{{ route('about') }}"   role="menuitem"><span class="info-menu__icon-box"><i class="fas fa-info-circle"></i></span> About Us</a>
                    <a href="{{ route('store') }}"   role="menuitem"><span class="info-menu__icon-box"><i class="fas fa-map-marker-alt"></i></span> Find a Store</a>
                    <a href="{{ route('contact') }}" role="menuitem"><span class="info-menu__icon-box"><i class="fas fa-headset"></i></span> Contact Us</a>
                    <a href="{{ route('faq') }}"     role="menuitem"><span class="info-menu__icon-box"><i class="fas fa-question-circle"></i></span> Help &amp; FAQ</a>
                </div>
            </div>

            {{-- Currency dropdown (desktop) --}}
            <div class="currency-dropdown">
                <button class="icon-btn" id="currencyBtn" aria-label="Select currency" aria-expanded="false" title="Currency">
                    <i class="fas fa-globe"></i>
                </button>
                <div class="currency-menu" id="currencyMenu" role="menu" aria-label="Select display currency">
                    <div class="currency-menu__title">Display Currency</div>
                    <div class="currency-menu__list" id="currencyMenuList">
                        {{-- options injected by JS --}}
                    </div>
                </div>
            </div>

            {{-- User dropdown --}}
            <div class="user-dropdown">
                @auth
                    <button class="user-btn user-btn--avatar" id="userBtn" aria-label="My Account" aria-expanded="false">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </button>
                    <div class="user-menu" id="userMenu" role="menu">
                        <div class="user-menu__info">
                            <div class="user-menu__avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                            <div class="user-menu__info-text">
                                <div class="user-menu__name">{{ auth()->user()->name }}</div>
                                <div class="user-menu__email">{{ auth()->user()->email }}</div>
                            </div>
                        </div>
                        @if(auth()->user()->role === 'admin')
                        <div class="user-menu__section">
                            <a href="{{ route('admin.dashboard') }}" role="menuitem" style="color:var(--g600);font-weight:600;">
                                <span class="user-menu__icon-box" style="background:var(--g50);border-color:var(--g200);"><i class="fas fa-arrow-left" style="color:var(--g600);"></i></span> Back to Admin
                            </a>
                        </div>
                        @endif
                        <div class="user-menu__section">
                            <a href="/account" role="menuitem">
                                <span class="user-menu__icon-box"><i class="fas fa-user"></i></span> My Account
                            </a>
                            <a href="/account/orders" role="menuitem">
                                <span class="user-menu__icon-box"><i class="fas fa-box"></i></span> My Orders
                            </a>
                            <a href="/tickets" role="menuitem">
                                <span class="user-menu__icon-box"><i class="fas fa-comment-dots"></i></span> Support Tickets
                            </a>
                        </div>
                        <div class="user-menu__section">
                            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                                @csrf
                                <button type="submit" class="user-menu__logout" role="menuitem">
                                    <span class="user-menu__icon-box"><i class="fas fa-sign-out-alt"></i></span> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <button class="user-btn" id="userBtn" aria-label="Sign In or Register" aria-expanded="false">
                        <i class="fas fa-user"></i>
                    </button>
                    <div class="user-menu" id="userMenu" role="menu">
                        <div class="user-menu__section">
                            <a href="{{ route('login') }}" role="menuitem">
                                <span class="user-menu__icon-box"><i class="fas fa-sign-in-alt"></i></span> Sign In
                            </a>
                            <a href="{{ route('register') }}" role="menuitem">
                                <span class="user-menu__icon-box"><i class="fas fa-user-plus"></i></span> Create Account
                            </a>
                        </div>
                    </div>
                @endauth
            </div>

            {{-- Cart --}}
            <div class="cart-wrap">
                <a href="{{ route('cart.index') }}" class="cart-btn" id="cartBtn" aria-label="Shopping cart">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-label">Cart</span>
                    <span class="cart-badge" id="cartCount">0</span>
                </a>
                <div class="mini-cart" id="miniCart" role="dialog" aria-label="Mini cart">
                    <div class="mini-cart__head">
                        <h4><i class="fas fa-shopping-bag" style="color:var(--g500);margin-right:7px;font-size:13px;"></i>Cart (<span id="miniCartCount">0</span>)</h4>
                        <a href="{{ route('cart.index') }}">View all <i class="fas fa-arrow-right" style="font-size:10px;"></i></a>
                    </div>
                    <div class="mini-cart__items" id="miniCartItems">
                        <div class="empty-cart">
                            <i class="fas fa-shopping-bag"></i>
                            <p>Your cart is empty</p>
                            <a href="/">Start Shopping</a>
                        </div>
                    </div>
                    <div class="mini-cart__foot" id="miniCartFoot" style="display:none;">
                        <div class="mini-cart__foot-total">
                            <span>Total</span>
                            <span id="miniCartTotal">&#8358;0</span>
                        </div>
                        <button class="mini-cart__checkout"
                                onclick="window.location='{{ route('checkout.index') }}'">
                            <i class="fas fa-lock" style="font-size:12px;"></i> Checkout Securely
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Mobile search bar --}}
    <div class="mobile-search-bar" id="mobileSearchBar">
        <form class="search-form" action="{{ route('search') }}" method="GET" role="search" id="mobileSearchForm">
            <input type="text" name="searchTerm" class="search-input"
                id="mobileSearchInput"
                placeholder="Search products, brands, categories…"
                value="{{ request('searchTerm') }}" aria-label="Search products"
                autocomplete="off"
                aria-expanded="false"
                aria-controls="mobileSearchSuggestions">
            <button type="submit" class="search-btn">Search</button>
        </form>
        <div class="search-suggestions" id="mobileSearchSuggestions" role="listbox" aria-label="Search suggestions"></div>
    </div>
</header>

{{-- ── CONTACT BAR ── --}}
<div class="contact-bar" role="complementary" aria-label="Contact information">
    <div class="contact-bar__inner">
        @php $ph = $storePhone ?? '08064066170'; @endphp
        <span><i class="fas fa-headset"></i> Call us: <a href="tel:{{ $ph }}">{{ $ph }}</a></span>
        @if($storeAddress)
            <span><i class="fas fa-map-marker-alt"></i> {{ $storeAddress }}</span>
        @else
            <span><i class="fas fa-map-marker-alt"></i> 17-18 Zik's Avenue, Uwani, Enugu</span>
        @endif
        @if($storeEmail)
            <span><i class="fas fa-envelope"></i> <a href="mailto:{{ $storeEmail }}">{{ $storeEmail }}</a></span>
        @else
            <span><i class="fas fa-envelope"></i> <a href="mailto:Info@Albertinang.com">Info@Albertinang.com</a></span>
        @endif
    </div>
</div>

{{-- ── MOBILE MENU OVERLAY ── --}}
<div class="mobile-overlay" id="mobileOverlay" role="presentation"></div>

{{-- ── MOBILE MENU ── --}}
<nav class="mobile-menu" id="mobileMenu" aria-label="Mobile navigation">

    {{-- Header --}}
    <div class="mobile-menu__head">
        <div class="mobile-menu__title" id="mobMenuTitle">
            <i class="fas fa-th-large"></i> Browse Menu
        </div>
        <div class="mobile-cur-pill" id="mobileCurPill">
            <button type="button" class="mobile-cur-pill__btn" id="mobileCurBtn"
                    aria-haspopup="true" aria-expanded="false" aria-label="Select currency">
                <i class="fas fa-globe"></i>
                <span id="mobileCurCode">NGN</span>
                <i class="fas fa-chevron-down mobile-cur-pill__chev"></i>
            </button>
            <div class="mobile-cur-pill__menu" id="mobileCurrencyList" role="menu">
                {{-- options injected by JS --}}
            </div>
        </div>
        <button class="mobile-menu__close" id="mobileMenuClose" aria-label="Close menu">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- Auth strip --}}
    @auth
    <div class="mobile-auth-strip">
        <div class="mobile-auth-strip__avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div class="mobile-auth-strip__text">
            <div class="mobile-auth-strip__name">{{ auth()->user()->name }}</div>
            <div class="mobile-auth-strip__sub">Member account</div>
        </div>
        <a href="/account" class="mobile-auth-strip__link">My Account</a>
    </div>
    @else
    <div class="mobile-auth-strip">
        <div class="mobile-auth-strip__avatar"><i class="fas fa-user"></i></div>
        <div class="mobile-auth-strip__text">
            <div class="mobile-auth-strip__name">Welcome</div>
            <div class="mobile-auth-strip__sub">Sign in for faster checkout</div>
        </div>
        <a href="{{ route('login') }}" class="mobile-auth-strip__link">Sign In</a>
    </div>
    @endauth

    {{-- Accordion category list --}}
    <div class="mobile-menu__body" id="mobileMenuBody">

        @foreach($topLevelCategories as $i => $category)
            @php $icon2 = $__catIconMap[$category->id] ?? 'fa-tag'; @endphp
            <div class="mobile-cat-row" id="mob-cat-row-{{ $i }}">
                <a href="/category/{{ rawurlencode($category->name) }}" class="mobile-cat-link">
                    <div class="mobile-cat__icon"><i class="fas {{ $icon2 }}"></i></div>
                    {{ $category->name }}
                </a>
                @if($category->subcategories->isNotEmpty())
                    <button class="mobile-cat-toggle"
                            data-target="mob-subs-{{ $i }}"
                            aria-expanded="false"
                            aria-label="Show {{ $category->name }} subcategories">
                        <i class="fas fa-plus"></i>
                    </button>
                @endif
            </div>
            @if($category->subcategories->isNotEmpty())
                <div class="mobile-cat-subs" id="mob-subs-{{ $i }}">
                    <a href="/category/{{ rawurlencode($category->name) }}" class="mobile-cat-subs__viewall">
                        <i class="fas fa-th-large" style="font-size:11px; color:var(--g600); flex-shrink:0;"></i>
                        View all {{ $category->name }}
                        <i class="fas fa-arrow-right arr"></i>
                    </a>
                    @foreach($category->subcategories as $sub)
                        <a href="{{ route('category.show', \Str::slug(str_replace(['/', '&', '+'], ' ', $sub->name))) }}" class="mob-sub-link">
                            {{ $sub->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        @endforeach

        @if(isset($navBrands) && $navBrands->isNotEmpty())
        <div class="mobile-cat-row" id="mob-cat-row-brands">
            <div class="mobile-cat-link" style="pointer-events:none;">
                <div class="mobile-cat__icon"><i class="fas fa-tag"></i></div>
                Shop by Brand
            </div>
            <button class="mobile-cat-toggle"
                    data-target="mob-subs-brands"
                    aria-expanded="false"
                    aria-label="Show brands">
                <i class="fas fa-plus"></i>
            </button>
        </div>
        <div class="mobile-cat-subs" id="mob-subs-brands">
            <div class="mob-brands-grid">
                @foreach($navBrands as $brand)
                    <a href="{{ route('brand.show', $brand->slug ?: \Str::slug($brand->name)) }}" class="mob-brand-item">{{ $brand->name }}</a>
                @endforeach
            </div>
        </div>
        @endif

        <div class="mobile-menu__footer">
            <div class="mobile-menu__footer-label">Help &amp; Info</div>
            <a href="{{ route('about') }}"><span class="mob-foot-icon"><i class="fas fa-info-circle"></i></span> About Us</a>
            <a href="{{ route('store') }}"><span class="mob-foot-icon"><i class="fas fa-map-marker-alt"></i></span> Find a Store</a>
            <a href="{{ route('contact') }}"><span class="mob-foot-icon"><i class="fas fa-headset"></i></span> Contact Us</a>
            <a href="{{ route('faq') }}"><span class="mob-foot-icon"><i class="fas fa-question-circle"></i></span> Help &amp; FAQ</a>
        </div>

    </div>

</nav>

{{-- ── PAGE CONTENT ── --}}
<main id="main-content">
    @yield('content')
</main>

{{-- ── FOOTER ── --}}
<footer class="footer" role="contentinfo">
    <div class="footer__inner">
        <div class="footer__grid">

            {{-- Brand col --}}
            <div class="footer__brand">
                <a href="/" style="display:inline-block;border-radius:10px;padding:8px 14px;margin-bottom:12px;">
                    @if(!empty($appStoreLogo))
                        <img src="{{ asset('storage/' . $appStoreLogo) }}"
                             alt="{{ $appStoreName ?? 'Store' }}"
                             style="height:40px;width:auto;object-fit:contain;display:block;">
                    @else
                        <img src="{{ asset('image.png') }}"
                             alt="{{ $appStoreName ?? 'Store' }}"
                             style="height:40px;width:auto;object-fit:contain;display:block;">
                    @endif
                </a>
                <p>Nigeria's trusted destination for premium electronics and home appliances.</p>
            </div>

            {{-- Shop col --}}
            <div class="footer__col">
                <h4>Shop</h4>
                <ul>
                    @foreach($topLevelCategories->take(6) as $footerCat)
                        <li>
                            <a href="{{ route('category.show', \Str::slug(str_replace(['/', '&', '+'], ' ', $footerCat->name))) }}">
                                {{ $footerCat->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Services col --}}
            <div class="footer__col">
                <h4>Services</h4>
                <ul>
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('store.locations') }}">Our Stores</a></li>
                    <li><a href="{{ route('store') }}">Store Locator</a></li>
                    <li><a href="{{ route('blog.index') }}">Blog</a></li>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                </ul>
            </div>

            {{-- Customer col --}}
            <div class="footer__col">
                <h4>Customer</h4>
                <ul>
                    <li><a href="{{ route('account.index') }}">My Account</a></li>
                    <li><a href="{{ route('account.orders') }}">My Orders</a></li>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    <li><a href="{{ route('faq') }}">FAQ</a></li>
                </ul>
            </div>

            {{-- Policies col --}}
            <div class="footer__col">
                <h4>Policies</h4>
                <ul>
                    <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}">Terms of Use</a></li>
                    <li><a href="{{ route('faq') }}">FAQ</a></li>
                </ul>
            </div>
        </div>

        <div class="footer__bottom">
            <span>© {{ date('Y') }} {{ $appStoreName ?? 'AlbertinaNG' }}. All rights reserved.</span>
            <span>
                @if($storeAddress)
                    {{ $storeAddress }}
                @else
                    17-18 Zik's Avenue, Uwani, Enugu
                @endif
                @if($storeEmail)
                    &nbsp;·&nbsp; <a href="mailto:{{ $storeEmail }}">{{ $storeEmail }}</a>
                @else
                    &nbsp;·&nbsp; <a href="mailto:Info@Albertinang.com">Info@Albertinang.com</a>
                @endif
            </span>
        </div>
    </div>
</footer>

{{-- ── SCRIPTS ── --}}
<script>
/* ════════════════════════════════════════════════════════════════
   AlbertinaNG — Global JS v2
   Currency loaded from Laravel API (/currencies)
   with 5-minute sessionStorage cache.
   Currency switcher rendered in header (desktop) + mobile drawer.
════════════════════════════════════════════════════════════════ */

/* ── 1. Bootstrap CURRENCY immediately (empty shell) ── */
;(function () {
    var _r = {}, _s = {}, _n = {}, _cur = 'NGN';

    window.CURRENCY = {
        rates: _r, symbols: _s, current: _cur,

        set: function (code) {
            if (!_r[code]) return;
            _cur = code; this.current = code; window.currentCurrency = code;
            localStorage.setItem('selectedCurrency', code);
            window.dispatchEvent(new CustomEvent('currencyChanged', { detail: { code: code } }));
        },

        convert: function (ngn, to) {
            to = to || _cur;
            if (!_r[to] || _r[to] === 1 || to === 'NGN') return parseFloat(ngn) || 0;
            return (parseFloat(ngn) || 0) / _r[to];
        },

        format: function (ngn, to) {
            to = to || _cur;
            var val = this.convert(ngn, to);
            var sym = _s[to] || '₦';
            return sym + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    };

    // Legacy aliases
    window.currentCurrency = _cur;
    window.currencyRates   = _r;
    window.currencySymbols = _s;
    window.convertCurrency = function (a, f, t) { return window.CURRENCY.convert(a, t || f); };
    window.formatCurrency  = function (a, c)    { return window.CURRENCY.format(a, c); };
    window.addEventListener('currencyChanged', function (e) { window.currentCurrency = e.detail.code; });

    /* ── 2. Fetch from backend or sessionStorage cache ── */
    var CACHE_KEY = 'abn_currencies_v2';
    var CACHE_TTL = 5 * 60 * 1000;
    var saved = (localStorage.getItem('selectedCurrency') || 'NGN').replace(/"/g,'').trim().toUpperCase();

    var _pendingList = null;

    /* Render currency options into both the desktop menu and mobile drawer */
    function buildSelect(list) {
        var desktopList = document.getElementById('currencyMenuList');
        var mobileList  = document.getElementById('mobileCurrencyList');
        var btn         = document.getElementById('currencyBtn');
        if (!desktopList && !mobileList) return false;

        var optionsHtml = list.map(function (c) {
            return '<button type="button" data-code="' + c.code + '"'
                 + (c.code === _cur ? ' class="active"' : '') + '>'
                 + '<span class="cur-sym">' + c.symbol + '</span>'
                 + '<span>' + c.code + '</span></button>';
        }).join('');

        if (desktopList) desktopList.innerHTML = optionsHtml;
        if (mobileList)  mobileList.innerHTML  = optionsHtml;

        // Badge on desktop globe button showing the active code
        if (btn && !btn.querySelector('.currency-btn-label')) {
            var lbl = document.createElement('span');
            lbl.className = 'currency-btn-label';
            btn.appendChild(lbl);
        }
        var lblEl = btn && btn.querySelector('.currency-btn-label');
        if (lblEl) lblEl.textContent = _cur;

        // Mobile pill code label
        var mCode = document.getElementById('mobileCurCode');
        if (mCode) mCode.textContent = _cur;

        function refreshActive() {
            document.querySelectorAll('#currencyMenuList button[data-code], #mobileCurrencyList button[data-code]')
                .forEach(function (x) {
                    x.classList.toggle('active', x.dataset.code === window.CURRENCY.current);
                });
            if (lblEl) lblEl.textContent = window.CURRENCY.current;
            var mc = document.getElementById('mobileCurCode');
            if (mc) mc.textContent = window.CURRENCY.current;
        }

        // Keep the visible selector (active dot, globe badge, mobile pill code)
        // in sync with *every* currency change — not just clicks on these
        // buttons. CURRENCY.set() always dispatches 'currencyChanged', whether
        // it was triggered by a manual click below or by the IP auto-detect
        // further down this file, so this is what makes auto-detected currency
        // actually show up in the UI instead of only affecting prices.
        window.addEventListener('currencyChanged', refreshActive);
        refreshActive();

        // Shared click handler for any currency button (desktop or mobile)
        document.querySelectorAll('#currencyMenuList button[data-code], #mobileCurrencyList button[data-code]')
            .forEach(function (b) {
                b.addEventListener('click', function () {
                    localStorage.setItem('al_cur_manual', '1'); // user explicitly chose — disable auto-detect
                    window.CURRENCY.set(this.dataset.code);
                    // Sync manual choice to Laravel session
                    var csrf = document.querySelector('meta[name="csrf-token"]');
                    fetch('/currency/set', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf ? csrf.getAttribute('content') : '',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ code: this.dataset.code })
                    }).catch(function () {});
                    window.showToast && window.showToast('Currency: ' + window.CURRENCY.current, 'fa-globe');
                    window.updatePricesOnPage && window.updatePricesOnPage();
                    refreshActive();
                    // close desktop menu if open
                    var menu = document.getElementById('currencyMenu');
                    if (menu) menu.classList.remove('open');
                    var cb = document.getElementById('currencyBtn');
                    if (cb) cb.setAttribute('aria-expanded', 'false');
                    // close mobile pill menu if open
                    var mMenu = document.getElementById('mobileCurrencyList');
                    if (mMenu) mMenu.classList.remove('open');
                    var mBtn = document.getElementById('mobileCurBtn');
                    if (mBtn) mBtn.setAttribute('aria-expanded', 'false');
                });
            });

        return true;
    }

    function apply(list) {
        list.forEach(function (c) {
            _r[c.code] = parseFloat(c.rate_to_ngn) || 1;
            _s[c.code] = c.symbol;
            _n[c.code] = c.name;
        });
        _r['NGN'] = _r['NGN'] || 1; _s['NGN'] = _s['NGN'] || '₦';

        _cur = _r[saved] ? saved : 'NGN';
        window.CURRENCY.current = _cur; window.currentCurrency = _cur;

        // Try to build immediately; if DOM not ready, store for later
        var built = buildSelect(list);
        if (!built) { _pendingList = list; }

        window.dispatchEvent(new CustomEvent('currencyChanged', { detail: { code: _cur } }));
    }

    // Once DOM is ready, flush any pending list
    document.addEventListener('DOMContentLoaded', function () {
        if (_pendingList) { buildSelect(_pendingList); _pendingList = null; }
    });

    function load() {
        try {
            var c = sessionStorage.getItem(CACHE_KEY);
            if (c) {
                var obj = JSON.parse(c);
                if (obj.ts && (Date.now() - obj.ts) < CACHE_TTL && obj.data && obj.data.length) {
                    apply(obj.data); return;
                }
            }
        } catch(e) {}

        fetch('/currencies', { headers: { 'Accept': 'application/json' } })
            .then(function(r){ return r.ok ? r.json() : Promise.reject(r.status); })
            .then(function(data){
                try { sessionStorage.setItem(CACHE_KEY, JSON.stringify({ ts: Date.now(), data: data })); } catch(e){}
                apply(data);
            })
            .catch(function(){
                // Graceful fallback — NGN only
                apply([{ code:'NGN', symbol:'₦', name:'Nigerian Naira', rate_to_ngn:1, is_base:true }]);
            });
    }

    load();

    /* ── IP currency auto-detection ──────────────────────────────────────────
       Geolocates on every load. A manual currency pick is respected as long as
       the visitor stays in the same country; when the country CHANGES (travel /
       VPN switch) we re-detect and override the stale manual pick with the new
       location's currency. On any geo failure we do NOTHING persistent, so a
       one-off block (ad blocker, flaky network) never permanently disables
       detection — it simply retries on the next load. */
    ;(function () {
        var MANUAL_KEY  = 'al_cur_manual';  // '1' once the user explicitly picks a currency
        var COUNTRY_KEY = 'al_cur_country'; // last country code we auto-applied
        // NOTE: only map countries to currencies that are actually seeded AND
        // active (see CurrencySeeder / admin Currencies). Mapping to a currency
        // the store cannot display just makes commitCurrency() silently fall back
        // to USD, which looks like a bug. Active set today:
        //   NGN, USD, EUR, GBP, CAD, AUD, GHS, ZAR, KES
        // Add a country row here only after its currency exists and is active.
        var COUNTRY_MAP = {
            // Nigeria
            'NG': 'NGN',
            // United Kingdom
            'GB': 'GBP',
            // United States (+ economies that transact in USD)
            'US': 'USD', 'EC': 'USD', 'SV': 'USD', 'PA': 'USD', 'ZW': 'USD',
            // Canada
            'CA': 'CAD',
            // Australia
            'AU': 'AUD',
            // Eurozone countries
            'AT': 'EUR', 'BE': 'EUR', 'CY': 'EUR', 'EE': 'EUR',
            'FI': 'EUR', 'FR': 'EUR', 'DE': 'EUR', 'GR': 'EUR',
            'IE': 'EUR', 'IT': 'EUR', 'LV': 'EUR', 'LT': 'EUR',
            'LU': 'EUR', 'MT': 'EUR', 'NL': 'EUR', 'PT': 'EUR',
            'SK': 'EUR', 'SI': 'EUR', 'ES': 'EUR', 'HR': 'EUR',
            // Other European (EUR de facto / closely tied)
            'AD': 'EUR', 'MC': 'EUR', 'SM': 'EUR', 'VA': 'EUR',
            'ME': 'EUR', 'XK': 'EUR',
            // Africa (active currencies only)
            'GH': 'GHS',
            'ZA': 'ZAR',
            'KE': 'KES',
        };

        // We always geolocate so a CHANGED country (travel / VPN) is noticed, but a
        // manual choice is respected as long as the country stays the same.
        fetch('https://ipwho.is/', { mode: 'cors' })
            .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
            .then(function (geo) {
                var country = geo && geo.country_code;
                if (!country) return; // malformed payload — leave state, retry next load

                var lastCountry = localStorage.getItem(COUNTRY_KEY);

                if (lastCountry === country) {
                    // Same location as last time → respect whatever is set (a manual
                    // pick, or the auto currency already stored). Nothing to do.
                    return;
                }
                if (lastCountry) {
                    // Country CHANGED → a manual pick from the previous location no
                    // longer applies; re-detect and override it below.
                    localStorage.removeItem(MANUAL_KEY);
                } else if (localStorage.getItem(MANUAL_KEY)) {
                    // First detection ever, but the user already chose manually before
                    // we resolved → honour it; just pin the baseline country.
                    localStorage.setItem(COUNTRY_KEY, country);
                    return;
                }

                var code = COUNTRY_MAP[country] || 'USD';

                function commitCurrency(c) {
                    // Only commit a currency we can actually display. If not,
                    // fall back to USD (always seeded); last resort is NGN (base).
                    var rates = window.CURRENCY && window.CURRENCY.rates;
                    var final = (rates && rates[c]) ? c : ((rates && rates['USD']) ? 'USD' : 'NGN');

                    localStorage.setItem(COUNTRY_KEY, country); // remember what we detected
                    localStorage.setItem('selectedCurrency', final);
                    var csrf = document.querySelector('meta[name="csrf-token"]');
                    fetch('/currency/set', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf ? csrf.getAttribute('content') : '',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ code: final })
                    }).catch(function () {});
                    // Flip the live currency (selector + prices) unconditionally.
                    window.CURRENCY.set(final);
                    window.updatePricesOnPage && window.updatePricesOnPage();
                }

                if (window.CURRENCY && window.CURRENCY.rates && Object.keys(window.CURRENCY.rates).length > 1) {
                    // Rates already loaded — apply immediately
                    commitCurrency(code);
                } else {
                    // Rates not ready yet — wait for the first currencyChanged event
                    window.addEventListener('currencyChanged', function applyAuto() {
                        window.removeEventListener('currencyChanged', applyAuto);
                        commitCurrency(code);
                    });
                }
            })
            .catch(function () {
                // ipwho.is unavailable/blocked — do nothing persistent so the next
                // page load retries instead of disabling auto-detect forever.
            });
    })();
})();


/* ── DOM Ready ── */
document.addEventListener('DOMContentLoaded', function () {

    /* ── Sticky header shadow + close-on-scroll ── */
    window.addEventListener('scroll', function () {
        var h = document.getElementById('main-nav');
        if (h) h.classList.toggle('scrolled', window.scrollY > 40);
        /* close all header dropdowns on scroll */
        closeHeaderDropdowns();
        var catMenu = document.getElementById('catMenu');
        var catBtn  = document.getElementById('catBtn');
        if (catMenu && catMenu.classList.contains('open')) {
            catMenu.classList.remove('open');
            if (catBtn) catBtn.setAttribute('aria-expanded','false');
            var sc = document.getElementById('catSubContainer');
            if (sc) { sc.classList.remove('has-panel', 'wide'); sc.innerHTML = ''; }
        }
    }, { passive: true });

    /* ── Toast — provided by partials/notify.blade.php ── */

    /* ── Cart ── */
    window.getCart  = function () { try { return JSON.parse(localStorage.getItem('shoppingCart') || '[]'); } catch(e){ return []; } };
    window.saveCart = function (cart) { localStorage.setItem('shoppingCart', JSON.stringify(cart)); updateMiniCart(); };

    window.addToCart = function (id, name, priceNgn, imageUrl, requiresTruck) {
        var cart = window.getCart();
        var idx  = cart.findIndex(function(i){ return i.id === id; });
        if (idx > -1) { cart[idx].quantity++; } else { cart.push({ id:id, name:name, basePriceNgn:priceNgn, image:imageUrl, quantity:1, requires_truck: requiresTruck === true || requiresTruck === 'true' }); }
        window.saveCart(cart);
        // bump badge
        var badge = document.getElementById('cartCount');
        if (badge) { badge.classList.remove('bump'); void badge.offsetWidth; badge.classList.add('bump'); }
        window.showToast(String(name).substring(0,30) + '… added to cart', 'fa-check');
    };

    function updateMiniCart() {
        var cart      = window.getCart();
        var countEl   = document.getElementById('cartCount');
        var mCountEl  = document.getElementById('miniCartCount');
        var itemsEl   = document.getElementById('miniCartItems');
        var totalEl   = document.getElementById('miniCartTotal');
        var footEl    = document.getElementById('miniCartFoot');
        var total     = cart.reduce(function(s,i){ return s + (parseFloat(i.basePriceNgn)||0)*(parseInt(i.quantity)||1); }, 0);
        var itemCount = cart.reduce(function(s,i){ return s + (parseInt(i.quantity)||1); }, 0);
        if (countEl)  countEl.textContent  = itemCount;
        if (mCountEl) mCountEl.textContent = itemCount;
        if (totalEl)  totalEl.textContent  = window.CURRENCY.format(total);
        if (!itemsEl) return;
        if (cart.length === 0) {
            itemsEl.innerHTML = '<div class="empty-cart"><i class="fas fa-shopping-bag"></i><p>Your cart is empty</p><a href="/">Start Shopping</a></div>';
            if (footEl) footEl.style.display = 'none';
            return;
        }
        if (footEl) footEl.style.display = 'block';
        itemsEl.innerHTML = cart.map(function(item){
            var price = parseFloat(item.basePriceNgn) || 0;
            var img   = item.image || 'https://placehold.co/54x54/eef3e8/3d8012?text=+';
            return '<div class="mini-cart__item">'
                 + '<img src="' + img + '" alt="' + item.name + '" loading="lazy" onerror="this.src=\'https://placehold.co/54x54/eef3e8/3d8012?text=+\'">'
                 + '<div class="mini-cart__item-info">'
                 + '<div class="mini-cart__item-name">' + item.name + '</div>'
                 + '<div class="mini-cart__item-price">' + window.CURRENCY.format(price) + '</div>'
                 + '<div class="mini-cart__item-qty">Qty: ' + (item.quantity||1) + '</div>'
                 + '</div></div>';
        }).join('');
    }

    window.updatePricesOnPage = function () {
        document.querySelectorAll('[data-price-ngn],[data-base-price-ngn]').forEach(function(el){
            var ngn = parseFloat(el.dataset.priceNgn || el.dataset.basePriceNgn);
            if (!isNaN(ngn)) el.textContent = window.CURRENCY.format(ngn);
        });
        document.querySelectorAll('[data-old-price-ngn]').forEach(function(el){
            var ngn = parseFloat(el.dataset.oldPriceNgn);
            if (!isNaN(ngn)) el.textContent = window.CURRENCY.format(ngn);
        });
        updateMiniCart();
        window.updateCheckoutAmounts && window.updateCheckoutAmounts();
        window.updateCartAmounts     && window.updateCartAmounts();
    };

    window.addEventListener('currencyChanged', function(){
        updateMiniCart();
        window.updatePricesOnPage && window.updatePricesOnPage();
    });
    updateMiniCart();

    /* ── Mobile menu (accordion) ── */
    var mobileMenuBtn   = document.getElementById('mobileMenuBtn');
    var mobileMenu      = document.getElementById('mobileMenu');
    var mobileMenuClose = document.getElementById('mobileMenuClose');
    var mobileOverlay   = document.getElementById('mobileOverlay');

    function openMobileMenu() {
        mobileMenu    && mobileMenu.classList.add('active');
        mobileOverlay && mobileOverlay.classList.add('active');
        mobileMenuBtn && mobileMenuBtn.setAttribute('aria-expanded','true');
        document.body.style.overflow = 'hidden';
    }
    function closeMobileMenu() {
        /* collapse all open accordions */
        document.querySelectorAll('.mobile-cat-subs.open').forEach(function(el){ el.classList.remove('open'); });
        document.querySelectorAll('.mobile-cat-row.has-open').forEach(function(el){ el.classList.remove('has-open'); });
        document.querySelectorAll('.mobile-cat-toggle').forEach(function(btn){
            btn.setAttribute('aria-expanded','false');
            var icon = btn.querySelector('i');
            if (icon) icon.className = 'fas fa-plus';
        });
        mobileMenu    && mobileMenu.classList.remove('active');
        mobileOverlay && mobileOverlay.classList.remove('active');
        mobileMenuBtn && mobileMenuBtn.setAttribute('aria-expanded','false');
        document.body.style.overflow = '';
    }

    mobileMenuBtn   && mobileMenuBtn.addEventListener('click', openMobileMenu);
    mobileMenuClose && mobileMenuClose.addEventListener('click', closeMobileMenu);
    mobileOverlay   && mobileOverlay.addEventListener('click', closeMobileMenu);
    document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeMobileMenu(); });

    /* Mobile sub-category accordion */
    document.querySelectorAll('.mobile-cat-toggle').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.stopPropagation();
            var targetId = this.dataset.target;
            var subs = document.getElementById(targetId);
            if (!subs) return;
            var row  = this.closest('.mobile-cat-row');
            var open = subs.classList.toggle('open');
            row.classList.toggle('has-open', open);
            this.setAttribute('aria-expanded', open ? 'true' : 'false');
            this.querySelector('i').className = open ? 'fas fa-minus' : 'fas fa-plus';
        });
    });

    /* ── Desktop categories flyout (hover-intent) ── */
    var catBtn       = document.getElementById('catBtn');
    var catMenu      = document.getElementById('catMenu');
    var subContainer = document.getElementById('catSubContainer');
    var _catHoverTimer;

    function showSubPanel(id) {
        var tpl = document.getElementById('subpanel-' + id);
        if (!tpl) { clearSubPanel(); return; }
        var subCount = tpl.querySelectorAll('.sub-links-grid a').length;
        var clone = tpl.cloneNode(true);
        clone.removeAttribute('id'); clone.removeAttribute('aria-hidden');
        clone.style.display = ''; clone.classList.add('visible');
        subContainer.innerHTML = ''; subContainer.appendChild(clone);
        subContainer.classList.toggle('wide', subCount > 7);
        subContainer.classList.add('has-panel');
    }
    function clearSubPanel() {
        subContainer.classList.remove('has-panel', 'wide');
        /* delay clearing content until collapse animation finishes */
        setTimeout(function(){ if (!subContainer.classList.contains('has-panel')) subContainer.innerHTML = ''; }, 230);
        catMenu.querySelectorAll('.cat-dropdown__item').forEach(function(i){ i.classList.remove('active'); });
    }
    function openCatMenu() {
        clearTimeout(_catHoverTimer);
        catMenu.classList.add('open');
        catBtn.setAttribute('aria-expanded', 'true');
    }
    function closeCatMenuNow() {
        catMenu.classList.remove('open');
        catBtn.setAttribute('aria-expanded', 'false');
        subContainer.classList.remove('has-panel', 'wide');
        subContainer.innerHTML = '';
        catMenu.querySelectorAll('.cat-dropdown__item').forEach(function(i){ i.classList.remove('active'); });
    }
    function scheduleCatClose() {
        _catHoverTimer = setTimeout(closeCatMenuNow, 160);
    }

    if (catBtn && catMenu) {
        var catWrap = catBtn.closest('.cat-dropdown-wrap');
        if (catWrap) {
            catWrap.addEventListener('mouseenter', openCatMenu);
            catWrap.addEventListener('mouseleave', scheduleCatClose);
        }
        /* Keyboard / click toggle */
        catBtn.addEventListener('click', function(e){
            e.stopPropagation();
            catMenu.classList.contains('open') ? closeCatMenuNow() : openCatMenu();
        });
        catMenu.querySelectorAll('.cat-dropdown__item').forEach(function(item){
            item.addEventListener('mouseenter', function(){
                catMenu.querySelectorAll('.cat-dropdown__item').forEach(function(i){ i.classList.remove('active'); });
                var id = item.dataset.catId;
                if (id !== undefined) { item.classList.add('active'); showSubPanel(id); } else { clearSubPanel(); }
            });
        });
        document.addEventListener('click', function(e){
            if (!catMenu.contains(e.target) && !catBtn.contains(e.target)) {
                clearTimeout(_catHoverTimer); closeCatMenuNow();
            }
        });
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape' && catMenu.classList.contains('open')) { closeCatMenuNow(); catBtn.focus(); }
        });
    }

    /* ── Cart hover element + suppression helper ── */
    var cartWrapEl = document.querySelector('.cart-wrap');

    function suppressCartHover() {
        if (cartWrapEl) cartWrapEl.classList.add('cart-suppressed');
    }
    if (cartWrapEl) {
        // Hovering the cart closes the click-dropdowns…
        cartWrapEl.addEventListener('mouseenter', function () {
            closeHeaderDropdowns(); // no "keep" arg → closes info, currency, and user
        });
        // …and once the pointer leaves the cart, allow its hover-menu to work again.
        cartWrapEl.addEventListener('mouseleave', function () {
            cartWrapEl.classList.remove('cart-suppressed');
        });
    }

    /* ── Close all header dropdowns except an optional one to keep open ── */
    function closeHeaderDropdowns(keep) {
        [
            ['infoMenu',     'infoBtn'],
            ['currencyMenu', 'currencyBtn'],
            ['userMenu',     'userBtn'],
        ].forEach(function (pair) {
            if (pair[0] === keep) return;
            var menu = document.getElementById(pair[0]);
            var btn  = document.getElementById(pair[1]);
            if (menu) menu.classList.remove('open');
            if (btn)  btn.setAttribute('aria-expanded', 'false');
        });
    }

    /* ── Info dropdown ── */
    var infoBtn  = document.getElementById('infoBtn');
    var infoMenu = document.getElementById('infoMenu');
    if (infoBtn && infoMenu) {
        infoBtn.addEventListener('click', function(e){
            e.stopPropagation();
            var willOpen = !infoMenu.classList.contains('open');
            closeHeaderDropdowns('infoMenu');
            suppressCartHover();
            infoMenu.classList.toggle('open', willOpen);
            infoBtn.setAttribute('aria-expanded', String(willOpen));
        });
        document.addEventListener('click', function(e){
            if (!infoMenu.contains(e.target) && !infoBtn.contains(e.target)) {
                infoMenu.classList.remove('open'); infoBtn.setAttribute('aria-expanded','false');
            }
        });
    }

    /* ── Currency dropdown (desktop) ── */
    var currencyBtn  = document.getElementById('currencyBtn');
    var currencyMenu = document.getElementById('currencyMenu');
    if (currencyBtn && currencyMenu) {
        currencyBtn.addEventListener('click', function(e){
            e.stopPropagation();
            var willOpen = !currencyMenu.classList.contains('open');
            closeHeaderDropdowns('currencyMenu');
            suppressCartHover();
            currencyMenu.classList.toggle('open', willOpen);
            currencyBtn.setAttribute('aria-expanded', String(willOpen));
        });
        document.addEventListener('click', function(e){
            if (!currencyMenu.contains(e.target) && !currencyBtn.contains(e.target)) {
                currencyMenu.classList.remove('open');
                currencyBtn.setAttribute('aria-expanded','false');
            }
        });
    }

    /* ── Currency pill (mobile drawer header) ── */
    var mobileCurBtn  = document.getElementById('mobileCurBtn');
    var mobileCurMenu = document.getElementById('mobileCurrencyList');
    if (mobileCurBtn && mobileCurMenu) {
        mobileCurBtn.addEventListener('click', function(e){
            e.stopPropagation();
            var open = mobileCurMenu.classList.toggle('open');
            mobileCurBtn.setAttribute('aria-expanded', String(open));
        });
        document.addEventListener('click', function(e){
            if (!mobileCurMenu.contains(e.target) && !mobileCurBtn.contains(e.target)) {
                mobileCurMenu.classList.remove('open');
                mobileCurBtn.setAttribute('aria-expanded','false');
            }
        });
    }

    /* ── User dropdown ── */
    var userBtn  = document.getElementById('userBtn');
    var userMenu = document.getElementById('userMenu');
    if (userBtn && userMenu) {
        userBtn.addEventListener('click', function(e){
            e.stopPropagation();
            var willOpen = !userMenu.classList.contains('open');
            closeHeaderDropdowns('userMenu');
            suppressCartHover();
            userMenu.classList.toggle('open', willOpen);
            userBtn.setAttribute('aria-expanded', String(willOpen));
        });
        document.addEventListener('click', function(e){
            if (!userMenu.contains(e.target) && !userBtn.contains(e.target)) {
                userMenu.classList.remove('open'); userBtn.setAttribute('aria-expanded','false');
            }
        });
    }

    /* ── Mobile search toggle ── */
    var mobileSearchBtn = document.getElementById('mobileSearchBtn');
    var header          = document.querySelector('.header');
    if (mobileSearchBtn && header) {
        mobileSearchBtn.addEventListener('click', function(){
            var open = header.classList.toggle('search-open');
            mobileSearchBtn.setAttribute('aria-expanded', String(open));
            if (open) { var inp = document.querySelector('#mobileSearchBar .search-input'); inp && inp.focus(); }
        });
    }

    /* ── Search Suggestions ── */
    function initSearchSuggestions(inputId, suggestionsId, formId) {
        var input       = document.getElementById(inputId);
        var suggestions = document.getElementById(suggestionsId);
        var form        = document.getElementById(formId);

        if (!input || !suggestions) return;

        var debounceTimer;
        var selectedIndex = -1;
        var currentResults = [];

        function closeSuggestions() {
            suggestions.classList.remove('open');
            input.setAttribute('aria-expanded', 'false');
            selectedIndex = -1;
        }

        function openSuggestions() {
            suggestions.classList.add('open');
            input.setAttribute('aria-expanded', 'true');
        }

        function fetchSuggestions(query) {
            clearTimeout(debounceTimer);

            if (!query || query.length < 2) {
                closeSuggestions();
                return;
            }

            debounceTimer = setTimeout(function() {
                suggestions.innerHTML = '<div class="search-suggestions__loading"><i class="fas fa-spinner fa-pulse"></i> Searching...</div>';
                openSuggestions();

                fetch('/search-suggestions?query=' + encodeURIComponent(query), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(response) {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(function(data) {
                    currentResults = data || [];
                    renderSuggestions(currentResults);
                })
                .catch(function(error) {
                    console.error('Search suggestions error:', error);
                    suggestions.innerHTML = '<div class="search-suggestions__empty">Unable to load suggestions</div>';
                });
            }, 300);
        }

        function renderSuggestions(results) {
            selectedIndex = -1;

            if (!results || results.length === 0) {
                suggestions.innerHTML = '<div class="search-suggestions__empty">No suggestions found</div>';
                return;
            }

            var html = '';
            results.forEach(function(item, index) {
                var icon = item.type === 'product' ? 'fa-box' : 'fa-folder';
                var typeLabel = item.type === 'product' ? 'Product' : 'Category';
                var url = item.type === 'product'
                    ? '/product/' + item.id
                    : '/category/' + encodeURIComponent(item.name);

                html += '<div class="search-suggestions__item" data-index="' + index + '" data-url="' + url + '">';
                // Product suggestions carry a thumbnail; fall back to the icon box
                // (and to a placeholder if the image URL 404s).
                if (item.image) {
                    html += '<div class="sug-img-box"><img src="' + escapeHtml(item.image) + '" alt="" ' +
                            'onerror="this.onerror=null;this.src=\'https://placehold.co/40x40/eef3e8/3d8012?text=%20\';"></div>';
                } else {
                    html += '<div class="sug-icon-box"><i class="fas ' + icon + '"></i></div>';
                }
                html += '<span class="sug-name">' + escapeHtml(item.name) + '</span>';
                html += '<span class="sug-type">' + typeLabel + '</span>';
                html += '</div>';
            });

            html += '<div class="search-suggestions__view-all" id="' + suggestionsId + 'ViewAll">';
            html += '<i class="fas fa-search"></i> View all results for "' + escapeHtml(input.value) + '"';
            html += '</div>';

            suggestions.innerHTML = html;

            // Click handlers for items
            suggestions.querySelectorAll('.search-suggestions__item').forEach(function(item) {
                item.addEventListener('click', function() {
                    window.location.href = this.dataset.url;
                });
            });

            // View all handler
            var viewAllBtn = document.getElementById(suggestionsId + 'ViewAll');
            if (viewAllBtn) {
                viewAllBtn.addEventListener('click', function() {
                    form.submit();
                });
            }
        }

        function escapeHtml(text) {
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Input event
        input.addEventListener('input', function() {
            fetchSuggestions(this.value.trim());
        });

        // Focus event
        input.addEventListener('focus', function() {
            if (this.value.trim().length >= 2 && currentResults.length > 0) {
                openSuggestions();
            }
        });

        // Keyboard navigation
        input.addEventListener('keydown', function(e) {
            var items = suggestions.querySelectorAll('.search-suggestions__item');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (items.length > 0) {
                    selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                    updateSelection(items);
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (items.length > 0) {
                    selectedIndex = Math.max(selectedIndex - 1, 0);
                    updateSelection(items);
                }
            } else if (e.key === 'Enter') {
                if (selectedIndex >= 0 && items[selectedIndex]) {
                    e.preventDefault();
                    items[selectedIndex].click();
                }
            } else if (e.key === 'Escape') {
                closeSuggestions();
            }
        });

        function updateSelection(items) {
            items.forEach(function(item, index) {
                item.classList.toggle('active', index === selectedIndex);
            });
            // Scroll selected into view
            if (items[selectedIndex]) {
                items[selectedIndex].scrollIntoView({ block: 'nearest' });
            }
        }

        // Close on outside click
        document.addEventListener('click', function(e) {
            if (!suggestions.contains(e.target) && e.target !== input) {
                closeSuggestions();
            }
        });
    }

    // Initialize both desktop and mobile search suggestions
    initSearchSuggestions('desktopSearchInput', 'desktopSearchSuggestions', 'desktopSearchForm');
    initSearchSuggestions('mobileSearchInput', 'mobileSearchSuggestions', 'mobileSearchForm');

    /* ── Slider arrows ── */
    document.querySelectorAll('.sarrow').forEach(function(btn){
        btn.addEventListener('click', function(){
            var row = document.getElementById(btn.dataset.target);
            if (row) row.scrollBy({ left: parseInt(btn.dataset.dir) * 420, behavior: 'smooth' });
        });
    });

    /* ── Scroll dots ── */
    window.initScrollDots = function (rowId, dotsId) {
        var row  = document.getElementById(rowId);
        var wrap = document.getElementById(dotsId);
        if (!row || !wrap) return;
        function build() {
            var pages = Math.ceil(row.scrollWidth / (row.clientWidth || 1));
            wrap.innerHTML = '';
            for (var i = 0; i < Math.min(pages, 10); i++) {
                var d = document.createElement('span');
                d.className = 'scroll-dot' + (i === 0 ? ' active' : '');
                (function(idx){ d.addEventListener('click', function(){ row.scrollTo({ left: idx * row.clientWidth, behavior: 'smooth' }); }); })(i);
                wrap.appendChild(d);
            }
        }
        row.addEventListener('scroll', function(){
            var idx = Math.round(row.scrollLeft / row.clientWidth);
            wrap.querySelectorAll('.scroll-dot').forEach(function(d, i){ d.classList.toggle('active', i === idx); });
        }, { passive: true });
        build();
        window.addEventListener('resize', build, { passive: true });
    };

    /* ── Auto-dismiss flash alerts ── */
    document.querySelectorAll('.alert-flash').forEach(function(el){
        setTimeout(function(){ el.style.opacity = '0'; setTimeout(function(){ el.remove(); }, 400); }, 4000);
    });

}); // end DOMContentLoaded
</script>
@stack('scripts')

<script>
/* ─────────────────────────────────────────────
   CUSTOM SELECT  (.cs-*)
   Replaces all <select> elements with a fully
   custom dropdown — no browser blue popup.
───────────────────────────────────────────── */
(function(){
    var CHEVRON = '<svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1l5 5 5-5" stroke="#6b7280" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    function initCS(sel) {
        if (sel.dataset.csInit) return;
        sel.dataset.csInit = '1';

        var wrap = document.createElement('div');
        wrap.className = 'cs-wrap';

        /* preserve layout-relevant computed styles (margin, max-width) */
        var cs = window.getComputedStyle(sel);
        ['marginTop','marginRight','marginBottom','marginLeft','maxWidth','minWidth'].forEach(function(p) {
            var v = cs[p];
            if (v && v !== '0px' && v !== 'none' && v !== 'auto') wrap.style[p] = v;
        });

        sel.parentNode.insertBefore(wrap, sel);

        var trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'cs-trigger';
        trigger.setAttribute('aria-haspopup', 'listbox');
        trigger.setAttribute('aria-expanded', 'false');
        if (sel.disabled) trigger.disabled = true;

        var valueSpan = document.createElement('span');
        valueSpan.className = 'cs-value';

        var chevron = document.createElement('span');
        chevron.className = 'cs-chevron';
        chevron.innerHTML = CHEVRON;

        trigger.appendChild(valueSpan);
        trigger.appendChild(chevron);

        var dropdown = document.createElement('div');
        dropdown.className = 'cs-dropdown';
        dropdown.setAttribute('role', 'listbox');

        function buildOptions() {
            dropdown.innerHTML = '';
            Array.from(sel.options).forEach(function(opt) {
                var item = document.createElement('div');
                item.className = 'cs-option';
                item.setAttribute('role', 'option');
                item.dataset.value = opt.value;
                item.textContent = opt.text;
                if (opt.disabled) {
                    item.classList.add('cs-option--disabled');
                } else {
                    item.addEventListener('click', function() {
                        pickOption(opt.value, opt.text, item);
                    });
                }
                if (opt.selected) {
                    item.classList.add('cs-option--selected');
                }
                dropdown.appendChild(item);
            });
        }

        function updateDisplay() {
            var idx = sel.selectedIndex;
            var opt = sel.options[idx];
            if (opt) {
                valueSpan.textContent = opt.text;
                valueSpan.classList.toggle('cs-value--placeholder', !opt.value);
            }
        }

        function pickOption(value, text, item) {
            sel.value = value;
            sel.dispatchEvent(new Event('change', { bubbles: true }));
            dropdown.querySelectorAll('.cs-option--selected').forEach(function(el) {
                el.classList.remove('cs-option--selected');
            });
            item.classList.add('cs-option--selected');
            valueSpan.textContent = text;
            valueSpan.classList.toggle('cs-value--placeholder', !value);
            close();
        }

        function positionDropdown() {
            dropdown.style.top    = '100%';
            dropdown.style.bottom = 'auto';
            dropdown.style.left   = '0';
            dropdown.style.right  = '0';
            dropdown.style.width  = '';
            dropdown.style.maxHeight = '226px';
        }

        function open() {
            document.querySelectorAll('.cs-wrap.cs-open').forEach(function(w) {
                if (w !== wrap) {
                    w.classList.remove('cs-open');
                    var t = w.querySelector('.cs-trigger');
                    if (t) t.setAttribute('aria-expanded', 'false');
                }
            });
            /* also close any open date pickers */
            document.querySelectorAll('.cdp-wrap.cdp-open').forEach(function(w) { w.classList.remove('cdp-open'); });
            wrap.classList.add('cs-open');
            trigger.setAttribute('aria-expanded', 'true');
            positionDropdown();
            var sel2 = dropdown.querySelector('.cs-option--selected');
            if (sel2) sel2.scrollIntoView({ block: 'nearest' });
        }

        function close() {
            wrap.classList.remove('cs-open');
            trigger.setAttribute('aria-expanded', 'false');
            dropdown.querySelectorAll('.cs-option--focused').forEach(function(el) { el.classList.remove('cs-option--focused'); });
        }

        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            wrap.classList.contains('cs-open') ? close() : open();
        });

        trigger.addEventListener('keydown', function(e) {
            var opts = Array.from(dropdown.querySelectorAll('.cs-option:not(.cs-option--disabled)'));
            var focusedIdx = opts.findIndex(function(o) { return o.classList.contains('cs-option--focused') || o.classList.contains('cs-option--selected'); });

            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (wrap.classList.contains('cs-open')) {
                    var focused = dropdown.querySelector('.cs-option--focused');
                    if (focused) focused.click();
                    else close();
                } else {
                    open();
                }
            } else if (e.key === 'Escape') {
                close();
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!wrap.classList.contains('cs-open')) { open(); return; }
                var next = opts[focusedIdx + 1] || opts[0];
                focusItem(next, opts);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (!wrap.classList.contains('cs-open')) { open(); return; }
                var prev = opts[focusedIdx - 1] || opts[opts.length - 1];
                focusItem(prev, opts);
            }
        });

        function focusItem(item, opts) {
            opts.forEach(function(o) { o.classList.remove('cs-option--focused'); });
            item.classList.add('cs-option--focused');
            item.scrollIntoView({ block: 'nearest' });
        }

        /* hide native select — keep it in the DOM for form submission */
        sel.style.cssText = 'display:none !important; position:absolute; opacity:0; pointer-events:none;';
        sel.setAttribute('aria-hidden', 'true');
        sel.tabIndex = -1;

        buildOptions();
        updateDisplay();

        /* Watch for dynamic option changes (e.g. AJAX-populated state/location selects)
           and disabled attribute toggling — rebuild the panel when either changes */
        var observer = new MutationObserver(function() {
            buildOptions();
            updateDisplay();
            trigger.disabled = sel.disabled;
            wrap.classList.toggle('cs-disabled', sel.disabled);
        });
        observer.observe(sel, {
            childList: true,        /* option additions / removals (innerHTML = '...') */
            subtree: true,          /* changes within option text nodes */
            attributes: true,       /* disabled attribute on the select itself */
            attributeFilter: ['disabled']
        });

        wrap.appendChild(trigger);
        wrap.appendChild(dropdown);
        wrap.appendChild(sel);
    }

    /* close on outside click or scroll */
    function closeAllCS() {
        document.querySelectorAll('.cs-wrap.cs-open').forEach(function(w) {
            w.classList.remove('cs-open');
            var t = w.querySelector('.cs-trigger');
            if (t) t.setAttribute('aria-expanded', 'false');
        });
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.cs-wrap')) closeAllCS();
    });

    window.addEventListener('scroll', closeAllCS, { passive: true });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.cs-wrap.cs-open').forEach(function(w) {
                w.classList.remove('cs-open');
            });
        }
    });

    function initAll() {
        document.querySelectorAll('select:not([data-cs-skip])').forEach(function(sel) {
            if (!sel.dataset.csInit) initCS(sel);
        });
    }

    document.addEventListener('DOMContentLoaded', initAll);
    window.initCustomSelects = initAll;
})();

/* ─────────────────────────────────────────────
   CUSTOM DATE PICKER  (.cdp-*)
   Replaces all <input type="date"> with a
   custom calendar panel — no browser chrome.
───────────────────────────────────────────── */
(function(){
    var MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var DAYS   = ['Su','Mo','Tu','We','Th','Fr','Sa'];

    var CAL_ICON = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>';

    function pad(n) { return String(n).padStart(2, '0'); }

    function parseISO(str) {
        if (!str) return null;
        var parts = str.split('-');
        if (parts.length !== 3) return null;
        var d = new Date(+parts[0], +parts[1] - 1, +parts[2]);
        return isNaN(d) ? null : d;
    }

    function toISO(d) {
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }

    function formatDisplay(d) {
        if (!d) return '';
        return pad(d.getDate()) + ' ' + MONTHS[d.getMonth()].slice(0, 3) + ' ' + d.getFullYear();
    }

    function initCDP(input) {
        if (input.dataset.cdpInit) return;
        input.dataset.cdpInit = '1';

        var state = {
            selected: parseISO(input.value),
            viewYear: 0,
            viewMonth: 0
        };
        var now = new Date();
        state.viewYear  = state.selected ? state.selected.getFullYear() : now.getFullYear();
        state.viewMonth = state.selected ? state.selected.getMonth()    : now.getMonth();

        var wrap = document.createElement('div');
        wrap.className = 'cdp-wrap';
        input.parentNode.insertBefore(wrap, input);

        /* hidden input carries the value for form submission */
        var hidden = document.createElement('input');
        hidden.type  = 'hidden';
        hidden.name  = input.name;
        hidden.value = input.value || '';
        input.name   = '';           /* prevent double submit */
        input.style.cssText = 'display:none !important; position:absolute; opacity:0; pointer-events:none;';

        var trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'cdp-trigger';
        trigger.setAttribute('aria-haspopup', 'dialog');
        trigger.setAttribute('aria-expanded', 'false');

        var textSpan = document.createElement('span');
        textSpan.className = 'cdp-trigger__text';

        var iconSpan = document.createElement('span');
        iconSpan.className = 'cdp-trigger__icon';
        iconSpan.innerHTML = CAL_ICON;

        trigger.appendChild(textSpan);
        trigger.appendChild(iconSpan);

        var panel = document.createElement('div');
        panel.className = 'cdp-panel';
        panel.setAttribute('role', 'dialog');
        panel.setAttribute('aria-label', 'Date picker');

        function updateTrigger() {
            if (state.selected) {
                textSpan.textContent = formatDisplay(state.selected);
                textSpan.style.color = '';
            } else {
                textSpan.textContent = input.placeholder || 'Select a date';
                textSpan.style.color = 'var(--ink3)';
            }
        }

        function renderCalendar() {
            panel.innerHTML = '';

            /* ── header ── */
            var head = document.createElement('div');
            head.className = 'cdp-head';

            var prevBtn = document.createElement('button');
            prevBtn.type = 'button';
            prevBtn.className = 'cdp-nav';
            prevBtn.innerHTML = '<svg width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M6 1L2.5 4.5 6 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            prevBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                state.viewMonth--;
                if (state.viewMonth < 0) { state.viewMonth = 11; state.viewYear--; }
                renderCalendar();
            });

            var monthLabel = document.createElement('button');
            monthLabel.type = 'button';
            monthLabel.className = 'cdp-month-label';
            monthLabel.textContent = MONTHS[state.viewMonth] + ' ' + state.viewYear;

            var nextBtn = document.createElement('button');
            nextBtn.type = 'button';
            nextBtn.className = 'cdp-nav';
            nextBtn.innerHTML = '<svg width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M3 1l3.5 3.5L3 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            nextBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                state.viewMonth++;
                if (state.viewMonth > 11) { state.viewMonth = 0; state.viewYear++; }
                renderCalendar();
            });

            head.appendChild(prevBtn);
            head.appendChild(monthLabel);
            head.appendChild(nextBtn);
            panel.appendChild(head);

            /* ── day-of-week headers ── */
            var dowRow = document.createElement('div');
            dowRow.className = 'cdp-dow-row';
            DAYS.forEach(function(d) {
                var s = document.createElement('span');
                s.textContent = d;
                dowRow.appendChild(s);
            });
            panel.appendChild(dowRow);

            /* ── days grid ── */
            var grid = document.createElement('div');
            grid.className = 'cdp-days';

            var firstDow    = new Date(state.viewYear, state.viewMonth, 1).getDay();
            var daysInMonth = new Date(state.viewYear, state.viewMonth + 1, 0).getDate();
            var daysInPrev  = new Date(state.viewYear, state.viewMonth, 0).getDate();
            var today       = new Date();

            /* previous month padding */
            for (var i = firstDow - 1; i >= 0; i--) {
                grid.appendChild(makeDayBtn(daysInPrev - i, state.viewMonth - 1, state.viewYear, true, today));
            }
            /* current month */
            for (var d = 1; d <= daysInMonth; d++) {
                grid.appendChild(makeDayBtn(d, state.viewMonth, state.viewYear, false, today));
            }
            /* next month padding */
            var total = Math.ceil((firstDow + daysInMonth) / 7) * 7;
            for (var nd = 1; nd <= total - firstDow - daysInMonth; nd++) {
                grid.appendChild(makeDayBtn(nd, state.viewMonth + 1, state.viewYear, true, today));
            }

            panel.appendChild(grid);

            /* ── footer ── */
            var footer = document.createElement('div');
            footer.className = 'cdp-footer';

            var clearBtn = document.createElement('button');
            clearBtn.type = 'button';
            clearBtn.className = 'cdp-clear';
            clearBtn.textContent = 'Clear';
            clearBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                state.selected = null;
                hidden.value   = '';
                input.value    = '';
                input.dispatchEvent(new Event('change', { bubbles: true }));
                updateTrigger();
                close();
            });

            var todayBtn = document.createElement('button');
            todayBtn.type = 'button';
            todayBtn.className = 'cdp-today-btn';
            todayBtn.textContent = 'Today';
            todayBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                var t = new Date();
                state.viewYear  = t.getFullYear();
                state.viewMonth = t.getMonth();
                pickDate(t.getDate(), t.getMonth(), t.getFullYear());
            });

            footer.appendChild(clearBtn);
            footer.appendChild(todayBtn);
            panel.appendChild(footer);
        }

        function makeDayBtn(d, m, y, outside, today) {
            /* wrap months */
            var mm = m, yy = y;
            if (mm < 0)  { mm = 11; yy--; }
            if (mm > 11) { mm = 0;  yy++; }

            var isSelected = state.selected &&
                state.selected.getDate()     === d &&
                state.selected.getMonth()    === mm &&
                state.selected.getFullYear() === yy;
            var isToday = today.getDate()     === d &&
                          today.getMonth()    === mm &&
                          today.getFullYear() === yy;

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'cdp-day';
            if (outside)    btn.classList.add('cdp-day--outside');
            if (isSelected) btn.classList.add('cdp-day--selected');
            if (isToday)    btn.classList.add('cdp-day--today');
            btn.textContent = d;

            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                pickDate(d, mm, yy);
            });
            return btn;
        }

        function pickDate(d, m, y) {
            state.selected  = new Date(y, m, d);
            state.viewYear  = y;
            state.viewMonth = m;
            var iso    = toISO(state.selected);
            hidden.value   = iso;
            input.value    = iso;
            input.dispatchEvent(new Event('change', { bubbles: true }));
            updateTrigger();
            close();
        }

        function positionPanel() {
            panel.style.top    = '100%';
            panel.style.bottom = 'auto';
            panel.style.left   = '0';
        }

        function open() {
            document.querySelectorAll('.cdp-wrap.cdp-open').forEach(function(w) {
                if (w !== wrap) w.classList.remove('cdp-open');
            });
            document.querySelectorAll('.cs-wrap.cs-open').forEach(function(w) {
                w.classList.remove('cs-open');
            });
            renderCalendar();
            wrap.classList.add('cdp-open');
            trigger.setAttribute('aria-expanded', 'true');
            positionPanel();
        }

        function close() {
            wrap.classList.remove('cdp-open');
            trigger.setAttribute('aria-expanded', 'false');
        }

        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            wrap.classList.contains('cdp-open') ? close() : open();
        });

        updateTrigger();

        wrap.appendChild(trigger);
        wrap.appendChild(panel);
        wrap.appendChild(hidden);
        wrap.appendChild(input);
    }

    /* close on outside click or scroll */
    function closeAllCDP() {
        document.querySelectorAll('.cdp-wrap.cdp-open').forEach(function(w) { w.classList.remove('cdp-open'); });
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.cdp-wrap')) closeAllCDP();
    });

    window.addEventListener('scroll', closeAllCDP, { passive: true });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.cdp-wrap.cdp-open').forEach(function(w) { w.classList.remove('cdp-open'); });
        }
    });

    function initAll() {
        document.querySelectorAll('input[type="date"]:not([data-cdp-skip])').forEach(function(el) {
            if (!el.dataset.cdpInit) initCDP(el);
        });
    }

    document.addEventListener('DOMContentLoaded', initAll);
    window.initCustomDatePickers = initAll;
})();
</script>

<style>
    @media (min-width: 769px) {
        .contact-bar__inner {
            justify-content: center;
        }
    }
    .cart-wrap.cart-suppressed .mini-cart { display: none !important; }
</style>

@include('partials.notify')
{{-- Brand-green success override for the storefront --}}
<style>
.pbn-success .pbn-icon  { background: var(--g50);  color: var(--g600); }
.pbn-success .pbn-fill  { background: linear-gradient(90deg, var(--g600), var(--g400)); }
.pbn-success .pbn-title { color: var(--g800); }
</style>

</body>
</html>