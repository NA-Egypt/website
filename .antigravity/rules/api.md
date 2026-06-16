---
description: RESTful API standards, Laravel Sanctum token validation, and mobile application support
globs:
  - routes/api.php
  - app/Http/Controllers/Api/**/*.php
---

# API & Mobile Integration Standards

- **Authentication:** Enforce Laravel Sanctum middleware (`auth:sanctum`) on all state-changing endpoints (POST, PUT, DELETE).
- **RESTful Design:** Adhere to RESTful resource routing. Return standard HTTP status codes:
  - `200 OK` or `201 Created` for successful requests.
  - `422 Unprocessable Content` for validation failures.
  - `401 Unauthorized` / `403 Forbidden` for auth failures.
- **Response Structure:** Always return data in consistent JSON envelopes.
- **Offline Compatibility:** Keep response payloads lightweight and ensure resource listings include `updated_at` timestamps to allow clients to determine sync status.
