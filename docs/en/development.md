# 🧪 Development

## Project Layout

- `api/` contains HTTP entrypoints.
- `src/AmoCrm/` contains amoCRM client, notes, and field mapping.
- `src/Documents/` contains quote calculation, template registry, and DOCX generation.
- `src/Security/` contains browser tokens and request authentication.
- `src/Storage/` contains prefill cache.
- `src/Support/` contains formatters and logging helpers.
- `tests/` contains PHPUnit tests.

## Local Checks

```powershell
composer install
.\vendor\bin\phpunit
```

## Contribution Rules

- Use feature branches such as `docs/readme-polish` or `fix/template-registry`.
- Do not commit `config/config.php`, `config/token.json`, `.env`, logs, cache files, or generated documents.
- Keep docs links relative and check them before opening a PR.

Next: [Back to documentation index](index.md)
