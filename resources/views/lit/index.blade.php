<x-layout>
    <div class="container-fluid py-4">
        {{-- Header Section --}}
        <div class="mb-4">
            <h1 class="h2 text-gradient font-bold mb-0">
                {{ __('messages.lit_inventory_title') }}
            </h1>
            <p class="text-secondary mb-0">
                {{ __('messages.lit_inventory_desc') }}
            </p>
        </div>

        {{-- Overview Stock Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card p-3 border-0 shadow-sm d-flex align-items-center justify-content-between flex-row">
                    <div>
                        <span class="text-secondary small fw-semibold">{{ __('messages.total_unique_items') }}</span>
                        <h3 class="mt-1 mb-0 font-bold text-dark">{{ $items->count() }}</h3>
                    </div>
                    <div class="fs-2 text-primary bg-primary-subtle rounded-3 p-2 px-3"><i class="bi bi-tags"></i></div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card p-3 border-0 shadow-sm d-flex align-items-center justify-content-between flex-row">
                    <div>
                        <span class="text-secondary small fw-semibold">{{ __('messages.total_lit_qty') }}</span>
                        <h3 class="mt-1 mb-0 font-bold text-info">{{ $items->sum('lit_quantity') }}</h3>
                    </div>
                    <div class="fs-2 text-info bg-info-subtle rounded-3 p-2 px-3"><i class="bi bi-book"></i></div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card p-3 border-0 shadow-sm d-flex align-items-center justify-content-between flex-row">
                    <div>
                        <span class="text-secondary small fw-semibold">{{ __('messages.total_estimated_value') }}</span>
                        <h3 class="mt-1 mb-0 font-bold text-success">
                            EGP {{ number_format($items->sum(fn($i) => $i->lit_quantity * $i->selling_price), 2) }}
                        </h3>
                    </div>
                    <div class="fs-2 text-success bg-success-subtle rounded-3 p-2 px-3"><i class="bi bi-currency-dollar"></i></div>
                </div>
            </div>
        </div>

        {{-- Search & Category filter --}}
        <div class="card p-3 shadow-sm border-0 mb-4 justify-content-center">
            <form action="{{ route('lit.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-5 position-relative">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control rounded-pill bg-transparent border-secondary-subtle px-4" placeholder="{{ __('messages.search_literature') }}">
                </div>
                <div class="col-12 col-md-4">
                    <select name="category" class="form-select rounded-pill border-secondary-subtle px-3">
                        <option value="">{{ __('messages.Category') }} ({{ __('messages.all') }})</option>
                        @foreach(\App\Models\InventoryItem::CATEGORIES as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                {{ __('messages.cat_' . Str::snake(str_replace(' ', '_', $cat))) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-secondary rounded-pill w-100 shadow-sm">
                        <i class="bi bi-search me-1"></i>
                        {{ __('messages.Search') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Inventory Table --}}
        <div class="card border-0 shadow-sm p-4">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.literature_title') }}</th>
                            <th>{{ __('messages.Category') }}</th>
                            <th>{{ __('messages.Description') }}</th>
                            <th class="text-end">{{ __('messages.selling_price') }}</th>
                            <th class="text-center">{{ __('messages.available_stock_lit') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->name }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
                                        {{ __('messages.cat_' . Str::snake(str_replace(' ', '_', $item->category))) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-secondary small">{{ Str::limit($item->description, 100) ?: '-' }}</span>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    EGP {{ number_format($item->selling_price, 2) }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill fs-6">
                                        {{ $item->lit_quantity }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    {{ __('messages.no_literature_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout>
