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
}
