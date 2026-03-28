# Application Features - NA-Egypt Website

This document outlines the core features of the NA-Egypt website and application system. The platform is built using Laravel and provides a public-facing website, an administrative dashboard, and a RESTful API for mobile integration.

## 1. Public Website (Frontend)
- **Bilingual Interface:** Full support for both English and Arabic, including Right-To-Left (RTL) layout for Arabic.
- **Meeting Finder:** Advanced search functionality to find NA meetings by City, Neighborhood, or Group name.
- **Meeting Exports:** Ability for users to export the list of meetings to PDF or CSV formats.
- **Events Calendar:** A public-facing page showcasing upcoming NA events and workshops.
- **Informational Pages:** Dedicated sections for Literature, Speaker recordings, Questions & Answers, and Information for the Public.
- **Committee Information:** Displays details about various service committees.
- **Surveys & Forms:** Includes a Fellowship Development (FD) survey and a "Contact Us" form for public inquiries.
- **Live Statistics:** The homepage displays real-time statistics including the number of weekly meetings, active groups, and covered governorates.

## 2. Management Dashboard (Admin Panel)
- **Secure Authentication:** Integrated Microsoft Azure Active Directory (Azure AD) for secure, enterprise-grade login.
- **Role-Based Access Control (RBAC):** Comprehensive management of Users, Roles, and detailed Permissions.
- **Geographic Management:** Create and manage entries for Cities (Governorates) and Neighborhoods.
- **Organizational Structure:** Tools to manage Groups, Service Bodies, and Service Committees.
- **Meeting Management:** Full CRUD (Create, Read, Update, Delete) capabilities to manage meeting schedules, locations, and linked features.
- **Committee Reports:** System to submit, manage, export to PDF, and email committee reports.
- **Financial Tracking:** Capability to track and manage organizational transactions.
- **Events Management:** Interactive Livewire-powered yearly calendar for adding and managing organizational events.
- **Topic Management:** Manage relevant topics or tags for discussions.

## 3. Mobile API
- **Comprehensive REST API:** Endpoints exposing data for all major models (Meetings, Groups, Cities, Events, Reports, etc.).
- **Authentication:** Token-based security using Laravel Sanctum for mobile clients.
- **Semi-Online Support:** Designed to provide data necessary for mobile applications that can function intermittently offline.

---

# ميزات التطبيق - موقع زمالة المدمنين المجهولين في مصر (NA-Egypt)

يوضح هذا المستند الميزات الأساسية لموقع ونظام زمالة المدمنين المجهولين في مصر. تم بناء المنصة باستخدام إطار عمل Laravel وتوفر موقعاً عاماً للجمهور، ولوحة تحكم إدارية، وواجهة برمجة تطبيقات (API) لتكامل تطبيقات الهاتف المحمول.

## 1. الموقع العام (الواجهة الأمامية)
- **واجهة ثنائية اللغة:** دعم كامل للغتين الإنجليزية والعربية، بما في ذلك التخطيط من اليمين إلى اليسار (RTL) للغة العربية.
- **الباحث عن الاجتماعات:** ميزة بحث متقدمة للعثور على اجتماعات الزمالة حسب المدينة أو الحي أو اسم المجموعة.
- **تصدير الاجتماعات:** إمكانية للمستخدمين لتصدير قائمة الاجتماعات إلى ملفات بصيغة PDF أو CSV.
- **تقويم الفعاليات:** صفحة عامة تعرض الفعاليات وورش العمل القادمة للزمالة.
- **صفحات المعلومات:** أقسام مخصصة للأدبيات، وتسجيلات المتحدثين، والأسئلة الشائعة، ومعلومات للجمهور.
- **معلومات اللجان:** يعرض تفاصيل حول لجان الخدمة المختلفة.
- **استطلاعات ونماذج:** يتضمن استبيان تنمية الزمالة (FD) ونموذج "اتصل بنا" للاستفسارات العامة.
- **إحصائيات حية:** تعرض الصفحة الرئيسية إحصائيات في الوقت الفعلي تشمل عدد الاجتماعات الأسبوعية، والمجموعات النشطة، والمحافظات المغطاة.

## 2. لوحة التحكم الإدارية (لوحة المشرفين)
- **مصادقة آمنة:** تسجيل دخول متكامل مع Microsoft Azure Active Directory (Azure AD) لتوفير أمان عالي.
- **التحكم في الوصول بناءً على الأدوار (RBAC):** إدارة شاملة للمستخدمين، والأدوار، والصلاحيات التفصيلية.
- **إدارة جغرافية:** إضافة وإدارة بيانات المدن (المحافظات) والأحياء.
- **الهيكل التنظيمي:** أدوات لإدارة المجموعات، وهيئات الخدمة، ولجان الخدمة.
- **إدارة الاجتماعات:** قدرات تحكم كاملة (إنشاء، قراءة، تحديث، حذف) لإدارة جداول الاجتماعات ومواقعها والبيانات المرتبطة بها.
- **تقارير اللجان:** نظام لتقديم وإدارة وتصدير (إلى PDF) وإرسال تقارير اللجان عبر البريد الإلكتروني.
- **تتبع مالي:** إمكانية تتبع وإدارة المعاملات المالية للمنظمة.
- **إدارة الفعاليات:** تقويم سنوي تفاعلي مدعوم بتقنية Livewire لإضافة وإدارة الفعاليات التنظيمية.
- **إدارة المواضيع:** إدارة المواضيع أو العلامات (Topics) المتعلقة بالنقاشات.

## 3. واجهة برمجة تطبيقات الهاتف المحمول (API)
- **واجهة برمجة تطبيقات شاملة (REST API):** توفير نقاط اتصال (Endpoints) لجميع النماذج الرئيسية (الاجتماعات، المجموعات، المدن، الفعاليات، التقارير، وغيرها).
- **المصادقة:** أمان يعتمد على الرموز (Tokens) باستخدام Laravel Sanctum لتطبيقات الهاتف.
- **دعم الاستخدام شبه المتصل (Semi-Online):** مصمم لتوفير البيانات اللازمة لتطبيقات الهاتف التي يمكن أن تعمل بشكل متقطع بدون إنترنت.
