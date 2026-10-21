@props([
    'cities' => [],
    'days' => [],
    'previewMeetings' => [],
    'locations' => [],
    'allMeetingsJson' => [],
])

@php
    $cairoNow = \Carbon\Carbon::now('Africa/Cairo');
    $cairoTomorrow = $cairoNow->copy()->addDay();
    $currentDayName = $cairoNow->locale(app()->getLocale())->dayName;
    $currentEnglishDay = $cairoNow->englishDayOfWeek;
    $tomorrowEnglishDay = $cairoTomorrow->englishDayOfWeek;
    $locale = app()->getLocale();
@endphp

<section id="meeting-finder-section" class="na-section-shell position-relative" aria-labelledby="meeting-finder-heading">
  <!-- Header & Full Directory Action -->
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div>
      <div class="d-inline-flex align-items-center gap-2 text-primary fw-bold small mb-1">
        <i class="bi bi-geo-alt-fill"></i>
        <span>{{ __('messages.find_meeting_fast_label') }}</span>
      </div>
      <h2 id="meeting-finder-heading" class="na-heading-2 mb-1">
        {{ __('messages.find_meeting_heading') }}
      </h2>
      <p class="text-muted small mb-0">
        {{ __('messages.find_meeting_subtitle') }}
      </p>
    </div>

    <!-- Full Directory Link -->
    <a href="{{ route('frontend.meetings') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold text-nowrap d-inline-flex align-items-center gap-2 shadow-xs transition-all">
      <span>{{ __('messages.view_full_meetings_directory') }}</span>
      <i class="bi {{ $locale === 'ar' ? 'bi-arrow-left' : 'bi-arrow-right' }}"></i>
    </a>
  </div>

  <!-- ======================================================================
       SMART QUICK-RESULT: "أقرب اجتماع يبدأ قريباً" (Next Upcoming Meeting)
       ====================================================================== -->
  <div id="smartUpcomingCardWrap" class="mb-4">
    <div class="card na-smart-upcoming-card shadow-sm border-0 position-relative overflow-hidden">
      <div class="card-body p-3 p-md-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 pb-3 border-bottom border-light-subtle">
          <div class="d-flex align-items-center flex-wrap gap-2">
            <span class="na-upcoming-header-badge d-inline-flex align-items-center gap-1.5 fw-bold">
              <i class="bi bi-lightning-charge-fill text-warning"></i>
              <span>{{ __('messages.next_upcoming_meeting') }}</span>
            </span>
            <span id="upcomingCountdownBadge" class="badge rounded-pill na-countdown-pill d-inline-flex align-items-center gap-1.5">
              <span class="upcoming-pulse-dot" aria-hidden="true"></span>
              <span id="upcomingCountdownText">{{ __('messages.starts_now') }}</span>
            </span>
            <span id="upcomingFormatBadge" class="na-meeting-badge-online">
              <i class="bi bi-camera-video-fill"></i> <span id="upcomingFormatLabel">{{ __('messages.online_zoom') }}</span>
            </span>
          </div>

          <div class="text-muted small font-monospace d-flex align-items-center gap-1">
            <i class="bi bi-clock-history text-primary"></i>
            <span id="upcomingTimeText">--:--</span>
          </div>
        </div>

        <div class="row align-items-center g-3 pt-3">
          <div class="col-12 col-lg-8">
            <h3 id="upcomingMeetingName" class="fw-bold fs-5 text-dark mb-1 lh-sm">
              {{ __('messages.loading_meetings') }}
            </h3>

            <div class="d-flex align-items-center gap-2 text-muted small mb-2 flex-wrap">
              <span class="d-inline-flex align-items-center gap-1">
                <i class="bi bi-geo-alt-fill text-primary"></i>
                <span id="upcomingLocationText">--</span>
              </span>
              <span class="text-secondary opacity-50">•</span>
              <span id="upcomingAddressText" class="text-secondary text-truncate-1">--</span>
            </div>

            <!-- Tags -->
            <div id="upcomingTagsContainer" class="d-flex flex-wrap gap-1">
              <!-- Dynamically rendered tags -->
            </div>
          </div>

          <div class="col-12 col-lg-4 text-lg-end">
            <div id="upcomingActionContainer">
              <a id="upcomingActionBtn" href="{{ route('frontend.meetings') }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm w-100 w-lg-auto justify-content-center">
                <i class="bi bi-box-arrow-up-right"></i>
                <span id="upcomingActionBtnText">{{ __('messages.details') }}</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ======================================================================
       INTERACTIVE FAST FILTER BAR & SEARCH
       ====================================================================== -->
  <div class="na-finder-filter-bar mb-3 p-3 p-md-4 rounded-4 bg-white shadow-sm border border-light-subtle">
    <div class="row g-3">
      <!-- 1. Location Search (Autocomplete with Governorates & Districts) -->
      <div class="col-12 col-md-5 position-relative">
        <label for="meetingFinderLocationInput" class="form-label fw-bold small text-secondary d-flex justify-content-between align-items-center mb-1">
          <span><i class="bi bi-search text-primary me-1"></i> {{ __('messages.city_governorate') }}</span>
          <span class="badge bg-light text-muted border font-monospace xsmall" style="font-size: 0.72rem;">{{ count($locations) }} {{ __('messages.district') }}/{{ __('messages.governorate') }}</span>
        </label>
        
        <div class="position-relative">
          <input type="text" 
                 id="meetingFinderLocationInput" 
                 class="na-filter-input pe-5" 
                 placeholder="{{ __('messages.search_location_placeholder') }}" 
                 autocomplete="off"
                 aria-autocomplete="list"
                 aria-expanded="false"
                 role="combobox"
                 aria-controls="meetingFinderLocationDropdown">
          
          <button type="button" 
                  id="meetingFinderClearLocationBtn" 
                  class="btn btn-link btn-sm position-absolute top-50 translate-middle-y text-muted text-decoration-none p-1 d-none" 
                  style="inset-inline-end: 12px; z-index: 5;"
                  title="{{ __('messages.clear_location') }}">
            <i class="bi bi-x-circle-fill fs-6"></i>
          </button>
        </div>

        <!-- Autocomplete Suggestions Dropdown -->
        <div id="meetingFinderLocationDropdown" class="na-autocomplete-dropdown shadow-lg d-none" role="listbox">
          <div class="p-2 border-bottom bg-light d-flex align-items-center justify-content-between">
            <span class="text-muted small fw-semibold">{{ __('messages.city_governorate') }}</span>
            <button type="button" class="btn btn-sm btn-link p-0 text-primary text-decoration-none small" onclick="selectLocationOption('all')">
              {{ __('messages.all_governorates') }}
            </button>
          </div>
          <!-- Popular shortcuts -->
          <div class="p-2 border-bottom bg-white">
            <div class="text-muted xsmall mb-1" style="font-size: 0.72rem;">{{ $locale === 'ar' ? 'وجهات شائعة:' : 'Popular destinations:' }}</div>
            <div class="d-flex flex-wrap gap-1">
              @php
                $popularLocations = ['القاهرة', 'الجيزة', 'الإسكندرية', 'المعادي', 'مدينة نصر', 'المهندسين', 'مصر الجديدة', 'مدينة 6 أكتوبر', 'المنصورة', 'طنطا', 'أسيوط'];
              @endphp
              @foreach($popularLocations as $popular)
                <button type="button" class="btn btn-xs na-quick-chip" onclick="selectLocationOption('{{ $popular }}')">
                  {{ $popular }}
                </button>
              @endforeach
            </div>
          </div>
          <div id="meetingFinderLocationItems" class="na-autocomplete-list">
            <!-- Dynamically populated options -->
          </div>
        </div>
      </div>

      <!-- 2. Day of Week Selector -->
      <div class="col-12 col-sm-6 col-md-3">
        <label for="meetingFinderDaySelect" class="form-label fw-bold small text-secondary mb-1">
          <i class="bi bi-calendar-event-fill me-1 text-primary"></i> {{ __('messages.day_of_week') }}
        </label>
        <select id="meetingFinderDaySelect" class="na-filter-select" onchange="applyMeetingFilters()">
          <option value="today" selected>{{ __('messages.today') }} ({{ $currentDayName }})</option>
          <option value="tomorrow">{{ __('messages.tomorrow') }} ({{ $cairoTomorrow->locale($locale)->dayName }})</option>
          <option value="all">{{ __('messages.all_week') }}</option>
          <option disabled>──────────</option>
          @foreach($days as $day)
            @php
              $dayLabel = $locale === 'ar' ? $day->ar_name : ($day->en_name ?: $day->ar_name);
            @endphp
            <option value="{{ $day->en_name }}">{{ $dayLabel }}</option>
          @endforeach
        </select>
      </div>

      <!-- 3. Time Slot Selector -->
      <div class="col-12 col-sm-6 col-md-4">
        <label for="meetingFinderTimeSlotSelect" class="form-label fw-bold small text-secondary mb-1">
          <i class="bi bi-clock-fill me-1 text-primary"></i> {{ __('messages.time_slot') }}
        </label>
        <select id="meetingFinderTimeSlotSelect" class="na-filter-select" onchange="applyMeetingFilters()">
          <option value="all" selected>{{ __('messages.time_all') }}</option>
          <option value="morning">{{ __('messages.time_morning') }}</option>
          <option value="afternoon">{{ __('messages.time_afternoon') }}</option>
          <option value="evening">{{ __('messages.time_evening') }}</option>
          <option value="night">{{ __('messages.time_night') }}</option>
        </select>
      </div>

      <!-- 4. Format Toggle & Proximity Geolocation Button Row -->
      <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-3 pt-2 border-top border-light-subtle">
        <div class="d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0" style="min-width: 280px; max-width: 440px;">
          <span class="text-secondary small fw-bold text-nowrap d-none d-sm-inline">
            <i class="bi bi-sliders text-primary"></i> {{ __('messages.meeting_format') }}:
          </span>
          <div class="na-format-pill-toggle flex-grow-1" role="group" aria-label="Meeting Format">
            <button type="button" id="fmtBtnAll" class="active" onclick="setFormatFilter('all')">{{ __('messages.all') ?? 'الكل' }}</button>
            <button type="button" id="fmtBtnInPerson" onclick="setFormatFilter('in_person')">{{ __('messages.in_person') ?? 'حضوري' }}</button>
            <button type="button" id="fmtBtnOnline" onclick="setFormatFilter('online')">{{ __('messages.online_zoom') ?? 'أونلاين (زووم)' }}</button>
          </div>
        </div>

        <!-- Proximity Search Button -->
        <div class="d-flex align-items-center gap-2 ms-auto">
          <button type="button" 
                  id="meetingFinderGeoBtn" 
                  class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs"
                  onclick="requestUserProximitySort()">
            <i class="bi bi-crosshair"></i>
            <span id="geoBtnText">{{ __('messages.use_my_location') }}</span>
          </button>

          <button type="button" 
                  id="meetingFinderResetAllBtn" 
                  class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 text-secondary d-none" 
                  onclick="resetAllMeetingFilters()"
                  title="{{ __('messages.reset_all_filters') }}">
            <i class="bi bi-arrow-counterclockwise"></i>
            <span>{{ __('messages.reset_all_filters') }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Active Geolocation Status Alert Banner -->
    <div id="geoActiveBanner" class="alert alert-info py-2 px-3 small border-0 rounded-3 mt-3 mb-0 d-none align-items-center justify-content-between gap-2">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-geo-alt-fill text-primary fs-6"></i>
        <span class="fw-semibold">{{ __('messages.location_sorting_active') }}</span>
      </div>
      <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-danger fw-bold" onclick="clearProximitySort()">
        {{ __('messages.clear_location') }}
      </button>
    </div>
  </div>

  <!-- Filter Summary & Count Header -->
  <div class="d-flex align-items-center justify-content-between mb-3 px-1 text-muted small">
    <div id="meetingResultsCountText" class="fw-semibold">
      <!-- "Showing X meetings" -->
    </div>
  </div>

  <!-- ======================================================================
       LIVE MEETING CARDS GRID
       ====================================================================== -->
  <div id="previewMeetingsContainer" class="na-meeting-card-grid">
    {{-- Initial SSR Render for Search Engines / No-JS --}}
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
                <i class="bi bi-camera-video-fill"></i> {{ __('messages.online_zoom') }}
              </span>
            @else
              <span class="na-meeting-badge-inperson">
                <i class="bi bi-building"></i> {{ __('messages.in_person') }}
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
          <div class="d-flex align-items-center gap-1 text-dark fw-bold small font-monospace">
            <i class="bi bi-clock text-info"></i>
            <span>{{ $meeting->start_time ? \Carbon\Carbon::parse($meeting->start_time)->format('h:i A') : 'محدد' }}</span>
          </div>

          @if($isOnline && $zoomLink)
            <a href="{{ $zoomLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary rounded-pill px-3 py-1 shadow-xs d-inline-flex align-items-center gap-1">
              <i class="bi bi-box-arrow-up-right"></i>
              <span>{{ __('messages.join_zoom') }}</span>
            </a>
          @elseif(!$isOnline && !empty($meeting->group?->location))
            <a href="https://maps.google.com/?q={{ urlencode($meeting->group?->location) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1">
              <i class="bi bi-map"></i>
              <span>{{ __('messages.directions') }}</span>
            </a>
          @else
            <a href="{{ route('frontend.meetings') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1">
              <span>{{ __('messages.details') }}</span>
            </a>
          @endif
        </div>
      </div>
    @empty
      <div class="col-12 text-center py-4 text-muted">
        <i class="bi bi-search fs-2 mb-2 d-block"></i>
        <span>{{ __('messages.loading_meetings') }}</span>
      </div>
    @endforelse
  </div>

  <!-- Load More Action -->
  <div id="meetingFinderLoadMoreWrap" class="text-center mt-4 d-none">
    <button type="button" id="meetingFinderLoadMoreBtn" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold shadow-xs d-inline-flex align-items-center gap-2" onclick="showMoreMeetings()">
      <i class="bi bi-arrow-down-circle"></i>
      <span id="loadMoreBtnText">{{ __('messages.load_more_meetings', ['count' => '']) }}</span>
    </button>
  </div>

  <!-- ======================================================================
       COMPASSIONATE FALLBACK (Zero Matches)
       ====================================================================== -->
  <div id="noMeetingFoundMsg" class="d-none text-center py-5 my-3">
    <div class="card border-0 shadow-sm rounded-4 mx-auto p-4" style="max-width: 620px; background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%); border: 1.5px solid #bae6fd !important;">
      <div class="d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 58px; height: 58px; border-radius: 50%; background: #e0f2fe; color: #0284c7;">
        <i class="bi bi-heart-pulse-fill fs-3"></i>
      </div>
      <h4 class="fw-bold text-dark mb-2 fs-5">
        {{ $locale === 'ar' ? 'نحن معك أينما كنت' : 'We are with you wherever you are' }}
      </h4>
      <p class="text-secondary small mb-4 lh-base">
        {{ __('messages.no_meetings_compassionate') }}
      </p>
      <div class="d-flex flex-wrap align-items-center justify-content-center gap-2">
        <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-xs d-inline-flex align-items-center gap-2" onclick="switchToOnlineAndClear()">
          <i class="bi bi-camera-video-fill"></i>
          <span>{{ __('messages.browse_online_meetings') }}</span>
        </button>
        <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold" onclick="resetAllMeetingFilters()">
          <i class="bi bi-arrow-counterclockwise"></i>
          <span>{{ __('messages.reset_all_filters') }}</span>
        </button>
      </div>
    </div>
  </div>
</section>

<!-- Scoped Styles -->
<style>
  .na-smart-upcoming-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fbfd 60%, #f0f7fb 100%);
    border: 1.5px solid rgba(50, 85, 127, 0.22) !important;
    border-radius: 20px;
    box-shadow: 0 8px 24px -4px rgba(50, 85, 127, 0.12);
    transition: all 0.3s ease;
  }
  .na-smart-upcoming-card:hover {
    border-color: var(--na-teal-600) !important;
    box-shadow: 0 12px 32px -4px rgba(50, 85, 127, 0.2);
  }
  .na-upcoming-header-badge {
    color: #32557f;
    background: rgba(50, 85, 127, 0.08);
    border: 1px solid rgba(50, 85, 127, 0.18);
    border-radius: 9999px;
    padding: 3px 12px;
    font-size: 0.82rem;
  }
  .na-countdown-pill {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    font-size: 0.8rem;
    font-weight: 700;
    padding: 3px 10px;
  }
  .upcoming-pulse-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background-color: #ef4444;
    display: inline-block;
    box-shadow: 0 0 6px #ef4444;
    animation: finderPulse 1.8s infinite;
  }
  @keyframes finderPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(1.25); }
  }
  .na-filter-input {
    border: 1.5px solid var(--na-border-strong);
    border-radius: 10px;
    padding: 9px 14px;
    font-size: 0.95rem;
    background-color: #ffffff;
    color: var(--na-text-main);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    width: 100%;
    font-family: inherit;
  }
  .na-filter-input:focus {
    outline: none;
    border-color: var(--na-teal-600);
    box-shadow: 0 0 0 3px rgba(50, 85, 127, 0.15);
  }
  .na-autocomplete-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1.5px solid var(--na-border);
    border-radius: 12px;
    z-index: 1050;
    max-height: 320px;
    overflow-y: auto;
  }
  .na-autocomplete-list {
    max-height: 200px;
    overflow-y: auto;
  }
  .na-autocomplete-item {
    padding: 8px 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.88rem;
    color: var(--na-text-main);
    transition: background 0.15s ease;
    border-bottom: 1px solid #f1f5f9;
  }
  .na-autocomplete-item:last-child {
    border-bottom: none;
  }
  .na-autocomplete-item:hover, .na-autocomplete-item.active {
    background: #f0f7ff;
    color: #32557f;
  }
  .na-quick-chip {
    font-size: 0.72rem;
    padding: 2px 8px;
    border-radius: 9999px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .na-quick-chip:hover {
    border-color: var(--na-teal-500);
    background: #f0f7ff;
    color: #32557f;
  }
  .na-tag-badge {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    border-radius: 9999px;
    font-size: 0.72rem;
    padding: 1.5px 7px;
    font-weight: 500;
  }
  .na-distance-badge {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
    border-radius: 9999px;
    font-size: 0.72rem;
    padding: 1.5px 7px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 3px;
  }
  .xsmall {
    font-size: 0.75rem;
  }
</style>

<!-- ======================================================================
     FULL CLIENT-SIDE CONTROLLER & DATASET SCRIPT
     ====================================================================== -->
<script>
  (function() {
    // 1. Initial Datasets passed from server
    const ALL_LOCATIONS = @json($locations ?? []);
    const ALL_MEETINGS = @json($allMeetingsJson ?? []);
    const LOCALE = "{{ $locale }}";
    const CURRENT_ENGLISH_DAY = "{{ $currentEnglishDay }}";
    const TOMORROW_ENGLISH_DAY = "{{ $tomorrowEnglishDay }}";
    const CAIRO_SERVER_ISO = "{{ $cairoNow->toIso8601String() }}";

    // Client State
    let selectedLocation = 'all';
    let selectedDay = 'today';
    let selectedTimeSlot = 'all';
    let selectedFormat = 'all';
    let userLocation = null; // { lat, lng }
    let proximitySortingActive = false;
    let visibleLimit = 9;
    const PAGE_CHUNK = 9;

    // Elements
    const locInput = document.getElementById('meetingFinderLocationInput');
    const locDropdown = document.getElementById('meetingFinderLocationDropdown');
    const locItems = document.getElementById('meetingFinderLocationItems');
    const clearLocBtn = document.getElementById('meetingFinderClearLocationBtn');
    const daySelect = document.getElementById('meetingFinderDaySelect');
    const timeSlotSelect = document.getElementById('meetingFinderTimeSlotSelect');
    const cardsContainer = document.getElementById('previewMeetingsContainer');
    const noResultsMsg = document.getElementById('noMeetingFoundMsg');
    const resultsCountText = document.getElementById('meetingResultsCountText');
    const loadMoreWrap = document.getElementById('meetingFinderLoadMoreWrap');
    const loadMoreBtnText = document.getElementById('loadMoreBtnText');
    const resetAllBtn = document.getElementById('meetingFinderResetAllBtn');
    const geoBtn = document.getElementById('meetingFinderGeoBtn');
    const geoBtnText = document.getElementById('geoBtnText');
    const geoActiveBanner = document.getElementById('geoActiveBanner');

    // Smart Upcoming Elements
    const upcomingCardWrap = document.getElementById('smartUpcomingCardWrap');
    const upcomingName = document.getElementById('upcomingMeetingName');
    const upcomingLocation = document.getElementById('upcomingLocationText');
    const upcomingAddress = document.getElementById('upcomingAddressText');
    const upcomingTime = document.getElementById('upcomingTimeText');
    const upcomingCountdownText = document.getElementById('upcomingCountdownText');
    const upcomingFormatBadge = document.getElementById('upcomingFormatBadge');
    const upcomingFormatLabel = document.getElementById('upcomingFormatLabel');
    const upcomingTags = document.getElementById('upcomingTagsContainer');
    const upcomingActionBtn = document.getElementById('upcomingActionBtn');
    const upcomingActionBtnText = document.getElementById('upcomingActionBtnText');

    // Helper: Haversine distance in km
    function haversineDistance(lat1, lon1, lat2, lon2) {
      const R = 6371; // km
      const dLat = (lat2 - lat1) * Math.PI / 180;
      const dLon = (lon2 - lon1) * Math.PI / 180;
      const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
      return R * c;
    }

    // Helper: Current Cairo Minutes from midnight
    function getCairoCurrentMinutes() {
      // Use client clock synced with server ISO
      const now = new Date();
      return now.getHours() * 60 + now.getMinutes();
    }

    // Helper: Parse HH:mm to minutes from midnight
    function timeToMinutes(timeStr) {
      if (!timeStr) return 0;
      const parts = timeStr.split(':');
      return parseInt(parts[0], 10) * 60 + parseInt(parts[1] || '0', 10);
    }

    // Helper: Format minutes countdown in Arabic/English
    function formatCountdown(diffMinutes) {
      if (diffMinutes < 0 && diffMinutes >= -90) {
        return LOCALE === 'ar' ? '{{ __("messages.in_progress") }}' : 'In progress';
      }
      if (diffMinutes <= 5 && diffMinutes >= 0) {
        return LOCALE === 'ar' ? '{{ __("messages.starts_now") }}' : 'Starting now';
      }
      const hrs = Math.floor(diffMinutes / 60);
      const mins = diffMinutes % 60;
      if (LOCALE === 'ar') {
        if (hrs > 0 && mins > 0) {
          return `يبدأ بعد ${hrs} س و ${mins} د`;
        } else if (hrs > 0) {
          return `يبدأ بعد ${hrs} س`;
        } else {
          return `يبدأ بعد ${mins} دقيقة`;
        }
      } else {
        if (hrs > 0 && mins > 0) {
          return `Starts in ${hrs}h ${mins}m`;
        } else if (hrs > 0) {
          return `Starts in ${hrs}h`;
        } else {
          return `Starts in ${mins} min`;
        }
      }
    }

    // 2. Setup Autocomplete Dropdown
    function initAutocomplete() {
      if (!locInput || !locDropdown) return;

      locInput.addEventListener('focus', function() {
        populateAutocomplete(this.value.trim());
        locDropdown.classList.remove('d-none');
        locInput.setAttribute('aria-expanded', 'true');
      });

      locInput.addEventListener('input', function() {
        const val = this.value.trim();
        if (clearLocBtn) {
          if (val.length > 0) clearLocBtn.classList.remove('d-none');
          else clearLocBtn.classList.add('d-none');
        }
        populateAutocomplete(val);
        locDropdown.classList.remove('d-none');
        locInput.setAttribute('aria-expanded', 'true');
        
        // Also live filter if user typed without picking from dropdown
        selectedLocation = val || 'all';
        applyMeetingFilters();
      });

      // Close dropdown when clicked outside
      document.addEventListener('click', function(e) {
        if (!locInput.contains(e.target) && !locDropdown.contains(e.target)) {
          locDropdown.classList.add('d-none');
          locInput.setAttribute('aria-expanded', 'false');
        }
      });

      if (clearLocBtn) {
        clearLocBtn.addEventListener('click', function() {
          locInput.value = '';
          selectedLocation = 'all';
          clearLocBtn.classList.add('d-none');
          locDropdown.classList.add('d-none');
          applyMeetingFilters();
        });
      }
    }

    function populateAutocomplete(query) {
      if (!locItems) return;
      locItems.innerHTML = '';

      const q = query.toLowerCase();
      const matches = ALL_LOCATIONS.filter(item => {
        if (!q) return true;
        const ar = (item.ar_name || '').toLowerCase();
        const en = (item.en_name || '').toLowerCase();
        const city = (item.city_name || '').toLowerCase();
        return ar.includes(q) || en.includes(q) || city.includes(q);
      }).slice(0, 30);

      if (matches.length === 0) {
        locItems.innerHTML = `<div class="p-3 text-center text-muted small">${LOCALE === 'ar' ? 'لا توجد نتائج مطابقة' : 'No matching locations'}</div>`;
        return;
      }

      matches.forEach(item => {
        const div = document.createElement('div');
        div.className = 'na-autocomplete-item';
        div.setAttribute('role', 'option');
        
        const label = LOCALE === 'ar' ? item.ar_name : (item.en_name || item.ar_name);
        const typeBadge = item.type === 'governorate' 
          ? `<span class="badge bg-primary-subtle text-primary xsmall">${LOCALE === 'ar' ? 'محافظة' : 'Gov'}</span>` 
          : `<span class="badge bg-secondary-subtle text-secondary xsmall">${LOCALE === 'ar' ? 'حي' : 'District'}</span>`;
        
        div.innerHTML = `
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-geo-alt text-muted small"></i>
            <span>${label}</span>
          </div>
          ${typeBadge}
        `;

        div.addEventListener('click', function() {
          selectLocationOption(item.ar_name);
        });

        locItems.appendChild(div);
      });
    }

    // Expose selectLocationOption globally for quick-chip clicks
    window.selectLocationOption = function(locName) {
      if (locName === 'all') {
        selectedLocation = 'all';
        if (locInput) locInput.value = '';
        if (clearLocBtn) clearLocBtn.classList.add('d-none');
      } else {
        selectedLocation = locName;
        if (locInput) locInput.value = locName;
        if (clearLocBtn) clearLocBtn.classList.remove('d-none');
      }
      if (locDropdown) locDropdown.classList.add('d-none');
      if (locInput) locInput.setAttribute('aria-expanded', 'false');
      applyMeetingFilters();
    };

    // 3. Format Toggle Handling
    window.setFormatFilter = function(fmt) {
      selectedFormat = fmt;
      ['fmtBtnAll', 'fmtBtnInPerson', 'fmtBtnOnline'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) btn.classList.remove('active');
      });

      if (fmt === 'all') document.getElementById('fmtBtnAll')?.classList.add('active');
      if (fmt === 'in_person') document.getElementById('fmtBtnInPerson')?.classList.add('active');
      if (fmt === 'online') document.getElementById('fmtBtnOnline')?.classList.add('active');

      applyMeetingFilters();
    };

    // 4. Geolocation Proximity Sorting
    window.requestUserProximitySort = function() {
      if (!navigator.geolocation) {
        alert("{{ __('messages.geo_not_supported') }}");
        return;
      }

      if (geoBtnText) geoBtnText.textContent = "{{ $locale === 'ar' ? 'جاري تحديد موقعك...' : 'Locating...' }}";

      navigator.geolocation.getCurrentPosition(
        function(position) {
          userLocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };
          proximitySortingActive = true;
          if (geoBtnText) geoBtnText.textContent = "{{ __('messages.location_sorting_active') }}";
          if (geoBtn) {
            geoBtn.classList.remove('btn-outline-primary');
            geoBtn.classList.add('btn-primary');
          }
          if (geoActiveBanner) {
            geoActiveBanner.classList.remove('d-none');
            geoActiveBanner.classList.add('d-flex');
          }

          // If format is online only, switch to All so distance sorting makes sense
          if (selectedFormat === 'online') {
            setFormatFilter('in_person');
          } else {
            applyMeetingFilters();
          }
        },
        function(err) {
          if (geoBtnText) geoBtnText.textContent = "{{ __('messages.use_my_location') }}";
          alert("{{ __('messages.geo_denied') }}");
        },
        { enableHighAccuracy: true, timeout: 8000 }
      );
    };

    window.clearProximitySort = function() {
      proximitySortingActive = false;
      userLocation = null;
      if (geoBtnText) geoBtnText.textContent = "{{ __('messages.use_my_location') }}";
      if (geoBtn) {
        geoBtn.classList.add('btn-outline-primary');
        geoBtn.classList.remove('btn-primary');
      }
      if (geoActiveBanner) {
        geoActiveBanner.classList.add('d-none');
        geoActiveBanner.classList.remove('d-flex');
      }
      applyMeetingFilters();
    };

    // 5. Reset All Filters
    window.resetAllMeetingFilters = function() {
      selectedLocation = 'all';
      if (locInput) locInput.value = '';
      if (clearLocBtn) clearLocBtn.classList.add('d-none');
      if (daySelect) daySelect.value = 'today';
      if (timeSlotSelect) timeSlotSelect.value = 'all';
      selectedDay = 'today';
      selectedTimeSlot = 'all';
      clearProximitySort();
      setFormatFilter('all');
    };

    window.switchToOnlineAndClear = function() {
      resetAllMeetingFilters();
      setFormatFilter('online');
    };

    // 6. Main Filter & Render Logic
    window.applyMeetingFilters = function() {
      selectedDay = daySelect ? daySelect.value : 'today';
      selectedTimeSlot = timeSlotSelect ? timeSlotSelect.value : 'all';

      // Show/hide reset all button if filters deviate from defaults
      const isFiltered = (selectedLocation !== 'all' || selectedDay !== 'today' || selectedTimeSlot !== 'all' || selectedFormat !== 'all' || proximitySortingActive);
      if (resetAllBtn) {
        if (isFiltered) resetAllBtn.classList.remove('d-none');
        else resetAllBtn.classList.add('d-none');
      }

      // Filter dataset
      let filtered = ALL_MEETINGS.filter(m => {
        // Location Filter (City / Neighborhood)
        if (selectedLocation && selectedLocation !== 'all') {
          const locQ = selectedLocation.toLowerCase();
          const matchCity = (m.city_ar || '').toLowerCase().includes(locQ) || (m.city_en || '').toLowerCase().includes(locQ);
          const matchNeigh = (m.neighborhood_ar || '').toLowerCase().includes(locQ) || (m.neighborhood_en || '').toLowerCase().includes(locQ);
          const matchAddress = (m.address || '').toLowerCase().includes(locQ);
          if (!matchCity && !matchNeigh && !matchAddress) {
            return false;
          }
        }

        // Day Filter
        if (selectedDay === 'today') {
          if ((m.day_en || '').toLowerCase() !== CURRENT_ENGLISH_DAY.toLowerCase()) return false;
        } else if (selectedDay === 'tomorrow') {
          if ((m.day_en || '').toLowerCase() !== TOMORROW_ENGLISH_DAY.toLowerCase()) return false;
        } else if (selectedDay !== 'all') {
          if ((m.day_en || '').toLowerCase() !== selectedDay.toLowerCase()) return false;
        }

        // Time Slot Filter
        if (selectedTimeSlot !== 'all') {
          const mins = timeToMinutes(m.start_time);
          if (selectedTimeSlot === 'morning' && (mins < 360 || mins >= 720)) return false; // 06:00 - 12:00
          if (selectedTimeSlot === 'afternoon' && (mins < 720 || mins >= 1020)) return false; // 12:00 - 17:00
          if (selectedTimeSlot === 'evening' && (mins < 1020 || mins >= 1260)) return false; // 17:00 - 21:00
          if (selectedTimeSlot === 'night' && (mins >= 360 && mins < 1260)) return false; // 21:00 - 06:00
        }

        // Format Filter
        if (selectedFormat === 'in_person' && m.is_online) return false;
        if (selectedFormat === 'online' && !m.is_online) return false;

        return true;
      });

      // Calculate Proximity distances if location sorting active
      if (proximitySortingActive && userLocation) {
        filtered.forEach(m => {
          if (!m.is_online && m.lat && m.lng) {
            m.distance = haversineDistance(userLocation.lat, userLocation.lng, m.lat, m.lng);
          } else {
            m.distance = 999999;
          }
        });
        filtered.sort((a, b) => a.distance - b.distance);
      } else {
        // Sort chronologically by start_time
        filtered.sort((a, b) => timeToMinutes(a.start_time) - timeToMinutes(b.start_time));
      }

      // Update Smart Quick Result Card
      updateSmartUpcomingCard(filtered);

      // Render Grid
      renderMeetingsGrid(filtered);
    };

    // 7. Smart Upcoming Card Updater
    function updateSmartUpcomingCard(activeFilteredList) {
      if (!upcomingCardWrap) return;

      const cairoMins = getCairoCurrentMinutes();
      
      // Look first inside today's active filtered meetings
      let candidate = null;
      let minDiff = 99999;

      // Filter candidate meetings on today's schedule
      const todayMeetings = ALL_MEETINGS.filter(m => (m.day_en || '').toLowerCase() === CURRENT_ENGLISH_DAY.toLowerCase());

      // If active filtered list contains today's meetings, search within them first
      const pool = (selectedDay === 'today' || selectedDay === 'all') 
        ? activeFilteredList.filter(m => (m.day_en || '').toLowerCase() === CURRENT_ENGLISH_DAY.toLowerCase())
        : todayMeetings;

      // Search for next meeting starting in next 2-3 hours (diff between -15 and 180 min)
      for (let m of pool) {
        const startMins = timeToMinutes(m.start_time);
        const diff = startMins - cairoMins;
        if (diff >= -15 && diff <= 180) {
          if (diff < minDiff) {
            minDiff = diff;
            candidate = m;
          }
        }
      }

      // If no meeting in tight 2-3h window, flexible fallback to earliest next today
      if (!candidate) {
        for (let m of pool) {
          const startMins = timeToMinutes(m.start_time);
          const diff = startMins - cairoMins;
          if (diff > 180) {
            if (diff < minDiff) {
              minDiff = diff;
              candidate = m;
            }
          }
        }
      }

      // If still none today, pick earliest upcoming meeting of entire day or tomorrow
      if (!candidate && todayMeetings.length > 0) {
        candidate = todayMeetings[0];
        minDiff = timeToMinutes(candidate.start_time) - cairoMins;
        if (minDiff < 0) minDiff += 1440; // tomorrow morning
      }

      if (!candidate) {
        upcomingCardWrap.classList.add('d-none');
        return;
      }

      upcomingCardWrap.classList.remove('d-none');
      if (upcomingName) upcomingName.textContent = LOCALE === 'ar' ? candidate.name : candidate.name_en;
      if (upcomingLocation) upcomingLocation.textContent = LOCALE === 'ar' ? candidate.city_ar : candidate.city_en;
      if (upcomingAddress) upcomingAddress.textContent = candidate.address;
      if (upcomingTime) upcomingTime.textContent = candidate.time_fmt;

      if (upcomingCountdownText) {
        upcomingCountdownText.textContent = formatCountdown(minDiff);
      }

      // Format Badge
      if (upcomingFormatBadge && upcomingFormatLabel) {
        if (candidate.is_online) {
          upcomingFormatBadge.className = 'na-meeting-badge-online';
          upcomingFormatBadge.innerHTML = '<i class="bi bi-camera-video-fill"></i>';
          upcomingFormatLabel.textContent = "{{ __('messages.online_zoom') }}";
        } else {
          upcomingFormatBadge.className = 'na-meeting-badge-inperson';
          upcomingFormatBadge.innerHTML = '<i class="bi bi-building"></i>';
          upcomingFormatLabel.textContent = "{{ __('messages.in_person') }}";
        }
      }

      // Tags
      if (upcomingTags) {
        upcomingTags.innerHTML = '';
        (candidate.tags || []).forEach(tag => {
          const span = document.createElement('span');
          span.className = 'badge na-tag-badge';
          span.textContent = tag;
          upcomingTags.appendChild(span);
        });
      }

      // Action Button
      if (upcomingActionBtn && upcomingActionBtnText) {
        if (candidate.is_online && candidate.zoom_url) {
          upcomingActionBtn.href = candidate.zoom_url;
          upcomingActionBtn.className = 'btn btn-primary rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm w-100 w-lg-auto justify-content-center';
          upcomingActionBtnText.textContent = "{{ __('messages.join_zoom') }}";
        } else if (!candidate.is_online && candidate.map_url) {
          upcomingActionBtn.href = candidate.map_url;
          upcomingActionBtn.className = 'btn btn-outline-primary rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 w-100 w-lg-auto justify-content-center';
          upcomingActionBtnText.textContent = "{{ __('messages.directions') }}";
        } else {
          upcomingActionBtn.href = "{{ route('frontend.meetings') }}";
          upcomingActionBtn.className = 'btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 w-100 w-lg-auto justify-content-center';
          upcomingActionBtnText.textContent = "{{ __('messages.details') }}";
        }
      }
    }

    // 8. Grid Renderer
    function renderMeetingsGrid(meetingsList) {
      if (!cardsContainer) return;
      cardsContainer.innerHTML = '';

      if (resultsCountText) {
        const count = meetingsList.length;
        if (LOCALE === 'ar') {
          resultsCountText.textContent = `تم العثور على ${count} اجتماع`;
        } else {
          resultsCountText.textContent = `Found ${count} meetings`;
        }
      }

      if (meetingsList.length === 0) {
        cardsContainer.classList.add('d-none');
        if (noResultsMsg) noResultsMsg.classList.remove('d-none');
        if (loadMoreWrap) loadMoreWrap.classList.add('d-none');
        return;
      }

      cardsContainer.classList.remove('d-none');
      if (noResultsMsg) noResultsMsg.classList.add('d-none');

      const itemsToRender = meetingsList.slice(0, visibleLimit);

      itemsToRender.forEach(m => {
        const card = document.createElement('div');
        card.className = 'na-meeting-card';
        card.setAttribute('data-city', m.city_ar);
        card.setAttribute('data-day', m.day_ar);
        card.setAttribute('data-format', m.is_online ? 'online' : 'in_person');

        const title = LOCALE === 'ar' ? m.name : m.name_en;
        const cityName = LOCALE === 'ar' ? m.city_ar : m.city_en;
        const dayLabel = LOCALE === 'ar' ? m.day_ar : m.day_en;
        
        let formatBadgeHtml = m.is_online 
          ? `<span class="na-meeting-badge-online"><i class="bi bi-camera-video-fill"></i> ${LOCALE === 'ar' ? 'أونلاين' : 'Online'}</span>`
          : `<span class="na-meeting-badge-inperson"><i class="bi bi-building"></i> ${LOCALE === 'ar' ? 'حضوري' : 'In-Person'}</span>`;

        let distanceBadgeHtml = '';
        if (proximitySortingActive && !m.is_online && m.distance && m.distance < 9999) {
          const distKm = m.distance < 10 ? m.distance.toFixed(1) : Math.round(m.distance);
          distanceBadgeHtml = `<span class="na-distance-badge"><i class="bi bi-geo-alt-fill"></i> ${LOCALE === 'ar' ? `على بُعد ${distKm} كم` : `${distKm} km`}</span>`;
        }

        let actionBtnHtml = '';
        if (m.is_online && m.zoom_url) {
          actionBtnHtml = `
            <a href="${m.zoom_url}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary rounded-pill px-3 py-1 shadow-xs d-inline-flex align-items-center gap-1">
              <i class="bi bi-box-arrow-up-right"></i>
              <span>{{ __('messages.join_zoom') }}</span>
            </a>
          `;
        } else if (!m.is_online && m.map_url) {
          actionBtnHtml = `
            <a href="${m.map_url}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1">
              <i class="bi bi-map"></i>
              <span>{{ __('messages.directions') }}</span>
            </a>
          `;
        } else {
          actionBtnHtml = `
            <a href="{{ route('frontend.meetings') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1">
              <span>{{ __('messages.details') }}</span>
            </a>
          `;
        }

        let tagsHtml = '';
        if (m.tags && m.tags.length > 0) {
          tagsHtml = '<div class="d-flex flex-wrap gap-1 mt-2">';
          m.tags.slice(0, 3).forEach(t => {
            tagsHtml += `<span class="badge na-tag-badge">${t}</span>`;
          });
          tagsHtml += '</div>';
        }

        card.innerHTML = `
          <div>
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="d-flex align-items-center gap-1.5 flex-wrap">
                ${formatBadgeHtml}
                ${distanceBadgeHtml}
              </div>
              <span class="badge bg-light text-secondary border font-monospace small">
                ${dayLabel}
              </span>
            </div>

            <h3 class="fw-bold fs-6 text-dark mb-1 lh-sm">${title}</h3>
            
            <div class="d-flex align-items-center gap-1 text-muted small mb-2">
              <i class="bi bi-geo-alt text-primary"></i>
              <span>${cityName}</span>
            </div>

            <p class="text-secondary small mb-2 text-truncate-2" style="font-size: 0.84rem; line-height: 1.4;">
              ${m.address}
            </p>

            ${tagsHtml}
          </div>

          <div class="pt-2 border-top mt-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-1 text-dark fw-bold small font-monospace">
              <i class="bi bi-clock text-info"></i>
              <span>${m.time_fmt}</span>
            </div>
            ${actionBtnHtml}
          </div>
        `;

        cardsContainer.appendChild(card);
      });

      // Show/hide Load More
      if (loadMoreWrap) {
        if (meetingsList.length > visibleLimit) {
          loadMoreWrap.classList.remove('d-none');
          const remaining = meetingsList.length - visibleLimit;
          if (loadMoreBtnText) {
            loadMoreBtnText.textContent = LOCALE === 'ar' ? `عرض المزيد من الاجتماعات (${remaining} متبقي)` : `Load More (${remaining} remaining)`;
          }
        } else {
          loadMoreWrap.classList.add('d-none');
        }
      }
    }

    // 9. Load More Pagination Action
    window.showMoreMeetings = function() {
      visibleLimit += PAGE_CHUNK;
      applyMeetingFilters();
    };

    // 10. Bootstrap component on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
      initAutocomplete();
      applyMeetingFilters();
    });
  })();
</script>
