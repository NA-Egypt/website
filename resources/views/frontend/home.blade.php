<x-frontend.layout :title="__('messages.NA')" description="الموقع الرسمي لزمالة المدمنين المجهولين في مصر - ابحث عن التعافي والاجتماعات وأرقام المساعدة في كافة المحافظات.">
  <!-- Pre-release Mobile APK Notification Card -->
  <div class="row justify-content-center mb-3">
    <div class="col-12 d-flex justify-content-center">
      <button type="button" class="btn btn-hero-apk-card btn-apk-hero shadow-sm border-0 d-inline-flex align-items-center touch-target position-relative overflow-hidden" data-bs-toggle="modal" data-bs-target="#apkDownloadModal" aria-label="{{ __('messages.apk_modal_title') }}">
        <span class="apk-card-shimmer" aria-hidden="true"></span>
        <div class="apk-card-emblem flex-shrink-0 position-relative">
          <span class="apk-emblem-pulse" aria-hidden="true"></span>
          <i class="bi bi-android2 apk-card-icon" aria-hidden="true"></i>
        </div>
        <div class="apk-card-info text-start d-flex flex-column justify-content-center">
          <div class="d-flex align-items-center flex-wrap apk-card-eyebrow">
            <span class="apk-live-dot" aria-hidden="true"></span>
            <span class="apk-card-caption fw-bold">{{ __('messages.apk_badge_caption') }}</span>
            <span class="badge apk-card-version-badge rounded-pill" dir="ltr">{{ __('messages.apk_beta_badge') }}</span>
            <span class="badge apk-card-format-badge rounded-pill">APK</span>
          </div>
          <div class="apk-card-title fw-bold">{{ __('messages.apk_badge_title') }}</div>
        </div>
        <div class="apk-card-action flex-shrink-0">
          <span class="apk-card-cta-btn">
            <span>{{ __('messages.apk_cta_download') }}</span>
            <i class="bi bi-arrow-down-circle-fill apk-card-cta-icon" aria-hidden="true"></i>
          </span>
        </div>
      </button>
    </div>
  </div>

  <style>
    .btn-hero-apk-card {
      position: relative;
      background: linear-gradient(135deg, #ffffff 0%, #f4f8fd 60%, #eef5fc 100%);
      border: 1.5px solid rgba(14, 131, 136, 0.22) !important;
      border-radius: 18px;
      padding: 10px 16px;
      width: 100%;
      max-width: 440px;
      min-height: 64px;
      box-shadow: 0 4px 16px rgba(14, 131, 136, 0.08);
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      cursor: pointer;
      text-decoration: none;
      color: inherit;
    }
    .btn-hero-apk-card:hover {
      background: linear-gradient(135deg, #ffffff 0%, #edf8f8 60%, #e5f6f6 100%);
      border-color: var(--na-teal-600) !important;
      transform: translateY(-2px);
      box-shadow: 0 12px 28px -6px rgba(14, 131, 136, 0.2);
    }
    .apk-card-shimmer {
      position: absolute;
      top: 0;
      left: -120%;
      width: 60%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.7), transparent);
      transform: skewX(-20deg);
      pointer-events: none;
    }
    .btn-hero-apk-card:hover .apk-card-shimmer {
      left: 180%;
      transition: left 0.85s cubic-bezier(0.2, 0.8, 0.2, 1);
    }
    .apk-card-emblem {
      width: 44px;
      height: 44px;
      min-width: 44px;
      border-radius: 13px;
      background: linear-gradient(135deg, #0e8388 0%, #0a6b70 100%);
      display: grid;
      place-items: center;
      box-shadow: 0 4px 14px rgba(14, 131, 136, 0.35);
      margin-inline-end: 12px;
      z-index: 1;
    }
    .apk-emblem-pulse {
      position: absolute;
      inset: -3px;
      border-radius: 16px;
      border: 2px solid rgba(14, 131, 136, 0.35);
      animation: apkAuraPulse 2.8s cubic-bezier(0.4, 0, 0.6, 1) infinite;
      pointer-events: none;
    }
    @keyframes apkAuraPulse {
      0%, 100% { transform: scale(1); opacity: 0.7; }
      50% { transform: scale(1.1); opacity: 0.15; }
    }
    .apk-card-icon {
      font-size: 1.65rem;
      line-height: 1 !important;
      color: #ffffff;
    }
    .apk-card-info {
      flex: 1 1 auto;
      min-width: 0;
      z-index: 1;
    }
    .apk-card-eyebrow {
      line-height: 1.2;
      margin-bottom: 3px;
      gap: 5px;
    }
    .apk-live-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background-color: #10b981;
      display: inline-block;
      box-shadow: 0 0 6px #10b981;
    }
    .apk-card-caption {
      font-size: 0.68rem;
      color: #64748b;
      white-space: nowrap;
    }
    .apk-card-version-badge {
      background: rgba(14, 131, 136, 0.12);
      color: #0e8388;
      border: 1px solid rgba(14, 131, 136, 0.25);
      font-size: 0.64rem;
      font-weight: 700;
      padding: 1.5px 7px;
    }
    .apk-card-format-badge {
      background: #f1f5f9;
      color: #475569;
      font-size: 0.64rem;
      font-weight: 700;
      padding: 1.5px 6px;
    }
    .apk-card-title {
      font-size: 0.95rem;
      color: #1e293b;
      line-height: 1.25;
    }
    .apk-card-cta-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #0e8388;
      color: #ffffff;
      padding: 6px 14px;
      border-radius: 9999px;
      font-size: 0.82rem;
      font-weight: 700;
    }
  </style>

  <!-- ======================================================================
       SECTION 1: Hero with High-Contrast Headline + Tri-Path Segmenter Cards
       ====================================================================== -->
  <x-frontend.hero-tripath />

  <!-- ======================================================================
       SECTION 2: Meeting Finder (Fast Lookup Widget)
       ====================================================================== -->
  <x-frontend.quick-meeting-finder 
      :cities="$cities" 
      :days="$days" 
      :previewMeetings="$previewMeetings"
      :locations="$locations ?? []"
      :allMeetingsJson="$allMeetingsJson ?? []" />

  <!-- ======================================================================
       SECTION 3: "Just For Today" (لليوم فقط) Daily Reflection Card
       ====================================================================== -->
  <x-frontend.jft-card :jftContent="$jftContent" />

  <!-- ======================================================================
       SECTION 4: Clean-Time Calculator & Keytag Milestones Journey
       ====================================================================== -->
  <x-frontend.cleantime-calculator />

  <!-- ======================================================================
       SECTION 5: Fellowship News, Conventions & Announcements
       ====================================================================== -->
  <x-frontend.news-announcements :announcements="$announcements" />

  {{-- ======================================================================
       SECTION 6: Sanctuary Footer is rendered automatically via layout footer
       with full literature, committees, and anonymity info
       ====================================================================== --}}

  <!-- Pre-release APK Request Modal -->
  <div class="modal fade" id="apkDownloadModal" tabindex="-1" aria-labelledby="apkDownloadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content apk-modal-content">
        <div class="modal-header border-0 text-white apk-modal-header d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="apk-app-badge">
              <i class="bi bi-android2 text-white fs-2"></i>
            </div>
            <div class="text-start">
              <h5 class="modal-title fw-bold m-0 text-white lh-sm" id="apkDownloadModalLabel">{{ __('messages.apk_modal_title') }}</h5>
              <span class="badge bg-white-20 text-white rounded-pill mt-1" style="background: rgba(255,255,255,0.22); font-size: 0.72rem;">{{ __('messages.apk_beta_badge') }}</span>
            </div>
          </div>
          <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="{{ __('messages.Close') ?? 'Close' }}"></button>
        </div>

        <div class="modal-body p-4">
          <div id="apkFormView">
            <p class="text-muted small mb-3 text-start lh-base">
              {{ __('messages.apk_modal_subtitle') }}
            </p>

            <div id="apkAlertError" class="alert alert-danger d-none rounded-3 py-2 px-3 border-0 shadow-sm mb-3" role="alert" style="background-color: #fef2f2; color: #991b1b;">
              <div class="d-flex align-items-center gap-2 text-start">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger flex-shrink-0"></i>
                <span id="apkErrorMessage" class="small fw-semibold"></span>
              </div>
            </div>

            <form id="apkRequestForm" method="POST" action="{{ route('apk.request_link') }}">
              @csrf
              <div class="mb-3 text-start">
                <label for="apkEmailInput" class="form-label fw-semibold small text-dark d-block">{{ __('messages.apk_email_label') }}</label>
                <div class="input-group apk-input-group">
                  <span class="input-group-text"><i class="bi bi-envelope fs-5"></i></span>
                  <input type="email" class="form-control" id="apkEmailInput" name="email" required placeholder="{{ __('messages.apk_email_placeholder') }}" dir="ltr" style="text-align: left;">
                </div>
                <div class="form-text text-muted small mt-2 d-flex align-items-center gap-1">
                  <i class="bi bi-info-circle text-primary"></i>
                  <span>{{ __('messages.apk_email_domain_restricted') }}</span>
                </div>
              </div>

              @if(!empty(config('services.turnstile.site_key')))
                <div class="mb-3 d-flex justify-content-center">
                  <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="light"></div>
                </div>
              @endif

              <div class="alert alert-info py-2 px-3 small border-0 rounded-3 mb-3 d-flex align-items-center gap-2 text-start" style="background-color: #f0f9ff; color: #0369a1;">
                <i class="bi bi-phone flex-shrink-0 fs-5"></i>
                <span>{{ __('messages.apk_install_note') }}</span>
              </div>

              <div class="d-grid mt-4">
                <button type="submit" id="apkSubmitBtn" class="btn btn-primary py-2 px-4 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm">
                  <span id="apkSubmitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                  <span id="apkSubmitText">{{ __('messages.apk_submit_btn') }}</span>
                </button>
              </div>
            </form>
          </div>

          <div id="apkSuccessView" class="d-none text-center py-4 px-2">
            <div class="apk-success-icon-wrap mb-3 mx-auto">
              <i class="bi bi-check-lg fs-1 text-success"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">{{ __('messages.apk_check_inbox_title') }}</h4>
            <p class="text-muted small mb-4 px-2 lh-base" id="apkSuccessMessage">
              {{ __('messages.apk_check_inbox_desc') }}
            </p>
            <div class="p-3 rounded-3 mb-4 text-start small border" style="background: #f8fafc; border-color: #e2e8f0 !important;">
              <div class="d-flex align-items-center gap-2 text-dark fw-semibold mb-1">
                <i class="bi bi-envelope-check text-primary fs-5"></i>
                <span id="apkSentEmailTarget" class="font-monospace text-primary"></span>
              </div>
              <div class="text-muted" style="font-size: 0.8rem;">
                {{ __('messages.apk_install_note') }}
              </div>
            </div>
            <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-semibold" data-bs-dismiss="modal">
              {{ __('messages.apk_done_btn') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .apk-modal-content {
      border: 1px solid rgba(255, 255, 255, 0.25);
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border-radius: 24px;
      box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
      overflow: hidden;
    }
    .apk-modal-header {
      background: linear-gradient(135deg, #101b30 0%, #1b2a4a 100%);
      padding: 22px 24px;
    }
    .apk-app-badge {
      width: 50px;
      height: 50px;
      border-radius: 14px;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.28);
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
      flex-shrink: 0;
    }
    .apk-input-group {
      border-radius: 12px;
      transition: all 0.25s ease;
      border: 1.5px solid #cbd5e1;
      background: #f8fafc;
      overflow: hidden;
    }
    .apk-input-group:focus-within {
      border-color: #0e8388;
      background: #ffffff;
      box-shadow: 0 0 0 4px rgba(14, 131, 136, 0.12);
    }
    .apk-input-group .form-control {
      border: none !important;
      background: transparent !important;
      padding: 12px 14px;
      font-size: 0.95rem;
    }
    .apk-input-group .input-group-text {
      border: none !important;
      background: transparent !important;
      color: #64748b;
      padding-inline: 14px;
    }
    .apk-success-icon-wrap {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: #ecfdf5;
      border: 2px solid #a7f3d0;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.25);
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('apkRequestForm');
      if (!form) return;

      const modalEl = document.getElementById('apkDownloadModal');
      const formView = document.getElementById('apkFormView');
      const successView = document.getElementById('apkSuccessView');
      const emailInput = document.getElementById('apkEmailInput');
      const sentEmailTarget = document.getElementById('apkSentEmailTarget');
      const submitBtn = document.getElementById('apkSubmitBtn');
      const submitSpinner = document.getElementById('apkSubmitSpinner');
      const submitText = document.getElementById('apkSubmitText');
      const alertError = document.getElementById('apkAlertError');
      const errorMsg = document.getElementById('apkErrorMessage');

      if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () {
          form.reset();
          alertError.classList.add('d-none');
          formView.classList.remove('d-none');
          successView.classList.add('d-none');
          submitBtn.disabled = false;
          submitSpinner.classList.add('d-none');
          submitText.textContent = "{{ __('messages.apk_submit_btn') }}";
        });
      }

      form.addEventListener('submit', async function(e) {
        e.preventDefault();
        alertError.classList.add('d-none');

        const email = emailInput.value.trim().toLowerCase();
        if (!email || !email.includes('@')) {
          errorMsg.textContent = "{{ __('messages.apk_email_invalid') }}";
          alertError.classList.remove('d-none');
          return;
        }

        submitBtn.disabled = true;
        submitSpinner.classList.remove('d-none');
        submitText.textContent = "{{ __('messages.apk_sending') }}";

        const formData = new FormData(form);

        try {
          const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            }
          });

          const data = await response.json().catch(() => ({}));

          if (response.ok && data.success) {
            if (sentEmailTarget) {
              sentEmailTarget.textContent = email;
            }
            formView.classList.add('d-none');
            successView.classList.remove('d-none');
            if (window.turnstile) {
              window.turnstile.reset();
            }
          } else {
            errorMsg.textContent = data.message || "{{ __('messages.apk_email_send_failed') }}";
            alertError.classList.remove('d-none');
            if (window.turnstile) {
              window.turnstile.reset();
            }
          }
        } catch (err) {
          errorMsg.textContent = "{{ __('messages.apk_email_send_failed') }}";
          alertError.classList.remove('d-none');
          if (window.turnstile) {
            window.turnstile.reset();
          }
        } finally {
          submitBtn.disabled = false;
          submitSpinner.classList.add('d-none');
          submitText.textContent = "{{ __('messages.apk_submit_btn') }}";
        }
      });
    });
  </script>
</x-frontend.layout>