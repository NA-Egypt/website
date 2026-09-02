<x-frontend.layout title="الأحداث والمؤتمرات" description="جدول الأحداث والمؤتمرات والأنشطة العامة لزمالة المدمنين المجهولين في مصر.">
    @php
        $canCreateEvent = auth()->check() && (
            auth()->user()->can('create calendar events') ||
            auth()->user()->hasRole('Committees') ||
            auth()->user()->hasRole('ServiceBody') ||
            auth()->user()->hasRole('rsc') ||
            auth()->user()->hasRole('super admin')
        );
        $canManageAllEvents = auth()->check() && (
            auth()->user()->can('manage calendar events') ||
            auth()->user()->hasRole('rsc') ||
            auth()->user()->hasRole('super admin')
        );
    @endphp

    <x-section-head>{{ __('messages.Events') ?? 'Events' }}</x-section-head>

    <div class="container my-5">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Top Bar with View Toggle & Permission-Aware Add Event Button --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-5">
            <div class="w-100" style="max-width: 600px;">
                <ul class="nav nav-pills bg-light p-2 rounded-pill shadow-sm border border-light w-100" id="eventsViewTab" role="tablist" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                    <li class="nav-item flex-fill" role="presentation">
                        <button class="nav-link active w-100 rounded-pill py-2.5 font-semibold transition-all d-flex align-items-center justify-content-center gap-2" id="slider-tab" data-bs-toggle="pill" data-bs-target="#slider-view" type="button" role="tab" aria-controls="slider-view" aria-selected="true">
                            <i class="bi bi-view-list fs-5"></i>
                            <span>{{ __('messages.List') }}</span>
                        </button>
                    </li>
                    <li class="nav-item flex-fill" role="presentation">
                        <button class="nav-link w-100 rounded-pill py-2.5 font-semibold transition-all d-flex align-items-center justify-content-center gap-2" id="calendar-tab" data-bs-toggle="pill" data-bs-target="#calendar-view" type="button" role="tab" aria-controls="calendar-view" aria-selected="false">
                            <i class="bi bi-calendar3 fs-5"></i>
                            <span>{{ __('messages.Calendar') }}</span>
                        </button>
                    </li>
                </ul>
            </div>

            @if($canCreateEvent)
                <button type="button" class="btn btn-primary rounded-pill px-4 py-2.5 shadow-sm fw-semibold d-inline-flex align-items-center gap-2 shrink-0 transition-all" data-bs-toggle="modal" data-bs-target="#createEventModal">
                    <i class="bi bi-plus-circle-fill fs-5"></i>
                    <span>{{ __('messages.add_event') ?? 'Add Event' }}</span>
                </button>
            @endif
        </div>

        @if($events->isEmpty())
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                <i class="bi bi-calendar-x fs-1 text-muted d-block mb-3"></i>
                <h4 class="fw-bold text-dark mb-1">{{ __('messages.No upcoming events.') ?? 'No upcoming events.' }}</h4>
                <p class="text-secondary small mb-3">{{ __('messages.check_back_later') ?? 'Please check back later for scheduled events.' }}</p>
                @if($canCreateEvent)
                    <div>
                        <button type="button" class="btn btn-outline-primary rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#createEventModal">
                            <i class="bi bi-plus-circle me-1"></i>{{ __('messages.add_first_event') ?? 'Add First Event' }}
                        </button>
                    </div>
                @endif
            </div>
        @else
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
                                <div class="d-flex align-items-center gap-3 mb-3 border-bottom pb-2">
                                    <h4 class="fw-bold mb-0 text-primary-dark" style="color: #00698f;">
                                        <i class="bi bi-calendar-event me-2"></i>{{ $month }}
                                    </h4>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
                                        {{ $monthEvents->count() }} {{ __('messages.Events') ?? 'Events' }}
                                    </span>
                                </div>

                                <div class="row g-3">
                                    @foreach($monthEvents as $event)
                                        @php
                                            $canEditThisEvent = $canManageAllEvents || ($canCreateEvent && auth()->id() && (int)$event->user_id === (int)auth()->id());
                                        @endphp
                                        <div class="col-12 event-card-item" data-search-text="{{ strtolower($event->title . ' ' . $event->description . ' ' . $event->location . ' ' . $event->organizer) }}">
                                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden hov-translate transition-all bg-white" style="border-left: 5px solid {{ $event->color ?? '#00698f' }} !important;">
                                                <div class="card-body p-3 p-md-4">
                                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-2">
                                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                                            <h5 class="card-title fw-bold mb-0" style="color: {{ $event->color ?? '#00698f' }};">
                                                                {{ $event->title }}
                                                            </h5>
                                                            @if($event->is_featured)
                                                                <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 shadow-xs font-semibold" style="font-size: 0.75rem;">
                                                                    ⭐ {{ __('messages.Featured') }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                                            <div class="text-muted small fw-semibold bg-light px-3 py-1.5 rounded-pill d-inline-flex align-items-center gap-1 shrink-0">
                                                                <i class="bi bi-clock text-primary"></i>
                                                                {{ \Carbon\Carbon::parse($event->start)->translatedFormat('M d, Y h:i A') }}
                                                            </div>

                                                            @if($canEditThisEvent)
                                                                <div class="dropdown">
                                                                    <button class="btn btn-sm btn-light rounded-circle p-1.5" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('messages.Actions') }}">
                                                                        <i class="bi bi-three-dots-vertical"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0">
                                                                        <li>
                                                                            <button type="button" class="dropdown-item text-primary small d-flex align-items-center gap-2" onclick="editEventInBlade({{ json_encode($event) }})">
                                                                                <i class="bi bi-pencil"></i>
                                                                                <span>{{ __('messages.Edit') ?? 'Edit' }}</span>
                                                                            </button>
                                                                        </li>
                                                                        <li>
                                                                            <button type="button" class="dropdown-item text-danger small d-flex align-items-center gap-2" onclick="deleteEventInBlade({{ $event->id }})">
                                                                                <i class="bi bi-trash"></i>
                                                                                <span>{{ __('messages.Delete') ?? 'Delete' }}</span>
                                                                            </button>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            @endif
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
                        @if($canCreateEvent) data-can-create @endif
                        @if($canManageAllEvents) data-can-manage @endif
                        @if(auth()->check()) data-user-id="{{ auth()->id() }}" @endif
                        class="w-100"
                    ></div>
                </div>
            </div>
        @endif
    </div>

    {{-- Create Event Modal --}}
    @if($canCreateEvent)
        <div class="modal fade" id="createEventModal" tabindex="-1" aria-labelledby="createEventModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                    <form id="createEventForm" onsubmit="handleCreateEventSubmit(event)">
                        @csrf
                        <div class="modal-header border-bottom bg-light px-4 py-3">
                            <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="createEventModalLabel">
                                <i class="bi bi-calendar-plus text-primary"></i>
                                <span>{{ __('messages.add_new_event') ?? 'Add New Event' }}</span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-dark">{{ __('messages.Event Title') ?? 'Event Title' }} <span class="text-danger">*</span></label>
                                    <input type="text" name="title" required class="form-control rounded-pill border-secondary-subtle px-3" placeholder="{{ __('messages.Event Title') ?? 'e.g. Annual Convention / Committee Meeting' }}">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold small text-dark">{{ __('messages.Start Date & Time') ?? 'Start Date & Time' }} <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="start" required class="form-control rounded-pill border-secondary-subtle px-3">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold small text-dark">{{ __('messages.End Date & Time') ?? 'End Date & Time' }} <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="end" required class="form-control rounded-pill border-secondary-subtle px-3">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold small text-dark">{{ __('messages.Organizer') ?? 'Organizer' }}</label>
                                    <input type="text" name="organizer" class="form-control rounded-pill border-secondary-subtle px-3" placeholder="{{ __('messages.Organizer') ?? 'e.g. Literature Committee / Cairo ASC' }}">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold small text-dark">{{ __('messages.Location') ?? 'Location' }}</label>
                                    <input type="text" name="location" class="form-control rounded-pill border-secondary-subtle px-3" placeholder="{{ __('messages.Location') ?? 'e.g. Community Center / Online Zoom' }}">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold small text-dark">{{ __('messages.Recurrence') ?? 'Recurrence' }}</label>
                                    <select name="recurrence" class="form-select rounded-pill border-secondary-subtle px-3">
                                        <option value="once">{{ __('messages.Once') ?? 'Once' }}</option>
                                        <option value="weekly">{{ __('messages.Weekly') ?? 'Weekly' }}</option>
                                        <option value="monthly">{{ __('messages.Monthly (Same Date)') ?? 'Monthly' }}</option>
                                        <option value="every_two_months">{{ __('messages.Every Two Months') ?? 'Every Two Months' }}</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold small text-dark">{{ __('messages.Color Tag') ?? 'Color Tag' }}</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" name="color" value="#00698f" class="form-control form-control-color rounded-circle border-0 p-0" style="width: 42px; height: 42px; cursor: pointer;">
                                        <span class="text-secondary small">{{ __('messages.pick_event_color') ?? 'Pick color for calendar marker' }}</span>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-dark">{{ __('messages.Description') ?? 'Description' }}</label>
                                    <textarea name="description" rows="3" class="form-control rounded-4 border-secondary-subtle p-3" placeholder="{{ __('messages.Event description and details...') ?? 'Event description...' }}"></textarea>
                                </div>

                                <div class="col-12">
                                    <div class="form-check form-switch mt-1">
                                        <input class="form-check-input" type="checkbox" role="switch" name="is_featured" id="createIsFeatured" value="1">
                                        <label class="form-check-label fw-semibold text-dark" for="createIsFeatured">
                                            ⭐ {{ __('messages.Highlight as Featured Event') ?? 'Highlight as Featured Event' }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top bg-light px-4 py-3 d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.Cancel') ?? 'Cancel' }}</button>
                            <button type="submit" id="createEventSubmitBtn" class="btn btn-primary rounded-pill px-4 fw-semibold d-inline-flex align-items-center gap-1">
                                <i class="bi bi-check-lg"></i>
                                <span>{{ __('messages.save_event') ?? 'Save Event' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit Event Modal --}}
        <div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                    <form id="editEventForm" onsubmit="handleEditEventSubmit(event)">
                        @csrf
                        <input type="hidden" id="editEventId" name="id">
                        <div class="modal-header border-bottom bg-light px-4 py-3">
                            <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="editEventModalLabel">
                                <i class="bi bi-pencil-square text-primary"></i>
                                <span>{{ __('messages.edit_event') ?? 'Edit Event' }}</span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-dark">{{ __('messages.Event Title') ?? 'Event Title' }} <span class="text-danger">*</span></label>
                                    <input type="text" id="editEventTitle" name="title" required class="form-control rounded-pill border-secondary-subtle px-3">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold small text-dark">{{ __('messages.Start Date & Time') ?? 'Start Date & Time' }} <span class="text-danger">*</span></label>
                                    <input type="datetime-local" id="editEventStart" name="start" required class="form-control rounded-pill border-secondary-subtle px-3">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold small text-dark">{{ __('messages.End Date & Time') ?? 'End Date & Time' }} <span class="text-danger">*</span></label>
                                    <input type="datetime-local" id="editEventEnd" name="end" required class="form-control rounded-pill border-secondary-subtle px-3">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold small text-dark">{{ __('messages.Organizer') ?? 'Organizer' }}</label>
                                    <input type="text" id="editEventOrganizer" name="organizer" class="form-control rounded-pill border-secondary-subtle px-3">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold small text-dark">{{ __('messages.Location') ?? 'Location' }}</label>
                                    <input type="text" id="editEventLocation" name="location" class="form-control rounded-pill border-secondary-subtle px-3">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold small text-dark">{{ __('messages.Recurrence') ?? 'Recurrence' }}</label>
                                    <select id="editEventRecurrence" name="recurrence" class="form-select rounded-pill border-secondary-subtle px-3">
                                        <option value="once">{{ __('messages.Once') ?? 'Once' }}</option>
                                        <option value="weekly">{{ __('messages.Weekly') ?? 'Weekly' }}</option>
                                        <option value="monthly">{{ __('messages.Monthly (Same Date)') ?? 'Monthly' }}</option>
                                        <option value="every_two_months">{{ __('messages.Every Two Months') ?? 'Every Two Months' }}</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold small text-dark">{{ __('messages.Color Tag') ?? 'Color Tag' }}</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" id="editEventColor" name="color" value="#00698f" class="form-control form-control-color rounded-circle border-0 p-0" style="width: 42px; height: 42px; cursor: pointer;">
                                        <span class="text-secondary small">{{ __('messages.pick_event_color') ?? 'Pick color for calendar marker' }}</span>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-dark">{{ __('messages.Description') ?? 'Description' }}</label>
                                    <textarea id="editEventDescription" name="description" rows="3" class="form-control rounded-4 border-secondary-subtle p-3"></textarea>
                                </div>

                                <div class="col-12">
                                    <div class="form-check form-switch mt-1">
                                        <input class="form-check-input" type="checkbox" role="switch" name="is_featured" id="editIsFeatured" value="1">
                                        <label class="form-check-label fw-semibold text-dark" for="editIsFeatured">
                                            ⭐ {{ __('messages.Highlight as Featured Event') ?? 'Highlight as Featured Event' }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top bg-light px-4 py-3 d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.Cancel') ?? 'Cancel' }}</button>
                            <button type="submit" id="editEventSubmitBtn" class="btn btn-primary rounded-pill px-4 fw-semibold d-inline-flex align-items-center gap-1">
                                <i class="bi bi-check-lg"></i>
                                <span>{{ __('messages.update_event') ?? 'Update Event' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        .hov-translate {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .hov-translate:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.08) !important;
        }
        #eventsViewTab .nav-link {
            color: #4b5563;
            background-color: transparent;
            border: none;
            font-size: 1rem;
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

        // Event creation submit handler
        async function handleCreateEventSubmit(e) {
            e.preventDefault();
            var form = e.target;
            var submitBtn = document.getElementById('createEventSubmitBtn');
            var originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

            var formData = new FormData(form);
            var payload = {
                title: formData.get('title'),
                start: formData.get('start'),
                end: formData.get('end'),
                organizer: formData.get('organizer'),
                location: formData.get('location'),
                recurrence: [formData.get('recurrence') || 'once'],
                color: formData.get('color') || '#00698f',
                description: formData.get('description'),
                is_featured: formData.get('is_featured') === '1'
            };

            try {
                var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                var res = await fetch("{{ route('web-calendar-events.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token || ''
                    },
                    body: JSON.stringify(payload)
                });

                if (res.ok) {
                    window.location.reload();
                } else {
                    var data = await res.json();
                    alert(data.message || 'Error creating event');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            } catch (err) {
                alert('Connection error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }

        // Event edit helper
        function editEventInBlade(event) {
            var modalEl = document.getElementById('editEventModal');
            if (!modalEl) return;

            document.getElementById('editEventId').value = event.id;
            document.getElementById('editEventTitle').value = event.title || '';

            var formatLocal = function(dt) {
                if (!dt) return '';
                var d = new Date(dt);
                var y = d.getFullYear();
                var m = String(d.getMonth() + 1).padStart(2, '0');
                var day = String(d.getDate()).padStart(2, '0');
                var h = String(d.getHours()).padStart(2, '0');
                var min = String(d.getMinutes()).padStart(2, '0');
                return `${y}-${m}-${day}T${h}:${min}`;
            };

            document.getElementById('editEventStart').value = formatLocal(event.start);
            document.getElementById('editEventEnd').value = formatLocal(event.end);
            document.getElementById('editEventOrganizer').value = event.organizer || '';
            document.getElementById('editEventLocation').value = event.location || '';
            document.getElementById('editEventColor').value = event.color || '#00698f';
            document.getElementById('editEventDescription').value = event.description || '';
            document.getElementById('editIsFeatured').checked = Boolean(event.is_featured);

            if (event.recurrence && Array.isArray(event.recurrence) && event.recurrence.length > 0) {
                document.getElementById('editEventRecurrence').value = event.recurrence[0];
            } else {
                document.getElementById('editEventRecurrence').value = 'once';
            }

            var bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
        }

        // Event edit submit handler
        async function handleEditEventSubmit(e) {
            e.preventDefault();
            var form = e.target;
            var eventId = document.getElementById('editEventId').value;
            var submitBtn = document.getElementById('editEventSubmitBtn');
            var originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';

            var formData = new FormData(form);
            var payload = {
                title: formData.get('title'),
                start: formData.get('start'),
                end: formData.get('end'),
                organizer: formData.get('organizer'),
                location: formData.get('location'),
                recurrence: [formData.get('recurrence') || 'once'],
                color: formData.get('color') || '#00698f',
                description: formData.get('description'),
                is_featured: formData.get('is_featured') === '1'
            };

            try {
                var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                var res = await fetch(`{{ url('/web-calendar-events') }}/${eventId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token || ''
                    },
                    body: JSON.stringify(payload)
                });

                if (res.ok) {
                    window.location.reload();
                } else {
                    var data = await res.json();
                    alert(data.message || 'Error updating event');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            } catch (err) {
                alert('Connection error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }

        // Event delete handler
        async function deleteEventInBlade(eventId) {
            if (!confirm("{{ __('messages.confirm_delete_event') ?? 'Are you sure you want to delete this event?' }}")) return;

            try {
                var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                var res = await fetch(`{{ url('/web-calendar-events') }}/${eventId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token || ''
                    }
                });

                if (res.ok) {
                    window.location.reload();
                } else {
                    var data = await res.json();
                    alert(data.message || 'Error deleting event');
                }
            } catch (err) {
                alert('Connection error');
            }
        }
    </script>
</x-frontend.layout>
