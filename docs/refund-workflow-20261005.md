# Separate cancellation, return and refund workflow

Implemented on codex/ecommerce-staging-improvements and deployed to test.albertinang.com on 5 October 2026.

## Release
Run migrations before serving the new code. New migration adds return workflow_stage, return_instructions and the order_request_events audit table. Existing refunds are not resent. Existing approved returns without a refund start at approved and require receipt/inspection. Existing refunds retain their provider state. Historical refunded order statuses are not guessed or rewritten.

## Paystack workflow
Customer cancellation is automatic while eligible, including ready-for-pickup collections. Shipped delivery cannot cancel. Unpaid orders cancel without requesting a refund. Cancellation support allows notes, not reversal or manual settlement.
Returns: requested -> approved with instructions -> received -> inspected with outcome -> refund requested. An explicit refund-without-return exception requires an audit reason. Repeated/out-of-order actions are rejected. Approving a return never sends a refund request.
Provider completion updates refund records while preserving delivered/completed/cancelled order history. Refund processed does not mean bank receipt is independently confirmed. Partial refunds are unsupported; actions request the full order amount including its delivery fee, preserving existing policy.
Admin cancellations, returns and refunds have shared tabs. Refund status checks use reconciliation and never initiate refunds. Scheduler already runs reconciliation every ten minutes; hosting must run Laravel schedule:run for this to execute.

## Scope and limits
The staged refund workflow is for Paystack. Existing Stripe/manual paths remain legacy and need a separate review before use. No new return-window or delivery-fee policy was invented. Return rejection after inspection requires support review; refunds that failed/are uncertain must be reconciled, not retried blindly. No historical provider calls are made by migrations.

## Validation
58 focused tests passed (661 assertions after adding admin return-screen rendering). Full regression: 127 tests, 1248 assertions, 7 failures and 1 error. These are the same outstanding categories documented before this change: return-window/old refund contract tests, stock and coupon concurrency, obsolete payment contracts, and a review fixture/schema mismatch. Do not treat this as a fully green production release. Local migration was applied after backing up the preview database; staging migration was not run.

## Staging deployment verification

Deployed to test.albertinang.com on 5 October 2026 after backing up albertin_tcopy and nine existing source files. Added the return workflow columns, request event table and migration record. All 16 files were read back and matched local source; subsequent email and customer wording fixes were deployed separately.

The live approval check exposed a reserved Laravel mail variable collision; renamed message to stageMessage and added a real email rendering test. Dummy return ALB-ENU-460802 was approved, marked received and inspected without issuing a refund. Customer history shows inspection complete and no refund requested. New Refunds workspace, Returns, Cancellations and customer order history rendered successfully. The manual check for ALB-ENU-588894 saved its check timestamp and preserved the processed refund. Inbox receipt was not verified.

Focused suite: 59 tests and 669 assertions passed before the customer wording adjustment; ReturnWorkflowTest was rerun after that adjustment: 5 tests, 65 assertions passed. The previously reported full-suite failures remain outside this deployment.

## Individual order approval repair

The individual admin order page still submitted the legacy status field. The new controller required action and return_instructions; validation redirected without displaying errors, leaving the return pending and sending no approval email. Both admin return entry points now share the action form. Validation errors are visible. Return notifications use the existing emails.layout company template. Cancellation mailables now render their HTML views directly, avoiding the missing markdown themes.default failure.

Seven patched files were deployed and read back identically. ALB-ENU-479409 was approved with return instructions; the customer order page displays those instructions and Return approved — send the goods back. The purchase remains completed and no refund was initiated. The hosting delivery report records the notification to uaezenwafor@gmail.com as accepted by smtp.antispamcloud.com at 18:25:18 on 5 October; inbox arrival was not independently verified. Evidence is under ../deployment/return-approval-*-20261005.*.

Final focused verification passed: 61 tests, 701 assertions, including real return and cancellation mailable rendering with the company layout. This is not a rerun of the previously failing full suite.

## Inspection validation repair

On staging, ALB-ENU-973589 remained at Goods received after inspect was submitted without admin_notes. Backend validation rejected the empty inspection outcome, but the Returns index did not display errors. Reproduced live. Adding a test inspection outcome saved Inspection completed and exposed Request full Paystack refund; no refund was issued. Local UI patch displays server errors and conditionally requires notes for inspection/rejection/exception, or instructions for approval. Seven ReturnWorkflowTest tests passed with 102 assertions. This two-view patch is packaged in deployment/inspection-validation-fix-20261005.zip and awaits hosting access for deployment.
