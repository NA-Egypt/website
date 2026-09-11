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
- **Explicit ID Assignment in Tests:** Eloquent `Model::create(['id' => X])` drops `id` unless it is in `$fillable`. When testing specific primary keys (e.g. Committee 83), instantiate the model, assign `$model->id = X;`, and call `$model->save()`.
- **Fakes & Mocks:** Utilize `Event::fake()`, `Queue::fake()`, `Mail::fake()`, `Http::fake()`.
- **Browser Automation:** Dusk for full end-to-end integration tests.

### 6. NA-Egypt Hierarchy, Roles & Scoping Standards
- **Service Committee & Workgroup Hierarchy:**
  - Workgroups are sub-entities belonging to a parent Service Committee (`parent_id != null`).
  - Workgroups have a dedicated `Workgroups` role, distinct from the parent committee's `Committees` role.
  - Parent committees have full visibility (view, create, edit, delete) over all child workgroups belonging to them.
  - Workgroup users only have access to view and manage their own assigned workgroup details and meetings.
  - Physical address fields (`ar_address`, `en_address`) must be nullable/optional for workgroups.
- **RSC Role (`rsc`) Representation & Scoping:**
  - Any officer with the `rsc` role (e.g. `RSC@naegypt.org`, `RCP@naegypt.org`, `RVCP@naegypt.org`, `ARSC@naegypt.org`) represents the **Egypt Regional Service Committee** (`id: 83`, `RSC@naegypt.org`).
  - Workgroup visibility, creation, and management for users with role `rsc` are strictly scoped to workgroups directly under Committee `83` (e.g. IT Workgroup), preserving fellowship committee boundaries unless the user has the `super admin` role.
  - RSC officers must have access to "My Committee Details" (linking to Committee 83) and "My Workgroups" (listing Committee 83 workgroups) in the navigation sidebar.
- **Workgroup Reports Embedding Lifecycle:**
  - Workgroup reports are created in `draft` status for the parent committee to review.
  - Workgroup reports are not submitted directly to RSC. Instead, the parent committee embeds active draft workgroup reports into their official committee report before submitting to RSC.
  - Embedded workgroup reports transition to status `embedded` and are linked via `parent_report_id`.
- **PHP 8 Ternary Expression Guardrail:**
  - In Blade templates and PHP files, never write unparenthesized nested ternary expressions (e.g., `a ? b : c ?: d`). Always explicitly parenthesize: `(a ? b : c) ?: d` or `a ? b : (c ?: d)`.
- **Bilingual Localization Invariant:**
  - Every user-facing string, action button, breadcrumb, and flash message must have corresponding translations added to both `resources/lang/ar/messages.php` and `resources/lang/en/messages.php`.
- **Human-Readable Permissions & RBAC Standards:**
  - **Permission Key Immutability:** Technical Spatie permission identifiers in the database and code checks (`hasPermissionTo(...)`, middleware) must remain stable identifiers and never be altered for UI display purposes.
  - **Decoupled Localization:** Human-readable names, descriptions, and categories are defined in `resources/lang/{locale}/permissions.php` and accessed via `App\Models\Permission` model accessors (`display_name`, `description`, `category`).
  - **Two-Line Card UI Standard:** In administration selection forms (User Create/Edit, Role Permission Assignment), permissions must be rendered as two-line cards: bold localized title, explanatory description subtitle, and a subtle monospace technical key badge, accompanied by a live filter matching title, description, and key.
  - **Centralized Grouping:** Use `App\Models\Permission::getGrouped()` across all views to maintain a single source of truth for categories, icons, and counter badges.
  - **DataTable Query Safety:** The `permissions` table does not contain virtual columns like `description`; search logic in controllers (`PermissionController`) must translate user search terms against `permissions.items` translation catalogs or query actual schema columns (`name`).

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
