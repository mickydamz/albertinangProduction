<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_name'                         => 'required|string|max:100',
            'store_address'                      => 'nullable|string|max:500',
            'store_phone'                        => 'nullable|string|max:50',
            'store_email'                        => 'nullable|email|max:150',
            'store_logo'                         => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
        ]);

        Setting::set('store_name',    $request->store_name);
        Setting::set('store_address', $request->store_address);
        Setting::set('store_phone',   $request->store_phone);
        Setting::set('store_email',   $request->store_email);
        Setting::set('delivery_enabled',   $request->boolean('delivery_enabled')   ? '1' : '0');
        Setting::set('pickup_enabled',     $request->boolean('pickup_enabled')     ? '1' : '0');
        Setting::set('require_2fa_admin',  $request->boolean('require_2fa_admin')  ? '1' : '0');
        Setting::set('require_2fa_users',  $request->boolean('require_2fa_users')  ? '1' : '0');

        // Email verification. When flipping it ON, mark all existing accounts as
        // verified so the change never locks out the current customer base — only
        // NEW sign-ups (created while it's on) will need to verify.
        $verifyWasOn = Setting::get('email_verification_enabled', '0') === '1';
        $verifyNowOn = $request->boolean('email_verification_enabled');
        Setting::set('email_verification_enabled', $verifyNowOn ? '1' : '0');
        if ($verifyNowOn && ! $verifyWasOn) {
            \App\Models\User::whereNull('email_verified_at')->update(['email_verified_at' => now()]);
        }

        if ($request->hasFile('store_logo')) {
            // Delete old logo if it was one we uploaded (not the default asset)
            $old = Setting::get('store_logo');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }

            $path = $request->file('store_logo')->store('logos', 'public');
            Setting::set('store_logo', $path);
        }

        Setting::clearCache();

        return back()->with('success', 'Store settings saved successfully!');
    }
}
