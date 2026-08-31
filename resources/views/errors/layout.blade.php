@php
    $locale = app()->getLocale() ?? 'ar';
    $isArabic = $locale === 'ar';
    $direction = $isArabic ? 'rtl' : 'ltr';
    $code = $__env->yieldContent('code', '404');
    $title = $__env->yieldContent('title', __('errors.' . $code . '_title', [], $locale));
    if ($title === 'errors.' . $code . '_title') {
        $title = __('errors.generic_title', [], $locale);
    }
    $pageTitle = $code . ' - ' . $title;
    $subtitle = $__env->yieldContent('subtitle', __('errors.' . $code . '_subtitle', [], $locale));
    if ($subtitle === 'errors.' . $code . '_subtitle') {
        $subtitle = __('errors.generic_subtitle', [], $locale);
    }
    $message = $__env->yieldContent('message', __('errors.' . $code . '_message', [], $locale));
    if ($message === 'errors.' . $code . '_message') {
        $message = __('errors.generic_message', [], $locale);
    }
    $icon = $__env->yieldContent('icon', 'bi-exclamation-triangle-fill');
@endphp

<x-frontend.layout :title="$pageTitle" :description="$subtitle">
    <div class="na-error-wrapper py-4 py-lg-5" dir="{{ $direction }}">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-9">
                <!-- Main Error Card -->
                <div class="na-error-card text-center p-4 p-md-5 mb-4 position-relative shadow-lg rounded-4">
                    <!-- Background NA Emblem Glow -->
                    <div class="na-error-watermark" aria-hidden="true"></div>

                    <!-- Status Code Badge & Visual Icon -->
                    <div class="d-inline-flex align-items-center justify-content-center mb-3">
                        <div class="na-error-code-badge position-relative">
                            <span class="na-error-number">{{ $code }}</span>
                            <div class="na-error-icon-bubble shadow">
                                <i class="bi {{ $icon }}"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Headings -->
                    <h1 class="na-error-title fw-bold text-dark mb-2">
                        {{ $title }}
                    </h1>
                    <p class="na-error-subtitle fs-5 text-secondary fw-semibold mb-3">
                        {{ $subtitle }}
                    </p>
                    <p class="na-error-desc text-muted mx-auto mb-4" style="max-width: 680px; line-height: 1.8;">
                        {{ $message }}
                    </p>

                    <!-- Recovery Quote Badge -->
                    <div class="na-recovery-quote-box p-3 rounded-3 mb-4 mx-auto text-start" style="max-width: 640px;">
                        <div class="d-flex align-items-center gap-2 mb-1 text-primary fw-bold small">
                            <i class="bi bi-quote fs-5"></i>
                            <span>{{ __('messages.NA') ?? 'زمالة المدمنين المجهولين' }}</span>
                        </div>
                        <p class="mb-0 small text-dark fst-italic">
                            {{ __('errors.recovery_quote', [], $locale) }}
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap justify-content-center gap-3 mb-2">
                        <a href="{{ url('/') }}" class="btn btn-primary btn-lg rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 shadow-sm na-btn-action">
                            <i class="bi bi-house-door-fill"></i>
                            <span>{{ __('errors.back_home', [], $locale) }}</span>
                        </a>

                        <a href="{{ route('meeting.index') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 na-btn-action">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>{{ __('errors.find_meetings', [], $locale) }}</span>
                        </a>

                        @if($code === '419' || $code === '500' || $code === '503')
                            <button type="button" onclick="window.location.reload();" class="btn btn-outline-secondary btn-lg rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 na-btn-action">
                                <i class="bi bi-arrow-clockwise"></i>
                                <span>{{ __('errors.try_again', [], $locale) }}</span>
                            </button>
                        @else
                            <button type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ url('/') }}';" class="btn btn-outline-secondary btn-lg rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 na-btn-action">
                                <i class="bi {{ $isArabic ? 'bi-arrow-right' : 'bi-arrow-left' }}"></i>
                                <span>{{ __('errors.go_back', [], $locale) }}</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Emergency Helpline & Crisis Support Drawer -->
                <div class="card na-helpline-drawer border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 text-start">
                        <div class="row align-items-center g-3">
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="na-helpline-icon-badge text-danger bg-danger-subtle rounded-circle p-3 flex-shrink-0">
                                        <i class="bi bi-headset fs-3"></i>
                                    </div>
                                    <div>
                                        <h2 class="h6 fw-bold text-dark mb-1">
                                            {{ __('errors.helplines_title', [], $locale) }}
                                        </h2>
                                        <p class="text-muted small mb-0">
                                            {{ __('errors.helplines_desc', [], $locale) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="d-flex flex-wrap gap-2 justify-content-md-end justify-content-start">
                                    <a href="tel:+201060933888" class="btn btn-danger rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 shadow-xs">
                                        <i class="bi bi-telephone-fill"></i>
                                        <span dir="ltr">+20 106 093 3888</span>
                                    </a>
                                    <a href="tel:+201006979198" class="btn btn-outline-danger rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-telephone-fill"></i>
                                        <span dir="ltr">+20 100 697 9198</span>
                                    </a>
                                    <a href="https://wa.me/201060933888" target="_blank" rel="noopener noreferrer" class="btn btn-success rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 shadow-xs">
                                        <i class="bi bi-whatsapp"></i>
                                        <span>{{ __('errors.whatsapp_direct', [], $locale) }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Page Modern Custom Styling -->
    <style>
        .na-error-wrapper {
            position: relative;
            min-height: calc(100vh - 280px);
            display: flex;
            align-items: center;
        }

        .na-error-card {
            background: radial-gradient(circle at top right, rgba(50, 85, 127, 0.07), transparent 45%),
                        radial-gradient(circle at bottom left, rgba(0, 105, 143, 0.05), transparent 40%),
                        linear-gradient(180deg, #ffffff 0%, #f8fbfe 100%);
            border: 1px solid rgba(50, 85, 127, 0.12);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .na-error-watermark {
            position: absolute;
            inset-inline-end: -40px;
            top: -40px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background-image: url('{{ asset('assets/images/icons/na-logo.png') }}');
            background-size: 140px;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.06;
            pointer-events: none;
            z-index: 0;
            animation: naSpinSlow 60s linear infinite;
        }

        @keyframes naSpinSlow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .na-error-code-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 30px;
            border-radius: 60px;
            background: linear-gradient(135deg, rgba(50, 85, 127, 0.12) 0%, rgba(0, 105, 143, 0.08) 100%);
            border: 1px solid rgba(50, 85, 127, 0.20);
            box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.8), 0 8px 24px -10px rgba(50, 85, 127, 0.3);
        }

        .na-error-number {
            font-size: clamp(3.2rem, 7vw, 5.5rem);
            font-weight: 900;
            letter-spacing: -2px;
            line-height: 1;
            background: linear-gradient(135deg, #1e3a5f 0%, #32557f 50%, #00698f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .na-error-icon-bubble {
            position: absolute;
            inset-inline-end: -12px;
            bottom: -8px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #ffffff;
            color: #00698f;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            border: 2px solid rgba(50, 85, 127, 0.15);
            animation: naFloat 3s ease-in-out infinite alternate;
        }

        @keyframes naFloat {
            0% { transform: translateY(0px) scale(1); }
            100% { transform: translateY(-6px) scale(1.08); }
        }

        .na-error-title {
            font-size: clamp(1.5rem, 3.5vw, 2.25rem);
            color: #1e293b !important;
            letter-spacing: -0.5px;
        }

        .na-error-subtitle {
            color: #475569 !important;
        }

        .na-recovery-quote-box {
            background: linear-gradient(135deg, rgba(235, 243, 252, 0.7) 0%, rgba(240, 249, 255, 0.85) 100%);
            border: 1px dashed rgba(50, 85, 127, 0.25);
            border-inline-start: 4px solid #32557f;
            box-shadow: 0 4px 14px -6px rgba(50, 85, 127, 0.08);
        }

        .na-btn-action {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 600;
        }

        .na-btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -8px rgba(50, 85, 127, 0.35) !important;
        }

        .na-helpline-drawer {
            background: linear-gradient(135deg, #ffffff 0%, #fffdfd 100%);
            border: 1px solid rgba(220, 53, 69, 0.15);
            box-shadow: 0 10px 30px -15px rgba(220, 53, 69, 0.12);
        }

        .na-helpline-icon-badge {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 576px) {
            .na-error-card {
                padding: 1.5rem 1rem !important;
            }
            .na-error-code-badge {
                padding: 6px 20px;
            }
            .na-btn-action {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</x-frontend.layout>
