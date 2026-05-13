<h1 align="center">🔌 API Reference</h1>

<p align="center">
  <strong>HTTP-контракты для prefill, quote preview и генерации документа.</strong>
</p>

<p align="center">
  <a href="index.md">📚 Документация</a> ·
  <a href="configuration.md">⚙️ Конфигурация</a> ·
  <a href="templates.md">🧾 Шаблоны</a> ·
  <a href="../en/api.md">🇬🇧 EN</a>
</p>

---

## 🧭 Карта endpoint-ов

| Endpoint | Auth | Назначение |
| --- | --- | --- |
| `GET /api/prefill.php?lead_id=` | нет | Восстановить форму и выдать `generate_token` |
| `POST /api/quote.php` | нет | Посчитать backend totals для UI preview |
| `POST /api/generate.php` | `generate_token` или HMAC | Сгенерировать DOCX и обновить заметку amoCRM |

Все ответы JSON, кроме ссылок на готовые DOCX-файлы.

## `GET /api/prefill.php?lead_id=<id>`

Возвращает кэш формы и серверный токен генерации.

```json
{
  "template": "order",
  "discount": 0,
  "products": [],
  "saved_at": 1710000000,
  "generate_token": "..."
}
```

## `POST /api/quote.php`

Считает строки, итоги и сумму прописью на backend.

```json
{
  "discount": 500,
  "products": [
    {"name": "Диагностика", "unit_price": 1500, "qty": 1, "discount_percent": 0}
  ]
}
```

Возвращает `rows`, `sum_gross`, `sum_after`, `discount`, `total`, `count`, `total_words`.

Пример ответа:

```json
{
  "rows": [
    {
      "index": 1,
      "name": "Диагностика",
      "qty": 1,
      "unit_price": 1500,
      "discount_label": "-",
      "net_sum": 1500
    }
  ],
  "sum_gross": 1500,
  "sum_after": 1500,
  "discount": 500,
  "total": 1000,
  "count": 1,
  "total_words": "одна тысяча рублей"
}
```

## `POST /api/generate.php`

Создает документ и обновляет заметку в amoCRM.

```json
{
  "lead_id": 123456,
  "template": "order",
  "discount": 500,
  "generate_token": "...",
  "products": [
    {"name": "Диагностика", "unit_price": 1500, "qty": 1, "discount_percent": 0}
  ]
}
```

Ответ:

```json
{"url": "https://<domain>/documents/doc_123456_1710000000.docx"}
```

Коды ошибок: `400` для некорректного ввода или неизвестного шаблона, `401` для ошибки авторизации, `500` для внутренних ошибок.

## HMAC для backend-клиента

В режиме `generate_auth_mode = hmac` отправляйте `X-Signature` как:

```text
hash_hmac('sha256', raw_json_body, hmac_secret)
```

Подписывать нужно ровно тот raw body, который отправляется в `generate.php`, без повторного JSON encoding.

---

**Дальше:** [Шаблоны](templates.md) · **Назад:** [Конфигурация](configuration.md)
