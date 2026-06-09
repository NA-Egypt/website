<x-layout>
    <x-backhead>{{ __('messages.Agendas Archive') ?? 'Agendas Archive' }}</x-backhead>

    <div class="container mt-4">
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
                                            $monthName = \Carbon\Carbon::create(null, (int) $monthNum, 1)->translatedFormat('F');
                                        @endphp
                                        <div class="mb-4">
                                            <h5 class="text-secondary border-bottom pb-2 mb-3 fw-bold">
                                                <i class="bi bi-calendar-month me-1 text-info"></i> {{ $monthName }}
                                            </h5>
                                            
                                            <div class="list-group">
                                                @foreach($agendas as $agenda)
                                                    <div class="list-group-item list-group-item-action flex-column align-items-start p-3 mb-2 border rounded shadow-sm">
                                                        <div class="d-flex w-100 justify-content-between align-items-center flex-wrap gap-2">
                                                            <div class="d-flex align-items-center gap-3">
                                                                <input type="checkbox" name="agenda_ids[]" value="{{ $agenda->id }}" class="form-check-input agenda-checkbox">
                                                                <h6 class="mb-1 text-dark fw-bold fs-5">
                                                                    {{ $agenda->group->ar_name ?? $agenda->group->en_name }}
                                                                </h6>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                                <span class="text-muted small fw-bold me-3">
                                                                    <strong>{{ __('messages.submitter_name') ?? 'Submitter Name' }}:</strong> {{ $agenda->submitter_name ?: __('messages.Not provided') }}
                                                                </span>
                                                                <span class="text-muted small fw-bold">
                                                                    <strong>{{ __('messages.agenda_date') ?? 'Agenda Date' }}:</strong> {{ \Carbon\Carbon::parse($agenda->agenda_date)->format('Y-m-d') }}
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class="mt-2 text-muted small">
                                                            <strong>{{ __('messages.recovery_atmosphere') ?? 'Recovery Atmosphere' }}:</strong>
                                                            <span class="text-dark">{{ Str::limit($agenda->recovery_atmosphere, 150, '...') ?: '-' }}</span>
                                                        </div>
                                                        
                                                        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-3">
                                                            <div class="text-muted small">
                                                                <strong>{{ __('messages.meetings_per_week') ?? 'Meetings per week' }}:</strong> {{ $agenda->meetings_per_week }} | 
                                                                <strong>{{ __('messages.new_comers') ?? 'Newcomers' }}:</strong> {{ $agenda->new_comers }}
                                                            </div>
                                                            
                                                            <!-- Actions -->
                                                            <div class="d-flex gap-2 align-self-end">
                                                                <a href="{{ route('agenda.show', $agenda->id) }}" class="btn btn-sm btn-outline-primary">
                                                                    <i class="bi bi-eye"></i> {{ __('messages.Show') }}
                                                                </a>
                                                                <a href="{{ route('agenda.exportPdf', $agenda->id) }}" class="btn btn-sm btn-secondary">
                                                                    <i class="bi bi-file-earmark-pdf"></i> {{ __('messages.PDF') ?? 'PDF' }}
                                                                </a>
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
