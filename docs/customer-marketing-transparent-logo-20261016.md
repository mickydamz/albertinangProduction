# Transparent email header logo

Created public/marketing-logo-transparent.png using the built-in image editing tool. Source public/image.png is retained. Email header previews and sent emails prefer the transparent variant when using the default company logo; sent emails embed it as a PNG attachment. Uploaded custom Store Settings logos continue to use their configured asset.

Prompt: Remove ONLY the white background from this existing AlbertinaNG company logo, producing a transparent PNG. This is an exact brand asset cleanup, NOT a redesign. Preserve all original lettering exactly 'AlbertinaNG', emblem details, gold outlines, green colors, original horizontal composition and aspect ratio, sharp edges. Make the surrounding white background and white negative spaces transparent, preserving intentional emblem details. No new elements, no shadow, no checkerboard painted into image. Return the edited transparent asset.

Validation: 30 marketing tests passed. PNG corner alpha is 127 (fully transparent in GD). Deployed asset to Laravel public and served staging document root; complete Laravel email generated with in-memory transport confirms embedded logo CID and one inline part. No email sent. Temporary deployment job removed. Live screenshot in ../deployment/marketing-transparent-20261016/live-preview.png.
