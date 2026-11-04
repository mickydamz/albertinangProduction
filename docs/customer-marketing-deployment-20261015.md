# Marketing feature staging deployment — 15 October 2026

Deployed application source commit `ab5146e` from `codex/customer-marketing` to **https://test.albertinang.com** only, at 21:45 BST. Production and the original testing application were not targeted.

## Installation evidence

- Guarded the application path `/home/albertin/test/ecommerce`, configured URL `test.albertinang.com`, and database `albertin_tcopy` before changes.
- Created a private full staging base-table SQL snapshot (6,263,952 bytes), source backups and environment backup under `/home/albertin/marketing-release-20261015/backup`, with directory permission 0700. The installer only runs from CLI and has a one-attempt lock.
- Applied the additive migration `2026_10_15_000001_create_marketing_campaigns` and installed/read back all 18 runtime files. The installer preserved unrelated existing changes in shared files where necessary.
- Rebuilt configuration and compiled views; cleared prior routes and views. Existing Laravel scheduler remains every minute. Removed the temporary installer cron entry after success.
- Explicitly set `MARKETING_EMAIL_ENABLED=false`. The live admin page confirms sending is disabled. No customer emails or admin test emails were sent.

## Live verification

- Admin campaign index renders and is linked under Pricing & promotions → Email campaigns.
- Created campaign #1, edited its subject to “Staging demo — seasonal offers (reviewed)”, and observed “Draft updated” with persisted content. The draft remains available for review; all delivery counters are zero.
- Email preview renders the saved subject/message, staging offer link and signed unsubscribe link.
- Signed-in email preferences page renders with opt-in unchecked. No account consent was changed.
- Anonymous access to `/admin/marketing` redirects to the sign-in page.
- Verified only the normal application scheduler remains in cPanel.

Local validation previously passed 10 feature tests / 52 assertions. Real mail transport acceptance/inbox delivery, live opt-in/out writes and live customer-role authorization were not exercised; those behaviours have local test coverage. Full ecommerce regression and mobile visual testing were not rerun for this deployment. Sending remains disabled pending a controlled mail test.

Local evidence: `/Users/uche/Downloads/ANG/deployment/customer-marketing-20261015/server-installation-report.txt`, `scheduler-after.txt`, and `staging-campaign.png`.
