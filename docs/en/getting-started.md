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

## Configure amoCRM OAuth

1. Create a private amoCRM integration.
2. Set Redirect URI to your deployed `oauth.php` URL.
3. Fill `client_id`, `client_secret`, `redirect_uri`, `base_domain`, and `subdomain` in `config/config.php`.
4. Open the amoCRM authorization URL and complete the flow.
5. Confirm that `config/token.json` exists.

## Open the UI

```text
https://<domain>/<path>/public/ui.html?lead_id=<amoCRM_LEAD_ID>
```

If the UI and API are not served from the same root, update `API_BASE` in `public/app.js`.

Next: [Configuration](configuration.md)
