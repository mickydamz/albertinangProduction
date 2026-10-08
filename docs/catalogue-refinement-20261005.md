# Catalogue refinement — 5 October 2026

Local changes on codex/ecommerce-staging-improvements, ready for review. No hosting deployment or GitHub push was performed for this catalogue update.

## Product journey

1. Add product: name, short description, category/subcategory, optional brand, base price, available stock and images are on one essentials screen.
2. Rich descriptions, variants/tags, specifications and installation options remain optional. Existing data and editors are retained.
3. New products default to unpublished. Saving a new product opens its edit screen for review, with a message indicating whether it is published.
4. Enable visibility to publish. The existing stock and checkout behaviour remains in place.
5. Edit listing and inventory without altering previous order item prices. Deleting a product referenced by an order now unpublishes it, preserving history.

Server validation requires a positive price, nonnegative stock and a subcategory belonging to the selected category. JPEG, PNG and WebP uploads allow up to ten newly uploaded files at 5 MB each. Existing image records are retained.

## Retired features

Removed Supplier and Affiliate navigation and endpoint registration, including supplier reviews and supplier analytics. Removed referral-code registration/account controls and stopped assigning referral codes to new customers. New admin user roles are admin, manager and user. Existing supplier/affiliate accounts fall back to the customer dashboard; historical database records are retained. Inactive controller/model files and migrations remain for historical compatibility; they have no registered feature endpoints.

## Admin filtering

Explicit Apply/Clear buttons replace immediate filter submissions. Search, category, brand, visibility, stock level, minimum/maximum base price and page size can be combined. Low stock means 1–5 units. Invalid ranges display validation errors. Sorting and pagination retain filters, including unpublished status=0. The result count is visible.

## Customer filtering

Products, brand and category pages share keyboard-accessible filter headings and option search for groups with six or more values. Instructions explain OR within a group and AND across groups. Minimum/maximum inputs have accessible labels and reject reversed or negative ranges before navigation. Existing selected-filter chips and mobile apply controls remain.

Removed the single-brand redirect that discarded other selections. Search results now honour explicit price/newest/rating sorting, and Best match is displayed as the default for text search. Pre-order selections have an active filter chip on search/product results. Customer price filtering continues to use the effective selling price; admin ranges are explicitly labelled base price.

## Verification

Final focused suite passed: 47 tests, 579 assertions (catalogue, return workflow, refund communication, order history and fulfilment boundaries). All ten modified PHP application files passed syntax checks. This is a focused verification, not a full ecommerce regression run.

Seven new catalogue scenarios cover create/edit validation, unpublished creation, combined admin filters and preserved sorting parameters, purchased-product removal, retired endpoint access, combined customer brand/stock/price filters, authorisation, historical order prices, hidden products and image upload limits. Run the test class with the existing phpunit.regression.xml configuration, which enforces an isolated SQLite in-memory database.

Browser checks on the local app confirmed the essentials/optional-section navigation, removed admin menu entries, combined customer filters and explicit invalid-price feedback. Local demo data contains no categories or brands, so full product creation was verified using the database regression fixtures rather than adding catalogue records to that preview database.

Screenshots are saved in ../deployment/catalogue-editor-20261005.png, ../deployment/admin-product-filters-20261005.png and ../deployment/customer-filters-20261005.png.

## Duplicate filter repair

Added ProductFilterNormalizer to produce a read-only filter projection. Normalises key case/spacing and common key aliases, 4K/Full HD resolution descriptions, numeric port counts, Hz values, inch values and unambiguous standard colours. Port version/support details remain in the original specifications. Mixed colours, OS versions and other qualified values are preserved. The old delimiter-based grouping no longer drops qualifiers.

Legacy option URLs are normalised through the same matcher. Colour relationship selections feed the same colour axis as legacy custom specifications. Labels are deduplicated per product before baseline and live counts, preventing a product with two equivalent values from being counted twice. No database specification rewrite or deletion was performed.

Sizes are labelled Legacy sizes with category-specific specification guidance; tags are labelled Search keywords. Existing associations are retained. Fixed the size-update controller's incorrect variable reference. Verified the local sizes page and saved deployment/catalogue-filter-guidance-20261005.png.

