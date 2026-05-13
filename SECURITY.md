# Security Policy

## Supported Branches

Security fixes target the current `main` branch.

## Sensitive Files

Never publish or attach these files in public issues, pull requests, screenshots, logs, or comments:

- `config/config.php`
- `config/token.json`
- `.env`
- amoCRM access or refresh tokens
- HMAC secrets
- customer CRM data
- generated documents containing private data

Use `config/config.example.php` for examples.

## Reporting a Vulnerability

If the report includes secrets, customer data, tokens, or a working exploit path, do not open a public issue. Send the maintainer a private report through the contact channel listed on the repository profile or open a minimal public issue that says a private security report is needed without including the sensitive details.

Include:

- affected endpoint or component;
- expected vs actual behavior;
- reproduction steps without real credentials;
- suggested severity;
- whether any token, document, or CRM data may be exposed.

## Request Authentication

Browser generation should use server-issued `generate_token` values. HMAC mode is only for trusted server-to-server clients and must never expose `hmac_secret` in browser JavaScript.
