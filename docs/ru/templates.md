<h1 align="center">🧾 Word-шаблоны</h1>

<p align="center">
  <strong>DOCX-шаблоны, плейсхолдеры, табличные строки и новые типы документов.</strong>
</p>

<p align="center">
  <a href="index.md">📚 Документация</a> ·
  <a href="api.md">🔌 API</a> ·
  <a href="operations.md">🛠️ Эксплуатация</a> ·
  <a href="../en/templates.md">🇬🇧 EN</a>
</p>

---

## 🧭 Карта шаблонов

| Template key | DOCX-файл | Назначение |
| --- | --- | --- |
| `order` | `order_template.docx` | Заказ-наряд со строками услуг |
| `act` | `act_template.docx` | Акт приема-передачи |
| custom | настроен в `templates` | Любой добавленный тип документа |

Шаблоны лежат в `templates/` и заполняются через PhpWord `TemplateProcessor`.

## Встроенные шаблоны

- `order` → `order_template.docx`
- `act` → `act_template.docx`

## Общие плейсхолдеры

- `${Номер}`
- `${Дата}`
- `${Телефон}`
- `${Марка}`
- `${Модель}`
- `${VIN}`
- `${Год выпуска}`
- `${Фамилия}`
- `${Имя}`
- `${Отчество}`
- `${Итого}`
- `${Скидка}`
- `${Всего к оплате}`
- `${Количество наименований}`
- `${Сумма прописью}`

## Табличные строки order

Строки клонируются по `row_num`:

- `${row_num}`
- `${услуга_название}`
- `${row_qty}`
- `${row_price}`
- `${row_discount}`
- `${row_sum}`

## Добавить шаблон

1. Положите `.docx` в `templates/`.
2. Добавьте ключ в `config/config.php` в секцию `templates`.
3. Передавайте этот ключ как `template` из UI или API клиента.

Пример:

```php
'templates' => [
    'order' => 'order_template.docx',
    'act' => 'act_template.docx',
    'invoice' => 'invoice_template.docx',
],
```

Затем отправляйте:

```json
{"template": "invoice"}
```

## Чеклист шаблона

- Имена плейсхолдеров должны совпадать точно.
- Плейсхолдеры таблицы должны быть внутри строки, которую нужно клонировать.
- Не переименовывайте существующие ключи без изменения backend-сервиса.
- Загружайте `.docx`, не `.doc`.

---

**Дальше:** [Эксплуатация](operations.md) · **Назад:** [API](api.md)
