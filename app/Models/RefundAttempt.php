<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundAttempt extends Model
{
    protected $guarded = [];
    protected $casts = ['amount' => 'decimal:2', 'completed_at' => 'datetime'];
    public function order() { return $this->belongsTo(Order::class); }
}
