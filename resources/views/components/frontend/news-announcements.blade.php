@props([
    'announcements' => [],
])

<section class="na-section-shell mb-5" aria-labelledby="news-heading">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <div class="d-inline-flex align-items-center gap-2 text-primary fw-bold small mb-1">
        <i class="bi bi-megaphone-fill"></i>
        <span>{{ __('messages.news_announcements_badge') ?? 'الأخبار والفعاليات الرسمية' }}</span>
      </div>
      <h2 id="news-heading" class="na-heading-2 mb-1">
        {{ __('messages.fellowship_news_conventions_heading') ?? 'أخبار الزمالة، المؤتمرات، وورش العمل' }}
      </h2>
      <p class="text-muted small mb-0">
        {{ __('messages.fellowship_news_subtitle') ?? 'متابعة مستمرة لكافة أنشطة الزمالة الإقليمية، المؤتمرات السنوية، واللقاءات الخدمية العامة في مصر.' }}
      </p>
    </div>

    <a href="{{ route('frontend.events') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold text-nowrap d-inline-flex align-items-center gap-2">
      <span>{{ __('messages.view_full_events_calendar') ?? 'تقويم الفعاليات الكامل' }}</span>
      <i class="bi bi-arrow-left"></i>
    </a>
  </div>

  <!-- Featured Regional Convention Banner -->
  <div class="p-4 rounded-4 mb-4 text-white position-relative overflow-hidden shadow-md" style="background: linear-gradient(135deg, #101b30 0%, #1b2a4a 55%, #32557f 100%);">
    <div class="row align-items-center">
      <div class="col-12 col-md-8">
        <span class="badge rounded-pill bg-warning text-dark fw-bold px-3 py-1.5 mb-2 shadow-xs">
          <i class="bi bi-star-fill me-1"></i> {{ __('messages.featured_fellowship_event') ?? 'الحدث الإقليمي البارز' }}
        </span>
        <h3 class="fw-bold fs-4 text-white mb-2">
          {{ __('messages.annual_egypt_convention_title') ?? 'المؤتمر السنوي لزمالة المدمنين المجهولين بمصر (مسار يجمعنا)' }}
        </h3>
        <p class="text-white-50 small mb-3 mb-md-0" style="max-width: 620px; line-height: 1.6;">
          {{ __('messages.convention_banner_desc') ?? 'تجمع  سنوي يضم أعضاء الزمالة من كافة المحافظات والزوار من حول العالم لمشاركة الأمل والقوة والتعافي.' }}
        </p>
      </div>

      <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('frontend.events') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-primary shadow-sm d-inline-flex align-items-center gap-2">
          <i class="bi bi-calendar2-check-fill text-primary"></i>
          <span>{{ __('messages.convention_details_btn') ?? 'تفاصيل وجدول المؤتمر' }}</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Announcements Grid -->
  <div class="row g-3">
    @forelse($announcements as $event)
      @php
        $startDate = $event->start ? \Carbon\Carbon::parse($event->start)->locale(app()->getLocale())->translatedFormat('j M Y') : 'قريباً';
        $location = $event->location ?: 'محدد في الإعلان';
      @endphp
      <div class="col-12 col-md-4">
        <div class="na-event-card">
          <div>
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="na-event-date-badge">
                <i class="bi bi-calendar-event"></i>
                <span>{{ $startDate }}</span>
              </span>
              @if($event->is_featured)
                <span class="badge rounded-pill shadow-xs" style="background-color: #fef3c7 !important; color: #92400e !important; border: 1px solid #fde68a !important; font-size: 0.75rem; font-weight: 700; padding: 4px 10px;">
                  <i class="bi bi-star-fill text-warning me-1"></i>{{ __('messages.featured') ?? 'مميز' }}
                </span>
              @endif
            </div>

            <h4 class="fw-bold fs-6 text-dark mb-2 lh-base">
              {{ $event->title }}
            </h4>

            @if(!empty($event->description))
              <p class="text-secondary small mb-3 text-truncate-2" style="line-height: 1.5;">
                {{ Str::limit(strip_tags($event->description), 110) }}
              </p>
            @endif
          </div>

          <div class="pt-2 border-top d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-1 text-muted small text-truncate">
              <i class="bi bi-geo-alt text-primary"></i>
              <span class="text-truncate">{{ $location }}</span>
            </div>

            <a href="{{ route('frontend.events') }}" class="btn btn-sm btn-link text-primary text-decoration-none fw-bold p-0">
              <span>{{ __('messages.read_more') ?? 'المزيد' }} &larr;</span>
            </a>
          </div>
        </div>
      </div>
    @empty
      <!-- Fallback Informational Announcements -->
      <div class="col-12 col-md-4">
        <div class="na-event-card">
          <div>
            <div class="na-event-date-badge mb-2">
              <i class="bi bi-calendar-event"></i>
              <span>شهري</span>
            </div>
            <h4 class="fw-bold fs-6 text-dark mb-2">
              اجتماعات لجان الخدمة ومجموعات العمل الإقليمية
            </h4>
            <p class="text-secondary small mb-3" style="line-height: 1.5;">
              تُعقد اجتماعات لجان الخدمة الفرعية لمناقشة تطوير الخدمات وإيصال الرسالة إلى المدمن الذي لا يزال يعاني.
            </p>
          </div>
          <div class="pt-2 border-top text-end">
            <a href="{{ route('frontend.forpublic') }}" class="btn btn-sm btn-link text-primary text-decoration-none fw-bold p-0">
              لجان الخدمة &larr;
            </a>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4">
        <div class="na-event-card">
          <div>
            <div class="na-event-date-badge mb-2">
              <i class="bi bi-book"></i>
              <span>مكتبة الأدبيات</span>
            </div>
            <h4 class="fw-bold fs-6 text-dark mb-2">
              الأدبيات والكتيبات المعتمدة للزمالة
            </h4>
            <p class="text-secondary small mb-3" style="line-height: 1.5;">
              النص الأساسي، خطوة خطوة، وكتيبات المعلومات العامة متوفرة رقمياً ومطبوعاً لكل المجموعات والأعضاء.
            </p>
          </div>
          <div class="pt-2 border-top text-end">
            <a href="{{ route('frontend.literature') }}" class="btn btn-sm btn-link text-primary text-decoration-none fw-bold p-0">
              تصفح الأدبيات &larr;
            </a>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4">
        <div class="na-event-card">
          <div>
            <div class="na-event-date-badge mb-2">
              <i class="bi bi-broadcast"></i>
              <span>العلاقات العامة</span>
            </div>
            <h4 class="fw-bold fs-6 text-dark mb-2">
              ورش عمل المستشفيات والمؤسسات (H&I)
            </h4>
            <p class="text-secondary small mb-3" style="line-height: 1.5;">
              ورش عمل دورية لأعضاء الزمالة الراغبين في الخدمة وحمل الرسالة للمرضى والنزلاء في مصحات العلاج والمؤسسات.
            </p>
          </div>
          <div class="pt-2 border-top text-end">
            <a href="{{ route('frontend.forpublic') }}" class="btn btn-sm btn-link text-primary text-decoration-none fw-bold p-0">
              دليل الخدمة &larr;
            </a>
          </div>
        </div>
      </div>
    @endforelse
  </div>
</section>
