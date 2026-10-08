@extends('layouts.authlayout')

@section('title', 'Create Account — AlbertinaNG')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css">
<style>
    /* ── Tom Select — matched to Albertina auth design system ───────────────── */
    .ts-wrapper { width: 100%; }

    /* Control (the visible box) mirrors .form-control */
    .ts-wrapper .ts-control {
        min-height: 44px;
        padding: 9px 40px 9px 14px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius);
        background: var(--surface);
        font-size: 14px;
        font-family: var(--font-body);
        color: var(--ink);
        line-height: 1.4;
        box-shadow: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .ts-wrapper.single .ts-control { cursor: pointer; }
    .ts-wrapper .ts-control input {
        font-size: 14px;
        color: var(--ink);
        font-family: var(--font-body);
    }
    .ts-wrapper .ts-control input::placeholder { color: var(--ink3); }

    /* Focus state — green glow to match inputs */
    .ts-wrapper.focus .ts-control {
        border-color: var(--g400);
        box-shadow: 0 0 0 3px rgba(90,171,31,.12);
    }
    .ts-wrapper.disabled .ts-control {
        background: var(--surf2);
        opacity: .7;
        cursor: not-allowed;
    }

    /* Selected value text + placeholder */
    .ts-wrapper.single .ts-control > .item { color: var(--ink); }
    .ts-wrapper .ts-control > input::placeholder,
    .ts-wrapper.plugin-drop_active .ts-control { color: var(--ink3); }

    /* Chevron caret */
    .ts-wrapper.single .ts-control::after {
        content: '';
        position: absolute;
        top: 50%; right: 15px;
        width: 9px; height: 9px;
        margin-top: -6px;
        border-right: 2px solid var(--ink3);
        border-bottom: 2px solid var(--ink3);
        transform: rotate(45deg);
        transition: transform .2s;
        pointer-events: none;
    }
    .ts-wrapper.single.dropdown-active .ts-control::after {
        transform: rotate(-135deg);
        margin-top: -2px;
    }

    /* Dropdown panel */
    .ts-dropdown {
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-md);
        margin-top: 6px;
        overflow: hidden;
        font-family: var(--font-body);
        z-index: 50;
    }
    .ts-dropdown .option {
        padding: 10px 14px;
        font-size: 14px;
        color: var(--ink2);
        cursor: pointer;
    }
    .ts-dropdown .option:hover,
    .ts-dropdown .active {
        background: var(--g50);
        color: var(--g700);
    }
    .ts-dropdown .option.selected,
    .ts-dropdown .active.selected {
        background: var(--g100);
        color: var(--g700);
        font-weight: 600;
    }
    .ts-dropdown .no-results,
    .ts-dropdown .optgroup-header {
        padding: 10px 14px;
        font-size: 13px;
        color: var(--ink3);
    }

    /* Scrollbar inside the dropdown */
    .ts-dropdown-content { max-height: 260px; }
    .ts-dropdown-content::-webkit-scrollbar { width: 8px; }
    .ts-dropdown-content::-webkit-scrollbar-thumb {
        background: var(--border2); border-radius: 8px;
    }

    #state-field { transition: opacity .15s ease; }
    #state-field.hidden { display: none; }
    #state-loading { font-size: 11px; color: var(--g600); margin-left: 6px; display: none; font-weight: 500; }
</style>
@endpush

