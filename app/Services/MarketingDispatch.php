<?php

namespace App\Services;

use App\Models\MarketingCampaign;
use Illuminate\Support\Facades\DB;

class MarketingDispatch
{
    public static function completeFinished(): int
    {
        return MarketingCampaign::whereIn('status', ['queued', 'paused', 'scheduled'])
            ->whereDoesntHave('deliveries', fn ($q) => $q->whereNotIn('status', ['sent', 'failed', 'skipped', 'cancelled']))
            ->where(function ($query) {
                $query->whereIn('status', ['queued', 'paused'])->orWhereHas('deliveries');
            })->update(['status' => 'completed']);
    }

    public static function queue(MarketingCampaign $campaign, $scheduledAt = null): void
    {
        DB::transaction(function () use ($campaign, $scheduledAt) {
            $locked = MarketingCampaign::whereKey($campaign->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'draft', 409, 'This campaign has already been queued.');
            $locked->eligibleUsers()->select(['id', 'email'])->chunkById(500, function ($users) use ($locked) {
                DB::table('marketing_deliveries')->insert($users->map(fn ($u) => ['marketing_campaign_id' => $locked->id, 'user_id' => $u->id, 'email' => $u->email, 'status' => 'pending', 'created_at' => now(), 'updated_at' => now()])->all());
            });
            $locked->scheduled_at = $scheduledAt ? $scheduledAt->copy()->timezone(config('app.timezone')) : null;
            $locked->status = $locked->deliveries()->exists() ? ($scheduledAt ? 'scheduled' : 'queued') : 'completed';
            $locked->save();
        });
    }
}
