---
description: Rules for Blade templates, Livewire components, forms, and custom CSS
globs:
  - resources/views/**/*.blade.php
  - resources/css/**/*.css
  - resources/js/**/*.js
---

# Frontend Development Standards

- **HTML Escaping:** Use Blade double curly braces `{{ $value }}` to automatically escape output. Only use `{!! $value !!}` when rendering sanitized/validated HTML explicitly.
- **RTL & Localization:** Ensure layouts automatically adjust for Arabic (RTL) using direction attributes and RTL-friendly utility classes (e.g. Bootstrap or Tailwind's start/end rules instead of left/right).
- **Livewire Components:** Keep component state minimal. Validate all properties in Livewire requests using standard Laravel validation rules.
- **Forms & reCAPTCHA:** Always include CSRF tokens. For public forms, integrate Google reCAPTCHA and validate the response token backend-side.
- **Interactivity:** Prefer subtle CSS animations and transitions for state changes.
