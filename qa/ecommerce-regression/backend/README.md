# Backend integration regression

These tests call the application's real Laravel routes and inspect database changes. Mail, notifications, uploads and outbound gateway HTTP calls are faked. They do not access the remote staging database or submit real refunds.

Copy these PHP files into `tests/Regression/` in an **isolated checkout of the current backend**. Copy `phpunit.regression.xml` to its root. Install its Composer dependencies, ensure PHP with SQLite is available, then run:

```sh
php vendor/bin/phpunit -c phpunit.regression.xml --log-junit regression-junit.xml
```

The suite refuses to migrate any database except SQLite `:memory:`. It uses the application's real migrations; do not replace missing migrations with a fake schema just to make the tests green. Configuration caching must be disabled in that isolated checkout.

Status: authored against the local Laravel snapshot, NOT EXECUTED. The local environment lacks PHP/Composer and the snapshot lacks order/return/cancellation migrations. Current server source and complete migrations are required. Factory fields may need adapting to that current schema; the expected security and financial assertions should stay strict.

Known source risks deliberately have normal failing assertions (not expected-failure markers): cross-customer order access, duplicate/expired returns, unverified paid-order creation and failed refunds incorrectly marked refunded.

A passing mocked-gateway integration test is not proof of the deployed Paystack/Stripe configuration, webhook delivery, email delivery or production behaviour. Those require separate sandbox end-to-end evidence.

## Supplied current source

The GitHub source is downloaded to `../../site-source`. Run `./backend/prepare-current-source.sh` from the regression project to copy guarded tests into its `tests/Regression`. This preparation has been completed. Four new CouponPolicyRegressionTest scenarios target current multi_use/global-limit rules and capped discounts above ₦1 million. Current source includes the order/return/cancellation migrations missing from the older snapshot; PHP/Composer and Composer dependencies are now installed. Original backend tests may still require adaptation to the current schema. Never use the source default PHPUnit configuration against staging databases.

## Critical suite

31 current-source critical scenarios are selectable independently using `./backend/run-critical.sh` (IntelliJ configuration 09). They cover payment recovery, actual separate-process SQLite contention, customer ownership and refund initiation versus later settlement. Native PHP syntax checks passed. All 31 critical tests executed: 21 passed, 10 failed, zero errors. See `../docs/critical-tests.md`. No actual gateway keys or refunds are used; outbound HTTP and notifications are faked.

## Runtime installation update

PHP 8.2.34 and Composer 2.10.3 are installed and available in the terminal. SQLite/PDO, MySQL/PDO, mbstring, DOM/XML, cURL, ZIP and OpenSSL were verified. All new critical PHP files pass native `php -l` syntax checks. Locked Composer dependencies are installed. Critical backend execution completed; see `../reports/CRITICAL-VERIFICATION.md` for exact results and failure snapshots. A local SQLite migration compatibility patch is saved in `sqlite-migration.patch`; it is already applied to the sibling checkout. Fresh copies of that same source require the patch before running on SQLite.
