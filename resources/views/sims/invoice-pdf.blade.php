<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Invoice {{ $order->order_number }}</title>
<style>
  @page { size: A4 portrait; margin: 0; }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  html, body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 12px;
    line-height: 1.45;
    color: #1a1a1a;
    background: #ffffff;
    padding: 28px 36px;
  }
  table { border-collapse: collapse; width: 100%; }
  td, th { vertical-align: top; }
  .num { text-align: right; }
</style>
</head>
<body>

@php
    $storeName    = $appStoreName ?? 'AlbertinaNG';
    $storeAddr    = $storeAddress ?? 'No. 22 Zik Avenue, Uwani, Enugu State, Nigeria';
    $storeContact = $storeEmail   ?? 'support@albertinang.com';
    $storePhone   = $storePhone   ?? null;

    $isPickup      = $order->fulfillment_method === 'pickup';
    $customerName  = $order->user->name  ?? 'Guest';
    $customerEmail = $order->user->email ?? $order->customer_email ?? '';
    $customerPhone = $order->user?->phone_no ?? null;

    // Base64 logo — DomPDF cannot load external URLs, must embed
    $logoFilePath = !empty($appStoreLogo)
        ? public_path('storage/' . $appStoreLogo)
        : public_path('image.png');

    if (!empty($appStoreLogo) && !file_exists($logoFilePath)) {
        $logoFilePath = public_path('image.png');
    }

    $logoSrc = null;
    if (file_exists($logoFilePath)) {
        $ext     = strtolower(pathinfo($logoFilePath, PATHINFO_EXTENSION));
        $mimeMap = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml'];
        $mime    = $mimeMap[$ext] ?? 'image/png';
        $data    = file_get_contents($logoFilePath);
        if ($data !== false) {
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode($data);
        }
    }

    $installTotal  = $order->items->sum(fn($i) => $i->installation_extra_ngn * $i->quantity);
    $itemsBase     = $order->items->sum(fn($i) => $i->price * $i->quantity);
    $baseOnlyTotal = $itemsBase - $installTotal;
    $discount      = (float) ($order->coupon_discount_ngn ?? 0);
    $deliveryFee   = (float) ($order->shipping_cost ?? 0);
    $grandTotal    = (float) $order->total;

    $invAccent   = $invAccent   ?? '#1a7a4a';
    $invAccentBg = $invAccentBg ?? '#e8f5ee';

    // Admin-configured body section order (per fulfilment mode). The PDF header
    // stays a fixed table — dompdf cannot reliably free-position elements — but
    // the section ORDER below honours the saved layout.
    $defaultBody = ['bill_to','fulfillment','items','notes_totals','bank','terms','footer'];
    $bodyOrder   = ($invLayout['body'] ?? null) ?: $defaultBody;

    $mintBg   = 'background-color:' . $invAccentBg . ';';
    $mintFill = 'background-color:' . $invAccentBg . ';';
    $hdrStyle = $mintBg . 'color:' . $invAccent . ';font-weight:bold;font-size:10px;text-transform:uppercase;letter-spacing:0.5px;padding:7px 14px;border-bottom:1px solid #d0d0d0;';
    $cellP    = 'padding:12px 14px;font-size:11.5px;line-height:1.7;';
    $itemHdr  = 'background-color:#1f2937;color:#ffffff;font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;padding:8px 10px;border:1px solid #1f2937;border-bottom:0;';
    $itemCell = 'padding:7px 10px;border:1px solid #e0e0e0;font-size:11.5px;';
    $instCell = 'background-color:#f7fdf9;border:1px solid #e0e0e0;border-top:0;padding:2px 10px 6px 10px;font-size:10.5px;';
@endphp

