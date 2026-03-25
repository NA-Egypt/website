<x-frontend.layout>
    <x-section-head>{{ __('messages.forpublic') }}</x-section-head>

    <div id="top" class="modern-page" style="direction: rtl; text-align: right;">

        {{-- Hero Section --}}
        <div class="hero-section">
            <div class="container">
                <h1 class="hero-title fade-in">{{__('messages.General information about Narcotics Anonymous')}}</h1>
                <p class="hero-subtitle fade-in fade-in-delay-1">{{__('messages.Discover the journey of recovery and hope through our community')}}</p>
            </div>
        </div>

        {{-- Navigation Cards --}}
        <div class="container py-5">
            <div class="nav-grid">
                <a href="#global" class="nav-card card-1 fade-in">
                    <div class="card-icon">
                        <i class="fas fa-globe-asia"></i>
                    </div>
                    <h5>تعريف زمالة المدمنين</h5>
                    <p>اعرف أكثر عن الزمالة العالمية</p>
                </a>
                <a href="#local" class="nav-card card-2 fade-in fade-in-delay-1">
                    <div class="card-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h5>الزمالة المحلية</h5>
                    <p>معلومات عن الزمالة في مصر</p>
                </a>
                <a href="#statistics" class="nav-card card-3 fade-in fade-in-delay-2">
                    <div class="card-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h5>الإحصائيات المحلية</h5>
                    <p>بيانات عن أعضاء الزمالة</p>
                </a>
                <a href="#not-do" class="nav-card card-4 fade-in fade-in-delay-3">
                    <div class="card-icon">
                        <i class="fas fa-ban"></i>
                    </div>
                    <h5>النشرات الإعلامية</h5>
                    <p>معلومات هامة وإرشادات</p>
                </a>
                <a href="#meetings" class="nav-card card-5 fade-in fade-in-delay-4">
                    <div class="card-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h5>الاجتماعات</h5>
                    <p>تفاصيل اجتماعات الزمالة</p>
                </a>
            </div>
        </div>

        {{-- Definition of Narcotics Anonymous--}}
        <div id="global" class="content-section flip-in" style="animation-delay: 0.1s;">
            <div class="container">
                <div class="section-header">
                    <div class="header-icon icon-green">
                        <i class="fas fa-globe-asia"></i>
                    </div>
                    <h2>{{__('messages.Definition of Narcotics Anonymous')}}</h2>
                </div>
                <div class="content-box">
                    <p class="fade-in fade-in-delay-1">
                        زمالة المدمنين المجهولين (NA)؛ زمالة عالمية غير ربحية، ويمكن اعتبارها مجتمع عالمي من المتعافين من إدمان المخدرات، تضم رجال ونساء لديهم مشكلة مع إدمان المواد المخدرة من مختلف الخلفيات الاجتماعية والثقافات واللغات، يشاركون خبراتهم وقوتهم وأملهم مع بعضهم البعض بهدف التعافي من مرض الإدمان ومساعدة الآخرين على التعافي.
                        نحن نعمل معًا من خلال برنامج عملي قائم على المشاركة والدعم المتبادل، بهدف الامتناع التام عن التعاطي وبناء حياة جديدة قائمة على التعافي، مما يتيح الفرصة لكل مدمن أن يصبح عضوًا مسؤولًا ومنتجًا في المجتمع. العضوية في الزمالة مفتوحة لكل من لديه رغبة في التوقف عن التعاطي، دون تمييز على أساس نوع المخدر أو السن أو الجنس أو الخلفية الاجتماعية أو الدينية أو الثقافية.
                        زمالة المدمنين المجهولين (NA) ليست مؤسسة علاجية أو طبية ولا توفر معالجين أو أطباء، ولا تنتمي إلى أي جهة سياسية أو دينية، بل هي مجتمع تطوعي قائم على الدعم المتبادل بين أعضائه، وتدار الزمالة بالكامل من خلال أعضائها وفق مبادئ الخدمة والمسؤولية المشتركة.
                        تتمثل زمالة المدمنين المجهولين بشكل أساسي في اجتماعات تعافي (مفتوحة أو مغلقة) تعقد بشكل دوري حضوريًا أو افتراضيًا عبر الإنترنت، وتوفر بيئة آمنة "خالية من المخدرات" تتيح للأعضاء مشاركة تجاربهم الشخصية في التعافي دون إصدار أحكام أو تقديم توجيهات مباشرة.
                        لا توجد قيادة فردية أو سلطة مركزية في المدمنين المجهولين، بل تعتمد الزمالة على الضمير الجماعي لكل مجموعة والخدمة الناكرة للذات. وتعتمد الاجتماعات على مبدأ المساواة بين جميع الأعضاء، وتشجع على الصدق والأمانة والتفتح والمسؤولية الفردية والجماعية المشتركة. باستخدام برنامج عملي قائم على المبادئ التي تضمنها الخطوات الاثنى عشر والتقاليد الاثنى عشر ومفاهيم الخدمة الاثنى عشر.
                        تنتشر زمالة المدمنين المجهولين (NA) في أكثر من 140 دولة حول العالم، وتعقد عشرات الآلاف من الاجتماعات أسبوعيًا بلغات متعددة في بيئات ثقافية متنوعة، تشمل المدن والقرى والمؤسسات الإصلاحية والمرافق الاجتماعية والعلاجية وغيره من الأماكن العامة. ويعكس هذا الانتشار الواسع عالمية الزمالة وقدرتها على الوصول إلى المدمنين في مختلف البيئات.
                        برنامج زمالة المدمنين المجهولين (NA) هو اسلوب حياة للتعافي وليس مجرد التوقف عن التعاطي، ويهدف إلى تغيير نمط التفكير والسلوك وبناء حياة متوازنة قائمة على الصدق والمسؤولية وتطوير علاقة مع الله (بشكل روحاني كما يعتقد كل عضو) فالبرنامج ليس ديني ولا يتناول المعتقدات الخاصة بالأعضاء.
                    </p>

                    <div class="definition-box fade-in fade-in-delay-2">
                        <h5 class="definition-title">المقصود باسم "زمالة المدمنين المجهولين"</h5>
                        <div class="definition-items">
                            <div class="definition-item">
                                <span class="term">زمالة:</span>
                                <p>تعبر عن روح المشاركة والدعم المتبادل والمساواة بين الأعضاء وعدم وجود تدرج أو تمييز، فكل الأعضاء متساوون ويتعلمون من بعضهم البعض اسلوب حياة جديد خالي من المخدرات.</p>
                            </div>
                            <div class="definition-item">
                                <span class="term">المدمنين:</span>
                                <p>تشير إلى التركيز على الإدمان كمرض واحد دون تمييز بين أنواع المواد المخدرة، مع التركيز على طبيعة التعافي دون التطرق لأي قضية أخرى خارجية.</p>
                            </div>
                            <div class="definition-item">
                                <span class="term">المجهولين:</span>
                                <p>تؤكد على حماية الهوية الشخصية بهدف توفير بيئة آمنة قائمة على الثقة واحترام الخصوصية ووضع المبادئ قبل الأشخاص، فالمجهولية هي الأساس الروحي لتقاليد الزمالة.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Information about the local fellowship--}}
        <div id="local" class="content-section flip-in" style="animation-delay: 0.2s;">
            <div class="container">
                <div class="section-header">
                    <div class="header-icon icon-blue">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h2>{{__('messages.Information about the local fellowship')}}</h2>
                </div>
                <div class="content-box">
                    <div class="timeline">
                        <div class="timeline-item fade-in fade-in-delay-1">
                            <div class="timeline-marker">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="timeline-content">
                                <h5>البداية الأولى</h5>
                                <p>أول اجتماع تم انعقاده في مدرسة بمصر الجديدة بتاريخ ٢٦ نوفمبر ١٩٨٩</p>
                            </div>
                        </div>
                        <div class="timeline-item fade-in fade-in-delay-2">
                            <div class="timeline-marker">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="timeline-content">
                                <h5>الأعضاء الأوائل</h5>
                                <p>كان عدد الأعضاء ٤ فقط في البداية</p>
                            </div>
                        </div>
                        <div class="timeline-item fade-in fade-in-delay-3">
                            <div class="timeline-marker">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="timeline-content">
                                <h5>النمو المستمر</h5>
                                <p>كان الانتشار بطئ فى البداية ثم أخذت فى النمو تدريجياً حتى أصبح اليوم هناك ٦٥ مجموعة فى ٢٦ محافظات/مدينة يعقدوا اكثر من ٢٠٠ اجتماعاً في الأسبوع.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Local statistics (general characteristics of members) --}}
        <div id="statistics" class="content-section flip-in" style="animation-delay: 0.3s;">
            <div class="container">
                <div class="section-header">
                    <div class="header-icon icon-purple">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h2>{{__('messages.Local statistics (general characteristics of members)')}}</h2>
                </div>
                <div class="content-box">
                    <p class="fade-in fade-in-delay-1">
                        تعكس هذه الإحصائيات لمحة عن التعافي من خلال برنامج زمالة المدمنين المجهولين كخيار فعال للتعافي ومورد مجتمعي يبعث الأمل دون تمييز قائم على أي اختلافات من أي نوع. وتعتمد البيانات على مسح شمل 689 عضوًا خلال المؤتمر السنوي بمصر (مايو 2025).
                    </p>
                    <p class="fade-in fade-in-delay-2">
                        ومع الالتزام بمبدأ عدم الكشف عن الهوية الشخصية للأعضاء، يتم عمل مسح دوري يقدم لمحة عامة عن العضوية وتنوعها دون تسجيل بيانات خاصة بالأعضاء، بهدف دعم جهود العلاقات العامة وإيصال رسالة عن التعافي من خلال برنامج الزمالة.
                    </p>

                    <div class="stats-grid fade-in fade-in-delay-3">
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-venus-mars"></i>
                            </div>
                            <p>النوع (Gender)</p>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-birthday-cake"></i>
                            </div>
                            <p>معدل الأعمار (Age)</p>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-hourglass-end"></i>
                            </div>
                            <p>مدة الامتناع (Years Drug – Free)</p>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <p>المخدرات المستخدمة (Drugs Used)</p>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <p>مستوى التعليم (Educational Status)</p>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <p>الحالة الوظيفية (Employment Status)</p>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <p>تحسن جودة الحياة (Quality of Life)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informational brochures (Arabic) --}}
        <div id="not-do" class="content-section flip-in" style="animation-delay: 0.4s;">
            <div class="container">
                <div class="section-header">
                    <div class="header-icon icon-red">
                        <i class="fas fa-ban"></i>
                    </div>
                    <h2>{{__('messages.Informational brochures (Arabic)')}}</h2>
                </div>
                <div class="content-box">
                    <div class="brochure-list">

                        {{-- Who, what, how and why --}}
                        <div class="brochure-item featured fade-in fade-in-delay-1">
                            <div class="brochure-header">
                                <i class="fas fa-star"></i>
                                <h5><a href="#" class="brochure-link">{{__('messages.Who, what, how and why')}}</a></h5>
                            </div>
                            <p>هذه النشرة توضح أن المدمن هو شخص تسيطر المخدرات على حياته، وأن زمالة المدمنين المجهولين هي تجمّع للمدمنين المتعافين الذين يساعدون بعضهم البعض في البقاء ممتنعين</p>
                        </div>

                        {{-- Welcome to Narcotics Anonymous --}}
                        <div class="brochure-item featured fade-in fade-in-delay-1">
                            <div class="brochure-header">
                                <i class="fas fa-star"></i>
                                <h5><a href="#" class="brochure-link">{{__('messages.Welcome to Narcotics Anonymous')}}</a></h5>
                            </div>
                            <p>هي نشرة ترحيبية توضح أساسيات زمالة المدمنين المجهولين وتبين أن الزمالة تتكون من مدمنين يتعافون من الإدمان من خلال دعم بعضهم البعض في اجتماعات منتظمة</p>
                        </div>

                        {{-- Introduction to Narcotics Anonymous Meetings --}}
                        <div class="brochure-item featured fade-in fade-in-delay-1">
                            <div class="brochure-header">
                                <i class="fas fa-star"></i>
                                <h5><a href="#" class="brochure-link">{{__('messages.Introduction to Narcotics Anonymous Meetings')}}</a></h5>
                            </div>
                            <p>القصد من المعلومات المتوفرة في هذا المنشور هي إعطائك مفهوم عن ما نقوم به عندما نجتمع لمشاركة التعافي</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Meetings Section --}}
        <div id="meetings" class="content-section flip-in" style="animation-delay: 0.5s;">
            <div class="container">
                <div class="section-header">
                    <div class="header-icon icon-indigo">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h2>الاجتماعات</h2>
                </div>
                <div class="content-box">
                    <div class="faq-section">
                        <div class="faq-item fade-in fade-in-delay-1">
                            <h5 class="faq-question">
                                <i class="fas fa-question-circle"></i>
                                من هم أعضاء زمالة المدمنين المجهولين؟
                            </h5>
                            <div class="faq-answer">
                                <p>
                                    أي شخص لديه الرغبة في الامتناع عن تعاطي المخدرات يمكنه أن يكون عضواً في زمالة المدمنين المجهولين. فإن العضوية ليست مقتصرة على مدمنين يتعاطون مخدر معين. إن الأشخاص الذين قد يكون لديهم مشكلة مع مخدر ممنوع أو مخدر بوصفة طبية وبالإضافة إلى مادة "الخمر" فهم مرحب بهم في زمالـة "المدمنين المجهولين". ما تركز عليه زمالة "المدمنين المجهولين" هو التعافي من الإدمان والامتناع الكلي عن جميع المخدرات وليس على مادة مخدرة معينة.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item fade-in fade-in-delay-2">
                            <h5 class="faq-question">
                                <i class="fas fa-question-circle"></i>
                                ما هي الاجتماعات المفتوحة والمغلقة؟
                            </h5>
                            <div class="faq-answer">
                                <p>
                                    الاجتماعات المغلقة في زمالة "المدمنين المجهولين" هي للمدمنين فقط، أو الناس الذين يعتقدون بأن لديهم مشكلة مع المخدرات. إن الاجتماعات المغلقة تهيأ الجو المناسب للمدمنين لكي يستطيعوا الإحساس بثقة أكبر، وإن هؤلاء المدمنين الذين يحضرون الاجتماعات قادرون على الاتفاق فيما بينهم. إن من يدير الأجتماع عادة يلفت نظر الأعضاء إلى أن هذا الاجتماع مغلق، وذلك لشرح فعالية الاجتماع المغلق، وفى نفس الوقت يوجه غير المدمنين إلى الاجتماع المفتوح.
                                </p>
                                <p>
                                    الاجتماعات المفتوحة في زمالة "المدمنين المجهولين" هي لكل من يرغب حضور هذه الاجتماعات، بعض المجموعات لديها اجتماعات مفتوحة مرة واحدة في الشهر، وذلك للسماح للأصدقاء غير المدمنين وأقارب الأعضاء المشاركة في الاحتفال بمناسبات التعافي. يجب أن يوضح أثناء الاجتماع بأن مجموعات زمالة "م.م" لا تقبل المساعدات الخارجية من غير الأعضاء.
                                </p>
                                <p>
                                    إن بعض المجموعات تستخدم التخطيط الدقيق للاجتماعات المفتوحة وخاصة اجتماعات "المتحدث"، وذلك لإعطاء الفرصة للمجتمع لكي يتسنى له الاطلاع على زمالة المدمنين المجهولين NA عن قرب وإتاحة الفرصة لهم للأسئلة.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Back to Top Button --}}
        <div class="text-center py-5">
            <a href="#top" class="back-to-top-btn">
                <i class="fas fa-arrow-up"></i> الرجوع إلى الأعلى
            </a>
        </div>
    </div>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .modern-page {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            padding-top: 0;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #3b82f6 0%, #3b82f6 100%);
            color: #ffffff;
            padding: 80px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -30%;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }

        .hero-section .container {
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 700;
            margin-bottom: 15px;
            letter-spacing: -1px;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            opacity: 0.95;
            font-weight: 300;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) translateX(0px); }
            50% { transform: translateY(30px) translateX(20px); }
        }

        /* Navigation Grid */
        .nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 40px 0;
        }

        .nav-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 30px 20px;
            text-align: center;
            text-decoration: none;
            color: #1e293b;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-top: 4px solid transparent;
            position: relative;
        }

        .nav-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .nav-card.card-1 { border-top-color: #10b981; }
        .nav-card.card-2 { border-top-color: #3b82f6; }
        .nav-card.card-3 { border-top-color: #8b5cf6; }
        .nav-card.card-4 { border-top-color: #ef4444; }
        .nav-card.card-5 { border-top-color: #6366f1; }

        .nav-card:hover h5 {
            color: #3b82f6;
        }

        .card-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            background: #f8fafc;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-left: auto;
            margin-right: auto;
        }

        .nav-card.card-1 .card-icon { color: #10b981; }
        .nav-card.card-2 .card-icon { color: #3b82f6; }
        .nav-card.card-3 .card-icon { color: #8b5cf6; }
        .nav-card.card-4 .card-icon { color: #ef4444; }
        .nav-card.card-5 .card-icon { color: #6366f1; }

        .nav-card h5 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
            transition: color 0.3s ease;
        }

        .nav-card p {
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.5;
        }

        /* Content Sections */
        .content-section {
            padding: 60px 20px;
            animation-fill-mode: forwards;
        }

        .content-section:nth-child(odd) {
            background: #f8fafc;
        }

        .content-section:nth-child(even) {
            background: #ffffff;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 40px;
        }

        .header-icon {
            font-size: 3rem;
            width: 90px;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: #ffffff;
            flex-shrink: 0;
        }

        .header-icon.icon-green { background: linear-gradient(135deg, #10b981, #059669); }
        .header-icon.icon-blue { background: linear-gradient(135deg, #3b82f6, #1e40af); }
        .header-icon.icon-purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .header-icon.icon-red { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .header-icon.icon-indigo { background: linear-gradient(135deg, #6366f1, #4f46e5); }

        .section-header h2 {
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            color: #1e293b;
            font-weight: 700;
        }

        .content-box {
            background: #ffffff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .content-box p {
            font-size: 1rem;
            line-height: 1.8;
            color: #1e293b;
            margin-bottom: 20px;
        }

        /* Definition Box */
        .definition-box {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border-left: 4px solid #10b981;
            padding: 30px;
            border-radius: 12px;
            margin-top: 30px;
        }

        .definition-title {
            color: #10b981;
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.2rem;
        }

        .definition-items {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .definition-item {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
        }

        .definition-item .term {
            font-weight: 700;
            color: #10b981;
            font-size: 1.1rem;
            display: block;
            margin-bottom: 10px;
        }

        .definition-item p {
            font-size: 0.95rem;
            color: #1e293b;
            margin-bottom: 0;
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            right: 30px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        }

        .timeline-item {
            margin-bottom: 30px;
            padding-right: 120px;
            position: relative;
        }

        .timeline-marker {
            position: absolute;
            right: 0;
            top: 0;
            width: 65px;
            height: 65px;
            background: #ffffff;
            border: 4px solid #3b82f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #3b82f6;
        }

        .timeline-content {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border-right: 3px solid #3b82f6;
        }

        .timeline-content h5 {
            color: #1e293b;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .timeline-content p {
            margin-bottom: 0;
            font-size: 0.95rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .stat-item {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            padding: 25px 20px;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-5px);
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            color: #ffffff;
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .stat-item p {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0;
        }

        /* Brochure Section */
        .brochure-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .brochure-item {
            padding: 25px;
            border-radius: 12px;
            background: #f8fafc;
            transition: all 0.3s ease;
        }

        .brochure-item.featured {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-left: 4px solid #f97316;
        }

        .brochure-item:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateX(-5px);
        }

        .brochure-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .brochure-header i {
            font-size: 1.5rem;
            color: #f97316;
        }

        .brochure-header h5 {
            margin: 0;
            color: #1e293b;
            font-size: 1.1rem;
        }

        .brochure-link {
            text-decoration: none;
            color: #f97316;
            font-weight: 700;
            transition: color 0.3s ease;
        }

        .brochure-link:hover {
            color: #ef4444;
            text-decoration: underline;
        }

        .brochure-item p {
            margin-bottom: 0;
            color: #1e293b;
        }

        .list-items {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .list-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .item-icon {
            flex-shrink: 0;
            font-size: 1.2rem;
            color: #ef4444;
            margin-top: 2px;
        }

        .list-item span:last-child {
            color: #1e293b;
            line-height: 1.6;
        }

        /* FAQ Section */
        .faq-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .faq-item {
            background: #f8fafc;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .faq-question {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #ffffff;
            padding: 20px 25px;
            margin: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            user-select: none;
        }

        .faq-question i {
            font-size: 1.3rem;
        }

        .faq-answer {
            padding: 25px;
            animation: slideDown 0.3s ease;
        }

        .faq-answer p {
            margin-bottom: 15px;
        }

        .faq-answer p:last-child {
            margin-bottom: 0;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Back to Top Button */
        .back-to-top-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: #ffffff;
            padding: 15px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .back-to-top-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
        }

        /* Animations */
        @keyframes flipIn {
            0% {
                transform: perspective(800px) rotateY(90deg);
                opacity: 0;
            }
            100% {
                transform: perspective(800px) rotateY(0);
                opacity: 1;
            }
        }

        .flip-in {
            animation: flipIn 0.8s ease forwards;
            transform-origin: right center;
            opacity: 0;
            backface-visibility: hidden;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeInUp 1s ease forwards;
            opacity: 0;
        }

        .fade-in-delay-1 { animation-delay: 0.3s; }
        .fade-in-delay-2 { animation-delay: 0.5s; }
        .fade-in-delay-3 { animation-delay: 0.7s; }
        .fade-in-delay-4 { animation-delay: 0.9s; }
        .fade-in-delay-5 { animation-delay: 1.1s; }

        html {
            scroll-behavior: smooth;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .section-header {
                flex-direction: column;
                text-align: center;
            }

            .header-icon {
                width: 80px;
                height: 80px;
            }

            .content-box {
                padding: 25px;
            }

            .nav-grid {
                grid-template-columns: 1fr;
                padding: 20px 0;
            }

            .nav-card {
                padding: 25px 20px;
            }

            .timeline::before {
                right: 20px;
            }

            .timeline-item {
                padding-right: 100px;
            }

            .timeline-marker {
                width: 55px;
                height: 55px;
                font-size: 1.2rem;
            }

            .definition-items {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .hero-section {
                padding: 50px 15px;
            }

            .hero-title {
                font-size: 1.5rem;
            }

            .content-section {
                padding: 30px 15px;
            }

            .content-box {
                padding: 20px;
            }

            .section-header h2 {
                font-size: 1.4rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .timeline-item {
                padding-right: 80px;
            }

            .faq-question {
                padding: 15px 20px;
                font-size: 1rem;
            }
        }
    </style>

    <script>
        // Smooth scroll for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // Scroll to top visibility
        const scrollToTopBtn = document.querySelector('.back-to-top-btn');
        if (scrollToTopBtn) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    scrollToTopBtn.style.display = 'inline-flex';
                } else {
                    scrollToTopBtn.style.display = 'none';
                }
            });
        }

        // Animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });
    </script>
</x-frontend.layout>

