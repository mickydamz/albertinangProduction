# Invoice Template — Work Notes / Resume Point

_Last updated: 2026-09-26_

Snapshot of the invoice template work so we can pick up from here.

---

## 1. Section reordering + saving — VERIFIED WORKING

The admin invoice-template editor lets you drag body sections into any order,
saved independently per fulfilment mode (Pickup vs Delivery).

**Flow (confirmed end-to-end):**
1. Drag a section in the editor → JS updates the order and writes JSON
   (`{body:[...]}`) into the hidden inputs `inv_layout_pickup` /
   `inv_layout_delivery`.
2. Click **Save Template** → form submits (PUT → `admin.invoice.settings.update`).
3. `AdminInvoiceSettingsController@update` validates both layout fields, persists
   with `Setting::set(...)`, and calls `Setting::clearCache()`.
4. On invoice render, `OrderController` calls
   `AdminInvoiceSettingsController::templateVars($order->fulfillment_method)` →
   `resolveLayout($mode)` reads the matching key, `normaliseLayout()` backfills
   any missing/invalid section.
5. The invoice view renders sections in the saved order:
   `$bodyOrder = ($invLayout['body'] ?? null) ?: $defaultBody; @foreach(...)`.

**Key files:**
- `app/Http/Controllers/AdminInvoiceSettingsController.php`
- `resources/views/admin/invoice-settings/index.blade.php` (editor + JS)
- `resources/views/sims/invoice.blade.php` (customer view)
- `resources/views/sims/invoice-pdf.blade.php` (PDF)
- Routes: `routes/web.php` lines ~489–491
  (`invoice.settings`, `invoice.settings.update`, `invoice.settings.layout`)

**Caveat / possible follow-up:**
- Persistence happens **only on the Save button**. Dragging alone does NOT
  autosave. An AJAX endpoint `updateLayout()` (route `invoice.settings.layout`)
  exists but is **not wired** to the drag handler. If we want drag-to-autosave,
  POST to that endpoint from the drop handler in the editor JS.

---

## 2. Mobile responsiveness — FIXED (not yet committed)

**Problem:** the customer invoice (`sims/invoice.blade.php`) had a viewport tag
but no mobile breakpoints — fixed multi-column tables cramped/overflowed on
phones.

**Fix applied (scale-to-fit, keeps exact desktop layout, just smaller):**
- Invoice renders at its fixed **760px design width**, then a script applies
  `transform: scale(viewport ÷ 760)` from the top-left on screens ≤767px, so the
  whole thing shrinks proportionally — no reflow, no horizontal scroll.
- A negative `margin-bottom` collapses the empty space the transform leaves.
- Re-fits on `resize`, `orientationchange`, and after web fonts load.
- **Print / PDF untouched:** the `@media print` block forces
  `transform: none !important; width: auto !important;`, and the DomPDF template
  is a separate file.

**Where the change lives:** `resources/views/sims/invoice.blade.php`
- `@media print` block — added transform/width reset
- new `@media (max-width: 767px)` block — fixes design width + transform-origin
- `@push('scripts')` — the `fitInvoice()` scaler

**Trade-off:** on small phones the invoice is genuinely small (as requested);
users can pinch-zoom. Alternative would be a reflow/stack layout (stack the two
info columns, wrap items table in horizontal scroll) — not done.

---

## Status / next steps
- [ ] Commit the mobile shrink change (as `mickydamz`, no co-author line).
- [ ] Optional: screenshot invoice at a phone width to confirm.
- [ ] Optional: wire drag-to-autosave via the `updateLayout` endpoint.
