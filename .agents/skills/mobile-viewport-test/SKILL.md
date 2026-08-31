---
name: mobile-viewport-test
description: Test and audit mobile viewport responsiveness and Core Web Vitals (CLS, LCP, INP) across Blade templates, Vue components, and CSS assets without using a browser. Trigger after frontend changes, npm run build, or artisan view/cache updates.
license: MIT
metadata:
  author: Antigravity
  version: "1.0"
allowed-tools: Bash(node:*, php:*) Read Edit Grep Glob
---

# Mobile Viewport & Web Vitals Testing Skill

Use this skill to audit, diagnose, and fix mobile viewport responsiveness (320px–430px) and Core Web Vitals (CLS, LCP, INP) layout health across all Blade views (`resources/views/**/*.blade.php`), Vue single-file components (`resources/js/**/*.vue`), CSS stylesheets (`resources/css/**`), and compiled Vite bundles (`public/build/assets/*.css`) without requiring a real or headless browser.

---

## When to Run This Skill

Run the mobile viewport and Web Vitals check whenever:
1. **Frontend / UI Changes Made**: Any edit to `.vue`, `.blade.php`, or `.css` files.
2. **Build Execution**: Immediately after `npm run build`, `npm run dev`, or `mix`.
3. **Template & View Cache Updates**: Immediately after running `php artisan view:cache`, `php artisan view:clear`, `php artisan optimize`, or `php artisan cache:clear`.
4. **Before Committing or Finishing a Task**: Verify zero mobile regressions and zero layout shift hazards before submitting work.

---

## How to Execute the Audit

### Option 1: Via NPM / Node CLI
```bash
# Standard scan (colorized table + JSON report generated)
npm run test:mobile

# Or run the Node script directly
node .agents/skills/mobile-viewport-test/scripts/audit.mjs

# Strict mode (fails with exit code 1 on any critical error)
npm run test:mobile -- --strict

# Target a specific file or directory
node .agents/skills/mobile-viewport-test/scripts/audit.mjs --target=resources/views/frontend/home.blade.php
```

### Option 2: Via Laravel Artisan
```bash
# Artisan command runner
php artisan viewport:test

# Strict mode
php artisan viewport:test --strict

# Specific file target
php artisan viewport:test --file=resources/js/components/GenericDataTable.vue
```

---

## Report Output

The analyzer produces:
1. **Color-Coded Terminal Tables**: Displays `File`, `Line`, `Severity` (`CRITICAL`, `WARNING`, `TIP`), `Rule Code`, and actionable `Fix Suggestion`.
2. **Machine-Readable Report**: Saved at `.agents/reports/mobile-viewport-report.json` with structured diagnostics for automated parsing.

---

## Rule Catalog & Remediation Recipes

### 1. Viewport Meta Configuration (`VIEWPORT_META_MISSING`, `VIEWPORT_ZOOM_DISABLED`)
- **Problem**: Root HTML layout is missing `<meta name="viewport" ...>` or disables accessibility pinch-to-zoom (`user-scalable=no`, `maximum-scale=1.0`).
- **Fix**: Ensure root layouts have:
  ```html
  <meta name="viewport" content="width=device-width, initial-scale=1">
  ```

### 2. Fixed Width & Oversizing on Mobile (`OVERSIZED_FIXED_WIDTH`, `FIXED_MIN_WIDTH_HAZARD`)
- **Problem**: Elements using fixed widths `w-[400px]`, `width: 500px`, or `min-w-[400px]` without mobile breakpoints cause horizontal scrolling on 320px/375px screens.
- **Fix**: Use fluid mobile-first widths:
  ```html
  <!-- Before -->
  <div class="w-[500px]">...</div>

  <!-- After (Mobile first, scaled on desktop) -->
  <div class="w-full max-w-[500px]">...</div>
  <!-- OR with responsive prefix -->
  <div class="w-full md:w-[500px]">...</div>
  ```

### 3. Horizontal Overflow & Tables (`UNWRAPPED_TABLE_OVERFLOW`, `W_SCREEN_SCROLLBAR_BUG`, `FLEX_NOWRAP_OVERFLOW`)
- **Problem**: Data tables or `<pre>` blocks without scroll wrappers push the document width beyond the 320px mobile viewport. Using `w-screen` causes unwanted horizontal scrollbars due to the browser vertical scrollbar width.
- **Fix**:
  - Wrap tables in `<div class="overflow-x-auto w-full"><table>...</table></div>`.
  - Replace `w-screen` with `w-full` or `max-w-full`.
  - Add `flex-wrap` or breakpoint prefixes `flex-col sm:flex-row`.

### 4. Multi-Column Grids without Breakpoints (`NON_RESPONSIVE_GRID`)
- **Problem**: `grid-cols-3` or `grid-cols-4` declared directly without `sm:` / `md:` forces 3–4 tiny squished columns on 320px mobile screens.
- **Fix**: Always adopt mobile-first column progression:
  ```html
  <!-- Before -->
  <div class="grid grid-cols-4 gap-4">...</div>

  <!-- After -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">...</div>
  ```

### 5. Touch Target Accessibility (`TOUCH_TARGET_TOO_SMALL`)
- **Problem**: Interactive buttons, anchors, or controls with height/width < 44px (`h-6 w-6` without padding) cause tap frustration on touchscreens (WCAG 2.5.5 / 2.5.8).
- **Fix**: Ensure buttons have at least 44x44px hitbox:
  ```html
  <!-- Before -->
  <button class="h-6 w-6">...</button>

  <!-- After (Using min-dimensions or padding) -->
  <button class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center p-2">...</button>
  ```

### 6. Web Vitals: Cumulative Layout Shift (CLS) (`CLS_UNSIZED_IMAGE`, `CLS_UNSIZED_MEDIA`, `CLS_FONT_DISPLAY_MISSING`)
- **Problem**: Images, SVGs, or iframes rendered without `width`/`height` or aspect-ratio bounding boxes shift the page layout when assets load. Font imports missing `display=swap` cause layout flickers.
- **Fix**:
  - Add `width="..." height="..."` or `aspect-video` / `aspect-square` / `w-full h-auto` classes.
  - Append `&display=swap` to Google/Bunny font URLs.

### 7. Web Vitals: Largest Contentful Paint (LCP) (`LCP_HERO_NOT_PRIORITIZED`, `LCP_LAZY_ON_HERO`)
- **Problem**: Hero banner images rendered with `loading="lazy"` or missing `fetchpriority="high"` delay initial render.
- **Fix**:
  ```html
  <img src="/images/hero.webp" fetchpriority="high" loading="eager" alt="Hero banner" class="w-full h-auto">
  ```

### 8. Web Vitals: Interaction to Next Paint (INP) (`INP_MISSING_TOUCH_ACTION`)
- **Problem**: Missing `touch-action: manipulation` causes a 300ms double-tap delay on mobile browsers.
- **Fix**: Apply `touch-action: manipulation` or Tailwind utility class on interactive buttons/tabs.

---

## The AI Agent Fix Loop

When executing changes:
1. Run `node .agents/skills/mobile-viewport-test/scripts/audit.mjs`.
2. Review the resulting list of warnings and errors.
3. Apply the appropriate remediation recipe to the affected file(s).
4. Re-run `npm run test:mobile` to verify all issues are resolved (target: 0 critical errors).
5. Verify build succeeds with `npm run build`.
