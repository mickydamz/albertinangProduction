# Checkout delivery mobile

Branch: feature/checkout-delivery-mobile
Base: f4375e689b6b497b096aaed548c36c32ada7b9b0 (verified origin/main on 21 October 2026).

Checkout requires a contact number before either gateway starts. Nigerian local mobile numbers are normalized to +234; international country-code numbers accept E.164 shape. This checks number format, not ownership, reachability or international mobile allocation. Delivery and collection both require contact details. Numbers are saved in pending_checkouts.delivery_mobile and copied from that trusted snapshot to orders.delivery_mobile by both gateways. Staff can see the escaped number in admin order details. No number is added to gateway metadata or application diagnostic logs.

A nullable migration preserves historical orders and already-paid legacy checkout recovery. Old pending checkouts without a contact cannot start a new Stripe intent; new checkout snapshots always require a valid number. Existing paid legacy references can still be reconciled without losing an order. The obsolete amount-trusting initiation aliases now return 422 and direct clients to checkout. Existing server pricing, gateway verification and duplicate protection remain in place.

Validation: full regression 184 tests / 1990 assertions; focused existing delivery, truck delivery, installation, shipping address and Stripe feature tests 46 tests / 142 assertions. All passed using isolated SQLite databases and synthetic gateways. Blade compilation, PHP syntax checks and git diff --check passed. No real payment, live browser checkout or remote migration was performed.

## Initial staging deployment status (superseded)

Deployment subsequently completed; see checkout-delivery-mobile-staging-20261021.md. Initial status: NOT DEPLOYED. Project records specify https://test.albertinang.com and /home/albertin/test/ecommerce; its document root is recorded as /home/albertin/test. Current hosting mapping must still be independently verified in cPanel. No text.albertinaNG domain was invented. No production files were changed.

Automatic approval review rejected reading the cPanel accessibility tree because it exposes broad private hosting data beyond staging verification. Further hosting inspection and staging deployment require user approval for that hosting view. Do not work around that rejection.

## Deployment procedure after hosting approval

1. Confirm cPanel Domains maps test.albertinang.com to /home/albertin/test and index.php loads /home/albertin/test/ecommerce. Verify the application's effective APP_URL, environment, database and payment test keys refer to this staging site. Stop on any production target or unexplained mismatch.
2. Back up the staging database and each target source file outside the document root. Compare each remote file with manifest previous_sha256. If remote source differs, merge the patch against current server source and repeat tests; do not blindly overwrite.
3. Upload only application files listed in application-files.json to /home/albertin/test/ecommerce. Keep credentials, vendor, local configuration, database and test artefacts out of the upload.
4. Apply only database/migrations/2026_10_21_000001_add_delivery_mobile_to_checkouts_and_orders.php using php artisan migrate --path=database/migrations/2026_10_21_000001_add_delivery_mobile_to_checkouts_and_orders.php --force from the verified staging application directory. Rebuild autoload if authoritative, and rebuild Blade cache. Preserve existing staging access protection.
5. Read source files back and compare SHA-256 hashes. Confirm both nullable columns exist. In a sandbox checkout verify missing/invalid mobile yields 422 before opening either gateway, valid Nigerian/international values persist, sandbox paid orders show the same number to staff, and callback retry creates one order. Do not trigger real payments or customer notifications.
6. For rollback restore backed-up source. Retain nullable columns and collected numbers; dropping columns destroys contact data and needs a separate reviewed decision.
