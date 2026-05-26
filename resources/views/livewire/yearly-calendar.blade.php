<div>
    <x-backhead>{{ __('messages.Yearly Calendar') }}</x-backhead>

    <div class="glass-card p-4 my-4" wire:ignore>
        <div id="calendar-component" x-data="{ open: false }">
            <div id="calendar" class="w-100" style="min-height: 700px;"></div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">{{ $eventId ? __('messages.Edit Event') : __('messages.Add Event') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="$set('eventId', null)"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveEvent">
                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('messages.Event Title') }}</label>
                            <input type="text" class="form-control" id="title" wire:model="title" required>
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="start" class="form-label">{{ __('messages.Start Date') }}</label>
                            <input type="datetime-local" class="form-control" id="start" wire:model="start" required>
                            @error('start') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="end" class="form-label">{{ __('messages.End Date') }}</label>
                            <input type="datetime-local" class="form-control" id="end" wire:model="end" required>
                            @error('end') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('messages.Description') }}</label>
                            <textarea class="form-control" id="description" wire:model="description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="organizer" class="form-label">{{ __('messages.Organizer') }}</label>
                            <input type="text" class="form-control" id="organizer" wire:model="organizer">
                            @error('organizer') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">{{ __('messages.Location') }}</label>
                            <input type="text" class="form-control" id="location" wire:model="location">
                            @error('location') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.Recurrence') }}</label>
                            <div class="d-flex flex-wrap gap-2">
                                @php
                                    $recurrenceOptions = [
                                        'once' => __('messages.Once'),
                                        'weekly' => __('messages.Weekly'),
                                        'monthly' => __('messages.Monthly (Same Date)'),
                                        'every_two_months' => __('messages.Every Two Months'),
                                        '1st' => __('messages.1st'),
                                        '2nd' => __('messages.2nd'),
                                        '3rd' => __('messages.3rd'),
                                        '4th' => __('messages.4th'),
                                        '5th' => __('messages.5th'),
                                        'last' => __('messages.last'),
                                    ];
                                @endphp
                                @foreach($recurrenceOptions as $val => $label)
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input" type="checkbox" wire:model="recurrence" id="rec-{{ $val }}" value="{{ $val }}">
                                        <label class="form-check-label" for="rec-{{ $val }}">
                                            {{ $label }}
                                            @if(in_array($val, ['1st', '2nd', '3rd', '4th', '5th', 'last']))
                                                <span class="dynamic-weekday fw-bold text-primary"></span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="color" class="form-label">{{ __('messages.Color') }}</label>
                            <input type="color" class="form-control form-control-color" id="color" wire:model="color" title="Choose your color">
                        </div>
                        <div class="d-flex justify-content-between">
                            @if($eventId)
                                <button type="button" wire:click="deleteEvent" class="btn btn-danger" onclick="return confirm('{{ __('messages.Are you sure you want to delete this event?') }}')">{{ __('messages.Delete') }}</button>
                            @else
                                <div></div>
                            @endif
                            <button type="submit" class="btn btn-primary">{{ __('messages.Save Event') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function tryInitCalendar() {
            if (!window.FullCalendar || !window.Livewire) {
                setTimeout(tryInitCalendar, 50);
                return;
            }

            var calendarEl = document.getElementById('calendar');
            if (!calendarEl || calendarEl.dataset.initialized) {
                return;
            }
            calendarEl.dataset.initialized = 'true';

            // Injected from Blade for client-side check
            const canManage = <?php echo json_encode(auth()->check() && (auth()->user()->hasPermissionTo('can_manage_calendar') || auth()->user()->hasRole('super admin') || auth()->user()->hasRole('Committees') || auth()->user()->hasRole('ServiceBody') || in_array(strtolower(auth()->user()->email ?? ''), ['rsc@naegypt.org', 'arsc@naegypt.org', 'rcp@naegypt.org', 'rvcp@naegypt.org']))); ?>;

            var calendar = new window.FullCalendar.Calendar(calendarEl, {
                plugins: [
                    window.FullCalendar.dayGridPlugin,
                    window.FullCalendar.timeGridPlugin,
                    window.FullCalendar.interactionPlugin,
                    window.FullCalendar.multiMonthPlugin
                ],
                initialView: 'multiMonthYear',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'multiMonthYear,dayGridMonth,timeGridWeek'
                },
                locale: '{{ app()->getLocale() }}',
                direction: '{{ app()->getLocale() === "ar" ? "rtl" : "ltr" }}',
                selectable: true,
                editable: false, // Start false, simpler
                events: @json($events), // Initial load
                
                select: function(info) {
                    if (info.jsEvent && info.jsEvent.target.closest('.fc-event')) {
                        return;
                    }
                    if (canManage) {
                        @this.call('selectDateRange', info.startStr, info.endStr);
                        setTimeout(updateDynamicWeekdays, 500);
                    }
                },

                eventClick: function(info) {
                    if (canManage) {
                        @this.call('editEvent', info.event.id);
                        setTimeout(updateDynamicWeekdays, 500); // Wait for Livewire to populate the modal, then update
                    }
                }
            });
            calendar.render();

            // Function to update the 1st, 2nd, etc. labels with the actual weekday
            function updateDynamicWeekdays() {
                var startInput = document.getElementById('start');
                if (!startInput || !startInput.value) return;

                var date = new Date(startInput.value);
                if (isNaN(date.getTime())) return;

                var weekdays = [
                    '{{ __("messages.sunday") }}', 
                    '{{ __("messages.monday") }}', 
                    '{{ __("messages.tuesday") }}', 
                    '{{ __("messages.wednesday") }}', 
                    '{{ __("messages.thursday") }}', 
                    '{{ __("messages.friday") }}', 
                    '{{ __("messages.saturday") }}'
                ];
                var weekdayName = weekdays[date.getDay()];

                var spans = document.querySelectorAll('.dynamic-weekday');
                spans.forEach(function(span) {
                    span.textContent = weekdayName;
                });
            }

            // Update weekday labels when the user changes the start date
            var startInputEl = document.getElementById('start');
            if(startInputEl) {
                startInputEl.addEventListener('input', updateDynamicWeekdays);
                startInputEl.addEventListener('change', updateDynamicWeekdays);
            }

            if (!window.yearlyCalendarListenersAttached) {
                window.yearlyCalendarListenersAttached = true;

                window.addEventListener('event-saved', () => {
                    var modalEl = document.getElementById('eventModal');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if(modal) {
                        modal.hide();
                    }
                    location.reload(); 
                });

                window.addEventListener('open-modal', () => {
                    var modalEl = document.getElementById('eventModal');
                    var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modal.show();
                });
            }

            var modalEl = document.getElementById('eventModal');
            if (modalEl) {
                modalEl.addEventListener('hidden.bs.modal', function () {
                    @this.call('resetModal');
                });
            }
        }

        // Initialize safely and resiliently
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', tryInitCalendar);
        } else {
            tryInitCalendar();
        }
        document.addEventListener('livewire:initialized', tryInitCalendar);
        document.addEventListener('livewire:navigated', tryInitCalendar);
    </script>
</div>

