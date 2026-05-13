<?php

declare(strict_types=1);

use AmoDocGenerator\AmoCrm\AmoCrmClient;
use PHPUnit\Framework\TestCase;

final class AmoCrmClientTest extends TestCase
{
    public function testRefreshesTokenAndRetriesRequestAfterUnauthorizedResponse(): void
    {
        $tokenPath = $this->writeTokens(['access_token' => 'old', 'refresh_token' => 'refresh']);
        $calls = [];
        $http = function (string $method, string $url, array $headers, $payload, int $timeout) use (&$calls): array {
            $calls[] = compact('method', 'url', 'headers', 'payload', 'timeout');

            if (count($calls) === 1) {
                return [401, '{"error":"expired"}'];
            }
            if (count($calls) === 2) {
                return [200, '{"access_token":"new","refresh_token":"refresh2"}'];
            }

            return [200, '{"id":123}'];
        };

        $client = new AmoCrmClient($this->config(), $tokenPath, $http);
        $response = $client->get('/api/v4/leads/123');

        $this->assertSame(['id' => 123], $response);
        $this->assertCount(3, $calls);
        $this->assertSame('https://example.amocrm.ru/oauth2/access_token', $calls[1]['url']);
        $this->assertContains('Authorization: Bearer new', $calls[2]['headers']);

        $saved = json_decode((string)file_get_contents($tokenPath), true);
        $this->assertSame('new', $saved['access_token']);
        $this->assertArrayHasKey('created_at', $saved);
    }

    public function testThrowsNormalizedErrorForFailedAmoResponse(): void
    {
        $tokenPath = $this->writeTokens(['access_token' => 'token']);
        $client = new AmoCrmClient($this->config(), $tokenPath, static function (): array {
            return [500, '{"detail":"boom"}'];
        });

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('AMO 500: {"detail":"boom"}');

        $client->get('/api/v4/leads/1');
    }

    /**
     * @param array<string, mixed> $tokens
     */
    private function writeTokens(array $tokens): string
    {
        $path = tempnam(sys_get_temp_dir(), 'amo_tokens_');
        self::assertIsString($path);
        file_put_contents($path, json_encode($tokens));

        return $path;
    }

    /**
     * @return array<string, string>
     */
    private function config(): array
    {
        return [
            'base_domain' => 'https://example.amocrm.ru',
            'client_id' => 'client',
            'client_secret' => 'secret',
            'redirect_uri' => 'https://example.test/oauth.php',
        ];
    }
}
