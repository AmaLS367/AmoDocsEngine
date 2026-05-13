# 🔌 API

All responses are JSON unless a generated document URL points to a DOCX file.

## `GET /api/prefill.php?lead_id=<id>`

Restores cached form data and issues a server-side token for generation.

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

Calculates rows, totals, and amount in words on the backend.

```json
{
  "discount": 500,
  "products": [
    {"name": "Diagnostics", "unit_price": 1500, "qty": 1, "discount_percent": 0}
  ]
}
```

Returns `rows`, `sum_gross`, `sum_after`, `discount`, `total`, `count`, and `total_words`.

## `POST /api/generate.php`

Generates the document and writes a note back to amoCRM.

```json
{
  "lead_id": 123456,
  "template": "order",
  "discount": 500,
  "generate_token": "...",
  "products": [
    {"name": "Diagnostics", "unit_price": 1500, "qty": 1, "discount_percent": 0}
  ]
}
```

Response:

```json
{"url": "https://<domain>/documents/doc_123456_1710000000.docx"}
```

Error codes: `400` for invalid input or unknown templates, `401` for missing/invalid auth, `500` for internal errors.

Next: [Templates](templates.md)
