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
      <div class="na-card-top-glow" aria-hidden="true"></div>
      <div class="na-card-content-wrap">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <!-- 3D Layered Newcomer Keytag Icon -->
          <div class="na-card-top-icon na-icon-keytag" aria-hidden="true">
            <svg viewBox="0 0 64 64" class="na-svg-3d" aria-hidden="true">
              <defs>
                <!-- Split Keyring Metallic Gradient -->
                <linearGradient id="silverRingGrad3D" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#FFFFFF"/>
                  <stop offset="25%" stop-color="#CBD5E1"/>
                  <stop offset="50%" stop-color="#64748B"/>
                  <stop offset="75%" stop-color="#94A3B8"/>
                  <stop offset="100%" stop-color="#334155"/>
                </linearGradient>
                <!-- Keytag Body Bevel Gradient -->
                <linearGradient id="keytagBevelGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                  <stop offset="0%" stop-color="#FFFFFF"/>
                  <stop offset="40%" stop-color="#F8FAFC"/>
                  <stop offset="100%" stop-color="#CBD5E1"/>
                </linearGradient>
                <!-- Keytag Front Surface Gradient -->
                <linearGradient id="keytagSurfaceGrad" x1="15%" y1="0%" x2="85%" y2="100%">
                  <stop offset="0%" stop-color="#FFFFFF"/>
                  <stop offset="70%" stop-color="#F8FAFC"/>
                  <stop offset="100%" stop-color="#E2E8F0"/>
                </linearGradient>
                <!-- Teal Emblem Gradient -->
                <linearGradient id="keytagTealGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#0D9488"/>
                  <stop offset="50%" stop-color="#0F766E"/>
                  <stop offset="100%" stop-color="#115E59"/>
                </linearGradient>
                <!-- Keytag Cast Shadow -->
                <filter id="keytagDeepShadow" x="-25%" y="-20%" width="150%" height="150%">
                  <feDropShadow dx="0" dy="5" stdDeviation="4" flood-color="#0F766E" flood-opacity="0.28"/>
                  <feDropShadow dx="0" dy="1" stdDeviation="1.5" flood-color="#000000" flood-opacity="0.12"/>
                </filter>
                <!-- Metallic Eyelet -->
                <radialGradient id="eyeletGrad" cx="40%" cy="35%" r="60%">
                  <stop offset="0%" stop-color="#FFFFFF"/>
                  <stop offset="60%" stop-color="#94A3B8"/>
                  <stop offset="100%" stop-color="#475569"/>
                </radialGradient>
              </defs>

              <!-- Ring Shadow -->
              <ellipse cx="32" cy="11" rx="8" ry="7.5" fill="none" stroke="rgba(15, 118, 110, 0.2)" stroke-width="3" filter="url(#keytagDeepShadow)"/>
              
              <!-- Metallic Split Keyring -->
              <circle cx="32" cy="11" r="7.5" fill="none" stroke="url(#silverRingGrad3D)" stroke-width="2.6"/>
              <circle cx="32" cy="11" r="7.5" fill="none" stroke="#FFFFFF" stroke-width="0.9" stroke-dasharray="4 8" opacity="0.9"/>
              <!-- Ring Split Gap -->
              <line x1="37" y1="8" x2="38.5" y2="10" stroke="#475569" stroke-width="1.8" stroke-linecap="round"/>

              <!-- Keytag Body (3D Beveled Polygon) -->
              <g filter="url(#keytagDeepShadow)">
                <!-- Bevel Edge Backing -->
                <path d="M 17 17 L 47 17 L 51 51 L 32 58 L 13 51 Z" fill="url(#keytagBevelGrad)" stroke="#94A3B8" stroke-width="1.2" stroke-linejoin="round"/>
                <!-- Front Plate -->
                <path d="M 18.5 18.5 L 45.5 18.5 L 49.2 49.8 L 32 56 L 14.8 49.8 Z" fill="url(#keytagSurfaceGrad)" stroke="#E2E8F0" stroke-width="0.8"/>
              </g>

              <!-- Glossy Specular Light Reflection Sweep -->
              <path d="M 18.5 18.5 L 36 18.5 L 20 52 L 14.8 49.8 Z" fill="#FFFFFF" opacity="0.45"/>

              <!-- Metallic Eyelet Grommet -->
              <circle cx="32" cy="22.5" r="3.2" fill="url(#eyeletGrad)" stroke="#475569" stroke-width="0.6"/>
              <circle cx="32" cy="22.5" r="1.7" fill="#0F766E" opacity="0.85"/>
              <circle cx="32" cy="22.5" r="1.7" fill="#CBD5E1"/>

              <!-- NA Fellowship Symbol with 3D Depth -->
              <g id="keytag-na-symbol">
                <!-- Outer Teal Ring -->
                <circle cx="32" cy="35" r="8.2" fill="none" stroke="url(#keytagTealGrad)" stroke-width="1.8"/>
                <circle cx="32" cy="35" r="9.2" fill="none" stroke="#FFFFFF" stroke-width="0.6" opacity="0.8"/>
                <!-- Diamond / Rotated Square -->
                <rect x="26.3" y="29.3" width="11.4" height="11.4" rx="1" fill="none" stroke="url(#keytagTealGrad)" stroke-width="1.6" transform="rotate(45 32 35)"/>
                <!-- Center Core Dot -->
                <circle cx="32" cy="35" r="1.2" fill="url(#keytagTealGrad)"/>
              </g>

              <!-- Embossed WELCOME Typography -->
              <text x="32" y="48.5" text-anchor="middle" font-size="4.2" font-weight="900" fill="url(#keytagTealGrad)" font-family="'Cairo', system-ui, sans-serif" letter-spacing="0.8">WELCOME</text>
              <!-- Micro 1-Day Ribbon Underneath -->
              <path d="M 23 51 L 41 51" stroke="#0D9488" stroke-width="0.75" stroke-linecap="round" stroke-dasharray="1.5 1.5"/>
            </svg>
          </div>

          <!-- Eyebrow Pill Badge -->
          <span class="na-card-eyebrow eyebrow-teal">
            <span class="na-pulsing-dot dot-teal" aria-hidden="true"></span>
            <span>{{ __('messages.tripath_eyebrow_help') ?? 'الخطوة الأولى للتعافي' }}</span>
          </span>
        </div>

        <h2 class="na-card-title">
          {{ __('messages.tripath_help_myself_title') ?? 'أنا أبحث عن مساعدة لنفسي' }}
        </h2>

        <ul class="na-card-bullets">
          <li>
            <span class="na-bullet-icon icon-teal" aria-hidden="true">
              <i class="bi bi-geo-alt-fill"></i>
            </span>
            <span>{{ __('messages.tripath_point_near_meeting') ?? 'أقرب اجتماع لك اليوم في منطقتك أو عبر الإنترنت' }}</span>
          </li>
          <li>
            <span class="na-bullet-icon icon-teal" aria-hidden="true">
              <i class="bi bi-door-open-fill"></i>
            </span>
            <span>{{ __('messages.tripath_point_first_meeting') ?? 'ماذا تتوقع في اجتماعك الأول؟ (حضور مجاني وبلا تسجيل)' }}</span>
          </li>
          <li>
            <span class="na-bullet-icon icon-teal" aria-hidden="true">
              <i class="bi bi-shield-check"></i>
            </span>
            <span>{{ __('messages.tripath_point_talk_member') ?? 'التحدث إلى عضو متعافٍ الآن بكل سرية' }}</span>
          </li>
        </ul>
      </div>

      <!-- Actions Area: Primary CTA Button + Quick Action Chips -->
      <div class="na-card-actions">
        <a href="#meeting-finder-section" onclick="focusMeetingFinderToday(event)" class="na-btn-card na-btn-teal" id="btn-tripath-finder">
          <span>{{ __('messages.tripath_cta_find_meeting_today') ?? 'ابحث عن اجتماع اليوم' }}</span>
          <i class="bi bi-arrow-down-short fs-4 na-btn-motion-icon" aria-hidden="true"></i>
        </a>

        <!-- Quick Helplines Row -->
        <div class="na-card-quicklinks">
          <a href="tel:+201207470004" class="na-quick-chip" title="{{ __('messages.tripath_quick_call') ?? 'اتصال بخطوط المساعدة' }}">
            <i class="bi bi-telephone-fill text-teal" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_quick_call') ?? 'اتصال بخطوط المساعدة' }}</span>
          </a>
          <a href="https://wa.me/201060933888" target="_blank" rel="noopener noreferrer" class="na-quick-chip" title="{{ __('messages.tripath_quick_whatsapp') ?? 'محادثة واتساب سرية' }}">
            <i class="bi bi-whatsapp text-success" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_quick_whatsapp') ?? 'محادثة واتساب' }}</span>
          </a>
        </div>
      </div>
    </div>

    <!-- Path B: For Family & Friends -->
    <div class="na-tripath-card family-help" id="tripath-for-family">
      <div class="na-card-top-glow" aria-hidden="true"></div>
      <div class="na-card-content-wrap">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <!-- 3D Layered Guidebook & Shield Icon -->
          <div class="na-card-top-icon na-icon-guidebook" aria-hidden="true">
            <svg viewBox="0 0 64 64" class="na-svg-3d" aria-hidden="true">
              <defs>
                <!-- Protective Aura / Shield Gradient -->
                <radialGradient id="shieldAuraGrad" cx="50%" cy="40%" r="65%">
                  <stop offset="0%" stop-color="#EFF6FF"/>
                  <stop offset="60%" stop-color="#DBEAFE"/>
                  <stop offset="100%" stop-color="#BFDBFE"/>
                </radialGradient>
                <!-- 3D Book Cover Gradient -->
                <linearGradient id="bookCoverGrad3D" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#1D4ED8"/>
                  <stop offset="35%" stop-color="#2563EB"/>
                  <stop offset="70%" stop-color="#1E40AF"/>
                  <stop offset="100%" stop-color="#1E3A8A"/>
                </linearGradient>
                <!-- Book Pages Gradient -->
                <linearGradient id="bookPagesGrad3D" x1="0%" y1="0%" x2="0%" y2="100%">
                  <stop offset="0%" stop-color="#FFFFFF"/>
                  <stop offset="80%" stop-color="#F8FAFC"/>
                  <stop offset="100%" stop-color="#E2E8F0"/>
                </linearGradient>
                <!-- Golden Ribbon Bookmark Gradient -->
                <linearGradient id="goldRibbonGrad3D" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#FEF08A"/>
                  <stop offset="40%" stop-color="#F59E0B"/>
                  <stop offset="100%" stop-color="#B45309"/>
                </linearGradient>
                <!-- Ambient Book Shadow -->
                <filter id="bookDeepShadow" x="-20%" y="-20%" width="145%" height="145%">
                  <feDropShadow dx="0" dy="5" stdDeviation="4.5" flood-color="#1E40AF" flood-opacity="0.25"/>
                  <feDropShadow dx="0" dy="1.5" stdDeviation="1.5" flood-color="#000000" flood-opacity="0.1"/>
                </filter>
              </defs>

              <!-- Radiant Protective Shield Aura Backdrop -->
              <path d="M 32 6 C 45 6 53 9 53 21 C 53 37 40 49 32 54 C 24 49 11 37 11 21 C 11 9 19 6 32 6 Z" fill="url(#shieldAuraGrad)" stroke="#93C5FD" stroke-width="1.4" stroke-dasharray="3 2" opacity="0.85"/>

              <!-- Open Book 3D Hardcover Base (Cast Shadow) -->
              <g filter="url(#bookDeepShadow)">
                <path d="M 14 18 C 22 15 30 17 32 19 C 34 17 42 15 50 18 L 50 44 C 42 41 34 43 32 45 C 30 43 22 41 14 44 Z" fill="url(#bookCoverGrad3D)" stroke="#1E3A8A" stroke-width="1.2" stroke-linejoin="round"/>
              </g>

              <!-- Spine Center 3D Fold -->
              <line x1="32" y1="19" x2="32" y2="45" stroke="#1E3A8A" stroke-width="2.2" stroke-linecap="round"/>

              <!-- Left Page (Layered 3D Curve) -->
              <path d="M 16 19.5 C 23 17 29.5 18.5 31 19.8 L 31 42.5 C 29.5 41 23 39.5 16 42 Z" fill="url(#bookPagesGrad3D)" stroke="#CBD5E1" stroke-width="0.8"/>
              <!-- Right Page (Layered 3D Curve) -->
              <path d="M 48 19.5 C 41 17 34.5 18.5 33 19.8 L 33 42.5 C 34.5 41 41 39.5 48 42 Z" fill="url(#bookPagesGrad3D)" stroke="#CBD5E1" stroke-width="0.8"/>

              <!-- Mini NA Emblem on Left Page -->
              <circle cx="23.5" cy="29" r="5.2" fill="none" stroke="#2563EB" stroke-width="1.2"/>
              <rect x="20.2" y="25.7" width="6.6" height="6.6" rx="0.6" fill="none" stroke="#2563EB" stroke-width="1" transform="rotate(45 23.5 29)"/>

              <!-- Page Reading Lines on Right Page -->
              <line x1="36" y1="25" x2="44.5" y2="25" stroke="#94A3B8" stroke-width="1.4" stroke-linecap="round"/>
              <line x1="36" y1="29" x2="45" y2="29" stroke="#94A3B8" stroke-width="1.4" stroke-linecap="round"/>
              <line x1="36" y1="33" x2="42" y2="33" stroke="#94A3B8" stroke-width="1.4" stroke-linecap="round"/>
              <line x1="36" y1="37" x2="44" y2="37" stroke="#CBD5E1" stroke-width="1.2" stroke-linecap="round"/>

              <!-- Golden Silk Bookmark Ribbon (Draped with 3D Fold) -->
              <g filter="url(#bookDeepShadow)">
                <path d="M 30.8 18.5 L 33.2 18.5 L 33.2 38 L 32 36 L 30.8 38 Z" fill="url(#goldRibbonGrad3D)"/>
                <!-- Ribbon Specular Highlight Edge -->
                <line x1="31.2" y1="18.5" x2="31.2" y2="37.5" stroke="#FEF08A" stroke-width="0.5"/>
              </g>

              <!-- Subtle Glass Specular Sweep across Cover Corner -->
              <path d="M 14 18 L 24 16 L 19 28 L 14 26 Z" fill="#FFFFFF" opacity="0.35"/>
            </svg>
          </div>

          <!-- Eyebrow Pill Badge -->
          <span class="na-card-eyebrow eyebrow-blue">
            <span class="na-pulsing-dot dot-blue" aria-hidden="true"></span>
            <span>{{ __('messages.tripath_eyebrow_family') ?? 'للأسر والمهتمين' }}</span>
          </span>
        </div>

        <h2 class="na-card-title">
          {{ __('messages.tripath_help_family_title') ?? 'أبحث عن مساعدة لشخص آخر' }}
        </h2>

        <ul class="na-card-bullets">
          <li>
            <span class="na-bullet-icon icon-blue" aria-hidden="true">
              <i class="bi bi-info-circle-fill"></i>
            </span>
            <span>{{ __('messages.tripath_point_what_is_na') ?? 'ما هي زمالة المدمنين المجهولين وما ليس منها؟' }}</span>
          </li>
          <li>
            <span class="na-bullet-icon icon-blue" aria-hidden="true">
              <i class="bi bi-shield-lock-fill"></i>
            </span>
            <span>{{ __('messages.tripath_point_boundaries_anonymity') ?? 'حدود المساعدة والحفاظ على المجهولية التامة' }}</span>
          </li>
          <li>
            <span class="na-bullet-icon icon-blue" aria-hidden="true">
              <i class="bi bi-journal-richtext"></i>
            </span>
            <span>{{ __('messages.tripath_point_public_booklets') ?? 'كتيبات ومعلومات موثقة للأهل والمهتمين' }}</span>
          </li>
        </ul>
      </div>

      <!-- Actions Area: Primary CTA Button + Quick Action Chips -->
      <div class="na-card-actions">
        <a href="{{ route('frontend.forpublic') }}" class="na-btn-card na-btn-blue" id="btn-tripath-family">
          <span>{{ __('messages.tripath_cta_family_guide') ?? 'دليل الأهل والأصدقاء' }}</span>
          <i class="bi bi-arrow-left-short fs-4 na-card-btn-arrow" aria-hidden="true"></i>
        </a>

        <!-- Quick Links Row -->
        <div class="na-card-quicklinks">
          <a href="{{ route('frontend.forpublic') }}" class="na-quick-chip" title="{{ __('messages.tripath_quick_what_is_na') ?? 'ما هي الزمالة؟' }}">
            <i class="bi bi-question-circle-fill text-primary" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_quick_what_is_na') ?? 'ما هي الزمالة؟' }}</span>
          </a>
          <a href="{{ route('frontend.literature') }}" class="na-quick-chip" title="{{ __('messages.tripath_quick_pamphlets') ?? 'كتيبات إعلامية' }}">
            <i class="bi bi-book-half text-primary" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_quick_pamphlets') ?? 'كتيبات إعلامية' }}</span>
          </a>
        </div>
      </div>
    </div>

    <!-- Path C: Current Fellowship Member -->
    <div class="na-tripath-card member-portal" id="tripath-for-member">
      <div class="na-card-top-glow" aria-hidden="true"></div>
      <div class="na-card-content-wrap">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <!-- 3D Layered Fellowship Medallion Icon -->
          <div class="na-card-top-icon na-icon-medallion" aria-hidden="true">
            <svg viewBox="0 0 64 64" class="na-svg-3d" aria-hidden="true">
              <defs>
                <!-- 3D Gold Rim Multi-Stop Gradient -->
                <linearGradient id="goldRimGrad3D" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#FFFBEB"/>
                  <stop offset="20%" stop-color="#FDE68A"/>
                  <stop offset="45%" stop-color="#F59E0B"/>
                  <stop offset="70%" stop-color="#D97706"/>
                  <stop offset="88%" stop-color="#B45309"/>
                  <stop offset="100%" stop-color="#78350F"/>
                </linearGradient>
                <!-- High-Relief Emboss Gold Gradient -->
                <linearGradient id="goldReliefGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#FEF08A"/>
                  <stop offset="35%" stop-color="#F59E0B"/>
                  <stop offset="75%" stop-color="#D97706"/>
                  <stop offset="100%" stop-color="#92400E"/>
                </linearGradient>
                <!-- Midnight Navy Core Gradient -->
                <radialGradient id="navyCoreGrad3D" cx="40%" cy="35%" r="65%">
                  <stop offset="0%" stop-color="#1E3A5F"/>
                  <stop offset="60%" stop-color="#0F243E"/>
                  <stop offset="100%" stop-color="#07111D"/>
                </radialGradient>
                <!-- Medallion Cast Shadow -->
                <filter id="medallionDeepShadow" x="-20%" y="-20%" width="145%" height="145%">
                  <feDropShadow dx="0" dy="5" stdDeviation="4.5" flood-color="#B45309" flood-opacity="0.32"/>
                  <feDropShadow dx="0" dy="1.5" stdDeviation="1.5" flood-color="#000000" flood-opacity="0.15"/>
                </filter>
              </defs>

              <!-- Outer Coin Cast Shadow & Heavy Rim -->
              <g filter="url(#medallionDeepShadow)">
                <!-- Outer Beveled Coin Base -->
                <circle cx="32" cy="32" r="26.5" fill="url(#goldRimGrad3D)" stroke="#92400E" stroke-width="0.8"/>
              </g>

              <!-- Reeded Milled Outer Ridge Guide -->
              <circle cx="32" cy="32" r="25" fill="none" stroke="#FEF08A" stroke-width="0.8" stroke-dasharray="1.5 2"/>
              <circle cx="32" cy="32" r="23.5" fill="none" stroke="#B45309" stroke-width="0.8"/>

              <!-- Recessed Midnight Navy Core Disc (The Fellowship) -->
              <circle cx="32" cy="32" r="22.2" fill="url(#navyCoreGrad3D)" stroke="#FEF08A" stroke-width="0.6"/>

              <!-- Concentric Inner Gold Orbit -->
              <circle cx="32" cy="32" r="17.5" fill="none" stroke="url(#goldReliefGrad)" stroke-width="0.9" stroke-dasharray="2 2.5"/>

              <!-- High-Relief 3D Diamond / Rotated Square (Physical, Mental, Spiritual, Emotional) -->
              <rect x="21" y="21" width="22" height="22" rx="2" fill="none" stroke="url(#goldReliefGrad)" stroke-width="2.4" transform="rotate(45 32 32)"/>
              <rect x="21" y="21" width="22" height="22" rx="2" fill="none" stroke="#FFFBEB" stroke-width="0.6" transform="rotate(45 32 32)" opacity="0.8"/>

              <!-- Official Stylized 'NA' Monogram with 3D Specular Highlight -->
              <text x="32.5" y="37" text-anchor="middle" font-size="12.2" font-weight="900" fill="#07111D" font-family="'Cairo', system-ui, sans-serif" letter-spacing="-0.5" opacity="0.4">NA</text>
              <text x="32" y="36.5" text-anchor="middle" font-size="12" font-weight="900" fill="url(#goldReliefGrad)" font-family="'Cairo', system-ui, sans-serif" letter-spacing="-0.5">NA</text>

              <!-- Four Cardinal Milestone Gold Rivets (Self, Society, Service, God) -->
              <circle cx="32" cy="8.5" r="1.5" fill="#FFFBEB" stroke="#92400E" stroke-width="0.5"/>
              <circle cx="32" cy="55.5" r="1.5" fill="#FFFBEB" stroke="#92400E" stroke-width="0.5"/>
              <circle cx="8.5" cy="32" r="1.5" fill="#FFFBEB" stroke="#92400E" stroke-width="0.5"/>
              <circle cx="55.5" cy="32" r="1.5" fill="#FFFBEB" stroke="#92400E" stroke-width="0.5"/>

              <!-- Top-Right Specular Shimmer Glint -->
              <path d="M 46 14 C 47.5 15.5 48 17 48 17 C 48 17 46.5 17.5 45 19 C 43.5 17.5 42 17 42 17 C 42 17 44.5 15.5 46 14 Z" fill="#FFFFFF" opacity="0.85"/>
            </svg>
          </div>

          <!-- Eyebrow Pill Badge -->
          <span class="na-card-eyebrow eyebrow-amber">
            <span class="na-pulsing-dot dot-amber" aria-hidden="true"></span>
            <span>{{ __('messages.tripath_eyebrow_member') ?? 'مجتمع الزمالة والخدمة' }}</span>
          </span>
        </div>

        <h2 class="na-card-title">
          {{ __('messages.tripath_help_member_title') ?? 'أنا عضو بالمدمنين المجهولين' }}
        </h2>

        <ul class="na-card-bullets">
          <li>
            <span class="na-bullet-icon icon-amber" aria-hidden="true">
              <i class="bi bi-calendar-check-fill"></i>
            </span>
            <span>{{ __('messages.tripath_point_updated_schedule') ?? 'جداول الاجتماعات المُحدثة حضورياً وأونلاين' }}</span>
          </li>
          <li>
            <span class="na-bullet-icon icon-amber" aria-hidden="true">
              <i class="bi bi-diagram-3-fill"></i>
            </span>
            <span>{{ __('messages.tripath_point_service_bodies') ?? 'لجان الخدمة ومجموعات العمل الإقليمية' }}</span>
          </li>
          <li>
            <span class="na-bullet-icon icon-amber" aria-hidden="true">
              <i class="bi bi-star-fill"></i>
            </span>
            <span>{{ __('messages.tripath_point_literature_events') ?? 'مكتبة الأدبيات والفعاليات والمؤتمرات السنوية' }}</span>
          </li>
        </ul>
      </div>

      <!-- Actions Area: Primary CTA Button + Quick Action Chips -->
      <div class="na-card-actions">
        <a href="{{ route('frontend.literature') }}" class="na-btn-card na-btn-navy" id="btn-tripath-member">
          <span>{{ __('messages.tripath_cta_member_portal') ?? 'بوابة الأدبيات والخدمة' }}</span>
          <i class="bi bi-arrow-left-short fs-4 na-card-btn-arrow" aria-hidden="true"></i>
        </a>

        <!-- Quick Links Row -->
        <div class="na-card-quicklinks">
          <a href="{{ route('frontend.comms') }}" class="na-quick-chip" title="{{ __('messages.tripath_quick_service') ?? 'لجان الخدمة' }}">
            <i class="bi bi-people-fill text-warning" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_quick_service') ?? 'لجان الخدمة' }}</span>
          </a>
          <a href="{{ route('frontend.service_bodies.map') }}" class="na-quick-chip" title="{{ __('messages.tripath_quick_map') ?? 'خريطة مناطق الخدمة' }}">
            <i class="bi bi-geo-alt-fill text-warning" aria-hidden="true"></i>
            <span>{{ __('messages.tripath_quick_map') ?? 'خريطة مناطق الخدمة' }}</span>
          </a>
        </div>
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
