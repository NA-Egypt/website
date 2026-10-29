<x-layout>
    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h1 class="h2 text-gradient font-bold mb-0">
                    {{ __('messages.inventory_transaction_reports') }}
                </h1>
                <p class="text-secondary mb-0">
                    {{ __('messages.reports_desc') }}
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="/guidelines/inventory/#store-reports" target="_blank" rel="noopener" class="btn btn-outline-primary rounded-pill px-3 shadow-sm" title="{{ __('messages.inventory_system_guide_desc') }}">
                    <i class="bi bi-book-half me-1"></i>
                    {{ __('messages.inventory_system_guide') }}
                </a>
                <a href="{{ route('store.reports.pdf', request()->all()) }}" class="btn btn-outline-danger rounded-pill px-3 shadow-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i>
                    {{ __('messages.export_pdf') }}
                </a>
                <a href="{{ route('store.reports.csv', request()->all()) }}" class="btn btn-outline-success rounded-pill px-3 shadow-sm">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                    {{ __('messages.export_csv') }}
                </a>
            </div>
        </div>

        {{-- Metrics Summary Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-secondary small fw-semibold">{{ __('messages.total_transactions') ?? 'Total Transactions' }}</span>
                                <x-inventory-card-help
                                    :title="__('messages.total_transactions')"
                                    :description="__('messages.card_help_reports_transactions')"
                                    :formula="__('messages.card_formula_reports_transactions')"
                                />
                            </div>
                            <h3 class="mt-1 mb-0 font-bold text-dark">{{ $totalTransactions }}</h3>
                        </div>
                        <div class="fs-2 text-primary bg-primary-subtle rounded-3 p-2 px-3"><i class="bi bi-receipt"></i></div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-secondary small fw-semibold">{{ __('messages.total_received_qty') ?? 'Total Received Qty' }}</span>
                                <x-inventory-card-help
                                    :title="__('messages.total_received_qty')"
                                    :description="__('messages.card_help_reports_received')"
                                    :formula="__('messages.card_formula_reports_received')"
                                />
                            </div>
                            <h3 class="mt-1 mb-0 font-bold text-success">{{ $totalReceivedQty }}</h3>
                        </div>
                        <div class="fs-2 text-success bg-success-subtle rounded-3 p-2 px-3"><i class="bi bi-box-arrow-in-down"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-secondary small fw-semibold">{{ __('messages.total_transferred_qty') ?? 'Total Transferred Qty' }}</span>
                                <x-inventory-card-help
                                    :title="__('messages.total_transferred_qty')"
                                    :description="__('messages.card_help_reports_transferred')"
                                    :formula="__('messages.card_formula_reports_transferred')"
                                />
                            </div>
                            <h3 class="mt-1 mb-0 font-bold text-info">{{ $totalTransferredQty }}</h3>
                        </div>
                        <div class="fs-2 text-info bg-info-subtle rounded-3 p-2 px-3"><i class="bi bi-box-arrow-up-right"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-secondary small fw-semibold">{{ __('messages.total_transaction_value') ?? 'Transaction Valuation' }}</span>
                                <x-inventory-card-help
                                    :title="__('messages.total_transaction_value')"
                                    :description="__('messages.card_help_reports_valuation')"
                                    :formula="__('messages.card_formula_reports_valuation')"
                                />
                            </div>
                            <h3 class="mt-1 mb-0 font-bold text-warning-emphasis">{{ __('messages.EGP') }} {{ number_format($totalTransactionValuation, 2) }}</h3>
                        </div>
                        <div class="fs-2 text-warning bg-warning-subtle rounded-3 p-2 px-3"><i class="bi bi-currency-dollar"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Movement Velocity & Category Analytics Row --}}
        @php
            $sumMovement = $totalReceivedQty + $totalTransferredQty + ($totalReturnedQty ?? 0);
            $receivePct = $sumMovement > 0 ? round(($totalReceivedQty / $sumMovement) * 100, 1) : 0;
            $transferPct = $sumMovement > 0 ? round(($totalTransferredQty / $sumMovement) * 100, 1) : 0;
            $returnPct = $sumMovement > 0 ? round((($totalReturnedQty ?? 0) / $sumMovement) * 100, 1) : 0;
        @endphp
        <div class="row g-4 mb-4">
            {{-- Stock Movement Velocity Bar Card --}}
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-speedometer2 text-primary"></i>
                            <span>{{ __('messages.stock_movement_velocity') }}</span>
                            <x-inventory-card-help
                                :title="__('messages.stock_movement_velocity')"
                                :description="__('messages.card_help_reports_velocity')"
                                :formula="__('messages.card_formula_reports_velocity')"
                            />
                        </h5>
                        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small">Total: {{ $sumMovement }} {{ __('messages.items') }}</span>
                    </div>

                    <div class="progress rounded-pill mb-4 shadow-inner" style="height: 14px; background-color: rgba(0,0,0,0.05);">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $receivePct }}%;" title="Received: {{ $receivePct }}%"></div>
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $transferPct }}%;" title="Transferred: {{ $transferPct }}%"></div>
                        <div class="progress-bar bg-warning text-dark" role="progressbar" style="width: {{ $returnPct }}%;" title="Returned: {{ $returnPct }}%"></div>
                    </div>

                    <div class="row text-center g-2 pt-3 border-top mt-auto">
                        <div class="col-12 col-sm-4">
                            <div class="p-2 rounded bg-light-subtle">
                                <span class="d-inline-block rounded-circle bg-success me-1" style="width: 8px; height: 8px;"></span>
                                <span class="small text-secondary fw-semibold">{{ __('messages.receive') }}</span>
                                <div class="fw-bold text-dark fs-7 mt-1">{{ $totalReceivedQty }} <span class="text-muted">({{ $receivePct }}%)</span></div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="p-2 rounded bg-light-subtle">
                                <span class="d-inline-block rounded-circle bg-primary me-1" style="width: 8px; height: 8px;"></span>
                                <span class="small text-secondary fw-semibold">{{ __('messages.transfer_to_lit') }}</span>
                                <div class="fw-bold text-dark fs-7 mt-1">{{ $totalTransferredQty }} <span class="text-muted">({{ $transferPct }}%)</span></div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="p-2 rounded bg-light-subtle">
                                <span class="d-inline-block rounded-circle bg-warning me-1" style="width: 8px; height: 8px;"></span>
                                <span class="small text-secondary fw-semibold">{{ __('messages.return_from_lit') }}</span>
                                <div class="fw-bold text-dark fs-7 mt-1">{{ $totalReturnedQty ?? 0 }} <span class="text-muted">({{ $returnPct }}%)</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Category Valuation Share Breakdown --}}
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-pie-chart text-info"></i>
                        <span>{{ __('messages.category_stock_share') }}</span>
                        <x-inventory-card-help
                            :title="__('messages.category_stock_share')"
                            :description="__('messages.card_help_reports_category_share')"
                            :formula="__('messages.card_formula_reports_category_share')"
                        />
                    </h5>
                    <div class="row g-2">
                        @foreach(($categoryBreakdown ?? []) as $catName => $catData)
                            @php
                                $catPct = $totalValuation > 0 ? round(($catData['valuation'] / $totalValuation) * 100, 1) : 0;
                            @endphp
                            <div class="col-12 col-sm-6">
                                <div class="p-2.5 px-3 rounded-3 bg-light-subtle border border-light-subtle h-100 d-flex flex-column justify-content-between">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-semibold text-dark small me-1">{{ __('messages.cat_' . Str::snake(str_replace(' ', '_', $catName))) }}</span>
                                        <span class="badge bg-info-subtle text-info rounded-pill" style="font-size: 0.7rem;">{{ $catPct }}%</span>
                                    </div>
                                    <div class="progress rounded-pill mb-1 shadow-sm" style="height: 5px; background-color: rgba(0,0,0,0.06);">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $catPct }}%;"></div>
                                    </div>
                                    <div class="text-secondary small fw-bold mt-1 text-end" style="font-size: 0.75rem;">
                                        {{ __('messages.EGP') }} {{ number_format($catData['valuation'], 2) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Panel --}}
        <div class="card border-0 shadow-sm p-4 mb-4">
            <form action="{{ route('store.reports') }}" method="GET" class="row g-3">
                <div class="col-12 col-md-2">
                    <label class="form-label small fw-semibold">{{ __('messages.transaction_type') }}</label>
                    <select name="type" class="form-select rounded-3">
                        <option value="">{{ __('messages.all_types') }}</option>
                        <option value="receive" {{ request('type') === 'receive' ? 'selected' : '' }}>{{ __('messages.receive') }}</option>
                        <option value="transfer_to_lit" {{ request('type') === 'transfer_to_lit' ? 'selected' : '' }}>{{ __('messages.transfer_to_lit') }}</option>
                        <option value="return_from_lit" {{ request('type') === 'return_from_lit' ? 'selected' : '' }}>{{ __('messages.return_from_lit') }}</option>
                        <option value="damaged" {{ request('type') === 'damaged' ? 'selected' : '' }}>{{ __('messages.tx_type_damaged') }}</option>
                        <option value="receive_cancelled" {{ request('type') === 'receive_cancelled' ? 'selected' : '' }}>{{ __('messages.tx_type_receive_cancelled') }}</option>
                        <option value="lit_sale" {{ request('type') === 'lit_sale' ? 'selected' : '' }}>{{ __('messages.tx_type_lit_sale') }}</option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold">{{ __('messages.Category') }}</label>
                    <select name="category" class="form-select rounded-3">
                        <option value="">{{ __('messages.all') }}</option>
                        @foreach(\App\Models\InventoryItem::CATEGORIES as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                {{ __('messages.cat_' . Str::snake(str_replace(' ', '_', $cat))) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold">{{ __('messages.item_name') }}</label>
                    <select name="item_id" class="form-select rounded-3 select2">
                        <option value="">{{ __('messages.all_items') }}</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->store_display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label small fw-semibold">{{ __('messages.start_date') }}</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control rounded-3">
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label small fw-semibold">{{ __('messages.end_date') }}</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control rounded-3">
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('store.reports') }}" class="btn btn-light rounded-pill px-4">{{ __('messages.reset_filters') }}</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('messages.apply_filters') }}</button>
                </div>
            </form>
        </div>

        {{-- Transactions Ledger Table --}}
        <div class="card border-0 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary"></i>
                    {{ __('messages.transaction_ledger') }}
                </h5>
                <div class="position-relative" style="min-width: 250px;">
                    <input type="search" id="ledgerSearchInput" class="form-control form-control-sm rounded-pill px-3" placeholder="{{ __('messages.Search') }}...">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0" id="ledgerTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">{{ __('messages.id') }}</th>
                            <th style="width: 150px;">{{ __('messages.date_time') }}</th>
                            <th>{{ __('messages.item_name') }}</th>
                            <th>{{ __('messages.Category') }}</th>
                            <th>{{ __('messages.transaction_type') }}</th>
                            <th class="text-center" style="width: 110px;">{{ __('messages.qty') }}</th>
                            <th>{{ __('messages.operator') }}</th>
                            <th>{{ __('messages.Notes') }}</th>
                            <th class="text-center">{{ __('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $t)
                            <tr class="ledger-row {{ ($t->status ?? 'active') === 'cancelled' ? 'opacity-50' : '' }}" id="ledger-tx-row-{{ $t->id }}">
                                <td>
                                    <span class="text-secondary font-monospace fw-semibold">#{{ $t->id }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $t->created_at->format('Y-m-d') }}</div>
                                    <div class="text-secondary small fs-7">{{ $t->created_at->format('H:i:s') }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $t->item->store_display_name ?? 'Deleted Item' }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1 small">
                                        {{ __('messages.cat_' . Str::snake(str_replace(' ', '_', $t->item->category ?? 'Others'))) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($t->type === 'receive')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-1 shadow-sm">
                                            <i class="bi bi-plus-circle-fill"></i>{{ __('messages.receive') }}
                                        </span>
                                        @if (($t->status ?? 'active') === 'cancelled')
                                            <span class="badge bg-danger text-white rounded-pill ms-1 small tx-cancelled-badge">{{ __('messages.tx_status_cancelled') }}</span>
                                        @endif
                                    @elseif ($t->type === 'transfer_to_lit' || $t->type === 'transfer')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-1 shadow-sm">
                                            <i class="bi bi-arrow-right-circle-fill"></i>{{ __('messages.transfer_to_lit') }}
                                        </span>
                                    @elseif ($t->type === 'return_from_lit')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-1 shadow-sm text-dark">
                                            <i class="bi bi-arrow-left-circle-fill"></i>{{ __('messages.return_from_lit') }}
                                        </span>
                                    @elseif ($t->type === 'damaged')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-1 shadow-sm">
                                            <i class="bi bi-heartbreak-fill"></i>{{ __('messages.tx_type_damaged') }}
                                        </span>
                                    @elseif ($t->type === 'receive_cancelled')
                                        <span class="badge bg-secondary-subtle text-dark border border-secondary-subtle px-3 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-1 shadow-sm">
                                            <i class="bi bi-arrow-counterclockwise"></i>{{ __('messages.tx_type_receive_cancelled') }}
                                        </span>
                                    @elseif ($t->type === 'lit_sale' || $t->type === 'lit_sale_adjustment')
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-1 shadow-sm">
                                            <i class="bi bi-cart-check-fill"></i>{{ __('messages.tx_type_lit_sale') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-dark-subtle text-dark px-3 py-2 rounded-pill font-monospace fw-bold fs-6 {{ ($t->status ?? 'active') === 'cancelled' ? 'text-decoration-line-through' : '' }}">
                                        {{ $t->quantity }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.8rem; font-weight: 600;">
                                            {{ strtoupper(substr($t->user->name ?? 'S', 0, 2)) }}
                                        </div>
                                        <span class="text-dark small fw-semibold">{{ $t->user->name ?? 'System' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-secondary small">{{ $t->notes ?: '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($t->type === 'receive' && ($t->status ?? 'active') === 'active' && $t->item)
                                        <button type="button" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-2 py-1 fw-semibold btn-ledger-wrong-receive"
                                                data-tx-id="{{ $t->id }}"
                                                data-item-id="{{ $t->item->id }}"
                                                data-item-name="{{ $t->item->store_display_name }}"
                                                data-qty="{{ $t->quantity }}"
                                                data-date="{{ $t->created_at->format('Y-m-d H:i') }}"
                                                data-notes="{{ $t->notes }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#reportsWrongReceiveModal">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('messages.wrong_receive_btn') }}
                                        </button>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-secondary py-5">
                                    <i class="bi bi-info-circle fs-2 d-block mb-2"></i>
                                    {{ __('messages.no_transactions_match') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Wrong Receive Modal on Reports Page --}}
    <div class="modal fade" id="reportsWrongReceiveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="" method="POST" class="modal-content border-0 shadow-lg rounded-4" id="reportsWrongReceiveForm">
                @csrf
                <input type="hidden" name="transaction_id" id="reportsWrongTxId" value="">
                <div class="modal-header border-0 bg-warning-subtle text-warning-emphasis">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>{{ __('messages.wrong_receive_title', ['name' => '']) }}<span class="modal-title-name"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-secondary small mb-3">{{ __('messages.wrong_receive_desc') }}</p>
                    <div class="alert alert-light border border-warning-subtle rounded-3 p-3 mb-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-secondary">{{ __('messages.received_quantity') }}:</span>
                            <span class="badge bg-success-subtle text-success fw-bold" id="reportsWrongQtyBadge">+0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary">{{ __('messages.Date') ?? 'Date' }}:</span>
                            <span class="fw-semibold text-dark" id="reportsWrongDateText">-</span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">{{ __('messages.wrong_receive_reason_label') }}</label>
                        <textarea name="reason" class="form-control rounded-3" rows="2" placeholder="{{ __('messages.wrong_receive_reason_placeholder') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm">
                        <i class="bi bi-arrow-repeat me-1"></i>{{ __('messages.confirm_cancel_and_reenter') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- New Receive Modal on Reports Page (opened automatically after cancelling wrong receive) --}}
    <div class="modal fade" id="reportsNewReceiveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="" method="POST" class="modal-content border-0 shadow-lg rounded-4" id="reportsNewReceiveForm">
                @csrf
                <div class="modal-header border-0 bg-success-subtle text-success">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>{{ __('messages.receive_stock_for', ['name' => '']) }}<span class="modal-title-name"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.received_quantity') }}</label>
                        <input type="number" name="quantity" min="1" required class="form-control rounded-3" placeholder="e.g. 50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.Notes') }}</label>
                        <textarea name="notes" class="form-control rounded-3" rows="3" placeholder="e.g. Corrected receive entry"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">{{ __('messages.confirm_receipt') }}</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .fs-7 { font-size: 0.75rem; }
        .ledger-row {
            transition: all 0.2s ease-in-out;
        }
        .ledger-row:hover {
            background-color: rgba(59, 130, 246, 0.04) !important;
            transform: scale(1.002);
        }
        .select2-container {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
        }
        /* Custom styling to match Bootstrap 5 minimal theme for Select2 */
        .select2-container--bootstrap4 .select2-selection--single {
            border: 1px solid var(--glass-border) !important;
            border-radius: 8px !important;
            height: 38px !important;
            background: rgba(0, 0, 0, 0.02) !important;
            display: flex !important;
            align-items: center !important;
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }
        .select2-container--bootstrap4 .select2-selection__rendered {
            color: var(--text-primary) !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            line-height: normal !important;
        }
        .select2-container--bootstrap4 .select2-selection__arrow {
            height: 36px !important;
            top: 1px !important;
            right: 10px !important;
        }
        [dir="rtl"] .select2-container--bootstrap4 .select2-selection__arrow {
            left: 10px !important;
            right: auto !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = '{{ csrf_token() }}';
            const inventoryChannel = ('BroadcastChannel' in window) ? new BroadcastChannel('na_egypt_inventory_live') : null;

            if (window.jQuery && $.fn.select2) {
                $('.select2').select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: "{{ __('messages.all_items') }}",
                    allowClear: true
                });
            }

            const searchInput = document.getElementById('ledgerSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const q = this.value.toLowerCase().trim();
                    const rows = document.querySelectorAll('#ledgerTable tbody tr.ledger-row');
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(q) ? '' : 'none';
                    });
                });
            }

            const wrongModal = document.getElementById('reportsWrongReceiveModal');
            const wrongForm = document.getElementById('reportsWrongReceiveForm');
            const newRecModal = document.getElementById('reportsNewReceiveModal');
            const newRecForm = document.getElementById('reportsNewReceiveForm');
            let activeItemName = '';

            if (wrongModal) {
                wrongModal.addEventListener('show.bs.modal', function (event) {
                    const btn = event.relatedTarget;
                    if (!btn) return;
                    const txId = btn.getAttribute('data-tx-id');
                    const itemId = btn.getAttribute('data-item-id');
                    activeItemName = btn.getAttribute('data-item-name') || '';
                    const qty = btn.getAttribute('data-qty');
                    const date = btn.getAttribute('data-date');

                    wrongForm.action = "{{ route('store.cancel_wrong_receive', ':id') }}".replace(':id', itemId);
                    wrongForm.reset();
                    document.getElementById('reportsWrongTxId').value = txId;
                    wrongModal.querySelector('.modal-title-name').textContent = activeItemName;
                    document.getElementById('reportsWrongQtyBadge').textContent = '+' + qty;
                    document.getElementById('reportsWrongDateText').textContent = date;
                });
            }

            if (wrongForm) {
                wrongForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const txId = document.getElementById('reportsWrongTxId').value;
                    fetch(wrongForm.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: new FormData(wrongForm)
                    })
                    .then(res => res.json().then(data => ({ status: res.status, body: data })))
                    .then(({ status, body }) => {
                        if (status >= 200 && status < 300 && body.success) {
                            const inst = bootstrap.Modal.getInstance(wrongModal);
                            if (inst) inst.hide();

                            // Mark row as cancelled live without reload
                            const row = document.getElementById('ledger-tx-row-' + txId);
                            if (row) {
                                row.classList.add('opacity-50');
                                const btn = row.querySelector('.btn-ledger-wrong-receive');
                                if (btn) btn.replaceWith(document.createTextNode('-'));
                            }

                            if (inventoryChannel && body.live_state) {
                                try { inventoryChannel.postMessage({ live_state: body.live_state }); } catch (err) {}
                            }

                            if (body.start_new_receive_item_id && newRecModal) {
                                newRecForm.action = "{{ route('store.receive', ':id') }}".replace(':id', body.start_new_receive_item_id);
                                newRecForm.reset();
                                newRecModal.querySelector('.modal-title-name').textContent = activeItemName;
                                setTimeout(() => {
                                    bootstrap.Modal.getOrCreateInstance(newRecModal).show();
                                }, 300);
                            }
                        } else {
                            alert(body.message || 'Error cancelling receive.');
                        }
                    });
                });
            }

            if (newRecForm) {
                newRecForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    fetch(newRecForm.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: new FormData(newRecForm)
                    })
                    .then(res => res.json().then(data => ({ status: res.status, body: data })))
                    .then(({ status, body }) => {
                        if (status >= 200 && status < 300 && body.success) {
                            const inst = bootstrap.Modal.getInstance(newRecModal);
                            if (inst) inst.hide();
                            if (inventoryChannel && body.live_state) {
                                try { inventoryChannel.postMessage({ live_state: body.live_state }); } catch (err) {}
                            }
                        } else {
                            alert(body.message || 'Error recording receive.');
                        }
                    });
                });
            }
        });
    </script>
</x-layout>
