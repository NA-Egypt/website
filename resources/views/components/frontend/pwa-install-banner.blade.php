<!-- Progressive Web App (PWA) In-App Install Banner & iOS Guidance Modal -->
<div id="naPwaInstallBanner" class="na-pwa-banner d-none shadow-lg border" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
  <div class="d-flex align-items-center justify-content-between gap-3 p-3">
    <!-- App Emblem & Info -->
    <div class="d-flex align-items-center gap-3">
      <div class="na-pwa-emblem flex-shrink-0">
        <img src="{{ asset('assets/images/icons/icon-192.png') }}" alt="NA Egypt" width="46" height="46" class="rounded-3 shadow-xs">
      </div>
      <div>
        <div class="d-flex align-items-center gap-1.5 flex-wrap">
          <span class="fw-bold text-white small">{{ __('messages.pwa_install_title') }}</span>
          <span class="badge bg-white-20 text-white rounded-pill x-small" style="background: rgba(255,255,255,0.2);">
            {{ __('messages.pwa_install_feature_tag') }}
          </span>
        </div>
        <div class="text-white-50 x-small mt-0.5 lh-sm na-pwa-desc">
          "{{ __('messages.pwa_install_desc') }}"
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="d-flex align-items-center gap-2 flex-shrink-0">
      <!-- Install / Add Button -->
      <button type="button" id="btnPwaAction" class="btn btn-warning btn-sm rounded-pill px-3 py-1.5 fw-black text-dark shadow-xs" onclick="onPwaInstallClick()">
        <i class="bi bi-cloud-arrow-down-fill me-1"></i>
        <span>{{ __('messages.pwa_btn_install') }}</span>
      </button>

      <!-- Dismiss Button -->
      <button type="button" class="btn btn-link btn-sm text-white-50 p-1 text-decoration-none" onclick="dismissPwaBanner()" aria-label="{{ __('messages.Close') ?? 'Close' }}">
        <i class="bi bi-x-lg fs-6"></i>
      </button>
    </div>
  </div>
</div>

