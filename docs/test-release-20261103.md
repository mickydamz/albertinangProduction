# Test release — 3 November 2026

Source for the test deployment at test.albertinang.com, including manager order workflows, catalogue editing, product colours, state browsing, and simplified delivery settings.

States is under Delivery & pickup. State visibility controls customer state choices independently from delivery enablement. Checkout inherits the shopping state; active delivery areas no longer require individual store coverage links. Server-side basket/store eligibility and delivery rates remain enforced.

Run pending migrations before using the release. The new state delivery flag is backfilled from existing state enablement. Current test data, accounts, uploads, credentials, logs and backups are excluded from this source commit.

The hosted release passed fresh-process verification on 3 November 2026 at 17:19 UTC, with existing original data and production/test boundaries unchanged. Local backend regression: 289 tests, 3,205 assertions. Temporary deployment cron, scripts and uploaded ZIP were cleaned up; private rollback evidence is retained on the hosting account.

This commit does not deploy production.
