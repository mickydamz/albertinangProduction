<?php

namespace App\Services;

use App\Models\MarketingCampaign;
use Illuminate\Support\Facades\DB;

class MarketingDispatch
{
    public static function queue(MarketingCampaign $campaign, $scheduledAt = null): void
    {
        DB::transaction(function () use ($campaign, $scheduledAt) {
            $locked = MarketingCampaign::whereKey($campaign->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'draft', 409, 'This campaign has already been queued.');
            $locked->eligibleUsers()->select(['id', 'email'])->chunkById(500, function ($users) use ($locked) {
                DB::table('marketing_deliveries')->insert($users->map(fn ($u) => ['marketing_campaign_id' => $locked->id, 'user_id' => $u->id, 'email' => $u->email, 'status' => 'pending', 'created_at' => now(), 'updated_at' => now()])->all());
            });
            $locked->scheduled_at = $scheduledAt;
            $locked->status = $locked->deliveries()->exists() ? ($scheduledAt ? 'scheduled' : 'queued') : 'completed';
            $locked->save();
        });
    }
}
