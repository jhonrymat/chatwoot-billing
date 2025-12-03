<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscriptionFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_plans()
    {
        $user = User::factory()->create();
        Plan::factory()->count(3)->create(['is_active' => true]);

        $response = $this->actingAs($user)
            ->get('/plans');

        $response->assertStatus(200);
        $response->assertViewHas('plans');
    }

    /** @test */
    public function user_can_start_checkout()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $response = $this->actingAs($user)
            ->post("/subscriptions/checkout/{$plan->id}");

        $response->assertRedirect(); // Redirige a MercadoPago
    }

    /** @test */
    public function user_cannot_subscribe_twice()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        // Crear suscripción activa
        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)
            ->post("/subscriptions/checkout/{$plan->id}");

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function user_can_cancel_subscription()
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)
            ->post('/subscription/cancel');

        $subscription->refresh();
        $this->assertEquals('cancelled', $subscription->status);
    }
}
