---
description: Backend rules for Laravel controllers, models, migrations, seeders, and Spatie permissions
globs:
  - app/**/*.php
  - config/**/*.php
  - database/**/*.php
---

# Backend Development Standards

- **Eloquent & Queries:** Always use Eloquent models or the Query Builder. Avoid raw database queries unless absolutely necessary.
- **Route Model Binding:** Utilize route model binding in controllers where possible.
- **Spatie Permissions:** Enforce role-based access control (RBAC) checks at the controller level or routes using middleware. Validate permissions before executing state-changing actions.
- **Migrations:** Define explicit foreign key constraints and indexes on lookup/search columns. Set sensible default values.
- **Seeders:** Ensure all seeders are idempotent and can be run safely multiple times.
- **Localization:** Use `mcamara/laravel-localization` helpers for multi-language routing and output messaging.
- **Type Hinting:** Declare return types and parameter types on all new controller methods and service functions.