@section('content')

    <h4>Create Account 🚀</h4>
    <p class="subtitle">Start your journey with us! Fill in your details below.</p>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the errors below:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('register') }}" method="POST" id="reg-form" onsubmit="return validateForm()">
        @csrf

        {{-- Full Name --}}
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" class="form-control" id="name" name="name"
                   placeholder="Enter your full name"
                   value="{{ old('name') }}" required autocomplete="name">
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" class="form-control" id="email" name="email"
                   placeholder="you@example.com"
                   value="{{ old('email') }}" required autocomplete="email">
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label for="password">Password</label>
            <div class="pwd-wrap">
                <input type="password" class="form-control" id="password" name="password"
                       placeholder="Create a password" required autocomplete="new-password">
                <button type="button" class="pwd-toggle"
                        onclick="togglePassword('password','pwdEyeIcon')" aria-label="Toggle password">
                    <i class="fas fa-eye" id="pwdEyeIcon"></i>
                </button>
            </div>
        </div>

        {{-- Confirm Password --}}
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <div class="pwd-wrap">
                <input type="password" class="form-control" id="password_confirmation"
                       name="password_confirmation"
                       placeholder="Repeat your password" required autocomplete="new-password">
                <button type="button" class="pwd-toggle"
                        onclick="togglePassword('password_confirmation','pwdEyeIcon2')" aria-label="Toggle confirm password">
                    <i class="fas fa-eye" id="pwdEyeIcon2"></i>
                </button>
            </div>
        </div>

        <p class="text-muted">Your address helps us prepare deliveries. You can also add or update it in your account later.</p>
        <div class="form-group">
            <label for="shipping_address">Street address (optional)</label>
            <textarea class="form-control" id="shipping_address" name="shipping_address"
                      rows="2" maxlength="255" autocomplete="street-address"
                      placeholder="House number and street&#10;Flat, suite or building (optional)">{{ old('shipping_address') }}</textarea>
        </div>
        <div class="form-group">
            <label for="city">City / town (optional)</label>
            <input type="text" class="form-control" id="city" name="city"
                   maxlength="100" autocomplete="address-level2" value="{{ old('city') }}"
                   placeholder="Enter your city or town">
        </div>

        {{-- Country (searchable, Tom Select) --}}
        <div class="form-group">
            <label for="country_id">Country</label>
            <select id="country_id" name="country_id" required autocomplete="country">
                <option value="" disabled {{ old('country_id') ? '' : 'selected' }}>Choose your country…</option>
                @foreach($countries as $c)
                    <option value="{{ $c->id }}" data-iso="{{ $c->iso_code }}" {{ (string) old('country_id') === (string) $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- State (loaded dynamically, Tom Select — with a free-text fallback for
             countries that have no predefined states/provinces) --}}
        <div class="form-group" id="state-field">
            <label for="state_id">
                State / Province
                <span id="state-loading">Loading…</span>
            </label>
            <select id="state_id" name="state_id" autocomplete="address-level1">
                <option value="" disabled {{ old('state_id') ? '' : 'selected' }}>Choose your state…</option>
                @foreach($initialStates as $s)
                    <option value="{{ $s['id'] }}" {{ (string) old('state_id') === (string) $s['id'] ? 'selected' : '' }}>
                        {{ $s['name'] }}
                    </option>
                @endforeach
            </select>
            {{-- Free-text fallback, shown instead of the select when the chosen
                 country has no predefined states (e.g. Montserrat, Monaco).
                 Hidden/disabled by default so it never submits a stale value
                 while the select mode is active. --}}
            <input type="text" class="form-control" id="state_text" name="state_text"
                   placeholder="Enter your state/province"
                   value="{{ old('state_text') }}"
                   style="display:none" disabled>
        </div>

        {{-- Postal Code --}}
        <div class="form-group">
            <label for="postal_code">Postcode / ZIP code (optional)</label>
            <input type="text" class="form-control" id="postal_code" name="postal_code" maxlength="20" autocomplete="postal-code"
                   placeholder="Enter your postal code"
                   value="{{ old('postal_code') }}">
        </div>

        {{-- Terms --}}
        <div class="form-group" style="margin-bottom:22px;">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="terms_conditions" required>
                <label class="form-check-label" for="terms_conditions">
                    I accept the <a href="/terms" target="_blank" style="color:var(--g600);font-weight:600;">Terms and Conditions</a>
                </label>
            </div>
        </div>

        <button type="submit" class="btn-primary">Create Account</button>
    </form>

    <div class="auth-divider"><span>Already have an account?</span></div>
    <p class="auth-card__foot"><a href="{{ route('login') }}">Sign in instead →</a></p>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
