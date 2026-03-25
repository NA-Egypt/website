<x-frontend.layout>
    <x-section-head>{{ __('messages.Events') ?? 'Events' }}</x-section-head>

    <div class="container my-5">
        @if($events->isEmpty())
            <div class="alert alert-info text-center">
                {{ __('messages.No upcoming events.') ?? 'No upcoming events.' }}
            </div>
        @else
            <div class="row g-4">
                @foreach($events as $event)
                    <div class="col-12">
                        <div class="card shadow-sm border-0 info-card hov-scale">
                            <div class="card-body p-4">
                                <h4 class="card-title fw-bold" style="color: {{ $event->color ? $event->color : '#00698f' }};">
                                    {{ $event->title }}
                                </h4>
                                <h6 class="card-subtitle mb-3 text-muted">
                                    <x-fas-calendar-alt style="width:14px; height:14px;" /> 
                                    {{ \Carbon\Carbon::parse($event->start)->format('M d, Y h:i A') }} - 
                                    {{ \Carbon\Carbon::parse($event->end)->format('M d, Y h:i A') }}
                                </h6>
                                <p class="card-text">{{ $event->description }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <style>
        .hov-scale {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hov-scale:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
    </style>
</x-frontend.layout>
