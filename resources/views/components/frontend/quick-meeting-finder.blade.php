@props([
    'cities' => [],
    'days' => [],
    'previewMeetings' => [],
])

@php
    $cairoNow = \Carbon\Carbon::now('Africa/Cairo');
    $currentDayName = $cairoNow->locale(app()->getLocale())->dayName;
    $locale = app()->getLocale();
@endphp

<section id="meeting-finder-section" class="na-section-shell" aria-labelledby="meeting-finder-heading">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2">
    <div>
      <div class="d-inline-flex align-items-center gap-2 text-primary fw-bold small mb-1">
        <i class="bi bi-geo-alt-fill"></i>
        <span>{{ __('messages.find_meeting_fast_label') ?? 'محرك البحث السريع عن الاجتماعات' }}</span>
      </div>
      <h2 id="meeting-finder-heading" class="na-heading-2 mb-1">
        {{ __('messages.find_meeting_heading') ?? 'ابحث عن اجتماع تعافي الآن' }}
      </h2>
      <p class="text-muted small mb-0">
        {{ __('messages.find_meeting_subtitle') ?? 'أكثر من 300 اجتماع أسبوعي في جميع محافظات مصر واجتماعات يومية عبر الإنترنت.' }}
      </p>
    </div>

    <!-- Full Directory Link -->
    <a href="{{ route('frontend.meetings') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold text-nowrap d-inline-flex align-items-center gap-2">
      <span>{{ __('messages.view_full_meetings_directory') ?? 'دليل كافة الاجتماعات' }}</span>
      <i class="bi bi-arrow-left"></i>
    </a>
  </div>

  <!-- Interactive Fast Filter Bar -->
  <div class="na-finder-filter-bar">
    <div class="row g-3 align-items-end">
      <!-- 1. City / Governorate Selector -->
      <div class="col-12 col-md-4">
        <label for="meetingFinderCitySelect" class="form-label fw-bold small text-secondary">
          <i class="bi bi-pin-map-fill me-1 text-primary"></i> {{ __('messages.city_governorate') ?? 'المحافظة / المدينة' }}
        </label>
        <select id="meetingFinderCitySelect" class="na-filter-select" onchange="filterPreviewMeetings()">
          <option value="all">{{ __('messages.all_governorates') ?? 'جميع المحافظات' }}</option>
          @foreach($cities as $city)
            <option value="{{ $locale === 'ar' ? $city->ar_name : $city->en_name }}">
              {{ $locale === 'ar' ? $city->ar_name : ($city->en_name ?: $city->ar_name) }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- 2. Day of Week Selector -->
      <div class="col-12 col-sm-6 col-md-4">
        <label for="meetingFinderDaySelect" class="form-label fw-bold small text-secondary">
          <i class="bi bi-calendar-event-fill me-1 text-primary"></i> {{ __('messages.day_of_week') ?? 'اليوم' }}
        </label>
        <select id="meetingFinderDaySelect" class="na-filter-select" onchange="filterPreviewMeetings()">
          <option value="all">{{ __('messages.all_days') ?? 'جميع أيام الأسبوع' }}</option>
          @foreach($days as $day)
            @php
              $dayLabel = $locale === 'ar' ? $day->ar_name : ($day->en_name ?: $day->ar_name);
              $isToday = (trim($day->ar_name ?? '') === trim($currentDayName) || strtolower($day->en_name ?? '') === strtolower($cairoNow->englishDayOfWeek));
            @endphp
            <option value="{{ $dayLabel }}" {{ $isToday ? 'selected' : '' }}>
              {{ $dayLabel }} {{ $isToday ? '(' . (__('messages.today') ?? 'اليوم') . ')' : '' }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- 3. Format Toggle (In-Person vs Online) -->
      <div class="col-12 col-sm-6 col-md-4">
        <label class="form-label fw-bold small text-secondary d-block">
          <i class="bi bi-sliders me-1 text-primary"></i> {{ __('messages.meeting_format') ?? 'طبيعة الحضور' }}
        </label>
        <div class="na-format-pill-toggle" role="group" aria-label="Meeting Format">
          <button type="button" id="fmtBtnAll" class="active" onclick="setFormatFilter('all')">{{ __('messages.all') ?? 'الكل' }}</button>
          <button type="button" id="fmtBtnInPerson" onclick="setFormatFilter('in_person')">{{ __('messages.in_person') ?? 'حضوري' }}</button>
          <button type="button" id="fmtBtnOnline" onclick="setFormatFilter('online')">{{ __('messages.online_zoom') ?? 'أونلاين (زووم)' }}</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Live Meeting Preview Cards Grid -->
  <div id="previewMeetingsContainer" class="na-meeting-card-grid">
    @forelse($previewMeetings as $meeting)
      @php
        $isOnline = !empty($meeting->direct_online_group_id);
        $groupName = $isOnline 
          ? ($meeting->directOnlineGroup?->ar_name ?? 'اجتماع أونلاين')
          : ($meeting->group?->ar_name ?? 'اجتماع زمالة');
        $cityName = $isOnline
          ? 'أونلاين (زووم)'
          : ($meeting->group?->neighborhood?->city?->ar_name ?? 'مصر');
        $address = $isOnline
          ? 'عبر تطبيق زووم'
          : ($meeting->group?->ar_address ?? $meeting->group?->location ?? 'العنوان متاح بالدليل');
        $zoomLink = $isOnline ? $meeting->directOnlineGroup?->location : null;
        $dayName = $meeting->day?->ar_name ?? '';
      @endphp
      <div class="na-meeting-card" 
           data-city="{{ $cityName }}" 
           data-day="{{ $dayName }}" 
           data-format="{{ $isOnline ? 'online' : 'in_person' }}">
        <div>
          <div class="d-flex align-items-center justify-content-between mb-2">
            @if($isOnline)
              <span class="na-meeting-badge-online">
                <i class="bi bi-camera-video-fill"></i> أونلاين
              </span>
            @else
              <span class="na-meeting-badge-inperson">
                <i class="bi bi-building"></i> حضوري
              </span>
            @endif

            <span class="badge bg-light text-secondary border font-monospace small">
              {{ $dayName }}
            </span>
          </div>

          <h3 class="fw-bold fs-6 text-dark mb-1 lh-sm">{{ $groupName }}</h3>
          
          <div class="d-flex align-items-center gap-1 text-muted small mb-2">
            <i class="bi bi-geo-alt text-primary"></i>
            <span>{{ $cityName }}</span>
          </div>

          <p class="text-secondary small mb-3 text-truncate-2" style="font-size: 0.84rem; line-height: 1.4;">
            {{ $address }}
          </p>
        </div>

        <div class="pt-2 border-top d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-1 text-dark fw-bold small">
            <i class="bi bi-clock text-info"></i>
            <span>{{ $meeting->start_time ? \Carbon\Carbon::parse($meeting->start_time)->format('h:i A') : 'محدد' }}</span>
          </div>

          @if($isOnline && $zoomLink)
            <a href="{{ $zoomLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary rounded-pill px-3 py-1 shadow-xs d-inline-flex align-items-center gap-1">
              <i class="bi bi-box-arrow-up-right"></i>
              <span>{{ __('messages.join_zoom') ?? 'دخول' }}</span>
            </a>
          @elseif(!$isOnline && !empty($meeting->group?->location))
            <a href="https://maps.google.com/?q={{ urlencode($meeting->group?->location) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1">
              <i class="bi bi-map"></i>
              <span>{{ __('messages.directions') ?? 'الخريطة' }}</span>
            </a>
          @else
            <a href="{{ route('frontend.meetings') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1">
              <span>{{ __('messages.details') ?? 'التفاصيل' }}</span>
            </a>
          @endif
        </div>
      </div>
    @empty
      <div class="col-12 text-center py-4 text-muted">
        <i class="bi bi-search fs-2 mb-2 d-block"></i>
        <span>{{ __('messages.loading_meetings') ?? 'جاري تحميل الاجتماعات...' }}</span>
      </div>
    @endforelse
  </div>

  <div id="noMeetingFoundMsg" class="d-none text-center py-4 my-2">
    <div class="alert alert-info d-inline-block rounded-4 px-4 py-3 border-0 shadow-sm">
      <i class="bi bi-info-circle-fill me-2 fs-5"></i>
      <span>{{ __('messages.no_meetings_preview_match') ?? 'لم نجد اجتماعات مطابقة لهذا الفلتر في المعاينة السريعة. يمكنك الاطلاع على كافة المواعيد في الدليل الكامل.' }}</span>
      <div class="mt-2">
        <a href="{{ route('frontend.meetings') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 mt-1">
          {{ __('messages.open_directory_now') ?? 'فتح دليل الاجتماعات الكامل' }}
        </a>
      </div>
    </div>
  </div>
</section>

<script>
  let currentFormatFilter = 'all';

  function setFormatFilter(fmt) {
    currentFormatFilter = fmt;
    ['fmtBtnAll', 'fmtBtnInPerson', 'fmtBtnOnline'].forEach(id => {
      const btn = document.getElementById(id);
      if (btn) btn.classList.remove('active');
    });

    if (fmt === 'all') document.getElementById('fmtBtnAll')?.classList.add('active');
    if (fmt === 'in_person') document.getElementById('fmtBtnInPerson')?.classList.add('active');
    if (fmt === 'online') document.getElementById('fmtBtnOnline')?.classList.add('active');

    filterPreviewMeetings();
  }

  function filterPreviewMeetings() {
    const cityVal = document.getElementById('meetingFinderCitySelect')?.value || 'all';
    const dayVal = document.getElementById('meetingFinderDaySelect')?.value || 'all';
    const cards = document.querySelectorAll('#previewMeetingsContainer .na-meeting-card');
    const emptyMsg = document.getElementById('noMeetingFoundMsg');
    
    let visibleCount = 0;

    cards.forEach(card => {
      const cardCity = card.getAttribute('data-city') || '';
      const cardDay = card.getAttribute('data-day') || '';
      const cardFormat = card.getAttribute('data-format') || '';

      const matchCity = (cityVal === 'all' || cardCity.includes(cityVal));
      const matchDay = (dayVal === 'all' || cardDay.includes(dayVal));
      const matchFormat = (currentFormatFilter === 'all' || cardFormat === currentFormatFilter);

      if (matchCity && matchDay && matchFormat) {
        card.style.display = 'flex';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    if (emptyMsg) {
      if (visibleCount === 0 && cards.length > 0) {
        emptyMsg.classList.remove('d-none');
      } else {
        emptyMsg.classList.add('d-none');
      }
    }
  }
</script>
