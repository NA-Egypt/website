<div>
    <div class="container min-vh-100 d-flex flex-column justify-content-topcenter align-items-center">
        <div class="w-100" style="max-width: 1140px;">
        <div class="container px-4 py-3 justify-content-center">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 text-center">
                            <h5 class="mb-0 fw-bold text-primary">
                                <i class="fas fa-filter me-2"></i>{{ __('messages.Filter Options') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                <div class="col-12" wire:key="filter-day">
                                    <x-filter.select :options="$days" name="day" wire:model.live="day" class="form-select form-control" label="{{ __('messages.Day') }}" />
                                </div>
                                <div class="col-12" wire:key="filter-group">
                                    <x-filter.select :options="$groups" name="group" wire:model.live="group" class="form-select form-control" label="{{__('messages.Group')}}" :disabled="!!$serviceBody || !!$city || !!$neighborhood" />
                                </div>
                                <div class="col-12" wire:key="filter-serviceBody">
                                    <x-filter.select :options="$serviceBodies" name="serviceBody" wire:model.live="serviceBody" class="form-select form-control" label="{{__('messages.Service Body')}}" :disabled="!!$group || !!$city || !!$neighborhood" />
                                </div>
                                <div class="col-12" wire:key="filter-type">
                                    <x-forms.label name="type" label="{{__('messages.Type')}}" />
                                    <select name="type" wire:model.live="type" class="form-select form-control" {{ $group ? 'disabled' : '' }}>
                                        <option value="">{{__('messages.Choose Type')}}</option>
                                        <option value="open">{{ __('messages.open') }} ({{ $openCount }})</option>
                                        <option value="closed">{{ __('messages.closed') }} ({{ $closedCount }})</option>
                                    </select>
                                </div>
                                <div class="col-12" wire:key="filter-city">
                                    <x-filter.select :options="$cities" name="city" wire:model.live="city" class="form-select form-control" label="{{__('messages.City')}}" :disabled="!!$serviceBody || !!$group" />
                                </div>
                                <div class="col-12" wire:key="filter-neighborhood">
                                    <x-filter.select :options="$neighborhoods" name="neighborhood" wire:model.live="neighborhood" class="form-select form-control" label="{{__('messages.Neighborhood')}}" :disabled="!!$serviceBody || !!$group" />
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-center align-items-center mt-4 pt-3 border-top" >
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
                <div class="d-flex justify-content-center mb-3">
                    <a href="{{ route('exportMeetingsToPDF', $exportParams) }}" target="_blank" class="btn btn-primary" style="max-width: 340px; width: 100%; text-align: center;">
                    {{__('messages.downloadmeetingspdf')}}
                    <x-fas-file-pdf style="width:16px; height:16px;"/>
                    </a>
                </div>
                <div class="d-flex justify-content-center mb-3">
                    <a href="{{ route('exportMeetingsToCSV', $exportParams) }}" target="_blank" class="btn btn-primary" style="max-width: 340px; width: 100%; text-align: center;">
                    {{__('messages.downloadmeetingscsv')}}
                    <x-fas-file-csv style="width:16px; height:16px;"/>
                    </a>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="d-flex justify-content-center mb-3">
                    <div class="position-relative" style="max-width: 340px; width: 100%;">
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
</div>
