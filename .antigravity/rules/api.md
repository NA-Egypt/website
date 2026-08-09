---
description: RESTful API standards, API v1 versioning, input validation, and HTTP status code standards
globs:
  - routes/api.php
  - app/Http/Controllers/Api/**/*.php
---

# API & Mobile Integration Standards

- **API Versioning:** All API endpoints MUST be versioned under the `/api/v1/` prefix in `routes/api.php`.
- **Strict Input Validation & Security:** NEVER pass raw `$request->all()` to `Model::create()` or `Model::update()`. All incoming API payloads must be explicitly validated using `$request->validate()` or dedicated `FormRequest` classes to prevent mass-assignment vulnerabilities.
- **HTTP Status Codes:**
  - `201 Created` for resource creation in `store()` methods (e.g. `(new Resource($item))->response()->setStatusCode(201)`).
  - `200 OK` for successful read (`index()`, `show()`) and update (`update()`) requests.
  - `204 No Content` for resource deletions (`destroy()`).
  - `422 Unprocessable Content` for validation failures.
  - `401 Unauthorized` / `403 Forbidden` for authentication or authorization failures.
- **RESTful Resource Naming:** Use lowercase plural nouns for resource endpoints (e.g. `/api/v1/contact-requests`, `/api/v1/agendas`). Avoid verbs in URIs.
- **Authentication:** Enforce Sanctum middleware (`auth:sanctum`) on all state-changing endpoints (POST, PUT, DELETE).
- **Pagination & Eager Loading:** `index()` methods must avoid returning unpaginated `Model::all()` on large tables. Eager load relationships (`with([...])`) to eliminate N+1 queries.
- **Response Envelopes:** Return data wrapped in standard API Eloquent Resources (`Resource::collection()` or `new Resource()`).
