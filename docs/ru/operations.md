<h1 align="center">🛠️ Эксплуатация</h1>

<p align="center">
  <strong>Smoke checks, логи, диагностика хостинга и миграция amoCRM аккаунтов.</strong>
</p>

<p align="center">
  <a href="index.md">📚 Документация</a> ·
  <a href="templates.md">🧾 Шаблоны</a> ·
  <a href="development.md">🧪 Разработка</a> ·
  <a href="../en/operations.md">🇬🇧 EN</a>
</p>

---

## 🧭 Карта эксплуатации

| Зона | Сначала проверить |
| --- | --- |
| Runtime data | `data/`, `documents/`, `logs/` |
| OAuth | `config/token.json` |
| Request auth | `data/security/generate_tokens.json` |
| Exceptions | `logs/generate.log` |

## Runtime-файлы

- `config/token.json` хранит OAuth-токены.
- `data/cache/` хранит payload префилла.
- `data/security/generate_tokens.json` хранит короткоживущие browser-токены.
- `documents/` хранит готовые DOCX.
- `logs/generate.log` хранит JSON-события ошибок.

По возможности держите `config/`, `data/`, `documents/` и `logs/` вне прямого публичного доступа. Если хостинг их раздает, отключите listing директорий и заблокируйте sensitive files.

## Smoke-тест

```bash
php -m | grep -E 'zip|xml|mbstring|curl|json'
composer install
vendor/bin/phpunit
```

Windows:

```powershell
.\vendor\bin\phpunit
```

## Диагностика

| Симптом | Что проверить |
| --- | --- |
| `401 Unauthorized` | UI сначала вызвал `prefill.php` и отправляет `generate_token` |
| amoCRM `401` | Есть `config/token.json`, refresh token валиден |
| Пустые поля авто | ID в `amo_fields` совпадают с полями сделки |
| Template not found | Ключ шаблона есть, файл лежит в `templates/` |
| Битая ссылка на документ | `public_documents_url` совпадает с hosting path |

## Чтение логов

`logs/generate.log` пишется append-only JSON lines. Типичная запись:

```json
{
  "EX": "Template not found",
  "line": 123
}
```

По сообщению можно понять источник: шаблон, amoCRM, security или filesystem.

## Миграция

Для другого amoCRM аккаунта обновите OAuth-данные, `base_domain`, `subdomain`, `amo_fields` и заново пройдите OAuth для создания свежего `config/token.json`.

---

**Дальше:** [Разработка](development.md) · **Назад:** [Шаблоны](templates.md)
