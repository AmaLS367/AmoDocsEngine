# 🧪 Разработка

## Структура проекта

- `api/` содержит HTTP entrypoints.
- `src/AmoCrm/` содержит amoCRM client, заметки и маппинг полей.
- `src/Documents/` содержит quote calculation, template registry и DOCX generation.
- `src/Security/` содержит browser tokens и request authentication.
- `src/Storage/` содержит prefill cache.
- `src/Support/` содержит formatters и logging helpers.
- `tests/` содержит PHPUnit tests.

## Локальные проверки

```powershell
composer install
.\vendor\bin\phpunit
```

## Правила контрибьюта

- Используйте ветки вроде `docs/readme-polish` или `fix/template-registry`.
- Не коммитьте `config/config.php`, `config/token.json`, `.env`, логи, cache-файлы и generated documents.
- Используйте относительные ссылки в документации и проверяйте их перед PR.

Дальше: [К индексу документации](index.md)
