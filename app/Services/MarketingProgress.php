<?php

namespace App\Services;

use App\Models\MarketingCampaign;

class MarketingProgress
{
    public static function token(?int $campaignId = null): string
    {
        $query = MarketingCampaign::query()->orderBy('id')->withCount([
            'deliveries',
            'deliveries as pending_count' => fn ($q) => $q->where('status', 'pending'),
            'deliveries as sending_count' => fn ($q) => $q->where('status', 'sending'),
            'deliveries as sent_count' => fn ($q) => $q->where('status', 'sent'),
            'deliveries as failed_count' => fn ($q) => $q->where('status', 'failed'),
            'deliveries as skipped_count' => fn ($q) => $q->where('status', 'skipped'),
            'deliveries as cancelled_count' => fn ($q) => $q->where('status', 'cancelled'),
            'deliveries as clicked_count' => fn ($q) => $q->whereNotNull('clicked_at'),
            'deliveries as unsubscribed_count' => fn ($q) => $q->whereNotNull('unsubscribed_at'),
        ])->withSum('deliveries', 'click_count');
        if ($campaignId !== null) {
            $query->whereKey($campaignId);
        }

        return hash('sha256', $query->get(['id', 'status', 'scheduled_at', 'tested_at', 'updated_at'])->toJson());
    }
}
