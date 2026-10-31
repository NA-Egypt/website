<x-layout>
    <div class="container-fluid px-2 px-sm-3 mt-4 mb-5 mx-auto" style="max-width: 1200px; width: 100%;">
        
        {{-- Breadcrumb & Backhead --}}
        <x-backhead>{{ __('messages.gsr_meetings_guide') }}</x-backhead>

        {{-- Guide Hero Banner --}}
        <div class="glass-card p-4 p-md-5 rounded-4 mb-4 shadow-sm position-relative overflow-hidden" style="border: 1px solid var(--glass-border); background: linear-gradient(135deg, rgba(37,99,235,0.06) 0%, rgba(14,165,233,0.03) 100%);">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3" style="background: rgba(37,99,235,0.1); color: #2563eb; font-size: 0.85rem; font-weight: 600;">
                        <i class="bi bi-shield-check"></i> {{ __('messages.gsr') }} &bull; {{ __('messages.Meetings') }}
                    </div>
                    <h2 class="fw-bold mb-2" style="color: var(--text-primary);">
                        {{ __('messages.gsr_meetings_guide') }}
                    </h2>
                    <p class="text-secondary mb-0" style="max-width: 750px; font-size: 1rem; line-height: 1.6;">
                        {{ __('messages.gsr_guide_subtitle') }}
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <a href="#practical-scenarios" class="btn btn-outline-primary rounded-pill px-3 py-2 btn-sm d-inline-flex align-items-center gap-1.5 shadow-sm">
                        <i class="bi bi-lightning-charge"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'سيناريوهات عملية' : 'Practical Scenarios' }}</span>
                    </a>
                    <a href="#faq-section" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm d-inline-flex align-items-center gap-1.5 shadow-sm">
                        <i class="bi bi-question-circle"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'الأسئلة الشائعة' : 'FAQ' }}</span>
                    </a>
                </div>
            </div>

            {{-- Quick Nav Jump Pills --}}
            <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top" style="border-color: var(--glass-border) !important;">
                <span class="text-secondary small d-flex align-items-center me-2">
                    <i class="bi bi-compass me-1"></i> {{ app()->getLocale() === 'ar' ? 'الانتقال السريع للخطوات:' : 'Quick Navigation:' }}
                </span>
                <a href="#step-1" class="badge rounded-pill px-3 py-2 text-decoration-none transition-hover" style="background: rgba(0,0,0,0.04); color: var(--text-primary); border: 1px solid var(--glass-border);">
                    ١. {{ app()->getLocale() === 'ar' ? 'لوحة تحكم المجموعة' : 'Group Hub' }}
                </a>
                <a href="#step-2" class="badge rounded-pill px-3 py-2 text-decoration-none transition-hover" style="background: rgba(0,0,0,0.04); color: var(--text-primary); border: 1px solid var(--glass-border);">
                    ٢. {{ app()->getLocale() === 'ar' ? 'نموذج الإضافة والمواعيد' : 'Add Form & Times' }}
                </a>
                <a href="#step-3" class="badge rounded-pill px-3 py-2 text-decoration-none transition-hover" style="background: rgba(0,0,0,0.04); color: var(--text-primary); border: 1px solid var(--glass-border);">
                    ٣. {{ app()->getLocale() === 'ar' ? 'الموضوعات وقفل اجتماع العمل' : 'Topics & Business Lock' }}
                </a>
                <a href="#step-4" class="badge rounded-pill px-3 py-2 text-decoration-none transition-hover" style="background: rgba(0,0,0,0.04); color: var(--text-primary); border: 1px solid var(--glass-border);">
                    ٤. {{ app()->getLocale() === 'ar' ? 'مفاتيح الإعدادات والخيارات' : 'Settings & Options' }}
                </a>
                <a href="#step-5" class="badge rounded-pill px-3 py-2 text-decoration-none transition-hover" style="background: rgba(0,0,0,0.04); color: var(--text-primary); border: 1px solid var(--glass-border);">
                    ٥. {{ app()->getLocale() === 'ar' ? 'التعديل والتعليق المؤقت' : 'Edit & Suspension' }}
                </a>
                <a href="#cards-showcase" class="badge rounded-pill px-3 py-2 text-decoration-none transition-hover" style="background: rgba(37,99,235,0.08); color: #2563eb; border: 1px solid rgba(37,99,235,0.2); font-weight: 600;">
                    <i class="bi bi-collection-play me-1"></i> {{ app()->getLocale() === 'ar' ? 'معرض بطاقات الاجتماعات' : 'Cards Showcase' }}
                </a>
                <a href="#step-6" class="badge rounded-pill px-3 py-2 text-decoration-none transition-hover" style="background: rgba(0,0,0,0.04); color: var(--text-primary); border: 1px solid var(--glass-border);">
                    ٦. {{ app()->getLocale() === 'ar' ? 'المعاينة في الدليل العام' : 'Public Directory' }}
                </a>
            </div>
        </div>

        {{-- Main Steps Workflow --}}
        <div class="d-flex flex-column gap-5 mt-4">

            {{-- Step 1 --}}
            <div class="glass-card p-4 p-md-5 rounded-4 shadow-sm" id="step-1" style="border: 1px solid var(--glass-border);">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center text-white shadow-sm" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); width: 38px; height: 38px; font-size: 1.1rem; font-weight: 700;">1</span>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">
                            {{ app()->getLocale() === 'ar' ? 'الوصول لقسم الاجتماعات في لوحة المجموعة' : 'Accessing the Meetings Hub on Group Profile' }}
                        </h4>
                        <span class="text-secondary small">
                            {{ app()->getLocale() === 'ar' ? 'المسار التلقائي للـ GSR عبر صفحة تفاصيل المجموعة' : 'Default GSR Landing on Group Details Profile' }}
                        </span>
                    </div>
                </div>

                <p class="text-secondary" style="line-height: 1.7;">
                    @if(app()->getLocale() === 'ar')
                        عند تسجيل دخولك بحساب الـ GSR الخاص بمجموعتك، يتم توجيهك تلقائياً إلى صفحة تفاصيل مجموعتك. في أسفل بطاقة البيانات الرئيسية، يظهر قسم <strong>اجتماعات التعافي</strong> والذي يعرض كافة اجتماعات المجموعة مرتبة زمنياً، وبجواره زر <strong>"إضافة اجتماع" (+ Add Meeting)</strong>.
                    @else
                        When logging into the portal with your GSR account, you are automatically routed to your Group Details profile. Beneath the primary group information card, you will find the <strong>Recovery Meetings</strong> section displaying all registered meetings and the prominent <strong>+ Add Meeting</strong> action button.
                    @endif
                </p>

                <div class="rounded-4 overflow-hidden border shadow-sm my-3 position-relative group-screenshot-wrapper" style="border-color: var(--glass-border) !important;">
                    <img src="{{ asset('images/guides/gsr/01-group-dashboard-meetings.png') }}" alt="Group Dashboard Meetings Hub" class="img-fluid w-100 guide-screenshot" style="display: block; cursor: zoom-in;" onclick="openScreenshotModal(this.src, '{{ app()->getLocale() === 'ar' ? 'لوحة تحكم المجموعة وقسم الاجتماعات' : 'Group Dashboard Meetings Hub' }}')">
                    <div class="p-2 text-center bg-light border-top small text-secondary">
                        <i class="bi bi-info-circle me-1"></i> {{ app()->getLocale() === 'ar' ? 'شكل قسم الاجتماعات والزر المخصص لإضافة اجتماع جديد (انقر على الصورة للتكبير)' : 'Meetings card layout and the "+ Add Meeting" trigger button (Click image to zoom)' }}
                    </div>
                </div>

                <div class="alert alert-info border-0 rounded-4 d-flex align-items-start gap-3 mt-4 mb-0" style="background: rgba(14, 165, 233, 0.08);">
                    <i class="bi bi-geo-alt-fill text-info fs-4 flex-shrink-0 mt-1"></i>
                    <div>
                        <strong class="d-block text-dark mb-1">{{ app()->getLocale() === 'ar' ? 'تعديل عنوان ومقر المجموعة:' : 'Updating Physical Venue & Address:' }}</strong>
                        <span class="text-secondary small">
                            {{ app()->getLocale() === 'ar' ? 'إذا انتقلت المجموعة لمقر جديد أو تغير رابط خرائط جوجل، يتم تعديله من خلال زر "تعديل المجموعة" في أعلى بطاقة المجموعة، لينعكس تلقائياً على كافة الاجتماعات دون الحاجة لتعديل كل اجتماع على حدة.' : 'If your group moves to a new venue or updates its Google Maps pin, use "Edit Group" at the top of the group profile. This updates all assigned meetings in a single step.' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Step 2 --}}
            <div class="glass-card p-4 p-md-5 rounded-4 shadow-sm" id="step-2" style="border: 1px solid var(--glass-border);">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center text-white shadow-sm" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); width: 38px; height: 38px; font-size: 1.1rem; font-weight: 700;">2</span>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">
                            {{ app()->getLocale() === 'ar' ? 'تحديد اليوم، المواعيد، ونمط التكرار' : 'Day, Timings, & Recurrence Pattern' }}
                        </h4>
                        <span class="text-secondary small">
                            {{ app()->getLocale() === 'ar' ? 'الخطوات الأساسية لضبط التوقيت ونمط انعقاد الاجتماع' : 'Core schedule settings and weekly vs monthly recurrence' }}
                        </span>
                    </div>
                </div>

                <p class="text-secondary" style="line-height: 1.7;">
                    @if(app()->getLocale() === 'ar')
                        عند الضغط على "إضافة اجتماع"، يتم نقلك إلى نموذج التسجيل (`/meeting/create`). قم بتحديد يوم الانعقاد ووقتي البداية والنهاية. يتيح النظام خيارات تكرار مرنة سواء كان الاجتماع أسبوعياً بانتظام أو ينعقد في أسابيع محددة من الشهر (مثل الأسبوع الأول، الثالث، أو الأخير).
                    @else
                        Clicking "Add Meeting" navigates to `/meeting/create`. Select the weekday, start time, and end time. You can specify whether the meeting convenes weekly or on designated weeks of the month (e.g. 1st, 3rd, or Last week).
                    @endif
                </p>

                <div class="rounded-4 overflow-hidden border shadow-sm my-3 position-relative group-screenshot-wrapper" style="border-color: var(--glass-border) !important;">
                    <img src="{{ asset('images/guides/gsr/02-add-meeting-form.png') }}" alt="Add Meeting Schedule & Recurrence" class="img-fluid w-100 guide-screenshot" style="display: block; cursor: zoom-in;" onclick="openScreenshotModal(this.src, '{{ app()->getLocale() === 'ar' ? 'تحديد اليوم والمواعيد ونمط التكرار' : 'Schedule & Recurrence Setup' }}')">
                    <div class="p-2 text-center bg-light border-top small text-secondary">
                        <i class="bi bi-info-circle me-1"></i> {{ app()->getLocale() === 'ar' ? 'حقول اليوم والمواعيد وخيارات تكرار الاجتماع' : 'Day, time pickers, and recurrence checkboxes' }}
                    </div>
                </div>

                <div class="alert alert-warning border-0 rounded-4 d-flex align-items-start gap-3 mt-4 mb-0" style="background: rgba(245, 158, 11, 0.08);">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-4 flex-shrink-0 mt-1"></i>
                    <div>
                        <strong class="d-block text-dark mb-1">{{ app()->getLocale() === 'ar' ? 'شرط التكرار الحصري:' : 'Recurrence Exclusivity Guardrail:' }}</strong>
                        <span class="text-secondary small">
                            {{ app()->getLocale() === 'ar' ? 'لا يمكن اختيار "أسبوعي" بالتزامن مع الأسابيع الفردية (مثل الأول أو الأخير). إذا كان الاجتماع أسبوعياً، اختر "أسبوعي" فقط. أما إذا كان اجتماعاً شهرياً، فاختر الأسبوع المعني دون تحديد أسبوعي.' : 'You cannot check "Weekly" alongside specific monthly weeks. For standard weekly meetings, select "Weekly" alone. For monthly meetings, pick the target week(s).' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Step 3 --}}
            <div class="glass-card p-4 p-md-5 rounded-4 shadow-sm" id="step-3" style="border: 1px solid var(--glass-border);">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center text-white shadow-sm" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); width: 38px; height: 38px; font-size: 1.1rem; font-weight: 700;">3</span>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">
                            {{ app()->getLocale() === 'ar' ? 'اختيار الموضوعات وقاعدة قفل اجتماع عمل المجموعة' : 'Topic Selection & Business Meeting Rule' }}
                        </h4>
                        <span class="text-secondary small">
                            {{ app()->getLocale() === 'ar' ? 'إضافة حتى ٣ موضوعات وقاعدة القفل التلقائي لاجتماع العمل' : 'Multi-tag topics selector and automatic closed type locking' }}
                        </span>
                    </div>
                </div>

                <p class="text-secondary" style="line-height: 1.7;">
                    @if(app()->getLocale() === 'ar')
                        يتيح لك محدد الموضوعات الذكي اختيار حتى ٣ موضوعات تعافي (مثل: دراسة خطوات، متحدث، لليوم فقط، مشاركات مفتوحة). يتم عرض الموضوعات المختارة في صورة شارات زرقاء قابلة للإزالة بنقرة واحدة.
                    @else
                        The multi-topic selector allows you to tag up to 3 topics (e.g. Steps Study, Speaker, Just For Today, Open Discussion). Chosen topics appear as removable blue badges.
                    @endif
                </p>

                <div class="rounded-4 overflow-hidden border shadow-sm my-3 position-relative group-screenshot-wrapper" style="border-color: var(--glass-border) !important;">
                    <img src="{{ asset('images/guides/gsr/03-topics-business-meeting-lock.png') }}" alt="Topic Selector & Business Meeting Lock" class="img-fluid w-100 guide-screenshot" style="display: block; cursor: zoom-in;" onclick="openScreenshotModal(this.src, '{{ app()->getLocale() === 'ar' ? 'اختيار الموضوعات وقفل نوع الاجتماع على مغلق' : 'Topics Selection & Locked Closed Type' }}')">
                    <div class="p-2 text-center bg-light border-top small text-secondary">
                        <i class="bi bi-info-circle me-1"></i> {{ app()->getLocale() === 'ar' ? 'اختيار "اجتماع عمل المجموعة" يقوم بقفل نوع الاجتماع تلقائياً على "مغلق" لمنع الأخطاء' : 'Selecting "Group Business Meeting" automatically locks meeting type to "Closed"' }}
                    </div>
                </div>

                <div class="alert alert-primary border-0 rounded-4 d-flex align-items-start gap-3 mt-4 mb-0" style="background: rgba(37, 99, 235, 0.08);">
                    <i class="bi bi-lock-fill text-primary fs-4 flex-shrink-0 mt-1"></i>
                    <div>
                        <strong class="d-block text-dark mb-1">{{ app()->getLocale() === 'ar' ? 'قاعدة الزمالة لاجتماع العمل:' : 'Fellowship Tradition Policy:' }}</strong>
                        <span class="text-secondary small">
                            {{ app()->getLocale() === 'ar' ? 'عند اختيار "اجتماع عمل المجموعة"، يقوم النظام بحصره كموضوع وحيد ويُحوّل نوع الاجتماع تلقائياً إلى "مغلق" ويقفل المفتاح لحماية خصوصية شؤون وضمير المجموعة وفقاً لتقاليد الزمالة.' : 'When "Group Business Meeting" is selected, it must be the sole topic and the meeting type is locked to "Closed", protecting fellowship group conscience in accordance with NA Traditions.' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Step 4 --}}
            <div class="glass-card p-4 p-md-5 rounded-4 shadow-sm" id="step-4" style="border: 1px solid var(--glass-border);">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center text-white shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669); width: 38px; height: 38px; font-size: 1.1rem; font-weight: 700;">4</span>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">
                            {{ app()->getLocale() === 'ar' ? 'مفاتيح إعدادات الاجتماع ومواصفات القاعة' : 'Meeting Switches & Facility Amenities' }}
                        </h4>
                        <span class="text-secondary small">
                            {{ app()->getLocale() === 'ar' ? 'النوع (مغلق/مفتوح)، اللغة، الحالة، وخيارات القاعة' : 'Open/Closed, Language, Status, and Accessibility/Smoking/Parking' }}
                        </span>
                    </div>
                </div>

                <p class="text-secondary" style="line-height: 1.7;">
                    @if(app()->getLocale() === 'ar')
                        تتضمن إعدادات الاجتماع ثلاثة مفاتيح تبديل سريعة: <strong>نوع الاجتماع</strong> (مغلق لأعضاء الزمالة فقط، أو مفتوح للجميع)، و<strong>اللغة</strong> (عربي / إنجليزي)، و<strong>الحالة</strong> (متاح للجمهور أو معلق مؤقتاً). بالإضافة إلى شبكة خيارات ومواصفات القاعة لتسهيل وصول الأعضاء.
                    @else
                        Meeting settings feature three toggles: <strong>Type</strong> (Closed vs Open), <strong>Language</strong> (Arabic vs English), and <strong>Status</strong> (Available vs Suspended). Below that is the amenities grid detailing facility features.
                    @endif
                </p>

                <div class="rounded-4 overflow-hidden border shadow-sm my-3 position-relative group-screenshot-wrapper" style="border-color: var(--glass-border) !important;">
                    <img src="{{ asset('images/guides/gsr/04-meeting-settings-and-options.png') }}" alt="Meeting Settings & Options" class="img-fluid w-100 guide-screenshot" style="display: block; cursor: zoom-in;" onclick="openScreenshotModal(this.src, '{{ app()->getLocale() === 'ar' ? 'مفاتيح إعدادات الاجتماع وخيارات ومواصفات القاعة' : 'Meeting Settings & Amenities Grid' }}')">
                    <div class="p-2 text-center bg-light border-top small text-secondary">
                        <i class="bi bi-info-circle me-1"></i> {{ app()->getLocale() === 'ar' ? 'مفاتيح النوع واللغة والحالة، وشبكة خيارات ذوي الهمم والباركينج والتدخين والشموع' : 'Switches for Type, Language, Status, and options for Accessibility, Parking, Smoking, and Candle' }}
                    </div>
                </div>
            </div>

            {{-- Step 5 --}}
            <div class="glass-card p-4 p-md-5 rounded-4 shadow-sm" id="step-5" style="border: 1px solid var(--glass-border);">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center text-white shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706); width: 38px; height: 38px; font-size: 1.1rem; font-weight: 700;">5</span>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">
                            {{ app()->getLocale() === 'ar' ? 'تعديل الاجتماع والتعليق المؤقت (Suspended)' : 'Editing Meetings & Temporary Suspension' }}
                        </h4>
                        <span class="text-secondary small">
                            {{ app()->getLocale() === 'ar' ? 'الفرق بين التعليق المؤقت والحذف النهائي' : 'When to Suspend vs Delete a Recovery Meeting' }}
                        </span>
                    </div>
                </div>

                <p class="text-secondary" style="line-height: 1.7;">
                    @if(app()->getLocale() === 'ar')
                        لتعديل أي اجتماع قائم، اضغط على زر <strong>"تعديل الاجتماع" (Edit Meeting)</strong> في بطاقة الاجتماع المعني. يتيح لك النموذج تعديل أي بيان وحفظه فوراً. عند تحويل مفتاح الحالة إلى <strong>"معلق" (Suspended)</strong>، يتحول لون الشارة إلى الأحمر، ويتغير مظهر بطاقة الاجتماع في لوحة تحكم المجموعة فوراً ليحمل شارة "معلق" مع إطار أحمر خفيف كما يظهر أدناه.
                    @else
                        To update an existing meeting, click <strong>"Edit Meeting"</strong> on the meeting's card. You can modify times, topics, or toggle status with immediate effect. Switching status to <strong>"Suspended"</strong> turns the toggle red, and the meeting card on your dashboard reflects the "Suspended" badge with a subtle warning border as shown below.
                    @endif
                </p>

                {{-- Row of Two Visuals: The Form Switch & The Resulting Card --}}
                <div class="row g-3 my-3">
                    <div class="col-12 col-lg-7">
                        <div class="rounded-4 overflow-hidden border shadow-sm h-100 group-screenshot-wrapper" style="border-color: var(--glass-border) !important;">
                            <img src="{{ asset('images/guides/gsr/05-edit-meeting-and-suspension.png') }}" alt="Edit Meeting & Suspension Toggle" class="img-fluid w-100 guide-screenshot" style="display: block; cursor: zoom-in;" onclick="openScreenshotModal(this.src, '{{ app()->getLocale() === 'ar' ? 'شاشة تعديل الاجتماع ومفتاح التعليق المؤقت' : 'Edit Meeting & Suspension Switch' }}')">
                            <div class="p-2 text-center bg-light border-top small text-secondary">
                                <i class="bi bi-info-circle me-1"></i> {{ app()->getLocale() === 'ar' ? '١. تحويل مفتاح حالة الاجتماع إلى "معلق" (Suspended) من نموذج التعديل' : '1. Toggling the status switch to "Suspended" on the edit form' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5">
                        <div class="rounded-4 overflow-hidden border shadow-sm h-100 d-flex flex-column justify-content-between group-screenshot-wrapper" style="border-color: var(--glass-border) !important; background: rgba(0,0,0,0.01);">
                            <div class="p-3">
                                <span class="badge bg-danger-subtle text-danger mb-2 px-2.5 py-1 rounded-pill small fw-bold">
                                    <i class="bi bi-eye me-1"></i> {{ app()->getLocale() === 'ar' ? 'مظهر بطاقة الاجتماع المُعلّق' : 'Suspended Card Appearance' }}
                                </span>
                                <img src="{{ asset('images/guides/gsr/05b-suspended-meeting-card.png') }}" alt="Suspended Meeting Card on Dashboard" class="img-fluid w-100 rounded-3 shadow-xs guide-screenshot" style="display: block; cursor: zoom-in;" onclick="openScreenshotModal(this.src, '{{ app()->getLocale() === 'ar' ? 'مظهر بطاقة الاجتماع المُعلّق في لوحة المجموعة' : 'Suspended Meeting Card Appearance' }}')">
                            </div>
                            <div class="p-2 text-center bg-light border-top small text-secondary">
                                <i class="bi bi-info-circle me-1"></i> {{ app()->getLocale() === 'ar' ? '٢. شكل بطاقة الاجتماع في لوحة تحكم المجموعة وتظهر شارة "معلق" الحمراء' : '2. Resulting meeting card displaying the red "Suspended" badge' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-4 h-100" style="background: rgba(245, 158, 11, 0.06); border: 1px solid rgba(245, 158, 11, 0.2);">
                            <h6 class="fw-bold text-warning d-flex align-items-center mb-2">
                                <i class="bi bi-pause-circle-fill me-2"></i> {{ app()->getLocale() === 'ar' ? 'التعليق المؤقت (Suspended)' : 'Temporary Suspension' }}
                            </h6>
                            <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                {{ app()->getLocale() === 'ar' ? 'يُستخدم عند توقف الاجتماع مؤقتاً (إجازات الأعياد، صيانة القاعة، توقف موسمي). يحفظ الاجتماع في تاريخ وسجلات المجموعة ويسمح بإعادته إلى "متاح" بنقرة واحدة فور استئنافه.' : 'Use when meetings pause temporarily for holidays or repairs. Preserves historical records and allows 1-click reactivation.' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-4 h-100" style="background: rgba(239, 68, 68, 0.06); border: 1px solid rgba(239, 68, 68, 0.2);">
                            <h6 class="fw-bold text-danger d-flex align-items-center mb-2">
                                <i class="bi bi-trash3-fill me-2"></i> {{ app()->getLocale() === 'ar' ? 'الحذف النهائي (Delete)' : 'Permanent Deletion' }}
                            </h6>
                            <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                {{ app()->getLocale() === 'ar' ? 'يُستخدم فقط إذا قرر ضمير المجموعة إلغاء موعد الاجتماع نهائياً وبشكل دائم. الحذف يزيل الاجتماع كلياً من قاعدة البيانات.' : 'Use only if the group conscience permanently disbands the meeting slot. Deletion irreversibly purges the meeting from the database.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Meeting Cards Showcase Section --}}
            <div class="glass-card p-4 p-md-5 rounded-4 shadow-sm" id="cards-showcase" style="border: 1px solid var(--glass-border); background: linear-gradient(135deg, rgba(37,99,235,0.02) 0%, rgba(139,92,246,0.02) 100%);">
                <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3" style="border-color: var(--glass-border) !important;">
                    <div class="widgets-icons text-white shadow-sm" style="background: linear-gradient(135deg, #2563eb, #7c3aed); border-radius: 12px; width: 44px; height: 44px; font-size: 1.3rem;">
                        <i class="bi bi-collection-play-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1 text-dark">
                            {{ app()->getLocale() === 'ar' ? 'معرض بطاقات الاجتماعات ودلالة كل خيار (Meeting Cards Showcase)' : 'Meeting Cards Showcase & Option Anatomy' }}
                        </h4>
                        <span class="text-secondary small">
                            {{ app()->getLocale() === 'ar' ? 'عرض حي لبطاقات الاجتماعات بمختلف الحالات: المواصفات الكاملة، التعليق، اجتماع العمل، والعرض العام' : 'Visual guide to how every switch, topic, recurrence, and amenity translates to the meeting card' }}
                        </span>
                    </div>
                </div>

                <div class="row g-4">
                    {{-- Showcase Card 1: All Amenities & Open Type --}}
                    <div class="col-12 col-lg-6">
                        <div class="glass-card p-3 p-md-4 rounded-4 h-100 border d-flex flex-column justify-content-between shadow-xs transition-hover" style="border-color: var(--glass-border) !important; background: rgba(255,255,255,0.6);">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: rgba(16, 185, 129, 0.15); color: #059669;">
                                        <i class="bi bi-stars me-1"></i> {{ app()->getLocale() === 'ar' ? '١. بطاقة بكافة المواصفات وتكرار شهري' : '1. All Amenities & Monthly Recurrence' }}
                                    </span>
                                    <span class="badge bg-light text-secondary border small">{{ app()->getLocale() === 'ar' ? 'لوحة المجموعة' : 'Group Hub' }}</span>
                                </div>
                                <div class="rounded-3 overflow-hidden border shadow-sm mb-3 group-screenshot-wrapper">
                                    <img src="{{ asset('images/guides/gsr/card-all-options.png') }}" alt="Meeting Card with All Amenities" class="img-fluid w-100 guide-screenshot" style="display: block; cursor: zoom-in;" onclick="openScreenshotModal(this.src, '{{ app()->getLocale() === 'ar' ? 'بطاقة اجتماع بكافة مواصفات القاعة وتكرار شهري' : 'Meeting Card with All Amenities & Monthly Recurrence' }}')">
                                </div>
                                <h6 class="fw-bold text-dark mb-2">{{ app()->getLocale() === 'ar' ? 'تفصيل خيارات وشارات البطاقة:' : 'Card Badges Breakdown:' }}</h6>
                                <ul class="text-secondary small mb-0 ps-3 pe-3" style="line-height: 1.8;">
                                    <li><strong>{{ app()->getLocale() === 'ar' ? 'التكرار الشهري:' : 'Monthly Recurrence:' }}</strong> <span class="badge bg-secondary-subtle text-secondary">{{ app()->getLocale() === 'ar' ? 'الأسبوع الأخير' : 'Last' }}</span> {{ app()->getLocale() === 'ar' ? 'ينعقد في الأحد الأخير من كل شهر فقط.' : 'Convenes only on the last Sunday of each month.' }}</li>
                                    <li><strong>{{ app()->getLocale() === 'ar' ? 'الحالة والنوع:' : 'Status & Type:' }}</strong> <span class="badge bg-success-subtle text-success">{{ app()->getLocale() === 'ar' ? 'متاح' : 'Available' }}</span> + <span class="badge bg-info-subtle text-primary">{{ app()->getLocale() === 'ar' ? 'مفتوح' : 'Open' }}</span> ({{ app()->getLocale() === 'ar' ? 'يرحب بالمدمنين وغير المدمنين' : 'Welcomes addicts & non-addicts' }}).</li>
                                    <li><strong>{{ app()->getLocale() === 'ar' ? 'مواصفات القاعة الكاملة (Amenities):' : 'Full Amenities Badges:' }}</strong>
                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            <span class="badge bg-light text-dark border"><i class="bi bi-check-circle-fill text-success me-1"></i>{{ app()->getLocale() === 'ar' ? 'لذوي الهمم' : 'Accessibility' }}</span>
                                            <span class="badge bg-light text-dark border"><i class="bi bi-check-circle-fill text-success me-1"></i>{{ app()->getLocale() === 'ar' ? 'باركينج' : 'Parking' }}</span>
                                            <span class="badge bg-light text-dark border"><i class="bi bi-check-circle-fill text-success me-1"></i>{{ app()->getLocale() === 'ar' ? 'التدخين' : 'Smoking' }}</span>
                                            <span class="badge bg-light text-dark border"><i class="bi bi-check-circle-fill text-success me-1"></i>{{ app()->getLocale() === 'ar' ? 'شموع' : 'Candle' }}</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Showcase Card 2: Suspended Status on Dashboard --}}
                    <div class="col-12 col-lg-6">
                        <div class="glass-card p-3 p-md-4 rounded-4 h-100 border d-flex flex-column justify-content-between shadow-xs transition-hover" style="border-color: rgba(239, 68, 68, 0.3) !important; background: rgba(239, 68, 68, 0.015);">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: rgba(239, 68, 68, 0.15); color: #dc2626;">
                                        <i class="bi bi-pause-circle-fill me-1"></i> {{ app()->getLocale() === 'ar' ? '٢. بطاقة اجتماع مُعلّق مؤقتاً' : '2. Suspended Meeting Card' }}
                                    </span>
                                    <span class="badge bg-light text-secondary border small">{{ app()->getLocale() === 'ar' ? 'لوحة المجموعة' : 'Group Hub' }}</span>
                                </div>
                                <div class="rounded-3 overflow-hidden border shadow-sm mb-3 group-screenshot-wrapper">
                                    <img src="{{ asset('images/guides/gsr/05b-suspended-meeting-card.png') }}" alt="Suspended Meeting Card on Dashboard" class="img-fluid w-100 guide-screenshot" style="display: block; cursor: zoom-in;" onclick="openScreenshotModal(this.src, '{{ app()->getLocale() === 'ar' ? 'بطاقة اجتماع معلق مؤقتاً في لوحة المجموعة' : 'Suspended Meeting Card on Group Dashboard' }}')">
                                </div>
                                <h6 class="fw-bold text-dark mb-2">{{ app()->getLocale() === 'ar' ? 'تفصيل خيارات وشارات البطاقة:' : 'Card Badges Breakdown:' }}</h6>
                                <ul class="text-secondary small mb-0 ps-3 pe-3" style="line-height: 1.8;">
                                    <li><strong>{{ app()->getLocale() === 'ar' ? 'شارة الحالة الحمراء:' : 'Red Status Badge:' }}</strong> <span class="badge bg-danger-subtle text-danger"><i class="bi bi-pause-circle-fill me-1"></i>{{ app()->getLocale() === 'ar' ? 'معلق' : 'Suspended' }}</span> {{ app()->getLocale() === 'ar' ? 'توضح توقف الاجتماع مؤقتاً.' : 'Indicates temporary pause.' }}</li>
                                    <li><strong>{{ app()->getLocale() === 'ar' ? 'إطار تحذيري وتعتيم خفيف:' : 'Card Styling:' }}</strong> {{ app()->getLocale() === 'ar' ? 'يتحول إطار البطاقة إلى اللون الأحمر الفاتح لتمييزه بصرياً فوراً عن باقي الاجتماعات النشطة.' : 'Light red border and subtle tint for immediate visual distinction.' }}</li>
                                    <li><strong>{{ app()->getLocale() === 'ar' ? 'سهولة الاستئناف:' : 'One-Click Resume:' }}</strong> {{ app()->getLocale() === 'ar' ? 'الحفاظ على كافة البيانات تمهيداً لإعادته متاحاً بنقرة واحدة عند انتهاء سبب التوقف.' : 'Preserves timings & topics for immediate reactivation.' }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Showcase Card 3: Group Business Meeting --}}
                    <div class="col-12 col-lg-6">
                        <div class="glass-card p-3 p-md-4 rounded-4 h-100 border d-flex flex-column justify-content-between shadow-xs transition-hover" style="border-color: var(--glass-border) !important; background: rgba(255,255,255,0.6);">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: rgba(139, 92, 246, 0.15); color: #7c3aed;">
                                        <i class="bi bi-journal-check me-1"></i> {{ app()->getLocale() === 'ar' ? '٣. بطاقة اجتماع عمل المجموعة' : '3. Group Business Meeting Card' }}
                                    </span>
                                    <span class="badge bg-light text-secondary border small">{{ app()->getLocale() === 'ar' ? 'لوحة المجموعة' : 'Group Hub' }}</span>
                                </div>
                                <div class="rounded-3 overflow-hidden border shadow-sm mb-3 group-screenshot-wrapper">
                                    <img src="{{ asset('images/guides/gsr/card-business-meeting.png') }}" alt="Group Business Meeting Card" class="img-fluid w-100 guide-screenshot" style="display: block; cursor: zoom-in;" onclick="openScreenshotModal(this.src, '{{ app()->getLocale() === 'ar' ? 'بطاقة اجتماع عمل المجموعة وتكرار الأسبوع الأول' : 'Group Business Meeting Card & 1st Recurrence' }}')">
                                </div>
                                <h6 class="fw-bold text-dark mb-2">{{ app()->getLocale() === 'ar' ? 'تفصيل خيارات وشارات البطاقة:' : 'Card Badges Breakdown:' }}</h6>
                                <ul class="text-secondary small mb-0 ps-3 pe-3" style="line-height: 1.8;">
                                    <li><strong>{{ app()->getLocale() === 'ar' ? 'الموضوع الحصري:' : 'Exclusive Topic:' }}</strong> <span class="badge bg-success-subtle text-success"><i class="bi bi-tag me-1"></i>{{ app()->getLocale() === 'ar' ? 'اجتماع عمل المجموعة' : 'Group Business Meeting' }}</span>.</li>
                                    <li><strong>{{ app()->getLocale() === 'ar' ? 'القفل على مغلق:' : 'Locked Closed Type:' }}</strong> <span class="badge bg-info-subtle text-primary">{{ app()->getLocale() === 'ar' ? 'مغلق للمدمنين' : 'Closed' }}</span> {{ app()->getLocale() === 'ar' ? 'تقتصر اجتماعات العمل على أعضاء الزمالة فقط.' : 'Business meetings are strictly restricted to recovering addicts.' }}</li>
                                    <li><strong>{{ app()->getLocale() === 'ar' ? 'التكرار الشهري:' : 'Monthly Schedule:' }}</strong> <span class="badge bg-secondary-subtle text-secondary">{{ app()->getLocale() === 'ar' ? 'الأسبوع الأول' : '1st' }}</span> {{ app()->getLocale() === 'ar' ? 'ينعقد في الثلاثاء الأول من كل شهر.' : 'Convenes on the 1st Tuesday of the month.' }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Showcase Card 4: Suspended Banner in Public Directory --}}
                    <div class="col-12 col-lg-6">
                        <div class="glass-card p-3 p-md-4 rounded-4 h-100 border d-flex flex-column justify-content-between shadow-xs transition-hover" style="border-color: rgba(220, 38, 38, 0.3) !important; background: rgba(255,255,255,0.6);">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: rgba(220, 38, 38, 0.15); color: #dc2626;">
                                        <i class="bi bi-globe2 me-1"></i> {{ app()->getLocale() === 'ar' ? '٤. ظهور الاجتماع المُعلّق في الدليل العام' : '4. Public Suspended Meeting Display' }}
                                    </span>
                                    <span class="badge bg-primary-subtle text-primary border small">{{ app()->getLocale() === 'ar' ? 'الدليل العام /meetings' : 'Public Directory' }}</span>
                                </div>
                                <div class="rounded-3 overflow-hidden border shadow-sm mb-3 group-screenshot-wrapper">
                                    <img src="{{ asset('images/guides/gsr/card-suspended-public.png') }}" alt="Public Directory Suspended Card Banner" class="img-fluid w-100 guide-screenshot" style="display: block; cursor: zoom-in;" onclick="openScreenshotModal(this.src, '{{ app()->getLocale() === 'ar' ? 'شريط تحذير تعليق الاجتماع في الدليل العام' : 'Public Directory Suspended Banner' }}')">
                                </div>
                                <h6 class="fw-bold text-dark mb-2">{{ app()->getLocale() === 'ar' ? 'أثر التعليق على الجمهور والباحثين:' : 'Public Directory Impact:' }}</h6>
                                <ul class="text-secondary small mb-0 ps-3 pe-3" style="line-height: 1.8;">
                                    <li><strong>{{ app()->getLocale() === 'ar' ? 'شريط تحذيري واضح:' : 'Prominent Notice:' }}</strong> <span class="badge bg-danger text-white">{{ app()->getLocale() === 'ar' ? 'هذا الاجتماع مُعلق مؤقتًا' : 'Temporarily Suspended' }}</span> {{ app()->getLocale() === 'ar' ? 'يظهر باللون القرمزي في أعلى البطاقة.' : 'Renders at the top of the card in crimson.' }}</li>
                                    <li><strong>{{ app()->getLocale() === 'ar' ? 'حماية المدمن الجديد:' : 'Newcomer Safety:' }}</strong> {{ app()->getLocale() === 'ar' ? 'يمنع الأعضاء والمدمن الجديد من الذهاب إلى مقر مغلق في غير مواعيده.' : 'Prevents newcomers from traveling to closed facilities.' }}</li>
                                    <li><strong>{{ app()->getLocale() === 'ar' ? 'تحديث بوت الواتساب:' : 'WhatsApp Bot Protection:' }}</strong> {{ app()->getLocale() === 'ar' ? 'يقوم بوت الواتساب تلقائياً باستبعاد الاجتماعات المعلقة من نتائج البحث عن أقرب اجتماع اليوم.' : 'WhatsApp bot automatically excludes suspended meetings.' }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Step 6 --}}
            <div class="glass-card p-4 p-md-5 rounded-4 shadow-sm" id="step-6" style="border: 1px solid var(--glass-border);">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center text-white shadow-sm" style="background: linear-gradient(135deg, #06b6d4, #0891b2); width: 38px; height: 38px; font-size: 1.1rem; font-weight: 700;">6</span>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">
                            {{ app()->getLocale() === 'ar' ? 'انعكاس التحديثات على الدليل العام ومحرك البحث' : 'Live Verification on Public Meeting Finder' }}
                        </h4>
                        <span class="text-secondary small">
                            {{ app()->getLocale() === 'ar' ? 'كيف يرى المدمن الباحث عن التعافي اجتماعات مجموعتك فور حفظها' : 'How newcomers and members see your meetings in real time' }}
                        </span>
                    </div>
                </div>

                <p class="text-secondary" style="line-height: 1.7;">
                    @if(app()->getLocale() === 'ar')
                        بمجرد حفظ الاجتماع بحالة "متاح" (Available)، يظهر في التو واللحظة في الدليل العام للجمهور (`/meetings`)، كما يتم تحديث محرك بوت الواتساب ومولد كتيبات الطباعة وجداول الـ PDF التي تعتمد عليها لجان العلاقات العامة والمستشفيات والمؤسسات.
                    @else
                        Once saved as "Available", the meeting reflects immediately on the public meeting finder (`/meetings`), automated WhatsApp bot responses, and PDF/Printable booklets used by Public Information (PI) and Hospitals & Institutions (H&I).
                    @endif
                </p>

                <div class="rounded-4 overflow-hidden border shadow-sm my-3 position-relative group-screenshot-wrapper" style="border-color: var(--glass-border) !important;">
                    <img src="{{ asset('images/guides/gsr/06-public-meetings-verification.png') }}" alt="Public Meetings Directory Search" class="img-fluid w-100 guide-screenshot" style="display: block; cursor: zoom-in;" onclick="openScreenshotModal(this.src, '{{ app()->getLocale() === 'ar' ? 'محرك البحث ودليل الاجتماعات العام على الموقع' : 'Public Meetings Directory & Filter Engine' }}')">
                    <div class="p-2 text-center bg-light border-top small text-secondary">
                        <i class="bi bi-info-circle me-1"></i> {{ app()->getLocale() === 'ar' ? 'شكل الاجتماعات في محرك البحث الخارجي لزمالة مصر' : 'Real-time appearance of meetings in the fellowship public search' }}
                    </div>
                </div>
            </div>

        </div>

        {{-- Practical Scenarios Section --}}
        <div class="glass-card p-4 p-md-5 rounded-4 shadow-sm mt-5" id="practical-scenarios" style="border: 1px solid var(--glass-border);">
            <h4 class="fw-bold mb-4 border-bottom pb-3 d-flex align-items-center gap-2" style="color: var(--text-primary); border-color: var(--glass-border) !important;">
                <i class="bi bi-lightning-charge-fill text-warning"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'سيناريوهات عملية شائعة لممثل المجموعة' : 'Fellowship Operational Scenarios for GSRs' }}</span>
            </h4>

            <div class="row g-4">
                {{-- Scenario A --}}
                <div class="col-12 col-md-6">
                    <div class="p-4 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: rgba(37, 99, 235, 0.03); border: 1px solid var(--glass-border);">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-primary rounded-pill px-2.5 py-1">A</span>
                                <h6 class="fw-bold mb-0 text-dark">{{ app()->getLocale() === 'ar' ? 'تعديل المواعيد لشهر رمضان المبارك' : 'Ramadan Schedule Adjustments' }}</h6>
                            </div>
                            <p class="text-secondary small mb-3" style="line-height: 1.6;">
                                {{ app()->getLocale() === 'ar' ? 'تقوم المجموعة بتأخير موعد الاجتماع لما بعد الإفطار أو التراويح. يقوم الـ GSR قبل رمضان بأيام بتعديل حقلي "من" و"إلى" وحفظ الاجتماع. ينعكس الميعاد الجديد فوراً في الموقع والواتساب. وعقب انتهاء الشهر الكريم تُعاد المواعيد الطبيعية بنفس السهولة.' : 'Groups usually push meetings later after Iftar/Taraweeh. GSR edits start/end times before Ramadan, saving changes live across web & WhatsApp. Regular times are restored after Eid.' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Scenario B --}}
                <div class="col-12 col-md-6">
                    <div class="p-4 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: rgba(16, 185, 129, 0.03); border: 1px solid var(--glass-border);">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-success rounded-pill px-2.5 py-1">B</span>
                                <h6 class="fw-bold mb-0 text-dark">{{ app()->getLocale() === 'ar' ? 'اجتماع متحدث أو تقاليد شهري (الأسبوع الأخير)' : 'Monthly Speaker / Tradition Meeting' }}</h6>
                            </div>
                            <p class="text-secondary small mb-3" style="line-height: 1.6;">
                                {{ app()->getLocale() === 'ar' ? 'ترغب المجموعة في عقد اجتماع دراسة تقاليد أو متحدث في الجمعة الأخيرة فقط من كل شهر: أضف اجتماعاً جديداً، اختر موضوع "متحدث" أو "روح تقاليدنا"، وفي التكرار أزل علامة "أسبوعي" وضع علامة على "الأخير" (Last) فقط.' : 'If your group holds a Speaker or Guiding Principles meeting on the last Friday of each month: create a meeting, select topic, uncheck "Weekly", and check "Last" week.' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Scenario C --}}
                <div class="col-12 col-md-6">
                    <div class="p-4 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: rgba(139, 92, 246, 0.03); border: 1px solid var(--glass-border);">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge rounded-pill px-2.5 py-1" style="background: #7c3aed; color: #fff;">C</span>
                                <h6 class="fw-bold mb-0 text-dark">{{ app()->getLocale() === 'ar' ? 'تنظيم اجتماع عمل المجموعة الشهري' : 'Group Business Meeting Scheduling' }}</h6>
                            </div>
                            <p class="text-secondary small mb-3" style="line-height: 1.6;">
                                {{ app()->getLocale() === 'ar' ? 'تحديد موعد اجتماع ضمير المجموعة (مثلاً الأحد الأول من كل شهر): اختر موضوع "اجتماع عمل المجموعة". سيقفل النظام نوع الاجتماع تلقائياً على "مغلق"، وفي التكرار اختر "الأول" (1st).' : 'Holding a monthly business meeting (e.g. 1st Sunday): pick "Group Business Meeting". The type auto-locks to "Closed", and check "1st" in recurrence.' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Scenario D --}}
                <div class="col-12 col-md-6">
                    <div class="p-4 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: rgba(14, 165, 233, 0.03); border: 1px solid var(--glass-border);">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-info text-white rounded-pill px-2.5 py-1">D</span>
                                <h6 class="fw-bold mb-0 text-dark">{{ app()->getLocale() === 'ar' ? 'تقرير المجموعة لمنطقة الخدمة (ASC Agenda)' : 'Area Agenda & Meeting Changes Flag' }}</h6>
                            </div>
                            <p class="text-secondary small mb-3" style="line-height: 1.6;">
                                {{ app()->getLocale() === 'ar' ? 'عند تقديم تقرير مجموعتك الدوري للجنة خدمة المنطقة عبر البوابة (/agenda/create)، أجب بـ "نعم" على سؤال: "هل حدثت تغييرات في اجتماعات التعافي؟" لتنبيه أمين سر المنطقة ولجنة الجدول بتحديث السجلات الرسمية.' : 'When preparing your bi-monthly group report via /agenda/create, check "Yes" on "Recovery Meetings Changes" to alert the ASC secretary and meeting directory coordinator.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FAQ Section --}}
        <div class="glass-card p-4 p-md-5 rounded-4 shadow-sm mt-5" id="faq-section" style="border: 1px solid var(--glass-border);">
            <h4 class="fw-bold mb-4 border-bottom pb-3 d-flex align-items-center gap-2" style="color: var(--text-primary); border-color: var(--glass-border) !important;">
                <i class="bi bi-question-circle-fill text-primary"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'الأسئلة الشائعة وحل المشكلات (FAQ)' : 'Frequently Asked Questions & Troubleshooting' }}</span>
            </h4>

            <div class="accordion accordion-flush" id="gsrFaqAccordion">
                
                {{-- FAQ 1 --}}
                <div class="accordion-item bg-transparent border-bottom" style="border-color: var(--glass-border) !important;">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed bg-transparent fw-bold text-dark px-0" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne" aria-expanded="false" aria-controls="faqOne">
                            {{ app()->getLocale() === 'ar' ? 'س١: لماذا لا يظهر زر "إضافة اجتماع" في صفحتي؟' : 'Q1: Why can\'t I see the "+ Add Meeting" button?' }}
                        </button>
                    </h2>
                    <div id="faqOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#gsrFaqAccordion">
                        <div class="accordion-body px-0 text-secondary" style="line-height: 1.7;">
                            {{ app()->getLocale() === 'ar' ? 'تأكد من تسجيل دخولك بحساب الـ GSR الرسمي المخصص للمجموعة وليس بحساب مراقب أو صفة أخرى، فصلاحيات إضافة وتعديل اجتماعات المجموعة مقتصرة على ممثل المجموعة. إذا استمرت المشكلة تواصل مع لجنة تكنولوجيا المعلومات (IT).' : 'Verify you are signed in under your designated GSR account. Meeting mutation privileges are restricted to the assigned GSR. If issues persist, contact the IT committee.' }}
                        </div>
                    </div>
                </div>

                {{-- FAQ 2 --}}
                <div class="accordion-item bg-transparent border-bottom" style="border-color: var(--glass-border) !important;">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed bg-transparent fw-bold text-dark px-0" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwo" aria-expanded="false" aria-controls="faqTwo">
                            {{ app()->getLocale() === 'ar' ? 'س٢: لماذا لا أستطيع جعل اجتماع عمل المجموعة "مفتوحاً"؟' : 'Q2: Why is "Group Business Meeting" locked to Closed?' }}
                        </button>
                    </h2>
                    <div id="faqTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#gsrFaqAccordion">
                        <div class="accordion-body px-0 text-secondary" style="line-height: 1.7;">
                            {{ app()->getLocale() === 'ar' ? 'وفقاً لتقاليد ومفاهيم خدمة الزمالة، فإن اجتماعات عمل المجموعة مخصصة لمناقشة شؤون المجموعة الداخلية وضميرها وأموالها، وهي مقتصرة فقط على المدمنين المتعافين ولا يصح أن تكون مفتوحة لغير المدمنين.' : 'NA Traditions mandate that group business meetings address internal group conscience, matters, and Seventh Tradition funds, and must be strictly confined to recovering addicts.' }}
                        </div>
                    </div>
                </div>

                {{-- FAQ 3 --}}
                <div class="accordion-item bg-transparent border-bottom" style="border-color: var(--glass-border) !important;">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed bg-transparent fw-bold text-dark px-0" type="button" data-bs-toggle="collapse" data-bs-target="#faqThree" aria-expanded="false" aria-controls="faqThree">
                            {{ app()->getLocale() === 'ar' ? 'س٣: قمت بإضافة اجتماع ولم يظهر في بحث الموقع الخارجي للجمهور؟' : 'Q3: I created a meeting but it does not appear in public search?' }}
                        </button>
                    </h2>
                    <div id="faqThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#gsrFaqAccordion">
                        <div class="accordion-body px-0 text-secondary" style="line-height: 1.7;">
                            {{ app()->getLocale() === 'ar' ? 'تحقق من مفتاح "الحالة" (Status) في الاجتماع؛ إذا كانت مضبوطة على "مُعلّق" (Suspended) فلن يظهر للاجتماع أي تواجد عام. قم بتحويل المفتاح إلى "متاح" (Available) ليظهر على الفور.' : 'Check the "Status" switch in the meeting settings. If set to "Suspended", it remains hidden from public directories. Toggle it to "Available" for instant visibility.' }}
                        </div>
                    </div>
                </div>

                {{-- FAQ 4 --}}
                <div class="accordion-item bg-transparent border-0">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed bg-transparent fw-bold text-dark px-0" type="button" data-bs-toggle="collapse" data-bs-target="#faqFour" aria-expanded="false" aria-controls="faqFour">
                            {{ app()->getLocale() === 'ar' ? 'س٤: هل يمكنني تحميل هذا الدليل كملف نصي أو طباعته؟' : 'Q4: Where can I find the markdown or downloadable copy?' }}
                        </button>
                    </h2>
                    <div id="faqFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#gsrFaqAccordion">
                        <div class="accordion-body px-0 text-secondary" style="line-height: 1.7;">
                            {{ app()->getLocale() === 'ar' ? 'نعم، يتوفر الدليل كاملاً بصيغة Markdown في مستودع النظام تحت المسار: docs/GSR_MEETINGS_GUIDE.md، كما يمكنك طباعة هذه الصفحة مباشرة عبر خيار الطباعة في متصفحك (Ctrl+P).' : 'Yes, the complete bilingual markdown guide is maintained in the repository at docs/GSR_MEETINGS_GUIDE.md, and this page can be printed directly using your browser\'s print function (Ctrl+P).' }}
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Lightbox Zoom Modal for Screenshots --}}
    <div class="modal fade" id="screenshotModal" tabindex="-1" aria-labelledby="screenshotModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content glass-card border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(25px); border-radius: 20px;">
                <div class="modal-header border-bottom py-3" style="border-color: var(--glass-border) !important;">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="screenshotModalLabel">
                        <i class="bi bi-image text-primary"></i>
                        <span id="modalImageTitle"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-2 p-md-3 text-center">
                    <img id="modalImageSrc" src="" alt="Screenshot" class="img-fluid rounded-3 shadow-sm" style="max-height: 80vh; width: auto; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>

    <script>
        function openScreenshotModal(src, title) {
            document.getElementById('modalImageSrc').src = src;
            document.getElementById('modalImageTitle').textContent = title;
            const modal = new bootstrap.Modal(document.getElementById('screenshotModal'));
            modal.show();
        }
    </script>
</x-layout>

