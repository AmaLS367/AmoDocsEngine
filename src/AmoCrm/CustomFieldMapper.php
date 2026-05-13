<?php

declare(strict_types=1);

namespace AmoDocGenerator\AmoCrm;

final class CustomFieldMapper
{
    /** @var array<string, int> */
    private array $fieldIds;

    /**
     * @param array<string, int|string> $fieldIds
     */
    public function __construct(array $fieldIds)
    {
        $this->fieldIds = [];
        foreach ($fieldIds as $key => $id) {
            $this->fieldIds[$key] = (int)$id;
        }
    }

    /**
     * @param array<int, array<string, mixed>> $fields
     */
    public function value(array $fields, string $key): string
    {
        $fieldId = $this->fieldIds[$key] ?? 0;
        if ($fieldId <= 0) {
            return '';
        }

        foreach ($fields as $field) {
            if ((int)($field['field_id'] ?? 0) !== $fieldId) {
                continue;
            }

            $value = $field['values'][0]['value'] ?? '';

            return is_scalar($value) ? (string)$value : '';
        }

        return '';
    }
}
