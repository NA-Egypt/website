@props(['meetings'])

<div class="meetings-container">
    @foreach ($meetings as $meeting)
        @php
            $isOnline = $meeting->group && 
                        in_array($meeting->group->group_type, ['اونلاين', 'اون لاين', 'online']) &&
                        !(\Illuminate\Support\Str::contains(strtolower($meeting->group->location), ['map', 'goo.gl']));
        @endphp
        <div class="flex gap-3 p-4 bg-white rounded border meeting-item mb-3 {{ $isOnline ? '' : 'border-primary' }}" style="{{ $isOnline ? 'border: 4px solid #10b981 !important;' : '' }}">           
            <div class="flex-1 flex flex-col">
                <div class="d-flex justify-content-between align-items-center" >
                    <p class="font-bold text-xl mt-3 text-primary meeting-day">
                        @if(empty($meeting->recurrence) || in_array('weekly', $meeting->recurrence))
                            {{ app()->getLocale() === 'ar' ? $meeting->day->ar_name : $meeting->day->en_name }}
                        @else
                            {{ $meeting->formatted_recurrence }} - {{ app()->getLocale() === 'ar' ? $meeting->day->ar_name : $meeting->day->en_name }}
                        @endif
                    </p>
                    <p class="text-sm text-gray-400 mt-auto meeting-topic">{{ app()->getLocale() === 'ar' ? $meeting->topic->ar_name : $meeting->topic->en_name }}</p>
                </div>
                <div class="d-flex justify-content-center align-items-center gap-3" >
                    <p class="text-sm text-gray-400 mt-auto meeting-start-time "><span class="text-primary font-bold">{{ __('messages.From') ?? 'From' }}:</span> {{ $meeting->formatted_start_time }} </p>
                    <p class="text-sm text-gray-400 mt-auto meeting-end-time"><span class="text-primary font-bold">{{ __('messages.To') ?? 'To' }}:</span> {{ $meeting->formatted_end_time }}</p>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center" >
                <div class="row g-2">
                    @if($meeting->group && $meeting->group->group_type === 'فعلي')
                        @foreach ($meeting->options as $option)
                            <x-dashboard.rounded-details>{{ app()->getLocale() === 'ar' ? $option->ar_name : $option->en_name }}</x-dashboard.rounded-details>
                        @endforeach
                    @endif
                </div>
                <div class="font-bold text-danger meeting-type" >
                    {{ __('messages.' . strtolower($meeting->type)) == 'messages.' . strtolower($meeting->type) ? $meeting->type : __('messages.' . strtolower($meeting->type)) }}
                </div>
            </div>

        </div>
    @endforeach
</div>