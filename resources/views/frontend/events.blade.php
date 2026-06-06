<x-frontend.layout>
    <x-section-head>{{ __('messages.Events') ?? 'Events' }}</x-section-head>

    <div class="container my-5">
        @if($events->isEmpty())
            <div class="alert alert-info text-center">
                {{ __('messages.No upcoming events.') ?? 'No upcoming events.' }}
            </div>
        @else
            <!-- View Toggle -->
            <ul class="nav nav-pills justify-content-center mb-4 w-100 mx-auto" id="eventsViewTab" role="tablist" dir="ltr" style="max-width: 600px;">
                <li class="nav-item col-6 pe-1" role="presentation">
                    <button class="nav-link active w-100" id="slider-tab" data-bs-toggle="pill" data-bs-target="#slider-view" type="button" role="tab" aria-controls="slider-view" aria-selected="true" style="border-radius: 20px 0 0 20px; border: 1px solid #00698f;">{{ __('messages.Slider View') ?? 'Slider View' }}</button>
                </li>
                <li class="nav-item col-6 ps-1" role="presentation">
                    <button class="nav-link w-100" id="calendar-tab" data-bs-toggle="pill" data-bs-target="#calendar-view" type="button" role="tab" aria-controls="calendar-view" aria-selected="false" style="border-radius: 0 20px 20px 0; border: 1px solid #00698f;">{{ __('messages.Calendar View') ?? 'Calendar View' }}</button>
                </li>
            </ul>

            <div class="tab-content w-100" id="eventsViewTabContent">
                <!-- Slider View -->
                <div class="tab-pane fade show active w-100" id="slider-view" role="tabpanel" aria-labelledby="slider-tab">
                    <div id="eventsCarousel" class="carousel slide w-100" data-bs-ride="false" data-bs-interval="false">
                        <div class="carousel-inner">
                            @foreach($events as $month => $monthEvents)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <div class="px-4 px-md-5 pb-4" style="width: 100%; overflow-x: hidden;">
                                        <div class="text-center mb-4">
                                            <h3 class="fw-bold" style="color: #00698f;">{{ $month }}</h3>
                                        </div>
                                        <div class="row g-4 mx-0">
                                            @foreach($monthEvents as $event)
                                                <div class="col-12 px-2">
                                                    <div class="card shadow-sm border-0 info-card hov-scale w-100">
                                                        <div class="card-body p-3 p-md-4">
                                                            <h4 class="card-title fw-bold d-flex justify-content-between align-items-center flex-wrap gap-2" style="color: {{ $event->color ? $event->color : '#00698f' }};">
                                                                <span>{{ $event->title }}</span>
                                                                @if($event->is_featured)
                                                                    <span class="badge rounded-pill bg-warning text-dark fs-6 px-3 py-1 shadow-sm" style="font-size: 0.8rem !important; font-weight: 600;">
                                                                        ⭐ {{ __('messages.Featured') }}
                                                                    </span>
                                                                @endif
                                                            </h4>
                                                            <h6 class="card-subtitle mb-3 text-muted" style="font-size: 0.9rem;">
                                                                <x-fas-calendar-alt style="width:14px; height:14px;" /> 
                                                                {{ \Carbon\Carbon::parse($event->start)->translatedFormat('M d, Y h:i A') }} - 
                                                                {{ \Carbon\Carbon::parse($event->end)->translatedFormat('M d, Y h:i A') }}
                                                            </h6>
                                                            @if($event->location)
                                                            <h6 class="card-subtitle mb-3 text-muted" style="font-size: 0.9rem;">
                                                                <x-fas-map-marker-alt style="width:14px; height:14px;" /> {{ $event->location }}
                                                            </h6>
                                                            @endif
                                                            @if($event->organizer)
                                                            <h6 class="card-subtitle mb-3 text-muted" style="font-size: 0.9rem;">
                                                                <x-fas-user style="width:14px; height:14px;" /> {{ $event->organizer }}
                                                            </h6>
                                                            @endif
                                                            @if($event->formatted_recurrence && $event->formatted_recurrence !== 'Once' && $event->formatted_recurrence !== __('messages.Once'))
                                                            <h6 class="card-subtitle mb-3 text-muted" style="font-size: 0.9rem;">
                                                                <i class="bi bi-arrow-repeat" style="width:14px; height:14px;"></i> {{ $event->formatted_recurrence }}
                                                            </h6>
                                                            @endif
                                                            <p class="card-text">{{ $event->description }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($events->count() > 1)
                            <!-- Carousel Controls -->
                            <button class="carousel-control-prev" type="button" data-bs-target="#eventsCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon shadow-sm" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#eventsCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon shadow-sm" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Calendar View -->
                <div class="tab-pane fade w-100" id="calendar-view" role="tabpanel" aria-labelledby="calendar-tab">
                    <div id="frontend-calendar" class="p-2 p-md-3 bg-white rounded shadow-sm border border-light w-100" style="min-height: 500px;"></div>
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
            color: #00698f;
            background-color: transparent;
        }
        #eventsViewTab .nav-link.active {
            color: #fff;
            background-color: #00698f;
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
            var calendarEl = document.getElementById('frontend-calendar');
            var calendarTab = document.getElementById('calendar-tab');
            var calendarInitialized = false;
            var calendar;

            if(calendarTab && calendarEl && window.FullCalendar) {
                calendarTab.addEventListener('shown.bs.tab', function (event) {
                    if (!calendarInitialized) {
                        calendar = new window.FullCalendar.Calendar(calendarEl, {
                            plugins: [
                                window.FullCalendar.dayGridPlugin,
                                window.FullCalendar.timeGridPlugin,
                                window.FullCalendar.interactionPlugin,
                                window.FullCalendar.multiMonthPlugin
                            ],
                            initialView: 'dayGridMonth',
                            headerToolbar: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'dayGridMonth,timeGridWeek'
                            },
                            locale: '{{ app()->getLocale() }}',
                            direction: '{{ app()->getLocale() === "ar" ? "rtl" : "ltr" }}',
                            buttonText: {
                                today: '{{ app()->getLocale() === "ar" ? "اليوم" : "Today" }}',
                                month: '{{ app()->getLocale() === "ar" ? "شهر" : "Month" }}',
                                week: '{{ app()->getLocale() === "ar" ? "أسبوع" : "Week" }}',
                                day: '{{ app()->getLocale() === "ar" ? "يوم" : "Day" }}'
                            },
                            events: {!! $allEventsJSON ?? "[]" !!},
                            eventClick: function(info) {
                                var event = info.event;
                                var color = event.extendedProps.color || '#00698f';
                                
                                document.getElementById('modalEventTitle').textContent = event.title;
                                document.getElementById('modalEventTitle').style.color = color;
                                
                                var formatOptions = { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' };
                                var timeStr = calendar.formatDate(event.start, formatOptions);
                                if (event.end) {
                                    timeStr += ' - ' + calendar.formatDate(event.end, formatOptions);
                                }
                                
                                document.getElementById('modalEventTime').textContent = timeStr;
                                document.getElementById('modalEventDescription').textContent = event.extendedProps.description || '';
                                
                                var location = event.extendedProps.location;
                                var organizer = event.extendedProps.organizer;

                                if (location) {
                                    document.getElementById('modalEventLocation').textContent = location;
                                    document.getElementById('modalEventLocationContainer').classList.remove('d-none');
                                } else {
                                    document.getElementById('modalEventLocationContainer').classList.add('d-none');
                                }

                                if (organizer) {
                                    document.getElementById('modalEventOrganizer').textContent = organizer;
                                    document.getElementById('modalEventOrganizerContainer').classList.remove('d-none');
                                } else {
                                    document.getElementById('modalEventOrganizerContainer').classList.add('d-none');
                                }

                                var recurrence = event.extendedProps.recurrence;
                                if (recurrence && recurrence !== 'Once' && recurrence !== '{{ __("messages.Once") }}') {
                                    document.getElementById('modalEventRecurrence').textContent = recurrence;
                                    document.getElementById('modalEventRecurrenceContainer').classList.remove('d-none');
                                } else {
                                    document.getElementById('modalEventRecurrenceContainer').classList.add('d-none');
                                }

                                var modal = new bootstrap.Modal(document.getElementById('calendarEventModal'));
                                modal.show();
                            }
                        });
                        calendar.render();
                        calendarInitialized = true;
                    }
                });
            }
        });
    </script>
</x-frontend.layout>
