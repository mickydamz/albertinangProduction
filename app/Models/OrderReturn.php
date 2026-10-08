<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    protected $fillable = [
        'workflow_stage',
        'return_instructions',
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

    public function stage(): string
    {
        if ($this->workflow_stage) return $this->workflow_stage;
        if ($this->refund_status || $this->status === 'refunded') return 'refund_requested';
        return $this->status === 'approved' ? 'approved' : ($this->status === 'rejected' ? 'rejected' : 'requested');
    }
    public function stageLabel(): string
    {
        return ['requested'=>'Return requested','approved'=>'Return approved — send the goods back',
            'received'=>'Goods received','inspected'=>'Inspection completed','rejected'=>'Return rejected',
            'refund_requested'=>'Return refund requested'][$this->stage()] ?? 'Return being reviewed';
    }
    public function order() { return $this->belongsTo(Order::class); }
    public function user()  { return $this->belongsTo(User::class); }
}