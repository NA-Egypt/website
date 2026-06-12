# NA-Egypt Website & Administration Portal

Welcome to the NA-Egypt (Narcotics Anonymous Egypt) website and administration portal. This platform hosts the bilingual public website (meeting finder, informational pages, calendar) and the secure management dashboard for service committees, groups, agendas, change requests, and reports, along with a RESTful API.

For a detailed view of the application capabilities, please check the [APPLICATION_FEATURES.md](file:///var/www/html/new/APPLICATION_FEATURES.md).

---

## 🛠️ Tech Stack

- **Backend Framework:** Laravel ^11.9 (PHP ^8.2)
- **Frontend Layer:** Livewire ^3.7, Tailwind CSS ^3.4, Bootstrap ^5.3, jQuery ^3.7
- **Database:** MySQL
- **Key Dependencies:**
  - `spatie/laravel-permission`: Role-based access controls
  - `laravel/socialite` & `socialiteproviders/microsoft-azure`: Secure Azure AD authentication
  - `laravel/sanctum`: API token validation
  - `mcamara/laravel-localization`: Bilingual route routing and locales (AR/EN)
  - `mpdf/mpdf` & `barryvdh/laravel-dompdf`: PDF generation with Arabic font rendering (Amiri, Cairo)
  - `google/recaptcha`: Form protection against spam

---

## 🔑 Core Environment Settings (`.env`)

To configure the application, duplicate your environment settings and ensure the following keys are populated:

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
Used for secure administration logins.
```env
AZURE_CLIENT_ID=your_azure_client_id
AZURE_CLIENT_SECRET=your_azure_client_secret
AZURE_REDIRECT_URI=https://your-domain.com/login/microsoft/callback
AZURE_TENANT_ID=your_azure_tenant_id
ALLOWED_DOMAIN=naegypt.org
```

### 3. Google reCAPTCHA
Required for protecting contact/submission forms. Refer to [RECAPTCHA_SETUP.md](file:///var/www/html/new/RECAPTCHA_SETUP.md) for full instructions.
```env
RECAPTCHA_SITE_KEY=your_recaptcha_site_key
RECAPTCHA_SECRET_KEY=your_recaptcha_secret_key
```

### 4. Mail Settings (SMTP)
Configured for automated report and change request notifications.
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=hello@naegypt.org
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@naegypt.org
MAIL_FROM_NAME="[Website Contact Form]"
```

---

## 🚀 Installation & Local Setup

Follow these steps to set up the project locally:

### 1. Clone & Setup Configuration
Ensure your PHP CLI worker settings and directory permissions are set up, and configure your `.env` file as described above.

### 2. Install Dependencies
Install PHP dependencies via Composer and Javascript dependencies via npm:
```bash
composer install
npm install
```

### 3. Generate Encryption Key
```bash
php artisan key:generate
```

### 4. Database Migrations & Seeders
Run the database migrations and seed system defaults (e.g., base roles, permissions, cities):
```bash
php artisan migrate --seed
```

### 5. Compile Assets
Build frontend styles and assets:
```bash
npm run build
```
Or run Vite in development mode:
```bash
npm run dev
```

### 6. Run the Application
You can run the built-in development server:
```bash
php artisan serve
```
Alternatively, use the configured Composer dev script which spins up the server, queue listener, logs, and Vite concurrently:
```bash
composer dev
```

---

## 🧪 Testing

The project is backed by a PHPUnit test suite validating critical flows like change requests, committee report approvals, and reCAPTCHA integrations.

To execute the test suite, run:
```bash
php artisan test
```
To run specific feature tests:
```bash
php artisan test tests/Feature/ChangeRequestTest.php
```

---

## 📄 License
This application is open-sourced software licensed under the [MIT license](file:///var/www/html/new/LICENSE).
