# Test Coverage

A living map of what the automated test suite covers. **406 tests passing.**
Run everything with `php artisan test` (tests use the `albertina3_test` DB from
`.env.testing` — run new migrations with `php artisan migrate --env=testing`).

---

## 🛍️ Customer (user) side

| Area | Test file | What's covered |
|------|-----------|----------------|
| Public pages | `PublicPagesSmokeTest` | home, about, terms, privacy, faq, blog, store-locator, store-locations, contact, cart all render (200) |
| Products index | `ProductsIndexPageTest` | browse-all (active only), brand-filter redirect |
| Product detail | `ProductShowPageTest` | renders; missing product → 404 |
| Category page | `CategoryPageTest` | category/subcategory resolution, active-only, hyphen slugs, 404; **filters**: brand (single/multi), price (min/max/range), availability; **sorts**: price asc/desc, newest; pagination (20/pg) |
| Brand page | `BrandPageTest` | brand's active products, hides inactive, 404 unknown brand |
| Search | `SearchPageTest` | name/brand/category match, ranking, wildcards, filters, suggestions |
| Reviews | `ReviewPurchaseGateTest` | purchase-gate (all qualifying statuses incl. `paid`), one-per-user, guest/non-buyer blocked |
| Reviews (edit) | `ReviewUpdateOwnershipTest` | owner/admin can edit, non-owner 403, guest 401, 404 |
| Currency | `CurrencySwitchTest` | set active currency, reject unknown/inactive, list active |
| Contact form | `ContactFormTest` | send admin + auto-reply, validation, Turnstile CAPTCHA, honeypot, dedupe |
| Coupons | `CouponRedemptionTest` | percent/fixed maths + caps, expiry, inactive, usage limit, min-spend, per-user, case-insensitive |
| Checkout (delivery) | `CheckoutDeliveryTest`, `CheckoutTruckDeliveryTest` | delivery fee, truck flags |
| Installation opts | `InstallationOptionsTest` | per-product options API |
| Order fulfilment | `CheckoutConfirmOrderTest`, `PaystackOrderFulfillmentTest`, `StripeOrderServiceTest`, `PaystackWebhookTest` | verify + create, idempotency, underpayment/currency/fraud guards, coupon redemption on fulfilment, webhook signature |
| Order emails | `OrderEmailPickupTest`, `OrderEmailDeliveryAddressTest`, `OrderStatusEmailTest` | confirmation + per-status emails, review-request on completion |
| Order lifecycle | `OrderUserLifecycleTest`, `OrderSelfCancelRefundTest`, `OrderCancellationRefundTest`, `OrderShippingAddressSyncTest` | list, self-cancel/return, refund |
| Order invoice | *(guard in `OrderController::authorizeOrderOwner`)* | owner-only (403) — used by lifecycle tests |
| Email verification | `EmailVerificationEnforcementTest` | gated by admin setting; registration, enforcement, signed verify link |
| Support tickets | `SupportTicketTest` | create, owner-scoped list, reply reopens + emails, **ownership guard (403)**, guest→login |
| Account | `AccountManagementTest` | profile update (+ email uniqueness), **change password** (current-password check, min-8/confirm) |
| Two-factor | `TwoFactorSettingsTest` | enable (emails code), disable (clears code), page render |
| Geo API | `GeoApiTest` | `/api/states`, `/api/locations` (+state/pickup filters), `/api/pickup-points` (validation, inactive → 404, scoping) |
| Auth | `AuthenticationTest`, `RegistrationTest`, `PasswordResetTest`, `PasswordUpdateTest`, `PasswordConfirmationTest`, `AuthPagesTest` | login/register/password/2FA page flows |
| Manager | `ManagerLoginTest` | login redirect + area access guard |

## 🛠️ Admin side

| Area | Test file | What's covered |
|------|-----------|----------------|
| Access control | `AdminAccessControlTest` | guest→login, non-admin→403, admin→200, writes blocked for non-admin |
| Index pages | `AdminIndexPagesSmokeTest` | brands, states, locations, pickup-points, store-locations, returns, cancellations, coupons, categories, settings, products, reviews, paystack-txns, shipping, invoice-settings render |
| Nav pages | `AdminNavPagesSmokeTest` | dashboard, users, tags, sizes, colors, banners, audit, about, contact, faqs, payment-methods, cities, transactions, currencies render |
| Taxonomy | `AdminTaxonomyCrudTest` | tags/sizes/colors create + delete, unique names |
| Content pages | `AdminContentPageTest` | About + Contact page content (stored as Settings), email validation |
| Logistics | `AdminLogisticsCrudTest` | states (+toggle), locations (+toggle), pickup-points, store-locations, cities — create/delete/validation |
| Commerce config | `AdminCommerceConfigCrudTest` | coupons CRUD (+%≤100), currencies (create/delete, base protected), payment-methods CRUD |
| Settings | `AdminEmailVerificationSettingTest` | settings PUT persists (email-verification toggle on/off) |
| Settings/Shipping | `AdminSettingsShippingTest` | store_name required validation, per-location shipping-cost update |
| Banners | `AdminBannerCrudTest` | create (image upload), validation, delete |
| Dashboard data | `AdminDashboardDataTest` | all 6 analytics JSON endpoints respond; non-admin 403 |
| Show pages | `AdminShowPagesTest` | product + user show pages, non-admin 403, 404 |
| Users CRUD | `AdminUserCrudTest` | create (+validation/unique email), update, delete |
| Products | `AdminProductManageTest` | create (+validation), toggle-active, delete, bulk-markup |
| Catalog/content | `AdminContentCrudTest` | categories CRUD, brands (create/slug/unique/delete), FAQs CRUD |
| Orders | `AdminOrderInstallationTest`, `AdminOrderDeliveryAddressTest`, `OrderStatusEmailTest`, `OrderCancellationRefundTest` | edit installation/delivery, status emails, refunds |
| Returns/Cancellations | `AdminCreateReturnCancellationTest`, `AdminStandaloneReturnCancellationTest` | log + review returns/cancellations |
| Support tickets | `AdminTicketManageTest` | reply (reopens + emails owner), status update (+validation) |
| Reviews | `AdminReviewListingTest`, `AdminReviewPickerTest` | listing + product picker |
| Coupons | `AdminCouponListingTest` | listing |
| Pricing tools | `AdminMarkupTest`, `AdminDiscountTest`, `AdminWeightTest` | markup / discount / weight managers |
| Invoices | `AdminInvoiceSettingsTest`, `InvoiceLayoutTest` | invoice settings + layout |
| Currencies | `AdminCurrencyFormTest` | currency form |
| Delivery | `AdminDeliveryListingTest` | delivery listing |
| Users column | `AdminUsersVerifiedColumnTest` | verified column |
| Settings | `AdminEmailVerificationSettingTest` | email-verification toggle (backfills existing users) |
| Brand→manager | `ManagerAssignmentTest` | brand assignment, per-manager product scoping |

---

## Known gaps (intentionally untested)
- **Server cart routes** (`/cart/add|increase|decrease|remove`) — legacy `Cart` model; the real cart is client-side `localStorage`.
- **Live chat** (`ChatController`), **transactions list**, **KYC/ID verification** — secondary/feature pages, not money/security-critical.
- **Suppliers & affiliates** — excluded by request (out of scope).
- **Admin Transactions CRUD** — skipped: broken at the schema level (missing `escrow_amount`/`status` columns), see `todo list.md`.
- **Audit trail** — read-only; its page render is covered.

See `todo list.md` for outstanding **code** fixes (not test gaps).
