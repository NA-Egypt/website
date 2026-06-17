<div>
    <div class="container min-vh-100 d-flex flex-column justify-content-topcenter align-items-center">
        <div class="w-100" style="max-width: 1140px;">
        <div class="px-2 px-sm-3 py-3 justify-content-center">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 text-center">
                            <h5 class="mb-2 fw-bold text-primary d-flex align-items-center justify-content-center">
                                <i class="fas fa-filter mx-2"></i>{{ __('messages.Filter Options') }}
                                <button type="button" class="btn btn-link text-info p-0 mx-2 text-decoration-none rounded-circle border border-info d-flex align-items-center justify-content-center" id="start-tour-btn" title="{{ __('messages.tour_start') }}" style="width: 24px; height: 24px; border-width: 2px !important;">
                                    <span style="font-size: 14px; font-weight: bold; line-height: 1;">?</span>
                                </button>
                            </h5>
                            <!-- Legend -->
                            <div class="d-flex flex-wrap justify-content-center gap-3 mt-2" style="font-size: 0.85rem; font-weight: 500;">
                                <div class="d-flex align-items-center gap-1">
                                    <span style="width: 12px; height: 12px; border-radius: 50%; background-color: #f43f5e; display: inline-block;"></span>
                                    <span class="text-muted">{{ __('messages.legend_open') }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span style="width: 12px; height: 12px; border-radius: 50%; background-color: #3b82f6; display: inline-block;"></span>
                                    <span class="text-muted">{{ __('messages.legend_closed') }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span style="width: 12px; height: 12px; border-radius: 50%; background-color: #10b981; display: inline-block;"></span>
                                    <span class="text-muted">{{ __('messages.legend_online') }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span style="width: 12px; height: 12px; border-radius: 50%; border: 2px dashed #cbd5e1; display: inline-block;"></span>
                                    <span class="text-muted">{{ __('messages.legend_suspended') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3 p-sm-4">
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
                                    <div class="d-flex bg-light p-1 rounded-3 border align-items-center w-100" style="height: 38px;">
                                        <input type="radio" class="btn-check" name="type" id="type-all" value="" wire:model.live="type" {{ $group ? 'disabled' : '' }}>
                                        <label class="btn btn-sm btn-outline-primary border-0 rounded-2 flex-fill h-100 d-flex align-items-center justify-content-center fw-bold text-nowrap" for="type-all" style="font-size: 0.85rem; cursor: pointer; transition: all 0.2s ease;">
                                            {{ __('messages.all') }}
                                        </label>

                                        <input type="radio" class="btn-check" name="type" id="type-open" value="open" wire:model.live="type" {{ $group ? 'disabled' : '' }}>
                                        <label class="btn btn-sm btn-outline-primary border-0 rounded-2 flex-fill h-100 d-flex align-items-center justify-content-center fw-bold text-nowrap" for="type-open" style="font-size: 0.85rem; cursor: pointer; transition: all 0.2s ease;">
                                            {{ __('messages.open') }} ({{ $openCount }})
                                        </label>

                                        <input type="radio" class="btn-check" name="type" id="type-closed" value="closed" wire:model.live="type" {{ $group ? 'disabled' : '' }}>
                                        <label class="btn btn-sm btn-outline-primary border-0 rounded-2 flex-fill h-100 d-flex align-items-center justify-content-center fw-bold text-nowrap" for="type-closed" style="font-size: 0.85rem; cursor: pointer; transition: all 0.2s ease;">
                                            {{ __('messages.closed') }} ({{ $closedCount }})
                                        </label>
                                    </div>
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
                                            wire:click="toggleVirtualOnly" 
                                            class="btn w-100 rounded-3 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 {{ $virtualOnly ? 'btn-success text-white' : 'btn-outline-success' }}"
                                            style="height: 38px; max-width: 278px;">
                                        <x-fas-video style="width:16px; height:16px;"/>
                                        {{ __('messages.Virtual Meetings Only') ?? 'Virtual Meetings Only' }}
                                    </button>
                                </div>
                                <div class="col-12" wire:key="filter-english-only" id="tour-english-only">
                                    <div class="d-flex align-items-center justify-content-start mb-2 gap-2" style="visibility: hidden;">
                                        <span style="width: 0.5rem; height: 0.5rem; display: inline-block;"></span>
                                        <label class="m-0 p-0">&nbsp;</label>
                                    </div>
                                    <button type="button" 
                                            wire:click="toggleEnglishOnly" 
                                            class="btn w-100 rounded-3 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 {{ $englishOnly ? 'btn-primary text-white' : 'btn-outline-primary' }}"
                                            style="height: 38px; max-width: 278px;">
                                        <x-fas-language style="width:16px; height:16px;"/>
                                        {{ __('messages.English Meetings Only') ?? 'English Meetings Only' }}
                                    </button>
                                </div>
                                <div class="col-12" wire:key="filter-business-meetings-only" id="tour-business-meetings-only">
                                    <div class="d-flex align-items-center justify-content-start mb-2 gap-2" style="visibility: hidden;">
                                        <span style="width: 0.5rem; height: 0.5rem; display: inline-block;"></span>
                                        <label class="m-0 p-0">&nbsp;</label>
                                    </div>
                                    <button type="button" 
                                            wire:click="toggleBusinessMeetingsOnly" 
                                            class="btn w-100 rounded-3 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 {{ $businessMeetingsOnly ? 'btn-warning text-dark' : 'btn-outline-warning' }}"
                                            style="height: 38px; max-width: 278px;">
                                        <x-fas-briefcase style="width:16px; height:16px;"/>
                                        {{ __('messages.Group Business Meetings Only') ?? 'Group Business Meetings Only' }}
                                    </button>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-center align-items-center mt-4 pt-3 border-top" id="tour-clear">
                                <a href="{{ route('frontend.meetings') }}" wire:navigate class="btn btn-danger text-white px-5 rounded-pill fw-bold transition-all hover-scale shadow-sm">
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
                        'search' => $search,
                        'businessMeetingsOnly' => $businessMeetingsOnly
                    ]);
                @endphp
                <div class="d-flex justify-content-center mb-3" id="tour-pdf">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exportWizardModal" style="max-width: 340px; width: 100%; text-align: center;">
                    {{__('messages.downloadmeetingspdf')}}
                    <x-fas-file-pdf style="width:16px; height:16px;"/>
                    </button>
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

    <!-- Export Wizard Modal -->
    <div class="modal fade" id="exportWizardModal" tabindex="-1" aria-labelledby="exportWizardModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content glass-card border-0" style="background: rgba(255, 255, 255, 0.96) !important; backdrop-filter: blur(25px); border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;">
                <div class="modal-body p-0">
                    <livewire:meeting-export-wizard :isModal="true" />
                </div>
            </div>
        </div>
    </div>

    <script>
        function initTour() {
            var startBtn = document.getElementById('start-tour-btn');
            if (!startBtn || startBtn.dataset.tourInitialized) return;
            
            startBtn.dataset.tourInitialized = 'true';
            
            const tourSteps = [
                { popover: { title: '{{ __("messages.tour_filter_options") }}', description: '{{ __("messages.tour_filter_desc") }}' } },
                { element: '#tour-day', popover: { title: '{{ __("messages.tour_day") }}', description: '{{ __("messages.tour_day_desc") }}' } },
                { element: '#tour-group', popover: { title: '{{ __("messages.tour_group") }}', description: '{{ __("messages.tour_group_desc") }}' } },
                { element: '#tour-service-body', popover: { title: '{{ __("messages.tour_service_body") }}', description: '{{ __("messages.tour_service_body_desc") }}' } },
                { element: '#tour-type', popover: { title: '{{ __("messages.tour_type") }}', description: '{{ __("messages.tour_type_desc") }}' } },
                { element: '#tour-city', popover: { title: '{{ __("messages.tour_city") }}', description: '{{ __("messages.tour_city_desc") }}' } },
                { element: '#tour-neighborhood', popover: { title: '{{ __("messages.tour_neighborhood") }}', description: '{{ __("messages.tour_neighborhood_desc") }}' } },
                { element: '#tour-virtual-only', popover: { title: '{{ __("messages.tour_virtual_only") }}', description: '{{ __("messages.tour_virtual_only_desc") }}' } },
                { element: '#tour-english-only', popover: { title: '{{ __("messages.tour_english_only") }}', description: '{{ __("messages.tour_english_only_desc") }}' } },
                { element: '#tour-clear', popover: { title: '{{ __("messages.tour_clear") }}', description: '{{ __("messages.tour_clear_desc") }}' } },
                { element: '#tour-search', popover: { title: '{{ __("messages.tour_search") }}', description: '{{ __("messages.tour_search_desc") }}' } },
                { element: '#tour-pdf', popover: { title: '{{ __("messages.tour_pdf") }}', description: '{{ __("messages.tour_pdf_desc") }}' } },
                { element: '#tour-csv', popover: { title: '{{ __("messages.tour_csv") }}', description: '{{ __("messages.tour_csv_desc") }}' } }
            ];

            if (document.getElementById('tour-meeting-card')) {
                tourSteps.push({
                    element: '#tour-meeting-card',
                    popover: {
                        title: '{{ __("messages.tour_meeting_card") }}',
                        description: '{{ __("messages.tour_meeting_card_desc") }}'
                    }
                });
            }

            const driverObj = window.driver.js.driver({
                showProgress: true,
                animate: true,
                progressText: '{!! __("messages.tour_progress_text") !!}',
                nextBtnText: '{{ __("messages.tour_next") }}',
                prevBtnText: '{{ __("messages.tour_prev") }}',
                doneBtnText: '{{ __("messages.tour_done") }}',
                steps: tourSteps
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
