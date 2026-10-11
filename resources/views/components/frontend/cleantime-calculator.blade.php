<section id="cleantime-section" class="na-calc-card mb-5" aria-labelledby="calc-heading">
  <div class="row align-items-center mb-4">
    <div class="col-12 col-lg-7">
      <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="background: rgba(27, 42, 74, 0.08); color: var(--na-navy-800); font-size: 0.85rem; font-weight: 700;">
        <i class="bi bi-award-fill text-warning"></i>
        <span>{{ __('messages.clean_time_calculator_badge') ?? 'حاسبة مدة التعافي وميداليات الزمالة' }}</span>
      </div>
      <h2 id="calc-heading" class="na-heading-2 mb-1">
        {{ __('messages.calculate_clean_time_title') ?? 'احسب أيام تعافيك واحتفل برحلتك' }}
      </h2>
      <p class="text-muted small mb-0">
        {{ __('messages.clean_time_subtitle') ?? 'كل يوم نظيف وبدون مخدرات هو معجزة وانتصار. أدخل تاريخ أول يوم امتناع لمعرفة رصيدك وعلامات التعافي التي اجتزتها.' }}
      </p>
    </div>

    <!-- Date Input Picker -->
    <div class="col-12 col-lg-5 mt-3 mt-lg-0">
      <div class="p-3 rounded-4 shadow-sm" style="background: var(--na-canvas); border: 1.5px solid var(--na-border-strong);">
        <label for="cleanDateInput" class="form-label fw-bold small text-secondary d-flex align-items-center justify-content-between mb-2">
          <span><i class="bi bi-calendar-heart-fill me-1 text-danger"></i> {{ __('messages.select_clean_date') ?? 'اختر تاريخ التعافي (أول يوم امتناع)' }}</span>
          <span class="badge bg-light text-muted border font-monospace small" id="todayHint">اليوم</span>
        </label>
        <input type="date" id="cleanDateInput" class="form-control form-control-lg text-center fw-bold font-monospace shadow-none" 
               style="border-radius: 12px; border: 1.5px solid #cbd5e1; font-size: 1.15rem;" 
               onchange="computeCleanTime(this.value)" 
               max="{{ date('Y-m-d') }}">
      </div>
    </div>
  </div>

  <!-- Real-time Counters Display -->
  <div class="row g-3 justify-content-center text-center mt-1">
    <!-- Years -->
    <div class="col-6 col-md-3">
      <div class="na-calc-stat-pill">
        <div id="calcYearsVal" class="na-calc-number">0</div>
        <div class="na-calc-label">{{ __('messages.years') ?? 'سنوات' }}</div>
      </div>
    </div>

    <!-- Months -->
    <div class="col-6 col-md-3">
      <div class="na-calc-stat-pill">
        <div id="calcMonthsVal" class="na-calc-number">0</div>
        <div class="na-calc-label">{{ __('messages.months') ?? 'شهور' }}</div>
      </div>
    </div>

    <!-- Days -->
    <div class="col-6 col-md-3">
      <div class="na-calc-stat-pill">
        <div id="calcDaysVal" class="na-calc-number">0</div>
        <div class="na-calc-label">{{ __('messages.days') ?? 'أيام' }}</div>
      </div>
    </div>

    <!-- Total Days -->
    <div class="col-6 col-md-3">
      <div class="na-calc-stat-pill" style="background: var(--na-teal-50); border-color: var(--na-teal-100);">
        <div id="calcTotalDaysVal" class="na-calc-number" style="color: var(--na-teal-700);">0</div>
        <div class="na-calc-label text-teal-800 fw-bold">{{ __('messages.total_days') ?? 'إجمالي الأيام' }}</div>
      </div>
    </div>
  </div>

  <!-- Interactive Milestone Journey & Keytags Track -->
  <div class="na-keytag-track-wrapper">
    <div class="d-flex align-items-center justify-content-between mb-3 px-1">
      <div class="fw-bold small text-secondary">
        <i class="bi bi-gem text-info me-1"></i>
        <span>{{ __('messages.milestones_track_title') ?? 'رحلة ميداليات الزمالة (Keytags)' }}</span>
      </div>
      <div class="text-muted small">
        <span id="milestonePromptText">{{ __('messages.select_date_to_illuminate') ?? 'أدخل تاريخك لإضاءة الميداليات المكتسبة' }}</span>
      </div>
    </div>

    <div class="na-keytag-track" id="keytagTrack">
      <!-- 1. Welcome / Just For Today (White) -->
      <div class="na-keytag-item" id="kt-welcome" data-days="1" data-name="{{ __('messages.kt_welcome_name') ?? 'ترحيب / لليوم فقط' }}" data-color="#FFFFFF" data-desc="{{ __('messages.kt_welcome_desc') ?? 'الرغبة في الامتناع، بداية حياة جديدة وباب أمل مفتوح.' }}">
        <div class="na-keytag-svg-wrap">
          <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
            <circle cx="29" cy="8" r="6" fill="none" stroke="#94a3b8" stroke-width="2.5" />
            <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#F8FAFC" stroke="#cbd5e1" stroke-width="2" />
            <circle cx="29" cy="42" r="14" fill="none" stroke="#1e293b" stroke-width="1.8" />
            <rect x="20" y="33" width="18" height="18" fill="none" stroke="#1e293b" stroke-width="1.5" transform="rotate(45 29 42)" />
            <text x="29" y="66" text-anchor="middle" font-size="7" font-weight="800" fill="#1e293b" font-family="sans-serif">WELCOME</text>
          </svg>
        </div>
        <div class="na-keytag-label">{{ __('messages.kt_welcome') ?? 'ترحيب' }}</div>
      </div>

      <!-- 2. 30 Days (Orange) -->
      <div class="na-keytag-item" id="kt-30d" data-days="30" data-name="{{ __('messages.kt_30d_name') ?? '30 يوماً' }}" data-color="#EA580C" data-desc="{{ __('messages.kt_30d_desc') ?? 'شهر كامل من التعافي والصفاء الذهني المستمر.' }}">
        <div class="na-keytag-svg-wrap">
          <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
            <circle cx="29" cy="8" r="6" fill="none" stroke="#94a3b8" stroke-width="2.5" />
            <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#EA580C" stroke="#c2410c" stroke-width="2" />
            <circle cx="29" cy="42" r="14" fill="none" stroke="#ffffff" stroke-width="1.8" />
            <rect x="20" y="33" width="18" height="18" fill="none" stroke="#ffffff" stroke-width="1.5" transform="rotate(45 29 42)" />
            <text x="29" y="66" text-anchor="middle" font-size="7" font-weight="800" fill="#ffffff" font-family="sans-serif">30 DAYS</text>
          </svg>
        </div>
        <div class="na-keytag-label">30 {{ __('messages.day_singular') ?? 'يوم' }}</div>
      </div>

      <!-- 3. 60 Days (Green) -->
      <div class="na-keytag-item" id="kt-60d" data-days="60" data-name="{{ __('messages.kt_60d_name') ?? '60 يوماً' }}" data-color="#16A34A" data-desc="{{ __('messages.kt_60d_desc') ?? 'شهران من النمو الروحي وترسيخ مبادئ الزمالة.' }}">
        <div class="na-keytag-svg-wrap">
          <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
            <circle cx="29" cy="8" r="6" fill="none" stroke="#94a3b8" stroke-width="2.5" />
            <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#16A34A" stroke="#15803d" stroke-width="2" />
            <circle cx="29" cy="42" r="14" fill="none" stroke="#ffffff" stroke-width="1.8" />
            <rect x="20" y="33" width="18" height="18" fill="none" stroke="#ffffff" stroke-width="1.5" transform="rotate(45 29 42)" />
            <text x="29" y="66" text-anchor="middle" font-size="7" font-weight="800" fill="#ffffff" font-family="sans-serif">60 DAYS</text>
          </svg>
        </div>
        <div class="na-keytag-label">60 {{ __('messages.day_singular') ?? 'يوم' }}</div>
      </div>

      <!-- 4. 90 Days (Red) -->
      <div class="na-keytag-item" id="kt-90d" data-days="90" data-name="{{ __('messages.kt_90d_name') ?? '90 يوماً' }}" data-color="#DC2626" data-desc="{{ __('messages.kt_90d_desc') ?? 'ثلاثة أشهر، أساس متين وركيزة هامة في التعافي.' }}">
        <div class="na-keytag-svg-wrap">
          <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
            <circle cx="29" cy="8" r="6" fill="none" stroke="#94a3b8" stroke-width="2.5" />
            <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#DC2626" stroke="#b91c1c" stroke-width="2" />
            <circle cx="29" cy="42" r="14" fill="none" stroke="#ffffff" stroke-width="1.8" />
            <rect x="20" y="33" width="18" height="18" fill="none" stroke="#ffffff" stroke-width="1.5" transform="rotate(45 29 42)" />
            <text x="29" y="66" text-anchor="middle" font-size="7" font-weight="800" fill="#ffffff" font-family="sans-serif">90 DAYS</text>
          </svg>
        </div>
        <div class="na-keytag-label">90 {{ __('messages.day_singular') ?? 'يوم' }}</div>
      </div>

      <!-- 5. 6 Months (Blue) -->
      <div class="na-keytag-item" id="kt-6m" data-days="182" data-name="{{ __('messages.kt_6m_name') ?? '6 شهور' }}" data-color="#2563EB" data-desc="{{ __('messages.kt_6m_desc') ?? 'نصف عام كامل من الحرية والسلام الداخلي.' }}">
        <div class="na-keytag-svg-wrap">
          <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
            <circle cx="29" cy="8" r="6" fill="none" stroke="#94a3b8" stroke-width="2.5" />
            <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#2563EB" stroke="#1d4ed8" stroke-width="2" />
            <circle cx="29" cy="42" r="14" fill="none" stroke="#ffffff" stroke-width="1.8" />
            <rect x="20" y="33" width="18" height="18" fill="none" stroke="#ffffff" stroke-width="1.5" transform="rotate(45 29 42)" />
            <text x="29" y="66" text-anchor="middle" font-size="7" font-weight="800" fill="#ffffff" font-family="sans-serif">6 MONTHS</text>
          </svg>
        </div>
        <div class="na-keytag-label">6 {{ __('messages.months_short') ?? 'شهور' }}</div>
      </div>

      <!-- 6. 9 Months (Yellow) -->
      <div class="na-keytag-item" id="kt-9m" data-days="273" data-name="{{ __('messages.kt_9m_name') ?? '9 شهور' }}" data-color="#CA8A04" data-desc="{{ __('messages.kt_9m_desc') ?? 'ثلاثة أرباع العام، نضج روحي والتزام واثق بالبرنامج.' }}">
        <div class="na-keytag-svg-wrap">
          <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
            <circle cx="29" cy="8" r="6" fill="none" stroke="#94a3b8" stroke-width="2.5" />
            <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#EAB308" stroke="#ca8a04" stroke-width="2" />
            <circle cx="29" cy="42" r="14" fill="none" stroke="#1e293b" stroke-width="1.8" />
            <rect x="20" y="33" width="18" height="18" fill="none" stroke="#1e293b" stroke-width="1.5" transform="rotate(45 29 42)" />
            <text x="29" y="66" text-anchor="middle" font-size="7" font-weight="800" fill="#1e293b" font-family="sans-serif">9 MONTHS</text>
          </svg>
        </div>
        <div class="na-keytag-label">9 {{ __('messages.months_short') ?? 'شهور' }}</div>
      </div>

      <!-- 7. 1 Year (Pearl White Glow) -->
      <div class="na-keytag-item" id="kt-1y" data-days="365" data-name="{{ __('messages.kt_1y_name') ?? 'سنة كاملة' }}" data-color="#0284C7" data-desc="{{ __('messages.kt_1y_desc') ?? '365 يوماً من النقاء، حياة جديدة بالكامل ومعجزة تتحقق.' }}">
        <div class="na-keytag-svg-wrap">
          <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
            <circle cx="29" cy="8" r="6" fill="none" stroke="#94a3b8" stroke-width="2.5" />
            <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#F0F9FF" stroke="#38BDF8" stroke-width="2.5" />
            <circle cx="29" cy="42" r="14" fill="none" stroke="#0284C7" stroke-width="2" />
            <rect x="20" y="33" width="18" height="18" fill="none" stroke="#0284C7" stroke-width="1.5" transform="rotate(45 29 42)" />
            <text x="29" y="66" text-anchor="middle" font-size="7.5" font-weight="900" fill="#0284C7" font-family="sans-serif">1 YEAR</text>
          </svg>
        </div>
        <div class="na-keytag-label">{{ __('messages.one_year') ?? 'سنة' }}</div>
      </div>

      <!-- 8. 18 Months (Grey) -->
      <div class="na-keytag-item" id="kt-18m" data-days="547" data-name="{{ __('messages.kt_18m_name') ?? '18 شهراً' }}" data-color="#64748B" data-desc="{{ __('messages.kt_18m_desc') ?? 'عام ونصف من الثبات والاستمرار في طريق التعافي.' }}">
        <div class="na-keytag-svg-wrap">
          <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
            <circle cx="29" cy="8" r="6" fill="none" stroke="#94a3b8" stroke-width="2.5" />
            <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#64748B" stroke="#475569" stroke-width="2" />
            <circle cx="29" cy="42" r="14" fill="none" stroke="#ffffff" stroke-width="1.8" />
            <rect x="20" y="33" width="18" height="18" fill="none" stroke="#ffffff" stroke-width="1.5" transform="rotate(45 29 42)" />
            <text x="29" y="66" text-anchor="middle" font-size="6.5" font-weight="800" fill="#ffffff" font-family="sans-serif">18 MONTHS</text>
          </svg>
        </div>
        <div class="na-keytag-label">18 {{ __('messages.months_short') ?? 'شهراً' }}</div>
      </div>

      <!-- 9. Multiple Years (Black) -->
      <div class="na-keytag-item" id="kt-multi" data-days="730" data-name="{{ __('messages.kt_multi_name') ?? 'سنوات متعددة' }}" data-color="#0F172A" data-desc="{{ __('messages.kt_multi_desc') ?? 'سنوات متتالية من الامتنان والعطاء في خدمة الزمالة.' }}">
        <div class="na-keytag-svg-wrap">
          <svg viewBox="0 0 58 84" width="58" height="84" aria-hidden="true">
            <circle cx="29" cy="8" r="6" fill="none" stroke="#94a3b8" stroke-width="2.5" />
            <path d="M 12 18 L 46 18 L 52 72 L 29 80 L 6 72 Z" fill="#0F172A" stroke="#EAB308" stroke-width="2" />
            <circle cx="29" cy="42" r="14" fill="none" stroke="#EAB308" stroke-width="1.8" />
            <rect x="20" y="33" width="18" height="18" fill="none" stroke="#EAB308" stroke-width="1.5" transform="rotate(45 29 42)" />
            <text x="29" y="66" text-anchor="middle" font-size="6.5" font-weight="800" fill="#EAB308" font-family="sans-serif">MULTIPLE</text>
          </svg>
        </div>
        <div class="na-keytag-label">{{ __('messages.multi_years') ?? 'سنوات' }}</div>
      </div>
    </div>
  </div>

  <!-- Active Earned Milestone Reflection Card -->
  <div id="milestoneHighlightContainer" class="na-current-milestone-highlight d-none">
    <div class="d-flex align-items-center gap-3">
      <div class="p-2 rounded-3 bg-white shadow-xs">
        <i class="bi bi-patch-check-fill text-success fs-3"></i>
      </div>
      <div>
        <div class="small text-muted fw-bold">{{ __('messages.highest_milestone_earned') ?? 'أعلى علامة تعافي مكتسبة حتى اليوم:' }}</div>
        <div id="highlightMilestoneTitle" class="fs-5 fw-bold text-dark"></div>
        <div id="highlightMilestoneDesc" class="small text-secondary mt-1"></div>
      </div>
    </div>

    <div class="text-start text-sm-end" id="nextMilestoneTargetBox">
      <div class="small text-muted">{{ __('messages.next_target_milestone') ?? 'المحطة القادمة:' }}</div>
      <div id="nextMilestoneTitle" class="fw-bold text-primary"></div>
      <div id="nextMilestoneRemaining" class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-3 py-1.5 mt-1"></div>
    </div>
  </div>
