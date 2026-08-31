<x-layout>
    <div class="container-fluid py-4">
        {{-- Header Section --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h2 text-gradient font-bold mb-1">
                    <i class="bi bi-arrow-left-right me-2"></i>{{ __('messages.lit_reconciliation_title') }}
                </h1>
                <p class="text-secondary mb-0">
                    {{ __('messages.lit_reconciliation_desc') }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('slips.index') }}" class="btn btn-outline-secondary rounded-pill shadow-sm px-3">
                    <i class="bi bi-receipt me-1"></i>{{ __('messages.inventory_slips') }}
                </a>
                <a href="{{ route('lit.ledger') }}" class="btn btn-primary rounded-pill shadow-sm px-3">
                    <i class="bi bi-journal-text me-1"></i>{{ __('messages.monthly_ledger') }}
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Month Selector & Status Banner --}}
        <div class="card p-3 shadow-sm border-0 mb-4 rounded-4">
            <form action="{{ route('lit.reconciliation') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-12 col-md-4">
                    <label class="form-label text-secondary small mb-1">{{ __('messages.select_reconciliation_month') }}</label>
                    <select name="month" class="form-select rounded-pill border-secondary-subtle" onchange="this.form.submit()">
                        @foreach($monthsList as $m)
                            <option value="{{ $m['value'] }}" {{ $selectedMonth === $m['value'] ? 'selected' : '' }}>
                                {{ $m['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-8 text-md-end">
                    @if($has_stocktaking)
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                            <i class="bi bi-clipboard-check me-1"></i>{{ __('messages.stocktaking_synced_badge', ['date' => $stocktaking_date]) }}
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2">
                            <i class="bi bi-info-circle me-1"></i>{{ __('messages.no_stocktaking_using_current_stock') }}
                        </span>
                    @endif
                </div>
            </form>
        </div>

        {{-- Overview Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm d-flex align-items-center justify-content-between flex-row rounded-4">
                    <div>
                        <span class="text-secondary small fw-semibold">{{ __('messages.received_from_store_this_month') }}</span>
                        <h3 class="mt-1 mb-0 font-bold text-primary">{{ $total_received }}</h3>
                    </div>
                    <div class="fs-2 text-primary bg-primary-subtle rounded-3 p-2 px-3"><i class="bi bi-box-arrow-in-down"></i></div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm d-flex align-items-center justify-content-between flex-row rounded-4">
                    <div>
                        <span class="text-secondary small fw-semibold">{{ __('messages.sold_this_month') }}</span>
                        <h3 class="mt-1 mb-0 font-bold text-success">{{ $total_sold }}</h3>
                    </div>
                    <div class="fs-2 text-success bg-success-subtle rounded-3 p-2 px-3"><i class="bi bi-cart-check"></i></div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm d-flex align-items-center justify-content-between flex-row rounded-4">
                    <div>
                        <span class="text-secondary small fw-semibold">{{ __('messages.returned_to_store_this_month') }}</span>
                        <h3 class="mt-1 mb-0 font-bold text-warning">{{ $total_returned }}</h3>
                    </div>
                    <div class="fs-2 text-warning bg-warning-subtle rounded-3 p-2 px-3"><i class="bi bi-arrow-return-left"></i></div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm d-flex align-items-center justify-content-between flex-row rounded-4">
                    <div>
                        <span class="text-secondary small fw-semibold">{{ __('messages.current_lit_balance') }}</span>
                        <h3 class="mt-1 mb-0 font-bold text-info">{{ $total_current_lit }}</h3>
                    </div>
                    <div class="fs-2 text-info bg-info-subtle rounded-3 p-2 px-3"><i class="bi bi-bookshelf"></i></div>
                </div>
            </div>
        </div>

        {{-- Reconciliation & Return Form --}}
        <form action="{{ route('lit.reconciliation.return') }}" method="POST" id="reconciliationForm">
            @csrf
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="font-bold mb-0">
                        <i class="bi bi-table me-2 text-primary"></i>{{ __('messages.item_reconciliation_table') }}
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="fillSuggestedReturns()">
                        <i class="bi bi-magic me-1"></i>{{ __('messages.fill_suggested_returns') }}
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.item_name') }}</th>
                                <th>{{ __('messages.Category') }}</th>
                                <th class="text-center">{{ __('messages.received_qty') }} (+)</th>
                                <th class="text-center">{{ __('messages.sold_qty') }} (-)</th>
                                <th class="text-center">{{ __('messages.counted_lit_qty') }}</th>
                                <th class="text-center">{{ __('messages.current_lit_stock') }}</th>
                                <th class="text-center">{{ __('messages.suggested_return') }}</th>
                                <th class="text-center" style="width: 150px;">{{ __('messages.return_qty_to_store') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item['display_name'] }}</div>
                                        <div class="small text-muted">EGP {{ number_format($item['selling_price'], 2) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-1 small">
                                            {{ $item['category'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 font-monospace">
                                            +{{ $item['received_qty'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 font-monospace">
                                            -{{ $item['sold_qty'] }}
                                        </span>
                                    </td>
                                    <td class="text-center fw-semibold text-dark">
                                        {{ $item['counted_qty'] }}
                                    </td>
                                    <td class="text-center fw-bold text-info">
                                        {{ $item['current_lit_qty'] }}
                                    </td>
                                    <td class="text-center">
                                        @if($item['suggested_return'] > 0)
                                            <span class="badge bg-warning-subtle text-dark rounded-pill px-2 py-1 font-monospace fw-bold">
                                                {{ $item['suggested_return'] }}
                                            </span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <input type="number" 
                                               name="returns[{{ $item['item_id'] }}]" 
                                               class="form-control form-control-sm rounded-pill text-center return-input" 
                                               min="0" 
                                               max="{{ $item['current_lit_qty'] }}" 
                                               data-suggested="{{ $item['suggested_return'] }}"
                                               value="0" 
                                               placeholder="0">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row g-3 align-items-end mt-3 pt-3 border-top">
                    <div class="col-12 col-md-8">
                        <label class="form-label text-secondary small">{{ __('messages.return_notes_optional') }}</label>
                        <input type="text" name="notes" class="form-control rounded-pill border-secondary-subtle" placeholder="{{ __('messages.return_notes_placeholder') }}">
                    </div>
                    <div class="col-12 col-md-4 text-md-end">
                        <button type="submit" class="btn btn-warning rounded-pill shadow-sm px-4 fw-bold w-100 w-md-auto" onclick="return confirm('{{ __('messages.confirm_process_return_prompt') }}')">
                            <i class="bi bi-box-arrow-left me-1"></i>{{ __('messages.submit_return_to_store') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
    function fillSuggestedReturns() {
        document.querySelectorAll('.return-input').forEach(function(input) {
            var suggested = input.getAttribute('data-suggested');
            if (suggested && parseInt(suggested) > 0) {
                input.value = suggested;
            } else {
                input.value = 0;
            }
        });
    }
    </script>
</x-layout>
