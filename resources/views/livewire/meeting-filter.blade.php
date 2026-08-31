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
                    <div x-show="open" x-transition.duration.300ms class="card mb-4 border-0 shadow-lg rounded-4 overflow-visible position-relative" style="background: rgba(255, 255, 255, 0.8) !important; backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.4) !important;">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 text-center">
                            <h5 class="mb-2 fw-bold text-primary d-flex align-items-center justify-content-center flex-wrap gap-2" id="tour-filter-options">
                                <i class="fas fa-filter mx-1"></i>{{ __('messages.Filter Options') }}
                                <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1 mx-2 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm transition-all hover-scale position-relative" id="start-tour-btn" onclick="startMeetingTour()" title="{{ __('messages.tour_start') }}" style="border-width: 1.5px; background: rgba(13, 202, 240, 0.08); backdrop-filter: blur(4px);">
                                    <i class="bi bi-compass-fill text-info fs-6"></i>
                                    <span>{{ __('messages.tour_start') }}</span>
                                </button>
                            </h5>
                            <!-- Legend -->
                            <div class="d-flex flex-wrap justify-content-center gap-3 mt-2" style="font-size: 0.85rem; font-weight: 500;" id="tour-legend">
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
                                <div wire:ignore.self class="col" wire:key="filter-group" id="tour-group">
                                    <x-filter.select :options="$groups" name="group" wire:model.live="group" label="{{__('messages.Group')}}" />
                                </div>
                                <div wire:ignore.self class="col" wire:key="filter-serviceBody" id="tour-service-body">
                                    <x-filter.select :options="$serviceBodies" name="serviceBody" wire:model.live="serviceBody" label="{{__('messages.Service Body')}}" />
                                </div>
                                <div wire:ignore.self class="col" wire:key="filter-city" id="tour-city">
                                    <x-filter.select :options="$cities" name="city" wire:model.live="city" label="{{__('messages.City')}}" />
                                </div>
                                <div wire:ignore.self class="col" wire:key="filter-neighborhood" id="tour-neighborhood">
                                    <x-filter.select :options="$neighborhoods" name="neighborhood" wire:model.live="neighborhood" label="{{__('messages.Neighborhood')}}" />
                                </div>
                                <div class="col" wire:key="filter-type" id="tour-type">
                                    <div class="d-none d-md-flex align-items-center justify-content-start mb-2 gap-2" style="visibility: hidden; height: 19px;">
                                        <label class="m-0 p-0">&nbsp;</label>
                                    </div>
                                    <div class="d-flex flex-wrap sm:flex-nowrap bg-light p-1 rounded-4 border align-items-center w-100 gap-1">
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
                                    <x-filter.select :options="$recurrences" name="recurrence" wire:model.live="recurrence" label="{{__('messages.Recurrence')}}" />
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
                                    {{ __('messages.Virtual Meetings Only') ?? 'Virtual Meetings Only' }} ({{ $onlineCount }})
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
                                <button type="button" wire:click="clearFilters" class="btn btn-danger text-white px-5 rounded-pill fw-bold transition-all hover-scale shadow-sm">
                                    <i class="fas fa-times me-1"></i> {{__('messages.Clear Filters')}}
                                </button>
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

    @script
    <script>
        window.startMeetingTour = function() {
            if (typeof window.driver === 'undefined' || !window.driver.js) {
                console.error('Driver.js library is not loaded');
                return;
            }

            const isRtl = document.documentElement.dir === 'rtl' || document.body.dir === 'rtl';

            const stepIcons = {
                '#tour-filter-options': 'bi-funnel-fill',
                '#tour-legend': 'bi-palette',
                '#tour-recurrence': 'bi-arrow-repeat',
                '#tour-day': 'bi-calendar-week',
                '#tour-group': 'bi-people',
                '#tour-service-body': 'bi-diagram-3',
                '#tour-type': 'bi-shield-lock',
                '#tour-city': 'bi-building',
                '#tour-neighborhood': 'bi-geo-alt',
                '#tour-virtual-only': 'bi-laptop',
                '#tour-english-only': 'bi-translate',
                '#tour-business-meetings-only': 'bi-briefcase',
                '#tour-clear': 'bi-arrow-counterclockwise',
                '#tour-search': 'bi-search',
                '#tour-pdf': 'bi-file-earmark-pdf',
                '#tour-csv': 'bi-file-earmark-excel',
                '#tour-city-section': 'bi-pin-map',
                '#tour-meeting-card': 'bi-card-heading',
                '#tour-card-time': 'bi-clock-history',
                '#tour-card-group': 'bi-hospital',
                '#tour-card-badges': 'bi-tags',
                '#tour-card-contact': 'bi-person-lines-fill',
                '#tour-card-actions': 'bi-compass'
            };
            
            let tourSteps = [
                { element: '#tour-filter-options', popover: { title: '{{ __("messages.tour_filter_options") }}', description: '{{ __("messages.tour_filter_desc") }}', icon: stepIcons['#tour-filter-options'] } },
                { element: '#tour-legend', popover: { title: '{{ __("messages.tour_legend") }}', description: '{{ __("messages.tour_legend_desc") }}', icon: stepIcons['#tour-legend'] } },
                { element: '#tour-recurrence', popover: { title: '{{ __("messages.tour_recurrence") }}', description: '{{ __("messages.tour_recurrence_desc") }}', icon: stepIcons['#tour-recurrence'] } },
                { element: '#tour-day', popover: { title: '{{ __("messages.tour_day") }}', description: '{{ __("messages.tour_day_desc") }}', icon: stepIcons['#tour-day'] } },
                { element: '#tour-group', popover: { title: '{{ __("messages.tour_group") }}', description: '{{ __("messages.tour_group_desc") }}', icon: stepIcons['#tour-group'] } },
                { element: '#tour-service-body', popover: { title: '{{ __("messages.tour_service_body") }}', description: '{{ __("messages.tour_service_body_desc") }}', icon: stepIcons['#tour-service-body'] } },
                { element: '#tour-type', popover: { title: '{{ __("messages.tour_type") }}', description: '{{ __("messages.tour_type_desc") }}', icon: stepIcons['#tour-type'] } },
                { element: '#tour-city', popover: { title: '{{ __("messages.tour_city") }}', description: '{{ __("messages.tour_city_desc") }}', icon: stepIcons['#tour-city'] } },
                { element: '#tour-neighborhood', popover: { title: '{{ __("messages.tour_neighborhood") }}', description: '{{ __("messages.tour_neighborhood_desc") }}', icon: stepIcons['#tour-neighborhood'] } },
                { element: '#tour-virtual-only', popover: { title: '{{ __("messages.tour_virtual_only") }}', description: '{{ __("messages.tour_virtual_only_desc") }}', icon: stepIcons['#tour-virtual-only'] } },
                { element: '#tour-english-only', popover: { title: '{{ __("messages.tour_english_only") }}', description: '{{ __("messages.tour_english_only_desc") }}', icon: stepIcons['#tour-english-only'] } },
                { element: '#tour-business-meetings-only', popover: { title: '{{ __("messages.tour_business_only") }}', description: '{{ __("messages.tour_business_only_desc") }}', icon: stepIcons['#tour-business-meetings-only'] } },
                { element: '#tour-clear', popover: { title: '{{ __("messages.tour_clear") }}', description: '{{ __("messages.tour_clear_desc") }}', icon: stepIcons['#tour-clear'] } },
                { element: '#tour-search', popover: { title: '{{ __("messages.tour_search") }}', description: '{{ __("messages.tour_search_desc") }}', icon: stepIcons['#tour-search'] } },
                { element: '#tour-pdf', popover: { title: '{{ __("messages.tour_pdf") }}', description: '{{ __("messages.tour_pdf_desc") }}', icon: stepIcons['#tour-pdf'] } },
                { element: '#tour-csv', popover: { title: '{{ __("messages.tour_csv") }}', description: '{{ __("messages.tour_csv_desc") }}', icon: stepIcons['#tour-csv'] } },
                { element: '#tour-city-section', popover: { title: '{{ __("messages.tour_city_section") }}', description: '{{ __("messages.tour_city_section_desc") }}', icon: stepIcons['#tour-city-section'] } },
                { element: '#tour-meeting-card', popover: { title: '{{ __("messages.tour_meeting_card") }}', description: '{{ __("messages.tour_meeting_card_desc") }}', icon: stepIcons['#tour-meeting-card'] } },
                { element: '#tour-card-time', popover: { title: '{{ __("messages.tour_card_time") }}', description: '{{ __("messages.tour_card_time_desc") }}', icon: stepIcons['#tour-card-time'] } },
                { element: '#tour-card-group', popover: { title: '{{ __("messages.tour_card_group") }}', description: '{{ __("messages.tour_card_group_desc") }}', icon: stepIcons['#tour-card-group'] } },
                { element: '#tour-card-badges', popover: { title: '{{ __("messages.tour_card_badges") }}', description: '{{ __("messages.tour_card_badges_desc") }}', icon: stepIcons['#tour-card-badges'] } },
                { element: '#tour-card-contact', popover: { title: '{{ __("messages.tour_card_contact") }}', description: '{{ __("messages.tour_card_contact_desc") }}', icon: stepIcons['#tour-card-contact'] } },
                { element: '#tour-card-actions', popover: { title: '{{ __("messages.tour_card_actions") }}', description: '{{ __("messages.tour_card_actions_desc") }}', icon: stepIcons['#tour-card-actions'] } }
            ];

            if (document.querySelector('#tour-meeting-card')) {
                tourSteps.push({
                    element: '#tour-meeting-card',
                    popover: {
                        title: '{{ __("messages.tour_meeting_card") }}',
                        description: '{{ __("messages.tour_meeting_card_desc") }}',
                        icon: stepIcons['#tour-meeting-card']
                    }
                });
            }

            // Filter out steps whose element does not exist in current DOM
            tourSteps = tourSteps.filter(step => !step.element || document.querySelector(step.element));

            const nextBtnLabel = isRtl ? '{{ __("messages.tour_next") }} &larr;' : '{{ __("messages.tour_next") }} &rarr;';
            const prevBtnLabel = isRtl ? '&rarr; {{ __("messages.tour_prev") }}' : '&larr; {{ __("messages.tour_prev") }}';

            const driverObj = window.driver.js.driver({
                showProgress: true,
                animate: true,
                progressText: '{!! __("messages.tour_progress_text") !!}',
                nextBtnText: nextBtnLabel,
                prevBtnText: prevBtnLabel,
                doneBtnText: '{{ __("messages.tour_done") }}',
                steps: tourSteps,
                onPopoverRendered: (popover, { state }) => {
                    const currentIndex = state.activeIndex || 0;
                    const totalSteps = tourSteps.length;
                    const percentage = Math.round(((currentIndex + 1) / totalSteps) * 100);

                    // Top visual progress bar
                    let progressBar = popover.wrapper.querySelector('.driver-popover-progress-bar');
                    if (!progressBar) {
                        progressBar = document.createElement('div');
                        progressBar.className = 'driver-popover-progress-bar';
                        progressBar.innerHTML = '<div class="driver-popover-progress-fill"></div>';
                        popover.wrapper.insertBefore(progressBar, popover.wrapper.firstChild);
                    }
                    const fill = progressBar.querySelector('.driver-popover-progress-fill');
                    if (fill) {
                        fill.style.width = percentage + '%';
                    }

                    // Prepend step icon to popover title
                    const currentStep = tourSteps[currentIndex];
                    const iconClass = (currentStep && currentStep.popover && currentStep.popover.icon) || 'bi-info-circle';
                    if (popover.title && !popover.title.querySelector('.step-icon')) {
                        const iconSpan = document.createElement('span');
                        iconSpan.className = 'step-icon';
                        iconSpan.innerHTML = `<i class="bi ${iconClass}"></i>`;
                        popover.title.insertBefore(iconSpan, popover.title.firstChild);
                    }

                    // Keyboard navigation hints footer
                    let keyHints = popover.wrapper.querySelector('.driver-keyboard-hints');
                    if (!keyHints) {
                        keyHints = document.createElement('div');
                        keyHints.className = 'driver-keyboard-hints';
                        keyHints.innerHTML = isRtl 
                            ? '<span><kbd>&larr;</kbd> <kbd>&rarr;</kbd> للتنقل</span><span><kbd>Esc</kbd> لإغلاق</span>'
                            : '<span><kbd>&larr;</kbd> <kbd>&rarr;</kbd> Navigate</span><span><kbd>Esc</kbd> Close</span>';
                        popover.wrapper.appendChild(keyHints);
                    }
                }
            });

            driverObj.drive();
        };

        function bindTourBtn() {
            const startBtn = document.getElementById('start-tour-btn');
            if (startBtn && !startBtn.dataset.tourBound) {
                startBtn.dataset.tourBound = 'true';
                startBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.startMeetingTour();
                });
            }
        }

        document.addEventListener('livewire:navigated', bindTourBtn);
        document.addEventListener('DOMContentLoaded', bindTourBtn);
        bindTourBtn();
    </script>
    @endscript
</div>
