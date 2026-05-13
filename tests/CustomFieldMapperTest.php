<?php

declare(strict_types=1);

use AmoDocGenerator\AmoCrm\CustomFieldMapper;
use PHPUnit\Framework\TestCase;

final class CustomFieldMapperTest extends TestCase
{
    public function testReadsCustomFieldByIdAndIgnoresRenamedFieldName(): void
    {
        $mapper = new CustomFieldMapper(['vin' => 333]);

        $value = $mapper->value([
            [
                'field_id' => 333,
                'field_name' => 'VIN renamed in CRM',
                'values' => [['value' => 'XTA123']],
            ],
        ], 'vin');

        $this->assertSame('XTA123', $value);
    }

    public function testReturnsEmptyStringWhenFieldIdIsNotConfigured(): void
    {
        $mapper = new CustomFieldMapper([]);

        $this->assertSame('', $mapper->value([
            [
                'field_id' => 333,
                'field_name' => 'VIN',
                'values' => [['value' => 'XTA123']],
            ],
        ], 'vin'));
    }
}
