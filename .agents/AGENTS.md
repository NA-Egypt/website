# Project AGENTS Guidelines

## Laravel Expert Principles & Architecture Guidelines

### 1. Key Principles
- **Syntax & Dev Experience:** Elegant syntax, maintainability, and developer happiness.
- **MVC Pattern:** Strict separation of concerns (Model-View-Controller).
- **Dependency Injection:** Leverage the Service Container and constructor/method injection.
- **Ecosystem:** Modern Laravel tooling (Forge, Vapor, Nova, Horizon, Pulse).
- **Modern PHP:** Exploit modern PHP features (enums, typed properties, match expressions, attributes).

### 2. Eloquent ORM & Data Layer
- **Active Record:** Idiomatic Eloquent usage with explicit `$fillable` mass-assignment guarding.
- **Relationships:** Well-defined `hasOne`, `hasMany`, `belongsTo`, `belongsToMany`, etc.
- **Scopes & Attributes:** Query scopes for reusable constraints; `Attribute::make()` for accessors/mutators.
- **Collections:** Eloquent & Support Collections for expressive transformations.
- **Migrations & Seeders:** Structured schema migrations and idempotent, additive seeders.
- **Query Optimization:** Proactively avoid N+1 query bottlenecks via eager loading (`with(...)`).

### 3. Core & Advanced Features
- **Routing & Middleware:** Distinct `routes/web.php` and `routes/api.php` with appropriate middleware filtering.
- **Skinny Controllers & Services:** Keep controllers slim; isolate business logic in dedicated Service/Action classes.
- **Blade Templating:** Clean componentized Blade views.
- **Artisan Console:** Custom commands for CLI and scheduled background tasks.
- **Validation:** Explicit Form Requests or `$request->validate()`.
- **Queues & Jobs:** Asynchronous job processing (Redis/database) for heavy operations.
- **Events & Listeners:** Decouple domain side-effects using Events/Listeners.
- **Task Scheduling & Storage:** Scheduled cron tasks via Scheduler; file abstraction via `Storage` facade.
- **Caching:** Cache expensive queries/computations via Redis/Memcached.

### 4. API & Architecture Rules
1. **API Versioning:** All REST API endpoints must be versioned under `/api/v1/` prefix in `routes/api.php`.
2. **Mass-Assignment Security:** Do not pass `$request->all()` directly to Eloquent models. Always perform explicit validation using `$request->validate([...])` or custom FormRequest classes.
3. **HTTP Status Code Consistency:**
   - Resource creation (`store()`) returns `201 Created`.
   - Read/update operations return `200 OK`.
   - Resource deletions return `204 No Content`.
   - Validation failures return `422 Unprocessable Content`.
4. **Pagination & Query Optimization:** Avoid returning unpaginated `Model::all()` collections for large datasets. Always eager-load relationships (`with(...)`) to prevent N+1 performance bottlenecks.
5. **Resource Naming:** Use plural nouns for REST endpoints (e.g. `/api/v1/contact-requests`, `/api/v1/calendar-events`).
6. **API Resources & Auth:** Use Eloquent API Resources for data transformations and Sanctum/Passport for endpoint security.

### 5. Testing & QA
- **PHPUnit & Pest:** Comprehensive Feature and Unit test coverage.
- **Database Transactions:** Test isolation via `DatabaseTransactions` trait or in-memory SQLite.
- **Fakes & Mocks:** Utilize `Event::fake()`, `Queue::fake()`, `Mail::fake()`, `Http::fake()`.
- **Browser Automation:** Dusk for full end-to-end integration tests.

## Security Audit Principles & OWASP Guidelines

### 1. Attack Surface Analysis
- Map all entry points (APIs, forms, file uploads, webhooks), data flows, and trust boundaries.
- Identify privileged operations and external dependency risks.

### 2. OWASP Security Checklist & Vulnerability Review
- **Injection (SQL, Command, NoSQL):** Parameterize all database queries. Avoid raw string concatenation or shell execution with user input.
- **Broken Access Control & Authorization:** Enforce authorization checks on every endpoint. Protect against IDOR and enforce principle of least privilege.
- **Sensitive Data Exposure:** Encrypt sensitive data at rest and in transit. Keep API keys/secrets in environment variables. Ensure generic error responses in production without exposing stack traces.
- **Cross-Site Scripting (XSS):** Validate and escape user inputs before rendering. Configure Content Security Policy and avoid dangerous dynamic sinks (`innerHTML`, `eval`).
- **Security Misconfiguration & Security Headers:**
  - `Strict-Transport-Security` (HSTS)
  - `Content-Security-Policy`
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: DENY`
  - `X-XSS-Protection: 1; mode=block`
  - `Referrer-Policy`
  - `Permissions-Policy`
- **Insufficient Logging & Monitoring:** Ensure key security events are logged without exposing secrets or PII.

### 3. Risk Assessment & Vulnerability Reporting Standard
When reporting vulnerabilities, classify by:
- **Severity**: Critical / High / Medium / Low
- **Likelihood & Impact**: Ease of exploitation vs potential damage
- **Report Format**: Include Location, Description, Impact, Reproduction Steps, Remediation Code/Strategy, and OWASP/CWE references.
