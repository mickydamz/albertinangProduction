# Critical regression tests

## Correct order journeys

Collection: paid → processing → ready_for_pickup → completed (admin completes collection).
Delivery: paid → processing → shipped → delivered. Delivery reviews are tested at delivered; no extra completed transition is applied.

IntelliJ **06 - Full payment and lifecycle tests** now contains 18 scenarios, including explicit status journeys for both methods. Paystack sandbox bank transfer only.

## Refunds are asynchronous

Paystack accepting a refund is different from completing it. Pending/processing must not be treated as a timeout failure or proof that money has reached the customer. The ordinary lifecycle checks application acknowledgement and attaches `refund-follow-up` evidence with order/reference, request text, observation time and `gatewaySettlementVerified: false`. These are workflow checks, not financial settlement verification. Existing refund evidence is retained.

Backend tests simulate queued, processing, failed, timed-out and later processed outcomes. A completed financial refund needs the gateway processed outcome; a queued refund must not get a settlement timestamp. Tests intentionally fail if the application marks queued/failed refunds financially complete.

Official semantics: https://paystack.com/docs/payments/refunds/

No staging Paystack secret was read or used. Follow-up verification remains within the user's current-evidence boundary. Do not wait days in a browser regression or remove financial assertions to make a refund appear verified.

## 31 critical backend scenarios

IntelliJ **09 - Critical backend regression (local)** invokes `backend/run-critical.sh` against the isolated downloaded source.

| ID | Scenario |
|---|---|
| CR-PAY-01 | Verified bank-transfer order matches trusted checkout |
| CR-PAY-02 | Webhook creates order without browser confirmation |
| CR-PAY-03 | Late transfer recovers original pending checkout |
| CR-PAY-04 | Unfunded transfer creates no paid order |
| CR-PAY-05 | Gateway timeout remains retryable; later verification recovers |
| CR-PAY-06 | Database save failure rolls back; retry creates one order |
| CR-PAY-07 | Underpayment is rejected |
| CR-PAY-08 | Wrong currency or missing checkout creates no order |
| CR-DUP-01 | Repeated webhook creates one order and one confirmation |
| CR-DUP-02 | Repeated browser confirmation returns the same order |
| CR-DUP-03 | Concurrent fulfilment service calls from browser/webhook paths create one order |
| CR-DUP-04 | Retry does not redeem a coupon twice |
| CR-DUP-05 | Two customers can each buy 150 units at zero recorded stock; stock stays unchanged |
| CR-DUP-06 | Final coupon use cannot discount two concurrent orders |
| CR-ACCESS-01 | Another customer's invoice denied |
| CR-ACCESS-02 | Another customer's PDF denied |
| CR-ACCESS-03 | Another customer's cancellation page denied |
| CR-ACCESS-04 | Another customer's cancellation POST rejected without mutation/refund |
| CR-ACCESS-05 | Another customer's return page denied |
| CR-ACCESS-06 | Another customer's return POST rejected without mutation |
| CR-ACCESS-07 | Another customer's review cannot be edited |
| CR-ACCESS-08 | Another customer's ticket cannot be read/edited/replied to |
| CR-ACCESS-09 | Ordinary customer denied admin pages and order mutation |
| CR-REFUND-01 | Cancellation full refund queued; not yet settled |
| CR-REFUND-02 | Approved return refund can remain processing |
| CR-REFUND-03 | Partial request must not silently cause a full refund |
| CR-REFUND-04 | Gateway rejection cannot mark order refunded |
| CR-REFUND-05 | Timeout cannot mark refund financially complete |
| CR-REFUND-06 | Repeat while pending cannot issue another refund |
| CR-REFUND-07 | Excessive refund request rejected before gateway |
| CR-REFUND-08 | Delayed processed notification and duplicates update one refund |

These tests create separate ordinary customers automatically in the isolated database. Customer permissions do not depend on the supplied staging account, which has admin privileges.

## Execution and limitations

PHP with SQLite and Composer dependencies are required. PHP/Composer are installed. The expanded local suite has 45 scenarios: 43 passed and two unrelated concurrency failures. All 22 refund-related checks pass, including the staging status adapter. Original before-fix results remain in reports. The runner rejects cached Laravel configuration and the test base refuses to migrate anything except testing SQLite :memory:. The concurrency helper copies the in-memory fixture into a private temporary SQLite file, launches two actual PHP worker processes behind a barrier, blocks real gateway requests and deletes the temporary database afterwards. The workers exercise the shared fulfilment service, not concurrent HTTP servers. SQLite locking differs from MySQL: production database concurrency needs a separate isolated MySQL verification too.

Partial refund amount inputs (`amount_ngn`/`refund_amount`) are a proposed contract. If unsupported, the server must explicitly reject them; silently sending a full refund is unsafe. The inspected source currently ignores these fields. No partial refund is submitted to the real gateway by these tests.

Current policy and results are documented in critical-staging.md and the release report. Stock is unlimited; coupon use is transactionally protected. Return requests are limited to 30 days after receipt. Historical results above must not be treated as current release evidence.
