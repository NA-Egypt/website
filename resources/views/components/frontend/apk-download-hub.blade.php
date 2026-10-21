@props([
    'isModal' => false
])

<div class="na-apk-hub-wrapper text-start" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
  <!-- Security & Privacy Assurance Callout -->
  <div class="p-3 rounded-4 mb-4 d-flex align-items-center gap-3 na-privacy-callout">
    <div class="na-callout-icon-wrap flex-shrink-0">
      <i class="bi bi-shield-check fs-3 text-success"></i>
    </div>
    <div>
      <div class="fw-bold small text-dark mb-0.5">
        {{ app()->getLocale() === 'ar' ? 'تحميل مباشر وخاص 100% دون جمع بيانات' : '100% Direct & Private Download Without Data Collection' }}
      </div>
      <div class="text-secondary x-small lh-base mb-0">
        {{ app()->getLocale() === 'ar' 
          ? 'تم إلغاء شرط إدخال البريد الإلكتروني. يمكنك تنزيل ملف التطبيق مباشرة وبصورة مجهولة تماماً حفاظاً على تقاليد الزمالة.' 
          : 'Email requirement has been completely removed. You can download the APK package directly and anonymously in accordance with NA traditions.' }}
      </div>
    </div>
  </div>

  <!-- Primary Direct Download Hero Card -->
  <div class="p-4 rounded-4 mb-4 border shadow-sm na-apk-hero-panel position-relative overflow-hidden">
    <div class="row align-items-center g-3">
      <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
          <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-monospace px-2.5 py-1">v1.2.0-Hope</span>
          <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1">28.4 MB</span>
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1">
            {{ app()->getLocale() === 'ar' ? 'إصدار رسمي ومستقر' : 'Official Release' }}
          </span>
        </div>
        <h3 class="h4 fw-black text-dark mb-1">
          {{ __('messages.apk_hub_title') }}
        </h3>
        <p class="text-secondary small mb-3">
          {{ __('messages.apk_hub_subtitle') }}
        </p>

        <!-- Primary Direct Download CTA Button -->
        <a href="{{ route('apk.direct_download.file') }}" class="btn btn-primary btn-lg rounded-pill px-4 py-3 fw-black d-inline-flex align-items-center gap-2 shadow-sm na-btn-direct-apk" id="btnDirectApkDownload">
          <i class="bi bi-android2 fs-3 text-white"></i>
          <span>تحميل naegypt-v1.2.0-hope.apk</span>
          <i class="bi bi-arrow-down-circle-fill fs-5 text-white-50 ms-2"></i>
        </a>
      </div>

      <!-- QR Code Section for Direct Mobile Scanning -->
      <div class="col-12 col-md-4 text-center">
        <div class="p-3 rounded-4 bg-white border shadow-xs d-inline-block na-qr-container">
          <img src="{{ asset('assets/images/apk-download-qr.svg') }}" alt="QR Code" width="130" height="130" class="img-fluid rounded-3 mb-1" onerror="this.src='{{ asset('assets/images/apk-download-qr.png') }}'">
          <div class="x-small text-muted fw-bold d-flex align-items-center justify-content-center gap-1 mt-1">
            <i class="bi bi-qr-code-scan text-primary"></i>
            <span>{{ __('messages.apk_scan_qr_desc') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Security & Integrity Card (SHA-256 Checksum) -->
  <div class="p-3.5 rounded-4 mb-4 border bg-white shadow-xs na-integrity-card">
    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
      <div class="fw-bold small text-dark d-flex align-items-center gap-1.5">
        <i class="bi bi-fingerprint text-primary fs-5"></i>
        <span>{{ __('messages.apk_checksum_label') }}</span>
      </div>
      <div class="d-flex align-items-center gap-2">
        <span class="x-small text-muted font-monospace">SHA-256</span>
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-0.5 x-small fw-bold d-inline-flex align-items-center gap-1" onclick="copyApkChecksum()">
          <i class="bi bi-clipboard"></i>
          <span>{{ __('messages.apk_copy_checksum') }}</span>
        </button>
      </div>
    </div>

    <div class="p-2.5 rounded-3 bg-light border font-monospace x-small text-break text-secondary text-center select-all mb-2" id="apkChecksumVal">
      a3f89b6c429d71e289e631df8435d10d65b1c90538743128c11e5f80b2d69941
    </div>

    <!-- Metadata Details Grid -->
    <div class="row g-2 text-muted x-small pt-1 border-top border-light-subtle">
      <div class="col-6 col-sm-3">
        <span class="fw-bold text-dark">{{ __('messages.apk_filename_label') }}</span>
        <div class="font-monospace text-secondary">naegypt-v1.2.0-hope.apk</div>
      </div>
      <div class="col-6 col-sm-3">
        <span class="fw-bold text-dark">{{ __('messages.apk_filesize_label') }}</span>
        <div class="font-monospace text-secondary">28.4 MB</div>
      </div>
      <div class="col-6 col-sm-3">
        <span class="fw-bold text-dark">{{ __('messages.apk_released_label') }}</span>
        <div class="text-secondary">14 أكتوبر 2026</div>
      </div>
      <div class="col-6 col-sm-3">
        <span class="fw-bold text-dark">{{ app()->getLocale() === 'ar' ? 'التوافق:' : 'Compatibility:' }}</span>
        <div class="text-secondary">Android 6.0+</div>
      </div>
    </div>

    <!-- Copy Toast Feedback -->
    <div id="checksumToast" class="badge bg-dark text-white rounded-pill px-3 py-1.5 mt-2 d-none animate__animated animate__fadeIn">
      <i class="bi bi-check-circle-fill text-success me-1"></i> {{ __('messages.apk_checksum_copied') }}
    </div>
  </div>

  <!-- 3-Step Visual Installation Guide -->
  <div class="mb-3">
    <div class="fw-bold small text-dark mb-3 d-flex align-items-center gap-2">
      <i class="bi bi-journal-check text-success fs-5"></i>
      <span>{{ __('messages.apk_guide_title') }}</span>
    </div>

    <div class="row g-3">
      <!-- Step 1 -->
      <div class="col-12 col-md-4">
        <div class="p-3 rounded-4 bg-light border h-100 na-step-card">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-primary text-white rounded-circle p-2 fs-6">1</span>
            <div class="fw-bold small text-dark">{{ __('messages.apk_step1_title') }}</div>
          </div>
          <p class="text-secondary x-small mb-0 lh-base">
            {{ __('messages.apk_step1_desc') }}
          </p>
        </div>
      </div>

      <!-- Step 2 -->
      <div class="col-12 col-md-4">
        <div class="p-3 rounded-4 bg-light border h-100 na-step-card">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-warning text-dark rounded-circle p-2 fs-6">2</span>
            <div class="fw-bold small text-dark">{{ __('messages.apk_step2_title') }}</div>
          </div>
          <p class="text-secondary x-small mb-0 lh-base">
            {{ __('messages.apk_step2_desc') }}
          </p>
        </div>
      </div>

      <!-- Step 3 -->
      <div class="col-12 col-md-4">
        <div class="p-3 rounded-4 bg-light border h-100 na-step-card">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-success text-white rounded-circle p-2 fs-6">3</span>
            <div class="fw-bold small text-dark">{{ __('messages.apk_step3_title') }}</div>
          </div>
          <p class="text-secondary x-small mb-0 lh-base">
            {{ __('messages.apk_step3_desc') }}
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.na-privacy-callout {
  background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
  border: 1px solid #a7f3d0;
}
.na-callout-icon-wrap {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 6px rgba(16, 185, 129, 0.15);
}
.na-apk-hero-panel {
  background: linear-gradient(135deg, #f8fafc 0%, #edf4fe 100%);
  border-color: #cbd5e1 !important;
}
.na-btn-direct-apk {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  border: none;
  font-size: 1rem;
  transition: all 0.25s ease;
}
.na-btn-direct-apk:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);
  background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
}
.na-qr-container {
  max-width: 170px;
}
.na-integrity-card {
  border-color: #e2e8f0 !important;
}
.na-step-card {
  transition: transform 0.2s ease, border-color 0.2s ease;
}
.na-step-card:hover {
  transform: translateY(-2px);
  border-color: #32557f !important;
}
.select-all {
  user-select: all;
  -webkit-user-select: all;
}
.x-small {
  font-size: 0.78rem;
}
</style>

<script>
function copyApkChecksum() {
  const checksum = 'a3f89b6c429d71e289e631df8435d10d65b1c90538743128c11e5f80b2d69941';
  navigator.clipboard.writeText(checksum).then(() => {
    const toast = document.getElementById('checksumToast');
    if (toast) {
      toast.classList.remove('d-none');
      setTimeout(() => toast.classList.add('d-none'), 3000);
    }
  });
}
</script>
