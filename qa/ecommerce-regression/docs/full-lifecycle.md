# Full payment, cancellation, return and review regression

In IntelliJ choose **06 - Full payment and lifecycle tests**, then press Run.

Or run:

```sh
./scripts/run.sh lifecycle
```

Seven scenarios are included:

| Test | Journey and evidence |
|---|---|
| E2E-PAY-001 | Sandbox bank transfer checkout → order save response → admin total/reference/status → customer invoice → refresh → only one stored order |
| E2E-PAY-002 | Unfunded bank transfer → server rejects payment confirmation without creating an order → basket retained |
| E2E-CANCEL-001 | Sandbox purchase → customer cancellation → one admin cancellation record → refunded order and visible refund reference |
| E2E-RETURN-001 | Sandbox purchase → processing/shipped/delivered → customer return → admin approval → refund evidence → customer order history |
| E2E-RETURN-002 | Sandbox purchase → delivered → customer return → rejection → delivered order retained → customer sees rejection |
| E2E-RETURN-003 | Bank transfer purchase → delivered → return request → repeat return page → only one pending request |
| E2E-REVIEW-001 | Sandbox purchase → completed → customer submits four-star review → public review survives refresh → admin sees it → duplicate request rejected |

Customer and administrator use separate browser sessions. Each scenario creates its own sandbox order and reports the created order ID and reference. Existing orders are not selected for mutation. Test orders remain as evidence. Review content and rating are attached and verified before the regression review is removed.

## Required configuration

The supplied customer/admin credentials and confirmed sandbox flags are already in the local `.env`. The dedicated lifecycle run enables `RUN_LIFECYCLE=true`. Normal desktop regression lists these scenarios as skipped, with a reason, to avoid creating orders every time you run smoke tests.

Lifecycle defaults use product 1213, the Hisense soundbar, currently ₦56,070, to fit the demo-bank balance. Override LIFECYCLE_PRODUCT_PATH and LIFECYCLE_PRODUCT_NAME for another low-value dummy product. Use a product that the customer has not already reviewed for E2E-REVIEW-001. The review scenario removes only reviews containing this suite's unique REGRESSION purchaser marker, before and after verification, so it can run repeatedly. Other customer reviews are preserved.

All lifecycle purchases use **Paystack bank transfer**. The adapter checks the visible sandbox controls and **Test Bank**, reads the temporary account and amount, and uses [Paystack's official demo bank](https://demobank.paystackintegrations.com/) to send simulated funds. It then checks payment completion and the website's order-save response. An unfunded transfer does not send simulated funds.

No card details are entered. The former card-decline scenario is now an abandoned-transfer scenario, and the card-specific refund-failure scenario is now a repeated-return scenario. No success response or persisted order is mocked.

## Verification status and limits

Live bank-transfer verification confirms successful payment, matching admin order/customer invoice, server rejection of an unfunded transfer, rejected-return persistence, repeated-return protection, and review persistence/duplicate rejection/cleanup. Return approval immediately changes the request to Refunded. Gateway refund identifiers are not exposed in the current order views, so financial refund verification remains unproven. `reports/VERIFICATION.md` lists the latest verified result per scenario and links its evidence. The earlier card run is archived: one decline scenario passed and six purchase-dependent scenarios stopped at Paystack Insufficient Funds. Those card results do not establish whether bank-transfer journeys work. See the latest reports/FAILURES.md and results.json for current completed counts.

Admin pages corroborate persistent server records, but this suite does not query the database directly. Refund checks demand displayed refund evidence; they do not independently verify gateway settlement. Gateway settlement/webhooks, return evidence uploads, full notification delivery, ordinary-customer permissions and Stripe journeys remain separate coverage gaps. Backend integration tests are in `backend/` and need the current Laravel code and full migrations.

Reports include screenshots, traces and the test order metadata. A failed stage stops that journey instead of reporting downstream stages as passed.

## Collection and delivery coverage

Configuration 06 runs all nine scenarios separately for collection and home delivery (18 scenarios). Collection selects Enugu HQ; delivery uses a clearly marked dummy Ogui Road address in Enugu State. Collection progresses paid → processing → ready_for_pickup → completed; delivery progresses paid → processing → shipped → delivered. Reviews use the respective received status. Paystack bank transfer is used for both. Checkout request and response evidence records the chosen journey.

Deferred issue REFUND-PROOF-001: missing refund identifier in the admin order view. Assertions remain active with a known-issue annotation; do not count application Refunded status as financial verification. Existing evidence remains unchanged.

SAVE10 high-value journey: basket and payable total must each exceed ₦1 million. SAVE10 gives 10% off capped at ₦5,000. The paid order must match the discounted total and show SAVE10 on its invoice/admin detail. Although admin now shows Active and unlimited uses, staging still rejects the dummy customer with “You have already used this coupon.” No coupon settings were changed; sandbox payment cannot proceed while this prerequisite fails.

Refund initiation is now recorded separately from settlement; pending/processing is allowed. See [critical test guide](critical-tests.md) for the 31 backend scenarios and isolated execution requirements.
