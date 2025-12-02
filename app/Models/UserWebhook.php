<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWebhook extends Model
{
    protected $fillable = [
        'user_id', 'name', 'url', 'event', 'secret',
        'is_active', 'headers', 'max_retries', 'timeout',
        'success_count', 'failure_count', 'last_triggered_at'
    ];

    protected $casts = [
        'headers' => 'array',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incrementSuccess(): void
    {
        $this->increment('success_count');
        $this->update(['last_triggered_at' => now()]);
    }

    public function incrementFailure(): void
    {
        $this->increment('failure_count');
    }
}
