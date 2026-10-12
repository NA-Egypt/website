@php
    $cairoNow = \Carbon\Carbon::now('Africa/Cairo');
    $cairoHour = (int) $cairoNow->format('H');
    // Active between 10:00 AM (10) and 10:00 PM (22)
    $isAvailable = ($cairoHour >= 10 && $cairoHour < 22);
    $locale = app()->getLocale();
@endphp

<div id="naEmergencyActionBar" class="na-emergency-bar-wrapper" role="region" aria-label="{{ __('messages.emergency_helpline_bar') ?? 'خط المساعدة المباشر لزمالة المدمنين المجهولين' }}">
  <!-- Expandable Helpline Drawer Sheet -->
  <div id="naEmergencyDrawer" class="na-emergency-drawer-sheet" aria-hidden="true">
    <div class="container-fluid px-3 px-md-5" style="max-width: 1280px; margin: 0 auto;">
      <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-white-10">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-shield-lock-fill text-info fs-5"></i>
          <span class="fw-bold fs-6">{{ __('messages.anonymity_helpline_promise') ?? 'خصوصية ومجهولية تامة — لن يسألك أحد عن هويتك' }}</span>
        </div>
        <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1" onclick="toggleEmergencyDrawer()" aria-label="{{ __('messages.close') ?? 'إغلاق' }}">
          <i class="bi bi-x-lg me-1"></i> {{ __('messages.close') ?? 'إغلاق' }}
        </button>
      </div>

      <div class="na-helpline-grid-expanded">
        <!-- Line 1: Regional Egypt 1 -->
        <div class="na-helpline-card-expanded">
          <div>
            <div class="text-white-50 small mb-1">{{ __('messages.helpline_regional_1') ?? 'الخط الساخن الإقليمي (مصر ١)' }}</div>
            <div class="fw-bold font-monospace fs-5 text-white" dir="ltr">+20 100 697 9198</div>
          </div>
          <div class="d-flex gap-2 flex-shrink-0">
            <a href="tel:+201006979198" class="btn btn-sm na-btn-primary rounded-pill px-3 py-2 text-white" title="{{ __('messages.call_now') ?? 'اتصال' }}" aria-label="{{ __('messages.call_now') ?? 'اتصال' }} +20 100 697 9198">
              <i class="bi bi-telephone-fill"></i>
            </a>
            <a href="https://wa.me/201060933888" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success rounded-pill px-3 py-2 text-white" title="{{ __('messages.whatsapp') ?? 'WhatsApp' }}" aria-label="{{ __('messages.whatsapp') ?? 'WhatsApp' }}">
              <i class="bi bi-whatsapp"></i>
            </a>
          </div>
        </div>

        <!-- Line 2: Regional Egypt 2 & WhatsApp -->
        <div class="na-helpline-card-expanded">
          <div>
            <div class="text-white-50 small mb-1">{{ __('messages.helpline_regional_2') ?? 'الخط الساخن الإقليمي (مصر ٢ / واتساب)' }}</div>
            <div class="fw-bold font-monospace fs-5 text-white" dir="ltr">+20 106 093 3888</div>
          </div>
          <div class="d-flex gap-2 flex-shrink-0">
            <a href="tel:+201060933888" class="btn btn-sm na-btn-primary rounded-pill px-3 py-2 text-white" title="{{ __('messages.call_now') ?? 'اتصال' }}" aria-label="{{ __('messages.call_now') ?? 'اتصال' }} +20 106 093 3888">
              <i class="bi bi-telephone-fill"></i>
            </a>
            <a href="https://wa.me/201060933888" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success rounded-pill px-3 py-2 text-white" title="{{ __('messages.whatsapp') ?? 'WhatsApp' }}" aria-label="{{ __('messages.whatsapp') ?? 'WhatsApp' }}">
              <i class="bi bi-whatsapp"></i>
            </a>
          </div>
        </div>

        <!-- Line 3: Alexandria & Coast -->
        <div class="na-helpline-card-expanded">
          <div>
            <div class="text-white-50 small mb-1">{{ __('messages.helpline_alexandria') ?? 'خط الإسكندرية والساحل الشمالي' }}</div>
            <div class="fw-bold font-monospace fs-5 text-white" dir="ltr">+20 150 388 4411</div>
          </div>
          <div class="d-flex gap-2 flex-shrink-0">
            <a href="tel:+201503884411" class="btn btn-sm na-btn-primary rounded-pill px-3 py-2 text-white" title="{{ __('messages.call_now') ?? 'اتصال' }}" aria-label="{{ __('messages.call_now') ?? 'اتصال' }} +20 150 388 4411">
              <i class="bi bi-telephone-fill"></i>
            </a>
            <a href="https://wa.me/201503884411" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success rounded-pill px-3 py-2 text-white" title="{{ __('messages.whatsapp') ?? 'WhatsApp' }}" aria-label="{{ __('messages.whatsapp') ?? 'WhatsApp' }}">
              <i class="bi bi-whatsapp"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- Schedule & Guidance -->
      <div class="mt-3 pt-2 d-flex flex-wrap align-items-center justify-content-between gap-2 text-white-50 small">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-clock-history text-warning"></i>
          <span>{{ __('messages.helpline_operating_hours') ?? 'ساعات عمل خط المساعدة الهاتفي يومياً: من 10:00 صباحاً حتى 10:00 مساءً بتوقيت مصر' }}</span>
        </div>
        <div>
          <a href="{{ route('frontend.meetings', ['type' => 'online']) }}" class="text-info text-decoration-underline">
            {{ __('messages.attend_online_meetings_notice') ?? 'خارج ساعات العمل؟ احضر اجتماع أونلاين الآن عبر زووم (متاح 24 ساعة)' }} &larr;
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Persistent Main Bar (Compact Floating Bar) -->
  <div class="container-fluid px-2 px-md-4 px-lg-5" style="max-width: 1280px; margin: 0 auto;">
    
    <!-- DESKTOP BAR (md and up: clean spaced layout, no overlap) -->
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

      <!-- Quick Dial Direct Phone Chips -->
      <div class="na-emergency-dial-chips">
        <a href="tel:+201006979198" class="na-dial-chip" title="{{ __('messages.helpline_regional_1') ?? 'مصر ١' }}">
          <i class="bi bi-telephone-fill text-warning"></i>
          <span class="d-none d-lg-inline">{{ __('messages.cairo_region') ?? 'القاهرة ومصر' }}:</span>
          <span class="d-inline d-lg-none">{{ __('messages.helpline_regional_1') ?? 'مصر ١' }}:</span>
          <span dir="ltr">0100 697 9198</span>
        </a>

        <a href="tel:+201060933888" class="na-dial-chip" title="{{ __('messages.helpline_regional_2') ?? 'مصر ٢' }}">
          <i class="bi bi-telephone-fill text-warning"></i>
          <span class="d-none d-lg-inline">{{ __('messages.helpline_short_2') ?? 'خط 2' }}:</span>
          <span dir="ltr">0106 093 3888</span>
        </a>

        <a href="tel:+201503884411" class="na-dial-chip d-none d-xl-inline-flex" title="{{ __('messages.helpline_alexandria') ?? 'الإسكندرية' }}">
          <i class="bi bi-telephone-fill text-warning"></i>
          <span>{{ __('messages.alexandria_short') ?? 'إسكندرية' }}:</span>
          <span dir="ltr">0150 388 4411</span>
        </a>

        <a href="https://wa.me/201060933888" target="_blank" rel="noopener noreferrer" class="na-dial-chip na-dial-chip-wa" title="WhatsApp">
          <i class="bi bi-whatsapp text-success"></i>
          <span>{{ __('messages.whatsapp') ?? 'واتساب' }}</span>
        </a>
      </div>

      <!-- Anonymity Seal & Drawer Expand Trigger -->
      <div class="d-flex align-items-center gap-2 flex-shrink-0">
        <div class="na-anonymity-seal-micro d-none d-xxl-flex">
          <i class="bi bi-shield-check text-success fs-6"></i>
          <span>{{ __('messages.anonymity_assurance_micro') ?? 'مكالمتك سرية ومجهولة بالكامل' }}</span>
        </div>

        <button type="button" class="na-emergency-drawer-toggle" onclick="toggleEmergencyDrawer()" aria-expanded="false" aria-controls="naEmergencyDrawer" title="{{ __('messages.view_all_lines') ?? 'عرض كافة الخطوط ومواعيد العمل' }}">
          <i class="bi bi-chevron-up transition-transform na-drawer-chevron"></i>
          <span>{{ __('messages.all_lines_info') ?? 'جميع الخطوط' }}</span>
        </button>
      </div>
    </div>

    <!-- MOBILE BAR (under md: streamlined 3-action buttons + compact status, zero overlap) -->
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

      <!-- Streamlined Quick Actions (Call, WhatsApp, All Lines) -->
      <div class="na-mobile-actions-deck">
        <a href="tel:+201006979198" class="na-mobile-btn na-mobile-btn-call" aria-label="{{ __('messages.call_now') ?? 'اتصال' }}">
          <i class="bi bi-telephone-fill"></i>
          <span>{{ __('messages.call_now') ?? 'اتصال' }}</span>
        </a>

        <a href="https://wa.me/201060933888" target="_blank" rel="noopener noreferrer" class="na-mobile-btn na-mobile-btn-wa" aria-label="WhatsApp">
          <i class="bi bi-whatsapp"></i>
          <span>{{ __('messages.whatsapp') ?? 'واتساب' }}</span>
        </a>

        <button type="button" class="na-mobile-btn na-mobile-btn-lines na-emergency-drawer-toggle" onclick="toggleEmergencyDrawer()" aria-expanded="false" aria-controls="naEmergencyDrawer" aria-label="{{ __('messages.all_lines_info') ?? 'جميع الخطوط' }}">
          <i class="bi bi-chevron-up transition-transform na-drawer-chevron"></i>
          <span>{{ __('messages.all_lines_info_short') ?? 'الخطوط' }}</span>
        </button>
      </div>
    </div>

  </div>
</div>

<script>
  function toggleEmergencyDrawer() {
    const drawer = document.getElementById('naEmergencyDrawer');
    const toggles = document.querySelectorAll('.na-emergency-drawer-toggle');
    const chevrons = document.querySelectorAll('.na-drawer-chevron');
    if (!drawer) return;

    const isOpen = drawer.classList.contains('open');
    if (isOpen) {
      drawer.classList.remove('open');
      drawer.setAttribute('aria-hidden', 'true');
      toggles.forEach(btn => btn.setAttribute('aria-expanded', 'false'));
      chevrons.forEach(ch => ch.style.transform = 'rotate(0deg)');
    } else {
      drawer.classList.add('open');
      drawer.setAttribute('aria-hidden', 'false');
      toggles.forEach(btn => btn.setAttribute('aria-expanded', 'true'));
      chevrons.forEach(ch => ch.style.transform = 'rotate(180deg)');
    }
  }

  // Client-Side Egypt Time Verifier to handle CDN/Edge Cache Time Drift
  document.addEventListener('DOMContentLoaded', function() {
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