</section>

<script>
  const milestonesData = [
    { id: 'kt-welcome', days: 1, name: "{{ __('messages.kt_welcome_name') ?? 'ترحيب / لليوم فقط' }}", desc: "{{ __('messages.kt_welcome_desc') ?? 'الرغبة في الامتناع، بداية حياة جديدة وباب أمل مفتوح.' }}" },
    { id: 'kt-30d', days: 30, name: "{{ __('messages.kt_30d_name') ?? '30 يوماً' }}", desc: "{{ __('messages.kt_30d_desc') ?? 'شهر كامل من التعافي والصفاء الذهني المستمر.' }}" },
    { id: 'kt-60d', days: 60, name: "{{ __('messages.kt_60d_name') ?? '60 يوماً' }}", desc: "{{ __('messages.kt_60d_desc') ?? 'شهران من النمو الروحي وترسيخ مبادئ الزمالة.' }}" },
    { id: 'kt-90d', days: 90, name: "{{ __('messages.kt_90d_name') ?? '90 يوماً' }}", desc: "{{ __('messages.kt_90d_desc') ?? 'ثلاثة أشهر، أساس متين وركيزة هامة في التعافي.' }}" },
    { id: 'kt-6m', days: 182, name: "{{ __('messages.kt_6m_name') ?? '6 شهور' }}", desc: "{{ __('messages.kt_6m_desc') ?? 'نصف عام كامل من الحرية والسلام الداخلي.' }}" },
    { id: 'kt-9m', days: 273, name: "{{ __('messages.kt_9m_name') ?? '9 شهور' }}", desc: "{{ __('messages.kt_9m_desc') ?? 'ثلاثة أرباع العام، نضج روحي والتزام واثق بالبرنامج.' }}" },
    { id: 'kt-1y', days: 365, name: "{{ __('messages.kt_1y_name') ?? 'سنة كاملة' }}", desc: "{{ __('messages.kt_1y_desc') ?? '365 يوماً من النقاء، حياة جديدة بالكامل ومعجزة تتحقق.' }}" },
    { id: 'kt-18m', days: 547, name: "{{ __('messages.kt_18m_name') ?? '18 شهراً' }}", desc: "{{ __('messages.kt_18m_desc') ?? 'عام ونصف من الثبات والاستمرار في طريق التعافي.' }}" },
    { id: 'kt-multi', days: 730, name: "{{ __('messages.kt_multi_name') ?? 'سنوات متعددة' }}", desc: "{{ __('messages.kt_multi_desc') ?? 'سنوات متتالية من الامتنان والعطاء في خدمة الزمالة.' }}" },
  ];

  function computeCleanTime(dateStr) {
    if (!dateStr) return;
    const cleanDate = new Date(dateStr);
    const today = new Date();

    if (isNaN(cleanDate.getTime()) || cleanDate > today) return;

    // Calculate calendar years, months, days
    let years = today.getFullYear() - cleanDate.getFullYear();
    let months = today.getMonth() - cleanDate.getMonth();
    let days = today.getDate() - cleanDate.getDate();

    if (days < 0) {
      months--;
      const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
      days += prevMonth.getDate();
    }

    if (months < 0) {
      years--;
      months += 12;
    }

    // Total clean days
    const diffTime = Math.abs(today - cleanDate);
    const totalCleanDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

    // Update Counter Elements
    document.getElementById('calcYearsVal').textContent = years;
    document.getElementById('calcMonthsVal').textContent = months;
    document.getElementById('calcDaysVal').textContent = days;
    document.getElementById('calcTotalDaysVal').textContent = totalCleanDays;

    // Update Keytags Illumination
    let highestMilestone = null;
    let nextMilestone = null;

    milestonesData.forEach((m, idx) => {
      const el = document.getElementById(m.id);
      if (!el) return;

      el.classList.remove('earned', 'current-milestone');

      if (totalCleanDays >= m.days) {
        el.classList.add('earned');
        highestMilestone = m;
      } else if (!nextMilestone && totalCleanDays < m.days) {
        nextMilestone = m;
      }
    });

    if (highestMilestone) {
      const currentEl = document.getElementById(highestMilestone.id);
      if (currentEl) currentEl.classList.add('current-milestone');

      // Highlight Container
      const highlightContainer = document.getElementById('milestoneHighlightContainer');
      const titleEl = document.getElementById('highlightMilestoneTitle');
      const descEl = document.getElementById('highlightMilestoneDesc');
      const nextTitleEl = document.getElementById('nextMilestoneTitle');
      const nextRemainingEl = document.getElementById('nextMilestoneRemaining');

      if (highlightContainer && titleEl && descEl) {
        highlightContainer.classList.remove('d-none');
        titleEl.textContent = highestMilestone.name;
        descEl.textContent = highestMilestone.desc;

        if (nextMilestone && nextTitleEl && nextRemainingEl) {
          const daysLeft = nextMilestone.days - totalCleanDays;
          nextTitleEl.textContent = nextMilestone.name;
          nextRemainingEl.textContent = `${daysLeft} {{ __('messages.days_remaining_to_next') ?? 'يوماً متبقياً' }}`;
          document.getElementById('nextMilestoneTargetBox')?.classList.remove('d-none');
        } else {
          document.getElementById('nextMilestoneTargetBox')?.classList.add('d-none');
        }
      }
    }
  }

  // Allow clicking on any keytag directly to preview its significance
  document.addEventListener('DOMContentLoaded', function() {
    milestonesData.forEach(m => {
      const el = document.getElementById(m.id);
      if (el) {
        el.addEventListener('click', () => {
          const prompt = document.getElementById('milestonePromptText');
          if (prompt) {
            prompt.textContent = `${m.name}: ${m.desc}`;
          }
        });
      }
    });
  });
</script>
