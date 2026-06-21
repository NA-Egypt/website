<x-layout>
    <x-backhead>
        @if(isset($selectedGroup) && $selectedGroup)
            {{ __('messages.agendas_archive_of', ['name' => (app()->getLocale() === 'ar' ? $selectedGroup->ar_name : $selectedGroup->en_name)]) }}
        @else
            {{ __('messages.Agendas Archive') ?? 'Agendas Archive' }}
        @endif
    </x-backhead>

    <div class="container mt-4">
        <!-- Statistics Summary Section -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="glass-card p-4 border rounded-4 shadow-sm transition-hover d-flex align-items-center justify-content-between" style="border-color: var(--glass-border) !important; background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(14, 165, 233, 0.05)); backdrop-filter: blur(10px);">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase d-block mb-1">{{ __('messages.Total Agendas') }}</span>
                        <h3 class="mb-0 fw-bold text-primary">{{ $totalAgendas }}</h3>
                    </div>
                    <div class="rounded-pill p-3 bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="bi bi-journals fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="glass-card p-4 border rounded-4 shadow-sm transition-hover d-flex align-items-center justify-content-between" style="border-color: var(--glass-border) !important; background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.05)); backdrop-filter: blur(10px);">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase d-block mb-1">{{ __('messages.Current Month Agendas') }}</span>
                        <h3 class="mb-0 fw-bold text-success">{{ $monthlyAgendas }}</h3>
                    </div>
                    <div class="rounded-pill p-3 bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="bi bi-calendar3 fs-4 text-success"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="glass-card p-4 border rounded-4 shadow-sm transition-hover d-flex align-items-center justify-content-between" style="border-color: var(--glass-border) !important; background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.05)); backdrop-filter: blur(10px);">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase d-block mb-1">{{ __('messages.Active Groups with Agendas') }}</span>
                        <h3 class="mb-0 fw-bold text-warning">{{ $activeGroupsCount }}</h3>
                    </div>
                    <div class="rounded-pill p-3 bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="bi bi-people-fill fs-4 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Filter & Search Card -->
        <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-funnel-fill me-2"></i>{{ __('messages.Filter Options') ?? 'Filter Options' }}
                    </h5>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="{{ request()->anyFilled(['search', 'group_id', 'start_date', 'end_date']) ? 'true' : 'false' }}" aria-controls="filterCollapse">
                        <i class="bi bi-filter"></i> {{ __('messages.Toggle Filters') ?? 'Toggle Filters' }}
                    </button>
                </div>
            </div>
            <div class="collapse {{ request()->anyFilled(['search', 'group_id', 'start_date', 'end_date']) ? 'show' : '' }}" id="filterCollapse">
                <div class="card-body p-4 border-top">
                    <form action="{{ route('groups-agendas.archive') }}" method="GET" id="searchFilterForm">
                        <div class="row g-3">
                            <div class="col-12 col-md-6 col-lg-4">
                                <label for="search" class="form-label fw-semibold text-muted">{{ __('messages.Search') ?? 'Search' }}</label>
                                <input type="text" name="search" id="search" class="form-control rounded-3" value="{{ request('search') }}" placeholder="{{ __('messages.search_by_submitter_or_content') ?? 'Search by submitter or content...' }}">
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label for="group_id" class="form-label fw-semibold text-muted">{{ __('messages.Group') ?? 'Group' }}</label>
                                <select name="group_id" id="group_id" class="form-select rounded-3">
                                    <option value="">{{ __('messages.All Groups') ?? 'All Groups' }}</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                            {{ $group->ar_name ?? $group->en_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 col-lg-2">
                                <label for="start_date" class="form-label fw-semibold text-muted">{{ __('messages.Start Date') ?? 'Start Date' }}</label>
                                <input type="date" name="start_date" id="start_date" class="form-control rounded-3" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-12 col-md-6 col-lg-2">
                                <label for="end_date" class="form-label fw-semibold text-muted">{{ __('messages.End Date') ?? 'End Date' }}</label>
                                <input type="date" name="end_date" id="end_date" class="form-control rounded-3" value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('groups-agendas.archive') }}" class="btn btn-light px-4 rounded-pill">
                                <i class="bi bi-x-circle me-1"></i> {{ __('messages.Reset') ?? 'Reset' }}
                            </a>
                            <button type="submit" class="btn btn-primary px-4 rounded-pill">
                                <i class="bi bi-search me-1"></i> {{ __('messages.Search') ?? 'Search' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if($archive->isEmpty())
            <div class="alert alert-info text-center py-4">
                <i class="bi bi-info-circle-fill fs-3 d-block mb-2 text-primary"></i>
                {{ __('messages.No agendas archived yet.') ?? 'No agendas archived yet.' }}
            </div>
        @else
            <!-- Bulk Export Form -->
            <form action="{{ route('groups-agendas.exportPdf') }}" method="POST" id="bulkExportForm">
                @csrf
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllAgendas">
                        <label class="form-check-label fw-semibold" for="selectAllAgendas">
                            {{ __('messages.Select All') ?? 'Select All' }}
                        </label>
                    </div>
                    <button type="submit" class="btn btn-outline-success rounded-pill px-4">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> {{ __('messages.export_selected_to_pdf') ?? 'Export Selected to PDF' }}
                    </button>
                </div>

                <div class="accordion" id="archiveAccordion">
                    @foreach($archive as $year => $months)
                        <div class="accordion-item shadow-sm mb-3 border rounded">
                            <h2 class="accordion-header" id="heading-{{ $year }}">
                                <button class="accordion-button {{ request()->anyFilled(['search', 'group_id', 'start_date', 'end_date']) ? '' : 'collapsed' }} fw-bold fs-5 text-dark bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $year }}" aria-expanded="{{ request()->anyFilled(['search', 'group_id', 'start_date', 'end_date']) ? 'true' : 'false' }}" aria-controls="collapse-{{ $year }}">
                                    <i class="bi bi-calendar-event text-primary me-2"></i> {{ $year }}
                                </button>
                            </h2>
                            <div id="collapse-{{ $year }}" class="accordion-collapse collapse {{ request()->anyFilled(['search', 'group_id', 'start_date', 'end_date']) ? 'show' : '' }}" aria-labelledby="heading-{{ $year }}" data-bs-parent="#archiveAccordion">
                                <div class="accordion-body bg-white p-4">
                                    @foreach($months as $monthNum => $agendas)
                                        @php
                                            $monthName = \App\Services\DateNumberHelper::translatedFormat(\Carbon\Carbon::create(null, (int) $monthNum, 1), 'F');
                                        @endphp
                                        <div class="mb-4">
                                            <h5 class="text-secondary border-bottom pb-2 mb-3 fw-bold">
                                                <i class="bi bi-calendar-month me-1 text-info"></i> {{ $monthName }}
                                            </h5>
                                            
                                            <div class="row row-cols-1 g-3">
                                                @foreach($agendas as $agenda)
                                                    <div class="col">
                                                        <div class="glass-card p-4 border rounded-4 transition-hover d-flex flex-column justify-content-between mb-2" style="border: 1px solid rgba(255, 255, 255, 0.4) !important; background: linear-gradient(135deg, rgba(255, 255, 255, 0.65), rgba(255, 255, 255, 0.35)); backdrop-filter: blur(12px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02); transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;">
                                                            <div class="d-flex w-100 justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                                                <div class="d-flex align-items-center gap-3">
                                                                    <input type="checkbox" name="agenda_ids[]" value="{{ $agenda->id }}" class="form-check-input agenda-checkbox shadow-sm">
                                                                    <h5 class="mb-0 text-primary fw-bold">
                                                                        {{ $agenda->group->ar_name ?? $agenda->group->en_name }}
                                                                    </h5>
                                                                </div>
                                                                <div class="d-flex align-items-center gap-2 flex-wrap small">
                                                                    <span class="neo-badge neo-badge-info px-3 py-2 d-flex align-items-center gap-1">
                                                                        <i class="bi bi-person"></i>
                                                                        <strong>{{ __('messages.submitter_name') }}:</strong> {{ $agenda->submitter_name ?: __('messages.Not provided') }}
                                                                    </span>
                                                                    <span class="neo-badge neo-badge-primary px-3 py-2 d-flex align-items-center gap-1">
                                                                        <i class="bi bi-calendar3"></i>
                                                                        <strong>{{ __('messages.agenda_date') }}:</strong> {{ \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'Y-m-d') }}
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div class="mb-3 text-secondary small p-3 rounded-3" style="background: rgba(255, 255, 255, 0.4); border: 1px dashed var(--glass-border); line-height: 1.6;">
                                                                <strong>{{ __('messages.recovery_atmosphere') }}:</strong>
                                                                @php
                                                                    $recAtmText = is_array($agenda->recovery_atmosphere) ? implode(', ', $agenda->recovery_atmosphere) : $agenda->recovery_atmosphere;
                                                                @endphp
                                                                <span class="text-dark">{{ Str::limit($recAtmText, 200, '...') ?: '-' }}</span>
                                                            </div>
                                                            
                                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-3 border-top" style="border-color: rgba(0, 0, 0, 0.05) !important;">
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    <span class="neo-badge neo-badge-primary px-3 py-2 d-flex align-items-center gap-1">
                                                                        <i class="bi bi-calendar-check"></i>
                                                                        {{ $agenda->meetings_per_week }} {{ __('messages.meetings_per_week') }}
                                                                    </span>
                                                                    @if($agenda->new_comers > 0)
                                                                        <span class="neo-badge neo-badge-success px-3 py-2 d-flex align-items-center gap-1">
                                                                            <i class="bi bi-person-plus"></i>
                                                                            {{ $agenda->new_comers }} {{ __('messages.new_comers') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                
                                                                <!-- Actions -->
                                                                <div class="d-flex gap-2">
                                                                    <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#quickViewModal{{ $agenda->id }}">
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
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </form>

            {{-- Render all modals at root context to prevent styling overlay issues --}}
            @foreach($archive as $year => $months)
                @foreach($months as $monthNum => $agendas)
                    @foreach($agendas as $agenda)
                        <div class="modal fade" id="quickViewModal{{ $agenda->id }}" tabindex="-1" aria-labelledby="quickViewModalLabel{{ $agenda->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content glass-card border-0" style="background: rgba(255, 255, 255, 0.96) !important; backdrop-filter: blur(25px); border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;">
                                    {{-- HSL Gradient Header Accent --}}
                                    <div class="modal-header border-bottom-0 pb-3 pt-4 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(14, 165, 233, 0.05)); border-bottom: 1px solid var(--glass-border) !important;">
                                        <h5 class="modal-title fw-bold text-primary mb-0" id="quickViewModalLabel{{ $agenda->id }}">
                                            <i class="bi bi-journal-richtext text-primary me-2"></i>{{ $agenda->group->ar_name ?? $agenda->group->en_name }} - {{ __('messages.month_year_agenda', ['month' => \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'F'), 'year' => \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'Y')]) }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body px-4 pt-3 pb-4">
                                        {{-- Custom styled tab navs --}}
                                        <ul class="nav nav-pills nav-fill mb-4 p-1 rounded-3 bg-light" id="agendaTab{{ $agenda->id }}" role="tablist" style="border: 1px solid var(--glass-border);">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active py-2 rounded-2 fw-bold" id="general-tab{{ $agenda->id }}" data-bs-toggle="tab" data-bs-target="#general{{ $agenda->id }}" type="button" role="tab" aria-controls="general{{ $agenda->id }}" aria-selected="true" style="font-size: 0.9rem;">
                                                    <i class="bi bi-info-circle-fill me-1"></i> {{ __('messages.group_data') }}
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link py-2 rounded-2 fw-bold" id="news-tab{{ $agenda->id }}" data-bs-toggle="tab" data-bs-target="#news{{ $agenda->id }}" type="button" role="tab" aria-controls="news{{ $agenda->id }}" aria-selected="false" style="font-size: 0.9rem;">
                                                    <i class="bi bi-megaphone-fill me-1"></i> {{ __('messages.group_news') }}
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link py-2 rounded-2 fw-bold" id="agenda-content-tab{{ $agenda->id }}" data-bs-toggle="tab" data-bs-target="#agenda-content{{ $agenda->id }}" type="button" role="tab" aria-controls="agenda-content{{ $agenda->id }}" aria-selected="false" style="font-size: 0.9rem;">
                                                    <i class="bi bi-file-text-fill me-1"></i> {{ __('messages.the_agenda') }}
                                                </button>
                                            </li>
                                        </ul>

                                        <div class="tab-content text-start" id="agendaTabContent{{ $agenda->id }}">
                                            {{-- Tab 1: General Info --}}
                                            <div class="tab-pane fade show active" id="general{{ $agenda->id }}" role="tabpanel" aria-labelledby="general-tab{{ $agenda->id }}">
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
                                            <div class="tab-pane fade" id="news{{ $agenda->id }}" role="tabpanel" aria-labelledby="news-tab{{ $agenda->id }}">
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
                                            <div class="tab-pane fade" id="agenda-content{{ $agenda->id }}" role="tabpanel" aria-labelledby="agenda-content-tab{{ $agenda->id }}">
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
                                                        <div class="p-3 rounded-3 bg-light text-secondary small" style="white-space: pre-line; border: 1px solid var(--glass-border);">
                                                            @if(is_array($agenda->other_topics))
                                                                @php
                                                                    $lines = [];
                                                                    foreach($agenda->other_topics as $item) {
                                                                        if (is_array($item) && isset($item['title'])) {
                                                                            $lines[] = $item['title'] . ": " . $item['content'];
                                                                        } else {
                                                                            $lines[] = $item;
                                                                        }
                                                                    }
                                                                @endphp
                                                                {{ implode("\n", $lines) ?: '-' }}
                                                            @else
                                                                {{ $agenda->other_topics ?: '-' }}
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
                @endforeach
            @endforeach
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAllAgendas');
            const agendaCheckboxes = document.querySelectorAll('.agenda-checkbox');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    agendaCheckboxes.forEach(cb => {
                        cb.checked = selectAllCheckbox.checked;
                    });
                });
            }

            // Update "Select All" status based on individual checkboxes
            agendaCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    } else {
                        const allChecked = Array.from(agendaCheckboxes).every(c => c.checked);
                        selectAllCheckbox.checked = allChecked;
                    }
                });
            });

            // Prevent form submit if no checkboxes are selected
            const bulkForm = document.getElementById('bulkExportForm');
            if (bulkForm) {
                bulkForm.addEventListener('submit', function(e) {
                    const selected = Array.from(agendaCheckboxes).some(cb => cb.checked);
                    if (!selected) {
                        e.preventDefault();
                        alert('{{ __("messages.no_agendas_selected") ?? "No agendas selected" }}');
                    }
                });
            }
        });
    </script>
    @endpush
</x-layout>
