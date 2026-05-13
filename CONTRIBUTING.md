<h1 align="center">🤝 Contributing</h1>

<p align="center">
  <strong>Small, focused, verifiable changes for a shared-hosting friendly PHP document engine.</strong>
</p>

<p align="center">
  <a href="README.md">🏠 README</a> ·
  <a href="docs/en/development.md">🧪 Development Docs</a> ·
  <a href="SECURITY.md">🛡️ Security</a> ·
  <a href="CODE_OF_CONDUCT.md">📜 Code of Conduct</a>
</p>

---

## ⚡ Fast Path

```powershell
composer install
Copy-Item config/config.example.php config/config.php
.\vendor\bin\phpunit
```

## 🧭 Contribution Map

| Change type | Start here | Required check |
| --- | --- | --- |
| Docs | `README.md`, `docs/en`, `docs/ru` | Link check + PHPUnit |
| API behavior | `api/`, `src/` | PHPUnit test for behavior |
| Templates | `templates/`, docs | Manual DOCX check if template changes |
| Security | `src/Security`, `SECURITY.md` | Token/HMAC tests |
| amoCRM integration | `src/AmoCrm` | Mocked client tests |

## 🌿 Branch Names

Use short, scoped branch names:

- `docs/readme-polish`
- `fix/generate-token-validation`
- `refactor/template-registry`
- `test/quote-service`

## ✅ Pull Request Checklist

Before opening a PR:

- [ ] Run `.\vendor\bin\phpunit`.
- [ ] Keep the PR focused on one concern.
- [ ] Check changed docs links.
- [ ] Explain config, deployment, or security impact.
- [ ] Include sanitized examples only.

## 🔒 Never Commit

Use real secrets only in ignored local files. Never commit:

- `config/config.php`
- `config/token.json`
- `.env`
- `data/`
- `documents/`
- `logs/`
- generated DOCX files
- real amoCRM/customer data

## 🧾 Commit Style

Prefer concise concern-based commits:

- `docs: update configuration guide`
- `fix(frontend): use api base path`
- `refactor(api): isolate note service`
- `test: cover field id mapping`

## 📚 Related Docs

- [English development guide](docs/en/development.md)
- [Russian development guide](docs/ru/development.md)
- [Security policy](SECURITY.md)
