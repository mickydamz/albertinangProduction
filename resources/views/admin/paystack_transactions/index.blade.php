@extends('layouts.adminlayout')

@section('title', 'Paystack Transactions')

@section('content')
<div class="content-header">
    <div class="content-header-left">
        <h2 class="content-header-title">Paystack Transactions</h2>
        <p style="font-size:13px;color:var(--al-text-3);margin-top:2px;">
            Raw transaction log from Paystack — every verified payment.
        </p>
    </div>
</div>

<div class="content-body">

    {{-- ── Stats strip ── --}}
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
        <div style="background:#fff;border:1px solid var(--al-border);border-radius:10px;padding:14px 20px;min-width:130px;flex:1;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--al-text-3);margin-bottom:4px;">Total</div>
            <div style="font-size:22px;font-weight:800;color:var(--al-text);">{{ number_format($totalCount) }}</div>
        </div>
        <div style="background:#fff;border:1px solid var(--al-border);border-radius:10px;padding:14px 20px;min-width:130px;flex:1;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#15803d;margin-bottom:4px;">Linked to Order</div>
            <div style="font-size:22px;font-weight:800;color:#15803d;">{{ number_format($linkedCount) }}</div>
        </div>
        <div style="background:#fff;border:1px solid #fde68a;border-radius:10px;padding:14px 20px;min-width:130px;flex:1;{{ $orphanCount > 0 ? 'background:#fffbeb;' : '' }}">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:{{ $orphanCount > 0 ? '#d97706' : 'var(--al-text-3)' }};margin-bottom:4px;">
                @if($orphanCount > 0)⚠ @endif No Order
            </div>
            <div style="font-size:22px;font-weight:800;color:{{ $orphanCount > 0 ? '#d97706' : 'var(--al-text)' }};">{{ number_format($orphanCount) }}</div>
        </div>
    </div>

    {{-- ── Filters ── --}}
    <form method="GET" action="{{ route('admin.paystack-transactions.index') }}"
          style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;align-items:flex-end;">
        <div style="flex:1;min-width:200px;">
            <label style="font-size:12px;font-weight:600;color:var(--al-text-3);display:block;margin-bottom:4px;">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Reference, Paystack ID…"
                   class="form-control" style="width:100%;">
        </div>
        <div>
            <label style="font-size:12px;font-weight:600;color:var(--al-text-3);display:block;margin-bottom:4px;">Status</label>
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                <option value="failed"  {{ request('status') === 'failed'  ? 'selected' : '' }}>Failed</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
        </div>
        <div>
            <label style="font-size:12px;font-weight:600;color:var(--al-text-3);display:block;margin-bottom:4px;">Order Link</label>
            <select name="linked" class="form-select">
                <option value="">All</option>
                <option value="1" {{ request('linked') === '1' ? 'selected' : '' }}>Has Order</option>
                <option value="0" {{ request('linked') === '0' ? 'selected' : '' }}>No Order ⚠</option>
            </select>
        </div>
        <div style="display:flex;gap:8px;">
            <button type="submit"
                    style="padding:8px 18px;background:var(--al-primary);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                Filter
            </button>
            @if(request()->hasAny(['search','status','linked']))
            <a href="{{ route('admin.paystack-transactions.index') }}"
               style="padding:8px 14px;background:#f3f4f6;color:var(--al-text-2);border-radius:8px;font-size:13px;text-decoration:none;display:flex;align-items:center;">
                Clear
            </a>
            @endif
        </div>
    </form>

    {{-- ── Table ── --}}
    <div style="background:#fff;border:1px solid var(--al-border);border-radius:12px;overflow:hidden;">
        <div style="overflow-x:auto;">
            <table id="paystackTxnTable" style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:var(--al-bg-2);border-bottom:1px solid var(--al-border);">
                        <th style="padding:11px 14px;text-align:left;font-weight:700;color:var(--al-text-2);white-space:nowrap;">Reference</th>
                        <th style="padding:11px 14px;text-align:left;font-weight:700;color:var(--al-text-2);">Paystack ID</th>
                        <th style="padding:11px 14px;text-align:right;font-weight:700;color:var(--al-text-2);">Amount</th>
                        <th style="padding:11px 14px;text-align:center;font-weight:700;color:var(--al-text-2);">Status</th>
                        <th style="padding:11px 14px;text-align:center;font-weight:700;color:var(--al-text-2);">Order</th>
                        <th style="padding:11px 14px;text-align:left;font-weight:700;color:var(--al-text-2);">Date</th>
                        <th style="padding:11px 14px;text-align:center;font-weight:700;color:var(--al-text-2);">Payload</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $txn)
                    <tr style="border-bottom:1px solid var(--al-border-2);" class="txn-row">
                        {{-- Reference --}}
                        <td data-label="Reference" style="padding:11px 14px;font-family:monospace;font-size:12px;white-space:nowrap;">
                            <a href="{{ route('admin.paystack-transactions.show', $txn) }}"
                               style="color:var(--al-primary);font-weight:700;text-decoration:none;">
                                {{ $txn->reference }}
                            </a>
                        </td>
                        {{-- Paystack ID --}}
                        <td data-label="Paystack ID" style="padding:11px 14px;font-family:monospace;font-size:12px;color:var(--al-text-3);">
                            {{ $txn->paystack_id ?? '—' }}
                        </td>
                        {{-- Amount --}}
                        <td data-label="Amount" style="padding:11px 14px;text-align:right;font-weight:700;color:var(--al-text);white-space:nowrap;">
                            ₦{{ number_format($txn->amount_kobo / 100, 2) }}
                        </td>
                        {{-- Status badge --}}
                        <td data-label="Status" style="padding:11px 14px;text-align:center;">
                            @if($txn->status === 'success')
                                <span style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;">
                                    ✓ Success
                                </span>
                            @elseif($txn->status === 'failed')
                                <span style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;">
                                    ✗ Failed
                                </span>
                            @else
                                <span style="background:#fefce8;color:#ca8a04;border:1px solid #fef08a;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;">
                                    {{ ucfirst($txn->status) }}
                                </span>
                            @endif
                        </td>
                        {{-- Order link --}}
                        <td data-label="Order" style="padding:11px 14px;text-align:center;">
                            @if($txn->order)
                                <a href="{{ route('admin.orders.show', $txn->order) }}"
                                   style="color:var(--al-primary);font-weight:700;font-size:12px;text-decoration:none;white-space:nowrap;">
                                    {{ $txn->order->order_number }}
                                </a>
                            @else
                                <span style="background:#fffbeb;color:#d97706;border:1px solid #fde68a;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;"
                                      title="Payment received but no order was created — investigate">
                                    ⚠ No Order
                                </span>
                            @endif
                        </td>
                        {{-- Date --}}
                        <td data-label="Date" style="padding:11px 14px;color:var(--al-text-3);white-space:nowrap;font-size:12px;">
                            {{ $txn->created_at->format('d M Y, g:i A') }}
                        </td>
                        {{-- Payload toggle --}}
                        <td data-label="Payload" style="padding:11px 14px;text-align:center;">
                            <button type="button"
                                    onclick="togglePayload(this)"
                                    style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:6px;padding:4px 10px;font-size:11px;cursor:pointer;color:var(--al-text-2);">
                                View JSON
                            </button>
                        </td>
                    </tr>
                    {{-- Payload row (hidden by default) --}}
                    <tr class="payload-row" style="display:none;background:#fafafa;">
                        <td colspan="7" style="padding:12px 14px;">
                            <pre style="margin:0;font-size:11.5px;color:#374151;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:14px;overflow-x:auto;max-height:300px;white-space:pre-wrap;word-break:break-all;">{{ json_encode($txn->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding:48px;text-align:center;color:var(--al-text-3);">
                            <i class="fas fa-credit-card" style="font-size:32px;margin-bottom:12px;display:block;opacity:.3;"></i>
                            No Paystack transactions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
        <div style="padding:14px 16px;border-top:1px solid var(--al-border-2);">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

</div>

<style>
  /* Responsive table — stack rows into cards on mobile (matches Products).
     Scoped to .txn-row so the toggleable JSON payload row is left untouched. */
  @media (max-width: 768px) {
    #paystackTxnTable thead { display: none; }
    #paystackTxnTable tr.txn-row { display: block; margin-bottom: 1rem; border: 1px solid #ebe9f1; border-radius: 6px; }
    #paystackTxnTable tr.txn-row > td {
      display: flex !important;
      justify-content: space-between;
      align-items: center;
      gap: 0.75rem;
      padding: 0.5rem 0.75rem !important;
      border: none;
      border-bottom: 1px solid #f3f2f7;
      text-align: left !important;
      white-space: normal !important;
    }
    #paystackTxnTable tr.txn-row > td:last-child { border-bottom: none; }
    #paystackTxnTable tr.txn-row > td::before {
      content: attr(data-label);
      font-weight: 600;
      color: #b9b9c3;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      flex-shrink: 0;
      padding-right: 0.5rem;
    }
    #paystackTxnTable .payload-row td { padding: 8px !important; }
  }
</style>
@endsection

@push('scripts')
<script>
function togglePayload(btn) {
    var row = btn.closest('tr').nextElementSibling;
    if (row && row.classList.contains('payload-row')) {
        var hidden = row.style.display === 'none';
        row.style.display = hidden ? 'table-row' : 'none';
        btn.textContent   = hidden ? 'Hide JSON' : 'View JSON';
        btn.style.background = hidden ? '#e0f2fe' : '#f3f4f6';
    }
}

document.querySelectorAll('.txn-row').forEach(function(row) {
    row.style.transition = 'background .1s';
    row.addEventListener('mouseenter', function() { row.style.background = 'var(--al-bg-2)'; });
    row.addEventListener('mouseleave', function() { row.style.background = ''; });
});
</script>
@endpush
