---
description: Testing guidelines for PHPUnit and Livewire tests
globs:
  - tests/**/*.php
---

# Testing & Quality Assurance Standards

- **Test Framework:** Use PHPUnit for running backend feature and unit tests.
- **Database Isolation:** Use `RefreshDatabase` or `DatabaseTransactions` trait to maintain a clean database state between tests.
- **Coverage:** Ensure critical flows such as role-based access, change request submissions, and PDF reports generation have corresponding automated test coverage.
- **Livewire Testing:** Test component state mutations and action emissions using Livewire test assertions (`Livewire::test(...)`).
