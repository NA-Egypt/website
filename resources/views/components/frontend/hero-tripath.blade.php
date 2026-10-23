<section class="na-hero-section text-center" aria-labelledby="hero-heading">
  <!-- Eyebrow Badge with NA Fellowship Emblem -->
  <div class="d-flex justify-content-center">
    <div class="na-hero-badge">
      <svg viewBox="0 0 100 100" style="width: 20px; height: 20px; color: var(--na-teal-700);" aria-hidden="true">
        <circle cx="50" cy="50" r="46" fill="none" stroke="currentColor" stroke-width="6" />
        <rect x="24" y="24" width="52" height="52" rx="3" fill="none" stroke="currentColor" stroke-width="5" transform="rotate(45 50 50)" />
      </svg>
      <span>{{ __('messages.na_fellowship_egypt_badge') ?? 'زمالة المدمنين المجهولين — مصر' }}</span>
    </div>
  </div>

  <!-- Main Headline & Subtitle -->
  <h1 id="hero-heading" class="na-heading-display text-gradient mb-3">
    {{ __('messages.hero_main_headline') ?? 'أهلاً بك في زمالة المدمنين المجهولين - مصر' }}
  </h1>
  <p class="na-lead mx-auto mb-4" style="max-width: 760px;">
    {{ __('messages.hero_main_subtitle') ?? 'أمل جديد، وحياة جديدة في التعافي. لست وحدك بعد اليوم.' }}
  </p>

  <!-- High-Impact Tri-Path Audience Segmenter Deck -->
  <div class="na-tripath-deck text-start">
    <!-- Path A: For the Addict (I Need Help) -->
    <div class="na-tripath-card primary-help" id="tripath-for-myself">
      <div>
        <div class="na-card-top-icon" aria-hidden="true">
          <i class="bi bi-heart-pulse-fill"></i>
        </div>
        <h2 class="na-card-title">
          {{ __('messages.tripath_help_myself_title') ?? 'أنا أبحث عن مساعدة لنفسي' }}
        </h2>
        <ul class="na-card-bullets">
          <li>
            <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_point_near_meeting') ?? 'أقرب اجتماع لك اليوم في منطقتك أو عبر الإنترنت' }}</span>
          </li>
          <li>
            <i class="bi bi-shield-check" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_point_first_meeting') ?? 'ماذا تتوقع في اجتماعك الأول؟ (حضور مجاني وبلا تسجيل)' }}</span>
          </li>
          <li>
            <i class="bi bi-telephone-inbound-fill" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_point_talk_member') ?? 'التحدث إلى عضو متعافٍ الآن بكل سرية' }}</span>
          </li>
        </ul>
      </div>

      <div class="mt-3">
        <a href="#meeting-finder-section" onclick="focusMeetingFinderToday(event)" class="na-btn-primary">
          <span>{{ __('messages.tripath_cta_find_meeting_today') ?? 'ابحث عن اجتماع اليوم' }}</span>
          <i class="bi bi-arrow-down-short fs-4"></i>
        </a>
      </div>
    </div>

    <!-- Path B: For Family & Friends -->
    <div class="na-tripath-card family-help" id="tripath-for-family">
      <div>
        <div class="na-card-top-icon" aria-hidden="true">
          <i class="bi bi-people-fill"></i>
        </div>
        <h2 class="na-card-title">
          {{ __('messages.tripath_help_family_title') ?? 'أبحث عن مساعدة لشخص آخر' }}
        </h2>
        <ul class="na-card-bullets">
          <li>
            <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_point_what_is_na') ?? 'ما هي زمالة المدمنين المجهولين وما ليس منها؟' }}</span>
          </li>
          <li>
            <i class="bi bi-eye-slash-fill" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_point_boundaries_anonymity') ?? 'حدود المساعدة والحفاظ على المجهولية التامة' }}</span>
          </li>
          <li>
            <i class="bi bi-book-fill" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_point_public_booklets') ?? 'كتيبات ومعلومات موثقة للأهل والمهتمين' }}</span>
          </li>
        </ul>
      </div>

      <div class="mt-3">
        <a href="{{ route('frontend.forpublic') }}" class="na-btn-secondary">
          <span>{{ __('messages.tripath_cta_family_guide') ?? 'دليل الأهل والأصدقاء' }}</span>
          <i class="bi bi-arrow-left-short fs-4"></i>
        </a>
      </div>
    </div>

    <!-- Path C: Current Fellowship Member -->
    <div class="na-tripath-card member-portal" id="tripath-for-member">
      <div>
        <div class="na-card-top-icon" aria-hidden="true">
          <i class="bi bi-compass-fill"></i>
        </div>
        <h2 class="na-card-title">
          {{ __('messages.tripath_help_member_title') ?? 'أنا عضو بالزمالة' }}
        </h2>
        <ul class="na-card-bullets">
          <li>
            <i class="bi bi-calendar2-week-fill" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_point_updated_schedule') ?? 'جداول الاجتماعات المُحدثة حضورياً وأونلاين' }}</span>
          </li>
          <li>
            <i class="bi bi-diagram-3-fill" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_point_service_bodies') ?? 'لجان الخدمة ومجموعات العمل الإقليمية' }}</span>
          </li>
          <li>
            <i class="bi bi-journals" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_point_literature_events') ?? 'مكتبة الأدبيات والفعاليات والمؤتمرات السنوية' }}</span>
          </li>
        </ul>
      </div>

      <div class="mt-3">
        <a href="{{ route('frontend.literature') }}" class="na-btn-secondary">
          <span>{{ __('messages.tripath_cta_member_portal') ?? 'بوابة الأعضاء والخدمة' }}</span>
          <i class="bi bi-arrow-left-short fs-4"></i>
        </a>
      </div>
    </div>
  </div>
</section>

<script>
  function focusMeetingFinderToday(e) {
    e.preventDefault();
    const section = document.getElementById('meeting-finder-section');
    if (section) {
      section.scrollIntoView({ behavior: 'smooth', block: 'start' });
      // Trigger today filter in meeting finder if exists
      const daySelect = document.getElementById('meetingFinderDaySelect');
      if (daySelect) {
        // Select current day index
        const todayDayName = "{{ \Carbon\Carbon::now('Africa/Cairo')->locale(app()->getLocale())->dayName }}";
        for (let i = 0; i < daySelect.options.length; i++) {
          if (daySelect.options[i].text.includes(todayDayName) || daySelect.options[i].value.includes(todayDayName)) {
            daySelect.selectedIndex = i;
            break;
          }
        }
        daySelect.dispatchEvent(new Event('change'));
      }
    }
  }
</script>
