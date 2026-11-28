<x-frontend.layout
    :title="__('messages.whatsapp_bot_guide_title')"
    :description="__('messages.whatsapp_bot_guide_subtitle')">

  @php
    $isArabic = app()->getLocale() === 'ar';
    $pageDir = $isArabic ? 'rtl' : 'ltr';
    $pageAlign = $isArabic ? 'right' : 'left';
    $waNumber = '201006979198';
    $waDisplay = '+20 100 697 9198';

    $menuItems = [
        [
            'num' => '1',
            'icon' => 'bi-book-half text-primary',
            'title' => $isArabic ? 'قراءة لليوم فقط (Just For Today)' : 'Just For Today (JFT) Reading',
            'desc' => $isArabic
                ? 'احصل على التأمل اليومي الكامل لليوم فقط بتوقيت القاهرة مع رابط القراءة على الموقع.'
                : 'Get today’s full Just For Today daily meditation (Cairo time) with a direct web link.',
            'keywords' => ['1', 'jft', 'فقط لليوم', 'قراءة اليوم', 'today'],
        ],
        [
            'num' => '2',
            'icon' => 'bi-geo-alt-fill text-danger',
            'title' => $isArabic ? 'البحث عن اجتماعات التعافي' : 'Find Recovery Meetings',
            'desc' => $isArabic
                ? 'اختر رقم 2 ثم اختر المحافظة (1 القاهرة، 2 الجيزة، 3 الإسكندرية، 4 أونلاين، 5 باقي المحافظات) لعرض اجتماعات اليوم.'
                : 'Send 2, then pick a region (1 Cairo, 2 Giza, 3 Alexandria, 4 Online, 5 Other Governorates) for today’s schedule.',
            'keywords' => ['2', 'اجتماعات', 'اجتماع', 'meetings', 'مواعيد'],
        ],
        [
            'num' => '3',
            'icon' => 'bi-telephone-fill text-success',
            'title' => $isArabic ? 'أرقام خط المساعدة' : 'Helpline Numbers',
            'desc' => $isArabic
                ? 'عرض أرقام هواتف خط المساعدة الرسمية للتواصل المباشر بسرية تامة.'
                : 'View official confidential helpline phone numbers for immediate support.',
            'keywords' => ['3', 'خط المساعدة', 'طوارئ', 'helpline', 'اتصال'],
        ],
        [
            'num' => '4',
            'icon' => 'bi-calendar-event-fill text-warning',
            'title' => $isArabic ? 'الفعاليات والمؤتمرات' : 'Upcoming Events & Conventions',
            'desc' => $isArabic
                ? 'معرفة أحدث فعاليات ومؤتمرات زمالة المدمنين المجهولين بمصر القادمة.'
                : 'Discover upcoming NA Egypt fellowship events and conventions.',
            'keywords' => ['4', 'فعاليات', 'مؤتمرات', 'events'],
        ],
        [
            'num' => '5',
            'icon' => 'bi-file-earmark-text-fill text-info',
            'title' => $isArabic ? 'النماذج الإلكترونية وطلب الأدبيات' : 'Forms & Literature Requests',
            'desc' => $isArabic
                ? 'روابط سريعة لنماذج التواصل، التعاون، وطلب الأدبيات للمجموعات.'
                : 'Quick links to contact, cooperation, and group literature order forms.',
            'keywords' => ['5', 'نماذج', 'مطبوعات', 'forms'],
        ],
        [
            'num' => '6',
            'icon' => 'bi-globe2 text-secondary',
            'title' => $isArabic ? 'وسائل التواصل والموقع الرسمي' : 'Social Media & Official Website',
            'desc' => $isArabic
                ? 'روابط الموقع الرسمي وصفحات الزمالة الموثقة على منصات التواصل الاجتماعي.'
                : 'Links to the official NA Egypt website and verified social media channels.',
            'keywords' => ['6', 'سوشيال', 'social', 'موقع', 'روابط'],
        ],
        [
            'num' => '7',
            'icon' => 'bi-sunrise-fill text-warning',
            'title' => $isArabic ? 'الاشتراك في رسائل لليوم فقط يومياً' : 'Daily JFT Morning Broadcast',
            'desc' => $isArabic
                ? 'اشترك لتصلك قراءة "لليوم فقط" تلقائياً كل صباح الساعة 7:00 بتوقيت القاهرة. أرسل "الغاء الاشتراك" للإلغاء في أي وقت.'
                : 'Subscribe to receive the daily JFT reading every morning at 7:00 AM Cairo time. Send "unsubscribe" anytime to opt out.',
            'keywords' => ['7', 'اشتراك', 'subscribe', 'الغاء الاشتراك', 'unsubscribe'],
        ],
        [
            'num' => '0',
            'icon' => 'bi-android2 text-success',
            'title' => $isArabic ? 'تحميل تطبيق الأندرويد' : 'Download Android App',
            'desc' => $isArabic
                ? 'احصل على رابط التحميل المباشر لتطبيق زمالة المدمنين المجهولين - مصر للأندرويد (APK).'
                : 'Get the direct APK download link for the official NA Egypt Android app.',
            'keywords' => ['0', 'app', 'android', 'apk', 'تطبيق', 'اندرويد'],
        ],
    ];
  @endphp

  <div class="pb-5" dir="{{ $pageDir }}" style="text-align: {{ $pageAlign }};">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-3" style="--bs-breadcrumb-divider: '';">
      <ol class="breadcrumb py-2 px-3 rounded-pill bg-light border small d-inline-flex align-items-center gap-2 mb-0">
        <li class="breadcrumb-item p-0">
          <a href="{{ route('frontend.home') }}" class="text-decoration-none text-muted d-inline-flex align-items-center gap-1">
            <i class="bi bi-house-door-fill"></i>
            <span>{{ __('messages.Home') }}</span>
          </a>
        </li>
        <li class="text-muted" aria-hidden="true">/</li>
        <li class="breadcrumb-item active text-success fw-bold p-0" aria-current="page">
          {{ __('messages.whatsapp_bot_guide_title') }}
        </li>
      </ol>
    </nav>

    {{-- Hero Section --}}
    <section class="mb-4">
      <div class="p-4 p-md-5 rounded-4 shadow-sm border bg-white position-relative overflow-hidden">
        <div class="row align-items-center g-4">
          <div class="col-12 col-lg-8">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3 bg-success-subtle text-success fw-bold small">
              <i class="bi bi-whatsapp fs-5"></i>
              <span>{{ __('messages.whatsapp_bot_guide_badge') }}</span>
            </div>
            <h1 class="display-6 fw-black text-dark mb-3 lh-sm">
              {{ __('messages.whatsapp_bot_guide_title') }}
            </h1>
            <p class="lead text-secondary mb-4 lh-base fs-6">
              {{ __('messages.whatsapp_bot_guide_subtitle') }}
            </p>
            <div class="d-flex flex-wrap align-items-center gap-3">
              <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer"
                 class="btn btn-success btn-lg rounded-pill px-4 shadow-sm d-inline-flex align-items-center gap-2">
                <i class="bi bi-whatsapp fs-5"></i>
                <span>{{ __('messages.whatsapp_bot_start_chat') }}</span>
              </a>
              <div class="px-3 py-2 rounded-pill bg-light border small text-dark font-monospace" dir="ltr">
                {{ $waDisplay }}
              </div>
            </div>
          </div>
          <div class="col-12 col-lg-4">
            <div class="p-4 rounded-4 bg-light border">
              <div class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock-fill text-success"></i>
                <span>{{ $isArabic ? 'خصوصية وسرية تامة' : 'Complete Anonymity' }}</span>
              </div>
              <p class="text-muted small mb-0 lh-base">
                {{ $isArabic
                    ? 'لا يتطلب البوت أي تسجيل حساب. أرسل أي رسالة أو رقم خدمة للحصول على رد فوري على مدار 24 ساعة، مع دعم كامل للأرقام العربية والإنجليزية (0-9 و ٠-٩).'
                    : 'No account or sign-up required. Send any message or menu number for instant 24/7 automated replies, supporting both English and Arabic-Indic numerals.' }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- Smart Shortcuts Highlight --}}
    <section class="mb-4">
      <div class="row g-3">
        <div class="col-12 col-md-6">
          <div class="card h-100 border-0 shadow-sm rounded-4 bg-white">
            <div class="card-body p-4">
              <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                  <i class="bi bi-search me-1"></i> {{ $isArabic ? 'اختصار ذكي' : 'Smart Shortcut' }}
                </span>
              </div>
              <h2 class="h5 fw-bold text-dark mb-2">
                {{ $isArabic ? 'البحث المباشر باسم المجموعة' : 'Instant Group Name Lookup' }}
              </h2>
              <p class="text-secondary small mb-3 lh-base">
                {{ $isArabic
                    ? 'اكتب اسم أي مجموعة مباشرة في المحادثة (بالعربية أو الإنجليزية مثل: "العباسية" أو "Abbaseya") وسيعرض لك البوت مواعيد اجتماعات هذه المجموعة اليوم فوراً.'
                    : 'Type any group name directly in the chat (in Arabic or English, e.g. "العباسية" or "Abbaseya") and the bot will immediately reply with today’s meetings for that group.' }}
              </p>
              <div class="d-flex flex-wrap gap-2">
                <code class="px-2 py-1 rounded bg-light border text-dark">العباسية</code>
                <code class="px-2 py-1 rounded bg-light border text-dark">Abbaseya</code>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-6">
          <div class="card h-100 border-0 shadow-sm rounded-4 bg-white">
            <div class="card-body p-4">
              <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-3 py-2">
                  <i class="bi bi-camera-video-fill me-1"></i> {{ $isArabic ? 'اجتماعات زووم' : 'Zoom Meetings' }}
                </span>
              </div>
              <h2 class="h5 fw-bold text-dark mb-2">
                {{ $isArabic ? 'أقرب 3 اجتماعات أونلاين (Zoom)' : 'Nearest 3 Online / Zoom Meetings' }}
              </h2>
              <p class="text-secondary small mb-3 lh-base">
                {{ $isArabic
                    ? 'أرسل كلمة "online" أو "zoom" أو "زووم" في أي وقت للحصول على روابط أقرب 3 اجتماعات أونلاين قادمة مرتبة حسب الأقرب زمنياً.'
                    : 'Send "online", "zoom", or "زووم" anytime to get direct join links for the next 3 upcoming Zoom meetings ordered by earliest start time.' }}
              </p>
              <div class="d-flex flex-wrap gap-2">
                <code class="px-2 py-1 rounded bg-light border text-dark">online</code>
                <code class="px-2 py-1 rounded bg-light border text-dark">zoom</code>
                <code class="px-2 py-1 rounded bg-light border text-dark">زووم</code>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- Interactive Menu Reference --}}
    <section class="mb-4">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <h2 class="h4 fw-bold text-dark mb-0">
          <i class="bi bi-list-ol text-success me-2"></i>
          {{ $isArabic ? 'خيارات القائمة الرئيسية والأوامر' : 'Main Menu Options & Keywords' }}
        </h2>
        <span class="text-muted small">
          {{ $isArabic ? 'أرسل "مرحبا" أو أي كلمة غير معروفة لعرض القائمة' : 'Send "hi" or any text to open the main menu' }}
        </span>
      </div>

      <div class="row g-3">
        @foreach ($menuItems as $item)
          <div class="col-12 col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-4 bg-white">
              <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                  <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center flex-shrink-0 fw-bold"
                       style="width: 44px; height: 44px; font-size: 1.1rem;">
                    {{ $item['num'] }}
                  </div>
                  <div class="flex-grow-1">
                    <h3 class="h6 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                      <i class="bi {{ $item['icon'] }}"></i>
                      <span>{{ $item['title'] }}</span>
                    </h3>
                    <p class="text-secondary small mb-2 lh-base">
                      {{ $item['desc'] }}
                    </p>
                    <div class="d-flex flex-wrap gap-1">
                      @foreach ($item['keywords'] as $kw)
                        <code class="px-2 py-0.5 rounded bg-light border text-dark small">{{ $kw }}</code>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </section>

    {{-- Volunteer Support Note --}}
    <section>
      <div class="p-4 rounded-4 border bg-light d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
          <h2 class="h6 fw-bold text-dark mb-1">
            <i class="bi bi-headset text-primary me-1"></i>
            {{ $isArabic ? 'التواصل مع متطوع خط المساعدة' : 'Speaking with a Helpline Volunteer' }}
          </h2>
          <p class="text-muted small mb-0 lh-base">
            {{ $isArabic
                ? 'عند الحاجة للمساعدة البشرية المباشرة، يمكن لمتطوع خط المساعدة الانضمام للمحادثة وإيقاف الرد الآلي مؤقتاً حتى انتهاء المحادثة، ثم إعادة تفعيل الرد الآلي.'
                : 'When personal support is needed, a helpline volunteer can join the conversation and pause automated replies until the conversation is finished.' }}
          </p>
        </div>
        <a href="{{ route('contactus.create') }}" class="btn btn-outline-primary rounded-pill px-4 flex-shrink-0">
          {{ __('messages.contactus') }}
        </a>
      </div>
    </section>
  </div>
</x-frontend.layout>
