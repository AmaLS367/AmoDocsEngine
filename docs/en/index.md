<p align="center">
  <img src="../assets/github-social-preview.png" alt="AmoDocsEngine documentation preview" width="100%">
</p>

<h1 align="center">📚 AmoDocsEngine Documentation</h1>

<p align="center">
  <strong>Install, configure, operate, and extend a secure amoCRM → DOCX automation engine.</strong>
</p>

<p align="center">
  <a href="../../README.md">🏠 README</a> ·
  <a href="../ru/index.md">🇷🇺 Русский</a> ·
  <a href="api.md">🔌 API</a> ·
  <a href="../../SECURITY.md">🛡️ Security</a>
</p>

---

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

## 🧩 Documentation Type Map

| Type | Pages |
| --- | --- |
| Tutorial | [Getting Started](getting-started.md) |
| How-to | [Configuration](configuration.md), [Templates](templates.md), [Operations](operations.md) |
| Reference | [API](api.md), [Development](development.md) |
| Explanation | Architecture and workflow sections below |

## 🏗️ Architecture Snapshot

The browser UI calls small PHP endpoints. Backend services handle request auth, quote calculation, amoCRM API calls, DOCX rendering, prefill cache, notes, and logs.

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

## 🔁 Generation Workflow

```mermaid
sequenceDiagram
    participant UI as Browser UI
    participant Prefill as prefill.php
    participant Quote as quote.php
    participant Generate as generate.php
    participant Amo as amoCRM
    participant Docx as DOCX

    UI->>Prefill: request cached state
    Prefill-->>UI: products + generate_token
    UI->>Quote: products + discount
    Quote-->>UI: backend totals
    UI->>Generate: token + template + products
    Generate->>Amo: lead/contact
    Generate->>Docx: render template
    Generate->>Amo: replace note
    Generate-->>UI: document URL
```

Diagram sources: [architecture](../assets/architecture-overview.mmd), [workflow](../assets/workflow-overview.mmd).

---

**Next:** [Getting Started](getting-started.md) · **Русский:** [Документация](../ru/index.md)