(function () {
    const STATES_ENDPOINT = '{{ url('/api/countries') }}'; // base; appended with /{id}/states
    const INITIAL_COUNTRY = {{ $initialCountryId ?? 'null' }};
    const INITIAL_STATE   = {{ old('state_id', 'null') }};
    const HAS_INITIAL_STATES = {{ count($initialStates) > 0 ? 'true' : 'false' }};
    // True only when the form is redisplaying after a validation error —
    // in that case the restored country always wins over geolocation.
    const HAS_OLD_COUNTRY = {{ ($hasOldCountry ?? false) ? 'true' : 'false' }};

    // ── Tom Select instances ───────────────────────────────────────────────────
    const countryTs = new TomSelect('#country_id', {
        placeholder: 'Search country…',
        allowEmptyOption: true,
        maxOptions: 300,
    });

    const stateTs = new TomSelect('#state_id', {
        placeholder: 'Choose your state…',
        allowEmptyOption: true,
        maxOptions: 200,
    });

    const stateField   = document.getElementById('state-field');
    const stateLoading = document.getElementById('state-loading');
    const stateText    = document.getElementById('state_text');
    const stateSelect  = document.getElementById('state_id');

    // ── State field modes ───────────────────────────────────────────────────────
    // 'select' → searchable dropdown (country has predefined states)
    // 'text'   → free-text input (country has none, e.g. Montserrat)
    // The field is never hidden once a country is chosen, so it can't "disappear".
    function setStateMode(mode) {
        stateField.classList.remove('hidden');

        if (mode === 'text') {
            stateTs.wrapper.style.display = 'none';
            stateTs.clear(true);
            stateSelect.required = false;
            stateText.style.display = '';
            stateText.disabled = false;
        } else { // 'select'
            stateTs.wrapper.style.display = '';
            stateSelect.required = true;
            stateText.style.display = 'none';
            stateText.value = '';
            stateText.disabled = true; // don't submit a stale free-text value
        }
    }

    // ── Populate state Tom Select from an array of {id, name} ─────────────────
    function populateStates(states, preselectId) {
        stateTs.clear(true);
        stateTs.clearOptions();

        if (states.length === 0) {
            // No predefined states → let the user type one instead of vanishing.
            setStateMode('text');
            return;
        }

        const opts = states.map(s => ({ value: String(s.id), text: s.name }));
        stateTs.addOptions(opts);

        if (preselectId && opts.some(o => o.value === String(preselectId))) {
            stateTs.setValue(String(preselectId), true);
        }

        setStateMode('select');
    }

    // ── Fetch states for a country ID ─────────────────────────────────────────
    let stateRequestVersion = 0;
    let countryChangedByUser = false;
    async function loadStates(countryId, preselectId) {
        const requestVersion = ++stateRequestVersion;
        if (!countryId) { stateField.classList.add('hidden'); return; }

        stateLoading.style.display = 'inline';
        stateTs.disable();

        try {
            const r = await fetch(`${STATES_ENDPOINT}/${countryId}/states`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!r.ok) throw new Error('HTTP ' + r.status);
            const data = await r.json();
            if (!Array.isArray(data)) throw new Error('bad response');
            if (requestVersion !== stateRequestVersion) return;
            populateStates(data, preselectId);
        } catch (err) {
            // A transient failure (rate-limit, network) must not make the field
            // vanish — fall back to a free-text input so the user can proceed.
            if (requestVersion !== stateRequestVersion) return;
            console.error('States load failed:', err);
            setStateMode('text');
        } finally {
            if (requestVersion === stateRequestVersion) {
                stateLoading.style.display = 'none';
                stateTs.enable();
            }
        }
    }

    // ── Form validation ────────────────────────────────────────────────────────
    window.validateForm = function () {
        if (!document.getElementById('terms_conditions').checked) {
            alert('You must accept the Terms and Conditions to proceed.');
            return false;
        }
        return true;
    };

    // ── Initialise ─────────────────────────────────────────────────────────────
    // The initial country's states are already rendered server-side, so they show
    // instantly (no dependency on the AJAX call). We only fetch when the user
    // switches to a *different* country.
    if (INITIAL_COUNTRY) {
        countryTs.setValue(String(INITIAL_COUNTRY), true);
    }

    if (HAS_INITIAL_STATES) {
        if (INITIAL_STATE) stateTs.setValue(String(INITIAL_STATE), true);
        setStateMode('select');
    } else if (INITIAL_COUNTRY) {
        // Country selected but no predefined states → free-text (keeps old value)
        setStateMode('text');
    } else {
        stateField.classList.add('hidden');
    }

    // ── Country change handler ─────────────────────────────────────────────────
    // Registered *after* init so the initial setValue above can never trigger a
    // fetch — states only reload when the user actually switches country.
    countryTs.on('change', function (value) {
        countryChangedByUser = true;
        loadStates(value ? parseInt(value) : null, null);
    });

    // ── IP-based country auto-detection ─────────────────────────────────────────
    // Same idea as the storefront's currency auto-detect (see authlayout): guess
    // the visitor's country from their IP and pre-select it, so most people never
    // have to touch the dropdown. Skipped entirely when the form is redisplaying
    // a validation error, since the restored old() selection must win. We also
    // re-check right before applying that the user hasn't already picked a
    // country themselves while this request was in flight, and we drive the
    // state load ourselves (setValue silently) rather than firing 'change', so
    // this can never be confused with a real user action.
    //
    // ipwho.is only returns a 2-letter (ISO 3166-1 alpha-2) country_code, but our
    // countries table stores 3-letter (alpha-3) codes, so we convert first and
    // match on that — far more reliable than matching by country name text.
    var ISO2_TO_ISO3 = {
        AD:'AND',AE:'ARE',AF:'AFG',AG:'ATG',AI:'AIA',AL:'ALB',AM:'ARM',AO:'AGO',AQ:'ATA',AR:'ARG',AS:'ASM',AT:'AUT',AU:'AUS',AW:'ABW',AX:'ALA',AZ:'AZE',
        BA:'BIH',BB:'BRB',BD:'BGD',BE:'BEL',BF:'BFA',BG:'BGR',BH:'BHR',BI:'BDI',BJ:'BEN',BL:'BLM',BM:'BMU',BN:'BRN',BO:'BOL',BQ:'BES',BR:'BRA',BS:'BHS',BT:'BTN',BV:'BVT',BW:'BWA',BY:'BLR',BZ:'BLZ',
        CA:'CAN',CC:'CCK',CD:'COD',CF:'CAF',CG:'COG',CH:'CHE',CI:'CIV',CK:'COK',CL:'CHL',CM:'CMR',CN:'CHN',CO:'COL',CR:'CRI',CU:'CUB',CV:'CPV',CW:'CUW',CX:'CXR',CY:'CYP',CZ:'CZE',
        DE:'DEU',DJ:'DJI',DK:'DNK',DM:'DMA',DO:'DOM',DZ:'DZA',
        EC:'ECU',EE:'EST',EG:'EGY',EH:'ESH',ER:'ERI',ES:'ESP',ET:'ETH',
        FI:'FIN',FJ:'FJI',FK:'FLK',FM:'FSM',FO:'FRO',FR:'FRA',
        GA:'GAB',GB:'GBR',GD:'GRD',GE:'GEO',GF:'GUF',GG:'GGY',GH:'GHA',GI:'GIB',GL:'GRL',GM:'GMB',GN:'GIN',GP:'GLP',GQ:'GNQ',GR:'GRC',GS:'SGS',GT:'GTM',GU:'GUM',GW:'GNB',GY:'GUY',
        HK:'HKG',HM:'HMD',HN:'HND',HR:'HRV',HT:'HTI',HU:'HUN',
        ID:'IDN',IE:'IRL',IL:'ISR',IM:'IMN',IN:'IND',IO:'IOT',IQ:'IRQ',IR:'IRN',IS:'ISL',IT:'ITA',
        JE:'JEY',JM:'JAM',JO:'JOR',JP:'JPN',
        KE:'KEN',KG:'KGZ',KH:'KHM',KI:'KIR',KM:'COM',KN:'KNA',KP:'PRK',KR:'KOR',KW:'KWT',KY:'CYM',KZ:'KAZ',
        LA:'LAO',LB:'LBN',LC:'LCA',LI:'LIE',LK:'LKA',LR:'LBR',LS:'LSO',LT:'LTU',LU:'LUX',LV:'LVA',LY:'LBY',
        MA:'MAR',MC:'MCO',MD:'MDA',ME:'MNE',MF:'MAF',MG:'MDG',MH:'MHL',MK:'MKD',ML:'MLI',MM:'MMR',MN:'MNG',MO:'MAC',MP:'MNP',MQ:'MTQ',MR:'MRT',MS:'MSR',MT:'MLT',MU:'MUS',MV:'MDV',MW:'MWI',MX:'MEX',MY:'MYS',MZ:'MOZ',
        NA:'NAM',NC:'NCL',NE:'NER',NF:'NFK',NG:'NGA',NI:'NIC',NL:'NLD',NO:'NOR',NP:'NPL',NR:'NRU',NU:'NIU',NZ:'NZL',
        OM:'OMN',
        PA:'PAN',PE:'PER',PF:'PYF',PG:'PNG',PH:'PHL',PK:'PAK',PL:'POL',PM:'SPM',PN:'PCN',PR:'PRI',PS:'PSE',PT:'PRT',PW:'PLW',PY:'PRY',
        QA:'QAT',
        RE:'REU',RO:'ROU',RS:'SRB',RU:'RUS',RW:'RWA',
        SA:'SAU',SB:'SLB',SC:'SYC',SD:'SDN',SE:'SWE',SG:'SGP',SH:'SHN',SI:'SVN',SJ:'SJM',SK:'SVK',SL:'SLE',SM:'SMR',SN:'SEN',SO:'SOM',SR:'SUR',SS:'SSD',ST:'STP',SV:'SLV',SX:'SXM',SY:'SYR',SZ:'SWZ',
        TC:'TCA',TD:'TCD',TF:'ATF',TG:'TGO',TH:'THA',TJ:'TJK',TK:'TKL',TL:'TLS',TM:'TKM',TN:'TUN',TO:'TON',TR:'TUR',TT:'TTO',TV:'TUV',TW:'TWN',TZ:'TZA',
        UA:'UKR',UG:'UGA',UM:'UMI',US:'USA',UY:'URY',UZ:'UZB',
        VA:'VAT',VC:'VCT',VE:'VEN',VG:'VGB',VI:'VIR',VN:'VNM',VU:'VUT',
        WF:'WLF',WS:'WSM',
        YE:'YEM',YT:'MYT',
        ZA:'ZAF',ZM:'ZMB',ZW:'ZWE'
    };

    if (!HAS_OLD_COUNTRY) {
        fetch('https://ipwho.is/', { mode: 'cors' })
            .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
            .then(function (geo) {
                if (!geo || !geo.country_code) return;
                if (countryChangedByUser) return;

                var iso3 = ISO2_TO_ISO3[geo.country_code.toUpperCase()];
                if (!iso3) return;

                var opt = document.querySelector('#country_id option[data-iso="' + iso3 + '"]');
                if (!opt) return; // that country isn't in our list — keep the default

                if (countryTs.getValue() === String(opt.value)) return;
                countryTs.setValue(String(opt.value), true); // silent
                loadStates(parseInt(opt.value, 10), null);
            })
            .catch(function () { /* geolocation unavailable — keep default country */ });
    }
}());
</script>
@endpush