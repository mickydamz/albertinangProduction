@extends('layouts.simslayout')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <style>
        :root {
            --red:#dc2626;--red-bg:#fef2f2;--red-bd:#fecaca;
            --ds-color-link:#375ea9;--ds-color-star:#f59e0b;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:var(--font-body);color:var(--ink);background:#f3f4f6;min-height:100vh;}
        img{display:block;max-width:100%;height:auto;}
        button{cursor:pointer;font-family:var(--font-body);}
        a{text-decoration:none;}

        /* ── Page wrapper ─────────────────────────────────────────────────────── */
        .co-page{max-width:1260px;margin:0 auto;padding:1.5rem 1.25rem 3rem;}

        /* ── Breadcrumb ───────────────────────────────────────────────────────── */
        .co-breadcrumb{display:flex;align-items:center;gap:6px;font-size:12.5px;color:var(--ink3);margin-bottom:1.5rem;}
        .co-breadcrumb a{color:var(--ink3);transition:color .15s;}
        .co-breadcrumb a:hover{color:var(--g600);}
        .co-breadcrumb .sep{color:var(--ink4);}
        .co-breadcrumb .current{color:var(--ink);font-weight:600;}

        /* ── Progress steps ───────────────────────────────────────────────────── */
        .co-steps{display:flex;align-items:center;margin-bottom:2rem;gap:0;}
        .co-step{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:var(--ink4);}
        .co-step.active{color:var(--ink);}
        .co-step .step-circle{width:26px;height:26px;border-radius:50%;border:2px solid var(--border);background:var(--surf);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--ink4);flex-shrink:0;transition:all .2s;}
        .co-step.active .step-circle{border-color:var(--g500);background:var(--g500);color:#fff;}
        .co-step.done .step-circle{border-color:var(--g400);background:var(--g50);color:var(--g600);}
        .co-step-line{flex:1;height:1px;background:var(--border);margin:0 12px;min-width:24px;}

        /* ── Two-column layout ────────────────────────────────────────────────── */
        .co-layout{display:grid;grid-template-columns:1fr 380px;gap:1.5rem;align-items:start;}

        /* ── Section card ─────────────────────────────────────────────────────── */
        .co-card{background:var(--surf);border-radius:var(--radius-lg);border:1.5px solid var(--border);box-shadow:var(--shadow-sm);overflow:hidden;margin-bottom:1rem;}
        .co-card:last-child{margin-bottom:0;}

        /* ── Section header inside card ───────────────────────────────────────── */
        .co-sec-head{display:flex;align-items:center;gap:12px;padding:1.25rem 1.5rem;border-bottom:1.5px solid var(--border);}
        .co-sec-num{width:30px;height:30px;border-radius:50%;background:var(--g500);color:#fff;font-family:var(--font-head);font-size:14px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .co-sec-title{font-family:var(--font-head);font-size:1.1rem;font-weight:800;color:var(--ink);line-height:1;}
        .co-sec-subtitle{font-size:12px;font-weight:400;color:var(--ink3);margin-top:2px;}
        .co-card-body{padding:1.5rem;}

        /* ── Cart table ───────────────────────────────────────────────────────── */
        .cart-table{width:100%;border-collapse:collapse;}
        .cart-table th{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--ink3);padding:0 1rem 10px;border-bottom:1.5px solid var(--border);text-align:left;}
        .cart-table th:nth-child(2),.cart-table th:nth-child(3),.cart-table th:nth-child(4){text-align:center;}
        .cart-table td{padding:1.125rem 1rem;border-bottom:1px solid var(--border);vertical-align:middle;color:var(--ink2);}
        .cart-item-row:last-child td{border-bottom:none;}
        .cart-item-row{transition:background .15s;}
        .cart-item-row:hover td{background:#fafafa;}

        .cart-item-info{display:flex;align-items:flex-start;gap:14px;}
        .cart-item-info img{width:76px;height:76px;object-fit:contain;border-radius:var(--radius-sm);border:1px solid var(--border);background:var(--surf2);padding:6px;flex-shrink:0;}
        .item-name{font-size:13.5px;font-weight:600;color:var(--ink);line-height:1.4;margin-bottom:4px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
        .item-price-single{font-size:12px;color:var(--ink3);}

        /* installation options */
        .install-opts-wrap{margin-top:10px;padding:10px 12px;background:var(--surf2);border:1px solid var(--border);border-radius:var(--radius-sm);}
        .install-opts-title{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--ink3);margin-bottom:8px;}
        .install-opts-list{display:flex;flex-direction:column;gap:5px;}
        .install-opt-label{display:flex;align-items:flex-start;gap:8px;cursor:pointer;padding:6px 10px;border-radius:var(--radius-sm);border:1.5px solid transparent;transition:border-color .15s,background .15s;}
        .install-opt-label:hover{background:var(--g50);border-color:var(--g200);}
        .install-opt-label.selected{background:var(--g50);border-color:var(--g500);}
        .install-opt-radio{margin-top:3px;accent-color:var(--g500);flex-shrink:0;}
        .install-opt-text{font-size:12px;color:var(--ink2);line-height:1.45;}
        .install-opt-text strong{color:var(--g600);font-weight:700;}
        .install-opt-text em{font-style:normal;color:var(--ink4);font-size:11px;}
        .install-opt-text small{display:block;font-size:11px;color:var(--ink4);}
        .install-opt-label.reject-opt:hover{background:var(--red-bg);border-color:var(--red-bd);}
        .install-opt-label.reject-opt.selected{background:var(--red-bg);border-color:var(--red);}

        /* Qty controls */
        .quantity-controls{display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;width:fit-content;margin:0 auto;}
        .quantity-btn{width:32px;height:32px;background:var(--surf2);border:none;font-size:16px;font-weight:700;color:var(--ink2);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background .15s,color .15s;flex-shrink:0;}
        .quantity-btn:hover{background:var(--g100);color:var(--g700);}
        .quantity-input{width:44px;height:32px;text-align:center;border:none;border-left:1px solid var(--border);border-right:1px solid var(--border);font-size:14px;font-weight:600;color:var(--ink);background:#fff;outline:none;-moz-appearance:textfield;}
        .quantity-input::-webkit-outer-spin-button,.quantity-input::-webkit-inner-spin-button{-webkit-appearance:none;}

        .item-subtotal{font-family:var(--font-head);font-size:15px;font-weight:800;color:var(--ink);white-space:nowrap;text-align:center;display:block;}
        .remove-item-btn{background:none;border:none;color:var(--ink4);font-size:16px;cursor:pointer;padding:6px;border-radius:var(--radius-sm);transition:color .15s,background .15s;display:flex;align-items:center;justify-content:center;}
        .remove-item-btn:hover{color:var(--red);background:var(--red-bg);}

        /* Cart action bar */
        .cart-actions{display:flex;justify-content:space-between;padding:1rem 1.5rem;border-top:1px solid var(--border);gap:10px;flex-wrap:wrap;background:var(--surf2);}
        .continue-shopping-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:var(--surf);color:var(--ink2);border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-weight:600;transition:all .2s;}
        .continue-shopping-btn:hover{border-color:var(--g400);color:var(--g600);background:var(--g50);}
        .clear-cart-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:var(--surf);color:var(--ink3);border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-weight:600;transition:all .2s;}
        .clear-cart-btn:hover{background:var(--red-bg);color:var(--red);border-color:var(--red-bd);}

        /* Empty cart */
        .empty-cart-message-page{text-align:center;padding:3.5rem 1.5rem;}
        .empty-cart-message-page .empty-icon{font-size:3rem;color:var(--ink4);margin-bottom:1rem;}
        .empty-cart-message-page p:first-of-type{font-family:var(--font-head);font-size:1.2rem;font-weight:700;color:var(--ink);margin-bottom:.5rem;}
        .empty-cart-message-page p{font-size:13.5px;color:var(--ink3);margin-bottom:1.5rem;}
        .empty-cart-message-page a{display:inline-flex;align-items:center;gap:8px;padding:11px 26px;background:var(--g500);color:#fff;border-radius:var(--radius-sm);font-size:14px;font-weight:700;transition:all .2s;}
        .empty-cart-message-page a:hover{background:var(--g700);transform:translateY(-1px);}

        /* ── Fulfillment section ─────────────────────────────────────────────── */
        .pickup-section{background:var(--surf);border-radius:var(--radius-lg);border:1px solid var(--border);box-shadow:var(--shadow-sm);overflow:visible;}
        .fulfillment-toggle{display:grid;grid-template-columns:1fr 1fr;border-bottom:1px solid var(--border);border-radius:var(--radius-lg) var(--radius-lg) 0 0;overflow:hidden;}
        .fulfillment-tab{padding:14px 16px;background:var(--surf2);border:none;border-bottom:3px solid transparent;font-size:13.5px;font-weight:700;color:var(--ink3);text-align:center;transition:all .18s;display:flex;align-items:center;justify-content:center;gap:8px;}
        .fulfillment-tab:hover{background:var(--surf);color:var(--ink);}
        .fulfillment-tab.active{background:var(--surf);color:var(--g600);border-bottom-color:var(--g500);}
        .fulfillment-panel{display:none;padding:1.5rem;}
        .fulfillment-panel.active{display:block;}
        .field-label{font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--ink3);display:block;margin-bottom:7px;}
        .select-field{-webkit-appearance:none;appearance:none;width:100%;padding:11px 38px 11px 14px;border:1.5px solid var(--border);border-radius:10px;font-size:14px;font-weight:500;font-family:var(--font-body);color:var(--ink);outline:none;background-color:#fff;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='none' stroke='%236b7280' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 13px center;cursor:pointer;margin-bottom:14px;transition:border-color .18s,box-shadow .18s;min-height:46px;}
        .select-field:hover{border-color:var(--border2);}
        .select-field:focus{border-color:var(--g500);box-shadow:0 0 0 3px rgba(90,171,31,.12);}
        .select-field:disabled{background:var(--surf2);color:var(--ink4);cursor:not-allowed;opacity:.7;}
        .delivery-fee-display{display:flex;justify-content:space-between;align-items:center;padding:12px 14px;background:var(--g50);border:1.5px solid var(--border2);border-radius:var(--radius-sm);font-size:13.5px;font-weight:700;color:var(--g700);margin-top:4px;}
        .shipping-address-wrap{margin-top:1rem;}
        .shipping-address-textarea{width:100%;min-height:80px;padding:10px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13.5px;color:var(--ink);background:#fff;resize:vertical;font-family:var(--font-body);transition:border-color .15s,box-shadow .15s;outline:none;}
        .shipping-address-textarea:hover{border-color:var(--border2);}
        .shipping-address-textarea:focus{border-color:var(--g500);box-shadow:0 0 0 3px rgba(90,171,31,.12);}
        .shipping-address-textarea::placeholder{color:var(--ink4);}

        /* Pickup points */
        #currentPickupLocation{font-weight:700;color:var(--g600);}
        #changePickupLocationBtn{background:none;color:var(--g600);border:1.5px solid var(--border2);padding:4px 12px;border-radius:var(--radius-sm);font-size:12px;font-weight:700;transition:all .15s;}
        #changePickupLocationBtn:hover{background:var(--g50);border-color:var(--g400);}
        .pickup-point-item{background:var(--surf);border:1.5px solid var(--border);border-radius:var(--radius);padding:14px 16px;transition:all .18s;cursor:pointer;margin-bottom:8px;position:relative;}
        .pickup-point-item:hover{border-color:var(--g400);box-shadow:0 2px 8px rgba(78,122,26,.10);}
        .pickup-point-item.selected{background:var(--g50);border-color:var(--g500);box-shadow:0 2px 8px rgba(78,122,26,.15);}
        .pickup-point-item.selected::after{content:'\f058';font-family:'Font Awesome 6 Free';font-weight:900;position:absolute;top:14px;right:14px;color:var(--g500);font-size:16px;}
        .pickup-point-item h4{font-family:var(--font-head);font-size:14px;font-weight:700;color:var(--ink);margin-bottom:4px;padding-right:26px;}
        .pickup-point-item p{font-size:12.5px;color:var(--ink3);margin-bottom:2px;line-height:1.5;}
        .pickup-point-item .hours{font-size:11.5px;color:var(--g600);font-weight:600;}
        .pickup-hint{font-size:12.5px;color:var(--ink3);margin-bottom:10px;padding:10px 14px;background:var(--surf2);border-radius:var(--radius-sm);border-left:3px solid var(--g400);}

        /* ── Order summary (right panel) ─────────────────────────────────────── */
        .co-summary-sticky{position:sticky;top:5.5rem;}
        .summary-item{display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--border);font-size:14px;color:var(--ink3);}
        .summary-item:last-of-type{border-bottom:none;}
        .summary-item span:last-child{font-weight:600;color:var(--ink);}
        .summary-total{display:flex;justify-content:space-between;align-items:center;padding:16px 0 0;margin-top:6px;border-top:2px solid var(--border);font-family:var(--font-head);}
        .summary-total span:first-child{font-size:1rem;font-weight:800;color:var(--ink);}
        .summary-total span:last-child{font-size:1.65rem;font-weight:800;color:var(--ink);letter-spacing:-.5px;}
        .summary-item.coupon-row span:first-child{color:var(--g600);font-weight:600;}
        .summary-item.coupon-row span:last-child{color:var(--g600);}
        .strikethrough{text-decoration:line-through;color:var(--ink4);font-size:12px;font-weight:400;margin-left:4px;}

        /* Coupon */
        .coupon-wrap{padding:14px 0 0;}
        .coupon-input-row{display:flex;gap:6px;}
        .coupon-input-row input{flex:1;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font-body);color:var(--ink);outline:none;transition:border-color .15s,box-shadow .15s;text-transform:uppercase;letter-spacing:.08em;background:#fff;}
        .coupon-input-row input:focus{border-color:var(--g400);box-shadow:0 0 0 3px rgba(78,122,26,.1);}
        .coupon-input-row input:disabled{background:var(--surf2);color:var(--ink3);}
        .coupon-apply-btn{padding:10px 16px;background:var(--surf2);color:var(--ink2);border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-weight:700;white-space:nowrap;transition:all .15s;}
        .coupon-apply-btn:hover:not(:disabled){background:var(--g50);border-color:var(--g400);color:var(--g600);}
        .coupon-apply-btn:disabled{opacity:.5;cursor:not-allowed;}
        .coupon-apply-btn.is-remove{background:var(--red-bg);color:var(--red);border-color:var(--red-bd);}
        .coupon-apply-btn.is-remove:hover{background:#fde8e8;}
        .coupon-msg{margin-top:8px;font-size:12.5px;min-height:18px;display:flex;align-items:center;gap:5px;}
        .coupon-msg.success{color:var(--g600);}
        .coupon-msg.error{color:var(--red);}

        /* CTA button */
        .checkout-btn-summary{width:100%;margin-top:1.25rem;padding:17px 20px;background:var(--g500);color:#fff;border:none;border-radius:10px;font-family:var(--font-head);font-size:1.0625rem;font-weight:800;letter-spacing:-.1px;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:9px;}
        .checkout-btn-summary:hover:not([disabled]){background:var(--g700);transform:translateY(-1px);box-shadow:0 6px 20px rgba(61,128,18,.28);}
        .checkout-btn-summary[disabled]{background:#d1d5db;color:#9ca3af;cursor:not-allowed;transform:none;box-shadow:none;}
        .checkout-btn-summary.btn-needs-action{background:#d97706;}
        .checkout-btn-summary.btn-needs-action:hover{background:#b45309;box-shadow:0 6px 20px rgba(217,119,6,.28);}

        /* Trust badge */
        .trust-badge{display:flex;align-items:center;justify-content:center;gap:6px;margin-top:10px;font-size:11.5px;color:var(--ink4);}
        .trust-badge i{color:var(--g500);}

        /* Payment section */
        #payment-forms-section .pmts-head{font-family:var(--font-head);font-size:1rem;font-weight:700;color:var(--ink);margin-bottom:1rem;}
        .payment-options{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:1rem;}
        .payment-method-btn{padding:12px 14px;border-radius:var(--radius-sm);border:2px solid var(--border);background:var(--surf);font-size:13.5px;font-weight:700;color:var(--ink2);transition:all .18s;display:flex;align-items:center;justify-content:center;gap:8px;}
        .payment-method-btn:hover{border-color:var(--g400);color:var(--g600);background:var(--g50);}
        #select-stripe{border-color:var(--border);}
        #select-stripe:hover{border-color:#3b5bdb;color:#3b5bdb;background:#eff3ff;}
        #select-paystack{border-color:var(--border);}
        #select-paystack:hover{border-color:var(--g500);color:var(--g600);background:var(--g50);}
        #stripe-payment-section,#paystack-payment-section{border:1.5px solid var(--border);background:var(--surf2);border-radius:var(--radius);padding:1.125rem;margin-top:.75rem;}
        #stripe-payment-section h3,#paystack-payment-section h3{font-family:var(--font-head);font-size:14px;font-weight:700;color:var(--ink);margin-bottom:.875rem;}
        #stripe-submit-button{width:100%;background:#3b5bdb;color:#fff;padding:12px;border:none;border-radius:var(--radius-sm);font-size:14px;font-weight:700;transition:all .18s;display:flex;align-items:center;justify-content:center;gap:8px;}
        #stripe-submit-button:hover{background:#2f4ac5;transform:translateY(-1px);box-shadow:0 4px 14px rgba(59,91,219,.3);}
        #paystack-submit-button{width:100%;background:var(--g500);color:#fff;padding:12px;border:none;border-radius:var(--radius-sm);font-size:14px;font-weight:700;transition:all .18s;display:flex;align-items:center;justify-content:center;gap:8px;}
        #paystack-submit-button:hover{background:var(--g700);transform:translateY(-1px);box-shadow:0 4px 14px rgba(78,122,26,.3);}
        #card-element{border:1.5px solid var(--border);padding:11px 14px;border-radius:var(--radius-sm);background:#fff;margin-bottom:14px;transition:border-color .15s;}
        #card-element:focus-within{border-color:var(--g400);box-shadow:0 0 0 3px rgba(78,122,26,.1);}
        #main-checkout-form label{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--ink3);display:block;margin-bottom:6px;}
        #main-checkout-form input{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13.5px;font-family:var(--font-body);color:var(--ink);outline:none;transition:border-color .15s,box-shadow .15s;margin-bottom:14px;background:#fff;}
        #main-checkout-form input:focus{border-color:var(--g400);box-shadow:0 0 0 3px rgba(78,122,26,.1);}
        #main-checkout-form input[readonly]{background:var(--surf2);color:var(--ink3);}
        #messages{margin-bottom:1rem;}

        /* ── Location modal ──────────────────────────────────────────────────── */
        .location-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:2000;display:flex;justify-content:center;align-items:center;opacity:0;transition:opacity .22s;pointer-events:none;}
        .location-modal-overlay.active{opacity:1;pointer-events:auto;}
        .location-modal-content{background:#fff;padding:1.875rem;border-radius:var(--radius-lg);box-shadow:var(--shadow-md);max-width:500px;width:92%;transform:translateY(-20px);opacity:0;transition:transform .25s ease-out,opacity .22s ease-out;position:relative;}
        .location-modal-overlay.active .location-modal-content{transform:translateY(0);opacity:1;}
        .location-modal-content h2{font-family:var(--font-head);font-size:1.2rem;font-weight:800;color:var(--ink);margin-bottom:1.25rem;text-align:center;}
        .location-modal-close{position:absolute;top:16px;right:16px;background:var(--surf2);border:1px solid var(--border);border-radius:50%;width:32px;height:32px;font-size:13px;color:var(--ink3);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .15s;}
        .location-modal-close:hover{background:var(--red-bg);color:var(--red);border-color:var(--red-bd);}
        .location-list{display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin-bottom:1.25rem;max-height:288px;overflow-y:auto;padding-right:4px;}
        @media(min-width:480px){.location-list{grid-template-columns:repeat(3,1fr);}}
        @media(min-width:640px){.location-list{grid-template-columns:repeat(4,1fr);}}
        .location-item{background:var(--surf2);border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:10px;text-align:center;cursor:pointer;font-size:13px;font-weight:500;color:var(--ink);transition:all .15s;}
        .location-item:hover{border-color:var(--g400);background:var(--g50);color:var(--g700);}
        .location-item.selected{background:var(--g500);color:#fff;border-color:var(--g600);font-weight:700;box-shadow:0 2px 8px rgba(78,122,26,.2);}
        .location-actions{display:flex;justify-content:space-between;gap:10px;}
        .location-confirm-btn{background:var(--g500);color:#fff;border:none;padding:12px 20px;border-radius:var(--radius-sm);font-size:14px;font-weight:700;transition:background .15s;cursor:pointer;flex:1;}
        .location-confirm-btn:hover{background:var(--g700);}
        .location-clear-btn{background:var(--surf2);color:var(--ink3);border:1.5px solid var(--border);padding:12px 20px;border-radius:var(--radius-sm);font-size:14px;font-weight:600;transition:all .15s;cursor:pointer;flex:1;}
        .location-clear-btn:hover{background:var(--border);color:var(--ink2);}

        /* ── Misc ────────────────────────────────────────────────────────────── */
        .hidden{display:none!important;}
        .text-danger{color:var(--red);font-size:12px;margin-top:4px;}
        .spinner-border{margin-right:.5rem;}
        .mobile-menu-overlay{position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:50;opacity:0;pointer-events:none;transition:opacity .2s;}
        .mobile-menu-overlay.active{opacity:1;pointer-events:auto;}
        .mobile-menu{position:fixed;top:0;left:-100%;width:91.666667%;max-width:320px;height:100vh;background:white;box-shadow:0 0 20px rgba(0,0,0,.15);z-index:50;transition:left .2s;overflow-y:auto;}
        .mobile-menu.active{left:0;}
        .mobile-menu-header{display:flex;justify-content:space-between;align-items:center;padding:12px;background:#f97316;color:#fff;}
        .mobile-menu-header h3{font-size:1.1rem;font-weight:600;color:#fff;margin:0;}
        .mobile-menu-close{font-size:1.25rem;color:#fff;cursor:pointer;background:none;border:none;padding:4px;transition:transform .2s;line-height:1;}
        .mobile-menu-close:hover{transform:rotate(45deg);}
        .mobile-menu-content{padding:12px;}

        /* Flash attention on fulfillment section */
        .pickup-section.flash-attention{animation:pickupFlash 1.2s ease;}
        @keyframes pickupFlash{0%,100%{box-shadow:0 0 0 0 rgba(217,119,6,0);}40%,60%{box-shadow:0 0 0 4px rgba(217,119,6,.35);}}

        /* Processing overlay */
        .processing-overlay{position:fixed;inset:0;z-index:5000;background:rgba(17,24,39,.75);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .25s;}
        .processing-overlay.active{opacity:1;pointer-events:auto;}
        .processing-card{background:#fff;border-radius:var(--radius-lg);padding:36px 44px;text-align:center;max-width:340px;width:88%;box-shadow:0 20px 60px rgba(0,0,0,.3);transform:translateY(14px);transition:transform .25s;}
        .processing-overlay.active .processing-card{transform:translateY(0);}
        .processing-spinner{width:52px;height:52px;margin:0 auto 20px;border:4px solid var(--g100);border-top-color:var(--g500);border-radius:50%;animation:spin .75s linear infinite;}
        .processing-title{font-family:var(--font-head);font-size:1.1rem;font-weight:700;color:var(--ink);margin-bottom:8px;}
        .processing-sub{font-size:13px;color:var(--ink3);line-height:1.5;}
        .processing-card.is-success .processing-spinner{border:4px solid #bbf7d0;border-top-color:#16a34a;animation:none;position:relative;}
        .processing-card.is-success .processing-spinner::after{content:'\f00c';font-family:'Font Awesome 6 Free';font-weight:900;position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#16a34a;font-size:22px;}

        @keyframes spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}

        /* ── Responsive ───────────────────────────────────────────────────────── */
        @media(max-width:1023px){
            .co-layout{grid-template-columns:1fr;}
            .co-summary-sticky{position:static;}
        }
        @media(max-width:767px){
            .co-page{padding:1rem .875rem 2.5rem;}
            .co-steps{display:none;}
            .co-layout{gap:.875rem;}
            .checkout-btn-summary{font-size:1rem;padding:14px;}
            .cart-table thead{display:none;}
            .cart-table,.cart-table tbody,.cart-item-row,.cart-table td{display:block;width:100%;}
            .cart-item-row td{padding:10px 1rem;border-bottom:1px dashed var(--border);}
            .cart-item-row:last-child td:last-child{border-bottom:none;}
            .cart-table td::before{content:attr(data-label);font-size:11px;font-weight:700;color:var(--ink3);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:4px;}
            .cart-table td:first-child::before{display:none;}
            .cart-table td:nth-child(3),.cart-table td:nth-child(4){display:flex;justify-content:space-between;align-items:center;}
            .cart-item-info img{width:60px;height:60px;}
            .item-name{font-size:13px;}
            .quantity-controls{margin:0;}
            .item-subtotal{text-align:right;}
            .cart-actions{flex-direction:column;}
            .continue-shopping-btn,.clear-cart-btn{width:100%;justify-content:center;}
            .payment-options{grid-template-columns:1fr;}
        }
    </style>

    <div class="mobile-menu-overlay"></div>
    <div class="mobile-menu">
        <div class="mobile-menu-header">
            <h3>Shop Categories</h3>
            <button class="mobile-menu-close" aria-label="Close navigation menu">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mobile-menu-content" id="mobileCategoriesMenu"></div>
    </div>

    <div class="co-page">

        {{-- Breadcrumb --}}
        <nav class="co-breadcrumb" aria-label="breadcrumb">
            <a href="/"><i class="fas fa-home" style="font-size:11px;"></i> Home</a>
            <span class="sep">/</span>
            <span class="current">Checkout</span>
        </nav>

        <div class="co-layout">

            {{-- ── LEFT COLUMN ── --}}
            <div class="co-left">

                {{-- Section 1: Basket --}}
                <div class="co-card">
                    <div class="co-sec-head">
                        <div class="co-sec-num">1</div>
                        <div>
                            <div class="co-sec-title">Your Basket</div>
                            <div class="co-sec-subtitle">Review your items before paying</div>
                        </div>
                    </div>

                    <div class="co-card-body" style="padding:1.5rem 1.5rem 0;">
                        <div id="cartItemsDisplay"></div>
                    </div>

                    <div class="cart-actions">
                        <a href="/" class="continue-shopping-btn">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>
                        <button id="clearCartBtn" class="clear-cart-btn">
                            <i class="fas fa-trash-alt"></i> Clear Cart
                        </button>
                    </div>
                </div>

                {{-- Section 2: Delivery / Pickup --}}
                <div class="pickup-section" id="fulfillmentSection">
                    <div class="co-sec-head" style="border-radius:0;">
                        <div class="co-sec-num">2</div>
                        <div>
                            <div class="co-sec-title">Delivery &amp; Collection</div>
                            <div class="co-sec-subtitle">Choose how you want to receive your order</div>
                        </div>
                    </div>

                    @if(!$deliveryEnabled && !$pickupEnabled)
                        <div style="padding:1rem 1.5rem;font-size:13.5px;color:#92400e;background:#fef3c7;border-left:4px solid #f59e0b;">
                            <i class="fas fa-exclamation-triangle" style="margin-right:6px;"></i>
                            Order fulfillment is temporarily unavailable. Please check back later.
                        </div>
                    @endif

                    <div class="fulfillment-toggle" id="fulfillmentToggle">
                        @if($pickupEnabled)
                        <button type="button" class="fulfillment-tab" id="tabPickup" data-method="pickup">
                            <i class="fas fa-store"></i> Collect in Store
                        </button>
                        @else
                        <button type="button" class="fulfillment-tab" id="tabPickup" data-method="pickup" style="display:none;"></button>
                        @endif

                        @if($deliveryEnabled)
                        <button type="button" class="fulfillment-tab" id="tabDelivery" data-method="delivery">
                            <i class="fas fa-truck"></i> Home Delivery
                        </button>
                        @else
                        <button type="button" class="fulfillment-tab" id="tabDelivery" data-method="delivery" style="display:none;"></button>
                        @endif
                    </div>

                    {{-- Pickup panel --}}
                    <div class="fulfillment-panel" id="pickupPanel">
                        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:1rem;padding:12px 14px;background:var(--surf2);border-radius:var(--radius-sm);border:1px solid var(--border);font-size:13.5px;color:var(--ink2);">
                            <i class="fas fa-map-marker-alt" style="color:var(--g500);font-size:15px;flex-shrink:0;"></i>
                            <span>Pickup location: <strong id="currentPickupLocation" style="color:var(--g600);">Not selected</strong></span>
                            <button id="changePickupLocationBtn" style="margin-left:auto;">Change</button>
                        </div>
                        <p class="pickup-hint" id="pickupPointHint" style="display:none;">
                            <i class="fas fa-info-circle" style="margin-right:5px;color:var(--g500);"></i>
                            Choose a pickup point below to continue.
                        </p>
                        <div id="pickupPointsList" style="display:none;margin-top:8px;"></div>
                        <p id="noPickupPointsMessage" style="display:none;text-align:center;padding:1.25rem;background:var(--surf2);border-radius:var(--radius-sm);font-size:13px;color:var(--ink3);">
                            No pickup points available for this location yet.
                        </p>
                    </div>

                    {{-- Delivery panel --}}
                    <div class="fulfillment-panel" id="deliveryPanel">
                        <div class="shipping-address-wrap" style="margin-top:0;margin-bottom:1rem;">
                            <label class="field-label" for="shippingAddressInput">
                                Street / Delivery Address <span style="color:var(--red);">*</span>
                            </label>
                            <textarea
                                class="shipping-address-textarea"
                                id="shippingAddressInput"
                                placeholder="e.g. 12 Ogui Road, GRA, Enugu — include any landmark that helps the driver find you"
                                maxlength="500"
                            ></textarea>
                        </div>

                        <label class="field-label" for="deliveryStateSelect">State</label>
                        <select class="select-field" id="deliveryStateSelect" data-cs-skip>
                            <option value="">Select a state&hellip;</option>
                        </select>

                        <label class="field-label" for="deliveryLocationSelect">Delivery Area</label>
                        <select class="select-field" id="deliveryLocationSelect" data-cs-skip disabled>
                            <option value="">Select a state first&hellip;</option>
                        </select>

                        <div class="delivery-fee-display" id="deliveryFeeDisplay" style="display:none;">
                            <span style="display:flex;align-items:center;gap:8px;"><i class="fas fa-truck"></i> Delivery Fee</span>
                            <span id="deliveryFeeAmount">&#8358;0</span>
                        </div>
                        <p id="noDeliveryLocationsMessage" style="display:none;text-align:center;padding:1.25rem;background:var(--surf2);border-radius:var(--radius-sm);font-size:13px;color:var(--ink3);">
                            No delivery locations available for this state yet.
                        </p>
                    </div>
                </div>

            </div>

            {{-- ── RIGHT COLUMN (sticky summary) ── --}}
            <div class="co-summary-sticky">

                <div class="co-card">
                    <div class="co-sec-head">
                        <div class="co-sec-num">3</div>
                        <div>
                            <div class="co-sec-title">Order Summary</div>
                        </div>
                    </div>
                    <div class="co-card-body">

                        <div class="summary-item">
                            <span>Subtotal</span>
                            <span id="summarySubtotal">&#8358;0</span>
                        </div>
                        <div class="summary-item">
                            <span>Installation</span>
                            <span id="summaryInstallation">&#8358;0</span>
                        </div>
                        <div class="summary-item coupon-row" id="summaryCouponRow" style="display:none;">
                            <span><i class="fas fa-tag" style="margin-right:5px;font-size:11px;"></i>Coupon (<span id="summaryCouponCode">—</span>)</span>
                            <span id="summaryCouponDiscount">-&#8358;0</span>
                        </div>
                        <div class="summary-item">
                            <span id="summaryShippingLabel">Shipping</span>
                            <span id="summaryShipping">&#8358;0</span>
                        </div>
                        <div class="summary-total">
                            <span>Total</span>
                            <span id="summaryTotal">&#8358;0</span>
                        </div>

                        {{-- Coupon --}}
                        <div class="coupon-wrap">
                            <label class="field-label" for="couponInput" style="margin-bottom:7px;">Have a coupon?</label>
                            <div class="coupon-input-row">
                                <input type="text" id="couponInput" placeholder="Enter code" maxlength="64" autocomplete="off">
                                <button class="coupon-apply-btn" id="couponBtn">Apply</button>
                            </div>
                            <div class="coupon-msg" id="couponMsg"></div>
                        </div>

                        <button class="checkout-btn-summary" id="proceedToPaymentBtn" disabled>
                            <i class="fas fa-lock"></i> Proceed to Payment
                        </button>
                        <div class="trust-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>Secure &amp; encrypted checkout</span>
                        </div>

                    </div>
                </div>

                {{-- Payment forms (shown after "Proceed") --}}
                <div id="payment-forms-section" class="hidden" style="margin-top:1rem;">
                    <div class="co-card">
                        <div class="co-sec-head">
                            <div class="co-sec-num" style="background:#3b5bdb;">4</div>
                            <div>
                                <div class="co-sec-title">Payment</div>
                                <div class="co-sec-subtitle">Choose your payment method</div>
                            </div>
                        </div>
                        <div class="co-card-body">

                            <div id="messages"></div>

                            <form id="main-checkout-form">
                                <div style="display:none;">
                                    <label for="amount">Amount (USD):</label>
                                    <input type="number" id="amount" name="amount" step="0.01" min="0.50" value="0.50" readonly>
                                </div>
                                <div style="margin-bottom:.25rem;">
                                    <label for="email">Email address</label>
                                    <input type="email" id="email" name="email" value="{{ auth()->user()?->email ?? 'test@example.com' }}" required readonly>
                                </div>
                                <div class="payment-options">
                                    <button type="button" id="select-stripe" class="payment-method-btn">
                                        <i class="fas fa-credit-card"></i> Card
                                    </button>
                                    <button type="button" id="select-paystack" class="payment-method-btn">
                                        <i class="fas fa-mobile-alt"></i> Paystack
                                    </button>
                                </div>
                            </form>

                            <div id="stripe-payment-section" class="hidden">
                                <h3>Pay with Card (Stripe)</h3>
                                <form id="stripe-payment-form">
                                    <label for="card-element" style="font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--ink3);display:block;margin-bottom:8px;">Card details</label>
                                    <div id="card-element"></div>
                                    <div id="card-errors" role="alert" class="text-danger"></div>
                                    <button type="submit" id="stripe-submit-button">
                                        <span class="spinner-border hidden" role="status" aria-hidden="true" style="width:14px;height:14px;border:2px solid rgba(255,255,255,.5);border-bottom-color:transparent;border-radius:50%;display:inline-block;animation:spin .7s linear infinite;"></span>
                                        <i class="fas fa-lock"></i> Pay with Stripe
                                    </button>
                                </form>
                            </div>

                            <div id="paystack-payment-section" class="hidden">
                                <h3>Pay with Paystack</h3>
                                <div id="paystack-large-order-hint" class="hidden" style="display:flex;gap:8px;align-items:flex-start;background:#fff8e6;border:1px solid #f0d98a;border-radius:8px;padding:10px 12px;margin-bottom:12px;font-size:13px;line-height:1.45;color:#7a5b00;">
                                    <i class="fas fa-info-circle" style="margin-top:2px;color:#c79100;"></i>
                                    <span>Large order? For an amount this size, choose <strong>Bank Transfer</strong> or <strong>USSD</strong> in the payment window — card payments often hit your bank's daily online limit and get declined.</span>
                                </div>
                                <button type="button" id="paystack-submit-button">
                                    <span class="spinner-border hidden" role="status" aria-hidden="true" style="width:14px;height:14px;border:2px solid rgba(255,255,255,.5);border-bottom-color:transparent;border-radius:50%;display:inline-block;animation:spin .7s linear infinite;"></span>
                                    <i class="fas fa-lock"></i> Pay with Paystack
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>{{-- /right column --}}

        </div>{{-- /co-layout --}}

    </div>{{-- /co-page --}}

    <div class="location-modal-overlay" id="locationModalOverlay">
        <div class="location-modal-content">
            <button class="location-modal-close" id="closeLocationModalBtn" aria-label="Close"><i class="fas fa-times"></i></button>
            <h2><i class="fas fa-map-marker-alt" style="color:var(--g500);margin-right:8px;"></i>Select Your Location</h2>
            <div class="location-list" id="locationList"></div>
            <div class="location-actions">
                <button class="location-clear-btn" id="clearLocationFilterBtn">Clear</button>
                <button class="location-confirm-btn" id="confirmLocationBtn">Confirm Location</button>
            </div>
        </div>
    </div>

    {{-- Processing overlay --}}
    <div class="processing-overlay" id="processingOverlay" aria-live="polite" aria-hidden="true">
        <div class="processing-card">
            <div class="processing-spinner"></div>
            <div class="processing-title" id="processingTitle">Saving your order…</div>
            <div class="processing-sub" id="processingSub">Please don't close or refresh this page.</div>
        </div>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', async () => {

        function fmt(ngnAmount) {
            if (window.CURRENCY && typeof window.CURRENCY.format === 'function') {
                return window.CURRENCY.format(ngnAmount);
            }
            return '\u20a6' + parseFloat(ngnAmount).toLocaleString('en-NG', {
                minimumFractionDigits: 0, maximumFractionDigits: 0,
            });
        }

        // ── DOM refs ──────────────────────────────────────────────────────────
        const cartItemsDisplay          = document.getElementById('cartItemsDisplay');
        const summarySubtotal           = document.getElementById('summarySubtotal');
        const summaryInstallation       = document.getElementById('summaryInstallation');
        const summaryShipping           = document.getElementById('summaryShipping');
        const summaryTotal              = document.getElementById('summaryTotal');
        const clearCartBtn              = document.getElementById('clearCartBtn');
        const cartCountSpan             = document.querySelector('.cart-count');
        const proceedToPaymentBtn       = document.getElementById('proceedToPaymentBtn');
        const paymentFormsSection       = document.getElementById('payment-forms-section');
        const locationModalOverlay      = document.getElementById('locationModalOverlay');
        const closeLocationModalBtn     = document.getElementById('closeLocationModalBtn');
        const locationListContainer     = document.getElementById('locationList');
        const confirmLocationBtn        = document.getElementById('confirmLocationBtn');
        const clearLocationFilterBtn    = document.getElementById('clearLocationFilterBtn');
        const changePickupLocationBtn   = document.getElementById('changePickupLocationBtn');
        const currentPickupLocationSpan = document.getElementById('currentPickupLocation');
        const pickupPointsList          = document.getElementById('pickupPointsList');
        const noPickupPointsMessage     = document.getElementById('noPickupPointsMessage');
        const pickupPointHint           = document.getElementById('pickupPointHint');
        const amountInput               = document.getElementById('amount');
        const emailInput                = document.getElementById('email');
        const messagesDiv               = document.getElementById('messages');
        const stripeSection             = document.getElementById('stripe-payment-section');
        const paystackSection           = document.getElementById('paystack-payment-section');
        const selectStripeBtn           = document.getElementById('select-stripe');
        const selectPaystackBtn         = document.getElementById('select-paystack');
        const stripeForm                = document.getElementById('stripe-payment-form');
        const stripeSubmitButton        = document.getElementById('stripe-submit-button');
        const stripeSpinner             = stripeSubmitButton?.querySelector('.spinner-border');
        const cardErrors                = document.getElementById('card-errors');
        const paystackSubmitButton      = document.getElementById('paystack-submit-button');
        const paystackSpinner           = paystackSubmitButton?.querySelector('.spinner-border');
        const paystackLargeOrderHint    = document.getElementById('paystack-large-order-hint');

        // Above this NGN total, card payments commonly hit the customer's bank
        // online limit — nudge them toward Bank Transfer / USSD instead.
        const LARGE_ORDER_HINT_NGN = 500000;
        function updatePaystackHint() {
            if (!paystackLargeOrderHint) return;
            const totalNgn = parseFloat(summaryTotal?.dataset.ngn) || 0;
            paystackLargeOrderHint.classList.toggle('hidden', totalNgn < LARGE_ORDER_HINT_NGN);
        }
        const paystackPk                = '{{ config("services.paystack.public") }}';

        // ── Fulfillment availability (set by admin settings) ─────────────────────
        const pickupEnabled   = {{ $pickupEnabled  ? 'true' : 'false' }};
        const deliveryEnabled = {{ $deliveryEnabled ? 'true' : 'false' }};

        // ── Fulfillment (pickup / delivery) refs ────────────────────────────────
        const tabPickupBtn               = document.getElementById('tabPickup');
        const tabDeliveryBtn             = document.getElementById('tabDelivery');
        const pickupPanel                = document.getElementById('pickupPanel');
        const deliveryPanel              = document.getElementById('deliveryPanel');
        const deliveryStateSelect        = document.getElementById('deliveryStateSelect');
        const deliveryLocationSelect     = document.getElementById('deliveryLocationSelect');
        const deliveryFeeDisplay         = document.getElementById('deliveryFeeDisplay');
        const deliveryFeeAmount          = document.getElementById('deliveryFeeAmount');
        const noDeliveryLocationsMessage = document.getElementById('noDeliveryLocationsMessage');
        const shippingAddressInput       = document.getElementById('shippingAddressInput');

        // ── Coupon refs ───────────────────────────────────────────────────────
        const couponInput        = document.getElementById('couponInput');
        const couponBtn          = document.getElementById('couponBtn');
        const couponMsg          = document.getElementById('couponMsg');
        const summaryCouponRow   = document.getElementById('summaryCouponRow');
        const summaryCouponCode  = document.getElementById('summaryCouponCode');
        const summaryCouponDiscount = document.getElementById('summaryCouponDiscount');

        let cart                       = [];
        let selectedLocation           = null;
        let selectedPickupPoint        = null;   // pickup: the chosen physical pickup point
        let appliedCoupon              = null;   // { id, code, discountNgn }
        let weightThresholdKg          = 30;     // overwritten by syncTruckFlags from server
        let orderValueThresholdNgn     = 1000000; // overwritten by syncTruckFlags from server
        const installationSelections   = {};
        const installationOptionsCache = {};
        let availableLocations         = [];
        let pickupPointsCache          = {};
        let _rawTotalNgn               = 0;

        // ── Delivery state ───────────────────────────────────────────────────
        let fulfillmentMethod        = localStorage.getItem('fulfillmentMethod') || 'pickup'; // 'pickup' | 'delivery'
        let availableStates          = [];
        let deliveryLocationsCache   = {};   // stateId -> [locations]
        let selectedDeliveryState    = null; // { id, name }
        let selectedDeliveryLocation = null; // { id, name, baseFeeNgn, truckFeeNgn }
        // Prefill from a previously typed address, else the customer's last order
        // delivery address. If they have never ordered, this stays blank —
        // we do NOT fall back to the profile address.
        let shippingAddress          = localStorage.getItem('shippingAddress')
            || @json($lastOrderAddress ?? '')
            || '';

        // ── Helpers ───────────────────────────────────────────────────────────
        function getCsrfToken() { return document.querySelector('meta[name="csrf-token"]').content; }

        function showMessage(type, msg) {
            const bg    = type === 'success' ? '#dcfce7' : type === 'info' ? '#dbeafe' : '#fee2e2';
            const color = type === 'success' ? '#166534' : type === 'info' ? '#1e40af' : '#991b1b';
            messagesDiv.innerHTML = '<div style="padding:10px 14px;border-radius:6px;font-size:13px;background:' + bg + ';color:' + color + ';">' + msg + '</div>';
        }
        function clearMessages()     { messagesDiv.innerHTML = ''; }
        function showLoading(btn,sp) { if (btn) btn.disabled = true;  if (sp) sp.classList.remove('hidden'); }
        function hideLoading(btn,sp) { if (btn) btn.disabled = false; if (sp) sp.classList.add('hidden'); }
        function resetPaymentForms() { stripeSection.classList.add('hidden'); paystackSection.classList.add('hidden'); clearMessages(); }

        // ── Full-screen processing overlay ────────────────────────────────────
        const processingOverlay = document.getElementById('processingOverlay');
        const processingTitle   = document.getElementById('processingTitle');
        const processingSub     = document.getElementById('processingSub');
        const processingCard    = processingOverlay?.querySelector('.processing-card');

        function showProcessing(title, sub) {
            if (!processingOverlay) return;
            processingCard.classList.remove('is-success');
            processingTitle.textContent = title || 'Processing…';
            processingSub.textContent   = sub   || 'Please don\'t close or refresh this page.';
            processingOverlay.classList.add('active');
            processingOverlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }
        function updateProcessing(title, sub) {
            if (!processingOverlay) return;
            if (title) processingTitle.textContent = title;
            if (sub)   processingSub.textContent   = sub;
        }
        function processingSuccess(title, sub) {
            if (!processingOverlay) return;
            processingCard.classList.add('is-success');
            processingTitle.textContent = title || 'Done!';
            processingSub.textContent   = sub   || 'Redirecting…';
        }
        function hideProcessing() {
            if (!processingOverlay) return;
            processingOverlay.classList.remove('active');
            processingOverlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function setCouponMsg(msg, type) {
            couponMsg.textContent  = msg;
            couponMsg.className    = 'coupon-msg' + (type ? ' ' + type : '');
        }

        // ── Re-evaluate the payment button ────────────────────────────────────
        // The button stays clickable so we can guide the user to what's missing.
        // Its LABEL tells them exactly what step is outstanding.
        function refreshPaymentGate() {
            const btn = proceedToPaymentBtn;
            btn.disabled = false; // clickable on purpose — the click handler guides them

            if (fulfillmentMethod === 'delivery') {
                if (!selectedDeliveryState) {
                    btn.innerHTML = '<i class="fas fa-map-marker-alt"></i> Select a delivery state';
                    btn.classList.add('btn-needs-action');
                } else if (!selectedDeliveryLocation) {
                    btn.innerHTML = '<i class="fas fa-truck"></i> Select a delivery location';
                    btn.classList.add('btn-needs-action');
                } else if (!shippingAddress.trim()) {
                    btn.innerHTML = '<i class="fas fa-home"></i> Enter delivery address';
                    btn.classList.add('btn-needs-action');
                } else {
                    btn.innerHTML = '<i class="fas fa-lock"></i> Make Payment';
                    btn.classList.remove('btn-needs-action');
                }
            } else {
                if (!selectedLocation) {
                    btn.innerHTML = '<i class="fas fa-map-marker-alt"></i> Select a pickup location';
                    btn.classList.add('btn-needs-action');
                } else if (!selectedPickupPoint) {
                    btn.innerHTML = '<i class="fas fa-store"></i> Select a pickup point';
                    btn.classList.add('btn-needs-action');
                } else {
                    btn.innerHTML = '<i class="fas fa-lock"></i> Make Payment';
                    btn.classList.remove('btn-needs-action');
                }
            }
        }

        // ── Coupon: apply ─────────────────────────────────────────────────────
        async function applyCoupon() {
            const code = couponInput.value.trim().toUpperCase();
            if (!code) { setCouponMsg('Please enter a coupon code.', 'error'); return; }

            couponBtn.disabled   = true;
            couponBtn.textContent = '…';
            setCouponMsg('', '');

            calculateCartTotals(); // ensure dataset.ngn is fresh
            const subtotalNgn = parseFloat(summarySubtotal.dataset.ngn) || 0;

            try {
                const res = await fetch('/api/coupons/validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ code, subtotal_ngn: subtotalNgn }),
                });
                const data = await res.json();

                if (data.success) {
                    appliedCoupon = { id: data.coupon_id, code: data.code, discountNgn: data.discount_ngn };
                    setCouponMsg('✓ ' + data.message, 'success');
                    couponInput.disabled  = true;
                    couponBtn.textContent = 'Remove';
                    couponBtn.classList.add('is-remove');
                    couponBtn.disabled    = false;
                } else {
                    appliedCoupon = null;
                    setCouponMsg(data.message, 'error');
                    couponBtn.textContent = 'Apply';
                    couponBtn.disabled    = false;
                }
            } catch (e) {
                setCouponMsg('Could not apply coupon. Please try again.', 'error');
                couponBtn.textContent = 'Apply';
                couponBtn.disabled    = false;
            }

            calculateCartTotals();
        }

        function removeCoupon() {
            appliedCoupon         = null;
            couponInput.value     = '';
            couponInput.disabled  = false;
            couponBtn.textContent = 'Apply';
            couponBtn.classList.remove('is-remove');
            couponBtn.disabled    = false;
            setCouponMsg('', '');
            calculateCartTotals();
        }

        couponBtn.addEventListener('click', () => {
            if (couponBtn.classList.contains('is-remove')) removeCoupon();
            else applyCoupon();
        });

        couponInput.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !couponBtn.classList.contains('is-remove')) applyCoupon();
        });

        // ── calculateCartTotals ───────────────────────────────────────────────
        function calculateCartTotals() {
            let subtotalNgn     = 0;
            let installationNgn = 0;
            let orderRequiresTruck = false;
            let totalWeightKg      = 0;

            cart.forEach((item, index) => {
                const base     = (typeof item.basePriceNgn === 'number' && !isNaN(item.basePriceNgn)) ? item.basePriceNgn : 0;
                const qty      = parseInt(item.quantity) || 0;
                const sels     = Array.isArray(installationSelections[index]) ? installationSelections[index] : [];
                const extraNgn = sels.reduce((sum, s) => sum + (s.extraNgn || 0), 0);
                subtotalNgn     += base * qty;
                installationNgn += extraNgn * qty;

                // Explicit truck flag (e.g. floor-standing AC, generators)
                if (item.requires_truck === true || item.requires_truck === "1" || item.requires_truck === 1) {
                    orderRequiresTruck = true;
                }

                // Accumulate inferred weight — falls back to 5 kg for items not
                // yet synced or from subcategories added after this table was built.
                const unitWeight = (item.inferred_weight_kg != null) ? parseFloat(item.inferred_weight_kg) : 5.0;
                totalWeightKg += unitWeight * qty;
            });

            // Weight threshold: total cart weight exceeds the admin-configured limit
            if (totalWeightKg > weightThresholdKg) {
                orderRequiresTruck = true;
            }

            // Order value threshold: high-value orders get truck for safety/insurance
            if ((subtotalNgn + installationNgn) >= orderValueThresholdNgn) {
                orderRequiresTruck = true;
            }

            // Delivery fee applies when delivery mode is active AND a location is chosen
            let deliveryFeeNgn = 0;
            if (fulfillmentMethod === 'delivery' && selectedDeliveryLocation) {
                // If anything in the cart requires a truck, upgrade to the truck price for this location
                deliveryFeeNgn = orderRequiresTruck 
                    ? selectedDeliveryLocation.truckFeeNgn 
                    : selectedDeliveryLocation.baseFeeNgn;
                
                deliveryFeeAmount.textContent = fmt(deliveryFeeNgn);
                deliveryFeeDisplay.style.display = 'flex';

                // Update localStorage so payload builder uses the currently calculated fee
                localStorage.setItem('deliveryFeeNgn', String(deliveryFeeNgn));
            } else {
                deliveryFeeDisplay.style.display = 'none';
                localStorage.setItem('deliveryFeeNgn', '0');
            }

            const preCouponTotal = subtotalNgn + installationNgn + deliveryFeeNgn;

            // Coupon deduction
            let couponDiscountNgn = 0;
            if (appliedCoupon) {
                couponDiscountNgn = Math.min(appliedCoupon.discountNgn, preCouponTotal);
                summaryCouponRow.style.display  = 'flex';
                summaryCouponCode.textContent   = appliedCoupon.code;
                summaryCouponDiscount.textContent = '-' + fmt(couponDiscountNgn);
            } else {
                summaryCouponRow.style.display = 'none';
            }

            const totalNgn = Math.max(0, preCouponTotal - couponDiscountNgn);

            // Display
            summarySubtotal.textContent     = fmt(subtotalNgn);
            summaryInstallation.textContent = installationNgn > 0 ? '+' + fmt(installationNgn) : fmt(0);
            summaryShipping.textContent     = fmt(deliveryFeeNgn);
            summaryTotal.textContent        = fmt(totalNgn);

            // Raw values on dataset — never re-parse formatted text
            summarySubtotal.dataset.ngn     = subtotalNgn;
            summaryInstallation.dataset.ngn = installationNgn;
            summaryShipping.dataset.ngn     = deliveryFeeNgn;
            summaryTotal.dataset.ngn        = totalNgn;
            _rawTotalNgn                    = totalNgn;

            // USD for Stripe — display only. The server recomputes the charge from
            // the same rate (config services.stripe.usd_rate) so the two always agree.
            const USD_RATE = {{ config('services.stripe.usd_rate', 0.00067) }};
            const usdCents = Math.max(Math.round(totalNgn * USD_RATE * 100), 50);
            amountInput.value = (usdCents / 100).toFixed(2);

            updatePaystackHint();
            return { usdCents, totalNgn, subtotalNgn, installationNgn, deliveryFeeNgn, couponDiscountNgn };
        }

        // ── Locations / pickup points (PICKUP) ──────────────────────────────────
       // ── Locations / pickup points (PICKUP) ──────────────────────────────────
        async function fetchLocations() {
            try {
                // Added ?for_pickup=1 to filter out delivery-only neighborhoods
                const res = await fetch('/api/locations?for_pickup=1', { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Failed');
                availableLocations = await res.json();
            } catch (e) { availableLocations = []; }
        }

        async function fetchPickupPoints(locationId) {
            if (pickupPointsCache[locationId]) return pickupPointsCache[locationId];
            try {
                const res = await fetch('/api/pickup-points?location_id=' + locationId, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Failed');
                const points = await res.json();
                pickupPointsCache[locationId] = points;
                return points;
            } catch (e) { return []; }
        }

        // ── States / delivery locations (DELIVERY) ──────────────────────────────
        async function fetchStates() {
            try {
                const res = await fetch('/api/states', { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Failed');
                availableStates = await res.json();
            } catch (e) { availableStates = []; }
        }

        async function fetchDeliveryLocations(stateId) {
            if (deliveryLocationsCache[stateId]) return deliveryLocationsCache[stateId];
            try {
                const res = await fetch('/api/locations?state_id=' + encodeURIComponent(stateId), { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Failed');
                const locs = await res.json();
                deliveryLocationsCache[stateId] = locs;
                return locs;
            } catch (e) { return []; }
        }

        function populateStateSelect() {
            deliveryStateSelect.innerHTML = '<option value="">Select a state\u2026</option>'
                + availableStates.map(s => '<option value="' + s.id + '">' + s.name + '</option>').join('');
            const savedStateId = localStorage.getItem('deliveryStateId');
            if (savedStateId && availableStates.some(s => String(s.id) === savedStateId)) {
                deliveryStateSelect.value = savedStateId;
            }
        }

        async function populateLocationSelectForState(stateId, preselectLocationId) {
            deliveryLocationSelect.disabled = true;
            deliveryLocationSelect.innerHTML = '<option value="">Loading\u2026</option>';
            deliveryFeeDisplay.style.display = 'none';
            noDeliveryLocationsMessage.style.display = 'none';

            const locs = await fetchDeliveryLocations(stateId);

            if (!locs.length) {
                deliveryLocationSelect.innerHTML = '<option value="">No locations available</option>';
                deliveryLocationSelect.disabled = true;
                noDeliveryLocationsMessage.style.display = 'block';
                selectedDeliveryLocation = null;
                refreshPaymentGate();
                calculateCartTotals();
                return;
            }

            deliveryLocationSelect.innerHTML = '<option value="">Select a location\u2026</option>'
                + locs.map(l => {
                    const baseFee  = Number(l.shipping_cost ?? 0);
                    const truckFee = Number(l.truck_shipping_cost ?? 0);
                    const name = String(l.name).replace(/"/g, '&quot;');
                    return '<option value="' + l.id + '" data-base-fee="' + baseFee + '" data-truck-fee="' + truckFee + '" data-name="' + name + '">' + l.name + '</option>';
                }).join('');
            
            deliveryLocationSelect.disabled = false;

            if (preselectLocationId) {
                deliveryLocationSelect.value = preselectLocationId;
                if (deliveryLocationSelect.value === String(preselectLocationId)) {
                    applyDeliveryLocationSelection();
                }
            }
        }

        function applyDeliveryLocationSelection() {
            const opt = deliveryLocationSelect.selectedOptions[0];
            if (!opt || !opt.value) {
                selectedDeliveryLocation = null;
                deliveryFeeDisplay.style.display = 'none';
                localStorage.removeItem('deliveryLocationId');
                localStorage.removeItem('deliveryLocationName');
                localStorage.removeItem('deliveryBaseFeeNgn');
                localStorage.removeItem('deliveryTruckFeeNgn');
                refreshPaymentGate();
                calculateCartTotals();
                return;
            }
            
            const baseFeeNgn = parseFloat(opt.dataset.baseFee) || 0;
            const truckFeeNgn = parseFloat(opt.dataset.truckFee) || 0;
            
            selectedDeliveryLocation = { 
                id: opt.value, 
                name: opt.dataset.name, 
                baseFeeNgn: baseFeeNgn,
                truckFeeNgn: truckFeeNgn
            };
            
            localStorage.setItem('deliveryLocationId', opt.value);
            localStorage.setItem('deliveryLocationName', opt.dataset.name);
            localStorage.setItem('deliveryBaseFeeNgn', String(baseFeeNgn));
            localStorage.setItem('deliveryTruckFeeNgn', String(truckFeeNgn));
            
            refreshPaymentGate();
            calculateCartTotals();
        }

        // ── Fulfillment method toggle ────────────────────────────────────────
        function setFulfillmentMethod(method, opts) {
            opts = opts || {};
            fulfillmentMethod = method;
            localStorage.setItem('fulfillmentMethod', method);
            tabPickupBtn.classList.toggle('active', method === 'pickup');
            tabDeliveryBtn.classList.toggle('active', method === 'delivery');
            pickupPanel.classList.toggle('active', method === 'pickup');
            deliveryPanel.classList.toggle('active', method === 'delivery');
            // If the user switches to delivery and a state is already selected but the
            // location dropdown hasn't been loaded yet (still disabled), fetch locations
            // now. This happens when the page restores a saved delivery state but the
            // current fulfillment tab was pickup — the change event never fired.
            if (method === 'delivery' && !opts.silent && deliveryStateSelect.value && deliveryLocationSelect.disabled) {
                populateLocationSelectForState(deliveryStateSelect.value);
            }
            if (!opts.silent) { refreshPaymentGate(); calculateCartTotals(); }
        }

        tabPickupBtn.addEventListener('click', () => setFulfillmentMethod('pickup'));
        tabDeliveryBtn.addEventListener('click', () => setFulfillmentMethod('delivery'));

        deliveryStateSelect.addEventListener('change', async () => {
            const stateId = deliveryStateSelect.value;
            selectedDeliveryLocation = null;
            deliveryFeeDisplay.style.display = 'none';
            localStorage.removeItem('deliveryLocationId');
            localStorage.removeItem('deliveryLocationName');
            localStorage.removeItem('deliveryBaseFeeNgn');
            localStorage.removeItem('deliveryTruckFeeNgn');

            if (!stateId) {
                selectedDeliveryState = null;
                localStorage.removeItem('deliveryStateId');
                localStorage.removeItem('deliveryStateName');
                deliveryLocationSelect.innerHTML = '<option value="">Select a state first\u2026</option>';
                deliveryLocationSelect.disabled = true;
                noDeliveryLocationsMessage.style.display = 'none';
                refreshPaymentGate();
                calculateCartTotals();
                return;
            }

            const stateName = deliveryStateSelect.selectedOptions[0].textContent;
            selectedDeliveryState = { id: stateId, name: stateName };
            localStorage.setItem('deliveryStateId', stateId);
            localStorage.setItem('deliveryStateName', stateName);
            await populateLocationSelectForState(stateId);
            refreshPaymentGate();
            calculateCartTotals();
        });

        deliveryLocationSelect.addEventListener('change', applyDeliveryLocationSelection);

        // ── Cart helpers ──────────────────────────────────────────────────────
        function loadCartFromLocalStorage() {
            try {
                const raw    = localStorage.getItem('shoppingCart');
                const parsed = raw ? JSON.parse(raw) : [];
                cart = Array.isArray(parsed)
                    ? parsed.map(item => ({
                        ...item,
                        basePriceNgn: parseFloat(item.basePriceNgn) || 0,
                        quantity:     parseInt(item.quantity)        || 1,
                    }))
                    : [];
            } catch (e) { cart = []; }
        }

        function saveCartToLocalStorage() {
            try { localStorage.setItem('shoppingCart', JSON.stringify(cart)); updateCartDisplayHeader(); } catch (e) {}
        }

        function updateCartDisplayHeader() {
            const c = JSON.parse(localStorage.getItem('shoppingCart') || '[]');
            if (cartCountSpan) { cartCountSpan.textContent = c.length; cartCountSpan.style.display = c.length > 0 ? 'flex' : 'none'; }
        }

        // ── Installation options ──────────────────────────────────────────────
        async function fetchInstallationOptions() {
            if (cart.length === 0) return;
            const ids = [...new Set(cart.map(i => i.id).filter(id => !(id in installationOptionsCache)))];
            if (ids.length === 0) return;
            try {
                const res = await fetch('/api/installation-options', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                    body: JSON.stringify({ product_ids: ids }),
                });
                if (!res.ok) return;
                Object.assign(installationOptionsCache, await res.json());
            } catch (e) {}
        }

        function buildInstallOptsHtml(cartIndex) {
            const item = cart[cartIndex];
            const opts = installationOptionsCache[item.id];
            if (!opts || opts.length === 0) return '';

            if (!Array.isArray(installationSelections[cartIndex]) || installationSelections[cartIndex].length === 0) {
                const first = opts[0];
                const firstLabel    = typeof first === 'object' ? (first.label || 'Option') : String(first);
                const firstExtraNgn = typeof first === 'object' && first.price ? Number(first.price) : 0;
                installationSelections[cartIndex] = [{ label: firstLabel, extraNgn: firstExtraNgn }];
            }

            const currentSelections = installationSelections[cartIndex];
            const optionsHtml = opts.map((opt, i) => {
                const label     = typeof opt === 'object' ? (opt.label || 'Option') : String(opt);
                const extraNgn  = typeof opt === 'object' && opt.price ? Number(opt.price) : 0;
                const desc      = typeof opt === 'object' && opt.description ? opt.description : '';
                const isChecked = currentSelections.some(s => s.label === label);
                const priceTag  = extraNgn > 0 ? '<strong>+' + fmt(extraNgn) + '</strong>' : '<em>Included</em>';
                return '<label class="install-opt-label ' + (isChecked ? 'selected' : '') + '" for="install_' + cartIndex + '_' + i + '">'
                    + '<input class="install-opt-radio" type="checkbox"'
                    + ' id="install_' + cartIndex + '_' + i + '"'
                    + ' name="install_opt_' + cartIndex + '"'
                    + ' data-cart-index="' + cartIndex + '"'
                    + ' data-label="' + label.replace(/"/g, '&quot;') + '"'
                    + ' data-extra="' + extraNgn + '"'
                    + (isChecked ? ' checked' : '') + '>'
                    + '<span class="install-opt-text">' + label + ' ' + priceTag + (desc ? '<small>' + desc + '</small>' : '') + '</span>'
                    + '</label>';
            }).join('');

            const clearHtml = '<label class="install-opt-label reject-opt" style="cursor:pointer;"'
                + ' onclick="window.clearInstallSelections(' + cartIndex + ', this)">'
                + '<span style="width:14px;height:14px;flex-shrink:0;display:flex;align-items:center;justify-content:center;">'
                + '<i class="fas fa-times-circle" style="font-size:12px;color:#dc2626;"></i></span>'
                + '<span class="install-opt-text" style="color:#dc2626;">No installation <em>Clear all options</em></span>'
                + '</label>';

            return '<div class="install-opts-wrap">'
                + '<div class="install-opts-title"><i class="fas fa-tools" style="color:#4e7a1a;margin-right:4px;"></i>Installation Options'
                + '<small style="font-weight:400;color:var(--ink4);text-transform:none;font-size:10px;"> (select all that apply)</small></div>'
                + '<div class="install-opts-list" id="install-list-' + cartIndex + '">' + optionsHtml + clearHtml + '</div></div>';
        }

        window.clearInstallSelections = function(cartIndex, clickedLabel) {
            installationSelections[cartIndex] = [];
            document.querySelectorAll('[name="install_opt_' + cartIndex + '"]').forEach(cb => {
                cb.checked = false;
                cb.closest('.install-opt-label').classList.remove('selected');
            });
            clickedLabel.classList.add('selected');
            setTimeout(() => clickedLabel.classList.remove('selected'), 600);
            calculateCartTotals();
            updateRowSubtotal(cartIndex);
        };

        function updateRowSubtotal(idx) {
            const rows = document.querySelectorAll('.cart-item-row');
            const row  = rows[idx];
            if (!row) return;
            const base       = typeof cart[idx].basePriceNgn === 'number' ? cart[idx].basePriceNgn : 0;
            const qty        = cart[idx].quantity || 1;
            const sels       = Array.isArray(installationSelections[idx]) ? installationSelections[idx] : [];
            const totalExtra = sels.reduce((sum, s) => sum + (s.extraNgn || 0), 0);
            const effective  = base + totalExtra;
            const cells      = row.querySelectorAll('td');
            if (cells[1]) cells[1].innerHTML = fmt(effective);
            if (cells[3]) cells[3].querySelector('.item-subtotal').textContent = fmt(effective * qty);
        }

        function attachInstallOptionListeners() {
            document.querySelectorAll('.install-opt-radio').forEach(checkbox => {
                checkbox.addEventListener('change', e => {
                    const idx      = parseInt(e.target.dataset.cartIndex);
                    const label    = e.target.dataset.label;
                    const extraNgn = parseFloat(e.target.dataset.extra) || 0;
                    if (!Array.isArray(installationSelections[idx])) installationSelections[idx] = [];
                    if (e.target.checked) {
                        if (!installationSelections[idx].some(s => s.label === label)) {
                            installationSelections[idx].push({ label, extraNgn });
                        }
                    } else {
                        installationSelections[idx] = installationSelections[idx].filter(s => s.label !== label);
                    }
                    e.target.closest('.install-opt-label').classList.toggle('selected', e.target.checked);
                    calculateCartTotals();
                    updateRowSubtotal(idx);
                });
            });
        }

        function attachCartEventListeners() {
            document.querySelectorAll('.increase-qty, .decrease-qty').forEach(b => b.addEventListener('click', handleQuantityChange));
            document.querySelectorAll('.quantity-input').forEach(i => i.addEventListener('change', handleQuantityChange));
            document.querySelectorAll('.remove-item-btn').forEach(b => b.addEventListener('click', handleRemoveItem));
        }

        async function renderCartItems() {
            if (!cartItemsDisplay) return;
            cartItemsDisplay.innerHTML = '';
            if (cart.length === 0) {
                cartItemsDisplay.innerHTML = '<div class="empty-cart-message-page"><p>Your shopping cart is empty.</p><p>Looks like you haven\'t added anything to your cart yet.</p><a href="/"><i class="fas fa-arrow-left"></i> Start Shopping</a></div>';
                calculateCartTotals();
                return;
            }
            cartItemsDisplay.innerHTML = '<div style="padding:1.5rem;text-align:center;color:#888;font-size:13px;"><i class="fas fa-circle-notch fa-spin" style="margin-right:6px;color:#4e7a1a;"></i>Loading your cart\u2026</div>';
            await fetchInstallationOptions();
            cartItemsDisplay.innerHTML = '';
            const table = document.createElement('table');
            table.className = 'cart-table';
            table.innerHTML = '<thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr></thead><tbody></tbody>';
            const tbody = table.querySelector('tbody');
            cart.forEach((item, index) => {
                const base = (typeof item.basePriceNgn === 'number' && !isNaN(item.basePriceNgn)) ? item.basePriceNgn : 0;
                const qty  = item.quantity || 1;
                const img  = item.image || item.imageSrc || 'https://placehold.co/80x80/f5f9f0/c5d9ae?text=Product';
                const installHtml = buildInstallOptsHtml(index);
                const sels        = Array.isArray(installationSelections[index]) ? installationSelections[index] : [];
                const totalExtra  = sels.reduce((sum, s) => sum + (s.extraNgn || 0), 0);
                const effective   = base + totalExtra;
                const row = document.createElement('tr');
                row.className = 'cart-item-row';
                row.dataset.productId = item.id;
                row.innerHTML = '<td data-label="Product"><div class="cart-item-info"><img src="' + img + '" alt="' + item.name + '" onerror="this.src=\'https://placehold.co/80x80/f5f9f0/c5d9ae?text=Product\'"><div style="flex:1;min-width:0;"><div class="item-name">' + item.name + '</div><div class="item-price-single">' + fmt(base) + ' per item</div>' + installHtml + '</div></div></td>'
                    + '<td data-label="Price">' + fmt(effective) + '</td>'
                    + '<td data-label="Quantity"><div class="quantity-controls"><button class="quantity-btn decrease-qty" data-index="' + index + '">\u2212</button><input type="number" class="quantity-input" value="' + qty + '" min="1" data-index="' + index + '"><button class="quantity-btn increase-qty" data-index="' + index + '">+</button></div></td>'
                    + '<td data-label="Subtotal"><span class="item-subtotal">' + fmt(effective * qty) + '</span></td>'
                    + '<td><button class="remove-item-btn" data-index="' + index + '" aria-label="Remove ' + item.name + '"><i class="fas fa-times-circle"></i></button></td>';
                tbody.appendChild(row);
            });
            cartItemsDisplay.appendChild(table);
            attachCartEventListeners();
            attachInstallOptionListeners();
            calculateCartTotals();
        }

        window.addEventListener('currencyChanged', () => { renderCartItems(); calculateCartTotals(); });

        function handleQuantityChange(e) {
            const t   = e.target;
            const idx = parseInt(t.dataset.index);
            if (isNaN(idx) || idx < 0 || idx >= cart.length) return;
            let qty;
            if (t.classList.contains('increase-qty'))      qty = cart[idx].quantity + 1;
            else if (t.classList.contains('decrease-qty')) qty = cart[idx].quantity - 1;
            else                                           qty = parseInt(t.value);
            cart[idx].quantity = Math.max(1, isNaN(qty) ? 1 : qty);
            saveCartToLocalStorage();
            const row = document.querySelector('.cart-item-row[data-product-id="' + cart[idx].id + '"]');
            if (row) {
                row.querySelector('.quantity-input').value = cart[idx].quantity;
                const base       = (typeof cart[idx].basePriceNgn === 'number') ? cart[idx].basePriceNgn : 0;
                const sels       = Array.isArray(installationSelections[idx]) ? installationSelections[idx] : [];
                const totalExtra = sels.reduce((sum, s) => sum + (s.extraNgn || 0), 0);
                row.querySelector('.item-subtotal').textContent = fmt((base + totalExtra) * cart[idx].quantity);
            }
            calculateCartTotals();
        }

        function handleRemoveItem(e) {
            const btn = e.target.closest('.remove-item-btn');
            if (!btn) return;
            const idx = parseInt(btn.dataset.index);
            if (isNaN(idx) || idx < 0 || idx >= cart.length) return;
            Swal.fire({ title: 'Are you sure?', text: 'Remove "' + cart[idx].name + '" from your cart?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#4e7a1a', cancelButtonColor: '#6c757d', confirmButtonText: 'Yes, remove it!' }).then(r => {
                if (r.isConfirmed) {
                    delete installationSelections[idx];
                    const newSelections = {};
                    Object.keys(installationSelections).forEach(k => {
                        const ki = parseInt(k);
                        if (ki > idx)      newSelections[ki - 1] = installationSelections[ki];
                        else if (ki < idx) newSelections[ki]     = installationSelections[ki];
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
        function openLocationModal()  { locationModalOverlay.classList.add('active'); populateLocationList(); }
        function closeLocationModal() { locationModalOverlay.classList.remove('active'); }

        function populateLocationList() {
            locationListContainer.innerHTML = '';
            const activeId = localStorage.getItem('pickupLocationId');
            availableLocations.forEach(loc => {
                const item = document.createElement('div');
                item.className = 'location-item';
                item.textContent = loc.name;
                item.dataset.locationId   = loc.id;
                item.dataset.locationName = loc.name;
                if (String(loc.id) === activeId) {
                    item.classList.add('selected');
                    selectedLocation = { id: loc.id, name: loc.name };
                }
                item.addEventListener('click', () => {
                    document.querySelectorAll('.location-item').forEach(i => i.classList.remove('selected'));
                    item.classList.add('selected');
                    selectedLocation = { id: loc.id, name: loc.name };
                });
                locationListContainer.appendChild(item);
            });
        }

        async function renderPickupSection() {
            const savedId   = localStorage.getItem('pickupLocationId');
            const savedName = localStorage.getItem('pickupLocation');
            selectedLocation = savedId ? { id: savedId, name: savedName } : null;
            currentPickupLocationSpan.textContent = selectedLocation?.name || 'Not selected';

            // A pickup point must be chosen before payment is allowed (pickup mode only).
            selectedPickupPoint                 = null;
            refreshPaymentGate();
            pickupPointsList.innerHTML           = '';
            pickupPointHint.style.display        = 'none';
            noPickupPointsMessage.style.display  = 'none';
            pickupPointsList.style.display       = 'none';

            if (!selectedLocation) return;

            pickupPointsList.style.display = 'block';
            pickupPointsList.innerHTML = '<div style="padding:.75rem;font-size:12px;color:#888;"><i class="fas fa-circle-notch fa-spin" style="color:#4e7a1a;margin-right:6px;"></i>Loading pickup points\u2026</div>';

            const points = await fetchPickupPoints(selectedLocation.id);
            pickupPointsList.innerHTML = '';

            if (points.length) {
                const savedPointId = localStorage.getItem('pickupPointId');
                pickupPointHint.style.display = 'block';

                points.forEach(p => {
                    const div = document.createElement('div');
                    div.className = 'pickup-point-item';
                    div.dataset.pointId      = p.id;
                    div.dataset.pointName    = p.name;
                    div.dataset.pointAddress = p.address || '';
                    div.innerHTML = '<h4>' + p.name + '</h4><p>' + (p.address || '') + '</p>' + (p.hours ? '<p class="hours">' + p.hours + '</p>' : '');

                    // Restore previously-selected point
                    if (savedPointId && String(p.id) === savedPointId) {
                        div.classList.add('selected');
                        selectedPickupPoint = { id: p.id, name: p.name, address: p.address || '' };
                        pickupPointHint.style.display = 'none';
                        refreshPaymentGate();
                    }

                    div.addEventListener('click', () => {
                        pickupPointsList.querySelectorAll('.pickup-point-item').forEach(i => i.classList.remove('selected'));
                        div.classList.add('selected');
                        selectedPickupPoint = { id: p.id, name: p.name, address: p.address || '' };
                        localStorage.setItem('pickupPointId',      p.id);
                        localStorage.setItem('pickupPointName',    p.name);
                        localStorage.setItem('pickupPointAddress', p.address || '');
                        pickupPointHint.style.display = 'none';
                        refreshPaymentGate();
                    });

                    pickupPointsList.appendChild(div);
                });
                noPickupPointsMessage.style.display = 'none';
            } else {
                pickupPointsList.style.display      = 'none';
                pickupPointHint.style.display       = 'none';
                noPickupPointsMessage.style.display = 'block';
            }
        }

        // ── Build order items ─────────────────────────────────────────────────
        function buildOrderItems(cartSnapshot) {
            return cartSnapshot.map((item, idx) => {
                const base     = (typeof item.basePriceNgn === 'number') ? item.basePriceNgn : 0;
                const sels     = Array.isArray(installationSelections[idx]) ? installationSelections[idx] : [];
                const extraNgn = sels.reduce((sum, s) => sum + (s.extraNgn || 0), 0);
                return {
                    id: item.id, name: item.name, basePriceNgn: base,
                    effective_price_ngn: base + extraNgn, quantity: item.quantity,
                    image: item.image || item.imageSrc || item.image_url || null,
                    sku: item.sku || item.id || null,
                    installation_option: sels.length > 0 ? sels.map(s => s.label).join(', ') : null,
                    installation_options: sels, installation_extra_ngn: extraNgn,
                };
            });
        }

        // ── Shared fulfillment payload (used by both stripe + paystack saves) ──
        function buildFulfillmentPayload() {
            if (fulfillmentMethod === 'delivery') {
                return {
                    fulfillment_method:      'delivery',
                    pickup_location:         null,
                    pickup_location_id:      null,
                    pickup_point_id:         null,
                    pickup_point_name:       null,
                    pickup_point_address:    null,
                    delivery_state_id:       localStorage.getItem('deliveryStateId'),
                    delivery_state_name:     localStorage.getItem('deliveryStateName'),
                    delivery_location_id:    localStorage.getItem('deliveryLocationId'),
                    delivery_location_name:  localStorage.getItem('deliveryLocationName'),
                    delivery_fee_ngn:        parseFloat(localStorage.getItem('deliveryFeeNgn')) || 0,
                    shipping_address:        shippingAddress.trim() || null,
                };
            }
            return {
                fulfillment_method:      'pickup',
                pickup_location:         localStorage.getItem('pickupLocation'),
                pickup_location_id:      localStorage.getItem('pickupLocationId'),
                pickup_point_id:         localStorage.getItem('pickupPointId'),
                pickup_point_name:       localStorage.getItem('pickupPointName'),
                pickup_point_address:    localStorage.getItem('pickupPointAddress'),
                delivery_state_id:       null,
                delivery_state_name:     null,
                delivery_location_id:    null,
                delivery_location_name:  null,
                delivery_fee_ngn:        0,
                shipping_address:        null,
            };
        }

        // ── Minimal fulfillment payload for save-checkout ─────────────────────
        // The server resolves all names and fees from the IDs — the browser sends
        // only what it cannot know from context (IDs and free-text address).
        function buildSaveCheckoutFulfillment() {
            const f = buildFulfillmentPayload();
            if (f.fulfillment_method === 'delivery') {
                return {
                    method:               'delivery',
                    delivery_location_id: f.delivery_location_id ?? null,
                    shipping_address:     f.shipping_address      ?? null,
                };
            }
            return {
                method:          'pickup',
                pickup_point_id: f.pickup_point_id ?? null,
            };
        }

        // ── Persist cart snapshot before Paystack popup opens ─────────────────
        // Sends only product IDs, quantities, and fulfillment choice. The server
        // computes prices, generates the reference, and returns both. Blocks on
        // failure — without a server-generated reference, the popup cannot open.
        async function savePendingCheckout() {
            const body = JSON.stringify({
                customer_email: emailInput.value,
                // ⚠️ DO NOT REMOVE / DO NOT CHANGE THE SOURCE OF installation_option.
                // This save-checkout payload builds PendingCheckout, which is the ONLY
                // trusted source the server uses to create the order (see
                // OrderController::saveCheckout and PaystackOrderService::fulfil).
                // installation_option MUST be read from installationSelections[idx]
                // (the live checkbox state), NOT from item.selectedInstallation — that
                // property does not exist and sending it silently drops installation on
                // every order. This bug shipped once already; keep it wired this way.
                items: cart.map((item, idx) => {
                    const sels = Array.isArray(installationSelections[idx]) ? installationSelections[idx] : [];
                    return {
                        product_id:          item.id,
                        quantity:            item.quantity,
                        installation_option: sels.length > 0 ? sels.map(s => s.label).join(', ') : null,
                    };
                }),
                coupon_code: appliedCoupon?.code ?? null,
                fulfillment: buildSaveCheckoutFulfillment(),
            });
            const opts = {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                body,
            };
            for (let attempt = 0; attempt < 2; attempt++) {
                try {
                    const res = await fetch('/paystack/save-checkout', opts);
                    if (res.ok) {
                        const data = await res.json();
                        return data; // { reference, total_ngn, coupon, coupon_error }
                    }
                    console.warn('[savePendingCheckout] server error', res.status);
                } catch (e) {
                    console.warn('[savePendingCheckout] network error:', e);
                }
                if (attempt === 0) await new Promise(r => setTimeout(r, 800));
            }
            return null;
        }

        // ── Confirm Stripe payment & create order ─────────────────────────────
        // Server verifies the PaymentIntent with Stripe and builds the order from
        // the trusted PendingCheckout snapshot. We send only the reference + the
        // PaymentIntent id — never prices, totals, or items.
        async function saveOrderToDatabase({ reference, paymentIntentId }) {
            try {
                const res = await fetch('/orders/stripe', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                    body: JSON.stringify({
                        reference,
                        payment_intent_id: paymentIntentId,
                    }),
                });
                const data = await res.json();
                if (!res.ok || !data.success) { console.error('Order save failed:', data); return null; }
                return data;
            } catch (err) { console.error('Could not save order:', err); return null; }
        }

        function finaliseOrder() {
            localStorage.removeItem('shoppingCart');
            localStorage.removeItem('pickupLocation');
            localStorage.removeItem('pickupLocationId');
            localStorage.removeItem('pickupPointId');
            localStorage.removeItem('pickupPointName');
            localStorage.removeItem('pickupPointAddress');
            localStorage.removeItem('deliveryStateId');
            localStorage.removeItem('deliveryStateName');
            localStorage.removeItem('deliveryLocationId');
            localStorage.removeItem('deliveryLocationName');
            localStorage.removeItem('deliveryBaseFeeNgn');
            localStorage.removeItem('deliveryTruckFeeNgn');
            localStorage.removeItem('deliveryFeeNgn');
            localStorage.removeItem('shippingAddress');
            cart = []; selectedLocation = null; selectedPickupPoint = null; _rawTotalNgn = 0; appliedCoupon = null;
            selectedDeliveryState = null; selectedDeliveryLocation = null; shippingAddress = '';
            Object.keys(installationSelections).forEach(k => delete installationSelections[k]);
            setFulfillmentMethod('pickup', { silent: true });
            renderCartItems(); renderPickupSection(); resetPaymentForms();
            window.location.href = '/account/orders';
        }

        // ── Stripe ────────────────────────────────────────────────────────────
        const stripePk = '{{ config("services.stripe.public") }}';
        let stripe, cardElement, cardMounted = false;
        try {
            stripe = Stripe(stripePk);
            const els = stripe.elements();
            cardElement = els.create('card', {
                style: { base: { fontSize: '14px', color: '#1a1a1a', fontFamily: 'DM Sans, Arial, sans-serif', '::placeholder': { color: '#aab7c4' } }, invalid: { color: '#dc2626' } }
            });
        } catch (e) { console.error('Stripe init error:', e); }

        function mountStripeCard() {
            if (!cardMounted && cardElement && stripeSection && !stripeSection.classList.contains('hidden')) {
                setTimeout(() => {
                    try {
                        cardElement.mount('#card-element');
                        cardMounted = true;
                        cardElement.on('change', event => { cardErrors.textContent = event.error ? event.error.message : ''; });
                    } catch (err) { console.error('Failed to mount Stripe card:', err); }
                }, 200);
            }
        }

        // ── Event listeners ───────────────────────────────────────────────────
        // Scrolls to the fulfillment section and flashes an amber ring around it.
        function flashFulfillmentSection() {
            const section = document.getElementById('fulfillmentSection');
            if (!section) return;
            section.scrollIntoView({ behavior: 'smooth', block: 'center' });
            section.classList.remove('flash-attention');
            void section.offsetWidth; // force reflow so the animation restarts
            section.classList.add('flash-attention');
        }

        proceedToPaymentBtn.addEventListener('click', () => {
            if (fulfillmentMethod === 'delivery') {
                if (!selectedDeliveryState) {
                    flashFulfillmentSection();
                    Swal.fire({ icon: 'warning', title: 'Delivery State Required', text: 'Please select a state to deliver to.', confirmButtonColor: '#4e7a1a' });
                    return;
                }
                if (!selectedDeliveryLocation) {
                    flashFulfillmentSection();
                    Swal.fire({ icon: 'warning', title: 'Delivery Location Required', text: 'Please choose a delivery location so we can calculate your delivery fee.', confirmButtonColor: '#4e7a1a' });
                    return;
                }
                if (!shippingAddress.trim()) {
                    flashFulfillmentSection();
                    shippingAddressInput?.focus();
                    Swal.fire({ icon: 'warning', title: 'Delivery Address Required', text: 'Please enter your street or delivery address so we know exactly where to bring your order.', confirmButtonColor: '#4e7a1a' });
                    return;
                }
            } else {
                if (!selectedLocation) {
                    flashFulfillmentSection();
                    Swal.fire({ icon: 'warning', title: 'Pickup Location Required', text: 'Please select a pickup location first — tap "Change" in the Pickup Options section.', confirmButtonColor: '#4e7a1a' });
                    return;
                }
                if (!selectedPickupPoint) {
                    flashFulfillmentSection();
                    Swal.fire({ icon: 'warning', title: 'Pickup Point Required', text: 'Almost there! Choose one of the pickup points shown below to continue.', confirmButtonColor: '#4e7a1a' });
                    return;
                }
            }
            if (cart.length === 0) { showMessage('danger', 'Your cart is empty.'); return; }
            paymentFormsSection.classList.remove('hidden');
            paymentFormsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        selectStripeBtn?.addEventListener('click', () => {
            resetPaymentForms(); stripeSection.classList.remove('hidden');
            if (cart.length === 0) { showMessage('danger', 'Your cart is empty.'); return; }
            cardErrors.textContent = ''; mountStripeCard();
        });

        selectPaystackBtn?.addEventListener('click', () => {
            resetPaymentForms(); paystackSection.classList.remove('hidden');
            updatePaystackHint();
            if (cart.length === 0) showMessage('danger', 'Your cart is empty.');
        });

        clearCartBtn.addEventListener('click', () => {
            if (cart.length === 0) { Swal.fire({ icon: 'info', title: 'Cart Already Empty', text: 'Your shopping cart is already empty!', confirmButtonColor: '#4e7a1a' }); return; }
            Swal.fire({ title: 'Are you sure?', text: 'Clear all items from your cart?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#4e7a1a', cancelButtonColor: '#6c757d', confirmButtonText: 'Yes, clear it!' }).then(r => {
                if (r.isConfirmed) {
                    cart = []; _rawTotalNgn = 0; appliedCoupon = null;
                    Object.keys(installationSelections).forEach(k => delete installationSelections[k]);
                    saveCartToLocalStorage(); renderCartItems(); updateCartDisplayHeader();
                    resetPaymentForms(); amountInput.value = '0.50';
                    refreshPaymentGate();
                    setCouponMsg('', '');
                    couponInput.value = ''; couponInput.disabled = false;
                    couponBtn.textContent = 'Apply'; couponBtn.classList.remove('is-remove');
                    summaryCouponRow.style.display = 'none';
                    Swal.fire('Cleared!', 'Your cart has been emptied.', 'success');
                }
            });
        });

        shippingAddressInput?.addEventListener('input', () => {
            shippingAddress = shippingAddressInput.value;
            localStorage.setItem('shippingAddress', shippingAddress);
            refreshPaymentGate();
        });

        closeLocationModalBtn?.addEventListener('click', closeLocationModal);
        locationModalOverlay?.addEventListener('click', e => { if (e.target === locationModalOverlay) closeLocationModal(); });
        confirmLocationBtn?.addEventListener('click', () => {
            if (selectedLocation) {
                // If the location changed, clear the previously-selected pickup point.
                if (localStorage.getItem('pickupLocationId') !== String(selectedLocation.id)) {
                    localStorage.removeItem('pickupPointId');
                    localStorage.removeItem('pickupPointName');
                    localStorage.removeItem('pickupPointAddress');
                }
                localStorage.setItem('pickupLocation',   selectedLocation.name);
                localStorage.setItem('pickupLocationId', selectedLocation.id);
            }
            closeLocationModal(); renderPickupSection();
        });
        clearLocationFilterBtn?.addEventListener('click', () => {
            localStorage.removeItem('pickupLocation');
            localStorage.removeItem('pickupLocationId');
            localStorage.removeItem('pickupPointId');
            localStorage.removeItem('pickupPointName');
            localStorage.removeItem('pickupPointAddress');
            selectedLocation = null;
            selectedPickupPoint = null;
            document.querySelectorAll('.location-item').forEach(i => i.classList.remove('selected'));
            closeLocationModal(); renderPickupSection();
        });
        changePickupLocationBtn?.addEventListener('click', openLocationModal);

        // ── Stripe submit ─────────────────────────────────────────────────────
        stripeForm?.addEventListener('submit', async e => {
            e.preventDefault(); clearMessages();
            if (!cardMounted) { showMessage('danger', 'Card form not ready. Please click "Pay with Card" again.'); return; }
            if (fulfillmentMethod === 'delivery' && !selectedDeliveryLocation) { showMessage('danger', 'Please select a delivery location first.'); return; }
            if (fulfillmentMethod === 'pickup' && !selectedPickupPoint) { showMessage('danger', 'Please select a pickup point first.'); return; }
            showLoading(stripeSubmitButton, stripeSpinner); cardErrors.textContent = '';
            const { usdCents } = calculateCartTotals();
            if (usdCents < 50) { showMessage('danger', 'Minimum payment is $0.50.'); hideLoading(stripeSubmitButton, stripeSpinner); return; }
            if (cart.length === 0) { showMessage('danger', 'Your cart is empty.'); hideLoading(stripeSubmitButton, stripeSpinner); return; }
            try {
                // 1. Save the cart server-side FIRST. The server prices everything from
                //    the DB, generates the reference, and (step 2) computes the USD
                //    charge — the browser never dictates the amount or the items.
                const saved = await savePendingCheckout();
                if (!saved?.reference) {
                    hideLoading(stripeSubmitButton, stripeSpinner);
                    showMessage('danger', 'Could not prepare your order. Please check your connection and try again.');
                    return;
                }
                // 2. Create the PaymentIntent for that reference (amount set server-side).
                const res = await fetch('/stripe/create-payment-intent', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                    body: JSON.stringify({ reference: saved.reference }),
                });
                const data = await res.json();
                if (!res.ok || data.error) throw new Error(data.error || ('Server error (' + res.status + ')'));
                const { paymentIntent, error } = await stripe.confirmCardPayment(data.clientSecret, {
                    payment_method: { card: cardElement, billing_details: { email: emailInput.value } }
                });
                if (error) { cardErrors.textContent = error.message; showMessage('danger', 'Payment failed: ' + error.message); }
                else if (paymentIntent.status === 'succeeded') {
                    showProcessing('Payment successful', 'Saving your order — please don\'t close this page.');
                    showMessage('info', 'Saving your order\u2026');
                    const stripeOrderData = await saveOrderToDatabase({ reference: saved.reference, paymentIntentId: paymentIntent.id });
                    if (!stripeOrderData) {
                        hideProcessing();
                        showMessage('danger', 'Your Stripe payment was successful but the order could not be saved. Please contact support immediately with your payment reference: ' + paymentIntent.id);
                        return;
                    }
                    processingSuccess('Order saved!', 'Redirecting to your orders\u2026');
                    showMessage('success', 'Payment successful! Redirecting\u2026');
                    setTimeout(finaliseOrder, 800);
                }
            } catch (err) { hideProcessing(); showMessage('danger', 'Payment error: ' + err.message); }
            finally { hideLoading(stripeSubmitButton, stripeSpinner); }
        });

        // ── Paystack submit ───────────────────────────────────────────────────
        paystackSubmitButton?.addEventListener('click', async () => {
            clearMessages();
            if (fulfillmentMethod === 'delivery' && !selectedDeliveryLocation) { showMessage('danger', 'Please select a delivery location first.'); return; }
            if (fulfillmentMethod === 'pickup' && !selectedPickupPoint) { showMessage('danger', 'Please select a pickup point first.'); return; }
            showLoading(paystackSubmitButton, paystackSpinner);
            calculateCartTotals();
            const totalNgn = parseFloat(summaryTotal.dataset.ngn) || 0;
            const email = emailInput.value.trim();
            if (!email || !email.includes('@')) { showMessage('danger', 'Please enter a valid email address.'); hideLoading(paystackSubmitButton, paystackSpinner); return; }
            const amountInKobo = Math.round(totalNgn * 100);
            if (amountInKobo < 50) { showMessage('danger', 'Amount too small for payment.'); hideLoading(paystackSubmitButton, paystackSpinner); return; }
            if (cart.length === 0) { showMessage('danger', 'Your cart is empty.'); hideLoading(paystackSubmitButton, paystackSpinner); return; }
            const cartSnapshot = [...cart];
            const totalUsd     = parseFloat(amountInput.value);
            const orderItems   = buildOrderItems(cartSnapshot);

            // Save cart to server — server computes prices, generates the reference,
            // and returns both. The browser never picks the reference.
            const saved = await savePendingCheckout();
            if (!saved?.reference) {
                hideLoading(paystackSubmitButton, paystackSpinner);
                showMessage('danger', 'Could not prepare your order. Please check your connection and try again.');
                return;
            }
            const ref       = saved.reference;
            const finalNgn  = saved.total_ngn ?? totalNgn;
            const finalKobo = Math.round(finalNgn * 100);
            PaystackPop.setup({
                key: paystackPk, email, amount: finalKobo, currency: 'NGN',
                ref,
                metadata: { cart: JSON.stringify(cartSnapshot), customer_email: email },
                onClose() { hideLoading(paystackSubmitButton, paystackSpinner); showMessage('info', 'Payment window closed.'); },
                callback(response) {
                    hideLoading(paystackSubmitButton, paystackSpinner);
                    if (response.status === 'success') {
                        // Show overlay IMMEDIATELY as the popup closes so there is zero gap.
                        showProcessing('Payment received!', 'Confirming your order\u2026');
                        verifyPaystackPayment(response.reference);
                    } else {
                        showMessage('danger', 'Transaction failed. Ref: ' + response.reference);
                    }
                }
            }).openIframe();
        });

    // Reassurance messages shown while the server verify call runs.
    // Clears itself; call stopVerifyTicker() if the call resolves early.
    let _verifyTickerTimer = null;
    function startVerifyTicker() {
        const steps = [
            [5000,  'Still confirming \u2014 your payment is safe.'],
            [10000, 'Almost there \u2014 this can take a moment.'],
            [18000, 'Taking a little longer than usual \u2014 please stay on this page.'],
            [28000, 'Still working \u2014 your payment is secured, we\u2019re finalising your order.'],
        ];
        steps.forEach(([delay, msg]) => {
            const t = setTimeout(() => { updateProcessing(null, msg); }, delay);
            _verifyTickerTimers = _verifyTickerTimers || [];
            _verifyTickerTimers.push(t);
        });
    }
    let _verifyTickerTimers = [];
    function stopVerifyTicker() {
        (_verifyTickerTimers || []).forEach(clearTimeout);
        _verifyTickerTimers = [];
    }

    // Guards against the popup callback and the redirect-back handler both
    // finalising the same payment (they can BOTH fire on fast connections).
    let _paymentFinalising = false;

    async function verifyPaystackPayment(ref, attempt = 1) {
        if (_paymentFinalising) return;
        clearMessages();
        if (attempt === 1) startVerifyTicker();
        try {
            const res = await fetch('/paystack/confirm-order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ reference: ref }),
            });
            const data = await res.json().catch(() => ({}));

            if (res.ok && data.success) {
                stopVerifyTicker();
                _paymentFinalising = true;
                processingSuccess('Order confirmed!', 'Redirecting to your orders\u2026');
                showMessage('success', 'Payment confirmed! Redirecting\u2026');
                setTimeout(finaliseOrder, 800);
                return;
            }

            // Not confirmed on this attempt. On fast connections the Paystack
            // webhook is often still creating the order server-side. fulfil() is
            // idempotent, so a retry returns the existing order as a success \u2014
            // that\u2019s why we retry silently instead of alarming the customer.
            if (attempt < 3) {
                updateProcessing(null, 'Finalising your order\u2026');
                setTimeout(() => verifyPaystackPayment(ref, attempt + 1), 2000);
                return;
            }

            // Still unconfirmed after retries. The payment succeeded (Paystack
            // fired the success callback) and the webhook is the backstop that
            // creates the order \u2014 send them to their orders page rather than a
            // scary popup. The order will be there momentarily.
            stopVerifyTicker();
            _paymentFinalising = true;
            processingSuccess('Payment received!', 'Your order is being finalised \u2014 taking you to your orders\u2026');
            setTimeout(finaliseOrder, 1500);
        } catch (err) {
            // Network hiccup confirming \u2014 retry silently, then fall back to the
            // orders page (webhook backstop still creates the order).
            if (attempt < 3) {
                setTimeout(() => verifyPaystackPayment(ref, attempt + 1), 2000);
                return;
            }
            stopVerifyTicker();
            _paymentFinalising = true;
            processingSuccess('Payment received!', 'Your order is being finalised \u2014 taking you to your orders\u2026');
            setTimeout(finaliseOrder, 1500);
        }
    }

    // ── Init ──────────────────────────────────────────────────────────────
    loadCartFromLocalStorage();

    // Re-verify requires_truck and inferred_weight_kg from server so stale
    // carts always use the current category/subcategory settings.
    async function syncTruckFlags() {
        if (!cart.length) return;
        const ids = cart.map(i => i.id).filter(Boolean).join(',');
        try {
            const res = await fetch('/api/cart/truck-check?ids=' + ids);
            if (!res.ok) return;
            const data = await res.json();

            // Response now wraps products + thresholds; fall back for old shape
            const map = data.products ?? data;

            if (data.thresholds) {
                weightThresholdKg      = parseFloat(data.thresholds.weight_kg)       || 30;
                orderValueThresholdNgn = parseFloat(data.thresholds.order_value_ngn) || 1000000;
            }

            let changed = false;
            cart.forEach(item => {
                const fresh = map[String(item.id)];
                if (fresh === undefined) return;
                const freshTruck  = Boolean(fresh.requires_truck);
                const freshWeight = parseFloat(fresh.inferred_weight_kg) || 5.0;
                if (freshTruck !== Boolean(item.requires_truck)) {
                    item.requires_truck = freshTruck;
                    changed = true;
                }
                if (freshWeight !== (item.inferred_weight_kg || 0)) {
                    item.inferred_weight_kg = freshWeight;
                    changed = true;
                }
            });
            if (changed) saveCartToLocalStorage();
        } catch (_) {}
    }
    await syncTruckFlags();

    await fetchLocations();
    await fetchStates();
    renderCartItems();
    updateCartDisplayHeader();
    populateStateSelect();

    // If one method is disabled, force the available one regardless of localStorage
    if (!pickupEnabled && deliveryEnabled) {
        fulfillmentMethod = 'delivery';
    } else if (!deliveryEnabled && pickupEnabled) {
        fulfillmentMethod = 'pickup';
    } else if (!pickupEnabled && !deliveryEnabled) {
        fulfillmentMethod = 'pickup'; // panel will still be hidden; user sees unavailable notice
    }

    setFulfillmentMethod(fulfillmentMethod, { silent: true });
    await renderPickupSection();

    if (fulfillmentMethod === 'delivery') {
        const savedStateId = localStorage.getItem('deliveryStateId');
        if (savedStateId) {
            selectedDeliveryState = { id: savedStateId, name: localStorage.getItem('deliveryStateName') };
            // Ensure pre-selected values are fully restored to state
            const savedBaseFeeNgn = parseFloat(localStorage.getItem('deliveryBaseFeeNgn')) || 0;
            const savedTruckFeeNgn = parseFloat(localStorage.getItem('deliveryTruckFeeNgn')) || 0;
            const savedLocationId = localStorage.getItem('deliveryLocationId');
            
            if (savedLocationId) {
                selectedDeliveryLocation = {
                    id: savedLocationId,
                    name: localStorage.getItem('deliveryLocationName'),
                    baseFeeNgn: savedBaseFeeNgn,
                    truckFeeNgn: savedTruckFeeNgn
                };
            }
            await populateLocationSelectForState(savedStateId, savedLocationId);
        }
    }

    // Restore shipping address textarea from localStorage
    if (shippingAddressInput && shippingAddress) {
        shippingAddressInput.value = shippingAddress;
    }

    refreshPaymentGate();

    // ── Handle Paystack redirect-back reference (AFTER cart is loaded) ─────
    const paystackRef = new URLSearchParams(window.location.search).get('reference');
    if (paystackRef) {
        history.replaceState(null, '', window.location.pathname);
        calculateCartTotals();
        const orderItems = buildOrderItems([...cart]);
        if (!orderItems.length) {
            // Cart already cleared — webhook likely created the order already.
            // Redirect to orders page rather than failing with a 422.
            showProcessing('Payment received!', 'Retrieving your order…');
            setTimeout(() => { window.location.href = '/account/orders'; }, 2000);
        } else {
            verifyPaystackPayment(paystackRef);
        }
    }
});
</script>

@endsection