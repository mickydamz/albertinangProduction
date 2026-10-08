<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="AlbertinaNG – Premium Electronics">
    <meta name="keywords" content="AlbertinaNG">
    <meta name="author" content="Albertina">
    <title>@yield('title', 'AlbertinaNG')</title>

    <link rel="apple-touch-icon" href="{{ asset('/app-asset/images/logo/favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/app-asset/images/logo/favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ===================================================
           AlbertinaNG — AUTH LAYOUT DESIGN SYSTEM
           Green-first palette · Plus Jakarta Sans + DM Sans
        =================================================== */
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

            --radius:    10px;
            --radius-lg: 16px;
            --radius-xl: 24px;

            --shadow-sm: 0 1px 3px rgba(0,0,0,.06);
            --shadow-md: 0 4px 16px rgba(0,0,0,.09);
            --shadow-lg: 0 12px 40px rgba(0,0,0,.14);

            --font-head: 'Plus Jakarta Sans', sans-serif;
            --font-body: 'DM Sans', sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }

        body {
            font-family: var(--font-body);
            font-size: 15px;
            line-height: 1.55;
            color: var(--ink);
            background: var(--surf2);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a { text-decoration: none; color: inherit; }
        button { cursor: pointer; font-family: var(--font-body); border: none; background: none; }
        img { display: block; max-width: 100%; height: auto; }

        /* ===== TOP STRIP ===== */
        .top-strip {
            background: var(--g700);
            color: var(--g100);
            font-size: 12.5px;
            padding: 7px 0;
        }
        .top-strip__inner {
            max-width: 1200px; margin: 0 auto; padding: 0 20px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .top-strip__links a { color: var(--g200); margin-left: 14px; transition: color .2s; }
        .top-strip__links a:hover { color: #fff; }

        /* ===== HEADER ===== */
        .auth-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }
        .auth-header__inner {
            max-width: 1200px; margin: 0 auto; padding: 0 20px;
            height: 64px; display: flex; align-items: center; justify-content: space-between;
        }
        .logo {
            font-family: var(--font-head);
            font-size: 26px; font-weight: 800;
            color: var(--ink); letter-spacing: -.5px;
            line-height: 1; text-decoration: none;
        }
        .logo span { color: var(--g500); }
        .logo sub {
            font-size: 10px; font-weight: 400; color: var(--ink3);
            letter-spacing: .6px; margin-left: 3px; vertical-align: baseline;
        }
        .auth-header__links { display: flex; gap: 20px; align-items: center; font-size: 13.5px; }
        .auth-header__links a { color: var(--ink3); transition: color .2s; }
        .auth-header__links a:hover { color: var(--g600); }
        .auth-header__links a.btn-link {
            background: var(--g500); color: #fff;
            padding: 8px 18px; border-radius: var(--radius);
            font-weight: 600; transition: background .2s;
        }
        .auth-header__links a.btn-link:hover { background: var(--g600); }

        /* ===== MAIN AUTH AREA ===== */
        .auth-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 52px 20px;
            position: relative;
            overflow: hidden;
        }

        /* Subtle background blobs */
        .auth-main::before {
            content: '';
            position: absolute; top: -80px; left: -80px;
            width: 320px; height: 320px; border-radius: 50%;
            background: rgba(90,171,31,.07); pointer-events: none;
        }
        .auth-main::after {
            content: '';
            position: absolute; bottom: -60px; right: -60px;
            width: 240px; height: 240px; border-radius: 50%;
            background: rgba(42,90,10,.06); pointer-events: none;
        }

        /* ===== AUTH CARD ===== */
        .auth-card {
            width: 100%;
            max-width: 440px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            padding: 28px 40px 44px;
            position: relative;
            z-index: 1;
        }

        /* Card top accent line */
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--g500), var(--g400));
            border-radius: var(--radius-xl) var(--radius-xl) 0 0;
        }

        .auth-card__logo {
            text-align: center;
            margin-bottom: 10px;
        }
        .auth-card__logo img {
            height: 54px;
            width: auto;
            object-fit: contain;
            display: inline-block;
        }
        .auth-card__logo p {
            font-size: 12px; color: var(--ink3);
            margin-top: 2px; letter-spacing: .5px;
        }

        .auth-card h4 {
            font-family: var(--font-head);
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 2px;
        }
        .auth-card p.subtitle {
            font-size: 13.5px;
            color: var(--ink3);
            margin-bottom: 20px;
        }

        /* ===== FORM ELEMENTS ===== */
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--ink2);
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-size: 14px;
            font-family: var(--font-body);
            color: var(--ink);
            background: var(--surface);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            border-color: var(--g400);
            box-shadow: 0 0 0 3px rgba(90,171,31,.12);
        }
        .form-control::placeholder { color: var(--ink3); }

        /* Password wrapper */
        .pwd-wrap { position: relative; }
        .pwd-wrap .form-control { padding-right: 44px; }
        .pwd-toggle {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            color: var(--ink3); font-size: 14px;
            padding: 2px; transition: color .2s;
            background: none; border: none; cursor: pointer;
        }
        .pwd-toggle:hover { color: var(--g600); }

        /* Remember + forgot row */
        .form-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
        }
        .form-check {
            display: flex; align-items: center; gap: 8px;
        }
        .form-check-input {
            width: 16px; height: 16px; cursor: pointer;
            accent-color: var(--g500);
        }
        .form-check-label { font-size: 13px; color: var(--ink3); cursor: pointer; }
        .form-forgot { font-size: 13px; color: var(--g600); font-weight: 500; }
        .form-forgot:hover { text-decoration: underline; }

        /* Submit button */
        .btn-primary {
            width: 100%;
            padding: 12px;
            background: var(--g500);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            font-size: 15px;
            font-weight: 700;
            font-family: var(--font-head);
            cursor: pointer;
            transition: background .2s, transform .15s;
            letter-spacing: .2px;
        }
        .btn-primary:hover { background: var(--g600); transform: translateY(-1px); }
        .btn-primary:active { transform: translateY(0); }

        /* Divider */
        .auth-divider {
            display: flex; align-items: center; gap: 12px;
            margin: 22px 0;
        }
        .auth-divider::before,
        .auth-divider::after {
            content: ''; flex: 1; height: 1px; background: var(--border);
        }
        .auth-divider span { font-size: 12px; color: var(--ink3); white-space: nowrap; }

        /* Footer links */
        .auth-card__foot {
            text-align: center;
            font-size: 13.5px;
            color: var(--ink3);
        }
        .auth-card__foot a { color: var(--g600); font-weight: 600; }
        .auth-card__foot a:hover { text-decoration: underline; }

        /* ===== ALERTS ===== */
        .alert {
            border-radius: var(--radius);
            padding: 12px 14px;
            margin-bottom: 20px;
            font-size: 13px;
            border: none;
            animation: slideDown .3s ease-out;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .alert-danger {
            background: #fff5f5;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        .alert-success {
            background: var(--g50);
            border-left: 4px solid var(--g500);
            color: var(--g700);
        }
        .alert ul { margin: 6px 0 0 16px; padding: 0; }
        .alert strong { display: block; margin-bottom: 2px; }

        /* ===== FOOTER ===== */
        .auth-footer {
            background: var(--g900);
            color: rgba(255,255,255,.35);
            font-size: 12px;
            text-align: center;
            padding: 16px 20px;
        }
        .auth-footer a { color: rgba(255,255,255,.45); }
        .auth-footer a:hover { color: var(--g200); }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 520px) {
            .auth-card { padding: 32px 22px; border-radius: var(--radius-lg); }
            .auth-main { padding: 32px 14px; }
            .auth-header__links .hide-mobile { display: none; }
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- TOP STRIP --}}
    <div class="top-strip">
        <div class="top-strip__inner">
            <span>Welcome to AlbertinaNG</span>
            <div class="top-strip__links">
                <a href="/account/orders">Track Order</a>
                <a href="/">Shop</a>
            </div>
        </div>
    </div>

    {{-- AUTH HEADER --}}
    <header class="auth-header">
        <div class="auth-header__inner">
            <a href="/" aria-label="AlbertinaNG Home">
                @if(!empty($appStoreLogo))
                    <img src="{{ asset('storage/' . $appStoreLogo) }}" alt="{{ $appStoreName ?? 'AlbertinaNG' }}" style="height:44px; width:auto; object-fit:contain; display:block;">
                @else
                    <img src="{{ asset('image.png') }}" alt="{{ $appStoreName ?? 'AlbertinaNG' }}" style="height:44px; width:auto; object-fit:contain; display:block;">
                @endif
            </a>
            <nav class="auth-header__links">
                <a href="{{ route('faq') }}" class="hide-mobile">Help</a>
                <a href="{{ route('contact') }}" class="hide-mobile">Contact</a>
                @guest
                    <a href="{{ route('register') }}" class="btn-link">Create Account</a>
                @else
                    <a href="/" class="btn-link">Back to Shop</a>
                @endguest
            </nav>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main class="auth-main">
        <div class="auth-card">

            {{-- Logo inside card --}}
            <div class="auth-card__logo">
                <a href="/">
                    @if(!empty($appStoreLogo))
                        <img src="{{ asset('storage/' . $appStoreLogo) }}" alt="{{ $appStoreName ?? 'AlbertinaNG' }}" style="height:54px; width:auto; object-fit:contain; display:inline-block;">
                    @else
                        <img src="{{ asset('image.png') }}" alt="{{ $appStoreName ?? 'AlbertinaNG' }}" style="height:54px; width:auto; object-fit:contain; display:inline-block;">
                    @endif
                </a>
                <p>Nigeria's Trusted Electronics Store</p>
            </div>

            @yield('content')

        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="auth-footer">
        © {{ date('Y') }} AlbertinaNG &nbsp;·&nbsp;
        <a href="#">Privacy Policy</a> &nbsp;·&nbsp;
        <a href="#">Terms of Use</a>
    </footer>

    @stack('scripts')

    <script>
        // Password visibility toggle (available to all auth pages)
        function togglePassword(fieldId, iconId) {
            fieldId = fieldId || 'password';
            iconId  = iconId  || 'pwdEyeIcon';
            var field = document.getElementById(fieldId);
            var icon  = document.getElementById(iconId);
            if (!field) return;
            if (field.type === 'password') {
                field.type = 'text';
                icon && icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = 'password';
                icon && icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>

</body>
</html>