<?php

declare(strict_types=1);

use AmoDocGenerator\AmoCrm\AmoCrmNoteService;
use PHPUnit\Framework\TestCase;

final class AmoCrmNoteServiceTest extends TestCase
{
    public function testBuildsDocumentNotePayload(): void
    {
        $service = new AmoCrmNoteService($this->dir(), static function (): void {
        }, static function (): array {
            return [];
        });

        $this->assertSame([
            'entity_id' => 123,
            'entity_type' => 'leads',
            'note_type' => 'common',
            'params' => ['text' => 'Акт приёма-передачи №123: https://docs.example/doc.docx'],
        ], $service->payload(123, 'act', 'https://docs.example/doc.docx'));
    }

    public function testDeletesPreviousNoteAndStoresNewNoteId(): void
    {
        $dir = $this->dir();
        mkdir($dir, 0777, true);
        file_put_contents($dir . '/lead_123_meta.json', json_encode(['note_id' => 10]));
        $deleted = [];
        $posted = [];

        $service = new AmoCrmNoteService(
            $dir,
            static function (int $noteId) use (&$deleted): void {
                $deleted[] = $noteId;
            },
            static function (array $payload) use (&$posted): array {
                $posted[] = $payload;

                return ['_embedded' => ['notes' => [['id' => 20]]]];
            }
        );

        $service->replaceDocumentNote(123, 'order', 'https://docs.example/doc.docx');

        $this->assertSame([10], $deleted);
        $this->assertCount(1, $posted);
        $meta = json_decode((string)file_get_contents($dir . '/lead_123_meta.json'), true);
        $this->assertSame(20, $meta['note_id']);
    }

    public function testThrowsWhenMetadataDirectoryCannotBeCreated(): void
    {
        $blockedPath = $this->dir();
        file_put_contents($blockedPath, 'not a directory');
        $service = new AmoCrmNoteService($blockedPath . '/meta', static function (): void {
        }, static function (): array {
            return [];
        });

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Parent directory');

        $service->replaceDocumentNote(123, 'order', 'https://docs.example/doc.docx');
    }

    private function dir(): string
    {
        return str_replace('\\', '/', sys_get_temp_dir() . '/amodocs_notes_' . uniqid('', true));
    }
}
