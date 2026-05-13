<?php

return [
    'client_id' => 'YOUR_CLIENT_ID',
    'client_secret' => 'YOUR_CLIENT_SECRET',
    'redirect_uri' => 'https://example.com/amo_doc_generator/oauth.php',
    'base_domain' => 'https://example.amocrm.ru',

    'template_path' => __DIR__ . '/../templates/',
    'document_path' => __DIR__ . '/../documents/',
    'temp_data_path' => __DIR__ . '/../data/',
    'logs_path' => __DIR__ . '/../logs/',
    'cache_path' => __DIR__ . '/../data/cache/',
    'token_path' => __DIR__ . '/token.json',

    'public_base_url' => 'https://example.com/amo_doc_generator',
    'public_documents_url' => 'https://example.com/amo_doc_generator/documents',

    'templates' => [
        'order' => 'order_template.docx',
        'act' => 'act_template.docx',
    ],
    'amo_fields' => [
        'last_name' => 111111,
        'first_name' => 222222,
        'middle_name' => 333333,
        'car_make' => 444444,
        'car_model' => 555555,
        'vin' => 666666,
        'year' => 777777,
    ],

    'security' => [
        'generate_auth_mode' => 'browser_token',
        'generate_token_ttl_seconds' => 1800,
        'generate_token_path' => __DIR__ . '/../data/security/generate_tokens.json',
        'hmac_secret' => '',
    ],

    'doc_filename_pattern' => 'doc_{leadId}_{ts}.docx',
    'timezone' => 'Europe/Moscow',
    'dir_mode' => 0775,
    'file_mode' => 0640,
    'subdomain' => 'example',
    'prefill_ttl_days' => 5,
];
