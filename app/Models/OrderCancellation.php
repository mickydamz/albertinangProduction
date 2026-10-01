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
        'refunded_at',
    ];

    protected $casts = [
        'refunded_at' => 'datetime',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function user()  { return $this->belongsTo(User::class); }
}