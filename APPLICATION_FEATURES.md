# Application Features - NA-Egypt Website & Operations System

This document provides a comprehensive operational and technical reference for all systems, modules, and workflows implemented across the NA-Egypt platform (`naegypt.org` / `egyptna.org`). The platform is built on Laravel and comprises a bilingual public website, an administrative management dashboard, an inventory and literature supply chain suite, dynamic form builders, and a RESTful API v1 for mobile application integration.

---

## 1. Public Website & Fellowship Services (Frontend)

- **Bilingual Experience:** Native English and Arabic language support with dynamic Right-To-Left (RTL) styling and localized date formatting.
- **Smart Meeting Finder:**
  - Multi-parameter search filter by City (Governorate), Neighborhood, Service Body, Group Name, Day of the Week, Meeting Format/Language, and Free-text keywords.
  - Prioritized fallback URL resolution hierarchy for in-person and online recovery meetings (`location_url`, `meeting_url`, `group.location`, `direct_online_group.zoom_url`).
  - Printable meeting directory wizard with PDF and CSV export tools.
- **Events & Assemblies Calendar:** Interactive yearly calendar showcasing regional conventions, service workshops, and fellowship gatherings.
- **Fellowship Information Repositories:** Dedicated sections for literature resources, audio speaker archives, public information (PI/PR), and frequently asked questions.
- **Public Form Submissions & Helplines:**
  - Public custom forms accessible via permanent clean URLs (`/f/{slug}`).
  - Public contact forms protected with Google reCAPTCHA.
  - Live helpline directory covering Egypt Region, Alexandria, and Upper Egypt.
- **Real-Time Fellowship Statistics:** Dynamic homepage counters tracking active weekly meetings, in-person groups, virtual groups, and covered governorates.

---

## 2. Store & Literature Inventory Management System

The platform features an enterprise-grade inventory, warehousing, and literature distribution system tailored for Narcotics Anonymous service structures:

### 2.1 Inventory Stocktaking & Auditing
- **Stocktaking Sessions:** Authorized servants can initiate dedicated stocktaking sessions (`/store/stocktaking`).
- **Mutation Locking:** While an active stocktaking session is open, store inventory mutation routes are automatically locked via middleware (`CheckStoreLocked`) to guarantee audit integrity and prevent race conditions.
- **Counting & Variance Auditing:** Dynamic item count entry, real-time variance calculation between counted and on-hand stock, and automated adjustment journal entries upon session completion.
- **Audit Reports:** Full export of stocktaking audit sessions in PDF (Amiri font rendering) and CSV formats.

### 2.2 Warehouse Operations & Inventory Ledger
- **Catalog Management:** Tracking Arabic/English titles, SKU/item codes, unit costs, selling prices, physical quantities, and low-stock thresholds.
- **Bulk Operations:** Rapid batch receiving, bulk stock transfers between warehouse locations, returns processing, and batch inline price/quantity updates.
- **Audit Ledger & Reconciliation:** Chronological ledger logging all incoming/outgoing stock transactions, linked to inventory slips and user IDs, exportable to PDF and CSV.

### 2.3 Inventory Slips & Committee Literature
- **Inventory Slips (`/slips`):** Automated delivery slips generated for every physical stock movement.
- **Digital Acknowledgement:** Receiving parties must digitally acknowledge receipt on the platform, transitioning slips from `issued` to `acknowledged`.
- **Committee Literature Orders (`/committee-literature`):** Non-invoice literature requests for fellowship committees (H&I, PR, Translations) with dedicated slip generation and return processing.

### 2.4 Literature Purchasing Lifecycle
- **Ordering Cart (`/literature-requests/cart`):** GSRs and group representatives build literature carts with live subtotal and prudent reserve calculations.
- **Multi-Stage Approval Pipeline:**
  1. *Submitted:* Group submits order.
  2. *Treasurer Review:* Regional/Area treasurer reviews payment verification (`/literature-requests/treasurer`).
  3. *Literature Committee Fulfillment:* Literature committee prepares stock and issues dispatch slips (`/literature-requests/committee`).
  4. *Archived:* Completed orders archived with downloadable printable invoice PDFs.

---

## 3. Custom Dynamic Form Builder (`/forms`)

A flexible visual form builder enabling service committees to generate custom surveys, registration forms, and questionnaires without writing code:

