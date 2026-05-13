<?php

declare(strict_types=1);

namespace AmoDocGenerator\AmoCrm;

use RuntimeException;

final class AmoCrmClient
{
    /** @var array<string, mixed> */
    private array $config;
    private string $tokenPath;
    /** @var array<string, mixed> */
    private array $tokens;
    /** @var callable */
    private $http;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config, string $tokenPath, ?callable $http = null)
    {
        $this->config = $config;
        $this->tokenPath = $tokenPath;
        $loaded = json_decode((string)@file_get_contents($tokenPath), true);
        $this->tokens = is_array($loaded) ? $loaded : [];
        $this->http = $http ?? [$this, 'curlRequest'];
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $pathOrUrl): array
    {
        return $this->request('GET', $pathOrUrl);
    }

    /**
     * @param mixed $payload
     * @return array<string, mixed>
     */
    public function post(string $pathOrUrl, $payload): array
    {
        return $this->request('POST', $pathOrUrl, $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function delete(string $pathOrUrl): array
    {
        return $this->request('DELETE', $pathOrUrl);
    }

    /**
     * @param mixed $payload
     * @return array<string, mixed>
     */
    private function request(string $method, string $pathOrUrl, $payload = null): array
    {
        [$code, $body] = $this->sendAuthorized($method, $this->url($pathOrUrl), $payload);

        if ($code === 401) {
            $this->refreshToken();
            [$code, $body] = $this->sendAuthorized($method, $this->url($pathOrUrl), $payload);
        }

        if ($code < 200 || $code >= 300) {
            throw new RuntimeException("AMO {$code}: {$body}");
        }

        if (trim((string)$body) === '') {
            return [];
        }

        $json = json_decode((string)$body, true);
        if (!is_array($json)) {
            throw new RuntimeException('AMO bad JSON');
        }

        return $json;
    }

    /**
     * @param mixed $payload
     * @return array{0:int,1:string}
     */
    private function sendAuthorized(string $method, string $url, $payload = null): array
    {
        $headers = ['Authorization: Bearer ' . ($this->tokens['access_token'] ?? '')];
        if ($payload !== null) {
            $headers[] = 'Content-Type: application/json';
        }

        return ($this->http)($method, $url, $headers, $payload, 25);
    }

    private function refreshToken(): void
    {
        $payload = [
            'client_id' => $this->config['client_id'] ?? '',
            'client_secret' => $this->config['client_secret'] ?? '',
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->tokens['refresh_token'] ?? '',
            'redirect_uri' => $this->config['redirect_uri'] ?? '',
        ];

        [$code, $body] = ($this->http)(
            'POST',
            $this->url('/oauth2/access_token'),
            ['Content-Type: application/json'],
            $payload,
            20
        );

        if ($code !== 200) {
            throw new RuntimeException("REFRESH {$code}: {$body}");
        }

        $newTokens = json_decode((string)$body, true);
        if (!is_array($newTokens) || empty($newTokens['access_token'])) {
            throw new RuntimeException('REFRESH: empty access_token');
        }

        $this->tokens = $newTokens;
        $this->tokens['created_at'] = time();
        file_put_contents(
            $this->tokenPath,
            json_encode($this->tokens, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }

    private function url(string $pathOrUrl): string
    {
        if (preg_match('~^https?://~', $pathOrUrl) === 1) {
            return $pathOrUrl;
        }

        return rtrim((string)$this->config['base_domain'], '/') . '/' . ltrim($pathOrUrl, '/');
    }

    /**
     * @param array<int, string> $headers
     * @param mixed $payload
     * @return array{0:int,1:string}
     */
    private function curlRequest(string $method, string $url, array $headers, $payload, int $timeout): array
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => $timeout,
        ]);

        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
        }

        $response = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [$code, $response === false ? '' : (string)$response];
    }
}
