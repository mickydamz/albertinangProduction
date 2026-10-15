# Customer email campaigns

Branch: `codex/customer-marketing`, based on GitHub `origin/main` at `9404f98` (15 October 2026). The separate checkout preserves the existing staging and regression work.

## Using the feature

- Admin: Pricing & promotions → Email campaigns (`/admin/marketing`). Save or edit a draft containing a subject, plain text message, and optional HTTPS offer link. Preview the email, send a test to your own admin address, then confirm and queue the campaign.
- Customer: account menu → Email preferences (`/account/email-preferences`). Explicitly opt in or opt out. All existing and new users start unsubscribed. No previous consent is assumed or imported.
- Only users with role `user` and a recorded opt-in enter the campaign audience. Each delivery checks consent, role and email again immediately before sending. Changed emails and deleted accounts are skipped.
- Every promotional email has a signed unsubscribe link. Opening the link shows a confirmation page; submitting it removes the subscription without logging in. Transactional emails are unaffected.
- Campaigns cannot be edited or queued again after dispatch. Delivery counts distinguish pending, sending, sent, skipped and failed. “Sent” means mail transport acceptance, not inbox delivery.

## Staging installation checklist

Nothing has been deployed and no customer emails have been sent by this implementation.

1. Back up the staging database and deploy this branch to **test.albertinang.com** using the usual staging process.
2. Set `APP_URL=https://test.albertinang.com` so signed email links point to staging. Confirm HTTPS and the staging mail transport/sandbox. Leave `MARKETING_EMAIL_ENABLED=false` until ready for a controlled test.
3. Run `php artisan migrate --force` and rebuild the usual application caches. The migration adds a nullable consent timestamp and campaign/delivery tables. Its rollback removes campaign history and consent records.
4. Confirm the Laravel scheduler is running every minute (`php artisan schedule:run`). The sender handles at most 100 recipients per run by default; `php artisan marketing:send --limit=100` also runs it manually.
5. Use fictional staging customer accounts and a sandbox mailbox. Opt in through preferences, enable `MARKETING_EMAIL_ENABLED=true`, rebuild configuration cache, and test preview, admin test email, campaign delivery and unsubscribe. Do not enable with a copied production audience or live customer mail transport during staging tests.

Failures are recorded without automatic retries because a transport error may occur after acceptance. A crashed process can leave a record at `sending`; inspect the mail provider before any manual recovery. These records are never automatically re-sent. A completed campaign may contain failed or skipped deliveries; inspect its counters. The command claims each recipient atomically, and scheduled runs use overlap protection. There is no transactional guarantee between a database write and an external mail provider, nor a provider delivery/bounce webhook in this first version.

## Local validation

Run from this checkout:

```sh
APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA= vendor/bin/phpunit tests/Feature/MarketingCampaignTest.php
php artisan view:cache
git diff --check
```

The feature tests create an isolated in-memory SQLite schema and use fake mail. Ten tests / 52 assertions passed, including rendered admin pages, role protection, input validation, draft edits, opt-in/out, signed unsubscribe confirmation, send-time suppression, duplicate dispatch prevention, changed email suppression, the environment switch, escaped preview content, admin-only test recipients, and transport failure handling. Blade compilation and whitespace checks passed. The full existing regression suite, full-schema migration, real SMTP delivery, scheduler on the server, and desktop/mobile visual review have not been verified.
