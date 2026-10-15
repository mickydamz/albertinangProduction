<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MarketingPreferenceController extends Controller
{
    public function edit(Request $request)
    {
        return view('marketing.preferences', ['subscribed' => (bool) $request->user()->marketing_subscribed_at]);
    }

    public function update(Request $request)
    {
        $request->validate(['subscribe' => 'nullable|boolean']);
        $request->user()->forceFill(['marketing_subscribed_at' => $request->boolean('subscribe') ? ($request->user()->marketing_subscribed_at ?: now()) : null])->save();

        return back()->with('success', 'Your email preferences have been saved.');
    }

    public function unsubscribe(User $user)
    {
        return view('marketing.unsubscribe');
    }

    public function confirmUnsubscribe(User $user)
    {
        $user->forceFill(['marketing_subscribed_at' => null])->save();

        return view('marketing.unsubscribe', ['done' => true]);
    }
}
