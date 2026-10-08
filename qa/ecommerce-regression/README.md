# AlbertinaNG ecommerce regression

This version accompanies the staging improvements branch. Target https://test.albertinang.com using dummy accounts and Paystack test keys. The application is two directories above this folder.

Install Node.js and pnpm, run `pnpm install` and `pnpm exec playwright install`, then copy `.env.example` to `.env`. Configure dummy logins locally. Keep test-write/payment flags false until staging and Paystack test mode are confirmed. Credentials, reports and refund ledgers are excluded from Git.

- Full configured browser regression: `./scripts/run.sh full`
- Quick desktop smoke: `./scripts/run.sh smoke`
- Payment cancellation/return acceptance: `./scripts/run.sh critical-staging`
- Read-only settlement follow-up: `./scripts/run.sh settlement`
- TypeScript check: `./scripts/run.sh check`

Full runs create sandbox orders and send notifications. Paystack bank transfer only; no cards or demo bank. Mobile and Firefox cover storefront/account/checkout and critical payment journeys; detailed admin and lifecycle cases run on Chrome. Optional historical diagnostics do not run by default. Refunds still processing are not counted as settled.

Backend tests already live in the application's tests directory. From the Laravel root, install Composer dependencies and run `vendor/bin/phpunit -c phpunit.regression.xml`. That configuration requires SQLite :memory: and fakes provider/mail responses. Feature tests additionally need a prepared testing database; never run them against staging or production databases.

See `docs/critical-staging.md` and the application's `docs/push-readiness-20261008.md` for policies, validation and known limitations. Existing IntelliJ tests in the original standalone project have also been updated; this copy makes their source available to the team.
