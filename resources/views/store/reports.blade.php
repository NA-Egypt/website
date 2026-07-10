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
            <div class="d-flex gap-2">
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
                <div class="card p-3 border-0 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-semibold">{{ __('messages.total_unique_items') }}</span>
                            <h3 class="mt-1 mb-0 font-bold text-dark">{{ $totalItems }}</h3>
                        </div>
                        <div class="fs-2 text-primary bg-primary-subtle rounded-3 p-2 px-3"><i class="bi bi-tags"></i></div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-semibold">{{ __('messages.total_store_stock') }}</span>
                            <h3 class="mt-1 mb-0 font-bold text-success">{{ $totalStoreStock }}</h3>
                        </div>
                        <div class="fs-2 text-success bg-success-subtle rounded-3 p-2 px-3"><i class="bi bi-box-seam"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-semibold">{{ __('messages.total_lit_stock') }}</span>
                            <h3 class="mt-1 mb-0 font-bold text-info">{{ $totalLitStock }}</h3>
                        </div>
                        <div class="fs-2 text-info bg-info-subtle rounded-3 p-2 px-3"><i class="bi bi-book"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-semibold">{{ __('messages.total_stock_value') }}</span>
                            <h3 class="mt-1 mb-0 font-bold text-warning-emphasis">{{ __('messages.EGP') }} {{ number_format($totalValuation, 2) }}</h3>
                        </div>
                        <div class="fs-2 text-warning bg-warning-subtle rounded-3 p-2 px-3"><i class="bi bi-currency-dollar"></i></div>
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
            <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-primary"></i>
                {{ __('messages.transaction_ledger') }}
            </h5>
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $t)
                            <tr class="ledger-row">
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
                                    @elseif ($t->type === 'transfer_to_lit')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-1 shadow-sm">
                                            <i class="bi bi-arrow-right-circle-fill"></i>{{ __('messages.transfer_to_lit') }}
                                        </span>
                                    @elseif ($t->type === 'return_from_lit')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-1 shadow-sm text-dark">
                                            <i class="bi bi-arrow-left-circle-fill"></i>{{ __('messages.return_from_lit') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-dark-subtle text-dark px-3 py-2 rounded-pill font-monospace fw-bold fs-6">
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-secondary py-5">
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

    <style>
        .fs-7 { font-size: 0.75rem; }
        .ledger-row {
            transition: all 0.2s ease-in-out;
        }
        .ledger-row:hover {
            background-color: rgba(59, 130, 246, 0.04) !important;
            transform: scale(1.002);
        }
        /* Custom styling to match Bootstrap 5 minimal theme for Select2 */
        .select2-container--bootstrap4 .select2-selection--single {
            border: 1px solid var(--glass-border) !important;
            border-radius: 8px !important;
            height: calc(1.5em + .75rem + 2px) !important;
            background: rgba(0, 0, 0, 0.02) !important;
        }
        .select2-container--bootstrap4 .select2-selection__rendered {
            color: var(--text-primary) !important;
            line-height: calc(1.5em + .75rem) !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery && $.fn.select2) {
                $('.select2').select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: "{{ __('messages.all_items') }}",
                    allowClear: true
                });
            }
        });
    </script>
</x-layout>