- **Form Construction:** Create forms with customizable titles, descriptions, status (`draft`, `published`, `archived`), and unique public slugs.
- **Dynamic Field Types:** Text, Textarea, Email, Numeric, Date, Yes/No with conditional explanation textbox, Dynamic Table/Matrix, Dropdown selects, and Dynamic Entity Lookups (`groups`, `cities`, `neighborhoods`, `committees`, `servicebodies`).
- **Public Rendering & Security:** Published forms render seamlessly on mobile and desktop via `/f/{slug}` and the public API. Submissions validate required inputs and field-specific rules dynamically.
- **Analytics & Conversion Metrics:** Built-in view count tracking and submission conversion rate percentages.
- **Notification Routing:** Configurable comma/newline-separated email distribution lists that trigger automated HTML email notifications upon every new submission.
- **Reporting & Data Export:** Visual report summary dashboard, individual submission PDF downloads, and consolidated CSV data exports.

---

## 4. Organizational Structure, Roles & Scoping

### 4.1 Service Committee & Workgroup Hierarchy
- **Entity Separation:** Service Committees represent permanent fellowship bodies (`parent_id = null`). Workgroups are specialized sub-entities belonging to a parent committee (`parent_id != null`).
- **Role Scoping Standards:**
  - `super admin`: Full system-wide visibility and administrative authority.
  - `Committees`: Access strictly scoped to managing their assigned committee and creating/editing child workgroups belonging to them.
  - `Workgroups`: Users assigned to a workgroup have access restricted to viewing and editing their own workgroup profile and meetings.
  - `rsc`: Regional officers represent the Egypt Regional Service Committee (`id: 83`). Workgroup access is strictly scoped to workgroups directly under Committee 83 (e.g., IT Workgroup).
- **Embedded Workgroup Reports Lifecycle:** Workgroups submit draft periodic reports. Parent committees review and embed draft workgroup reports into their official committee report before submitting to the Regional Service Committee (RSC).

### 4.2 User Management & RBAC
- **Azure Active Directory SSO:** Enterprise OAuth integration with Microsoft 365 (`@naegypt.org` domain verification).
- **Two-Line Permission Cards:** Localized technical permissions categorized under `resources/lang/{locale}/permissions.php` with real-time UI filtering.
- **User Impersonation:** Super admins can safely impersonate service officers to troubleshoot issues, with a one-click session restore banner.

---

## 5. Administrative Dashboard & Service Tools

- **Group & Meeting Operations:** Comprehensive CRUD interface for in-person groups, direct online groups, and weekly meeting schedules.
- **Committee Periodic Reports:** Sub-committee periodic reporting module with PDF generation, status workflow (`draft`, `submitted`, `approved`, `returned`, `embedded`), and file attachment storage.
- **Agendas Management:** Group business meeting agenda archiving with bulk PDF generation and Service Body agenda approval pipelines.
- **IT Change Request System (`/change-requests`):** Form enabling members and trusted servants to request data updates (meetings, groups, committees), attach supporting documents (PDF, images, spreadsheets up to 5MB), and track ticket statuses (`pending`, `in_progress`, `completed`, `rejected`).
- **Helpline Management & Volunteer Tracking (`/helpline`):** End-to-end helpline call management featuring a public responsive submission form (`/forms/helpline`), active volunteer directory management (`/helpline/volunteers`), administrative datatable with real-time filtering (by date range, shift including `8:00 PM - 10:00 PM`, caller type, referral source, and Step 12 status), dynamic PDF report generator with Arabic typography, and dedicated REST API endpoints (`/api/v1/helpline-calls`).
- **Facebook Ad Targeting Generator:** Geographic calculation tool generating coordinates, radiuses, and static map previews for social media awareness campaigns.

---

## 6. RESTful API v1 (Mobile & External Clients)

- **Architecture:** Complete REST API versioned under `/api/v1/` with 33 production endpoints.
- **Security:** Mass-assignment protection via explicit validation on all mutations, Sanctum personal access token revocation on logout (`POST /api/v1/auth/logout`), and strict HTTP status code invariants (`200`, `201`, `204`, `422`).
- **Hybrid & Public Endpoints:** Unauthenticated public read access for meetings, groups, direct online groups, calendar events, workgroups, lookup directories, and public helpline schema/submissions (`/api/v1/helpline-calls/schema`, `POST /api/v1/helpline-calls`); authenticated administrative access for helpline call listings and sensitive resources.
- **Comprehensive Test Suite:** 100% test passing across 13 PHPUnit API feature test suites (84 tests, 493 assertions).

---
---

# ميزات التطبيق - موقع ونظام زمالة المدمنين المجهولين في مصر (NA-Egypt)

