@extends('layouts.adminlayout')

@section('title', 'Transaction · ' . $paystackTransaction->reference)

@section('content')
<div class="content-header">
    <div class="content-header-left">
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('admin.paystack-transactions.index') }}"
               style="display:inline-flex;align-items:center;gap:6px;color:var(--al-text-3);font-size:13px;text-decoration:none;">
                <i class="fas fa-arrow-left" style="font-size:11px;"></i> Paystack Transactions
            </a>
            <span style="color:var(--al-border);font-size:14px;">/</span>
            <h2 class="content-header-title" style="margin:0;font-size:15px;">{{ $paystackTransaction->reference }}</h2>
            @if($paystackTransaction->status === 'success')
                <span style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;">✓ Success</span>
            @elseif($paystackTransaction->status === 'failed')
                <span style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;">✗ Failed</span>
            @else
                <span style="background:#fefce8;color:#ca8a04;border:1px solid #fef08a;border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;">{{ ucfirst($paystackTransaction->status) }}</span>
            @endif
        </div>
    </div>
</div>

<div class="content-body">

    {{-- ── Top cards ─────────────────────────────────────────────────────── --}}
    <div style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:22px;">

        <div style="background:#fff;border:1px solid var(--al-border);border-radius:12px;padding:18px 22px;min-width:160px;flex:1;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--al-text-3);margin-bottom:6px;">Amount Charged</div>
            <div style="font-size:26px;font-weight:800;color:var(--al-text);">₦{{ number_format($paystackTransaction->amount_kobo / 100, 2) }}</div>
        </div>

        <div style="background:#fff;border:1px solid var(--al-border);border-radius:12px;padding:18px 22px;min-width:160px;flex:1;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--al-text-3);margin-bottom:6px;">Paystack ID</div>
            <div style="font-size:14px;font-family:monospace;font-weight:700;color:var(--al-text);word-break:break-all;">{{ $paystackTransaction->paystack_id ?? '—' }}</div>
        </div>

        <div style="background:#fff;border:1px solid var(--al-border);border-radius:12px;padding:18px 22px;min-width:160px;flex:1;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--al-text-3);margin-bottom:6px;">Received At</div>
            <div style="font-size:14px;font-weight:700;color:var(--al-text);">{{ $paystackTransaction->created_at->format('d M Y') }}</div>
            <div style="font-size:12px;color:var(--al-text-3);">{{ $paystackTransaction->created_at->format('g:i A') }}</div>
        </div>

        <div style="background:#fff;border:1px solid var(--al-border);border-radius:12px;padding:18px 22px;min-width:160px;flex:1;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--al-text-3);margin-bottom:6px;">Order</div>
            @if($paystackTransaction->order)
                <a href="{{ route('admin.orders.show', $paystackTransaction->order) }}"
                   style="font-size:15px;font-weight:800;color:var(--al-primary);text-decoration:none;">
                    {{ $paystackTransaction->order->order_number }}
                </a>
                <div style="font-size:11px;color:var(--al-text-3);margin-top:3px;">{{ ucfirst($paystackTransaction->order->status) }}</div>
            @else
                <div style="font-size:13px;font-weight:700;color:#d97706;">⚠ No order created</div>
                <div style="font-size:11px;color:var(--al-text-3);margin-top:3px;">Payment received but fulfillment failed — investigate</div>
            @endif
        </div>

    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;align-items:start;">

        {{-- ── Order items (if linked) ─────────────────────────────────────── --}}
        <div style="background:#fff;border:1px solid var(--al-border);border-radius:12px;overflow:hidden;">
            <div style="padding:14px 18px;border-bottom:1px solid var(--al-border-2);display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:13px;font-weight:700;color:var(--al-text);">Order Items</span>
            </div>
            @if($paystackTransaction->order && $paystackTransaction->order->items->count())
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr style="background:var(--al-bg-2);border-bottom:1px solid var(--al-border);">
                            <th style="padding:9px 14px;text-align:left;font-weight:700;color:var(--al-text-2);">Item</th>
                            <th style="padding:9px 14px;text-align:center;font-weight:700;color:var(--al-text-2);">Qty</th>
                            <th style="padding:9px 14px;text-align:right;font-weight:700;color:var(--al-text-2);">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paystackTransaction->order->items as $item)
                        <tr style="border-bottom:1px solid var(--al-border-2);">
                            <td style="padding:10px 14px;">
                                <div style="font-weight:600;color:var(--al-text);">{{ $item->name }}</div>
                                @if($item->sku)<div style="font-size:11px;color:var(--al-text-3);">SKU: {{ $item->sku }}</div>@endif
                            </td>
                            <td style="padding:10px 14px;text-align:center;color:var(--al-text-2);">{{ $item->quantity }}</td>
                            <td style="padding:10px 14px;text-align:right;font-weight:700;color:var(--al-text);white-space:nowrap;">
                                ₦{{ number_format($item->price, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:var(--al-bg-2);border-top:2px solid var(--al-border);">
                            <td colspan="2" style="padding:10px 14px;font-weight:700;color:var(--al-text);">Order Total</td>
                            <td style="padding:10px 14px;text-align:right;font-weight:800;color:var(--al-text);white-space:nowrap;">
                                ₦{{ number_format($paystackTransaction->order->total, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <div style="padding:32px;text-align:center;color:var(--al-text-3);">
                    <i class="fas fa-box-open" style="font-size:28px;opacity:.3;display:block;margin-bottom:10px;"></i>
                    {{ $paystackTransaction->order ? 'No items on this order.' : 'No order linked to this transaction.' }}
                </div>
            @endif
        </div>

        {{-- ── Raw Paystack payload ─────────────────────────────────────────── --}}
        <div style="background:#fff;border:1px solid var(--al-border);border-radius:12px;overflow:hidden;">
            <div style="padding:14px 18px;border-bottom:1px solid var(--al-border-2);">
                <span style="font-size:13px;font-weight:700;color:var(--al-text);">Raw Paystack Payload</span>
            </div>
            <div style="padding:14px;">
                @if($paystackTransaction->payload)
                    <pre style="margin:0;font-size:11.5px;color:#374151;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;overflow:auto;max-height:520px;white-space:pre-wrap;word-break:break-all;line-height:1.6;">{{ json_encode($paystackTransaction->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <div style="padding:32px;text-align:center;color:var(--al-text-3);">No payload recorded.</div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
