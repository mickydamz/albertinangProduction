@extends('layouts.adminlayout')
@section('title', 'Invoice Template')
@section('content')

@php
$defaultHdr = [
    'logo'      => ['t'=>5,  'l'=>0,  'w'=>30],
    'storename' => ['t'=>48, 'l'=>0,  'w'=>38],
    'invtitle'  => ['t'=>4,  'l'=>60, 'w'=>40],
    'issuedate' => ['t'=>60, 'l'=>60, 'w'=>40],
];
$defaultBody = ['bill_to','fulfillment','items','notes_totals','bank','terms','footer'];

// Two independent layouts — one per fulfilment mode. Fall back to the legacy
// single `inv_layout` (so existing installs migrate cleanly), then to defaults.
$legacyLayout   = json_decode($settings['inv_layout']          ?? '{}', true) ?: [];
$pickupLayout   = json_decode($settings['inv_layout_pickup']   ?? '{}', true) ?: [];
$deliveryLayout = json_decode($settings['inv_layout_delivery'] ?? '{}', true) ?: [];

$mkLayout = function ($layout) use ($legacyLayout, $defaultHdr, $defaultBody) {
    $src = !empty($layout) ? $layout : $legacyLayout;
    return [
        'hdr'  => $src['hdr']  ?? $defaultHdr,
        'body' => $src['body'] ?? $defaultBody,
    ];
};

// Pre-build JS init data here so @json receives a single variable, not an inline array literal
$invInitData = [
    'pickup'     => $mkLayout($pickupLayout),
    'delivery'   => $mkLayout($deliveryLayout),
    'defaultHdr' => $defaultHdr,
    'defaultBody'=> $defaultBody,
    'accent'     => $settings['inv_accent_color'] ?? '#1a7a4a',
    'accentBg'   => $settings['inv_accent_bg']    ?? '#e8f5ee',
    'headerNote' => $settings['inv_header_note']  ?? '',
    'bankDetails'=> $settings['inv_bank_details'] ?? '',
    'terms'      => $settings['inv_terms']        ?? '',
    'footer'     => $settings['inv_footer']       ?? '',
    'storeName'  => $appStoreName ?? 'Albertina Nigeria',
    'storeAddr'  => $storeAddress ?? 'No. 22 Zik Avenue, Uwani, Enugu',
    'storeEmail' => $storeEmail   ?? 'support@albertinang.com',
    'storePhone' => $storePhone   ?? '',
    // Real logo so the preview matches the printed invoice exactly.
    'logoUrl'    => !empty($appStoreLogo) ? asset('storage/' . $appStoreLogo) : asset('image.png'),
    'issueDate'  => now()->format('d F Y'),
];
@endphp

<style>
.inv-page{display:flex;height:calc(100vh - 64px);overflow:hidden;}

