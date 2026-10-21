@php
    $cairoNow = \Carbon\Carbon::now('Africa/Cairo');
    $cairoHour = (int) $cairoNow->format('H');
    // Active between 10:00 AM (10) and 10:00 PM (22)
    $isAvailable = ($cairoHour >= 10 && $cairoHour < 22);
    $locale = app()->getLocale();
@endphp

<div id="naEmergencyActionBar" class="na-emergency-bar-wrapper" role="region" aria-label="{{ __('messages.emergency_helpline_bar') ?? 'خط المساعدة المباشر لزمالة المدمنين المجهولين' }}">

  <!-- Lightweight Floating Popover for Additional Helpline Numbers -->
  <div id="naLinesPopoverCard" class="na-lines-popover-card" style="display: none;" role="dialog" aria-modal="false" aria-labelledby="naPopoverTitle">
    <div class="na-popover-inner">
      <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom border-white-10">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-telephone-outbound-fill text-info fs-6"></i>
          <span id="naPopoverTitle" class="fw-bold small text-white">{{ __('messages.direct_helplines_title') ?? 'خطوط المساعدة المباشرة' }}</span>
        </div>
        <button type="button" class="btn-close btn-close-white p-1" style="font-size: 0.65rem;" onclick="closeLinesPopover()" aria-label="{{ __('messages.close') ?? 'إغلاق' }}"></button>
      </div>

      <div class="d-flex flex-column gap-2">
        <!-- Alexandria & North Coast -->
        <div class="na-popover-item p-2 rounded-3">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <span class="text-white-50 x-small fw-semibold">{{ __('messages.helpline_alexandria') ?? 'خط الإسكندرية والساحل الشمالي' }}</span>
            <span class="badge bg-white-10 text-white rounded-pill x-small px-2 py-0.5">Alexandria</span>
          </div>
          <div class="d-flex align-items-center justify-content-between gap-2">
            <span class="fw-bold font-monospace text-white fs-6" dir="ltr">+20 150 388 4411</span>
            <div class="d-flex gap-1">
              <a href="tel:+201503884411" class="btn btn-sm btn-outline-info rounded-pill px-2.5 py-1 text-white x-small d-inline-flex align-items-center gap-1" title="{{ __('messages.call_now') ?? 'اتصال' }}">
                <i class="bi bi-telephone-fill"></i>
                <span>{{ __('messages.call_now') ?? 'اتصال' }}</span>
              </a>
              <a href="https://wa.me/201503884411" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success rounded-pill px-2.5 py-1 text-white x-small d-inline-flex align-items-center gap-1" title="WhatsApp">
                <i class="bi bi-whatsapp"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Regional Line 2 (WhatsApp & Phone) -->
        <div class="na-popover-item p-2 rounded-3">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <span class="text-white-50 x-small fw-semibold">{{ __('messages.helpline_regional_2') ?? 'الخط الساخن الإقليمي (مصر ٢ / واتساب)' }}</span>
            <span class="badge bg-success-subtle text-success rounded-pill x-small px-2 py-0.5">WhatsApp / Call</span>
          </div>
          <div class="d-flex align-items-center justify-content-between gap-2">
            <span class="fw-bold font-monospace text-white fs-6" dir="ltr">+20 106 093 3888</span>
            <div class="d-flex gap-1">
              <a href="tel:+201060933888" class="btn btn-sm btn-outline-info rounded-pill px-2.5 py-1 text-white x-small d-inline-flex align-items-center gap-1" title="{{ __('messages.call_now') ?? 'اتصال' }}">
                <i class="bi bi-telephone-fill"></i>
                <span>{{ __('messages.call_now') ?? 'اتصال' }}</span>
              </a>
              <a href="https://wa.me/201060933888" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success rounded-pill px-2.5 py-1 text-white x-small d-inline-flex align-items-center gap-1" title="WhatsApp">
                <i class="bi bi-whatsapp"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Regional Line 1 (Egypt 1) -->
        <div class="na-popover-item p-2 rounded-3">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <span class="text-white-50 x-small fw-semibold">{{ __('messages.helpline_regional_1') ?? 'الخط الساخن الإقليمي (مصر ١)' }}</span>
            <span class="badge bg-white-10 text-white rounded-pill x-small px-2 py-0.5">Hotline</span>
          </div>
          <div class="d-flex align-items-center justify-content-between gap-2">
            <span class="fw-bold font-monospace text-white fs-6" dir="ltr">+20 100 697 9198</span>
            <div class="d-flex gap-1">
              <a href="tel:+201006979198" class="btn btn-sm na-btn-primary rounded-pill px-2.5 py-1 text-white x-small d-inline-flex align-items-center gap-1" title="{{ __('messages.call_now') ?? 'اتصال' }}">
                <i class="bi bi-telephone-fill"></i>
                <span>{{ __('messages.call_now') ?? 'اتصال' }}</span>
              </a>
              <a href="https://wa.me/201060933888" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success rounded-pill px-2.5 py-1 text-white x-small d-inline-flex align-items-center gap-1" title="WhatsApp">
                <i class="bi bi-whatsapp"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Schedule note & Zoom link -->
      <div class="mt-2 pt-2 border-top border-white-10 d-flex flex-column gap-1">
        <div class="d-flex align-items-center gap-1 text-white-50 x-small">
          <i class="bi bi-clock-history text-warning"></i>
          <span>{{ __('messages.helpline_hours_short') ?? 'يومياً 10:00 ص - 10:00 م بتوقيت مصر' }}</span>
        </div>
        <div>
          <a href="{{ route('frontend.meetings', ['type' => 'online']) }}" class="text-info x-small text-decoration-underline d-inline-flex align-items-center gap-1">
            <i class="bi bi-camera-video"></i>
            <span>{{ __('messages.attend_online_meetings_notice') ?? 'خارج ساعات العمل؟ احضر اجتماع أونلاين الآن عبر زووم' }}</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Persistent Main Bar (Compact Floating Bar) -->
  <div class="container-fluid px-2 px-md-4 px-lg-5 position-relative" style="max-width: 1280px; margin: 0 auto;">
    
    <!-- DESKTOP BAR (md and up: sleek single row, zero overflow) -->
    <div class="na-emergency-main-bar na-emergency-desktop-bar d-none d-md-flex">
      <!-- Status Badge & Zoom Link -->
      <div class="d-flex align-items-center gap-2 flex-shrink-0">
        <div class="na-emergency-status-badge na-status-badge-element {{ $isAvailable ? 'online' : 'offline' }}">
          <span class="na-pulse-dot" aria-hidden="true"></span>
          <span class="na-status-text-element">
            @if($isAvailable)
              {{ __('messages.helpline_status_available') ?? 'متاح الآن للاتصال' }}
            @else
              {{ __('messages.helpline_status_offhours') ?? 'خط المساعدة غير متاح حالياً' }}
            @endif
          </span>
        </div>

        <a href="{{ route('frontend.meetings', ['type' => 'online']) }}" class="na-offhours-zoom-element badge rounded-pill bg-primary text-white text-decoration-none px-3 py-2 {{ $isAvailable ? 'd-none' : 'd-inline-flex' }} align-items-center gap-1 shadow-sm">
          <i class="bi bi-camera-video-fill"></i>
          <span>{{ __('messages.join_24_7_zoom') ?? 'اجتماع أونلاين الآن' }}</span>
        </a>
      </div>

      <!-- Quick Dial Direct Actions (Single Line) -->
      <div class="na-emergency-dial-chips flex-nowrap">
        <!-- Primary Hotline Chip -->
        <a href="tel:+201006979198" class="na-dial-chip na-dial-chip-call" title="{{ __('messages.helpline_regional_1') ?? 'الخط الساخن الإقليمي (مصر ١)' }}">
          <i class="bi bi-telephone-fill text-warning"></i>
          <span class="d-none d-lg-inline text-white-50 small">{{ __('messages.cairo_region') ?? 'مصر' }}:</span>
          <span class="fw-bold font-monospace" dir="ltr">0100 697 9198</span>
        </a>

        <!-- WhatsApp Chip -->
        <a href="https://wa.me/201060933888" target="_blank" rel="noopener noreferrer" class="na-dial-chip na-dial-chip-wa" title="WhatsApp">
          <i class="bi bi-whatsapp"></i>
          <span>{{ __('messages.whatsapp') ?? 'واتساب' }}</span>
        </a>

        <!-- More Numbers Popover Trigger -->
        <div class="na-lines-popover-anchor position-relative d-inline-block">
          <button type="button" class="na-dial-chip na-dial-chip-more" onclick="toggleLinesPopover(event)" id="naLinesDesktopToggle" aria-expanded="false" aria-haspopup="dialog" aria-controls="naLinesPopoverCard" title="{{ __('messages.more_helpline_numbers') ?? 'أرقام أخرى' }}">
            <i class="bi bi-grid-fill text-info"></i>
            <span>{{ __('messages.more_helpline_numbers') ?? 'أرقام أخرى' }}</span>
            <i class="bi bi-chevron-up transition-transform na-popover-chevron ms-1" style="font-size: 0.72rem;"></i>
          </button>
        </div>
      </div>

      <!-- Anonymity Seal & Minimize Button -->
      <div class="d-flex align-items-center gap-2 flex-shrink-0">
        <div class="na-anonymity-seal-micro d-none d-xl-flex">
          <i class="bi bi-shield-check text-success fs-6"></i>
          <span>{{ __('messages.anonymity_assurance_micro') ?? 'مكالمتك سرية ومجهولة بالكامل' }}</span>
        </div>

        <button type="button" class="na-emergency-minimize-btn" onclick="toggleEmergencyBarMinimize()" aria-label="{{ __('messages.minimize_helpline_bar') ?? 'تصغير شريط المساعدة' }}" title="{{ __('messages.minimize_helpline_bar') ?? 'تصغير شريط المساعدة' }}">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    </div>

    <!-- MOBILE BAR (under md: streamlined 3-action buttons + compact status, zero overflow) -->
    <div class="na-emergency-main-bar na-emergency-mobile-bar d-flex d-md-none">
      <!-- Compact Status Dot & Offhours Zoom -->
      <div class="d-flex align-items-center gap-1 flex-shrink-0">
        <div class="na-emergency-status-badge na-status-badge-element na-mobile-status-badge {{ $isAvailable ? 'online' : 'offline' }}" title="{{ $isAvailable ? ((string) __('messages.helpline_status_available')) : ((string) __('messages.helpline_status_offhours')) }}">
          <span class="na-pulse-dot" aria-hidden="true"></span>
          <span class="na-mobile-status-text na-status-text-element">
            {{ $isAvailable ? ((string) __('messages.helpline_status_available_short')) : ((string) __('messages.helpline_status_offhours_short')) }}
          </span>
        </div>

        <a href="{{ route('frontend.meetings', ['type' => 'online']) }}" class="na-offhours-zoom-element na-mobile-zoom-btn {{ $isAvailable ? 'd-none' : 'd-inline-flex' }}" title="{{ __('messages.join_24_7_zoom') ?? 'اجتماع أونلاين الآن' }}" aria-label="{{ __('messages.join_24_7_zoom') ?? 'اجتماع أونلاين' }}">
          <i class="bi bi-camera-video-fill"></i>
        </a>
      </div>

      <!-- Streamlined Quick Actions (Call, WhatsApp, More Lines, Minimize) -->
      <div class="na-mobile-actions-deck flex-nowrap">
        <a href="tel:+201006979198" class="na-mobile-btn na-mobile-btn-call" aria-label="{{ __('messages.call_now') ?? 'اتصال' }}">
          <i class="bi bi-telephone-fill"></i>
          <span>{{ __('messages.call_now') ?? 'اتصال' }}</span>
        </a>

        <a href="https://wa.me/201060933888" target="_blank" rel="noopener noreferrer" class="na-mobile-btn na-mobile-btn-wa" aria-label="WhatsApp">
          <i class="bi bi-whatsapp"></i>
          <span>{{ __('messages.whatsapp') ?? 'واتساب' }}</span>
        </a>

        <button type="button" class="na-mobile-btn na-mobile-btn-lines" onclick="toggleLinesPopover(event)" id="naLinesMobileToggle" aria-expanded="false" aria-haspopup="dialog" aria-controls="naLinesPopoverCard" aria-label="{{ __('messages.more_helpline_numbers') ?? 'أرقام أخرى' }}">
          <i class="bi bi-grid-fill text-info"></i>
          <span>{{ __('messages.more_helpline_numbers') ?? 'أرقام أخرى' }}</span>
          <i class="bi bi-chevron-up transition-transform na-popover-chevron ms-1" style="font-size: 0.7rem;"></i>
        </button>

        <button type="button" class="na-mobile-btn na-mobile-btn-minimize" onclick="toggleEmergencyBarMinimize()" aria-label="{{ __('messages.minimize_helpline_bar') ?? 'تصغير' }}" title="{{ __('messages.minimize_helpline_bar') ?? 'تصغير' }}">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    </div>

  </div>
