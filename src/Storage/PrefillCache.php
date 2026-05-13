<?php

declare(strict_types=1);

namespace AmoDocGenerator\Storage;

final class PrefillCache
{
    private string $cacheDir;
    private int $dirMode;

    public function __construct(string $cacheDir, int $dirMode = 0775)
    {
        $this->cacheDir = rtrim($cacheDir, '/');
        $this->dirMode = $dirMode;
    }

    public static function fromConfig(array $config): self
    {
        $cacheDir = (string)($config['cache_path'] ?? (
            rtrim((string)($config['temp_data_path'] ?? (__DIR__ . '/../../data')), '/') . '/cache'
        ));

        return new self($cacheDir, (int)($config['dir_mode'] ?? 0775));
    }

    /**
     * @return array<string, mixed>
     */
    public function read(int $leadId): array
    {
        $default = ['template' => 'order', 'discount' => 0, 'products' => [], 'saved_at' => 0];
        if ($leadId <= 0) {
            return $default;
        }

        $file = $this->path($leadId);
        if (!is_file($file)) {
            return $default;
        }

        $data = json_decode((string)@file_get_contents($file), true);

        return is_array($data) ? $data : $default;
    }

    /**
     * @param array<int, array<string, mixed>> $products
     */
    public function write(int $leadId, string $template, int $discount, array $products): void
    {
        @is_dir($this->cacheDir) || @mkdir($this->cacheDir, $this->dirMode, true);
        file_put_contents($this->path($leadId), json_encode([
            'saved_at' => time(),
            'template' => $template,
            'discount' => $discount,
            'products' => $products,
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    private function path(int $leadId): string
    {
        return $this->cacheDir . '/' . $leadId . '.json';
    }
}
