<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'status',
        'reason',
        'evidence_path',
        'admin_notes',
        'reviewed_at',
        'refund_id',
        'refund_status',
        'refund_amount',
        'refunded_at',
        'refund_failure_reason',
    ];

    protected $casts = [
        'reviewed_at'   => 'datetime',
        'refunded_at'   => 'datetime',
        'refund_amount' => 'decimal:2',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function user()  { return $this->belongsTo(User::class); }
}