@extends('emails.layout')

@section('title', 'Leave a Review — AlbertinaNG')

@section('header_title', 'How did we do?')
@section('header_sub', 'Share your thoughts and help other shoppers.')

@section('body')

    <p class="greeting">
        Hello {{ $order->user->name ?? 'there' }},<br><br>
        Thanks again for your order <strong>{{ $order->order_number }}</strong>. If you have a moment, we'd love to hear what you think — tap any item below to leave a review.
    </p>

    <div class="items-title">Review Your Items</div>
    <table class="items-table" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        @foreach($order->items as $item)
            @php
                $pid = $item->product_id ?: optional($item->product())->id;
                $base = rtrim(config('app.url'), '/');
                $reviewUrl = $pid
                    ? $base . '/product/' . $pid . '#reviews'
                    : $base;
            @endphp
            <tr class="item-row">
                {{-- Product thumbnail (links to product reviews) --}}
                <td width="64" valign="top" style="padding:12px 0;">
                    <a href="{{ $reviewUrl }}" target="_blank" style="display:block;">
                        <img src="{{ $item->image_url }}"
                             alt="{{ $item->name }}"
                             width="56" height="56"
                             style="width:56px; height:56px; object-fit:contain; border:1px solid #dcefd0; border-radius:8px; background:#f7faf3; display:block;">
                    </a>
                </td>
                {{-- Name + qty --}}
                <td valign="top" style="padding:12px 0 12px 12px;">
                    <div class="item-name">{{ $item->name }}</div>
                    <div class="item-qty">Qty: {{ $item->quantity }}</div>
                </td>
                {{-- Review button --}}
                {{-- Review button --}}
<td valign="top" style="text-align:right; padding:12px 0;">
    <table cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin-left:auto;">
        <tr>
            <td align="center" bgcolor="#2d7010" style="border-radius:8px;">
                <a href="{{ $reviewUrl }}" target="_blank"
                   style="display:inline-block; padding:9px 18px; font-family:'DM Sans',Arial,sans-serif; font-size:12px; font-weight:700; color:#ffffff; text-decoration:none; border-radius:8px; white-space:nowrap;">
                    Write a Review
                </a>
            </td>
        </tr>
    </table>
</td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <div class="help-box">
        <p>
            Only verified buyers like you can review — your feedback genuinely helps other customers choose with confidence.
        </p>
    </div>

    <p class="greeting" style="margin-bottom:0;">
        Regards,<br>
        <strong>AlbertinaNG</strong>
    </p>

@endsection