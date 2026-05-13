<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class FrontendApiPathTest extends TestCase
{
    public function testFrontendUsesApiBasePathInsteadOfGenerateEndpointAsBase(): void
    {
        $script = file_get_contents(__DIR__ . '/../public/app.js');

        $this->assertIsString($script);
        $this->assertStringContainsString("const API_BASE = '/api';", $script);
        $this->assertStringContainsString("fetch(API_BASE + '/generate.php'", $script);
        $this->assertStringContainsString("fetch(API_BASE + '/prefill.php?lead_id='", $script);
        $this->assertStringNotContainsString('/api/generate.php/api/', $script);
        $this->assertStringNotContainsString("const API = '/api/generate.php';", $script);
    }
}
