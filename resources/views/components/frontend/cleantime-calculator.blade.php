@props([
    'isStandalone' => false
])

<section id="cleantime-section" class="na-cleantime-wrapper mb-5" aria-labelledby="cleantime-heading" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
  <!-- Confetti Canvas for Celebratory Moments -->
  <canvas id="naConfettiCanvas" class="na-confetti-canvas" aria-hidden="true"></canvas>

  <div class="na-cleantime-card">
    <!-- Card Header / Title -->
    <div class="na-cleantime-header text-center mb-4">
      <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-2 na-badge-pill">
        <i class="bi bi-award-fill text-warning fs-5"></i>
        <span class="fw-bold">{{ __('messages.clean_time_calculator_badge') }}</span>
      </div>
      <h2 id="cleantime-heading" class="na-title mb-2">
        {{ __('messages.calculate_clean_time_title') }}
      </h2>
      <p class="text-muted small mx-auto na-subtitle mb-0">
        {{ __('messages.clean_time_subtitle') }}
      </p>
    </div>

    <!-- Date Input Card -->
    <div class="na-date-input-panel mb-4">
      <div class="row align-items-center g-3">
        <div class="col-12 col-md-5">
          <label class="form-label fw-bold small text-dark d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-calendar-heart-fill text-danger fs-5"></i>
            <span>{{ __('messages.clean_date_input') }}</span>
          </label>
          <p class="text-muted x-small mb-0">
            {{ app()->getLocale() === 'ar' ? 'حدد اليوم والشهر والسنة لأول يوم امتناع تام عن التعاطي' : 'Select Day, Month, and Year of your first day clean' }}
          </p>
        </div>

        <div class="col-12 col-md-7">
          <!-- Segmented Day / Month / Year Dropdowns + Native Sync -->
          <div class="row g-2">
            <!-- Day -->
            <div class="col-12 col-sm-4">
              <label for="ctDaySelect" class="visually-hidden">{{ __('messages.day') }}</label>
              <div class="input-group input-group-sm ct-date-input-group">
                <span class="input-group-text bg-light text-muted"><i class="bi bi-calendar3"></i></span>
                <select id="ctDaySelect" class="form-select form-select-sm fw-bold" aria-label="{{ __('messages.day') }}" onchange="onSegmentedDateChange()">
                  <option value="">{{ __('messages.day') }}</option>
                  @for ($d = 1; $d <= 31; $d++)
                    <option value="{{ $d }}">{{ $d }}</option>
                  @endfor
                </select>
              </div>
            </div>

            <!-- Month -->
            <div class="col-12 col-sm-4">
              <label for="ctMonthSelect" class="visually-hidden">{{ __('messages.month') }}</label>
              <select id="ctMonthSelect" class="form-select form-select-sm fw-bold" aria-label="{{ __('messages.month') }}" onchange="onSegmentedDateChange()">
                <option value="">{{ __('messages.month') }}</option>
                @php
                  $monthNamesAr = [1 => '1 - يناير', 2 => '2 - فبراير', 3 => '3 - مارس', 4 => '4 - أبريل', 5 => '5 - مايو', 6 => '6 - يونيو', 7 => '7 - يوليو', 8 => '8 - أغسطس', 9 => '9 - سبتمبر', 10 => '10 - أكتوبر', 11 => '11 - نوفمبر', 12 => '12 - ديسمبر'];
                  $monthNamesEn = [1 => '1 - Jan', 2 => '2 - Feb', 3 => '3 - Mar', 4 => '4 - Apr', 5 => '5 - May', 6 => '6 - Jun', 7 => '7 - Jul', 8 => '8 - Aug', 9 => '9 - Sep', 10 => '10 - Oct', 11 => '11 - Nov', 12 => '12 - Dec'];
                  $monthList = app()->getLocale() === 'ar' ? $monthNamesAr : $monthNamesEn;
                @endphp
                @foreach ($monthList as $num => $mName)
                  <option value="{{ $num }}">{{ $mName }}</option>
                @endforeach
              </select>
            </div>

            <!-- Year -->
            <div class="col-12 col-sm-4">
              <label for="ctYearSelect" class="visually-hidden">{{ __('messages.year') }}</label>
              <select id="ctYearSelect" class="form-select form-select-sm fw-bold" aria-label="{{ __('messages.year') }}" onchange="onSegmentedDateChange()">
                <option value="">{{ __('messages.year') }}</option>
                @php
                  $currentYear = (int)date('Y');
                @endphp
                @for ($y = $currentYear; $y >= 1953; $y--)
                  <option value="{{ $y }}">{{ $y }}</option>
                @endfor
              </select>
            </div>
          </div>

          <!-- Hidden Native Date Input for Synchronization -->
          <input type="date" id="ctHiddenNativeDate" class="d-none" max="{{ date('Y-m-d') }}" onchange="onNativeDateChange(this.value)">

          <!-- Quick Action Shortcuts -->
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-2 pt-1 border-top border-light-subtle">
            <div class="d-flex align-items-center gap-1">
              <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill x-small" onclick="setQuickDate('today')">
                <i class="bi bi-clock-history me-1"></i> {{ __('messages.quick_today') }}
              </button>
              <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill x-small" onclick="setQuickDate('yesterday')">
                {{ __('messages.quick_yesterday') }}
              </button>
              <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill x-small" onclick="resetCalculator()">
                <i class="bi bi-x-circle me-1"></i> {{ __('messages.quick_reset') }}
              </button>
            </div>
            <div>
              <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 text-muted x-small" onclick="triggerNativePicker()">
                <i class="bi bi-calendar4-week me-1"></i> {{ app()->getLocale() === 'ar' ? 'عرض التقويم' : 'Open Calendar' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Future Date / Validation Error Alert -->
      <div id="ctValidationAlert" class="alert alert-warning border-0 rounded-3 mt-3 mb-0 d-none d-flex align-items-center gap-2 text-start" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5 text-warning flex-shrink-0"></i>
        <div id="ctValidationMessage" class="small fw-semibold">
          {{ __('messages.future_date_warning') }}
        </div>
      </div>
    </div>

    <!-- Active Clean-Time Prominent Banner -->
    <div id="ctProminentDisplay" class="na-cleantime-banner mb-4 d-none">
      <div class="na-banner-shimmer"></div>
      <div class="row align-items-center g-3 text-center text-md-start">
        <div class="col-12 col-md-8">
          <div class="d-inline-flex align-items-center gap-2 mb-2 px-3 py-1 rounded-pill na-celebrate-pill">
            <span class="na-pulse-dot" aria-hidden="true"></span>
            <span class="fw-bold small">{{ __('messages.just_for_today') }}</span>
          </div>
          <div id="ctFormattedText" class="na-headline-text fw-black text-dark mb-1">
            0 يوماً
          </div>
          <div id="ctTotalDaysHeadline" class="text-secondary small fw-bold">
            0 يوماً ممتناً
          </div>
          <div id="ctSpiritualQuote" class="na-humble-quote mt-2 p-2 rounded-2 text-dark-emphasis small">
            "{{ __('messages.miracle_quote_humble') }}"
          </div>
        </div>

        <div class="col-12 col-md-4 text-center text-md-end">
          <div class="d-inline-flex flex-column align-items-center gap-2">
            <!-- Dynamic Mini Badge / Highest Medal Preview -->
            <div id="ctActiveMedalPreview" class="na-active-medal-preview">
              <!-- Dynamically injected SVG preview -->
            </div>
            <!-- Trigger Share Modal Button -->
            <button type="button" class="btn btn-primary btn-sm px-3 py-2 rounded-pill shadow-sm fw-bold d-inline-flex align-items-center gap-2 na-btn-share" onclick="openShareModal()">
              <i class="bi bi-share-fill"></i>
              <span>{{ __('messages.share_milestone_card') }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Real-time Counters Display (Pills) -->
    <div class="row g-2 g-md-3 justify-content-center text-center mb-4">
      <!-- Years -->
      <div class="col-6 col-md-3">
        <div class="na-stat-card">
          <div id="ctYearsVal" class="na-stat-num">0</div>
          <div class="na-stat-label">{{ __('messages.years') ?? 'سنوات' }}</div>
        </div>
      </div>

      <!-- Months -->
      <div class="col-6 col-md-3">
        <div class="na-stat-card">
          <div id="ctMonthsVal" class="na-stat-num">0</div>
          <div class="na-stat-label">{{ __('messages.months') ?? 'شهور' }}</div>
        </div>
      </div>

      <!-- Days -->
      <div class="col-6 col-md-3">
        <div class="na-stat-card">
          <div id="ctDaysVal" class="na-stat-num">0</div>
          <div class="na-stat-label">{{ __('messages.days') ?? 'أيام' }}</div>
        </div>
      </div>

      <!-- Total Continuous Days -->
      <div class="col-6 col-md-3">
        <div class="na-stat-card na-stat-card-total">
          <div id="ctTotalDaysVal" class="na-stat-num na-stat-teal">0</div>
          <div class="na-stat-label fw-bold text-navy-800">{{ __('messages.total_clean_days') }}</div>
        </div>
      </div>
    </div>

    <!-- Interactive Keytag Shelf & Carousel -->
    <div class="na-shelf-container mb-4">
      <div class="d-flex align-items-center justify-content-between mb-3 px-1 flex-wrap gap-2">
        <div class="fw-bold small text-dark d-flex align-items-center gap-2">
          <i class="bi bi-collection-fill text-primary"></i>
          <span>{{ __('messages.all_keytags_shelf') }}</span>
        </div>
        <div class="text-muted small">
          <span id="ctPromptText">{{ __('messages.click_keytag_to_inspect') }}</span>
        </div>
      </div>

      <!-- Shelf Wooden Base & Hooks -->
      <div class="na-keytag-shelf" id="keytagShelf">
        <!-- 1. Welcome / Just For Today (White) -->
        <div class="na-shelf-item" id="kt-welcome" data-milestone="welcome" onclick="showKeytagModal('welcome')" role="button" tabindex="0" aria-label="{{ __('messages.keytag_white_title') }}">
          <div class="na-keytag-ring"></div>
          <div class="na-keytag-graphic kt-white">
            <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
              <defs>
                <filter id="shadowWhite" x="-10%" y="-10%" width="120%" height="120%">
                  <feDropShadow dx="0" dy="2" stdDeviation="2" flood-opacity="0.15"/>
                </filter>
              </defs>
              <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="2" filter="url(#shadowWhite)"/>
              <circle cx="29" cy="42" r="14" fill="none" stroke="#1E293B" stroke-width="1.8" />
              <rect x="20" y="33" width="18" height="18" fill="none" stroke="#1E293B" stroke-width="1.5" transform="rotate(45 29 42)" />
              <text x="29" y="65" text-anchor="middle" font-size="6.5" font-weight="900" fill="#1E293B" font-family="'Cairo', sans-serif">WELCOME</text>
            </svg>
            <span class="na-earned-check" aria-hidden="true"><i class="bi bi-check-circle-fill"></i></span>
          </div>
          <div class="na-shelf-label">{{ __('messages.kt_welcome') ?? 'ترحيب' }}</div>
          <div class="na-shelf-sublabel">1 {{ __('messages.day_singular') ?? 'يوم' }}</div>
        </div>

        <!-- 2. 30 Days (Orange) -->
        <div class="na-shelf-item" id="kt-30d" data-milestone="30d" onclick="showKeytagModal('30d')" role="button" tabindex="0" aria-label="{{ __('messages.keytag_orange_title') }}">
          <div class="na-keytag-ring"></div>
          <div class="na-keytag-graphic kt-orange">
            <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
              <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#EA580C" stroke="#C2410C" stroke-width="2" />
              <circle cx="29" cy="42" r="14" fill="none" stroke="#FFFFFF" stroke-width="1.8" />
              <rect x="20" y="33" width="18" height="18" fill="none" stroke="#FFFFFF" stroke-width="1.5" transform="rotate(45 29 42)" />
              <text x="29" y="65" text-anchor="middle" font-size="6.8" font-weight="900" fill="#FFFFFF" font-family="'Cairo', sans-serif">30 DAYS</text>
            </svg>
            <span class="na-earned-check" aria-hidden="true"><i class="bi bi-check-circle-fill"></i></span>
          </div>
          <div class="na-shelf-label">30 {{ __('messages.day_singular') ?? 'يوم' }}</div>
          <div class="na-shelf-sublabel">{{ __('messages.month') ?? 'شهر' }}</div>
        </div>

        <!-- 3. 60 Days (Green) -->
        <div class="na-shelf-item" id="kt-60d" data-milestone="60d" onclick="showKeytagModal('60d')" role="button" tabindex="0" aria-label="{{ __('messages.keytag_green_title') }}">
          <div class="na-keytag-ring"></div>
          <div class="na-keytag-graphic kt-green">
            <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
              <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#16A34A" stroke="#15803D" stroke-width="2" />
              <circle cx="29" cy="42" r="14" fill="none" stroke="#FFFFFF" stroke-width="1.8" />
              <rect x="20" y="33" width="18" height="18" fill="none" stroke="#FFFFFF" stroke-width="1.5" transform="rotate(45 29 42)" />
              <text x="29" y="65" text-anchor="middle" font-size="6.8" font-weight="900" fill="#FFFFFF" font-family="'Cairo', sans-serif">60 DAYS</text>
            </svg>
            <span class="na-earned-check" aria-hidden="true"><i class="bi bi-check-circle-fill"></i></span>
          </div>
          <div class="na-shelf-label">60 {{ __('messages.day_singular') ?? 'يوم' }}</div>
          <div class="na-shelf-sublabel">2 {{ __('messages.months_short') ?? 'شهور' }}</div>
        </div>

        <!-- 4. 90 Days (Red) -->
        <div class="na-shelf-item" id="kt-90d" data-milestone="90d" onclick="showKeytagModal('90d')" role="button" tabindex="0" aria-label="{{ __('messages.keytag_red_title') }}">
          <div class="na-keytag-ring"></div>
          <div class="na-keytag-graphic kt-red">
            <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
              <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#DC2626" stroke="#B91C1C" stroke-width="2" />
              <circle cx="29" cy="42" r="14" fill="none" stroke="#FFFFFF" stroke-width="1.8" />
              <rect x="20" y="33" width="18" height="18" fill="none" stroke="#FFFFFF" stroke-width="1.5" transform="rotate(45 29 42)" />
              <text x="29" y="65" text-anchor="middle" font-size="6.8" font-weight="900" fill="#FFFFFF" font-family="'Cairo', sans-serif">90 DAYS</text>
            </svg>
            <span class="na-earned-check" aria-hidden="true"><i class="bi bi-check-circle-fill"></i></span>
          </div>
          <div class="na-shelf-label">90 {{ __('messages.day_singular') ?? 'يوم' }}</div>
          <div class="na-shelf-sublabel">3 {{ __('messages.months_short') ?? 'شهور' }}</div>
        </div>

        <!-- 5. 6 Months (Blue) -->
        <div class="na-shelf-item" id="kt-6m" data-milestone="6m" onclick="showKeytagModal('6m')" role="button" tabindex="0" aria-label="{{ __('messages.keytag_blue_title') }}">
          <div class="na-keytag-ring"></div>
          <div class="na-keytag-graphic kt-blue">
            <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
              <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#2563EB" stroke="#1D4ED8" stroke-width="2" />
              <circle cx="29" cy="42" r="14" fill="none" stroke="#FFFFFF" stroke-width="1.8" />
              <rect x="20" y="33" width="18" height="18" fill="none" stroke="#FFFFFF" stroke-width="1.5" transform="rotate(45 29 42)" />
              <text x="29" y="65" text-anchor="middle" font-size="6.2" font-weight="900" fill="#FFFFFF" font-family="'Cairo', sans-serif">6 MONTHS</text>
            </svg>
            <span class="na-earned-check" aria-hidden="true"><i class="bi bi-check-circle-fill"></i></span>
          </div>
          <div class="na-shelf-label">6 {{ __('messages.months_short') ?? 'شهور' }}</div>
          <div class="na-shelf-sublabel">182 {{ __('messages.day_singular') ?? 'يوم' }}</div>
        </div>

        <!-- 6. 9 Months (Yellow) -->
        <div class="na-shelf-item" id="kt-9m" data-milestone="9m" onclick="showKeytagModal('9m')" role="button" tabindex="0" aria-label="{{ __('messages.keytag_yellow_title') }}">
          <div class="na-keytag-ring"></div>
          <div class="na-keytag-graphic kt-yellow">
            <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
              <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#EAB308" stroke="#CA8A04" stroke-width="2" />
              <circle cx="29" cy="42" r="14" fill="none" stroke="#1E293B" stroke-width="1.8" />
              <rect x="20" y="33" width="18" height="18" fill="none" stroke="#1E293B" stroke-width="1.5" transform="rotate(45 29 42)" />
              <text x="29" y="65" text-anchor="middle" font-size="6.2" font-weight="900" fill="#1E293B" font-family="'Cairo', sans-serif">9 MONTHS</text>
            </svg>
            <span class="na-earned-check" aria-hidden="true"><i class="bi bi-check-circle-fill"></i></span>
          </div>
          <div class="na-shelf-label">9 {{ __('messages.months_short') ?? 'شهور' }}</div>
          <div class="na-shelf-sublabel">273 {{ __('messages.day_singular') ?? 'يوم' }}</div>
        </div>

        <!-- 7. 1 Year (Glow-in-the-dark) -->
        <div class="na-shelf-item" id="kt-1y" data-milestone="1y" onclick="showKeytagModal('1y')" role="button" tabindex="0" aria-label="{{ __('messages.keytag_glow_title') }}">
          <div class="na-keytag-ring"></div>
          <div class="na-keytag-graphic kt-glow">
            <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
              <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#D1FAE5" stroke="#10B981" stroke-width="2.5" />
              <circle cx="29" cy="42" r="14" fill="none" stroke="#047857" stroke-width="2" />
              <rect x="20" y="33" width="18" height="18" fill="none" stroke="#047857" stroke-width="1.5" transform="rotate(45 29 42)" />
              <text x="29" y="65" text-anchor="middle" font-size="7" font-weight="900" fill="#047857" font-family="'Cairo', sans-serif">1 YEAR</text>
            </svg>
            <span class="na-earned-check" aria-hidden="true"><i class="bi bi-check-circle-fill"></i></span>
          </div>
          <div class="na-shelf-label text-success fw-bold">{{ __('messages.one_year') ?? 'سنة واحدة' }}</div>
          <div class="na-shelf-sublabel">365 {{ __('messages.day_singular') ?? 'يوم' }}</div>
        </div>

        <!-- 8. 18 Months (Grey) -->
        <div class="na-shelf-item" id="kt-18m" data-milestone="18m" onclick="showKeytagModal('18m')" role="button" tabindex="0" aria-label="{{ __('messages.keytag_grey_title') }}">
          <div class="na-keytag-ring"></div>
          <div class="na-keytag-graphic kt-grey">
            <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
              <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#64748B" stroke="#475569" stroke-width="2" />
              <circle cx="29" cy="42" r="14" fill="none" stroke="#FFFFFF" stroke-width="1.8" />
              <rect x="20" y="33" width="18" height="18" fill="none" stroke="#FFFFFF" stroke-width="1.5" transform="rotate(45 29 42)" />
              <text x="29" y="65" text-anchor="middle" font-size="5.8" font-weight="900" fill="#FFFFFF" font-family="'Cairo', sans-serif">18 MONTHS</text>
            </svg>
            <span class="na-earned-check" aria-hidden="true"><i class="bi bi-check-circle-fill"></i></span>
          </div>
          <div class="na-shelf-label">18 {{ __('messages.months_short') ?? 'شهراً' }}</div>
          <div class="na-shelf-sublabel">547 {{ __('messages.day_singular') ?? 'يوم' }}</div>
        </div>

        <!-- 9. Multiple Years (Black & Gold / Medallion) -->
        <div class="na-shelf-item" id="kt-multi" data-milestone="multi" onclick="showKeytagModal('multi')" role="button" tabindex="0" aria-label="{{ __('messages.keytag_multi_title') }}">
          <div class="na-keytag-ring"></div>
          <div class="na-keytag-graphic kt-multi">
            <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
              <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#0F172A" stroke="#EAB308" stroke-width="2.5" />
              <circle cx="29" cy="42" r="14" fill="none" stroke="#EAB308" stroke-width="1.8" />
              <rect x="20" y="33" width="18" height="18" fill="none" stroke="#EAB308" stroke-width="1.5" transform="rotate(45 29 42)" />
              <text x="29" y="65" text-anchor="middle" font-size="5.8" font-weight="900" fill="#EAB308" font-family="'Cairo', sans-serif">MULTIPLE</text>
            </svg>
            <span class="na-earned-check" aria-hidden="true"><i class="bi bi-check-circle-fill"></i></span>
          </div>
          <div class="na-shelf-label text-warning fw-bold">{{ __('messages.multi_years') ?? 'سنوات' }}</div>
          <div class="na-shelf-sublabel">730+ {{ __('messages.day_singular') ?? 'يوم' }}</div>
        </div>
      </div>
    </div>

    <!-- Next Milestone Progress Banner -->
    <div id="ctNextMilestoneContainer" class="na-next-milestone-panel d-none">
      <div class="row align-items-center g-3">
        <div class="col-12 col-md-7">
          <div class="d-flex align-items-center gap-3">
            <div class="na-next-icon-wrap flex-shrink-0">
              <i class="bi bi-flag-fill text-primary fs-4"></i>
            </div>
            <div>
              <div class="x-small text-muted fw-bold">{{ __('messages.next_milestone') }}</div>
              <div id="ctNextTitle" class="fw-bold text-dark fs-6"></div>
              <div id="ctNextDesc" class="x-small text-secondary mt-0.5"></div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-5">
          <div class="d-flex flex-column gap-1">
            <div class="d-flex justify-content-between align-items-center x-small">
              <span id="ctNextCountdownBadge" class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-2.5 py-1"></span>
              <span id="ctNextProgressPercent" class="fw-bold text-muted font-monospace">0%</span>
            </div>
            <div class="progress na-progress-bar" style="height: 8px;">
              <div id="ctNextProgressBar" class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ======================================================================
     Modal 1: Anonymous Milestone Share & Download Card Modal
     ====================================================================== -->
<div class="modal fade" id="naShareModal" tabindex="-1" aria-labelledby="naShareModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content na-modal-card border-0 shadow-lg overflow-hidden">
      <div class="modal-header border-0 bg-dark text-white px-4 py-3 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-shield-check text-warning fs-4"></i>
          <div>
            <h5 class="modal-title fw-bold m-0" id="naShareModalLabel">{{ __('messages.share_milestone_card') }}</h5>
            <span class="x-small text-white-50">{{ __('messages.anonymous_card_notice') }}</span>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('messages.Close') ?? 'Close' }}"></button>
      </div>

      <div class="modal-body p-4 text-center">
        <!-- Rendered Canvas Preview Container -->
        <div class="na-canvas-preview-wrap mx-auto shadow-sm mb-3">
          <canvas id="naMilestoneCanvas" width="1080" height="1080" class="na-milestone-canvas" aria-label="بطاقة إنجاز التعافي"></canvas>
        </div>

        <div class="alert alert-info border-0 rounded-3 py-2 px-3 small text-start d-flex align-items-center gap-2 mb-3 mx-auto" style="max-width: 540px; background-color: #f0f9ff; color: #0369a1;">
          <i class="bi bi-info-circle-fill fs-5 flex-shrink-0 text-primary"></i>
          <span class="x-small">{{ __('messages.anonymous_card_notice') }}</span>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex align-items-center justify-content-center flex-wrap gap-2">
          <!-- Download Button -->
          <button type="button" class="btn btn-primary px-3 py-2 rounded-pill fw-bold d-inline-flex align-items-center gap-2 shadow-sm" onclick="downloadMilestoneCard()">
            <i class="bi bi-download"></i>
            <span>{{ __('messages.download_image') }}</span>
          </button>

          <!-- Web Share Button -->
          <button type="button" id="btnWebShare" class="btn btn-success px-3 py-2 rounded-pill fw-bold d-inline-flex align-items-center gap-2 shadow-sm" onclick="shareMilestoneCard()">
            <i class="bi bi-whatsapp"></i>
            <span>{{ __('messages.share_badge') }}</span>
          </button>

          <!-- Copy Text Button -->
          <button type="button" class="btn btn-outline-secondary px-3 py-2 rounded-pill fw-bold d-inline-flex align-items-center gap-2" onclick="copyMilestoneText()">
            <i class="bi bi-clipboard-check"></i>
            <span>{{ __('messages.copy_text') }}</span>
          </button>
        </div>

        <!-- Copy Feedback Toast Message -->
        <div id="ctCopyFeedback" class="badge bg-dark text-white py-1.5 px-3 rounded-pill mt-3 d-none animate__animated animate__fadeIn">
          <i class="bi bi-check2 text-success me-1"></i> {{ __('messages.text_copied') }}
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ======================================================================
     Modal 2: Keytag Spiritual Significance & Literature Details Modal
     ====================================================================== -->
<div class="modal fade" id="naKeytagDetailModal" tabindex="-1" aria-labelledby="naKeytagDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content na-modal-card border-0 shadow-lg">
      <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center">
        <h5 class="modal-title fw-bold text-dark" id="naKeytagDetailLabel">{{ __('messages.keytag_details') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('messages.Close') ?? 'Close' }}"></button>
      </div>

      <div class="modal-body p-4 text-center">
        <div id="modalKeytagGraphic" class="na-modal-keytag-large mx-auto mb-3"></div>
        <h4 id="modalKeytagTitle" class="fw-bold text-dark mb-1"></h4>
        <div id="modalKeytagDays" class="badge bg-light text-muted border font-monospace mb-3"></div>
        <div class="p-3 rounded-3 bg-light text-start mb-3 border">
          <div class="fw-bold small text-dark mb-1"><i class="bi bi-bookmark-heart-fill text-danger me-1"></i> {{ app()->getLocale() === 'ar' ? 'المعنى والبعد الروحي:' : 'Spiritual Meaning:' }}</div>
          <p id="modalKeytagDesc" class="small text-secondary mb-0 lh-base"></p>
        </div>
        <div id="modalKeytagStatus" class="p-2 rounded-pill small fw-bold"></div>
      </div>
    </div>
  </div>
</div>

<!-- Styles for Clean-Time Calculator & Shelf Component -->
<style>
.na-cleantime-wrapper {
  position: relative;
}

.na-cleantime-card {
  background: var(--na-surface, #ffffff);
  border: 1.5px solid var(--na-border, #e2e8f0);
  border-radius: 24px;
  padding: 2.25rem 2rem;
  box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
  position: relative;
  overflow: hidden;
}

.na-badge-pill {
  background: rgba(50, 85, 127, 0.08);
  color: #32557f;
  font-size: 0.85rem;
}

.na-title {
  font-size: clamp(1.4rem, 2.8vw, 2rem);
  font-weight: 800;
  color: #0f172a;
}

.na-subtitle {
  max-width: 650px;
  line-height: 1.6;
}

.na-date-input-panel {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border: 1.5px solid #e2e8f0;
  border-radius: 18px;
  padding: 1.25rem 1.5rem;
}

.x-small {
  font-size: 0.76rem;
}

/* Clean-Time Segmented Date Dropdowns Styling & RTL Fixes */
.ct-date-input-group {
  display: flex !important;
  flex-wrap: nowrap !important;
  width: 100%;
  border-radius: 0.375rem;
  transition: box-shadow 0.2s ease, border-color 0.2s ease;
}

/* LTR Styles */
[dir="ltr"] .ct-date-input-group,
.na-cleantime-wrapper[dir="ltr"] .ct-date-input-group {
  direction: ltr !important;
  flex-direction: row !important;
}

[dir="ltr"] .ct-date-input-group .input-group-text,
.na-cleantime-wrapper[dir="ltr"] .ct-date-input-group .input-group-text {
  border-top-left-radius: 0.375rem !important;
  border-bottom-left-radius: 0.375rem !important;
  border-top-right-radius: 0 !important;
  border-bottom-right-radius: 0 !important;
  border: 1px solid #ced4da !important;
  border-right: none !important;
  background-color: #f8fafc !important;
  color: #64748b !important;
  padding: 0.25rem 0.65rem !important;
  display: inline-flex !important;
  align-items: center !important;
}

[dir="ltr"] .ct-date-input-group .form-select,
.na-cleantime-wrapper[dir="ltr"] .ct-date-input-group .form-select {
  border-top-left-radius: 0 !important;
  border-bottom-left-radius: 0 !important;
  border-top-right-radius: 0.375rem !important;
  border-bottom-right-radius: 0.375rem !important;
  border: 1px solid #ced4da !important;
  border-left: 1px solid #e2e8f0 !important;
  margin-left: 0 !important;
  margin-right: 0 !important;
  background-position: right 0.75rem center !important;
  padding-right: 2.25rem !important;
  padding-left: 0.75rem !important;
}

/* RTL Styles: Keep icon on the right (start) attached seamlessly to Day select */
[dir="rtl"] .ct-date-input-group,
.na-cleantime-wrapper[dir="rtl"] .ct-date-input-group {
  direction: rtl !important;
  flex-direction: row !important;
}

[dir="rtl"] .ct-date-input-group .input-group-text,
.na-cleantime-wrapper[dir="rtl"] .ct-date-input-group .input-group-text {
  border-top-right-radius: 0.375rem !important;
  border-bottom-right-radius: 0.375rem !important;
  border-top-left-radius: 0 !important;
  border-bottom-left-radius: 0 !important;
  border: 1px solid #ced4da !important;
  border-left: none !important;
  background-color: #f8fafc !important;
  color: #64748b !important;
  padding: 0.25rem 0.65rem !important;
  display: inline-flex !important;
  align-items: center !important;
}

[dir="rtl"] .ct-date-input-group .form-select,
.na-cleantime-wrapper[dir="rtl"] .ct-date-input-group .form-select {
  border-top-right-radius: 0 !important;
  border-bottom-right-radius: 0 !important;
  border-top-left-radius: 0.375rem !important;
  border-bottom-left-radius: 0.375rem !important;
  border: 1px solid #ced4da !important;
  border-right: 1px solid #e2e8f0 !important;
  margin-left: 0 !important;
  margin-right: 0 !important;
  background-position: left 0.75rem center !important;
  padding-left: 2.25rem !important;
  padding-right: 0.75rem !important;
}

/* RTL Chevron & Padding Alignment for Month and Year Selects */
[dir="rtl"] .na-date-input-panel .form-select,
.na-cleantime-wrapper[dir="rtl"] .na-date-input-panel .form-select {
  background-position: left 0.75rem center !important;
  padding-left: 2.25rem !important;
  padding-right: 0.75rem !important;
  border-radius: 0.375rem !important;
}

.ct-date-input-group:focus-within {
  box-shadow: 0 0 0 0.2rem rgba(50, 85, 127, 0.15) !important;
  border-radius: 0.375rem;
}

.ct-date-input-group:focus-within .input-group-text,
.ct-date-input-group:focus-within .form-select {
  border-color: #32557f !important;
}

/* Prominent Clean-Time Banner */
.na-cleantime-banner {
  position: relative;
  background: linear-gradient(135deg, #f0fdf4 0%, #e6f7ec 100%);
  border: 1.5px solid #86efac;
  border-radius: 20px;
  padding: 1.5rem 1.75rem;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(34, 197, 94, 0.12);
  animation: naFadeSlideIn 0.4s ease-out forwards;
}

.na-banner-shimmer {
  position: absolute;
  top: 0;
  left: -100%;
  width: 50%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
  transform: skewX(-25deg);
  pointer-events: none;
  animation: naShimmer 4s infinite;
}

@keyframes naShimmer {
  0% { left: -100%; }
  40% { left: 200%; }
  100% { left: 200%; }
}

@keyframes naFadeSlideIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.na-celebrate-pill {
  background: rgba(22, 163, 74, 0.12);
  color: #15803d;
}

.na-pulse-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #22c55e;
  box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
  animation: naPulse 1.8s infinite;
}

@keyframes naPulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}

.na-headline-text {
  font-size: clamp(1.4rem, 2.8vw, 2.2rem);
  letter-spacing: -0.02em;
  color: #0f172a;
  line-height: 1.25;
}

.na-humble-quote {
  background: rgba(255, 255, 255, 0.75);
  border-right: 3px solid #16a34a;
  font-style: italic;
}

/* Stat Cards */
.na-stat-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 1.1rem 0.75rem;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.na-stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
}

.na-stat-card-total {
  background: #f0f7ff;
  border-color: #dbe7f6;
}

.na-stat-num {
  font-size: clamp(1.8rem, 3.2vw, 2.5rem);
  font-weight: 800;
  line-height: 1;
  color: #0f172a;
  font-variant-numeric: tabular-nums;
  font-family: inherit;
}

.na-stat-teal {
}

.na-stat-label {
  font-size: 0.82rem;
  font-weight: 600;
  color: #64748b;
  margin-top: 6px;
}

/* Shelf & Keytags */
.na-shelf-container {
  margin-top: 2rem;
  padding: 1rem 0.5rem;
  overflow-x: auto;
  scrollbar-width: thin;
}

.na-keytag-shelf {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  width: max-content;
  min-width: 100%;
  gap: 14px;
  padding-bottom: 24px;
  position: relative;
}

.na-keytag-shelf::before {
  content: '';
  position: absolute;
  top: 14px;
  left: 20px;
  right: 20px;
  height: 4px;
  background: linear-gradient(90deg, #cbd5e1 0%, #94a3b8 50%, #cbd5e1 100%);
  border-radius: 2px;
  z-index: 0;
}

.na-shelf-item {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
  opacity: 0.38;
  transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
  flex: 1;
  max-width: 86px;
  outline: none;
}

.na-shelf-item:hover, .na-shelf-item:focus-visible {
  opacity: 0.85;
  transform: translateY(-4px);
}

.na-shelf-item.earned {
  opacity: 1;
}

.na-shelf-item.current-highest {
  opacity: 1;
  transform: scale(1.15) translateY(-6px);
}

.na-shelf-item.current-highest .na-keytag-graphic {
  filter: drop-shadow(0 10px 22px rgba(50, 85, 127, 0.45));
}

.na-shelf-item.next-target {
  opacity: 0.75;
  animation: naTargetPulse 2s infinite ease-in-out;
}

@keyframes naTargetPulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

.na-keytag-ring {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  border: 2.5px solid #94a3b8;
  background: transparent;
  margin-bottom: -5px;
  z-index: 2;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.na-keytag-graphic {
  width: 58px;
  height: 84px;
  position: relative;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.12));
  transition: transform 0.25s ease, filter 0.25s ease;
}

/* Glow in the dark phosphor keytag style */
.kt-glow svg {
  filter: drop-shadow(0 0 6px rgba(16, 185, 129, 0.5));
}

.na-shelf-item.earned .kt-glow svg {
  animation: naPhosphorGlow 2.5s infinite alternate ease-in-out;
}

@keyframes naPhosphorGlow {
  from { filter: drop-shadow(0 0 4px rgba(16, 185, 129, 0.4)); }
  to { filter: drop-shadow(0 0 14px rgba(52, 211, 153, 0.9)); }
}

.na-earned-check {
  display: none;
  position: absolute;
  top: 14px;
  right: -4px;
  color: #16a34a;
  background: #ffffff;
  border-radius: 50%;
  font-size: 0.95rem;
  line-height: 1;
  box-shadow: 0 2px 5px rgba(0,0,0,0.15);
}

.na-shelf-item.earned .na-earned-check {
  display: block;
}

.na-shelf-label {
  font-size: 0.78rem;
  font-weight: 700;
  color: #1e293b;
  margin-top: 8px;
  text-align: center;
  white-space: nowrap;
}

.na-shelf-sublabel {
  font-size: 0.68rem;
  color: #64748b;
  text-align: center;
  white-space: nowrap;
}

/* Next Milestone Progress Panel */
.na-next-milestone-panel {
  background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
  border: 1.5px solid #bfdbfe;
  border-radius: 18px;
  padding: 1.25rem 1.75rem;
  box-shadow: 0 4px 14px rgba(59, 130, 246, 0.08);
  animation: naFadeSlideIn 0.35s ease-out;
}

.na-next-icon-wrap {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 6px rgba(59, 130, 246, 0.15);
}

/* Modal Canvas Styling */
.na-modal-card {
  border-radius: 24px;
}

.na-canvas-preview-wrap {
  max-width: 420px;
  width: 100%;
  aspect-ratio: 1 / 1;
  border-radius: 16px;
  overflow: hidden;
  background: #0f172a;
}

.na-milestone-canvas {
  width: 100%;
  height: 100%;
  display: block;
}

.na-confetti-canvas {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 99;
}

.na-btn-share {
  background: linear-gradient(135deg, #32557f 0%, #243f60 100%);
  border: none;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.na-btn-share:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(50, 85, 127, 0.35);
}

/* Mobile responsive adjustments */
@media (max-width: 576px) {
  .na-cleantime-card {
    padding: 1.5rem 1rem;
    border-radius: 18px;
  }
  .na-date-input-panel {
    padding: 1rem 0.75rem;
  }
}
</style>

<!-- Clean-Time Calculator Engine & Share Card Generator -->
<script>
(function() {
  // Milestone Catalog
  const NA_MILESTONES = [
    {
      id: 'welcome',
      elementId: 'kt-welcome',
      days: 1,
      name: "{{ __('messages.keytag_white_title') }}",
      shortName: "{{ __('messages.kt_welcome') ?? 'ترحيب' }}",
      color: '#FFFFFF',
      textColor: '#1E293B',
      desc: "{{ __('messages.kt_welcome_desc') ?? 'الرغبة في الامتناع، بداية حياة جديدة وباب أمل مفتوح.' }}",
      quote: "أهم شخص في أي اجتماع هو العضو الجديد، لأننا لا نستطيع الاحتفاظ بما لدينا إلا بتقديمه للآخرين."
    },
    {
      id: '30d',
      elementId: 'kt-30d',
      days: 30,
      name: "{{ __('messages.keytag_orange_title') }}",
      shortName: "30 {{ __('messages.day_singular') ?? 'يوم' }}",
      color: '#EA580C',
      textColor: '#FFFFFF',
      desc: "{{ __('messages.kt_30d_desc') ?? 'شهر كامل من التعافي والصفاء الذهني المستمر.' }}",
      quote: "لليوم فقط أفكاري ستكون مركزة على تعافيي، عائشاً ومستمتعاً بالحياة بدون تعاطي."
    },
    {
      id: '60d',
      elementId: 'kt-60d',
      days: 60,
      name: "{{ __('messages.keytag_green_title') }}",
      shortName: "60 {{ __('messages.day_singular') ?? 'يوم' }}",
      color: '#16A34A',
      textColor: '#FFFFFF',
      desc: "{{ __('messages.kt_60d_desc') ?? 'شهران من النمو الروحي وترسيخ مبادئ الزمالة.' }}",
      quote: "عندما نمارس المبادئ، تتغير حياتنا ويزداد إيماننا بأن التعافي حقيقة ممكنة."
    },
    {
      id: '90d',
      elementId: 'kt-90d',
      days: 90,
      name: "{{ __('messages.keytag_red_title') }}",
      shortName: "90 {{ __('messages.day_singular') ?? 'يوم' }}",
      color: '#DC2626',
      textColor: '#FFFFFF',
      desc: "{{ __('messages.kt_90d_desc') ?? 'ثلاثة أشهر، أساس متين وركيزة هامة في التعافي.' }}",
      quote: "تسعون يوماً من الصدق والأمانة والانفتاح الذهني.. لقد أرسينا أساساً جديداً للحياة."
    },
    {
      id: '6m',
      elementId: 'kt-6m',
      days: 182,
      name: "{{ __('messages.keytag_blue_title') }}",
      shortName: "6 {{ __('messages.months_short') ?? 'شهور' }}",
      color: '#2563EB',
      textColor: '#FFFFFF',
      desc: "{{ __('messages.kt_6m_desc') ?? 'نصف عام كامل من الحرية والسلام الداخلي.' }}",
      quote: "نصف عام من السلام الداخلي؛ الأمل الذي شعرنا به في أول اجتماع تحول اليوم إلى يقين."
    },
    {
      id: '9m',
      elementId: 'kt-9m',
      days: 273,
      name: "{{ __('messages.keytag_yellow_title') }}",
      shortName: "9 {{ __('messages.months_short') ?? 'شهور' }}",
      color: '#EAB308',
      textColor: '#1E293B',
      desc: "{{ __('messages.kt_9m_desc') ?? 'ثلاثة أرباع العام، نضج روحي والتزام واثق بالبرنامج.' }}",
      quote: "تسعة أشهر من المثابرة والخدمة.. نحن نولد روحياً من جديد."
    },
    {
      id: '1y',
      elementId: 'kt-1y',
      days: 365,
      name: "{{ __('messages.keytag_glow_title') }}",
      shortName: "{{ __('messages.one_year') ?? 'سنة واحدة' }}",
      color: '#10B981',
      textColor: '#047857',
      desc: "{{ __('messages.kt_1y_desc') ?? '365 يوماً من النقاء، حياة جديدة بالكامل ومعجزة تتحقق.' }}",
      quote: "سنة كاملة بدون مخدرات! معجزة حقيقية لمدمن كان يظن أنه ميؤوس منه."
    },
    {
      id: '18m',
      elementId: 'kt-18m',
      days: 547,
      name: "{{ __('messages.keytag_grey_title') }}",
      shortName: "18 {{ __('messages.months_short') ?? 'شهراً' }}",
      color: '#64748B',
      textColor: '#FFFFFF',
      desc: "{{ __('messages.kt_18m_desc') ?? 'عام ونصف من الثبات والاستمرار في طريق التعافي.' }}",
      quote: "عام ونصف من النضج والثبات.. التعافي أصبح أسلوب حياة يومي."
    },
    {
      id: 'multi',
      elementId: 'kt-multi',
      days: 730,
      name: "{{ __('messages.keytag_multi_title') }}",
      shortName: "{{ __('messages.multi_years') ?? 'سنوات' }}",
      color: '#0F172A',
      textColor: '#EAB308',
      desc: "{{ __('messages.kt_multi_desc') ?? 'سنوات متتالية من الامتنان والعطاء في خدمة الزمالة.' }}",
      quote: "سنوات متعاقبة من الامتنان لليوم فقط. إن ما لم نستطع فعله وحدنا، فعلناه معاً."
    }
  ];

  // Active state storage
  let currentCalculation = null;

  // Global functions exposed to window
  window.onSegmentedDateChange = function() {
    const day = document.getElementById('ctDaySelect').value;
    const month = document.getElementById('ctMonthSelect').value;
    const year = document.getElementById('ctYearSelect').value;

    if (!day || !month || !year) {
      clearValidation();
      return;
    }

    const pad = (n) => String(n).padStart(2, '0');
    const dateStr = `${year}-${pad(month)}-${pad(day)}`;

    // Sync to hidden native picker
    const nativePicker = document.getElementById('ctHiddenNativeDate');
    if (nativePicker) nativePicker.value = dateStr;

    executeCalculation(new Date(parseInt(year, 10), parseInt(month, 10) - 1, parseInt(day, 10)));
  };

  window.onNativeDateChange = function(dateStr) {
    if (!dateStr) return;
    const parts = dateStr.split('-');
    if (parts.length === 3) {
      document.getElementById('ctYearSelect').value = parseInt(parts[0], 10);
      document.getElementById('ctMonthSelect').value = parseInt(parts[1], 10);
      document.getElementById('ctDaySelect').value = parseInt(parts[2], 10);
      executeCalculation(new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10)));
    }
  };

  window.setQuickDate = function(type) {
    const today = new Date();
    let target = new Date();
    if (type === 'yesterday') {
      target.setDate(today.getDate() - 1);
    }
    
    document.getElementById('ctYearSelect').value = target.getFullYear();
    document.getElementById('ctMonthSelect').value = target.getMonth() + 1;
    document.getElementById('ctDaySelect').value = target.getDate();
    
    const pad = (n) => String(n).padStart(2, '0');
    const nativePicker = document.getElementById('ctHiddenNativeDate');
    if (nativePicker) {
      nativePicker.value = `${target.getFullYear()}-${pad(target.getMonth() + 1)}-${pad(target.getDate())}`;
    }

    executeCalculation(target, true);
  };

  window.triggerNativePicker = function() {
    const nativePicker = document.getElementById('ctHiddenNativeDate');
    if (nativePicker) {
      if (typeof nativePicker.showPicker === 'function') {
        nativePicker.showPicker();
      } else {
        nativePicker.classList.remove('d-none');
        nativePicker.focus();
        nativePicker.click();
      }
    }
  };

  window.resetCalculator = function() {
    document.getElementById('ctDaySelect').value = '';
    document.getElementById('ctMonthSelect').value = '';
    document.getElementById('ctYearSelect').value = '';
    const nativePicker = document.getElementById('ctHiddenNativeDate');
    if (nativePicker) nativePicker.value = '';

    clearValidation();
    document.getElementById('ctProminentDisplay').classList.add('d-none');
    document.getElementById('ctNextMilestoneContainer').classList.add('d-none');
    
    document.getElementById('ctYearsVal').textContent = '0';
    document.getElementById('ctMonthsVal').textContent = '0';
    document.getElementById('ctDaysVal').textContent = '0';
    document.getElementById('ctTotalDaysVal').textContent = '0';

    NA_MILESTONES.forEach(m => {
      const el = document.getElementById(m.elementId);
      if (el) el.classList.remove('earned', 'current-highest', 'next-target');
    });

    currentCalculation = null;
  };

  function showValidation(msg) {
    const alertEl = document.getElementById('ctValidationAlert');
    const msgEl = document.getElementById('ctValidationMessage');
    if (alertEl && msgEl) {
      msgEl.textContent = msg;
      alertEl.classList.remove('d-none');
    }
    document.getElementById('ctProminentDisplay')?.classList.add('d-none');
    document.getElementById('ctNextMilestoneContainer')?.classList.add('d-none');
  }

  function clearValidation() {
    const alertEl = document.getElementById('ctValidationAlert');
    if (alertEl) alertEl.classList.add('d-none');
  }

  // Calculation Engine
  function executeCalculation(cleanDate, isQuick = false) {
    clearValidation();

    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const inputDate = new Date(cleanDate.getFullYear(), cleanDate.getMonth(), cleanDate.getDate());

    // Validation: Date in future
    if (inputDate > today) {
      showValidation("{{ __('messages.future_date_warning') }}");
      return;
    }

    // Validation: Invalid day/month combination (e.g. Feb 31)
    if (isNaN(inputDate.getTime()) || inputDate.getMonth() !== cleanDate.getMonth()) {
      showValidation("{{ __('messages.invalid_date_warning') }}");
      return;
    }

    // Precise Calendar Difference
    let years = today.getFullYear() - inputDate.getFullYear();
    let months = today.getMonth() - inputDate.getMonth();
    let days = today.getDate() - inputDate.getDate();

    if (days < 0) {
      months -= 1;
      const prevMonthLast = new Date(today.getFullYear(), today.getMonth(), 0).getDate();
      days += prevMonthLast;
    }

    if (months < 0) {
      years -= 1;
      months += 12;
    }

    // Total Days
    const diffMs = today.getTime() - inputDate.getTime();
    const totalDays = Math.round(diffMs / (1000 * 60 * 60 * 24));
    const totalHours = totalDays * 24;

    // Update Counter Numbers
    animateNumber('ctYearsVal', years);
    animateNumber('ctMonthsVal', months);
    animateNumber('ctDaysVal', days);
    animateNumber('ctTotalDaysVal', totalDays);

    // Milestones Evaluation
    let earnedMilestones = [];
    let highestEarned = null;
    let nextTarget = null;

    NA_MILESTONES.forEach(m => {
      const el = document.getElementById(m.elementId);
      if (!el) return;

      el.classList.remove('earned', 'current-highest', 'next-target');

      let isEarned = false;
      if (m.id === 'welcome' && totalDays >= 0) {
        // Day 1 / Welcome includes desire on day 0
        isEarned = true;
      } else if (m.id === 'multi' && years >= 2) {
        isEarned = true;
      } else if (totalDays >= m.days) {
        isEarned = true;
      }

      if (isEarned) {
        el.classList.add('earned');
        earnedMilestones.push(m);
        highestEarned = m;
      } else if (!nextTarget) {
        nextTarget = m;
        el.classList.add('next-target');
      }
    });

    if (highestEarned) {
      const highestEl = document.getElementById(highestEarned.elementId);
      if (highestEl) highestEl.classList.add('current-highest');
    }

    // Build Natural Arabic / English Formatted Text
    const formattedText = buildNaturalString(years, months, days, totalDays);
    const totalDaysText = `${totalDays.toLocaleString()} {{ __('messages.grateful_days') }}`;

    const displayBox = document.getElementById('ctProminentDisplay');
    const headlineEl = document.getElementById('ctFormattedText');
    const totalDaysEl = document.getElementById('ctTotalDaysHeadline');
    const quoteEl = document.getElementById('ctSpiritualQuote');

    if (headlineEl) headlineEl.textContent = formattedText;
    if (totalDaysEl) totalDaysEl.textContent = totalDaysText;
    if (quoteEl && highestEarned) quoteEl.textContent = `"${highestEarned.quote}"`;
    if (displayBox) displayBox.classList.remove('d-none');

    // Update Next Milestone Progress
    const nextBox = document.getElementById('ctNextMilestoneContainer');
    if (nextTarget && nextBox) {
      nextBox.classList.remove('d-none');
      document.getElementById('ctNextTitle').textContent = nextTarget.name;
      document.getElementById('ctNextDesc').textContent = nextTarget.desc;

      const remainingDays = Math.max(1, nextTarget.days - totalDays);
      document.getElementById('ctNextCountdownBadge').textContent = `${remainingDays} {{ __('messages.days_remaining') }}`;

      // Calculate progress relative to previous milestone
      const prevDays = highestEarned ? (highestEarned.id === 'welcome' ? 0 : highestEarned.days) : 0;
      const span = nextTarget.days - prevDays;
      const achieved = Math.max(0, totalDays - prevDays);
      const percent = Math.min(99, Math.max(5, Math.round((achieved / span) * 100)));

      document.getElementById('ctNextProgressBar').style.width = `${percent}%`;
      document.getElementById('ctNextProgressPercent').textContent = `${percent}%`;
    } else if (nextBox) {
      // Reached Multiple Years
      nextBox.classList.remove('d-none');
      const nextYear = years + 1;
      const targetDays = Math.round(nextYear * 365.25);
      const remainingDays = Math.max(1, targetDays - totalDays);
      
      document.getElementById('ctNextTitle').textContent = `${nextYear} {{ __('messages.multi_years') ?? 'سنوات' }}`;
      document.getElementById('ctNextDesc').textContent = "{{ app()->getLocale() === 'ar' ? 'الاستمرار في التعافي خطوة بخطوة ويوماً بيوم' : 'Continuing recovery one day at a time' }}";
      document.getElementById('ctNextCountdownBadge').textContent = `${remainingDays} {{ __('messages.days_remaining') }}`;
      
      const currentYearDays = Math.round(years * 365.25);
      const span = targetDays - currentYearDays;
      const achieved = Math.max(0, totalDays - currentYearDays);
      const percent = Math.min(99, Math.max(5, Math.round((achieved / span) * 100)));

      document.getElementById('ctNextProgressBar').style.width = `${percent}%`;
      document.getElementById('ctNextProgressPercent').textContent = `${percent}%`;
    }

    // Save calculation snapshot
    currentCalculation = {
      cleanDate: inputDate,
      years,
      months,
      days,
      totalDays,
      totalHours,
      highestEarned: highestEarned || NA_MILESTONES[0],
      formattedText,
      totalDaysText
    };

    // Trigger Confetti celebration if earned milestone
    if (totalDays > 0) {
      launchConfetti();
    }
  }

  // Natural Arabic / English clean-time pluralization
  function buildNaturalString(y, m, d, total) {
    const isAr = "{{ app()->getLocale() }}" === 'ar';
    if (!isAr) {
      const parts = [];
      if (y > 0) parts.push(`${y} ${y === 1 ? 'Year' : 'Years'}`);
      if (m > 0) parts.push(`${m} ${m === 1 ? 'Month' : 'Months'}`);
      if (d > 0 || parts.length === 0) parts.push(`${d} ${d === 1 ? 'Day' : 'Days'}`);
      return parts.join(', ');
    }

    if (total === 0) return 'اليوم الأول في التعافي (الرغبة في الامتناع)';

    const parts = [];
    if (y > 0) {
      if (y === 1) parts.push('سنة واحدة');
      else if (y === 2) parts.push('سنتان');
      else if (y >= 3 && y <= 10) parts.push(`${y} سنوات`);
      else parts.push(`${y} سنة`);
    }

    if (m > 0) {
      if (m === 1) parts.push('شهر واحد');
      else if (m === 2) parts.push('شهران');
      else if (m >= 3 && m <= 10) parts.push(`${m} أشهر`);
      else parts.push(`${m} شهراً`);
    }

    if (d > 0 || parts.length === 0) {
      if (d === 1) parts.push('يوم واحد');
      else if (d === 2) parts.push('يومان');
      else if (d >= 3 && d <= 10) parts.push(`${d} أيام`);
      else parts.push(`${d} يوماً`);
    }

    return parts.join('، و ');
  }

  function animateNumber(elementId, targetValue) {
    const el = document.getElementById(elementId);
    if (!el) return;
    el.textContent = targetValue;
  }

  // ======================================================================
  // Confetti Particle System (Vanilla HTML5 Canvas)
  // ======================================================================
  function launchConfetti() {
    const canvas = document.getElementById('naConfettiCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const rect = canvas.parentElement.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = rect.height;

    const colors = ['#EA580C', '#16A34A', '#DC2626', '#2563EB', '#EAB308', '#10B981', '#64748B', '#0E8388', '#F59E0B'];
    const particles = [];
    const count = Math.min(80, Math.floor(rect.width / 10));

    for (let i = 0; i < count; i++) {
      particles.push({
        x: Math.random() * canvas.width,
        y: -10 - Math.random() * 50,
        w: 6 + Math.random() * 6,
        h: 8 + Math.random() * 8,
        vx: (Math.random() - 0.5) * 4,
        vy: 2 + Math.random() * 4,
        rot: Math.random() * 360,
        vRot: (Math.random() - 0.5) * 8,
        color: colors[Math.floor(Math.random() * colors.length)],
        opacity: 1
      });
    }

    let start = null;
    function render(ts) {
      if (!start) start = ts;
      const elapsed = ts - start;
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      let alive = false;
      particles.forEach(p => {
        p.x += p.vx;
        p.y += p.vy;
        p.rot += p.vRot;
        if (p.y > canvas.height * 0.7) {
          p.opacity -= 0.02;
        }

        if (p.opacity > 0 && p.y < canvas.height) {
          alive = true;
          ctx.save();
          ctx.translate(p.x, p.y);
          ctx.rotate((p.rot * Math.PI) / 180);
          ctx.globalAlpha = Math.max(0, p.opacity);
          ctx.fillStyle = p.color;
          ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
          ctx.restore();
        }
      });

      if (alive && elapsed < 3500) {
        requestAnimationFrame(render);
      } else {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
      }
    }

    requestAnimationFrame(render);
  }

  // ======================================================================
  // Keytag Details Modal
  // ======================================================================
  window.showKeytagModal = function(id) {
    const milestone = NA_MILESTONES.find(m => m.id === id);
    if (!milestone) return;

    document.getElementById('modalKeytagTitle').textContent = milestone.name;
    document.getElementById('modalKeytagDays').textContent = milestone.days === 1 ? 'اليوم الأول أو الرغبة في الامتناع' : `${milestone.days} يوماً متصلاً`;
    document.getElementById('modalKeytagDesc').textContent = `${milestone.desc} — "${milestone.quote}"`;

    // Clone SVG Graphic into Modal
    const sourceEl = document.getElementById(milestone.elementId);
    const graphicContainer = document.getElementById('modalKeytagGraphic');
    if (sourceEl && graphicContainer) {
      const svg = sourceEl.querySelector('svg').cloneNode(true);
      svg.setAttribute('width', '110');
      svg.setAttribute('height', '160');
      graphicContainer.innerHTML = '';
      graphicContainer.appendChild(svg);
    }

    // Status Banner in Modal
    const statusEl = document.getElementById('modalKeytagStatus');
    if (currentCalculation) {
      const isEarned = (milestone.id === 'welcome' && currentCalculation.totalDays >= 0) ||
                       (milestone.id === 'multi' && currentCalculation.years >= 2) ||
                       (currentCalculation.totalDays >= milestone.days);
      if (isEarned) {
        statusEl.className = 'p-2 rounded-pill small fw-bold bg-success-subtle text-success border border-success-subtle';
        statusEl.innerHTML = '<i class="bi bi-patch-check-fill me-1"></i> تم تحقيق هذه الميدالية بنجاح في رحلتك المباركة!';
      } else {
        const left = Math.max(1, milestone.days - currentCalculation.totalDays);
        statusEl.className = 'p-2 rounded-pill small fw-bold bg-warning-subtle text-warning-emphasis border border-warning-subtle';
        statusEl.innerHTML = `<i class="bi bi-hourglass-split me-1"></i> في الطريق للتحقيق: متبقي ${left} يوماً لليوم فقط.`;
      }
    } else {
      statusEl.className = 'p-2 rounded-pill small fw-bold bg-light text-muted border';
      statusEl.innerHTML = 'أدخل تاريخ تعافيك لاكتشاف رصيدك من الميداليات.';
    }

    const modal = new bootstrap.Modal(document.getElementById('naKeytagDetailModal'));
    modal.show();
  };

  // ======================================================================
  // Anonymous Milestone Canvas Card Generator
  // ======================================================================
  window.openShareModal = function() {
    if (!currentCalculation) return;

    renderMilestoneCanvas();
    const modal = new bootstrap.Modal(document.getElementById('naShareModal'));
    modal.show();
  };

  function renderMilestoneCanvas() {
    const canvas = document.getElementById('naMilestoneCanvas');
    if (!canvas || !currentCalculation) return;
    const ctx = canvas.getContext('2d');
    const w = 1080;
    const h = 1080;

    ctx.clearRect(0, 0, w, h);

    // 1. Rich Radial Background (Deep NA Navy & Emerald)
    const bgGrad = ctx.createRadialGradient(w / 2, h / 2, 80, w / 2, h / 2, 700);
    bgGrad.addColorStop(0, '#0F2847');
    bgGrad.addColorStop(0.65, '#0B192C');
    bgGrad.addColorStop(1, '#050B14');
    ctx.fillStyle = bgGrad;
    ctx.fillRect(0, 0, w, h);

    // 2. Subtle Watermark Geometric Pattern (Circle & Diamond)
    ctx.save();
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.04)';
    ctx.lineWidth = 4;
    ctx.beginPath();
    ctx.arc(w / 2, h / 2, 380, 0, Math.PI * 2);
    ctx.stroke();

    ctx.translate(w / 2, h / 2);
    ctx.rotate(Math.PI / 4);
    ctx.strokeRect(-260, -260, 520, 520);
    ctx.restore();

    // 3. Subtle Outer Glow Frame
    ctx.strokeStyle = 'rgba(50, 85, 127, 0.35)';
    ctx.lineWidth = 3;
    ctx.strokeRect(36, 36, w - 72, h - 72);

    ctx.strokeStyle = 'rgba(234, 179, 8, 0.25)';
    ctx.lineWidth = 1;
    ctx.strokeRect(44, 44, w - 88, h - 88);

    // 4. Header: NA Egypt
    ctx.textAlign = 'center';
    ctx.fillStyle = '#EAB308';
    ctx.font = 'bold 26px "Cairo", sans-serif';
    ctx.fillText('زمالة المدمنين المجهولين — مصر', w / 2, 110);

    ctx.fillStyle = 'rgba(255, 255, 255, 0.65)';
    ctx.font = '500 18px "Cairo", sans-serif';
    ctx.fillText('Narcotics Anonymous Egypt • www.naegypt.org', w / 2, 142);

    // 5. Draw Keytag Centerpiece
    const milestone = currentCalculation.highestEarned;
    drawKeytagOnCanvas(ctx, w / 2, 320, milestone);

    // 6. Milestone Title
    ctx.fillStyle = '#FFFFFF';
    ctx.font = 'bold 36px "Cairo", sans-serif';
    ctx.fillText(milestone.name, w / 2, 530);

    // 7. Clean-Time Main Typography
    ctx.fillStyle = '#38BDF8';
    ctx.font = '900 48px "Cairo", sans-serif';
    ctx.fillText(currentCalculation.formattedText, w / 2, 615);

    // 8. Total Days Badge
    ctx.fillStyle = '#F8FAFC';
    ctx.font = 'bold 32px "Cairo", sans-serif';
    ctx.fillText(currentCalculation.totalDaysText, w / 2, 675);

    // 9. Humble Spiritual Quote Box
    ctx.fillStyle = 'rgba(255, 255, 255, 0.06)';
    roundRect(ctx, 140, 720, w - 280, 110, 16, true, false);

    ctx.fillStyle = '#FDE047';
    ctx.font = 'italic bold 24px "Cairo", sans-serif';
    ctx.fillText(`"${milestone.quote}"`, w / 2, 782);

    // 10. Footer / Anonymity Statement
    ctx.fillStyle = 'rgba(255, 255, 255, 0.5)';
    ctx.font = '500 18px "Cairo", sans-serif';
    ctx.fillText('لليوم فقط • بطاقة تعافي مجهولة الهوية وفقاً للتقاليد الإثني عشر', w / 2, 920);

    ctx.fillStyle = 'rgba(255, 255, 255, 0.35)';
    ctx.font = '400 16px "Cairo", sans-serif';
    ctx.fillText('Anonymity is the spiritual foundation of all our traditions', w / 2, 950);

    // 11. Subtle Date calculated on
    const dateStr = new Date().toLocaleDateString('ar-EG', { year: 'numeric', month: 'long', day: 'numeric' });
    ctx.fillStyle = 'rgba(50, 85, 127, 0.7)';
    ctx.font = '600 15px "Cairo", sans-serif';
    ctx.fillText(`تاريخ الحساب: ${dateStr}`, w / 2, 1005);
  }

  // Draw authentic Keytag on Canvas
  function drawKeytagOnCanvas(ctx, cx, cy, milestone) {
    ctx.save();
    ctx.translate(cx, cy);

    // Keyring
    ctx.beginPath();
    ctx.arc(0, -115, 24, 0, Math.PI * 2);
    ctx.strokeStyle = '#94A3B8';
    ctx.lineWidth = 6;
    ctx.stroke();

    // Body dimensions: 174w x 252h
    const bw = 174;
    const bh = 252;
    const topW = 100;

    // Drop Shadow
    ctx.shadowColor = 'rgba(0, 0, 0, 0.45)';
    ctx.shadowBlur = 24;
    ctx.shadowOffsetY = 12;

    ctx.beginPath();
    ctx.moveTo(-topW / 2, -bh / 2);
    ctx.lineTo(topW / 2, -bh / 2);
    ctx.lineTo(bw / 2, bh / 2 - 30);
    ctx.lineTo(0, bh / 2);
    ctx.lineTo(-bw / 2, bh / 2 - 30);
    ctx.closePath();

    ctx.fillStyle = milestone.color;
    ctx.fill();

    // Reset shadow
    ctx.shadowColor = 'transparent';
    ctx.shadowBlur = 0;
    ctx.shadowOffsetY = 0;

    // Border
    ctx.strokeStyle = milestone.id === 'welcome' ? '#94A3B8' : (milestone.id === 'multi' ? '#EAB308' : 'rgba(0,0,0,0.25)');
    ctx.lineWidth = 4;
    ctx.stroke();

    // NA Emblem on keytag: Circle + Rotated Square
    const symbolColor = milestone.textColor;
    ctx.strokeStyle = symbolColor;
    ctx.lineWidth = 4;

    ctx.beginPath();
    ctx.arc(0, -10, 42, 0, Math.PI * 2);
    ctx.stroke();

    ctx.save();
    ctx.translate(0, -10);
    ctx.rotate(Math.PI / 4);
    ctx.strokeRect(-28, -28, 56, 56);
    ctx.restore();

    // Text on keytag
    ctx.fillStyle = symbolColor;
    ctx.textAlign = 'center';
    ctx.font = '900 18px "Cairo", sans-serif';
    
    let tagText = 'JUST FOR TODAY';
    if (milestone.id === 'welcome') tagText = 'WELCOME';
    else if (milestone.id === '30d') tagText = '30 DAYS';
    else if (milestone.id === '60d') tagText = '60 DAYS';
    else if (milestone.id === '90d') tagText = '90 DAYS';
    else if (milestone.id === '6m') tagText = '6 MONTHS';
    else if (milestone.id === '9m') tagText = '9 MONTHS';
    else if (milestone.id === '1y') tagText = '1 YEAR';
    else if (milestone.id === '18m') tagText = '18 MONTHS';
    else if (milestone.id === 'multi') tagText = 'MULTIPLE';

    ctx.fillText(tagText, 0, 72);

    ctx.restore();
  }

  function roundRect(ctx, x, y, width, height, radius, fill, stroke) {
    ctx.beginPath();
    ctx.moveTo(x + radius, y);
    ctx.lineTo(x + width - radius, y);
    ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
    ctx.lineTo(x + width, y + height - radius);
    ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
    ctx.lineTo(x + radius, y + height);
    ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
    ctx.lineTo(x, y + radius);
    ctx.quadraticCurveTo(x, y, x + radius, y);
    ctx.closePath();
    if (fill) ctx.fill();
    if (stroke) ctx.stroke();
  }

  // Download Card
  window.downloadMilestoneCard = function() {
    const canvas = document.getElementById('naMilestoneCanvas');
    if (!canvas) return;

    const link = document.createElement('a');
    link.download = `NA-Egypt-CleanTime-${currentCalculation.totalDays}-Days.png`;
    link.href = canvas.toDataURL('image/png');
    link.click();
  };

  // Web Share API
  window.shareMilestoneCard = async function() {
    const canvas = document.getElementById('naMilestoneCanvas');
    if (!canvas || !currentCalculation) return;

    const textToShare = `احتفالاً بـ [${currentCalculation.formattedText}] من التعافي النظيف (${currentCalculation.totalDaysText}) لليوم فقط بفضل زمالة المدمنين المجهولين بمصر. معجزة التعافي تحدث يوماً بيوم! 🤍🕊️ https://naegypt.org/clean-time`;

    if (navigator.share && canvas.toBlob) {
      canvas.toBlob(async (blob) => {
        try {
          const file = new File([blob], 'NA-Clean-Time-Badge.png', { type: 'image/png' });
          if (navigator.canShare && navigator.canShare({ files: [file] })) {
            await navigator.share({
              title: 'بطاقة تعافي زمالة المدمنين المجهولين',
              text: textToShare,
              files: [file]
            });
            return;
          }
          await navigator.share({
            title: 'بطاقة تعافي زمالة المدمنين المجهولين',
            text: textToShare,
            url: 'https://naegypt.org/clean-time'
          });
        } catch (err) {
          // User cancelled or fallback
          downloadMilestoneCard();
        }
      });
    } else {
      // Fallback: Copy text and download
      copyMilestoneText();
      downloadMilestoneCard();
    }
  };

  // Copy Text
  window.copyMilestoneText = function() {
    if (!currentCalculation) return;
    const textToCopy = `احتفالاً بـ [${currentCalculation.formattedText}] من التعافي النظيف (${currentCalculation.totalDaysText}) لليوم فقط بفضل زمالة المدمنين المجهولين بمصر. معجزة التعافي تحدث يوماً بيوم! 🤍🕊️ https://naegypt.org/clean-time`;

    navigator.clipboard.writeText(textToCopy).then(() => {
      const fb = document.getElementById('ctCopyFeedback');
      if (fb) {
        fb.classList.remove('d-none');
        setTimeout(() => fb.classList.add('d-none'), 3000);
      }
    });
  };

  // Initialize with today or empty
  document.addEventListener('DOMContentLoaded', function() {
    // Check if URL has ?date=YYYY-MM-DD
    const urlParams = new URLSearchParams(window.location.search);
    const dateParam = urlParams.get('date');
    if (dateParam) {
      onNativeDateChange(dateParam);
    }
  });
})();
</script>
