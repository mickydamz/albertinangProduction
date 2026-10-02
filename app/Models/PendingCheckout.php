<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingCheckout extends Model
{
    protected $fillable = [
        'gateway', 'payment_intent_id', 'gateway_amount', 'expires_at', 'payment_confirmed_at', 'recovery_error',
        'request_key',
        'reference',
        'user_id',
        'customer_email',
        'total_ngn',
        'items',
        'fulfillment',
        'coupon',
        'fulfilled_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime', 'payment_confirmed_at' => 'datetime',
        'items'        => 'array',
        'fulfillment'  => 'array',
        'coupon'       => 'array',
        'fulfilled_at' => 'datetime',
    ];
}
