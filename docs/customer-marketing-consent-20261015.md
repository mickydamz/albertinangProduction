# Registration consent, subscriber visibility and button placement

Registration now has an optional, unchecked email promotions checkbox. Only validated explicit consent sets marketing_subscribed_at. Existing preferences and unsubscribe controls remain authoritative.

Investigation on staging found Admin A had saved consent; the old dashboard counted only role=user accounts and therefore hid this staff opt-in. Dashboard now shows total subscribed people and eligible customer totals separately, with a paginated name/email/eligibility/consent-date list for all opted-in accounts. Customer sending still excludes staff. Counts refresh every 30 seconds and when returning to the tab; the subscriber list has an explicit refresh action. Dashboard/count responses disable caching.

Offer button position defaults to centre and can be changed to left or right in each draft. Position is validated and stored in draft branding metadata, so no schema change is required. Existing drafts default to centre. Email rendering uses presentation tables and alignment attributes.

Validation: 29 isolated marketing feature tests passed, including opt-in versus no opt-in registration persistence, live counter queries following preference changes, staff/customer separation, admin access enforcement, and all button positions. No campaign was sent.
