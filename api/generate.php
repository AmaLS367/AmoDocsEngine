<?php
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/config.php';
date_default_timezone_set($config['timezone']);
$DIR_MODE  = $config['dir_mode'];
$FILE_MODE = $config['file_mode'];

use AmoDocGenerator\DocumentDataBuilder;
use AmoDocGenerator\AmoCrm\AmoCrmClient;
use AmoDocGenerator\Documents\DocumentGenerationService;

// Directory setup / Пути к директориям
$baseDir  = realpath(__DIR__ . '/..');
$docDir  = rtrim($config['document_path'], '/');
$logDir  = rtrim($config['logs_path'], '/');
$prefDir = rtrim($config['temp_data_path'], '/').'/prefill';
@is_dir($docDir)  || @mkdir($docDir,  $DIR_MODE, true);
@is_dir($logDir)  || @mkdir($logDir,  $DIR_MODE, true);
@is_dir($prefDir) || @mkdir($prefDir, $DIR_MODE, true);
$LOG = $logDir . '/generate.log';
$log = function($x) use($LOG){ file_put_contents($LOG, json_encode($x, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)."\n", FILE_APPEND); };

// get input data / получить входные данные
$raw = file_get_contents('php://input');
if (!empty($config['hmac_secret'])) {
    $sig  = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
    $calc = hash_hmac('sha256', $raw, $config['hmac_secret']);
    if (!hash_equals($calc, $sig)) { http_response_code(401); echo json_encode(['error'=>'bad signature']); exit; }
}
$in  = json_decode($raw, true);
if (json_last_error() !== JSON_ERROR_NONE) { http_response_code(400); echo json_encode(['error'=>'Bad JSON']); exit; }

$leadId   = (int)($in['lead_id'] ?? 0);
$template = (($in['template'] ?? 'order') === 'act') ? 'act' : 'order';
$products = is_array($in['products'] ?? null) ? $in['products'] : [];
$discount = (int)($in['discount'] ?? 0);

if ($leadId <= 0 || !count($products)) { http_response_code(400); echo json_encode(['error'=>'Invalid lead_id or products']); exit; }

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

    // кэш для префилла (1–7 дней) / cache for prefill (1-7 days)
    $cacheDir = rtrim($config['cache_path'] ?? (rtrim($config['temp_data_path'],'/').'/cache'), '/');
    @mkdir($cacheDir, $DIR_MODE, true);
    file_put_contents(
        $cacheDir . '/' . $leadId . '.json',
        json_encode([
            'saved_at' => time(),
            'template' => $template,
            'discount' => $discount,
            'products' => $products
        ], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)
    );


  // примечание: удалить предыдущее (если можно), создать новое / note: delete previous (if possible), create new
  $metaPath = $prefDir . "/lead_{$leadId}_meta.json";
  $meta = is_file($metaPath) ? json_decode(file_get_contents($metaPath), true) : [];
  $prevNoteId = $meta['note_id'] ?? null;

  // попытка удалить прошлое примечание (если API разрешит) / attempt to delete the previous note (if API allows)
  if ($prevNoteId) {
    try {
      $amo->delete('/api/v4/leads/notes/'.(int)$prevNoteId);
    } catch (Throwable $ignored) {
      // игнорируем ошибки удаления — не критично / ignore delete errors — not critical
    }
  }

  // создаём новое примечание / create a new note
  $title = ($template==='act' ? 'Акт приёма-передачи' : 'Заказ-наряд');
  $text  = "{$title} №{$leadId}: {$url}";
  $r = $amo->post('/api/v4/leads/notes', [[
    'entity_id'=>(int)$leadId,'entity_type'=>'leads','note_type'=>'common','params'=>['text'=>$text]
  ]]);
  $newId = $r['_embedded']['notes'][0]['id'] ?? null;
  if ($newId) { $meta['note_id'] = $newId; file_put_contents($metaPath, json_encode($meta, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)); }

  echo json_encode(['url'=>$url], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e){
  $log(['EX'=>$e->getMessage(),'line'=>$e->getLine()]);
  http_response_code(500);
  echo json_encode(['error'=>'Internal Server Error'], JSON_UNESCAPED_UNICODE);
}
