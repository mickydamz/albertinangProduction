<?php

namespace App\Console\Commands;

use App\Mail\MarketingPromotion;
use App\Models\MarketingCampaign;
use App\Models\MarketingDelivery;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMarketingCampaigns extends Command
{
    protected $signature = 'marketing:send {--limit=100 : Maximum recipients per run}';

    protected $description = 'Send pending opted-in marketing emails';

    public function handle()
    {
        if (! config('marketing.enabled')) {
            $this->warn('Marketing email sending is disabled.');

            return 0;
        }
        $limit = max(1, min(1000, (int) $this->option('limit')));
        $deliveries = MarketingDelivery::where('status', 'pending')->whereHas('campaign', fn ($q) => $q->where('status', 'queued'))->orderBy('id')->limit($limit)->get();
        foreach ($deliveries as $delivery) {
            // Claim atomically so overlapping invocations cannot send the same record.
            if (! MarketingDelivery::whereKey($delivery->id)->where('status', 'pending')->update(['status' => 'sending', 'updated_at' => now()])) {
                continue;
            }
            $user = User::find($delivery->user_id);
            if (! $user || $user->role !== 'user' || ! $user->marketing_subscribed_at || $user->email !== $delivery->email) {
                $delivery->update(['status' => 'skipped']);

                continue;
            }
            try {
                Mail::to($delivery->email)->send(new MarketingPromotion($delivery->campaign, $user));
                $delivery->update(['status' => 'sent', 'sent_at' => now()]);
            } catch (\Throwable $e) {
                // Never automatically retry an uncertain send: transport may have accepted it.
                $delivery->update(['status' => 'failed']);
                Log::warning('Marketing delivery failed', ['delivery_id' => $delivery->id, 'exception' => get_class($e)]);
            }
        }
        MarketingCampaign::where('status', 'queued')->whereDoesntHave('deliveries', fn ($q) => $q->whereIn('status', ['pending', 'sending']))->update(['status' => 'completed']);

        return 0;
    }
}
