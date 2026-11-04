# Delivery mobile staging deployment — 21 October 2026

Verified target: https://test.albertinang.com, document root /home/albertin/test, application /home/albertin/test/ecommerce. Hosting mapping, application bootstrap, effective APP_URL, staging environment and staging database were checked before deployment. Production was not deployed.

The initial deployment at 15:42 BST backed up existing source and all 56 staging database tables outside web roots, applied the scoped nullable-column migration, compiled templates and verified all 10 uploaded files by SHA-256. The staging checkout had an older single delivery-address textarea; a merge preserved that layout while adding the mobile requirement. The repository keeps its current two-line address layout.

Subsequent staging source updates restricted numbers to Nigeria, shortened the copy, added the bold Collection/ delivery number label, and placed Delivery mobile directly in Order Summary. Source backups, template compilation and file readback checks completed for each update.

Order ALB-ENU-308230 (#260) retained its contact in its fulfilled checkout but had no contact on the order. Current source hashes and model configuration were correct at inspection; the original transfer failure's runtime cause was not established. At 16:22 BST the guarded recovery service restored the contact from the matching checkout. The refreshed authenticated admin order page displayed the Nigerian number and tel link instead of Not provided. No profile-phone substitution, payment replay or order-total/status change occurred.

Recovery source and prior order contact state were backed up at /home/albertin/mobile-recovery-20261021/backup. Initial database/source backups remain at /home/albertin/mobile-staging-20261021-r2/backup. Temporary deployment and diagnostic cron entries were removed; the original staging scheduler was preserved.

Validation: 193 backend regression tests / 2064 assertions passed. Source hash verification, migration-column checks, Blade compilation and authenticated admin display were verified on staging. No real payment or notification was triggered by deployment or repair; a fresh end-to-end sandbox payment was not executed on staging.

Rollback: restore the relevant backed-up source. Retain nullable contact columns and recovered contact data; dropping columns would destroy data and requires a separately reviewed decision.
