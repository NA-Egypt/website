// Glossary & Robert's Rules Data for NA Egypt RSC Guidelines 2025

const GLOSSARY_DATA = [
  { sn: 1, acronym: "NAWS", en: "Narcotics Anonymous World Services", ar: "الخدمات العالمية للمدمنين المجهولين" },
  { sn: 2, acronym: "WSO", en: "World Service Office", ar: "مكتب الخدمة العالمي" },
  { sn: 3, acronym: "WSC", en: "World Service Conference", ar: "مؤتمر الخدمة العالمي" },
  { sn: 4, acronym: "WC", en: "World Convention", ar: "التجمع العالمي / المؤتمر العالمي" },
  { sn: 5, acronym: "WB", en: "World Board", ar: "مجلس إدارة الخدمة العالمي" },
  { sn: 6, acronym: "EDM", en: "European Delegates Meeting", ar: "إجتماع مندوبي أوروبا" },
  { sn: 7, acronym: "GSR", en: "Group Service Representative", ar: "ممثل خدمة المجموعة" },
  { sn: 8, acronym: "GSR-Alt", en: "Group Service Representative alternate", ar: "ممثل خدمة المجموعة المناوب" },
  { sn: 9, acronym: "RCM", en: "Regional Committee Member", ar: "عضو لجنة الإقليم" },
  { sn: 10, acronym: "RD", en: "Regional Delegate", ar: "مندوب الإقليم" },
  { sn: 11, acronym: "RD-Alt", en: "Regional delegate alternate", ar: "مندوب الإقليم المناوب" },
  { sn: 12, acronym: "CAR", en: "Conference Agenda Report", ar: "تقرير أجندة مؤتمر الخدمة العالمي" },
  { sn: 13, acronym: "CAT", en: "Conference Approved Track", ar: "مسار مقترحات المؤتمر الموافق عليها" },
  { sn: 14, acronym: "Starter kit", en: "Starter kit", ar: "مجموعة البداية: الكتب والكتيبات والقراءات والميداليات التي توفرها لجنة خدمة المنطقة للمجموعات الجديدة" },
  { sn: 15, acronym: "PR/PI", en: "Public Relations/Public information", ar: "العلاقات العامة / المعلومات العامة" },
  { sn: 16, acronym: "H&I", en: "Hospitals and Institutions", ar: "المستشفيات والمؤسسات" },
  { sn: 17, acronym: "IP", en: "Information Pamphlet", ar: "كتيب معلومات" },
  { sn: 18, acronym: "ASC / GSF", en: "Area Service Committee / Group Support Forum", ar: "لجنة خدمة المنطقة / منتدى دعم مجموعات" },
  { sn: 19, acronym: "Ad-Hoc subcommittee", en: "Ad-Hoc subcommittee", ar: "لجنة فرعية مؤقتة يتم تكوينها لهدف معين وينتهي عملها فور الانتهاء من المهمة الموكلة إليها" },
  { sn: 20, acronym: "Workgroup", en: "Workgroup", ar: "مجموعة عمل تقوم لهدف محدد ويكون أعضائه من ذوي الخبرة يختارهم رئيس لجنة خدمة المنطقة" },
  { sn: 21, acronym: "Agenda of meeting", en: "Agenda of meeting", ar: "جدول أعمال الإجتماع" },
  { sn: 22, acronym: "Minutes of meeting", en: "Minutes of meeting", ar: "محضر الإجتماع" },
  { sn: 23, acronym: "RROO", en: "Robert's rules of order", ar: "قواعد روبرت للنظام" }
];

