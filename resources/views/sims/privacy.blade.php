@extends('layouts.simslayout')

@section('title', 'Privacy Policy – Albertina Nigeria')

@section('content')
<div class="main-wrap">

    {{-- Breadcrumb --}}
    <nav style="display:flex;flex-wrap:wrap;gap:4px;align-items:center;font-size:13px;color:var(--ink3);margin-bottom:20px;padding:10px 0;border-bottom:1px solid var(--border);">
        <a href="{{ url('/') }}" style="color:var(--ink3);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='var(--g600)'" onmouseout="this.style.color='var(--ink3)'">Home</a>
        <i class="fas fa-chevron-right" style="font-size:9px;color:var(--border2);"></i>
        <span style="color:var(--ink);font-weight:600;">Privacy Policy</span>
    </nav>

    {{-- Page Header --}}
    <div style="background:linear-gradient(135deg,var(--g700) 0%,var(--g600) 100%);border-radius:0;padding:36px 40px;margin-bottom:28px;position:relative;overflow:hidden;">
        <div style="position:absolute;right:-40px;top:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.06);"></div>
        <div style="position:absolute;right:60px;bottom:-60px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,.04);"></div>
        <div style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.12);border-radius:20px;padding:5px 14px;font-size:12px;color:rgba(255,255,255,.8);font-weight:600;margin-bottom:12px;">
                <i class="fas fa-shield-alt"></i> Legal
            </div>
            <h1 style="font-family:var(--font-head);font-size:2rem;font-weight:800;color:#fff;margin-bottom:8px;letter-spacing:-.3px;">Privacy Policy</h1>
            <p style="color:rgba(255,255,255,.65);font-size:14px;max-width:520px;line-height:1.6;">
                We are committed to protecting your privacy. This policy explains how we collect, use, and safeguard your information.
            </p>
            <p style="color:rgba(255,255,255,.4);font-size:12px;margin-top:10px;">
                <i class="fas fa-calendar-alt" style="margin-right:4px;"></i> Last updated: January 2025
            </p>
        </div>
    </div>

    {{-- Content Grid --}}
    <div style="display:grid;grid-template-columns:220px 1fr;gap:24px;align-items:flex-start;">

        {{-- Table of Contents (sticky sidebar) --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:0;padding:20px;position:sticky;top:90px;">
            <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--ink3);margin-bottom:12px;">Contents</p>
            <nav style="display:flex;flex-direction:column;gap:4px;">
                @foreach([
                    ['#info-collect',   'fa-database',        'Information We Collect'],
                    ['#how-we-use',     'fa-cogs',            'How We Use It'],
                    ['#sharing',        'fa-share-alt',       'Sharing Your Info'],
                    ['#security',       'fa-lock',            'Data Security'],
                    ['#your-rights',    'fa-user-check',      'Your Rights'],
                    ['#cookies',        'fa-cookie-bite',     'Cookies & Tracking'],
                    ['#contact',        'fa-headset',         'Contact Us'],
                ] as [$href, $icon, $label])
                <a href="{{ $href }}" style="display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:var(--radius);font-size:13px;color:var(--ink3);text-decoration:none;transition:all .18s;" onmouseover="this.style.background='var(--g50)';this.style.color='var(--g600)';" onmouseout="this.style.background='';this.style.color='var(--ink3)';">
                    <i class="fas {{ $icon }}" style="width:14px;font-size:11px;color:var(--g500);flex-shrink:0;"></i>
                    {{ $label }}
                </a>
                @endforeach
            </nav>
        </div>

        {{-- Main Policy Sections --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            @php
            $sections = [
                [
                    'id'    => 'info-collect',
                    'icon'  => 'fa-database',
                    'num'   => '01',
                    'title' => 'Information We Collect',
                    'body'  => 'We may collect personal information such as your name, email address, phone number, shipping address, and payment details when you place an order, register an account, or contact us. We also collect non-personal information like browsing behaviour, IP address, and device information via cookies and similar technologies.',
                ],
                [
                    'id'    => 'how-we-use',
                    'icon'  => 'fa-cogs',
                    'num'   => '02',
                    'title' => 'How We Use Your Information',
                    'body'  => 'Your information is used to process orders, improve our services, personalise your experience, and communicate with you about promotions or updates. We may also use data for analytics to understand customer preferences and enhance our website.',
                ],
                [
                    'id'    => 'sharing',
                    'icon'  => 'fa-share-alt',
                    'num'   => '03',
                    'title' => 'Sharing Your Information',
                    'body'  => 'We may share your information with trusted third parties, such as payment processors and shipping providers, to fulfil orders. We do not sell your personal information to third parties for marketing purposes without your consent.',
                ],
                [
                    'id'    => 'security',
                    'icon'  => 'fa-lock',
                    'num'   => '04',
                    'title' => 'Data Security',
                    'body'  => 'We implement industry-standard security measures to protect your information. However, no method of transmission over the internet is 100% secure, and we cannot guarantee absolute security.',
                ],
                [
                    'id'    => 'your-rights',
                    'icon'  => 'fa-user-check',
                    'num'   => '05',
                    'title' => 'Your Rights',
                    'body'  => 'You have the right to access, correct, or delete your personal information. You may also opt out of marketing communications at any time. Contact us at info@albertinang.com to exercise these rights.',
                ],
                [
                    'id'    => 'cookies',
                    'icon'  => 'fa-cookie-bite',
                    'num'   => '06',
                    'title' => 'Cookies and Tracking',
                    'body'  => 'We use cookies to enhance your browsing experience. You can manage cookie preferences through your browser settings. Disabling cookies may affect website functionality.',
                ],
            ];
            @endphp

            @foreach($sections as $section)
            <div id="{{ $section['id'] }}" style="background:var(--surface);border-bottom:1px solid var(--border);border-radius:0;padding:28px 0 24px;scroll-margin-top:90px;">
                <div style="display:flex;align-items:flex-start;gap:16px;">
                    <div style="flex-shrink:0;width:44px;height:44px;border-radius:12px;background:var(--g50);border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;">
                        <i class="fas {{ $section['icon'] }}" style="font-size:16px;color:var(--g600);"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                            <span style="font-size:11px;font-weight:700;color:var(--g500);letter-spacing:.5px;">{{ $section['num'] }}</span>
                            <h2 style="font-family:var(--font-head);font-size:1.05rem;font-weight:700;color:var(--ink);margin:0;">{{ $section['title'] }}</h2>
                        </div>
                        <p style="font-size:14px;color:var(--ink3);line-height:1.75;margin:0;">{{ $section['body'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Contact Section --}}
            <div id="contact" style="background:var(--g50);border-radius:0;padding:28px 0;scroll-margin-top:90px;">
                <div style="display:flex;align-items:flex-start;gap:16px;">
                    <div style="flex-shrink:0;width:44px;height:44px;border-radius:12px;background:var(--g500);display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-headset" style="font-size:16px;color:#fff;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                            <span style="font-size:11px;font-weight:700;color:var(--g500);letter-spacing:.5px;">07</span>
                            <h2 style="font-family:var(--font-head);font-size:1.05rem;font-weight:700;color:var(--ink);margin:0;">Contact Us</h2>
                        </div>
                        <p style="font-size:14px;color:var(--ink3);line-height:1.75;margin-bottom:16px;">
                            If you have questions about this Privacy Policy, please get in touch with us:
                        </p>
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            <a href="mailto:Info@Albertinang.com" style="display:inline-flex;align-items:center;gap:10px;padding:11px 16px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);font-size:13.5px;color:var(--g600);font-weight:500;text-decoration:none;transition:all .2s;width:fit-content;" onmouseover="this.style.borderColor='var(--g400)';this.style.background='var(--surface)';" onmouseout="this.style.borderColor='var(--border)';this.style.background='var(--surface)';">
                                <i class="fas fa-envelope" style="color:var(--g500);"></i>
                                Info@Albertinang.com
                            </a>
                            <a href="tel:08064066170" style="display:inline-flex;align-items:center;gap:10px;padding:11px 16px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);font-size:13.5px;color:var(--g600);font-weight:500;text-decoration:none;transition:all .2s;width:fit-content;" onmouseover="this.style.borderColor='var(--g400)';" onmouseout="this.style.borderColor='var(--border)';">
                                <i class="fas fa-phone" style="color:var(--g500);"></i>
                                08064066170
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<style>
@media (max-width: 768px) {
    .main-wrap > div[style*="grid-template-columns:220px"] {
        display: block !important;
    }
    .main-wrap > div[style*="grid-template-columns:220px"] > div:first-child {
        display: none !important;
    }
}
</style>
@endsection