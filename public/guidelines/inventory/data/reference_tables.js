/**
 * Reference Data Tables for NA-Egypt Inventory System Manual
 * - Glossary of Terms & Codes (GLOSSARY_DATA)
 * - Keyboard Shortcuts Matrix (KEYBOARD_SHORTCUTS_DATA)
 * - Roles & Permissions Matrix (ROLES_MATRIX_DATA)
 */

const GLOSSARY_DATA = [
  {
    sn: 1,
    code: "store_quantity",
    ar: "رصيد المخزن الرئيسي (Store Stock)",
    desc: "إجمالي الوحدات السليمة المتوفرة حالياً في المخزن الرئيسي (Litstore) لجميع الأصناف."
  },
  {
    sn: 2,
    code: "lit_quantity",
    ar: "رصيد لجنة الأدبيات (Lit Stock)",
    desc: "إجمالي الوحدات المتوفرة في عهدة لجنة الأدبيات والجاهزة للبيع للمجموعات أو التوزيع على اللجان الفرعية."
  },
  {
    sn: 3,
    code: "damaged_quantity",
    ar: "المخزون التالف (Damaged Stock)",
    desc: "الوحدات المستبعدة بسبب التلف (سواء خصمت من المخزن الرئيسي أو لجنة الأدبيات) مع توثيق سبب التلف والمستخدم."
  },
  {
    sn: 4,
    code: "TR-YYYYMM-XXXX",
    ar: "إذن تحويل للأدبيات (Transfer Slip)",
    desc: "مستند رسمي يصدر عند تحويل كميات من المخزن الرئيسي إلى لجنة الأدبيات، وينتظر تأكيد الاستلام (Acknowledge)."
  },
  {
    sn: 5,
    code: "RT-YYYYMM-XXXX",
    ar: "إذن مرتجع للمخزن (Return Slip)",
    desc: "مستند رسمي يصدر عند إرجاع فائض الأدبيات من لجنة الأدبيات إلى المخزن الرئيسي."
  },
  {
    sn: 6,
    code: "IC-YYYYMM-XXXX",
    ar: "إذن صرف للجنة فرعية (Issue to Committee)",
    desc: "إذن كميات مجاني (بدون أسعار/فواتير) يصدر عند تسليم مطبوعات للجان الخدمية مثل H&I أو PR ويخصم من رصيد الأدبيات."
  },
  {
    sn: 7,
    code: "RC-YYYYMM-XXXX",
    ar: "إذن مرتجع من لجنة فرعية (Return from Committee)",
    desc: "إذن كميات يصدر عند إرجاع المطبوعات الفائضة من لجنة فرعية إلى رصيد لجنة الأدبيات."
  },
  {
    sn: 8,
    code: "wrong_receive",
    ar: "إلغاء استلام خاطئ (Cancel Wrong Receive)",
    desc: "آلية رقابية لعكس آخر عملية استلام تمت بالخطأ في المخزن الرئيسي، وتوثيقها في السجل، وفتح نافذة استلام جديد فوراً."
  },
  {
    sn: 9,
    code: "CheckStoreLocked",
    ar: "قفل المخزن أثناء الجرد (Store Lock)",
    desc: "حماية تلقائية تمنع أي إضافة أو تحويل أو خصم في المخزن الرئيسي طالما توجد جلسة جرد فعلي قيد التنفيذ."
  },
  {
    sn: 10,
    code: "Day 19 Cutoff",
    ar: "موعد إغلاق طلبات المجموعات (يوم 19)",
    desc: "يُغلق تعديل سلة المطبوعات لمندوبي المجموعات (GSR) بعد يوم 19 من كل شهر، بينما يستمر أمين الصندوق بالمراجعة حتى الاعتماد."
  }
];

