<?php

namespace App\Http\Controllers;

use App\Models\MarketingDelivery;

class MarketingTrackingController extends Controller
{
    public function click(MarketingDelivery $delivery)
    {
        $campaign = $delivery->campaign;
        abort_unless($campaign && $campaign->offer_url && preg_match('~^https://~i', $campaign->offer_url), 404);
        if ($delivery->status === 'sent') {
            $delivery->newQuery()->whereKey($delivery->id)->whereNull('clicked_at')->update(['clicked_at' => now()]);
            $delivery->increment('click_count');
        }

        return redirect()->away($campaign->offer_url)->header('Cache-Control', 'no-store')->header('Referrer-Policy', 'no-referrer');
    }
}