/* Settings panel */
.inv-settings{width:300px;flex-shrink:0;background:#fff;border-right:1px solid var(--al-border);display:flex;flex-direction:column;height:100%;overflow:hidden;}
.inv-s-head{padding:14px 16px 10px;border-bottom:1px solid var(--al-border);display:flex;align-items:center;gap:9px;flex-shrink:0;}
.inv-s-head h2{font-size:13px;font-weight:800;color:var(--al-text);margin:0;}
.inv-s-body{padding:12px 16px;flex:1;overflow-y:auto;}
.inv-s-foot{padding:10px 16px;border-top:1px solid var(--al-border);flex-shrink:0;}
.inv-field{margin-bottom:13px;}
.inv-lbl{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--al-text-3);display:block;margin-bottom:5px;}
.inv-input,.inv-ta{width:100%;padding:7px 10px;border:1px solid var(--al-border);border-radius:7px;font-size:12px;color:var(--al-text);background:#fff;outline:none;transition:border-color .15s,box-shadow .15s;font-family:inherit;box-sizing:border-box;}
.inv-input:focus,.inv-ta:focus{border-color:var(--al-primary);box-shadow:0 0 0 3px rgba(78,122,26,.12);}
.inv-ta{resize:vertical;min-height:68px;line-height:1.5;}
.inv-cr{display:flex;gap:7px;align-items:center;}
.inv-sw{width:32px;height:32px;border-radius:7px;border:1px solid var(--al-border);padding:2px;cursor:pointer;flex-shrink:0;}
.inv-hex{flex:1;}
.inv-div{height:1px;background:var(--al-border);margin:12px 0;}
.inv-slbl{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.7px;color:var(--al-text-3);margin-bottom:8px;display:flex;align-items:center;gap:5px;}
.inv-slbl::after{content:'';flex:1;height:1px;background:var(--al-border);}
.inv-save{width:100%;padding:9px;background:var(--al-primary);color:#fff;border:none;border-radius:7px;font-size:12.5px;font-weight:700;cursor:pointer;transition:background .15s;display:flex;align-items:center;justify-content:center;gap:7px;}
.inv-save:hover{background:var(--al-primary-dark,#3a5e14);}

/* Canvas area */
.inv-canvas-area{flex:1;overflow:auto;background:#cdd0d4 radial-gradient(circle at 1px 1px,rgba(0,0,0,.07) 1px,transparent 0);background-size:22px 22px;padding:20px 28px;}
.inv-toolbar{display:flex;align-items:center;gap:8px;margin-bottom:14px;flex-wrap:wrap;}
.inv-toolbar-info{display:flex;align-items:center;gap:7px;background:rgba(255,255,255,.9);backdrop-filter:blur(6px);border:1px solid rgba(0,0,0,.1);border-radius:20px;padding:5px 14px;font-size:11px;color:#555;box-shadow:0 1px 4px rgba(0,0,0,.1);}
.inv-tb-btn{padding:5px 12px;border-radius:12px;background:#fff;border:1px solid #ddd;font-size:11px;font-weight:600;color:#555;cursor:pointer;transition:all .15s;}
.inv-tb-btn:hover{background:#f0f0f0;border-color:#bbb;}
.inv-tb-btn.active{background:#1a1a1a;color:#fff;border-color:#1a1a1a;}

/* Paper */
.inv-paper{width:760px;box-sizing:border-box;background:#fff;box-shadow:0 2px 4px rgba(0,0,0,.07),0 8px 24px rgba(0,0,0,.13),0 28px 64px rgba(0,0,0,.09);border-radius:2px;margin:0 auto;padding:28px 36px;}

/* ── Mobile / tablet: stack the settings form above the preview ── */
@media (max-width: 991px) {
    .inv-page { flex-direction: column; height: auto; overflow: visible; }
    .inv-settings {
        width: 100%; height: auto; overflow: visible;
        border-right: none; border-bottom: 1px solid var(--al-border);
    }
    .inv-s-body { overflow-y: visible; }
    .inv-canvas-area { overflow-x: auto; padding: 14px; }
    .inv-paper { width: 100%; min-width: 0; padding: 18px; }
    .inv-toolbar { justify-content: flex-start; }
}


/* Body sections */
.body-sec{position:relative;border-left:3px solid transparent;}
.body-sec.drag-src{opacity:.4;}
.body-sec.drag-over-top{border-top:2px solid #2563eb;}
.body-sec.drag-over-bot{border-bottom:2px solid #2563eb;}
.sec-hndl{
    display:flex;align-items:center;gap:7px;
    padding:4px 14px 3px;background:#f8f8f8;border-bottom:1px solid #ececec;
    cursor:grab;opacity:0;transition:opacity .12s;
    font-size:9.5px;color:#999;font-weight:600;letter-spacing:.3px;text-transform:uppercase;
}
.body-sec:hover>.sec-hndl,.sec-hndl:hover{opacity:1!important;background:#eef2ff;color:#2563eb;}
.sec-hndl .dots{display:grid;grid-template-columns:repeat(3,4px);gap:2.5px;}
.sec-hndl .dots span{width:3px;height:3px;background:currentColor;border-radius:50%;display:block;}

/* ═══ WYSIWYG PREVIEW — mirrors sims/invoice.blade.php exactly ═══
   Same class names + rules as the real invoice, scoped to the paper so they
   can't leak into the admin chrome. Colours come from CSS vars set in JS. */
.inv-paper{color:#1a1a1a;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.45;}
.inv-paper .inv-header-tbl{width:100%;border-collapse:collapse;margin-bottom:16px;}
.inv-paper .inv-header-tbl td{vertical-align:middle;}
.inv-paper .inv-right-head{text-align:right;}
.inv-paper .inv-issue-date{margin-bottom:16px;}
.inv-paper .inv-brand-logo{height:60px;width:auto;max-width:220px;object-fit:contain;display:block;}
.inv-paper .inv-title{font-size:24px;font-weight:bold;color:var(--inv-accent);letter-spacing:2px;display:block;}
.inv-paper .inv-contact-small{font-size:10px;color:#888;margin-top:3px;line-height:1.7;}
.inv-paper .inv-issue-date{text-align:right;font-weight:bold;font-size:12px;letter-spacing:.4px;text-transform:uppercase;color:#1a1a1a;}
.inv-paper .inv-green-bar{background:var(--inv-accent);height:26px;width:100%;}
.inv-paper .inv-info-tbl{width:100%;border-collapse:collapse;border:1px solid #d0d0d0;}
.inv-paper .inv-info-tbl td{width:50%;vertical-align:top;}
.inv-paper .inv-info-header{background:var(--inv-accent-bg);padding:10px 20px;font-weight:700;font-size:12.5px;color:var(--inv-accent);text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #d0d0d0;}
.inv-paper .inv-info-header.right{text-align:right;}
.inv-paper .inv-info-body{padding:18px 20px;font-size:13.5px;line-height:1.8;}
.inv-paper .inv-info-right{border-left:1px solid #d0d0d0;}
.inv-paper .inv-info-body-right{padding:18px 20px;text-align:right;font-size:13.5px;line-height:1.9;}
.inv-paper .inv-fulfilment-tbl{width:100%;border-collapse:collapse;border:1px solid #d0d0d0;}
.inv-paper .inv-fulfilment-header{background:var(--inv-accent-bg);padding:10px 20px;font-weight:700;font-size:12.5px;color:var(--inv-accent);text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #d0d0d0;}
.inv-paper .inv-fulfilment-body{padding:18px 20px;font-size:13.5px;line-height:1.8;}
.inv-paper table.items{width:100%;border-collapse:collapse;}
.inv-paper table.items thead th{background:#1f2937;color:#fff;font-size:12px;font-weight:700;text-align:left;padding:11px 14px;letter-spacing:.5px;text-transform:uppercase;border:1px solid #1f2937;}
.inv-paper table.items thead th.num{text-align:right;}
.inv-paper table.items tbody td{padding:11px 14px;border:1px solid #e0e0e0;font-size:13.5px;color:#1a1a1a;}
.inv-paper table.items tbody td.num{text-align:right;}
.inv-paper table.items tbody tr:nth-child(even) td{background:#fafafa;}
.inv-paper .item-install-row td{background:#f7fdf9!important;border-top:none!important;}
.inv-paper .item-sku{font-size:11.5px;color:#888;margin-top:2px;}
.inv-paper .item-install-label{font-size:12.5px;color:var(--inv-accent);padding-left:24px;}
.inv-paper table.inv-bottom{width:100%;border-collapse:collapse;}
.inv-paper table.inv-bottom>tbody>tr>td{vertical-align:top;}
.inv-paper .inv-notes-box{background:var(--inv-accent-fill);padding:18px 20px;font-size:13px;line-height:1.8;}
.inv-paper .inv-notes-box strong{display:block;margin-bottom:8px;font-size:12.5px;color:var(--inv-accent);text-transform:uppercase;letter-spacing:.4px;}
.inv-paper .inv-totals-box{background:var(--inv-accent-fill);padding:18px 20px;font-size:13.5px;}
.inv-paper .inv-totals-tbl{width:100%;border-collapse:collapse;}
.inv-paper .inv-totals-tbl td{padding:4px 0;}
.inv-paper .inv-totals-tbl td.lbl{color:#333;font-weight:600;}
.inv-paper .inv-totals-tbl td.val{text-align:right;color:#1a1a1a;}
.inv-paper .inv-totals-tbl tr.total-row td{border-top:2px solid #1a1a1a;font-weight:700;font-size:15px;padding-top:8px;}
.inv-paper .inv-terms{border:1px solid #d0d0d0;padding:16px 20px;font-size:12px;line-height:1.9;color:#444;}
.inv-paper .inv-terms strong{display:block;margin-bottom:6px;font-size:13px;font-weight:700;color:#1a1a1a;}
.inv-paper .inv-footer{text-align:center;color:#888;font-size:12.5px;}
/* Body sections get a little breathing room between them (matches invoice margins) */
.inv-paper .body-sec + .body-sec{margin-top:16px;}
</style>

{{-- PHP → JS data bridge --}}
<script>
var INV_INIT = @json($invInitData);
</script>

<div class="inv-page">

    {{-- ══ SETTINGS PANEL ══ --}}
    <aside class="inv-settings">
        <div class="inv-s-head">
            <div style="width:28px;height:28px;border-radius:7px;background:var(--al-primary-bg,#f0f7e8);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-file-invoice" style="color:var(--al-primary);font-size:12px;"></i>
            </div>
            <div>
                <h2>Invoice Template</h2>
                <div style="font-size:10px;color:var(--al-text-3);">Drag elements · reorder sections</div>
            </div>
        </div>

        <form class="inv-s-body" method="POST" action="{{ route('admin.invoice.settings.update') }}" id="invForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="inv_layout_pickup"   id="invLayoutPickup">
            <input type="hidden" name="inv_layout_delivery" id="invLayoutDelivery">

            <div class="inv-slbl"><i class="fas fa-palette"></i> Colours</div>
            <div class="inv-field">
                <label class="inv-lbl">Accent Colour</label>
                <div class="inv-cr">
                    <input type="color" class="inv-sw" id="inv_accent_color" name="inv_accent_color"
                           value="{{ old('inv_accent_color', $settings['inv_accent_color'] ?? '#1a7a4a') }}">
                    <input type="text" class="inv-input inv-hex" id="inv_accent_hex"
                           value="{{ old('inv_accent_color', $settings['inv_accent_color'] ?? '#1a7a4a') }}" maxlength="7">
                </div>
            </div>
            <div class="inv-field">
                <label class="inv-lbl">Light Tint</label>
                <div class="inv-cr">
                    <input type="color" class="inv-sw" id="inv_accent_bg" name="inv_accent_bg"
                           value="{{ old('inv_accent_bg', $settings['inv_accent_bg'] ?? '#e8f5ee') }}">
                    <input type="text" class="inv-input inv-hex" id="inv_accent_bg_hex"
                           value="{{ old('inv_accent_bg', $settings['inv_accent_bg'] ?? '#e8f5ee') }}" maxlength="7">
                </div>
            </div>

            <div class="inv-div"></div>
            <div class="inv-slbl"><i class="fas fa-file-contract"></i> Terms &amp; Conditions</div>
            <div class="inv-field">
                <textarea class="inv-ta" name="inv_terms" id="inv_terms" rows="8">{{ old('inv_terms', ($settings['inv_terms'] ?? '') ?: $defaultTerms) }}</textarea>
                <div style="font-size:10.5px;color:var(--al-text-3);margin-top:5px;">Shown in the invoice's Terms section. Edit to customise; clear to restore the default.</div>
            </div>

            <div class="inv-div"></div>
            <div class="inv-slbl"><i class="fas fa-comment-dots"></i> Footer</div>
            <div class="inv-field">
                <textarea class="inv-ta" name="inv_footer" id="inv_footer" rows="3"
                    maxlength="500">{{ old('inv_footer', ($settings['inv_footer'] ?? '') ?: $defaultFooter) }}</textarea>
                <div style="font-size:10.5px;color:var(--al-text-3);margin-top:5px;">Centred line at the bottom of the invoice.</div>
            </div>
        </form>

        <div class="inv-s-foot">
            <button type="submit" form="invForm" class="inv-save" id="saveBtn">
                <i class="fas fa-save"></i> Save Template
            </button>
            @if(session('success'))
            <div style="margin-top:8px;padding:6px 10px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;font-size:11.5px;color:#15803d;display:flex;align-items:center;gap:6px;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif
            @if($errors->any())
            <div style="margin-top:8px;padding:6px 10px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;font-size:11.5px;color:#dc2626;">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
            @endif
        </div>
    </aside>

    {{-- ══ CANVAS AREA ══ --}}
    <div class="inv-canvas-area">
        <div class="inv-toolbar">
            <div class="inv-toolbar-info">
                <i class="fas fa-grip-vertical"></i> Grab a section bar and drag to reorder &nbsp;·&nbsp;
                <i class="fas fa-layer-group"></i> Pickup &amp; Delivery each save their own order
            </div>
            <button type="button" class="inv-tb-btn active" id="tabPickup"   onclick="invSwitchTab('pickup')">
                <i class="fas fa-store"  style="font-size:10px;margin-right:4px;"></i>Pickup
            </button>
            <button type="button" class="inv-tb-btn"        id="tabDelivery" onclick="invSwitchTab('delivery')">
                <i class="fas fa-truck"  style="font-size:10px;margin-right:4px;"></i>Delivery
            </button>
            <button type="button" class="inv-tb-btn" onclick="invResetLayout()" style="margin-left:4px;">
                <i class="fas fa-undo"   style="font-size:10px;margin-right:4px;"></i>Reset
            </button>
        </div>

        <div class="inv-paper" id="invPaper">
            {{-- Header — the exact same shared partial the real invoice uses --}}
            @include('sims.partials.invoice-header', ['invHeaderNote' => $settings['inv_header_note'] ?? ''])
            {{-- Body sections (drag to reorder) --}}
            <div id="bodySections"></div>
        </div>
    </div>
</div>

<script>
(function() {

var tabMode = 'pickup';
var bodyOrder = [];

// One section order per fulfilment mode. bodyOrder is the working copy for the
// mode currently being edited; saveActive()/loadActive() sync it with LAYOUTS.
var LAYOUTS = { pickup: null, delivery: null };
function saveActive() { LAYOUTS[tabMode] = { body: bodyOrder.slice() }; }
function loadActive() { bodyOrder = LAYOUTS[tabMode].body.slice(); }

var SECTION_META = {
    bill_to:     { label:'Bill To + Invoice Details',  icon:'fas fa-user' },
    fulfillment: { label:'Collection / Delivery',      icon:'fas fa-map-marker-alt' },
    items:       { label:'Items Table',                icon:'fas fa-list' },
    notes_totals:{ label:'Order Notes + Totals',       icon:'fas fa-calculator' },
    bank:        { label:'Bank Details',               icon:'fas fa-university' },
    terms:       { label:'Terms & Conditions',         icon:'fas fa-file-contract' },
    footer:      { label:'Footer',                     icon:'fas fa-comment-dots' },
};

var DEFAULT_BODY = ['bill_to','fulfillment','items','notes_totals','bank','terms','footer'];

var STORE = {
    name:  INV_INIT.storeName  || 'Albertina Nigeria',
    addr:  INV_INIT.storeAddr  || 'No. 22 Zik Avenue, Uwani, Enugu',
    email: INV_INIT.storeEmail || 'support@albertinang.com',
    phone: INV_INIT.storePhone || '',
};

var SAMPLE = {
    pickup: {
        customer:'Emeka Okafor', email:'emeka@email.com', phone:'+234 803 456 7890',
        orderNo:'ALB-00123', date:'{{ now()->format("d M Y") }}',
        ref:'ps_ab3f290', payment:'Paystack',
        point:'Garki 2 Collection Point', pointAddr:'12 Moshood Abiola Way, Garki 2, Abuja',
        items:[
            {name:'LG 1.5HP Split Air Conditioner',sku:'LG-AC-15HP',qty:1,price:285000,inst:null},
            {name:'Haier Thermocool 320L Fridge',   sku:'HTC-320L',  qty:1,price:175000,inst:{name:'AC Installation',price:18000}},
        ],
        delivery:0, discount:15000,
    },
    delivery: {
        customer:'Adaeze Nwosu', email:'adaeze@email.com', phone:'+234 806 789 0123',
        orderNo:'ALB-00124', date:'{{ now()->format("d M Y") }}',
        ref:'ps_cd9e112', payment:'Paystack',
        state:'Lagos State', area:'Lekki Phase 1',
        items:[
            {name:'Samsung 55" QLED Smart TV 4K', sku:'SAM-TV-55Q', qty:1,price:620000,inst:null},
            {name:'Scanfrost 1.5PK Standing AC',  sku:'SF-AC-15PK', qty:2,price:198000,inst:null},
        ],
        delivery:8500, discount:0,
    },
};

/* ── Helpers ── */
function esc(t){ return String(t||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function nl2br(t){ return esc(t).replace(/\n/g,'<br>'); }
function fmt(n){ return '₦'+Number(n).toLocaleString('en-NG',{minimumFractionDigits:2,maximumFractionDigits:2}); }
function getS() {
    return {
        accent:      document.getElementById('inv_accent_color').value||'#1a7a4a',
        accentBg:    document.getElementById('inv_accent_bg').value||'#e8f5ee',
        bankDetails: '',
        terms:       document.getElementById('inv_terms').value||'',
        footer:      document.getElementById('inv_footer').value||'',
    };
}

/* ── Render body section HTML — identical markup/classes to the real invoice ── */
function buildSec(secKey, s, ord) {
    if (secKey==='bill_to') {
        return '<table class="inv-info-tbl">'
            +'<tr><td><div class="inv-info-header">Bill To</div></td>'
            +'<td class="inv-info-right"><div class="inv-info-header right">Invoice Details</div></td></tr>'
            +'<tr><td><div class="inv-info-body"><strong>'+esc(ord.customer)+'</strong><br>'
            +esc(ord.email)+'<br>'+esc(ord.phone)+'</div></td>'
            +'<td class="inv-info-right"><div class="inv-info-body-right">'
            +'<span style="color:#555;">Order No.:</span> '+esc(ord.orderNo)+'<br>'
            +'<span style="color:#555;">Currency:</span> NGN (₦)<br>'
            +'<span style="color:#555;">Payment:</span> '+esc(ord.payment)+'<br>'
            +'<span style="color:#555;">Reference:</span> '+esc(ord.ref)+'</div></td></tr></table>';
    }
    if (secKey==='fulfillment') {
        if (tabMode==='pickup') {
            return '<table class="inv-fulfilment-tbl"><tr><td>'
                +'<div class="inv-fulfilment-header">Collection From</div>'
                +'<div class="inv-fulfilment-body"><strong>'+esc(SAMPLE.pickup.point)+'</strong><br>'
                +esc(SAMPLE.pickup.pointAddr)+'<br>'+esc(STORE.email)+'</div></td></tr></table>';
        }
        return '<table class="inv-fulfilment-tbl"><tr><td>'
            +'<div class="inv-fulfilment-header">Delivery To</div>'
            +'<div class="inv-fulfilment-body"><strong>'+esc(ord.customer)+'</strong><br>'
            +(ord.area?esc(ord.area)+', ':'')+esc(ord.state||'')+'<br>Nigeria<br>'+esc(ord.email)
            +(ord.phone?'<br>'+esc(ord.phone):'')+'</div></td></tr></table>';
    }
    if (secKey==='items') {
        var rows='';
        ord.items.forEach(function(item,i){
            rows+='<tr><td>'+(i+1)+'</td>'
                +'<td>'+esc(item.name)+(item.sku?'<div class="item-sku">SKU: '+esc(item.sku)+'</div>':'')+'</td>'
                +'<td class="num">'+item.qty+'</td>'
                +'<td class="num">'+fmt(item.price)+'</td>'
                +'<td class="num">'+fmt(item.price*item.qty)+'</td></tr>';
            if (item.inst) {
                rows+='<tr class="item-install-row"><td></td>'
                    +'<td class="item-install-label">&#8627; Installation: '+esc(item.inst.name)+'</td>'
                    +'<td class="num" style="font-size:12.5px;color:#555;">'+item.qty+'</td>'
                    +'<td class="num" style="font-size:12.5px;color:#555;">'+fmt(item.inst.price)+'</td>'
                    +'<td class="num" style="font-size:12.5px;color:#555;">'+fmt(item.inst.price)+'</td></tr>';
            }
        });
        return '<table class="items"><thead><tr>'
            +'<th style="width:4%">#</th><th style="width:52%">Item Description</th>'
            +'<th class="num" style="width:8%">Qty</th><th class="num" style="width:18%">Unit Price</th>'
            +'<th class="num" style="width:18%">Amount</th></tr></thead><tbody>'+rows+'</tbody></table>';
    }
    if (secKey==='notes_totals') {
        var sub  = ord.items.reduce(function(x,i){ return x+i.price*i.qty; },0);
        var inst = ord.items.reduce(function(x,i){ return x+(i.inst?i.inst.price:0); },0);
        var tot  = sub+inst+(ord.delivery||0)-(ord.discount||0);
        var notes = 'Fulfilment: '+(tabMode==='pickup'?'Customer collection':'Home delivery')+'<br>'
            +(tabMode!=='pickup'&&ord.state?'Delivery to: '+(ord.area?esc(ord.area)+', ':'')+esc(ord.state)+'<br>':'')
            +'Order reference: '+esc(ord.orderNo)+'<br>Customer support: '+esc(STORE.email);
        var totals = '<tr><td class="lbl">Items Subtotal</td><td class="val">'+fmt(sub)+'</td></tr>';
        if (inst>0)         totals+='<tr><td class="lbl">Installation</td><td class="val">+'+fmt(inst)+'</td></tr>';
        if (ord.delivery>0) totals+='<tr><td class="lbl">Delivery Fee</td><td class="val">+'+fmt(ord.delivery)+'</td></tr>';
        if (ord.discount>0) totals+='<tr><td class="lbl">Discount</td><td class="val">-'+fmt(ord.discount)+'</td></tr>';
        totals+='<tr class="total-row"><td class="lbl">Total Due</td><td class="val">'+fmt(tot)+'</td></tr>';
        return '<table class="inv-bottom"><tr>'
            +'<td style="width:55%;padding-right:16px;"><div class="inv-notes-box"><strong>Order Notes</strong>'+notes+'</div></td>'
            +'<td style="width:45%;"><div class="inv-totals-box"><table class="inv-totals-tbl">'+totals+'</table></div></td>'
            +'</tr></table>';
    }
    if (secKey==='bank') {
        if (!s.bankDetails.trim())
            return '<div class="inv-terms" style="color:#bbb;font-style:italic;">Bank details empty — this section is hidden on the real invoice</div>';
        return '<div class="inv-terms"><strong>Bank Details</strong>'+nl2br(s.bankDetails)+'</div>';
    }
    if (secKey==='terms') {
        var t = s.terms || ('1. This invoice is evidence of your order and payment record. Please retain it for your records.\n'
            +'2. Goods may be returned or exchanged only in accordance with the '+STORE.name+' Returns Policy; eligibility depends on product condition and category.\n'
            +'3. Report delivery issues or damaged goods within 48 hours of receipt via '+STORE.email+', quoting your order number.\n'
            +'4. Prices are in Nigerian Naira (₦).\n'
            +'5. This invoice is generated electronically and requires no signature.');
        return '<div class="inv-terms"><strong>Terms &amp; Conditions</strong>'+nl2br(t)+'</div>';
    }
    if (secKey==='footer') {
        var f = s.footer || ('Thank you for shopping with '+STORE.name+'. | '+STORE.email);
        return '<div class="inv-footer">'+nl2br(f)+'</div>';
    }
    return '';
}

function renderBody() {
    var s   = getS();
    var ord = tabMode==='pickup' ? SAMPLE.pickup : SAMPLE.delivery;
    var ctr = document.getElementById('bodySections');
    ctr.innerHTML = '';
    bodyOrder.forEach(function(key) {
        var meta = SECTION_META[key]; if (!meta) return;
        var div = document.createElement('div');
        div.className = 'body-sec'; div.draggable = true; div.dataset.sec = key;
        div.innerHTML = '<div class="sec-hndl" title="Drag to reorder">'
            +'<div class="dots"><span></span><span></span><span></span><span></span><span></span><span></span></div>'
            +'<i class="'+meta.icon+'" style="font-size:10px;"></i>'
            +'<span>'+meta.label+'</span>'
            +'<span style="margin-left:auto;font-size:9px;opacity:.45;font-style:italic;">drag to reorder</span>'
            +'</div>'
            +'<div>'+buildSec(key,s,ord)+'</div>';
        ctr.appendChild(div);
    });
    initBodySort();
}

function renderAll() {
    var s = getS();
    // Drive the preview via the same CSS custom properties the real invoice uses,
    // so a colour change updates the header (INVOICE title + green bar) and every
    // section at once — exactly like production.
    var paper = document.getElementById('invPaper');
    paper.style.setProperty('--inv-accent',      s.accent);
    paper.style.setProperty('--inv-accent-bg',   s.accentBg);
    paper.style.setProperty('--inv-accent-fill', s.accentBg);
    renderBody();
}

/* ── Serialize BOTH layouts to their hidden inputs ── */
function serialize() {
    saveActive(); // fold the current edits into LAYOUTS[tabMode] first
    document.getElementById('invLayoutPickup').value   = JSON.stringify(LAYOUTS.pickup);
    document.getElementById('invLayoutDelivery').value = JSON.stringify(LAYOUTS.delivery);
}

/* ── Body sort ── */
var dragSrc = null;
function initBodySort() {
    document.querySelectorAll('.body-sec').forEach(function(sec) {
        sec.addEventListener('dragstart', function(e) {
            dragSrc=sec; sec.classList.add('drag-src');
            e.dataTransfer.effectAllowed='move'; e.dataTransfer.setData('text/plain',sec.dataset.sec);
        });
        sec.addEventListener('dragend', function() {
            dragSrc=null; sec.classList.remove('drag-src');
            document.querySelectorAll('.body-sec').forEach(function(s){
                s.classList.remove('drag-over-top','drag-over-bot');
            });
        });
        sec.addEventListener('dragover', function(e) {
            e.preventDefault(); if (!dragSrc||dragSrc===sec) return;
            document.querySelectorAll('.body-sec').forEach(function(s){s.classList.remove('drag-over-top','drag-over-bot');});
            var mid = sec.getBoundingClientRect().top + sec.offsetHeight/2;
            sec.classList.add(e.clientY<mid?'drag-over-top':'drag-over-bot');
        });
        sec.addEventListener('dragleave', function() {
            sec.classList.remove('drag-over-top','drag-over-bot');
        });
        sec.addEventListener('drop', function(e) {
            e.preventDefault(); if (!dragSrc||dragSrc===sec) return;
            sec.classList.remove('drag-over-top','drag-over-bot');
            var mid = sec.getBoundingClientRect().top + sec.offsetHeight/2;
            sec.parentNode.insertBefore(dragSrc, e.clientY<mid ? sec : sec.nextSibling);
            bodyOrder=[];
            document.querySelectorAll('.body-sec').forEach(function(s){bodyOrder.push(s.dataset.sec);});
            serialize();
        });
    });
}

/* ── Tab + Reset ── */
window.invSwitchTab = function(mode) {
    if (mode === tabMode) return;
    saveActive();                 // persist the order of the mode we're leaving
    tabMode = mode;
    document.getElementById('tabPickup').classList.toggle('active',   mode==='pickup');
    document.getElementById('tabDelivery').classList.toggle('active', mode==='delivery');
    loadActive();                 // pull the target mode's own order
    renderAll(); serialize();
};
window.invResetLayout = function() {
    // Reset only the mode currently being edited.
    bodyOrder = DEFAULT_BODY.slice();
    renderAll(); serialize();
};

/* ── Settings change ── */
document.getElementById('inv_accent_color').addEventListener('input', function() {
    document.getElementById('inv_accent_hex').value=this.value; renderAll();
});
document.getElementById('inv_accent_bg').addEventListener('input', function() {
    document.getElementById('inv_accent_bg_hex').value=this.value; renderAll();
});
document.getElementById('inv_accent_hex').addEventListener('input', function() {
    if (/^#[0-9a-fA-F]{6}$/.test(this.value)){ document.getElementById('inv_accent_color').value=this.value; renderAll(); }
});
document.getElementById('inv_accent_bg_hex').addEventListener('input', function() {
    if (/^#[0-9a-fA-F]{6}$/.test(this.value)){ document.getElementById('inv_accent_bg').value=this.value; renderAll(); }
});
['inv_terms','inv_footer'].forEach(function(id){
    var el=document.getElementById(id); if(el) el.addEventListener('input', renderAll);
});

document.getElementById('saveBtn').addEventListener('click', serialize);

/* ── Init ── */
LAYOUTS.pickup   = { body: ((INV_INIT.pickup   && INV_INIT.pickup.body)   || DEFAULT_BODY).slice() };
LAYOUTS.delivery = { body: ((INV_INIT.delivery && INV_INIT.delivery.body) || DEFAULT_BODY).slice() };
tabMode = 'pickup';
loadActive();
renderAll();
serialize();

})();
</script>
@endsection
