@props(['meetings'])

<div class="meetings-container">
    @foreach ($meetings as $meeting)
        <div class="flex gap-3 p-4 bg-white rounded  border border-primary meeting-item mb-3">           
            <div class="flex-1 flex flex-col">
                <div class="d-flex justify-content-between align-items-center" >
                    <p class="font-bold text-xl mt-3 text-primary meeting-day">{{ $meeting->formatted_recurrence }} - {{ __('messages.' . strtolower($meeting->day->name)) }}</p>
                    <p class="text-sm text-gray-400 mt-auto meeting-topic">{{ __('messages.' . strtolower($meeting->topic->title)) }}</p>
                </div>
                <div class="d-flex justify-content-center align-items-center gap-3" >
                    <p class="text-sm text-gray-400 mt-auto meeting-start-time "><span class="text-primary font-bold">From:</span> {{ $meeting->formatted_start_time }} </p>
                    <p class="text-sm text-gray-400 mt-auto meeting-end-time"><span class="text-primary font-bold">To:</span> {{ $meeting->formatted_end_time }}</p>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center" >
                <div class="row g-2">
                    @foreach ($meeting->options as $option)
                        <x-dashboard.rounded-details>{{$option->name}}</x-dashboard.rounded-details>
                    @endforeach
                </div>
                <div class="font-bold text-danger meeting-type" >
                    {{$meeting->type}}
                </div>
            </div>

        </div>
    @endforeach
</div>