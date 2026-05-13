<?php

declare(strict_types=1);

use AmoDocGenerator\Documents\DocumentGenerationService;
use PHPUnit\Framework\TestCase;

final class DocumentGenerationServiceTest extends TestCase
{
    public function testUsesActTemplateWhenTemplateKeyIsAct(): void
    {
        $paths = $this->fixturePaths();
        touch($paths['template'] . '/act_template.docx');
        $usedTemplate = null;

        $service = new DocumentGenerationService($this->config($paths), function (string $templatePath) use (&$usedTemplate) {
            $usedTemplate = $templatePath;

            return new FakeTemplateProcessor();
        });

        $result = $service->generate(['id' => 55], null, 'act', [['name' => 'Service', 'unit_price' => 100, 'qty' => 1]], 0);

        $this->assertSame($paths['template'] . '/act_template.docx', $usedTemplate);
        $this->assertFileExists($result['path']);
        $this->assertStringStartsWith('https://docs.example/doc_55_', $result['url']);
    }

    public function testThrowsWhenTemplateFileDoesNotExist(): void
    {
        $service = new DocumentGenerationService($this->config($this->fixturePaths()));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Template not found');

        $service->generate(['id' => 55], null, 'order', [['name' => 'Service', 'unit_price' => 100, 'qty' => 1]], 0);
    }

    public function testThrowsWhenDocumentDirectoryCannotBeCreated(): void
    {
        $paths = $this->fixturePaths();
        touch($paths['template'] . '/order_template.docx');
        $blockedDocumentPath = $paths['document'] . '/blocked-by-file';
        file_put_contents($blockedDocumentPath, 'not a directory');
        $config = $this->config($paths);
        $config['document_path'] = $blockedDocumentPath . '/documents';
        $service = new DocumentGenerationService($config, static function (): FakeTemplateProcessor {
            return new FakeTemplateProcessor();
        });

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Parent directory');

        $service->generate(['id' => 55], null, 'order', [['name' => 'Service', 'unit_price' => 100, 'qty' => 1]], 0);
    }

    /**
     * @return array{template:string,document:string}
     */
    private function fixturePaths(): array
    {
        $root = sys_get_temp_dir() . '/amodocs_' . uniqid('', true);
        mkdir($root . '/templates', 0777, true);
        mkdir($root . '/documents', 0777, true);

        return [
            'template' => str_replace('\\', '/', $root . '/templates'),
            'document' => str_replace('\\', '/', $root . '/documents'),
        ];
    }

    /**
     * @param array{template:string,document:string} $paths
     * @return array<string, mixed>
     */
    private function config(array $paths): array
    {
        return [
            'template_path' => $paths['template'],
            'document_path' => $paths['document'],
            'public_documents_url' => 'https://docs.example',
            'dir_mode' => 0777,
            'file_mode' => 0644,
            'templates' => [
                'order' => 'order_template.docx',
                'act' => 'act_template.docx',
            ],
        ];
    }
}

final class FakeTemplateProcessor
{
    /** @param mixed $value */
    public function setValue(string $key, $value): void
    {
    }

    public function cloneRow(string $search, int $numberOfClones): void
    {
    }

    public function saveAs(string $path): void
    {
        file_put_contents($path, 'docx');
    }
}
