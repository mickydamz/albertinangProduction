<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Invoice'); ?> — <?php echo e(config('app.name')); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="icon" type="image/x-icon"            href="<?php echo e(asset('favicon/favicon.ico')); ?>">
    <link rel="icon" type="image/png" sizes="16x16"  href="<?php echo e(asset('favicon/favicon-16x16.png')); ?>">
    <link rel="icon" type="image/png" sizes="32x32"  href="<?php echo e(asset('favicon/favicon-32x32.png')); ?>">
    <link rel="apple-touch-icon" sizes="180x180"     href="<?php echo e(asset('favicon/apple-touch-icon.png')); ?>">
    <link rel="manifest"                             href="<?php echo e(asset('favicon/site.webmanifest')); ?>">

    <style>
        /* ── Design tokens ── */
        :root {
            --g50:  #f0fce8;
            --g100: #d5f5b8;
            --g200: #abeb73;
            --g400: #78c83a;
            --g500: #5aab1f;
            --g600: #3d8012;
            --g700: #2a5a0a;
            --g900: #162e05;

            --ink:     #111310;
            --ink2:    #3a3f37;
            --ink3:    #6b7268;
            --surface: #ffffff;
            --surf2:   #f7f9f5;
            --surf3:   #eef3e8;
            --border:  #dde8d4;
            --border2: #c4d8b0;

            --r:    10px;
            --r-lg: 16px;
            --r-xl: 24px;

            --sh-sm: 0 1px 3px rgba(0,0,0,.06);
            --sh-md: 0 4px 16px rgba(0,0,0,.09);
            --sh-lg: 0 12px 40px rgba(0,0,0,.14);

            --fh: 'Plus Jakarta Sans', sans-serif;
            --fb: 'DM Sans', sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: var(--fb);
            font-size: 15px;
            line-height: 1.55;
            color: var(--ink);
            background: var(--surf2);
            -webkit-font-smoothing: antialiased;
        }
        a { text-decoration: none; color: inherit; }
        img { display: block; max-width: 100%; height: auto; }

        /* ── Minimal header ── */
        .invlayout-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .invlayout-header__logo img {
            height: 34px;
            width: auto;
            object-fit: contain;
        }
        .invlayout-header__back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--ink3);
            font-family: var(--fb);
            transition: color .18s;
        }
        .invlayout-header__back:hover { color: var(--g600); }
        .invlayout-header__back i { font-size: 11px; }

        /* ── Content area ── */
        .invlayout-body {
            max-width: 900px;
            margin: 0 auto;
            padding: 32px 20px 64px;
        }

        /* ── Minimal footer ── */
        .invlayout-footer {
            border-top: 1px solid var(--border);
            background: var(--surface);
            padding: 16px 24px;
            text-align: center;
            font-size: 12px;
            color: var(--ink3);
        }

        /* ── Print ── */
        @media print {
            .invlayout-header,
            .invlayout-footer { display: none !important; }
            body { background: #fff; }
            .invlayout-body { max-width: 100%; padding: 20px 32px; margin: 0; }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }

        @page {
            margin: 0;
            size: A4;
        }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>

    <?php if (! (View::hasSection('hide_header'))): ?>
    <header class="invlayout-header">
        <a href="<?php echo e(url('/')); ?>" class="invlayout-header__logo" aria-label="<?php echo e($appStoreName ?? config('app.name')); ?> Home">
            <?php if(!empty($appStoreLogo)): ?>
                <img src="<?php echo e(asset('storage/' . $appStoreLogo)); ?>" alt="<?php echo e($appStoreName ?? config('app.name')); ?>">
            <?php else: ?>
                <img src="<?php echo e(asset('image.png')); ?>" alt="<?php echo e($appStoreName ?? config('app.name')); ?>">
            <?php endif; ?>
        </a>

        <?php if (! empty(trim($__env->yieldContent('header_actions')))): ?>
            <?php echo $__env->yieldContent('header_actions'); ?>
        <?php else: ?>
            <a href="<?php echo e(route('account.orders')); ?>" class="invlayout-header__back">
                <i class="fas fa-arrow-left"></i> Back to orders
            </a>
        <?php endif; ?>
    </header>
    <?php endif; ?>

    <main class="invlayout-body">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php if (! (View::hasSection('hide_header'))): ?>
    <footer class="invlayout-footer">
        © <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. All rights reserved.
    </footer>
    <?php endif; ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>
</html><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/layouts/invoicelayout.blade.php ENDPATH**/ ?>