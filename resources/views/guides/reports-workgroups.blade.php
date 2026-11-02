<x-layout>
    <div class="container-fluid px-2 px-sm-3 mt-4 mb-5 mx-auto" style="max-width: 1200px; width: 100%;">
        
        {{-- Breadcrumb & Backhead --}}
        <x-backhead>{{ __('messages.reports_workgroups_guide') }}</x-backhead>

        {{-- Guide Hero Banner --}}
        <div class="glass-card p-4 p-md-5 rounded-4 mb-4 shadow-sm position-relative overflow-hidden" style="border: 1px solid var(--glass-border); background: linear-gradient(135deg, rgba(37,99,235,0.06) 0%, rgba(16,185,129,0.03) 100%);">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3" style="background: rgba(37,99,235,0.1); color: #2563eb; font-size: 0.85rem; font-weight: 600;">
                        <i class="bi bi-file-earmark-text"></i> {{ __('messages.Committee Reports') }} &bull; {{ __('messages.Workgroups') }}
                    </div>
                    <h2 class="fw-bold mb-2" style="color: var(--text-primary);">
                        {{ __('messages.reports_workgroups_guide') }}
                    </h2>
                    <p class="text-secondary mb-0" style="max-width: 750px; font-size: 1rem; line-height: 1.6;">
                        {{ __('messages.reports_workgroups_guide_subtitle') }}
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <a href="{{ route('committee-reports.index') }}" class="btn btn-outline-primary rounded-pill px-3 py-2 btn-sm d-inline-flex align-items-center gap-1.5 shadow-sm">
                        <i class="bi bi-arrow-left"></i>
                        <span>{{ __('messages.Committee Reports') }}</span>
                    </a>
                    <a href="{{ route('workgroup.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm d-inline-flex align-items-center gap-1.5 shadow-sm">
                        <i class="bi bi-people"></i>
                        <span>{{ __('messages.Workgroups') }}</span>
                    </a>
                </div>
            </div>

            {{-- Quick Links / Role Highlights --}}
            <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top" style="border-color: var(--glass-border) !important;">
                <span class="text-secondary small d-flex align-items-center me-2">
                    <i class="bi bi-person-badge me-1"></i> {{ app()->getLocale() === 'ar' ? 'حدد دورك الخدمي للبدء:' : 'Select your role to start:' }}
                </span>
                <button class="badge rounded-pill px-3 py-2 border-0 transition-hover" onclick="switchGuideTab('workgroups-tab')" style="background: rgba(16,185,129,0.1); color: #059669; font-weight: 600; cursor: pointer;">
                    <i class="bi bi-people me-1"></i> {{ __('messages.role_workgroup_tab') }}
                </button>
                <button class="badge rounded-pill px-3 py-2 border-0 transition-hover" onclick="switchGuideTab('committees-tab')" style="background: rgba(37,99,235,0.1); color: #2563eb; font-weight: 600; cursor: pointer;">
                    <i class="bi bi-building me-1"></i> {{ __('messages.role_committee_tab') }}
                </button>
                <button class="badge rounded-pill px-3 py-2 border-0 transition-hover" onclick="switchGuideTab('rsc-tab')" style="background: rgba(139,92,246,0.1); color: #7c3aed; font-weight: 600; cursor: pointer;">
                    <i class="bi bi-shield-check me-1"></i> {{ __('messages.role_rsc_tab') }}
                </button>
                <button class="badge rounded-pill px-3 py-2 border-0 transition-hover" onclick="switchGuideTab('scenarios-tab')" style="background: rgba(245,158,11,0.1); color: #d97706; font-weight: 600; cursor: pointer;">
                    <i class="bi bi-diagram-3 me-1"></i> {{ __('messages.role_scenarios_faq_tab') }}
                </button>
            </div>
        </div>

        {{-- Role Tabs Header --}}
        <ul class="nav nav-pills custom-guide-tabs mb-4 gap-2 flex-nowrap overflow-auto pb-2" id="guideTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active px-4 py-2.5 rounded-pill fw-bold d-flex align-items-center gap-2 text-nowrap" id="workgroups-tab" data-bs-toggle="pill" data-bs-target="#workgroups-content" type="button" role="tab" aria-controls="workgroups-content" aria-selected="true">
                    <i class="bi bi-people-fill text-success"></i>
                    <span>{{ __('messages.role_workgroup_tab') }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4 py-2.5 rounded-pill fw-bold d-flex align-items-center gap-2 text-nowrap" id="committees-tab" data-bs-toggle="pill" data-bs-target="#committees-content" type="button" role="tab" aria-controls="committees-content" aria-selected="false">
                    <i class="bi bi-building text-primary"></i>
                    <span>{{ __('messages.role_committee_tab') }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4 py-2.5 rounded-pill fw-bold d-flex align-items-center gap-2 text-nowrap" id="rsc-tab" data-bs-toggle="pill" data-bs-target="#rsc-content" type="button" role="tab" aria-controls="rsc-content" aria-selected="false">
                    <i class="bi bi-shield-check text-purple" style="color: #8b5cf6;"></i>
                    <span>{{ __('messages.role_rsc_tab') }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4 py-2.5 rounded-pill fw-bold d-flex align-items-center gap-2 text-nowrap" id="scenarios-tab" data-bs-toggle="pill" data-bs-target="#scenarios-content" type="button" role="tab" aria-controls="scenarios-content" aria-selected="false">
                    <i class="bi bi-lightbulb-fill text-warning"></i>
                    <span>{{ __('messages.role_scenarios_faq_tab') }}</span>
                </button>
            </li>
        </ul>

        {{-- Tabs Content --}}
        <div class="tab-content" id="guideTabsContent">

            {{-- ========================================================================= --}}
            {{-- TAB 1: WORKGROUPS SERVANTS                                               --}}
            {{-- ========================================================================= --}}
            <div class="tab-pane fade show active" id="workgroups-content" role="tabpanel" aria-labelledby="workgroups-tab">
                
                {{-- Intro Card --}}
                <div class="card border-0 glass-card shadow-sm mb-4 rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white" style="width: 48px; height: 48px;">
                                <i class="bi bi-people-fill fs-4"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0" style="color: var(--text-primary);">
                                    {{ app()->getLocale() === 'ar' ? 'دليل خدم مجموعات العمل الفرعية' : 'Sub-Workgroup Servants Guide' }}
                                </h4>
                                <small class="text-secondary">{{ app()->getLocale() === 'ar' ? 'الصلاحيات، إنشاء مسودات التقارير، وتوثيق الاجتماعات' : 'Responsibilities, draft reporting, and meeting management' }}</small>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 rounded-3 mb-0">
                            <i class="bi bi-info-circle-fill fs-3 text-info"></i>
                            <div>
                                <strong>{{ app()->getLocale() === 'ar' ? 'مفهوم عمل مجموعة العمل في البوابة:' : 'Workgroup Operational Model in the Portal:' }}</strong><br>
                                {{ app()->getLocale() === 'ar' 
                                    ? 'مجموعة العمل هي كيان فرعي يتبع لجنة خدمة رئيسية لتنفيذ مشروع أو مهمة محددة. تقارير مجموعة العمل تُحفظ داخلياً كمسودة لمراجعتها وتضمينها من قِبل لجنتك الرئيسية، ولا تُرسل مباشرة للإقليم.' 
                                    : 'A workgroup is a sub-entity belonging to a parent Service Committee to execute a specific project or task. Workgroup reports are saved internally as drafts for parent committee review and embedding, and are not submitted directly to RSC.' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step-by-Step for Workgroups --}}
                <div class="row g-4 mb-4">
                    
                    {{-- Step 1 --}}
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 glass-card shadow-sm h-100 rounded-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="badge rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.95rem;">1</span>
                                    <h5 class="fw-bold mb-0">{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول والوصول التلقائي' : 'Login & Automatic Routing' }}</h5>
                                </div>
                                <p class="text-secondary mb-3">
                                    {{ app()->getLocale() === 'ar'
                                        ? 'بمجرد تسجيل دخولك بحساب مجموعة العمل الخاص بك، يقوم النظام بتوجيهك تلقائياً لصفحة تفاصيل مجموعتك (`/workgroup/show/{id}`).'
                                        : 'When you log in with your assigned workgroup account, the system automatically redirects you directly to your workgroup details page (`/workgroup/show/{id}`).' }}
                                </p>
                                <ul class="list-unstyled d-flex flex-column gap-2 small text-secondary mb-0">
                                    <li class="d-flex align-items-center gap-2">
                                        <i class="bi bi-check2-circle text-success"></i>
                                        <span>{{ app()->getLocale() === 'ar' ? 'معاينة اسم ورسالة اللجنة الرئيسية التابع لها' : 'Inspect parent committee details and mandate' }}</span>
                                    </li>
                                    <li class="d-flex align-items-center gap-2">
                                        <i class="bi bi-check2-circle text-success"></i>
                                        <span>{{ app()->getLocale() === 'ar' ? 'متابعة نوع المجموعة: دائمة (Permanent) أو مؤقتة (Temporary)' : 'Verify workgroup type: Permanent or Temporary project' }}</span>
                                    </li>
                                    <li class="d-flex align-items-center gap-2">
                                        <i class="bi bi-check2-circle text-success"></i>
                                        <span>{{ app()->getLocale() === 'ar' ? 'إدارة اجتماعات مجموعة العمل (الدورية أو الافتراضية)' : 'Manage linked workgroup recovery or business meetings' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 glass-card shadow-sm h-100 rounded-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="badge rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.95rem;">2</span>
                                    <h5 class="fw-bold mb-0">{{ app()->getLocale() === 'ar' ? 'إنشاء مسودة تقرير الاجتماع' : 'Drafting the Workgroup Report' }}</h5>
                                </div>
                                <p class="text-secondary mb-3">
                                    {{ app()->getLocale() === 'ar'
                                        ? 'من القائمة الجانبية أو زر الإجراءات، انتقل إلى "تقارير اللجان" واضغط على "إضافة تقرير". يقوم النظام بحصر الاختيار تلقائياً على مجموعة عملك.'
                                        : 'Navigate to Committee Reports and click "Add Report". The system automatically pre-locks the selection to your workgroup.' }}
                                </p>
                                <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.02); border: 1px solid var(--glass-border);">
                                    <div class="small fw-bold text-dark mb-1">{{ app()->getLocale() === 'ar' ? 'الحقول المطلوبة في التقرير:' : 'Key Form Fields:' }}</div>
                                    <div class="small text-secondary">
                                        &bull; <strong>{{ app()->getLocale() === 'ar' ? 'تاريخ الاجتماع:' : 'Meeting Date:' }}</strong> {{ app()->getLocale() === 'ar' ? 'تاريخ انعقاد الاجتماع' : 'Actual session date' }}<br>
                                        &bull; <strong>{{ app()->getLocale() === 'ar' ? 'وصف اليوم:' : 'Day Description:' }}</strong> {{ app()->getLocale() === 'ar' ? 'مثل: السبت الأول من الشهر' : 'e.g. First Tuesday of each month' }}<br>
                                        &bull; <strong>{{ app()->getLocale() === 'ar' ? 'الأقسام الديناميكية:' : 'Dynamic Sections:' }}</strong> {{ app()->getLocale() === 'ar' ? 'عنوان ونص لكل بند أو محور عمل' : 'Headline + detailed content for each project area' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 glass-card shadow-sm h-100 rounded-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="badge rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.95rem;">3</span>
                                    <h5 class="fw-bold mb-0">{{ app()->getLocale() === 'ar' ? 'شواغر المهام والمرفقات' : 'Positions Roster & Attachments' }}</h5>
                                </div>
                                <p class="text-secondary mb-3">
                                    {{ app()->getLocale() === 'ar'
                                        ? 'يمكنك تسجيل قائمة خدام مجموعة العمل وتوثيق شواغر المهام وتواريخ الانتخابات، وإرفاق ما يصل إلى 5 ملفات.'
                                        : 'Record servant roles, vacancy status, election dates, and upload up to 5 supporting documents or spreadsheets.' }}
                                </p>
                                <ul class="list-unstyled d-flex flex-column gap-2 small text-secondary mb-0">
                                    <li class="d-flex align-items-center gap-2">
                                        <i class="bi bi-file-earmark-arrow-up text-primary"></i>
                                        <span>{{ app()->getLocale() === 'ar' ? 'الصيغ المسموحة: PDF، صور JPG/PNG، ومستندات Word/Excel (حتى 5MB للملف)' : 'Allowed formats: PDF, JPG/PNG, Word, and Excel (up to 5MB per file)' }}</span>
                                    </li>
                                    <li class="d-flex align-items-center gap-2">
                                        <i class="bi bi-lock-fill text-warning"></i>
                                        <span>{{ app()->getLocale() === 'ar' ? 'التخزين آمن ومحمي داخل البوابة ولا يُتاح للعامة' : 'Files are securely stored in private non-public directories' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Step 4 --}}
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 glass-card shadow-sm h-100 rounded-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="badge rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.95rem;">4</span>
                                    <h5 class="fw-bold mb-0">{{ app()->getLocale() === 'ar' ? 'الحفظ كمسودة والإشعار التلقائي' : 'Saving Draft & Notification' }}</h5>
                                </div>
                                <p class="text-secondary mb-3">
                                    {{ app()->getLocale() === 'ar'
                                        ? 'يقوم النظام بحفظ تقرير مجموعة العمل كـ "مسودة" (Draft). فور الحفظ، يظهر إشعار تنبيهي فورى في لوحة تحكم لجنتك الرئيسية لمراجعته وتضمينه.'
                                        : 'The portal automatically saves your report as a Draft. Once saved, an instant alert banner appears in your parent committee dashboard.' }}
                                </p>
                                <div class="alert alert-warning border-0 p-2 small mb-0 rounded-3">
                                    <i class="bi bi-shield-exclamation me-1"></i>
                                    {{ app()->getLocale() === 'ar' 
                                        ? 'قاعدة هامة: لا يمكن لمجموعة العمل إرسال التقرير للإقليم مباشرة. اللجنة الرئيسية وحدها هي المسؤولة عن المراجعة والتضمين والإرسال.' 
                                        : 'Rule: Workgroups cannot submit reports to RSC directly. The parent committee is responsible for reviewing, embedding, and submitting.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ========================================================================= --}}
            {{-- TAB 2: PARENT SERVICE COMMITTEES                                         --}}
            {{-- ========================================================================= --}}
            <div class="tab-pane fade" id="committees-content" role="tabpanel" aria-labelledby="committees-tab">
                
                {{-- Intro Card --}}
                <div class="card border-0 glass-card shadow-sm mb-4 rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 48px; height: 48px;">
                                <i class="bi bi-building fs-4"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0" style="color: var(--text-primary);">
                                    {{ app()->getLocale() === 'ar' ? 'دليل لجان الخدمة الرئيسية' : 'Parent Service Committees Guide' }}
                                </h4>
                                <small class="text-secondary">{{ app()->getLocale() === 'ar' ? 'إنشاء وإدارة مجموعات العمل، دورة التضمين، والتقديم للإقليم' : 'Workgroup management, the embedding pipeline, and RSC submission' }}</small>
                            </div>
                        </div>

                        <p class="text-secondary mb-0">
                            {{ app()->getLocale() === 'ar'
                                ? 'تمتلك لجنة الخدمة الرئيسية كامل الصلاحيات الإشرافية والتنظيمية على مجموعات العمل التابعة لها. تقوم اللجنة بمراجعة مسودات مجموعات العمل وتضمينها بضغطة زر واحدة داخل تقرير اللجنة الشهري الموحد قبل إرساله للإقليم.'
                                : 'Parent committees maintain full managerial oversight over child workgroups. Committees review workgroup drafts and embed them with a single click into their monthly official committee report before RSC submission.' }}
                        </p>
                    </div>
                </div>

                {{-- Embedding Pipeline Highlights --}}
                <div class="card border-0 glass-card shadow-sm mb-4 rounded-4 overflow-hidden">
                    <div class="card-header bg-transparent p-4 border-bottom" style="border-color: var(--glass-border) !important;">
                        <h5 class="fw-bold mb-1 text-primary">
                            <i class="bi bi-diagram-3-fill me-2"></i> {{ app()->getLocale() === 'ar' ? 'خطوات دورة التضمين (The Embedding Workflow)' : 'The Embedding Workflow Steps' }}
                        </h5>
                        <small class="text-secondary">{{ app()->getLocale() === 'ar' ? 'كيف يتم دمج تقارير مجموعات العمل ضمن التقرير الرسمي للجنة' : 'How workgroup reports get integrated into the official committee report' }}</small>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12 col-md-4">
                                <div class="p-3 rounded-4 h-100" style="background: rgba(37,99,235,0.04); border: 1px dashed rgba(37,99,235,0.2);">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-primary rounded-pill">1</span>
                                        <h6 class="fw-bold mb-0">{{ app()->getLocale() === 'ar' ? 'تنبيه المسودات الجاهزة' : 'Drafts Notice' }}</h6>
                                    </div>
                                    <p class="small text-secondary mb-0">
                                        {{ app()->getLocale() === 'ar'
                                            ? 'عند وجود مسودة من مجموعة عمل، يظهر شريط تنبيه أصفر في أعلى شاشة التقارير يوضح عدد المسودات بانتظار التضمين مع زر للمعاينة السريعة.'
                                            : 'Whenever child workgroups create drafts, a top yellow banner indicates the count of pending drafts with a quick preview shortcut.' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="p-3 rounded-4 h-100" style="background: rgba(16,185,129,0.04); border: 1px dashed rgba(16,185,129,0.2);">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-success rounded-pill">2</span>
                                        <h6 class="fw-bold mb-0">{{ app()->getLocale() === 'ar' ? 'تحديد خيار التضمين' : 'Check to Embed' }}</h6>
                                    </div>
                                    <p class="small text-secondary mb-0">
                                        {{ app()->getLocale() === 'ar'
                                            ? 'في نموذج إضافة/تعديل تقرير لجنتك، يظهر قسم "تضمين تقارير مجموعات العمل". ضع علامة (✓) على التقارير المراد دمجها لشهر الانعقاد.'
                                            : 'Inside your committee report form, select the active workgroup drafts via the checkboxes to embed them for the reporting cycle.' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="p-3 rounded-4 h-100" style="background: rgba(139,92,246,0.04); border: 1px dashed rgba(139,92,246,0.2);">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge rounded-pill text-white" style="background: #8b5cf6;">3</span>
                                        <h6 class="fw-bold mb-0">{{ app()->getLocale() === 'ar' ? 'التحول إلى "مُضمَّن"' : 'Transition to Embedded' }}</h6>
                                    </div>
                                    <p class="small text-secondary mb-0">
                                        {{ app()->getLocale() === 'ar'
                                            ? 'تتحول حالة تقرير مجموعة العمل فوراً إلى "مُضمَّن" (embedded) وترتبط بتقرير اللجنة، وتُطبع تلقائياً داخل ملف الـ PDF الرسمي.'
                                            : 'The workgroup report transitions to status "embedded", links to the master report, and is automatically compiled into the official PDF.' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Detach / Revert Notice --}}
                        <div class="alert alert-secondary border-0 mt-4 mb-0 p-3 rounded-3 d-flex align-items-center gap-3">
                            <i class="bi bi-arrow-repeat fs-4 text-primary"></i>
                            <div class="small">
                                <strong>{{ app()->getLocale() === 'ar' ? 'إمكانية فك التضمين التلقائي:' : 'Automatic Detach Safety:' }}</strong> 
                                {{ app()->getLocale() === 'ar'
                                    ? 'إذا قمت بتعديل تقرير لجنتك وأزلت علامة التحديد (✓) عن أي تقرير مجموعة عمل، يعيده النظام تلقائياً إلى حالة مسودة مستقلة (draft) دون فقد أي من بياناته أو مرفقاته!'
                                    : 'If you edit your report and uncheck a workgroup draft, the system safely unlinks it and restores its status to "draft" without any data loss.' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Committee Practical Duties --}}
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <div class="card border-0 glass-card shadow-sm h-100 rounded-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold text-dark mb-3">
                                    <i class="bi bi-person-plus-fill text-primary me-2"></i>
                                    {{ app()->getLocale() === 'ar' ? 'إنشاء وإدارة مجموعات العمل' : 'Workgroup Administration' }}
                                </h5>
                                <ul class="list-unstyled d-flex flex-column gap-2 small text-secondary mb-0">
                                    <li>&bull; <strong>{{ app()->getLocale() === 'ar' ? 'التعيين السلس للخدام:' : 'Servant Assignment:' }}</strong> {{ app()->getLocale() === 'ar' ? 'اختيار المستخدم أو بريده الإلكتروني يمنحه تلقائياً دور Workgroups' : 'Selecting an officer or typing their email auto-assigns the Workgroups role' }}</li>
                                    <li>&bull; <strong>{{ app()->getLocale() === 'ar' ? 'مرونة العناوين:' : 'Address Flexibility:' }}</strong> {{ app()->getLocale() === 'ar' ? 'العناوين المادية اختيارية تماماً لمجموعات العمل' : 'Physical addresses are nullable and optional for workgroups' }}</li>
                                    <li>&bull; <strong>{{ app()->getLocale() === 'ar' ? 'تذييل التقرير الافتراضي:' : 'Default Report Footer:' }}</strong> {{ app()->getLocale() === 'ar' ? 'تخصيص نص ختامي يظهر تلقائياً في تقارير مجموعة العمل' : 'Set a default closing footer pre-filled in their reports' }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="card border-0 glass-card shadow-sm h-100 rounded-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold text-dark mb-3">
                                    <i class="bi bi-send-check-fill text-success me-2"></i>
                                    {{ app()->getLocale() === 'ar' ? 'تقديم التقرير واستقبال الملاحظات' : 'Submission & Handling Returns' }}
                                </h5>
                                <ul class="list-unstyled d-flex flex-column gap-2 small text-secondary mb-0">
                                    <li>&bull; <strong>{{ app()->getLocale() === 'ar' ? 'التقديم للإقليم:' : 'Submit to RSC:' }}</strong> {{ app()->getLocale() === 'ar' ? 'عند الحفظ بحالة "مُرسل"، يرسل النظام إشعاراً بريدياً فورياً للإقليم ويقفل التعديل' : 'Saving as "submitted" alerts RSC officers via email and locks the report against edits' }}</li>
                                    <li>&bull; <strong>{{ app()->getLocale() === 'ar' ? 'التقارير المعادة بملاحظات:' : 'Returned Reports:' }}</strong> {{ app()->getLocale() === 'ar' ? 'إذا أعاد الإقليم التقرير، تظهر ملاحظات المراجعة في تنبيه أحمر بارز، وتفتح إمكانية التعديل لتصحيح المطلوب وإعادة الإرسال' : 'If returned, an alert displays the RSC review notes and re-opens editing mode for revision' }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ========================================================================= --}}
            {{-- TAB 3: RSC OFFICERS & SUPER ADMIN                                        --}}
            {{-- ========================================================================= --}}
            <div class="tab-pane fade" id="rsc-content" role="tabpanel" aria-labelledby="rsc-tab">
                
                {{-- Intro Card --}}
                <div class="card border-0 glass-card shadow-sm mb-4 rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 48px; height: 48px; background: #8b5cf6;">
                                <i class="bi bi-shield-check fs-4"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0" style="color: var(--text-primary);">
                                    {{ app()->getLocale() === 'ar' ? 'دليل خدم لجنة خدمة الإقليم والإدارة العليا' : 'RSC Officers & Platform Admin Guide' }}
                                </h4>
                                <small class="text-secondary">{{ app()->getLocale() === 'ar' ? 'طابور المراجعة، الاعتماد والأرشفة السحابية، وسجلات التدقيق' : 'Review queue, approval, cloud archiving, and audit logging' }}</small>
                            </div>
                        </div>

                        <p class="text-secondary mb-0">
                            {{ app()->getLocale() === 'ar'
                                ? 'يختص خدم الإقليم (دور rsc) بمراجعة تقارير لجان الخدمة الرسمية واعتمادها لتوليد وثائق الـ PDF وأرشفتها تلقائياً على خوادم التخزين السحابي Storagebox، أو إرجاعها كمسودة مع تدوين ملاحظات توجيهية.'
                                : 'Regional officers (rsc role) review official committee reports, approve them for automated PDF generation and Hetzner Storagebox archiving, or return them to draft with actionable review notes.' }}
                        </p>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    {{-- RSC Scoping --}}
                    <div class="col-12 col-md-6">
                        <div class="card border-0 glass-card shadow-sm h-100 rounded-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold text-dark mb-3">
                                    <i class="bi bi-diagram-2 text-purple me-2" style="color: #8b5cf6;"></i>
                                    {{ app()->getLocale() === 'ar' ? 'نطاق تمثيل الإقليم ولجنة 83' : 'RSC Committee 83 Scoping' }}
                                </h5>
                                <p class="small text-secondary mb-3">
                                    {{ app()->getLocale() === 'ar'
                                        ? 'وفقاً لمعايير النظام، يمثل حساب ضابط الإقليم (rsc) لجنة خدمة الإقليم (Service Committee ID: 83). لذلك، تقتصر صلاحية ضابط الإقليم في إدارة مجموعات العمل على مجموعات العمل التابعة للإقليم حصراً (مثل مجموعة عمل تكنولوجيا المعلومات IT).'
                                        : 'In compliance with NA Egypt standards, RSC officers represent Committee 83. Their workgroup administration is scoped strictly to Committee 83 workgroups (e.g. IT Workgroup), protecting committee independence.' }}
                                </p>
                                <div class="badge rounded-pill px-3 py-2 text-wrap text-start w-100" style="background: rgba(139,92,246,0.08); color: #7c3aed;">
                                    <i class="bi bi-shield-lock me-1"></i> {{ app()->getLocale() === 'ar' ? 'المدير العام (super admin) وحده يملك الرؤية الشاملة على كافة مجموعات العمل ولجان الإقليم.' : 'Only super admin retains global cross-committee administration.' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Review Queue & Actions --}}
                    <div class="col-12 col-md-6">
                        <div class="card border-0 glass-card shadow-sm h-100 rounded-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold text-dark mb-3">
                                    <i class="bi bi-check2-all text-success me-2"></i>
                                    {{ app()->getLocale() === 'ar' ? 'طابور المراجعة وإجراءات الاعتماد' : 'Review Queue & Approvals' }}
                                </h5>
                                <div class="d-flex flex-column gap-3">
                                    <div class="p-3 rounded-3" style="background: rgba(16,185,129,0.05); border: 1px solid rgba(16,185,129,0.2);">
                                        <div class="fw-bold text-success small mb-1">
                                            <i class="bi bi-file-earmark-pdf"></i> {{ app()->getLocale() === 'ar' ? 'اعتماد ونشر (Approve & Publish):' : 'Approve & Publish:' }}
                                        </div>
                                        <div class="small text-secondary">
                                            {{ app()->getLocale() === 'ar'
                                                ? 'يعتمد التقرير ويستدعي فوراً خدمة الأرشفة السحابية ReportArchiver لتوليد ملف PDF متكامل وحفظه مع المرفقات في مجلد الأرشيف السحابي.'
                                                : 'Transitions report to approved and immediately dispatches ReportArchiver to compile the PDF and store it with attachments in Storagebox.' }}
                                        </div>
                                    </div>

                                    <div class="p-3 rounded-3" style="background: rgba(239,68,68,0.05); border: 1px solid rgba(239,68,68,0.2);">
                                        <div class="fw-bold text-danger small mb-1">
                                            <i class="bi bi-arrow-counterclockwise"></i> {{ app()->getLocale() === 'ar' ? 'إرجاع كمسودة بملاحظات (Return to Draft):' : 'Return to Draft with Notes:' }}
                                        </div>
                                        <div class="small text-secondary">
                                            {{ app()->getLocale() === 'ar'
                                                ? 'يلزم النظام ضابط الإقليم بكتابة ملاحظات توجيهية (Review Notes) تظهر للجنة لتصحيح النواقص أو إرفاق الفواتير وإعادة التقديم.'
                                                : 'Enforces mandatory review notes describing required updates, alerting the committee to amend and resubmit.' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Storagebox & Audit Trail --}}
                <div class="card border-0 glass-card shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-3">
                            <i class="bi bi-hdd-network-fill text-info me-2"></i>
                            {{ app()->getLocale() === 'ar' ? 'أرشيف التخزين السحابي وسجلات التدقيق' : 'Storagebox Archiving & Audit Logs' }}
                        </h5>
                        <div class="row g-4">
                            <div class="col-12 col-lg-6">
                                <div class="small text-secondary">
                                    <strong class="text-dark">{{ app()->getLocale() === 'ar' ? 'هيكل الأرشيف السحابي (Storagebox Hierarchy):' : 'Cloud Directory Hierarchy:' }}</strong><br>
                                    <code>Archives/أجندة إجتماع لجنة خدمة الاقليم/{السنة}/أجندة {الشهر} {السنة}/التقارير الشهرية حتى 10 {الشهر}/{اسم_اللجنة}/</code><br>
                                    {{ app()->getLocale() === 'ar'
                                        ? 'يتم دمج ملفات السحابة التاريخية والتقارير البرمجية في شجرة مجلدات تفاعلية مع إمكانية المعاينة والتنزيل الآمن المشفر.'
                                        : 'Historical cloud files and database records are dynamically merged into an interactive tree with encrypted secure downloads.' }}
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="small text-secondary">
                                    <strong class="text-dark">{{ app()->getLocale() === 'ar' ? 'سجل التدقيق غير القابل للتعديل (Audit Trail):' : 'Immutable Audit Trail:' }}</strong><br>
                                    {{ app()->getLocale() === 'ar'
                                        ? 'يسجل النظام آلياً في جدول committee_report_logs كافة الحركات: (الإنشاء، التعديل، الإرسال، الاعتماد، الإرجاع، والحذف) موثقة بالوقت وهوية الخادم والبيانات المتغيرة.'
                                        : 'Tracks all lifecycle events (created, updated, submitted, approved, returned_to_draft, deleted) with precise timestamps, actor details, and diff metadata.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ========================================================================= --}}
            {{-- TAB 4: SCENARIOS & FAQ                                                   --}}
            {{-- ========================================================================= --}}
            <div class="tab-pane fade" id="scenarios-content" role="tabpanel" aria-labelledby="scenarios-tab">
                
                {{-- Scenarios Header --}}
                <div class="card border-0 glass-card shadow-sm mb-4 rounded-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-2 text-warning">
                            <i class="bi bi-lightning-charge-fill me-2"></i>
                            {{ app()->getLocale() === 'ar' ? 'سيناريوهات عملية واقعية من هيكل الخدمة' : 'Real-World Fellowship Operational Scenarios' }}
                        </h4>
                        <p class="text-secondary mb-0">
                            {{ app()->getLocale() === 'ar'
                                ? 'أمثلة توضيحية خطوة بخطوة لكيفية سير العمل بين مجموعات العمل واللجان وخدم الإقليم في الحالات الشائعة.'
                                : 'Step-by-step practical walkthroughs demonstrating the end-to-end interactions across roles and entities.' }}
                        </p>
                    </div>
                </div>

                {{-- Scenario 1 & 2 Cards --}}
                <div class="row g-4 mb-4">
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 glass-card shadow-sm h-100 rounded-4">
                            <div class="card-body p-4">
                                <div class="badge bg-primary mb-2">{{ app()->getLocale() === 'ar' ? 'السيناريو الأول' : 'Scenario 1' }}</div>
                                <h5 class="fw-bold text-dark mb-2">
                                    {{ app()->getLocale() === 'ar' ? 'مجموعة عمل تكنولوجيا المعلومات (IT) ولجنة الإقليم' : 'IT Workgroup & Regional Committee (83)' }}
                                </h5>
                                <p class="small text-secondary mb-3">
                                    {{ app()->getLocale() === 'ar'
                                        ? 'مجموعة عمل IT تتبع مباشرة لجنة خدمة الإقليم (Committee 83) وتدير البوابة الإلكترونية.'
                                        : 'The IT Workgroup operates directly under RSC Committee 83 to maintain the portal and server infrastructure.' }}
                                </p>
                                <ol class="small text-secondary d-flex flex-column gap-2 mb-0 ps-3">
                                    <li>{{ app()->getLocale() === 'ar' ? 'يجتمع خدام مجموعة عمل IT ويوثقون أعمال الصيانة والتحديثات في تقرير مسودة لشهر مايو.' : 'IT servants document portal updates in their monthly draft report.' }}</li>
                                    <li>{{ app()->getLocale() === 'ar' ? 'يقوم ضابط الإقليم بفتح تقرير لجنة الإقليم 83، فيجد مسودة IT في قسم التضمين، ويضع علامة (✓).' : 'The RSC officer compiles Committee 83 report, notices IT draft, and checks it.' }}</li>
                                    <li>{{ app()->getLocale() === 'ar' ? 'يتم حفظ التقرير وإرساله للإقليم مع دمج إنجازات ومرفقات تكنولوجيا المعلومات في ملف PDF واحد.' : 'The master report is saved and published with IT data bundled seamlessly into the final PDF.' }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card border-0 glass-card shadow-sm h-100 rounded-4">
                            <div class="card-body p-4">
                                <div class="badge bg-success mb-2">{{ app()->getLocale() === 'ar' ? 'السيناريو الثاني' : 'Scenario 2' }}</div>
                                <h5 class="fw-bold text-dark mb-2">
                                    {{ app()->getLocale() === 'ar' ? 'لجنة النشريات ومجموعة عمل الترجمة مع الإرجاع' : 'Literature Committee, PR Workgroup & Return' }}
                                </h5>
                                <p class="small text-secondary mb-3">
                                    {{ app()->getLocale() === 'ar'
                                        ? 'إنشاء مجموعة عمل، تضمين تقريرها، ثم إعادة التقرير من الإقليم لاستيفاء إيصالات المصروفات.'
                                        : 'Workgroup report embedding followed by an RSC return to draft requesting expense receipts.' }}
                                </p>
                                <ol class="small text-secondary d-flex flex-column gap-2 mb-0 ps-3">
                                    <li>{{ app()->getLocale() === 'ar' ? 'أنشأت لجنة النشريات مجموعة عمل مؤقتة لمشروع طباعة، ورفعت مسودة تقريرها.' : 'Literature Committee forms temporary printing workgroup which saves a draft.' }}</li>
                                    <li>{{ app()->getLocale() === 'ar' ? 'قامت لجنة النشريات بتضمين المسودة وإرسال التقرير للإقليم.' : 'Literature Committee embeds the draft and submits to RSC.' }}</li>
                                    <li>{{ app()->getLocale() === 'ar' ? 'أعاد ضابط الإقليم التقرير بملاحظة: "يرجى إرفاق إيصالات الطباعة في المرفقات".' : 'RSC returns with notes: "Please attach printing payment receipts".' }}</li>
                                    <li>{{ app()->getLocale() === 'ar' ? 'دخلت اللجنة، ورفعت ملف الإيصالات عبر المرفقات، وأعادت الإرسال ليعتمده الإقليم فوراً!' : 'Literature Committee uploads the receipts and resubmits for immediate approval.' }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FAQ Section --}}
                <div class="card border-0 glass-card shadow-sm rounded-4">
                    <div class="card-header bg-transparent p-4 border-bottom" style="border-color: var(--glass-border) !important;">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-question-circle-fill text-primary me-2"></i>
                            {{ app()->getLocale() === 'ar' ? 'الأسئلة الشائعة والأخطاء الشائعة (FAQ)' : 'Frequently Asked Questions & Troubleshooting' }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="accordion accordion-flush" id="faqAccordion">
                            
                            {{-- Q1 --}}
                            <div class="accordion-item bg-transparent border-0 mb-3">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button collapsed rounded-3 p-3 fw-bold" style="background: rgba(0,0,0,0.03);" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                        {{ app()->getLocale() === 'ar' ? 'لماذا لا يمكن لخادم مجموعة العمل إرسال تقريره مباشرة للإقليم؟' : 'Why can workgroup servants only save as draft and not submit to RSC?' }}
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary small pt-3">
                                        {{ app()->getLocale() === 'ar'
                                            ? 'وفقاً لمفاهيم الخدمة في NA، مجموعة العمل هي لجنة فرعية مسؤولة أمام لجنتها الرئيسية. اللجنة الرئيسية هي المخولة بمراجعة أعمال المجموعة وتضمينها ضمن تقريرها الرسمي المعتمد قبل عرضه على مؤتمر الإقليم.'
                                            : 'Per NA service concepts, workgroups are accountable to their parent committee. The parent committee is responsible for verifying their sub-entities work and embedding it into their official report before regional presentation.' }}
                                    </div>
                                </div>
                            </div>

                            {{-- Q2 --}}
                            <div class="accordion-item bg-transparent border-0 mb-3">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed rounded-3 p-3 fw-bold" style="background: rgba(0,0,0,0.03);" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        {{ app()->getLocale() === 'ar' ? 'متى تتاح التقارير المعتمدة لممثلي المجموعات وممثلي لجان المناطق؟' : 'When do approved reports become visible to GSRs and Area Service Bodies?' }}
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary small pt-3">
                                        {{ app()->getLocale() === 'ar'
                                            ? 'حفاظاً على نزاهة مداولات الخدمة واكتمال الدورة الشهرية، تصبح التقارير المعتمدة مرئية لحسابات ممثلي المجموعات ولجان المناطق (ServiceBody / gsr) في اليوم العاشر من الشهر التالي لتاريخ انعقاد الاجتماع.'
                                            : 'To protect fellowship integrity and align with the monthly regional schedule, approved reports become visible to restricted consumers (GSRs and Area bodies) on the 10th day of the month following the meeting date.' }}
                                    </div>
                                </div>
                            </div>

                            {{-- Q3 --}}
                            <div class="accordion-item bg-transparent border-0 mb-3">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed rounded-3 p-3 fw-bold" style="background: rgba(0,0,0,0.03);" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        {{ app()->getLocale() === 'ar' ? 'ماذا يحدث لمسودات مجموعات العمل المضمنة إذا تم حذف مسودة تقرير اللجنة؟' : 'What happens to embedded workgroup drafts if the parent report is deleted?' }}
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary small pt-3">
                                        {{ app()->getLocale() === 'ar'
                                            ? 'يقوم النظام بفك الارتباط تلقائياً؛ حيث يُعاد ضبط parent_report_id إلى null وتعود حالة تقارير مجموعات العمل تلقائياً إلى "مسودة" (draft) دون فقد أي نص أو مرفقات!'
                                            : 'The system automatically releases them: parent_report_id is set to null and status reverts back to "draft" without any data loss.' }}
                                    </div>
                                </div>
                            </div>

                            {{-- Q4 --}}
                            <div class="accordion-item bg-transparent border-0">
                                <h2 class="accordion-header" id="headingFour">
                                    <button class="accordion-button collapsed rounded-3 p-3 fw-bold" style="background: rgba(0,0,0,0.03);" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                        {{ app()->getLocale() === 'ar' ? 'هل يمكن لمجموعة العمل تنظيم اجتماعات تعافي أو اجتماعات عمل؟' : 'Can workgroups schedule meetings on the fellowship calendar?' }}
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary small pt-3">
                                        {{ app()->getLocale() === 'ar'
                                            ? 'نعم، يمكن لمجموعة العمل إنشاء وإدارة اجتماعات تابعة لها من خلال صفحة تفاصيل المجموعة، مع دعم كامل لكافة أنماط التكرار الأسبوعية والمواضيع المحددة.'
                                            : 'Yes, workgroups can create and manage their own linked meetings from their details page, with full recurrence patterns and meeting topics.' }}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- Interactive Tab Switcher Script --}}
    <script>
        function switchGuideTab(tabId) {
            const tabEl = document.getElementById(tabId);
            if (tabEl) {
                const tab = new bootstrap.Tab(tabEl);
                tab.show();
                tabEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
    </script>
</x-layout>

