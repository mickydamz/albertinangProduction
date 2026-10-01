<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class AdminContactController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.contact.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'contact_hero_title'    => 'nullable|string|max:200',
            'contact_hero_subtitle' => 'nullable|string|max:500',
            'contact_email'         => 'nullable|email|max:200',
            'contact_phone_1'       => 'nullable|string|max:60',
            'contact_phone_2'       => 'nullable|string|max:60',
            'contact_address_1'     => 'nullable|string|max:200',
            'contact_address_2'     => 'nullable|string|max:200',
            'contact_address_3'     => 'nullable|string|max:200',
            'contact_hours_weekday' => 'nullable|string|max:100',
            'contact_hours_weekend' => 'nullable|string|max:100',
        ]);

        $keys = [
            'contact_hero_title', 'contact_hero_subtitle',
            'contact_email', 'contact_phone_1', 'contact_phone_2',
            'contact_address_1', 'contact_address_2', 'contact_address_3',
            'contact_hours_weekday', 'contact_hours_weekend',
        ];

        foreach ($keys as $key) {
            Setting::set($key, $request->input($key, ''));
        }

        Setting::clearCache();

        return back()->with('success', 'Contact page updated successfully!');
    }
}
