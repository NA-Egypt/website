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
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-4">
                <div class="glass-card p-4 border-0 shadow-sm d-flex align-items-center justify-content-between position-relative overflow-hidden" 
                     style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.12), rgba(14, 165, 233, 0.06)); backdrop-filter: blur(12px); border-radius: 20px; transition: transform 0.3s ease, box-shadow 0.3s ease; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04) !important;">
                    <div style="z-index: 2;">
                        <span class="text-secondary small fw-bold text-uppercase d-block mb-1" style="letter-spacing: 0.5px; opacity: 0.85;">{{ __('messages.Total Agendas') }}</span>
                        <h2 class="mb-0 fw-extrabold text-primary" style="font-size: 2.2rem; font-weight: 800;">{{ $totalAgendas }}</h2>
                    </div>
                    <div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                         style="width: 58px; height: 58px; background: rgba(59, 130, 246, 0.15); z-index: 2;">
                        <i class="bi bi-journals fs-3 text-primary"></i>
                    </div>
                    <div style="position: absolute; right: -20px; bottom: -20px; font-size: 8rem; opacity: 0.03; color: var(--bs-primary); line-height: 1; pointer-events: none; font-weight: 900;">
                        <i class="bi bi-journals"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="glass-card p-4 border-0 shadow-sm d-flex align-items-center justify-content-between position-relative overflow-hidden" 
                     style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.12), rgba(5, 150, 105, 0.06)); backdrop-filter: blur(12px); border-radius: 20px; transition: transform 0.3s ease, box-shadow 0.3s ease; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04) !important;">
                    <div style="z-index: 2;">
                        <span class="text-secondary small fw-bold text-uppercase d-block mb-1" style="letter-spacing: 0.5px; opacity: 0.85;">{{ __('messages.Current Month Agendas') }}</span>
                        <h2 class="mb-0 fw-extrabold text-success" style="font-size: 2.2rem; font-weight: 800;">{{ $monthlyAgendas }}</h2>
                    </div>
                    <div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                         style="width: 58px; height: 58px; background: rgba(16, 185, 129, 0.15); z-index: 2;">
                        <i class="bi bi-calendar3 fs-3 text-success"></i>
                    </div>
                    <div style="position: absolute; right: -20px; bottom: -20px; font-size: 8rem; opacity: 0.03; color: var(--bs-success); line-height: 1; pointer-events: none; font-weight: 900;">
                        <i class="bi bi-calendar3"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="glass-card p-4 border-0 shadow-sm d-flex align-items-center justify-content-between position-relative overflow-hidden" 
                     style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(217, 119, 6, 0.06)); backdrop-filter: blur(12px); border-radius: 20px; transition: transform 0.3s ease, box-shadow 0.3s ease; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04) !important;">
                    <div style="z-index: 2;">
                        <span class="text-secondary small fw-bold text-uppercase d-block mb-1" style="letter-spacing: 0.5px; opacity: 0.85;">{{ __('messages.Active Groups with Agendas') }}</span>
                        <h2 class="mb-0 fw-extrabold text-warning" style="font-size: 2.2rem; font-weight: 800;">{{ $activeGroupsCount }}</h2>
                    </div>
                    <div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                         style="width: 58px; height: 58px; background: rgba(245, 158, 11, 0.15); z-index: 2;">
                        <i class="bi bi-people-fill fs-3 text-warning"></i>
                    </div>
                    <div style="position: absolute; right: -20px; bottom: -20px; font-size: 8rem; opacity: 0.03; color: var(--bs-warning); line-height: 1; pointer-events: none; font-weight: 900;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Filter & Search Card -->
        <div class="glass-card mb-4 border-0 shadow-sm overflow-hidden" style="background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(15px); border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03) !important;">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 px-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 fw-bold text-primary d-flex align-items-center">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-light p-2 me-2" style="background: rgba(59, 130, 246, 0.1); width: 36px; height: 36px;">
                            <i class="bi bi-funnel-fill text-primary" style="font-size: 1.1rem;"></i>
                        </span>
                        {{ __('messages.Filter Options') ?? 'Filter Options' }}
                    </h5>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-4 py-2 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="{{ request()->anyFilled(['search', 'group_id', 'start_date', 'end_date']) ? 'true' : 'false' }}" aria-controls="filterCollapse">
                        <i class="bi bi-filter-right me-1"></i> {{ __('messages.Toggle Filters') ?? 'Toggle Filters' }}
                    </button>
                </div>
            </div>
            <div class="collapse {{ request()->anyFilled(['search', 'group_id', 'start_date', 'end_date']) ? 'show' : '' }}" id="filterCollapse">
                <div class="card-body p-4 border-top" style="border-top-color: rgba(0, 0, 0, 0.05) !important;">
                    <form action="{{ route('groups-agendas.archive') }}" method="GET" id="searchFilterForm">
                        <div class="row g-3">
                            <div class="col-12 col-md-6 col-lg-4">
                                <label for="search" class="form-label fw-semibold text-muted small mb-2">{{ __('messages.Search') ?? 'Search' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3" style="border-color: #e2e8f0;"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" name="search" id="search" class="form-control bg-light border-start-0 rounded-end-3 py-2" value="{{ request('search') }}" placeholder="{{ __('messages.search_by_submitter_or_content') ?? 'Search by submitter or content...' }}" style="border-color: #e2e8f0; font-size: 0.95rem;">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label for="group_id" class="form-label fw-semibold text-muted small mb-2">{{ __('messages.Group') ?? 'Group' }}</label>
                                <select name="group_id" id="group_id" class="form-select bg-light py-2 rounded-3" style="border-color: #e2e8f0; font-size: 0.95rem;">
                                    <option value="">{{ __('messages.All Groups') ?? 'All Groups' }}</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                            {{ $group->ar_name ?? $group->en_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 col-lg-2">
                                <label for="start_date" class="form-label fw-semibold text-muted small mb-2">{{ __('messages.Start Date') ?? 'Start Date' }}</label>
                                <input type="date" name="start_date" id="start_date" class="form-control bg-light py-2 rounded-3" value="{{ request('start_date') }}" style="border-color: #e2e8f0; font-size: 0.95rem;">
                            </div>
                            <div class="col-12 col-md-6 col-lg-2">
                                <label for="end_date" class="form-label fw-semibold text-muted small mb-2">{{ __('messages.End Date') ?? 'End Date' }}</label>
                                <input type="date" name="end_date" id="end_date" class="form-control bg-light py-2 rounded-3" value="{{ request('end_date') }}" style="border-color: #e2e8f0; font-size: 0.95rem;">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-top-color: rgba(0, 0, 0, 0.05) !important;">
                            <a href="{{ route('groups-agendas.archive') }}" class="btn btn-light px-4 py-2 rounded-pill fw-semibold" style="font-size: 0.9rem;">
                                <i class="bi bi-x-circle me-1"></i> {{ __('messages.Reset') ?? 'Reset' }}
                            </a>
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold" style="font-size: 0.9rem;">
                                <i class="bi bi-search me-1"></i> {{ __('messages.Search') ?? 'Search' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if($archive->isEmpty())
            <div class="glass-card text-center py-5 px-4" style="background: rgba(255,255,255,0.7); border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.02) !important;">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 bg-light" style="width: 80px; height: 80px;">
                    <i class="bi bi-info-circle text-primary" style="font-size: 2.2rem;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">{{ __('messages.No agendas archived yet.') ?? 'No agendas archived yet.' }}</h5>
                <p class="text-secondary mb-0 small">{{ __('messages.Try adjusting filters or search parameters.') ?? 'Try adjusting filters or search parameters.' }}</p>
            </div>
        @else
            <!-- Bulk Export Form -->
            <form action="{{ route('groups-agendas.exportPdf') }}" method="POST" id="bulkExportForm">
                @csrf
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 px-2">
                    <div class="form-check d-flex align-items-center">
                        <input class="form-check-input cursor-pointer" type="checkbox" id="selectAllAgendas" style="width: 20px; height: 20px; margin-top: 0;">
                        <label class="form-check-label fw-bold ms-2 cursor-pointer text-dark" for="selectAllAgendas" style="font-size: 0.95rem; user-select: none;">
                            {{ __('messages.Select All') ?? 'Select All' }}
                        </label>
                    </div>
                    <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm hover-up">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> {{ __('messages.export_selected_to_pdf') ?? 'Export Selected to PDF' }}
                    </button>
                </div>

                <div class="accordion border-0" id="archiveAccordion">
                    @foreach($archive as $year => $months)
                        <div class="accordion-item shadow-sm mb-4 border-0" style="border-radius: 16px; overflow: hidden; background: transparent;">
                            <h2 class="accordion-header" id="heading-{{ $year }}">
                                <button class="accordion-button {{ request()->anyFilled(['search', 'group_id', 'start_date', 'end_date']) ? '' : 'collapsed' }} fw-bold fs-5 text-dark px-4 py-3" 
                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $year }}" aria-expanded="{{ request()->anyFilled(['search', 'group_id', 'start_date', 'end_date']) ? 'true' : 'false' }}" aria-controls="collapse-{{ $year }}"
                                        style="background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-bottom: 1px solid rgba(0,0,0,0.03); box-shadow: none;">
                                    <i class="bi bi-calendar-event text-primary me-2"></i> {{ $year }}
                                </button>
                            </h2>
                            <div id="collapse-{{ $year }}" class="accordion-collapse collapse {{ request()->anyFilled(['search', 'group_id', 'start_date', 'end_date']) ? 'show' : '' }}" aria-labelledby="heading-{{ $year }}" data-bs-parent="#archiveAccordion">
                                <div class="accordion-body bg-white p-4" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                                    @foreach($months as $monthNum => $agendas)
                                        @php
                                            $monthName = \App\Services\DateNumberHelper::translatedFormat(\Carbon\Carbon::create(null, (int) $monthNum, 1), 'F');
                                        @endphp
                                        <div class="mb-5">
                                            <h5 class="text-secondary border-bottom pb-2 mb-4 fw-bold d-flex align-items-center">
                                                <i class="bi bi-calendar-month me-2 text-info fs-5"></i> {{ $monthName }}
                                                <span class="badge rounded-pill bg-light text-secondary ms-2 border px-3 py-1 font-monospace" style="font-size: 0.75rem;">{{ $agendas->count() }}</span>
                                            </h5>
                                            
                                            <!-- Modern Multi-Column Grid of Agenda Cards -->
                                            <div class="row row-cols-1 row-cols-md-2 row-cols-xxl-3 g-4">
                                                @foreach($agendas as $agenda)
                                                    <div class="col">
                                                        <div class="glass-card p-4 border d-flex flex-column justify-content-between h-100 position-relative transition-hover" 
                                                             style="border: 1px solid rgba(0,0,0,0.06) !important; background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.95)); border-radius: 18px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.025); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                                                            <div>
                                                                <div class="d-flex w-100 justify-content-between align-items-start gap-2 mb-3">
                                                                    <div class="d-flex align-items-start gap-3">
                                                                        <div class="mt-1">
                                                                            <input type="checkbox" name="agenda_ids[]" value="{{ $agenda->id }}" class="form-check-input agenda-checkbox cursor-pointer shadow-sm" style="width: 19px; height: 19px;">
                                                                        </div>
                                                                        <div>
                                                                            <h5 class="mb-1 text-primary fw-bold text-wrap" style="font-size: 1.1rem; line-height: 1.35;">
                                                                                {{ $agenda->group->ar_name ?? $agenda->group->en_name }}
                                                                            </h5>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="d-flex flex-column gap-2 mb-3">
                                                                    <div class="d-flex align-items-center gap-2 text-muted small">
                                                                        <i class="bi bi-person text-secondary"></i>
                                                                        <span class="text-truncate">
                                                                            <strong>{{ __('messages.submitter_name') }}:</strong> {{ $agenda->submitter_name ?: __('messages.Not provided') }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="d-flex align-items-center gap-2 text-muted small">
                                                                        <i class="bi bi-calendar3 text-secondary"></i>
                                                                        <span>
                                                                            <strong>{{ __('messages.agenda_date') }}:</strong> {{ \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'Y-m-d') }}
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3 text-secondary small p-3 rounded-3" style="background: rgba(0, 0, 0, 0.02); border: 1px dashed rgba(0,0,0,0.08); line-height: 1.5; font-size: 0.85rem;">
                                                                    <strong class="text-dark d-block mb-1">{{ __('messages.recovery_atmosphere') }}:</strong>
                                                                    @php
                                                                        $recAtmText = is_array($agenda->recovery_atmosphere) ? implode(', ', $agenda->recovery_atmosphere) : $agenda->recovery_atmosphere;
                                                                    @endphp
                                                                    <span class="text-secondary" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; min-height: 3.8em;">
                                                                        {{ $recAtmText ?: '-' }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            
                                                            <div>
                                                                <div class="d-flex flex-wrap gap-2 mb-3 pt-2">
                                                                    <span class="badge bg-primary-light text-primary px-2.5 py-1.5 rounded-pill d-flex align-items-center gap-1 small" style="background: rgba(59, 130, 246, 0.08); font-weight: 600;">
                                                                        <i class="bi bi-calendar-check"></i>
                                                                        {{ $agenda->meetings_per_week }} {{ __('messages.meetings_per_week') }}
                                                                    </span>
                                                                    @if($agenda->new_comers > 0)
                                                                        <span class="badge bg-success-light text-success px-2.5 py-1.5 rounded-pill d-flex align-items-center gap-1 small" style="background: rgba(16, 185, 129, 0.08); font-weight: 600;">
                                                                            <i class="bi bi-person-plus"></i>
                                                                            {{ $agenda->new_comers }} {{ __('messages.new_comers') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                
                                                                <!-- Action Buttons -->
                                                                <div class="d-flex gap-2 pt-3 border-top" style="border-top-color: rgba(0, 0, 0, 0.05) !important;">
                                                                    <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1.5 flex-grow-1 fw-semibold d-flex align-items-center justify-content-center gap-1" data-bs-toggle="modal" data-bs-target="#quickViewModal{{ $agenda->id }}">
                                                                        <i class="bi bi-eye-fill"></i> {{ __('messages.Details') }}
                                                                    </button>
                                                                    <a href="{{ route('agenda.show', $agenda->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 flex-grow-1 fw-semibold d-flex align-items-center justify-content-center gap-1">
                                                                        <i class="bi bi-arrow-right-short"></i> {{ __('messages.Show') }}
                                                                    </a>
                                                                    <a href="{{ route('agenda.exportPdf', $agenda->id) }}" class="btn btn-sm btn-secondary rounded-pill px-3 py-1.5 fw-semibold d-flex align-items-center justify-content-center" title="{{ __('messages.PDF') }}">
                                                                        <i class="bi bi-file-earmark-pdf"></i>
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
                                <div class="modal-content glass-card border-0" style="background: rgba(255, 255, 255, 0.98) !important; backdrop-filter: blur(25px); border-radius: 22px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.15) !important;">
                                    {{-- Accent Gradient Header --}}
                                    <div class="modal-header border-bottom-0 pb-3 pt-4 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(14, 165, 233, 0.04)); border-bottom: 1px solid rgba(0,0,0,0.05) !important;">
                                        <h5 class="modal-title fw-bold text-primary mb-0 d-flex align-items-center" id="quickViewModalLabel{{ $agenda->id }}">
                                            <i class="bi bi-journal-richtext text-primary me-2 fs-4"></i>
                                            <span class="text-truncate" style="max-width: 500px;">
                                                {{ $agenda->group->ar_name ?? $agenda->group->en_name }} - {{ __('messages.month_year_agenda', ['month' => \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'F'), 'year' => \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'Y')]) }}
                                            </span>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body px-4 pt-3 pb-4">
                                        {{-- Nav Pill Tabs --}}
                                        <ul class="nav nav-pills nav-fill mb-4 p-1 rounded-3 bg-light" id="agendaTab{{ $agenda->id }}" role="tablist" style="border: 1px solid rgba(0,0,0,0.04);">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active py-2.5 rounded-2 fw-bold" id="general-tab{{ $agenda->id }}" data-bs-toggle="tab" data-bs-target="#general{{ $agenda->id }}" type="button" role="tab" aria-controls="general{{ $agenda->id }}" aria-selected="true" style="font-size: 0.9rem; transition: all 0.2s;">
                                                    <i class="bi bi-info-circle-fill me-1"></i> {{ __('messages.group_data') }}
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link py-2.5 rounded-2 fw-bold" id="news-tab{{ $agenda->id }}" data-bs-toggle="tab" data-bs-target="#news{{ $agenda->id }}" type="button" role="tab" aria-controls="news{{ $agenda->id }}" aria-selected="false" style="font-size: 0.9rem; transition: all 0.2s;">
                                                    <i class="bi bi-megaphone-fill me-1"></i> {{ __('messages.group_news') }}
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link py-2.5 rounded-2 fw-bold" id="agenda-content-tab{{ $agenda->id }}" data-bs-toggle="tab" data-bs-target="#agenda-content{{ $agenda->id }}" type="button" role="tab" aria-controls="agenda-content{{ $agenda->id }}" aria-selected="false" style="font-size: 0.9rem; transition: all 0.2s;">
                                                    <i class="bi bi-file-text-fill me-1"></i> {{ __('messages.the_agenda') }}
                                                </button>
                                            </li>
                                        </ul>

                                        <div class="tab-content text-start" id="agendaTabContent{{ $agenda->id }}">
                                            {{-- Tab 1: General Info --}}
                                            <div class="tab-pane fade show active" id="general{{ $agenda->id }}" role="tabpanel" aria-labelledby="general-tab{{ $agenda->id }}">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="p-3 rounded-3 h-100" style="background: rgba(59, 130, 246, 0.02); border: 1px solid rgba(0,0,0,0.04);">
                                                            <span class="text-secondary small d-block mb-1">{{ __('messages.submitter_name') ?? 'Submitter Name' }}</span>
                                                            <h6 class="fw-bold mb-0 text-dark">{{ $agenda->submitter_name ?: __('messages.Not provided') }}</h6>
                                                            @if($agenda->service_position)
                                                                <span class="badge bg-primary mt-2 rounded-pill px-3">{{ $agenda->translated_service_position }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="p-3 rounded-3 h-100" style="background: rgba(0, 0, 0, 0.01); border: 1px solid rgba(0,0,0,0.04);">
                                                            <span class="text-secondary small d-block mb-1">{{ __('messages.agenda_date') }}</span>
                                                            <h6 class="fw-bold mb-0 text-dark">{{ \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'd M Y') }}</h6>
                                                        </div>
                                                    </div>
                                                    
                                                    @if($agenda->alt_gsr_name)
                                                    <div class="col-md-6">
                                                        <div class="p-3 rounded-3 h-100" style="background: rgba(0, 0, 0, 0.01); border: 1px solid rgba(0,0,0,0.04);">
                                                            <span class="text-secondary small d-block mb-1">{{ __('messages.alt_gsr_name') ?? 'Alt. GSR Name' }}</span>
                                                            <h6 class="fw-bold mb-0 text-dark">{{ $agenda->alt_gsr_name }}</h6>
                                                            @if($agenda->alt_gsr_position)
                                                                <span class="badge bg-secondary mt-2 rounded-pill px-3">{{ $agenda->translated_alt_gsr_position }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @endif

                                                    <div class="col-md-6">
                                                        <div class="p-3 rounded-3 h-100" style="background: rgba(59, 130, 246, 0.01); border: 1px solid rgba(0,0,0,0.04);">
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
                                                        <div class="p-3 text-center rounded-3 h-100" style="background: rgba(59, 130, 246, 0.03); border: 1px solid rgba(59, 130, 246, 0.08);">
                                                            <span class="text-secondary small d-block mb-1">{{ __('messages.meetings_per_week') }}</span>
                                                            <span class="fw-bold text-primary fs-4">{{ $agenda->meetings_per_week }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <div class="p-3 text-center rounded-3 h-100" style="background: rgba(16, 185, 129, 0.03); border: 1px solid rgba(16, 185, 129, 0.08);">
                                                            <span class="text-secondary small d-block mb-1">{{ __('messages.new_comers') }}</span>
                                                            <span class="fw-bold text-success fs-4">{{ $agenda->new_comers }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-12">
                                                        <div class="p-3 text-center rounded-3 h-100" style="background: rgba(245, 158, 11, 0.03); border: 1px solid rgba(245, 158, 11, 0.08);">
                                                            <span class="text-secondary small d-block mb-1">{{ __('messages.next_business_meeting') }}</span>
                                                            <span class="fw-bold text-warning" style="font-size: 0.95rem;">{{ $agenda->next_business_meeting ? \App\Services\DateNumberHelper::translatedFormat($agenda->next_business_meeting, 'd M Y') : '-' }}</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-12">
                                                        <div class="p-3 rounded-3 d-flex align-items-center justify-content-between" style="background: rgba(0, 0, 0, 0.01); border: 1px solid rgba(0,0,0,0.04);">
                                                            <span class="text-secondary small fw-medium">{{ __('messages.recovery_meetings_changes') ?? 'Recovery Meetings Changes' }}</span>
                                                            <span class="badge {{ $agenda->recovery_meetings_changes ? 'bg-danger' : 'bg-success' }} px-3 py-2 rounded-pill fw-bold">
                                                                {{ $agenda->recovery_meetings_changes ? (__('messages.yes') ?? 'Yes') : (__('messages.no') ?? 'No') }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="p-3 rounded-3" style="background: rgba(0, 0, 0, 0.01); border: 1px solid rgba(0,0,0,0.04);">
                                                            <span class="text-secondary small d-block mb-2">{{ __('messages.open_positions') ?? 'Open Positions' }}</span>
                                                            <div class="text-dark small fw-semibold" style="white-space: pre-line;">{{ $agenda->open_positions ?: 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Tab 3: Agenda Content --}}
                                            <div class="tab-pane fade" id="agenda-content{{ $agenda->id }}" role="tabpanel" aria-labelledby="agenda-content-tab{{ $agenda->id }}">
                                                <div class="d-flex flex-column gap-3">
                                                    <div>
                                                        <h6 class="fw-bold text-dark mb-1.5 d-flex align-items-center gap-1.5"><i class="bi bi-chat-left-text text-primary"></i> {{ __('messages.recovery_atmosphere') }}</h6>
                                                        <div class="p-3 rounded-3 bg-light text-secondary small" style="white-space: pre-line; border: 1px solid rgba(0,0,0,0.04); line-height: 1.6;">
                                                            @if(is_array($agenda->recovery_atmosphere))
                                                                {{ implode("\n", $agenda->recovery_atmosphere) ?: '-' }}
                                                            @else
                                                                {{ $agenda->recovery_atmosphere ?: '-' }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold text-dark mb-1.5 d-flex align-items-center gap-1.5"><i class="bi bi-people text-info"></i> {{ __('messages.trusted_servants') }}</h6>
                                                        <div class="p-3 rounded-3 bg-light text-secondary small" style="white-space: pre-line; border: 1px solid rgba(0,0,0,0.04); line-height: 1.6;">
                                                            @if(is_array($agenda->trusted_servants))
                                                                {{ implode("\n", $agenda->trusted_servants) ?: '-' }}
                                                            @else
                                                                {{ $agenda->trusted_servants ?: '-' }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold text-dark mb-1.5 d-flex align-items-center gap-1.5"><i class="bi bi-cash-coin text-success"></i> {{ __('messages.financial_issues') }}</h6>
                                                        <div class="p-3 rounded-3 bg-light text-secondary small" style="white-space: pre-line; border: 1px solid rgba(0,0,0,0.04); line-height: 1.6;">
                                                            @if(is_array($agenda->financial_issues))
                                                                {{ implode("\n", $agenda->financial_issues) ?: '-' }}
                                                            @else
                                                                {{ $agenda->financial_issues ?: '-' }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold text-dark mb-1.5 d-flex align-items-center gap-1.5"><i class="bi bi-three-dots text-secondary"></i> {{ __('messages.other_topics') }}</h6>
                                                        <div class="p-3 rounded-3 bg-light text-secondary small" style="white-space: pre-line; border: 1px solid rgba(0,0,0,0.04); line-height: 1.6;">
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
                                    <div class="modal-footer border-top-0 pt-0 px-4 pb-4 gap-2">
                                        <a href="{{ route('agenda.exportPdf', $agenda->id) }}" class="btn btn-outline-success rounded-pill px-4 btn-sm fw-semibold">
                                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> {{ __('messages.PDF') }}
                                        </a>
                                        <a href="{{ route('agenda.show', $agenda->id) }}" class="btn btn-primary rounded-pill px-4 btn-sm fw-semibold">
                                            <i class="bi bi-eye-fill me-1"></i> {{ __('messages.Show') }}
                                        </a>
                                        <button type="button" class="btn btn-secondary rounded-pill px-4 btn-sm fw-semibold" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
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
