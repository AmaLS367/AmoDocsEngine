# 🔌 API

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

Дальше: [Шаблоны](templates.md)
