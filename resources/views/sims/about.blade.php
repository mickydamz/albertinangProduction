@extends('layouts.simslayout')

@section('title', 'About Us — AlbertinaNG')

@section('content')
@php
use App\Models\Setting;
$aHeroSub   = Setting::get('about_hero_subtitle', 'Your trusted partner for quality electronics and home appliances in Nigeria — proudly serving customers since 1991.');
$aWhoP1     = Setting::get('about_who_p1',        'AlbertinaNG is a leading electronics and appliances retailer, proudly serving Nigeria since our establishment in 1991. With showrooms in Enugu, Lagos, and Awka, we specialise in offering a wide range of high-quality products, including Samsung, LG, Thermocool, and innovative solar solutions. Our mission is to provide reliable, affordable, and cutting-edge electronics for both residential and commercial needs, ensuring customer satisfaction at every step.');
$aWhoP2     = Setting::get('about_who_p2',        "From our headquarters at 17-18 Zik's Avenue, Uwani, Enugu, we have built a reputation for competitive pricing, knowledgeable staff, and exceptional after-sales service. Whether you're upgrading your home with the latest appliances or seeking energy-efficient solar solutions, Albertina is your go-to destination.");
$aStoryP1   = Setting::get('about_story_p1',      "Founded in 1991, AlbertinaNG has grown from a single store in Enugu to a trusted name across Nigeria, with additional locations at 26 Lawanson Road, Surulere, Lagos, and along the Enugu-Onitsha Expressway in Awka. Our journey is driven by a commitment to quality, innovation, and customer-centric service.");
$aYears     = Setting::get('about_years',     '30+');
$aShowrooms = Setting::get('about_showrooms', '3');
$aFeedback  = Setting::get('about_feedback',  '99%');
$aEstYear   = Setting::get('about_est_year',  '1991');
@endphp
<div class="main-wrap">

    {{-- Hero Header --}}
    <div style="
        background: linear-gradient(135deg, var(--g700) 0%, var(--g600) 100%);
        border-radius: 0;
        padding: 56px 52px;
        margin-bottom: 36px;
        position: relative;
        overflow: hidden;
        color: #fff;
        text-align: center;
    ">
        {{-- Decorative blobs --}}
        <div style="position:absolute;top:-60px;right:-60px;width:260px;height:260px;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-50px;left:-40px;width:180px;height:180px;border-radius:50%;background:rgba(90,171,31,.12);pointer-events:none;"></div>
        <div style="position:absolute;top:30px;left:80px;width:90px;height:90px;border-radius:50%;background:rgba(171,235,115,.07);pointer-events:none;"></div>

        <div style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:20px;padding:6px 14px;margin-bottom:18px;">
                <i class="fas fa-store" style="font-size:12px;color:var(--g200);"></i>
                <span style="font-size:12px;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;color:var(--g200);">Est. 1991</span>
            </div>
            <h1 style="font-family:var(--font-head);font-size:2.4rem;font-weight:800;margin-bottom:14px;line-height:1.15;">About AlbertinaNG</h1>
            <p style="font-size:15px;color:rgba(255,255,255,.7);max-width:580px;margin:0 auto;line-height:1.7;">
                {{ $aHeroSub }}
            </p>
        </div>
    </div>

    {{-- Who We Are --}}
    <section class="about-section" style="animation-delay: 0s;">
        <div class="about-section__icon">
            <i class="fas fa-building"></i>
        </div>
        <h2 class="about-section__title">Who We Are</h2>
        <p class="about-section__p">{{ $aWhoP1 }}</p>
        @if($aWhoP2)<p class="about-section__p">{{ $aWhoP2 }}</p>@endif

        {{-- Stats row --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:28px;">
            @foreach([
                [$aYears,     'Years in Business'],
                [$aShowrooms, 'Showroom Locations'],
                [$aFeedback,  'Positive Feedback'],
            ] as [$num, $label])
            <div style="background:var(--g50);border:1px solid var(--border2);border-radius:0;padding:20px;text-align:center;">
                <div style="font-family:var(--font-head);font-size:2rem;font-weight:800;color:var(--g500);line-height:1;">{{ $num }}</div>
                <div style="font-size:12.5px;color:var(--ink3);margin-top:6px;font-weight:500;">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- Our Story & Values --}}
    <section class="about-section" style="animation-delay:.15s;">
        <div class="about-section__icon">
            <i class="fas fa-heart"></i>
        </div>
        <h2 class="about-section__title">Our Story &amp; Values</h2>
        <p class="about-section__p">{{ $aStoryP1 }}</p>

        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-top:20px;">
            @foreach([
                ['fa-gem',        'Quality',               'Offering only the best products from trusted brands like Samsung, LG, and Thermocool.'],
                ['fa-star',       'Customer Satisfaction', 'Personalised support and expert advice to meet every customer\'s unique needs.'],
                ['fa-lightbulb',  'Innovation',            'Embracing new technologies, including solar solutions to address Nigeria\'s energy challenges.'],
                ['fa-map-pin',    'Accessibility',         'Serving communities across Enugu, Lagos, Awka, and beyond with convenient locations.'],
            ] as [$icon, $title, $desc])
            <div style="background:var(--surf2);border:1px solid var(--border);border-radius:0;padding:20px 18px;display:flex;gap:14px;align-items:flex-start;transition:border-color .2s;" onmouseover="this.style.borderColor='var(--border2)'" onmouseout="this.style.borderColor='var(--border)'">
                <div style="width:38px;height:38px;border-radius:10px;background:var(--g50);border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas {{ $icon }}" style="font-size:14px;color:var(--g600);"></i>
                </div>
                <div>
                    <p style="font-family:var(--font-head);font-size:13.5px;font-weight:700;color:var(--ink);margin-bottom:5px;">{{ $title }}</p>
                    <p style="font-size:13px;color:var(--ink3);line-height:1.55;">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- Products & Services --}}
    <section class="about-section" style="animation-delay:.3s;">
        <div class="about-section__icon">
            <i class="fas fa-box-open"></i>
        </div>
        <h2 class="about-section__title">Our Products &amp; Services</h2>
        <p class="about-section__p">At Albertina, we offer a diverse selection of electronics and appliances to suit every lifestyle and budget. Explore our offerings:</p>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-top:20px;">
            @foreach([
                ['fa-tv',          'Consumer Electronics',  'Televisions, audio systems, and home entertainment solutions from Samsung, LG, and more.'],
                ['fa-solar-panel', 'Solar Solutions',       'Reliable solar panels, inverters, and batteries to reduce electricity costs.'],
                ['fa-blender',     'Kitchen Appliances',    'Refrigerators, microwaves, cookers, and every essential for the modern kitchen.'],
                ['fa-wind',        'Home Appliances',       'Washing machines, air conditioners, fans, and generators for a comfortable home.'],
            ] as [$icon, $title, $desc])
            <div class="product-feature-card">
                <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,var(--g600),var(--g500));display:flex;align-items:center;justify-content:center;margin-bottom:14px;transition:transform .2s;">
                    <i class="fas {{ $icon }}" style="font-size:18px;color:#fff;"></i>
                </div>
                <h3 style="font-family:var(--font-head);font-size:14px;font-weight:700;color:var(--ink);margin-bottom:7px;">{{ $title }}</h3>
                <p style="font-size:13px;color:var(--ink3);line-height:1.55;">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </section>

    {{-- Locations --}}
    <section class="about-section" style="animation-delay:.45s;">
        <div class="about-section__icon">
            <i class="fas fa-map-marker-alt"></i>
        </div>
        <h2 class="about-section__title">Our Locations</h2>
        <p class="about-section__p">Visit any of our showrooms across Nigeria — our expert staff are always on hand to help you find the right product.</p>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:20px;">
            @foreach([
                ['Enugu (HQ)',  '17-18 Zik\'s Avenue, Uwani, Enugu', 'fa-star'],
                ['Lagos',       '26 Lawanson Road, Surulere, Lagos',  null],
                ['Awka',        'Enugu-Onitsha Expressway, Awka',     null],
            ] as [$city, $address, $badge])
            <div style="background:var(--surf2);border:1px solid var(--border);border-radius:0;padding:20px;position:relative;transition:border-color .2s;" onmouseover="this.style.borderColor='var(--g400)'" onmouseout="this.style.borderColor='var(--border)'">
                @if($badge)
                <span style="position:absolute;top:12px;right:12px;background:var(--g400);color:#fff;font-size:10px;font-weight:700;padding:3px 8px;border-radius:4px;letter-spacing:.4px;">HQ</span>
                @endif
                <i class="fas fa-map-marker-alt" style="color:var(--g500);font-size:20px;margin-bottom:10px;display:block;"></i>
                <p style="font-family:var(--font-head);font-size:14px;font-weight:700;color:var(--ink);margin-bottom:5px;">{{ $city }}</p>
                <p style="font-size:13px;color:var(--ink3);line-height:1.55;">{{ $address }}</p>
            </div>
            @endforeach
        </div>
    </section>

    {{-- Contact CTA --}}
    <section style="background:var(--g50);border:1px solid var(--border2);border-radius:0;padding:44px 48px;text-align:center;margin-bottom:0;position:relative;overflow:hidden;" class="about-section">
        <div style="position:absolute;top:-40px;right:-40px;width:160px;height:160px;border-radius:50%;background:rgba(90,171,31,.08);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-30px;left:-30px;width:120px;height:120px;border-radius:50%;background:rgba(42,90,10,.06);pointer-events:none;"></div>

        <div style="position:relative;z-index:1;">
            <div style="width:52px;height:52px;border-radius:14px;background:var(--g500);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <i class="fas fa-headset" style="font-size:20px;color:#fff;"></i>
            </div>
            <h2 style="font-family:var(--font-head);font-size:1.5rem;font-weight:800;color:var(--ink);margin-bottom:10px;">Get in Touch</h2>
            <p style="font-size:14px;color:var(--ink3);max-width:480px;margin:0 auto 28px;line-height:1.7;">Have questions or need assistance? Our team at AlbertinaNG is here to help. Reach out today.</p>

            <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                <a href="mailto:Info@Albertinang.com" style="display:inline-flex;align-items:center;gap:8px;background:var(--g500);color:#fff;padding:12px 24px;border-radius:var(--radius);font-size:14px;font-weight:600;font-family:var(--font-head);transition:all .2s;" onmouseover="this.style.background='var(--g600)';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='var(--g500)';this.style.transform='none'">
                    <i class="fas fa-envelope"></i> Email Us
                </a>
                <a href="tel:08064066170" style="display:inline-flex;align-items:center;gap:8px;background:var(--surface);color:var(--g600);border:1.5px solid var(--g400);padding:12px 24px;border-radius:var(--radius);font-size:14px;font-weight:600;font-family:var(--font-head);transition:all .2s;" onmouseover="this.style.background='var(--g50)';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='var(--surface)';this.style.transform='none'">
                    <i class="fas fa-phone-alt"></i> Call Us
                </a>
                <a href="{{ route('contact') }}" style="display:inline-flex;align-items:center;gap:8px;background:var(--surface);color:var(--ink2);border:1.5px solid var(--border);padding:12px 24px;border-radius:var(--radius);font-size:14px;font-weight:600;font-family:var(--font-head);transition:all .2s;" onmouseover="this.style.borderColor='var(--g400)';this.style.color='var(--g600)';this.style.transform='translateY(-1px)'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--ink2)';this.style.transform='none'">
                    <i class="fas fa-comment-dots"></i> Contact Page
                </a>
            </div>
        </div>
    </section>

