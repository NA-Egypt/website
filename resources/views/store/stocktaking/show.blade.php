<x-layout>
    <div class="container-fluid py-4">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <a href="{{ route('store.stocktaking.index') }}" class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 32px; height: 32px;">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h1 class="h2 text-gradient font-bold mb-0">
                        {{ __('messages.stocktaking_comparison_report') }} &bull; Session #{{ $session->id }}
                    </h1>
                </div>
                <p class="text-secondary mb-0">
                    {{ __('messages.stocktaking_report_desc') }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('store.stocktaking.pdf', $session->id) }}" class="btn btn-outline-danger rounded-pill px-3 shadow-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i>{{ __('messages.export_pdf') }}
                </a>
                <a href="{{ route('store.stocktaking.csv', $session->id) }}" class="btn btn-outline-success rounded-pill px-3 shadow-sm">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>{{ __('messages.export_csv') }}
                </a>
                @if ($session->isInProgress())
                    <a href="{{ route('store.stocktaking.count', $session->id) }}" class="btn btn-warning rounded-pill px-3 shadow-sm text-dark fw-bold">
                        <i class="bi bi-pencil-square me-1"></i>{{ __('messages.continue_counting') }}
                    </a>
                @endif
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Status Card --}}
        <div class="card border-0 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="text-secondary small fw-semibold">{{ __('messages.status') }}:</span>
                    <span class="ms-2">
                        @if ($session->isInProgress())
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill fw-bold">
                                <i class="bi bi-lock-fill me-1"></i>{{ __('messages.in_progress_store_locked') }}
                            </span>
                        @elseif ($session->isCompleted())
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 rounded-pill fw-bold">
                                <i class="bi bi-clock-history me-1"></i>{{ __('messages.completed_pending_adj') }}
                            </span>
                        @elseif ($session->isAdjusted())
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">
                                <i class="bi bi-check-circle-fill me-1"></i>{{ __('messages.adjusted_and_unlocked') }}
                            </span>
                        @endif
                    </span>
                    <div class="small text-secondary mt-1">
                        {{ __('messages.started_by') }}: <strong class="text-dark">{{ $session->user->name ?? 'System' }}</strong> ({{ $session->started_at->format('Y-m-d H:i') }})
                        @if ($session->isAdjusted())
                            &bull; {{ __('messages.adjusted_by') }}: <strong class="text-dark">{{ $session->adjustedByUser->name ?? 'System' }}</strong> ({{ $session->adjusted_at ? $session->adjusted_at->format('Y-m-d H:i') : '' }})
                        @endif
                    </div>
                </div>

                @if (!$session->isAdjusted())
                    <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#applyAdjustmentsModal">
                        <i class="bi bi-check-all me-1"></i>{{ __('messages.apply_adjustments_to_system') }}
                    </button>
                @endif
            </div>
        </div>

        {{-- Metrics Summary Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-semibold">{{ __('messages.total_expected_system_qty') }}</span>
                            <h3 class="mt-1 mb-0 font-bold text-dark">{{ $totalSystemQty }}</h3>
                        </div>
                        <div class="fs-2 text-secondary bg-light rounded-3 p-2 px-3"><i class="bi bi-box"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-semibold">{{ __('messages.total_physical_counted_qty') }}</span>
                            <h3 class="mt-1 mb-0 font-bold text-primary">{{ $totalCountedQty }}</h3>
                        </div>
                        <div class="fs-2 text-primary bg-primary-subtle rounded-3 p-2 px-3"><i class="bi bi-calculator"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-semibold">{{ __('messages.net_qty_variance') }}</span>
                            <h3 class="mt-1 mb-0 font-bold {{ $totalVarianceQty >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $totalVarianceQty > 0 ? "+{$totalVarianceQty}" : $totalVarianceQty }}
                            </h3>
                        </div>
                        <div class="fs-2 {{ $totalVarianceQty >= 0 ? 'text-success bg-success-subtle' : 'text-danger bg-danger-subtle' }} rounded-3 p-2 px-3">
                            <i class="bi {{ $totalVarianceQty >= 0 ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' }}"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-semibold">{{ __('messages.net_financial_variance_value') }}</span>
                            <h3 class="mt-1 mb-0 font-bold {{ $totalVarianceValue >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ __('messages.EGP') }} {{ number_format($totalVarianceValue, 2) }}
                            </h3>
                        </div>
                        <div class="fs-2 text-warning bg-warning-subtle rounded-3 p-2 px-3"><i class="bi bi-currency-dollar"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Comparison Ledger Table --}}
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-table text-primary"></i>
                {{ __('messages.stocktaking_comparison_details') }}
            </h5>
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.item_name') }}</th>
                            <th>{{ __('messages.Category') }}</th>
                            <th class="text-center">{{ __('messages.system_store_qty') }}</th>
                            <th class="text-center">{{ __('messages.counted_store_qty') }}</th>
                            <th class="text-center">{{ __('messages.store_variance') }}</th>
                            <th class="text-center">{{ __('messages.system_lit_qty') }}</th>
                            <th class="text-center">{{ __('messages.counted_lit_qty') }}</th>
                            <th class="text-center">{{ __('messages.lit_variance') }}</th>
                            <th class="text-end">{{ __('messages.unit_price') }}</th>
                            <th class="text-end">{{ __('messages.variance_value') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            @php
                                $countedStore = $item->counted_store_qty ?? $item->system_store_qty;
                                $countedLit = $item->counted_lit_qty ?? $item->system_lit_qty;
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->inventoryItem->store_display_name ?? 'Item #' . $item->inventory_item_id }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
                                        {{ __('messages.cat_' . Str::snake(str_replace(' ', '_', $item->inventoryItem->category ?? 'Others'))) }}
                                    </span>
                                </td>
                                <td class="text-center fw-semibold text-secondary">{{ $item->system_store_qty }}</td>
                                <td class="text-center fw-bold text-dark">{{ $countedStore }}</td>
                                <td class="text-center">
                                    @if ($item->store_variance == 0)
                                        <span class="text-muted small">0</span>
                                    @elseif ($item->store_variance > 0)
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">+{{ $item->store_variance }}</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">{{ $item->store_variance }}</span>
                                    @endif
                                </td>
                                <td class="text-center fw-semibold text-secondary">{{ $item->system_lit_qty }}</td>
                                <td class="text-center fw-bold text-dark">{{ $countedLit }}</td>
                                <td class="text-center">
                                    @if ($item->lit_variance == 0)
                                        <span class="text-muted small">0</span>
                                    @elseif ($item->lit_variance > 0)
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">+{{ $item->lit_variance }}</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">{{ $item->lit_variance }}</span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold text-dark">{{ __('messages.EGP') }} {{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end fw-bold {{ $item->variance_value >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ __('messages.EGP') }} {{ number_format($item->variance_value, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Apply Adjustments Modal --}}
    @if (!$session->isAdjusted())
        <div class="modal fade" id="applyAdjustmentsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('store.stocktaking.adjust', $session->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                    @csrf
                    <div class="modal-header border-0 bg-success-subtle text-success">
                        <h5 class="modal-title fw-bold"><i class="bi bi-check-all me-2"></i>{{ __('messages.apply_adjustments_to_system') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-dark mb-3">
                            {{ __('messages.apply_adjustments_confirm_desc', ['count' => $items->count()]) }}
                        </p>
                        <div class="card p-3 bg-light border-0 rounded-3 mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-secondary">{{ __('messages.net_qty_variance') }}:</span>
                                <strong class="{{ $totalVarianceQty >= 0 ? 'text-success' : 'text-danger' }}">{{ $totalVarianceQty > 0 ? "+{$totalVarianceQty}" : $totalVarianceQty }}</strong>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-secondary">{{ __('messages.net_financial_variance_value') }}:</span>
                                <strong class="{{ $totalVarianceValue >= 0 ? 'text-success' : 'text-danger' }}">{{ __('messages.EGP') }} {{ number_format($totalVarianceValue, 2) }}</strong>
                            </div>
                        </div>
                        <div class="alert alert-warning border-0 rounded-3 small mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            {{ __('messages.apply_adjustments_warning') }}
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4">{{ __('messages.confirm_and_apply_adjustments') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-layout>
