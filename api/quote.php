<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

require __DIR__ . '/../vendor/autoload.php';

use AmoDocGenerator\Documents\DocumentQuoteService;

$raw = file_get_contents('php://input');
$input = json_decode((string)$raw, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($input)) {
    http_response_code(400);
    echo json_encode(['error' => 'Bad JSON'], JSON_UNESCAPED_UNICODE);
    exit;
}

$products = is_array($input['products'] ?? null) ? $input['products'] : [];
$discount = (int)($input['discount'] ?? 0);

echo json_encode((new DocumentQuoteService())->quote($products, $discount), JSON_UNESCAPED_UNICODE);