{{-- ══════════ HEADER ══════════ --}}
<table style="margin-bottom:16px;">
  <tr>
    <td style="width:55%;vertical-align:middle;">
      @if($logoSrc)
        <img src="{{ $logoSrc }}" style="height:60px;width:auto;max-width:220px;display:block;" alt="{{ $storeName }}">
      @else
        <span style="font-size:22px;font-weight:bold;color:{{ $invAccent }};">{{ $storeName }}</span>
      @endif
    </td>
    <td style="width:45%;vertical-align:middle;text-align:right;">
      <span style="font-size:24px;font-weight:bold;color:{{ $invAccent }};letter-spacing:2px;">INVOICE</span><br>
      <span style="font-size:10px;color:#888;line-height:1.7;">
        {{ $storeName }}@if($storeAddr) | {{ $storeAddr }}@endif<br>
        {{ $storeContact }}@if($storePhone) | {{ $storePhone }}@endif
        @if(!empty($invHeaderNote))<br><em>{{ $invHeaderNote }}</em>@endif
      </span>
    </td>
  </tr>
</table>

{{-- ══════════ ISSUE DATE ══════════ --}}
<p style="text-align:right;font-weight:bold;font-size:12px;text-transform:uppercase;letter-spacing:0.4px;margin-bottom:16px;">
  Issue Date: {{ $order->created_at->format('d F Y') }}
</p>

{{-- ══════════ ACCENT BAR ══════════ --}}
<table style="margin-bottom:0;">
  <tr>
    <td style="background-color:{{ $invAccent }};height:26px;font-size:1px;line-height:26px;">&nbsp;</td>
  </tr>
</table>

{{-- ══════════ BODY SECTIONS — rendered in the admin-configured order ══════════ --}}
@foreach($bodyOrder as $sec)

@if($sec === 'bill_to')
{{-- ══════════ BILL TO / INVOICE DETAILS ══════════ --}}
<table style="border:1px solid #d0d0d0;margin-bottom:0;">
  <tr>
    <td style="width:50%;{{ $hdrStyle }}">Bill To</td>
    <td style="width:50%;{{ $hdrStyle }}text-align:right;border-left:1px solid #d0d0d0;">Invoice Details</td>
  </tr>
  <tr>
    <td style="{{ $cellP }}">
      <strong>{{ $customerName }}</strong><br>
      @if($order->user?->shipping_address){{ $order->user->shipping_address }}<br>@endif
      @if($order->user?->city){{ $order->user->city }}<br>@endif
      @if($order->user?->state){{ $order->user->state }}<br>@endif
      @if($customerEmail)<span style="color:#555;">{{ $customerEmail }}</span><br>@endif
      @if($customerPhone)<span style="color:#555;">{{ $customerPhone }}</span>@endif
    </td>
    <td style="{{ $cellP }}text-align:right;border-left:1px solid #d0d0d0;">
      <span style="color:#555;">Order No.:</span> {{ $order->order_number }}<br>
      <span style="color:#555;">Currency:</span> NGN (&#8358;)<br>
      <span style="color:#555;">Payment:</span> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}<br>
      @if($order->reference)<span style="color:#555;">Reference:</span> {{ $order->reference }}<br>@endif
      @if($order->coupon_code_used)<span style="color:#555;">Coupon:</span> {{ $order->coupon_code_used }}@endif
    </td>
  </tr>
</table>

@elseif($sec === 'fulfillment')
{{-- ══════════ COLLECTION FROM / DELIVERY TO ══════════ --}}
<table style="border:1px solid #d0d0d0;margin-top:10px;margin-bottom:10px;">
  <tr>
    <td style="{{ $hdrStyle }}">{{ $isPickup ? 'Collection From' : 'Delivery To' }}</td>
  </tr>
  <tr>
    <td style="{{ $cellP }}">
      @if($isPickup)
        <strong>{{ $order->pickup_point_name ?: $storeName . ' Collection Point' }}</strong><br>
        @if($order->pickup_point_address){{ $order->pickup_point_address }}<br>@endif
        @if($order->pickup_location){{ $order->pickup_location }}<br>@endif
        {{ $storeContact }}
      @else
        <strong>{{ $customerName }}</strong><br>
        @if($order->shipping_address){{ $order->shipping_address }}<br>@endif
        @if($order->delivery_location_name){{ $order->delivery_location_name }}, @endif
        @if($order->delivery_state_name){{ $order->delivery_state_name }}<br>@endif
        Nigeria<br>
        @if($customerEmail){{ $customerEmail }}@endif
        @if($customerPhone)<br>{{ $customerPhone }}@endif
      @endif
    </td>
  </tr>
