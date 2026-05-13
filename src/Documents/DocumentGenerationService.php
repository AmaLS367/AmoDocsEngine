<?php

declare(strict_types=1);

namespace AmoDocGenerator\Documents;

use AmoDocGenerator\DocumentDataBuilder;
use AmoDocGenerator\Support\RubleFormatter;
use PhpOffice\PhpWord\TemplateProcessor;
use RuntimeException;

final class DocumentGenerationService
{
    /** @var array<string, mixed> */
    private array $config;
    /** @var callable */
    private $processorFactory;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config, ?callable $processorFactory = null)
    {
        $this->config = $config;
        $this->processorFactory = $processorFactory ?? static function (string $templatePath): TemplateProcessor {
            return new TemplateProcessor($templatePath);
        };
    }

    /**
     * @param array<string, mixed> $lead
     * @param array<string, mixed>|null $contact
     * @param array<int, array<string, mixed>> $products
     * @return array{path:string,url:string,filename:string,total:int}
     */
    public function generate(array $lead, ?array $contact, string $template, array $products, int $discount): array
    {
        $leadId = (int)($lead['id'] ?? 0);
        if ($leadId <= 0) {
            throw new RuntimeException('Invalid lead id for document generation');
        }

        $templatePath = $this->templatePath($template);
        $documentDir = rtrim((string)$this->config['document_path'], '/');
        @is_dir($documentDir) || @mkdir($documentDir, (int)$this->config['dir_mode'], true);

        foreach (glob($documentDir . "/doc_{$leadId}_*.docx") ?: [] as $oldFile) {
            @unlink($oldFile);
        }

        $processor = ($this->processorFactory)($templatePath);
        $fields = $lead['custom_fields_values'] ?? [];
        $phone = $this->phoneFromContact($contact);

        [$lastName, $firstName, $middleName] = $this->fioParts($contact);
        $lastName = $this->customField($fields, 'Фамилия') ?: $lastName;
        $firstName = $this->customField($fields, 'Имя') ?: $firstName;
        $middleName = $this->customField($fields, 'Отчество') ?: $middleName;

        $processor->setValue('Номер', $leadId);
        $processor->setValue('Дата', date('d.m.Y'));
        $processor->setValue('Телефон', $phone ? ' ' . $phone : '');
        $processor->setValue('Марка', $this->customField($fields, 'Марка') ?: '—');
        $processor->setValue('Модель', $this->customField($fields, 'Модель') ?: '—');
        $processor->setValue('VIN', $this->customField($fields, 'VIN') ?: '—');
        $processor->setValue('Год выпуска', $this->customField($fields, 'Год выпуска') ?: '—');
        $processor->setValue('Фамилия', $lastName);
        $processor->setValue('Имя', $firstName);
        $processor->setValue('Отчество', $middleName);

        if ($template === 'order' && count($products) > 0) {
            $rows = DocumentDataBuilder::buildRows($products);
            $processor->cloneRow('row_num', count($rows));

            foreach ($rows as $row) {
                $n = $row['index'];
                $processor->setValue("row_num#{$n}", $n);
                $processor->setValue("услуга_название#{$n}", $row['name']);
                $processor->setValue("row_qty#{$n}", $row['qty']);
                $processor->setValue("row_price#{$n}", number_format((int)$row['unit_price'], 0, ',', ' '));
                $processor->setValue("row_discount#{$n}", $row['discount_label']);
                $processor->setValue("row_sum#{$n}", number_format((int)$row['net_sum'], 0, ',', ' '));
            }
        }

        $summary = DocumentDataBuilder::summarize($products, $discount);
        $processor->setValue('Итого', $summary['sum_gross']);
        $processor->setValue('Скидка', $summary['discount']);
        $processor->setValue('Всего к оплате', $summary['total']);
        $processor->setValue('Количество наименований', $summary['count']);
        $processor->setValue('Сумма прописью', RubleFormatter::toWords((int)$summary['total']));

        $filename = "doc_{$leadId}_" . time() . ".docx";
        $savePath = $documentDir . '/' . $filename;
        $processor->saveAs($savePath);
        @chmod($savePath, (int)$this->config['file_mode']);

        return [
            'path' => $savePath,
            'url' => rtrim((string)$this->config['public_documents_url'], '/') . '/' . rawurlencode($filename),
            'filename' => $filename,
            'total' => (int)$summary['total'],
        ];
    }

    private function templatePath(string $template): string
    {
        $templateDir = rtrim((string)$this->config['template_path'], '/');
        $templateFile = $template === 'act' ? 'act_template.docx' : 'order_template.docx';
        $path = $templateDir . '/' . $templateFile;

        if (!is_file($path)) {
            throw new RuntimeException('Template not found');
        }

        return $path;
    }

    /**
     * @param array<string, mixed>|null $contact
     */
    private function phoneFromContact(?array $contact): string
    {
        foreach (($contact['custom_fields_values'] ?? []) as $field) {
            if (($field['field_code'] ?? '') === 'PHONE') {
                return (string)($field['values'][0]['value'] ?? '');
            }
        }

        return '';
    }

    /**
     * @param array<string, mixed>|null $contact
     * @return array{0:string,1:string,2:string}
     */
    private function fioParts(?array $contact): array
    {
        $parts = preg_split('/\s+/', trim((string)($contact['name'] ?? '')), 3);
        $parts = is_array($parts) ? $parts : [];

        return array_pad($parts, 3, '');
    }

    /**
     * @param array<int, array<string, mixed>> $fields
     */
    private function customField(array $fields, string $name): string
    {
        foreach ($fields as $field) {
            if (($field['field_name'] ?? '') === $name) {
                return (string)($field['values'][0]['value'] ?? '');
            }
        }

        return '';
    }
}
