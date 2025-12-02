<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo('subject', 'model_type', 'model_id');
    }

    // Métodos estáticos de utilidad
    public static function log(
        string $action,
        ?Model $subject = null,
        ?string $description = null,
        array $properties = []
    ): self {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $subject ? get_class($subject) : null,
            'model_id' => $subject?->id,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
