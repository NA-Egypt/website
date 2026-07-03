# Permissions and Roles System - Service Bodies
# نظام الصلاحيات والأدوار - هيئات الخدمة

This document details the configuration, assignment, and enforcement of the permissions and roles system for Service Bodies on the NA-Egypt platform.
يوضح هذا المستند إعداد وتعيين وتطبيق نظام الصلاحيات والأدوار الخاص بهيئات الخدمة في منصة زمالة المدمنين المجهولين بمصر.

---

## English Version

### 1. Overview
The platform uses Spatie's Laravel Permission package to implement Role-Based Access Control (RBAC). For Service Bodies, access controls are refined to distinguish three primary service positions:
- **RCM (Regional Committee Member)**
- **FAC (Facilitator)**
- **SEC (Secretary)**

Rather than creating separate Spatie roles for each position, the platform uses a single **ServiceBody** role combined with direct, granular **Spatie Permissions** assigned to user accounts.

### 2. Spatie Permissions Mapping
The following table outlines the permissions introduced for Service Body Agendas and how they map to the service positions:

| Position | Role Requirement | Default Spatie Permissions | Allowed Actions |
| :--- | :--- | :--- | :--- |
| **RCM** | `ServiceBody` | `create sb agenda`, `edit sb agenda` | Create and edit draft agendas for their own Service Body. View agendas. |
| **Secretary (SEC)** | `ServiceBody` | `create sb agenda`, `edit sb agenda` | Create and edit draft agendas for their own Service Body. |
| **Facilitator (FAC)** | `ServiceBody` | `approve sb agenda`, `delete sb agenda` | Approve submitted agendas (releasing them) or delete draft agendas for their own Service Body. |
| **RSC / Admin** | `super admin` or `rsc` | *All permissions granted by default* | Fully manage all agendas across all Service Bodies. |

