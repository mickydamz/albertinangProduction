<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class AdminAboutController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.about.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'about_hero_subtitle'  => 'nullable|string|max:300',
            'about_who_p1'         => 'nullable|string',
            'about_who_p2'         => 'nullable|string',
            'about_story_p1'       => 'nullable|string',
            'about_years'          => 'nullable|string|max:20',
            'about_showrooms'      => 'nullable|string|max:20',
            'about_feedback'       => 'nullable|string|max:20',
            'about_est_year'       => 'nullable|string|max:10',
        ]);

        $keys = [
            'about_hero_subtitle', 'about_who_p1', 'about_who_p2',
            'about_story_p1', 'about_years', 'about_showrooms',
            'about_feedback', 'about_est_year',
        ];

        foreach ($keys as $key) {
            Setting::set($key, $request->input($key, ''));
        }

        Setting::clearCache();

        return back()->with('success', 'About Us content updated successfully!');
    }
}