<!-- iOS Safari Add to Home Screen Instructions Modal -->
<div class="modal fade" id="naIosPwaModal" tabindex="-1" aria-labelledby="naIosPwaModalLabel" aria-hidden="true" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
      <div class="modal-header border-0 bg-dark text-white px-4 py-3 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-apple fs-4 text-warning"></i>
          <h5 class="modal-title fw-bold m-0" id="naIosPwaModalLabel">{{ __('messages.pwa_ios_title') }}</h5>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('messages.Close') ?? 'Close' }}"></button>
      </div>

      <div class="modal-body p-4 text-start">
        <div class="alert alert-info border-0 rounded-3 py-2 px-3 small text-dark d-flex align-items-center gap-2 mb-3" style="background: #f0f9ff; color: #0369a1;">
          <i class="bi bi-info-circle-fill fs-5 text-primary flex-shrink-0"></i>
          <span class="x-small">{{ __('messages.pwa_install_desc') }}</span>
        </div>

        <ol class="list-unstyled p-0 m-0 d-flex flex-column gap-3">
          <!-- Step 1 -->
          <li class="p-3 rounded-3 bg-light border d-flex align-items-start gap-3">
            <div class="badge bg-primary text-white rounded-circle p-2 fs-6 flex-shrink-0">1</div>
            <div>
              <div class="fw-bold text-dark small mb-0.5">{{ __('messages.pwa_ios_step1') }}</div>
              <div class="text-muted x-small">
                {{ __('messages.pwa_ios_step1_sub') }}
                <span class="badge bg-white text-primary border px-2 py-0.5 ms-1"><i class="bi bi-box-arrow-up"></i></span>
              </div>
            </div>
          </li>

          <!-- Step 2 -->
          <li class="p-3 rounded-3 bg-light border d-flex align-items-start gap-3">
            <div class="badge bg-primary text-white rounded-circle p-2 fs-6 flex-shrink-0">2</div>
            <div>
              <div class="fw-bold text-dark small mb-0.5">{{ __('messages.pwa_ios_step2') }}</div>
              <div class="text-muted x-small">
                {{ __('messages.pwa_ios_step2_sub') }}
                <span class="badge bg-white text-dark border px-2 py-0.5 ms-1"><i class="bi bi-plus-square"></i> Add to Home Screen</span>
              </div>
            </div>
          </li>

          <!-- Step 3 -->
          <li class="p-3 rounded-3 bg-light border d-flex align-items-start gap-3">
            <div class="badge bg-success text-white rounded-circle p-2 fs-6 flex-shrink-0">3</div>
            <div>
              <div class="fw-bold text-dark small mb-0.5">{{ __('messages.pwa_ios_step3') }}</div>
              <div class="text-muted x-small">{{ __('messages.pwa_ios_step3_sub') }}</div>
            </div>
          </li>
        </ol>

        <div class="text-center mt-4">
          <button type="button" class="btn btn-primary px-4 py-2 rounded-pill fw-bold" data-bs-dismiss="modal">
            {{ __('messages.apk_done_btn') ?? 'حسناً، فهمت' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.na-pwa-banner {
  position: fixed;
  bottom: 64px; /* Just above emergency bar */
  left: 50%;
  transform: translateX(-50%);
  width: calc(100% - 24px);
  max-width: 680px;
  background: linear-gradient(135deg, #101b30 0%, #1b2a4a 100%);
  border-radius: 20px;
  border-color: rgba(255, 255, 255, 0.15) !important;
  z-index: 1040;
  animation: naSlideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes naSlideUp {
  from { opacity: 0; transform: translate(-50%, 20px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}

.na-pwa-desc {
  max-width: 420px;
}

.x-small {
  font-size: 0.76rem;
}

@media (max-width: 576px) {
  .na-pwa-banner {
    bottom: 58px;
    border-radius: 16px;
  }
  .na-pwa-desc {
    display: none; /* Keep super clean on narrow mobile */
  }
}
</style>

<script>
(function() {
  let deferredPrompt = null;
  const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
  const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

  // Don't show if already installed
  if (isStandalone) return;

  // Check 7-day dismissal cooldown
  const dismissedUntil = localStorage.getItem('na_pwa_dismissed_until');
  if (dismissedUntil && Date.now() < parseInt(dismissedUntil, 10)) {
    return;
  }

  // Android / Chromium beforeinstallprompt event
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    showPwaBanner();
  });

  // On iOS Safari, show prompt if not standalone after 3s
  document.addEventListener('DOMContentLoaded', function() {
    if (isIOS && !isStandalone) {
      setTimeout(() => {
        showPwaBanner();
      }, 3500);
    }
  });

  function showPwaBanner() {
    const banner = document.getElementById('naPwaInstallBanner');
    if (banner) banner.classList.remove('d-none');
  }

  window.dismissPwaBanner = function() {
    const banner = document.getElementById('naPwaInstallBanner');
    if (banner) banner.classList.add('d-none');
    // Cooldown 7 days
    localStorage.setItem('na_pwa_dismissed_until', String(Date.now() + (7 * 24 * 60 * 60 * 1000)));
  };

  window.onPwaInstallClick = function() {
    if (deferredPrompt) {
      deferredPrompt.prompt();
      deferredPrompt.userChoice.then((choiceResult) => {
        if (choiceResult.outcome === 'accepted') {
          dismissPwaBanner();
        }
        deferredPrompt = null;
      });
    } else if (isIOS) {
      // Show iOS modal guide
      const modal = new bootstrap.Modal(document.getElementById('naIosPwaModal'));
      modal.show();
    } else {
      // Fallback
      alert("{{ app()->getLocale() === 'ar' ? 'لتثبيت التطبيق، اضغط على قائمة المتصفح (⋮) ثم اختر «إضافة إلى الشاشة الرئيسية».' : 'To install, open your browser menu (⋮) and select \"Add to Home Screen\".' }}");
    }
  };

  window.triggerPWAInstall = function() {
    window.onPwaInstallClick();
  };
})();
</script>
