<?php

namespace Tests\Feature;

use App\Services\ChatwootService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatwootServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ChatwootService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Configurar variables de entorno para testing
        config([
            'chatwoot.url' => 'https://chatwoot.test',
            'chatwoot.api_key' => 'test_api_key',
        ]);

        $this->service = new ChatwootService();
    }

    /** @test */
    public function it_can_create_an_account()
    {
        Http::fake([
            'chatwoot.test/platform/api/v1/accounts' => Http::response([
                'id' => 123,
                'name' => 'Test Account',
                'locale' => 'es',
            ], 200),
        ]);

        $result = $this->service->createAccount('Test Account', 'es');

        $this->assertEquals(123, $result['id']);
        $this->assertEquals('Test Account', $result['name']);
    }

    /** @test */
    public function it_can_create_a_user()
    {
        Http::fake([
            'chatwoot.test/platform/api/v1/accounts/123/account_users' => Http::response([
                'id' => 456,
                'email' => 'test@example.com',
            ], 200),
        ]);

        $result = $this->service->createUser(123, [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertEquals(456, $result['id']);
        $this->assertEquals('test@example.com', $result['email']);
    }

    /** @test */
    public function it_handles_api_errors_gracefully()
    {
        Http::fake([
            'chatwoot.test/platform/api/v1/accounts' => Http::response([], 500),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->service->createAccount('Test Account');
    }

    /** @test */
    public function it_can_test_connection()
    {
        Http::fake([
            'chatwoot.test/platform/api/v1/accounts' => Http::response([], 200),
        ]);

        $this->assertTrue($this->service->testConnection());
    }

    /** @test */
    public function it_can_get_comprehensive_metrics()
    {
        Http::fake([
            'chatwoot.test/*' => Http::response([
                'meta' => ['count' => 100, 'all_count' => 100],
                'payload' => [],
            ], 200),
        ]);

        $metrics = $this->service->getComprehensiveMetrics(123);

        $this->assertArrayHasKey('conversations', $metrics);
        $this->assertArrayHasKey('agents', $metrics);
        $this->assertArrayHasKey('inboxes', $metrics);
        $this->assertArrayHasKey('contacts', $metrics);
    }
}
