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

Опциональная проверка PHP syntax:

```powershell
$files = Get-ChildItem -Recurse -Filter *.php | Where-Object { $_.FullName -notlike '*\vendor\*' }
foreach ($file in $files) { php -l $file.FullName }
```

## Правила контрибьюта

- Используйте ветки вроде `docs/readme-polish` или `fix/template-registry`.
- Не коммитьте `config/config.php`, `config/token.json`, `.env`, логи, cache-файлы и generated documents.
- Используйте относительные ссылки в документации и проверяйте их перед PR.

## Стиль коммитов

Делайте небольшие коммиты по смыслу:

- `docs: update api guide`
- `fix(frontend): use api base path`
- `refactor(api): isolate prefill cache`
- `test: cover token validation`

Дальше: [К индексу документации](index.md)
