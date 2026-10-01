<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

/**
 * Drop this trait onto any Eloquent model to automatically record
 * create / update / delete events into the audit_logs table.
 *
 *   use App\Traits\Auditable;
 *   class Order extends Model { use Auditable; }
 *
 * Optionally limit which attributes are NOT logged (passwords, tokens):
 *   protected array $auditExclude = ['password', 'remember_token'];
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->writeAudit('created', [], $model->auditableAttributes($model->getAttributes()));
        });

        static::updated(function ($model) {
            // Only the fields that actually changed
            $changes = $model->auditableAttributes($model->getChanges());
            unset($changes['updated_at']);
            if (empty($changes)) return;

            $before = [];
            foreach (array_keys($changes) as $key) {
                $before[$key] = $model->getOriginal($key);
            }
            $model->writeAudit('updated', $before, $changes);
        });

        static::deleted(function ($model) {
            $model->writeAudit('deleted', $model->auditableAttributes($model->getOriginal()), []);
        });
    }

    /** Strip excluded / sensitive attributes before logging. */
    protected function auditableAttributes(array $attributes): array
    {
        $exclude = property_exists($this, 'auditExclude')
            ? $this->auditExclude
            : ['password', 'remember_token'];

        return array_diff_key($attributes, array_flip($exclude));
    }

    protected function writeAudit(string $event, array $old, array $new): void
    {
        // Don't let audit logging ever break the real operation.
        try {
            $user = Auth::user();

            AuditLog::create([
                'user_id'        => $user?->id,
                'user_name'      => $user?->name,
                'event'          => $event,
                'auditable_type' => static::class,
                'auditable_id'   => $this->getKey(),
                'old_values'     => $old ?: null,
                'new_values'     => $new ?: null,
                'url'            => request()?->fullUrl(),
                'ip_address'     => request()?->ip(),
                'user_agent'     => substr((string) request()?->userAgent(), 0, 512),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Audit log write failed: ' . $e->getMessage());
        }
    }

    /** Convenience relation: all audit entries for this record. */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable')->latest();
    }
}