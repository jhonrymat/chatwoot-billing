<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'user_id',
        'payment_method_id',
        'payment_gateway', // Nuevo
        'amount',
        'currency',
        'status',
        'gateway_payment_id', // Cambiado
        'gateway_status', // Cambiado
        'gateway_status_detail', // Cambiado
        'payment_method_id_gateway', // Cambiado
        'payment_type',
        'metadata',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'paid_at' => 'datetime',
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

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === 'rejected';
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0, ',', '.') . ' ' . $this->currency;
    }

    // Métodos de utilidad
    public function markAsApproved(): bool
    {
        return $this->update([
            'status' => 'approved',
            'paid_at' => now(),
        ]);
    }

    public function markAsRejected(): bool
    {
        return $this->update(['status' => 'rejected']);
    }

    public function refund(): bool
    {
        return $this->update(['status' => 'refunded']);
    }


    public function scopeForGateway($query, string $gateway)
    {
        return $query->where('payment_gateway', $gateway);
    }
}
