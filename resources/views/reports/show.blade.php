<x-layout>
    <x-backhead>{{ __('messages.Report Details') }}</x-backhead>

    <style>
        .section-html-content {
            overflow-x: auto;
        }
        .section-html-content table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .section-html-content table td, .section-html-content table th {
            border: 1px solid var(--glass-border, #dee2e6);
            padding: 10px 12px;
            text-align: inherit;
            vertical-align: top;
        }
        .section-html-content table th {
            background-color: rgba(0, 0, 0, 0.03);
            font-weight: bold;
        }
        .section-html-content table tr:nth-child(even) {
            background-color: rgba(0, 0, 0, 0.01);
        }
    </style>

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
            <div class="card-header bg-light py-3">
                <div class="row align-items-center">
                    <div class="col-md-4 text-start">
                        @if(file_exists(public_path('assets/images/na.png')))
                            <img src="{{ asset('assets/images/na.png') }}" alt="NA Logo" style="max-height: 60px;">
                        @endif
                    </div>
                    <div class="col-md-4 text-center">
                        <h4 class="mb-0 fw-bold text-primary">{{ $report->serviceCommittee->{app()->getLocale() . '_name'} ?? $report->serviceCommittee->ar_name }}</h4>
                        <span class="text-muted fw-bold">{{ __('messages.Committee Reports') ?? 'Committee Report' }}</span>
                    </div>
                    <div class="col-md-4 text-end">
                        @if($report->serviceCommittee->logo)
                            <img src="{{ asset('storage/' . $report->serviceCommittee->logo) }}" alt="Committee Logo" style="max-height: 60px;">
                        @endif
                    </div>
                </div>
                <hr class="my-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted me-3"><strong>{{ __('messages.Report Date') }}:</strong> {{ $report->report_date ? \App\Services\DateNumberHelper::translatedFormat($report->report_date, 'Y-m-d') : \App\Services\DateNumberHelper::translatedFormat($report->created_at, 'Y-m-d') }}</span>
                        <span class="text-muted me-3"><strong>{{ __('messages.Meeting Date') }}:</strong> {{ \App\Services\DateNumberHelper::translatedFormat($report->meeting_date, 'Y-m-d') }} ({{ $report->meeting_day_description }})</span>
                    </div>
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
                            <th>{{ __('messages.Member Name') }}</th>
                            <th>{{ __('messages.Status') }}</th>
                            <th>{{ __('messages.Election') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($report->positions_status)
                            @foreach($report->positions_status as $pos)
                                <tr>
                                    <td>{{ $pos['name'] ?? '-' }}</td>
                                    <td>{{ $pos['member_name'] ?? '-' }}</td>
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

                @if($report->attended_members)
                    <h6 class="mt-4 mb-3 fw-bold">{{ __('messages.Attended Members') }}</h6>
                    <div class="border p-3 rounded bg-light mb-4 text-start">
                        {!! nl2br(e($report->attended_members)) !!}
                    </div>
                @endif

                <h6 class="mt-4 mb-3 fw-bold">{{ __('messages.Report Body') }}</h6>
                @foreach($report->body_sections as $section)
                    <div class="border p-4 rounded bg-light mb-4 text-start">
                        @if(!empty($section['headline']))
                            <h5 class="fw-bold mb-3 text-primary border-bottom pb-2">{{ $section['headline'] }}</h5>
                        @endif
                        <div class="section-html-content">{!! $section['content'] !!}</div>
                    </div>
                @endforeach

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

                @if($report->footer || ($report->serviceCommittee && $report->serviceCommittee->default_footer))
                    <div class="border-top pt-3 mt-4 text-center text-muted fst-italic">
                        <p class="mb-0">{{ $report->footer ?: $report->serviceCommittee->default_footer }}</p>
                    </div>
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
            auth()->user()->hasRole('rsc')
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
