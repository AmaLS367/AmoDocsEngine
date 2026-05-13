<h1 align="center">🔌 API Reference</h1>

<p align="center">
  <strong>HTTP contracts for prefill, quote preview, and document generation.</strong>
</p>

<p align="center">
  <a href="index.md">📚 Docs</a> ·
  <a href="configuration.md">⚙️ Configuration</a> ·
  <a href="templates.md">🧾 Templates</a> ·
  <a href="../ru/api.md">🇷🇺 RU</a>
</p>

---

## 🧭 Endpoint Map

| Endpoint | Auth | Purpose |
| --- | --- | --- |
| `GET /api/prefill.php?lead_id=` | none | Restore cached form and issue `generate_token` |
| `POST /api/quote.php` | none | Calculate backend totals for UI preview |
| `POST /api/generate.php` | `generate_token` or HMAC | Generate DOCX and update amoCRM note |

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

Example response:

```json
{
  "rows": [
    {
      "index": 1,
      "name": "Diagnostics",
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

## HMAC Server Client

When `generate_auth_mode` is `hmac`, send `X-Signature` as:

```text
hash_hmac('sha256', raw_json_body, hmac_secret)
```

Use exactly the raw body bytes sent to `generate.php`. Do not sign a re-encoded JSON object.

---

**Next:** [Templates](templates.md) · **Back:** [Configuration](configuration.md)
