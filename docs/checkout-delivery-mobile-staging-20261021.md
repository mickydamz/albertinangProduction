# Delivery mobile: staging deployment completed

Staging URL: https://test.albertinang.com
Feature branch: feature/checkout-delivery-mobile
Implementation commit: 5c0f89ce4fc03a8c42f1ff0627219346abc591db
Branch is local and has not been pushed or merged into main.

## Verified staging deployment

Completed on 21 October 2026 at 15:42 BST (server report: 2026-10-21T14:42:01+00:00).

cPanel Domains confirmed test.albertinang.com maps to /home/albertin/test. Application bootstrap, effective APP_URL, APP_ENV=staging and database albertin_tcopy were checked by a guarded CLI deployment script. Production was not deployed or changed.

The first read-only preflight stopped on checkout template drift without changing application files or schema. The server's existing single delivery-address textarea was preserved by merging only the feature patch into the current server template. The other nine target files matched their expected baselines. The exact merged staging template compiled locally before deployment.

The guarded deployment backed up existing target files and all 56 staging database tables outside web roots at /home/albertin/mobile-staging-20261021-r2/backup. Database backup size: 6315427 bytes. SHA-256: 7f006f7a172f8e70432b4ef41f855285f6be5333e96a833359be698a3ddd7032.

The scoped migration completed. Both orders.delivery_mobile and pending_checkouts.delivery_mobile were absent before and present afterward. Templates compiled successfully. Readback hashes matched all 10 files in application-files.json. The temporary inspection and deployment cron entries were removed; the original staging scheduler was preserved. Backup and deployment result files remain outside all document roots.

## Behavior and validation

Customers must supply a mobile number before checkout can prepare either payment flow. Nigerian local mobile numbers normalize to +234. International country-code numbers accept E.164 shape; validation does not prove ownership, reachability or international mobile allocation. The contact is saved in the checkout snapshot and copied onto the paid order by either gateway. Staff can view the contact on admin order details. Legacy paid checkout reconciliation remains possible. The obsolete amount-trusting initiation aliases are blocked.

Local tests: 184 regression tests / 1990 assertions, plus 46 focused existing checkout and Stripe feature tests / 142 assertions, all passed. PHP syntax, Blade compilation and diff checks passed. These use synthetic gateways and isolated SQLite databases.

Browser check: the staging site responded and protected /checkout redirected to its sign-in page. The browser has no authenticated customer session, so a signed-in visual checkout and a sandbox payment have not been executed on staging. No real payment or customer notification was triggered during deployment.

The staging package contains the merged staging template. The feature branch retains the current main-branch two-line address layout, with the same mobile behavior; this prevents reverting the repository's address layout solely to match older staging code.
