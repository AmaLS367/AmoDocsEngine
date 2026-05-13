<?php

declare(strict_types=1);

use AmoDocGenerator\Security\GenerateTokenStore;
use AmoDocGenerator\Security\RequestAuthenticator;
use PHPUnit\Framework\TestCase;

final class RequestAuthenticatorTest extends TestCase
{
    public function testRejectsMissingBrowserToken(): void
    {
        $auth = new RequestAuthenticator([], new GenerateTokenStore($this->path(), 60));

        $this->assertFalse($auth->isAuthorized('{"lead_id":123}', ['lead_id' => 123], []));
    }

    public function testRejectsBrowserTokenForAnotherLead(): void
    {
        $store = new GenerateTokenStore($this->path(), 60);
        $token = $store->issue(123);
        $auth = new RequestAuthenticator([], $store);

        $this->assertFalse($auth->isAuthorized('', ['lead_id' => 124, 'generate_token' => $token], []));
    }

    public function testAcceptsValidBrowserToken(): void
    {
        $store = new GenerateTokenStore($this->path(), 60);
        $token = $store->issue(123);
        $auth = new RequestAuthenticator([], $store);

        $this->assertTrue($auth->isAuthorized('', ['lead_id' => 123, 'generate_token' => $token], []));
    }

    public function testHmacModeRequiresConfiguredSecret(): void
    {
        $auth = new RequestAuthenticator(['security' => ['generate_auth_mode' => 'hmac']], new GenerateTokenStore($this->path(), 60));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('HMAC secret is required');

        $auth->isAuthorized('{"lead_id":123}', ['lead_id' => 123], ['HTTP_X_SIGNATURE' => 'abc']);
    }

    public function testAcceptsValidHmacSignatureInHmacMode(): void
    {
        $raw = '{"lead_id":123}';
        $secret = 'secret';
        $auth = new RequestAuthenticator(
            ['security' => ['generate_auth_mode' => 'hmac', 'hmac_secret' => $secret]],
            new GenerateTokenStore($this->path(), 60)
        );

        $this->assertTrue($auth->isAuthorized($raw, ['lead_id' => 123], [
            'HTTP_X_SIGNATURE' => hash_hmac('sha256', $raw, $secret),
        ]));
    }

    private function path(): string
    {
        $dir = sys_get_temp_dir() . '/amodocs_auth_' . uniqid('', true);
        mkdir($dir, 0777, true);

        return str_replace('\\', '/', $dir . '/tokens.json');
    }
}
