# ⚙️ Configuration

`config/config.php` is ignored by git because it contains secrets. Start from `config/config.example.php`.

## Required amoCRM Settings

- `client_id`
- `client_secret`
- `redirect_uri`
- `base_domain`
- `subdomain`
- `token_path`

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

## Templates

```php
'templates' => [
    'order' => 'order_template.docx',
    'act' => 'act_template.docx',
],
```

Add another key here to expose a new template through the registry.

Next: [API](api.md)
