<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingCampaign extends Model
{
    protected $fillable = ['created_by', 'subject', 'message', 'offer_url'];

    public function deliveries()
    {
        return $this->hasMany(MarketingDelivery::class);
    }
}
