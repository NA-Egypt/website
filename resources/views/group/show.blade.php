<x-layout>
    <div class="container-fluid px-2 px-sm-3 mt-4 mb-5 mx-auto" style="max-width: 1200px; width: 100%;">
        <x-backhead>{{__("messages.Group information for") }}
            @if(app()->getLocale() === 'ar')
                {{$group->ar_name}}
            @else
                {{$group->en_name}}
            @endif
        </x-backhead>

        {{-- Consolidated Group Profile Card --}}
        <div class="glass-card p-3 p-md-4 rounded-4 mb-5 shadow-sm mt-4">
            {{-- Header Details --}}
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 border-bottom pb-3 mb-4" style="border-color: var(--glass-border) !important;">
                <h4 class="mb-0 fw-bold d-flex align-items-center" style="color: var(--text-primary);">
                    <i class="bi bi-shield-check me-2"></i> 
                    {{ __('messages.Group Details') ?? 'Group Details' }}
                    <button class="btn btn-link btn-sm d-md-none p-0 text-primary ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#groupDetailsCollapse" aria-expanded="false" aria-controls="groupDetailsCollapse">
                        <i class="bi bi-chevron-down" id="collapseIcon" style="transition: transform 0.2s ease;"></i>
                    </button>
                </h4>
                
                {{-- Actions --}}
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    @unless(auth()->user()->hasRole('ServiceBody'))
                        <x-button-a href="{{ route('group.edit', $group->id) }}" color='primary' name="{{ __('messages.Edit Group') }}" class="btn-sm rounded-pill px-3 py-2 shadow-sm" />
                    @endunless
                </div>
            </div>

            {{-- Grid details (Collapsible on mobile) --}}
            <div class="collapse d-md-block" id="groupDetailsCollapse">
                <div class="row g-4">
                    {{-- Left/Right Columns depending on direction --}}
                    <div class="col-12 col-md-6">
                        <div class="d-flex flex-column gap-3">
                            <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.015); border: 1px solid var(--glass-border);">
                                <span class="text-secondary small d-block mb-1"><i class="bi bi-translate me-1 text-primary"></i> {{ __('messages.Arabic Group Name') }}</span>
                                <span class="fw-bold text-dark">{{ $group->ar_name }}</span>
                            </div>

                            <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.015); border: 1px solid var(--glass-border);">
                                <span class="text-secondary small d-block mb-1"><i class="bi bi-globe me-1 text-primary"></i> {{ __('messages.English Group Name') }}</span>
                                <span class="fw-bold text-dark">{{ $group->en_name }}</span>
                            </div>

                            <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.015); border: 1px solid var(--glass-border);">
                                <span class="text-secondary small d-block mb-1"><i class="bi bi-diagram-3 me-1 text-primary"></i> {{ __('messages.Service Body') }}</span>
                                <span class="fw-bold text-dark">{{ $group->serviceBody->ar_name }}</span>
                            </div>
                            
                            <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.015); border: 1px solid var(--glass-border);">
                                <span class="text-secondary small d-block mb-1"><i class="bi bi-geo-alt me-1 text-primary"></i> {{ __('messages.Neighborhood') }}</span>
                                <span class="fw-bold text-dark">{{ $group->neighborhood->ar_name }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="d-flex flex-column gap-3">
                            <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.015); border: 1px solid var(--glass-border);">
                                <span class="text-secondary small d-block mb-1"><i class="bi bi-people me-1 text-primary"></i> {{ __('messages.Capacity') }}</span>
                                <span class="fw-bold text-dark">{{ $group->capacity ?? 'N/A' }}</span>
                            </div>

                            <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.015); border: 1px solid var(--glass-border);">
                                <span class="text-secondary small d-block mb-1"><i class="bi bi-person-badge me-1 text-primary"></i> {{ __('messages.Arabic GSR Name') }}</span>
                                <span class="fw-bold text-dark">{{ $group->ar_gsr_name ?: 'N/A' }}</span>
                            </div>

                            <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.015); border: 1px solid var(--glass-border);">
                                <span class="text-secondary small d-block mb-1"><i class="bi bi-person-badge-fill me-1 text-primary"></i> {{ __('messages.English GSR Name') }}</span>
                                <span class="fw-bold text-dark">{{ $group->en_gsr_name ?: 'N/A' }}</span>
                            </div>

                            <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.015); border: 1px solid var(--glass-border);">
                                <span class="text-secondary small d-block mb-1"><i class="bi bi-pin-map me-1 text-primary"></i> {{ __('messages.Locations') }}</span>
                                <span class="fw-bold text-dark text-break">
                                    @if(filter_var($group->location, FILTER_VALIDATE_URL))
                                        <a href="{{ $group->location }}" target="_blank" class="text-primary text-decoration-underline">{{ $group->location }}</a>
                                    @else
                                        {{ $group->location }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Contacts --}}
                <div class="d-flex flex-wrap gap-2 align-items-center mt-4 pt-3 border-top" style="border-color: var(--glass-border) !important;">
                    <span class="text-secondary small me-2"><i class="bi bi-person-lines-fill"></i> {{ __('messages.Contacts') ?? 'Contacts' }}:</span>
                    <a href="mailto:{{ $group->user->email }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 d-inline-flex align-items-center overflow-hidden" style="max-width: 100%;">
                        <i class="bi bi-envelope me-2 flex-shrink-0"></i>
                        <span class="text-truncate" style="max-width: 180px; display: inline-block; vertical-align: middle;">{{ $group->user->email }}</span>
                    </a>
                    @if($group->phone)
                        <a href="tel:{{ $group->phone }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 d-inline-flex align-items-center" dir="ltr">
                            <i class="bi bi-telephone me-2 flex-shrink-0"></i> {{ $group->phone }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Service & Archives Quick Access --}}
        <div class="glass-card p-3 p-md-4 rounded-4 mt-4 mt-md-5">
            <h4 class="mb-4 fw-bold border-bottom pb-3" style="color: var(--text-primary); border-color: var(--glass-border) !important;">
                <i class="bi bi-folder-fill me-2"></i> {{ __('messages.Service & Archives Quick Access') ?? 'Service & Archives Quick Access' }}
            </h4>
            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between transition-hover animate-card" style="background: rgba(59, 130, 246, 0.03); border: 1px solid var(--glass-border);">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="widgets-icons bg-light text-primary rounded-3 flex-shrink-0 shadow-sm border border-light">
                                <i class="bi bi-archive-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">{{ __('messages.Reports Archive') ?? 'Reports Archive' }}</h5>
                                <p class="text-secondary small mb-0">{{ __('messages.Browse regional committees reports and archives') ?? 'Browse regional committees reports and archives.' }}</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('committee-reports.archive') }}" class="btn btn-primary rounded-pill px-4 btn-sm">
                                <i class="bi bi-folder2-open me-1"></i> {{ __('messages.Show') ?? 'Open' }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between transition-hover animate-card" style="background: rgba(16, 185, 129, 0.03); border: 1px solid var(--glass-border);">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="widgets-icons bg-light text-success rounded-3 flex-shrink-0 shadow-sm border border-light">
                                <i class="bi bi-file-earmark-text-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">{{ __('messages.agendas_archive_of', ['name' => (app()->getLocale() === 'ar' ? $group->ar_name : $group->en_name)]) }}</h5>
                                <p class="text-secondary small mb-0">{{ __('messages.Browse monthly group agendas and documents') ?? 'Browse monthly group agendas and documents.' }}</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('groups-agendas.archive', ['group_id' => $group->id]) }}" class="btn btn-success rounded-pill px-4 btn-sm">
                                <i class="bi bi-folder2-open me-1"></i> {{ __('messages.Show') ?? 'Open' }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between transition-hover animate-card" style="background: rgba(245, 158, 11, 0.03); border: 1px solid var(--glass-border);">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="widgets-icons bg-light text-warning rounded-3 flex-shrink-0 shadow-sm border border-light">
                                <i class="bi bi-cart-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">{{ __('messages.Literature Requests') }}</h5>
                                <p class="text-secondary small mb-0">{{ app()->getLocale() === 'ar' ? 'طلب المطبوعات شهرياً وعرض الطلبات السابقة والفواتير.' : 'Request literature monthly and view past requests/invoices.' }}</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 flex-wrap">
                            @if(auth()->user()->hasRole('gsr'))
                                <a href="{{ route('literature-requests.cart') }}" class="btn btn-warning text-white rounded-pill px-3 btn-sm">
                                    <i class="bi bi-cart-plus me-1"></i> {{ __('messages.submit_request') }}
                                </a>
                            @elseif(auth()->user()->hasRole('Treasurer') || auth()->user()->hasRole('ServiceBody'))
                                <a href="{{ route('literature-requests.treasurer') }}" class="btn btn-warning text-white rounded-pill px-3 btn-sm">
                                    <i class="bi bi-wallet2 me-1"></i> {{ __('messages.Treasurer Dashboard') }}
                                </a>
                            @elseif(auth()->user()->hasRole('Store Manager') || auth()->user()->hasRole('Lit User'))
                                <a href="{{ route('literature-requests.committee') }}" class="btn btn-warning text-white rounded-pill px-3 btn-sm">
                                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> {{ __('messages.Literature Requests') }}
                                </a>
                            @endif
                            <a href="{{ route('literature-requests.archive') }}" class="btn btn-outline-warning rounded-pill px-3 btn-sm">
                                <i class="bi bi-folder2-open me-1"></i> {{ __('messages.Show') ?? 'Open' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Meetings Section --}}
        <div class="glass-card p-3 p-md-4 rounded-4 mt-4 mt-md-5">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 border-bottom pb-3 mb-4" style="border-color: var(--glass-border) !important;">
                <h4 class="mb-0 fw-bold" style="color: var(--text-primary);"><i class="bi bi-calendar-event me-2"></i> {{ __('messages.Meetings') }}</h4>
                @unless(auth()->user()->hasRole('ServiceBody'))
                <x-button-a href="{{ route('meeting.create') }}" color='outline-primary' name="{{__('messages.Add') . ' ' . __('messages.Meeting')}}" class="rounded-pill" />
                @endunless
            </div>
            
            @if($meetings->count() > 0)
                <div class="row row-cols-1 row-cols-lg-2 g-4">
                    @foreach($meetings as $meeting)
                        <div class="col">
                            <div class="glass-card h-100 p-3 p-md-4 rounded-4 border position-relative transition-hover" style="border-color: var(--glass-border) !important; background: rgba(0,0,0,0.01);">
                                
                                {{-- Meeting Header --}}
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="mb-1 fw-bold text-primary">
                                            @if(app()->getLocale() === 'ar')
                                                {{ $meeting->day->ar_name }}
                                            @else
                                                {{ $meeting->day->en_name }}
                                            @endif
                                        </h5>
                                        <p class="text-secondary small fw-bold mb-0" dir="ltr">
                                            <i class="bi bi-clock mx-1"></i>
                                            {{ \Carbon\Carbon::parse($meeting->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($meeting->end_time)->format('h:i A') }}
                                        </p>
                                    </div>
                                    <div class="badge px-3 py-2 rounded-pill fw-medium" style="background-color: rgba(59, 130, 246, 0.15) !important; color: #2563eb !important; border: 1px solid rgba(59, 130, 246, 0.4) !important;">
                                        @if(app()->getLocale() === 'ar')
                                            {{ __("messages." . $meeting->type) }}
                                        @else
                                            {{ $meeting->type }}
                                        @endif
                                    </div>
                                </div>

                                {{-- Title --}}
                                @if($meeting->title)
                                    <h6 class="fw-bold mb-3">{{ $meeting->title }}</h6>
                                @endif

                                {{-- Topics Tags --}}
                                @if($meeting->topics && $meeting->topics->count() > 0)
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        @foreach($meeting->topics as $topic)
                                            <span class="badge rounded-pill px-3 py-2 fw-medium" style="background-color: rgba(14, 165, 233, 0.15) !important; color: #0284c7 !important; border: 1px solid rgba(14, 165, 233, 0.4) !important;">
                                                <i class="bi bi-tag me-1"></i>
                                                @if(app()->getLocale() === 'ar')
                                                    {{ $topic->ar_name }}
                                                @else
                                                    {{ $topic->en_name }}
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Description --}}
                                @if($meeting->description)
                                    <div class="p-3 rounded-3 mb-3" style="background: rgba(0,0,0,0.03); border: 1px dashed var(--glass-border);">
                                        <p class="mb-0 text-secondary small" style="line-height: 1.6;">{{ $meeting->description }}</p>
                                    </div>
                                @endif

                                {{-- Meeting Options --}}
                                @if($meeting->options->count() > 0)
                                    <div class="d-flex flex-wrap gap-2 mb-4">
                                        @foreach($meeting->options as $option)
                                            <span class="badge rounded-pill px-2 py-1 fw-medium" style="background-color: rgba(0, 0, 0, 0.03) !important; color: var(--text-secondary) !important; border: 1px solid var(--glass-border) !important;">
                                                <i class="bi bi-check-circle-fill text-success me-1"></i>
                                                @if(app()->getLocale() === 'ar')
                                                    {{ $option->ar_name }}
                                                @else
                                                    {{ $option->en_name }}
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Actions --}}
                                @unless(auth()->user()->hasRole('ServiceBody'))
                                <div class="d-flex gap-2 mt-auto pt-3 border-top" style="border-color: var(--glass-border) !important;">
                                    <x-button-a href="{{ route('meeting.edit', $meeting->id) }}"
                                                 color='outline-primary'
                                                 name="{{ __('messages.Edit Meeting') }}"
                                                 class="btn-sm rounded-pill flex-grow-1" />

                                    <x-forms.delete-button
                                             name="{{ __('messages.Delete') }}"
                                             formName='delete-item'
                                             id="{{$meeting->id}}"
                                             routeName="meeting.destroy"
                                             class="btn-sm rounded-pill flex-grow-1" />
                                </div>
                                @endunless

                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Agendas Section --}}
        <div class="glass-card p-3 p-md-4 rounded-4 mt-4 mt-md-5">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 border-bottom pb-3 mb-4" style="border-color: var(--glass-border) !important;">
                <h4 class="mb-0 fw-bold" style="color: var(--text-primary);"><i class="bi bi-journal-text me-2"></i> {{ __('messages.agendas') ?? 'Agendas' }}</h4>
                <x-button-a href="{{ route('agenda.create', ['group_id' => $group->id]) }}" color='outline-primary' name="{{__('messages.create_agenda') ?? 'Create Agenda'}}" class="rounded-pill" />
            </div>
            
            @if($group->agendas && $group->agendas->count() > 0)
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    @foreach($group->agendas->sortByDesc('agenda_date') as $agenda)
                        <div class="col">
                            <div class="glass-card h-100 p-3 p-md-4 rounded-4 border position-relative transition-hover d-flex flex-column justify-content-between" style="border-color: var(--glass-border) !important; background: rgba(255,255,255,0.4);">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="mb-0 text-primary fw-bold">
                                            {{ __('messages.month_year_agenda', ['month' => \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'F'), 'year' => \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'Y')]) }}
                                        </h5>
                                        <span class="badge px-3 py-2 rounded-pill fw-medium bg-light text-secondary border">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'Y-m-d') }}
                                        </span>
                                    </div>
                                    
                                    <div class="mb-3 text-secondary small p-3 rounded-3" style="background: rgba(0,0,0,0.02); border: 1px dashed var(--glass-border);">
                                        <strong>{{ __('messages.recovery_atmosphere') }}:</strong>
                                        <span class="text-dark">
                                            @php
                                                $recAtm = is_array($agenda->recovery_atmosphere) ? implode(', ', $agenda->recovery_atmosphere) : $agenda->recovery_atmosphere;
                                            @endphp
                                            {{ Str::limit($recAtm, 120, '...') ?: '-' }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center flex-wrap gap-3 pt-3 border-top" style="border-color: var(--glass-border) !important;">
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge rounded-pill px-3 py-2 fw-medium bg-light text-primary border">
                                            <i class="bi bi-calendar-check me-1"></i>
                                            {{ $agenda->meetings_per_week }} {{ __('messages.meetings_per_week') }}
                                        </span>
                                        @if($agenda->new_comers > 0)
                                            <span class="badge rounded-pill px-3 py-2 fw-medium bg-light text-success border">
                                                <i class="bi bi-person-plus me-1"></i>
                                                {{ $agenda->new_comers }} {{ __('messages.new_comers') }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="d-flex flex-wrap gap-2 w-100 w-sm-auto justify-content-stretch justify-content-sm-end">
                                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3 flex-grow-1 flex-sm-grow-0" data-bs-toggle="modal" data-bs-target="#quickViewModalGroup{{ $agenda->id }}">
                                            <i class="bi bi-eye-fill"></i> {{ __('messages.Details') }}
                                        </button>
                                        <a href="{{ route('agenda.show', $agenda->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 flex-grow-1 flex-sm-grow-0">
                                            <i class="bi bi-arrow-right-short"></i> {{ __('messages.Show') }}
                                        </a>
                                        <a href="{{ route('agenda.exportPdf', $agenda->id) }}" class="btn btn-sm btn-secondary rounded-pill px-3 flex-grow-1 flex-sm-grow-0">
                                            <i class="bi bi-file-earmark-pdf"></i> {{ __('messages.PDF') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center p-5 rounded-4" style="background: rgba(0,0,0,0.02); border: 1px dashed var(--glass-border);">
                    <i class="bi bi-journal-x text-secondary" style="font-size: 3rem; opacity: 0.5;"></i>
                    <h5 class="text-secondary mt-3">{{ __('messages.No agendas submitted yet') ?? 'No agendas submitted yet' }}</h5>
                </div>
            @endif
        </div>
    </div> {{-- Close container-fluid --}}

    {{-- Render all modals at root context of layout to prevent styling overlay/backdrop stacking context issues --}}
    @if($group->agendas && $group->agendas->count() > 0)
        @foreach($group->agendas as $agenda)
            <div class="modal fade" id="quickViewModalGroup{{ $agenda->id }}" tabindex="-1" aria-labelledby="quickViewModalGroupLabel{{ $agenda->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content glass-card border-0" style="background: rgba(255, 255, 255, 0.96) !important; backdrop-filter: blur(25px); border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;">
                        {{-- HSL Gradient Header Accent --}}
                        <div class="modal-header border-bottom-0 pb-3 pt-4 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(14, 165, 233, 0.05)); border-bottom: 1px solid var(--glass-border) !important;">
                            <h5 class="modal-title fw-bold text-primary mb-0" id="quickViewModalGroupLabel{{ $agenda->id }}">
                                <i class="bi bi-journal-richtext text-primary me-2"></i>{{ $agenda->group->ar_name ?? $agenda->group->en_name }} - {{ __('messages.month_year_agenda', ['month' => \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'F'), 'year' => \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'Y')]) }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-4 pt-3 pb-4">
                            {{-- Custom styled tab navs --}}
                            <ul class="nav nav-pills flex-nowrap overflow-x-auto mb-4 p-1 rounded-3 bg-light" id="agendaTabGroup{{ $agenda->id }}" role="tablist" style="border: 1px solid var(--glass-border); -webkit-overflow-scrolling: touch; scrollbar-width: none;">
                                <li class="nav-item flex-shrink-0" role="presentation">
                                    <button class="nav-link active py-2 rounded-2 fw-bold" id="general-tab-group{{ $agenda->id }}" data-bs-toggle="tab" data-bs-target="#general-group{{ $agenda->id }}" type="button" role="tab" aria-controls="general-group{{ $agenda->id }}" aria-selected="true" style="font-size: 0.9rem;">
                                        <i class="bi bi-info-circle-fill me-1"></i> {{ __('messages.group_data') }}
                                    </button>
                                </li>
                                <li class="nav-item flex-shrink-0" role="presentation">
                                    <button class="nav-link py-2 rounded-2 fw-bold" id="news-tab-group{{ $agenda->id }}" data-bs-toggle="tab" data-bs-target="#news-group{{ $agenda->id }}" type="button" role="tab" aria-controls="news-group{{ $agenda->id }}" aria-selected="false" style="font-size: 0.9rem;">
                                        <i class="bi bi-megaphone-fill me-1"></i> {{ __('messages.group_news') }}
                                    </button>
                                </li>
                                <li class="nav-item flex-shrink-0" role="presentation">
                                    <button class="nav-link py-2 rounded-2 fw-bold" id="agenda-content-tab-group{{ $agenda->id }}" data-bs-toggle="tab" data-bs-target="#agenda-content-group{{ $agenda->id }}" type="button" role="tab" aria-controls="agenda-content-group{{ $agenda->id }}" aria-selected="false" style="font-size: 0.9rem;">
                                        <i class="bi bi-file-text-fill me-1"></i> {{ __('messages.the_agenda') }}
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content text-start" id="agendaTabContentGroup{{ $agenda->id }}">
                                {{-- Tab 1: General Info --}}
                                <div class="tab-pane fade show active" id="general-group{{ $agenda->id }}" role="tabpanel" aria-labelledby="general-tab-group{{ $agenda->id }}">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-4 h-100 transition-hover" style="background: rgba(59, 130, 246, 0.02); border: 1px solid var(--glass-border);">
                                                <span class="text-secondary small d-block mb-1">{{ __('messages.submitter_name') ?? 'Submitter Name' }}</span>
                                                <h6 class="fw-bold mb-0 text-dark">{{ $agenda->submitter_name ?: __('messages.Not provided') }}</h6>
                                                @if($agenda->service_position)
                                                    <span class="badge bg-primary mt-2 rounded-pill px-3">{{ $agenda->translated_service_position }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-4 h-100 transition-hover" style="background: rgba(0, 0, 0, 0.01); border: 1px solid var(--glass-border);">
                                                <span class="text-secondary small d-block mb-1">{{ __('messages.agenda_date') }}</span>
                                                <h6 class="fw-bold mb-0 text-dark">{{ \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'd M Y') }}</h6>
                                            </div>
                                        </div>
                                        
                                        @if($agenda->alt_gsr_name)
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-4 h-100 transition-hover" style="background: rgba(0, 0, 0, 0.01); border: 1px solid var(--glass-border);">
                                                <span class="text-secondary small d-block mb-1">{{ __('messages.alt_gsr_name') ?? 'Alt. GSR Name' }}</span>
                                                <h6 class="fw-bold mb-0 text-dark">{{ $agenda->alt_gsr_name }}</h6>
                                                @if($agenda->alt_gsr_position)
                                                    <span class="badge bg-secondary mt-2 rounded-pill px-3">{{ $agenda->translated_alt_gsr_position }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                        <div class="col-md-6">
                                            <div class="p-3 rounded-4 h-100 transition-hover" style="background: rgba(0, 0, 0, 0.01); border: 1px solid var(--glass-border);">
                                                <span class="text-secondary small d-block mb-1">{{ __('messages.Group') }}</span>
                                                <h6 class="fw-bold mb-0 text-primary">{{ $agenda->group->ar_name ?? $agenda->group->en_name }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tab 2: Group News --}}
                                <div class="tab-pane fade" id="news-group{{ $agenda->id }}" role="tabpanel" aria-labelledby="news-tab-group{{ $agenda->id }}">
                                    <div class="row g-3">
                                        <div class="col-md-4 col-6">
                                            <div class="p-3 text-center rounded-4 h-100 transition-hover" style="background: rgba(59, 130, 246, 0.04); border: 1px solid rgba(59, 130, 246, 0.1);">
                                                <span class="text-secondary small d-block mb-1">{{ __('messages.meetings_per_week') }}</span>
                                                <span class="fw-bold text-primary fs-4">{{ $agenda->meetings_per_week }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <div class="p-3 text-center rounded-4 h-100 transition-hover" style="background: rgba(16, 185, 129, 0.04); border: 1px solid rgba(16, 185, 129, 0.1);">
                                                <span class="text-secondary small d-block mb-1">{{ __('messages.new_comers') }}</span>
                                                <span class="fw-bold text-success fs-4">{{ $agenda->new_comers }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="p-3 text-center rounded-4 h-100 transition-hover" style="background: rgba(245, 158, 11, 0.04); border: 1px solid rgba(245, 158, 11, 0.1);">
                                                <span class="text-secondary small d-block mb-1">{{ __('messages.next_business_meeting') }}</span>
                                                <span class="fw-bold text-warning" style="font-size: 0.95rem;">{{ $agenda->next_business_meeting ? \App\Services\DateNumberHelper::translatedFormat($agenda->next_business_meeting, 'd M Y') : '-' }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <div class="p-3 rounded-4" style="background: rgba(0, 0, 0, 0.01); border: 1px solid var(--glass-border);">
                                                <span class="text-secondary small d-block mb-1">{{ __('messages.recovery_meetings_changes') ?? 'Recovery Meetings Changes' }}</span>
                                                <span class="badge {{ $agenda->recovery_meetings_changes ? 'bg-danger' : 'bg-success' }} px-3 py-2 rounded-pill fw-bold">
                                                    {{ $agenda->recovery_meetings_changes ? (__('messages.yes') ?? 'Yes') : (__('messages.no') ?? 'No') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="p-3 rounded-4" style="background: rgba(0, 0, 0, 0.01); border: 1px solid var(--glass-border);">
                                                <span class="text-secondary small d-block mb-2">{{ __('messages.open_positions') ?? 'Open Positions' }}</span>
                                                <div class="text-dark small fw-medium">{{ $agenda->open_positions ?: 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tab 3: The Agenda Content --}}
                                <div class="tab-pane fade" id="agenda-content-group{{ $agenda->id }}" role="tabpanel" aria-labelledby="agenda-content-tab-group{{ $agenda->id }}">
                                    <div class="d-flex flex-column gap-3">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-chat-left-text text-primary me-2"></i> {{ __('messages.recovery_atmosphere') }}</h6>
                                            <div class="p-3 rounded-3 bg-light text-secondary small" style="white-space: pre-line; border: 1px solid var(--glass-border);">
                                                @if(is_array($agenda->recovery_atmosphere))
                                                    {{ implode("\n", $agenda->recovery_atmosphere) ?: '-' }}
                                                @else
                                                    {{ $agenda->recovery_atmosphere ?: '-' }}
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-people text-info me-2"></i> {{ __('messages.trusted_servants') }}</h6>
                                            <div class="p-3 rounded-3 bg-light text-secondary small" style="white-space: pre-line; border: 1px solid var(--glass-border);">
                                                @if(is_array($agenda->trusted_servants))
                                                    {{ implode("\n", $agenda->trusted_servants) ?: '-' }}
                                                @else
                                                    {{ $agenda->trusted_servants ?: '-' }}
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-cash-coin text-success me-2"></i> {{ __('messages.financial_issues') }}</h6>
                                            <div class="p-3 rounded-3 bg-light text-secondary small" style="white-space: pre-line; border: 1px solid var(--glass-border);">
                                                @if(is_array($agenda->financial_issues))
                                                    {{ implode("\n", $agenda->financial_issues) ?: '-' }}
                                                @else
                                                    {{ $agenda->financial_issues ?: '-' }}
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-three-dots text-secondary me-2"></i> {{ __('messages.other_topics') }}</h6>
                                            <div class="p-3 rounded-3 bg-light text-secondary small" style="border: 1px solid var(--glass-border);">
                                                @if(is_array($agenda->other_topics))
                                                    @forelse($agenda->other_topics as $item)
                                                        @if(is_array($item) && isset($item['title']))
                                                            <div class="mb-2">
                                                                <strong>{{ $item['title'] }}:</strong>
                                                                <div>{!! $item['content'] !!}</div>
                                                            </div>
                                                        @else
                                                            <div class="mb-2">{!! $item !!}</div>
                                                        @endif
                                                    @empty
                                                        -
                                                    @endforelse
                                                @else
                                                    {!! $agenda->other_topics ?: '-' !!}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                            <a href="{{ route('agenda.exportPdf', $agenda->id) }}" class="btn btn-outline-success rounded-pill px-4 btn-sm">
                                <i class="bi bi-file-earmark-pdf-fill me-1"></i> {{ __('messages.PDF') }}
                            </a>
                            <a href="{{ route('agenda.show', $agenda->id) }}" class="btn btn-primary rounded-pill px-4 btn-sm">
                                <i class="bi bi-eye-fill me-1"></i> {{ __('messages.Show') }}
                            </a>
                            <button type="button" class="btn btn-secondary rounded-pill px-4 btn-sm" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</x-layout>


<style>
/* Collapse chevron animation */
.btn[aria-expanded="true"] #collapseIcon {
    transform: rotate(180deg);
}

.transition-hover {
    transition: all 0.3s ease;
}
.transition-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
}
.widgets-icons {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
}
/* Hide scrollbar for Chrome, Safari and Opera */
.overflow-x-auto::-webkit-scrollbar {
    display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.overflow-x-auto {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
/* RTL specific fixes */
[dir="rtl"] .me-1 { margin-left: 0.25rem !important; margin-right: 0 !important; }
[dir="rtl"] .me-2 { margin-left: 0.5rem !important; margin-right: 0 !important; }
[dir="rtl"] .ms-auto { margin-right: auto !important; margin-left: 0 !important; }
</style>