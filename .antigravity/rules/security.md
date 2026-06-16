---
description: Web security guardrails including SQLi/XSS prevention, file upload safety, and Azure AD guidelines
globs:
  - **/*.php
  - **/*.js
---

# Secure Coding Standards

- **SQL Injection Prevention:** Never concatenate user input into database query strings. Always use prepared statement parameters or Eloquent binding.
- **XSS Prevention:** Ensure all user input rendered in the browser is properly escaped. Validate and sanitize HTML when rich text is required.
- **File Upload Safety:** Only allow specific safe file extensions (e.g. `pdf`, `png`, `jpg`, `docx`, `xlsx`). Verify the actual MIME type of uploaded files. Store uploaded assets outside the public web root unless explicitly intended.
- **Secrets & Credentials:** Never commit API keys, client secrets, passwords, or credentials to version control. Reference environment variables via `env()` or `config()`.
- **Azure AD Session Handling:** Ensure session states match and validate claims upon return from Azure AD callback.
- **Database Safety Guardrail:** The agent must never run commands that perform destructive operations (e.g., `migrate:fresh`, `migrate:rollback`, `db:wipe`) on non-sqlite databases. The agent must never execute code, scripts, or migrations that truncate or drop database tables.