Final focused suite: 50 tests, 616 assertions passed, including duplicate counts, legacy links, original specification preservation, colour convergence, distinct OS versions and size editing. These changes were deployed to test.albertinang.com on 5 October 2026.

## Staging deployment verification

Deployed the 30-file catalogue-staging-20261005.zip package to /home/albertin/test/ecommerce through cPanel. Saved the 26 existing files before deployment in deployment/backup-20261005/catalogue-source-before.json. Read all 30 files back through cPanel and verified their SHA-256 hashes against the package manifest (30/30 matched); evidence: deployment/catalogue-staging-20261005-readback.json. No database migration, images or environment files were included.

Live browser checks confirmed the simpler product editor, unpublished default, legacy size guidance, admin unpublished filter (3 results) and preserved status=0 sorting links. Supplier and affiliate admin endpoints both return 404. TV search preserves distinct OS versions and combines equivalent resolution, refresh-rate and USB-port values. Selecting 4K Ultra HD (33) returns exactly 33 products with an active filter chip. Saved staging editor and customer filter screenshots in deployment/catalogue-staging-editor-20261005.png and deployment/catalogue-staging-filters-20261005.png.

The initial cPanel extraction stalled; retrying with the full absolute destination succeeded. The package also includes the pending return inspection validation views, verified by file readback. No live orders, products or refunds were created or changed during deployment verification. Focused local tests passed 50 tests and 616 assertions; this deployment check was not a full payment/lifecycle regression.

## Sound and Vision refinement

On 5 October 2026, added Product type ahead of Brand, Price and Availability on the parent category. Types use assigned subcategories, with clear product-name classification for older sound/vision listings without that relationship. The projection does not rewrite product records or original specifications. Chosen types scope specification options while all type choices remain available. Screen sizes are pinned and single-product sizes remain selectable. Normalised annotated inch values, 60Hz Base Architecture and singular port labels; unknown USB counts display Available (count not specified). OS versions remain separate.

Focused tests: 51 tests, 633 assertions passed, including legacy type projection and unchanged original specifications. Deployed three files using sound-vision-refinement-v2-20261005.zip, backed up in deployment/backup-20261005/sound-vision-before.json. All three deployed files matched their local SHA-256 hashes. Live checks: types cover all 70 products (38 TVs, 13 soundbars, 12 home theatres, 7 speakers); soundbar selection returns 13 and hides TV facets; TVs + 55 inches returns 5, matching the count. Screenshot: deployment/sound-vision-refined-20261005.png. No catalogue records or transactions were modified.

## Catalogue-wide filters and prevention

Extended controlled Product type to all categories, All Products, search and brand listings. Uses assigned subcategory, category fallback for unassigned legacy records, and the existing sound/vision title fallback. Free-text Product Type cannot override this facet. Specification ranking follows selected types on all three controller flows. Known type-specific fields (TV screen size, washer load capacity, freezer capacity, AC BTU and generator output) remain distinct. Numeric unit spellings normalise without converting different units or rewriting stored descriptions. Mandatory-axis normalisation also handles legacy key names such as Washing Capacity.

Publishing through admin create/edit or the admin/manager publish switches requires a valid subcategory when the selected category has subcategories. Drafts are allowed without one. Existing published records are not bulk-changed. Product editor guidance explains the type source and specification units.

Regression coverage expanded across category, all-products, search and brand paths, mixed capacity units, legacy specification keys, misleading free-text types, drafts and publication checks. Focused suite: 53 tests, 672 assertions passed. To rerun the catalogue checks locally, use phpunit.regression.xml with an isolated in-memory SQLite database and filter ProductFilterNormalizationTest|CatalogueWorkflowTest.

Deployed to test.albertinang.com only on 5 October 2026. Six changed application/view files are recorded in deployment/catalogue-filter-guards-20261005-files.json. Backups: deployment/backup-20261005/catalogue-guards-before.json. Uploaded the base package and two small unit fixes; readback confirmed exact local contents. Live All Products shows Product type first, Washing Machines returns 41, and combined Washing Machines + 10 kg returns 5, matching the merged option count. Screenshot: deployment/catalogue-all-filter-guards-20261005.png. No live product records, orders or payments were changed. This improves prevention but does not enforce every possible free-text specification vocabulary; new units/aliases should be covered by additional normaliser tests.
