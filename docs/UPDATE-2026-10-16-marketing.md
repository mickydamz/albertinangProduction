# Update: email promotions and marketing

Date: 16 October 2026

Registered customers can now choose to receive AlbertinaNG marketing emails during registration or later through Email preferences. Registration consent is optional and unchecked by default. Signed unsubscribe links and one-click unsubscribe support let recipients withdraw consent; eligibility is checked again before queued emails are delivered.

The admin Email campaigns workspace supports rich text, up to four promotional pictures, branded layouts, inbox preview text, optional messages and a configurable Shop now destination. The offer button defaults to the centre with left and right options. Drafts can be edited, previewed and copied; campaigns can be sent, scheduled, paused, resumed or cancelled. Delivery reports show transport acceptance, failures, skipped recipients, offer clicks and unsubscribe events, with CSV export.

The workspace now displays all opted-in people in a paginated list with their names, emails, consent dates and campaign eligibility. Separate totals distinguish all subscribers from eligible customer accounts. Counts refresh every 30 seconds. Staff opt-ins remain visible but staff accounts are excluded from customer campaigns.

Promotional emails use promotions@albertinang.com as the sender and reply address. Company name, logo and contact footer resolve from Store Settings. The phone country code is configurable, and telephone/email links are clickable. Subjects are not repeated in the email body. Pictures and the default transparent company logo are embedded in sent emails, including on hosts that keep Laravel and the served document root in separate folders.

## Verified on the test site

- Campaign sending was enabled on test.albertinang.com at the owner's request.
- Following the owner's confirmation of prior consent, all 51 existing customer accounts on that test database were subscribed. This was a staging data update, not an automatic subscription rule for future registrations.
- A Halloween campaign was saved as campaign #10 with a new banner about shopping for family in Enugu. One test email was accepted by the mail transport for the explicitly requested iCloud recipient. The customer list was not queued.
- Test-site offer links continue to use https://test.albertinang.com/shop, as requested.

## Validation and rollout

30 isolated marketing feature tests passed. Coverage includes registration consent, preference changes, subscriber totals, access controls, safe rich text, images, scheduling, dispatch controls, tracking, unsubscribe handling, Settings contacts, button placement and embedded logos.

This repository update includes application code, migrations, email views, the transparent logo and documentation. It does not include mailbox credentials, private configuration, database backups, private deployment scripts or unrelated regression-workspace edits. Hosting-only configuration and campaign/customer data changes are not reproduced by a Git pull.

For another environment, apply the two marketing migrations, configure the marketing mail transport and enable flags explicitly, ensure the scheduler runs, and publish public assets to that environment's served document root. Existing defaults leave marketing sending disabled until configured. Updating main does not itself deploy or enable campaigns on the production website.
