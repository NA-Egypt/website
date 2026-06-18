<x-layout>
    <x-backhead>{{__("messages.Group information for") }}
        @if(app()->getLocale() === 'ar')
            {{$group->ar_name}}
        @else
            {{$group->en_name}}
        @endif
    </x-backhead>

    <div class="container-fluid mt-4 mb-5">
        
        {{-- Header Actions --}}
        @unless(auth()->user()->hasRole('ServiceBody'))
        <div class="d-flex justify-content-end mb-4">
            <x-button-a href="{{ route('group.edit', $group->id) }}" color='primary' name="{{  __('messages.Edit Group') }}" class="rounded-pill shadow-sm px-4" />
        </div>
        @endunless

        {{-- Group Information Grid (Dashboard UI/UX) --}}
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mb-5">
            
            {{-- Arabic Group Name --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.Arabic Group Name') }}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary);">{{$group->ar_name}}</h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-translate"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- English Group Name --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.English Group Name') }}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary);">{{$group->en_name}}</h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-globe"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Email --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.Email')}}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary); font-size: 1rem; word-break: break-all;">{{$group->user->email}}</h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #ec4899, #db2777); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-envelope"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Phone --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.Phone')}}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary);" dir="ltr">{{$group->phone}}</h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-telephone"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Capacity --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.Capacity')}}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary);">{{$group->capacity ?? 'N/A'}}</h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #6366f1, #4f46e5); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Locations --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.Locations')}}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary); font-size: 0.95rem; word-break: break-word; overflow-wrap: anywhere;">
                                @if(filter_var($group->location, FILTER_VALIDATE_URL))
                                    <a href="{{ $group->location }}" target="_blank" class="text-primary text-decoration-underline">{{ $group->location }}</a>
                                @else
                                    {{$group->location}}
                                @endif
                            </h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-pin-map"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Service Body --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.Service Body')}}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary);">{{$group->serviceBody->ar_name}}</h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Neighborhood --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.Neighborhood')}}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary);">{{$group->neighborhood->ar_name}}</h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #64748b, #475569); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Arabic GSR Name --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.Arabic GSR Name')}}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary);">{{$group->ar_gsr_name}}</h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #14b8a6, #0d9488); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-person-badge"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- English GSR Name --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.English GSR Name')}}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary);">{{$group->en_gsr_name}}</h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #0f766e, #115e59); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-person-badge"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Meetings Section --}}
        <div class="glass-card p-4 rounded-4 mt-5">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4" style="border-color: var(--glass-border) !important;">
                <h4 class="mb-0 fw-bold" style="color: var(--text-primary);"><i class="bi bi-calendar-event me-2"></i> {{ __('messages.Meetings') }}</h4>
                @unless(auth()->user()->hasRole('ServiceBody'))
                <x-button-a href="{{ route('meeting.create') }}" color='outline-primary' name="{{__('messages.Add') . ' ' . __('messages.Meeting')}}" class="rounded-pill" />
                @endunless
            </div>
            
            @if($meetings->count() > 0)
                <div class="row row-cols-1 row-cols-lg-2 g-4">
                    @foreach($meetings as $meeting)
                        <div class="col">
                            <div class="glass-card h-100 p-4 rounded-4 border position-relative transition-hover" style="border-color: var(--glass-border) !important; background: rgba(0,0,0,0.01);">
                                
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
        <div class="glass-card p-4 rounded-4 mt-5">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4" style="border-color: var(--glass-border) !important;">
                <h4 class="mb-0 fw-bold" style="color: var(--text-primary);"><i class="bi bi-journal-text me-2"></i> {{ __('messages.agendas') ?? 'Agendas' }}</h4>
                <x-button-a href="{{ route('agenda.create', ['group_id' => $group->id]) }}" color='outline-primary' name="{{__('messages.create_agenda') ?? 'Create Agenda'}}" class="rounded-pill" />
            </div>
            
            @if($group->agendas && $group->agendas->count() > 0)
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    @foreach($group->agendas->sortByDesc('agenda_date') as $agenda)
                        <div class="col">
                            <div class="glass-card h-100 p-4 rounded-4 border position-relative transition-hover d-flex flex-column justify-content-between" style="border-color: var(--glass-border) !important; background: rgba(255,255,255,0.4);">
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
                                        <span class="text-dark">{{ Str::limit($agenda->recovery_atmosphere, 120, '...') ?: '-' }}</span>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-3 border-top" style="border-color: var(--glass-border) !important;">
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
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#quickViewModalGroup{{ $agenda->id }}">
                                            <i class="bi bi-eye-fill"></i> {{ __('messages.Details') }}
                                        </button>
                                        <a href="{{ route('agenda.show', $agenda->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-arrow-right-short"></i> {{ __('messages.Show') }}
                                        </a>
                                        <a href="{{ route('agenda.exportPdf', $agenda->id) }}" class="btn btn-sm btn-secondary rounded-pill px-3">
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
                            <ul class="nav nav-pills nav-fill mb-4 p-1 rounded-3 bg-light" id="agendaTabGroup{{ $agenda->id }}" role="tablist" style="border: 1px solid var(--glass-border);">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-2 rounded-2 fw-bold" id="general-tab-group{{ $agenda->id }}" data-bs-toggle="tab" data-bs-target="#general-group{{ $agenda->id }}" type="button" role="tab" aria-controls="general-group{{ $agenda->id }}" aria-selected="true" style="font-size: 0.9rem;">
                                        <i class="bi bi-info-circle-fill me-1"></i> {{ __('messages.group_data') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-2 rounded-2 fw-bold" id="news-tab-group{{ $agenda->id }}" data-bs-toggle="tab" data-bs-target="#news-group{{ $agenda->id }}" type="button" role="tab" aria-controls="news-group{{ $agenda->id }}" aria-selected="false" style="font-size: 0.9rem;">
                                        <i class="bi bi-megaphone-fill me-1"></i> {{ __('messages.group_news') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
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
                                            <div class="p-3 rounded-3 bg-light text-secondary small" style="white-space: pre-line; border: 1px solid var(--glass-border);">{{ $agenda->recovery_atmosphere ?: '-' }}</div>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-people text-info me-2"></i> {{ __('messages.trusted_servants') }}</h6>
                                            <div class="p-3 rounded-3 bg-light text-secondary small" style="white-space: pre-line; border: 1px solid var(--glass-border);">{{ $agenda->trusted_servants ?: '-' }}</div>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-cash-coin text-success me-2"></i> {{ __('messages.financial_issues') }}</h6>
                                            <div class="p-3 rounded-3 bg-light text-secondary small" style="white-space: pre-line; border: 1px solid var(--glass-border);">{{ $agenda->financial_issues ?: '-' }}</div>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-three-dots text-secondary me-2"></i> {{ __('messages.other_topics') }}</h6>
                                            <div class="p-3 rounded-3 bg-light text-secondary small" style="white-space: pre-line; border: 1px solid var(--glass-border);">{{ $agenda->other_topics ?: '-' }}</div>
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
/* RTL specific fixes */
[dir="rtl"] .me-1 { margin-left: 0.25rem !important; margin-right: 0 !important; }
[dir="rtl"] .me-2 { margin-left: 0.5rem !important; margin-right: 0 !important; }
[dir="rtl"] .ms-auto { margin-right: auto !important; margin-left: 0 !important; }
</style>