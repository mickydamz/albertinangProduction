<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingCampaign extends Model
{
    protected $fillable = ['created_by', 'subject', 'message', 'offer_url', 'body_html', 'preview_text', 'button_text', 'template', 'images', 'brand', 'audience', 'selected_users', 'scheduled_at', 'tested_at'];

    protected $casts = ['images' => 'array', 'brand' => 'array', 'selected_users' => 'array', 'scheduled_at' => 'datetime', 'tested_at' => 'datetime'];

    public function deliveries()
    {
        return $this->hasMany(MarketingDelivery::class);
    }

    public function eligibleUsers()
    {
        $query = User::where('role', 'user')->whereNotNull('marketing_subscribed_at');
        if ($this->audience === 'recent') {
            $query->where('marketing_subscribed_at', '>=', now()->subDays(30));
        }
        if ($this->audience === 'selected') {
            $query->whereIn('id', $this->selected_users ?? []);
        }

        return $query;
    }
}
