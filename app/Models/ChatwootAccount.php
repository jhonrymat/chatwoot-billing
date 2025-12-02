<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatwootAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subscription_id',
        'user_id',
        'chatwoot_account_id',
        'chatwoot_account_name',
        'chatwoot_user_id',
        'chatwoot_url',
        'chatwoot_dashboard_url',
        'status',
        'locale',
        'timezone',
        'last_synced_at',
        'sync_error',
    ];

    protected $casts = [
        'chatwoot_account_id' => 'integer',
        'chatwoot_user_id' => 'integer',
        'last_synced_at' => 'datetime',
    ];

    // Relaciones
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(ChatwootMetric::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeNeedsSyncing($query, int $minutes = 60)
    {
        return $query->where(function ($q) use ($minutes) {
            $q->whereNull('last_synced_at')
              ->orWhere('last_synced_at', '<', now()->subMinutes($minutes));
        });
    }

    // Accessors
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getIsSuspendedAttribute(): bool
    {
        return $this->status === 'suspended';
    }

    public function getFullDashboardUrlAttribute(): string
    {
        return $this->chatwoot_dashboard_url
            ?? config('chatwoot.url') . "/app/accounts/{$this->chatwoot_account_id}/dashboard";
    }

    // Métodos de utilidad
    public function activate(): bool
    {
        return $this->update(['status' => 'active']);
    }

    public function suspend(): bool
    {
        return $this->update(['status' => 'suspended']);
    }

    public function markAsSynced(): bool
    {
        return $this->update([
            'last_synced_at' => now(),
            'sync_error' => null,
        ]);
    }

    public function recordSyncError(string $error): bool
    {
        return $this->update(['sync_error' => $error]);
    }

    public function getLatestMetrics(): ?ChatwootMetric
    {
        return $this->metrics()->latest('metrics_date')->first();
    }
}
