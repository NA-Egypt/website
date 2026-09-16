# NA-Egypt Website & Administration Portal

Welcome to the NA-Egypt (Narcotics Anonymous Egypt) platform (`naegypt.org` / `egyptna.org`). This repository houses the complete bilingual fellowship portal, combining public service directories, an administrative dashboard, a warehouse and literature inventory suite, a dynamic form builder, and a production-grade RESTful API v1 for mobile and external client integrations.

---

## 📚 Project Documentation Index

- **[APPLICATION_FEATURES.md](file:///var/www/html/new/APPLICATION_FEATURES.md):** Exhaustive bilingual (English & Arabic) operational and technical guide to all platform features, workflows, and role standards.
- **[api_documentation.md](file:///var/www/html/new/api_documentation.md):** Complete REST API v1 reference covering all 30 endpoints, JSON schemas, headers, authentication, and TypeScript integration clients.
- **[CROSS_PLATFORM_MOBILE_APP_PROMPT.md](file:///var/www/html/new/CROSS_PLATFORM_MOBILE_APP_PROMPT.md):** Master specification and AI prompt for building/updating the offline-first React Native (Expo + WatermelonDB) mobile app.
- **[PERMISSIONS_ROLES.md](file:///var/www/html/new/PERMISSIONS_ROLES.md):** Comprehensive reference on Spatie RBAC roles, permission categories, and scoping invariants.
- **[TURNSTILE_SETUP.md](file:///var/www/html/new/TURNSTILE_SETUP.md):** Cloudflare Turnstile configuration instructions for spam prevention.

---

## 🌟 Key Platform Capabilities

- **Bilingual Public Portal (AR / EN):** Native RTL/LTR layout, meeting search engine with prioritized fallback URLs, printable meeting directory wizard, events calendar, literature, and helplines.
- **Store & Literature Inventory Suite:**
  - Active stocktaking sessions with real-time discrepancy calculation and automatic adjustment journals.
  - Middleware-enforced mutation lock (`CheckStoreLocked`) during active audits.
  - Chronological audit ledger, digital inventory slips with receipt acknowledgement, and multi-stage literature cart approvals (GSR -> Treasurer -> Literature Committee).
- **Custom Dynamic Form Builder:**
  - Visual form construction with dynamic field types (text, email, date, yes/no with conditional text, dynamic tables, entity lookups).
  - Public clean URLs (`/f/{slug}` and `/api/v1/forms/public/{slug}`), view counter, conversion rates, and automated email notifications.
- **Organizational Hierarchy & Workgroups:**
  - Clear structural separation between Parent Committees (`parent_id = null`) and Workgroups (`parent_id != null`).
  - Strict role scoping (Committees, Workgroups, RSC Committee 83, Super Admin).
  - Embedded workgroup reports workflow feeding into official regional reporting.
- **IT Change Request System:**
  - Service update request portal with multipart file uploads (PDF, images, spreadsheets up to 5MB) and ticket tracking.
- **RESTful API v1:**
  - 30 production endpoints versioned under `/api/v1/`.
  - Mass-assignment protection with explicit validation, Sanctum token authentication & revocation (`POST /api/v1/auth/logout`), and 100% automated test coverage.

---

## 🛠️ Tech Stack

- **Backend Framework:** Laravel ^11.9 (PHP ^8.2 / 8.5 compatible)
- **Frontend Layer:** Livewire ^3.7, Tailwind CSS ^3.4, Bootstrap ^5.3, Alpine.js, jQuery ^3.7
- **Database:** MySQL / SQLite (for testing)
- **Key Dependencies:**
  - `spatie/laravel-permission`: Role-based access control with localized two-line cards
  - `laravel/socialite` & `socialiteproviders/microsoft-azure`: Azure AD enterprise SSO
  - `laravel/sanctum`: Token-based API security
  - `mcamara/laravel-localization`: Bilingual routing and locale management (AR/EN)
  - `mpdf/mpdf` & `barryvdh/laravel-dompdf`: PDF generation with native Arabic fonts (Cairo, Amiri)
  - `google/recaptcha`: Public form spam protection

---

## 🔑 Core Environment Settings (`.env`)

Configure your `.env` file with the following minimum required settings:

### 1. Database Connection
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### 2. Microsoft Azure AD Authentication
Used for administrative logins:
```env
AZURE_CLIENT_ID=your_azure_client_id
AZURE_CLIENT_SECRET=your_azure_client_secret
AZURE_REDIRECT_URI=https://your-domain.com/login/microsoft/callback
AZURE_TENANT_ID=your_azure_tenant_id
ALLOWED_DOMAIN=naegypt.org
```

### 3. Cloudflare Turnstile
```env
TURNSTILE_SITE_KEY=your_turnstile_site_key
TURNSTILE_SECRET_KEY=your_turnstile_secret_key
```

### 4. Mail Settings (SMTP)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=hello@naegypt.org
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@naegypt.org
MAIL_FROM_NAME="NA Egypt"
```

---

## 🚀 Installation & Local Setup

```bash
# 1. Install PHP & Node dependencies
composer install
npm install

# 2. Setup environment & encryption key
cp .env.example .env
php artisan key:generate

# 3. Database migrations & seeders
php artisan migrate --seed

# 4. Compile frontend assets
npm run build
# Or start Vite in dev mode:
npm run dev

# 5. Serve the application
php artisan serve
```

---

## 🧪 Testing & Quality Assurance

The application is thoroughly covered by PHPUnit Feature and Unit test suites:

```bash
# Run all tests (260 tests across all application modules)
php vendor/bin/phpunit

# Run the complete REST API v1 test suite (77 tests, 420 assertions)
php vendor/bin/phpunit tests/Feature/Api/

# Run individual test suites:
php vendor/bin/phpunit tests/Feature/Api/MeetingApiTest.php
php vendor/bin/phpunit tests/Feature/Api/CustomFormApiTest.php
php vendor/bin/phpunit tests/Feature/Api/DirectOnlineGroupApiTest.php
php vendor/bin/phpunit tests/Feature/Api/WorkgroupApiTest.php
php vendor/bin/phpunit tests/Feature/Api/ChangeRequestApiTest.php
php vendor/bin/phpunit tests/Feature/Api/AuthApiTest.php
```

---

## 📄 License
This software is licensed under the [MIT license](file:///var/www/html/new/LICENSE).
