# 🛠️ Эксплуатация

## Runtime-файлы

- `config/token.json` хранит OAuth-токены.
- `data/cache/` хранит payload префилла.
- `data/security/generate_tokens.json` хранит короткоживущие browser-токены.
- `documents/` хранит готовые DOCX.
- `logs/generate.log` хранит JSON-события ошибок.

## Smoke-тест

```bash
php -m | grep -E 'zip|xml|mbstring|curl|json'
composer install
vendor/bin/phpunit
```

Windows:

```powershell
.\vendor\bin\phpunit
```

## Диагностика

| Симптом | Что проверить |
| --- | --- |
| `401 Unauthorized` | UI сначала вызвал `prefill.php` и отправляет `generate_token` |
| amoCRM `401` | Есть `config/token.json`, refresh token валиден |
| Пустые поля авто | ID в `amo_fields` совпадают с полями сделки |
| Template not found | Ключ шаблона есть, файл лежит в `templates/` |
| Битая ссылка на документ | `public_documents_url` совпадает с hosting path |

## Миграция

Для другого amoCRM аккаунта обновите OAuth-данные, `base_domain`, `subdomain`, `amo_fields` и заново пройдите OAuth для создания свежего `config/token.json`.

Дальше: [Разработка](development.md)
