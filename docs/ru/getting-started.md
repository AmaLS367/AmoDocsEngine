<h1 align="center">🚀 Запуск</h1>

<p align="center">
  <strong>Установить AmoDocsEngine, подключить amoCRM OAuth и открыть UI генерации документов.</strong>
</p>

<p align="center">
  <a href="index.md">📚 Документация</a> ·
  <a href="configuration.md">⚙️ Конфигурация</a> ·
  <a href="api.md">🔌 API</a> ·
  <a href="../en/getting-started.md">🇬🇧 EN</a>
</p>

---

## 🧭 Коротко

| Шаг | Результат |
| --- | --- |
| Установить зависимости | готов `vendor/` |
| Скопировать конфиг | есть ignored `config/config.php` |
| Пройти OAuth | есть `config/token.json` |
| Открыть UI | загружается `public/ui.html?lead_id=<ID>` |

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

## Развернуть файлы

Загрузите проект в директорию хостинга, откуда будут раздаваться UI и API. PHP-процессу нужны права на запись:

```bash
chmod -R 775 documents logs data
```

`777` используйте только если хостинг не дает нормально настроить владельца процесса.

## OAuth amoCRM

1. Создайте приватную интеграцию amoCRM.
2. Укажите Redirect URI на ваш `oauth.php`.
3. Заполните `client_id`, `client_secret`, `redirect_uri`, `base_domain`, `subdomain`.
4. Откройте ссылку авторизации и завершите flow.
5. Проверьте, что появился `config/token.json`.

Refresh работает автоматически: если amoCRM вернул `401`, `AmoCrmClient` обновит access token и повторит запрос.

## Открыть UI

```text
https://<domain>/<path>/public/ui.html?lead_id=<amoCRM_LEAD_ID>
```

Если UI и API раздаются не из одного root, обновите `API_BASE` в `public/app.js`.

## Первая проверка

Перед реальной генерацией проверьте backend-расчет:

```bash
curl -X POST https://<domain>/<path>/api/quote.php \
  -H 'Content-Type: application/json' \
  -d '{"discount":0,"products":[{"name":"Test","unit_price":1000,"qty":1}]}'
```

Ожидается JSON с `"total":1000` и `total_words`.

---

**Дальше:** [Конфигурация](configuration.md) · **Назад:** [Индекс документации](index.md)
