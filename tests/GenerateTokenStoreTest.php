<?php

declare(strict_types=1);

use AmoDocGenerator\Security\GenerateTokenStore;
use PHPUnit\Framework\TestCase;

final class GenerateTokenStoreTest extends TestCase
{
    public function testIssuedTokenIsValidOnlyForTheSameLead(): void
    {
        $store = new GenerateTokenStore($this->path(), 60);
        $token = $store->issue(123);

        $this->assertTrue($store->validate(123, $token));
        $this->assertFalse($store->validate(124, $token));
        $this->assertFalse($store->validate(123, 'bad-token'));
    }

    public function testExpiredTokenIsRejected(): void
    {
        $path = $this->path();
        file_put_contents($path, json_encode([
            'old' => ['lead_id' => 123, 'expires_at' => time() - 1],
        ]));

        $store = new GenerateTokenStore($path, 60);

        $this->assertFalse($store->validate(123, 'old'));
    }

    private function path(): string
    {
        $dir = sys_get_temp_dir() . '/amodocs_tokens_' . uniqid('', true);
        mkdir($dir, 0777, true);

        return str_replace('\\', '/', $dir . '/tokens.json');
    }
}
