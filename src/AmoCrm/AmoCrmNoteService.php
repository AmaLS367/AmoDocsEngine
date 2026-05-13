<?php

declare(strict_types=1);

namespace AmoDocGenerator\AmoCrm;

use Throwable;

final class AmoCrmNoteService
{
    private string $metaDir;
    /** @var callable */
    private $delete;
    /** @var callable */
    private $post;

    public function __construct(string $metaDir, callable $delete, callable $post)
    {
        $this->metaDir = rtrim($metaDir, '/');
        $this->delete = $delete;
        $this->post = $post;
    }

    public static function fromClient(AmoCrmClient $client, string $metaDir): self
    {
        return new self(
            $metaDir,
            static function (int $noteId) use ($client): void {
                $client->delete('/api/v4/leads/notes/' . $noteId);
            },
            static function (array $payload) use ($client): array {
                return $client->post('/api/v4/leads/notes', $payload);
            }
        );
    }

    public function replaceDocumentNote(int $leadId, string $template, string $url): void
    {
        @is_dir($this->metaDir) || @mkdir($this->metaDir, 0775, true);
        $metaPath = $this->metaPath($leadId);
        $meta = is_file($metaPath) ? json_decode((string)file_get_contents($metaPath), true) : [];
        $meta = is_array($meta) ? $meta : [];
        $prevNoteId = (int)($meta['note_id'] ?? 0);

        if ($prevNoteId > 0) {
            try {
                ($this->delete)($prevNoteId);
            } catch (Throwable $ignored) {
            }
        }

        $response = ($this->post)([$this->payload($leadId, $template, $url)]);
        $newId = $response['_embedded']['notes'][0]['id'] ?? null;
        if ($newId) {
            $meta['note_id'] = (int)$newId;
            file_put_contents($metaPath, json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(int $leadId, string $template, string $url): array
    {
        $title = $template === 'act' ? 'Акт приёма-передачи' : 'Заказ-наряд';

        return [
            'entity_id' => $leadId,
            'entity_type' => 'leads',
            'note_type' => 'common',
            'params' => ['text' => "{$title} №{$leadId}: {$url}"],
        ];
    }

    private function metaPath(int $leadId): string
    {
        return $this->metaDir . "/lead_{$leadId}_meta.json";
    }
}
