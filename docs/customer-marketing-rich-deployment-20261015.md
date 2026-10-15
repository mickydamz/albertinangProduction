# Branded promotions staging deployment — 15 October 2026

Installed commits `ccc44c8` and `f5f455b` from `codex/customer-marketing` on **test.albertinang.com**. Application `/home/albertin/test/ecommerce`, database `albertin_tcopy`. The storefront application and database in production were not deployed or migrated.

## Completed

- Verified `promotions@albertinang.com` exists in cPanel after the user completed password entry and mailbox creation. No mailbox password was read, saved in code or copied into the app.
- Deployed and read back 22 runtime files, applied the additive campaign expansion migration, and rebuilt config/views. Backed up staging base tables (6,278,653 bytes) and existing source privately under `/home/albertin/marketing-rich-release-20261015/backup` before changes.
- Deployed inline-image support with private source/environment backups under `/home/albertin/marketing-mail-ready-20261015/backup`.
- Discovered the served asset root differs from the Laravel application public directory. Copied the new editor script to `/home/albertin/test/js/marketing-editor.js`; an authenticated HTTP check returned 200 and an exact source hash match. Future deployments must publish assets to that served staging document root as well as app/public.
- Configured the dedicated marketing mailer to use the host's existing local sendmail transport with sender/reply-to `promotions@albertinang.com`. Transactional mailer settings remain unchanged.
- With explicit user approval, applied cPanel's suggested domain SPF record: `v=spf1 +ip4:82.38.198.68 +include:spf.antispamcloud.com +ip4:176.58.99.77 -all`. A refreshed cPanel report shows SPF, DKIM, DMARC and PTR Valid. This domain-level email configuration change applies to company email, including the main domain; it is not limited to the staging subdomain. DMARC remains `v=DMARC1; p=none;`.
- Removed the temporary installer cron entries. The existing Laravel scheduler remains every minute.

## Verification

21 isolated feature tests / 128 assertions passed, plus Blade compilation and whitespace checks. No full ecommerce regression was run. Existing unrelated QA changes in the checkout were left intact and were not included in feature commits.

Live Safari checks: new rich editor renders; picture selection produces an upload preview; campaign #3 saves with its uploaded image; the finished branded preview displays the official company logo, picture, message, offer button and contact footer; the Mobile button narrows the email preview. Campaign #3 remains a demonstration draft. Existing user-created campaigns were preserved.

With explicit user approval, sent exactly one `[TEST] Demo — branded promotion preview` message from promotions@albertinang.com to the signed-in admin address. Application recorded mail transport acceptance at 22:32 London time. cPanel Track Delivery reports the message accepted by `smtp.antispamcloud.com` using `smarthost_dkim` / `remote_smtp_smart_dkim`, message id `1xHT3v-0000000Bxp7-0wzC`, size 93.83 KB. This confirms acceptance by the outgoing relay, not Gmail inbox placement. The connected Gmail search did not return the message; inbox display remains for the recipient to confirm. No automatic retry or second test was sent.

Local logos and uploaded pictures are embedded inline in actual email messages; browser previews use their web URLs. Twenty-first test specifically verifies inline attachments and rejects unsafe file paths.

## Current operating state

- Internal test email action: enabled, restricted to the signed-in admin's own address.
- Customer/bulk campaign sending: disabled on this password-protected staging site. No customer campaign or subscriber opt-in was triggered.
- Staging shop, tracking and unsubscribe links still require the hosting login. Do not send real customer campaigns from this protected staging site. The application feature is ready for testing; public customer delivery requires deployment on the intended publicly accessible site or an explicitly approved staging access arrangement.
- Inbox placement, real bounce/complaint webhooks, open tracking and email-client rendering across Outlook/Apple Mail/Gmail were not verified or claimed. Click reporting counts the tracked offer button, not arbitrary rich-text links, and can include security scanners.

Evidence directory: `/Users/uche/Downloads/ANG/deployment/marketing-rich-20261015/` contains deployment reports, mailbox verification, final domain authentication, test delivery evidence, desktop/mobile campaign screenshots and the branded email preview.
