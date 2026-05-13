# 🚀 Запуск

## Требования

- PHP 7.4+ с `curl`, `intl`, `mbstring`, `zip`.
- Composer.
- Права на запись в `documents/`, `logs/`, `data/`, `data/cache/`.

## Установка

```bash
composer install
cp config/config.example.php config/config.php
```

PowerShell:

```powershell
composer install
Copy-Item config/config.example.php config/config.php
```

## OAuth amoCRM

1. Создайте приватную интеграцию amoCRM.
2. Укажите Redirect URI на ваш `oauth.php`.
3. Заполните `client_id`, `client_secret`, `redirect_uri`, `base_domain`, `subdomain`.
4. Откройте ссылку авторизации и завершите flow.
5. Проверьте, что появился `config/token.json`.

## Открыть UI

```text
https://<domain>/<path>/public/ui.html?lead_id=<amoCRM_LEAD_ID>
```

Если UI и API раздаются не из одного root, обновите `API_BASE` в `public/app.js`.

Дальше: [Конфигурация](configuration.md)