</table>

@elseif($sec === 'items')
{{-- ══════════ ITEMS TABLE ══════════ --}}
<table style="margin-top:10px;margin-bottom:10px;">
  <thead>
    <tr>
      <td style="{{ $itemHdr }}width:5%;">#</td>
      <td style="{{ $itemHdr }}width:51%;">Item Description</td>
      <td style="{{ $itemHdr }}width:8%;text-align:right;">Qty</td>
      <td style="{{ $itemHdr }}width:18%;text-align:right;">Unit Price</td>
      <td style="{{ $itemHdr }}width:18%;text-align:right;">Amount</td>
    </tr>
  </thead>
  <tbody>
    @php $rowNum = 0; @endphp
    @foreach($order->items as $item)
      @php
          $rowNum++;
          $basePrice  = $item->price - $item->installation_extra_ngn;
          $baseAmount = $basePrice * $item->quantity;
          $instAmount = $item->installation_extra_ngn * $item->quantity;
      @endphp
      <tr>
        <td style="{{ $itemCell }}">{{ $rowNum }}</td>
        <td style="{{ $itemCell }}">
          {{ $item->name }}
          @if($item->sku)<br><span style="font-size:10.5px;color:#888;">SKU: {{ $item->sku }}</span>@endif
        </td>
        <td style="{{ $itemCell }}text-align:right;">{{ $item->quantity }}</td>
        <td style="{{ $itemCell }}text-align:right;">&#8358;{{ number_format($basePrice, 2) }}</td>
        <td style="{{ $itemCell }}text-align:right;">&#8358;{{ number_format($baseAmount, 2) }}</td>
      </tr>
      @if($item->installation_option && $item->installation_extra_ngn > 0)
        <tr>
          <td style="{{ $instCell }}"></td>
          <td style="{{ $instCell }}padding-left:28px;color:{{ $invAccent }};">+ Installation: {{ $item->installation_option }}</td>
          <td style="{{ $instCell }}text-align:right;color:#555;">{{ $item->quantity }}</td>
          <td style="{{ $instCell }}text-align:right;color:#555;">&#8358;{{ number_format($item->installation_extra_ngn, 2) }}</td>
          <td style="{{ $instCell }}text-align:right;color:#555;">&#8358;{{ number_format($instAmount, 2) }}</td>
        </tr>
      @elseif($item->installation_option)
        <tr>
          <td style="{{ $instCell }}"></td>
          <td style="{{ $instCell }}padding-left:28px;color:{{ $invAccent }};" colspan="4">+ Installation: {{ $item->installation_option }} (included)</td>
        </tr>
      @endif
    @endforeach
  </tbody>
</table>

