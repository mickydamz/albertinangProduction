# Local Paystack refund fix

This change is local only. No staging keys were read, refunds issued, commits created, or deployment performed.

## Behavior

Cancellation decisions and refund settlement are separate. A successful refund creation response stores the gateway ID/status. Pending, processing, failed, unknown and needs-attention do not set a settlement timestamp or mark the order refunded. Only a matching processed response/event completes the refund and queues the customer completion email.

The shared refund service handles customer cancellation, admin cancellation and admin return approval. A unique ledger row per order plus an order-row lock reserves the request before calling Paystack. Unknown outcomes survive request timeout/process interruption and block further requests. The service does not automatically retry failed/uncertain refunds. Administrators cannot manually mark a Paystack order refunded without recorded gateway confirmation.

Only one full refund per order is supported. Amount overrides, partial amounts and excessive amounts are explicitly rejected. Introducing partial refunds later requires a multiple-refund ledger, remaining-balance calculation and dedicated UI.

Signed refund.pending, refund.processing, refund.needs-attention, refund.failed and refund.processed events validate the recorded reference, amount, currency and refund identifier when present. Documented webhook payloads without an identifier can match the unique full-refund record. Duplicate processed events do not send duplicate completion mail or rewrite completion timestamps; older pending/processing events cannot undo completion. Unmatched events are logged without changing records. Database errors escape so Paystack can retry delivery.

The scheduled reconciliation command only reads Paystack. It fetches a known refund ID; if a response contains a numeric transaction ID, it verifies that transaction against the original reference. Uncertain requests without a refund ID are found through transaction verification and paginated refund lookup, accepting only a unique matching refund. Missing/ambiguous results remain unresolved; they never cause another POST. A 20-page cap prevents unbounded lookups and leaves larger results for admin investigation.

The UI shows separate refund status on customer return/cancellation pages and admin lists. Bank details needed for needs-attention must currently be handled through Paystack's existing administrative workflow.

## Before deploying later

1. Review the source diff, including the new `paystack_refunds` migration. The earlier SQLite compatibility migration is a separate local testing change saved in the regression project's `backend/sqlite-migration.patch`.
2. Back up the destination database, deploy reviewed code, and apply migrations with `php artisan migrate --force` on the intended server. No migration has been run against staging or production here.
3. Ensure the existing Paystack webhook URL points to `/webhooks/paystack` in the matching test/live environment. Refund events use the same signature verification as payments. Do not expose the secret to the browser.
4. Ensure Laravel's scheduler runs every minute and the queue worker runs. The scheduled command is `php artisan paystack:reconcile-refunds`, every ten minutes. A nonzero exit means refunds remain unresolved and require review.
5. On staging, complete a dummy bank-transfer payment and cancel/approve its return. Check the recorded refund ID, pending/processing UI, later signed completion event, database timestamp and single completion notification. Local simulated tests do not prove real Paystack processing or MySQL locking.

## Historical records

Existing incorrectly marked refunded records are deliberately not rewritten by a migration. Their old state does not prove money was returned. Existing IDs/timestamps/refunded request statuses block a second refund. Audit each against Paystack's recorded refund first. For an unresolved legacy record, restore an appropriate request/order status only after reviewing its actual fulfillment and gateway status, then import the verified refund into the new ledger. Do not blindly reset all orders, rerun refund creation, or fabricate a completion date. Historical records have not been reconciled here because no gateway credentials were accessed.

## Test results

See the regression project's `reports/REFUND-FIX.md` for the executed test results. Tests use the real local Laravel routes/services/migrations, isolated SQLite and simulated Paystack responses. No real transfers or refunds occur.

References: https://paystack.com/docs/payments/refunds/ and https://paystack.com/docs/api/refund/.
