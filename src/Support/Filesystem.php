<?php

declare(strict_types=1);

namespace AmoDocGenerator\Support;

use RuntimeException;

final class Filesystem
{
    public static function ensureDirectory(string $dir, int $mode = 0775, string $label = 'Directory'): void
    {
        if (is_dir($dir)) {
            return;
        }

        if (file_exists($dir)) {
            throw new RuntimeException(sprintf('%s "%s" exists and is not a directory.', $label, $dir));
        }

        $parent = dirname($dir);
        if ($parent !== $dir && $parent !== '' && !is_dir($parent)) {
            self::ensureDirectory($parent, $mode, 'Parent directory');
        }

        if (!mkdir($dir, $mode) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('%s "%s" could not be created.', $label, $dir));
        }
    }
}
