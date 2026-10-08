# AlbertinaNG branding deployment

Replaced Albertina Nigeria / Limited / Ltd in 67 application source files and removed the separately appended Limited from the shared footer. Deployed branding-20261007.zip to /home/albertin/test/ecommerce via cPanel. Updated staging store_name through admin settings to AlbertinaNG; admin About and Contact form values have no old-name references.

Verified /about, /terms, /contact, /products and /store-locator display AlbertinaNG and no Albertina Nigeria in visible text. Email subject and template source changed; no test emails sent. SMTP sender display name has not been independently verified.

CatalogueWorkflowTest: 10 tests, 131 assertions passed, including customer-page and email-subject branding. Broader suite: 146 tests, 1510 assertions, 7 failures and 1 error in payment/coupon/cancellation/review checks; full regression does not pass.

Evidence: deployment/branding-fixed-20261007.png. Production untouched.
