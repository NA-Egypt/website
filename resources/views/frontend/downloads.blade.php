<x-frontend.layout 
    :title="__('messages.apk_hub_title')" 
    :description="__('messages.apk_hub_subtitle')">

  @php
    $isArabic = app()->getLocale() === 'ar';
    $pageDir = $isArabic ? 'rtl' : 'ltr';
    $pageAlign = $isArabic ? 'right' : 'left';
  @endphp

  <div class="app-hub-page pb-5" dir="{{ $pageDir }}" style="text-align: {{ $pageAlign }};">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb py-2 px-3 rounded-pill bg-light border small d-inline-flex">
        <li class="breadcrumb-item">
          <a href="{{ route('frontend.home') }}" class="text-decoration-none text-muted">
            <i class="bi bi-house-door-fill me-1"></i> {{ __('messages.Home') }}
          </a>
        </li>
        <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">
          {{ __('messages.apk_hub_title') }}
        </li>
      </ol>
    </nav>

    <!-- Header Section -->
    <section class="app-hero mb-4">
      <div class="hero-shell p-4 p-md-5 rounded-4 shadow-sm position-relative overflow-hidden">
        <div class="row align-items-center g-4 position-relative" style="z-index: 1;">
          <div class="col-12 col-lg-8">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3" style="background: rgba(50, 85, 127, 0.08); color: #32557f; font-weight: 700; font-size: 0.88rem;">
              <i class="bi bi-android2 fs-5"></i>
              <span>v1.2.0-Hope • {{ app()->getLocale() === 'ar' ? 'تطبيق الزمالة الرسمي للأجهزة الذكية' : 'Official Fellowship Mobile App' }}</span>
            </div>
            <h1 class="display-6 fw-black text-dark mb-3 lh-sm">
              {{ __('messages.apk_hub_title') }}
            </h1>
            <p class="lead text-secondary mb-3 lh-base fs-6">
              {{ __('messages.apk_hub_subtitle') }}
            </p>
            <div class="d-flex align-items-center gap-3 flex-wrap text-muted small">
              <span class="d-inline-flex align-items-center gap-1">
                <i class="bi bi-shield-check text-success fs-5"></i>
                <span>{{ $isArabic ? 'تنزيل مباشر دون إدخال بريد أو بيانات' : 'Direct download without email or data' }}</span>
              </span>
              <span class="d-inline-flex align-items-center gap-1">
                <i class="bi bi-wifi-off text-primary fs-5"></i>
                <span>{{ $isArabic ? 'يعمل بدون إنترنت (دليل الاجتماعات والتأمل)' : 'Works offline (Meetings & JFT)' }}</span>
              </span>
              <span class="d-inline-flex align-items-center gap-1">
                <i class="bi bi-apple text-dark fs-5"></i>
                <span>{{ $isArabic ? 'دعم الآيفون عبر تطبيق الويب PWA' : 'iOS supported via Web App PWA' }}</span>
              </span>
            </div>
          </div>

          <div class="col-12 col-lg-4 text-center">
            <div class="p-3 rounded-4 bg-white shadow-sm border d-inline-block">
              <img src="{{ asset('assets/images/icons/icon-192.png') }}" alt="NA App Icon" width="100" height="100" class="img-fluid rounded-4 mb-2 shadow-xs">
              <div class="fw-bold small text-dark">{{ __('messages.na_egypt_full_name') ?? 'زمالة المدمنين المجهولين - مصر' }}</div>
              <div class="x-small text-muted font-monospace mt-1">Version 1.2.0 (Build 2026)</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Direct Android APK Hub Component -->
    <div class="bg-white p-4 p-md-5 rounded-4 border shadow-sm mb-4">
      <x-frontend.apk-download-hub :isModal="false" />
    </div>

    <!-- PWA Section (For iPhone & Non-Store Devices) -->
    <section class="p-4 p-md-5 rounded-4 border shadow-sm" style="background: linear-gradient(135deg, #101b30 0%, #1b2a4a 100%); color: #ffffff;">
      <div class="row align-items-center g-4">
        <div class="col-12 col-md-8 text-start">
          <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="background: rgba(255,255,255,0.12); font-size: 0.82rem;">
            <i class="bi bi-apple text-warning"></i>
            <span>{{ $isArabic ? 'لمستخدمي الآيفون وكافة المتصفحات (PWA)' : 'For iPhone & All Modern Browsers (PWA)' }}</span>
          </div>
          <h3 class="h4 fw-bold text-white mb-2">
            {{ $isArabic ? 'لا ترغب في تثبيت APK أو تستخدم iPhone؟' : 'Prefer not to install APK or using iPhone?' }}
          </h3>
          <p class="text-white-50 small mb-0 lh-base">
            {{ __('messages.pwa_install_desc') }}
          </p>
        </div>

        <div class="col-12 col-md-4 text-center text-md-end">
          <button type="button" class="btn btn-warning px-4 py-2.5 rounded-pill fw-black text-dark shadow-sm d-inline-flex align-items-center gap-2" onclick="triggerPWAInstall()">
            <i class="bi bi-cloud-arrow-down-fill fs-5"></i>
            <span>{{ __('messages.pwa_btn_install') }}</span>
          </button>
        </div>
      </div>
    </section>
  </div>

  <style>
    .hero-shell {
      background:
        radial-gradient(circle at top left, rgba(50, 85, 127, 0.08), transparent 28%),
        linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
      border: 1px solid rgba(50, 85, 127, 0.15);
    }
  </style>
</x-frontend.layout>
