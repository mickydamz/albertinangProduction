@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-0" style="font-weight:700;color:var(--al-text);">Contact Us Page</h4>
                        <p class="mb-0" style="font-size:13px;color:var(--al-text-3);">Edit the content shown on your public Contact Us page.</p>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 mb-3" style="border-radius:0;font-size:13.5px;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.contact.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">

                {{-- Left column --}}
                <div class="col-lg-8">

                    {{-- Hero --}}
                    <div class="card mb-3" style="border-radius:0;border:1px solid var(--al-border);box-shadow:none;">
                        <div class="card-header" style="border-radius:0;background:var(--al-surface);border-bottom:1px solid var(--al-border);padding:14px 20px;">
                            <h6 class="mb-0" style="font-weight:600;font-size:13.5px;">Page Hero</h6>
                        </div>
                        <div class="card-body" style="padding:20px;">
                            <div class="mb-3">
                                <label class="form-label" style="font-size:12.5px;font-weight:600;">Page Title</label>
                                <input type="text" name="contact_hero_title" class="form-control" style="border-radius:var(--al-radius);font-size:13.5px;"
                                       value="{{ $settings['contact_hero_title'] ?? 'Contact Albertina Nigeria' }}"
                                       placeholder="Contact Albertina Nigeria">
                            </div>
                            <div class="mb-0">
                                <label class="form-label" style="font-size:12.5px;font-weight:600;">Hero Subtitle</label>
                                <textarea name="contact_hero_subtitle" class="form-control" rows="3" style="border-radius:var(--al-radius);font-size:13.5px;"
                                          placeholder="We're here to help…">{{ $settings['contact_hero_subtitle'] ?? "We're here to help with all your electronics and appliance needs. Reach out and our team will get back to you promptly." }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Details --}}
                    <div class="card mb-3" style="border-radius:0;border:1px solid var(--al-border);box-shadow:none;">
                        <div class="card-header" style="border-radius:0;background:var(--al-surface);border-bottom:1px solid var(--al-border);padding:14px 20px;">
                            <h6 class="mb-0" style="font-weight:600;font-size:13.5px;">Contact Details</h6>
                        </div>
                        <div class="card-body" style="padding:20px;">
                            <div class="mb-3">
                                <label class="form-label" style="font-size:12.5px;font-weight:600;">Email Address</label>
                                <input type="email" name="contact_email" class="form-control" style="border-radius:var(--al-radius);font-size:13.5px;"
                                       value="{{ $settings['contact_email'] ?? 'Info@Albertinang.com' }}"
                                       placeholder="Info@Albertinang.com">
                            </div>
                            <div class="row g-3 mb-0">
                                <div class="col-sm-6">
                                    <label class="form-label" style="font-size:12.5px;font-weight:600;">Phone Number 1</label>
                                    <input type="text" name="contact_phone_1" class="form-control" style="border-radius:var(--al-radius);font-size:13.5px;"
                                           value="{{ $settings['contact_phone_1'] ?? '+234 806 406 6170' }}"
                                           placeholder="+234 806 406 6170">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" style="font-size:12.5px;font-weight:600;">Phone Number 2 <span style="font-weight:400;color:var(--al-text-3);">(optional)</span></label>
                                    <input type="text" name="contact_phone_2" class="form-control" style="border-radius:var(--al-radius);font-size:13.5px;"
                                           value="{{ $settings['contact_phone_2'] ?? '+234 703 768 0738' }}"
                                           placeholder="+234 703 768 0738">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Showroom Addresses --}}
                    <div class="card mb-3" style="border-radius:0;border:1px solid var(--al-border);box-shadow:none;">
                        <div class="card-header" style="border-radius:0;background:var(--al-surface);border-bottom:1px solid var(--al-border);padding:14px 20px;">
                            <h6 class="mb-0" style="font-weight:600;font-size:13.5px;">Showroom Addresses</h6>
                        </div>
                        <div class="card-body" style="padding:20px;">
                            <div class="mb-3">
                                <label class="form-label" style="font-size:12.5px;font-weight:600;">Address 1</label>
                                <input type="text" name="contact_address_1" class="form-control" style="border-radius:var(--al-radius);font-size:13.5px;"
                                       value="{{ $settings['contact_address_1'] ?? '17-18 Zik\'s Avenue, Uwani, Enugu' }}"
                                       placeholder="17-18 Zik's Avenue, Uwani, Enugu">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" style="font-size:12.5px;font-weight:600;">Address 2 <span style="font-weight:400;color:var(--al-text-3);">(optional)</span></label>
                                <input type="text" name="contact_address_2" class="form-control" style="border-radius:var(--al-radius);font-size:13.5px;"
                                       value="{{ $settings['contact_address_2'] ?? '26 Lawanson Road, Surulere, Lagos' }}"
                                       placeholder="26 Lawanson Road, Surulere, Lagos">
                            </div>
                            <div class="mb-0">
                                <label class="form-label" style="font-size:12.5px;font-weight:600;">Address 3 <span style="font-weight:400;color:var(--al-text-3);">(optional)</span></label>
                                <input type="text" name="contact_address_3" class="form-control" style="border-radius:var(--al-radius);font-size:13.5px;"
                                       value="{{ $settings['contact_address_3'] ?? 'Enugu-Onitsha Expressway, Awka' }}"
                                       placeholder="Enugu-Onitsha Expressway, Awka">
                            </div>
                        </div>
                    </div>

                    {{-- Working Hours --}}
                    <div class="card mb-3" style="border-radius:0;border:1px solid var(--al-border);box-shadow:none;">
                        <div class="card-header" style="border-radius:0;background:var(--al-surface);border-bottom:1px solid var(--al-border);padding:14px 20px;">
                            <h6 class="mb-0" style="font-weight:600;font-size:13.5px;">Working Hours</h6>
                        </div>
                        <div class="card-body" style="padding:20px;">
                            <div class="row g-3 mb-0">
                                <div class="col-sm-6">
                                    <label class="form-label" style="font-size:12.5px;font-weight:600;">Weekday Hours</label>
                                    <input type="text" name="contact_hours_weekday" class="form-control" style="border-radius:var(--al-radius);font-size:13.5px;"
                                           value="{{ $settings['contact_hours_weekday'] ?? 'Mon – Sat: 8:00 AM – 6:00 PM' }}"
                                           placeholder="Mon – Sat: 8:00 AM – 6:00 PM">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" style="font-size:12.5px;font-weight:600;">Weekend Hours <span style="font-weight:400;color:var(--al-text-3);">(optional)</span></label>
                                    <input type="text" name="contact_hours_weekend" class="form-control" style="border-radius:var(--al-radius);font-size:13.5px;"
                                           value="{{ $settings['contact_hours_weekend'] ?? 'Sunday: 10:00 AM – 4:00 PM' }}"
                                           placeholder="Sunday: 10:00 AM – 4:00 PM">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Right column — tips + save --}}
                <div class="col-lg-4">
                    <div class="card mb-3" style="border-radius:0;border:1px solid var(--al-border);box-shadow:none;position:sticky;top:76px;">
                        <div class="card-header" style="border-radius:0;background:var(--al-surface);border-bottom:1px solid var(--al-border);padding:14px 20px;">
                            <h6 class="mb-0" style="font-weight:600;font-size:13.5px;">Publish</h6>
                        </div>
                        <div class="card-body" style="padding:20px;">
                            <button type="submit" class="btn w-100" style="background:var(--al-primary);color:#fff;border-radius:var(--al-radius);font-weight:600;font-size:13.5px;">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                            <a href="{{ url('/contact') }}" target="_blank" class="btn btn-outline-secondary w-100 mt-2" style="border-radius:var(--al-radius);font-size:13px;">
                                <i class="fas fa-arrow-up-right-from-square me-1"></i> View Contact Page
                            </a>
                        </div>
                        <div class="card-body border-top" style="padding:16px 20px;border-color:var(--al-border)!important;">
                            <p style="font-size:12px;color:var(--al-text-3);margin:0;line-height:1.6;">
                                <i class="fas fa-info-circle me-1"></i>
                                Leave any optional field blank to hide it on the contact page. Changes take effect immediately after saving.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>
@endsection
