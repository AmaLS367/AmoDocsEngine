# Contributing

Thanks for improving AmoDocsEngine. Keep changes small, practical, and easy to verify.

## Local Setup

```powershell
composer install
Copy-Item config/config.example.php config/config.php
.\vendor\bin\phpunit
```

Use real secrets only in ignored local files. Never commit:

- `config/config.php`
- `config/token.json`
- `.env`
- `data/`
- `documents/`
- `logs/`
- generated DOCX files

## Branches

Use short, scoped branch names:

- `docs/readme-polish`
- `fix/generate-token-validation`
- `refactor/template-registry`
- `test/quote-service`

## Pull Requests

Before opening a PR:

1. Run `.\vendor\bin\phpunit`.
2. Check changed docs links.
3. Keep the PR focused on one concern.
4. Explain config or deployment impact clearly.

## Commit Style

Prefer concise concern-based commits:

- `docs: update configuration guide`
- `fix(frontend): use api base path`
- `refactor(api): isolate note service`
- `test: cover field id mapping`
