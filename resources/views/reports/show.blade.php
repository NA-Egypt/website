<x-layout>
    <x-backhead>{{ __('messages.Report Details') }}</x-backhead>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($report->review_notes)
            <div class="alert alert-warning border-start border-warning border-4 mb-4 shadow-sm">
                <h5 class="alert-heading fw-bold text-dark"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>{{ __('messages.Review Notes') ?? 'RSC Review Notes / Comments' }}</h5>
                <p class="mb-0 text-dark">{{ $report->review_notes }}</p>
            </div>
        @endif

        <div class="card mb-4 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-light py-3">
                <h5 class="mb-0 fw-bold">{{ $report->serviceCommittee->ar_name }}</h5>
                <div class="text-end">
                    <span class="text-muted me-3"><strong>{{ __('messages.Report Date') }}:</strong> {{ $report->report_date ? $report->report_date->format('Y-m-d') : $report->created_at->format('Y-m-d') }}</span>
                    <span class="text-muted me-3"><strong>{{ __('messages.Meeting Date') }}:</strong> {{ $report->meeting_date->format('Y-m-d') }} ({{ $report->meeting_day_description }})</span>
                    @if($report->is_exceptional)
                        <span class="badge bg-danger text-white">{{ __('messages.Exceptional Meeting') }}</span>
                    @endif
                </div>
            </div>
            <div class="card-body p-4">
                
                <h6 class="mb-3 fw-bold">{{ __('messages.Positions Status') }}</h6>
                <table class="table table-bordered table-sm w-100 mb-4 text-center">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.Position') }}</th>
                            <th>{{ __('messages.Status') }}</th>
                            <th>{{ __('messages.Election') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($report->positions_status)
                            @foreach($report->positions_status as $pos)
                                <tr>
                                    <td>{{ $pos['name'] ?? '-' }}</td>
                                    <td>
                                        <span class="badge 
                                            @if(($pos['status']??'') == 'Present') bg-success 
                                            @elseif(($pos['status']??'') == 'Absent') bg-danger 
                                            @elseif(($pos['status']??'') == 'Excused') bg-warning text-dark 
                                            @else bg-secondary @endif">
                                            {{ $pos['status'] ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(!empty($pos['election']))
                                            <span class="text-danger fw-bold">{{ __('messages.Open') }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="3">{{ __('messages.No positions data') }}</td></tr>
                        @endif
                    </tbody>
                </table>

                <h6 class="mt-4 mb-3 fw-bold">{{ __('messages.Report Body') }}</h6>
                <div class="border p-4 rounded bg-light mb-4">
                    {!! $report->body !!}
                </div>

                @if($report->attachments->isNotEmpty())
                    <h6 class="mt-4 mb-3 fw-bold">{{ __('messages.Attachments') ?? 'Attachments' }}</h6>
                    <ul class="list-group mb-4">
                        @foreach($report->attachments as $attachment)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-file-earmark-arrow-down text-primary me-2"></i>
                                    <a href="{{ route('committee-reports.downloadAttachment', $attachment->id) }}" class="text-decoration-none fw-medium" target="_blank">
                                        {{ $attachment->original_name }}
                                    </a>
                                    <span class="text-muted ms-2 small">({{ number_format($attachment->file_size / 1024, 1) }} KB)</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="mt-4 text-end">
                    <a href="{{ route('committee-reports.pdf', $report->id) }}" class="btn btn-secondary me-2">
                        <i class="bi bi-file-earmark-pdf"></i> {{ __('messages.Download PDF') }}
                    </a>
                    <a href="{{ route('committee-reports.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> {{ __('messages.Back to List') }}
                    </a>
                </div>
            </div>
        </div>

        @php
        $isRsc = auth()->check() && (
            auth()->user()->hasRole('super admin') ||
            in_array(strtolower(auth()->user()->email), ['rsc@naegypt.org', 'rcp@naegypt.org', 'rvcp@naegypt.org'])
        );
        @endphp

        @if($isRsc && $report->status === 'submitted')
            <div class="card border-primary mb-4 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock-fill me-2"></i>{{ __('messages.RSC Review Actions') ?? 'Region Service Committee - Review Actions' }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-start">
                        <!-- Approve Side -->
                        <div class="col-md-6 border-end pe-md-4 mb-4 mb-md-0">
                            <h6 class="fw-bold text-success"><i class="bi bi-patch-check-fill me-2"></i>{{ __('messages.Approve & Publish') ?? 'Approve & Publish' }}</h6>
                            <p class="text-muted small">{{ __('messages.approve_report_desc') ?? 'Approving this report will publish it, making it visible to all authenticated users in the archive.' }}</p>
                            <form action="{{ route('committee-reports.approveAndSend', $report->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.Are you sure you want to approve and publish this report?') }}')">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 mt-2 py-2 fw-bold">
                                    <i class="bi bi-check-lg"></i> {{ __('messages.Approve & Publish') ?? 'Approve & Publish' }}
                                </button>
                            </form>
                        </div>
                        
                        <!-- Return Side -->
                        <div class="col-md-6 ps-md-4">
                            <h6 class="fw-bold text-danger"><i class="bi bi-arrow-counterclockwise me-2"></i>{{ __('messages.Return to Committee as Draft') ?? 'Return to Committee as Draft' }}</h6>
                            <p class="text-muted small">{{ __('messages.return_report_desc') ?? 'Send this report back to the committee for correction. You must provide review feedback notes below.' }}</p>
                            <form action="{{ route('committee-reports.returnToDraft', $report->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <textarea name="review_notes" class="form-control" rows="3" placeholder="{{ __('messages.Enter your review notes here...') ?? 'Enter your review notes/comments for correction here...' }}" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger w-100 py-2 fw-bold">
                                    <i class="bi bi-arrow-left-right"></i> {{ __('messages.Return to Draft with Notes') ?? 'Return to Draft with Notes' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layout>
