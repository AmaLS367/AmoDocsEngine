# 🚀 Getting Started

## Requirements

- PHP 7.4+ with `curl`, `intl`, `mbstring`, and `zip`.
- Composer.
- Writable runtime directories: `documents/`, `logs/`, `data/`, `data/cache/`.

## Install

```bash
composer install
cp config/config.example.php config/config.php
```

On Windows PowerShell:

```powershell
composer install
Copy-Item config/config.example.php config/config.php
```

## Deploy Files

Upload the project to the hosting directory that will serve the UI and API. Keep these directories writable by the PHP process:

```bash
chmod -R 775 documents logs data
```

If the host has strict ownership rules, fix ownership first and use `777` only as a last resort.

## Configure amoCRM OAuth

1. Create a private amoCRM integration.
2. Set Redirect URI to your deployed `oauth.php` URL.
3. Fill `client_id`, `client_secret`, `redirect_uri`, `base_domain`, and `subdomain` in `config/config.php`.
4. Open the amoCRM authorization URL and complete the flow.
5. Confirm that `config/token.json` exists.

The refresh flow is automatic: when amoCRM returns `401`, `AmoCrmClient` refreshes the access token and retries the request.

## Open the UI

```text
https://<domain>/<path>/public/ui.html?lead_id=<amoCRM_LEAD_ID>
```

If the UI and API are not served from the same root, update `API_BASE` in `public/app.js`.

## First Validation

Run the backend quote endpoint before testing a real document:

```bash
curl -X POST https://<domain>/<path>/api/quote.php \
  -H 'Content-Type: application/json' \
  -d '{"discount":0,"products":[{"name":"Test","unit_price":1000,"qty":1}]}'
```

Expected: JSON with `"total":1000` and a Russian `total_words` value.

Next: [Configuration](configuration.md)
