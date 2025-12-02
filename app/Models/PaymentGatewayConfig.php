<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PaymentGatewayConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway_name',
        'display_name',
        'is_enabled',
        'is_default',
        'credentials',
        'settings',
        'supported_countries',
        'description',
        'logo_url',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_default' => 'boolean',
        'credentials' => 'array',
        'settings' => 'array',
        'supported_countries' => 'array',
    ];

    // Relaciones
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'payment_gateway', 'gateway_name');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'payment_gateway', 'gateway_name');
    }

    // Scopes
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeForCountry($query, string $countryCode)
    {
        return $query->whereJsonContains('supported_countries', $countryCode);
    }

    // Accessors & Mutators
    public function getCredentialsAttribute($value)
    {
        if (!$value) {
            return null;
        }

        $decrypted = json_decode($value, true);

        // Desencriptar valores sensibles
        if (isset($decrypted['access_token'])) {
            try {
                $decrypted['access_token'] = Crypt::decryptString($decrypted['access_token']);
            } catch (\Exception $e) {
                // Si no está encriptado, retornar tal cual (para compatibilidad)
            }
        }

        return $decrypted;
    }

    public function setCredentialsAttribute($value)
    {
        if (!$value) {
            $this->attributes['credentials'] = null;
            return;
        }

        // Encriptar valores sensibles antes de guardar
        if (isset($value['access_token'])) {
            try {
                $value['access_token'] = Crypt::encryptString($value['access_token']);
            } catch (\Exception $e) {
                // Si ya está encriptado, mantener
            }
        }

        $this->attributes['credentials'] = json_encode($value);
    }

    // Métodos de utilidad
    public function supportsCountry(string $countryCode): bool
    {
        return in_array($countryCode, $this->supported_countries ?? []);
    }

    public function enable(): bool
    {
        return $this->update(['is_enabled' => true]);
    }

    public function disable(): bool
    {
        return $this->update(['is_enabled' => false]);
    }

    public function setAsDefault(): bool
    {
        // Quitar default de otros gateways
        static::where('id', '!=', $this->id)
              ->update(['is_default' => false]);

        return $this->update(['is_default' => true]);
    }

    public function getDecryptedCredentials(): array
    {
        return $this->credentials ?? [];
    }

    public function updateCredentials(array $credentials): bool
    {
        $this->credentials = $credentials;
        return $this->save();
    }
}
