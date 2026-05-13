<?php

declare(strict_types=1);

use AmoDocGenerator\Storage\PrefillCache;
use PHPUnit\Framework\TestCase;

final class PrefillCacheTest extends TestCase
{
    public function testReadsDefaultPayloadWhenCacheFileIsMissing(): void
    {
        $cache = new PrefillCache($this->dir());

        $this->assertSame([
            'template' => 'order',
            'discount' => 0,
            'products' => [],
            'saved_at' => 0,
        ], $cache->read(123));
    }

    public function testWritesAndReadsPayload(): void
    {
        $cache = new PrefillCache($this->dir());
        $products = [['name' => 'Service', 'qty' => 1, 'unit_price' => 100]];

        $cache->write(123, 'act', 50, $products);
        $payload = $cache->read(123);

        $this->assertSame('act', $payload['template']);
        $this->assertSame(50, $payload['discount']);
        $this->assertSame($products, $payload['products']);
        $this->assertGreaterThan(0, $payload['saved_at']);
    }

    public function testThrowsWhenCacheDirectoryCannotBeCreated(): void
    {
        $blockedPath = $this->dir();
        file_put_contents($blockedPath, 'not a directory');
        $cache = new PrefillCache($blockedPath . '/cache');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Parent directory');

        $cache->write(123, 'order', 0, []);
    }

    private function dir(): string
    {
        return str_replace('\\', '/', sys_get_temp_dir() . '/amodocs_cache_' . uniqid('', true));
    }
}
