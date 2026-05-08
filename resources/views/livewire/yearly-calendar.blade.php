<div>
    <div
        x-data="{
            calendar: null,
            initCalendar() {
                // This initCalendar function is now empty or removed as the calendar initialization moves to the script block.
                // The calendar will be initialized in the livewire:initialized event listener.
            }
        }"
        x-init="initCalendar()"
        wire:ignore
    >
        <div id="calendar-component" x-data="{ open: false }">
        <div id="calendar"></div>
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
        document.addEventListener('livewire:initialized', () => {
             // Injected from Blade for client-side check
            const canManage = @json(auth()->check() && (auth()->user()->hasPermissionTo('can_manage_calendar') || auth()->user()->hasRole(['super admin', 'Committees'])));

            var calendarEl = document.getElementById('calendar');
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
                direction: '{{ $direction ?? "ltr" }}',
                selectable: true,
                editable: false, // Start false, simpler
                events: @json($events), // Initial load
                
                select: function(info) {
                    if (canManage) {
                        @this.set('eventId', null);
                        // Set values directly to inputs to avoid network roundtrip of @this.set
                        let startInput = document.getElementById('start');
                        let endInput = document.getElementById('end');
                        
                        if(startInput && endInput) {
                            startInput.value = info.startStr + "T09:00"; // formatting might be needed depending on input type datetime-local
                            // FullCalendar returns YYYY-MM-DD for all day, but input is datetime-local. 
                            // If it's just date, we might need to append time.
                            // Let's check input type. It is datetime-local.
                            // info.startStr is ISO. If view is month, it's just date.
                            
                            let startVal = info.startStr;
                            if(startVal.indexOf('T') === -1) startVal += 'T09:00';
                            
                            let endVal = info.endStr;
                            if(endVal.indexOf('T') === -1) endVal += 'T10:00';
                            
                            startInput.value = startVal;
                            startInput.dispatchEvent(new Event('input'));
                            
                            endInput.value = endVal;
                            endInput.dispatchEvent(new Event('input'));
                        }
                        
                        var modal = new bootstrap.Modal(document.getElementById('eventModal'));
                        modal.show();
                        updateDynamicWeekdays(); // Update the labels when modal opens
                    } else {
                        // Optional: silent or alert
                        // alert('You are not authorized to add events.');
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

            @this.on('event-saved', (event) => {
                var modalEl = document.getElementById('eventModal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                if(modal) {
                    modal.hide();
                }
                location.reload(); 
            });

            @this.on('open-modal', () => {
                var modal = new bootstrap.Modal(document.getElementById('eventModal'));
                modal.show();
            });
        });
    </script>
</div>