const KEYBOARD_SHORTCUTS_DATA = [
  { key: "↑ / ↓ أو J / K", scope: "المخزن + الأدبيات", action: "التنقل لأعلى أو لأسفل بين صفوف جدول الأصناف مع تمييز الصف النشط باللون الأزرق." },
  { key: "Space", scope: "المخزن الرئيسي", action: "تحديد أو إلغاء تحديد الصنف الحالي (Checkbox) لتنفيذ الحركات المجمعة." },
  { key: "/", scope: "المخزن + الأدبيات", action: "الانتقال الفوري إلى خانة البحث السريع وتحديد النص للكتابة المباشرة." },
  { key: "?", scope: "المخزن + الأدبيات", action: "فتح أو إغلاق نافذة دليل اختصارات لوحة المفاتيح." },
  { key: "R", scope: "المخزن الرئيسي", action: "فتح نافذة استلام مخزون جديد (Receive) للصنف المحدد حالياً." },
  { key: "T", scope: "المخزن الرئيسي", action: "فتح نافذة تحويل مخزون إلى لجنة الأدبيات (Transfer) للصنف المحدد." },
  { key: "U", scope: "المخزن الرئيسي", action: "فتح نافذة إرجاع مخزون من الأدبيات إلى المخزن (Return) للصنف المحدد." },
  { key: "D", scope: "المخزن + الأدبيات", action: "فتح نافذة تسجيل عدد تالف مع ملاحظة سبب التلف (Damaged) للصنف المحدد." },
  { key: "W", scope: "المخزن الرئيسي", action: "فتح نافذة إلغاء آخر استلام خاطئ (Wrong Receive) وعكس الكمية وإعادة الإدخال." },
  { key: "E", scope: "المخزن الرئيسي", action: "تفعيل أو إغلاق وضع التعديل السريع المباشر (Inline Edit) للاسم والسعر والفئة." },
  { key: "M", scope: "المخزن الرئيسي", action: "فتح نافذة حركة المخزون المجمعة السريعة (Quick Batch Movement) لعدة أصناف معاً." },
  { key: "N", scope: "المخزن الرئيسي", action: "فتح نافذة إضافة صنف جديد (Add New Item) إلى دليل المخزون." },
  { key: "Enter", scope: "أثناء التعديل السريع", action: "حفظ التعديلات السريعة في الصف الحالي أو إرسال النموذج المفتوح." },
  { key: "Esc", scope: "عام", action: "إلغاء التعديل السريع، أو إغلاق النافذة المنبثقة المفتوحة، أو إلغاء تحديد الصفوف." }
];

const ROLES_MATRIX_DATA = [
  {
    role: "مدير المخزن (Store Manager)",
    permission: "manage store",
    screens: "مخزون المستودع (/store)، تقارير المستودع (/store/reports)، الجرد الفعلي (/store/stocktaking)، الأذونات (/slips)، الدفتر الشهري (/lit/ledger)",
    responsibilities: "إضافة الأصناف، استلام الوارد، التحويل للجنة الأدبيات، تسجيل التالف، إلغاء الاستلام الخاطئ، وإجراء الجرد الفعلي."
  },
  {
    role: "مسؤول لجنة الأدبيات (Lit User)",
    permission: "view lit inventory / acknowledge inventory slips / view lit ledger",
    screens: "مخزون المطبوعات (/lit)، الأذونات (/slips)، التسوية والإرجاع (/lit/reconciliation)، الدفتر الشهري (/lit/ledger)، طلبات المجموعات (/literature-requests/committee)، طلبات اللجان (/committee-literature)",
    responsibilities: "تأكيد استلام التحويلات من المخزن، صرف وتعديل طلبات المجموعات واللجان، تسجيل تالف الأدبيات، وإرجاع الفائض للمخزن."
  },
  {
    role: "مندوب المجموعة (GSR)",
    permission: "Group Owner (user_id)",
    screens: "سلة طلبات المطبوعات (/literature-requests/cart)، أرشيف الطلبات (/literature-requests/archive)",
    responsibilities: "اختيار المطبوعات والعملات المطلوبة للمجموعة شهرياً وإرسال الطلب لأمين الصندوق قبل يوم 19 في الشهر."
  },
  {
    role: "أمين صندوق الهيئة الخدمية (Treasurer / ServiceBody)",
    permission: "Role: Treasurer | ServiceBody",
    screens: "لوحة أمين الصندوق (/literature-requests/treasurer)، سلة المجموعات التابعة، أرشيف الفواتير",
    responsibilities: "مراجعة طلبات المجموعات التابعة للمنطقة، تعديلها أو فتحها/قفلها، واعتماد الفاتورة المجمعة وإرسالها للجنة الأدبيات."
  },
  {
    role: "رئيس / مسؤول لجنة فرعية (Service Committee)",
    permission: "Role: Committees",
    screens: "بوابة مطبوعات اللجان (/committee-literature)، طلب صرف جديد (/committee-literature/create)، إرجاع فائض (/committee-literature/{id}/return)",
    responsibilities: "طلب مطبوعات مجانية لأنشطة اللجنة (مثل المستشفيات والمؤسسات H&I أو العلاقات العامة PR)، تأكيد استلام إذن الصرف، وإرجاع المتبقي."
  },
  {
    role: "لجنة خدمة الإقليم (RSC) / المشرف العام (Super Admin)",
    permission: "Role: rsc | super admin",
    screens: "الدفتر الشهري الموحد (/lit/ledger)، أذونات المخزون (/slips)، أرشيف الفواتير، وكافة شاشات الرقابة",
    responsibilities: "الرقابة المالية والإدارية الشاملة، متابعة التقييم المالي للمخزون والمبيعات، ومراجعة تقارير الجرد والفروقات."
  }
];

