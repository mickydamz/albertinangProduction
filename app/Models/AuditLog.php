<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'user_name', 'event',
        'auditable_type', 'auditable_id',
        'old_values', 'new_values',
        'url', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /** Short, human label for the affected model, e.g. "Order". */
    public function getModelLabelAttribute(): string
    {
        return class_basename($this->auditable_type);
    }
}