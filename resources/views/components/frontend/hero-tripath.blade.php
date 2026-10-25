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
    {{ __('messages.hero_main_subtitle') ?? 'حياة جديدة في التعافي. لست وحدك بعد اليوم.' }}
  </p>

  <!-- High-Impact Tri-Path Audience Segmenter Deck -->
  <div class="na-tripath-deck text-start">
    <!-- Path A: For the Addict (I Need Help) -->
    <div class="na-tripath-card primary-help" id="tripath-for-myself">
      <div>
        <div class="na-card-top-icon" aria-hidden="true">
          <svg viewBox="0 0 44 44" class="na-svg-newcomer-keytag" aria-hidden="true">
            <defs>
              <linearGradient id="silverRingGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#E2E8F0"/>
                <stop offset="50%" stop-color="#94A3B8"/>
                <stop offset="100%" stop-color="#CBD5E1"/>
              </linearGradient>
              <linearGradient id="keytagWhiteGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#FFFFFF"/>
                <stop offset="65%" stop-color="#FFFFFF"/>
                <stop offset="100%" stop-color="#F1F5F9"/>
              </linearGradient>
              <filter id="keytagShadow" x="-15%" y="-15%" width="130%" height="130%">
                <feDropShadow dx="0" dy="1.5" stdDeviation="1.5" flood-color="#0F766E" flood-opacity="0.18"/>
              </filter>
            </defs>
            <!-- Metallic Keyring -->
            <circle cx="22" cy="7.5" r="4.2" fill="none" stroke="url(#silverRingGrad)" stroke-width="1.8"/>
            <circle cx="22" cy="7.5" r="4.2" fill="none" stroke="#FFFFFF" stroke-width="0.8" stroke-dasharray="3 7"/>
            <!-- Keytag Body -->
            <path d="M 12 11.5 L 32 11.5 L 35 36.5 L 22 41 L 9 36.5 Z" fill="url(#keytagWhiteGrad)" stroke="#94A3B8" stroke-width="1.2" stroke-linejoin="round" filter="url(#keytagShadow)"/>
            <!-- Top Hole -->
            <circle cx="22" cy="14.5" r="1.8" fill="#E2E8F0" stroke="#64748B" stroke-width="0.8"/>
            <!-- NA Circle & Square Symbol -->
            <circle cx="22" cy="24.5" r="6.2" fill="none" stroke="#0F766E" stroke-width="1.2"/>
            <rect x="17.7" y="20.2" width="8.6" height="8.6" fill="none" stroke="#0F766E" stroke-width="1.1" transform="rotate(45 22 24.5)"/>
            <!-- WELCOME Text -->
            <text x="22" y="34.8" text-anchor="middle" font-size="3.5" font-weight="900" fill="#0F766E" font-family="'Cairo', system-ui, sans-serif" letter-spacing="0.4">WELCOME</text>
          </svg>
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
            <i class="bi bi-door-open-fill" aria-hidden="true"></i>
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
          <svg viewBox="0 0 44 44" class="na-svg-family-guide" aria-hidden="true">
            <defs>
              <linearGradient id="bookCoverGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#1D4ED8"/>
                <stop offset="100%" stop-color="#1E40AF"/>
              </linearGradient>
              <linearGradient id="bookPageGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stop-color="#FFFFFF"/>
                <stop offset="100%" stop-color="#F1F5F9"/>
              </linearGradient>
              <linearGradient id="goldRibbonGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stop-color="#FBBF24"/>
                <stop offset="100%" stop-color="#D97706"/>
              </linearGradient>
              <filter id="bookShadow" x="-15%" y="-15%" width="130%" height="130%">
                <feDropShadow dx="0" dy="2" stdDeviation="2" flood-color="#1D4ED8" flood-opacity="0.22"/>
              </filter>
            </defs>
            <!-- Background Protective Shield (Subtle Anonymity & Care) -->
            <path d="M 22 5 C 31 5 36 7 36 15 C 36 26 27 34.5 22 38 C 17 34.5 8 26 8 15 C 8 7 13 5 22 5 Z" fill="#EFF6FF" stroke="#93C5FD" stroke-width="1.2" stroke-dasharray="2.5 1.5"/>
            <!-- Open Guidebook Body (Cover Backing) -->
            <path d="M 10 14 C 16 12 21 13.5 22 14.5 C 23 13.5 28 12 34 14 L 34 31 C 28 29 23 30.5 22 31.5 C 21 30.5 16 29 10 31 Z" fill="url(#bookCoverGrad)" filter="url(#bookShadow)"/>
            <!-- Left Page -->
            <path d="M 11.5 14.8 C 16.5 13.2 20.8 14.5 21.5 15.2 L 21.5 30.5 C 20.8 29.8 16.5 28.5 11.5 30 Z" fill="url(#bookPageGrad)" stroke="#CBD5E1" stroke-width="0.6"/>
            <!-- Right Page -->
            <path d="M 32.5 14.8 C 27.5 13.2 23.2 14.5 22.5 15.2 L 22.5 30.5 C 23.2 29.8 27.5 28.5 32.5 30 Z" fill="url(#bookPageGrad)" stroke="#CBD5E1" stroke-width="0.6"/>
            <!-- Page Content Lines on Right Page -->
            <line x1="24.5" y1="18.5" x2="30.5" y2="18.5" stroke="#94A3B8" stroke-width="1" stroke-linecap="round"/>
            <line x1="24.5" y1="21.5" x2="30.5" y2="21.5" stroke="#94A3B8" stroke-width="1" stroke-linecap="round"/>
            <line x1="24.5" y1="24.5" x2="28.5" y2="24.5" stroke="#94A3B8" stroke-width="1" stroke-linecap="round"/>
            <!-- Mini NA Symbol on Left Page -->
            <circle cx="16.5" cy="22.5" r="4" fill="none" stroke="#2563EB" stroke-width="0.9"/>
            <rect x="14" y="20" width="5" height="5" fill="none" stroke="#2563EB" stroke-width="0.8" transform="rotate(45 16.5 22.5)"/>
            <!-- Golden Bookmark Ribbon Hanging Down from Spine -->
            <path d="M 21.2 14 L 22.8 14 L 22.8 27 L 22 25.5 L 21.2 27 Z" fill="url(#goldRibbonGrad)"/>
          </svg>
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
            <i class="bi bi-shield-shaded" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_point_boundaries_anonymity') ?? 'حدود المساعدة والحفاظ على المجهولية التامة' }}</span>
          </li>
          <li>
            <i class="bi bi-book-half" aria-hidden="true"></i>
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
          <svg viewBox="0 0 44 44" class="na-svg-official-symbol" aria-hidden="true">
            <defs>
              <linearGradient id="naGoldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#FDE68A"/>
                <stop offset="35%" stop-color="#F59E0B"/>
                <stop offset="70%" stop-color="#D97706"/>
                <stop offset="100%" stop-color="#B45309"/>
              </linearGradient>
              <linearGradient id="naNavyGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#1E3A5F"/>
                <stop offset="60%" stop-color="#0F243E"/>
                <stop offset="100%" stop-color="#0A192C"/>
              </linearGradient>
              <filter id="symbolShadow" x="-15%" y="-15%" width="130%" height="130%">
                <feDropShadow dx="0" dy="1.5" stdDeviation="1.5" flood-color="#0F172A" flood-opacity="0.25"/>
              </filter>
            </defs>
            <!-- Outer Gold Rim -->
            <circle cx="22" cy="22" r="18.5" fill="none" stroke="url(#naGoldGrad)" stroke-width="1.8" filter="url(#symbolShadow)"/>
            <!-- Inner Royal Navy Disc (The Universal Fellowship) -->
            <circle cx="22" cy="22" r="16.5" fill="url(#naNavyGrad)"/>
            <!-- Concentric Inner Circle Guide -->
            <circle cx="22" cy="22" r="13" fill="none" stroke="url(#naGoldGrad)" stroke-width="0.8" stroke-dasharray="1.5 2"/>
            <!-- The 4 Dimensions: Physical, Mental, Spiritual, Emotional (Diamond / Square) -->
            <rect x="14" y="14" width="16" height="16" rx="1.5" fill="none" stroke="url(#naGoldGrad)" stroke-width="1.8" transform="rotate(45 22 22)"/>
            <!-- Official Stylized 'NA' Monogram -->
            <text x="22" y="25.5" text-anchor="middle" font-size="8.8" font-weight="900" fill="url(#naGoldGrad)" font-family="'Cairo', system-ui, sans-serif" letter-spacing="-0.3">NA</text>
            <!-- Four Cardinal Milestones of Recovery -->
            <circle cx="22" cy="5.5" r="1.1" fill="#FDE68A"/>
            <circle cx="22" cy="38.5" r="1.1" fill="#FDE68A"/>
            <circle cx="5.5" cy="22" r="1.1" fill="#FDE68A"/>
            <circle cx="38.5" cy="22" r="1.1" fill="#FDE68A"/>
          </svg>
        </div>
        <h2 class="na-card-title">
          {{ __('messages.tripath_help_member_title') ?? 'أنا عضو بالمدمنين المجهولين' }}
        </h2>
        <ul class="na-card-bullets">
          <li>
            <i class="bi bi-calendar-check-fill" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_point_updated_schedule') ?? 'جداول الاجتماعات المُحدثة حضورياً وأونلاين' }}</span>
          </li>
          <li>
            <i class="bi bi-diagram-3-fill" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_point_service_bodies') ?? 'لجان الخدمة ومجموعات العمل الإقليمية' }}</span>
          </li>
          <li>
            <i class="bi bi-bookmark-star-fill" aria-hidden="true"></i>
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
