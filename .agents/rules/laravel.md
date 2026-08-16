---
description: Comprehensive Laravel architecture, Eloquent ORM, core/advanced features, API standards, testing, and best practices.
globs:
  - app/**/*.php
  - routes/**/*.php
  - database/**/*.php
  - config/**/*.php
  - tests/**/*.php
  - resources/views/**/*.blade.php
---

# Laravel Framework & Architecture Guidelines

You are an expert in Laravel, the PHP framework for web artisans.

## Key Principles
- **Developer Experience & Syntax:** Prioritize elegant syntax, clean code, and developer happiness.
- **Architecture:** Strictly adhere to the MVC (Model-View-Controller) architecture.
- **Dependency Injection:** Leverage Laravel's powerful Service Container and Dependency Injection.
- **Ecosystem Awareness:** Utilize modern Laravel tools and ecosystem solutions (Forge, Vapor, Nova, Horizon, Pulse, etc.).
- **Modern PHP:** Use modern PHP 8+ features (named arguments, attributes, match expressions, typed properties, constructor property promotion).

## Eloquent ORM
- **Active Record:** Use Eloquent's Active Record implementation idiomatically.
- **Relationships:** Properly define and leverage model relationships (`hasOne`, `hasMany`, `belongsTo`, `belongsToMany`, etc.).
- **Scopes & Attributes:** Use local/global scopes for reusable query constraints, and modern Accessors/Mutators via `Attribute::make()`.
- **Collections:** Leverage Eloquent/Support Collections for fluent, declarative data manipulation.
- **Database Migrations & Seeders:** Ensure structured migrations and idempotent seeders for consistent database schema and data states.

## Core Features
- **Routing:** Organize routes cleanly across `routes/web.php` and `routes/api.php`.
- **Middleware:** Apply middleware for request filtering, authentication, rate limiting, and authorization.
- **Controllers:** Prefer skinny Resource Controllers over bloated actions.
- **Blade Templating:** Use Blade templating, components, and layout inheritance for clean view rendering.
- **Artisan Console:** Create custom Artisan commands for background, batch, and administrative operations.
- **Validation:** Always encapsulate validation logic within dedicated `FormRequest` classes or `$request->validate()`.

## Advanced Features
- **Queues & Jobs:** Offload time-consuming tasks (emails, webhooks, heavy calculations) to queued jobs (Redis/Database).
- **Events & Listeners:** Decouple domain events from side effects using Events and Listeners/Subscribers.
- **Task Scheduling:** Schedule recurring commands and background maintenance using Laravel's Console Scheduler (`schedule`).
- **Notifications:** Deliver multi-channel notifications (Email, Slack, Database, SMS).
- **File Storage:** Abstract file manipulation using the `Storage` facade and disk configurations (Local, S3).
- **Caching:** Cache expensive queries and computed values using Redis or Memcached.

## API Development
- **API Resources:** Transform Eloquent models and responses consistently using Eloquent API Resources (`JsonResource`).
- **Authentication:** Secure API endpoints with Laravel Sanctum or Passport tokens.
- **Rate Limiting:** Protect endpoints against abuse with configured rate limiting middleware (`throttle`).
- **API Versioning:** Version all API endpoints (e.g., `/api/v1/`).
- **Exception Handling:** Return consistent, structured JSON responses for API exceptions and validation failures.

## Testing
- **Testing Frameworks:** Integrate PHPUnit or Pest for comprehensive test suites.
- **Feature vs. Unit Tests:** Distinguish between lightweight unit tests and comprehensive HTTP/feature tests.
- **Database Isolation:** Use database transactions (`DatabaseTransactions` trait) or in-memory databases for fast, safe test runs.
- **Mocking & Faking:** Utilize built-in fakes (`Event::fake()`, `Queue::fake()`, `Mail::fake()`, `Http::fake()`, `Storage::fake()`).
- **Browser Testing:** Employ Laravel Dusk for end-to-end browser automation when required.

## Best Practices
- **Skinny Controllers:** Keep controller methods slim; delegate business logic to dedicated Service, Action, or Domain classes.
- **Service Classes & DI:** Encapsulate complex domain logic in Service classes injected via the container.
- **PSR Standards:** Comply with PSR-12 coding standards and PSR-4 autoloading.
- **Mass Assignment Security:** Guard all models against mass assignment using explicit `$fillable` definitions.
- **Query Optimization:** Proactively prevent N+1 query bottlenecks with eager loading (`with([...])`).
