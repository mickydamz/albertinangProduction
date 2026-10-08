# Regression and push readiness — 8 October 2026

Ready for branch push and review. The completed correction runs have no failures. Staging fixes are deployed to https://test.albertinang.com; production has not been deployed. No push or merge has been performed.

Branch: `codex/ecommerce-staging-improvements`.

## Verified results

| Check | Result |
| --- | --- |
| Backend feature suite | 445 passed; 1449 assertions |
| Backend regression suite | 165 passed; 1748 assertions |
| Local browser suite | 63 passed |
| Staging registration: Chrome, Firefox and mobile | 27 passed |
| Staging storefront/account/browser coverage: Firefox and mobile | 70 passed across the main run and two replacement mobile search checks |
| Desktop API and deployment correction checks | 28 passed |
| Critical payment, cancellation, return and email journeys: Firefox and mobile | 12 passed across targeted runs |
| Saved refund settlement checks | 28 passed; 5 unresolved checks skipped; no failures |
| TypeScript, PHP syntax and Git whitespace checks | Passed |

These are separate suites and targeted reruns; overlapping cases must not be added into a unique scenario total. The earlier desktop run had 135 passes and 8 failures. Its outdated stock, removed supplier-route and settlement expectations were corrected and rerun successfully. This is cumulative regression evidence, not a claim that one fresh uninterrupted full run passed.

Detailed admin and lifecycle tests are configured for Chrome. Firefox and mobile cover storefront, account, checkout and the critical payment/notification journeys. Four optional historical diagnostic skips remain in the main browser batch.

## Changes completed

- Unlimited positive purchase quantities, with admin availability review before processing.
- Atomic coupon allocation and duplicate-payment protection, including concurrent checkout coverage.
- Returns within 30 days of delivery or completed collection, using recorded receipt dates.
- Full registration address fields and country/state request race fixes.
- Updated selectors, mobile search checks and Paystack test bank transfer selection. Payment success requires persisted server confirmation.
- Support ticket editing now loads replies and saves priority without requiring a status field absent from the form.
- Copied catalogue banner links remain on staging when browsing staging.
- Portable regression suite included under `qa/ecommerce-regression`, with a full-run command and current policy documentation.

## Staging and email evidence

The policy/registration deployment and subsequent ticket/banner fixes were uploaded and their source read back against local files. The receipt-date migration was applied, with 88 historical receipt transitions recovered from audit logs. Previous files and the database backup remain outside this application repository.

Gmail receipt was verified for paid, processing, ready for pickup and completed collection notifications, and paid, processing, shipped and delivered delivery notifications, including review invitations. Refund notifications distinguish requests and processing from provider completion.

## Remaining limitation

Five historical refund records lack confirmed provider settlement or an accepted refund record. They remain unresolved, rather than being reported as passed or automatically retried. New refund acceptance is also distinct from settlement; asynchronous settlement must be checked later. These records require reconciliation before claiming every historical refund is settled.

Credentials, inbox screenshots, database backups, dependencies and generated reports are excluded from the branch. The local IntelliJ suite is updated, and the portable suite is included for collaborators. See its README for setup and the full regression command.
