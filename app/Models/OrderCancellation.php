<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCancellation extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'reason',
        'status',
        'admin_notes',
        'refund_id',
        'refund_status',
        'refund_amount',
        'refunded_at',
        'refund_failure_reason',
    ];

    protected $casts = [
        'refunded_at'   => 'datetime',
        'refund_amount' => 'decimal:2',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function user()  { return $this->belongsTo(User::class); }
}