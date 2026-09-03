<x-frontend.layout 
    :title="__('messages.contactus_page_title')" 
    :description="__('messages.contactus_subtitle')">

    <x-slot:head>
        @php
            $currentHost = request()->getHost();
            $isNaEgypt = ($currentHost === 'naegypt.org' || str_ends_with($currentHost, '.naegypt.org'));
        @endphp
        @if($isNaEgypt)
        <script type="application/ld+json">
{
  "{{ '@' }}context": "https://schema.org",
  "{{ '@' }}type": "NGO",
  "name": "Narcotics Anonymous Egypt",
  "alternateName": "NA Egypt",
  "url": "https://naegypt.org",
  "sameAs": [
    "https://egyptna.org"
  ],
  "contactPoint": {
    "{{ '@' }}type": "ContactPoint",
    "contactType": "Helplines",
    "url": "https://naegypt.org/contactus"
  }
}
</script>
        @endif
    </x-slot:head>

    @php
        $currentHost = request()->getHost();
        $isEgyptNa = ($currentHost === 'egyptna.org' || str_ends_with($currentHost, '.egyptna.org'));
        $isArabic = app()->getLocale() === 'ar';
        $pageDir = $isArabic ? 'rtl' : 'ltr';
    @endphp

    <div class="contact-page-wrapper" dir="{{ $pageDir }}">
        <!-- Section Header -->
        <div class="text-center mb-3">
            <x-section-head>{{ __('messages.contactus_page_title') }}</x-section-head>
        </div>

        <!-- Subtitle Introduction -->
        <p class="text-center text-muted mx-auto mb-4 lead-subtitle" style="max-width: 780px;">
            {{ __('messages.contactus_subtitle') }}
        </p>

        <!-- Urgent Recovery Assistance Banner -->
        <div class="card urgent-help-card mb-4 border-0 shadow-sm overflow-hidden">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex flex-column flex-md-row align-items-center gap-3 text-start">
                    <div class="urgent-icon-wrapper flex-shrink-0">
                        <i class="bi bi-shield-heart-fill"></i>
                    </div>
                    <div class="flex-grow-1 text-center text-md-start urgent-text-col">
                        <h2 class="h5 fw-bold mb-1 text-danger-emphasis title-safe">{{ __('messages.urgent_help_title') }}</h2>
                        <p class="mb-0 text-muted small" style="line-height: 1.7;">
                            {{ __('messages.urgent_help_desc') }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 d-flex flex-wrap gap-2 justify-content-center">
                        <a href="tel:+201060933888" class="btn btn-danger btn-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-telephone-fill"></i>
                            <span>{{ __('messages.call_now') }}</span>
                        </a>
                        <a href="https://wa.me/201060933888" target="_blank" rel="noopener noreferrer" class="btn btn-success btn-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-whatsapp"></i>
                            <span>{{ __('messages.chat_whatsapp') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 g-xl-4 mb-5 align-items-stretch">
            <!-- Column 1: Official Charity Info & Direct Helplines -->
            <div class="col-lg-5">
                <div class="d-flex flex-column gap-4 h-100 justify-content-between">

                    <!-- Official Charity Registration & Address Card -->
                    <div class="card modern-contact-card official-charity-card border-0 shadow-sm">
                        <div class="card-body p-4 text-start">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="card-icon-badge bg-primary-subtle text-primary">
                                    <i class="bi bi-building-check"></i>
                                </div>
                                <div class="text-start">
                                    <h2 class="h5 fw-bold mb-1 text-dark title-safe">{{ __('messages.charity_info_title') }}</h2>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill" dir="ltr">
                                        {{ __('messages.charity_reg_label') }}: 7786 / 2009-12-21
                                    </span>
                                </div>
                            </div>

                            <div class="charity-entity-info mb-3 p-3 rounded-3 bg-light-subtle border text-start">
                                <div class="fw-bold text-dark small mb-1 title-safe d-flex align-items-center gap-2">
                                    <i class="bi bi-award-fill text-warning"></i>
                                    <span>{{ __('messages.charity_assoc_name') }}</span>
                                </div>
                                <div class="text-muted small" style="line-height: 1.6;">
                                    {{ __('messages.thewarning') }}
                                </div>
                            </div>

                            <div class="address-box p-3 rounded-3 border bg-white shadow-xs text-start">
                                <div class="d-flex align-items-start gap-2 mb-2">
                                    <i class="bi bi-geo-alt-fill text-danger fs-5 mt-1 flex-shrink-0"></i>
                                    <div class="text-start">
                                        <div class="fw-semibold text-secondary small">{{ __('messages.charity_assoc_address_label') }}</div>
                                        <div class="fw-bold text-dark fs-6 mt-1 title-safe" style="line-height: 1.6;">{{ __('messages.charity_assoc_address') }}</div>
                                    </div>
                                </div>
                                <div class="mt-3 d-flex justify-content-start">
                                    <a href="https://www.google.com/maps/search/?api=1&query=9458+%D8%B4%D8%A7%D8%B1%D8%B9+%D8%A7%D9%84%D8%B4%D9%87%D9%8A%D8%AF+%D9%83%D8%B1%D9%8A%D9%85+%D8%A8%D9%86%D9%88%D9%86%D9%87+%D8%A7%D9%84%D9%85%D9%82%D8%B7%D9%85" 
                                       target="_blank" 
                                       rel="noopener noreferrer" 
                                       class="btn btn-outline-primary btn-sm rounded-pill d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-map-fill"></i>
                                        <span>{{ __('messages.open_in_google_maps') }}</span>
                                        <i class="bi bi-box-arrow-up-right small"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Direct Helplines Card -->
                    <div class="card modern-contact-card helplines-info-card border-0 shadow-sm">
                        <div class="card-body p-4 text-start">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="card-icon-badge bg-info-subtle text-info">
                                    <i class="bi bi-headset"></i>
                                </div>
                                <div class="text-start">
                                    <h2 class="h5 fw-bold mb-0 text-dark title-safe">{{ __('messages.direct_helplines') }}</h2>
                                    <!-- <small class="text-muted">{{ __('messages.helplines') }}</small> -->
                                </div>
                            </div>

                            <!-- Cairo & Regional Helpline Block -->
                            <div class="helpline-entry p-3 rounded-3 bg-white border shadow-xs mb-3 text-start">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                    <span class="fw-bold text-dark fs-6 title-safe">{{ __('messages.cairo_regional_helpline') }}</span>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill small d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-clock-fill text-primary"></i>
                                        {{ __('messages.regionaltiming') }}
                                    </span>
                                </div>
                                <div class="d-flex flex-column gap-2 mt-2">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-1 border-bottom">
                                        <span class="font-monospace fw-bold fs-6 text-dark" dir="ltr">+20 100 697 9198</span>
                                        <div class="d-flex gap-2">
                                            <a href="tel:+201006979198" class="btn btn-sm btn-outline-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-telephone-fill"></i>
                                                <span>{{ __('messages.call_now') }}</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-1">
                                        <span class="font-monospace fw-bold fs-6 text-dark" dir="ltr">+20 106 093 3888</span>
                                        <div class="d-flex gap-2">
                                            <a href="tel:+201060933888" class="btn btn-sm btn-outline-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-telephone-fill"></i>
                                                <span>{{ __('messages.call_now') }}</span>
                                            </a>
                                            <a href="https://wa.me/201060933888" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success rounded-pill px-3 d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-whatsapp"></i>
                                                <span>{{ __('messages.chat_whatsapp') }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Alexandria Helpline Block -->
                            <div class="helpline-entry p-3 rounded-3 bg-white border shadow-xs text-start">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                    <span class="fw-bold text-dark fs-6 title-safe">{{ __('messages.alex_helpline') }}</span>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill small d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-clock-fill text-primary"></i>
                                        {{ __('messages.leactiming') }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-2">
                                    <span class="font-monospace fw-bold fs-6 text-dark" dir="ltr">+20 150 388 4411</span>
                                    <div class="d-flex gap-2">
                                        <a href="tel:+201503884411" class="btn btn-sm btn-outline-primary rounded-pill px-3 d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-telephone-fill"></i>
                                            <span>{{ __('messages.call_now') }}</span>
                                        </a>
                                        <a href="https://wa.me/201503884411" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success rounded-pill px-3 d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-whatsapp"></i>
                                            <span>{{ __('messages.chat_whatsapp') }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Email & Social Channels Card -->
                    <div class="card modern-contact-card channels-card border-0 shadow-sm">
                        <div class="card-body p-4 text-start">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-envelope-at-fill text-primary fs-5"></i>
                                    <div class="text-start">
                                        <div class="fw-bold text-dark title-safe">{{ __('messages.official_email') }}</div>
                                        <a href="mailto:info@naegypt.org" class="text-decoration-none text-muted font-monospace small" dir="ltr">info@naegypt.org</a>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 d-inline-flex align-items-center gap-1" onclick="copyEmail(this, 'info@naegypt.org')">
                                    <i class="bi bi-copy"></i>
                                    <span class="copy-text">{{ __('messages.copy_email') }}</span>
                                </button>
                            </div>
                            <hr class="my-3 opacity-25">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <!-- <span class="small text-muted fw-semibold title-safe">{{ __('messages.EgyptPRCommitteeFacebookPage') }}</span> -->
                                <div class="d-flex gap-2">
                                    <a href="https://www.facebook.com/OfficialNAEgyPage" target="_blank" rel="noopener noreferrer" class="social-circle-btn" aria-label="Facebook">
                                        <i class="bi bi-facebook"></i>
                                    </a>
                                    <a href="https://www.instagram.com/narcoticsanonymousegy" target="_blank" rel="noopener noreferrer" class="social-circle-btn" aria-label="Instagram">
                                        <i class="bi bi-instagram"></i>
                                    </a>
                                    <a href="https://www.tiktok.com/@narcoticsanonymousegypt" target="_blank" rel="noopener noreferrer" class="social-circle-btn" aria-label="TikTok">
                                        <i class="bi bi-tiktok"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Official Staging & Development Notice Card -->
                    <div class="card modern-contact-card staging-notice-card border-0 shadow-sm">
                        <div class="card-body p-4 text-start">
                            @if($isEgyptNa)
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="card-icon-badge bg-warning-subtle text-warning-emphasis">
                                        <i class="bi bi-cone-striped"></i>
                                    </div>
                                    <div class="text-start">
                                        <h2 class="h6 fw-bold mb-0 text-dark title-safe">egyptna.org Staging Portal</h2>
                                        <small class="text-muted">{{ __('messages.staging_portal_subtitle') }}</small>
                                    </div>
                                </div>
                                <p class="mb-0 text-dark small fw-medium" style="line-height: 1.7;" dir="ltr">
                                    egyptna.org is a staging portal for Narcotics Anonymous Egypt. The official public site is hosted at <a href="https://naegypt.org" class="fw-bold text-primary text-decoration-none">naegypt.org</a>.
                                </p>
                                @if($isArabic)
                                <p class="mb-0 mt-2 text-muted small border-top pt-2" style="line-height: 1.7;" dir="rtl">
                                    {{ __('messages.staging_portal_desc') }}
                                </p>
                                @endif
                            @else
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="card-icon-badge bg-primary-subtle text-primary">
                                        <i class="bi bi-shield-check"></i>
                                    </div>
                                    <div class="text-start">
                                        <h2 class="h6 fw-bold mb-0 text-dark title-safe">Official Staging &amp; Development Notice:</h2>
                                        <small class="text-muted">{{ __('messages.staging_notice_subtitle') }}</small>
                                    </div>
                                </div>
                                <p class="mb-0 text-muted small" style="line-height: 1.7;" dir="ltr">
                                    The website <a href="https://egyptna.org" class="fw-bold text-primary text-decoration-none" target="_blank" rel="noopener">egyptna.org</a> is an authorized, official staging and preview environment operated by <strong>Narcotics Anonymous Egypt</strong> (<a href="https://naegypt.org" class="text-primary text-decoration-none" target="_blank" rel="noopener">naegypt.org</a>). Both domains are owned and managed by the same entity.
                                </p>
                                @if($isArabic)
                                <p class="mb-0 mt-2 text-muted small border-top pt-2" style="line-height: 1.7;" dir="rtl">
                                    {{ __('messages.staging_notice_desc') }}
                                </p>
                                @endif
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            <!-- Column 2: Enhanced Contact Form -->
            <div class="col-lg-7">
                <div class="card modern-contact-card form-container-card border-0 shadow-sm h-100">
                    <div class="card-body p-4 p-md-5 d-flex flex-column justify-content-between text-start">
                        <div>
                            <div class="d-flex align-items-center gap-3 mb-4 text-start">
                                <div class="card-icon-badge bg-primary-subtle text-primary">
                                    <i class="bi bi-chat-square-text-fill"></i>
                                </div>
                                <div class="text-start">
                                    <h2 class="h4 fw-bold mb-1 text-dark title-safe">{{ __('messages.contact_form_title') }}</h2>
                                    <p class="text-muted small mb-0" style="line-height: 1.6;">{{ __('messages.contact_form_subtitle') }}</p>
                                </div>
                            </div>

                            <form class="row g-3" id="contactForm" method="POST" action="{{ route('contactus.store') }}">
                                @csrf

                                <!-- Inquiry Subject / Category Dropdown -->
                                <div class="col-12 text-start">
                                    <label class="form-label fw-semibold text-dark small mb-1 title-safe d-flex align-items-center gap-2" for="contactSubject">
                                        <i class="bi bi-tag-fill text-primary"></i>
                                        <span>{{ __('messages.Subject') }}</span>
                                    </label>
                                    <select class="form-select modern-input @error('subject') is-invalid @enderror" id="contactSubject" name="subject">
                                        <option value="" selected>{{ __('messages.Please select a subject') }}</option>
                                        <option value="{{ __('messages.subject_general') }}" {{ old('subject') === __('messages.subject_general') ? 'selected' : '' }}>
                                            {{ __('messages.subject_general') }}
                                        </option>
                                        <option value="{{ __('messages.subject_pr') }}" {{ old('subject') === __('messages.subject_pr') ? 'selected' : '' }}>
                                            {{ __('messages.subject_pr') }}
                                        </option>
                                        <option value="{{ __('messages.subject_literature') }}" {{ old('subject') === __('messages.subject_literature') ? 'selected' : '' }}>
                                            {{ __('messages.subject_literature') }}
                                        </option>
                                        <option value="{{ __('messages.subject_meetings') }}" {{ old('subject') === __('messages.subject_meetings') ? 'selected' : '' }}>
                                            {{ __('messages.subject_meetings') }}
                                        </option>
                                        <option value="{{ __('messages.subject_website') }}" {{ old('subject') === __('messages.subject_website') ? 'selected' : '' }}>
                                            {{ __('messages.subject_website') }}
                                        </option>
                                    </select>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Name Field -->
                                <div class="col-md-6 text-start">
                                    <label class="form-label fw-semibold text-dark small mb-1 title-safe d-flex align-items-center gap-2" for="contactName">
                                        <i class="bi bi-person-fill text-primary"></i>
                                        <span>{{ __('messages.Name') }} <span class="text-danger">*</span></span>
                                    </label>
                                    <input type="text" 
                                           id="contactName" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="{{ __('messages.Please enter your name') }}" 
                                           required 
                                           autocomplete="name"
                                           class="form-control modern-input @error('name') is-invalid @enderror">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email Field -->
                                <div class="col-md-6 text-start">
                                    <label class="form-label fw-semibold text-dark small mb-1 title-safe d-flex align-items-center gap-2" for="contactEmail">
                                        <i class="bi bi-envelope-fill text-primary"></i>
                                        <span>{{ __('messages.Email') }} <span class="text-danger">*</span></span>
                                    </label>
                                    <input type="email" 
                                           id="contactEmail" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="{{ __('messages.Please enter your email') }}" 
                                           required 
                                           autocomplete="email"
                                           dir="ltr"
                                           class="form-control modern-input text-email-input @error('email') is-invalid @enderror">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Phone Field (Optional) -->
                                <div class="col-12 text-start">
                                    <label class="form-label fw-semibold text-dark small mb-1 title-safe d-flex align-items-center gap-2" for="contactPhone">
                                        <i class="bi bi-telephone-fill text-primary"></i>
                                        <span>{{ __('messages.Phone') }}</span>
                                    </label>
                                    <input type="tel" 
                                           id="contactPhone" 
                                           name="phone" 
                                           value="{{ old('phone') }}" 
                                           placeholder="{{ __('messages.Please enter your phone') }}" 
                                           autocomplete="tel"
                                           dir="{{ $isArabic ? 'rtl' : 'ltr' }}"
                                           class="form-control modern-input @error('phone') is-invalid @enderror">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Message Field -->
                                <div class="col-12 text-start">
                                    <label class="form-label fw-semibold text-dark small mb-1 title-safe d-flex align-items-center gap-2" for="contactMessage">
                                        <i class="bi bi-pencil-fill text-primary"></i>
                                        <span>{{ __('messages.Message') }} <span class="text-danger">*</span></span>
                                    </label>
                                    <textarea class="form-control modern-input @error('message') is-invalid @enderror" 
                                              id="contactMessage" 
                                              rows="5" 
                                              name="message" 
                                              placeholder="{{ __('messages.Please enter your message') }}" 
                                              required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Google reCAPTCHA -->
                                <div class="col-12">
                                    <div class="recaptcha-holder d-flex justify-content-start">
                                        <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                                    </div>
                                    @error('g-recaptcha-response')
                                        <div class="text-danger mt-1 small text-start">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12 mt-4">
                                    <button type="submit" 
                                            id="submitBtn" 
                                            class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm g-recaptcha"
                                            data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}" 
                                            data-callback="onSubmit" 
                                            data-action="submit">
                                        <i class="bi bi-send-fill"></i>
                                        <span class="title-safe">{{ __('messages.Send') }}</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Reassurance Note -->
                        <div class="form-footer-note text-center mt-4 pt-3 border-top">
                            <small class="text-muted d-inline-flex align-items-center gap-1 title-safe" style="line-height: 1.6;">
                                <i class="bi bi-lock-fill text-success"></i>
                                {{ __('messages.thewarning') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert and Form Scripts -->
    <script>
        function onSubmit(token) {
            const submitBtn = document.getElementById("submitBtn");
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2 ms-2" role="status" aria-hidden="true"></span> {{ __("messages.sending") }}';
            }
            document.getElementById("contactForm").submit();
        }

        function copyEmail(btn, emailText) {
            navigator.clipboard.writeText(emailText).then(function() {
                const textSpan = btn.querySelector('.copy-text');
                const originalText = textSpan.innerText;
                textSpan.innerText = '{{ __("messages.copied") }}';
                btn.classList.add('btn-success', 'text-white');
                btn.classList.remove('btn-light');
                setTimeout(function() {
                    textSpan.innerText = originalText;
                    btn.classList.remove('btn-success', 'text-white');
                    btn.classList.add('btn-light');
                }, 2000);
            });
        }
    </script>

    @if(session('status') === 'mail-sent')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: '{{ __("messages.Success!") }}',
                    text: '{{ __("messages.mail-sent") }}',
                    icon: 'success',
                    confirmButtonText: '{{ __("messages.Done") }}',
                    confirmButtonColor: '#32557f',
                    customClass: {
                        popup: 'rounded-4 shadow-lg'
                    }
                });
            });
        </script>
    @endif

    <!-- Scoped Styling -->
    <style>
        .contact-page-wrapper {
            animation: fadeInPage 0.4s ease-in-out;
            padding-bottom: 2rem;
        }

        @keyframes fadeInPage {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Directional Alignment Rules */
        [dir="rtl"] .contact-page-wrapper,
        [dir="rtl"] .contact-page-wrapper .text-start,
        [dir="rtl"] .contact-page-wrapper .card-body,
        [dir="rtl"] .contact-page-wrapper .urgent-text-col,
        [dir="rtl"] .contact-page-wrapper .official-charity-card,
        [dir="rtl"] .contact-page-wrapper .helplines-info-card,
        [dir="rtl"] .contact-page-wrapper .channels-card,
        [dir="rtl"] .contact-page-wrapper .staging-notice-card,
        [dir="rtl"] .contact-page-wrapper .form-container-card,
        [dir="rtl"] .contact-page-wrapper .charity-entity-info,
        [dir="rtl"] .contact-page-wrapper .address-box,
        [dir="rtl"] .contact-page-wrapper .helpline-entry,
        [dir="rtl"] .contact-page-wrapper .form-label,
        [dir="rtl"] .contact-page-wrapper .invalid-feedback {
            text-align: right !important;
            direction: rtl !important;
        }

        [dir="rtl"] .contact-page-wrapper .form-select,
        [dir="rtl"] .contact-page-wrapper .form-control:not(.text-email-input) {
            text-align: right !important;
            direction: rtl !important;
        }

        [dir="rtl"] .contact-page-wrapper .form-select {
            background-position: left 0.75rem center !important;
            padding-left: 2.25rem !important;
            padding-right: 1rem !important;
        }

        [dir="rtl"] .contact-page-wrapper .text-email-input {
            text-align: right !important;
            direction: ltr !important;
        }

        [dir="rtl"] .contact-page-wrapper .text-email-input::placeholder {
            text-align: right !important;
            direction: rtl !important;
        }

        [dir="ltr"] .contact-page-wrapper,
        [dir="ltr"] .contact-page-wrapper .text-start,
        [dir="ltr"] .contact-page-wrapper .card-body,
        [dir="ltr"] .contact-page-wrapper .urgent-text-col,
        [dir="ltr"] .contact-page-wrapper .official-charity-card,
        [dir="ltr"] .contact-page-wrapper .helplines-info-card,
        [dir="ltr"] .contact-page-wrapper .channels-card,
        [dir="ltr"] .contact-page-wrapper .staging-notice-card,
        [dir="ltr"] .contact-page-wrapper .form-container-card,
        [dir="ltr"] .contact-page-wrapper .charity-entity-info,
        [dir="ltr"] .contact-page-wrapper .address-box,
        [dir="ltr"] .contact-page-wrapper .helpline-entry,
        [dir="ltr"] .contact-page-wrapper .form-label,
        [dir="ltr"] .contact-page-wrapper .form-select,
        [dir="ltr"] .contact-page-wrapper .form-control,
        [dir="ltr"] .contact-page-wrapper .invalid-feedback {
            text-align: left !important;
            direction: ltr !important;
        }

        [dir="ltr"] .contact-page-wrapper .form-select {
            background-position: right 0.75rem center !important;
            padding-right: 2.25rem !important;
            padding-left: 1rem !important;
        }

        /* Title Line-Height and Descender Protection */
        .title-safe,
        .contact-page-wrapper h1,
        .contact-page-wrapper h2,
        .contact-page-wrapper h3,
        .contact-page-wrapper h4,
        .contact-page-wrapper h5,
        .contact-page-wrapper .h4,
        .contact-page-wrapper .h5 {
            line-height: 1.55 !important;
            padding-bottom: 3px;
            overflow: visible;
        }

        .text-gradient {
            line-height: 1.5 !important;
            padding-bottom: 8px !important;
            padding-top: 2px !important;
            display: inline-block;
            overflow: visible;
        }

        .lead-subtitle {
            font-size: 1.05rem;
            line-height: 1.7;
        }

        /* Modern Card Styling */
        .modern-contact-card {
            background: #ffffff;
            border-radius: 20px;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            border: 1px solid rgba(50, 85, 127, 0.08) !important;
        }

        .modern-contact-card:hover {
            box-shadow: 0 12px 30px rgba(50, 85, 127, 0.08) !important;
        }

        /* Urgent Help Banner */
        .urgent-help-card {
            background: linear-gradient(135deg, #fff5f5 0%, #fff8f0 100%);
            border-radius: 20px;
            border: 1px solid #fed7d7 !important;
        }

        .urgent-icon-wrapper {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #ffe3e3;
            color: #e53e3e;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            box-shadow: 0 4px 12px rgba(229, 62, 62, 0.15);
        }

        /* Card Icon Badges */
        .card-icon-badge {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        /* Inputs & Selects */
        .modern-input {
            border-radius: 12px;
            padding: 0.8rem 1rem;
            border: 1px solid #dce4ec;
            background-color: #f8fafc;
            line-height: 1.5;
            transition: all 0.2s ease;
        }

        .modern-input:focus {
            background-color: #ffffff;
            border-color: #32557f;
            box-shadow: 0 0 0 0.25rem rgba(50, 85, 127, 0.15);
        }

        /* Social Circle Buttons */
        .social-circle-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #32557f;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 1.05rem;
        }

        .social-circle-btn:hover {
            background: #32557f;
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(50, 85, 127, 0.2);
        }

        /* Submit Button */
        #submitBtn {
            background: linear-gradient(135deg, #32557f 0%, #1e3a5f 100%);
            border: none;
            transition: all 0.3s ease;
        }

        #submitBtn:hover {
            background: linear-gradient(135deg, #274567 0%, #152943 100%);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(50, 85, 127, 0.25) !important;
        }

        .shadow-xs {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }
    </style>
</x-frontend.layout>
