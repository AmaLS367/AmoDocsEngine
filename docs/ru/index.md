# 📚 Документация AmoDocsEngine

Русская документация по установке, настройке, эксплуатации и расширению AmoDocsEngine.

## 🧭 Навигация

| Раздел | Для чего |
| --- | --- |
| [🚀 Запуск](getting-started.md) | Установить зависимости, развернуть файлы, пройти OAuth, открыть UI |
| [⚙️ Конфигурация](configuration.md) | `config/config.php`, `amo_fields`, шаблоны, безопасность и пути |
| [🔌 API](api.md) | Контракты `prefill.php`, `quote.php`, `generate.php` |
| [🧾 Шаблоны](templates.md) | DOCX-плейсхолдеры, строки таблицы, новые шаблоны |
| [🛠️ Эксплуатация](operations.md) | Логи, диагностика, миграция, smoke-тесты |
| [🧪 Разработка](development.md) | Структура проекта, тесты, локальный workflow |

## ⚡ Быстрый маршрут

1. Запустить: `composer install`.
2. Настроить: скопировать `config/config.example.php` в `config/config.php`.
3. Заполнить amoCRM OAuth и ID полей в `amo_fields`.
4. Проверить API через `quote.php`.
5. Разобрать ошибки через `logs/generate.log`.

## 🏗️ Кратко об архитектуре

Browser UI вызывает небольшие PHP endpoints. Backend отвечает за авторизацию запроса, расчет сумм, amoCRM API, генерацию DOCX, кэш префилла, заметки и логи.

```mermaid
flowchart LR
    UI["Browser UI"]
    API["api/ endpoints"]
    Security["src/Security"]
    Amo["src/AmoCrm"]
    Documents["src/Documents"]
    Storage["src/Storage"]
    Logs["src/Support"]

    UI --> API
    API --> Security
    API --> Amo
    API --> Documents
    API --> Storage
    API --> Logs
```

## 🔁 Workflow генерации

```mermaid
sequenceDiagram
    participant UI as Browser UI
    participant Prefill as prefill.php
    participant Quote as quote.php
    participant Generate as generate.php
    participant Amo as amoCRM
    participant Docx as DOCX

    UI->>Prefill: запросить cached state
    Prefill-->>UI: products + generate_token
    UI->>Quote: products + discount
    Quote-->>UI: backend totals
    UI->>Generate: token + template + products
    Generate->>Amo: lead/contact
    Generate->>Docx: render template
    Generate->>Amo: replace note
    Generate-->>UI: document URL
```

Исходники схем: [architecture](../assets/architecture-overview.mmd), [workflow](../assets/workflow-overview.mmd).

Дальше: [Запуск](getting-started.md)
