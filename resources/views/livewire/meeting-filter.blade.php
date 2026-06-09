<div>
    <div class="container min-vh-100 d-flex flex-column justify-content-topcenter align-items-center">
        <div class="w-100" style="max-width: 1140px;">
        <div class="container px-4 py-3 justify-content-center">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 text-center">
                            <h5 class="mb-0 fw-bold text-primary d-flex align-items-center justify-content-center">
                                <i class="fas fa-filter mx-2"></i>{{ __('messages.Filter Options') }}
                                <button type="button" class="btn btn-link text-info p-0 mx-2 text-decoration-none rounded-circle border border-info d-flex align-items-center justify-content-center" id="start-tour-btn" title="{{ __('messages.tour_start') }}" style="width: 24px; height: 24px; border-width: 2px !important;">
                                    <span style="font-size: 14px; font-weight: bold; line-height: 1;">?</span>
                                </button>
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                <div class="col-12" wire:key="filter-day" id="tour-day">
                                    <x-filter.select :options="$days" name="day" wire:model.live="day" class="form-select form-control" label="{{ __('messages.Day') }}" />
                                </div>
                                <div class="col-12" wire:key="filter-group" id="tour-group">
                                    <x-filter.select :options="$groups" name="group" wire:model.live="group" class="form-select form-control" label="{{__('messages.Group')}}" :disabled="!!$serviceBody || !!$city || !!$neighborhood" />
                                </div>
                                <div class="col-12" wire:key="filter-serviceBody" id="tour-service-body">
                                    <x-filter.select :options="$serviceBodies" name="serviceBody" wire:model.live="serviceBody" class="form-select form-control" label="{{__('messages.Service Body')}}" :disabled="!!$group || !!$city || !!$neighborhood" />
                                </div>
                                <div class="col-12" wire:key="filter-type" id="tour-type">
                                    <x-forms.label name="type" label="{{__('messages.Type')}}" />
                                    <select name="type" wire:model.live="type" class="form-select form-control" {{ $group ? 'disabled' : '' }}>
                                        <option value="">{{__('messages.Choose Type')}}</option>
                                        <option value="open">{{ __('messages.open') }} ({{ $openCount }})</option>
                                        <option value="closed">{{ __('messages.closed') }} ({{ $closedCount }})</option>
                                    </select>
                                </div>
                                <div class="col-12" wire:key="filter-city" id="tour-city">
                                    <x-filter.select :options="$cities" name="city" wire:model.live="city" class="form-select form-control" label="{{__('messages.City')}}" :disabled="!!$serviceBody || !!$group" />
                                </div>
                                <div class="col-12" wire:key="filter-neighborhood" id="tour-neighborhood">
                                    <x-filter.select :options="$neighborhoods" name="neighborhood" wire:model.live="neighborhood" class="form-select form-control" label="{{__('messages.Neighborhood')}}" :disabled="!!$serviceBody || !!$group" />
                                </div>
                                <div class="col-12" wire:key="filter-virtual-only" id="tour-virtual-only">
                                    <div class="d-flex align-items-center justify-content-start mb-2 gap-2" style="visibility: hidden;">
                                        <span style="width: 0.5rem; height: 0.5rem; display: inline-block;"></span>
                                        <label class="m-0 p-0">&nbsp;</label>
                                    </div>
                                    <button type="button" 
                                            wire:click="$set('virtualOnly', {{ $virtualOnly ? 'false' : 'true' }})" 
                                            class="btn w-100 rounded-3 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 {{ $virtualOnly ? 'btn-success text-white' : 'btn-outline-success' }}"
                                            style="height: 38px; max-width: 278px;">
                                        <x-fas-video style="width:16px; height:16px;"/>
                                        {{ __('messages.Virtual Meetings Only') ?? 'Virtual Meetings Only' }}
                                    </button>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-center align-items-center mt-4 pt-3 border-top" id="tour-clear">
                                <a href="{{ route('frontend.meetings') }}" wire:navigate class="btn btn-light text-danger px-4 rounded-pill fw-medium transition-all hover-scale">
                                    <i class="fas fa-times me-1"></i> {{__('messages.Clear Filters')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            @if($meetings->isEmpty())
            <div class="row justify-content-center mt-4">
                <div class="col-auto">
                    <p class="text-center text-muted">{{ __('messages.No meetings found') }}</p>
                </div>
            </div>
            @else
            <div class="row justify-content-center">
                @php
                   $exportParams = array_filter([
                       'day' => $day, 
                       'city' => $city, 
                       'neighborhood' => $neighborhood, 
                       'group' => $group, 
                       'serviceBody' => $serviceBody, 
                       'type' => $type, 
                       'search' => $search
                   ]);
                @endphp
                <div class="d-flex justify-content-center mb-3" id="tour-pdf">
                    <a href="{{ route('exportMeetingsToPDF', $exportParams) }}" target="_blank" class="btn btn-primary" style="max-width: 340px; width: 100%; text-align: center;">
                    {{__('messages.downloadmeetingspdf')}}
                    <x-fas-file-pdf style="width:16px; height:16px;"/>
                    </a>
                </div>
                <div class="d-flex justify-content-center mb-3" id="tour-csv">
                    <a href="{{ route('exportMeetingsToCSV', $exportParams) }}" target="_blank" class="btn btn-primary" style="max-width: 340px; width: 100%; text-align: center;">
                    {{__('messages.downloadmeetingscsv')}}
                    <x-fas-file-csv style="width:16px; height:16px;"/>
                    </a>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="d-flex justify-content-center mb-3">
                    <div class="position-relative" style="max-width: 340px; width: 100%;" id="tour-search">
                        <input type="search" wire:model.live.debounce.300ms="search" class="form-control ps-5" placeholder="{{ __('messages.Search meetings') }}...">
                        <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted">
                            <x-fas-search style="width:14px; height:14px;" />
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="position-relative mt-4">
                <div wire:loading.delay.longest class="position-absolute w-100 h-100 top-0 start-0 mt-5" style="z-index: 10; background: rgba(255,255,255,0.7);">
                    <div class="d-flex justify-content-center align-items-center h-100 flex-column pb-5">
                       <span class="spinner-border text-primary" role="status"></span>
                       <span class="mt-2 text-primary fw-bold">Loading...</span>
                    </div>
                </div>
                
                <x-filter.filter-card :$meetings />
            </div>
            @endif
        </div>
    </div>

    <script>
        function initTour() {
            var startBtn = document.getElementById('start-tour-btn');
            if (!startBtn || startBtn.dataset.tourInitialized) return;
            
            startBtn.dataset.tourInitialized = 'true';
            
            const driverObj = window.driver.js.driver({
                showProgress: true,
                animate: true,
                nextBtnText: '{{ __("messages.tour_next") }}',
                prevBtnText: '{{ __("messages.tour_prev") }}',
                doneBtnText: '{{ __("messages.tour_done") }}',
                steps: [
                    { popover: { title: '{{ __("messages.tour_filter_options") }}', description: '{{ __("messages.tour_filter_desc") }}' } },
                    { element: '#tour-day', popover: { title: '{{ __("messages.tour_day") }}', description: '{{ __("messages.tour_day_desc") }}' } },
                    { element: '#tour-group', popover: { title: '{{ __("messages.tour_group") }}', description: '{{ __("messages.tour_group_desc") }}' } },
                    { element: '#tour-service-body', popover: { title: '{{ __("messages.tour_service_body") }}', description: '{{ __("messages.tour_service_body_desc") }}' } },
                    { element: '#tour-type', popover: { title: '{{ __("messages.tour_type") }}', description: '{{ __("messages.tour_type_desc") }}' } },
                    { element: '#tour-city', popover: { title: '{{ __("messages.tour_city") }}', description: '{{ __("messages.tour_city_desc") }}' } },
                    { element: '#tour-neighborhood', popover: { title: '{{ __("messages.tour_neighborhood") }}', description: '{{ __("messages.tour_neighborhood_desc") }}' } },
                    { element: '#tour-virtual-only', popover: { title: '{{ __("messages.tour_virtual_only") }}', description: '{{ __("messages.tour_virtual_only_desc") }}' } },
                    { element: '#tour-clear', popover: { title: '{{ __("messages.tour_clear") }}', description: '{{ __("messages.tour_clear_desc") }}' } },
                    { element: '#tour-search', popover: { title: '{{ __("messages.tour_search") }}', description: '{{ __("messages.tour_search_desc") }}' } },
                    { element: '#tour-pdf', popover: { title: '{{ __("messages.tour_pdf") }}', description: '{{ __("messages.tour_pdf_desc") }}' } },
                    { element: '#tour-csv', popover: { title: '{{ __("messages.tour_csv") }}', description: '{{ __("messages.tour_csv_desc") }}' } }
                ]
            });

            startBtn.addEventListener('click', function() {
                driverObj.drive();
            });
        }
        
        document.addEventListener('livewire:navigated', initTour);
        document.addEventListener('DOMContentLoaded', initTour);
        // Fallback for dynamic updates
        if(document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(initTour, 100);
        }
    </script>
</div>
