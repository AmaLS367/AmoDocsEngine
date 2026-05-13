<?php

declare(strict_types=1);

namespace AmoDocGenerator\Security;

use RuntimeException;

final class RequestAuthenticator
{
    /** @var array<string, mixed> */
    private array $config;
    private GenerateTokenStore $tokenStore;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config, GenerateTokenStore $tokenStore)
    {
        $this->config = $config;
        $this->tokenStore = $tokenStore;
    }

    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed> $server
     */
    public function isAuthorized(string $rawBody, array $input, array $server): bool
    {
        $mode = $this->authMode();
        if ($mode === 'hmac') {
            return $this->validHmac($rawBody, $server);
        }
        if ($mode === 'either' && !empty($server['HTTP_X_SIGNATURE'])) {
            return $this->validHmac($rawBody, $server);
        }

        return $this->tokenStore->validate(
            (int)($input['lead_id'] ?? 0),
            (string)($input['generate_token'] ?? '')
        );
    }

    private function authMode(): string
    {
        $security = is_array($this->config['security'] ?? null) ? $this->config['security'] : [];
        $mode = (string)($security['generate_auth_mode'] ?? 'browser_token');

        return in_array($mode, ['browser_token', 'hmac', 'either'], true) ? $mode : 'browser_token';
    }

    /**
     * @param array<string, mixed> $server
     */
    private function validHmac(string $rawBody, array $server): bool
    {
        $secret = $this->hmacSecret();
        $signature = (string)($server['HTTP_X_SIGNATURE'] ?? '');
        if ($signature === '') {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $rawBody, $secret), $signature);
    }

    private function hmacSecret(): string
    {
        $security = is_array($this->config['security'] ?? null) ? $this->config['security'] : [];
        $secret = (string)($security['hmac_secret'] ?? ($this->config['hmac_secret'] ?? ''));
        if ($secret === '') {
            throw new RuntimeException('HMAC secret is required for HMAC authentication');
        }

        return $secret;
    }
}