يوفر هذا المستند مرجعاً تشغيلياً وتقنياً شاملاً لجميع الأنظمة والوحدات وسير العمل المطبقة في منصة زمالة المدمنين المجهولين في مصر (`naegypt.org` / `egyptna.org`). بنيت المنصة باستخدام إطار عمل Laravel وتتضمن موقعاً عاماً ثنائي اللغة، ولوحة تحكم إدارية، ونظاماً متكاملاً للمستودع وإدارة سلاسل إمداد الأدبيات، ومنشئ نماذج ديناميكي، وواجهة برمجة تطبيقات (RESTful API v1) لتكامل تطبيقات الهاتف المحمول.

---

## 1. الموقع العام وخدمات الزمالة (الواجهة الأمامية)

- **تجربة ثنائية اللغة:** دعم كامل وتلقائي للغتين العربية والإنجليزية مع دعم كامل لاتجاه الكتابة من اليمين إلى اليسار (RTL) وتنسيق التواريخ محلياً.
- **الباحث الذكي عن الاجتماعات:**
  - تصفية متقدمة حسب المحافظة، الحي، هيئة الخدمة، اسم المجموعة، يوم الأسبوع، لغة ونوع الاجتماع، والبحث بالكلمات المفتاحية.
  - تسلسل هرمي ذكي لتحديد الروابط التلقائية لاجتماعات التعافي الميدانية والافتراضية عبر الإنترنت (`zoom_url`، خرائط جوجل).
  - معالج طباعة وتصدير دليل الاجتماعات بصيغتي PDF و CSV.
- **تقويم الفعاليات والمؤتمرات:** تقويم سنوي تفاعلي يعرض المؤتمرات الإقليمية وورش عمل الخدمة وتجمعات الزمالة.
- **مكتبة معلومات الزمالة:** أقسام مخصصة لمصادر الأدبيات، وأرشيف التسجيلات الصوتية للمتحدثين، ومعلومات العلاقات العامة للجمهور (PI/PR)، والأسئلة الشائعة.
- **النماذج العامة وخطوط المساعدة:**
  - نماذج مخصصة عامة يمكن الوصول إليها عبر روابط نظيفة دائمة (`/f/{slug}`).
  - نموذج "اتصل بنا" العام محمي بواسطة Google reCAPTCHA.
  - دليل خطوط المساعدة الهاتفية المباشرة الذي يغطي إقليم مصر، والإسكندرية، ومصر العليا.
- **إحصائيات حية وفورية:** عدادات تفاعلية في الصفحة الرئيسية تحصي الاجتماعات الأسبوعية النشطة، والمجموعات الميدانية، والمجموعات الافتراضية، والمحافظات المغطاة.

---

## 2. نظام إدارة المستودع ومخزون الأدبيات

تتميز المنصة بنظام متقدم لإدارة المستودعات وتوزيع أدبيات الزمالة مصمم خصيصاً لهياكل الخدمة:

### 2.1 الجرد والتدقيق المخزني
- **جلسات الجرد المخزني:** يمكن للخدام المخولين بدء جلسات جرد رسمية للمستودع (`/store/stocktaking`).
- **قفل العمليات المخزنية:** أثناء وجود جلسة جرد نشطة، يتم قفل جميع عمليات التعديل على المستودع تلقائياً عبر وسيط برمجيات (`CheckStoreLocked`) لضمان دقة ونزاهة التدقيق ومنع تضارب البيانات.
- **تسجيل العد وحساب الفروقات:** إدخال كميات الجرد الفعلي، وحساب الفروقات في الوقت الفعلي بين المخزون الفعلي والدفتري، وإنشاء قيود تسوية آلية عند اعتماد الجلسة.
- **تقارير التدقيق:** تصدير جلسات تدقيق الجرد بصيغتي PDF (باستخدام خط الأميري) و CSV.

### 2.2 عمليات المستودع وسجل حركات المخزون
- **إدارة كتالوج الأدبيات:** تتبع العناوين بالعربية والإنجليزية، والأكواد (SKU)، وتكلفة الشراء، وسعر التوزيع، والكميات الفعلية، وحدود إعادة الطلب المنخفضة.
- **العمليات المجمعة:** استلام شحنات مجمعة، تحويل المخزون بين مواقع التخزين، معالجة المرتجعات، وتحديث الأسعار والكميات بشكل فوري ومجمع.
- **دفتر الأستاذ والمطابقة المخزنية:** سجل زمني يوثق كافة حركات المخزون الواردة والمنصرفة والمرتجعة مرتبطاً بأذون الصرف وأرقام المستخدمين، مع إمكانية التصدير إلى PDF و CSV.

