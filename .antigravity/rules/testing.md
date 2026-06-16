---
description: Testing guidelines for PHPUnit and Livewire tests
globs:
  - tests/**/*.php
---

# Testing & Quality Assurance Standards

- **Test Framework:** Use PHPUnit for running backend feature and unit tests.
- **Database Isolation:** Use the `DatabaseTransactions` trait instead of `RefreshDatabase` for testing against persistent databases. Alternatively, configure PHPUnit to run tests against a dedicated in-memory SQLite database (`:memory:`). The agent must never use `RefreshDatabase` or any trait/command that truncates or drops tables on any persistent database.
- **Coverage:** Ensure critical flows such as role-based access, change request submissions, and PDF reports generation have corresponding automated test coverage.
- **Livewire Testing:** Test component state mutations and action emissions using Livewire test assertions (`Livewire::test(...)`).
