# Email logo attachment repair

The staging Settings logo resolves to https://test.albertinang.com/image.png. Server verification confirmed Laravel public/image.png was absent while the served sibling document-root image.png existed. The previous resolver fell back to the password-protected website URL, so email clients could not retrieve the logo.

MarketingAssets now checks both Laravel's public folder and the hosting document root for the default PNG, validates it as an image, and embeds the local file. Store settings and the logo itself are unchanged. Added a regression test reproducing the split hosting folders. All 30 isolated marketing feature tests pass.

Deployed to test.albertinang.com. Built a complete Laravel email using an in-memory array transport, with no external delivery. Server report confirms logo CID and one inline image; downloaded MIME confirms image/png and the HTML/attachment CID match. Private backup retained and temporary job removed. Existing received emails retain their original content; subsequent emails use the repair.