const ROBERTS_RULES_DATA = [
  {
    m: 1,
    type: "بإنهاء الإجتماع",
    purpose: "إنهاء إجتماع اللجنة",
    interrupt: "لا",
    second: "نعم",
    debatable: "لا",
    majority: "بسيطة"
  },
  {
    m: 2,
    type: "بالتعديل",
    purpose: "تعديل جزء من الاقتراح الأساسي لغوياً",
    interrupt: "لا",
    second: "نعم",
    debatable: "نعم",
    majority: "بسيطة"
  },
  {
    m: 3,
    type: "تعديل بالتغيير",
    purpose: "تعديل اقتراح أساسي بالكامل بإعادة كتابته",
    interrupt: "لا",
    second: "نعم",
    debatable: "نعم",
    majority: "بسيطة"
  },
  {
    m: 4,
    type: "استئناف قرار الرئيس",
    purpose: "استئناف قرار اتخذه الرئيس",
    interrupt: "نعم",
    second: "نعم",
    debatable: "نعم",
    majority: "بسيطة"
  },
  {
    m: 5,
    type: "طلب معلومة",
    purpose: "طلب معلومة عن اقتراح جاري مناقشته وليس تقديم معلومة",
    interrupt: "نعم",
    second: "لا",
    debatable: "لا",
    majority: "لا يصوت عليه"
  },
  {
    m: 6,
    type: "الاقتراح الأساسي",
    purpose: "فكرة يريد عضو اللجنة وضعها موضع التنفيذ",
    interrupt: "لا",
    second: "نعم",
    debatable: "نعم",
    majority: "حسب الاقتراح"
  },
  {
    m: 7,
    type: "العودة إلى جدول الأعمال",
    purpose: "للعودة إلى جدول الأعمال عند الابتعاد عنه",
    interrupt: "نعم",
    second: "لا",
    debatable: "لا",
    majority: "لا يصوت عليه"
  },
  {
    m: 8,
    type: "نقطة نظام",
    purpose: "طلب توضيح لقواعد النظام حين يبدو أن هناك خرقاً لها",
    interrupt: "نعم",
    second: "لا",
    debatable: "لا",
    majority: "لا يصوت عليه"
  },
  {
    m: 9,
    type: "استفسار عن كيفية التعامل",
    purpose: "للاستفسار من الرئيس عن كيفية العمل وفقاً لقواعد النظام",
    interrupt: "نعم",
    second: "لا",
    debatable: "لا",
    majority: "لا يصوت عليه"
  },
  {
    m: 10,
    type: "انهاء المناقشة والتصويت مباشرة",
    purpose: "إذا طالت المناقشات أكثر من اللزوم",
    interrupt: "لا",
    second: "لا",
    debatable: "لا",
    majority: "ثلثين"
  },
  {
    m: 11,
    type: "طلب ميزة شخصية",
    purpose: "الحصول على ميزة شخصية",
    interrupt: "لو كان أمراً عاجلاً",
    second: "لا",
    debatable: "لا",
    majority: "لا يصوت عليه"
  },
  {
    m: 12,
    type: "إعادة النظر",
    purpose: "لإعادة فتح موضوع تم إقراره من قبل",
    interrupt: "لا",
    second: "نعم",
    debatable: "نعم",
    majority: "بسيطة"
  },
  {
    m: 13,
    type: "بالتحويل",
    purpose: "تحويل اقتراح إلى لجنة قائمة أو مؤقتة",
    interrupt: "لا",
    second: "نعم",
    debatable: "نعم",
    majority: "بسيطة"
  },
  {
    m: 14,
    type: "بالعدول عن التأجيل",
    purpose: "إعادة طرح اقتراح كان قد تم تأجيله",
    interrupt: "لا",
    second: "نعم",
    debatable: "لا",
    majority: "بسيطة"
  },
  {
    m: 15,
    type: "إلغاء ما يترتب على قرار",
    purpose: "إلغاء ما يترتب على قرار قد تم إتخاذه",
    interrupt: "لا",
    second: "نعم",
    debatable: "نعم",
    majority: "ثلثين"
  },
  {
    m: 16,
    type: "بالتأجيل",
    purpose: "تأجيل اقتراح إلى وقت آخر محدد",
    interrupt: "لا",
    second: "نعم",
    debatable: "لا",
    majority: "بسيطة"
  },
  {
    m: 17,
    type: "سحب اقتراح",
    purpose: "إذا أراد عضو سحب اقتراحه الجاري مناقشته",
    interrupt: "نعم",
    second: "لا",
    debatable: "لا",
    majority: "إجماع"
  }
];
