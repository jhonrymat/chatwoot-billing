<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_gateway', // Nuevo
        'type',
        'is_default',
        'gateway_card_id', // Cambiado
        'gateway_customer_id', // Cambiado
        'last_four_digits',
        'card_brand',
        'expiration_month',
        'expiration_year',
        'cardholder_name',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeCreditCards($query)
    {
        return $query->where('type', 'credit_card');
    }

    public function scopeDebitCards($query)
    {
        return $query->where('type', 'debit_card');
    }

    // Accessors
    public function getMaskedNumberAttribute(): string
    {
        return $this->last_four_digits
            ? "**** **** **** {$this->last_four_digits}"
            : 'N/A';
    }

    public function getIsExpiredAttribute(): bool
    {
        if (!$this->expiration_month || !$this->expiration_year) {
            return false;
        }

        $expirationDate = now()->setYear($this->expiration_year)
            ->setMonth($this->expiration_month)
            ->endOfMonth();

        return $expirationDate->isPast();
    }

    public function getDisplayNameAttribute(): string
    {
        $brand = $this->card_brand ? ucfirst($this->card_brand) : 'Tarjeta';
        return "{$brand} {$this->masked_number}";
    }

    // Métodos de utilidad
    public function setAsDefault(): bool
    {
        // Quitar default de otros métodos del mismo usuario
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        return $this->update(['is_default' => true]);
    }

    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    public function scopeForGateway($query, string $gateway)
    {
        return $query->where('payment_gateway', $gateway);
    }
}
