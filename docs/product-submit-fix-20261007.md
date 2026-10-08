# Product submit fix — 7 October 2026

Staging: https://test.albertinang.com

Removed legacy JavaScript validation that prevented Add/Edit Product submissions without a brand, despite brand being optional. Live verification then exposed MySQL error 1048: products.moq cannot be null. Both create and update now default omitted/empty MOQ to 1 after validation. Added regression coverage for blank MOQ on create/edit and explicit MOQ preservation.

Validation: CatalogueWorkflowTest and ProductFilterNormalizationTest: 14 tests, 189 assertions passed. Live staging created unpublished product 1348 with blank brand/MOQ, then updated price from 1000 to 1200 with blank brand/MOQ; product list displayed Product updated successfully and persisted 1200, stock 1, inactive.

Deployed create.blade.php, edit.blade.php and AdminProductController.php only. Readback matched local files. Backups are in deployment/backup-20261007. Evidence: deployment/product-submit-fixed-20261007.png. Dummy product remains unpublished. Production was not modified.
