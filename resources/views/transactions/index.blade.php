<x-layout>

    <x-backhead>{{ __('messages.Logs')}}</x-backhead>

    <div class="container my-4">

        <!-- Advanced Filter Form -->
        <div class="card shadow-sm border mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>{{ __('messages.Filters') ?? 'Search & Filters' }}</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('transactions.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="filter_model" class="form-label small fw-bold text-secondary">{{ __('messages.Model') }}</label>
                            <select name="filter_model" id="filter_model" class="form-select">
                                <option value="">{{ __('messages.All Models') ?? 'All Models' }}</option>
                                @foreach($availableModels as $model)
                                    <option value="{{ $model }}" {{ request('filter_model') == $model ? 'selected' : '' }}>{{ $model }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter_operation" class="form-label small fw-bold text-secondary">{{ __('messages.Operation') }}</label>
                            <select name="filter_operation" id="filter_operation" class="form-select">
                                <option value="">{{ __('messages.All Operations') ?? 'All Operations' }}</option>
                                @foreach($availableOperations as $op)
                                    <option value="{{ $op }}" {{ request('filter_operation') == $op ? 'selected' : '' }}>{{ ucfirst($op) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="search_user" class="form-label small fw-bold text-secondary">{{ __('messages.User') }}</label>
                            <input type="text" name="search_user" id="search_user" class="form-control" placeholder="{{ __('messages.Search by user name or email...') ?? 'User name or email' }}" value="{{ request('search_user') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary">{{ __('messages.Date Range') ?? 'Date Range' }}</label>
                            <div class="input-group">
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From">
                                <span class="input-group-text">to</span>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3 gap-2">
                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">{{ __('messages.Reset') ?? 'Reset' }}</a>
                        <button type="submit" class="btn btn-primary px-4">{{ __('messages.Apply Filters') ?? 'Filter' }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
            <table class="main-tables manage-member text-center table table-bordered display" id="example">
               <thead>
                <tr>
                    <th>{{  __('messages.Operation') }}</th>
                    <th>{{  __('messages.Model') }}</th>
                    <th>{{  __('messages.User') }}</th>
                    <th>{{  __('messages.Date') }}</th>
                    <th>{{  __('messages.Time') }}</th>
                    <th>{{  __('messages.Context') ?? 'Context' }}</th>
                    <th>{{  __('messages.Action') ?? 'Action' }}</th>
                </tr>
                </thead>
                <tbody>
                
                @forelse ($transactions as $trans)                    
                    <tr>
                        <td>
                            @php
                                $badgeColor = 'bg-secondary';
                                if ($trans->operation === 'create') {
                                    $badgeColor = 'bg-success';
                                } elseif ($trans->operation === 'update') {
                                    $badgeColor = 'bg-info text-dark';
                                } elseif ($trans->operation === 'delete') {
                                    $badgeColor = 'bg-danger';
                                }
                            @endphp
                            <span class="badge {{ $badgeColor }} px-2 py-1">{{ ucfirst($trans->operation) }}</span>
                        </td>
                        <td><strong class="text-primary">{{ $trans->model }}</strong></td>
                        <td>
                            @if ($trans->user)
                                <div class="text-start small">
                                    <strong>{{ $trans->user->name }}</strong><br>
                                    <span class="text-muted">{{ $trans->user->email }}</span>
                                </div>
                            @else
                                <span class="text-muted italic">System / Guest</span>
                            @endif
                        </td>
                        <td>{{ $trans->created_at->format('Y-m-d') }}</td>
                        <td>{{ $trans->created_at->format('H:i:s') }}</td>
                        <td>
                            @if ($trans->model === 'Meeting')
                                <span class="small text-secondary">{{ $trans->group_name ?? 'N/A' }}</span>
                            @elseif ($trans->model === 'Group')
                                <span class="small text-secondary">{{ $trans->group_name ?? 'N/A' }}</span>
                            @else
                                <span class="text-muted small">N/A</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary toggle-details" data-target="details-data-{{ $trans->id }}">
                                {{ __('messages.Show') ?? 'Show' }}
                            </button>

                            <!-- Hidden container for DataTables child row content -->
                            <div id="details-data-{{ $trans->id }}" class="d-none">
                                <div class="p-3 text-start bg-light rounded border">
                                    <!-- Context Badges -->
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-4">
                                            <div class="p-2 border rounded bg-white small">
                                                <strong>IP Address:</strong> <code>{{ $trans->ip_address ?? 'N/A' }}</code>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-2 border rounded bg-white small text-truncate" title="{{ $trans->url }}">
                                                <strong>URL:</strong> <code>{{ $trans->url ?? 'N/A' }}</code>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-2 border rounded bg-white small text-truncate" title="{{ $trans->user_agent }}">
                                                <strong>User Agent:</strong> <span class="text-muted">{{ $trans->user_agent ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Values Table -->
                                    <div class="card border shadow-sm">
                                        <div class="card-header bg-white py-2 fw-bold text-secondary small">
                                            <i class="fas fa-list-alt me-1"></i>{{ __('messages.Log Details') ?? 'Database Changes' }}
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm table-hover mb-0 align-middle small">
                                                <thead>
                                                    <tr class="table-light text-secondary">
                                                        <th style="width: 25%;">Field</th>
                                                        @if($trans->operation === 'update')
                                                            <th style="width: 37.5%;">Original Value</th>
                                                            <th style="width: 37.5%;">New Value</th>
                                                        @else
                                                            <th>Value</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if($trans->operation === 'create' && $trans->new_values)
                                                        @foreach($trans->new_values as $key => $value)
                                                            <tr>
                                                                <td><strong>{{ $key }}</strong></td>
                                                                <td class="text-success">
                                                                    <span class="badge bg-success-light text-success me-1">+</span>
                                                                    <code>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</code>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @elseif($trans->operation === 'delete' && $trans->old_values)
                                                        @foreach($trans->old_values as $key => $value)
                                                            <tr>
                                                                <td><strong>{{ $key }}</strong></td>
                                                                <td class="text-danger text-decoration-line-through">
                                                                    <span class="badge bg-danger-light text-danger me-1">-</span>
                                                                    <code>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</code>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @elseif($trans->operation === 'update' && ($trans->old_values || $trans->new_values))
                                                        @foreach(($trans->new_values ?? []) as $key => $newValue)
                                                            @php $oldValue = $trans->old_values[$key] ?? null; @endphp
                                                            <tr>
                                                                <td><strong>{{ $key }}</strong></td>
                                                                <td class="text-danger text-decoration-line-through">
                                                                    <code>{{ is_array($oldValue) ? json_encode($oldValue, JSON_UNESCAPED_UNICODE) : $oldValue }}</code>
                                                                </td>
                                                                <td class="text-success fw-bold">
                                                                    <code>{{ is_array($newValue) ? json_encode($newValue, JSON_UNESCAPED_UNICODE) : $newValue }}</code>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <!-- Fallback for historical records using 'details' -->
                                                        @if($trans->details)
                                                            @foreach($trans->details as $key => $value)
                                                                <tr>
                                                                    <td><strong>{{ $key }}</strong></td>
                                                                    <td>
                                                                        <code>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</code>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="3" class="text-center py-2 text-muted">No details logged.</td>
                                                            </tr>
                                                        @endif
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            {{ __('messages.No logs found matching your filters.') ?? 'No logs found matching your filters.' }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
                
            </table>
        </div>
        <div class="d-flex justify-content-center mt-3">
            {{ $transactions->links() }}
        </div>
    </div>

    <style>
        .bg-success-light { background-color: rgba(40, 167, 69, 0.1); }
        .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
        .text-decoration-line-through { text-decoration: line-through; }
        .toggle-details {
            transition: all 0.2s ease-in-out;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const $table = $('#example');
                if (window.jQuery && $.fn.dataTable && $.fn.DataTable.isDataTable($table[0])) {
                    const api = $table.DataTable();

                    // Use DataTable row child API to display the expandable details content without causing column-mismatch alerts
                    $table.on('click', '.toggle-details', function() {
                        const btn = $(this);
                        const tr = btn.closest('tr');
                        const row = api.row(tr);
                        const targetId = btn.attr('data-target');
                        const detailsContainer = $('#' + targetId);

                        if (row.child.isShown()) {
                            row.child.hide();
                            tr.removeClass('shown');
                            btn.text("{{ __('messages.Show') ?? 'Show' }}");
                            btn.removeClass('btn-secondary text-white').addClass('btn-outline-primary');
                        } else {
                            row.child(detailsContainer.html()).show();
                            tr.addClass('shown');
                            btn.text("{{ __('messages.Hide') ?? 'Hide' }}");
                            btn.removeClass('btn-outline-primary').addClass('btn-secondary text-white');
                        }
                    });
                } else {
                    // Fallback for non-datatable mode
                    $('.toggle-details').on('click', function() {
                        const btn = $(this);
                        const tr = btn.closest('tr');
                        const targetId = btn.attr('data-target');
                        const detailsContainer = $('#' + targetId);
                        const targetRowId = 'row-' + targetId;
                        let targetRow = $('#' + targetRowId);

                        if (targetRow.length === 0) {
                            const colspan = tr.find('td').length;
                            targetRow = $('<tr id="' + targetRowId + '" class="bg-light"><td colspan="' + colspan + '">' + detailsContainer.html() + '</td></tr>');
                            tr.after(targetRow);
                            btn.text("{{ __('messages.Hide') ?? 'Hide' }}");
                            btn.removeClass('btn-outline-primary').addClass('btn-secondary text-white');
                        } else {
                            targetRow.remove();
                            btn.text("{{ __('messages.Show') ?? 'Show' }}");
                            btn.removeClass('btn-secondary text-white').addClass('btn-outline-primary');
                        }
                    });
                }
            }, 500);
        });
    </script>

</x-layout>