@elseif($sec === 'notes_totals')
{{-- ══════════ NOTES + TOTALS ══════════ --}}
<table style="margin-top:10px;margin-bottom:10px;">
  <tr>
    <td style="{{ $mintFill }}padding:12px 14px;font-size:11px;line-height:1.75;width:55%;vertical-align:top;">
      <strong style="font-size:10px;color:{{ $invAccent }};text-transform:uppercase;letter-spacing:0.4px;">Order Notes</strong><br><br>
      Fulfilment: {{ $isPickup ? 'Customer collection' : 'Home delivery' }}<br>
      @if(!$isPickup && $order->delivery_state_name)
        Delivery to: {{ $order->delivery_location_name ? $order->delivery_location_name . ', ' : '' }}{{ $order->delivery_state_name }}<br>
      @endif
      Order reference: {{ $order->order_number }}<br>
      @if($order->coupon_code_used)Coupon applied: {{ $order->coupon_code_used }}<br>@endif
      Customer support: {{ $storeContact }}
    </td>
    <td style="width:14px;"></td>
    <td style="{{ $mintFill }}padding:12px 14px;width:43%;vertical-align:top;">
      <table style="width:100%;">
        <tr>
          <td style="color:#333;font-weight:bold;padding:2px 0;font-size:11px;">Items Subtotal</td>
          <td style="text-align:right;color:#1a1a1a;padding:2px 0;font-size:11px;">&#8358;{{ number_format($baseOnlyTotal, 2) }}</td>
        </tr>
        @if($installTotal > 0)
        <tr>
          <td style="color:#333;font-weight:bold;padding:2px 0;font-size:11px;">Installation</td>
          <td style="text-align:right;color:#1a1a1a;padding:2px 0;font-size:11px;">+&#8358;{{ number_format($installTotal, 2) }}</td>
        </tr>
        @endif
        @if($deliveryFee > 0)
        <tr>
          <td style="color:#333;font-weight:bold;padding:2px 0;font-size:11px;">Delivery Fee</td>
          <td style="text-align:right;color:#1a1a1a;padding:2px 0;font-size:11px;">+&#8358;{{ number_format($deliveryFee, 2) }}</td>
        </tr>
        @endif
        @if($discount > 0)
        <tr>
          <td style="color:#333;font-weight:bold;padding:2px 0;font-size:11px;">Discount</td>
          <td style="text-align:right;color:#1a1a1a;padding:2px 0;font-size:11px;">-&#8358;{{ number_format($discount, 2) }}</td>
        </tr>
        @endif
        <tr>
          <td style="color:#1a1a1a;font-weight:bold;font-size:12px;border-top:2px solid #1a1a1a;padding-top:6px;">Total Due</td>
          <td style="text-align:right;color:#1a1a1a;font-weight:bold;font-size:12px;border-top:2px solid #1a1a1a;padding-top:6px;">&#8358;{{ number_format($grandTotal, 2) }}</td>
        </tr>
      </table>
    </td>
  </tr>
</table>

@elseif($sec === 'bank')
{{-- ══════════ BANK DETAILS (optional) ══════════ --}}
@if(!empty($invBankDetails))
<table style="margin-top:12px;margin-bottom:12px;">
  <tr>
    <td style="border:1px solid #d0d0d0;padding:10px 14px;font-size:10.5px;line-height:1.8;color:#444;">
      <strong style="font-size:11px;color:#1a1a1a;">Bank Details</strong><br>
      {!! nl2br(e($invBankDetails)) !!}
    </td>
  </tr>
</table>
@endif

@elseif($sec === 'terms')
{{-- ══════════ TERMS ══════════ --}}
<table style="margin-top:12px;margin-bottom:12px;">
  <tr>
    <td style="border:1px solid #d0d0d0;padding:10px 14px;font-size:10.5px;line-height:1.8;color:#444;">
      <strong style="font-size:11px;color:#1a1a1a;">Terms &amp; Conditions</strong><br>
      @if(!empty($invTerms))
        {!! nl2br(e($invTerms)) !!}
      @else
        1. This invoice is evidence of your order and payment record. Please retain it for your records.<br>
        2. Goods may be returned or exchanged only in accordance with the {{ $storeName }} Returns Policy; eligibility depends on product condition and category.<br>
        3. Report delivery issues or damaged goods within 48 hours of receipt via {{ $storeContact }}, quoting your order number.<br>
        4. Prices are in Nigerian Naira (&#8358;).<br>
        5. This invoice is generated electronically and requires no signature.
      @endif
    </td>
  </tr>
</table>

@elseif($sec === 'footer')
{{-- ══════════ FOOTER ══════════ --}}
<p style="text-align:center;color:#888;font-size:12px;margin-top:8px;">
  @if(!empty($invFooter))
    {!! nl2br(e($invFooter)) !!}
  @else
    Thank you for shopping with {{ $storeName }}. | {{ $storeContact }}
  @endif
</p>
@endif

@endforeach

</body>
</html>
