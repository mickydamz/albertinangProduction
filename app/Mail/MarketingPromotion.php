<?php

namespace App\Mail;

use App\Models\MarketingCampaign;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;

class MarketingPromotion extends Mailable
{
    public $campaign;

    public $unsubscribeUrl;

    public function __construct(MarketingCampaign $campaign, User $user)
    {
        $this->campaign = $campaign;
        $this->unsubscribeUrl = URL::signedRoute('marketing.unsubscribe', ['user' => $user->id]);
    }

    public function build()
    {
        return $this->subject($this->campaign->subject)->view('marketing.email');
    }
}
