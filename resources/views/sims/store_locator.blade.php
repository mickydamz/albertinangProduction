@extends('layouts.simslayout')

@section('title', 'Store Locator — AlbertinaNG')

@php
use App\Models\StoreLocation;
$storePoints = StoreLocation::orderByDesc('is_hq')->orderBy('sort_order')->orderBy('name')->get();
@endphp

@section('content')
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
        <div style="position:absolute;top:-60px;right:-60px;width:260px;height:260px;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-50px;left:-40px;width:180px;height:180px;border-radius:50%;background:rgba(90,171,31,.12);pointer-events:none;"></div>
        <div style="position:absolute;top:30px;left:80px;width:90px;height:90px;border-radius:50%;background:rgba(171,235,115,.07);pointer-events:none;"></div>

        <div style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:20px;padding:6px 14px;margin-bottom:18px;">
                <i class="fas fa-map-marker-alt" style="font-size:12px;color:var(--g200);"></i>
                <span style="font-size:12px;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;color:var(--g200);">Our Locations</span>
            </div>
            <h1 style="font-family:var(--font-head);font-size:2.4rem;font-weight:800;margin-bottom:14px;line-height:1.15;">Find an Albertina Store</h1>
            <p style="font-size:15px;color:rgba(255,255,255,.7);max-width:560px;margin:0 auto;line-height:1.7;">
                Visit our showrooms in Enugu, Lagos, and Awka to explore our wide range of electronics and home appliances.
            </p>
        </div>
    </div>

    {{-- Store Locations --}}
    <section class="sloc-section">
        <div class="sloc-section__icon"><i class="fas fa-store"></i></div>
        <h2 class="sloc-section__title">Our Store Locations</h2>
        <p class="sloc-section__p">AlbertinaNG is proud to serve you at our conveniently located showrooms across Nigeria. Each store offers a wide selection of Samsung, LG, Thermocool, and solar products, backed by our expert staff and excellent customer service.</p>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:18px;margin-top:24px;">
            @forelse($storePoints as $pt)
            <div class="sloc-card">
                @if($pt->is_hq)
                <span style="position:absolute;top:14px;right:14px;background:var(--g400);color:#fff;font-size:10px;font-weight:700;padding:3px 8px;border-radius:4px;letter-spacing:.4px;">HQ</span>
                @endif

                <div style="width:50px;height:50px;border-radius:12px;background:linear-gradient(135deg,var(--g600),var(--g500));display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                    <i class="fas fa-store" style="font-size:18px;color:#fff;"></i>
                </div>

                <h3 style="font-family:var(--font-head);font-size:15px;font-weight:700;color:var(--ink);margin-bottom:12px;">{{ $pt->name }}</h3>

                <div style="display:flex;flex-direction:column;gap:8px;">
                    <div style="display:flex;align-items:flex-start;gap:9px;font-size:13px;color:var(--ink3);">
                        <i class="fas fa-map-marker-alt" style="color:var(--g500);margin-top:2px;flex-shrink:0;font-size:12px;"></i>
                        <span>{{ $pt->address }}</span>
                    </div>
                    @if($pt->phone)
                    <div style="display:flex;align-items:center;gap:9px;font-size:13px;">
                        <i class="fas fa-phone-alt" style="color:var(--g500);flex-shrink:0;font-size:12px;"></i>
                        <a href="tel:{{ preg_replace('/\s+/','',$pt->phone) }}" style="color:var(--g600);font-weight:500;transition:color .18s;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">{{ $pt->phone }}</a>
                    </div>
                    @endif
                    <div style="display:flex;align-items:center;gap:9px;font-size:13px;">
                        <i class="fas fa-envelope" style="color:var(--g500);flex-shrink:0;font-size:12px;"></i>
                        <a href="mailto:Info@Albertinang.com" style="color:var(--g600);font-weight:500;transition:color .18s;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Info@Albertinang.com</a>
                    </div>
                    @if($pt->hours)
                    <div style="display:flex;align-items:center;gap:9px;font-size:13px;color:var(--ink3);">
                        <i class="fas fa-clock" style="color:var(--g500);flex-shrink:0;font-size:12px;"></i>
                        <span>{{ $pt->hours }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            {{-- Fallback: no pickup points in DB yet --}}
            @foreach([
                ['name' => 'Enugu Showroom (HQ)', 'address' => "17-18 Zik's Avenue, Uwani, Enugu 400105", 'phone' => '+234 806 406 6170', 'hours' => 'Mon–Sat: 8:00 AM – 6:00 PM · Sun: Closed', 'is_hq' => true],
                ['name' => 'Lagos Showroom',      'address' => '26 Lawanson Road, Surulere, Lagos',          'phone' => '+234 806 406 6170', 'hours' => 'Mon–Sat: 8:00 AM – 6:00 PM · Sun: Closed', 'is_hq' => false],
                ['name' => 'Awka Showroom',       'address' => 'Enugu-Onitsha Expressway, Awka (near Unizik)', 'phone' => '+234 806 406 6170', 'hours' => 'Mon–Sat: 8:00 AM – 6:00 PM · Sun: Closed', 'is_hq' => false],
            ] as $fb)
            <div class="sloc-card">
                @if($fb['is_hq'])
                <span style="position:absolute;top:14px;right:14px;background:var(--g400);color:#fff;font-size:10px;font-weight:700;padding:3px 8px;border-radius:4px;letter-spacing:.4px;">HQ</span>
                @endif
                <div style="width:50px;height:50px;border-radius:12px;background:linear-gradient(135deg,var(--g600),var(--g500));display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                    <i class="fas fa-store" style="font-size:18px;color:#fff;"></i>
                </div>
                <h3 style="font-family:var(--font-head);font-size:15px;font-weight:700;color:var(--ink);margin-bottom:12px;">{{ $fb['name'] }}</h3>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <div style="display:flex;align-items:flex-start;gap:9px;font-size:13px;color:var(--ink3);">
                        <i class="fas fa-map-marker-alt" style="color:var(--g500);margin-top:2px;flex-shrink:0;font-size:12px;"></i>
                        <span>{{ $fb['address'] }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:9px;font-size:13px;">
                        <i class="fas fa-phone-alt" style="color:var(--g500);flex-shrink:0;font-size:12px;"></i>
                        <a href="tel:+2348064066170" style="color:var(--g600);font-weight:500;transition:color .18s;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">{{ $fb['phone'] }}</a>
                    </div>
                    <div style="display:flex;align-items:center;gap:9px;font-size:13px;">
                        <i class="fas fa-envelope" style="color:var(--g500);flex-shrink:0;font-size:12px;"></i>
                        <a href="mailto:Info@Albertinang.com" style="color:var(--g600);font-weight:500;transition:color .18s;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Info@Albertinang.com</a>
                    </div>
                    <div style="display:flex;align-items:center;gap:9px;font-size:13px;color:var(--ink3);">
                        <i class="fas fa-clock" style="color:var(--g500);flex-shrink:0;font-size:12px;"></i>
                        <span>{{ $fb['hours'] }}</span>
                    </div>
                </div>
            </div>
            @endforeach
            @endforelse
        </div>
    </section>

    {{-- Map --}}
    <section class="sloc-section">
        <div class="sloc-section__icon"><i class="fas fa-map"></i></div>
        <h2 class="sloc-section__title">Locate Us on the Map</h2>
        <p class="sloc-section__p">Use the embedded map below to get directions to our Enugu headquarters, or search for our Lagos and Awka branches.</p>

        <div style="border-radius:0;overflow:hidden;border:1px solid var(--border);margin-top:8px;">
           <iframe
    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.3!2d7.4951354!2d6.4351312!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1044a163ab071f77%3A0xade02a3de0845ebb!2s17%20Zik%20Ave%2C%20Uwani%2C%20Enugu%20400001%2C%20Nigeria!5e0!3m2!1sen!2sng!4v1747000000000"
    width="100%"
    height="380"
    style="display:block;border:none;"
    allowfullscreen=""
    loading="lazy"
    referrerpolicy="no-referrer-when-downgrade"
    title="AlbertinaNG — Enugu Showroom">
</iframe>
        </div>

        <p style="font-size:12.5px;color:var(--ink3);margin-top:10px;display:flex;align-items:center;gap:6px;">
            <i class="fas fa-info-circle" style="color:var(--g500);"></i>
            Map shows the Enugu HQ. Use Google Maps to search "AlbertinaNG Lagos" or "AlbertinaNG Awka" for other branches.
        </p>
    </section>

    {{-- CTA --}}
    <section style="background:var(--g50);border:1px solid var(--border2);border-radius:0;padding:44px 48px;text-align:center;margin-bottom:0;position:relative;overflow:hidden;" class="sloc-section">
        <div style="position:absolute;top:-40px;right:-40px;width:160px;height:160px;border-radius:50%;background:rgba(90,171,31,.08);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-30px;left:-30px;width:120px;height:120px;border-radius:50%;background:rgba(42,90,10,.06);pointer-events:none;"></div>

        <div style="position:relative;z-index:1;">
            <div style="width:52px;height:52px;border-radius:14px;background:var(--g500);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <i class="fas fa-headset" style="font-size:20px;color:#fff;"></i>
            </div>
            <h2 style="font-family:var(--font-head);font-size:1.5rem;font-weight:800;color:var(--ink);margin-bottom:10px;">Need Assistance?</h2>
            <p style="font-size:14px;color:var(--ink3);max-width:460px;margin:0 auto 28px;line-height:1.7;">Contact our team for help with store visits, product inquiries, or any other questions.</p>

            <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                <a href="{{ route('contact') }}" style="display:inline-flex;align-items:center;gap:8px;background:var(--g500);color:#fff;padding:12px 24px;border-radius:var(--radius);font-size:14px;font-weight:600;font-family:var(--font-head);transition:all .2s;" onmouseover="this.style.background='var(--g600)';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='var(--g500)';this.style.transform='none'">
                    <i class="fas fa-envelope"></i> Email Us
                </a>
                <a href="tel:08064066170" style="display:inline-flex;align-items:center;gap:8px;background:var(--surface);color:var(--g600);border:1.5px solid var(--g400);padding:12px 24px;border-radius:var(--radius);font-size:14px;font-weight:600;font-family:var(--font-head);transition:all .2s;" onmouseover="this.style.background='var(--g50)';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='var(--surface)';this.style.transform='none'">
                    <i class="fas fa-phone-alt"></i> 08064066170
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
    .sloc-section {
        padding: 36px 0;
        margin-bottom: 0;
        border-bottom: 1px solid var(--border);
        opacity: 0;
        transform: translateY(18px);
        animation: slocFadeIn .55s ease forwards;
        position: relative;
    }

    @keyframes slocFadeIn {
        to { opacity: 1; transform: translateY(0); }
    }

    .sloc-section:nth-child(2) { animation-delay: .1s; }
    .sloc-section:nth-child(3) { animation-delay: .2s; }
    .sloc-section:nth-child(4) { animation-delay: .3s; }

    .sloc-section__icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: var(--g50);
        border: 1px solid var(--border2);
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 16px;
    }
    .sloc-section__icon i { font-size: 16px; color: var(--g600); }

    .sloc-section__title {
        font-family: var(--font-head);
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 10px;
    }
    .sloc-section__p {
        font-size: 14px;
        color: var(--ink2);
        line-height: 1.8;
        margin-bottom: 6px;
    }

    .sloc-card {
        background: var(--surf2);
        border: 1px solid var(--border);
        border-radius: 0;
        padding: 22px 20px;
        transition: border-color .22s;
        position: relative;
    }
    .sloc-card:hover {
        border-color: var(--g400);
    }

    @media (max-width: 900px) {
        .sloc-section { padding: 26px 22px; }
    }
    @media (max-width: 600px) {
        .main-wrap > div:first-child { padding: 32px 22px !important; }
        .main-wrap > div:first-child h1 { font-size: 1.8rem !important; }
        .sloc-section[style*="g50"] { padding: 28px 0 !important; }
    }
</style>
@endpush