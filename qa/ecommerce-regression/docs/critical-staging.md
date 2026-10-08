# Paystack staging regression

Target: https://test.albertinang.com. Use existing server test keys and dummy accounts only. Never put credentials or service keys in Git.

## 08 — cancellation and return journeys

Run `./scripts/run.sh critical-staging`, or the existing IntelliJ 08 configuration. Four scenarios create fresh sandbox bank-transfer orders: collection cancellation, collection return, delivery cancellation and delivery return. The test requires Paystack's Test Bank account before clicking “I've sent the money”, then verifies the server-confirmed payment reference, amount and persisted order. It never uses card or demo bank payment.

Collection: paid → processing → ready_for_pickup → completed. Cancellation ends at completion; returns begin after completion. Delivery: paid → processing → shipped → delivered. Cancellation is blocked from shipped onward; returns begin after delivery. Returns expire 30 days after actual receipt.

Returns progress through approval with instructions, goods received, inspection with customer notes, then refund request. Approval alone does not refund the payment. Refunds are requested once, and uncertain results retain their reference for investigation rather than retrying a refund.

08 verifies refund acceptance without waiting 30 minutes. Pending/processing must have no completion timestamp. Order status remains cancelled, completed or delivered; refund status is separate. Save evidence and run 10 later for settlement.

## 09 — controlled backend regression

Use the local PHP regression configurations for ownership, invalid signatures, duplicate payment callbacks, failed/timeout responses, coupon contention and the 30-day return boundary. The business allows unlimited stock quantities, so the old final-stock-unit contention expectation is retired. Coupon redemption remains atomic. These tests use SQLite and mocked provider responses; they do not establish actual Paystack settlement.

## 10 — saved refund settlement

Run `./scripts/run.sh settlement`, or IntelliJ 10. The ledger for test.albertinang.com is `reports/refund-followups-test.json`; the original testing domain uses a separate ledger. Ledgers and reports are ignored by Git.

This check reads saved refunds using the admin endpoint. It does not create orders, approve returns or retry refunds. Checks are paced to respect the endpoint limit. Processed Paystack + processed application refund + completion timestamp + correct order status passes. Pending/processing or unavailable provider status is skipped with a PENDING/UNRESOLVED annotation. Failed/needs-attention or an identity/amount/currency mismatch fails. Historical records from before 5 October may retain the old refunded order status; they are explicitly annotated as legacy.

An empty ledger skips with instructions to run 08 first. It does not invent settlement evidence. Test-mode readiness and private-file protection must pass before release. Running 10 again provides a fresh observation without waiting inside a test.

## Full browser run

Set staging dummy credentials in local `.env`, and explicitly set `ALLOW_TEST_WRITES=true` and `PAYMENT_TEST_MODE=true` only when the gateway uses test keys. Run `./scripts/run.sh full`. It runs configured Chrome, mobile and Firefox projects serially and saves a dated report. Admin and detailed lifecycle cases run on Chrome; mobile and Firefox cover storefront/account/checkout and the critical payment journeys. Optional historical diagnostics are excluded unless their specific flags are enabled. A skipped pending refund is not settlement success.