### 2.3 أذون الصرف وأدبيات اللجان
- **أذون استلام المخزون (`/slips`):** إنشاء أذون صرف وتسليم إلكترونية آلية لكل حركة مخزنية.
- **إقرار الاستلام الرقمي:** يجب على المستلم تأكيد الاستلام رقمياً عبر حسابه في المنصة، مما ينقل الإذن من حالة "صادر" إلى "تم الإقرار بالاستلام".
- **طلبات أدبيات اللجان (`/committee-literature`):** طلبات أدبيات غير مسعرة للجان الخدمة (المستشفيات والمؤسسات H&I، العلاقات العامة PR، الترجمة) مع أذون تسليم ومتابعة للمرتجعات.

### 2.4 دورة شراء وطلب الأدبيات للمجموعات
- **عربة الطلب (`/literature-requests/cart`):** يقوم ممثلو المجموعات (GSR) ببناء عربة طلبات الأدبيات مع حساب تلقائي للتكلفة الإجمالية والاحتياطي الحكيم.
- **مسار الاعتماد متعدد المراحل:**
  1. *مقدم:* ترسل المجموعة طلب الأدبيات.
  2. *مراجعة أمين الصندوق:* يراجع أمين صندوق الإقليم/المنطقة بيانات التحويل البنكي أو الدفع (`/literature-requests/treasurer`).
  3. *تنفيذ لجنة الأدبيات:* تجهز لجنة الأدبيات الشحنة وتصدر أذون الصرف الإلكترونية (`/literature-requests/committee`).
  4. *الأرشيف:* أرشفة الطلبات المكتملة مع فواتير إلكترونية قابلة للتنزيل بصيغة PDF.

---

## 3. منشئ النماذج والاستبيانات الديناميكي (`/forms`)

منشئ نماذج مرئي ومرن يمكن لجان الخدمة وهيئاتها من بناء استبيانات ونماذج تسجيل مخصصة دون الحاجة لكتابة كود برمجي:

- **بناء النماذج:** إنشاء نماذج بعناوين مخصصة، وأوصاف، وحالات متعددة (`مسودة`، `منشور`، `مؤرشف`)، وروابط نصية مخصصة (`slug`).
- **أنواع الحقول الديناميكية:** نص، نص طويل، بريد إلكتروني، رقم، تاريخ، نعم/لا مع صندوق توضيح شرطي، جداول ديناميكية (صفوف وأعمدة)، قوائم منسدلة، وقوائم ربط الكيانات الحية (`المجموعات`، `المدن`، `الأحياء`، `لجان الخدمة`، `هيئات الخدمة`).
- **العرض العام والأمان:** تعرض النماذج المنشورة بسلاسة على الهواتف والمتصفحات عبر `/f/{slug}` والـ API العام، مع التحقق الديناميكي من صحة المدخلات وإلزامية الحقول.
- **إحصائيات ومعدلات التحويل:** تتبع تلقائي لعدد مرات مشاهدة النموذج ونسبة معدل اكتمال وتقديم الإجابات (Conversion Rate).
- **توجيه الإشعارات التلقائية:** تحديد قوائم بريدية متعددة تتلقى إشعاراً بريدياً منسقاً وفورياً عند تقديم أي استجابة جديدة للنموذج.
- **التقارير وتصدير البيانات:** لوحة تحكم لتلخيص الإجابات، وتنزيل الإجابات الفردية بصيغة PDF، وتصدير قاعدة البيانات بالكامل إلى CSV.

---

## 4. الهيكل التنظيمي، الأدوار ونطاقات الصلاحية

### 4.1 الهيكل الهرمي للجان الخدمة ومجموعات العمل
- **فصل الكيانات:** لجان الخدمة هي كيانات خدمة دائمة ورئيسية (`parent_id = null`). مجموعات العمل (Workgroups) هي كيانات فرعية متخصصة تابعة للجنة خدمة رئيسية (`parent_id != null`).
- **معايير تحديد الصلاحيات والنطاق:**
  - المشرف العام (`super admin`): رؤية وصلاحيات إدارية كاملة عبر جميع اللجان والمجموعات.
  - مستخدم اللجان (`Committees`): تقتصر صلاحياته فقط على إدارة لجنته المعينة وإنشاء وتعديل مجموعات العمل التابعة لها.
  - مستخدم مجموعات العمل (`Workgroups`): مقيد برؤية وتعديل بيانات مجموعة العمل المعين عليها واجتماعاتها فقط.
  - خدام المؤتمر الإقليمي (`rsc`): يمثلون لجنة الخدمة الإقليمية لمصر (`id: 83`). وتقتصر صلاحيات مجموعات العمل لديهم على المجموعات التابعة مباشرة للجنة 83 (مثل مجموعة عمل تكنولوجيا المعلومات).
