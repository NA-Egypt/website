<x-frontend.layout 
    :title="__('messages.literature_page_title')" 
    :description="__('messages.literature_hero_subtitle')">

    @php
        $isArabic = app()->getLocale() === 'ar';
        $pageDir = $isArabic ? 'rtl' : 'ltr';

        $literatureData = [
            // --- BOOKS & BOOKLETS ---
            [
                'id' => 'basic-text',
                'category' => 'books',
                'badge' => $isArabic ? 'النص الأساسي' : 'Basic Text',
                'badge_class' => 'badge-book',
                'title' => __('messages.basic_text_title'),
                'description' => __('messages.basic_text_desc'),
                'type' => 'link',
                'file' => 'https://na.org/wp-content/uploads/2025/04/BT-Audio-Arabic.zip',
                'stream_link' => 'https://soundcloud.com/user-197598456/sets/8161e314-b4a0-417f-97a2-092b3005efb3',
                'icon' => 'bi-book-half',
                'is_flagship' => true,
            ],
            [
                'id' => 'white-booklet',
                'category' => 'books',
                'badge' => $isArabic ? 'كتيب' : 'Booklet',
                'badge_class' => 'badge-book',
                'title' => __('messages.WhiteBooklet'),
                'description' => __('messages.white_booklet_desc'),
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR1500_LWB-White-Booklet-Arabic.pdf',
                'icon' => 'bi-journal-bookmark-fill',
                'is_flagship' => true,
            ],
            [
                'id' => 'intro-guide',
                'category' => 'books',
                'badge' => $isArabic ? 'دليل' : 'Guide',
                'badge_class' => 'badge-book',
                'title' => __('messages.introductoryGuide'),
                'description' => $isArabic ? 'دليل تمهيدي لفهم برنامج زمالة المدمنين المجهولين وطريقة عمله.' : 'An introductory overview to understanding the NA recovery program.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/12/An-Introductory-Guide-to-NA-Arabic.pdf',
                'icon' => 'bi-journal-text',
                'is_flagship' => false,
            ],

            // --- AUDIO LITERATURE ---
            [
                'id' => 'audio-ip11',
                'category' => 'audio',
                'badge' => 'IP #11 ' . ($isArabic ? 'صوتي' : 'Audio'),
                'badge_class' => 'badge-audio',
                'title' => __('messages.AudioSponsorship'),
                'description' => $isArabic ? 'تسجيل صوتي كامل لنشرة التوجيه باللغة العربية.' : 'Complete Arabic audio recording for the Sponsorship pamphlet.',
                'type' => 'audio',
                'file' => asset('literature/_IP11_Sponsorship_Arabic.aac'),
                'icon' => 'bi-headphones',
                'is_flagship' => false,
            ],
            [
                'id' => 'audio-ip7',
                'category' => 'audio',
                'badge' => 'IP #7 ' . ($isArabic ? 'صوتي' : 'Audio'),
                'badge_class' => 'badge-audio',
                'title' => __('messages.AudioAmIanAddict'),
                'description' => $isArabic ? 'تسجيل صوتي كامل لنشرة هل أنا مدمن؟ باللغة العربية.' : 'Complete Arabic audio recording for Am I an Addict? pamphlet.',
                'type' => 'audio',
                'file' => asset('literature/_IP7_Am_I_an_addict_Arabic.aac'),
                'icon' => 'bi-headphones',
                'is_flagship' => false,
            ],
            [
                'id' => 'audio-ip16',
                'category' => 'audio',
                'badge' => 'IP #16 ' . ($isArabic ? 'صوتي' : 'Audio'),
                'badge_class' => 'badge-audio',
                'title' => __('messages.AudioForthenewcomer'),
                'description' => $isArabic ? 'تسجيل صوتي كامل لنشرة للعضو الجديد باللغة العربية.' : 'Complete Arabic audio recording for the Newcomer pamphlet.',
                'type' => 'audio',
                'file' => asset('literature/_IP16_For_the_new_comer_Arabic.aac'),
                'icon' => 'bi-headphones',
                'is_flagship' => false,
            ],
            [
                'id' => 'audio-ip22',
                'category' => 'audio',
                'badge' => 'IP #22 ' . ($isArabic ? 'صوتي' : 'Audio'),
                'badge_class' => 'badge-audio',
                'title' => __('messages.AudioWelcometoNA'),
                'description' => $isArabic ? 'تسجيل صوتي كامل لنشرة مرحباً بك في زمالة المدمنين المجهولين.' : 'Complete Arabic audio recording for Welcome to NA pamphlet.',
                'type' => 'audio',
                'file' => asset('literature/_IP22_Welcome_to_NA_Arabic.aac'),
                'icon' => 'bi-headphones',
                'is_flagship' => false,
            ],

            // --- RECOVERY PAMPHLETS (IPs) ---
            [
                'id' => 'ip-1',
                'category' => 'ips',
                'badge' => 'IP #1',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP1'),
                'description' => $isArabic ? 'من هو المدمن، ما هو البرنامج، كيف يعمل ولماذا نلتزم به.' : 'Who is an addict, what is the program, how it works and why.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR3101_2015-IP-1-Arabic.pdf',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-2',
                'category' => 'ips',
                'badge' => 'IP #2',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP2'),
                'description' => $isArabic ? 'كيف تبدأ المجموعة وتحافظ على جو التعافي ورسالتها الأساسية.' : 'Understanding the NA group and preserving the atmosphere of recovery.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR3102-IP-2-Arabic.pdf',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-5',
                'category' => 'ips',
                'badge' => 'IP #5',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP5'),
                'description' => $isArabic ? 'نظرة واقعية أخرى على مرض الإدمان والتعافي منه.' : 'Another perspective on the disease of addiction and recovery.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR3105-IP-5-Arabic.pdf',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-6',
                'category' => 'ips',
                'badge' => 'IP #6',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP6'),
                'description' => $isArabic ? 'فهم ديناميكية التعافي وكيفية الوقاية من الانتكاسة والتعامل معها.' : 'Understanding recovery dynamics, relapse triggers and prevention.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/09/AR3106-IP-6-Arabic.pdf',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-7',
                'category' => 'ips',
                'badge' => 'IP #7',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP7'),
                'description' => $isArabic ? 'أسئلة موجهة تساعدك على استكشاف مشكلتك مع التعاطي.' : 'Self-assessment questions to help identify if you have a problem with drugs.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR3107-IP-7-Arabic.pdf',
                'has_audio' => true,
                'audio_id' => 'audio-ip7',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-8',
                'category' => 'ips',
                'badge' => 'IP #8',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP8'),
                'description' => $isArabic ? 'المبادئ الإرشادية للعيش يوماً بيوم في التعافي.' : 'Key daily principles for living cleanly one day at a time.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/09/AR3108-IP-8-Arabic.pdf',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-9',
                'category' => 'ips',
                'badge' => 'IP #9',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP9'),
                'description' => $isArabic ? 'تطبيق خطوات ومبادئ البرنامج في شتى مجالات الحياة اليومية.' : 'Applying program principles in all areas of daily life.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR3109-IP-9-Arabic.pdf',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-11',
                'category' => 'ips',
                'badge' => 'IP #11',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP11'),
                'description' => $isArabic ? 'أهمية الموجه والموجه إليه وكيف تبني علاقة توجيه ناجحة.' : 'The role of sponsorship in NA recovery and how it works.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR3111-IP-11-Arabic.pdf',
                'has_audio' => true,
                'audio_id' => 'audio-ip11',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-12',
                'category' => 'ips',
                'badge' => 'IP #12',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP12'),
                'description' => $isArabic ? 'تحليل الاستياء والشعور بالذنب والخوف في حلقة الهاجس الذاتي.' : 'Understanding resentment, guilt, and fear in the self-obsession cycle.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR3112-IP-12-Arabic.pdf',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-13',
                'category' => 'ips',
                'badge' => 'IP #13',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP13'),
                'description' => $isArabic ? 'تجارب ورسائل موجهة من المدمنين الشباب إلى أقرانهم في الزمالة.' : 'Experiences and insights written by young addicts for young addicts.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR3113-IP-13-Arabic.pdf',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-14',
                'category' => 'ips',
                'badge' => 'IP #14',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP14'),
                'description' => $isArabic ? 'تجربة حقيقية لمدمن تعكس التقبل والإيمان والالتزام بالبرنامج.' : 'A personal journey demonstrating acceptance, faith, and commitment.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR3114-IP-14-Arabic.pdf',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-16',
                'category' => 'ips',
                'badge' => 'IP #16',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP16'),
                'description' => $isArabic ? 'رسالة ترحيبية ودليل مبسط لكل من يحضر اجتماعه الأول.' : 'Welcoming guidance for newcomers attending their first meetings.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR3116-IP-16-Arabic.pdf',
                'has_audio' => true,
                'audio_id' => 'audio-ip16',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-19',
                'category' => 'ips',
                'badge' => 'IP #19',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP19'),
                'description' => $isArabic ? 'رحلة التسامح مع النفس وتطوير التقبل الذاتي خلال التعافي.' : 'Finding self-worth, healing, and self-acceptance in recovery.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR3119-IP-19-Arabic.pdf',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-21',
                'category' => 'ips',
                'badge' => 'IP #21',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP21'),
                'description' => $isArabic ? 'إرشادات للأعضاء الذين يعيشون في مناطق نائية أو بعيدة عن الاجتماعات.' : 'Guidance for members staying clean in isolated or remote areas.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR3121-IP-21-Arabic.pdf',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-22',
                'category' => 'ips',
                'badge' => 'IP #22',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP22'),
                'description' => $isArabic ? 'مقدمة شاملة عن زمالة المدمنين المجهولين وما تقدمه لكل مدمن.' : 'A broad introduction welcoming anyone seeking recovery from addiction.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR3122-IP-22-Arabic.pdf',
                'has_audio' => true,
                'audio_id' => 'audio-ip22',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-23',
                'category' => 'ips',
                'badge' => 'IP #23',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP23'),
                'description' => $isArabic ? 'إرشادات للأعضاء الخارجين من المصحات والمؤسسات العلاجية.' : 'Support for members transitioning from treatment institutions into the rooms.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR3123-IP-23-Arabic.pdf',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-24',
                'category' => 'ips',
                'badge' => 'IP #24',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP24'),
                'description' => $isArabic ? 'مفهوم الدعم الذاتي المالي والتقاليد المرتبطة بتمويل الزمالة.' : 'Financial self-support principles and the Seventh Tradition.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR3124-IP-24-Arabic.pdf',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-26',
                'category' => 'ips',
                'badge' => 'IP #26',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP26'),
                'description' => $isArabic ? 'تسهيل وصول ومشاركة الأعضاء ذوي الاحتياجات الخاصة والإضافية.' : 'Ensuring NA meetings and service are accessible for members with additional needs.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR3126-IP-26-Arabic.pdf',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-27',
                'category' => 'ips',
                'badge' => 'IP #27',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP27'),
                'description' => $isArabic ? 'معلومات ورسائل توعوية لأولياء أمور وعائلات المدمنين الشباب.' : 'Information and reassurance for parents and guardians of young addicts.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR3127-IP-27-Arabic.pdf',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-28',
                'category' => 'ips',
                'badge' => 'IP #28',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP28'),
                'description' => $isArabic ? 'كيف تساهم مجموعات الزمالة في تمويل الخدمات المحلية والعالمية.' : 'Funding our service structure from group contributions to global services.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/11/AR3128_2024.pdf',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],
            [
                'id' => 'ip-29',
                'category' => 'ips',
                'badge' => 'IP #29',
                'badge_class' => 'badge-ip',
                'title' => __('messages.IP29'),
                'description' => $isArabic ? 'دليل إرشادي يوضح شكل الاجتماعات وطبيعتها لأول مرة.' : 'An introductory guide clarifying what happens in NA recovery meetings.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/11/AR3129_2024.pdf%20',
                'icon' => 'bi-file-earmark-pdf-fill',
            ],

            // --- GROUP READINGS ---
            [
                'id' => 'group-readings',
                'category' => 'group_readings',
                'badge' => $isArabic ? 'قراءات الاجتماع' : 'Readings',
                'badge_class' => 'badge-group',
                'title' => __('messages.groupreadings'),
                'description' => $isArabic ? 'كروت قراءات افتتاح وختام الاجتماعات المعتمدة باللغة العربية.' : 'Official Arabic group reading cards for meeting openings and closings.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/05/AR_GRC_2015-Group-Reading-Cards-Arabic.pdf',
                'icon' => 'bi-collection-play-fill',
            ],

            // --- SERVICE & GUIDELINES ---
            [
                'id' => 'service-trusted-servants',
                'category' => 'service',
                'badge' => $isArabic ? 'خدمة' : 'Service',
                'badge_class' => 'badge-service',
                'title' => __('messages.IPGroupTrustedServants'),
                'description' => $isArabic ? 'إرشادات وشروط الخدمة ومسؤوليات الخدم الموثوق بهم في المجموعات.' : 'Guidelines and qualifications for group trusted servant roles.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/06/AR2203-Group-Trusted-Servants-Arabic.pdf',
                'icon' => 'bi-people-fill',
            ],
            [
                'id' => 'service-business-meetings',
                'category' => 'service',
                'badge' => $isArabic ? 'خدمة' : 'Service',
                'badge_class' => 'badge-service',
                'title' => __('messages.IPGroupBusinessMeetings'),
                'description' => $isArabic ? 'كيفية تنظيم وإدارة اجتماعات العمل الإدارية والخدمية للمجموعة.' : 'How to organize and conduct effective group business meetings.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/06/AR2202-GroupBbuisness-Meetings-Arabic.pdf',
                'icon' => 'bi-briefcase-fill',
            ],
            [
                'id' => 'service-disruptive-behavior',
                'category' => 'service',
                'badge' => $isArabic ? 'خدمة' : 'Service',
                'badge_class' => 'badge-service',
                'title' => __('messages.IPDisruptiveAndViolentBehavior'),
                'description' => $isArabic ? 'التعامل مع السلوكيات غير المناسبة لضمان أمان جو التعافي بالاجتماعات.' : 'Safeguarding the recovery atmosphere from disruptive or violent behavior.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/06/AR2204-Disruptive-and-Violent-Behavior-Arabic.pdf',
                'icon' => 'bi-shield-shaded',
            ],
            [
                'id' => 'service-phoneline-volunteer',
                'category' => 'service',
                'badge' => $isArabic ? 'دليل متطوع' : 'Handbook',
                'badge_class' => 'badge-service',
                'title' => __('messages.phonelinedoc'),
                'description' => $isArabic ? 'دليل إرشادي شامل لمتطوعي الرد على خطوط المساعدة الهاتفية.' : 'Comprehensive handbook for helpline volunteer responders.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2025/08/%D8%AF%D9%84%D9%8A%D9%84-%D8%A7%D8%B3%D8%AA%D8%AE%D8%AF%D8%A7%D9%85-%D9%85%D8%AA%D8%B7%D9%88%D8%B9-%D8%A7%D9%84%D8%B1%D8%AF-%D8%B9%D9%84%D9%89-%D8%AE%D8%B7-%D8%A7%D9%84%D9%87%D8%A7%D8%AA%D9%81-.pdf',
                'icon' => 'bi-telephone-inbound-fill',
            ],
            [
                'id' => 'service-phoneline-gsr',
                'category' => 'service',
                'badge' => $isArabic ? 'إرشادات' : 'Guidelines',
                'badge_class' => 'badge-service',
                'title' => __('messages.phonelinegsrguide'),
                'description' => $isArabic ? 'إرشادات وتوجيهات استقبال المكالمات لممثلي خدمة المجموعات.' : 'Guidelines for GSRs when handling inquiries and newcomer calls.',
                'type' => 'pdf',
                'file' => asset('literature/receiving_phone_call_guide.pdf'),
                'icon' => 'bi-headset',
            ],
            [
                'id' => 'service-replacement-meds',
                'category' => 'service',
                'badge' => $isArabic ? 'نشرة توعوية' : 'Pamphlet',
                'badge_class' => 'badge-service',
                'title' => __('messages.replacementdubs'),
                'description' => $isArabic ? 'نشرة توضيحية حول الامتناع التام واستخدام العقاقير البديلة واستبدال المخدر.' : 'Clarification pamphlet regarding medication-assisted treatment and total abstinence.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2025/08/%D9%86%D8%B4%D8%B1%D8%A9-%D8%A7%D9%84%D8%A7%D9%85%D8%AA%D9%86%D8%A7%D8%B9-%D8%A8%D8%A7%D9%84%D8%B9%D9%82%D8%A7%D9%82%D9%8A%D8%B1-%D8%A7%D9%84%D8%A8%D8%AF%D9%8A%D9%84%D8%A9-%D9%84%D9%84%D9%85%D8%AE%D8%AF%D8%B1%D8%A7%D8%AA-%D9%88-%D8%A7%D8%B3%D8%AA%D8%A8%D8%AF%D8%A7%D9%84-%D8%A7%D9%84%D9%85%D8%AE%D8%AF%D8%B1.pdf',
                'icon' => 'bi-capsule',
            ],
            [
                'id' => 'service-pr-facebook',
                'category' => 'service',
                'badge' => $isArabic ? 'علاقات عامة' : 'PR',
                'badge_class' => 'badge-service',
                'title' => __('messages.EgyptPRCommitteeFacebookPage'),
                'description' => $isArabic ? 'السياسات والخطوط الإرشادية لصفحة لجنة العلاقات العامة على فيسبوك.' : 'Social media and public relations guidelines for the PR Facebook page.',
                'type' => 'pdf',
                'file' => 'https://na.org/wp-content/uploads/2024/06/Egypt-PR-Committee-Facebook-page-SP.pdf',
                'icon' => 'bi-facebook',
            ],
            [
                'id' => 'service-survey',
                'category' => 'service',
                'badge' => $isArabic ? 'استبيان' : 'Survey',
                'badge_class' => 'badge-service',
                'title' => __('messages.membershipSurvey'),
                'description' => $isArabic ? 'نتائج وإحصائيات استبيان عضوية زمالة المدمنين المجهولين بمصر 2025.' : 'Official statistics and findings from the NA Egypt 2025 Membership Survey.',
                'type' => 'pdf',
                'file' => asset('literature/membership_survey.pdf'),
                'icon' => 'bi-bar-chart-line-fill',
            ],
        ];
    @endphp

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" integrity="sha384-3zSEDfvllQohrq0PHL1fOXJuC/jSOO34H46t6UQfobFOmxE5BpjjaIJY5F2/bMnU" crossorigin="anonymous"></script>

    <div class="literature-hub-wrapper" dir="{{ $pageDir }}">

        <!-- Hero Header Showcase -->
        <section class="literature-hero text-center mb-4">
            <div class="container-fluid px-0">
                <div class="hero-badge d-inline-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-book"></i>
                    <span>{{ __('messages.Literature') }}</span>
                </div>
                <h1 class="hero-title fw-bold mb-2">{{ __('messages.literature_page_title') }}</h1>
                <p class="hero-subtitle text-muted mx-auto mb-4">{{ __('messages.literature_hero_subtitle') }}</p>

                <!-- Flagship Showcase Cards -->
                <div class="row g-3 g-lg-4 mb-2 text-start justify-content-center">
                    
                    <!-- Basic Text Hero Card -->
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="flagship-card card-basic-text h-100 p-3 p-md-4 rounded-4 shadow-sm border-0 position-relative overflow-hidden">
                            <div class="flagship-decor"></div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="badge bg-primary text-white px-3 py-2 rounded-pill fw-semibold">
                                    <i class="bi bi-star-fill me-1 text-warning"></i> {{ $isArabic ? 'النص الأساسي' : 'Basic Text' }}
                                </span>
                                <span class="flagship-icon text-primary"><i class="bi bi-book-half"></i></span>
                            </div>
                            <h2 class="h5 fw-bold text-dark mb-2">{{ __('messages.basic_text_title') }}</h2>
                            <p class="text-muted small mb-3 mb-md-4 flagship-desc">{{ __('messages.basic_text_desc') }}</p>
                            <div class="d-flex flex-wrap gap-2 mt-auto">
                                <a href="https://soundcloud.com/user-197598456/sets/8161e314-b4a0-417f-97a2-092b3005efb3" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-soundwave"></i>
                                    <span>{{ __('messages.listen_soundcloud') }}</span>
                                    <i class="bi bi-box-arrow-up-right small"></i>
                                </a>
                                <a href="https://na.org/wp-content/uploads/2025/04/BT-Audio-Arabic.zip" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-download"></i>
                                    <span>{{ __('messages.download_audio_zip') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- White Booklet Hero Card -->
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="flagship-card card-white-booklet h-100 p-3 p-md-4 rounded-4 shadow-sm border-0 position-relative overflow-hidden">
                            <div class="flagship-decor"></div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="badge bg-teal text-white px-3 py-2 rounded-pill fw-semibold">
                                    <i class="bi bi-bookmark-star-fill me-1"></i> {{ $isArabic ? 'الكتيب الأبيض' : 'White Booklet' }}
                                </span>
                                <span class="flagship-icon text-teal"><i class="bi bi-journal-bookmark-fill"></i></span>
                            </div>
                            <h2 class="h5 fw-bold text-dark mb-2">{{ __('messages.WhiteBooklet') }}</h2>
                            <p class="text-muted small mb-3 mb-md-4 flagship-desc">{{ __('messages.white_booklet_desc') }}</p>
                            <div class="d-flex flex-wrap gap-2 mt-auto">
                                <a href="https://na.org/wp-content/uploads/2024/05/AR1500_LWB-White-Booklet-Arabic.pdf" target="_blank" rel="noopener noreferrer" class="btn btn-teal text-white btn-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                    <span>{{ __('messages.read_online') }} (PDF)</span>
                                    <i class="bi bi-box-arrow-up-right small"></i>
                                </a>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2" onclick="copyLiteratureLink('https://na.org/wp-content/uploads/2024/05/AR1500_LWB-White-Booklet-Arabic.pdf')">
                                    <i class="bi bi-share"></i>
                                    <span>{{ __('messages.share_link') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Just For Today (JFT) Quick Card -->
                    <div class="col-12 col-md-12 col-xl-4">
                        <div class="flagship-card card-jft h-100 p-3 p-md-4 rounded-4 shadow-sm border-0 position-relative overflow-hidden">
                            <div class="flagship-decor"></div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="badge bg-amber text-dark px-3 py-2 rounded-pill fw-semibold">
                                    <i class="bi bi-sun-fill me-1 text-warning"></i> {{ $isArabic ? 'التأمل اليومي' : 'Daily Meditation' }}
                                </span>
                                <span class="flagship-icon text-amber"><i class="bi bi-calendar-heart-fill"></i></span>
                            </div>
                            <h2 class="h5 fw-bold text-dark mb-2">{{ __('messages.jft_card_title') }}</h2>
                            <p class="text-muted small mb-3 mb-md-4 flagship-desc">{{ __('messages.jft_card_desc') }}</p>
                            <div class="d-flex flex-wrap gap-2 mt-auto">
                                <a href="{{ route('frontend.home') }}#jft" class="btn btn-amber text-dark fw-semibold btn-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 shadow-xs">
                                    <i class="bi bi-book-half"></i>
                                    <span>{{ __('messages.jft_read_today') }}</span>
                                    <i class="bi bi-arrow-left-short rtl-flip fs-5"></i>
                                </a>
                                <a href="https://na.org/wp-content/uploads/2024/09/AR3108-IP-8-Arabic.pdf" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-file-text"></i>
                                    <span>IP #8 (PDF)</span>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Filter & Search Controls Bar -->
        <section class="controls-section sticky-controls mb-4 p-3 p-md-4 rounded-4 shadow-sm bg-white border">
            <div class="row g-3 align-items-center">
                
                <!-- Search Input with Clear Button -->
                <div class="col-12 col-xl-4">
                    <div class="search-input-wrapper position-relative">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" 
                               id="literatureSearchInput" 
                               class="form-control literature-search-input rounded-pill" 
                               placeholder="{{ __('messages.search_literature_placeholder') }}" 
                               aria-label="Search literature"
                               autocomplete="off">
                        <button type="button" id="clearSearchBtn" class="clear-search-btn" title="Clear search" style="display: none;">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>
                </div>

                <!-- Category Filter Tabs (Pills) -->
                <div class="col-12 col-xl-8">
                    <div class="category-pills-container d-flex flex-wrap align-items-center gap-2">
                        <button type="button" class="filter-pill active" data-category="all">
                            <i class="bi bi-grid-fill"></i>
                            <span>{{ __('messages.filter_all') }}</span>
                        </button>
                        <button type="button" class="filter-pill" data-category="books">
                            <i class="bi bi-journal-bookmark"></i>
                            <span>{{ __('messages.filter_books') }}</span>
                        </button>
                        <button type="button" class="filter-pill" data-category="ips">
                            <i class="bi bi-file-earmark-text"></i>
                            <span>{{ __('messages.filter_ips') }}</span>
                        </button>
                        <button type="button" class="filter-pill" data-category="audio">
                            <i class="bi bi-headphones"></i>
                            <span>{{ __('messages.filter_audio') }}</span>
                        </button>
                        <button type="button" class="filter-pill" data-category="group_readings">
                            <i class="bi bi-collection-play"></i>
                            <span>{{ __('messages.filter_group_readings') }}</span>
                        </button>
                        <button type="button" class="filter-pill" data-category="service">
                            <i class="bi bi-people"></i>
                            <span>{{ __('messages.filter_service') }}</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Active Results Bar -->
            <div class="results-stats d-flex align-items-center justify-content-between mt-3 pt-2 border-top text-muted small">
                <div>
                    <span>{{ __('messages.search_results_count') }}</span>
                    <strong id="itemsCountBadge" class="text-primary fs-6 px-1">{{ count($literatureData) }}</strong>
                </div>
                <button type="button" id="resetFiltersBtn" class="btn btn-link btn-sm text-decoration-none p-0 text-muted" onclick="resetAllFilters()" style="display: none;">
                    <i class="bi bi-arrow-counterclockwise"></i> {{ __('messages.reset_filter') }}
                </button>
            </div>
        </section>

        <!-- Literature Items Grid -->
        <section class="literature-grid-section mb-5">
            <div id="literatureGrid" class="row g-3 g-md-4">
                @foreach($literatureData as $item)
                    <div class="col-12 col-md-6 col-xl-4 literature-card-col" 
                         data-category="{{ $item['category'] }}" 
                         data-id="{{ $item['id'] }}"
                         data-type="{{ $item['type'] }}"
                         data-title="{{ strtolower($item['title']) }}"
                         data-desc="{{ strtolower($item['description']) }}"
                         data-badge="{{ strtolower($item['badge']) }}">
                        
                        <div class="literature-card h-100 p-3 p-md-4 rounded-4 shadow-sm border bg-white d-flex flex-column justify-content-between position-relative">
                            
                            <div>
                                <!-- Card Header: Badge & Icon -->
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="lit-badge {{ $item['badge_class'] }}">
                                        {{ $item['badge'] }}
                                    </span>
                                    <div class="lit-type-icon">
                                        <i class="bi {{ $item['icon'] }}"></i>
                                    </div>
                                </div>

                                <!-- Card Title & Description -->
                                <h2 class="h6 fw-bold text-dark lit-title mb-2 title-safe" title="{{ $item['title'] }}">
                                    {{ $item['title'] }}
                                </h2>
                                <p class="text-muted lit-desc mb-3 small" style="line-height: 1.6;">
                                    {{ $item['description'] }}
                                </p>
                            </div>

                            <!-- Card Footer Actions -->
                            <div class="lit-card-actions pt-3 border-top mt-auto d-flex flex-wrap align-items-center justify-content-between gap-2">
                                
                                @if($item['type'] === 'audio')
                                    <!-- Play in Docked Player -->
                                    <button type="button" 
                                            class="btn btn-primary btn-sm rounded-pill px-3 py-1-5 d-inline-flex align-items-center gap-2 play-audio-btn shadow-xs"
                                            onclick="playLiteratureAudio('{{ $item['title'] }}', '{{ $item['badge'] }}', '{{ $item['file'] }}')">
                                        <i class="bi bi-play-fill fs-5"></i>
                                        <span>{{ __('messages.listen_now') }}</span>
                                    </button>
                                    
                                    <div class="d-flex align-items-center gap-1">
                                        <a href="{{ $item['file'] }}" download class="btn btn-light btn-sm rounded-circle action-icon-btn" title="{{ __('messages.download_file') }}">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <button type="button" class="btn btn-light btn-sm rounded-circle action-icon-btn" title="{{ __('messages.share_link') }}" onclick="copyLiteratureLink('{{ $item['file'] }}')">
                                            <i class="bi bi-share"></i>
                                        </button>
                                    </div>

                                @elseif($item['type'] === 'pdf')
                                    <!-- Read PDF & Download -->
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ $item['file'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1-5 d-inline-flex align-items-center gap-2">
                                            <i class="bi bi-eye-fill"></i>
                                            <span>{{ __('messages.read_online') }}</span>
                                        </a>

                                        @if(!empty($item['has_audio']) && !empty($item['audio_id']))
                                            <button type="button" 
                                                    class="btn btn-outline-info btn-sm rounded-pill px-2-5 py-1-5 d-inline-flex align-items-center gap-1"
                                                    title="{{ __('messages.Audio') }}"
                                                    onclick="quickPlayFromCard('{{ $item['audio_id'] }}')">
                                                <i class="bi bi-headphones"></i>
                                                <span class="d-none d-sm-inline">{{ __('messages.listen_now') }}</span>
                                            </button>
                                        @endif
                                    </div>

                                    <div class="d-flex align-items-center gap-1">
                                        <a href="{{ $item['file'] }}" target="_blank" download class="btn btn-light btn-sm rounded-circle action-icon-btn" title="{{ __('messages.download_file') }}">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <button type="button" class="btn btn-light btn-sm rounded-circle action-icon-btn" title="{{ __('messages.share_link') }}" onclick="copyLiteratureLink('{{ $item['file'] }}')">
                                            <i class="bi bi-share"></i>
                                        </button>
                                    </div>

                                @else
                                    <!-- Generic Link / Multi Action -->
                                    <div class="d-flex align-items-center gap-2">
                                        @if(!empty($item['stream_link']))
                                            <a href="{{ $item['stream_link'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1-5 d-inline-flex align-items-center gap-2">
                                                <i class="bi bi-soundwave"></i>
                                                <span>{{ __('messages.BasicTextlisten') }}</span>
                                            </a>
                                        @endif
                                        <a href="{{ $item['file'] }}" target="_blank" class="btn btn-light btn-sm rounded-pill px-3 py-1-5 d-inline-flex align-items-center gap-2 border">
                                            <i class="bi bi-download"></i>
                                            <span>{{ __('messages.download_file') }}</span>
                                        </a>
                                    </div>
                                    <button type="button" class="btn btn-light btn-sm rounded-circle action-icon-btn" title="{{ __('messages.share_link') }}" onclick="copyLiteratureLink('{{ $item['stream_link'] ?? $item['file'] }}')">
                                        <i class="bi bi-share"></i>
                                    </button>
                                @endif

                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Empty State for Search -->
            <div id="noResultsState" class="no-results-box text-center p-5 rounded-4 bg-white border shadow-sm my-4" style="display: none;">
                <div class="empty-icon text-muted mb-3">
                    <i class="bi bi-journal-x fs-1 text-secondary"></i>
                </div>
                <h3 class="h5 fw-bold text-dark mb-2">{{ __('messages.no_literature_found') }}</h3>
                <p class="text-muted small mb-3">{{ $isArabic ? 'جرب البحث بكلمات أخرى أو اختر قسماً مختلفاً من القائمة أعلاه.' : 'Try searching with different keywords or select another category.' }}</p>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4 py-2" onclick="resetAllFilters()">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> {{ __('messages.reset_filter') }}
                </button>
            </div>
        </section>

        @auth
        <!-- Trusted Servants / Group Orders Callout Banner (Logged-in only) -->
        <section class="group-orders-banner mb-5">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden text-white group-order-card position-relative">
                <div class="banner-decor-glow-1"></div>
                <div class="banner-decor-glow-2"></div>
                <div class="card-body p-4 p-md-5 position-relative" style="z-index: 2;">
                    <div class="row align-items-center g-4">
                        
                        <div class="col-lg-8 text-start">
                            <!-- Top Badge -->
                            <div class="d-inline-flex align-items-center gap-2 px-3 py-1-5 rounded-pill banner-badge text-white small mb-3">
                                <i class="bi bi-box-seam-fill text-warning"></i>
                                <span class="fw-semibold">{{ __('messages.group_order_badge') }}</span>
                            </div>

                            <h2 class="h3 fw-bold text-white mb-2 title-safe" style="line-height: 1.45;">
                                {{ __('messages.group_order_title') }}
                            </h2>
                            
                            <p class="text-white-50 mb-3 lead-desc" style="line-height: 1.8; max-width: 680px; font-size: 0.96rem;">
                                {{ __('messages.group_order_desc') }}
                            </p>

                            <!-- Feature Pills / Highlights -->
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <div class="banner-feature-chip d-inline-flex align-items-center gap-2 px-3 py-1-5 rounded-pill text-white small">
                                    <i class="bi bi-check2-circle text-info"></i>
                                    <span>{{ __('messages.group_order_feature_quota') }}</span>
                                </div>
                                <div class="banner-feature-chip d-inline-flex align-items-center gap-2 px-3 py-1-5 rounded-pill text-white small">
                                    <i class="bi bi-receipt-cutoff text-info"></i>
                                    <span>{{ __('messages.group_order_feature_tracking') }}</span>
                                </div>
                                <div class="banner-feature-chip d-inline-flex align-items-center gap-2 px-3 py-1-5 rounded-pill text-white small">
                                    <i class="bi bi-shield-check text-info"></i>
                                    <span>{{ __('messages.group_order_feature_committee') }}</span>
                                </div>
                            </div>

                            <!-- Timing Notice -->
                            <div class="timing-notice d-flex align-items-center gap-2 text-warning-emphasis small pt-1">
                                <i class="bi bi-clock-history text-warning"></i>
                                <span class="text-white-50">{{ __('messages.group_order_timing_note') }}</span>
                            </div>
                        </div>

                        <!-- CTA Column -->
                        <div class="col-lg-4 text-start text-lg-end">
                            <div class="d-flex flex-column align-items-start align-items-lg-end gap-3">
                                <a href="{{ route('literature-requests.cart') }}" class="btn btn-banner-cta btn-lg rounded-pill px-4 py-3 fw-bold shadow-lg d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-cart3 fs-5"></i>
                                    <span>{{ __('messages.group_order_cta') }}</span>
                                    <i class="bi bi-arrow-left-short rtl-flip fs-4"></i>
                                </a>

                                <div class="d-flex align-items-center gap-2 text-white-50 small ps-2 pe-2">
                                    <i class="bi bi-shield-lock-fill text-warning"></i>
                                    <span>{{ $isArabic ? 'بوابة الخدمة المعتمدة للمجموعات' : 'Authorized group service portal' }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
        @endauth

    </div>

    <!-- Docked Persistent Glassmorphism Audio Player -->
    <div id="dockedAudioPlayer" class="docked-audio-player shadow-lg">
        <div class="container-fluid px-3 px-md-4">
            <div class="d-flex align-items-center justify-content-between gap-3 py-2">
                
                <!-- Track Meta -->
                <div class="track-meta d-flex align-items-center gap-3 flex-shrink-0">
                    <div class="player-cover-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-music-note-beamed text-white"></i>
                    </div>
                    <div class="track-text">
                        <div class="d-flex align-items-center gap-2">
                            <span id="playerBadge" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill py-0 px-2 font-monospace" style="font-size: 11px;">IP #</span>
                            <span class="player-status-badge text-white-50 small">{{ __('messages.now_playing') }}</span>
                        </div>
                        <div id="playerTitle" class="track-title text-white fw-bold text-truncate" style="max-width: 240px;">-</div>
                    </div>
                </div>

                <!-- Main Audio Controls & Scrubber -->
                <div class="player-controls-center flex-grow-1 d-flex flex-column align-items-center justify-content-center">
                    
                    <div class="d-flex align-items-center gap-3 mb-1">
                        <button type="button" id="playerSkipBack" class="btn btn-icon-player text-white-50" title="-10s" onclick="seekAudio(-10)">
                            <i class="bi bi-arrow-counterclockwise fs-5"></i>
                        </button>

                        <button type="button" id="playerPlayPauseBtn" class="btn btn-play-main rounded-circle shadow" title="Play/Pause" onclick="toggleAudioPlay()">
                            <i class="bi bi-play-fill fs-3" id="playerPlayIcon"></i>
                        </button>

                        <button type="button" id="playerSkipForward" class="btn btn-icon-player text-white-50" title="+10s" onclick="seekAudio(10)">
                            <i class="bi bi-arrow-clockwise fs-5"></i>
                        </button>

                        <button type="button" id="playerSpeedBtn" class="btn btn-speed-toggle badge rounded-pill bg-white bg-opacity-10 text-white border-0 px-2 py-1" title="{{ __('messages.speed') }}" onclick="cyclePlaybackSpeed()">
                            1.0x
                        </button>
                    </div>

                    <!-- Scrubber Timeline -->
                    <div class="scrubber-timeline w-100 d-flex align-items-center gap-2" style="max-width: 540px;">
                        <span id="playerCurrentTime" class="time-stamp text-white-50 small font-monospace">00:00</span>
                        <div class="scrubber-bar flex-grow-1 position-relative" id="scrubberTrack">
                            <div id="scrubberProgress" class="scrubber-progress"></div>
                            <input type="range" id="playerRange" class="scrubber-input" min="0" max="100" value="0" step="0.1">
                        </div>
                        <span id="playerDuration" class="time-stamp text-white-50 small font-monospace">00:00</span>
                    </div>

                </div>

                <!-- Volume & Extra Tools -->
                <div class="player-tools-right d-flex align-items-center gap-2 flex-shrink-0">
                    
                    <div class="volume-box d-none d-md-flex align-items-center gap-2">
                        <button type="button" class="btn btn-icon-player text-white-50 p-0" id="volumeMuteBtn" onclick="toggleMuteAudio()">
                            <i class="bi bi-volume-up-fill fs-5" id="volumeIcon"></i>
                        </button>
                        <input type="range" id="volumeRange" class="volume-slider" min="0" max="1" step="0.05" value="1">
                    </div>

                    <a id="playerDownloadBtn" href="#" download class="btn btn-icon-player text-white-50" title="{{ __('messages.download_file') }}">
                        <i class="bi bi-download fs-5"></i>
                    </a>

                    <button type="button" class="btn btn-icon-player text-white-50 fs-4 p-0" title="Close" onclick="closeAudioPlayer()">
                        &times;
                    </button>
                </div>

            </div>
        </div>
        
        <!-- Native Audio Element -->
        <audio id="nativeAudioElement" preload="metadata"></audio>
    </div>

    <!-- Floating QR Code Button & Modal -->
    <button id="qrFloatingBtn" title="{{ __('messages.scan_qr') }}" class="shadow-lg">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-qr-code" viewBox="0 0 16 16">
            <path d="M2 2h2v2H2V2Z"/>
            <path d="M6 0v6H0V0h6ZM5 1H1v4h4V1ZM4 12H2v2h2v-2Z"/>
            <path d="M6 10v6H0v-6h6Zm-5 1v4h4v-4H1Zm11-9h2v2h-2V2Z"/>
            <path d="M10 0v6h6V0h-6Zm5 1v4h-4V1h4ZM8 1V0h1v2H8v2H7V1h1Zm0 5V4h1v2H8ZM6 8V7h1V6h1v2h1V7h5v1h-4v1h2v1h-1v2h1v1h-1v1h-1v-2h-1v2h-2v-1h2v-1h-3V8Zm2 2v1h2v-1H8Z"/>
            <path d="M10 11v1h2v-1h-2Zm2 2v1h1v-2h-1v1Zm-2-2v1h2v-1h-2Z"/>
        </svg>
    </button>

    <div id="qrModal" class="qr-modal">
        <div class="qr-modal-content">
            <span class="qr-close-btn">&times;</span>
            <div class="modal-icon-badge mx-auto mb-2 text-primary">
                <i class="bi bi-qr-code-scan fs-3"></i>
            </div>
            <h3 class="qr-modal-title fw-bold">{{ __('messages.meetings_qr') }}</h3>
            <p class="qr-modal-subtitle text-muted">{{ __('messages.scan_qr') }}</p>
            <div class="qr-canvas-wrapper bg-white shadow-xs">
                <div id="qrCanvasContainer" style="width: 256px; height: 256px; margin: 0 auto; display: flex; align-items: center; justify-content: center;"></div>
            </div>
            <button id="qrDownloadBtn" class="qr-download-btn mt-3">
                <i class="bi bi-download me-2"></i>
                {{ __('messages.download_qr') }}
            </button>
        </div>
    </div>

    <!-- Toast Notification for Copying Links -->
    <div id="toastNotification" class="toast-popup rounded-pill px-4 py-2 shadow-lg bg-dark text-white d-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill text-success"></i>
        <span id="toastMessage">{{ __('messages.link_copied') }}</span>
    </div>

    <!-- Custom CSS Styles -->
    <style>
        /* Base Colors & Design Tokens */
        :root {
            --na-primary: #00698f;
            --na-primary-hover: #005070;
            --na-primary-subtle: #e6f3f7;
            --na-teal: #0d9488;
            --na-amber: #d97706;
            --na-navy: #0d3b66;
            --na-bg-light: #f8fafc;
            --na-card-border: #e2e8f0;
        }

        .literature-hub-wrapper {
            margin-top: 0.5rem;
            padding-bottom: 5rem;
        }

        /* Hero Section */
        .literature-hero {
            padding: 1.5rem 0 1rem;
        }

        .hero-badge {
            background-color: var(--na-primary-subtle);
            color: var(--na-primary);
            font-size: 0.85rem;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 50rem;
            letter-spacing: 0.5px;
        }

        .hero-title {
            color: #1e293b;
            font-size: clamp(1.6rem, 3.5vw, 2.3rem);
            letter-spacing: -0.5px;
            line-height: 1.5;
        }

        .hero-subtitle {
            max-width: 680px;
            font-size: 1.05rem;
            line-height: 1.7;
        }

        /* Flagship Cards */
        .flagship-card {
            background: #ffffff;
            border: 1px solid var(--na-card-border) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .flagship-card h2 {
            line-height: 1.6;
        }

        .flagship-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -10px rgba(0, 105, 143, 0.18) !important;
        }

        .card-basic-text {
            border-top: 4px solid var(--na-primary) !important;
        }

        .card-white-booklet {
            border-top: 4px solid var(--na-teal) !important;
        }

        .card-jft {
            border-top: 4px solid var(--na-amber) !important;
        }

        .bg-teal {
            background-color: var(--na-teal) !important;
        }

        .text-teal {
            color: var(--na-teal) !important;
        }

        .bg-amber {
            background-color: #fef3c7 !important;
            color: #92400e !important;
        }

        .text-amber {
            color: var(--na-amber) !important;
        }

        .btn-amber {
            background-color: #f59e0b;
            color: #ffffff;
            border: none;
        }

        .btn-amber:hover {
            background-color: #d97706;
            color: #ffffff;
        }

        .flagship-icon {
            font-size: 2rem;
            opacity: 0.85;
        }

        .flagship-desc {
            line-height: 1.6;
        }

        /* Sticky Controls Bar */
        .sticky-controls {
            border-color: var(--na-card-border);
            z-index: 10;
        }

        .search-input-wrapper {
            position: relative;
        }

        .search-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
            pointer-events: none;
        }

        html[dir="rtl"] .search-icon {
            right: 18px;
        }

        html[dir="ltr"] .search-icon,
        html:not([dir]) .search-icon {
            left: 18px;
        }

        .literature-search-input {
            padding-top: 0.65rem;
            padding-bottom: 0.65rem;
            font-size: 0.95rem;
            border-color: #cbd5e1;
            transition: all 0.2s ease;
        }

        html[dir="rtl"] .literature-search-input {
            padding-right: 46px;
            padding-left: 40px;
        }

        html[dir="ltr"] .literature-search-input,
        html:not([dir]) .literature-search-input {
            padding-left: 46px;
            padding-right: 40px;
        }

        .literature-search-input:focus {
            border-color: var(--na-primary);
            box-shadow: 0 0 0 4px rgba(0, 105, 143, 0.12);
        }

        .clear-search-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            font-size: 1.1rem;
            line-height: 1;
            transition: color 0.2s;
        }

        html[dir="rtl"] .clear-search-btn {
            left: 14px;
        }

        html[dir="ltr"] .clear-search-btn,
        html:not([dir]) .clear-search-btn {
            right: 14px;
        }

        .clear-search-btn:hover {
            color: #475569;
        }

        /* Filter Pills */
        .category-pills-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            width: 100%;
        }

        .filter-pill {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 7px 14px;
            border-radius: 50rem;
            font-size: 0.84rem;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .filter-pill:hover {
            background-color: #e2e8f0;
            color: #0f172a;
        }

        .filter-pill.active {
            background-color: var(--na-primary);
            color: #ffffff;
            border-color: var(--na-primary);
            box-shadow: 0 4px 10px rgba(0, 105, 143, 0.25);
        }

        /* Literature Card Styling */
        .literature-card {
            border-color: var(--na-card-border);
            transition: all 0.25s ease;
        }

        .literature-card:hover {
            transform: translateY(-3px);
            border-color: #cbd5e1;
            box-shadow: 0 10px 20px -8px rgba(0, 0, 0, 0.08) !important;
        }

        .lit-badge {
            font-size: 0.78rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 50rem;
            display: inline-block;
        }

        .badge-ip {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        .badge-book {
            background-color: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .badge-audio {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .badge-group {
            background-color: #f5f3ff;
            color: #6d28d9;
            border: 1px solid #ddd6fe;
        }

        .badge-service {
            background-color: #fefce8;
            color: #a16207;
            border: 1px solid #fef08a;
        }

        .lit-type-icon {
            color: #94a3b8;
            font-size: 1.25rem;
        }

        .lit-title {
            color: #0f172a;
            line-height: 1.65;
            min-height: 3.2rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .lit-desc {
            min-height: 3.4rem;
            line-height: 1.7;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .action-icon-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .action-icon-btn:hover {
            color: var(--na-primary);
            background-color: var(--na-primary-subtle);
            border-color: var(--na-primary);
        }

        .py-1-5 {
            padding-top: 0.35rem !important;
            padding-bottom: 0.35rem !important;
        }

        .px-2-5 {
            padding-left: 0.65rem !important;
            padding-right: 0.65rem !important;
        }

        /* Group Orders Banner Styles */
        .group-order-card {
            background: linear-gradient(135deg, #071f34 0%, #004d6b 40%, #00698f 75%, #0d9488 100%);
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 15px 35px -10px rgba(0, 50, 80, 0.4) !important;
        }

        .banner-decor-glow-1 {
            position: absolute;
            top: -60px;
            right: -60px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(13, 148, 136, 0.4) 0%, rgba(13, 148, 136, 0) 70%);
            filter: blur(30px);
            pointer-events: none;
        }

        .banner-decor-glow-2 {
            position: absolute;
            bottom: -60px;
            left: -60px;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.25) 0%, rgba(56, 189, 248, 0) 70%);
            filter: blur(35px);
            pointer-events: none;
        }

        .banner-badge {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .banner-feature-chip {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            font-size: 0.82rem;
            transition: all 0.2s ease;
        }

        .banner-feature-chip:hover {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }

        .btn-banner-cta {
            background: #ffffff;
            color: var(--na-navy);
            border: none;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .btn-banner-cta:hover {
            background: #f0f9ff;
            color: var(--na-primary);
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.25) !important;
        }

        .btn-banner-cta:active {
            transform: translateY(0) scale(1);
        }

        /* Docked Audio Player */
        .docked-audio-player {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 99990;
            background: rgba(13, 59, 102, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            transform: translateY(120%);
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .docked-audio-player.active {
            transform: translateY(0);
        }

        .player-cover-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #00698f 0%, #0099cc 100%);
            flex-shrink: 0;
        }

        .btn-icon-player {
            background: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-icon-player:hover {
            color: #ffffff !important;
            transform: scale(1.1);
        }

        .btn-play-main {
            width: 44px;
            height: 44px;
            background-color: #ffffff;
            color: var(--na-navy);
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-play-main:hover {
            transform: scale(1.08);
            background-color: var(--na-primary-subtle);
            color: var(--na-primary);
        }

        .btn-speed-toggle {
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-speed-toggle:hover {
            background-color: rgba(255, 255, 255, 0.25) !important;
        }

        /* Scrubber */
        .scrubber-bar {
            height: 6px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            cursor: pointer;
        }

        .scrubber-progress {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 0%;
            background: #38bdf8;
            border-radius: 4px;
            pointer-events: none;
            transition: width 0.1s linear;
        }

        html[dir="rtl"] .scrubber-progress {
            left: auto;
            right: 0;
        }

        .scrubber-input {
            position: absolute;
            top: -6px;
            left: 0;
            width: 100%;
            height: 18px;
            opacity: 0;
            cursor: pointer;
            margin: 0;
        }

        .volume-slider {
            width: 80px;
            height: 4px;
            accent-color: #38bdf8;
            cursor: pointer;
        }

        /* QR Floating Button & Modal */
        #qrFloatingBtn {
            position: fixed;
            bottom: 30px;
            z-index: 99980;
            background-color: var(--na-primary);
            color: #ffffff;
            border: none;
            outline: none;
            cursor: pointer;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        html[dir="rtl"] #qrFloatingBtn {
            left: 24px;
        }

        html[dir="ltr"] #qrFloatingBtn,
        html:not([dir]) #qrFloatingBtn {
            right: 24px;
        }

        #qrFloatingBtn:hover {
            background-color: var(--na-primary-hover);
            transform: scale(1.12);
        }

        .qr-modal {
            display: none;
            position: fixed;
            z-index: 100000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .qr-modal.show {
            display: flex;
            opacity: 1;
        }

        .qr-modal-content {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            width: 90%;
            max-width: 390px;
            text-align: center;
            position: relative;
            transform: scale(0.85);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .qr-modal.show .qr-modal-content {
            transform: scale(1);
        }

        .qr-close-btn {
            position: absolute;
            top: 16px;
            font-size: 26px;
            color: #94a3b8;
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s;
        }

        html[dir="rtl"] .qr-close-btn {
            left: 16px;
        }

        html[dir="ltr"] .qr-close-btn,
        html:not([dir]) .qr-close-btn {
            right: 16px;
        }

        .qr-close-btn:hover {
            color: #0f172a;
            background-color: #f1f5f9;
        }

        .qr-canvas-wrapper {
            padding: 16px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            display: inline-block;
        }

        .qr-download-btn {
            background-color: var(--na-primary);
            color: #ffffff;
            border: none;
            border-radius: 50rem;
            padding: 12px 24px;
            font-size: 0.95rem;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s;
        }

        .qr-download-btn:hover {
            background-color: var(--na-primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 105, 143, 0.3);
        }

        /* Toast Popup */
        .toast-popup {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            z-index: 100010;
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            font-size: 0.92rem;
        }

        .toast-popup.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* Responsive RTL Flip Utility */
        html[dir="rtl"] .rtl-flip {
            transform: scaleX(-1);
        }

        @media (max-width: 767.98px) {
            .literature-hub-wrapper {
                padding-bottom: 6rem;
                margin-top: 0;
            }

            .literature-hero {
                padding: 0.5rem 0 0.25rem;
            }

            .hero-title {
                font-size: 1.5rem;
                margin-bottom: 0.35rem !important;
            }

            .hero-subtitle {
                font-size: 0.92rem;
                margin-bottom: 1rem !important;
                line-height: 1.6;
            }

            .flagship-card {
                border-radius: 16px !important;
            }

            .flagship-desc {
                font-size: 0.85rem;
                line-height: 1.55;
            }

            .controls-section {
                padding: 0.85rem 0.9rem !important;
                border-radius: 16px !important;
                margin-bottom: 1.25rem !important;
            }

            .literature-search-input {
                font-size: 0.88rem;
                padding-top: 0.55rem;
                padding-bottom: 0.55rem;
            }

            .category-pills-container {
                gap: 6px;
            }

            .filter-pill {
                padding: 5px 11px;
                font-size: 0.78rem;
                gap: 4px;
            }

            .results-stats {
                margin-top: 0.6rem !important;
                padding-top: 0.4rem !important;
                font-size: 0.8rem;
            }

            .literature-card {
                border-radius: 16px !important;
            }

            .lit-title {
                font-size: 0.95rem;
                line-height: 1.5;
                min-height: auto;
                margin-bottom: 0.35rem !important;
            }

            .lit-desc {
                font-size: 0.83rem;
                line-height: 1.55;
                min-height: auto;
                margin-bottom: 0.6rem !important;
            }

            .lit-card-actions {
                padding-top: 0.6rem !important;
            }

            .group-order-card {
                border-radius: 16px !important;
            }

            .group-order-card .card-body {
                padding: 1.25rem !important;
            }

            .group-order-card h2 {
                font-size: 1.2rem;
                line-height: 1.4;
            }

            .group-order-card .lead-desc {
                font-size: 0.86rem;
                line-height: 1.6;
                margin-bottom: 0.75rem !important;
            }

            .banner-feature-chip {
                font-size: 0.75rem;
                padding: 3px 9px !important;
                gap: 4px;
            }

            .btn-banner-cta {
                width: 100%;
                justify-content: center;
                padding: 0.75rem 1.25rem !important;
                font-size: 0.95rem;
            }

            /* Docked Audio Player on Mobile */
            .docked-audio-player .container-fluid {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }

            .track-title {
                max-width: 120px !important;
                font-size: 0.82rem;
            }

            .player-cover-icon {
                width: 36px;
                height: 36px;
                font-size: 0.85rem;
            }

            .btn-play-main {
                width: 38px;
                height: 38px;
            }

            .player-status-badge {
                display: none;
            }

            .scrubber-timeline {
                gap: 4px;
            }

            .time-stamp {
                font-size: 10px;
            }

            #qrFloatingBtn {
                bottom: 75px;
                width: 44px;
                height: 44px;
            }
        }
    </style>

    <!-- Interactive Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- FILTERING & SEARCH SYSTEM ---
            const searchInput = document.getElementById('literatureSearchInput');
            const clearSearchBtn = document.getElementById('clearSearchBtn');
            const filterPills = document.querySelectorAll('.filter-pill');
            const cardCols = document.querySelectorAll('.literature-card-col');
            const itemsCountBadge = document.getElementById('itemsCountBadge');
            const noResultsState = document.getElementById('noResultsState');
            const resetFiltersBtn = document.getElementById('resetFiltersBtn');

            let activeCategory = 'all';
            let searchQuery = '';

            function applyFilters() {
                let visibleCount = 0;
                const normalizedQuery = searchQuery.trim().toLowerCase();

                cardCols.forEach(col => {
                    const category = col.getAttribute('data-category');
                    const title = col.getAttribute('data-title') || '';
                    const desc = col.getAttribute('data-desc') || '';
                    const badge = col.getAttribute('data-badge') || '';
                    const id = col.getAttribute('data-id') || '';

                    const matchesCategory = (activeCategory === 'all') || (category === activeCategory);
                    const matchesSearch = !normalizedQuery || 
                                          title.includes(normalizedQuery) || 
                                          desc.includes(normalizedQuery) || 
                                          badge.includes(normalizedQuery) || 
                                          id.includes(normalizedQuery);

                    if (matchesCategory && matchesSearch) {
                        col.style.display = 'block';
                        visibleCount++;
                    } else {
                        col.style.display = 'none';
                    }
                });

                // Update count badge & empty state
                itemsCountBadge.textContent = visibleCount;
                if (visibleCount === 0) {
                    noResultsState.style.display = 'block';
                } else {
                    noResultsState.style.display = 'none';
                }

                // Show/hide reset button
                if (activeCategory !== 'all' || normalizedQuery !== '') {
                    resetFiltersBtn.style.display = 'inline-block';
                } else {
                    resetFiltersBtn.style.display = 'none';
                }

                // Show/hide clear search button
                clearSearchBtn.style.display = normalizedQuery ? 'block' : 'none';
            }

            // Category pill click handler
            filterPills.forEach(pill => {
                pill.addEventListener('click', function() {
                    filterPills.forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    activeCategory = this.getAttribute('data-category');
                    applyFilters();
                });
            });

            // Live search input handler
            searchInput.addEventListener('input', function() {
                searchQuery = this.value;
                applyFilters();
            });

            // Clear search button handler
            clearSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                searchQuery = '';
                searchInput.focus();
                applyFilters();
            });

            window.resetAllFilters = function() {
                activeCategory = 'all';
                searchQuery = '';
                searchInput.value = '';
                filterPills.forEach(p => p.classList.toggle('active', p.getAttribute('data-category') === 'all'));
                applyFilters();
            };

            // --- PERSISTENT AUDIO PLAYER SYSTEM ---
            const audioPlayer = document.getElementById('dockedAudioPlayer');
            const audioElement = document.getElementById('nativeAudioElement');
            const playPauseBtn = document.getElementById('playerPlayPauseBtn');
            const playIcon = document.getElementById('playerPlayIcon');
            const playerBadge = document.getElementById('playerBadge');
            const playerTitle = document.getElementById('playerTitle');
            const playerCurrentTime = document.getElementById('playerCurrentTime');
            const playerDuration = document.getElementById('playerDuration');
            const playerRange = document.getElementById('playerRange');
            const scrubberProgress = document.getElementById('scrubberProgress');
            const playerSpeedBtn = document.getElementById('playerSpeedBtn');
            const volumeRange = document.getElementById('volumeRange');
            const volumeMuteBtn = document.getElementById('volumeMuteBtn');
            const volumeIcon = document.getElementById('volumeIcon');
            const playerDownloadBtn = document.getElementById('playerDownloadBtn');

            const speeds = [1.0, 1.25, 1.5, 2.0];
            let currentSpeedIndex = 0;

            window.playLiteratureAudio = function(title, badge, audioUrl) {
                playerTitle.textContent = title;
                playerBadge.textContent = badge;
                playerDownloadBtn.href = audioUrl;

                audioElement.src = audioUrl;
                audioElement.playbackRate = speeds[currentSpeedIndex];
                audioElement.play().then(() => {
                    updatePlayIcon(true);
                }).catch(err => {
                    console.log('Audio autoplay prevented:', err);
                    updatePlayIcon(false);
                });

                audioPlayer.classList.add('active');
            };

            window.quickPlayFromCard = function(audioId) {
                const targetCol = document.querySelector(`.literature-card-col[data-id="${audioId}"]`);
                if (targetCol) {
                    const playBtn = targetCol.querySelector('.play-audio-btn');
                    if (playBtn) playBtn.click();
                }
            };

            window.toggleAudioPlay = function() {
                if (!audioElement.src) return;
                if (audioElement.paused) {
                    audioElement.play();
                    updatePlayIcon(true);
                } else {
                    audioElement.pause();
                    updatePlayIcon(false);
                }
            };

            function updatePlayIcon(isPlaying) {
                if (isPlaying) {
                    playIcon.className = 'bi bi-pause-fill fs-3';
                } else {
                    playIcon.className = 'bi bi-play-fill fs-3';
                }
            }

            window.seekAudio = function(seconds) {
                if (!audioElement.duration) return;
                audioElement.currentTime = Math.max(0, Math.min(audioElement.duration, audioElement.currentTime + seconds));
            };

            window.cyclePlaybackSpeed = function() {
                currentSpeedIndex = (currentSpeedIndex + 1) % speeds.length;
                const speed = speeds[currentSpeedIndex];
                audioElement.playbackRate = speed;
                playerSpeedBtn.textContent = speed.toFixed(speed % 1 === 0 ? 1 : 2) + 'x';
            };

            window.closeAudioPlayer = function() {
                audioElement.pause();
                updatePlayIcon(false);
                audioPlayer.classList.remove('active');
            };

            // Audio Time & Scrubber Events
            audioElement.addEventListener('timeupdate', function() {
                if (!audioElement.duration) return;
                const current = audioElement.currentTime;
                const total = audioElement.duration;
                const pct = (current / total) * 100;

                playerRange.value = pct;
                scrubberProgress.style.width = pct + '%';
                playerCurrentTime.textContent = formatTime(current);
            });

            audioElement.addEventListener('loadedmetadata', function() {
                playerDuration.textContent = formatTime(audioElement.duration);
            });

            audioElement.addEventListener('ended', function() {
                updatePlayIcon(false);
                playerRange.value = 0;
                scrubberProgress.style.width = '0%';
            });

            playerRange.addEventListener('input', function() {
                if (!audioElement.duration) return;
                const pct = this.value;
                audioElement.currentTime = (pct / 100) * audioElement.duration;
                scrubberProgress.style.width = pct + '%';
            });

            // Volume Controls
            volumeRange.addEventListener('input', function() {
                audioElement.volume = this.value;
                updateVolumeIcon(this.value);
            });

            window.toggleMuteAudio = function() {
                if (audioElement.volume > 0) {
                    audioElement.dataset.lastVol = audioElement.volume;
                    audioElement.volume = 0;
                    volumeRange.value = 0;
                } else {
                    const restored = audioElement.dataset.lastVol || 1;
                    audioElement.volume = restored;
                    volumeRange.value = restored;
                }
                updateVolumeIcon(audioElement.volume);
            };

            function updateVolumeIcon(vol) {
                if (vol == 0) {
                    volumeIcon.className = 'bi bi-volume-mute-fill fs-5';
                } else if (vol < 0.5) {
                    volumeIcon.className = 'bi bi-volume-down-fill fs-5';
                } else {
                    volumeIcon.className = 'bi bi-volume-up-fill fs-5';
                }
            }

            function formatTime(sec) {
                if (isNaN(sec)) return '00:00';
                const m = Math.floor(sec / 60);
                const s = Math.floor(sec % 60);
                return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
            }

            // --- LINK COPYING & TOAST POPUP ---
            window.copyLiteratureLink = function(url) {
                navigator.clipboard.writeText(url).then(() => {
                    showToast();
                }).catch(() => {
                    const input = document.createElement('input');
                    input.value = url;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    document.body.removeChild(input);
                    showToast();
                });
            };

            function showToast() {
                const toast = document.getElementById('toastNotification');
                toast.classList.add('show');
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 2500);
            }

            // --- QR CODE GENERATION & MODAL ---
            const qrFloatingBtn = document.getElementById('qrFloatingBtn');
            const qrModal = document.getElementById('qrModal');
            const qrCloseBtn = document.querySelector('.qr-close-btn');
            const qrDownloadBtn = document.getElementById('qrDownloadBtn');
            const container = document.getElementById('qrCanvasContainer');
            let qrGenerated = false;

            qrFloatingBtn.addEventListener('click', function() {
                qrModal.style.display = 'flex';
                setTimeout(() => {
                    qrModal.classList.add('show');
                }, 10);
                if (!qrGenerated) generateQRCode();
            });

            function closeQRModal() {
                qrModal.classList.remove('show');
                setTimeout(() => {
                    qrModal.style.display = 'none';
                }, 300);
            }

            qrCloseBtn.addEventListener('click', closeQRModal);
            window.addEventListener('click', function(e) {
                if (e.target === qrModal) closeQRModal();
            });

            function generateQRCode() {
                const currentUrl = window.location.href;
                container.innerHTML = "";
                new QRCode(container, {
                    text: currentUrl,
                    width: 256,
                    height: 256,
                    colorDark : "#000000",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H
                });

                setTimeout(() => {
                    const canvas = container.querySelector('canvas');
                    if (!canvas) return;
                    const ctx = canvas.getContext('2d');
                    const logo = new Image();
                    logo.onload = function() {
                        const logoSize = 64;
                        const x = (canvas.width - logoSize) / 2;
                        const y = (canvas.height - logoSize) / 2;
                        ctx.fillStyle = '#ffffff';
                        ctx.beginPath();
                        const padding = 4;
                        if (ctx.roundRect) {
                            ctx.roundRect(x - padding, y - padding, logoSize + padding * 2, logoSize + padding * 2, 8);
                        } else {
                            ctx.rect(x - padding, y - padding, logoSize + padding * 2, logoSize + padding * 2);
                        }
                        ctx.fill();
                        ctx.drawImage(logo, x, y, logoSize, logoSize);
                        qrGenerated = true;
                    };
                    logo.onerror = function() {
                        qrGenerated = true;
                    };
                    logo.src = "{{ asset('assets/images/na-logo-qr.jpg') }}";
                }, 150);
            }

            qrDownloadBtn.addEventListener('click', function() {
                const canvas = container.querySelector('canvas');
                if (!canvas) return;
                const link = document.createElement('a');
                link.download = 'na-egypt-literature-qr.png';
                link.href = canvas.toDataURL('image/png');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        });
    </script>
</x-frontend.layout>