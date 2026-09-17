<?php

return [
    'categories' => [
        'agenda' => [
            'title' => 'جداول أعمال هيئات الخدمة',
            'icon' => 'bi-journals',
        ],
        'store' => [
            'title' => 'المخزن والمطبوعات',
            'icon' => 'bi-box-seam',
        ],
        'calendar' => [
            'title' => 'التقويم والفعاليات',
            'icon' => 'bi-calendar-check',
        ],
        'forms' => [
            'title' => 'النماذج المخصصة',
            'icon' => 'bi-ui-checks',
        ],
        'system' => [
            'title' => 'النظام والأدوار القديمة',
            'icon' => 'bi-shield-lock',
        ],
        'general' => [
            'title' => 'صلاحيات عامة وأخرى',
            'icon' => 'bi-gear-fill',
        ],
    ],

    'items' => [
        // Service Body Agendas
        'create sb agenda' => [
            'label' => 'إنشاء جداول أعمال هيئة الخدمة',
            'description' => 'إنشاء وصياغة جداول أعمال الاجتماعات وتقديمها لهيئة الخدمة التابع لها.',
            'category' => 'agenda',
        ],
        'edit sb agenda' => [
            'label' => 'تعديل جداول أعمال هيئة الخدمة',
            'description' => 'تعديل مسودات جداول الأعمال الحالية قبل اعتمادها رسمياً من الميسر.',
            'category' => 'agenda',
        ],
        'approve sb agenda' => [
            'label' => 'اعتماد جداول أعمال هيئة الخدمة',
            'description' => 'مراجعة واعتماد ونشر جداول الأعمال المقدمة لتصبح رسمية ومعلنة.',
            'category' => 'agenda',
        ],
        'delete sb agenda' => [
            'label' => 'حذف جداول أعمال هيئة الخدمة',
            'description' => 'حذف مسودات جداول الأعمال أو الأجندات غير المطلوبة من أرشيف الهيئة.',
            'category' => 'agenda',
        ],

        // Store & Literature
        'manage store' => [
            'label' => 'إدارة مخزن المطبوعات',
            'description' => 'التحكم في بيانات ومحتويات المخزن وتعديل الأسعار والإشراف الكامل عليه.',
            'category' => 'store',
        ],
        'view lit inventory' => [
            'label' => 'عرض مخزون المطبوعات',
            'description' => 'الاطلاع الفوري على أرصدة وكميات المطبوعات المتوفرة في المخزن.',
            'category' => 'store',
        ],
        'view inventory slips' => [
            'label' => 'عرض أذونات المخزن',
            'description' => 'استعراض أذونات الصرف والإضافة والتسليم الخاصة بحركة المخزون.',
            'category' => 'store',
        ],
        'acknowledge inventory slips' => [
            'label' => 'تأكيد واستلام أذونات المخزن',
            'description' => 'تأكيد الاستلام الفعلي أو تسليم الأذونات والطلبيات المنصرفة من المخزن.',
            'category' => 'store',
        ],
        'view lit ledger' => [
            'label' => 'عرض دفتر أستاذ المطبوعات',
            'description' => 'متابعة سجل الحركات المالية والمخزنية التفصيلية لعهد المطبوعات.',
            'category' => 'store',
        ],
        'manage literature requests' => [
            'label' => 'إدارة طلبات المطبوعات',
            'description' => 'معالجة وتنفيذ طلبات المطبوعات المقدمة من اللجان والمجموعات.',
            'category' => 'store',
        ],
        'approve literature requests' => [
            'label' => 'اعتماد طلبات المطبوعات',
            'description' => 'الموافقة على طلبات المطبوعات وصرفها رسمياً قبل إخراجها من المخزن.',
            'category' => 'store',
        ],
        'edit literature requests' => [
            'label' => 'تعديل طلبات المطبوعات',
            'description' => 'تعديل الكميات والبنود المدرجة في طلبات المطبوعات قيد المعالجة.',
            'category' => 'store',
        ],

        // Calendar & Events
        'can_manage_calendar' => [
            'label' => 'إدارة التقويم العام',
            'description' => 'صلاحية شاملة لإدارة كافة مواعيد فعاليات وأحداث الزمالة في التقويم.',
            'category' => 'calendar',
        ],
        'create calendar events' => [
            'label' => 'إنشاء فعاليات التقويم',
            'description' => 'إضافة وجدولة فعاليات ومناسبات جديدة على مستوى المنطقة أو اللجان.',
            'category' => 'calendar',
        ],
        'manage calendar events' => [
            'label' => 'إدارة فعاليات التقويم',
            'description' => 'تعديل الفعاليات المجدولة أو تغيير مواعيدها أو إلغاؤها.',
            'category' => 'calendar',
        ],

        // Custom Forms
        'manage own forms' => [
            'label' => 'إدارة النماذج المخصصة الخاصة',
            'description' => 'إنشاء وتعديل واستعراض الردود على النماذج الخاصة باللجنة التابع لها.',
            'category' => 'forms',
        ],
        'manage helpline' => [
            'label' => 'إدارة خطوط المساعدة',
            'description' => 'عرض وتصدير استجابات مكالمات خط المساعدة، وإدارة قائمة المتطوعين ومزامنة التقارير.',
            'category' => 'forms',
        ],

        // Legacy / System Role-Named Permissions
        'super admin' => [
            'label' => 'صلاحية المشرف العام (قديم)',
            'description' => 'صلاحية قديمة من النظام السابق تمنح تحكماً إدارياً كاملاً بدون قيود.',
            'category' => 'system',
        ],
        'rsc' => [
            'label' => 'صلاحية لجنة الخدمة الإقليمية RSC (قديم)',
            'description' => 'صلاحية قديمة تمثل النفاذ الإداري لأعضاء لجنة الخدمة الإقليمية.',
            'category' => 'system',
        ],
        'Committees' => [
            'label' => 'صلاحية لجان الخدمة (قديم)',
            'description' => 'صلاحية قديمة لتمييز منسقي وأعضاء لجان الخدمة الفرعية.',
            'category' => 'system',
        ],
        'store' => [
            'label' => 'صلاحية مسؤول المخزن (قديم)',
            'description' => 'صلاحية قديمة لإدارة عمليات مخزن المطبوعات.',
            'category' => 'system',
        ],
        'gsr' => [
            'label' => 'صلاحية ممثل المجموعة GSR (قديم)',
            'description' => 'صلاحية قديمة لممثلي المجموعات في اللجان الخدمية.',
            'category' => 'system',
        ],
        'RCM' => [
            'label' => 'صلاحية ممثل المنطقة RCM (قديم)',
            'description' => 'صلاحية قديمة لممثلي لجان خدمة المناطق.',
            'category' => 'system',
        ],
        'FAC' => [
            'label' => 'صلاحية الميسر (قديم)',
            'description' => 'صلاحية قديمة لإدارة وتيسير اجتماعات الهيئات الخدمية.',
            'category' => 'system',
        ],
        'TR' => [
            'label' => 'صلاحية أمين الصندوق (قديم)',
            'description' => 'صلاحية قديمة لمتابعة الشؤون المالية وهيئات الخدمة.',
            'category' => 'system',
        ],
        'Sec' => [
            'label' => 'صلاحية السكرتير (قديم)',
            'description' => 'صلاحية قديمة للسكرتارية وتدوين جداول الأعمال ومحاضر الاجتماعات.',
            'category' => 'system',
        ],
        'Phoneline' => [
            'label' => 'صلاحية خط المساعدة (قديم)',
            'description' => 'صلاحية قديمة لمستلمي مكالمات خط المساعدة الهاتفي.',
            'category' => 'system',
        ],
        'view api analytics' => [
            'label' => 'عرض تحليلات الـ API والتطبيق',
            'description' => 'مراقبة حركة واجهة البرمجة للتطبيقات، وسرعة الاستجابة، والأخطاء، وسجلات الطلبات.',
            'category' => 'system',
        ],
    ],
];

