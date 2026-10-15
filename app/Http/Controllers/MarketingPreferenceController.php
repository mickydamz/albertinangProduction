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

    public function oneClickUnsubscribe(User $user, Request $request)
    {
        abort_unless($request->input('List-Unsubscribe') === 'One-Click', 422);
        $this->confirmUnsubscribe($user, $request);

        return response()->noContent();
    }

    public function confirmUnsubscribe(User $user, Request $request)
    {
        $user->forceFill(['marketing_subscribed_at' => null])->save();
        if ($request->filled('delivery')) {
            \App\Models\MarketingDelivery::whereKey($request->delivery)->where('user_id', $user->id)->whereNull('unsubscribed_at')->update(['unsubscribed_at' => now()]);
        }

        return view('marketing.unsubscribe', ['done' => true]);
    }
}
