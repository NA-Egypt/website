<x-layout>
    <x-backhead>{{ __('messages.apk_download_requests') }}</x-backhead>

    <div class="container-fluid px-4 py-4">
        {{-- Flash Alerts --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div class="fw-medium">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div class="fw-medium">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Header Section --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1 d-flex align-items-center gap-2" style="color: var(--text-primary, #1e293b);">
                    <i class="bi bi-android2 text-success fs-3"></i>
                    <span>{{ __('messages.apk_download_requests') }}</span>
                </h3>
                <p class="text-muted mb-0 small">
                    {{ __('messages.apk_requests_subtitle') }}
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.apk_requests.export', request()->all()) }}" class="btn btn-outline-success rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                    <span>{{ __('messages.Export CSV') }}</span>
                </a>
                <a href="{{ route('admin.apk_requests.index') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2" title="{{ __('messages.Reset') }}">
                    <i class="bi bi-arrow-clockwise"></i>
                    <span>{{ __('messages.Reset') }}</span>
                </a>
            </div>
        </div>

        {{-- KPI Summary Cards --}}
        <div class="row g-3 mb-4">
            {{-- Total Requests --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, rgba(37, 99, 235, 0.02) 100%); border-inline-start: 4px solid #2563eb !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">{{ __('messages.Total Requests') ?? 'Total Requests' }}</span>
                            <h3 class="fw-bold mb-0 text-primary">{{ number_format($kpiStats['total_requests']) }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white shadow-sm" style="width: 48px; height: 48px;">
                            <i class="bi bi-envelope-paper-fill fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Unique Requesters --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, rgba(14, 165, 233, 0.08) 0%, rgba(2, 132, 199, 0.02) 100%); border-inline-start: 4px solid #0284c7 !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">{{ __('messages.unique_requesters') }}</span>
                            <h3 class="fw-bold mb-0 text-info">{{ number_format($kpiStats['unique_emails']) }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-info text-white shadow-sm" style="width: 48px; height: 48px;">
                            <i class="bi bi-people-fill fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Downloads --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(5, 150, 105, 0.02) 100%); border-inline-start: 4px solid #059669 !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">{{ __('messages.download_count') }} ({{ $kpiStats['download_rate'] }})</span>
                            <h3 class="fw-bold mb-0 text-success">{{ number_format($kpiStats['total_downloads']) }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white shadow-sm" style="width: 48px; height: 48px;">
                            <i class="bi bi-cloud-arrow-down-fill fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Active Links --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.08) 0%, rgba(109, 40, 217, 0.02) 100%); border-inline-start: 4px solid #7c3aed !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">{{ __('messages.active_links') }}</span>
                            <h3 class="fw-bold mb-0" style="color: #7c3aed;">{{ number_format($kpiStats['active_links']) }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 48px; height: 48px; background: #7c3aed;">
                            <i class="bi bi-shield-check fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Search Form --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--card-bg, #ffffff);">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.apk_requests.index') }}" class="row g-3 align-items-end" id="filterForm">
                    <div class="col-lg-6 col-md-8">
                        <label class="form-label small text-muted fw-semibold mb-1">{{ __('messages.search') ?? 'Search' }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="search" value="{{ $search }}" class="form-control border-start-0" placeholder="{{ __('messages.search_apk_requests') }}">
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-4">
                        <label class="form-label small text-muted fw-semibold mb-1">{{ __('messages.Status') }}</label>
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>{{ __('messages.all_statuses') }}</option>
                            <option value="downloaded" {{ $status === 'downloaded' ? 'selected' : '' }}>{{ __('messages.downloaded_status') }}</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>{{ __('messages.pending_status') }}</option>
                            <option value="expired" {{ $status === 'expired' ? 'selected' : '' }}>{{ __('messages.expired_status') }}</option>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-3 flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                            <i class="bi bi-funnel-fill"></i>
                            <span>{{ __('messages.Filter') ?? 'Filter' }}</span>
                        </button>
                        <a href="{{ route('admin.apk_requests.index') }}" class="btn btn-outline-secondary rounded-3" title="{{ __('messages.Clear Filters') }}">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bulk Action Bar --}}
        <div id="bulkActionBar" class="card border-0 shadow-sm rounded-4 mb-3 d-none bg-light" style="border-inline-start: 4px solid #ef4444 !important;">
            <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-dark rounded-pill px-3 py-2 fs-6" id="selectedCountBadge">0</span>
                    <span class="fw-semibold text-secondary small">{{ __('messages.entries') }} {{ __('messages.Selected') ?? 'Selected' }}</span>
                </div>
                <form id="bulkDeleteForm" method="POST" action="{{ route('admin.apk_requests.bulk_destroy') }}" onsubmit="return confirm('{{ __('messages.apk_bulk_delete_confirm') }}');">
                    @csrf
                    <div id="bulkDeleteInputsContainer"></div>
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                        <i class="bi bi-trash3-fill"></i>
                        <span>{{ __('messages.bulk_delete') }}</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Data Table Card --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--card-bg, #ffffff);">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                            </th>
                            <th>{{ __('messages.requester_email') }}</th>
                            <th>{{ __('messages.Status') }}</th>
                            <th>{{ __('messages.download_count') }}</th>
                            <th>{{ __('messages.client_info') }}</th>
                            <th>{{ __('messages.Created At') }}</th>
                            <th>{{ __('messages.Expires At') ?? 'Expires At' }}</th>
                            <th class="text-end px-4">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($apkRequests as $item)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $item->id }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-primary" style="width: 34px; height: 34px;">
                                            <i class="bi bi-envelope-at-fill"></i>
                                        </div>
                                        <div>
                                            <a href="mailto:{{ $item->email }}" class="fw-bold text-decoration-none text-dark d-block">
                                                {{ $item->email }}
                                            </a>
                                            <small class="text-muted font-monospace" style="font-size: 0.75rem;">ID: #{{ $item->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($item->download_count > 0)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i>{{ __('messages.downloaded_status') }}
                                        </span>
                                    @elseif ($item->isExpired())
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1">
                                            <i class="bi bi-clock-history me-1"></i>{{ __('messages.expired_status') }}
                                        </span>
                                    @else
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-1">
                                            <i class="bi bi-hourglass-split me-1"></i>{{ __('messages.pending_status') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold fs-6">{{ $item->download_count }}</span>
                                        @if ($item->last_downloaded_at)
                                            <span class="text-muted small" title="{{ $item->last_downloaded_at->format('Y-m-d H:i:s') }}">
                                                ({{ $item->last_downloaded_at->diffForHumans() }})
                                            </span>
                                        @else
                                            <span class="text-muted small">({{ __('messages.never_downloaded') }})</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge bg-light text-dark font-monospace border mb-1">
                                            {{ $item->ip_address ?? 'N/A' }}
                                        </span>
                                        @if ($item->user_agent)
                                            <div class="text-muted small text-truncate" style="max-width: 220px;" title="{{ $item->user_agent }}">
                                                <i class="bi bi-phone me-1"></i>{{ Str::limit($item->user_agent, 35) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="small d-block">{{ $item->created_at->format('Y-m-d H:i') }}</span>
                                    <span class="text-muted small">{{ $item->created_at->diffForHumans() }}</span>
                                </td>
                                <td>
                                    @if ($item->isExpired())
                                        <span class="text-danger small fw-semibold d-block">
                                            {{ __('messages.expired_ago', ['time' => $item->expires_at->diffForHumans(null, true)]) }}
                                        </span>
                                        <span class="text-muted small">{{ $item->expires_at->format('Y-m-d H:i') }}</span>
                                    @else
                                        <span class="text-success small fw-semibold d-block">
                                            {{ __('messages.expires_in') }} {{ $item->expires_at->diffForHumans(null, true) }}
                                        </span>
                                        <span class="text-muted small">{{ $item->expires_at->format('Y-m-d H:i') }}</span>
                                    @endif
                                </td>
                                <td class="text-end px-4">
                                    <div class="d-inline-flex gap-1">
                                        {{-- Resend Link Button --}}
                                        <form method="POST" action="{{ route('admin.apk_requests.resend', $item) }}" onsubmit="return confirm('{{ __('messages.apk_resend_confirm') }}');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-1" title="{{ __('messages.apk_resend_link') }}">
                                                <i class="bi bi-send-fill"></i>
                                                <span class="d-none d-md-inline">{{ __('messages.apk_resend_link') }}</span>
                                            </button>
                                        </form>

                                        {{-- Delete Button --}}
                                        <form method="POST" action="{{ route('admin.apk_requests.destroy', $item) }}" onsubmit="return confirm('{{ __('messages.apk_delete_confirm') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2 shadow-sm" title="{{ __('messages.delete') }}">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="bi bi-inbox text-muted fs-1 d-block mb-2"></i>
                                        <h5 class="text-secondary fw-semibold">{{ __('messages.no_apk_requests') }}</h5>
                                        <p class="text-muted small mb-0">{{ __('messages.Try adjusting your search or filters') ?? 'Try adjusting your search or filters' }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            @if ($apkRequests->hasPages())
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="text-muted small">
                        {{ __('messages.showing') }} {{ $apkRequests->firstItem() }} {{ __('messages.to') }} {{ $apkRequests->lastItem() }} {{ __('messages.of') }} {{ $apkRequests->total() }} {{ __('messages.entries') }}
                    </span>
                    <div>
                        {{ $apkRequests->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Interactive Checkbox & Bulk Actions Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAllCheckbox');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const bulkActionBar = document.getElementById('bulkActionBar');
            const selectedCountBadge = document.getElementById('selectedCountBadge');
            const bulkDeleteInputsContainer = document.getElementById('bulkDeleteInputsContainer');

            function updateBulkBar() {
                const checked = Array.from(rowCheckboxes).filter(cb => cb.checked);
                const count = checked.length;

                if (count > 0) {
                    bulkActionBar.classList.remove('d-none');
                    selectedCountBadge.textContent = count;

                    // Rebuild hidden inputs
                    bulkDeleteInputsContainer.innerHTML = '';
                    checked.forEach(cb => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = cb.value;
                        bulkDeleteInputsContainer.appendChild(input);
                    });
                } else {
                    bulkActionBar.classList.add('d-none');
                    bulkDeleteInputsContainer.innerHTML = '';
                }

                if (selectAll) {
                    selectAll.checked = rowCheckboxes.length > 0 && count === rowCheckboxes.length;
                    selectAll.indeterminate = count > 0 && count < rowCheckboxes.length;
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
                    updateBulkBar();
                });
            }

            rowCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkBar);
            });
        });
    </script>
</x-layout>
