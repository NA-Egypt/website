@props([
    'announcements' => [],
    'heroEvent' => null,
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

  <!-- Dynamic Featured Hero Event Banner -->
  @if($heroEvent)
    @php
      $heroStartDate = $heroEvent->start ? \Carbon\Carbon::parse($heroEvent->start)->locale(app()->getLocale())->translatedFormat('j M Y - h:i A') : null;
      $heroEndDate = $heroEvent->end ? \Carbon\Carbon::parse($heroEvent->end)->locale(app()->getLocale())->translatedFormat('j M Y - h:i A') : null;
      $heroLocInfo = $heroEvent->location_link_info;
    @endphp
    <div id="homepage-hero-event-banner" class="p-4 rounded-4 mb-4 text-white position-relative overflow-hidden shadow-md" style="background: linear-gradient(135deg, #101b30 0%, #1b2a4a 55%, #32557f 100%);">
      <div class="row align-items-center g-3">
        <div class="col-12 col-md-8">
          <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <span class="badge rounded-pill bg-warning text-dark fw-bold px-3 py-1.5 shadow-xs">
              <i class="bi bi-star-fill me-1"></i> {{ __('messages.featured_fellowship_event') ?? 'الحدث الإقليمي البارز' }}
            </span>

            @if($heroStartDate)
              <span class="badge rounded-pill fw-semibold px-3 py-1.5 d-inline-flex align-items-center gap-1" style="background: rgba(255, 255, 255, 0.14); color: #f8fafc; border: 1px solid rgba(255, 255, 255, 0.22);">
                <i class="bi bi-calendar-event text-warning"></i>
                <span>{{ $heroStartDate }}{{ ($heroEndDate && $heroEndDate !== $heroStartDate) ? ' — ' . $heroEndDate : '' }}</span>
              </span>
            @endif
          </div>

          <h3 class="fw-bold fs-4 text-white mb-2">
            {{ $heroEvent->title }}
          </h3>

          @if(!empty($heroEvent->description))
            <p class="text-white-50 small mb-3" style="max-width: 680px; line-height: 1.6;">
              {{ $heroEvent->description }}
            </p>
          @endif

          <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
            @if(!empty($heroEvent->organizer))
              <span class="badge rounded-pill fw-semibold px-3 py-1.5 d-inline-flex align-items-center gap-1" style="background: rgba(255, 255, 255, 0.12); color: #e2e8f0; border: 1px solid rgba(255, 255, 255, 0.2);">
                <i class="bi bi-person-badge text-info"></i>
                <span>{{ $heroEvent->organizer }}</span>
              </span>
            @endif

            @if(!empty($heroEvent->location))
              @if($heroLocInfo['is_link'])
                <a href="{{ $heroLocInfo['url'] }}" target="_blank" rel="noopener noreferrer" class="badge rounded-pill fw-bold px-3 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-xs" style="background: {{ $heroLocInfo['type'] === 'map' ? '#10b981' : '#3b82f6' }}; color: #ffffff;">
                  <i class="bi {{ $heroLocInfo['type'] === 'zoom' ? 'bi-camera-video-fill' : ($heroLocInfo['type'] === 'map' ? 'bi-geo-alt-fill' : 'bi-box-arrow-up-right') }}"></i>
                  <span>{{ $heroLocInfo['label'] }}</span>
                </a>
              @else
                <span class="badge rounded-pill fw-semibold px-3 py-1.5 d-inline-flex align-items-center gap-1" style="background: rgba(255, 255, 255, 0.12); color: #e2e8f0; border: 1px solid rgba(255, 255, 255, 0.2);">
                  <i class="bi bi-geo-alt-fill text-warning"></i>
                  <span>{{ $heroEvent->location }}</span>
                </span>
              @endif
            @endif
          </div>
        </div>

        <div class="col-12 col-md-4 text-md-end">
          <a href="{{ route('frontend.events') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-primary shadow-sm d-inline-flex align-items-center gap-2">
            <i class="bi bi-calendar2-check-fill text-primary"></i>
            <span>{{ __('messages.event_details_btn') ?? 'تفاصيل وجدول الحدث' }}</span>
          </a>
        </div>
      </div>
    </div>
  @endif

  <!-- Announcements Grid -->
  <div class="row g-3">
    @forelse($announcements as $event)
      @php
        $startDate = $event->start ? \Carbon\Carbon::parse($event->start)->locale(app()->getLocale())->translatedFormat('j M Y') : 'قريباً';
        $location = $event->location ?: 'محدد في الإعلان';
        $locInfo = $event->location_link_info;
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

          <div class="pt-2 border-top d-flex align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-1 text-muted small text-truncate">
              @if($locInfo['is_link'])
                <a href="{{ $locInfo['url'] }}" target="_blank" rel="noopener noreferrer" class="text-primary fw-semibold text-decoration-none d-inline-flex align-items-center gap-1">
                  <i class="bi {{ $locInfo['type'] === 'zoom' ? 'bi-camera-video-fill' : ($locInfo['type'] === 'map' ? 'bi-geo-alt-fill' : 'bi-box-arrow-up-right') }}"></i>
                  <span>{{ $locInfo['label'] }}</span>
                </a>
              @else
                <i class="bi bi-geo-alt text-primary"></i>
                <span class="text-truncate">{{ $location }}</span>
              @endif
            </div>

            <a href="{{ route('frontend.events') }}" class="btn btn-sm btn-link text-primary text-decoration-none fw-bold p-0 shrink-0">
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
