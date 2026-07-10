<x-layout>
    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="mb-4">
            <h1 class="h2 text-gradient font-bold mb-0">
                {{ __('messages.literature_requests_archive') }}
            </h1>
            <p class="text-secondary mb-0">
                {{ __('messages.literature_requests_archive_description') }}
            </p>
        </div>

        @php
            $flatRequests = [];
            $allYears = array_keys($archiveData);
            $allMonths = [];
            
            foreach ($archiveData as $year => $months) {
                foreach ($months as $monthNum => $data) {
                    $allMonths[$monthNum] = $data['name'];
                    foreach ($data['requests'] as $req) {
                        $flatRequests[] = [
                            'year' => $year,
                            'monthNum' => $monthNum,
                            'request' => $req
                        ];
                    }
                }
            }
            
            // Sort flat requests by month desc
            usort($flatRequests, function($a, $b) {
                return $b['request']->month <=> $a['request']->month;
            });
        @endphp

        {{-- Filters & Content --}}
        @if(count($archiveData) > 0)
            <div class="card border-0 shadow-sm p-3 mb-4">
                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-bold text-secondary">{{ __('messages.Year') ?? 'Year' }}</label>
                        <select id="filter-year" class="form-select rounded-pill">
                            <option value="all">{{ __('messages.all') ?? 'All' }}</option>
                            @foreach($allYears as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-bold text-secondary">{{ __('messages.Month') ?? 'Month' }}</label>
                        <select id="filter-month" class="form-select rounded-pill">
                            <option value="all">{{ __('messages.all') ?? 'All' }}</option>
                            @foreach($allMonths as $num => $name)
                                <option value="{{ $num }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            {{-- Flat Table --}}
            <div class="card border-0 shadow-sm p-4">
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.Type') ?? 'Type' }}</th>
                                <th>{{ __('messages.group_or_service_body') }}</th>
                                <th class="text-center">{{ __('messages.total_unique_items') ?? 'Total Items' }}</th>
                                <th class="text-end">{{ __('messages.total_valuation') ?? 'Total Price' }}</th>
                                <th class="text-center">{{ __('messages.status') ?? 'Status' }}</th>
                                <th class="text-center">{{ __('messages.Actions') ?? 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($flatRequests as $item)
                                @php
                                    $req = $item['request'];
                                @endphp
                                <tr class="archive-row" data-year="{{ $item['year'] }}" data-month="{{ $item['monthNum'] }}">
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2">
                                            {{ strtoupper($req->type) }}
                                        </span>
                                    </td>
                                    <td class="fw-bold">
                                        @if($req->type === 'group')
                                            {{ $req->group->{app()->getLocale() . '_name'} ?? $req->group->en_name ?? 'Unknown Group' }}
                                        @else
                                            {{ $req->serviceBody->{app()->getLocale() . '_name'} ?? $req->serviceBody->en_name ?? 'Unknown Service Body' }}
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $req->total_items_count }}</td>
                                    <td class="text-end fw-bold text-primary">{{ $req->total_price }} {{ __('messages.EGP') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success rounded-pill px-2">
                                            {{ __('messages.' . $req->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('literature-requests.pdf', $req->id) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                            <i class="bi bi-file-earmark-pdf me-1"></i>
                                            {{ __('messages.Download PDF') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            
                            {{-- No matching row message row --}}
                            <tr id="no-matching-rows" style="display: none;">
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x fs-1 mb-2 d-block"></i>
                                    {{ __('messages.no_transactions_match') ?? 'No requests match the selected filters.' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const yearSelect = document.getElementById('filter-year');
                    const monthSelect = document.getElementById('filter-month');
                    const rows = document.querySelectorAll('.archive-row');
                    const noMatchingRow = document.getElementById('no-matching-rows');
                    
                    function filterArchive() {
                        const selectedYear = yearSelect.value;
                        const selectedMonth = monthSelect.value;
                        let visibleCount = 0;
                        
                        rows.forEach(row => {
                            const yearMatch = (selectedYear === 'all' || row.getAttribute('data-year') === selectedYear);
                            const monthMatch = (selectedMonth === 'all' || row.getAttribute('data-month') === selectedMonth);
                            
                            if (yearMatch && monthMatch) {
                                row.style.display = '';
                                visibleCount++;
                            } else {
                                row.style.display = 'none';
                            }
                        });
                        
                        if (visibleCount === 0) {
                            noMatchingRow.style.display = '';
                        } else {
                            noMatchingRow.style.display = 'none';
                        }
                    }
                    
                    yearSelect.addEventListener('change', filterArchive);
                    monthSelect.addEventListener('change', filterArchive);
                });
            </script>
        @else
            <div class="card p-5 border-0 shadow-sm text-center text-muted">
                <i class="bi bi-folder-x fs-1 mb-2"></i>
                <p class="mb-0">No archived literature requests found.</p>
            </div>
        @endif
    </div>
</x-layout>
