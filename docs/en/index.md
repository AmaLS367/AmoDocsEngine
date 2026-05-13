# 📚 AmoDocsEngine Documentation

English documentation for installing, configuring, operating, and extending AmoDocsEngine.

## 🧭 Navigation

| Section | Use it for |
| --- | --- |
| [🚀 Getting Started](getting-started.md) | Install dependencies, deploy files, run OAuth, open the UI |
| [⚙️ Configuration](configuration.md) | `config/config.php`, `amo_fields`, templates, security and paths |
| [🔌 API](api.md) | `prefill.php`, `quote.php`, `generate.php` request/response contracts |
| [🧾 Templates](templates.md) | DOCX placeholders, row cloning, adding new document templates |
| [🛠️ Operations](operations.md) | Logs, troubleshooting, migration, smoke tests |
| [🧪 Development](development.md) | Project structure, tests, local workflow, contribution checks |

## ⚡ Fast Route

1. Install dependencies with `composer install`.
2. Copy `config/config.example.php` to `config/config.php`.
3. Fill amoCRM OAuth credentials and `amo_fields` IDs.
4. Run OAuth through `oauth.php?code=...`.
5. Open `public/ui.html?lead_id=<ID>`.

## 🏗️ Architecture Snapshot

The browser UI calls small PHP endpoints. Backend services handle request auth, quote calculation, amoCRM API calls, DOCX rendering, prefill cache, notes, and logs.

Next: [Getting Started](getting-started.md)
