<?php

namespace Database\Factories;

use App\Models\ChatwootAccount;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatwootAccountFactory extends Factory
{
    protected $model = ChatwootAccount::class;

    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'user_id' => User::factory(),
            'chatwoot_account_id' => $this->faker->numberBetween(1, 10000),
            'chatwoot_account_name' => $this->faker->company,
            'chatwoot_user_id' => $this->faker->numberBetween(1, 10000),
            'chatwoot_url' => 'https://chatwoot.test',
            'chatwoot_dashboard_url' => 'https://chatwoot.test/app/accounts/123/dashboard',
            'status' => 'active',
            'locale' => 'es',
            'timezone' => 'America/Bogota',
            'last_synced_at' => now(),
        ];
    }

    public function suspended(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
        ]);
    }

    public function active(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }
}
