<?php

declare(strict_types=1);

use AmoDocGenerator\Support\JsonLogger;
use PHPUnit\Framework\TestCase;

final class JsonLoggerTest extends TestCase
{
    public function testAppendsJsonEventToLogFile(): void
    {
        $path = str_replace('\\', '/', sys_get_temp_dir() . '/amodocs_logs_' . uniqid('', true) . '/generate.log');
        $logger = new JsonLogger($path);

        $logger->log(['EX' => 'boom', 'line' => 12]);

        $this->assertStringContainsString('"EX": "boom"', (string)file_get_contents($path));
        $this->assertStringContainsString('"line": 12', (string)file_get_contents($path));
    }

    public function testTriggersWarningWhenLogDirectoryCannotBeCreated(): void
    {
        $blockedPath = str_replace('\\', '/', sys_get_temp_dir() . '/amodocs_log_blocked_' . uniqid('', true));
        file_put_contents($blockedPath, 'not a directory');
        $logger = new JsonLogger($blockedPath . '/generate.log');
        $warning = null;

        set_error_handler(static function (int $severity, string $message) use (&$warning): bool {
            $warning = ['severity' => $severity, 'message' => $message];

            return true;
        });

        try {
            $logger->log(['EX' => 'boom']);
        } finally {
            restore_error_handler();
        }

        $this->assertSame(E_USER_WARNING, $warning['severity'] ?? null);
        $this->assertStringContainsString('Log directory', $warning['message'] ?? '');
    }
}
