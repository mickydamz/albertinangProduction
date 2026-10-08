@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">About Us Content</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">About Us</li>
                        </ol>
                    </div>
                    <a href="{{ route('about') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-up-right-from-square me-50"></i> View Page
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.about.update') }}">
                @csrf @method('PUT')

                <div class="row">
                    <div class="col-lg-8">

                        {{-- Hero --}}
                        <div class="card mb-3">
                            <div class="card-header border-bottom">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-star me-50 text-primary"></i> Hero Section
                                </h4>
                            </div>
                            <div class="card-body pt-2">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Establishment Year</label>
                                    <input type="text" name="about_est_year" class="form-control"
                                           value="{{ old('about_est_year', $settings['about_est_year'] ?? '1991') }}"
                                           placeholder="1991">
                                    <div class="form-text">Shown in the "Est. XXXX" badge</div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-semibold">Hero Subtitle</label>
                                    <textarea name="about_hero_subtitle" rows="2" class="form-control"
                                              placeholder="Your trusted partner for quality electronics…">{{ old('about_hero_subtitle', $settings['about_hero_subtitle'] ?? 'Your trusted partner for quality electronics and home appliances in Nigeria — proudly serving customers since 1991.') }}</textarea>
                                    <div class="form-text">Shown under the page title in the hero banner</div>
                                </div>
                            </div>
                        </div>

                        {{-- Who We Are --}}
                        <div class="card mb-3">
                            <div class="card-header border-bottom">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-building me-50 text-primary"></i> Who We Are
                                </h4>
                            </div>
                            <div class="card-body pt-2">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Paragraph 1</label>
                                    <textarea name="about_who_p1" rows="4" class="form-control"
                                              placeholder="AlbertinaNG is a leading electronics…">{{ old('about_who_p1', $settings['about_who_p1'] ?? 'AlbertinaNG is a leading electronics and appliances retailer, proudly serving Nigeria since our establishment in 1991. With showrooms in Enugu, Lagos, and Awka, we specialise in offering a wide range of high-quality products, including Samsung, LG, Thermocool, and innovative solar solutions. Our mission is to provide reliable, affordable, and cutting-edge electronics for both residential and commercial needs, ensuring customer satisfaction at every step.') }}</textarea>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-semibold">Paragraph 2</label>
                                    <textarea name="about_who_p2" rows="3" class="form-control"
                                              placeholder="From our headquarters at…">{{ old('about_who_p2', $settings['about_who_p2'] ?? "From our headquarters at 17-18 Zik's Avenue, Uwani, Enugu, we have built a reputation for competitive pricing, knowledgeable staff, and exceptional after-sales service. Whether you're upgrading your home with the latest appliances or seeking energy-efficient solar solutions, Albertina is your go-to destination.") }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Story --}}
                        <div class="card mb-3">
                            <div class="card-header border-bottom">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-heart me-50 text-primary"></i> Our Story
                                </h4>
                            </div>
                            <div class="card-body pt-2">
                                <div class="mb-0">
                                    <label class="form-label fw-semibold">Story Paragraph</label>
                                    <textarea name="about_story_p1" rows="4" class="form-control"
                                              placeholder="Founded in 1991, AlbertinaNG…">{{ old('about_story_p1', $settings['about_story_p1'] ?? 'Founded in 1991, AlbertinaNG has grown from a single store in Enugu to a trusted name across Nigeria, with additional locations at 26 Lawanson Road, Surulere, Lagos, and along the Enugu-Onitsha Expressway in Awka. Our journey is driven by a commitment to quality, innovation, and customer-centric service.') }}</textarea>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-4">

                        {{-- Stats --}}
                        <div class="card mb-3">
                            <div class="card-header border-bottom">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-chart-bar me-50 text-primary"></i> Stats Row
                                </h4>
                            </div>
                            <div class="card-body pt-2">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Years in Business</label>
                                    <input type="text" name="about_years" class="form-control"
                                           value="{{ old('about_years', $settings['about_years'] ?? '30+') }}"
                                           placeholder="30+">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Showroom Locations</label>
                                    <input type="text" name="about_showrooms" class="form-control"
                                           value="{{ old('about_showrooms', $settings['about_showrooms'] ?? '3') }}"
                                           placeholder="3">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-semibold">Positive Feedback</label>
                                    <input type="text" name="about_feedback" class="form-control"
                                           value="{{ old('about_feedback', $settings['about_feedback'] ?? '99%') }}"
                                           placeholder="99%">
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <p class="text-muted mb-2" style="font-size:12.5px;">
                                    <i class="fas fa-info-circle me-50"></i>
                                    Leave any field blank to keep the existing default text from the page.
                                </p>
                                <p class="text-muted mb-0" style="font-size:12.5px;">
                                    Store locations (Enugu, Lagos, Awka) are managed under <strong>Locations → Pickup Points</strong>.
                                </p>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-50"></i> Save About Us
                        </button>

                    </div>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection
