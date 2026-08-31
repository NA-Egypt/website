<x-layout>
    <div class="container-fluid py-4">
        {{-- Header Section --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h2 text-gradient font-bold mb-1">
                    <i class="bi bi-journal-text me-2"></i>{{ __('messages.monthly_ledger_title') }}
                </h1>
                <p class="text-secondary mb-0">
                    {{ __('messages.monthly_ledger_desc') }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('lit.ledger.pdf', ['month' => $selectedMonth]) }}" target="_blank" class="btn btn-outline-danger rounded-pill shadow-sm px-3">
                    <i class="bi bi-file-earmark-pdf me-1"></i>{{ __('messages.export_pdf') }}
                </a>
                <a href="{{ route('lit.ledger.csv', ['month' => $selectedMonth]) }}" class="btn btn-outline-success rounded-pill shadow-sm px-3">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>{{ __('messages.export_csv') }}
                </a>
            </div>
        </div>

        {{-- Month Selector --}}
        <div class="card p-3 shadow-sm border-0 mb-4 rounded-4">
            <form action="{{ route('lit.ledger') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <label class="form-label text-secondary small mb-1">{{ __('messages.select_ledger_month') }}</label>
                    <select name="month" class="form-select rounded-pill border-secondary-subtle" onchange="this.form.submit()">
                        @foreach($months_list as $m)
                            <option value="{{ $m['value'] }}" {{ $selectedMonth === $m['value'] ? 'selected' : '' }}>
                                {{ $m['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-8 text-md-end pt-3 pt-md-0">
                    <span class="text-secondary small">{{ __('messages.active_period') }}: </span>
                    <strong class="text-dark">{{ \App\Services\DateNumberHelper::translatedFormat($month, 'F Y') }}</strong>
                </div>
            </form>
        </div>

        {{-- 3 Main Summary KPI Cards --}}
        <div class="row g-4 mb-4">
            {{-- Litstore Card --}}
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 border-top border-4 border-primary">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-bold text-primary mb-0">
                            <i class="bi bi-box-seam me-2"></i>{{ __('messages.litstore_monthly_summary') }}
                        </h5>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1">{{ __('messages.litstore') }}</span>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-secondary small">{{ __('messages.store_received') }}</span>
                            <span class="fw-bold font-monospace text-success">+{{ $store_summary['received'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-secondary small">{{ __('messages.store_transferred_to_lit') }}</span>
                            <span class="fw-bold font-monospace text-primary">-{{ $store_summary['transferred'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-secondary small">{{ __('messages.store_returned_from_lit') }}</span>
                            <span class="fw-bold font-monospace text-warning">+{{ $store_summary['returned'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-secondary small">{{ __('messages.store_ending_remains') }}</span>
                            <span class="fw-bold fs-5 text-dark">{{ $store_summary['remains'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-secondary small">{{ __('messages.store_stock_valuation') }}</span>
                            <span class="fw-bold text-success">EGP {{ number_format($store_summary['valuation'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lit Committee Card --}}
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 border-top border-4 border-info">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-bold text-info mb-0">
                            <i class="bi bi-book me-2"></i>{{ __('messages.lit_comm_monthly_summary') }}
                        </h5>
                        <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1">{{ __('messages.lit_committee') }}</span>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-secondary small">{{ __('messages.lit_received_from_store') }}</span>
                            <span class="fw-bold font-monospace text-primary">+{{ $lit_summary['received'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-secondary small">{{ __('messages.lit_sold_to_groups') }}</span>
                            <span class="fw-bold font-monospace text-success">-{{ $lit_summary['sold'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-secondary small">{{ __('messages.lit_returned_to_store') }}</span>
                            <span class="fw-bold font-monospace text-warning">-{{ $lit_summary['returned'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-secondary small">{{ __('messages.lit_ending_remains') }}</span>
                            <span class="fw-bold fs-5 text-dark">{{ $lit_summary['remains'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-secondary small">{{ __('messages.total_monthly_sales_value') }}</span>
                            <span class="fw-bold text-success">EGP {{ number_format($lit_summary['sales_valuation'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Combined Overview Card --}}
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 border-top border-4 border-success bg-light bg-opacity-25">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-bold text-success mb-0">
                            <i class="bi bi-pie-chart me-2"></i>{{ __('messages.combined_monthly_overview') }}
                        </h5>
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">{{ __('messages.total') }}</span>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-secondary">{{ __('messages.total_combined_stock') }}</span>
                            <span class="fw-bold fs-4 text-dark">{{ $grand_totals['total_stock'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-secondary">{{ __('messages.total_combined_inventory_valuation') }}</span>
                            <span class="fw-bold fs-5 text-success">EGP {{ number_format($grand_totals['total_valuation'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-secondary">{{ __('messages.total_month_sales_revenue') }}</span>
                            <span class="fw-bold fs-5 text-primary">EGP {{ number_format($grand_totals['total_sales_value'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ledger Breakdown Tabs --}}
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <ul class="nav nav-pills mb-4" id="ledgerTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-4" id="categories-tab" data-bs-toggle="pill" data-bs-target="#categories-content" type="button" role="tab">
                        <i class="bi bi-grid me-2"></i>{{ __('messages.category_breakdown') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4" id="items-tab" data-bs-toggle="pill" data-bs-target="#items-content" type="button" role="tab">
                        <i class="bi bi-list-check me-2"></i>{{ __('messages.itemized_detailed_ledger') }}
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="ledgerTabContent">
                {{-- Category Breakdown Tab --}}
                <div class="tab-pane fade show active" id="categories-content" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" class="align-middle">{{ __('messages.Category') }}</th>
                                    <th rowspan="2" class="align-middle text-center">{{ __('messages.items_count') }}</th>
                                    <th colspan="4" class="text-center bg-primary bg-opacity-10">{{ __('messages.litstore') }}</th>
                                    <th colspan="4" class="text-center bg-info bg-opacity-10">{{ __('messages.lit_committee') }}</th>
                                    <th colspan="2" class="text-center bg-success bg-opacity-10">{{ __('messages.total') }}</th>
                                </tr>
                                <tr>
                                    {{-- Store --}}
                                    <th class="text-center small">{{ __('messages.received_short') }}</th>
                                    <th class="text-center small">{{ __('messages.transferred_short') }}</th>
                                    <th class="text-center small">{{ __('messages.returned_short') }}</th>
                                    <th class="text-center small">{{ __('messages.remains_short') }}</th>
                                    {{-- Lit --}}
                                    <th class="text-center small">{{ __('messages.received_short') }}</th>
                                    <th class="text-center small">{{ __('messages.sold_short') }}</th>
                                    <th class="text-center small">{{ __('messages.returned_short') }}</th>
                                    <th class="text-center small">{{ __('messages.remains_short') }}</th>
                                    {{-- Total --}}
                                    <th class="text-center small">{{ __('messages.remains_short') }}</th>
                                    <th class="text-end small">{{ __('messages.Valuation') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $cat)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $cat['category'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-secondary border rounded-pill">{{ $cat['items_count'] }}</span>
                                        </td>
                                        {{-- Store --}}
                                        <td class="text-center text-success">+{{ $cat['store_received'] }}</td>
                                        <td class="text-center text-primary">-{{ $cat['store_transferred'] }}</td>
                                        <td class="text-center text-warning">+{{ $cat['store_returned'] }}</td>
                                        <td class="text-center fw-bold">{{ $cat['store_remains'] }}</td>
                                        {{-- Lit --}}
                                        <td class="text-center text-primary">+{{ $cat['lit_received'] }}</td>
                                        <td class="text-center text-success fw-bold">-{{ $cat['lit_sold'] }}</td>
                                        <td class="text-center text-warning">-{{ $cat['lit_returned'] }}</td>
                                        <td class="text-center fw-bold text-info">{{ $cat['lit_remains'] }}</td>
                                        {{-- Total --}}
                                        <td class="text-center fw-bold">{{ $cat['total_remains'] }}</td>
                                        <td class="text-end fw-bold text-success">EGP {{ number_format($cat['total_valuation'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="2">{{ __('messages.Total') }}:</td>
                                    {{-- Store --}}
                                    <td class="text-center text-success">+{{ $store_summary['received'] }}</td>
                                    <td class="text-center text-primary">-{{ $store_summary['transferred'] }}</td>
                                    <td class="text-center text-warning">+{{ $store_summary['returned'] }}</td>
                                    <td class="text-center">{{ $store_summary['remains'] }}</td>
                                    {{-- Lit --}}
                                    <td class="text-center text-primary">+{{ $lit_summary['received'] }}</td>
                                    <td class="text-center text-success">-{{ $lit_summary['sold'] }}</td>
                                    <td class="text-center text-warning">-{{ $lit_summary['returned'] }}</td>
                                    <td class="text-center text-info">{{ $lit_summary['remains'] }}</td>
                                    {{-- Total --}}
                                    <td class="text-center">{{ $grand_totals['total_stock'] }}</td>
                                    <td class="text-end text-success">EGP {{ number_format($grand_totals['total_valuation'], 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Itemized Detailed Ledger Tab --}}
                <div class="tab-pane fade" id="items-content" role="tabpanel">
                    <div class="mb-3">
                        <input type="text" id="ledgerItemSearch" class="form-control rounded-pill border-secondary-subtle px-3" placeholder="{{ __('messages.search_ledger_items_placeholder') }}">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle table-hover mb-0" id="ledgerItemTable">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" class="align-middle">{{ __('messages.item_name') }}</th>
                                    <th rowspan="2" class="align-middle">{{ __('messages.Category') }}</th>
                                    <th rowspan="2" class="align-middle text-end">{{ __('messages.selling_price') }}</th>
                                    <th colspan="4" class="text-center bg-primary bg-opacity-10">{{ __('messages.litstore') }}</th>
                                    <th colspan="4" class="text-center bg-info bg-opacity-10">{{ __('messages.lit_committee') }}</th>
                                    <th colspan="2" class="text-center bg-success bg-opacity-10">{{ __('messages.total') }}</th>
                                </tr>
                                <tr>
                                    {{-- Store --}}
                                    <th class="text-center small">{{ __('messages.received_short') }}</th>
                                    <th class="text-center small">{{ __('messages.transferred_short') }}</th>
                                    <th class="text-center small">{{ __('messages.returned_short') }}</th>
                                    <th class="text-center small">{{ __('messages.remains_short') }}</th>
                                    {{-- Lit --}}
                                    <th class="text-center small">{{ __('messages.received_short') }}</th>
                                    <th class="text-center small">{{ __('messages.sold_short') }}</th>
                                    <th class="text-center small">{{ __('messages.returned_short') }}</th>
                                    <th class="text-center small">{{ __('messages.remains_short') }}</th>
                                    {{-- Total --}}
                                    <th class="text-center small">{{ __('messages.remains_short') }}</th>
                                    <th class="text-end small">{{ __('messages.Valuation') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $it)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $it['display_name'] }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary border rounded-pill">{{ $it['category'] }}</span>
                                        </td>
                                        <td class="text-end font-monospace">
                                            EGP {{ number_format($it['selling_price'], 2) }}
                                        </td>
                                        {{-- Store --}}
                                        <td class="text-center">{{ $it['store_received'] ?: '-' }}</td>
                                        <td class="text-center text-primary">{{ $it['store_transferred'] ? "-{$it['store_transferred']}" : '-' }}</td>
                                        <td class="text-center text-warning">{{ $it['store_returned'] ? "+{$it['store_returned']}" : '-' }}</td>
                                        <td class="text-center fw-semibold">{{ $it['store_remains'] }}</td>
                                        {{-- Lit --}}
                                        <td class="text-center text-primary">{{ $it['lit_received'] ? "+{$it['lit_received']}" : '-' }}</td>
                                        <td class="text-center text-success fw-bold">{{ $it['lit_sold'] ? "-{$it['lit_sold']}" : '-' }}</td>
                                        <td class="text-center text-warning">{{ $it['lit_returned'] ? "-{$it['lit_returned']}" : '-' }}</td>
                                        <td class="text-center fw-bold text-info">{{ $it['lit_remains'] }}</td>
                                        {{-- Total --}}
                                        <td class="text-center fw-bold">{{ $it['total_remains'] }}</td>
                                        <td class="text-end fw-bold text-success font-monospace">EGP {{ number_format($it['total_valuation'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var searchInput = document.getElementById("ledgerItemSearch");
        if (searchInput) {
            searchInput.addEventListener("keyup", function() {
                var value = this.value.toLowerCase();
                var rows = document.querySelectorAll("#ledgerItemTable tbody tr");
                rows.forEach(function(row) {
                    var text = row.textContent.toLowerCase();
                    row.style.display = text.indexOf(value) > -1 ? "" : "none";
                });
            });
        }
    });
    </script>
</x-layout>
