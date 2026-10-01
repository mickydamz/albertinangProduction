<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingCheckout extends Model
{
    protected $fillable = [
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
        'items'        => 'array',
        'fulfillment'  => 'array',
        'coupon'       => 'array',
        'fulfilled_at' => 'datetime',
    ];
}
