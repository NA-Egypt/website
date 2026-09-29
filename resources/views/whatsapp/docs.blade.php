<x-layout>
    <x-backhead>{{ __('messages.whatsapp_docs') }}</x-backhead>

    <div class="container-fluid px-4 py-3">
        {{-- Header & Subtitle --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: var(--text-primary);">
                    <i class="bi bi-book-half text-primary me-2"></i>{{ __('messages.whatsapp_docs') }}
                </h3>
                <p class="text-muted mb-0 small">
                    {{ __('messages.whatsapp_docs_desc') }}
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('whatsapp.device.status') }}" class="btn btn-outline-success rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-whatsapp"></i>
                    <span>{{ __('messages.whatsapp_device') }}</span>
                </a>
                <a href="{{ route('whatsapp.inbox') }}" class="btn btn-outline-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-chat-dots-fill"></i>
                    <span>{{ __('messages.whatsapp_inbox') }}</span>
                </a>
            </div>
        </div>

        {{-- Nav Tabs --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--card-bg, #ffffff);">
            <div class="card-body p-2">
                <ul class="nav nav-pills nav-fill gap-2" id="docTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill py-2 px-3 fw-semibold small" id="executive-tab" data-bs-toggle="pill" data-bs-target="#executive" type="button" role="tab">
                            <i class="bi bi-shield-check me-1"></i> {{ __('1. Executive & Anonymity') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill py-2 px-3 fw-semibold small" id="volunteer-tab" data-bs-toggle="pill" data-bs-target="#volunteer" type="button" role="tab">
                            <i class="bi bi-headset me-1"></i> {{ __('2. Volunteer User Manual') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill py-2 px-3 fw-semibold small" id="keywords-tab" data-bs-toggle="pill" data-bs-target="#keywords" type="button" role="tab">
                            <i class="bi bi-card-checklist me-1"></i> {{ __('3. Keyword Dictionary') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill py-2 px-3 fw-semibold small" id="dev-tab" data-bs-toggle="pill" data-bs-target="#dev" type="button" role="tab">
                            <i class="bi bi-tools me-1"></i> {{ __('4. Development (egyptna.org)') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill py-2 px-3 fw-semibold small" id="architecture-tab" data-bs-toggle="pill" data-bs-target="#architecture" type="button" role="tab">
                            <i class="bi bi-diagram-3-fill me-1"></i> {{ __('5. Technical Architecture') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill py-2 px-3 fw-semibold small" id="faq-tab" data-bs-toggle="pill" data-bs-target="#faq" type="button" role="tab">
                            <i class="bi bi-question-circle-fill me-1"></i> {{ __('6. Troubleshooting & FAQ') }}
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Tab Contents --}}
        <div class="tab-content" id="docTabsContent">
            {{-- Tab 1: Executive & Traditions --}}
            <div class="tab-pane fade show active" id="executive" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3 text-primary">
                            <i class="bi bi-shield-lock-fill me-2"></i>الرؤية التنفيذية والالتزام بتقاليد الزمالة الإثني عشر
                        </h4>
                        <p class="text-muted leading-relaxed">
                            تم تطوير خدمة واتساب لزمالة المدمنين المجهولين في مصر (NA Egypt WhatsApp Service) كقناة تواصل رقمية فورية ورسمية، تهدف إلى إيصال رسالة الأمل والتعافي لكل مدمن يبحث عن المساعدة، وتيسير الوصول إلى اجتماعات الزمالة، وقراءة الأدبيات اليومية، وتوفير أرقام خط المساعدة.
                        </p>

                        <div class="row g-4 mt-2">
                            <div class="col-md-4">
                                <div class="p-3 rounded-4 bg-light h-100 border">
                                    <h6 class="fw-bold text-dark"><i class="bi bi-eye-slash-fill text-primary me-2"></i>التقليد الحادي عشر (السرية والجاذبية)</h6>
                                    <p class="small text-muted mb-0">
                                        سياستنا في العلاقات العامة تقوم على الجاذبية لا الترويج؛ والسرية الشخصية مصانة دائماً على مستوى الصحافة والإذاعة والإنترنت. لا يتم نشر أو مشاركة أرقام المتصلين، وتقتصر المحادثات على المتطوعين المعتمدين بخط المساعدة.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded-4 bg-light h-100 border">
                                    <h6 class="fw-bold text-dark"><i class="bi bi-shield-fill text-success me-2"></i>التقليد الثاني عشر (الأساس الروحي)</h6>
                                    <p class="small text-muted mb-0">
                                        السرية هي الأساس الروحي لجميع تقاليدنا؛ وتذكرنا دائماً بوضع المبادئ قبل الشخصيات. المنظومة مصممة لضمان حماية هوية المتصلين والأعضاء بشكل كامل في قواعد البيانات.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded-4 bg-light h-100 border">
                                    <h6 class="fw-bold text-dark"><i class="bi bi-link-45deg text-warning me-2"></i>التقليد السادس (عدم التبعية)</h6>
                                    <p class="small text-muted mb-0">
                                        لا تمول مجموعاتنا أو تصادق على أي منشأة ذات صلة أو جهة خارجية؛ لكي لا تحيد بنا مشاكل المال والملكية والجاه عن هدفنا الرئيسي وهو مساعدة المدمن الذي ما زال يعاني.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab 2: Volunteer User Manual --}}
            <div class="tab-pane fade" id="volunteer" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3 text-primary">
                            <i class="bi bi-headset me-2"></i>دليل تشغيل واستخدام خط المساعدة للمتطوعين (Volunteer Manual)
                        </h4>
                        
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-2">1. صندوق المحادثات المباشر (Chat Inbox)</h5>
                            <p class="text-muted small">
                                من خلال القائمة الجانبية <code>خدمة واتساب > صندوق محادثات واتساب</code>، يستطيع المتطوعون المكلفون بنوبة خط المساعدة متابعة الاستفسارات الواردة، وقراءة سجلات المحادثات في الوقت الفعلي.
                            </p>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-2">2. التدخل اليدوي (Live Agent Takeover)</h5>
                            <p class="text-muted small">
                                بمجرد قيام المتطوع بكتابة رد يدوي وإرساله من لوحة التحكم، يقوم النظام تلقائياً بـ:
                            </p>
                            <ul class="small text-muted">
                                <li>تفعيل وضع المتطوع المباشر (Live Volunteer Mode) وتعيين المتطوع الحالي كمسؤول عن المحادثة.</li>
                                <li>إيقاف الرد الآلي مؤقتاً لمدة <strong>30 دقيقة</strong> لإتاحة الفرصة للمتطوع للتواصل الإنساني دون مقاطعة من الروبوت.</li>
                                <li>يمكن للمتطوع إنهاء التدخل يدوياً في أي وقت بالضغط على زر <strong>"استئناف الرد الآلي"</strong>.</li>
                                <li>كما يستطيع المتصل من هاتفه استئناف الرد الآلي بكتابة كلمة: <code>انهاء</code>.</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-2">3. قوالب الردود السريعة (Quick Chips)</h5>
                            <p class="text-muted small">
                                وفرنا شريطاً للردود السريعة أسفل شاشة المحادثة يتيح للمتطوع إدراج رسائل الترحيب الرسمية، وروابط اجتماعات اليوم، وروابط الاتصال الهاتفي بنقرة زر واحدة لتسريع زمن الاستجابة.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab 3: Keyword Dictionary --}}
            <div class="tab-pane fade" id="keywords" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3 text-primary">
                            <i class="bi bi-card-checklist me-2"></i>قاموس الأوامر والكلمات المفتاحية التفاعلية
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-light small">
                                    <tr>
                                        <th style="width: 15%;">الرقم / الخيار</th>
                                        <th style="width: 25%;">الكلمات المفتاحية المدعومة</th>
                                        <th style="width: 40%;">الوظيفة والاستجابة التلقائية</th>
                                        <th style="width: 20%;">التصنيف (Category)</th>
                                    </tr>
                                </thead>
                                <tbody class="small">
                                    <tr>
                                        <td class="fw-bold text-center">1️⃣</td>
                                        <td><code>1</code>, <code>jft</code>, <code>فقط لليوم</code>, <code>قراءة اليوم</code>, <code>today</code></td>
                                        <td>جلب قراءة "فقط لليوم" لتاريخ اليوم بتوقيت القاهرة وتنسيقها بشكل مريح للقراءة مع رابط الموقع.</td>
                                        <td><span class="badge bg-success-subtle text-success">jft</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-center">2️⃣</td>
                                        <td><code>2</code>, <code>اجتماعات</code>, <code>meetings</code>, <code>مواعيد</code></td>
                                        <td>عرض قائمة فرعية لاختيار المحافظة (القاهرة، الجيزة، الإسكندرية، أونلاين) وجلب اجتماعات اليوم الفعلية.</td>
                                        <td><span class="badge bg-primary-subtle text-primary">meetings</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-center">3️⃣</td>
                                        <td><code>3</code>, <code>خط المساعدة</code>, <code>طوارئ</code>, <code>ارقام</code>, <code>helpline</code></td>
                                        <td>عرض أرقام خط المساعدة الرسمية للرجال والسيدات مع خيار طلب التحدث مع متطوع مباشرة.</td>
                                        <td><span class="badge bg-danger-subtle text-danger">helpline</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-center">4️⃣</td>
                                        <td><code>4</code>, <code>فعاليات</code>, <code>مؤتمرات</code>, <code>events</code></td>
                                        <td>جلب الفعاليات والمؤتمرات الإقليمية القادمة من جدول التقويم المحدث.</td>
                                        <td><span class="badge bg-warning-subtle text-warning-emphasis">events</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-center">5️⃣</td>
                                        <td><code>5</code>, <code>نماذج</code>, <code>مطبوعات</code>, <code>استمارات</code>, <code>forms</code></td>
                                        <td>إرسال الروابط المباشرة لطلب التعاون مع الزمالة، وطلب الأدبيات، وقراءة المطبوعات مجاناً.</td>
                                        <td><span class="badge bg-info-subtle text-info">forms</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-center">6️⃣</td>
                                        <td><code>6</code>, <code>سوشيال</code>, <code>social</code>, <code>موقع</code>, <code>روابط</code></td>
                                        <td>مشاركة روابط الموقع الرسمي وصفحة فيسبوك وقناة يوتيوب والبريد الإلكتروني للزمالة.</td>
                                        <td><span class="badge bg-secondary-subtle text-secondary">social</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-center">7️⃣</td>
                                        <td><code>7</code>, <code>اشتراك</code>, <code>subscribe</code></td>
                                        <td>تسجيل رقم المتصل في خدمة البث الصباحي اليومي لرسائل "فقط لليوم" تلقائياً.</td>
                                        <td><span class="badge bg-success">subscription</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-center">—</td>
                                        <td><code>الغاء الاشتراك</code>, <code>unsubscribe</code></td>
                                        <td>إيقاف الاشتراك في رسائل البث اليومي بنقرة واحدة احتراماً لرغبة المستخدم.</td>
                                        <td><span class="badge bg-secondary">subscription</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-center">0️⃣</td>
                                        <td><code>0</code>, <code>متطوع</code>, <code>agent</code>, <code>تحدث مع شخص</code></td>
                                        <td>تفعيل وضع المتطوع وإيقاف الرد الآلي وتنبيه مسؤولي خط المساعدة للرد الفوري.</td>
                                        <td><span class="badge bg-danger">live_agent</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-center">—</td>
                                        <td><code>انهاء</code>, <code>إنهاء المحادثة</code>, <code>bot</code></td>
                                        <td>إلغاء وضع المتطوع وإعادة تفعيل الرد الآلي فوراً مع إظهار القائمة الترحيبية.</td>
                                        <td><span class="badge bg-light text-dark border">menu</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab 4: Development & Testing (egyptna.org) --}}
            <div class="tab-pane fade" id="dev" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3 text-warning-emphasis">
                            <i class="bi bi-tools me-2"></i>دليل الاختبار والتطوير الآمن لبيئة egyptna.org
                        </h4>
                        <div class="alert alert-warning border-0 rounded-4 p-3 mb-4">
                            <strong>{{ __('موقع egyptna.org هو بوابة المعاينة والتطوير الرسمية:') }}</strong>
                            لحماية خصوصية أعضاء الزمالة وتجنب إرسال أي رسائل تجريبية لأرقام حقيقية، تم تجهيز النظام بضوابط أمان مشددة عند العمل على بيئة <code>egyptna.org</code>.
                        </div>

                        <h5 class="fw-bold text-dark mb-2">1. القائمة البيضاء لأرقام المطورين (Whitelist Guardrail)</h5>
                        <p class="text-muted small">
                            في ملف الإعدادات <code>.env</code> على خادم التطوير، يتم تحديد الأرقام المصرح لها بتلقي الردود فقط:
                        </p>
                        <pre class="bg-light p-3 rounded-4 small"><code>WHATSAPP_DEV_MODE=true
WHATSAPP_DEV_WHITELIST=201006979198,201060933888</code></pre>
                        <p class="text-muted small">
                            عندما يكون <code>WHATSAPP_DEV_MODE=true</code>، يقوم خادم التطبيقات بفحص أي رسالة صادرة؛ فإذا لم يكن الرقم المستهدف مدرجاً ضمن القائمة البيضاء، يتم تجاهل الإرسال وتسجيل إشعار في سجلات النظام لمنع أي تداخل مع الأعضاء الحقيقيين.
                        </p>

                        <h5 class="fw-bold text-dark mb-2">2. تجربة الـ Webhook محلياً بواسطة cURL</h5>
                        <p class="text-muted small">
                            يمكن محاكاة رسالة واردة من واتساب إلى نظام لارافيل مباشرة دون الحاجة لهاتف:
                        </p>
                        <pre class="bg-light p-3 rounded-4 small"><code>curl -X POST https://egyptna.org/api/v1/whatsapp/webhook \
  -H "Content-Type: application/json" \
  -d '{
    "sender": "201006979198@s.whatsapp.net",
    "phone": "201006979198",
    "name": "Dev Tester",
    "message": "jft"
  }'</code></pre>
                    </div>
                </div>
            </div>

            {{-- Tab 5: Technical Architecture --}}
            <div class="tab-pane fade" id="architecture" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3 text-primary">
                            <i class="bi bi-diagram-3-fill me-2"></i>البنية المعمارية والربط البرمجي (Technical Architecture)
                        </h4>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-dark">حاوية Docker الدقيقة (Microservice)</h6>
                                <p class="text-muted small">
                                    تعتمد الخدمة على محرك <code>aldinokemal/go-whatsapp-web-multidevice</code> المكتوب بلغة Go (المبني على مكتبة <code>whatsmeow</code>). تعمل الحاوية محلياً على المنفذ <code>127.0.0.1:3000</code> لضمان عدم إمكانية الوصول إليها من خارج الخادم.
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-dark">معالجة الطوابير غير المتزامنة (Database Queues)</h6>
                                <p class="text-muted small">
                                    يستقبل مسار الـ Webhook الطلب ويعيد رمز <code>200 OK</code> خلال أقل من 50 مللي ثانية، ويقوم فوراً بإدراج وظيفة <code>ProcessIncomingWhatsAppMessage</code> في طابور مهام لارافيل لتجنب استهلاك موارد الـ Web server.
                                </p>
                            </div>
                        </div>

                        <h6 class="fw-bold text-dark mb-2">أمر تشغيل الحاوية عبر Docker Compose:</h6>
                        <pre class="bg-light p-3 rounded-4 small"><code>docker compose -f docker-compose.whatsapp.yml up -d</code></pre>
                    </div>
                </div>
            </div>

            {{-- Tab 6: Troubleshooting & FAQ --}}
            <div class="tab-pane fade" id="faq" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3 text-primary">
                            <i class="bi bi-question-circle-fill me-2"></i>الأسئلة الشائعة واستكشاف الأخطاء وإصلاحها (Troubleshooting)
                        </h4>

                        <div class="accordion accordion-flush" id="faqAccordion">
                            <div class="accordion-item border-bottom">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold small" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        س: ماذا أفعل إذا تم فصل جلسة واتساب من الهاتف؟
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted small">
                                        توجه إلى صفحة <code>خدمة واتساب > حالة الاتصال والباركود</code>، واضغط على زر "مسح رمز الاستجابة السريعة (QR Code)". افتح تطبيق واتساب على الهاتف المسؤول، اختر "الأجهزة المرتبطة"، ثم قم بمسح الرمز لتجديد الجلسة.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-bottom">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold small" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        س: كيف يحمي النظام رقم هاتف الزمالة من الحظر عند إرسال البث الجماعي؟
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted small">
                                        تحتوي وظيفة البث الجماعي <code>SendWhatsAppBroadcast</code> على نظام تأخير عشوائي (Anti-Ban Jitter Delay) بمعدل 2 إلى 5 ثوانٍ بين كل رسالة وأخرى، ويتم تقسيم الإرسال على دفعات لتفادي كشف خوارزميات مكافحة البريد المزعج (Spam) في شركة واتساب.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold small" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        س: من هم المستخدمون الذين يستطيعون الرد على المحادثات؟
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted small">
                                        يحق للمستخدمين الذين يحملون دور <code>super admin</code> أو صلاحية <code>reply-whatsapp-messages</code> (مثل متطوعي خط المساعدة) كتابة وإرسال ردود مباشرة على الرسائل الواردة.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
