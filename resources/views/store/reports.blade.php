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
                            <h3 class="mt-1 mb-0 font-bold text-warning-emphasis">EGP {{ number_format($totalValuation, 2) }}</h3>
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
                    <label class="form-label small fw-semibold">{{ __('messages.Topic') }}</label>
                    <select name="item_id" class="form-select rounded-3">
                        <option value="">{{ __('messages.all_items') }}</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
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
            <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>{{ __('messages.transaction_ledger') }}</h5>
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.id') }}</th>
                            <th>{{ __('messages.date_time') }}</th>
                            <th>{{ __('messages.item_name') }}</th>
                            <th>{{ __('messages.Category') }}</th>
                            <th>{{ __('messages.Type') }}</th>
                            <th class="text-center">{{ __('messages.Capacity') }}</th>
                            <th>{{ __('messages.operator') }}</th>
                            <th>{{ __('messages.Notes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $t)
                            <tr>
                                <td><span class="text-secondary font-monospace">#{{ $t->id }}</span></td>
                                <td>
                                    <div class="small fw-semibold text-dark">{{ $t->created_at->format('Y-m-d') }}</div>
                                    <div class="text-secondary small">{{ $t->created_at->format('H:i:s') }}</div>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $t->item->name ?? 'Deleted Item' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 small">
                                        {{ __('messages.cat_' . Str::snake(str_replace(' ', '_', $t->item->category ?? 'Others'))) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($t->type === 'receive')
                                        <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">
                                            <i class="bi bi-plus-circle-fill me-1"></i>{{ __('messages.receive') }}
                                        </span>
                                    @elseif ($t->type === 'transfer_to_lit')
                                        <span class="badge bg-primary-subtle text-primary px-2 py-1 rounded-pill">
                                            <i class="bi bi-arrow-right-circle-fill me-1"></i>{{ __('messages.transfer_to_lit') }}
                                        </span>
                                    @elseif ($t->type === 'return_from_lit')
                                        <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1 rounded-pill">
                                            <i class="bi bi-arrow-left-circle-fill me-1"></i>{{ __('messages.return_from_lit') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold">
                                    {{ $t->quantity }}
                                </td>
                                <td>
                                    <span class="text-secondary small"><i class="bi bi-person me-1"></i>{{ $t->user->name ?? 'System' }}</span>
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
</x-layout>
