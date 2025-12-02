<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'payment_gateway', // Nuevo
        'status',
        'started_at',
        'ends_at',
        'cancelled_at',
        'trial_ends_at',
        'gateway_subscription_id', // Cambiado
        'gateway_customer_id', // Cambiado
        'gateway_preapproval_id', // Cambiado
        'next_billing_date',
        'last_payment_date',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'next_billing_date' => 'date',
        'last_payment_date' => 'date',
    ];

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function chatwootAccount(): HasOne
    {
        return $this->hasOne(ChatwootAccount::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeOnTrial($query)
    {
        return $query->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', now());
    }

    public function scopeUpcomingRenewal($query, int $days = 3)
    {
        return $query->active()
            ->whereNotNull('next_billing_date')
            ->whereBetween('next_billing_date', [
                now()->toDateString(),
                now()->addDays($days)->toDateString()
            ]);
    }

    // Accessors & Mutators
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->status === 'expired';
    }

    public function getIsSuspendedAttribute(): bool
    {
        return $this->status === 'suspended';
    }

    public function getIsOnTrialAttribute(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function getDaysUntilRenewalAttribute(): ?int
    {
        if (!$this->next_billing_date) {
            return null;
        }

        return now()->diffInDays($this->next_billing_date, false);
    }

    // Métodos de utilidad
    public function activate(): bool
    {
        return $this->update([
            'status' => 'active',
            'started_at' => $this->started_at ?? now(),
        ]);
    }

    public function cancel(): bool
    {
        return $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    public function suspend(): bool
    {
        return $this->update(['status' => 'suspended']);
    }

    public function expire(): bool
    {
        return $this->update(['status' => 'expired']);
    }

    public function resume(): bool
    {
        return $this->update([
            'status' => 'active',
            'cancelled_at' => null,
        ]);
    }

    public function hasChatwootAccount(): bool
    {
        return $this->chatwootAccount()->exists();
    }
    public function getPaymentGateway(): string
    {
        return $this->payment_gateway ?? 'mercadopago';
    }

}
