<x-layout>
    <x-backhead>{{ __('messages.Report Details') }}</x-backhead>

    <div class="container mt-4">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $report->serviceCommittee->ar_name }}</h5>
                <div class="text-end">
                    <span class="text-muted me-3"><strong>{{ __('messages.Report Date') }}:</strong> {{ $report->report_date ? $report->report_date->format('Y-m-d') : $report->created_at->format('Y-m-d') }}</span>
                    <span class="text-muted me-3"><strong>{{ __('messages.Meeting Date') }}:</strong> {{ $report->meeting_date->format('Y-m-d') }} ({{ $report->meeting_day_description }})</span>
                    @if($report->is_exceptional)
                        <span class="badge bg-danger text-white">{{ __('messages.Exceptional Meeting') }}</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                
                <h6 class="mt-4 mb-3 fw-bold">{{ __('messages.Positions Status') }}</h6>
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
                <div class="border p-4 rounded bg-light">
                    {!! $report->body !!}
                </div>

                <div class="mt-4 text-end">
                    <a href="{{ route('committee-reports.pdf', $report->id) }}" class="btn btn-secondary me-2">{{ __('messages.Download PDF') }}</a>
                    <a href="{{ route('committee-reports.index') }}" class="btn btn-outline-primary">{{ __('messages.Back to List') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-layout>