</div>
@endsection

@push('styles')
<style>
    .about-section {
        padding: 36px 0;
        margin-bottom: 0;
        border-bottom: 1px solid var(--border);
        opacity: 0;
        transform: translateY(18px);
        animation: aboutFadeIn .55s ease forwards;
    }

    @keyframes aboutFadeIn {
        to { opacity: 1; transform: translateY(0); }
    }

    .about-section__icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: var(--g50);
        border: 1px solid var(--border2);
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 16px;
    }
    .about-section__icon i { font-size: 16px; color: var(--g600); }

    .about-section__title {
        font-family: var(--font-head);
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 14px;
    }
    .about-section__p {
        font-size: 14px;
        color: var(--ink2);
        line-height: 1.8;
        margin-bottom: 14px;
    }

    .product-feature-card {
        background: var(--surf2);
        border: 1px solid var(--border);
        border-radius: 0;
        padding: 22px 18px;
        transition: border-color .22s;
        cursor: default;
    }
    .product-feature-card:hover {
        border-color: var(--g400);
    }

    @media (max-width: 900px) {
        .about-section { padding: 26px 0; }
        .about-section > div[style*="grid-template-columns:repeat(3"] { grid-template-columns: 1fr !important; }
        .about-section > div[style*="grid-template-columns:repeat(2"] { grid-template-columns: 1fr !important; }
    }
    @media (max-width: 600px) {
        .main-wrap > div:first-child { padding: 32px 22px !important; }
        .main-wrap > div:first-child h1 { font-size: 1.75rem !important; }
    }
</style>
@endpush