@php
    $direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
    $isRtl = $direction === 'rtl';
    $pageTitle = __('messages.commsmeetings');
    $pageDescription = __('messages.comms_hero_subtitle');

    // Helper for committee-specific visual branding & metadata
    $getCommitteeTheme = function($comm) use ($isRtl) {
        $name = mb_strtolower(($comm->ar_name ?? '') . ' ' . ($comm->en_name ?? ''));
        
        if (str_contains($name, 'علاقات') || str_contains($name, 'public relations') || str_contains($name, 'pr')) {
            return [
                'icon' => 'bi-megaphone-fill',
                'bg' => 'linear-gradient(135deg, #059669 0%, #10b981 100%)',
                'border' => '#10b981',
                'badge_bg' => '#d1fae5',
                'badge_color' => '#065f46',
                'glow' => 'rgba(16, 185, 129, 0.15)',
                'desc' => $isRtl 
                    ? 'مسؤولة عن نشر الوعي بزمالة المدمنين المجهولين في المجتمع، والتعامل مع وسائل الإعلام والجهات الرسمية والمهتمين بالتعافي.' 
                    : 'Responsible for public awareness, media communications, community outreach, and carrying the message to the public.',
            ];
        } elseif (str_contains($name, 'مستشفيات') || str_contains($name, 'hospitals') || str_contains($name, 'h&i')) {
            return [
                'icon' => 'bi-heart-pulse-fill',
                'bg' => 'linear-gradient(135deg, #e11d48 0%, #f43f5e 100%)',
                'border' => '#f43f5e',
                'badge_bg' => '#ffe4e6',
                'badge_color' => '#9f1239',
                'glow' => 'rgba(244, 63, 94, 0.15)',
                'desc' => $isRtl 
                    ? 'تحمل رسالة التعافي إلى المدمنين النزلاء داخل المستشفيات والمصحات ومراكز التأهيل والمؤسسات العقابية الذين لا يستطيعون حضور الاجتماعات العادية.' 
                    : 'Carries the NA message to addicts in treatment centers, hospitals, psychiatric facilities, and correctional institutions.',
            ];
        } elseif (str_contains($name, 'أدبيات') || str_contains($name, 'literature') || str_contains($name, 'lit')) {
            return [
                'icon' => 'bi-journal-bookmark-fill',
                'bg' => 'linear-gradient(135deg, #d97706 0%, #f59e0b 100%)',
                'border' => '#f59e0b',
                'badge_bg' => '#fef3c7',
                'badge_color' => '#92400e',
                'glow' => 'rgba(245, 158, 11, 0.15)',
                'desc' => $isRtl 
                    ? 'توفر وتوزع أدبيات وكتب ونشرات ومطبوعات زمالة المدمنين المجهولين وميداليات الامتناع للمجموعات والأعضاء.' 
                    : 'Maintains stock and oversees the distribution of NA fellowship-approved literature, books, pamphlets, and keytags.',
            ];
        } elseif (str_contains($name, 'دعم') || str_contains($name, 'تطوير') || str_contains($name, 'fellowship development') || str_contains($name, 'f-d')) {
            return [
                'icon' => 'bi-compass-fill',
                'bg' => 'linear-gradient(135deg, #0f766e 0%, #14b8a6 100%)',
                'border' => '#14b8a6',
                'badge_bg' => '#ccfbf1',
                'badge_color' => '#115e59',
                'glow' => 'rgba(20, 184, 166, 0.15)',
                'desc' => $isRtl 
                    ? 'تدعم المجموعات الناشئة والمحافظات البعيدة وتعمل على ورش العمل لتطوير الخدمة وحل التحديات داخل المجموعات.' 
                    : 'Fosters fellowship growth, supports isolated groups, conducts service workshops, and assists struggling meetings.',
            ];
        } elseif (str_contains($name, 'ترجمة') || str_contains($name, 'translation') || str_contains($name, 'ltc')) {
            return [
                'icon' => 'bi-translate',
                'bg' => 'linear-gradient(135deg, #0284c7 0%, #38bdf8 100%)',
                'border' => '#0284c7',
                'badge_bg' => '#e0f2fe',
                'badge_color' => '#0369a1',
                'glow' => 'rgba(2, 132, 199, 0.15)',
                'desc' => $isRtl 
                    ? 'تتولى ترجمة ومراجعة أدبيات ونشرات وكتيبات وخدمات زمالة المدمنين المجهولين العالمية إلى اللغة العربية بدقة وأمانة.' 
                    : 'Translates and reviews NA World Services literature, IP pamphlets, and service materials into Arabic.',
            ];
        } elseif (str_contains($name, 'أنشطة') || str_contains($name, 'activities') || str_contains($name, 'activity')) {
            return [
                'icon' => 'bi-balloon-heart-fill',
                'bg' => 'linear-gradient(135deg, #7c3aed 0%, #a855f7 100%)',
                'border' => '#a855f7',
                'badge_bg' => '#f3e8ff',
                'badge_color' => '#6b21a8',
                'glow' => 'rgba(168, 85, 247, 0.15)',
                'desc' => $isRtl 
                    ? 'تنظم المؤتمرات واللقاءات الرياضية والترفيهية وفعاليات الوحدة لتعزيز الروابط والتعافي المرح بين الأعضاء.' 
                    : 'Plans conventions, workshops, athletic events, and fellowship unity gatherings promoting fun in recovery.',
            ];
        } elseif (str_contains($name, 'تقنية') || str_contains($name, 'information technology') || str_contains($name, 'it') || str_contains($name, 'web')) {
            return [
                'icon' => 'bi-code-slash',
                'bg' => 'linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)',
                'border' => '#6366f1',
                'badge_bg' => '#e0e7ff',
                'badge_color' => '#3730a3',
                'glow' => 'rgba(99, 102, 241, 0.15)',
                'desc' => $isRtl 
                    ? 'تدير وتطور موقع الزمالة الرسمي، ونظام الاجتماعات، وتطبيقات الهاتف، وقواعد البيانات والخدمات السحابية.' 
                    : 'Develops and maintains the NA Egypt website, meeting locators, databases, mobile apps, and digital platforms.',
            ];
        } elseif (str_contains($name, 'إقليم') || str_contains($name, 'regional') || str_contains($name, 'rsc')) {
            return [
                'icon' => 'bi-shield-shaded',
                'bg' => 'linear-gradient(135deg, #1e293b 0%, #334155 100%)',
                'border' => '#00698f',
                'badge_bg' => '#f1f5f9',
                'badge_color' => '#0f172a',
                'glow' => 'rgba(0, 105, 143, 0.18)',
                'desc' => $isRtl 
                    ? 'لجنة خدمة إقليم مصر تجمع ممثلي جميع لجان المناطق واللجان الفرعية لتنسيق الخدمة على مستوى جمهورية مصر العربية.' 
                    : 'Egypt Regional Service Committee coordinates subcommittees and area service committees nationwide.',
            ];
        }

        return [
            'icon' => 'bi-diagram-3-fill',
            'bg' => 'linear-gradient(135deg, #00698f 0%, #0284c7 100%)',
            'border' => '#00698f',
            'badge_bg' => '#e0f2fe',
            'badge_color' => '#0369a1',
            'glow' => 'rgba(0, 105, 143, 0.15)',
            'desc' => $isRtl 
                ? 'لجنة خدمية تابعة لزمالة المدمنين المجهولين بمصر لدعم المجموعات وحمل رسالة التعافي.' 
                : 'A Narcotics Anonymous Egypt service body dedicated to carrying the message of recovery.',
        ];
    };
@endphp

