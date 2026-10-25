<!-- Progressive Web App (PWA) In-App Install Banner & iOS Guidance Modal -->
<div id="naPwaInstallBanner" class="na-pwa-banner d-none shadow-lg border" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
  <div class="na-pwa-banner-content d-flex align-items-center justify-content-between gap-2 gap-sm-3">
    <!-- App Emblem & Info -->
    <div class="d-flex align-items-center gap-2 gap-sm-3 min-w-0 flex-grow-1">
      <div class="na-pwa-emblem flex-shrink-0">
        <img src="{{ asset('assets/images/icons/icon-192.png') }}" alt="NA Egypt" width="44" height="44" class="rounded-3 shadow-xs na-pwa-icon">
      </div>
      <div class="min-w-0 flex-grow-1">
        <div class="d-flex align-items-center gap-1.5 flex-wrap">
          <span class="fw-bold text-white small text-truncate d-block">{{ __('messages.pwa_install_title') }}</span>
          <span class="badge bg-white-20 text-white rounded-pill x-small d-none d-md-inline-block" style="background: rgba(255,255,255,0.2);">
            {{ __('messages.pwa_install_feature_tag') }}
          </span>
        </div>
        <div class="text-white-50 x-small mt-0.5 lh-sm na-pwa-desc">
          "{{ __('messages.pwa_install_desc') }}"
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="d-flex align-items-center gap-1.5 gap-sm-2 flex-shrink-0">
      <!-- Install / Add Button -->
      <button type="button" id="btnPwaAction" class="btn btn-warning btn-sm rounded-pill fw-bold text-dark shadow-xs" onclick="onPwaInstallClick()">
        <i class="bi bi-cloud-arrow-down-fill me-1"></i>
        <span class="d-none d-sm-inline">{{ __('messages.pwa_btn_install') }}</span>
        <span class="d-inline d-sm-none">{{ __('messages.pwa_btn_install_short') }}</span>
      </button>

      <!-- Dismiss Button -->
      <button type="button" class="btn btn-link btn-sm text-white-50 p-0 text-decoration-none na-pwa-dismiss-btn" onclick="dismissPwaBanner()" aria-label="{{ __('messages.Close') ?? 'Close' }}">
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
  bottom: calc(var(--na-emergency-bar-height, 60px) + 12px);
  left: 50%;
  transform: translateX(-50%);
  width: calc(100% - 24px);
  max-width: 680px;
  background: linear-gradient(135deg, #101b30 0%, #1b2a4a 100%);
  border-radius: 20px;
  border-color: rgba(255, 255, 255, 0.15) !important;
  z-index: 1040;
  animation: naSlideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  transition: bottom 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  overflow: hidden;
  box-sizing: border-box;
}

@keyframes naSlideUp {
  from { opacity: 0; transform: translate(-50%, 20px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}

.na-pwa-banner-content {
  padding: 1rem;
}

.na-pwa-desc {
  max-width: 420px;
}

.x-small {
  font-size: 0.76rem;
}

#btnPwaAction {
  padding: 0.4rem 1rem;
  white-space: nowrap;
}

.na-pwa-dismiss-btn {
  width: 32px;
  height: 32px;
  min-width: 32px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background-color 0.15s ease, color 0.15s ease;
}

.na-pwa-dismiss-btn:hover {
  background-color: rgba(255, 255, 255, 0.1);
  color: #fff !important;
}

@media (max-width: 576px) {
  .na-pwa-banner {
    width: calc(100% - 16px);
    bottom: calc(var(--na-emergency-bar-height, 56px) + 8px);
    border-radius: 16px;
  }
  .na-pwa-banner-content {
    padding: 0.65rem 0.75rem;
  }
  .na-pwa-desc {
    display: none; /* Keep super clean on narrow mobile */
  }
  .na-pwa-icon {
    width: 38px;
    height: 38px;
  }
  #btnPwaAction {
    padding: 0.35rem 0.65rem;
    font-size: 0.8rem;
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
