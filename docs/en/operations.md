# 🛠️ Operations

## Runtime Files

- `config/token.json` stores OAuth tokens.
- `data/cache/` stores prefill payloads.
- `data/security/generate_tokens.json` stores short-lived browser generation tokens.
- `documents/` stores generated DOCX files.
- `logs/generate.log` stores JSON error events.

## Smoke Test

```bash
php -m | grep -E 'zip|xml|mbstring|curl|json'
composer install
vendor/bin/phpunit
```

On Windows:

```powershell
.\vendor\bin\phpunit
```

## Troubleshooting

| Symptom | Check |
| --- | --- |
| `401 Unauthorized` | UI called `prefill.php` first and sends `generate_token` |
| amoCRM `401` | OAuth token exists and refresh token is valid |
| Empty vehicle fields | `amo_fields` IDs match the deal custom fields |
| Template not found | Template key exists and file is present in `templates/` |
| Document URL broken | `public_documents_url` matches hosting path |

## Migration

For another amoCRM account, update OAuth credentials, `base_domain`, `subdomain`, `amo_fields`, and run OAuth again to create a fresh `config/token.json`.

Next: [Development](development.md)
