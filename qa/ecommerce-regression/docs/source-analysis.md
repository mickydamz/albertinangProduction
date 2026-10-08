# Source-derived regression expansion

Analysed repository: https://github.com/mickydamz/albertinangProduction
Snapshot: b76cebbf6e97d75a794011be6bf02bfbb44e8160 (downloaded 1 October 2026).
Local checkout: ../site-source. Tests target testing.albertinang.com; the repository snapshot is not proof of what is deployed.

## Added coverage

| Area | New staging scenarios | Assertions |
|---|---:|---|
| Coupon input validation | 3 | Missing code, negative subtotal and nonnumeric subtotal rejected with field errors |
| Installation API | 3 | Invalid IDs/input rejected; requested product has an options array |
| Delivery APIs | 3 | State filtering, physical pickup locations, truck weights and thresholds |
| Search and geography | 2 | Product suggestion shape, unknown search empty, Nigeria present, unknown country empty |
| Guest access and invoice links | 8 | Tickets/chat/security/manager/supplier routes require login; unsigned invoice inaccessible |
| Review validation | 4 | Ratings outside 1–5; comments below 10 or above 1,000 characters rejected |
| Checkout server validation | 4 | Empty basket, quantity 0, quantity 101, invalid fulfilment method rejected |
| Payment confirmation | 1 | Missing reference rejected before gateway confirmation |
| Delivery browser journeys | 3 | Truck fee and arithmetic, refresh persistence, collection toggle removes fee, missing address blocks payment |
| Ticket/account validation | 3 | Blank ticket, invalid priority and password-confirmation mismatch rejected |
| Support ticket lifecycle | 1 | Customer creation, reply, admin persistence and edited description |
| **Total** | **35** | Added to configurations 02; browser journeys also available in 03 |

Four new backend scenarios in backend/CouponPolicyRegressionTest.php cover high-value capped discounts, repeat use enabled, repeat use disabled despite unlimited global usage, and exhausted global limit despite repeat use enabled. Backend preparation copies our regression PHP tests into the isolated downloaded source; SQLite :memory: and testing environment are required before migrations. PHP/Composer are absent locally, so backend execution is unverified.

The downloaded repository already includes substantial Laravel feature tests for admin CRUD, ownership, refunds, webhooks, email, invoices, delivery, tickets and authentication. Do not run its default phpunit.xml against an unspecified database: its SQLite overrides are commented out. Our guarded configuration is limited to tests/Regression.

## Findings from source inspection

- **SAVE10 repeat-use explanation:** app/Models/Coupon.php checks max_uses separately from multi_use. Removing the global limit does not enable repeat use by the same customer. Admin create/edit forms expose multi_use; its migration is dated 1 October 2026. A deployment/settings mismatch remains possible.
- **Truck weight boundary mismatch:** checkout.blade.php uses totalWeightKg > threshold; OrderController::saveCheckout uses >=. At exactly the weight threshold the browser and server may quote different delivery charges. This is a source finding; the boundary was not reproduced live.
- **Refund evidence:** admin order detail displays the payment reference and refund status but no explicit gateway refund identifier in the inspected view. Existing REFUND-PROOF-001 remains a verification gap, not proof of a failed refund.
- **Public maintenance/mutation routes:** routes/web.php includes /fix-pdf-now and /foo without auth; standalone brands/categories/subcategories toggle routes also sit outside the admin role group. The maintenance handler can write a provider file and clear caches. These routes were not called: they need a separate source fix and access-control review.
- **Ticket edit contract:** source TicketController::update requires status, while the inspected customer edit template has no status field. The staging journey checks actual persistence and may expose this mismatch.

## Coverage limits

This does not establish full admin CRUD coverage, normal-customer role enforcement, independent financial refund verification, email delivery, or direct database consistency on staging. Invalid-input tests stop before creating reviews/orders or changing passwords. Support tests create clearly marked dummy tickets only; retained tickets provide evidence for later investigation.

## Confirmed staging issue

The dummy customer ticket edit page returns HTTP 500 with “Undefined variable $replies” in tickets/edit.blade.php. Ticket creation and reply succeeded first. Failure evidence is preserved under `reports/source-ticket-final` (latest) and `reports/source-journeys-recheck` (initial discovery). The test asserts the HTTP result before trying to fill missing fields.

Run only the new staging coverage with IntelliJ **07 - Source-derived regression** or `REPORT_DIR=reports/source-full ./scripts/run.sh source`. These checks are also included in **02 - Desktop regression**.
