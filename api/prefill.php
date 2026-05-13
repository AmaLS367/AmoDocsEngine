<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

require __DIR__ . '/../vendor/autoload.php';

use AmoDocGenerator\Security\GenerateTokenStore;
use AmoDocGenerator\Storage\PrefillCache;

$config = require __DIR__ . '/../config/config.php';
$leadId = isset($_GET['lead_id']) ? (int)$_GET['lead_id'] : 0;
$out = PrefillCache::fromConfig($config)->read($leadId);

if ($leadId > 0) {
    $out['generate_token'] = GenerateTokenStore::fromConfig($config)->issue($leadId);
}

echo json_encode($out, JSON_UNESCAPED_UNICODE);
