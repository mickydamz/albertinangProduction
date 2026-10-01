@extends('layouts.invoicelayout')

@section('title', 'Invoice #' . $order->order_number)

@push('styles')
<style>
    :root {
        --inv-accent:      {{ $invAccent      ?? '#1a7a4a' }};
        --inv-accent-bg:   {{ $invAccentBg    ?? '#e8f5ee' }};
        --inv-accent-fill: {{ $invAccentBg    ?? '#eef7f1' }};
    }
    .invoice-actions { display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-bottom: 24px; }
    .inv-btn {
        background: var(--inv-accent); color: #fff; padding: 10px 22px; border: none;
        border-radius: 5px; cursor: pointer; font-weight: 700; font-size: 14px;
        display: inline-flex; align-items: center; gap: 8px; transition: background .2s;
    }
    .inv-btn:hover { background: #155e38; }
    @media print {
        .invoice-actions { display: none !important; }
        body, html { background: #fff !important; padding: 0 !important; }
        /* Cancel the mobile shrink transform for print/PDF output */
        .inv-page { box-shadow: none !important; margin: 0 !important; transform: none !important; width: auto !important; }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
    }

    .inv-page {
        background: #ffffff;
        max-width: 960px;
        margin: 0 auto;
        padding: 28px 36px;
        color: #1a1a1a;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        line-height: 1.45;
    }

    /* Header — sizes matched to PDF output */
    .inv-header-tbl { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .inv-header-tbl td { vertical-align: middle; }
    .inv-brand-logo { height: 60px; width: auto; max-width: 220px; object-fit: contain; display: block; }
    .inv-right-head { text-align: right; }
    .inv-title { font-size: 24px; font-weight: bold; color: var(--inv-accent); letter-spacing: 2px; display: block; }
    .inv-contact-small { font-size: 10px; color: #888; margin-top: 3px; line-height: 1.7; }

    /* Issue date */
    .inv-issue-date {
        text-align: right; font-weight: bold; font-size: 12px;
        letter-spacing: .4px; text-transform: uppercase; color: #1a1a1a; margin-bottom: 16px;
    }

    /* Green bar */
    .inv-green-bar { background: var(--inv-accent); height: 26px; width: 100%; margin-bottom: 0; }

    /* Info tables */
    .inv-info-tbl { width: 100%; border-collapse: collapse; border: 1px solid #d0d0d0; margin-bottom: 0; }
    .inv-info-tbl td { width: 50%; vertical-align: top; }
    .inv-info-header {
        background: var(--inv-accent-bg); padding: 10px 20px; font-weight: 700; font-size: 12.5px;
        color: var(--inv-accent); text-transform: uppercase; letter-spacing: .5px;
        border-bottom: 1px solid #d0d0d0;
    }
    .inv-info-header.right { text-align: right; }
    .inv-info-body { padding: 18px 20px; font-size: 13.5px; line-height: 1.8; }
    .inv-info-right { border-left: 1px solid #d0d0d0; }
    .inv-info-body-right { padding: 18px 20px; text-align: right; font-size: 13.5px; line-height: 1.9; }

    /* Fulfilment box */
    .inv-fulfilment-tbl {
        width: 100%; border-collapse: collapse;
        border: 1px solid #d0d0d0; border-top: 0; margin-bottom: 22px;
    }
    .inv-fulfilment-header {
        background: var(--inv-accent-bg); padding: 10px 20px; font-weight: 700; font-size: 12.5px;
        color: var(--inv-accent); text-transform: uppercase; letter-spacing: .5px;
        border-bottom: 1px solid #d0d0d0;
    }
    .inv-fulfilment-body { padding: 18px 20px; font-size: 13.5px; line-height: 1.8; }

    /* Items table */
    table.items { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
    table.items thead th {
        background: #1f2937; color: #fff; font-size: 12px; font-weight: 700;
        text-align: left; padding: 11px 14px; letter-spacing: .5px;
        text-transform: uppercase; border: 1px solid #1f2937;
    }
    table.items thead th.num { text-align: right; }
    table.items tbody td { padding: 11px 14px; border: 1px solid #e0e0e0; font-size: 13.5px; color: #1a1a1a; }
    table.items tbody td.num { text-align: right; }
    table.items tbody tr:nth-child(even) td { background: #fafafa; }
    .item-install-row td { background: #f7fdf9 !important; border-top: none !important; padding-top: 4px !important; padding-bottom: 8px !important; }
    .item-sku { font-size: 11.5px; color: #888; margin-top: 2px; }
    .item-install-label { font-size: 12.5px; color: var(--inv-accent); padding-left: 24px; }

    /* Notes + Totals */
    table.inv-bottom { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
    table.inv-bottom > tbody > tr > td { vertical-align: top; }
    .inv-notes-box { background: var(--inv-accent-fill); padding: 18px 20px; font-size: 13px; line-height: 1.8; }
    .inv-notes-box strong { display: block; margin-bottom: 8px; font-size: 12.5px; color: var(--inv-accent); text-transform: uppercase; letter-spacing: .4px; }
    .inv-totals-box { background: var(--inv-accent-fill); padding: 18px 20px; font-size: 13.5px; }
    .inv-totals-tbl { width: 100%; border-collapse: collapse; }
    .inv-totals-tbl td { padding: 4px 0; }
    .inv-totals-tbl td.lbl { color: #333; font-weight: 600; }
    .inv-totals-tbl td.val { text-align: right; color: #1a1a1a; }
    .inv-totals-tbl tr.total-row td { border-top: 2px solid #1a1a1a; font-weight: 700; font-size: 15px; padding-top: 8px; }

    /* Terms */
    .inv-terms { border: 1px solid #d0d0d0; padding: 16px 20px; font-size: 12px; line-height: 1.9; color: #444; margin-bottom: 28px; }
    .inv-terms strong { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 700; color: #1a1a1a; }

    /* Footer */
    .inv-footer { text-align: center; color: #888; font-size: 12.5px; margin-top: 12px; }

    /* ── Mobile: shrink the whole invoice to fit (same layout, just smaller) ──
       The full desktop layout is rendered at a fixed design width, then scaled
       down proportionally by JS so everything fits without reflowing. */
    @media (max-width: 767px) {
        .invlayout-body { padding-left: 0 !important; padding-right: 0 !important; overflow-x: hidden; }
        .inv-page {
            width: 760px;          /* fixed design width; JS scales to fit viewport */
            max-width: none;
            margin: 0;
            transform-origin: top left;
        }
        .invoice-actions { margin-left: 14px; margin-right: 14px; }
    }
</style>
@endpush

@section('header_actions')
    <div class="invoice-actions" style="gap:10px;">
        @isset($downloadUrl)
            <a href="{{ $downloadUrl }}" class="inv-btn" style="background:#155e38;text-decoration:none;">
                <i class="fas fa-download"></i> Download PDF
            </a>
        @endisset
        <button onclick="window.print()" class="inv-btn">
            <i class="fas fa-print"></i> Print Invoice
        </button>
    </div>
@endsection

@section('content')

@php
    $storeName    = $appStoreName ?? 'AlbertinaNG';
    $storeAddr    = $storeAddress ?? 'No. 22 Zik Avenue, Uwani, Enugu State, Nigeria';
    $storeContact = $storeEmail   ?? 'support@albertinang.com';
    $storePhone   = $storePhone   ?? null;

    $isPickup     = $order->fulfillment_method === 'pickup';

    // Customer info
    $customerName  = $order->user->name  ?? 'Guest';
    $customerEmail = $order->user->email ?? $order->customer_email ?? '';
    $customerPhone = $order->user?->phone_no ?? null;

    // item->price is stored as base + installation_extra_ngn per unit
    $installTotal  = $order->items->sum(fn($i) => $i->installation_extra_ngn * $i->quantity);
    $itemsBase     = $order->items->sum(fn($i) => $i->price * $i->quantity);
    $baseOnlyTotal = $itemsBase - $installTotal;  // pure product cost for the totals breakdown
    $discount      = (float) ($order->coupon_discount_ngn ?? 0);
    $deliveryFee   = (float) ($order->shipping_cost ?? 0);
    $grandTotal    = (float) $order->total;

    // ── Admin-configured layout (per fulfilment mode) ─────────────────────────
    // Only the body SECTION ORDER is configurable; the header is fixed.
    $defaultBody = ['bill_to','fulfillment','items','notes_totals','bank','terms','footer'];
    $bodyOrder = ($invLayout['body'] ?? null) ?: $defaultBody;
@endphp

<div class="inv-page">

    {{-- HEADER (shared partial — identical to the admin editor preview) --}}
    @include('sims.partials.invoice-header', ['issueDate' => $order->created_at->format('d F Y')])

    {{-- BODY SECTIONS — rendered in the admin-configured order --}}
    @foreach($bodyOrder as $sec)

        @if($sec === 'bill_to')
        {{-- BILL TO / INVOICE DETAILS --}}
        <table class="inv-info-tbl">
            <tr>
                <td><div class="inv-info-header">Bill To</div></td>
                <td class="inv-info-right"><div class="inv-info-header right">Invoice Details</div></td>
            </tr>
            <tr>
                <td>
                    <div class="inv-info-body">
                        <strong>{{ $customerName }}</strong><br>
                        @if($order->user?->shipping_address){{ $order->user->shipping_address }}<br>@endif
                        @if($order->user?->city){{ $order->user->city }}<br>@endif
                        @if($order->user?->state){{ $order->user->state }}<br>@endif
                        @if($customerEmail)<span style="color:#555;">{{ $customerEmail }}</span><br>@endif
                        @if($customerPhone)<span style="color:#555;">{{ $customerPhone }}</span>@endif
                    </div>
                </td>
                <td class="inv-info-right">
                    <div class="inv-info-body-right">
                        <span style="color:#555;">Order No.:</span> {{ $order->order_number }}<br>
                        <span style="color:#555;">Currency:</span> NGN (₦)<br>
                        <span style="color:#555;">Payment:</span> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}<br>
                        @if($order->reference)
                            <span style="color:#555;">Reference:</span> {{ $order->reference }}<br>
                        @endif
                        @if($order->coupon_code_used)
                            <span style="color:#555;">Coupon:</span> {{ $order->coupon_code_used }}
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        @elseif($sec === 'fulfillment')
        {{-- COLLECTION FROM / DELIVERY TO --}}
        <table class="inv-fulfilment-tbl">
            <tr>
                <td>
                    <div class="inv-fulfilment-header">
                        {{ $isPickup ? 'Collection From' : 'Delivery To' }}
                    </div>
                    <div class="inv-fulfilment-body">
                        @if($isPickup)
                            <strong>{{ $order->pickup_point_name ?: $storeName . ' Collection Point' }}</strong><br>
                            @if($order->pickup_point_address){{ $order->pickup_point_address }}<br>@endif
                            @if($order->pickup_location){{ $order->pickup_location }}<br>@endif
                            {{ $storeContact }}
                        @else
                            <strong>{{ $customerName }}</strong><br>
                            @if($order->shipping_address){{ $order->shipping_address }}<br>@endif
                            @if($order->delivery_location_name){{ $order->delivery_location_name }},&nbsp;@endif
                            @if($order->delivery_state_name){{ $order->delivery_state_name }}<br>@endif
                            Nigeria<br>
                            @if($customerEmail){{ $customerEmail }}@endif
                            @if($customerPhone)<br>{{ $customerPhone }}@endif
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        @elseif($sec === 'items')
        {{-- ITEMS TABLE --}}
        <table class="items">
            <thead>
                <tr>
                    <th style="width:4%">#</th>
                    <th style="width:52%">Item Description</th>
                    <th class="num" style="width:8%">Qty</th>
                    <th class="num" style="width:18%">Unit Price</th>
                    <th class="num" style="width:18%">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $rowNum = 0; @endphp
                @foreach($order->items as $item)
                    @php $rowNum++; @endphp
                    @php
                        $basePrice  = $item->price - $item->installation_extra_ngn;
                        $baseAmount = $basePrice * $item->quantity;
                        $instAmount = $item->installation_extra_ngn * $item->quantity;
                    @endphp
                    <tr>
                        <td>{{ $rowNum }}</td>
                        <td>
                            {{ $item->name }}
                            @if($item->sku)
                                <div class="item-sku">SKU: {{ $item->sku }}</div>
                            @endif
                        </td>
                        <td class="num">{{ $item->quantity }}</td>
                        <td class="num">&#8358;{{ number_format($basePrice, 2) }}</td>
                        <td class="num">&#8358;{{ number_format($baseAmount, 2) }}</td>
                    </tr>
                    @if($item->installation_option && $item->installation_extra_ngn > 0)
                        <tr class="item-install-row">
                            <td></td>
                            <td class="item-install-label">
                                &#8627; Installation: {{ $item->installation_option }}
                            </td>
                            <td class="num" style="font-size:12.5px;color:#555;">{{ $item->quantity }}</td>
                            <td class="num" style="font-size:12.5px;color:#555;">&#8358;{{ number_format($item->installation_extra_ngn, 2) }}</td>
                            <td class="num" style="font-size:12.5px;color:#555;">&#8358;{{ number_format($instAmount, 2) }}</td>
                        </tr>
                    @elseif($item->installation_option)
                        <tr class="item-install-row">
                            <td></td>
                            <td class="item-install-label" colspan="4">
                                &#8627; Installation: {{ $item->installation_option }} (included)
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        @elseif($sec === 'notes_totals')
        {{-- ORDER NOTES + TOTALS --}}
        <table class="inv-bottom">
            <tr>
                <td style="width:55%;padding-right:16px;">
                    <div class="inv-notes-box">
                        <strong>Order Notes</strong>
                        Fulfilment: {{ $isPickup ? 'Customer collection' : 'Home delivery' }}<br>
                        @if(!$isPickup && $order->delivery_state_name)
                            Delivery to: {{ $order->delivery_location_name ? $order->delivery_location_name . ', ' : '' }}{{ $order->delivery_state_name }}<br>
                        @endif
                        Order reference: {{ $order->order_number }}<br>
                        @if($order->coupon_code_used)
                            Coupon applied: {{ $order->coupon_code_used }}<br>
                        @endif
                        Customer support: {{ $storeContact }}
                    </div>
                </td>
                <td style="width:45%;">
                    <div class="inv-totals-box">
                        <table class="inv-totals-tbl">
                            <tr>
                                <td class="lbl">Items Subtotal</td>
                                <td class="val">&#8358;{{ number_format($baseOnlyTotal, 2) }}</td>
                            </tr>
                            @if($installTotal > 0)
                            <tr>
                                <td class="lbl">Installation</td>
                                <td class="val">+&#8358;{{ number_format($installTotal, 2) }}</td>
                            </tr>
                            @endif
                            @if($deliveryFee > 0)
                            <tr>
                                <td class="lbl">Delivery Fee</td>
                                <td class="val">+&#8358;{{ number_format($deliveryFee, 2) }}</td>
                            </tr>
                            @endif
                            @if($discount > 0)
                            <tr>
                                <td class="lbl">Discount</td>
                                <td class="val">-&#8358;{{ number_format($discount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="total-row">
                                <td class="lbl">Total Due</td>
                                <td class="val">&#8358;{{ number_format($grandTotal, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        @elseif($sec === 'bank')
        {{-- BANK DETAILS (optional) --}}
        @if(!empty($invBankDetails))
        <div class="inv-terms" style="margin-bottom:16px;">
            <strong>Bank Details</strong>
            {!! nl2br(e($invBankDetails)) !!}
        </div>
        @endif

        @elseif($sec === 'terms')
        {{-- TERMS --}}
        <div class="inv-terms">
            <strong>Terms &amp; Conditions</strong>
            @if(!empty($invTerms))
                {!! nl2br(e($invTerms)) !!}
            @else
                1. This invoice is evidence of your order and payment record. Please retain it for your records.<br>
                2. Goods may be returned or exchanged only in accordance with the {{ $storeName }} Returns Policy; eligibility depends on product condition and category.<br>
                3. Report delivery issues or damaged goods within 48 hours of receipt via {{ $storeContact }}, quoting your order number.<br>
                4. Prices are in Nigerian Naira (₦).<br>
                5. This invoice is generated electronically and requires no signature.
            @endif
        </div>

        @elseif($sec === 'footer')
        {{-- FOOTER --}}
        <div class="inv-footer">
            @if(!empty($invFooter))
                {!! nl2br(e($invFooter)) !!}
            @else
                Thank you for shopping with {{ $storeName }}. | {{ $storeContact }}
            @endif
        </div>
        @endif

    @endforeach

</div>
@endsection

@push('scripts')
<script>
    const params = new URLSearchParams(window.location.search);
    if (params.get('print') === '1') {
        window.addEventListener('load', () => setTimeout(() => window.print(), 500));
    }

    // ── Mobile: scale the whole invoice down to fit the viewport ──
    // The invoice renders at its fixed 760px design width; here we shrink it
    // proportionally so everything is visible at once (no horizontal scroll),
    // then pull the following flow up to remove the gap the transform leaves.
    (function () {
        var DESIGN_W = 760;
        var mq = window.matchMedia('(max-width: 767px)');

        function fitInvoice() {
            var page = document.querySelector('.inv-page');
            if (!page) return;

            // Reset first so measurements are taken at natural size.
            page.style.transform = 'none';
            page.style.marginBottom = '';

            if (!mq.matches) return;

            var avail = (page.parentElement || document.body).clientWidth;
            var scale = Math.min(1, avail / DESIGN_W);
            var h = page.offsetHeight;               // natural (unscaled) height

            page.style.transform = 'scale(' + scale + ')';
            page.style.marginBottom = (-(h * (1 - scale))) + 'px'; // collapse leftover space
        }

        window.addEventListener('load', fitInvoice);
        window.addEventListener('resize', fitInvoice);
        window.addEventListener('orientationchange', fitInvoice);
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(fitInvoice);
    })();
</script>
@endpush
