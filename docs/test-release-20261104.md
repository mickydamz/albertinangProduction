# Current test release - 4 November 2026

Source snapshot for test.albertinang.com through the verified 3 November 22:16 UTC deposit-navigation update. Includes manager workflows, catalogue editing, product colours, state browsing, collection transfers, per-unit transfer plus area-delivery charges, checkout store selection and race handling, admin transfer-price editing, store-locator navigation, and retired deposit links with authenticated redirects.

Reconciled in the existing IntelliJ site-source project against release 7eefee57a234fd941fa57eaac94f723af7c5cc4c and subsequent test deployment manifests. The final hosted deposit manifest was downloaded through cPanel on 4 November; live settings and supplier templates match its final SHA256 hashes, preserving hosted differences absent from the local draft. The hosted scheduler also matches the release baseline.

Run pending migrations before using this source. Credentials, environment instances, database contents, uploads, backups, deployment installers, generated caches and reports are excluded. The separate local BDD and IDE configuration restructuring is excluded. Regression tests covering the deployed application changes are included.

This Git commit publishes source only; it does not merge or deploy to any hosting environment.
