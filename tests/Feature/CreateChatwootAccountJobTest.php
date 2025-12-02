<?php

namespace Tests\Feature;

use App\Jobs\CreateChatwootAccountJob;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\ChatwootAccount;
use App\Services\ChatwootService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CreateChatwootAccountJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_chatwoot_account_for_subscription()
    {
        // Arrange
        Queue::fake();

        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);

        Http::fake([
            '*/platform/api/v1/accounts' => Http::response([
                'id' => 123,
                'name' => 'Test Account',
            ], 200),
            '*/platform/api/v1/accounts/123/account_users' => Http::response([
                'id' => 456,
                'email' => $user->email,
            ], 200),
            '*/api/v1/accounts/123/inboxes' => Http::response([
                'id' => 789,
                'name' => 'Canal Web',
            ], 200),
        ]);

        // Act
        $job = new CreateChatwootAccountJob($subscription);
        $job->handle(new ChatwootService());

        // Assert
        $this->assertDatabaseHas('chatwoot_accounts', [
            'subscription_id' => $subscription->id,
            'user_id' => $user->id,
            'chatwoot_account_id' => 123,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function it_does_not_create_duplicate_accounts()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        // Crear cuenta existente
        ChatwootAccount::factory()->create([
            'subscription_id' => $subscription->id,
            'user_id' => $user->id,
            'chatwoot_account_id' => 999,
        ]);

        $initialCount = ChatwootAccount::count();

        // Intentar crear otra
        $job = new CreateChatwootAccountJob($subscription);
        $job->handle(new ChatwootService());

        // No debería crear otra cuenta
        $this->assertEquals($initialCount, ChatwootAccount::count());
    }
}
