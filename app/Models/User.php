<?php

// ============================================
// app/Models/User.php
// ============================================

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'company_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relaciones
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
                    ->where('status', 'active')
                    ->latest();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function defaultPaymentMethod(): HasOne
    {
        return $this->hasOne(PaymentMethod::class)
                    ->where('is_default', true);
    }

    public function chatwootAccounts(): HasMany
    {
        return $this->hasMany(ChatwootAccount::class);
    }

    public function activeChatwootAccount(): HasOne
    {
        return $this->hasOne(ChatwootAccount::class)
                    ->where('status', 'active')
                    ->latest();
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Métodos de utilidad
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    public function getCurrentPlan(): ?Plan
    {
        return $this->activeSubscription?->plan;
    }

    public function hasChatwootAccount(): bool
    {
        return $this->activeChatwootAccount()->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isSubscriber(): bool
    {
        return $this->hasRole('subscriber');
    }

    public function canAccessFilament(): bool
    {
        return $this->hasAnyRole(['admin', 'subscriber']);
    }

    // Para Filament
    public function getFilamentName(): string
    {
        return $this->name;
    }
}
