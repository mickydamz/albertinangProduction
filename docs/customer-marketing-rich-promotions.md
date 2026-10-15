# Branded promotions workspace

The promotions feature now supports:

- A rich-text editor with headings, bold/italic/underline, lists, alignment, text colour and HTTPS links. Pasted content is cleaned, and server-side allowlisting rebuilds safe HTML. A plain-text email alternative is included.
- Up to four JPG/PNG pictures per campaign, descriptive alternative text, upload previews and picture removal. Pictures are public promotional assets; maximum 3 MB and 4,000 pixels per dimension. Previously referenced assets are retained so copied campaigns remain intact.
- Classic, Sale and New arrivals branded layouts. The company logo comes from store settings (falling back to the existing official image.png), with a saved company footer and promotions@albertinang.com sender/reply address.
- Inbox preview text, a configurable offer button, desktop/mobile preview widths, and an admin-address-only test action. Sending a test does not add customer delivery records.
- All opted-in customers, opt-ins from the last 30 days, or selected opted-in customer accounts. No account is automatically subscribed. Audience membership is captured when queued; consent, role and current email are checked again before each send.
- Send now or schedule using Europe/London time, pause/resume, cancel pending recipients, copy a campaign to a reusable new draft, and campaign status filters. Queued content is immutable. A current in-flight email can finish after pause/cancel.
- Delivery status history, offer-button click counts, campaign unsubscribe attribution and CSV reports. Signed tracking redirects cannot be used as arbitrary redirects. Click figures may include mail security scanners. No open-tracking pixel is used. Inbox delivery, bounces and spam complaints require provider integration and are not claimed by these reports.
- Signed unsubscribe confirmation links plus mail-provider one-click unsubscribe headers. The one-click endpoint requires a signed action-specific URL and the expected provider POST body; only this endpoint is exempt from CSRF tokens.

## Sender setup

Create promotions@albertinang.com in cPanel. New-password entry and submission must be completed by the user in the browser. Do not put the mailbox password into source control or send it in chat.

Promotions use the dedicated `marketing` mailer, configured separately from order/account messages. SMTP configuration variables are in .env.example. On the hosting server, an administrator can alternatively choose the existing local sendmail transport with `MARKETING_MAIL_TRANSPORT=sendmail`; this does not require copying mailbox credentials into the application. Confirm the hosting provider permits this sender and that SPF/DKIM are healthy before enabling delivery.

`MARKETING_TEST_EMAIL_ENABLED` controls the internal test button; `MARKETING_EMAIL_ENABLED` controls customer campaign sending. Both default to false. Log/array transports are rejected for actual dispatch. The default batch cap is 50 recipients per scheduler invocation; adjust to the hosting provider's actual limits. The existing Laravel scheduler processes due campaigns every minute. Failures and uncertain/in-flight records are never automatically retried.

## Validation

20 isolated feature tests / 125 assertions passed. These cover admin access, input validation, safe HTML, picture upload/removal/type restrictions, sender and reply address, plain-text fallback, audience filtering, future/due scheduling, pause/resume/cancel, duplication, consent suppression, signed clicks and unsubscribe, CSV response, batch limits and failure behaviour. Blade compilation and whitespace checks passed. Real SMTP delivery is not implied by fake-mail tests. Staging visual verification and deployment status are recorded separately.
