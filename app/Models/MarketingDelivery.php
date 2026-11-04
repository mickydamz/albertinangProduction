<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingDelivery extends Model
{
    protected $guarded = [];

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class, 'marketing_campaign_id');
    }
}
