<?php

declare(strict_types=1);

namespace AmoDocGenerator\Documents;

use InvalidArgumentException;
use RuntimeException;

final class TemplateRegistry
{
    private string $templateDir;
    /** @var array<string, string> */
    private array $templates;

    /**
     * @param array<string, string> $templates
     */
    public function __construct(string $templateDir, array $templates)
    {
        $this->templateDir = rtrim($templateDir, '/');
        $this->templates = $templates;
    }

    public static function fromConfig(array $config): self
    {
        $templates = is_array($config['templates'] ?? null) ? $config['templates'] : [
            'order' => 'order_template.docx',
            'act' => 'act_template.docx',
        ];

        return new self((string)$config['template_path'], $templates);
    }

    public function path(string $key): string
    {
        if (!isset($this->templates[$key])) {
            throw new InvalidArgumentException('Unknown template');
        }

        $path = $this->templateDir . '/' . $this->templates[$key];
        if (!is_file($path)) {
            throw new RuntimeException('Template not found');
        }

        return $path;
    }
}
