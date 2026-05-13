<?php

declare(strict_types=1);

use AmoDocGenerator\Documents\DocumentQuoteService;
use PHPUnit\Framework\TestCase;

final class DocumentQuoteServiceTest extends TestCase
{
    public function testQuoteReturnsRowsTotalsAndTotalWordsFromBackendBuilders(): void
    {
        $quote = (new DocumentQuoteService())->quote([
            ['name' => 'Диагностика', 'unit_price' => 1500, 'qty' => 1],
            ['name' => 'Ремонт', 'price' => 6000, 'quantity' => 2, 'discount_percent' => 10],
        ], 700);

        $this->assertCount(2, $quote['rows']);
        $this->assertSame(7500, $quote['sum_gross']);
        $this->assertSame(6900, $quote['sum_after']);
        $this->assertSame(700, $quote['discount']);
        $this->assertSame(6200, $quote['total']);
        $this->assertSame(2, $quote['count']);
        $this->assertSame('шесть тысяч двести рублей', $quote['total_words']);
    }
}
