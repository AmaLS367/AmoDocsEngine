<?php
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/config.php';
date_default_timezone_set($config['timezone']);

use AmoDocGenerator\AmoCrm\AmoCrmClient;
use AmoDocGenerator\AmoCrm\AmoCrmNoteService;
use AmoDocGenerator\Documents\DocumentGenerationService;
use AmoDocGenerator\Security\GenerateTokenStore;
use AmoDocGenerator\Security\RequestAuthenticator;
use AmoDocGenerator\Storage\PrefillCache;
use AmoDocGenerator\Support\JsonLogger;

$logger = JsonLogger::fromConfig($config, 'generate.log');

// get input data / получить входные данные
$raw = file_get_contents('php://input');
$in  = json_decode($raw, true);
if (json_last_error() !== JSON_ERROR_NONE) { http_response_code(400); echo json_encode(['error'=>'Bad JSON']); exit; }

$leadId   = (int)($in['lead_id'] ?? 0);
$template = (string)($in['template'] ?? 'order');
$products = is_array($in['products'] ?? null) ? $in['products'] : [];
$discount = (int)($in['discount'] ?? 0);

if ($leadId <= 0 || !count($products)) { http_response_code(400); echo json_encode(['error'=>'Invalid lead_id or products']); exit; }
if (!(new RequestAuthenticator($config, GenerateTokenStore::fromConfig($config)))->isAuthorized((string)$raw, $in, $_SERVER)) {
    http_response_code(401);
    echo json_encode(['error'=>'Unauthorized'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Load amoCRM client / Загрузка клиента amoCRM
$tokenPath = $config['token_path'];
$amo = new AmoCrmClient($config, $tokenPath);

// Main logic / Основная логика
try{
  // token check and refresh / проверка токена и обновление
  $lead = $amo->get("/api/v4/leads/{$leadId}?with=contacts");
  $cid  = $lead['_embedded']['contacts'][0]['id'] ?? null;
  $contact = $cid ? $amo->get("/api/v4/contacts/{$cid}") : null;

  $documentService = new DocumentGenerationService($config);
  $document = $documentService->generate($lead + ['id' => $leadId], $contact, $template, $products, $discount);
  $url = $document['url'];

  PrefillCache::fromConfig($config)->write($leadId, $template, $discount, $products);
  AmoCrmNoteService::fromClient($amo, rtrim($config['temp_data_path'], '/').'/prefill')
      ->replaceDocumentNote($leadId, $template, $url);

  echo json_encode(['url'=>$url], JSON_UNESCAPED_UNICODE);

} catch (InvalidArgumentException $e) {
  http_response_code(400);
  echo json_encode(['error'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e){
  $logger->log(['EX'=>$e->getMessage(),'line'=>$e->getLine()]);
  http_response_code(500);
  echo json_encode(['error'=>'Internal Server Error'], JSON_UNESCAPED_UNICODE);
}
