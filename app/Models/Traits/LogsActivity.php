<?php

// ABOUTME: Trait that auto-logs create, update, and delete events to the activity_logs table.
// ABOUTME: Apply to any Eloquent model to track admin actions with old/new value diffs.

namespace App\Models\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * Boot the trait and register model event listeners.
     */
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            static::logActivity($model, 'created');
        });

        static::updated(function ($model) {
            static::logActivity($model, 'updated', $model->getChanges(), $model->getOriginal());
        });

        static::deleted(function ($model) {
            static::logActivity($model, 'deleted');
        });
    }

    /**
     * Write an activity log entry for the given model event.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $action  created|updated|deleted
     * @param  array  $new  Changed attributes (for updates)
     * @param  array  $original  Original attributes before change (for updates)
     */
    protected static function logActivity($model, string $action, array $new = [], array $original = []): void
    {
        $changes = null;

        if ($action === 'updated' && ! empty($new)) {
            $changes = [];
            foreach ($new as $key => $value) {
                // Skip updated_at timestamp noise
                if ($key === 'updated_at') {
                    continue;
                }
                $changes[$key] = [
                    'old' => $original[$key] ?? null,
                    'new' => $value,
                ];
            }
            // If only updated_at changed, skip logging entirely
            if (empty($changes)) {
                return;
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'changes' => $changes,
            'ip_address' => request()->ip(),
        ]);
    }
}
