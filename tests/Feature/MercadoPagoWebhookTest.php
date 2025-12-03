<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MercadoPagoWebhookTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_processes_approved_payment_webhook()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        // Simular webhook de pago aprobado
        $response = $this->postJson('/webhook/mercadopago', [
            'type' => 'payment',
            'action' => 'payment.created',
            'data' => [
                'id' => 'test_payment_123',
            ],
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function it_creates_subscription_on_approved_payment()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        // Crear suscripción pendiente
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
        ]);

        // Simular webhook
        $this->postJson('/webhook/mercadopago', [
            'type' => 'payment',
            'data' => ['id' => 'payment_123'],
        ]);

        $subscription->refresh();
        $this->assertEquals('active', $subscription->status);
    }
}