- **دورة تقارير مجموعات العمل المدمجة:** تنشئ مجموعات العمل تقاريرها كمسودات. تقوم اللجنة الأم بمراجعة هذه التقارير ودمجها داخل التقرير الرسمي للجنة الأم قبل تقديمه لاجتماع خدمة الإقليم (RSC).

### 4.2 إدارة المستخدمين والصلاحيات (RBAC)
- **تسجيل الدخول الموحد (SSO) مع Azure AD:** تكامل مؤسسي مع Microsoft 365 (التحقق من نطاق `@naegypt.org`).
- **بطاقات الصلاحيات ثنائية الأسطر:** صلاحيات تقنية معربة ومفصلة تحت `resources/lang/{locale}/permissions.php` مع فلترة فورية وسريعة في واجهة المستخدم.
- **محاكاة المستخدمين (Impersonation):** إمكانية للمشرفين العامين بالدخول بحساب أي خادم خدمة لحل المشكلات التقنية مع شريط استعادة فوري بنقرة واحدة.

---

## 5. لوحة التحكم الإدارية وأدوات الخدمة

- **إدارة المجموعات والاجتماعات:** واجهة تحكم متكاملة للمجموعات الميدانية، ومجموعات التعافي المباشرة عبر الإنترنت، وجداول الاجتماعات الأسبوعية.
- **تقارير اللجان الدورية:** نظام رفع وتوثيق تقارير اللجان مع إنشاء ملفات PDF، ودورة اعتماد الحالات (`مسودة`، `مقدم`، `معتمد`، `مرتجع`، `مدمج`)، وإرفاق المستندات.
- **إدارة جداول الأعمال (الأجندات):** أرشفة أجندات اجتماعات عمل المجموعات مع التصدير المجمع إلى PDF، ومسارات اعتماد أجندات هيئات الخدمة.
- **نظام طلبات التعديل التقنية (`/change-requests`):** نموذج يمكن الأعضاء والخدام من إرسال طلبات تعديل البيانات (اجتماعات، مجموعات، لجان)، مع إرفاق مستندات مساعدة (PDF، صور، جداول بيانات حتى 5 ميجابايت)، ومتابعة حالات التذاكر.
- **نظام إدارة وتوثيق مكالمات خط المساعدة (`/helpline`):** منظومة متكاملة لخط المساعدة تضم نموذج استجابة عام وسريع (`/forms/helpline`)، وإدارة دليل المتطوعين النشطين (`/helpline/volunteers`)، وجدول بيانات إداري تفاعلي مع فلترة فورية (حسب التواريخ وفترات المناوبة بما فيها مناوبة `8:00 PM - 10:00 PM` ونوع المتصل ومصدر الإحالة وحالة الخطوة 12)، واستخراج تقارير إحصائية دورية بصيغة PDF، مع واجهات برمجة تطبيقات مخصصة (`/api/v1/helpline-calls`).
- **أداة استهداف إعلانات فيسبوك التوعوية:** أداة حسابية جغرافية تحسب إحداثيات المجموعات ونطاقاتها وتولد معاينة للخرائط الثابتة لحملات التوعية.

---

## 6. واجهة برمجة التطبيقات (RESTful API v1)

- **الهندسة البرمجية:** واجهة برمجية كاملة تحت البادئة `/api/v1/` تضم 33 نقطة اتصال للمنظومة.
- **الأمان والتحقق:** حماية كاملة من ثغرات التعيين المجمع (Mass Assignment) عبر التحقق الصريح لجميع المدخلات، وإلغاء صلاحية الرموز عند تسجيل الخروج (`POST /api/v1/auth/logout`)، وثبات رموز الحالة HTTP (`200`, `201`, `204`, `422`).
- **نقاط اتصال هجينة وعامة:** قراءة عامة بدون مصادقة للاجتماعات والمجموعات والمجموعات الافتراضية والتقويم ومجموعات العمل والدلائل، بالإضافة إلى استكشاف مخطط وتقديم مكالمات خط المساعدة العامة (`/api/v1/helpline-calls/schema`, `POST /api/v1/helpline-calls`)؛ مع فرض المصادقة لعمليات الإنشاء والتعديل والحذف واستعراض سجلات المكالمات.
- **اختبارات آلية شاملة:** نجاح بنسبة 100% عبر 13 حزمة اختبارات ميزات PHPUnit (84 اختباراً، 493 توكيداً).
