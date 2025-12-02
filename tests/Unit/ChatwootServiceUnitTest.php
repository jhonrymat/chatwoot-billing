<?php

namespace Tests\Unit;

use App\Services\ChatwootService;
use PHPUnit\Framework\TestCase;

class ChatwootServiceUnitTest extends TestCase
{
    /** @test */
    public function it_constructs_correct_dashboard_url()
    {
        config([
            'chatwoot.url' => 'https://chatwoot.test',
            'chatwoot.api_key' => 'test_key',
        ]);

        $service = new ChatwootService();
        $url = $service->getDashboardUrl(123);

        $this->assertEquals('https://chatwoot.test/app/accounts/123/dashboard', $url);
    }

    /** @test */
    public function it_throws_exception_when_configuration_is_missing()
    {
        config([
            'chatwoot.url' => null,
            'chatwoot.api_key' => null,
        ]);

        $this->expectException(\RuntimeException::class);
        new ChatwootService();
    }
}
