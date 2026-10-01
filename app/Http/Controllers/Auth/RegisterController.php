<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm(Request $request)
    {
        $referralCode = $request->query('ref');

        // Only offer countries that have predefined states/provinces — countries
        // with none (e.g. Montserrat, Monaco) are hidden from the dropdown.
        $countries = Cache::rememberForever('reg:countries:withstates', fn () =>
            Country::whereHas('cities')
                ->orderByRaw("name = 'Nigeria' DESC")
                ->orderBy('name')
                ->get(['id', 'name', 'iso_code'])
        );

        $nigeriaId = $countries->firstWhere('name', 'Nigeria')?->id;

        // The country selected on first paint: the one restored after a validation
        // error, otherwise the default (Nigeria). We render its states server-side
        // so they appear instantly without depending on the AJAX call.
        $initialCountryId = old('country_id', $nigeriaId);

        // Tells the view whether $initialCountryId came from a restored validation
        // error (true) or is just the Nigeria default (false). The client-side
        // IP geolocation auto-select only runs when this is false, so it can
        // never clobber a country the user had already chosen before submitting.
        $hasOldCountry = old('country_id') !== null;

        $initialStates    = [];
        if ($initialCountryId) {
            $initialStates = Cache::rememberForever('geo:states:' . $initialCountryId, fn () =>
                City::where('country_id', $initialCountryId)->orderBy('name')->get(['id', 'name'])->toArray()
            );
        }

        return view('auth.register', compact(
            'referralCode', 'countries', 'nigeriaId', 'initialCountryId', 'initialStates', 'hasOldCountry'
        ));
    }

    protected function validator(array $data)
    {
        $countryId = (int) ($data['country_id'] ?? 0);
        $hasStates = $countryId > 0
            && DB::table('cities')->where('country_id', $countryId)->exists();

        return Validator::make($data, [
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'country_id'     => ['required', 'integer', 'exists:countries,id'],
            'state_id'       => $hasStates
                ? ['required', 'integer',
                   Rule::exists('cities', 'id')->where('country_id', $countryId)]
                : ['nullable', 'integer'],
            'postal_code'    => ['nullable', 'string', 'max:20'],
            'affiliate_code' => ['nullable', 'exists:users,affiliate_code'],
        ], [
            'country_id.required' => 'Please select a country.',
            'country_id.exists'   => 'The selected country is not valid.',
            'state_id.required'   => 'Please select a state/province.',
            'state_id.exists'     => 'The selected state does not belong to the chosen country.',
        ]);
    }

    protected function create(array $data)
    {
        try {
            $referrer = null;
            if (!empty($data['affiliate_code'])) {
                $referrer = User::where('affiliate_code', $data['affiliate_code'])->first();
            }

            $country   = Country::find($data['country_id']);
            $stateName = !empty($data['state_id'])
                ? City::whereKey($data['state_id'])->value('name')
                : null;

            $user = User::create([
                'name'           => $data['name'],
                'email'          => $data['email'],
                'password'       => Hash::make($data['password']),
                'country_id'     => $data['country_id'],
                'country'        => $country?->name,   // keep legacy string column in sync
                'state_id'       => $data['state_id'] ?? null,
                'state'          => $stateName,        // legacy string column (invoices, account page)
                'postal_code'    => $data['postal_code'] ?? null,
                'role'           => 'user',
                'affiliate_code' => Str::random(10),
                'referred_by'    => $referrer?->id,
            ]);

            session()->flash('success', 'Registration successful! Welcome to our platform, ' . $user->name . '!');

            return $user;

        } catch (\Exception $e) {
            Log::error('User registration failed: ' . $e->getMessage());
            session()->flash('error', 'Registration failed. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            session()->flash('error', 'Please correct the errors below and try again.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = $this->create($request->all());

        if (!$user instanceof User) {
            return $user;
        }

        $this->guard()->login($user);

        return $this->registered($request, $user) ?: redirect($this->redirectPath());
    }

    protected function registered(Request $request, $user)
    {
        // When the admin has turned on email verification, send the link and park
        // the (now logged-in) user on the verification notice until they confirm.
        if (\App\Models\Setting::get('email_verification_enabled', '0') === '1'
            && $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail
            && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();

            return redirect()->route('verification.notice')
                ->with('success', 'Almost there! We sent a verification link to ' . $user->email . '.');
        }

        if (!session()->has('success')) {
            session()->flash('success', 'Account created successfully! You are now logged in.');
        }

        return match ($user->role) {
            'admin'     => redirect()->route('admin.dashboard'),
            'user'      => redirect()->route('dashboard'),
            'supplier'  => redirect()->route('supplier.dashboard'),
            'affiliate' => redirect()->route('affiliate.dashboard'),
            default     => redirect($this->redirectPath()),
        };
    }
}