<x-frontend.layout :title="$pageTitle" :description="$pageDescription">
    {{-- QRCode.js for client-side QR generation --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" integrity="sha384-3zSEDfvllQohrq0PHL1fOXJuC/jSOO34H46t6UQfobFOmxE5BpjjaIJY5F2/bMnU" crossorigin="anonymous"></script>

    <div class="comms-page-container" dir="{{ $direction }}">
        {{-- Hero Section --}}
        <div class="comms-hero mb-4">
            <div class="comms-hero-content">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-3 rounded-pill comms-hero-badge">
                    <i class="bi bi-people-fill"></i>
                    <span>{{ __('messages.commsmeetings') }}</span>
                </div>
                <h1 class="comms-hero-title mb-2 text-white">{{ __('messages.comms_hero_title') }}</h1>
                <p class="comms-hero-subtitle mb-3">{{ __('messages.comms_hero_subtitle') }}</p>

                {{-- Stats Counters --}}
                @php
                    $totalCount = $serviceCommittees->count();
                    $onlineCount = $serviceCommittees->filter(function($c) {
                        $loc = strtolower($c->location ?? '');
                        $notes = strtolower($c->notes ?? '');
                        $addrAr = strtolower($c->ar_address ?? '');
                        $addrEn = strtolower($c->en_address ?? '');
                        return str_contains($loc, 'zoom') || str_contains($loc, 'meet') || str_contains($loc, 'online') 
                            || str_contains($notes, 'أونلاين') || str_contains($notes, 'online') 
                            || str_contains($addrAr, 'أونلاين') || str_contains($addrEn, 'online');
                    })->count();
                    $inPersonCount = $totalCount - $onlineCount;
                @endphp

                <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 gap-md-3 mt-3">
                    <div class="comms-stat-pill">
                        <span class="stat-num">{{ $totalCount }}</span>
                        <span class="stat-label">{{ __('messages.total_committees_count') }}</span>
                    </div>
                    <div class="comms-stat-pill">
                        <span class="stat-num">{{ $inPersonCount }}</span>
                        <span class="stat-label">{{ __('messages.in_person_meetings') }}</span>
                    </div>
                    <div class="comms-stat-pill">
                        <span class="stat-num">{{ $onlineCount }}</span>
                        <span class="stat-label">{{ __('messages.online_meetings') }}</span>
                    </div>
                </div>

                {{-- Info Accordion Toggle --}}
                <div class="mt-4">
                    <button class="btn comms-info-toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#commsInfoCollapse" aria-expanded="false" aria-controls="commsInfoCollapse">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        <span>{{ __('messages.about_service_committees') }}</span>
                        <i class="bi bi-chevron-down ms-1 toggle-icon"></i>
                    </button>
                    <div class="collapse mt-3" id="commsInfoCollapse">
                        <div class="comms-info-card p-3 p-md-4 text-start">
                            <div class="d-flex align-items-start gap-3">
                                <div class="comms-info-icon-wrapper">
                                    <i class="bi bi-lightbulb-fill"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-primary">{{ __('messages.about_service_committees') }}</h6>
                                    <p class="mb-0 text-muted small lh-lg">
                                        {{ __('messages.about_service_committees_desc') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Search Toolbar --}}
        <div class="comms-toolbar mb-4">
            <div class="row g-3 align-items-center justify-content-between">
                {{-- Search Bar --}}
                <div class="col-12 col-md-5 col-lg-4">
                    <div class="input-group comms-search-box">
                        <span class="input-group-text bg-transparent border-0 pe-2">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="commsSearchInput" class="form-control border-0 shadow-none ps-1" 
                               placeholder="{{ __('messages.search_committees_placeholder') }}" 
                               aria-label="Search committees">
                        <button class="btn btn-link text-muted pe-3 d-none" type="button" id="clearSearchBtn" title="Clear">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>
                </div>

                {{-- Center: Format Filter Chips --}}
                <div class="col-12 col-md-7 col-lg-5">
                    <div class="d-flex flex-wrap align-items-center justify-content-md-start justify-content-lg-center gap-2">
                        <button type="button" class="btn filter-chip active" data-filter="all">
                            <i class="bi bi-grid-fill me-1"></i>
                            <span>{{ __('messages.all_committees') }}</span>
                            <span class="badge rounded-pill ms-1 bg-primary text-white count-all">{{ $totalCount }}</span>
                        </button>
                        <button type="button" class="btn filter-chip" data-filter="in-person">
                            <i class="bi bi-geo-alt-fill me-1"></i>
                            <span>{{ __('messages.in_person_meetings') }}</span>
                            <span class="badge rounded-pill ms-1 bg-light text-dark count-in-person">{{ $inPersonCount }}</span>
                        </button>
                        <button type="button" class="btn filter-chip" data-filter="online">
                            <i class="bi bi-camera-video-fill me-1"></i>
                            <span>{{ __('messages.online_meetings') }}</span>
                            <span class="badge rounded-pill ms-1 bg-light text-dark count-online">{{ $onlineCount }}</span>
                        </button>
                    </div>
                </div>

                {{-- Right: View Switcher (Grid vs Monthly Timeline) --}}
                <div class="col-12 col-lg-3">
                    <div class="d-flex align-items-center justify-content-lg-end">
                        <div class="btn-group view-switcher-group p-1 rounded-pill bg-light border w-100 w-lg-auto" role="group">
                            <button type="button" class="btn btn-sm view-switch-btn active rounded-pill px-3 py-1.5" id="viewBtnGrid" data-view="grid">
                                <i class="bi bi-grid-fill me-1"></i>
                                <span>{{ __('messages.grid_view') }}</span>
                            </button>
                            <button type="button" class="btn btn-sm view-switch-btn rounded-pill px-3 py-1.5" id="viewBtnTimeline" data-view="timeline">
                                <i class="bi bi-clock-history me-1"></i>
                                <span>{{ __('messages.timeline_view') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Live Result Count Bar --}}
            <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top comms-results-bar">
                <div class="text-muted small">
                    <span id="resultsCount">{{ $totalCount }}</span> / {{ $totalCount }} {{ __('messages.total_committees_count') }}
                </div>
            </div>
        </div>

        {{-- VIEW 1: Committee Cards Grid --}}
        <div id="commsGridViewContainer">
            <div class="row g-4" id="committeeCardsGrid">
                @foreach ($serviceCommittees as $comm)
                    @php
                        $name = $isRtl ? $comm->ar_name : $comm->en_name;
                        $subName = $isRtl ? $comm->en_name : $comm->ar_name;
                        $address = $isRtl ? ($comm->ar_address ?: $comm->en_address) : ($comm->en_address ?: $comm->ar_address);
                        $locationUrl = trim($comm->location ?? '');
                        $notes = trim($comm->notes ?? '');
                        $email = trim($comm->email ?? '');
                        $chairmanName = trim($comm->chairman_name ?? '');
                        $chairmanPhone = trim($comm->chairman_phone ?? '');

                        // Determine format (Online vs In-Person)
                        $isOnline = false;
                        if (str_contains(strtolower($locationUrl), 'zoom.us') || 
                            str_contains(strtolower($locationUrl), 'meet.google') || 
                            str_contains(strtolower($locationUrl), 'teams.live') || 
                            strtolower($locationUrl) === 'online' ||
                            str_contains(mb_strtolower($notes), 'أونلاين') || 
                            str_contains(strtolower($notes), 'online') ||
                            str_contains(mb_strtolower($address ?? ''), 'أونلاين') || 
                            str_contains(strtolower($address ?? ''), 'online')) {
                            $isOnline = true;
                        }

                        // Determine location link type
                        $isZoomUrl = str_contains(strtolower($locationUrl), 'zoom.us');
                        $isGenericUrl = filter_var($locationUrl, FILTER_VALIDATE_URL) !== false;
                        
                        $mapUrl = null;
                        if ($isGenericUrl && !$isZoomUrl) {
                            $mapUrl = $locationUrl;
                        } elseif (!$isOnline && !empty($address)) {
                            $mapUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($address . ' Egypt');
                        }

                        // Theme
                        $theme = $getCommitteeTheme($comm);

                        // Logo asset
                        $logoUrl = ($comm->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($comm->logo)) 
                            ? asset('storage/' . $comm->logo) 
                            : null;
                    @endphp

                    <div class="col-12 col-md-6 col-lg-4 committee-card-col" 
                         data-format="{{ $isOnline ? 'online' : 'in-person' }}"
                         data-search="{{ mb_strtolower($name . ' ' . $subName . ' ' . $notes . ' ' . $address . ' ' . $email) }}">
                        <div class="card h-100 committee-card {{ $isOnline ? 'card-online' : 'card-inperson' }}" 
                             style="--committee-border: {{ $theme['border'] }}; --committee-glow: {{ $theme['glow'] }};">
                            
                            {{-- Top Accent Indicator --}}
                            <div class="committee-card-accent-bar" style="background: {{ $theme['bg'] }};"></div>

                            {{-- Card Header (Clickable to open profile drawer) --}}
                            <div class="card-header bg-transparent border-0 p-3 pb-0">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div class="d-flex align-items-center gap-3 cursor-pointer btn-open-drawer" 
                                         role="button"
                                         tabindex="0"
                                         data-id="{{ $comm->id }}"
                                         data-name="{{ $name }}"
                                         data-subname="{{ $subName }}"
                                         data-format="{{ $isOnline ? 'online' : 'in-person' }}"
                                         data-schedule="{{ $notes }}"
                                         data-address="{{ $address }}"
                                         data-location-url="{{ $locationUrl }}"
                                         data-map-url="{{ $mapUrl }}"
                                         data-email="{{ $email }}"
                                         data-chairman-name="{{ $chairmanName }}"
                                         data-chairman-phone="{{ $chairmanPhone }}"
                                         data-desc="{{ $theme['desc'] }}"
                                         data-icon="{{ $theme['icon'] }}"
                                         data-bg="{{ $theme['bg'] }}"
                                         data-border="{{ $theme['border'] }}">
                                        
                                        <div class="committee-avatar-wrapper" style="border-color: {{ $theme['border'] }}40;">
                                            @if($logoUrl)
                                                <img src="{{ $logoUrl }}" alt="{{ $name }}" class="committee-logo-img" decoding="async" onerror="this.parentElement.innerHTML='<div class=\'committee-avatar-fallback\' style=\'background: {{ $theme['bg'] }};\'><i class=\'bi {{ $theme['icon'] }}\'></i></div>'">
                                            @else
                                                <div class="committee-avatar-fallback" style="background: {{ $theme['bg'] }};">
                                                    <i class="bi {{ $theme['icon'] }}"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h5 class="committee-title mb-1 hover-primary">{{ $name }}</h5>
                                            @if($subName && $subName !== $name)
                                                <div class="committee-subtitle text-muted small">{{ $subName }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="badge {{ $isOnline ? 'badge-online' : 'badge-inperson' }} text-nowrap shadow-xs">
                                        <i class="bi {{ $isOnline ? 'bi-camera-video-fill' : 'bi-geo-alt-fill' }} me-1"></i>
                                        {{ $isOnline ? __('messages.service_badge_online') : __('messages.service_badge_in_person') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Card Body --}}
                            <div class="card-body p-3">
                                {{-- Meeting Schedule Box --}}
                                <div class="schedule-box mb-3 p-2.5 rounded-3">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="schedule-icon-pill" style="background: {{ $theme['bg'] }};">
                                            <i class="bi bi-calendar2-week-fill text-white"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="schedule-label">{{ __('messages.meeting_schedule') }}</div>
                                            <div class="schedule-value fw-bold">
                                                {{ $notes ?: ($isRtl ? 'حسب جدول أعمال اللجنة' : 'As scheduled by committee') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Location / Venue Box --}}
                                @if(!empty($address) || !empty($locationUrl))
                                    <div class="detail-item mb-2.5 d-flex align-items-start gap-2 p-2 rounded-2 bg-light border">
                                        <i class="bi {{ $isOnline ? 'bi-globe2 text-primary' : 'bi-geo-alt-fill text-danger' }} detail-icon mt-1"></i>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="detail-label text-muted small">{{ __('messages.meeting_location') }}</div>
                                            <div class="detail-text fw-medium text-truncate" title="{{ $address ?: $locationUrl }}">
                                                @if($isOnline && ($address === 'أونلاين' || $address === 'Online' || empty($address)))
                                                    <span class="text-primary fw-semibold">{{ __('messages.online_meetings') }} (Zoom / Virtual)</span>
                                                @else
                                                    {{ $address ?: $locationUrl }}
                                                @endif
                                            </div>
                                        </div>
                                        @if(!$isOnline && !empty($address))
                                            <button type="button" class="btn btn-sm btn-link p-0 text-muted btn-copy-address" 
                                                    data-address="{{ $address }}" 
                                                    title="{{ __('messages.copy_meeting_details') }}">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endif

                                {{-- Email Box --}}
                                @if(!empty($email))
                                    <div class="detail-item d-flex align-items-center justify-content-between gap-2 p-2 rounded-2 bg-light border">
                                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                                            <i class="bi bi-envelope-fill text-muted"></i>
                                            <span class="text-truncate small font-monospace" style="direction: ltr;">{{ $email }}</span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-link p-0 text-primary btn-copy-email" 
                                                data-email="{{ $email }}" 
                                                title="{{ __('messages.copy_email') }}">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            {{-- Card Footer Single-Row Actions --}}
                            <div class="card-footer bg-transparent border-0 p-3 pt-0 mt-auto">
                                <div class="comms-card-actions">
                                    {{-- Primary Action Pill --}}
                                    @if($isOnline)
                                        @if($isZoomUrl || $isGenericUrl)
                                            <a href="{{ $locationUrl }}" target="_blank" rel="noopener noreferrer" 
                                               class="btn btn-primary comms-action-pill btn-zoom flex-grow-1 shadow-xs" 
                                               title="{{ __('messages.join_zoom_meeting') }}">
                                                <i class="bi bi-camera-video-fill"></i>
                                                <span>{{ __('messages.btn_zoom') }}</span>
                                            </a>
                                        @else
                                            <a href="mailto:{{ $email }}" 
                                               class="btn btn-primary comms-action-pill btn-zoom flex-grow-1 shadow-xs" 
                                               title="{{ __('messages.committee_contact_email') }}">
                                                <i class="bi bi-envelope-fill"></i>
                                                <span>{{ __('messages.btn_contact') }}</span>
                                            </a>
                                        @endif
                                    @elseif($mapUrl)
                                        <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" 
                                           class="btn btn-outline-primary comms-action-pill flex-grow-1 shadow-xs" 
                                           title="{{ __('messages.open_map_location') }}">
                                            <i class="bi bi-geo-alt-fill text-danger"></i>
                                            <span>{{ __('messages.btn_map') }}</span>
                                        </a>
                                    @else
                                        <button type="button" 
                                                class="btn btn-outline-primary comms-action-pill flex-grow-1 shadow-xs btn-open-drawer" 
                                                data-id="{{ $comm->id }}"
                                                data-name="{{ $name }}"
                                                data-subname="{{ $subName }}"
                                                data-format="{{ $isOnline ? 'online' : 'in-person' }}"
                                                data-schedule="{{ $notes }}"
                                                data-address="{{ $address }}"
                                                data-location-url="{{ $locationUrl }}"
                                                data-map-url="{{ $mapUrl }}"
                                                data-email="{{ $email }}"
                                                data-chairman-name="{{ $chairmanName }}"
                                                data-chairman-phone="{{ $chairmanPhone }}"
                                                data-desc="{{ $theme['desc'] }}"
                                                data-icon="{{ $theme['icon'] }}"
                                                data-bg="{{ $theme['bg'] }}"
                                                data-border="{{ $theme['border'] }}"
                                                title="{{ __('messages.view_committee_profile') }}">
                                            <i class="bi bi-info-circle-fill"></i>
                                            <span>{{ __('messages.btn_details') }}</span>
                                        </button>
                                    @endif

                                    {{-- Icon 1: QR Code --}}
                                    @if(($isOnline && ($isZoomUrl || $isGenericUrl)) || (!$isOnline && $mapUrl))
                                        <button type="button" class="btn btn-light border comms-icon-btn btn-show-qr shadow-xs" 
                                                data-qr-type="{{ $isOnline ? 'zoom' : 'map' }}"
                                                data-url="{{ $isOnline ? $locationUrl : $mapUrl }}" 
                                                data-title="{{ $name }}"
                                                title="{{ $isOnline ? __('messages.zoom_meeting_qr') : __('messages.map_location_qr') }}">
                                            <i class="bi bi-qr-code"></i>
                                        </button>
                                    @endif

                                    {{-- Icon 2: Details Drawer --}}
                                    <button type="button" class="btn btn-light border comms-icon-btn btn-open-drawer shadow-xs" 
                                            data-id="{{ $comm->id }}"
                                            data-name="{{ $name }}"
                                            data-subname="{{ $subName }}"
                                            data-format="{{ $isOnline ? 'online' : 'in-person' }}"
                                            data-schedule="{{ $notes }}"
                                            data-address="{{ $address }}"
                                            data-location-url="{{ $locationUrl }}"
                                            data-map-url="{{ $mapUrl }}"
                                            data-email="{{ $email }}"
                                            data-chairman-name="{{ $chairmanName }}"
                                            data-chairman-phone="{{ $chairmanPhone }}"
                                            data-desc="{{ $theme['desc'] }}"
                                            data-icon="{{ $theme['icon'] }}"
                                            data-bg="{{ $theme['bg'] }}"
                                            data-border="{{ $theme['border'] }}"
                                            title="{{ __('messages.view_committee_profile') }}">
                                        <i class="bi bi-info-circle text-primary"></i>
                                    </button>

                                    {{-- Icon 3: Share / Copy Details --}}
                                    <button type="button" class="btn btn-light border comms-icon-btn btn-copy-details shadow-xs" 
                                            data-title="{{ $name }}" 
                                            data-schedule="{{ $notes }}" 
                                            data-location="{{ $address ?: $locationUrl }}" 
                                            data-email="{{ $email }}"
                                            data-url="{{ $locationUrl }}"
                                            title="{{ __('messages.copy_meeting_details') }}">
                                        <i class="bi bi-share text-muted"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- VIEW 2: Monthly Schedule Timeline Container (Hidden by default) --}}
        <div id="commsTimelineViewContainer" class="d-none">
            <div class="timeline-header-card p-3 p-md-4 rounded-4 mb-4 bg-white border shadow-xs">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h4 class="fw-bold mb-1 text-primary d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-range-fill"></i>
                            <span>{{ __('messages.monthly_schedule_timeline') }}</span>
                        </h4>
                        <p class="text-muted small mb-0">{{ __('messages.timeline_subtitle') }}</p>
                    </div>

                    {{-- Quick Jump Pills --}}
                    <div class="d-flex flex-wrap align-items-center gap-1.5">
                        <span class="text-muted small me-1 d-none d-sm-inline">{{ __('messages.quick_jump_week') }}</span>
                        <a href="#weekSection1" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 text-nowrap">W1</a>
                        <a href="#weekSection2" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 text-nowrap">W2</a>
                        <a href="#weekSection3" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 text-nowrap">W3</a>
                        <a href="#weekSection4" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 text-nowrap">W4</a>
                    </div>
                </div>
            </div>

            <div class="timeline-weeks-wrapper d-flex flex-column gap-4">
                {{-- WEEK 1 --}}
                <div class="timeline-week-block" id="weekSection1">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="timeline-week-pill px-3 py-1 rounded-pill fw-bold bg-primary text-white">
                            <i class="bi bi-1-circle-fill me-1"></i> {{ __('messages.week_1') }}
                        </span>
                        <div class="flex-grow-1 border-top"></div>
                    </div>

                    <div class="row g-3">
                        {{-- LTC (Translation) --}}
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="timeline-event-card p-3 rounded-3 bg-white border h-100 shadow-xs">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge rounded-pill bg-light text-primary border font-monospace">{{ $isRtl ? 'أول سبت • ٨:٠٠ م' : '1st Sat • 8:00 PM' }}</span>
                                    <span class="badge badge-online">{{ $isRtl ? 'أونلاين' : 'Online' }}</span>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $isRtl ? 'لجنة الترجمة' : 'Local Translation' }}</h6>
                                <p class="text-muted small mb-2"><i class="bi bi-camera-video me-1"></i> Zoom</p>
                                <a href="https://us06web.zoom.us/j/3991041880?pwd=scgJbdf0PtFib69OIy4VJL5i3Bhaej.1&omn=94722422307" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                    <i class="bi bi-camera-video-fill me-1"></i> {{ __('messages.join_zoom_meeting') }}
                                </a>
                            </div>
                        </div>

                        {{-- Fellowship Development --}}
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="timeline-event-card p-3 rounded-3 bg-white border h-100 shadow-xs">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge rounded-pill bg-light text-primary border font-monospace">{{ $isRtl ? 'أول سبت • ٧:٣٠ م' : '1st Sat • 7:30 PM' }}</span>
                                    <span class="badge badge-inperson">{{ $isRtl ? 'المقطم' : 'Mokattam' }}</span>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $isRtl ? 'لجنة الدعم والتطوير' : 'Fellowship Development' }}</h6>
                                <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i> {{ $isRtl ? 'المقطم' : 'Mokattam' }}</p>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill w-100 btn-copy-details" data-title="{{ $isRtl ? 'لجنة الدعم والتطوير' : 'Fellowship Development' }}" data-schedule="أول و ثالث سبت من كل شهر الساعة ٧:٣٠ م" data-location="المقطم" data-email="f-d@naegypt.org">
                                    <i class="bi bi-share me-1"></i> {{ __('messages.copy_meeting_details') }}
                                </button>
                            </div>
                        </div>

                        {{-- Public Relations (PR) --}}
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="timeline-event-card p-3 rounded-3 bg-white border h-100 shadow-xs">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge rounded-pill bg-light text-primary border font-monospace">{{ $isRtl ? 'أول أحد • ٧:٣٠ م' : '1st Sun • 7:30 PM' }}</span>
                                    <span class="badge badge-inperson">{{ $isRtl ? 'المقطم' : 'Mokattam' }}</span>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $isRtl ? 'لجنة العلاقات العامة' : 'Public Relations' }}</h6>
                                <p class="text-muted small mb-2 text-truncate"><i class="bi bi-geo-alt me-1"></i> {{ $isRtl ? 'جمعية المدمنين المجهولين - المقطم' : 'NA Assoc. - Mokattam' }}</p>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill w-100 btn-copy-details" data-title="{{ $isRtl ? 'لجنة العلاقات العامة' : 'Public Relations' }}" data-schedule="أول و ثالث أحد من كل شهر الساعة ٧:٣٠ م" data-location="جمعية المدمنين المجهولين - المقطم" data-email="pr@naegypt.org">
                                    <i class="bi bi-share me-1"></i> {{ __('messages.copy_meeting_details') }}
                                </button>
                            </div>
                        </div>

                        {{-- Literature Committee --}}
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="timeline-event-card p-3 rounded-3 bg-white border h-100 shadow-xs">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge rounded-pill bg-light text-primary border font-monospace">{{ $isRtl ? 'أول إثنين • ٨:٠٠ م' : '1st Mon • 8:00 PM' }}</span>
                                    <span class="badge badge-inperson">{{ $isRtl ? 'المقطم' : 'Mokattam' }}</span>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $isRtl ? 'لجنة الأدبيات' : 'Literature Committee' }}</h6>
                                <p class="text-muted small mb-2 text-truncate"><i class="bi bi-geo-alt me-1"></i> {{ $isRtl ? 'شارع الشهيد كريم بنونه - المقطم' : 'Karem Banouna St. - Mokattam' }}</p>
                                <a href="https://maps.app.goo.gl/x4P5NapoS87MMe6v9" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                    <i class="bi bi-map me-1"></i> {{ __('messages.open_map_location') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- WEEK 2 --}}
                <div class="timeline-week-block" id="weekSection2">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="timeline-week-pill px-3 py-1 rounded-pill fw-bold bg-secondary text-white">
                            <i class="bi bi-2-circle-fill me-1"></i> {{ __('messages.week_2') }}
                        </span>
                        <div class="flex-grow-1 border-top"></div>
                    </div>

                    <div class="row g-3">
                        {{-- Activities Committee --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="timeline-event-card p-3 rounded-3 bg-white border h-100 shadow-xs">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge rounded-pill bg-light text-primary border font-monospace">{{ $isRtl ? 'ثاني سبت • ٨:٠٠ م' : '2nd Sat • 8:00 PM' }}</span>
                                    <span class="badge badge-inperson">{{ $isRtl ? 'المقطم' : 'Mokattam' }}</span>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $isRtl ? 'لجنة الأنشطة' : 'Activities Committee' }}</h6>
                                <p class="text-muted small mb-2 text-truncate"><i class="bi bi-geo-alt me-1"></i> {{ $isRtl ? 'شارع كريم بنونه - المقطم' : 'Mokattam' }}</p>
                                <a href="https://maps.google.com/?q=30.001537,31.316307" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                    <i class="bi bi-map me-1"></i> {{ __('messages.open_map_location') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- WEEK 3 --}}
                <div class="timeline-week-block" id="weekSection3">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="timeline-week-pill px-3 py-1 rounded-pill fw-bold bg-info text-white">
                            <i class="bi bi-3-circle-fill me-1"></i> {{ __('messages.week_3') }}
                        </span>
                        <div class="flex-grow-1 border-top"></div>
                    </div>

                    <div class="row g-3">
                        {{-- Fellowship Development --}}
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="timeline-event-card p-3 rounded-3 bg-white border h-100 shadow-xs">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge rounded-pill bg-light text-primary border font-monospace">{{ $isRtl ? 'ثالث سبت • ٧:٣٠ م' : '3rd Sat • 7:30 PM' }}</span>
                                    <span class="badge badge-inperson">{{ $isRtl ? 'المقطم' : 'Mokattam' }}</span>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $isRtl ? 'لجنة الدعم والتطوير' : 'Fellowship Development' }}</h6>
                                <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i> {{ $isRtl ? 'المقطم' : 'Mokattam' }}</p>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill w-100 btn-copy-details" data-title="{{ $isRtl ? 'لجنة الدعم والتطوير' : 'Fellowship Development' }}" data-schedule="أول و ثالث سبت من كل شهر الساعة ٧:٣٠ م" data-location="المقطم" data-email="f-d@naegypt.org">
                                    <i class="bi bi-share me-1"></i> {{ __('messages.copy_meeting_details') }}
                                </button>
                            </div>
                        </div>

                        {{-- Public Relations (PR) --}}
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="timeline-event-card p-3 rounded-3 bg-white border h-100 shadow-xs">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge rounded-pill bg-light text-primary border font-monospace">{{ $isRtl ? 'ثالث أحد • ٧:٣٠ م' : '3rd Sun • 7:30 PM' }}</span>
                                    <span class="badge badge-inperson">{{ $isRtl ? 'المقطم' : 'Mokattam' }}</span>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $isRtl ? 'لجنة العلاقات العامة' : 'Public Relations' }}</h6>
                                <p class="text-muted small mb-2 text-truncate"><i class="bi bi-geo-alt me-1"></i> {{ $isRtl ? 'جمعية المدمنين المجهولين - المقطم' : 'NA Assoc. - Mokattam' }}</p>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill w-100 btn-copy-details" data-title="{{ $isRtl ? 'لجنة العلاقات العامة' : 'Public Relations' }}" data-schedule="أول و ثالث أحد من كل شهر الساعة ٧:٣٠ م" data-location="جمعية المدمنين المجهولين - المقطم" data-email="pr@naegypt.org">
                                    <i class="bi bi-share me-1"></i> {{ __('messages.copy_meeting_details') }}
                                </button>
                            </div>
                        </div>

                        {{-- Hospitals & Institutions (H&I) --}}
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="timeline-event-card p-3 rounded-3 bg-white border h-100 shadow-xs">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge rounded-pill bg-light text-danger border font-monospace">{{ $isRtl ? 'ثالث جمعة (فردي) • ٨:٠٠ م' : '3rd Fri (Odd) • 8:00 PM' }}</span>
                                    <span class="badge badge-inperson">{{ $isRtl ? 'وسط البلد' : 'Downtown' }}</span>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $isRtl ? 'لجنة المستشفيات والمؤسسات' : 'Hospitals & Institutions' }}</h6>
                                <p class="text-muted small mb-2 text-truncate"><i class="bi bi-geo-alt me-1"></i> {{ $isRtl ? 'كاريتاس وسط البلد' : 'Downtown Karitas' }}</p>
                                <a href="https://maps.app.goo.gl/R926wHhjT45gZf9R7" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                    <i class="bi bi-map me-1"></i> {{ __('messages.open_map_location') }}
                                </a>
                            </div>
                        </div>

                        {{-- Egypt RSC --}}
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="timeline-event-card p-3 rounded-3 bg-white border h-100 shadow-xs">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge rounded-pill bg-light text-dark border font-monospace">{{ $isRtl ? 'ثالث جمعة (زوجي) • ٣:٠٠ ظ' : '3rd Fri (Even) • 3:00 PM' }}</span>
                                    <span class="badge badge-inperson">{{ $isRtl ? 'مدينة نصر' : 'Nasr City' }}</span>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $isRtl ? 'لجنة خدمة إقليم مصر' : 'Egypt RSC' }}</h6>
                                <p class="text-muted small mb-2 text-truncate"><i class="bi bi-geo-alt me-1"></i> {{ $isRtl ? 'الجامعة العمالية' : 'Workers University' }}</p>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill w-100 btn-copy-details" data-title="{{ $isRtl ? 'لجنة خدمة إقليم مصر' : 'Egypt RSC' }}" data-schedule="ثالث جمعة من الشهر الزوجي الساعة ٣ ظهرًا" data-location="الجامعة العمالية" data-email="RSC@naegypt.org">
                                    <i class="bi bi-share me-1"></i> {{ __('messages.copy_meeting_details') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- WEEK 4 & LAST FRIDAY --}}
                <div class="timeline-week-block" id="weekSection4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="timeline-week-pill px-3 py-1 rounded-pill fw-bold bg-dark text-white">
                            <i class="bi bi-4-circle-fill me-1"></i> {{ __('messages.week_4') }}
                        </span>
                        <div class="flex-grow-1 border-top"></div>
                    </div>

                    <div class="row g-3">
                        {{-- Activities Committee --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="timeline-event-card p-3 rounded-3 bg-white border h-100 shadow-xs">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge rounded-pill bg-light text-primary border font-monospace">{{ $isRtl ? 'رابع سبت • ٨:٠٠ م' : '4th Sat • 8:00 PM' }}</span>
                                    <span class="badge badge-inperson">{{ $isRtl ? 'المقطم' : 'Mokattam' }}</span>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $isRtl ? 'لجنة الأنشطة' : 'Activities Committee' }}</h6>
                                <p class="text-muted small mb-2 text-truncate"><i class="bi bi-geo-alt me-1"></i> {{ $isRtl ? 'شارع كريم بنونه - المقطم' : 'Mokattam' }}</p>
                                <a href="https://maps.google.com/?q=30.001537,31.316307" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                    <i class="bi bi-map me-1"></i> {{ __('messages.open_map_location') }}
                                </a>
                            </div>
                        </div>

                        {{-- Literature Distribution --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="timeline-event-card p-3 rounded-3 bg-white border h-100 shadow-xs">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge rounded-pill bg-light text-warning border font-monospace">{{ $isRtl ? 'آخر جمعة • ٦:٣٠ - ٨:٠٠ م' : 'Last Fri • 6:30 - 8:00 PM' }}</span>
                                    <span class="badge badge-inperson">{{ $isRtl ? 'المقطم' : 'Mokattam' }}</span>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $isRtl ? 'اجتماع توزيع الأدبيات' : 'Literature Distribution' }}</h6>
                                <p class="text-muted small mb-2 text-truncate"><i class="bi bi-box-seam me-1"></i> {{ $isRtl ? 'توزيع الأدبيات والميداليات' : 'Literature & Keytags Distribution' }}</p>
                                <a href="https://maps.app.goo.gl/x4P5NapoS87MMe6v9" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                    <i class="bi bi-map me-1"></i> {{ __('messages.open_map_location') }}
                                </a>
                            </div>
                        </div>

                        {{-- IT Workgroup (Continuous / Online) --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="timeline-event-card p-3 rounded-3 bg-white border h-100 shadow-xs">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge rounded-pill bg-light text-primary border font-monospace">{{ $isRtl ? 'مستمر • أونلاين' : 'Continuous • Online' }}</span>
                                    <span class="badge badge-online">{{ $isRtl ? 'أونلاين' : 'Online' }}</span>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $isRtl ? 'مجموعة تقنية المعلومات' : 'IT Workgroup' }}</h6>
                                <p class="text-muted small mb-2 text-truncate"><i class="bi bi-globe me-1"></i> web@naegypt.org</p>
                                <a href="mailto:web@naegypt.org" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                    <i class="bi bi-envelope me-1"></i> {{ __('messages.contact') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Empty State (Search / Filter not found) --}}
        <div id="noResultsState" class="comms-empty-state d-none my-5 p-5 text-center rounded-4">
            <div class="empty-state-icon mb-3">
                <i class="bi bi-search"></i>
            </div>
            <h4 class="fw-bold mb-2">{{ __('messages.no_committees_found') }}</h4>
            <p class="text-muted mb-4">{{ __('messages.try_different_search') }}</p>
            <button type="button" id="resetFiltersBtn" class="btn btn-primary px-4 py-2 rounded-pill">
                <i class="bi bi-arrow-counterclockwise me-1"></i>
                {{ __('messages.reset_filters') }}
            </button>
        </div>
    </div>

    {{-- Slide-over Committee Profile Offcanvas Drawer --}}
    <div id="commsDrawerBackdrop" class="comms-drawer-backdrop d-none">
        <div id="commsDrawerPanel" class="comms-drawer-panel" dir="{{ $direction }}">
            <div class="comms-drawer-header p-4 pb-3 border-bottom d-flex align-items-start justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div id="drawerAvatar" class="committee-avatar-wrapper shadow-xs">
                        <i id="drawerIcon" class="bi bi-diagram-3-fill fs-4 text-white"></i>
                    </div>
                    <div>
                        <h4 id="drawerTitle" class="fw-bold mb-0 text-dark"></h4>
                        <div id="drawerSubtitle" class="text-muted small"></div>
                    </div>
                </div>
                <button type="button" class="btn-close" id="closeDrawerBtn" aria-label="Close"></button>
            </div>

            <div class="comms-drawer-body p-4 overflow-y-auto" style="max-height: calc(100vh - 180px);">
                {{-- Format Badge --}}
                <div class="mb-3">
                    <span id="drawerFormatBadge" class="badge px-3 py-1.5 rounded-pill fw-semibold shadow-xs"></span>
                </div>

                {{-- Schedule Box --}}
                <div class="schedule-box mb-3 p-3 rounded-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-calendar2-week-fill text-primary fs-5 mt-0.5"></i>
                        <div>
                            <div class="schedule-label">{{ __('messages.meeting_schedule') }}</div>
                            <div id="drawerSchedule" class="schedule-value fw-bold fs-6"></div>
                        </div>
                    </div>
                </div>

                {{-- Location / Venue Box --}}
                <div class="detail-item mb-3 p-3 rounded-3 bg-light border">
                    <div class="d-flex align-items-start gap-2">
                        <i id="drawerLocationIcon" class="bi bi-geo-alt-fill text-danger fs-5 mt-0.5"></i>
                        <div class="flex-grow-1">
                            <div class="detail-label text-muted small">{{ __('messages.meeting_location') }}</div>
                            <div id="drawerAddress" class="detail-text fw-medium"></div>
                        </div>
                    </div>
                </div>

                {{-- Committee Purpose / Mission --}}
                <div class="mb-3 p-3 rounded-3 bg-light border">
                    <h6 class="fw-bold text-primary mb-1 d-flex align-items-center gap-1.5">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>{{ __('messages.committee_purpose') }}</span>
                    </h6>
                    <p id="drawerDesc" class="text-muted small mb-0 lh-base"></p>
                </div>

                {{-- Chairperson / Trusted Servant info (if available) --}}
                <div id="drawerChairmanBox" class="mb-3 p-3 rounded-3 bg-light border d-none">
                    <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-1.5">
                        <i class="bi bi-person-badge-fill text-primary"></i>
                        <span>{{ __('messages.chairperson') }}</span>
                    </h6>
                    <div class="d-flex align-items-center justify-content-between">
                        <span id="drawerChairmanName" class="fw-medium text-secondary"></span>
                        <a id="drawerChairmanPhoneLink" href="#" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1">
                            <i class="bi bi-telephone-fill me-1"></i>
                            <span id="drawerChairmanPhone"></span>
                        </a>
                    </div>
                </div>

                {{-- Official Email Box --}}
                <div class="detail-item d-flex align-items-center justify-content-between gap-2 p-3 rounded-3 bg-light border">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <i class="bi bi-envelope-fill text-primary fs-5"></i>
                        <div>
                            <div class="text-muted small" style="font-size: 0.72rem;">{{ __('messages.committee_contact_email') }}</div>
                            <span id="drawerEmailText" class="text-truncate fw-medium font-monospace" style="direction: ltr;"></span>
                        </div>
                    </div>
                    <button type="button" id="drawerCopyEmailBtn" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="{{ __('messages.copy_email') }}">
                        <i class="bi bi-clipboard me-1"></i>
                        <span>{{ __('messages.copy_email') }}</span>
                    </button>
                </div>
            </div>

            {{-- Sticky Drawer Footer Actions --}}
            <div class="comms-drawer-footer p-3 px-4 border-top bg-white d-flex flex-column gap-2">
                <div class="d-flex gap-2">
                    <a id="drawerPrimaryActionBtn" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-primary comms-action-btn flex-grow-1 shadow-sm">
                        <i id="drawerPrimaryActionIcon" class="bi bi-camera-video-fill me-1"></i>
                        <span id="drawerPrimaryActionLabel"></span>
                    </a>
                    <button type="button" id="drawerQrBtn" class="btn btn-outline-primary comms-action-btn px-3 shadow-sm" title="QR Code">
                        <i class="bi bi-qr-code"></i>
                    </button>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" id="drawerCopyDetailsBtn" class="btn btn-light border comms-subaction-btn flex-grow-1">
                        <i class="bi bi-share-fill me-1 text-muted"></i>
                        <span>{{ __('messages.copy_meeting_details') }}</span>
                    </button>
                    <button type="button" id="drawerCalendarBtn" class="btn btn-light border comms-subaction-btn">
                        <i class="bi bi-calendar-plus text-primary me-1"></i>
                        <span>{{ __('messages.add_to_calendar') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Unified Smart QR Modal (Zoom & Location Map) --}}
    <div id="smartQrModal" class="comms-modal-backdrop d-none" tabindex="-1" role="dialog" aria-modal="true">
        <div class="comms-modal-card">
            <button type="button" class="comms-modal-close" id="closeSmartQrModalBtn" aria-label="Close">&times;</button>
            
            {{-- Modal Header Badge --}}
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <span id="smartQrModalBadge" class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1 shadow-xs">
                    <i id="smartQrModalIcon" class="bi bi-camera-video-fill"></i>
                    <span id="smartQrModalTitle">{{ __('messages.zoom_meeting_qr') }}</span>
                </span>
            </div>
            
            <p id="smartQrModalSubtitle" class="text-muted small text-center mb-3">{{ __('messages.scan_to_join') }}</p>
            
            {{-- QR Canvas --}}
            <div class="qr-canvas-wrapper p-3 bg-white border rounded-3 mb-3 d-flex align-items-center justify-content-center shadow-xs" style="min-height: 220px;">
                <div id="smartQrCanvasContainer"></div>
            </div>

            {{-- Link Display Box --}}
            <div class="p-2 px-3 rounded-3 bg-light border mb-3 text-truncate d-flex align-items-center justify-content-between gap-2" style="font-size: 0.82rem; text-align: start;">
                <span id="smartQrUrlText" class="text-break text-truncate text-secondary font-monospace" style="direction: ltr;"></span>
                <button type="button" id="smartQrModalCopyIcon" class="btn btn-link btn-sm p-0 text-decoration-none text-primary" title="{{ __('messages.copy_zoom_link') }}">
                    <i class="bi bi-clipboard"></i>
                </button>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex flex-column gap-2">
                <a id="smartQrActionBtn" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-primary rounded-pill py-2 fw-bold d-inline-flex align-items-center justify-content-center gap-1 shadow-sm">
                    <i id="smartQrActionIcon" class="bi bi-box-arrow-up-right"></i>
                    <span id="smartQrActionLabel">{{ __('messages.join_zoom_meeting') }}</span>
                </a>

                <div class="d-flex gap-2">
                    <button type="button" id="smartQrModalCopyBtn" class="btn btn-outline-secondary rounded-pill py-2 flex-grow-1 fw-semibold d-inline-flex align-items-center justify-content-center gap-1" style="font-size: 0.85rem;">
                        <i class="bi bi-clipboard"></i>
                        <span id="smartQrCopyLabel">{{ __('messages.copy_zoom_link') }}</span>
                    </button>

                    <button type="button" id="smartQrModalDownloadBtn" class="btn btn-outline-primary rounded-pill py-2 flex-grow-1 fw-semibold d-inline-flex align-items-center justify-content-center gap-1" style="font-size: 0.85rem;">
                        <i class="bi bi-download"></i>
                        <span>{{ __('messages.download_zoom_qr') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification for Copy Actions --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 999999;">
        <div id="copyToast" class="toast align-items-center text-white bg-dark border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                    <span id="toastMessage">{{ __('messages.copied') }}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    {{-- Custom Page Styles --}}
    <style>
        .comms-page-container {
            font-family: inherit;
        }

        /* Hero Banner */
        .comms-hero {
            background: linear-gradient(135deg, #004d6b 0%, #00698f 60%, #0284c7 100%);
            color: #ffffff;
            border-radius: 20px;
            padding: 2.5rem 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 105, 143, 0.15);
        }
        .comms-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -20%;
            width: 140%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 10%, transparent 60%);
            pointer-events: none;
        }
        .comms-hero-content {
            position: relative;
            z-index: 2;
            max-width: 850px;
            margin: 0 auto;
        }
        .comms-hero-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #e0f2fe;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .comms-hero-title {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.3;
        }
        .comms-hero-subtitle {
            font-size: 1.05rem;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
        }
        .comms-stat-pill {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 9999px;
            padding: 0.4rem 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: #ffffff;
        }
        .comms-stat-pill .stat-num {
            font-weight: 800;
            font-size: 1.1rem;
            color: #bae6fd;
        }
        .comms-info-toggle-btn {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #ffffff;
            font-size: 0.9rem;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            border-radius: 9999px;
            transition: all 0.2s ease;
        }
        .comms-info-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.28);
            color: #ffffff;
            transform: translateY(-1px);
        }
        .comms-info-card {
            background: #ffffff;
            color: #333333;
            border-radius: 14px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        }
        .comms-info-icon-wrapper {
            background: #e0f2fe;
            color: #0284c7;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        /* Toolbar & Filters */
        .comms-toolbar {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 1.25rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }
        .comms-search-box {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .comms-search-box:focus-within {
            border-color: #00698f;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(0, 105, 143, 0.12);
        }
        .comms-search-box input {
            background: transparent;
            font-size: 0.95rem;
        }
        .filter-chip {
            border-radius: 9999px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #475569;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.45rem 0.9rem;
            transition: all 0.2s ease;
        }
        .filter-chip:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
        .filter-chip.active {
            background: #00698f;
            color: #ffffff;
            border-color: #00698f;
            box-shadow: 0 2px 8px rgba(0, 105, 143, 0.25);
        }
        .filter-chip.active .badge {
            background: #ffffff !important;
            color: #00698f !important;
        }

        /* View Switcher */
        .view-switcher-group .view-switch-btn {
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
            border: none;
            transition: all 0.2s ease;
        }
        .view-switcher-group .view-switch-btn.active {
            background: #ffffff;
            color: #00698f;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        /* Cards Styling */
        .committee-card {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .committee-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 28px var(--committee-glow, rgba(0, 105, 143, 0.15));
            border-color: var(--committee-border, #cbd5e1);
        }
        .committee-card-accent-bar {
            height: 4px;
            width: 100%;
        }
        .committee-avatar-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }
        .committee-logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 4px;
        }
        .committee-avatar-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.4rem;
        }
        .committee-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #0f172a;
            line-height: 1.35;
            transition: color 0.15s ease;
        }
        .hover-primary:hover {
            color: #00698f;
        }
        .badge-online {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 9999px;
        }
        .badge-inperson {
            background-color: #e0f7f6;
            color: #00698f;
            border: 1px solid #b2dfdb;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 9999px;
        }

        /* Schedule Box */
        .schedule-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 0.75rem 0.9rem;
        }
        .schedule-icon-pill {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            flex-shrink: 0;
        }
        .schedule-label {
            font-size: 0.72rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .schedule-value {
            font-size: 0.92rem;
            color: #1e293b;
            line-height: 1.4;
        }

        /* Action Buttons */
        .comms-card-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            width: 100%;
        }
        .comms-action-pill {
            height: 36px;
            padding: 0 12px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            white-space: nowrap;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .comms-action-pill:hover {
            transform: translateY(-1px);
        }
        .comms-icon-btn {
            width: 36px;
            height: 36px;
            min-width: 36px;
            padding: 0;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            color: #475569;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.15s ease;
        }
        .comms-icon-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
            transform: translateY(-1px);
        }
        .comms-action-btn {
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.55rem 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .btn-zoom {
            background-color: #0284c7;
            border-color: #0284c7;
            color: #ffffff !important;
        }
        .btn-zoom:hover {
            background-color: #0369a1;
            border-color: #0369a1;
        }
        .comms-subaction-btn {
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 0.45rem 0.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }
        .comms-subaction-btn:hover {
            background-color: #e2e8f0;
        }

        /* Timeline View Styling */
        .timeline-header-card {
            border-left: 4px solid #00698f;
        }
        html[dir="rtl"] .timeline-header-card {
            border-left: none;
            border-right: 4px solid #00698f;
        }
        .timeline-week-pill {
            font-size: 0.88rem;
            letter-spacing: 0.3px;
        }
        .timeline-event-card {
            transition: all 0.2s ease;
            position: relative;
        }
        .timeline-event-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 18px rgba(0, 105, 143, 0.1);
            border-color: #cbd5e1;
        }

        /* Slide-over Profile Drawer */
        .comms-drawer-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 99999;
            transition: opacity 0.3s ease;
        }
        .comms-drawer-panel {
            position: fixed;
            top: 0;
            bottom: 0;
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            z-index: 100000;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        html[dir="ltr"] .comms-drawer-panel {
            right: 0;
            transform: translateX(100%);
        }
        html[dir="rtl"] .comms-drawer-panel {
            left: 0;
            transform: translateX(-100%);
        }
        .comms-drawer-backdrop.show .comms-drawer-panel {
            transform: translateX(0) !important;
        }

        /* Modal Backdrop & Card */
        .comms-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(6px);
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .comms-modal-card {
            background: #ffffff;
            border-radius: 20px;
            max-width: 420px;
            width: 100%;
            padding: 1.75rem;
            position: relative;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            animation: modalSlideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .comms-modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #f1f5f9;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            cursor: pointer;
            transition: all 0.15s ease;
            z-index: 2;
        }
        html[dir="rtl"] .comms-modal-close {
            right: auto;
            left: 1rem;
        }
        .comms-modal-close:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* Empty State */
        .comms-empty-state {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
        }
        .empty-state-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .comms-hero {
                padding: 1.75rem 1rem;
            }
            .comms-hero-title {
                font-size: 1.5rem;
            }
            .comms-hero-subtitle {
                font-size: 0.92rem;
            }
            .comms-toolbar {
                padding: 1rem;
            }
            .comms-drawer-panel {
                max-width: 100vw;
            }
        }
    </style>

    {{-- Interactive Client-side Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('commsSearchInput');
            const clearSearchBtn = document.getElementById('clearSearchBtn');
            const filterChips = document.querySelectorAll('.filter-chip');
            const cardCols = document.querySelectorAll('.committee-card-col');
            const resultsCountSpan = document.getElementById('resultsCount');
            const noResultsState = document.getElementById('noResultsState');
            const resetFiltersBtn = document.getElementById('resetFiltersBtn');
            
            // View Switcher Elements
            const viewBtnGrid = document.getElementById('viewBtnGrid');
            const viewBtnTimeline = document.getElementById('viewBtnTimeline');
            const commsGridViewContainer = document.getElementById('commsGridViewContainer');
            const commsTimelineViewContainer = document.getElementById('commsTimelineViewContainer');

            // Toast
            const copyToastEl = document.getElementById('copyToast');
            const toastMessageEl = document.getElementById('toastMessage');
            let copyToast = null;

            if (typeof bootstrap !== 'undefined' && copyToastEl) {
                copyToast = new bootstrap.Toast(copyToastEl, { delay: 2500 });
            }

            function showToast(message) {
                if (toastMessageEl) {
                    toastMessageEl.textContent = message;
                }
                if (copyToast) {
                    copyToast.show();
                }
            }

            // View Switching Logic
            if (viewBtnGrid && viewBtnTimeline) {
                viewBtnGrid.addEventListener('click', function() {
                    viewBtnGrid.classList.add('active');
                    viewBtnTimeline.classList.remove('active');
                    if (commsGridViewContainer) commsGridViewContainer.classList.remove('d-none');
                    if (commsTimelineViewContainer) commsTimelineViewContainer.classList.add('d-none');
                });

                viewBtnTimeline.addEventListener('click', function() {
                    viewBtnTimeline.classList.add('active');
                    viewBtnGrid.classList.remove('active');
                    if (commsTimelineViewContainer) commsTimelineViewContainer.classList.remove('d-none');
                    if (commsGridViewContainer) commsGridViewContainer.classList.add('d-none');
                });
            }

            // Search & Filtering
            let currentFilter = 'all';
            let currentSearchTerm = '';

            function updateCardsVisibility() {
                let visibleCount = 0;

                cardCols.forEach(col => {
                    const format = col.getAttribute('data-format');
                    const searchData = col.getAttribute('data-search') || '';

                    const matchesFilter = (currentFilter === 'all') || (format === currentFilter);
                    const matchesSearch = !currentSearchTerm || searchData.includes(currentSearchTerm);

                    if (matchesFilter && matchesSearch) {
                        col.classList.remove('d-none');
                        visibleCount++;
                    } else {
                        col.classList.add('d-none');
                    }
                });

                if (resultsCountSpan) {
                    resultsCountSpan.textContent = visibleCount;
                }

                if (noResultsState) {
                    if (visibleCount === 0) {
                        noResultsState.classList.remove('d-none');
                    } else {
                        noResultsState.classList.add('d-none');
                    }
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    currentSearchTerm = this.value.trim().toLowerCase();
                    if (clearSearchBtn) {
                        if (currentSearchTerm.length > 0) {
                            clearSearchBtn.classList.remove('d-none');
                        } else {
                            clearSearchBtn.classList.add('d-none');
                        }
                    }
                    updateCardsVisibility();
                });
            }

            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', function() {
                    if (searchInput) {
                        searchInput.value = '';
                        currentSearchTerm = '';
                        clearSearchBtn.classList.add('d-none');
                        searchInput.focus();
                        updateCardsVisibility();
                    }
                });
            }

            filterChips.forEach(chip => {
                chip.addEventListener('click', function() {
                    filterChips.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = this.getAttribute('data-filter') || 'all';
                    updateCardsVisibility();
                });
            });

            if (resetFiltersBtn) {
                resetFiltersBtn.addEventListener('click', function() {
                    if (searchInput) {
                        searchInput.value = '';
                        currentSearchTerm = '';
                    }
                    if (clearSearchBtn) {
                        clearSearchBtn.classList.add('d-none');
                    }
                    filterChips.forEach(c => {
                        if (c.getAttribute('data-filter') === 'all') {
                            c.classList.add('active');
                        } else {
                            c.classList.remove('active');
                        }
                    });
                    currentFilter = 'all';
                    updateCardsVisibility();
                });
            }

            // Copy Email Action
            document.querySelectorAll('.btn-copy-email').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const email = this.getAttribute('data-email');
                    if (!email) return;
                    navigator.clipboard.writeText(email).then(() => {
                        showToast("{{ __('messages.copied') }} (" + email + ")");
                    }).catch(() => {
                        showToast(email);
                    });
                });
            });

            // Copy Address Action
            document.querySelectorAll('.btn-copy-address').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const address = this.getAttribute('data-address');
                    if (!address) return;
                    navigator.clipboard.writeText(address).then(() => {
                        showToast("{{ __('messages.copied') }} (" + address + ")");
                    }).catch(() => {
                        showToast(address);
                    });
                });
            });

            // Copy Full Meeting Details Action
            function copyMeetingDetailsFormatted(title, schedule, location, email, url) {
                let text = `📋 ${title}\n`;
                if (schedule) text += `🕒 {{ __('messages.meeting_schedule') }}: ${schedule}\n`;
                if (location) text += `📍 {{ __('messages.meeting_location') }}: ${location}\n`;
                if (url && (url.startsWith('http://') || url.startsWith('https://'))) text += `🔗 {{ __('messages.zoomlink') }}: ${url}\n`;
                if (email) text += `✉️ {{ __('messages.committee_contact_email') }}: ${email}\n`;
                text += `\nNA Egypt - www.naegypt.org`;

                navigator.clipboard.writeText(text).then(() => {
                    showToast("{{ __('messages.copied') }}");
                }).catch(() => {
                    showToast("{{ __('messages.copied') }}");
                });
            }

            document.querySelectorAll('.btn-copy-details').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const title = this.getAttribute('data-title') || '';
                    const schedule = this.getAttribute('data-schedule') || '';
                    const location = this.getAttribute('data-location') || '';
                    const email = this.getAttribute('data-email') || '';
                    const url = this.getAttribute('data-url') || '';
                    copyMeetingDetailsFormatted(title, schedule, location, email, url);
                });
            });

            // Add to Calendar Action (.ics file download)
            function downloadCalendarIcs(title, schedule, location) {
                const now = new Date();
                const startStr = now.toISOString().replace(/-|:|\.\d+/g, '').substring(0, 15) + 'Z';
                const endStr = new Date(now.getTime() + 7200000).toISOString().replace(/-|:|\.\d+/g, '').substring(0, 15) + 'Z';

                const icsContent = 
`BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//NA Egypt//Service Committee Meetings//EN
CALSCALE:GREGORIAN
BEGIN:VEVENT
SUMMARY:${title}
DESCRIPTION:${schedule}
LOCATION:${location}
DTSTART:${startStr}
DTEND:${endStr}
STATUS:CONFIRMED
END:VEVENT
END:VCALENDAR`;

                const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.setAttribute('download', `${title.replace(/\s+/g, '_')}_Meeting.ics`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                showToast("{{ __('messages.add_to_calendar') }} (ICS)");
            }

            document.querySelectorAll('.btn-calendar').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const title = this.getAttribute('data-title') || 'NA Service Committee Meeting';
                    const schedule = this.getAttribute('data-schedule') || '';
                    const location = this.getAttribute('data-location') || 'Egypt';
                    downloadCalendarIcs(title, schedule, location);
                });
            });

            // Universal Smart QR Modal Logic (Zoom & Google Maps)
            const smartQrModal = document.getElementById('smartQrModal');
            const closeSmartQrModalBtn = document.getElementById('closeSmartQrModalBtn');
            const smartQrCanvasContainer = document.getElementById('smartQrCanvasContainer');
            const smartQrModalBadge = document.getElementById('smartQrModalBadge');
            const smartQrModalIcon = document.getElementById('smartQrModalIcon');
            const smartQrModalTitle = document.getElementById('smartQrModalTitle');
            const smartQrModalSubtitle = document.getElementById('smartQrModalSubtitle');
            const smartQrUrlText = document.getElementById('smartQrUrlText');
            const smartQrActionBtn = document.getElementById('smartQrActionBtn');
            const smartQrActionIcon = document.getElementById('smartQrActionIcon');
            const smartQrActionLabel = document.getElementById('smartQrActionLabel');
            const smartQrModalCopyBtn = document.getElementById('smartQrModalCopyBtn');
            const smartQrModalCopyIcon = document.getElementById('smartQrModalCopyIcon');
            const smartQrCopyLabel = document.getElementById('smartQrCopyLabel');
            const smartQrModalDownloadBtn = document.getElementById('smartQrModalDownloadBtn');
            
            let currentQrCodeInstance = null;
            let currentActiveUrl = '';

            function openSmartQr(type, title, url) {
                if (!url) return;
                currentActiveUrl = url;

                if (type === 'zoom') {
                    if (smartQrModalBadge) {
                        smartQrModalBadge.style.backgroundColor = '#e0f2fe';
                        smartQrModalBadge.style.color = '#0369a1';
                        smartQrModalBadge.style.border = '1px solid #bae6fd';
                    }
                    if (smartQrModalIcon) smartQrModalIcon.className = 'bi bi-camera-video-fill text-primary';
                    if (smartQrModalTitle) smartQrModalTitle.textContent = title + ' - ' + "{{ __('messages.zoom_meeting_qr') }}";
                    if (smartQrModalSubtitle) smartQrModalSubtitle.textContent = "{{ __('messages.scan_to_join') }}";
                    if (smartQrActionIcon) smartQrActionIcon.className = 'bi bi-camera-video-fill me-1';
                    if (smartQrActionLabel) smartQrActionLabel.textContent = "{{ __('messages.join_zoom_meeting') }}";
                    if (smartQrCopyLabel) smartQrCopyLabel.textContent = "{{ __('messages.copy_zoom_link') }}";
                } else {
                    if (smartQrModalBadge) {
                        smartQrModalBadge.style.backgroundColor = '#e0f7f6';
                        smartQrModalBadge.style.color = '#00698f';
                        smartQrModalBadge.style.border = '1px solid #b2dfdb';
                    }
                    if (smartQrModalIcon) smartQrModalIcon.className = 'bi bi-geo-alt-fill text-danger';
                    if (smartQrModalTitle) smartQrModalTitle.textContent = title + ' - ' + "{{ __('messages.map_location_qr') }}";
                    if (smartQrModalSubtitle) smartQrModalSubtitle.textContent = "{{ __('messages.scan_for_directions') }}";
                    if (smartQrActionIcon) smartQrActionIcon.className = 'bi bi-map-fill me-1';
                    if (smartQrActionLabel) smartQrActionLabel.textContent = "{{ __('messages.open_in_google_maps') }}";
                    if (smartQrCopyLabel) smartQrCopyLabel.textContent = "{{ __('messages.copy_map_link') }}";
                }

                if (smartQrUrlText) smartQrUrlText.textContent = url;
                if (smartQrActionBtn) smartQrActionBtn.href = url;

                // Render QR code
                if (smartQrCanvasContainer) {
                    smartQrCanvasContainer.innerHTML = '';
                    if (typeof QRCode !== 'undefined') {
                        currentQrCodeInstance = new QRCode(smartQrCanvasContainer, {
                            text: url,
                            width: 190,
                            height: 190,
                            colorDark: (type === 'zoom') ? "#0284c7" : "#00698f",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.M
                        });
                    }
                }

                if (smartQrModal) smartQrModal.classList.remove('d-none');
            }

            function closeSmartQr() {
                if (smartQrModal) smartQrModal.classList.add('d-none');
            }

            document.querySelectorAll('.btn-show-qr').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const type = this.getAttribute('data-qr-type') || 'zoom';
                    const url = this.getAttribute('data-url');
                    const title = this.getAttribute('data-title');
                    openSmartQr(type, title, url);
                });
            });

            if (closeSmartQrModalBtn) {
                closeSmartQrModalBtn.addEventListener('click', closeSmartQr);
            }

            if (smartQrModal) {
                smartQrModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeSmartQr();
                    }
                });
            }

            // Keyboard ESC to close modal or drawer
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (smartQrModal && !smartQrModal.classList.contains('d-none')) {
                        closeSmartQr();
                    } else if (commsDrawerBackdrop && commsDrawerBackdrop.classList.contains('show')) {
                        closeDrawer();
                    }
                }
            });

            function copySmartQrUrl() {
                if (!currentActiveUrl) return;
                navigator.clipboard.writeText(currentActiveUrl).then(() => {
                    if (smartQrModalCopyBtn) {
                        smartQrModalCopyBtn.classList.remove('btn-outline-secondary');
                        smartQrModalCopyBtn.classList.add('btn-success', 'text-white');
                        const origHtml = smartQrModalCopyBtn.innerHTML;
                        smartQrModalCopyBtn.innerHTML = '<i class="bi bi-check2"></i> {{ __("messages.copied") }}';
                        setTimeout(() => {
                            smartQrModalCopyBtn.classList.remove('btn-success', 'text-white');
                            smartQrModalCopyBtn.classList.add('btn-outline-secondary');
                            smartQrModalCopyBtn.innerHTML = origHtml;
                        }, 2000);
                    }
                    showToast("{{ __('messages.copied') }}");
                }).catch(() => {
                    showToast("{{ __('messages.copied') }}");
                });
            }

            if (smartQrModalCopyBtn) smartQrModalCopyBtn.addEventListener('click', copySmartQrUrl);
            if (smartQrModalCopyIcon) smartQrModalCopyIcon.addEventListener('click', copySmartQrUrl);

            if (smartQrModalDownloadBtn) {
                smartQrModalDownloadBtn.addEventListener('click', function() {
                    const canvas = smartQrCanvasContainer ? smartQrCanvasContainer.querySelector('canvas') : null;
                    const img = smartQrCanvasContainer ? smartQrCanvasContainer.querySelector('img') : null;
                    let imgSrc = canvas ? canvas.toDataURL("image/png") : (img ? img.src : null);

                    if (imgSrc) {
                        const link = document.createElement('a');
                        link.download = 'NA_Committee_QR.png';
                        link.href = imgSrc;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        showToast("{{ __('messages.download_zoom_qr') }}");
                    }
                });
            }

            // Slide-over Profile Drawer Logic
            const commsDrawerBackdrop = document.getElementById('commsDrawerBackdrop');
            const closeDrawerBtn = document.getElementById('closeDrawerBtn');
            const drawerTitle = document.getElementById('drawerTitle');
            const drawerSubtitle = document.getElementById('drawerSubtitle');
            const drawerAvatar = document.getElementById('drawerAvatar');
            const drawerIcon = document.getElementById('drawerIcon');
            const drawerFormatBadge = document.getElementById('drawerFormatBadge');
            const drawerSchedule = document.getElementById('drawerSchedule');
            const drawerAddress = document.getElementById('drawerAddress');
            const drawerLocationIcon = document.getElementById('drawerLocationIcon');
            const drawerDesc = document.getElementById('drawerDesc');
            const drawerChairmanBox = document.getElementById('drawerChairmanBox');
            const drawerChairmanName = document.getElementById('drawerChairmanName');
            const drawerChairmanPhone = document.getElementById('drawerChairmanPhone');
            const drawerChairmanPhoneLink = document.getElementById('drawerChairmanPhoneLink');
            const drawerEmailText = document.getElementById('drawerEmailText');
            const drawerCopyEmailBtn = document.getElementById('drawerCopyEmailBtn');
            const drawerPrimaryActionBtn = document.getElementById('drawerPrimaryActionBtn');
            const drawerPrimaryActionIcon = document.getElementById('drawerPrimaryActionIcon');
            const drawerPrimaryActionLabel = document.getElementById('drawerPrimaryActionLabel');
            const drawerQrBtn = document.getElementById('drawerQrBtn');
            const drawerCopyDetailsBtn = document.getElementById('drawerCopyDetailsBtn');
            const drawerCalendarBtn = document.getElementById('drawerCalendarBtn');

            let activeDrawerData = null;

            function openDrawer(data) {
                activeDrawerData = data;

                if (drawerTitle) drawerTitle.textContent = data.name;
                if (drawerSubtitle) drawerSubtitle.textContent = (data.subname && data.subname !== data.name) ? data.subname : '';

                if (drawerAvatar) {
                    drawerAvatar.style.background = data.bg || '#00698f';
                    drawerAvatar.style.borderColor = data.border || '#00698f';
                }
                if (drawerIcon) {
                    drawerIcon.className = `bi ${data.icon || 'bi-diagram-3-fill'} fs-4 text-white`;
                }

                if (drawerFormatBadge) {
                    if (data.format === 'online') {
                        drawerFormatBadge.className = 'badge badge-online px-3 py-1.5 rounded-pill shadow-xs';
                        drawerFormatBadge.innerHTML = '<i class="bi bi-camera-video-fill me-1"></i> {{ __("messages.service_badge_online") }}';
                    } else {
                        drawerFormatBadge.className = 'badge badge-inperson px-3 py-1.5 rounded-pill shadow-xs';
                        drawerFormatBadge.innerHTML = '<i class="bi bi-geo-alt-fill me-1"></i> {{ __("messages.service_badge_in_person") }}';
                    }
                }

                if (drawerSchedule) {
                    drawerSchedule.textContent = data.schedule || "{{ $isRtl ? 'حسب جدول أعمال اللجنة' : 'As scheduled by committee' }}";
                }

                if (drawerAddress) {
                    drawerAddress.textContent = (data.format === 'online' && (!data.address || data.address === 'Online' || data.address === 'أونلاين'))
                        ? "{{ __('messages.online_meetings') }} (Zoom / Virtual)"
                        : (data.address || data.locationUrl);
                }

                if (drawerLocationIcon) {
                    drawerLocationIcon.className = (data.format === 'online') 
                        ? 'bi bi-globe2 text-primary fs-5 mt-0.5' 
                        : 'bi bi-geo-alt-fill text-danger fs-5 mt-0.5';
                }

                if (drawerDesc) drawerDesc.textContent = data.desc || '';

                if (drawerChairmanBox) {
                    if (data.chairmanName || data.chairmanPhone) {
                        drawerChairmanBox.classList.remove('d-none');
                        if (drawerChairmanName) drawerChairmanName.textContent = data.chairmanName || '';
                        if (drawerChairmanPhone) drawerChairmanPhone.textContent = data.chairmanPhone || '';
                        if (drawerChairmanPhoneLink) {
                            if (data.chairmanPhone) {
                                drawerChairmanPhoneLink.href = 'tel:' + data.chairmanPhone;
                                drawerChairmanPhoneLink.classList.remove('d-none');
                            } else {
                                drawerChairmanPhoneLink.classList.add('d-none');
                            }
                        }
                    } else {
                        drawerChairmanBox.classList.add('d-none');
                    }
                }

                if (drawerEmailText) drawerEmailText.textContent = data.email || '—';

                // Setup Primary Action button
                if (drawerPrimaryActionBtn) {
                    if (data.format === 'online' && data.locationUrl && (data.locationUrl.startsWith('http://') || data.locationUrl.startsWith('https://'))) {
                        drawerPrimaryActionBtn.href = data.locationUrl;
                        drawerPrimaryActionBtn.className = 'btn btn-primary comms-action-btn flex-grow-1 shadow-sm';
                        if (drawerPrimaryActionIcon) drawerPrimaryActionIcon.className = 'bi bi-camera-video-fill me-1';
                        if (drawerPrimaryActionLabel) drawerPrimaryActionLabel.textContent = "{{ __('messages.join_zoom_meeting') }}";
                        if (drawerQrBtn) {
                            drawerQrBtn.classList.remove('d-none');
                            drawerQrBtn.onclick = function() {
                                openSmartQr('zoom', data.name, data.locationUrl);
                            };
                        }
                    } else if (data.mapUrl) {
                        drawerPrimaryActionBtn.href = data.mapUrl;
                        drawerPrimaryActionBtn.className = 'btn btn-outline-primary comms-action-btn flex-grow-1 shadow-sm';
                        if (drawerPrimaryActionIcon) drawerPrimaryActionIcon.className = 'bi bi-geo-alt-fill text-danger me-1';
                        if (drawerPrimaryActionLabel) drawerPrimaryActionLabel.textContent = "{{ __('messages.open_map_location') }}";
                        if (drawerQrBtn) {
                            drawerQrBtn.classList.remove('d-none');
                            drawerQrBtn.onclick = function() {
                                openSmartQr('map', data.name, data.mapUrl);
                            };
                        }
                    } else {
                        drawerPrimaryActionBtn.href = 'mailto:' + (data.email || 'web@naegypt.org');
                        drawerPrimaryActionBtn.className = 'btn btn-primary comms-action-btn flex-grow-1 shadow-sm';
                        if (drawerPrimaryActionIcon) drawerPrimaryActionIcon.className = 'bi bi-envelope-fill me-1';
                        if (drawerPrimaryActionLabel) drawerPrimaryActionLabel.textContent = "{{ __('messages.committee_contact_email') }}";
                        if (drawerQrBtn) drawerQrBtn.classList.add('d-none');
                    }
                }

                // Drawer Copy Email
                if (drawerCopyEmailBtn) {
                    drawerCopyEmailBtn.onclick = function() {
                        if (data.email) {
                            navigator.clipboard.writeText(data.email).then(() => {
                                showToast("{{ __('messages.copied') }} (" + data.email + ")");
                            });
                        }
                    };
                }

                // Drawer Copy Full Details
                if (drawerCopyDetailsBtn) {
                    drawerCopyDetailsBtn.onclick = function() {
                        copyMeetingDetailsFormatted(data.name, data.schedule, data.address || data.locationUrl, data.email, data.locationUrl);
                    };
                }

                // Drawer Calendar
                if (drawerCalendarBtn) {
                    drawerCalendarBtn.onclick = function() {
                        downloadCalendarIcs(data.name, data.schedule, data.address || data.locationUrl);
                    };
                }

                if (commsDrawerBackdrop) {
                    commsDrawerBackdrop.classList.remove('d-none');
                    setTimeout(() => {
                        commsDrawerBackdrop.classList.add('show');
                    }, 10);
                }
            }

            function closeDrawer() {
                if (commsDrawerBackdrop) {
                    commsDrawerBackdrop.classList.remove('show');
                    setTimeout(() => {
                        commsDrawerBackdrop.classList.add('d-none');
                    }, 300);
                }
            }

            document.querySelectorAll('.btn-open-drawer').forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    const data = {
                        id: this.getAttribute('data-id'),
                        name: this.getAttribute('data-name'),
                        subname: this.getAttribute('data-subname'),
                        format: this.getAttribute('data-format'),
                        schedule: this.getAttribute('data-schedule'),
                        address: this.getAttribute('data-address'),
                        locationUrl: this.getAttribute('data-location-url'),
                        mapUrl: this.getAttribute('data-map-url'),
                        email: this.getAttribute('data-email'),
                        chairmanName: this.getAttribute('data-chairman-name'),
                        chairmanPhone: this.getAttribute('data-chairman-phone'),
                        desc: this.getAttribute('data-desc'),
                        icon: this.getAttribute('data-icon'),
                        bg: this.getAttribute('data-bg'),
                        border: this.getAttribute('data-border'),
                    };
                    openDrawer(data);
                });
            });

            if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeDrawer);
            if (commsDrawerBackdrop) {
                commsDrawerBackdrop.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeDrawer();
                    }
                });
            }
        });
    </script>
</x-frontend.layout>
