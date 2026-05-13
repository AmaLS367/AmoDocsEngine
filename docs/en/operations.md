<h1 align="center">🛠️ Operations</h1>

<p align="center">
  <strong>Run smoke checks, read logs, troubleshoot hosting issues, and migrate amoCRM accounts.</strong>
</p>

<p align="center">
  <a href="index.md">📚 Docs</a> ·
  <a href="templates.md">🧾 Templates</a> ·
  <a href="development.md">🧪 Development</a> ·
  <a href="../ru/operations.md">🇷🇺 RU</a>
</p>

---

## 🧭 Operations Map

| Area | First file to check |
| --- | --- |
| Runtime data | `data/`, `documents/`, `logs/` |
| OAuth | `config/token.json` |
| Request auth | `data/security/generate_tokens.json` |
| Exceptions | `logs/generate.log` |

## Runtime Files

- `config/token.json` stores OAuth tokens.
- `data/cache/` stores prefill payloads.
- `data/security/generate_tokens.json` stores short-lived browser generation tokens.
- `documents/` stores generated DOCX files.
- `logs/generate.log` stores JSON error events.

Keep `config/`, `data/`, `documents/`, and `logs/` outside direct public access when possible. If hosting exposes them, disable directory listing and block sensitive files.

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

## Log Reading

`logs/generate.log` is append-only JSON lines. Typical entries contain:

```json
{
  "EX": "Template not found",
  "line": 123
}
```

Use the error message to decide whether the failure is template, amoCRM, security, or filesystem related.

## Migration

For another amoCRM account, update OAuth credentials, `base_domain`, `subdomain`, `amo_fields`, and run OAuth again to create a fresh `config/token.json`.

---

**Next:** [Development](development.md) · **Back:** [Templates](templates.md)
