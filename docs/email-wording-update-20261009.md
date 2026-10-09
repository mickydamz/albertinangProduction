# Email wording and staging deployment

Deployed to https://test.albertinang.com. Refund headers show only the provider-state title (for example, Refund processed); the provider explanation appears once in the body. Refund and return detail panels use neutral headings rather than repeating the notification title. Removed repeated header explanations from order confirmation, processing, shipping, delivery, collection, completion, cancellation, review invitations and support replies. The company email layout and refund settlement distinctions remain intact.

Validation: RefundCommunicationTest and ReturnWorkflowTest passed: 34 tests, 476 assertions. Regression checks require one refund explanation, title-only notification headers and preserved return instructions. The portable QA copies of these tests are updated. All 15 staging template sources were read back and matched local files after deployment. Rollback copies are retained outside the repository under deployment/email-backup-20261009. No database changes were required.

This updates future emails; previously received messages are unchanged. This focused check does not claim a fresh full regression or fresh inbox delivery verification. Five historical refund settlement records remain unresolved as recorded above.
