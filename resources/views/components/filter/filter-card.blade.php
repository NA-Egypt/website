@props(['meetings'])

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10">
            
            <!-- Search Input -->
            <input type="search" id="search-input" class="form-control mb-3" placeholder="{{__('messages.Search meetings')}}...">
            
            @foreach ($meetings as $meeting)
                <div class="row bg-white rounded filter-card-border meeting-item mb-3 p-3 d-flex align-items-stretch">
                    
                    <!-- Left Section: Day & Options -->
                    <div class="col-12 col-md-4 d-flex flex-column align-items-md-start align-items-center text-center text-md-start h-100">
                        <p class="digital-clock meeting-start-time">
                            {{ date('h:i a', strtotime($meeting->formatted_start_time)) }}  
                        </p>
                        <p class="duration-clock meeting-end-time">
                            {{ $meeting->duration }}
                        </p>
                        <div class="pt-1">
                            <p>
                                @php
                                    $groupType = $meeting->group->group_type;
                                    $locale = app()->getLocale();
                                    $translatedType = ($groupType === 'فعلي') ? ($locale === 'ar' ? '' : '') : ($locale === 'ar' ? 'اون لاين' : 'Online');
                                @endphp
                                {{ $translatedType }}
                            </p>

                        </div>

                    </div>

                    <!-- Center Section: Group Name & Topic -->
                    <div class="col-12 col-md-4 d-flex flex-column text-center justify-content-center flex-grow-1">
                        <h4 class="font-bold text-xl text-primary meeting-day">
                            {{ app()->getLocale() === 'ar' ? $meeting->group->ar_name : $meeting->group->en_name }}
                        </h4>

                        <p class="text-sm text-gray-400 meeting-topic">{{ __('messages.' . strtolower($meeting->topic->title)) }}</p>
                        <p class="text-sm text-gray-400">{{ $meeting->group->location }}</p>
                        <div class="font-bold text-danger meeting-type">
                            @if ($meeting->type == 'open')
                                {{ __('messages.open (for non addict)') }}
                            @else
                                {{ __('messages.close (only for addict)') }}
                            @endif
                        </div>
                    </div>

                    <!-- Right Section: Timings -->
                    <div class="col-12 col-md-4 d-flex flex-column align-items-md-end align-items-center text-center text-md-end h-100">

                        <p class="font-bold text-xl text-primary meeting-day">
                            {{ app()->getLocale() === 'ar' ? $meeting->day->ar_name : $meeting->day->en_name }}
                        </p>

                        <div class="d-flex flex-wrap gap-1 justify-content-center justify-content-md-end">
                            @foreach ($meeting->options as $option)
                                <x-dashboard.rounded-details class="text-nowrap px-2 py-1">{{ __('messages.' . strtolower($option->name)) }}</x-dashboard.rounded-details>
                            @endforeach
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    </div>
</div>


