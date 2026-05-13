# 🧾 Word Templates

Templates live in `templates/` and are rendered through PhpWord `TemplateProcessor`.

## Built-in Templates

- `order` → `order_template.docx`
- `act` → `act_template.docx`

## Common Placeholders

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

## Order Table Rows

The order template clones rows by `row_num` and fills:

- `${row_num}`
- `${услуга_название}`
- `${row_qty}`
- `${row_price}`
- `${row_discount}`
- `${row_sum}`

## Adding a Template

1. Add a `.docx` file to `templates/`.
2. Add a key in `config/config.php` under `templates`.
3. Send that key as `template` from the UI or API client.

Next: [Operations](operations.md)
