<div>
    <x-backhead>{{ __('messages.Yearly Calendar') }}</x-backhead>

    <div class="glass-card p-4 my-4" wire:ignore>
        <div
            data-vue-app="EventsCalendar"
            data-fetch-url="{{ route('web-calendar-events.index') }}"
            data-store-url="{{ route('web-calendar-events.store') }}"
            data-locale="{{ app()->getLocale() }}"
            data-can-manage
            class="w-100"
        ></div>
    </div>
</div>
