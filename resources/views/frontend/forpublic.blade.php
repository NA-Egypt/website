<x-frontend.layout>
    <x-section-head>{{ __('messages.forpublic') }}</x-section-head>

    <div id="top" class="container py-4" style="direction: rtl; text-align: right; background: linear-gradient(to bottom, #ffffff, #f0f4f8); border-radius: 12px;">
        <div class="mb-5">
            <h5 class="fw-bold mb-4 fade-in fade-in-delay-1">فهرس الصفحة</h5>
            <ul class="list-unstyled mb-5">
                <li class="mb-2 fade-in fade-in-delay-1"><a href="#global" class="text-decoration-none text-primary">معلومات عن الزمالة العالمية</a></li>
                <li class="mb-2 fade-in fade-in-delay-2"><a href="#local" class="text-decoration-none text-primary">معلومات عن الزمالة المحلية</a></li>
                <li class="mb-2 fade-in fade-in-delay-3"><a href="#statistics" class="text-decoration-none text-primary">الإحصائيات العالمية</a></li>
                <li class="mb-2"><a href="#not-do" class="text-decoration-none text-primary">ما لا تقوم به الزمالة</a></li>
                <li class="mb-2"><a href="#meetings" class="text-decoration-none text-primary">الاجتماعات</a></li>
            </ul>
        </div>
        <div id="global" class="mb-5 p-5 rounded shadow-lg border flip-in" style="animation-delay: 0.1s; background-color: #ffffff; border-right: 6px solid #198754;">
            <h4 class="fw-bold mb-4 fade-in fade-in-delay-1"><i class="fas fa-globe-asia text-success me-2 fa-bounce fade-icon fade-icon-delay-1"></i>معلومات عن الزمالة العالمية</h4>
            <p class="mb-4 fade-in fade-in-delay-1">
                إنبثقت زمالة المدمنون المجهولون من برنامج مدمني الخمر في أواخر الأربعينيات، حيث عقدت إجتماعاته الأولى
                في منطقة لوس أنجلوس بكاليفورنيا في الولايات المتحدة الأمريكية في أوائل الخمسينيات. بدأ برنامج المدمنون
                المجهولون كتحرك صغير بأمريكا الشمالية والذي نما إلى أن أصبح من أقدم وأكبر الهيئات من نوعه.
            </p>
            <p class="mb-4 fade-in fade-in-delay-2">
                قامت زمالة المدمنون المجهولون بنشر أول كتاب لها بعنوان “النص الأساسي” الذي ساهم في النمو الهائل للزمالة.
                وفي خلال بضعة أعوام، تكونت مجموعات في البرازيل وكولومبيا وألمانيا والهند وأيرلندا واليابان ونيوزيلاندة
                والمملكة المتحدة (بريطانيا).
            </p>
            <p class="fade-in fade-in-delay-3">
                اليوم، زمالة المدمنين المجهولين مترسخة بشكل جيد في معظم القارات الأمريكية وأوروبا الغربية وأستراليا
                ونيوزيلاندة. المجموعات المتكونة حديثاً ومجتمعات زمالة المدمنين المجهولين متناثرين الآن في شبه قارة
                الهند وأفريقيا وشرق آسيا والشرق الأوسط وأوروبا الشرقية.
            </p>
        </div>

        <div id="local" class="mb-5 p-5 rounded shadow-lg border flip-in" style="animation-delay: 0.2s; background-color: #ffffff; border-right: 6px solid #0d6efd;">
            <h4 class="fw-bold mb-4 fade-in fade-in-delay-1"><i class="fas fa-map-marker-alt text-primary me-2 fa-bounce fade-icon fade-icon-delay-2"></i>معلومات عن الزمالة المحلية</h4>
            <ul class="list-group list-group-flush mb-4" style="direction: rtl; text-align: right;">
                <li class="list-group-item mb-2 fade-in fade-in-delay-1">أول اجتماع تم انعقاده في مدرسة بمصر الجديدة بتاريخ ٢٦ نوفمبر ١٩٨٩.</li>
                <li class="list-group-item mb-2 fade-in fade-in-delay-2">كان عدد الأعضاء ٤.</li>
                <li class="list-group-item fade-in fade-in-delay-3">كان الانتشار بطئ فى البداية ثم أخذت فى النمو تدريجياً حتى أصبح اليوم هناك ٦٥ مجموعة فى ٢٦ محافظات/مدينة يعقدوا اكثر من ٢٠٠ اجتماعاً في الأسبوع.</li>
            </ul>
        </div>

        <div id="statistics" class="mb-5 flip-in" style="animation-delay: 0.3s;">
            <h4 class="fw-bold mb-4 fade-in fade-in-delay-1">الإحصائيات العالمية</h4>
            <p class="fade-in fade-in-delay-1">
              <a href="https://na.org/wp-content/uploads/2024/06/2301-Membership-Survey-English-2018-11-19.pdf" target="_blank" class="text-decoration-underline text-info">
                (متوفرة باللغة الإنجليزية فقط)
              </a>
            </p>
        </div>

        <div id="not-do" class="mb-5 p-5 rounded shadow-lg border flip-in" style="animation-delay: 0.4s; background-color: #ffffff; border-right: 6px solid #007bff;">
            <div class="d-flex align-items-center mb-4">
                <i class="fas fa-ban text-danger fs-4 me-2 fa-bounce fade-icon fade-icon-delay-3"></i>
                <h4 class="fw-bold mb-0 fade-in fade-in-delay-1">زمالة المدمنين المجهولين لا تقوم بما يلي:</h4>
            </div>
            <p class="mb-4 text-muted fade-in fade-in-delay-1">
                إليك بعض الأمثلة على ما لا تقوم به الزمالة، توضيحًا لطبيعة دورها ونطاق خدماتها:
            </p>
            <ul class="list-group list-group-flush" style="direction: rtl; text-align: right;">
                <li class="list-group-item mb-2 fade-in fade-in-delay-1">تلاحق أو تحاول التحكم في أعضائها.</li>
                <li class="list-group-item mb-2 fade-in fade-in-delay-2">تجري التشخيصات والأستشارات الطبية أو النفسية.</li>
                <li class="list-group-item mb-2 fade-in fade-in-delay-3">توفر علاج بالمستشفيات ، عقاقير أو إقامة بمراكز العلاج الطبي والنفسي.</li>
                <li class="list-group-item mb-2">توفر السكن، الغذاء، الملابس، الوظائف، المال أو غيرها من مثل هذه الخدمات.</li>
                <li class="list-group-item mb-2">تقدم المشورة أو النصائح المهنية والعائلية.</li>
                <li class="list-group-item mb-2">ترعى أو تمول زمالة المدمنين المجهولين الأبحاث الخارجية عن الإدمان.</li>
                <li class="list-group-item mb-2">ترتبط بالجمعيات والمؤسسات الاجتماعية (على الرغم من تعاون العديد من الأعضاء ومكاتب خدمات الزمالة معهم).</li>
                <li class="list-group-item mb-2">تقدم الخدمات الدينية.</li>
                <li class="list-group-item mb-2">تدخل في أي جدل حول المخدرات والخمور أو غيرها.</li>
                <li class="list-group-item mb-2">تقبل المال نظير خدماتها أو المساهمات من مصادر غير المدمنين المجهولين.</li>
                <li class="list-group-item">تقدم خطاب توصية لضباط المراقبة، للمحامين، لموظفي المحاكم، للمدارس، للشركات، للمؤسسات الاجتماعية أو لأي منظمة أو مؤسسة أخرى.</li>
            </ul>
        </div>

        <div id="meetings" class="mb-5 p-5 rounded shadow-lg border flip-in" style="animation-delay: 0.5s; background-color: #ffffff; border-right: 6px solid #6f42c1;">
            <h4 class="fw-bold mb-4 fade-in fade-in-delay-1"><i class="fas fa-handshake text-purple me-2 fa-bounce fade-icon fade-icon-delay-1"></i>الاجتماعات</h4>

            <h5 class="fw-bold text-dark mb-3 fade-in fade-in-delay-1">من هم أعضاء زمالة المدمنين المجهولين؟</h5>
            <p class="mb-4 fade-in fade-in-delay-1">
                أي شخص لديه الرغبة في الامتناع عن تعاطي المخدرات يمكنه أن يكون عضواً في زمالة المدمنين المجهولين. فإن العضوية ليست مقتصرة على مدمنين يتعاطون مخدر معين. إن الأشخاص الذين قد يكون لديهم مشكلة مع مخدر ممنوع أو مخدر بوصفة طبية وبالإضافة إلى مادة “الخمر” فهم مرحب بهم في زمالـة “المدمنين المجهولين”. ما تركز عليه زمالة “المدمنين المجهولين” هو التعافي من الإدمان والامتناع الكلي عن جميع المخدرات وليس على مادة مخدرة معينة.
            </p>

            <h5 class="fw-bold text-dark mb-3 fade-in fade-in-delay-1">ما هي الاجتماعات المفتوحة والمغلقة؟</h5>
            <p class="mb-4 fade-in fade-in-delay-2">
                الاجتماعات المغلقة في زمالة “المدمنين المجهولين” هي للمدمنين فقط، أو الناس الذين يعتقدون بأن لديهم مشكلة مع المخدرات. إن الاجتماعات المغلقة تهيأ الجو المناسب للمدمنين لكي يستطيعوا الإحساس بثقة أكبر، وإن هؤلاء المدمنين الذين يحضرون الاجتماعات قادرون على الاتفاق فيما بينهم. إن من يدير الأجتماع عادة يلفت نظر الأعضاء إلى أن هذا الاجتماع مغلق، وذلك لشرح فعالية الاجتماع المغلق، وفى نفس الوقت يوجه غير المدمنين إلى الاجتماع المفتوح.
            </p>
            <p class="mb-4 fade-in fade-in-delay-3">
                الاجتماعات المفتوحة في زمالة “المدمنين المجهولين” هي لكل من يرغب حضور هذه الاجتماعات، بعض المجموعات لديها اجتماعات مفتوحة مرة واحدة في الشهر، وذلك للسماح للأصدقاء غير المدمنين وأقارب الأعضاء المشاركة في الاحتفال بمناسبات التعافي. يجب أن يوضح أثناء الاجتماع بأن مجموعات زمالة “م.م” لا تقبل المساعدات الخارجية من غير الأعضاء.
            </p>
            <p>
                إن بعض المجموعات تستخدم التخطيط الدقيق للاجتماعات المفتوحة وخاصة اجتماعات “المتحدث”، وذلك لإعطاء الفرصة للمجتمع لكي يتسنى له الاطلاع على زمالة المدمنين المجهولين NA عن قرب وإتاحة الفرصة لهم للأسئلة. إنه من الأهمية قراءة التقليد المتعلق بالهوية المجهولة للزمالة “التقليد الحادي عشر”، وإشعار الضيوف بعدم التقاط الصور، أو ذكر أسماء الأعضاء، أو التفاصيـل الشخصية المتعلقة بالأعضاء عند وصف الاجتماعات للآخرين. (إن مقالة الهوية المجهولة توجد في آخر هذا الكتيب). ولمزيد من المعلومات عن الاجتماعات العامة، انظر كتيب دليل “العلاقات العامة” المتواجد لدى مندوب الخدمة في المجموعة، أو بالكتابة إلى مكتب الخدمات العالمي WSO.
            </p>
        </div>
        <div class="text-center mt-5">
            <a href="#top" class="btn btn-outline-primary">
                ↑ الرجوع إلى أعلى
            </a>
        </div>
    </div>
    <style>
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
        html {
            scroll-behavior: smooth;
        }

        #floatingTopBtn {
            position: fixed;
            bottom: 40px;
            right: 20px;
            left: auto;
            z-index: 999;
            display: none;
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            color: white;
            border: none;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            font-size: 24px;
            line-height: 48px;
            text-align: center;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #floatingTopBtn:hover {
            background: linear-gradient(135deg, #6610f2, #0d6efd);
            transform: scale(1.2) rotate(-8deg);
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
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

        @keyframes fadeInIcon {
            0% {
                transform: scale(0.5) rotate(-15deg);
                opacity: 0;
            }
            100% {
                transform: scale(1) rotate(0);
                opacity: 1;
            }
        }

        .fade-icon {
            animation: fadeInIcon 1s ease forwards;
            opacity: 0;
        }

        .fade-icon-delay-1 { animation-delay: 0.3s; }
        .fade-icon-delay-2 { animation-delay: 0.5s; }
        .fade-icon-delay-3 { animation-delay: 0.7s; }
    </style>

    <button id="floatingTopBtn" title="الرجوع للأعلى" onclick="window.scrollTo({top: 0, behavior: 'smooth'});">
        <x-fas-arrow-up style="width:32px; height:32px;"/>
    </button>

    <script>
        const topBtn = document.getElementById('floatingTopBtn');
        window.addEventListener('scroll', () => {
            topBtn.style.display = window.scrollY > 300 ? 'block' : 'none';
        });
    </script>
</x-frontend.layout>