</div>

<!-- Floating Restore Trigger Pill when Minimized -->
<button type="button" id="naEmergencyRestoreBtn" class="na-emergency-restore-pill d-none" onclick="toggleEmergencyBarMinimize()" aria-label="{{ __('messages.show_helpline_bar') ?? 'إظهار خط المساعدة' }}" title="{{ __('messages.show_helpline_bar') ?? 'إظهار خط المساعدة' }}">
  <span class="na-pulse-dot" aria-hidden="true"></span>
  <i class="bi bi-telephone-fill text-warning"></i>
  <span>{{ __('messages.emergency_helpline_short') ?? 'خط المساعدة' }}</span>
  <i class="bi bi-chevron-up ms-1"></i>
</button>

<script>
  function updateEmergencyBarOffset() {
    const bar = document.getElementById('naEmergencyActionBar');
    if (!bar) return;
    const isMinimized = bar.classList.contains('minimized');
    if (isMinimized) {
      document.documentElement.style.setProperty('--na-emergency-bar-height', '0px');
      return;
    }

    const isMobile = window.innerWidth < 768;
    const mainBar = isMobile
      ? bar.querySelector('.na-emergency-mobile-bar')
      : bar.querySelector('.na-emergency-desktop-bar');

    const baseHeight = isMobile ? 50 : 54;
    const measuredHeight = (mainBar && mainBar.offsetHeight > 0) ? mainBar.offsetHeight : baseHeight;
    // Strictly clamp between 40px and 60px to prevent any layout runaway
    const clampedHeight = Math.min(Math.max(measuredHeight, 40), 60);

    document.documentElement.style.setProperty('--na-emergency-bar-height', clampedHeight + 'px');
  }

  function toggleEmergencyBarMinimize() {
    const bar = document.getElementById('naEmergencyActionBar');
    const restoreBtn = document.getElementById('naEmergencyRestoreBtn');
    if (!bar) return;

    // If popover is open, close it before minimizing
    closeLinesPopover();

    const isMinimized = bar.classList.toggle('minimized');
    if (restoreBtn) {
      if (isMinimized) {
        restoreBtn.classList.remove('d-none');
      } else {
        restoreBtn.classList.add('d-none');
      }
    }

    try {
      sessionStorage.setItem('na_emergency_bar_minimized', isMinimized ? '1' : '0');
    } catch(e) {}

    updateEmergencyBarOffset();
  }

  function toggleLinesPopover(e) {
    if (e) {
      e.stopPropagation();
      e.preventDefault();
    }
    const popover = document.getElementById('naLinesPopoverCard');
    const desktopBtn = document.getElementById('naLinesDesktopToggle');
    const mobileBtn = document.getElementById('naLinesMobileToggle');
    if (!popover) return;

    const isOpen = popover.classList.contains('open');
    if (isOpen) {
      closeLinesPopover();
    } else {
      popover.style.display = 'block';
      requestAnimationFrame(() => {
        popover.classList.add('open');
      });
      if (desktopBtn) desktopBtn.setAttribute('aria-expanded', 'true');
      if (mobileBtn) mobileBtn.setAttribute('aria-expanded', 'true');
      document.querySelectorAll('.na-popover-chevron').forEach(ch => ch.style.transform = 'rotate(180deg)');
    }
  }

  function closeLinesPopover() {
    const popover = document.getElementById('naLinesPopoverCard');
    const desktopBtn = document.getElementById('naLinesDesktopToggle');
    const mobileBtn = document.getElementById('naLinesMobileToggle');
    if (!popover) return;

    popover.classList.remove('open');
    document.querySelectorAll('.na-popover-chevron').forEach(ch => ch.style.transform = 'rotate(0deg)');
    if (desktopBtn) desktopBtn.setAttribute('aria-expanded', 'false');
    if (mobileBtn) mobileBtn.setAttribute('aria-expanded', 'false');

    setTimeout(() => {
      if (!popover.classList.contains('open')) {
        popover.style.display = 'none';
      }
    }, 200);
  }

  // Close popover when clicking outside
  document.addEventListener('click', function(e) {
    const popover = document.getElementById('naLinesPopoverCard');
    if (!popover || popover.style.display === 'none') return;
    const isInside = popover.contains(e.target);
    const isDesktopToggle = e.target.closest('#naLinesDesktopToggle');
    const isMobileToggle = e.target.closest('#naLinesMobileToggle');
    if (!isInside && !isDesktopToggle && !isMobileToggle) {
      closeLinesPopover();
    }
  });

  // Escape key closes popover
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeLinesPopover();
    }
  });

  // Client-Side Egypt Time Verifier to handle CDN/Edge Cache Time Drift
  document.addEventListener('DOMContentLoaded', function() {
    // Restore minimized state on page load if user previously minimized
    try {
      if (sessionStorage.getItem('na_emergency_bar_minimized') === '1') {
        const bar = document.getElementById('naEmergencyActionBar');
        const restoreBtn = document.getElementById('naEmergencyRestoreBtn');
        if (bar) bar.classList.add('minimized');
        if (restoreBtn) restoreBtn.classList.remove('d-none');
      }
    } catch(e) {}

    updateEmergencyBarOffset();
    window.addEventListener('resize', updateEmergencyBarOffset);
    window.addEventListener('orientationchange', updateEmergencyBarOffset);

    try {
      const now = new Date();
      // Format current hour in Africa/Cairo timezone
      const cairoFormatter = new Intl.DateTimeFormat('en-US', {
        timeZone: 'Africa/Cairo',
        hour: 'numeric',
        hourCycle: 'h23'
      });
      const cairoHour = parseInt(cairoFormatter.format(now), 10);
      const isOnline = (cairoHour >= 10 && cairoHour < 22);

      const badges = document.querySelectorAll('.na-status-badge-element');
      const texts = document.querySelectorAll('.na-status-text-element');
      const offhoursLinks = document.querySelectorAll('.na-offhours-zoom-element');

      badges.forEach(badge => {
        if (isOnline) {
          badge.classList.remove('offline');
          badge.classList.add('online');
        } else {
          badge.classList.remove('online');
          badge.classList.add('offline');
        }
      });

      texts.forEach(textEl => {
        const isMobileText = textEl.classList.contains('na-mobile-status-text');
        if (isOnline) {
          textEl.textContent = isMobileText
            ? "{{ __('messages.helpline_status_available_short') ?? 'متاح' }}"
            : "{{ __('messages.helpline_status_available') ?? 'متاح الآن للاتصال' }}";
        } else {
          textEl.textContent = isMobileText
            ? "{{ __('messages.helpline_status_offhours_short') ?? 'مغلق' }}"
            : "{{ __('messages.helpline_status_offhours') ?? 'خط المساعدة غير متاح حالياً' }}";
        }
      });

      offhoursLinks.forEach(link => {
        if (isOnline) {
          link.classList.add('d-none');
          link.classList.remove('d-inline-flex');
        } else {
          link.classList.remove('d-none');
          link.classList.add('d-inline-flex');
        }
      });
    } catch(e) {
      console.warn('Egypt time formatting error', e);
    }
  });
</script>
