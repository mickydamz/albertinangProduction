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
        'refunded_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function user()  { return $this->belongsTo(User::class); }
}