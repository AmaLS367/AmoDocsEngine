<?php

declare(strict_types=1);

namespace AmoDocGenerator\Security;

final class GenerateTokenStore
{
    private string $path;
    private int $ttlSeconds;

    public function __construct(string $path, int $ttlSeconds)
    {
        $this->path = $path;
        $this->ttlSeconds = $ttlSeconds;
    }

    public static function fromConfig(array $config): self
    {
        $security = is_array($config['security'] ?? null) ? $config['security'] : [];
        $ttl = (int)($security['generate_token_ttl_seconds'] ?? 1800);
        $path = (string)($security['generate_token_path'] ?? (
            rtrim((string)($config['temp_data_path'] ?? (__DIR__ . '/../../data')), '/') . '/security/generate_tokens.json'
        ));

        return new self($path, $ttl > 0 ? $ttl : 1800);
    }

    public function issue(int $leadId): string
    {
        $tokens = $this->prune($this->load());
        $token = bin2hex(random_bytes(32));
        $tokens[$token] = [
            'lead_id' => $leadId,
            'expires_at' => time() + $this->ttlSeconds,
        ];
        $this->save($tokens);

        return $token;
    }

    public function validate(int $leadId, string $token): bool
    {
        if ($leadId <= 0 || $token === '') {
            return false;
        }

        $tokens = $this->prune($this->load());
        $this->save($tokens);

        if (!isset($tokens[$token])) {
            return false;
        }

        return (int)$tokens[$token]['lead_id'] === $leadId;
    }

    /**
     * @return array<string, array{lead_id:int,expires_at:int}>
     */
    private function load(): array
    {
        $data = json_decode((string)@file_get_contents($this->path), true);
        if (!is_array($data)) {
            return [];
        }

        $tokens = [];
        foreach ($data as $token => $row) {
            if (!is_string($token) || !is_array($row)) {
                continue;
            }
            $tokens[$token] = [
                'lead_id' => (int)($row['lead_id'] ?? 0),
                'expires_at' => (int)($row['expires_at'] ?? 0),
            ];
        }

        return $tokens;
    }

    /**
     * @param array<string, array{lead_id:int,expires_at:int}> $tokens
     * @return array<string, array{lead_id:int,expires_at:int}>
     */
    private function prune(array $tokens): array
    {
        $now = time();

        return array_filter($tokens, static function (array $row) use ($now): bool {
            return $row['expires_at'] >= $now;
        });
    }

    /**
     * @param array<string, array{lead_id:int,expires_at:int}> $tokens
     */
    private function save(array $tokens): void
    {
        $dir = dirname($this->path);
        @is_dir($dir) || @mkdir($dir, 0775, true);
        file_put_contents($this->path, json_encode($tokens, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}
