<?php

// ABOUTME: Eloquent model for the activity_logs table (admin audit trail).
// ABOUTME: Records who did what, when, and what changed on tracked models.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    /**
     * This model only uses created_at (no updated_at).
     */
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'changes',
        'ip_address',
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * The user who performed the action.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The tracked model (polymorphic manual lookup).
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function subject()
    {
        if (class_exists($this->model_type)) {
            return $this->model_type::find($this->model_id);
        }

        return null;
    }

    /**
     * Get a human-readable short name for the model type.
     *
     * @return string e.g. "Product", "Category"
     */
    public function getModelLabelAttribute(): string
    {
        return class_basename($this->model_type);
    }
}
