<x-layout>
    <x-backhead>{{ __('messages.Committee Reports') }}</x-backhead>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(!$isRsc && $childWorkgroups->isNotEmpty() && $unembeddedDraftsCount > 0)
            <div class="alert alert-warning d-flex align-items-center justify-content-between mb-4 shadow-sm border-0">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bell-fill fs-4 text-warning"></i>
                    <div>
                        <strong>{{ __('messages.Pending Workgroup Reports Notice', ['count' => $unembeddedDraftsCount]) }}</strong>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('committee-reports.index', ['tab' => 'workgroups']) }}" class="btn btn-sm btn-outline-dark fw-bold">
                        <i class="bi bi-eye"></i> {{ __('messages.Workgroup Drafts') }} ({{ $unembeddedDraftsCount }})
                    </a>
                    <a href="{{ route('committee-reports.create') }}" class="btn btn-sm btn-primary fw-bold">
                        <i class="bi bi-plus-lg"></i> {{ __('messages.Add Report') }}
                    </a>
                </div>
            </div>
        @endif

        @if(!$isRsc && $childWorkgroups->isNotEmpty())
            <ul class="nav nav-pills mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'committee' ? 'active' : '' }}" href="{{ route('committee-reports.index', ['tab' => 'committee']) }}">
                        <i class="bi bi-building me-1"></i> {{ __('messages.Committee Reports') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'workgroups' ? 'active' : '' }}" href="{{ route('committee-reports.index', ['tab' => 'workgroups']) }}">
                        <i class="bi bi-people me-1"></i> {{ __('messages.Workgroup Drafts') }}
                        @if($unembeddedDraftsCount > 0)
                            <span class="badge bg-danger rounded-pill ms-1">{{ $unembeddedDraftsCount }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex gap-2">
                @if(auth()->check() && (auth()->user()->hasRole('super admin') || auth()->user()->hasRole('rsc') || \App\Models\ServiceCommittee::where('user_id', auth()->id())->orWhere('email', auth()->user()->email)->exists()))
                    <x-button-a href="{{ route('committee-reports.create') }}" color="outline-primary" name="{{ __('messages.Add Report') }}" />
                @endif
                <a href="{{ route('committee-reports.archive') }}" class="btn btn-outline-info">
                    <i class="bi bi-archive-fill"></i> {{ __('messages.Reports Archive') ?? 'Reports Archive' }}
                </a>
            </div>
            
            <form action="{{ route('committee-reports.index') }}" method="GET" class="d-flex">
                @if(request('tab'))
                    <input type="hidden" name="tab" value="{{ request('tab') }}">
                @endif
                @if($isRsc)
                    <select name="committee_id" class="form-select me-2" style="width: 200px;">
                        <option value="">{{ __('messages.All Committees') }}</option>
                        @foreach($committees as $c)
                            <option value="{{ $c->id }}" {{ request('committee_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->ar_name }}
                            </option>
                        @endforeach
                    </select>
                @endif
                <input type="text" name="search" class="form-control me-2" placeholder="{{ __('messages.Search...') }}" value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-secondary">{{ __('messages.Search') }}</button>
            </form>
        </div>

        <form action="{{ route('committee-reports.exportPdf') }}" method="POST">
            @csrf
            <div class="mb-3">
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf"></i> {{ __('messages.export_selected_to_pdf') ?? 'Export Selected to PDF' }}
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="selectAllReports">
                                </div>
                            </th>
                            @if($isRsc || ($activeTab === 'workgroups'))
                                <th>{{ __('messages.Committee') }}</th>
                            @endif
                            <th>{{ __('messages.Report Date') }}</th>
                            <th>{{ __('messages.Meeting Date') }}</th>
                            <th>{{ __('messages.Day') }}</th>
                            <th>{{ __('messages.Status') ?? 'Status' }}</th>
                            <th>{{ __('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td>
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input report-checkbox" type="checkbox" name="report_ids[]" value="{{ $report->id }}">
                                    </div>
                                </td>
                                @if($isRsc || ($activeTab === 'workgroups'))
                                    <td>{{ $report->serviceCommittee->ar_name ?? '-' }}</td>
                                @endif
                                <td>{{ $report->report_date ? \App\Services\DateNumberHelper::translatedFormat($report->report_date, 'Y-m-d') : \App\Services\DateNumberHelper::translatedFormat($report->created_at, 'Y-m-d') }}</td>
                                <td>
                                    {{ \App\Services\DateNumberHelper::translatedFormat($report->meeting_date, 'Y-m-d') }}
                                    @if($report->is_exceptional)
                                        <span class="badge bg-danger ms-1" style="font-size: 0.75rem;">{{ __('messages.Exceptional Meeting') }}</span>
                                    @endif
                                </td>
                                <td>{{ $report->meeting_day_description }}</td>
                                <td>
                                    @if($report->isEmbedded())
                                        <span class="badge bg-info text-dark"><i class="bi bi-link-45deg"></i> {{ __('messages.Embedded in Report') }}</span>
                                    @elseif($report->serviceCommittee && $report->serviceCommittee->isWorkgroup())
                                        <span class="badge bg-warning text-dark"><i class="bi bi-file-earmark"></i> {{ __('messages.Draft for Committee') }}</span>
                                    @elseif($report->status === 'draft')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-file-earmark"></i> {{ __('messages.Draft') ?? 'Draft' }}</span>
                                    @elseif($report->status === 'approved')
                                        <span class="badge bg-success"><i class="bi bi-patch-check-fill"></i> {{ __('messages.Approved') ?? 'Approved & Published' }}</span>
                                    @else
                                        <span class="badge bg-primary"><i class="bi bi-check-circle"></i> {{ __('messages.Submitted') ?? 'Submitted' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('committee-reports.show', $report->id) }}" class="btn btn-sm btn-info text-white me-1">
                                        <i class="bi bi-eye"></i> {{ __('messages.Show') }}
                                    </a>
                                    <a href="{{ route('committee-reports.pdf', $report->id) }}" class="btn btn-sm btn-secondary me-1">
                                        <i class="bi bi-file-earmark-pdf"></i> {{ __('messages.PDF Report') }}
                                    </a>
                                    @if($isRsc && $report->status === 'submitted')
                                        <form action="{{ route('committee-reports.approveAndSend', $report->id) }}" method="POST" class="d-inline me-1" onsubmit="return confirm('{{ __('messages.Are you sure you want to approve and publish this report?') ?? 'Are you sure you want to approve and publish this report?' }}')">
                                            @csrf
                                            <button class="btn btn-sm btn-success">
                                                <i class="bi bi-patch-check-fill"></i> {{ __('messages.Approve & Publish') ?? 'Approve & Publish' }}
                                            </button>
                                        </form>
                                    @endif
                                    @if(!$isRsc && $report->status === 'draft')
                                        <a href="{{ route('committee-reports.edit', $report->id) }}" class="btn btn-sm btn-primary me-1">
                                            <i class="bi bi-pencil"></i> {{ __('messages.Edit') }}
                                        </a>
                                        @if(!$report->serviceCommittee || !$report->serviceCommittee->isWorkgroup())
                                            <form action="{{ route('committee-reports.send', $report->id) }}" method="POST" class="d-inline me-1" onsubmit="return confirm('{{ __('messages.Are you sure you want to approve and send this report to RSC?') }}')">
                                                @csrf
                                                <button class="btn btn-sm btn-success">
                                                    <i class="bi bi-check-circle"></i> {{ __('messages.Send to RSC') }}
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('committee-reports.destroy', $report->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.Are you sure you want to delete this report?') ?? 'Are you sure you want to delete this report?' }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> {{ __('messages.Delete') }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isRsc ? 7 : 6 }}">{{ __('messages.No reports found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectAll = document.getElementById('selectAllReports');
                const checkboxes = document.querySelectorAll('.report-checkbox');
                
                if (selectAll) {
                    selectAll.addEventListener('change', function() {
                        checkboxes.forEach(cb => cb.checked = selectAll.checked);
                    });
                }
            });
        </script>
        <div class="mt-3">
            {{ $reports->links() }}
        </div>
    </div>
</x-layout>
