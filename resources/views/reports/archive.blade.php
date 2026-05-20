<x-layout>
    <x-backhead>{{ __('messages.Reports Archive') ?? 'Reports Archive' }}</x-backhead>

    <div class="container mt-4">
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
                            <button class="accordion-button collapsed fw-bold fs-5 text-dark bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $year }}" aria-expanded="false" aria-controls="collapse-{{ $year }}">
                                <i class="bi bi-calendar-event text-primary me-2"></i> {{ $year }}
                            </button>
                        </h2>
                        <div id="collapse-{{ $year }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $year }}" data-bs-parent="#archiveAccordion">
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
                                                <div class="list-group-item list-group-item-action flex-column align-items-start p-3 mb-2 border rounded shadow-sm">
                                                    <div class="d-flex w-100 justify-content-between align-items-center flex-wrap gap-2">
                                                        <h6 class="mb-1 text-dark fw-bold fs-5">
                                                            {{ $report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name }}
                                                        </h6>
                                                        <span class="text-muted small fw-bold">
                                                            <i class="bi bi-calendar3 me-1"></i>{{ $report->meeting_date->format('Y-m-d') }} ({{ $report->meeting_day_description }})
                                                        </span>
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
