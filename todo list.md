# Suggested Code Fixes — To Do

Tracked from the review/checkout/testing session. These are **code fixes**, not
test gaps. Ordered by priority. None of these are done yet.

---

## 0. IDOR on support tickets — ✅ FIXED this session
`TicketController::show / edit / update / reply / deleteImage` now call
`authorizeAccess($ticket)` (owner-or-admin, else 403), closing the hole where any
user could reach another customer's ticket by ID. Covered by `SupportTicketTest`
(non-owner → 403 on view + reply; owner + admin allowed).

Also fixed: `ticket_replies` had no `images` column though the model/controller
write it, so every reply 500'd — migration
`2026_09_27_000001_add_images_to_ticket_replies_table` adds it (run on both DBs).

---

## 0b. Admin "Transactions" create/update is broken (schema mismatch) — LOW
`AdminTransactionController::store/update` write `escrow_amount` and `status`
columns that the `transactions` table doesn't have (it only has `user_id`,
`payment_method_id`, `total_amount`) — so creating a transaction from the admin
panel 500s. Also `payment_method_id` is a required FK. Looks like an unfinished
legacy/escrow feature; the real money flow is the Paystack/Stripe order pipeline.
Either finish it (add columns + default a payment method) or remove the admin
Transactions CRUD. Test intentionally **skipped** (per request).

---

## 1. IDOR / broken ownership check on payment confirmation — HIGH
**Where:** `OrderController::verifyAndStorePaystackOrder` (`POST /paystack/confirm-order`)
and the Stripe twin `OrderController::storeStripeOrder` (`POST /orders/stripe`).

**Problem:** The endpoint fulfils/returns an order from any `reference` the
caller submits, without checking the `PendingCheckout`/`Order` belongs to the
authenticated user. `fulfil()` uses `$pending->user_id ?? $userId` and returns
`order_id` + `order_number` for any valid reference → an authenticated user can
confirm-existence and read another customer's order number.

**Mitigated by:** random ULID references, `throttle:checkout-confirm`, `auth`,
and `PendingCheckout.user_id` always being set at save-time (so no re-assignment
/ takeover). It's info-disclosure, not account takeover.

**Fix sketch (in the controller, before trusting the result — leave the webhook
path, which passes `null`, untouched):**
```php
$pending = \App\Models\PendingCheckout::where('reference', $validated['reference'])->first();
$order   = \App\Models\Order::where('reference', $validated['reference'])->first();
$ownerId = $pending->user_id ?? $order?->user_id;
if ($ownerId !== null && $ownerId !== Auth::id()) {
    return response()->json(['success' => false, 'message' => 'Not found.'], 404);
}
```

---

## 2. Public coupon endpoint crashes for guests — MEDIUM
**Where:** `CouponController::apply` (`POST /api/coupons/validate`, a **public**
route) calls `Coupon::validate(float $subtotalNgn, int $userId)`.

**Problem:** `auth()->id()` is `null` for a guest, but the method's `$userId`
param is a non-nullable `int` → TypeError → HTTP 500. Works today only because
the coupon UI sits behind the authed checkout page.

**Fix options:**
- Add `auth` middleware to the route (coupons are only usable at checkout), OR
- Make the param nullable: `validate(float $subtotalNgn, ?int $userId)` and guard
  the per-user usage check with `if ($userId && $this->usages()->where(...)->exists())`.

---

## 3. `env()` in Blade breaks under `config:cache` — MEDIUM
**Where:** `resources/views/checkout.blade.php` and
`resources/views/WithPaystackcheckout.blade.php` —
`env("PAYSTACK_PUBLIC_KEY")`, `env("STRIPE_PUBLISHABLE_KEY")`.

**Problem:** After `php artisan config:cache` (standard in production), `env()`
returns `null` outside config files → the payment keys silently become empty and
the popup won't open. (The keys themselves are publishable, so exposure is fine —
the issue is reliability.)

**Fix:** move them into `config/services.php` and reference via `config(...)`:
```php
// config/services.php
'paystack' => ['public' => env('PAYSTACK_PUBLIC_KEY'), ...],
'stripe'   => ['publishable' => env('STRIPE_PUBLISHABLE_KEY'), ...],
```
```blade
const paystackPk = '{{ config('services.paystack.public') }}';
const stripePk   = '{{ config('services.stripe.publishable') }}';
```

---

## 4. Remove temp diagnostic logging (PII) — LOW
**Where:** `OrderController::saveCheckout` — the `Log::info('saveCheckout
prepared checkout', [...])` block (marked `TEMP DIAGNOSTIC`).

**Problem:** Logs customer email presence, amounts, and `user_id` on **every**
checkout attempt. Its own comment says "Remove once resolved."

**Fix:** delete the block once the "Unable to process transaction" issue it was
added for is confirmed resolved.

---

## Notes
- Concurrent coupon over-redemption is already guarded by `lockForUpdate` in
  `PaystackOrderService::fulfil` and is now covered by a test — no fix needed.
- The `orders.reference` unique index exists, so the browser+webhook fulfilment
  race cannot create duplicate orders — no fix needed.
