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

<div>
    <x-backhead>{{ __('messages.Yearly Calendar') }}</x-backhead>

    <div class="glass-card p-4 my-4" wire:ignore>
        <div
            data-vue-app="EventsCalendar"
            data-fetch-url="{{ route('web-calendar-events.index') }}"
            data-store-url="{{ route('web-calendar-events.store') }}"
            data-locale="{{ app()->getLocale() }}"
            @if($canCreateEvent) data-can-create @endif
            @if($canManageAllEvents) data-can-manage @endif
            @if(auth()->check()) data-user-id="{{ auth()->id() }}" @endif
            class="w-100"
        ></div>
    </div>
</div>
