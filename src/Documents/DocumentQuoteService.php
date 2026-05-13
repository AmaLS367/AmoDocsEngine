<?php

declare(strict_types=1);

namespace AmoDocGenerator\Documents;

use AmoDocGenerator\DocumentDataBuilder;
use AmoDocGenerator\Support\RubleFormatter;

final class DocumentQuoteService
{
    /**
     * @param array<int, array<string, mixed>> $products
     * @return array<string, mixed>
     */
    public function quote(array $products, int $discount): array
    {
        $rows = DocumentDataBuilder::buildRows($products);
        $summary = DocumentDataBuilder::summarize($products, $discount);

        return [
            'rows' => $rows,
            'sum_gross' => $summary['sum_gross'],
            'sum_after' => $summary['sum_after'],
            'discount' => $summary['discount'],
            'total' => $summary['total'],
            'count' => $summary['count'],
            'total_words' => RubleFormatter::toWords((int)$summary['total']),
        ];
    }
}
