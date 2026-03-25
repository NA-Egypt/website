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
                    <h5 class="modal-title" id="eventModalLabel">{{ $eventId ? 'Edit Event' : 'Add Event' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="$set('eventId', null)"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveEvent">
                        <div class="mb-3">
                            <label for="title" class="form-label">Event Title</label>
                            <input type="text" class="form-control" id="title" wire:model="title" required>
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="start" class="form-label">Start Date</label>
                            <input type="datetime-local" class="form-control" id="start" wire:model="start" required>
                            @error('start') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="end" class="form-label">End Date</label>
                            <input type="datetime-local" class="form-control" id="end" wire:model="end" required>
                            @error('end') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" wire:model="description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="organizer" class="form-label">Organizer</label>
                            <input type="text" class="form-control" id="organizer" wire:model="organizer">
                            @error('organizer') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" wire:model="location">
                            @error('location') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="color" class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" id="color" wire:model="color" title="Choose your color">
                        </div>
                        <div class="d-flex justify-content-between">
                            @if($eventId)
                                <button type="button" wire:click="deleteEvent" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this event?')">Delete</button>
                            @else
                                <div></div>
                            @endif
                            <button type="submit" class="btn btn-primary">Save Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
             // Injected from Blade for client-side check
            const canManage = @json(auth()->check() && (auth()->user()->hasPermission('can_manage_calendar') || auth()->user()->hasRole('super admin')));

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
                    } else {
                        // Optional: silent or alert
                        // alert('You are not authorized to add events.');
                    }
                },

                eventClick: function(info) {
                    if (canManage) {
                        @this.call('editEvent', info.event.id);
                    }
                }
            });
            calendar.render();

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
