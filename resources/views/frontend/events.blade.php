<x-frontend.layout title="الأحداث والمؤتمرات" description="جدول الأحداث والمؤتمرات والأنشطة العامة لزمالة المدمنين المجهولين في مصر.">
    <x-section-head>{{ __('messages.Events') ?? 'Events' }}</x-section-head>

    <div class="container my-5">
        @if($events->isEmpty())
            <div class="alert alert-info text-center">
                {{ __('messages.No upcoming events.') ?? 'No upcoming events.' }}
            </div>
        @else
            <!-- View Toggle -->
            <div class="d-flex justify-content-center mb-5">
                <ul class="nav nav-pills bg-light p-2.5 rounded-pill shadow-sm border border-light w-100" id="eventsViewTab" role="tablist" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" style="max-width: 960px;">
                    <li class="nav-item col-6" role="presentation">
                        <button class="nav-link active w-100 rounded-pill py-3 font-semibold transition-all d-flex align-items-center justify-content-center gap-2" id="slider-tab" data-bs-toggle="pill" data-bs-target="#slider-view" type="button" role="tab" aria-controls="slider-view" aria-selected="true" style="font-size: 1.1rem;">
                            <i class="bi bi-view-list fs-4"></i>
                            <span>{{ __('messages.List') }}</span>
                        </button>
                    </li>
                    <li class="nav-item col-6" role="presentation">
                        <button class="nav-link w-100 rounded-pill py-3 font-semibold transition-all d-flex align-items-center justify-content-center gap-2" id="calendar-tab" data-bs-toggle="pill" data-bs-target="#calendar-view" type="button" role="tab" aria-controls="calendar-view" aria-selected="false" style="font-size: 1.1rem;">
                            <i class="bi bi-calendar3 fs-4"></i>
                            <span>{{ __('messages.Calendar') }}</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content w-100" id="eventsViewTabContent">
                <!-- List View (Interactive Timeline) -->
                <div class="tab-pane fade show active w-100" id="slider-view" role="tabpanel" aria-labelledby="slider-tab">
                    
                    <!-- Search & Filter Header Bar -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
                        <div class="row g-3 align-items-center">
                            <!-- Search Box -->
                            <div class="col-12 col-md-5">
                                <div class="input-group search-input-group" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                                    <span class="input-group-text bg-light border-0 text-muted px-3" style="border-radius: {{ app()->getLocale() === 'ar' ? '0 12px 12px 0' : '12px 0 0 12px' }};">
                                        <i class="bi bi-search" style="color: #00698f;"></i>
                                    </span>
                                    <input type="text" id="eventSearchInput" class="form-control bg-light border-0 shadow-none text-start" style="border-radius: {{ app()->getLocale() === 'ar' ? '12px 0 0 12px' : '0 12px 12px 0' }};" placeholder="{{ __('messages.Search events...') }}">
                                </div>
                            </div>
                            
                            <!-- Month Filter Dropdown -->
                            <div class="col-12 col-md-7">
                                <div class="input-group month-select-group" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                                    <span class="input-group-text bg-light border-0 text-muted px-3" style="border-radius: {{ app()->getLocale() === 'ar' ? '0 12px 12px 0' : '12px 0 0 12px' }};">
                                        <i class="bi bi-calendar3" style="color: #00698f;"></i>
                                    </span>
                                    <select id="eventMonthSelect" class="form-select bg-light border-0 shadow-none text-start cursor-pointer" style="border-radius: {{ app()->getLocale() === 'ar' ? '12px 0 0 12px' : '0 12px 12px 0' }}; font-weight: 500;">
                                        <option value="all">{{ __('messages.All Months') }}</option>
                                        @foreach($events as $month => $monthEvents)
                                            <option value="{{ Str::slug($month) }}">{{ $month }} ({{ $monthEvents->count() }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Feed -->
                    <div id="eventsListContainer" class="space-y-4">
                        @foreach($events as $month => $monthEvents)
                            <div class="month-section mb-5" data-month-slug="{{ Str::slug($month) }}">
                                <div class="d-flex items-center gap-3 mb-3 border-bottom pb-2">
                                    <h4 class="fw-bold mb-0 text-primary-dark" style="color: #00698f;">
                                        <i class="bi bi-calendar-event me-2"></i>{{ $month }}
                                    </h4>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
                                        {{ $monthEvents->count() }} {{ __('messages.Events') ?? 'Events' }}
                                    </span>
                                </div>

                                <div class="row g-3">
                                    @foreach($monthEvents as $event)
                                        <div class="col-12 event-card-item" data-search-text="{{ strtolower($event->title . ' ' . $event->description . ' ' . $event->location . ' ' . $event->organizer) }}">
                                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden hov-translate transition-all" style="border-left: 5px solid {{ $event->color ?? '#00698f' }} !important;">
                                                <div class="card-body p-3 p-md-4">
                                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-2">
                                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                                            <h5 class="card-title fw-bold mb-0" style="color: {{ $event->color ?? '#00698f' }};">
                                                                {{ $event->title }}
                                                            </h5>
                                                            @if($event->is_featured)
                                                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1 shadow-xs font-semibold" style="font-size: 0.75rem;">
                                                                    ⭐ {{ __('messages.Featured') }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <div class="text-muted small fw-semibold bg-light px-3 py-1.5 rounded-pill d-inline-flex align-items-center gap-1 shrink-0">
                                                            <i class="bi bi-clock text-primary"></i>
                                                            {{ \Carbon\Carbon::parse($event->start)->translatedFormat('M d, Y h:i A') }}
                                                        </div>
                                                    </div>

                                                    <div class="row g-2 my-2 text-muted small">
                                                        @if($event->location)
                                                            <div class="col-12 col-sm-auto me-3">
                                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $event->location }}
                                                            </div>
                                                        @endif

                                                        @if($event->organizer)
                                                            <div class="col-12 col-sm-auto me-3">
                                                                <i class="bi bi-person-fill text-info me-1"></i> {{ $event->organizer }}
                                                            </div>
                                                        @endif

                                                        @if($event->formatted_recurrence && $event->formatted_recurrence !== 'Once' && $event->formatted_recurrence !== __('messages.Once'))
                                                            <div class="col-12 col-sm-auto me-3">
                                                                <i class="bi bi-arrow-repeat text-success me-1"></i> {{ $event->formatted_recurrence }}
                                                            </div>
                                                        @endif
                                                    </div>

                                                    @if($event->description)
                                                        <p class="card-text text-secondary mt-2 mb-0 text-break line-clamp-3">
                                                            {{ $event->description }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div id="noSearchResults" class="alert alert-warning text-center d-none rounded-4 my-4">
                        {{ __('messages.No matching events found.') ?? 'No matching events found.' }}
                    </div>

                </div>

                <!-- Calendar View -->
                <div class="tab-pane fade w-100" id="calendar-view" role="tabpanel" aria-labelledby="calendar-tab">
                    <div
                        data-vue-app="EventsCalendar"
                        data-fetch-url="{{ route('web-calendar-events.index') }}"
                        data-store-url="{{ route('web-calendar-events.store') }}"
                        data-locale="{{ app()->getLocale() }}"
                        data-initial-events='{!! $allEventsJSON ?? "[]" !!}'
                        @if(auth()->check() && (auth()->user()->hasPermissionTo('can_manage_calendar') || auth()->user()->hasRole('super admin') || auth()->user()->hasRole('rsc'))) data-can-manage @endif
                        class="w-100"
                    ></div>
                </div>
            </div>
        @endif
    </div>

    <!-- Calendar Event Modal -->
    <div class="modal fade" id="calendarEventModal" tabindex="-1" aria-labelledby="calendarEventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="calendarEventModalLabel">{{ __('messages.Event Details') ?? 'Event Details' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <h4 id="modalEventTitle" class="fw-bold mb-3"></h4>
                    <h6 class="card-subtitle mb-3 text-muted">
                        <x-fas-calendar-alt style="width:14px; height:14px;" /> 
                        <span id="modalEventTime"></span>
                    </h6>
                    <h6 class="card-subtitle mb-3 text-muted d-none" id="modalEventLocationContainer">
                        <x-fas-map-marker-alt style="width:14px; height:14px;" /> 
                        <span id="modalEventLocation"></span>
                    </h6>
                    <h6 class="card-subtitle mb-3 text-muted d-none" id="modalEventOrganizerContainer">
                        <x-fas-user style="width:14px; height:14px;" /> 
                        <span id="modalEventOrganizer"></span>
                    </h6>
                    <h6 class="card-subtitle mb-3 text-muted d-none" id="modalEventRecurrenceContainer">
                        <i class="bi bi-arrow-repeat" style="width:14px; height:14px;"></i> 
                        <span id="modalEventRecurrence"></span>
                    </h6>
                    <p id="modalEventDescription" class="card-text"></p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hov-scale {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hov-scale:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        #eventsViewTab .nav-link {
            color: #4b5563;
            background-color: transparent;
            border: none;
            font-size: 0.95rem;
            transition: all 0.25s ease;
        }
        #eventsViewTab .nav-link:hover:not(.active) {
            color: #00698f;
            background-color: #ffffff;
        }
        #eventsViewTab .nav-link.active {
            color: #ffffff;
            background-color: #00698f;
            box-shadow: 0 4px 12px rgba(0, 105, 143, 0.35);
        }
        #eventsCarousel .carousel-control-prev,
        #eventsCarousel .carousel-control-next {
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            z-index: 1040;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(0, 105, 143, 0.2);
            transition: all 0.3s ease;
            opacity: 0.85;
        }
        #eventsCarousel .carousel-control-prev:hover,
        #eventsCarousel .carousel-control-next:hover {
            opacity: 1;
            transform: translateY(-50%) scale(1.1);
            background: #00698f;
            color: #fff;
        }
        #eventsCarousel .carousel-control-prev {
            left: 20px;
        }
        #eventsCarousel .carousel-control-next {
            right: 20px;
        }
        #eventsCarousel .carousel-control-prev-icon,
        #eventsCarousel .carousel-control-next-icon {
            filter: invert(33%) sepia(85%) saturate(1412%) hue-rotate(167deg) brightness(85%) contrast(101%);
            width: 20px;
            height: 20px;
            transition: filter 0.3s ease;
        }
        #eventsCarousel .carousel-control-prev:hover .carousel-control-prev-icon,
        #eventsCarousel .carousel-control-next:hover .carousel-control-next-icon {
            filter: invert(100%) sepia(0%) saturate(7500%) hue-rotate(349deg) brightness(102%) contrast(104%);
        }
        @media (max-width: 768px) {
            #eventsCarousel .carousel-control-prev {
                left: 10px;
            }
            #eventsCarousel .carousel-control-next {
                right: 10px;
            }
            #eventsCarousel .carousel-control-prev,
            #eventsCarousel .carousel-control-next {
                width: 40px;
                height: 40px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarTab = document.getElementById('calendar-tab');
            if (calendarTab) {
                calendarTab.addEventListener('shown.bs.tab', function () {
                    window.dispatchEvent(new Event('resize'));
                });
            }

            // Real-time Search & Month Filter Logic
            var searchInput = document.getElementById('eventSearchInput');
            var monthSelect = document.getElementById('eventMonthSelect');
            var monthSections = document.querySelectorAll('.month-section');
            var noResults = document.getElementById('noSearchResults');

            function filterEvents() {
                var query = searchInput ? searchInput.value.toLowerCase().trim() : '';
                var activeMonth = monthSelect ? monthSelect.value : 'all';
                var totalVisible = 0;

                monthSections.forEach(function(section) {
                    var sectionMonth = section.getAttribute('data-month-slug');
                    var isMonthMatch = (activeMonth === 'all' || activeMonth === sectionMonth);
                    var cardItems = section.querySelectorAll('.event-card-item');
                    var visibleInMonth = 0;

                    cardItems.forEach(function(item) {
                        var searchText = item.getAttribute('data-search-text') || '';
                        var isSearchMatch = (!query || searchText.indexOf(query) !== -1);

                        if (isMonthMatch && isSearchMatch) {
                            item.classList.remove('d-none');
                            visibleInMonth++;
                            totalVisible++;
                        } else {
                            item.classList.add('d-none');
                        }
                    });

                    if (visibleInMonth > 0) {
                        section.classList.remove('d-none');
                    } else {
                        section.classList.add('d-none');
                    }
                });

                if (noResults) {
                    if (totalVisible === 0) {
                        noResults.classList.remove('d-none');
                    } else {
                        noResults.classList.add('d-none');
                    }
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterEvents);
            }

            if (monthSelect) {
                monthSelect.addEventListener('change', filterEvents);
            }
        });
    </script>
</x-frontend.layout>
