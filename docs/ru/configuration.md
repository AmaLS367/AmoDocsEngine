# ⚙️ Конфигурация

`config/config.php` игнорируется git, потому что содержит секреты. Начинайте с `config/config.example.php`.

## amoCRM

Заполните:

- `client_id`
- `client_secret`
- `redirect_uri`
- `base_domain`
- `subdomain`
- `token_path`

В `base_domain` указывайте полный домен amoCRM, например `https://example.amocrm.ru`.

## Маппинг полей

Кастомные поля сделки ищутся по ID amoCRM, не по названию:

```php
'amo_fields' => [
    'last_name' => 111111,
    'first_name' => 222222,
    'middle_name' => 333333,
    'car_make' => 444444,
    'car_model' => 555555,
    'vin' => 666666,
    'year' => 777777,
],
```

Переименование полей в amoCRM не ломает генерацию, если ID остались верными. Телефон берется по стабильному коду `PHONE`.

## Безопасность

Browser flow использует серверный `generate_token` из `prefill.php`.

HMAC-режим нужен только для доверенных server-to-server клиентов:

```php
'security' => [
    'generate_auth_mode' => 'browser_token',
    'generate_token_ttl_seconds' => 1800,
    'hmac_secret' => '',
],
```

Режимы:

| Режим | Для чего |
| --- | --- |
| `browser_token` | Режим по умолчанию. `prefill.php` выдает `generate_token`, `generate.php` проверяет его. |
| `hmac` | Доверенный backend-клиент подписывает raw body через `X-Signature`. |
| `either` | Переходный режим, где разрешены browser token или HMAC. |

Не кладите HMAC secret в браузерный JavaScript. Если включен `hmac`, `hmac_secret` обязан быть заполнен.

## Runtime-пути

| Ключ | Назначение |
| --- | --- |
| `template_path` | Исходные `.docx` шаблоны |
| `document_path` | Сгенерированные документы |
| `public_documents_url` | Публичный URL-префикс документов |
| `temp_data_path` | Корень runtime-состояния |
| `cache_path` | Кэш префилла |
| `logs_path` | JSON-логи |
| `token_path` | OAuth-токены amoCRM |

## Шаблоны

```php
'templates' => [
    'order' => 'order_template.docx',
    'act' => 'act_template.docx',
],
```

Новый ключ в этой секции подключает новый шаблон через registry.

## Не коммитить

- `config/config.php`
- `config/token.json`
- `.env`
- `data/`
- `documents/`
- `logs/`

Дальше: [API](api.md)
