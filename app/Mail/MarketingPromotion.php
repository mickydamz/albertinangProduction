<?php

namespace App\Mail;

use App\Models\MarketingCampaign;
use App\Models\MarketingDelivery;
use App\Models\User;
use App\Services\MarketingBrand;
use App\Services\MarketingHtml;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;

class MarketingPromotion extends Mailable
{
    public $campaign;

    public $unsubscribeUrl;

    public $oneClickUrl;

    public $offerUrl;

    public $isTest;

    public function __construct(MarketingCampaign $campaign, User $user, ?MarketingDelivery $delivery = null, bool $isTest = false)
    {
        $this->oneClickUrl = URL::signedRoute('marketing.unsubscribe.oneclick', ['user' => $user->id] + ($delivery ? ['delivery' => $delivery->id] : []));
        $this->campaign = $campaign;
        $this->isTest = $isTest;
        $this->unsubscribeUrl = URL::signedRoute('marketing.unsubscribe', ['user' => $user->id] + ($delivery ? ['delivery' => $delivery->id] : []));
        $this->offerUrl = $delivery ? URL::signedRoute('marketing.click', ['delivery' => $delivery->id]) : ($campaign->offer_url ?: url('/shop'));
    }

    public function viewData(): array
    {
        return ['campaign' => $this->campaign, 'unsubscribeUrl' => $this->unsubscribeUrl, 'offerUrl' => $this->offerUrl, 'safeHtml' => MarketingHtml::clean($this->campaign->body_html), 'brand' => MarketingBrand::current(), 'isTest' => $this->isTest];
    }

    public function build()
    {
        return $this->from(config('marketing.from.address'), config('marketing.from.name'))->replyTo(config('marketing.reply_to'))
         ->subject(($this->isTest ? '[TEST] ' : '').$this->campaign->subject)->view('marketing.email')->text('marketing.plain')->with($this->viewData())
         ->withSymfonyMessage(function ($message) {
             $message->getHeaders()->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
             $message->getHeaders()->addTextHeader('List-Unsubscribe', '<'.$this->oneClickUrl.'>');
         });
    }
}
