<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'billing_cycle',
        'max_agents',
        'max_inboxes',
        'max_contacts',
        'max_conversations_per_month',
        'features',
        'is_active',
        'is_popular',
        'sort_order',
        'gateway_plan_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'sort_order' => 'integer',
        'max_agents' => 'integer',
        'max_inboxes' => 'integer',
        'max_contacts' => 'integer',
        'max_conversations_per_month' => 'integer',
    ];

    // Relaciones
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, ',', '.') . ' ' . $this->currency;
    }

    public function getIsMonthlyAttribute(): bool
    {
        return $this->billing_cycle === 'monthly';
    }

    public function getIsYearlyAttribute(): bool
    {
        return $this->billing_cycle === 'yearly';
    }

    // Métodos de utilidad
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    public function canHaveAgents(int $count): bool
    {
        return $count <= $this->max_agents;
    }

    public function canHaveInboxes(int $count): bool
    {
        return $count <= $this->max_inboxes;
    }

    public function canHaveContacts(int $count): bool
    {
        return $count <= $this->max_contacts;
    }
}
