<?php

declare(strict_types=1);

use AmoDocGenerator\Documents\TemplateRegistry;
use PHPUnit\Framework\TestCase;

final class TemplateRegistryTest extends TestCase
{
    public function testResolvesConfiguredTemplates(): void
    {
        $dir = $this->templateDir(['order_template.docx', 'act_template.docx']);
        $registry = new TemplateRegistry($dir, [
            'order' => 'order_template.docx',
            'act' => 'act_template.docx',
        ]);

        $this->assertSame($dir . '/order_template.docx', $registry->path('order'));
        $this->assertSame($dir . '/act_template.docx', $registry->path('act'));
    }

    public function testRejectsUnknownTemplateKey(): void
    {
        $registry = new TemplateRegistry($this->templateDir(['order_template.docx']), [
            'order' => 'order_template.docx',
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown template');

        $registry->path('invoice');
    }

    public function testCanAddThirdTemplateThroughConfigOnly(): void
    {
        $dir = $this->templateDir(['invoice_template.docx']);
        $registry = new TemplateRegistry($dir, [
            'invoice' => 'invoice_template.docx',
        ]);

        $this->assertSame($dir . '/invoice_template.docx', $registry->path('invoice'));
    }

    /**
     * @param array<int, string> $files
     */
    private function templateDir(array $files): string
    {
        $dir = str_replace('\\', '/', sys_get_temp_dir() . '/amodocs_tpl_' . uniqid('', true));
        mkdir($dir, 0777, true);
        foreach ($files as $file) {
            touch($dir . '/' . $file);
        }

        return $dir;
    }
}
