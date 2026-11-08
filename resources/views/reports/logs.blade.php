<x-layout>
    <x-backhead>{{ __('messages.Committee Report Audit Logs') }}</x-backhead>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-shield-check text-primary me-2"></i>{{ __('messages.Committee Report Audit Logs') }}
                </h4>
                <p class="text-muted small mb-0">{{ __('messages.committee_report_logs_desc') }}</p>
            </div>
            <div>
                <a href="{{ route('committee-reports.index') }}" class="btn btn-outline-primary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('messages.Back to Reports') }}
                </a>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 p-md-4">
                <form action="{{ route('committee-reports.logs') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted">{{ __('messages.Search') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="{{ __('messages.Search by ID, summary, user...') }}" value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted">{{ __('messages.Action') }}</label>
                        <select name="action" class="form-select bg-light">
                            <option value="">{{ __('messages.All Actions') }}</option>
                            <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>{{ __('messages.action_created') }}</option>
                            <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>{{ __('messages.action_updated') }}</option>
                            <option value="submitted" {{ request('action') === 'submitted' ? 'selected' : '' }}>{{ __('messages.action_submitted') }}</option>
                            <option value="approved" {{ request('action') === 'approved' ? 'selected' : '' }}>{{ __('messages.action_approved') }}</option>
                            <option value="returned_to_draft" {{ request('action') === 'returned_to_draft' ? 'selected' : '' }}>{{ __('messages.action_returned_to_draft') }}</option>
                            <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>{{ __('messages.action_deleted') }}</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted">{{ __('messages.Committee') }}</label>
                        <select name="committee_id" class="form-select bg-light">
                            <option value="">{{ __('messages.All Committees') }}</option>
                            @foreach($committees as $committee)
                                <option value="{{ $committee->id }}" {{ request('committee_id') == $committee->id ? 'selected' : '' }}>
                                    {{ $committee->ar_name ?: $committee->en_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted">{{ __('messages.Date From') }}</label>
                        <input type="date" name="date_from" class="form-control bg-light" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted">{{ __('messages.Date To') }}</label>
                        <input type="date" name="date_to" class="form-control bg-light" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                        <a href="{{ route('committee-reports.logs') }}" class="btn btn-light rounded-pill px-3">
                            <i class="bi bi-x-circle me-1"></i>{{ __('messages.Reset') }}
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-funnel me-1"></i>{{ __('messages.Filter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Logs Table Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">{{ __('messages.Date & Time') }}</th>
                            <th>{{ __('messages.Action') }}</th>
                            <th>{{ __('messages.Report ID') }}</th>
                            <th>{{ __('messages.Committee') }}</th>
                            <th>{{ __('messages.User') }}</th>
                            <th>{{ __('messages.Summary') }}</th>
                            <th class="text-end pe-4">{{ __('messages.Details') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="ps-4 text-nowrap">
                                    <div class="fw-semibold text-dark">{{ $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : 'N/A' }}</div>
                                    <div class="text-muted small">{{ $log->created_at ? $log->created_at->diffForHumans() : '' }}</div>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($log->action) {
                                            'approved' => 'bg-success text-white',
                                            'submitted' => 'bg-primary text-white',
                                            'returned_to_draft' => 'bg-warning text-dark',
                                            'deleted' => 'bg-danger text-white',
                                            'updated' => 'bg-info text-dark',
                                            'created' => 'bg-secondary text-white',
                                            default => 'bg-dark text-white'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} rounded-pill px-3 py-2">
                                        {{ __('messages.action_' . $log->action) }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->committee_report_id)
                                        <span class="badge bg-light text-dark border">
                                            #{{ $log->committee_report_id }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $log->committee_name ?? '—' }}</span>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $log->user_name ?? 'System' }}</div>
                                    @if($log->user_email)
                                        <div class="text-muted small">{{ $log->user_email }}</div>
                                    @endif
                                    @if($log->ip_address)
                                        <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $log->ip_address }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-break">{{ $log->summary }}</div>
                                </td>
                                <td class="text-end pe-4 text-nowrap">
                                    @if(!empty($log->details))
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill view-details-btn" 
                                                data-summary="{{ $log->summary }}" 
                                                data-action="{{ $log->action }}"
                                                data-time="{{ $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '' }}"
                                                data-details='@json($log->details)'>
                                            <i class="bi bi-info-circle me-1"></i>{{ __('messages.View') }}
                                        </button>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    {{ __('messages.No logs found matching the criteria.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="logDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-info-circle-fill text-primary me-2"></i>{{ __('messages.Log Details') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small text-muted fw-semibold mb-1">{{ __('messages.Summary') }}</label>
                        <p id="modalSummary" class="fw-semibold text-dark mb-0"></p>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted fw-semibold mb-1">{{ __('messages.Metadata') }}</label>
                        <div id="modalDetailsContainer" class="bg-light p-3 rounded-3 small"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        {{ __('messages.Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modalElement = document.getElementById('logDetailsModal');
            var modal = modalElement ? new bootstrap.Modal(modalElement) : null;
            var summaryEl = document.getElementById('modalSummary');
            var containerEl = document.getElementById('modalDetailsContainer');

            document.querySelectorAll('.view-details-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var summary = this.getAttribute('data-summary');
                    var detailsRaw = this.getAttribute('data-details');
                    
                    if (summaryEl) summaryEl.textContent = summary;
                    if (containerEl) {
                        try {
                            var details = JSON.parse(detailsRaw);
                            var html = '<ul class="list-unstyled mb-0">';
                            for (var key in details) {
                                if (details.hasOwnProperty(key)) {
                                    var val = details[key];
                                    if (typeof val === 'object' && val !== null) {
                                        val = JSON.stringify(val);
                                    }
                                    html += '<li class="py-1 border-bottom border-light-subtle d-flex justify-content-between gap-3">' +
                                            '<span class="text-secondary fw-medium text-capitalize">' + key.replace(/_/g, ' ') + ':</span>' +
                                            '<span class="text-dark fw-bold text-end">' + (val !== null && val !== '' ? val : '—') + '</span>' +
                                            '</li>';
                                }
                            }
                            html += '</ul>';
                            containerEl.innerHTML = html;
                        } catch (e) {
                            containerEl.textContent = detailsRaw;
                        }
                    }

                    if (modal) modal.show();
                });
            });
        });
    </script>
</x-layout>

