# Checkout collection and delivery mobile

Checkout requires a Nigerian mobile number before either payment flow starts. The bold label is “Collection/ delivery number”, with “Nigerian mobile numbers only.” beneath it. Local Nigerian mobile numbers and +234 equivalents normalize to +234. Validation checks number format, not ownership or reachability. Checkout still requires sign-in.

The server validates and stores delivery_mobile in the pending checkout. Paystack and Stripe copy that saved contact onto the order. Admin > Orders > open order shows Delivery mobile directly in Order Summary, above Payment Method. Historical orders without a contact show Not provided. Profile phone numbers are not substituted.

The migration adds nullable delivery_mobile columns to pending_checkouts and orders, preserving historical records and reconciliation of previously paid checkouts. The obsolete browser-amount initiation aliases return 422; pricing and gateway payment verification remain server controlled.

Successful callbacks and duplicate retries also recover a missing order contact only from the exact fulfilled checkout with a matching reference, customer and total. Recovery accepts a valid Nigerian mobile and never overwrites an existing contact or replays payment.

Validation: full backend regression passed 193 tests / 2064 assertions. Coverage includes invalid and foreign numbers, normalized persistence, both payment gateways, admin display, duplicate payment handling and safe contact recovery. The earlier focused delivery, truck delivery, installation, shipping-address and Stripe feature suite passed 46 tests / 142 assertions. PHP syntax, Blade compilation and git diff checks passed. Local gateway tests use synthetic responses and isolated SQLite databases.

Deployed to staging only. See checkout-delivery-mobile-staging-20261021.md for the verified deployment and its limits.
