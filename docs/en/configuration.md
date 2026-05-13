# ⚙️ Configuration

`config/config.php` is ignored by git because it contains secrets. Start from `config/config.example.php`.

## Required amoCRM Settings

- `client_id`
- `client_secret`
- `redirect_uri`
- `base_domain`
- `subdomain`
- `token_path`

Use the full amoCRM domain in `base_domain`, for example `https://example.amocrm.ru`.

## Field Mapping

Deal custom fields are mapped by amoCRM field ID, not by display name:

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

Renaming fields in amoCRM is safe as long as these IDs stay correct. Contact phone uses the stable amoCRM `PHONE` field code.

## Security

Browser generation uses server-issued `generate_token` values from `prefill.php`.

HMAC mode is for trusted server-to-server clients only:

```php
'security' => [
    'generate_auth_mode' => 'browser_token',
    'generate_token_ttl_seconds' => 1800,
    'hmac_secret' => '',
],
```

Supported modes:

| Mode | Use case |
| --- | --- |
| `browser_token` | Default UI flow. `prefill.php` issues `generate_token`; `generate.php` validates it. |
| `hmac` | Trusted backend integration signs the raw request body with `X-Signature`. |
| `either` | Transitional mode for clients that may use browser token or HMAC. |

Do not put an HMAC secret into browser JavaScript. If `hmac` mode is enabled, `hmac_secret` must be non-empty.

## Runtime Paths

| Key | Purpose |
| --- | --- |
| `template_path` | Source `.docx` templates |
| `document_path` | Generated document files |
| `public_documents_url` | Public URL prefix for generated documents |
| `temp_data_path` | Runtime state root |
| `cache_path` | Prefill cache |
| `logs_path` | JSON logs |
| `token_path` | amoCRM OAuth tokens |

## Templates

```php
'templates' => [
    'order' => 'order_template.docx',
    'act' => 'act_template.docx',
],
```

Add another key here to expose a new template through the registry.

## Do Not Commit

These files must stay out of git:

- `config/config.php`
- `config/token.json`
- `.env`
- `data/`
- `documents/`
- `logs/`

Next: [API](api.md)
