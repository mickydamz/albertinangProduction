@extends('layouts.simslayout')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <style>
        /* ===== CUSTOM FONT FACES ===== */
        @font-face {
            font-display: auto; font-family: Tu; font-style: normal; font-weight: 400;
            src: url(https://cdn.tu.co.uk/fonts/Tu_W_Rg.woff2);
        }
        @font-face {
            font-display: auto; font-family: Tu; font-style: normal; font-weight: 600;
            src: url(https://cdn.tu.co.uk/fonts/Tu_W_Bd.woff2);
        }
        @font-face {
            font-display: auto; font-family: Tu; font-style: normal; font-weight: 300;
            src: url(https://cdn.tu.co.uk/fonts/Tu_W_Lt.woff2);
        }

        /* ===== DESIGN TOKENS ===== */
        :root {
            --red:    #dc2626;
            --red-bg: #fff0f0;
            --red-bd: #fecaca;

            --ds-color-link: #375ea9;
            --ds-color-star: #EFC71D;
        }

        /* ===== RESET & BASE ===== */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-body);
            color: var(--ink);
            background: var(--surf2);
            min-height: 100vh;
        }

        img { display: block; max-width: 100%; height: auto; }
        button { cursor: pointer; font-family: var(--font-body); }
        a { text-decoration: none; }

        /* ===== TOP BANNER ===== */
        .top-banner {
            background: linear-gradient(90deg, #333 0%, #4a5e70 100%);
            color: white;
            padding: 0.375rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
            font-size: 0.8125rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }
        .top-banner::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: shimmer 3s infinite;
        }
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* ===== HEADER ===== */
        .header {
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 0.625rem 0;
        }

        /* ===== PAGE LAYOUT ===== */
        .cart-page-container {
            max-width: 1200px;
            margin: 1.5rem auto;
            padding: 0 1rem;
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 1.5rem;
            align-items: start;
        }

        /* ===== SECTION CARDS ===== */
        .cart-items-section,
        .cart-summary-section {
            background: var(--surf);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            padding: 1.5rem;
        }
        .cart-summary-section {
            position: sticky;
            top: 5.5rem;
        }

        /* ===== BREADCRUMB ===== */
        .breadcrumb {
            padding: 8px 0;
            margin-bottom: 1.25rem;
            font-size: 12px;
            color: var(--ink4);
            border-bottom: 1px solid var(--border);
        }
        .breadcrumb ul { display: flex; flex-wrap: wrap; list-style: none; }
        .breadcrumb a { color: var(--ink3); transition: color .15s; }
        .breadcrumb a:hover { color: var(--g600); }
        .breadcrumb span { color: var(--ink); font-weight: 600; }
        .breadcrumb li::before { color: var(--border2); }

        /* ===== PAGE TITLE ===== */
        .cart-page-title {
            font-family: var(--font-head);
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--ink);
            margin-bottom: 1.25rem;
            letter-spacing: -0.3px;
            text-align: left;
        }

        /* ===== CART TABLE ===== */
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5rem;
        }
        .cart-table th {
            background: var(--surf2);
            color: var(--ink3);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: 10px 12px;
            border-bottom: 2px solid var(--border);
            text-align: left;
        }
        .cart-table td {
            padding: 14px 12px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            font-size: 13.5px;
            color: var(--ink3);
        }
        .cart-item-row:last-child td { border-bottom: none; }
        .cart-item-row:hover td { background: var(--surf2); }

        /* Product cell */
        .cart-item-info {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .cart-item-info img {
            width: 64px;
            height: 64px;
            object-fit: contain;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            background: var(--surf2);
            padding: 4px;
            flex-shrink: 0;
        }
        .item-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
            line-height: 1.35;
            margin-bottom: 3px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .item-price-single {
            font-size: 11.5px;
            color: var(--ink4);
        }

        /* ===== INSTALLATION OPTIONS (checkout) ===== */
        .install-opts-wrap {
            margin-top: 8px;
            padding: 8px 10px;
            background: var(--surf2);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
        }
        .install-opts-title {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--ink3);
            margin-bottom: 6px;
        }
        .install-opts-list {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .install-opt-label {
            display: flex;
            align-items: flex-start;
            gap: 7px;
            cursor: pointer;
            padding: 5px 8px;
            border-radius: var(--radius-sm);
            border: 1.5px solid transparent;
            transition: border-color .15s, background .15s;
        }
        .install-opt-label:hover {
            background: var(--g50);
            border-color: var(--g200);
        }
        .install-opt-label.selected {
            background: var(--g50);
            border-color: var(--g500);
        }
        .install-opt-radio {
            margin-top: 2px;
            accent-color: var(--g500);
            flex-shrink: 0;
        }
        .install-opt-text {
            font-size: 12px;
            color: var(--ink2);
            line-height: 1.4;
        }
        .install-opt-text strong {
            color: var(--g600);
            font-weight: 700;
        }
        .install-opt-text em {
            font-style: normal;
            color: var(--ink4);
            font-size: 11px;
        }
        .install-opt-text small {
            display: block;
            font-size: 11px;
            color: var(--ink4);
        }
        /* "No installation" reject option */
        .install-opt-label.reject-opt:hover {
            background: var(--red-bg);
            border-color: var(--red-bd);
        }
        .install-opt-label.reject-opt.selected {
            background: var(--red-bg);
            border-color: var(--red);
        }
        .install-opts-loading {
            font-size: 11px;
            color: var(--ink4);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Qty stepper */
        .quantity-controls {
            display: flex;
            align-items: center;
            border: 1.5px solid var(--border2);
            border-radius: var(--radius-sm);
            overflow: hidden;
            width: fit-content;
        }
        .quantity-btn {
            width: 30px;
            height: 30px;
            background: var(--surf2);
            border: none;
            font-size: 15px;
            font-weight: 700;
            color: var(--ink2);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background .15s, color .15s;
            flex-shrink: 0;
        }
        .quantity-btn:hover { background: var(--g100); color: var(--g700); }
        .quantity-input {
            width: 40px;
            height: 30px;
            text-align: center;
            border: none;
            border-left: 1px solid var(--border);
            border-right: 1px solid var(--border);
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
            background: #fff;
            outline: none;
            -moz-appearance: textfield;
        }
        .quantity-input::-webkit-outer-spin-button,
        .quantity-input::-webkit-inner-spin-button { -webkit-appearance: none; }

        /* Subtotal cell */
        .item-subtotal {
            font-family: var(--font-head);
            font-size: 14px;
            font-weight: 700;
            color: var(--g600);
            white-space: nowrap;
        }

        /* Remove btn */
        .remove-item-btn {
            background: none;
            border: none;
            color: var(--ink4);
            font-size: 15px;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: var(--radius-sm);
            transition: color .15s, background .15s;
        }
        .remove-item-btn:hover { color: var(--red); background: var(--red-bg); }

        /* ===== CART ACTIONS BAR ===== */
        .cart-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
            gap: 10px;
            flex-wrap: wrap;
        }
        .continue-shopping-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            background: var(--surf2);
            color: var(--g600);
            border: 1.5px solid var(--g400);
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            transition: all .2s;
        }
        .continue-shopping-btn:hover { background: var(--g100); transform: translateY(-1px); }
        .clear-cart-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            background: var(--red-bg);
            color: var(--red);
            border: 1.5px solid var(--red-bd);
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            transition: all .2s;
        }
        .clear-cart-btn:hover { background: #fde8e8; border-color: var(--red); transform: translateY(-1px); }

        /* ===== PICKUP SECTION ===== */
        .pickup-section {
            background: var(--surf2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem;
            margin-top: 1.5rem;
        }
        .pickup-section h3 {
            font-family: var(--font-head);
            font-size: 1rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 1rem;
            padding-bottom: 0.6rem;
            border-bottom: 2px solid var(--border2);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .pickup-section h3::before {
            content: '';
            display: inline-block;
            width: 4px; height: 18px;
            background: var(--g500);
            border-radius: 2px;
        }
        #currentPickupLocation { font-weight: 700; color: var(--g600); }
        #changePickupLocationBtn {
            background: var(--g500);
            color: #fff;
            border: none;
            padding: 5px 12px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 600;
            transition: background .15s;
        }
        #changePickupLocationBtn:hover { background: var(--g700); }
        .pickup-point-item {
            background: var(--surf);
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            padding: 14px;
            transition: all .2s;
            cursor: pointer;
            margin-bottom: 8px;
        }
        .pickup-point-item:hover { border-color: var(--g400); box-shadow: 0 2px 8px rgba(78,122,26,.12); }
        .pickup-point-item.selected {
            background: var(--g50);
            border-color: var(--g500);
            box-shadow: 0 2px 10px rgba(78,122,26,.18);
        }
        .pickup-point-item h4 {
            font-family: var(--font-head);
            font-size: 14px; font-weight: 700;
            color: var(--ink); margin-bottom: 4px;
        }
        .pickup-point-item p { font-size: 12px; color: var(--ink3); margin-bottom: 2px; line-height: 1.5; }
        .pickup-point-item .hours { font-size: 11px; color: var(--g600); font-weight: 500; }

        /* ===== ORDER SUMMARY ===== */
        .cart-summary-section h3 {
            font-family: var(--font-head);
            font-size: 1.15rem; font-weight: 800;
            color: var(--ink);
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--border);
            display: flex; align-items: center; gap: 8px;
        }
        .cart-summary-section h3::before {
            content: '';
            display: inline-block;
            width: 4px; height: 18px;
            background: var(--g500);
            border-radius: 2px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed var(--border);
            font-size: 14px;
            color: var(--ink3);
        }
        .summary-item span:last-child { font-weight: 600; color: var(--ink); }
        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 0 0;
            margin-top: 4px;
            font-family: var(--font-head);
            font-size: 1.3rem; font-weight: 800;
            color: var(--g700);
        }

        /* Make Payment CTA */
        .checkout-btn-summary {
            width: 100%;
            margin-top: 1.25rem;
            padding: 13px 20px;
            background: var(--g500);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            font-family: var(--font-head);
            font-size: 1rem; font-weight: 700;
            letter-spacing: .2px;
            transition: all .2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .checkout-btn-summary:hover:not([disabled]) {
            background: var(--g700);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(61,128,18,.3);
        }
        .checkout-btn-summary[disabled] {
            background: #c5d5b5;
            color: #7a9262;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* ===== PAYMENT FORMS SECTION ===== */
        #payment-forms-section h2 {
            font-family: var(--font-head);
            font-size: 1.1rem; font-weight: 700;
            color: var(--ink);
            margin-bottom: 1rem;
            text-align: left;
        }
        .payment-options {
            display: flex;
            gap: 10px; flex-wrap: wrap;
            justify-content: flex-start;
            margin-bottom: 1rem;
        }
        #select-stripe {
            background: #1a56db; color: #fff;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-size: 13px; font-weight: 600;
            border: none; transition: background .15s;
        }
        #select-stripe:hover { background: #1044b8; }
        #select-paystack {
            background: var(--g500); color: #fff;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-size: 13px; font-weight: 600;
            border: none; transition: background .15s;
        }
        #select-paystack:hover { background: var(--g700); }
        #stripe-payment-section,
        #paystack-payment-section {
            border: 1px solid var(--border);
            background: var(--surf2);
            border-radius: var(--radius);
            padding: 1rem;
            margin-top: 0.75rem;
        }
        #stripe-payment-section h3,
        #paystack-payment-section h3 {
            font-family: var(--font-head);
            font-size: 14px; font-weight: 700;
            color: var(--ink); margin-bottom: 0.75rem;
        }
        #stripe-submit-button {
            width: 100%; background: #1a56db; color: #fff;
            padding: 11px; border: none;
            border-radius: var(--radius-sm);
            font-size: 14px; font-weight: 600;
            transition: background .15s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        #stripe-submit-button:hover { background: #1044b8; }
        #paystack-submit-button {
            width: 100%; background: var(--g500); color: #fff;
            padding: 11px; border: none;
            border-radius: var(--radius-sm);
            font-size: 14px; font-weight: 600;
            transition: background .15s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        #paystack-submit-button:hover { background: var(--g700); }
        #card-element {
            border: 1.5px solid var(--border2);
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            background: #fff;
            margin-bottom: 12px;
        }
        #main-checkout-form label {
            font-size: 12px; font-weight: 600;
            color: var(--ink2);
            display: block; margin-bottom: 5px;
        }
        #main-checkout-form input {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid var(--border2);
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-family: var(--font-body);
            color: var(--ink);
            outline: none;
            transition: border-color .15s;
            margin-bottom: 12px;
            background: #fff;
        }
        #main-checkout-form input:focus { border-color: var(--g400); }
        #main-checkout-form input[readonly] { background: var(--surf2); color: var(--ink3); }

        /* ===== LOCATION MODAL ===== */
        .location-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.55);
            z-index: 2000;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity .25s;
            pointer-events: none;
        }
        .location-modal-overlay.active { opacity: 1; pointer-events: auto; }
        .location-modal-content {
            background: #fff;
            padding: 1.75rem;
            border-radius: var(--radius-lg);
            box-shadow: 0 8px 40px rgba(0,0,0,.25);
            max-width: 480px; width: 92%;
            transform: translateY(-16px);
            opacity: 0;
            transition: transform .25s ease-out, opacity .25s ease-out;
            position: relative;
        }
        .location-modal-overlay.active .location-modal-content {
            transform: translateY(0);
            opacity: 1;
        }
        .location-modal-content h2 {
            font-family: var(--font-head);
            font-size: 1.15rem; font-weight: 800;
            color: var(--ink);
            margin-bottom: 1.25rem;
            text-align: center;
        }
        .location-modal-close {
            position: absolute;
            top: 14px; right: 14px;
            background: var(--surf2);
            border: 1px solid var(--border);
            border-radius: 50%;
            width: 30px; height: 30px;
            font-size: 13px; color: var(--ink3);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all .15s;
        }
        .location-modal-close:hover { background: var(--red-bg); color: var(--red); border-color: var(--red-bd); }
        .location-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-bottom: 1.25rem;
            max-height: 288px;
            overflow-y: auto;
            padding-right: 4px;
        }
        @media (min-width: 480px) { .location-list { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 640px) { .location-list { grid-template-columns: repeat(4, 1fr); } }
        .location-item {
            background: var(--surf2);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 10px;
            text-align: center;
            cursor: pointer;
            font-size: 13px; font-weight: 500;
            color: var(--ink);
            transition: all .15s;
        }
        .location-item:hover { border-color: var(--g400); background: var(--g50); color: var(--g700); }
        .location-item.selected {
            background: var(--g500); color: #fff;
            border-color: var(--g600); font-weight: 700;
            box-shadow: 0 2px 8px rgba(78,122,26,.25);
        }
        .location-actions { display: flex; justify-content: space-between; gap: 8px; }
        .location-confirm-btn {
            background: var(--g500); color: #fff;
            border: none; padding: 11px 20px;
            border-radius: var(--radius-sm);
            font-size: 14px; font-weight: 700;
            transition: background .15s; cursor: pointer; flex: 1;
        }
        .location-confirm-btn:hover { background: var(--g700); }
        .location-clear-btn {
            background: var(--surf2); color: var(--ink3);
            border: 1.5px solid var(--border2);
            padding: 11px 20px;
            border-radius: var(--radius-sm);
            font-size: 14px; font-weight: 600;
            transition: all .15s; cursor: pointer; flex: 1;
        }
        .location-clear-btn:hover { background: #e8e8e8; }

        /* ===== EMPTY CART ===== */
        .empty-cart-message-page {
            text-align: center;
            padding: 3rem 1.5rem;
            background: var(--surf2);
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }
        .empty-cart-message-page p:first-of-type {
            font-family: var(--font-head);
            font-size: 1.15rem; font-weight: 700;
            color: var(--ink); margin-bottom: 0.5rem;
        }
        .empty-cart-message-page p {
            font-size: 13px; color: var(--ink3); margin-bottom: 1.25rem;
        }
        .empty-cart-message-page a {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 22px;
            background: var(--g500); color: #fff;
            border-radius: var(--radius-sm);
            font-size: 13px; font-weight: 700;
            transition: all .2s;
        }
        .empty-cart-message-page a:hover { background: var(--g700); transform: translateY(-1px); }

        /* ===== MOBILE MENU ===== */
        .mobile-menu-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 50;
            opacity: 0; transition: opacity 0.2s;
        }
        .mobile-menu-overlay.active { opacity: 1; pointer-events: auto; }
        .mobile-menu {
            position: fixed; top: 0; left: -100%;
            width: 91.666667%; max-width: 320px;
            height: 100vh;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.15);
            z-index: 50;
            transition: left 0.2s;
            overflow-y: auto;
        }
        .mobile-menu.active { left: 0; }

        /* ===== UTILITIES ===== */
        .hidden { display: none !important; }
        .text-danger { color: var(--red); font-size: 12px; margin-top: 4px; }
        .spinner-border { margin-right: 0.625rem; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1023px) {
            .cart-page-container {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            .cart-summary-section { position: static; }
        }

        @media (max-width: 767px) {
            .cart-page-container {
                margin: 0.75rem auto;
                padding: 0 0.75rem;
            }
            .cart-page-title { font-size: 1.3rem; }
            .checkout-btn-summary { font-size: 0.95rem; }

            /* Stack table into cards on mobile */
            .cart-table thead { display: none; }
            .cart-table,
            .cart-table tbody,
            .cart-item-row,
            .cart-table td { display: block; width: 100%; }

            .cart-item-row {
                border: 1px solid var(--border);
                border-radius: var(--radius);
                margin-bottom: 10px;
                overflow: hidden;
            }
            .cart-item-row:hover td { background: transparent; }

            .cart-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 14px;
                border-bottom: 1px dashed var(--border);
                font-size: 13px;
            }
            .cart-table td:last-child { border-bottom: none; }
            .cart-table td::before {
                content: attr(data-label);
                font-size: 11px; font-weight: 700;
                color: var(--ink3);
                text-transform: uppercase;
                letter-spacing: .05em;
                flex-shrink: 0;
                margin-right: 10px;
            }
            .cart-table td:first-child { display: block; }
            .cart-table td:first-child::before { display: none; }

            .cart-item-info img { width: 52px; height: 52px; }
            .item-name { font-size: 12.5px; }

            .cart-actions { flex-direction: column; }
            .continue-shopping-btn,
            .clear-cart-btn { width: 100%; justify-content: center; }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

</head>
<body>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay fixed inset-0 bg-black bg-opacity-40 z-50 hidden opacity-0 transition-opacity duration-200"></div>
    <div class="mobile-menu fixed top-0 -left-full w-11/12 max-w-xs h-screen bg-white shadow-lg z-50 transition-all duration-200 overflow-y-auto">
        <div class="mobile-menu-header flex justify-between items-center p-3 bg-orange-500 text-white">
            <h3 class="text-lg font-semibold">Shop Categories</h3>
            <button class="mobile-menu-close text-xl text-white cursor-pointer transition-transform duration-200 hover:rotate-45" aria-label="Close navigation menu">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mobile-menu-content p-3" id="mobileCategoriesMenu"></div>
    </div>

    <!-- Main Content -->
    <div class="cart-page-container">
        <!-- Left: Cart Items and Pickup -->
        <div class="cart-items-section">
            <nav class="breadcrumb" aria-label="breadcrumb">
                <ul>
                    <li><a href="/">Home</a></li>
                    <li class="flex items-center before:content-['>'] before:mx-2 before:text-gray-400 before:font-bold"><span>Checkout</span></li>
                </ul>
            </nav>

            <h1 class="cart-page-title">Checkout</h1>

            <div id="cartItemsDisplay"></div>

            <div class="cart-actions">
                <a href="/" class="continue-shopping-btn">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
                <button id="clearCartBtn" class="clear-cart-btn">
                    <i class="fas fa-trash-alt"></i> Clear Cart
                </button>
            </div>

            <!-- Pickup Section -->
            <div class="pickup-section">
                <h3>Pickup Options</h3>
                <p style="font-size:13px; color:var(--ink3); display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:0.5rem;">
                    Your selected pickup location:
                    <span id="currentPickupLocation">Not selected</span>
                    <button id="changePickupLocationBtn">Change</button>
                </p>
                <div id="pickupPointsList" class="mt-2"></div>
                <p id="noPickupPointsMessage" style="display:none; text-align:center; padding:1rem; background:var(--surf2); border-radius:var(--radius-sm); font-size:13px; color:var(--ink3);">
                    No pickup points available for this location.
                </p>
            </div>
        </div>

        <!-- Right: Order Summary and Payment -->
        <div class="cart-summary-section">
            <h3>Order Summary</h3>
            <div class="summary-item">
                <span>Subtotal:</span>
                <span id="summarySubtotal">₦0</span>
            </div>
            <div class="summary-item">
                <span>Installation:</span>
                <span id="summaryInstallation">₦0</span>
            </div>
            <div class="summary-item">
                <span>Shipping:</span>
                <span>₦0</span>
            </div>
            <div class="summary-total">
                <span>Total:</span>
                <span id="summaryTotal">₦0</span>
            </div>
            <button class="checkout-btn-summary" id="proceedToPaymentBtn" disabled>
                <i class="fas fa-lock"></i> Make Payment
            </button>

            <!-- Payment Forms -->
            <div id="payment-forms-section" class="hidden" style="margin-top:1.5rem;">
                <h2>Complete Your Payment</h2>
                <div id="messages" style="margin-bottom:1rem;"></div>

                <form id="main-checkout-form">
                    <div>
                        <label for="amount">Amount (USD):</label>
                        <input type="number" id="amount" name="amount" step="0.01" min="0.50" value="0.50" readonly>
                    </div>
                    <div>
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="{{ auth()->user()?->email ?? 'test@example.com' }}" required>
                    </div>
                    <div class="payment-options">
                        <p style="width:100%; font-size:12px; font-weight:600; color:var(--ink3); margin-bottom:4px;">Choose a Payment Method:</p>
                        <button type="button" id="select-stripe">Pay with Card</button>
                        <button type="button" id="select-paystack">Pay with Paystack</button>
                    </div>
                </form>

                <!-- Stripe -->
                <div id="stripe-payment-section" class="hidden">
                    <h3>Pay with Stripe</h3>
                    <form id="stripe-payment-form">
                        <label for="card-element" style="font-size:12px; font-weight:600; color:var(--ink2); display:block; margin-bottom:6px;">Credit or debit card</label>
                        <div id="card-element"></div>
                        <div id="card-errors" role="alert" class="text-danger"></div>
                        <button type="submit" id="stripe-submit-button">
                            <span class="spinner-border hidden" role="status" aria-hidden="true"
                                style="width:14px; height:14px; border:2px solid #fff; border-bottom-color:transparent; border-radius:50%; display:inline-block; animation:spin .7s linear infinite;"></span>
                            Pay with Stripe
                        </button>
                    </form>
                </div>

                <!-- Paystack -->
                <div id="paystack-payment-section" class="hidden">
                    <h3>Pay with Paystack</h3>
                    <button type="button" id="paystack-submit-button">
                        <span class="spinner-border hidden" role="status" aria-hidden="true"
                            style="width:14px; height:14px; border:2px solid #fff; border-bottom-color:transparent; border-radius:50%; display:inline-block; animation:spin .7s linear infinite;"></span>
                        Pay with Paystack
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Location Modal -->
    <div class="location-modal-overlay" id="locationModalOverlay">
        <div class="location-modal-content">
            <button class="location-modal-close" id="closeLocationModalBtn" aria-label="Close"><i class="fas fa-times"></i></button>
            <h2>Select Your Location</h2>
            <div class="location-list" id="locationList"></div>
            <div class="location-actions">
                <button class="location-clear-btn" id="clearLocationFilterBtn">Clear Filter</button>
                <button class="location-confirm-btn" id="confirmLocationBtn">Apply Filter</button>
            </div>
        </div>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
    window.currencyExchangeRates = {
        'NGN': { symbol: '₦', rate: 1 },
        'USD': { symbol: '$', rate: 0.00067 },
        'GBP': { symbol: '£', rate: 0.00053 },
        'EUR': { symbol: '€', rate: 0.00062 },
        'CAD': { symbol: 'C$', rate: 0.00097 },
    };

    document.addEventListener('DOMContentLoaded', () => {
        // ── DOM refs ──────────────────────────────────────────────────────────
        const cartItemsDisplay        = document.getElementById('cartItemsDisplay');
        const summarySubtotal         = document.getElementById('summarySubtotal');
        const summaryInstallation     = document.getElementById('summaryInstallation');
        const summaryTotal            = document.getElementById('summaryTotal');
        const clearCartBtn            = document.getElementById('clearCartBtn');
        const cartCountSpan           = document.querySelector('.cart-count');
        const proceedToPaymentBtn     = document.getElementById('proceedToPaymentBtn');
        const paymentFormsSection     = document.getElementById('payment-forms-section');

        const locationModalOverlay    = document.getElementById('locationModalOverlay');
        const closeLocationModalBtn   = document.getElementById('closeLocationModalBtn');
        const locationListContainer   = document.getElementById('locationList');
        const confirmLocationBtn      = document.getElementById('confirmLocationBtn');
        const clearLocationFilterBtn  = document.getElementById('clearLocationFilterBtn');
        const changePickupLocationBtn = document.getElementById('changePickupLocationBtn');

        const currentPickupLocationSpan = document.getElementById('currentPickupLocation');
        const pickupPointsList          = document.getElementById('pickupPointsList');
        const noPickupPointsMessage     = document.getElementById('noPickupPointsMessage');

        const amountInput        = document.getElementById('amount');
        const emailInput         = document.getElementById('email');
        const messagesDiv        = document.getElementById('messages');

        const stripeSection      = document.getElementById('stripe-payment-section');
        const paystackSection    = document.getElementById('paystack-payment-section');
        const selectStripeBtn    = document.getElementById('select-stripe');
        const selectPaystackBtn  = document.getElementById('select-paystack');

        const stripeForm           = document.getElementById('stripe-payment-form');
        const stripeSubmitButton   = document.getElementById('stripe-submit-button');
        const stripeSpinner        = stripeSubmitButton?.querySelector('.spinner-border');
        const cardErrors           = document.getElementById('card-errors');
        const paystackSubmitButton = document.getElementById('paystack-submit-button');
        const paystackSpinner      = paystackSubmitButton?.querySelector('.spinner-border');
        // ✅ Get Paystack key from .env
        const paystackPk           = '{{ env("PAYSTACK_PUBLIC_KEY") }}';

        let cart             = [];
        let selectedLocation = null;

        // ── Installation options state ────────────────────────────────────────
        const installationSelections = {};
        const installationOptionsCache = {};

        // ── Static data ───────────────────────────────────────────────────────
        const availableLocations = [
            { id: 1, name: 'Lagos' }, { id: 2, name: 'Abuja' },
            { id: 3, name: 'Port Harcourt' }, { id: 4, name: 'Kano' },
            { id: 5, name: 'Ibadan' }, { id: 6, name: 'Enugu' },
            { id: 7, name: 'Calabar' }, { id: 8, name: 'Kaduna' }
        ];

        const pickupLocationsData = {
            'Lagos': [{ id: 'lagos-island', name: 'Lagos Island Store', address: '123 Main St, Lagos Island', hours: 'Mon-Sat: 9 AM - 7 PM' }, { id: 'ikeja-mall', name: 'Ikeja Mall Pickup', address: 'Shop G20, Ikeja City Mall', hours: 'Mon-Sun: 10 AM - 9 PM' }],
            'Abuja': [{ id: 'wuse-market', name: 'Wuse Market Point', address: 'Shop 5, Wuse Market', hours: 'Mon-Fri: 8 AM - 6 PM' }],
            'Port Harcourt': [{ id: 'ph-city-center', name: 'PH City Center', address: '45 Rivers Rd, Port Harcourt', hours: 'Mon-Sat: 9 AM - 6 PM' }],
            'Enugu': [{ id: 'enugu-city-center', name: 'Enugu City', address: '45 Zik Estate Uwani, Enugu State', hours: 'Mon-Sat: 9 AM - 6 PM' }]
        };

        // ── Currency setup ────────────────────────────────────────────────────
        let effectiveCurrency = 'NGN';
        const storedCurrencyRaw = localStorage.getItem('selectedCurrency');
        if (storedCurrencyRaw) {
            const t = storedCurrencyRaw.trim().toUpperCase();
            effectiveCurrency = (t.startsWith('"') && t.endsWith('"')) ? t.slice(1, -1) : t;
        }
        const currencyFromURL = new URLSearchParams(window.location.search).get('currency')?.trim().toUpperCase();
        if (currencyFromURL && window.currencyExchangeRates[currencyFromURL]) effectiveCurrency = currencyFromURL;
        window.currentCurrency = effectiveCurrency;

        // ── Helpers ───────────────────────────────────────────────────────────
        function formatCurrency(amount) {
            if (!window.currencyExchangeRates?.[window.currentCurrency])
                return `₦${parseFloat(amount).toLocaleString('en-NG', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
            const { rate } = window.currencyExchangeRates[window.currentCurrency];
            return new Intl.NumberFormat('en-US', { style: 'currency', currency: window.currentCurrency, minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(amount * rate);
        }

        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]').content;
        }

        function showMessage(type, msg) {
            const bg = type === 'success' ? '#dcfce7' : type === 'info' ? '#dbeafe' : '#fee2e2';
            const color = type === 'success' ? '#166534' : type === 'info' ? '#1e40af' : '#991b1b';
            messagesDiv.innerHTML = `<div style="padding:10px 14px; border-radius:6px; font-size:13px; background:${bg}; color:${color};">${msg}</div>`;
        }

        function clearMessages() { messagesDiv.innerHTML = ''; }
        function showLoading(btn, sp) { if(btn) btn.disabled = true; if(sp) sp.classList.remove('hidden'); }
        function hideLoading(btn, sp) { if(btn) btn.disabled = false; if(sp) sp.classList.add('hidden'); }
        function resetPaymentForms() { stripeSection.classList.add('hidden'); paystackSection.classList.add('hidden'); clearMessages(); }

        // ── Cart helpers ──────────────────────────────────────────────────────
        function loadCartFromLocalStorage() {
            try { cart = JSON.parse(localStorage.getItem('shoppingCart') || '[]'); }
            catch (e) { cart = []; }
        }

        function saveCartToLocalStorage() {
            try { localStorage.setItem('shoppingCart', JSON.stringify(cart)); updateCartDisplayHeader(); }
            catch (e) { console.error(e); }
        }

        function updateCartDisplayHeader() {
            const c = JSON.parse(localStorage.getItem('shoppingCart') || '[]');
            if (cartCountSpan) { cartCountSpan.textContent = c.length; cartCountSpan.style.display = c.length > 0 ? 'flex' : 'none'; }
        }

        // ── Installation: fetch options from API ──────────────────────────────
        async function fetchInstallationOptions() {
            if (cart.length === 0) return;
            const ids = [...new Set(cart.map(i => i.id).filter(id => !(id in installationOptionsCache)))];
            if (ids.length === 0) return;
            try {
                const res = await fetch('/api/installation-options', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ product_ids: ids }),
                });
                if (!res.ok) return;
                const data = await res.json();
                Object.assign(installationOptionsCache, data);
            } catch (e) {
                console.warn('Could not fetch installation options:', e);
            }
        }

        // ── Build installation options HTML ───────────────────────────────────
        function buildInstallOptsHtml(cartIndex) {
            const item = cart[cartIndex];
            const opts = installationOptionsCache[item.id];
            if (!opts || opts.length === 0) return '';

            const current = installationSelections[cartIndex];
            if (current === undefined) {
                const first = opts[0];
                const label = typeof first === 'object' ? (first.label || 'Option') : String(first);
                const extraNgn = typeof first === 'object' && first.price ? Number(first.price) : 0;
                installationSelections[cartIndex] = { label, extraNgn };
            }

            const optionsHtml = opts.map((opt, i) => {
                const label = typeof opt === 'object' ? (opt.label || 'Option') : String(opt);
                const extraNgn = typeof opt === 'object' && opt.price ? Number(opt.price) : 0;
                const desc = typeof opt === 'object' && opt.description ? opt.description : '';
                const isChecked = installationSelections[cartIndex]?.label === label;
                const priceTag = extraNgn > 0 ? `<strong>+${formatCurrency(extraNgn)}</strong>` : `<em>Included</em>`;
                return `<label class="install-opt-label ${isChecked ? 'selected' : ''}" for="install_${cartIndex}_${i}"><input class="install-opt-radio" type="radio" id="install_${cartIndex}_${i}" name="install_opt_${cartIndex}" data-cart-index="${cartIndex}" data-label="${label.replace(/"/g, '&quot;')}" data-extra="${extraNgn}" ${isChecked ? 'checked' : ''}><span class="install-opt-text">${label} ${priceTag}${desc ? `<small>${desc}</small>` : ''}</span></label>`;
            }).join('');

            const rejectChecked = installationSelections[cartIndex] === null;
            const rejectHtml = `<label class="install-opt-label reject-opt ${rejectChecked ? 'selected' : ''}" for="install_${cartIndex}_reject"><input class="install-opt-radio" type="radio" id="install_${cartIndex}_reject" name="install_opt_${cartIndex}" data-cart-index="${cartIndex}" data-label="" data-extra="0" data-reject="true" ${rejectChecked ? 'checked' : ''}><span class="install-opt-text" style="color:#dc2626;"><i class="fas fa-times-circle" style="font-size:11px;"></i> No installation <em>Skip this option</em></span></label>`;

            return `<div class="install-opts-wrap"><div class="install-opts-title"><i class="fas fa-tools" style="color:#4e7a1a;margin-right:4px;"></i>Installation Options</div><div class="install-opts-list">${optionsHtml}${rejectHtml}</div></div>`;
        }

        // ── Totals ────────────────────────────────────────────────────────────
        function calculateCartTotals() {
            let subtotalNgn = 0;
            let installationNgn = 0;
            cart.forEach((item, index) => {
                const base = (typeof item.basePriceNgn === 'number' && !isNaN(item.basePriceNgn)) ? item.basePriceNgn : 0;
                const qty = parseInt(item.quantity) || 0;
                const sel = installationSelections[index];
                const extraNgn = (sel && sel.extraNgn) ? sel.extraNgn : 0;
                subtotalNgn += base * qty;
                installationNgn += extraNgn * qty;
            });
            const totalNgn = subtotalNgn + installationNgn;
            summarySubtotal.textContent = formatCurrency(subtotalNgn);
            summaryInstallation.textContent = installationNgn > 0 ? `+${formatCurrency(installationNgn)}` : formatCurrency(0);
            summaryTotal.textContent = formatCurrency(totalNgn);
            const usdRate = window.currencyExchangeRates['USD']?.rate || (1 / 1000);
            const usdCents = Math.max(Math.round(totalNgn * usdRate * 100), 50);
            amountInput.value = (usdCents / 100).toFixed(2);
            return { usdCents, totalNgn, subtotalNgn, installationNgn };
        }

        // ── Attach installation listeners ─────────────────────────────────────
        function attachInstallOptionListeners() {
            document.querySelectorAll('.install-opt-radio').forEach(radio => {
                radio.addEventListener('change', e => {
                    const idx = parseInt(e.target.dataset.cartIndex);
                    const label = e.target.dataset.label;
                    const extraNgn = parseFloat(e.target.dataset.extra) || 0;
                    const isReject = e.target.dataset.reject === 'true';
                    installationSelections[idx] = isReject ? null : { label, extraNgn };
                    document.querySelectorAll(`[name="install_opt_${idx}"]`).forEach(r => {
                        r.closest('.install-opt-label').classList.toggle('selected', r === e.target);
                    });
                    const rows = document.querySelectorAll('.cart-item-row');
                    const row = rows[idx];
                    if (row) {
                        const base = (typeof cart[idx].basePriceNgn === 'number') ? cart[idx].basePriceNgn : 0;
                        const qty = cart[idx].quantity || 1;
                        const effective = base + (isReject ? 0 : extraNgn);
                        const cells = row.querySelectorAll('td');
                        if (cells[1]) cells[1].innerHTML = formatCurrency(effective);
                        if (cells[3]) cells[3].querySelector('.item-subtotal').textContent = formatCurrency(effective * qty);
                    }
                    calculateCartTotals();
                });
            });
        }

        // ── Cart event listeners ──────────────────────────────────────────────
        function attachCartEventListeners() {
            document.querySelectorAll('.increase-qty, .decrease-qty').forEach(b => b.addEventListener('click', handleQuantityChange));
            document.querySelectorAll('.quantity-input').forEach(i => i.addEventListener('change', handleQuantityChange));
            document.querySelectorAll('.remove-item-btn').forEach(b => b.addEventListener('click', handleRemoveItem));
        }

        // ── Render cart ───────────────────────────────────────────────────────
        async function renderCartItems() {
            if (!cartItemsDisplay) return;
            cartItemsDisplay.innerHTML = '';
            if (cart.length === 0) {
                cartItemsDisplay.innerHTML = `<div class="empty-cart-message-page"><p>Your shopping cart is empty.</p><p>Looks like you haven't added anything to your cart yet. Go ahead and explore our amazing products!</p><a href="/"><i class="fas fa-arrow-left"></i> Start Shopping</a></div>`;
                calculateCartTotals();
                return;
            }
            cartItemsDisplay.innerHTML = `<div style="padding:1.5rem;text-align:center;color:#888;font-size:13px;"><i class="fas fa-circle-notch fa-spin" style="margin-right:6px;color:#4e7a1a;"></i>Loading your cart…</div>`;
            await fetchInstallationOptions();
            cartItemsDisplay.innerHTML = '';
            const table = document.createElement('table');
            table.className = 'cart-table';
            table.innerHTML = `<thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr></thead><tbody></tbody>`;
            const tbody = table.querySelector('tbody');
            cart.forEach((item, index) => {
                const base = (typeof item.basePriceNgn === 'number' && !isNaN(item.basePriceNgn)) ? item.basePriceNgn : 0;
                const qty = item.quantity || 1;
                const img = item.image || item.imageSrc || 'https://placehold.co/80x80/f5f9f0/c5d9ae?text=Product';
                const sel = installationSelections[index];
                const extraNgn = (sel && sel.extraNgn) ? sel.extraNgn : 0;
                const effective = base + extraNgn;
                const row = document.createElement('tr');
                row.className = 'cart-item-row';
                row.dataset.productId = item.id;
                row.innerHTML = `<td data-label="Product"><div class="cart-item-info"><img src="${img}" alt="${item.name}" onerror="this.src='https://placehold.co/80x80/f5f9f0/c5d9ae?text=Product'"><div style="flex:1;min-width:0;"><div class="item-name">${item.name}</div><div class="item-price-single">${formatCurrency(base)} per item</div>${buildInstallOptsHtml(index)}</div></div></td><td data-label="Price">${formatCurrency(effective)}</td><td data-label="Quantity"><div class="quantity-controls"><button class="quantity-btn decrease-qty" data-index="${index}">−</button><input type="number" class="quantity-input" value="${qty}" min="1" data-index="${index}"><button class="quantity-btn increase-qty" data-index="${index}">+</button></div></td><td data-label="Subtotal"><span class="item-subtotal">${formatCurrency(effective * qty)}</span></td><td><button class="remove-item-btn" data-index="${index}" aria-label="Remove ${item.name}"><i class="fas fa-times-circle"></i></button></td>`;
                tbody.appendChild(row);
            });
            cartItemsDisplay.appendChild(table);
            attachCartEventListeners();
            attachInstallOptionListeners();
            calculateCartTotals();
        }

        function handleQuantityChange(e) {
            const t = e.target;
            const idx = parseInt(t.dataset.index);
            if (isNaN(idx) || idx < 0 || idx >= cart.length) return;
            let qty;
            if (t.classList.contains('increase-qty')) qty = cart[idx].quantity + 1;
            else if (t.classList.contains('decrease-qty')) qty = cart[idx].quantity - 1;
            else qty = parseInt(t.value);
            cart[idx].quantity = Math.max(1, isNaN(qty) ? 1 : qty);
            saveCartToLocalStorage();
            const row = document.querySelector(`.cart-item-row[data-product-id="${cart[idx].id}"]`);
            if (row) {
                row.querySelector('.quantity-input').value = cart[idx].quantity;
                const base = (typeof cart[idx].basePriceNgn === 'number') ? cart[idx].basePriceNgn : 0;
                const sel = installationSelections[idx];
                const extraNgn = (sel && sel.extraNgn) ? sel.extraNgn : 0;
                row.querySelector('.item-subtotal').textContent = formatCurrency((base + extraNgn) * cart[idx].quantity);
            }
            calculateCartTotals();
        }

        function handleRemoveItem(e) {
            const btn = e.target.closest('.remove-item-btn');
            if (!btn) return;
            const idx = parseInt(btn.dataset.index);
            if (isNaN(idx) || idx < 0 || idx >= cart.length) return;
            Swal.fire({ title: 'Are you sure?', text: `Remove "${cart[idx].name}" from your cart?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#4e7a1a', cancelButtonColor: '#6c757d', confirmButtonText: 'Yes, remove it!' }).then(r => {
                if (r.isConfirmed) {
                    delete installationSelections[idx];
                    const newSelections = {};
                    Object.keys(installationSelections).forEach(k => {
                        const ki = parseInt(k);
                        if (ki > idx) newSelections[ki - 1] = installationSelections[ki];
                        else if (ki < idx) newSelections[ki] = installationSelections[ki];
                    });
                    Object.keys(installationSelections).forEach(k => delete installationSelections[k]);
                    Object.assign(installationSelections, newSelections);
                    cart.splice(idx, 1);
                    saveCartToLocalStorage();
                    renderCartItems();
                    Swal.fire('Removed!', 'Item removed from cart.', 'success');
                }
            });
        }

        // ── Location / pickup ─────────────────────────────────────────────────
        function openLocationModal() { locationModalOverlay.classList.add('active'); populateLocationList(); }
        function closeLocationModal() { locationModalOverlay.classList.remove('active'); }

        function populateLocationList() {
            locationListContainer.innerHTML = '';
            const activeLocation = new URLSearchParams(window.location.search).get('location');
            availableLocations.forEach(loc => {
                const item = document.createElement('div');
                item.className = 'location-item';
                item.textContent = loc.name;
                item.dataset.locationName = loc.name;
                if (activeLocation === loc.name) { item.classList.add('selected'); selectedLocation = loc.name; }
                item.addEventListener('click', () => {
                    document.querySelectorAll('.location-item').forEach(i => i.classList.remove('selected'));
                    item.classList.add('selected');
                    selectedLocation = item.dataset.locationName;
                });
                locationListContainer.appendChild(item);
            });
        }

        function renderPickupSection() {
            selectedLocation = new URLSearchParams(window.location.search).get('location');
            currentPickupLocationSpan.textContent = selectedLocation || 'Not selected';
            proceedToPaymentBtn.disabled = !selectedLocation;
            pickupPointsList.innerHTML = '';
            noPickupPointsMessage.style.display = 'none';
            const points = pickupLocationsData[selectedLocation];
            if (points?.length) {
                points.forEach(p => {
                    const div = document.createElement('div');
                    div.className = 'pickup-point-item';
                    div.innerHTML = `<h4>${p.name}</h4><p>${p.address}</p><p class="hours">${p.hours}</p>`;
                    pickupPointsList.appendChild(div);
                });
            } else {
                noPickupPointsMessage.style.display = 'block';
            }
        }

        // ── Build items payload with installation data ────────────────────────
        function buildOrderItems(cartSnapshot) {
            return cartSnapshot.map((item, idx) => {
                const base = (typeof item.basePriceNgn === 'number') ? item.basePriceNgn : 0;
                const sel = installationSelections[idx];
                const extraNgn = (sel && sel.extraNgn) ? sel.extraNgn : 0;
                return {
                    id: item.id,
                    name: item.name,
                    basePriceNgn: base,
                    effective_price_ngn: base + extraNgn,
                    quantity: item.quantity,
                    image: item.image || item.imageSrc || null,
                    sku: item.sku || item.id || null,
                    installation_option: sel === null ? null : (sel?.label || null),
                    installation_extra_ngn: extraNgn,
                };
            });
        }

        // ── Save order to DATABASE ────────────────────────────────────────────
        async function saveOrderToDatabase({ paymentId, method, reference, totalNgn, totalUsd, items }) {
            const endpoint = method === 'stripe' ? '/orders/stripe' : '/orders/paystack';
            try {
                const res = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        payment_id: paymentId,
                        reference: reference,
                        total_ngn: totalNgn,
                        total_usd: totalUsd,
                        pickup_location: new URLSearchParams(window.location.search).get('location'),
                        customer_email: emailInput.value,
                        items: items,
                    }),
                });
                const data = await res.json();
                if (!res.ok || !data.success) {
                    console.error('Order save failed:', data);
                    return null;
                }
                console.log('Order saved to database:', data);
                return data;
            } catch (err) {
                console.error('Could not save order to database:', err);
                return null;
            }
        }

        function finaliseOrder() {
            localStorage.removeItem('shoppingCart');
            cart = [];
            Object.keys(installationSelections).forEach(k => delete installationSelections[k]);
            renderCartItems();
            resetPaymentForms();
            window.location.href = '/account/orders';
        }

        // ── Stripe setup - MOUNT IMMEDIATELY ──────────────────────────────────
        // ✅ Get Stripe key from .env
        const stripePk = '{{ env("STRIPE_PUBLISHABLE_KEY") }}';
        let stripe, cardElement;
        let cardMounted = false;

        try {
            stripe = Stripe(stripePk);
            const els = stripe.elements();
            cardElement = els.create('card', {
                style: {
                    base: { fontSize: '14px', color: '#1a1a1a', fontFamily: 'DM Sans, Arial, sans-serif', '::placeholder': { color: '#aab7c4' } },
                    invalid: { color: '#dc2626' }
                }
            });
            // Mount the card element immediately
            cardElement.mount('#card-element');
            cardMounted = true;
            cardElement.on('change', (event) => {
                if (event.error) {
                    cardErrors.textContent = event.error.message;
                } else {
                    cardErrors.textContent = '';
                }
            });
        } catch (e) { console.error('Stripe init error:', e); }

        // ── Event listeners ───────────────────────────────────────────────────
        proceedToPaymentBtn.addEventListener('click', () => {
            if (!selectedLocation) { Swal.fire({ icon: 'warning', title: 'Pickup Location Required', text: 'Please select a pickup location before proceeding to payment.', confirmButtonColor: '#4e7a1a' }); return; }
            if (cart.length === 0) { showMessage('danger', 'Your cart is empty.'); return; }
            paymentFormsSection.classList.remove('hidden');
            paymentFormsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        selectStripeBtn?.addEventListener('click', () => {
            resetPaymentForms();
            stripeSection.classList.remove('hidden');
            if (cart.length === 0) { showMessage('danger', 'Your cart is empty.'); return; }
            cardErrors.textContent = '';
            // Card is already mounted - no need to mount again
        });

        selectPaystackBtn?.addEventListener('click', () => {
            resetPaymentForms();
            paystackSection.classList.remove('hidden');
            if (cart.length === 0) showMessage('danger', 'Your cart is empty.');
        });

        clearCartBtn.addEventListener('click', () => {
            if (cart.length === 0) { Swal.fire({ icon: 'info', title: 'Cart Already Empty', text: 'Your shopping cart is already empty!', confirmButtonColor: '#4e7a1a' }); return; }
            Swal.fire({ title: 'Are you sure?', text: 'Clear all items from your cart?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#4e7a1a', cancelButtonColor: '#6c757d', confirmButtonText: 'Yes, clear it!' }).then(r => {
                if (r.isConfirmed) {
                    cart = [];
                    Object.keys(installationSelections).forEach(k => delete installationSelections[k]);
                    saveCartToLocalStorage(); renderCartItems(); updateCartDisplayHeader();
                    resetPaymentForms(); amountInput.value = '0.50'; proceedToPaymentBtn.disabled = true;
                    Swal.fire('Cleared!', 'Your cart has been emptied.', 'success');
                }
            });
        });

        closeLocationModalBtn?.addEventListener('click', closeLocationModal);
        locationModalOverlay?.addEventListener('click', e => { if (e.target === locationModalOverlay) closeLocationModal(); });
        confirmLocationBtn?.addEventListener('click', () => {
            if (selectedLocation) { const url = new URL(window.location.href); url.searchParams.set('location', selectedLocation); window.location.href = url.toString(); }
            else closeLocationModal();
        });
        clearLocationFilterBtn?.addEventListener('click', () => { const url = new URL(window.location.href); url.searchParams.delete('location'); window.location.href = url.toString(); });
        changePickupLocationBtn?.addEventListener('click', openLocationModal);

        // ── Stripe submit ─────────────────────────────────────────────────────
        stripeForm?.addEventListener('submit', async e => {
            e.preventDefault();
            clearMessages();
            if (!cardMounted) { showMessage('danger', 'Card form not ready. Please refresh.'); return; }
            showLoading(stripeSubmitButton, stripeSpinner);
            cardErrors.textContent = '';

            const { usdCents, totalNgn } = calculateCartTotals();
            if (usdCents < 50) { showMessage('danger', 'Minimum payment is $0.50.'); hideLoading(stripeSubmitButton, stripeSpinner); return; }
            if (cart.length === 0) { showMessage('danger', 'Your cart is empty.'); hideLoading(stripeSubmitButton, stripeSpinner); return; }

            const cartSnapshot = [...cart];
            const totalUsd = parseFloat(amountInput.value);
            const orderItems = buildOrderItems(cartSnapshot);

            try {
                const res = await fetch('/stripe/create-payment-intent', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                    body: JSON.stringify({ amount: usdCents, cart: cartSnapshot }),
                });
                if (!res.ok) throw new Error(`Server error (${res.status})`);
                const data = await res.json();
                if (data.error) throw new Error(data.error);

                const { paymentIntent, error } = await stripe.confirmCardPayment(data.clientSecret, {
                    payment_method: { card: cardElement, billing_details: { email: emailInput.value } }
                });

                if (error) {
                    cardErrors.textContent = error.message;
                    showMessage('danger', 'Payment failed: ' + error.message);
                } else if (paymentIntent.status === 'succeeded') {
                    showMessage('info', 'Saving your order…');
                    await saveOrderToDatabase({
                        paymentId: paymentIntent.id,
                        method: 'stripe',
                        reference: paymentIntent.id,
                        totalNgn,
                        totalUsd,
                        items: orderItems,
                    });
                    showMessage('success', 'Payment successful! Redirecting…');
                    setTimeout(finaliseOrder, 800);
                }
            } catch (err) {
                showMessage('danger', 'Payment error: ' + err.message);
            } finally {
                hideLoading(stripeSubmitButton, stripeSpinner);
            }
        });

        // ── Paystack submit ───────────────────────────────────────────────────
        paystackSubmitButton?.addEventListener('click', async () => {
            clearMessages();

            const { totalNgn } = calculateCartTotals();
            const email        = emailInput.value.trim();

            if (!email || !email.includes('@')) { showMessage('danger', 'Please enter a valid email address.'); return; }
            if (Math.round(totalNgn * 100) < 50) { showMessage('danger', 'Amount too small for payment.'); return; }
            if (cart.length === 0) { showMessage('danger', 'Your cart is empty.'); return; }

            const orderItems = buildOrderItems([...cart]);

            showLoading(paystackSubmitButton, paystackSpinner);
            showMessage('info', 'Preparing payment…');

            // Save cart server-side BEFORE the popup. The server computes all prices
            // from the DB, generates the reference, and returns both. The browser
            // never picks the reference — prevents reference-substitution attacks.
            let ref, serverTotal;
            try {
                const r = await fetch('/paystack/save-checkout', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                    body: JSON.stringify({
                        customer_email: email,
                        items: cart.map((item, idx) => ({
                            product_id:          item.id,
                            quantity:            item.quantity,
                            installation_option: installationSelections[idx]?.label ?? null,
                        })),
                        fulfillment: { method: 'pickup', pickup_location: selectedLocation },
                    }),
                });
                if (!r.ok) throw new Error('Could not prepare checkout.');
                const saved = await r.json();
                if (!saved.reference) throw new Error('Server did not return a payment reference.');
                ref          = saved.reference;
                serverTotal  = saved.total_ngn;
            } catch (err) {
                showMessage('danger', err.message + ' Please try again.');
                hideLoading(paystackSubmitButton, paystackSpinner);
                return;
            }

            clearMessages();
            const finalKobo = Math.round((serverTotal ?? totalNgn) * 100);

            PaystackPop.setup({
                key: paystackPk, email, amount: finalKobo, currency: 'NGN', ref,
                onClose() {
                    hideLoading(paystackSubmitButton, paystackSpinner);
                    showMessage('info', 'Payment window closed.');
                },
                async callback(response) {
                    hideLoading(paystackSubmitButton, paystackSpinner);
                    if (response.status !== 'success') {
                        showMessage('danger', 'Transaction failed. Ref: ' + response.reference);
                        return;
                    }
                    await confirmPaystackOrder(response.reference, orderItems, totalNgn, email);
                },
            }).openIframe();
        });

        // ── Confirm order with Paystack (verify + create, idempotent) ─────────
        async function confirmPaystackOrder(reference, orderItems, totalNgn, email) {
            clearMessages();
            showMessage('info', 'Confirming your order…');
            showLoading(paystackSubmitButton, paystackSpinner);
            try {
                const res  = await fetch('/paystack/confirm-order', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                    body: JSON.stringify({ reference }),
                });
                const data = await res.json();
                if (data.success || data.status === 'success') {
                    showMessage('success', 'Order confirmed! Redirecting…');
                    setTimeout(finaliseOrder, 800);
                } else {
                    // Payment received — webhook will create the order if browser path failed.
                    showMessage('info', 'Payment received. Order will appear in your account shortly. Ref: ' + reference);
                    setTimeout(finaliseOrder, 3000);
                }
            } catch {
                // Never show "failed" after a successful payment.
                showMessage('info', 'Payment received. Ref: ' + reference + '. Your order will appear in your account shortly.');
                setTimeout(finaliseOrder, 3000);
            } finally {
                hideLoading(paystackSubmitButton, paystackSpinner);
            }
        }

        // ── Init ──────────────────────────────────────────────────────────────
        loadCartFromLocalStorage();
        renderCartItems();
        updateCartDisplayHeader();
        renderPickupSection();

        // Redirect-back: runs AFTER loadCartFromLocalStorage so cart is populated.
        const paystackRef = new URLSearchParams(window.location.search).get('reference');
        if (paystackRef) {
            history.replaceState(null, '', window.location.pathname);
            const { totalNgn } = calculateCartTotals();
            confirmPaystackOrder(paystackRef, buildOrderItems([...cart]), totalNgn, emailInput.value.trim());
        }

        window.updateCartAmounts = () => { renderCartItems(); calculateCartTotals(); };
    });
</script>

@endsection