### 3. User Administration UI/UX
The user creation and edit interfaces ([create.blade.php](file:///var/www/html/new/resources/views/users/create.blade.php) and [edit.blade.php](file:///var/www/html/new/resources/views/users/edit.blade.php)) have been updated with premium UI components:
- **Glassmorphic Containers**: Clean, semi-transparent card layouts utilizing the application's native theme.
- **Collapsible Accordions**: Permissions are grouped into logical, expandable panels:
  - **Service Body Agendas** (`create sb agenda`, `edit sb agenda`, `approve sb agenda`, `delete sb agenda`)
  - **Store & Inventory** (`manage store`, `view lit inventory`)
  - **General Calendar** (`can_manage_calendar`)
  - **General & Others** (fallback category)
- **Select All Checkbox**: Each accordion header contains a "Select All" toggle to check or uncheck all permissions inside that category simultaneously.
- **iOS-style Toggles**: Checkboxes are rendered as modern switch selectors.

### 4. Code Enforcements
Permission checks are enforced in both the web and REST API controllers:
- **Web Controller**: [ServiceBodyAgendaController.php](file:///var/www/html/new/app/Http/Controllers/ServiceBodyAgendaController.php)
- **API Controller**: [Api/ServiceBodyAgendaController.php](file:///var/www/html/new/app/Http/Controllers/Api/ServiceBodyAgendaController.php)

#### Enforcement Logic:
- **Create/Store**: Checked via `$user->hasPermissionTo('create sb agenda')`. The user must belong to the Service Body target unless they are RSC/Admin.
- **Edit/Update**: Checked via `$user->hasPermissionTo('edit sb agenda')`. The agenda must be in `draft` status.
- **Destroy**: Checked via `$user->hasPermissionTo('delete sb agenda')`. The agenda must be in `draft` status.
- **Approve / Return to Draft**: Checked via `$user->hasPermissionTo('approve sb agenda')`. The agenda must be in `submitted` status.

---

## النسخة العربية (Arabic Version)

### 1. نظرة عامة
تستخدم المنصة حزمة Spatie للتحكم في الوصول بناءً على الأدوار (RBAC). بالنسبة لهيئات الخدمة، تم تحسين التحكم في الوصول للتمييز بين ثلاثة مناصب رئيسية:
- **RCM (ممثل لجنة الخدمة)**
- **FAC (الميسر)**
- **SEC (السكرتير)**

بدلاً من إنشاء أدوار منفصلة لكل منصب، يستخدم النظام دوراً موحداً وهو **ServiceBody**، مقترناً بصلاحيات **Spatie Permissions** تفصيلية ومباشرة يتم تعيينها لحسابات المستخدمين.

### 2. خريطة الصلاحيات (Spatie Permissions)
يوضح الجدول التالي الصلاحيات المخصصة لأجندات هيئة الخدمة وتوزيعها على المناصب:

| المنصب | الدور المطلوب | صلاحيات Spatie الافتراضية | الإجراءات المسموح بها |
| :--- | :--- | :--- | :--- |
| **RCM** | `ServiceBody` | `create sb agenda`, `edit sb agenda` | إنشاء وتعديل مسودات الأجندات الخاصة بهيئة الخدمة التابع لها، واستعراضها. |
| **السكرتير (SEC)** | `ServiceBody` | `create sb agenda`, `edit sb agenda` | إنشاء وتعديل مسودات الأجندات التابعة لهيئة الخدمة الخاصة به. |
| **الميسر (FAC)** | `ServiceBody` | `approve sb agenda`, `delete sb agenda` | اعتماد الأجندات المقدمة ونشرها، أو حذف مسودات الأجندات التابعة لهيئة الخدمة الخاصة به. |
| **لجنة الخدمة الإقليمية / المشرف** | `super admin` أو `rsc` | *تمنح جميع الصلاحيات افتراضياً* | إدارة كاملة للأجندات عبر كافة هيئات الخدمة. |

### 3. واجهة مستخدم إدارة الأعضاء (UI/UX)
تم تحديث واجهات إضافة وتعديل المستخدمين ([create.blade.php](file:///var/www/html/new/resources/views/users/create.blade.php) و [edit.blade.php](file:///var/www/html/new/resources/views/users/edit.blade.php)) بتصميم متطور يحتوي على:
- **حاويات زجاجية (Glassmorphic)**: تصاميم كروت شبه شفافة تتكيف مع ثيم الموقع.
- **منسدلات قابلة للطي (Accordions)**: يتم تجميع الصلاحيات في لوحات قابلة للتوسيع:
  - **أجندات هيئة الخدمة (Service Body Agendas)**
  - **المخزن والمخزون (Store & Inventory)**
  - **التقويم العام (General Calendar)**
  - **صلاحيات عامة وأخرى (General & Others)**
- **زر اختيار الكل (Select All)**: يتضمن ترويسة كل منسدل مفتاح "اختر الكل" لتحديد أو إلغاء تحديد كافة صلاحيات هذا القسم دفعة واحدة.
- **مفاتيح تبديل iOS**: تم استبدال خانات الاختيار العادية بمفاتيح تبديل حديثة.

### 4. تطبيق القيود في البرمجة
يتم التحقق من الصلاحيات في كل من وحدات التحكم الخاصة بالويب وواجهة برمجة التطبيقات (API):
- **الويب**: [ServiceBodyAgendaController.php](file:///var/www/html/new/app/Http/Controllers/ServiceBodyAgendaController.php)
- **واجهة التطبيقات (API)**: [Api/ServiceBodyAgendaController.php](file:///var/www/html/new/app/Http/Controllers/Api/ServiceBodyAgendaController.php)

#### منطق التحقق:
- **الإنشاء والحفظ**: يتم التحقق بواسطة `$user->hasPermissionTo('create sb agenda')` ويشترط انتساب المستخدم لهيئة الخدمة المستهدفة (ما لم يكن مشرفاً أو RSC).
- **التعديل والتحديث**: يتم التحقق بواسطة `$user->hasPermissionTo('edit sb agenda')` ويشترط أن تكون الأجندة بحالة "مسودة" (draft).
- **الحذف**: يتم التحقق بواسطة `$user->hasPermissionTo('delete sb agenda')` ويشترط أن تكون الأجندة بحالة "مسودة".
- **الاعتماد / الإرجاع لمسودة**: يتم التحقق بواسطة `$user->hasPermissionTo('approve sb agenda')` ويشترط أن تكون الأجندة بحالة "مقدمة" (submitted).
