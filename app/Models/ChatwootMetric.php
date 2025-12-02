<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatwootMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'chatwoot_account_id',
        'total_conversations',
        'open_conversations',
        'resolved_conversations',
        'pending_conversations',
        'total_agents',
        'active_agents',
        'total_inboxes',
        'total_contacts',
        'avg_first_response_time',
        'avg_resolution_time',
        'metrics_date',
        'raw_data',
    ];

    protected $casts = [
        'total_conversations' => 'integer',
        'open_conversations' => 'integer',
        'resolved_conversations' => 'integer',
        'pending_conversations' => 'integer',
        'total_agents' => 'integer',
        'active_agents' => 'integer',
        'total_inboxes' => 'integer',
        'total_contacts' => 'integer',
        'avg_first_response_time' => 'integer',
        'avg_resolution_time' => 'integer',
        'metrics_date' => 'date',
        'raw_data' => 'array',
    ];

    // Relaciones
    public function chatwootAccount(): BelongsTo
    {
        return $this->belongsTo(ChatwootAccount::class);
    }

    // Scopes
    public function scopeForDate($query, string $date)
    {
        return $query->where('metrics_date', $date);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('metrics_date', 'desc');
    }

    // Accessors
    public function getFormattedFirstResponseTimeAttribute(): string
    {
        return $this->formatSeconds($this->avg_first_response_time);
    }

    public function getFormattedResolutionTimeAttribute(): string
    {
        return $this->formatSeconds($this->avg_resolution_time);
    }

    // Helper
    private function formatSeconds(?int $seconds): string
    {
        if (!$seconds) {
            return 'N/A';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }
}
