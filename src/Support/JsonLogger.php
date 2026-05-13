<?php

declare(strict_types=1);

namespace AmoDocGenerator\Support;

final class JsonLogger
{
    private string $path;
    private int $dirMode;

    public function __construct(string $path, int $dirMode = 0775)
    {
        $this->path = $path;
        $this->dirMode = $dirMode;
    }

    public static function fromConfig(array $config, string $filename): self
    {
        $logDir = rtrim((string)$config['logs_path'], '/');

        return new self($logDir . '/' . $filename, (int)($config['dir_mode'] ?? 0775));
    }

    /**
     * @param array<string, mixed> $event
     */
    public function log(array $event): void
    {
        $dir = dirname($this->path);
        @is_dir($dir) || @mkdir($dir, $this->dirMode, true);
        file_put_contents(
            $this->path,
            json_encode($event, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n",
            FILE_APPEND
        );
    }
}
