<x-layout>
    <x-backhead>{{ __('messages.Reports Archive') ?? 'Reports Archive' }}</x-backhead>

    <div class="container mt-4">
        <!-- Advanced Filter & Search Card -->
        <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-funnel-fill me-2"></i>{{ __('messages.Filter Options') ?? 'Filter Options' }}
                    </h5>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="{{ request()->anyFilled(['search', 'committee_id', 'start_date', 'end_date', 'exceptional']) ? 'true' : 'false' }}" aria-controls="filterCollapse">
                        <i class="bi bi-filter"></i> {{ __('messages.Toggle Filters') ?? 'Toggle Filters' }}
                    </button>
                </div>
            </div>
            <div class="collapse {{ request()->anyFilled(['search', 'committee_id', 'start_date', 'end_date', 'exceptional']) ? 'show' : '' }}" id="filterCollapse">
                <div class="card-body p-4 border-top">
                    <form action="{{ route('committee-reports.archive') }}" method="GET" id="searchFilterForm">
                        <div class="row g-3">
                            <div class="col-12 col-md-6 col-lg-3">
                                <label for="search" class="form-label fw-semibold text-muted">{{ __('messages.Search') ?? 'Search' }}</label>
                                <input type="text" name="search" id="search" class="form-control rounded-3" value="{{ request('search') }}" placeholder="{{ __('messages.Search by day or body') ?? 'Search by day or body...' }}">
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <label for="committee_id" class="form-label fw-semibold text-muted">{{ __('messages.Committee') ?? 'Committee' }}</label>
                                <select name="committee_id" id="committee_id" class="form-select rounded-3">
                                    <option value="">{{ __('messages.All Committees') ?? 'All Committees' }}</option>
                                    @foreach($committees as $committee)
                                        <option value="{{ $committee->id }}" {{ request('committee_id') == $committee->id ? 'selected' : '' }}>
                                            {{ $committee->ar_name ?? $committee->en_name }}
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
                            <div class="col-12 col-md-6 col-lg-2 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="exceptional" id="exceptional" value="1" {{ request('exceptional') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-muted" for="exceptional">
                                        {{ __('messages.Exceptional') ?? 'Exceptional' }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('committee-reports.archive') }}" class="btn btn-light px-4 rounded-pill">
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
                {{ __('messages.No reports archived yet.') ?? 'No reports archived yet.' }}
            </div>
        @else
            <div class="accordion" id="archiveAccordion">
                @foreach($archive as $year => $months)
                    <div class="accordion-item shadow-sm mb-3 border rounded">
                        <h2 class="accordion-header" id="heading-{{ $year }}">
                            <button class="accordion-button {{ request()->anyFilled(['search', 'committee_id', 'start_date', 'end_date', 'exceptional']) ? '' : 'collapsed' }} fw-bold fs-5 text-dark bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $year }}" aria-expanded="{{ request()->anyFilled(['search', 'committee_id', 'start_date', 'end_date', 'exceptional']) ? 'true' : 'false' }}" aria-controls="collapse-{{ $year }}">
                                <i class="bi bi-calendar-event text-primary me-2"></i> {{ $year }}
                            </button>
                        </h2>
                        <div id="collapse-{{ $year }}" class="accordion-collapse collapse {{ request()->anyFilled(['search', 'committee_id', 'start_date', 'end_date', 'exceptional']) ? 'show' : '' }}" aria-labelledby="heading-{{ $year }}" data-bs-parent="#archiveAccordion">
                            <div class="accordion-body bg-white p-4">
                                @foreach($months as $monthNum => $reports)
                                    @php
                                        $monthName = \Carbon\Carbon::create(null, (int) $monthNum, 1)->translatedFormat('F');
                                    @endphp
                                    <div class="mb-4">
                                        <h5 class="text-secondary border-bottom pb-2 mb-3 fw-bold">
                                            <i class="bi bi-calendar-month me-1 text-info"></i> {{ $monthName }}
                                        </h5>
                                        
                                        <div class="list-group">
                                            @foreach($reports as $report)
                                                @if(isset($report->is_legacy) && $report->is_legacy)
                                                    <div class="list-group-item list-group-item-action flex-column align-items-start p-3 mb-2 border rounded shadow-sm bg-light-subtle">
                                                        <div class="d-flex w-100 justify-content-between align-items-center flex-wrap gap-2">
                                                            <h6 class="mb-1 text-dark fw-bold fs-5">
                                                                <i class="bi bi-file-earmark-text text-secondary me-2"></i>{{ $report->title }}
                                                            </h6>
                                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                                <span class="badge bg-secondary text-white">{{ __('messages.Storage Box Archive') ?? 'Storage Box Archive' }}</span>
                                                                <span class="text-muted small fw-bold">
                                                                    <strong>{{ __('messages.Size') ?? 'Size' }}:</strong> {{ number_format($report->file_size / 1024, 1) }} KB
                                                                </span>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-3">
                                                            <div class="text-muted small italic">
                                                                <i class="bi bi-folder text-secondary me-1"></i> {{ $report->subtitle }}
                                                            </div>
                                                            
                                                            <div class="d-flex gap-2 align-self-end">
                                                                <a href="{{ route('committee-reports.downloadStorageboxFile', ['file' => $report->encrypted_path]) }}" class="btn btn-sm btn-primary" target="_blank">
                                                                    <i class="bi bi-file-earmark-arrow-down"></i> {{ __('messages.Download') ?? 'Download' }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="list-group-item list-group-item-action flex-column align-items-start p-3 mb-2 border rounded shadow-sm">
                                                        <div class="d-flex w-100 justify-content-between align-items-center flex-wrap gap-2">
                                                            <h6 class="mb-1 text-dark fw-bold fs-5">
                                                                {{ $report->title }}
                                                            </h6>
                                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                                @if($report->is_exceptional)
                                                                    <span class="badge bg-danger text-white me-2">{{ __('messages.Exceptional Meeting') }}</span>
                                                                @endif
                                                                <span class="text-muted small fw-bold me-3">
                                                                    <strong>{{ __('messages.Report Date') }}:</strong> {{ is_string($report->report_date) ? $report->report_date : $report->report_date->format('Y-m-d') }}
                                                                </span>
                                                                <span class="text-muted small fw-bold">
                                                                    <strong>{{ __('messages.Meeting Date') }}:</strong> {{ $report->meeting_date->format('Y-m-d') }} ({{ $report->meeting_day_description }})
                                                                </span>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-3">
                                                            <!-- Attachments list -->
                                                            <div class="attachments-area">
                                                                @if($report->attachments->isNotEmpty())
                                                                    <span class="text-muted small d-block mb-1 fw-bold">
                                                                        <i class="bi bi-paperclip"></i> {{ __('messages.Attachments') ?? 'Attachments' }}:
                                                                    </span>
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        @foreach($report->attachments as $attachment)
                                                                            <a href="{{ route('committee-reports.downloadAttachment', $attachment->id) }}" class="btn btn-sm btn-outline-secondary py-1 px-2 small rounded-pill text-decoration-none" target="_blank">
                                                                                <i class="bi bi-file-earmark-arrow-down-fill text-success"></i> 
                                                                                {{ $attachment->original_name }}
                                                                                <span class="text-muted ms-1" style="font-size: 0.75rem;">({{ number_format($attachment->file_size / 1024, 1) }} KB)</span>
                                                                            </a>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted small italic">
                                                                        <i class="bi bi-paperclip text-secondary"></i> {{ __('messages.No attachments') ?? 'No attachments' }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            
                                                            <!-- Actions -->
                                                            <div class="d-flex gap-2 align-self-end">
                                                                <a href="{{ route('committee-reports.show', $report->id) }}" class="btn btn-sm btn-outline-primary">
                                                                    <i class="bi bi-eye"></i> {{ __('messages.Show') }}
                                                                </a>
                                                                <a href="{{ route('committee-reports.pdf', $report->id) }}" class="btn btn-sm btn-secondary">
                                                                    <i class="bi bi-file-earmark-pdf"></i> {{ __('messages.PDF') ?? 'PDF' }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layout>
