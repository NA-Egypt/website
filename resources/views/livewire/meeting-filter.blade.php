<div>
    <div class="w-100 min-vh-100 d-flex flex-column justify-content-start align-items-center py-3">
        <div class="container-fluid px-2 px-sm-3" style="max-width: 1140px; width: 100%;">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12" x-data="{ open: true }">
                    <!-- Toggle Filters Button -->
                    <div class="d-flex justify-content-center mb-3">
                        <button @click="open = !open" type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2 shadow-sm transition-all hover-scale">
                            <x-fas-filter style="width:16px; height:16px;"/>
                            <span>{{ __('messages.Toggle Filters') }}</span>
                            <i class="fas ms-1" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'" style="font-size: 0.8rem;"></i>
                        </button>
                    </div>

                    <!-- Glassmorphic Filters Card -->
                    <div x-show="open" x-transition.duration.300ms class="card mb-4 border-0 shadow-lg rounded-4 overflow-hidden position-relative" style="background: rgba(255, 255, 255, 0.8) !important; backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.4) !important;">
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
                                <div class="d-flex align-items-center gap-1">
                                    <span style="width: 12px; height: 12px; border-radius: 50%; background-color: #ffc107; display: inline-block;"></span>
                                    <span class="text-muted">{{ __('messages.legend_business') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3 p-sm-4">
                            <!-- Days Segmented Bar Selector -->
                            <div class="bg-light p-2 rounded-4 border mb-4 w-100 overflow-hidden" wire:key="filter-day" id="tour-day">
                                <div class="d-flex flex-wrap gap-2 justify-content-start align-items-center">
                                    <input type="radio" class="btn-check" name="day" id="day-all" value="" wire:model.live="day">
                                    <label class="btn btn-sm btn-outline-primary border-0 rounded-pill px-2 py-1 fw-bold text-nowrap d-flex align-items-center" for="day-all" style="font-size: 0.75rem; cursor: pointer; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);">
                                        <x-fas-calendar style="width:14px; height:14px;" class="me-1"/> {{ __('messages.All Days') }}
                                    </label>

                                    @foreach($days as $d)
                                        @php
                                            $dayName = app()->getLocale() === 'ar' ? $d->ar_name : $d->en_name;
                                        @endphp
                                        <input type="radio" class="btn-check" name="day" id="day-{{ $d->id }}" value="{{ $dayName }}" wire:model.live="day">
                                        <label class="btn btn-sm btn-outline-primary border-0 rounded-pill px-2 py-1 fw-bold text-nowrap" for="day-{{ $d->id }}" style="font-size: 0.75rem; cursor: pointer; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);">
                                            {{ $dayName }} ({{ $d->meetings_count }})
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <style>
                                .no-scrollbar::-webkit-scrollbar {
                                    display: none;
                                }
                                .btn-check + .btn-outline-primary {
                                    border: 1px solid rgba(0,0,0,0.05) !important;
                                }
                                .btn-check:not(:checked) + .btn-outline-primary {
                                    background-color: #f8fafc !important;
                                    color: #475569 !important;
                                }
                                .btn-check:checked + .btn-outline-primary {
                                    background-color: var(--bs-primary) !important;
                                    color: #ffffff !important;
                                    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25) !important;
                                    transform: translateY(-1px);
                                }
                                .btn-check + .btn-outline-primary:hover {
                                    background-color: #f1f5f9 !important;
                                    color: var(--bs-primary) !important;
                                    transform: translateY(-1px);
                                }
                            </style>

                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                <div class="col" wire:key="filter-group" id="tour-group">
                                    <x-filter.select :options="$groups" name="group" wire:model.live="group" class="form-select form-control" label="{{__('messages.Group')}}" :disabled="!!$serviceBody || !!$city || !!$neighborhood" />
                                </div>
                                <div class="col" wire:key="filter-serviceBody" id="tour-service-body">
                                    <x-filter.select :options="$serviceBodies" name="serviceBody" wire:model.live="serviceBody" class="form-select form-control" label="{{__('messages.Service Body')}}" :disabled="!!$group || !!$city || !!$neighborhood" />
                                </div>
                                <div class="col" wire:key="filter-city" id="tour-city">
                                    <x-filter.select :options="$cities" name="city" wire:model.live="city" class="form-select form-control" label="{{__('messages.City')}}" :disabled="!!$serviceBody || !!$group" />
                                </div>
                                <div class="col" wire:key="filter-neighborhood" id="tour-neighborhood">
                                    <x-filter.select :options="$neighborhoods" name="neighborhood" wire:model.live="neighborhood" class="form-select form-control" label="{{__('messages.Neighborhood')}}" :disabled="!!$serviceBody || !!$group" />
                                </div>
                                <div class="col" wire:key="filter-type" id="tour-type">
                                    <div class="d-none d-md-flex align-items-center justify-content-start mb-2 gap-2" style="visibility: hidden; height: 19px;">
                                        <label class="m-0 p-0">&nbsp;</label>
                                    </div>
                                    <div class="d-flex flex-nowrap bg-light p-1 rounded-4 border align-items-center w-100 gap-1">
                                        <input type="radio" class="btn-check" name="type" id="type-all" value="" wire:model.live="type" {{ $group ? 'disabled' : '' }}>
                                        <label class="btn btn-sm btn-outline-primary border-0 rounded-pill px-2 py-1 fw-bold text-nowrap flex-fill text-center" for="type-all" style="font-size: 0.75rem; cursor: pointer; transition: all 0.2s ease;">
                                            {{ __('messages.all') }}
                                        </label>

                                        <input type="radio" class="btn-check" name="type" id="type-open" value="open" wire:model.live="type" {{ $group ? 'disabled' : '' }}>
                                        <label class="btn btn-sm btn-outline-primary border-0 rounded-pill px-2 py-1 fw-bold text-nowrap flex-fill text-center" for="type-open" style="font-size: 0.75rem; cursor: pointer; transition: all 0.2s ease;">
                                            {{ __('messages.open') }} ({{ $openCount }})
                                        </label>

                                        <input type="radio" class="btn-check" name="type" id="type-closed" value="closed" wire:model.live="type" {{ $group ? 'disabled' : '' }}>
                                        <label class="btn btn-sm btn-outline-primary border-0 rounded-pill px-2 py-1 fw-bold text-nowrap flex-fill text-center" for="type-closed" style="font-size: 0.75rem; cursor: pointer; transition: all 0.2s ease;">
                                            {{ __('messages.closed') }} ({{ $closedCount }})
                                        </label>
                                    </div>
                                </div>
                                <div class="col" wire:key="filter-recurrence" id="tour-recurrence">
                                    <x-filter.select :options="$recurrences" name="recurrence" wire:model.live="recurrence" class="form-select form-control" label="{{__('messages.Recurrence')}}" />
                                </div>
                            </div>

                            @php
                                $hasActiveFilters = !empty($day) || !empty($group) || !empty($serviceBody) || !empty($type) || !empty($city) || !empty($neighborhood) || !empty($search) || $virtualOnly || $englishOnly || $businessMeetingsOnly || ($recurrence !== 'weekly' && !empty($recurrence));
                            @endphp

                            @if($hasActiveFilters)
                                <div class="mt-4 w-100" id="active-filters-container">
                                    <div class="d-flex flex-wrap align-items-center gap-2 bg-light p-3 rounded-4 border">
                                        @if($recurrence !== 'weekly' && !empty($recurrence))
                                            <span class="badge bg-primary text-white d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem; font-weight: 600;">
                                                @php
                                                    $selectedOpt = $recurrences->firstWhere('id', $recurrence);
                                                    $field = app()->getLocale() === 'ar' ? 'ar_name' : 'en_name';
                                                    $displayName = $selectedOpt ? $selectedOpt->$field : $recurrence;
                                                @endphp
                                                <span>{{ $displayName }}</span>
                                                <button type="button" wire:click="$set('recurrence', 'weekly')" class="btn-close btn-close-white p-0 m-0" style="font-size: 0.65rem; width: 0.5rem; height: 0.5rem;" aria-label="Reset Recurrence"></button>
                                            </span>
                                        @endif

                                        @if(!empty($day))
                                            <span class="badge bg-primary text-white d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem; font-weight: 600;">
                                                <span>{{ $day }}</span>
                                                <button type="button" wire:click="$set('day', '')" class="btn-close btn-close-white p-0 m-0" style="font-size: 0.65rem; width: 0.5rem; height: 0.5rem;" aria-label="Clear Day"></button>
                                            </span>
                                        @endif

                                        @if(!empty($group))
                                            <span class="badge bg-primary text-white d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem; font-weight: 600;">
                                                <span>{{ $group }}</span>
                                                <button type="button" wire:click="$set('group', '')" class="btn-close btn-close-white p-0 m-0" style="font-size: 0.65rem; width: 0.5rem; height: 0.5rem;" aria-label="Clear Group"></button>
                                            </span>
                                        @endif

                                        @if(!empty($serviceBody))
                                            <span class="badge bg-primary text-white d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem; font-weight: 600;">
                                                <span>{{ $serviceBody }}</span>
                                                <button type="button" wire:click="$set('serviceBody', '')" class="btn-close btn-close-white p-0 m-0" style="font-size: 0.65rem; width: 0.5rem; height: 0.5rem;" aria-label="Clear Service Body"></button>
                                            </span>
                                        @endif

                                        @if(!empty($type))
                                            <span class="badge bg-primary text-white d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem; font-weight: 600;">
                                                <span>{{ __('messages.' . $type) }}</span>
                                                <button type="button" wire:click="$set('type', '')" class="btn-close btn-close-white p-0 m-0" style="font-size: 0.65rem; width: 0.5rem; height: 0.5rem;" aria-label="Clear Type"></button>
                                            </span>
                                        @endif

                                        @if(!empty($city))
                                            <span class="badge bg-primary text-white d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem; font-weight: 600;">
                                                <span>{{ $city }}</span>
                                                <button type="button" wire:click="$set('city', '')" class="btn-close btn-close-white p-0 m-0" style="font-size: 0.65rem; width: 0.5rem; height: 0.5rem;" aria-label="Clear City"></button>
                                            </span>
                                        @endif

                                        @if(!empty($neighborhood))
                                            <span class="badge bg-primary text-white d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem; font-weight: 600;">
                                                <span>{{ $neighborhood }}</span>
                                                <button type="button" wire:click="$set('neighborhood', '')" class="btn-close btn-close-white p-0 m-0" style="font-size: 0.65rem; width: 0.5rem; height: 0.5rem;" aria-label="Clear Neighborhood"></button>
                                            </span>
                                        @endif

                                        @if($virtualOnly)
                                            <span class="badge bg-success text-white d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem; font-weight: 600;">
                                                <span>{{ __('messages.Virtual Meetings Only') ?? 'Virtual' }}</span>
                                                <button type="button" wire:click="toggleVirtualOnly" class="btn-close btn-close-white p-0 m-0" style="font-size: 0.65rem; width: 0.5rem; height: 0.5rem;" aria-label="Clear Virtual"></button>
                                            </span>
                                        @endif

                                        @if($englishOnly)
                                            <span class="badge bg-primary text-white d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem; font-weight: 600;">
                                                <span>{{ __('messages.English Meetings Only') ?? 'English' }}</span>
                                                <button type="button" wire:click="toggleEnglishOnly" class="btn-close btn-close-white p-0 m-0" style="font-size: 0.65rem; width: 0.5rem; height: 0.5rem;" aria-label="Clear English"></button>
                                            </span>
                                        @endif

                                        @if($businessMeetingsOnly)
                                            <span class="badge bg-warning text-dark d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem; font-weight: 600;">
                                                <span>{{ __('messages.Group Business Meetings Only') ?? 'Business' }}</span>
                                                <button type="button" wire:click="toggleBusinessMeetingsOnly" class="btn-close p-0 m-0" style="font-size: 0.65rem; width: 0.5rem; height: 0.5rem;" aria-label="Clear Business"></button>
                                            </span>
                                        @endif

                                        @if(!empty($search))
                                            <span class="badge bg-secondary text-white d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem; font-weight: 600;">
                                                <span>"{{ $search }}"</span>
                                                <button type="button" wire:click="$set('search', '')" class="btn-close btn-close-white p-0 m-0" style="font-size: 0.65rem; width: 0.5rem; height: 0.5rem;" aria-label="Clear Search"></button>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex flex-wrap justify-content-center gap-3 mt-4 w-100">
                                <button type="button" 
                                        wire:click="toggleVirtualOnly" 
                                        class="btn rounded-3 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 {{ $virtualOnly ? 'btn-success text-white' : 'btn-outline-success' }}"
                                        style="height: 38px; min-width: 200px; max-width: 280px; flex: 1 1 200px;"
                                        id="tour-virtual-only">
                                    <x-fas-video style="width:16px; height:16px;"/>
                                    {{ __('messages.Virtual Meetings Only') ?? 'Virtual Meetings Only' }}
                                </button>

                                <button type="button" 
                                        wire:click="toggleEnglishOnly" 
                                        class="btn rounded-3 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 {{ $englishOnly ? 'btn-primary text-white' : 'btn-outline-primary' }}"
                                        style="height: 38px; min-width: 200px; max-width: 280px; flex: 1 1 200px;"
                                        id="tour-english-only">
                                    <x-fas-language style="width:16px; height:16px;"/>
                                    {{ __('messages.English Meetings Only') ?? 'English Meetings Only' }}
                                </button>

                                <button type="button" 
                                        wire:click="toggleBusinessMeetingsOnly" 
                                        class="btn rounded-3 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 {{ $businessMeetingsOnly ? 'btn-warning text-dark' : 'btn-outline-warning' }}"
                                        style="height: 38px; min-width: 200px; max-width: 280px; flex: 1 1 200px;"
                                        id="tour-business-meetings-only">
                                    <x-fas-briefcase style="width:16px; height:16px;"/>
                                    {{ __('messages.Group Business Meetings Only') ?? 'Group Business Meetings Only' }}
                                </button>
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
        @if($meetings->isEmpty())
            @php
                $activeFilters = [];
                if (!empty($group)) {
                    $activeFilters[] = $group;
                }
                if (!empty($day)) {
                    $activeFilters[] = $day;
                }
                if (!empty($city)) {
                    $activeFilters[] = $city;
                }
                if (!empty($neighborhood)) {
                    $activeFilters[] = $neighborhood;
                }
                if (!empty($serviceBody)) {
                    $activeFilters[] = $serviceBody;
                }
                if (!empty($type)) {
                    $activeFilters[] = __('messages.' . $type);
                }
                if ($virtualOnly) {
                    $activeFilters[] = __('messages.Virtual Meetings Only') ?? 'Virtual Meetings Only';
                }
                if ($englishOnly) {
                    $activeFilters[] = __('messages.English Meetings Only') ?? 'English Meetings Only';
                }
                if ($businessMeetingsOnly) {
                    $activeFilters[] = __('messages.Group Business Meetings Only') ?? 'Group Business Meetings Only';
                }
                if (!empty($search)) {
                    $activeFilters[] = '"' . $search . '"';
                }
            @endphp
            <div class="row justify-content-center mt-4">
                <div class="col-auto">
                    <p class="text-center text-muted fw-semibold">
                        @if(!empty($activeFilters))
                            {{ __('messages.No meetings found in') }} {{ implode(' ', $activeFilters) }}
                        @else
                            {{ __('messages.No meetings found') }}
                        @endif
                    </p>
                </div>
            </div>
            @else
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 mb-4 w-100">
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
                <div id="tour-pdf" class="flex-fill" style="max-width: 280px; min-width: 200px;">
                    <button type="button" class="btn btn-danger w-100 rounded-pill py-2 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm transition-all hover-scale" data-bs-toggle="modal" data-bs-target="#exportWizardModal">
                        <x-fas-file-pdf style="width:16px; height:16px;"/>
                        <span>{{__('messages.downloadmeetingspdf')}}</span>
                    </button>
                </div>
                <div id="tour-csv" class="flex-fill" style="max-width: 280px; min-width: 200px;">
                    <a href="{{ route('exportMeetingsToCSV', $exportParams) }}" target="_blank" class="btn btn-success w-100 rounded-pill py-2 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm text-decoration-none transition-all hover-scale">
                        <x-fas-file-csv style="width:16px; height:16px;"/>
                        <span>{{__('messages.downloadmeetingscsv')}}</span>
                    </a>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="d-flex justify-content-center mb-3">
                    <div class="position-relative" style="max-width: 340px; width: 100%;" id="tour-search">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               id="search-input-box"
                               class="form-control rounded-pill ps-5 pe-5 shadow-sm transition-all" 
                               placeholder="{{ __('messages.Search meetings') }}..."
                               style="border: 1px solid rgba(0,0,0,0.12); background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); transition: all 0.3s ease;">
                        
                        <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted">
                            <x-fas-search style="width:14px; height:14px;" />
                        </span>

                        @if(!empty($search))
                            <button type="button" 
                                    wire:click="$set('search', '')" 
                                    class="position-absolute top-50 end-0 translate-middle-y pe-3 border-0 bg-transparent text-muted hover-text-primary p-0 d-flex align-items-center" 
                                    style="cursor: pointer; z-index: 5;"
                                    title="{{ __('messages.Clear Filters') }}">
                                <x-fas-times style="width: 14px; height: 14px;" />
                            </button>
                        @endif
                    </div>
                    
                    <style>
                        #search-input-box:focus {
                            background: rgba(255, 255, 255, 0.95) !important;
                            border-color: var(--bs-primary) !important;
                            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.18) !important;
                            outline: 0;
                        }
                        .hover-text-primary:hover {
                            color: var(--bs-primary) !important;
                        }
                    </style>
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
