<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'resource_type',
        'resource_id',
        'payload',
        'status',
        'processed_at',
        'error_message',
        'attempts',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
        'attempts' => 'integer',
    ];

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Métodos de utilidad
    public function markAsProcessed(): bool
    {
        return $this->update([
            'status' => 'processed',
            'processed_at' => now(),
        ]);
    }

    public function markAsFailed(string $error): bool
    {
        return $this->update([
            'status' => 'failed',
            'error_message' => $error,
            'attempts' => $this->attempts + 1,
        ]);
    }

    public function canRetry(int $maxAttempts = 3): bool
    {
        return $this->attempts < $maxAttempts;
    }
}
