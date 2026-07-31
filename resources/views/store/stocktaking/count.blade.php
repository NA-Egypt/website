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
                        {{ __('messages.physical_count_entry') }} &bull; Session #{{ $session->id }}
                    </h1>
                </div>
                <p class="text-secondary mb-0">
                    {{ __('messages.enter_physical_count_desc') }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('store.stocktaking.show', $session->id) }}" class="btn btn-outline-dark rounded-pill px-3 shadow-sm">
                    <i class="bi bi-eye me-1"></i>{{ __('messages.view_report') }}
                </a>
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

        {{-- Form --}}
        <form action="{{ route('store.stocktaking.update_count', $session->id) }}" method="POST" id="countForm">
            @csrf
            <div class="card border-0 shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-calculator text-primary"></i>
                        {{ __('messages.items_counting_list') }} ({{ $items->count() }} {{ __('messages.items') }})
                    </h5>
                    <div class="position-relative" style="min-width: 250px;">
                        <input type="search" id="countSearchInput" class="form-control form-control-sm rounded-pill px-3" placeholder="{{ __('messages.Search') }}...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0" id="countTable">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.item_name') }}</th>
                                <th>{{ __('messages.Category') }}</th>
                                <th class="text-center" style="width: 140px;">{{ __('messages.system_store_qty') }}</th>
                                <th class="text-center" style="width: 150px;">{{ __('messages.counted_store_qty') }}</th>
                                <th class="text-center" style="width: 140px;">{{ __('messages.system_lit_qty') }}</th>
                                <th class="text-center" style="width: 150px;">{{ __('messages.counted_lit_qty') }}</th>
                                <th class="text-center" style="width: 120px;">{{ __('messages.variance_status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr class="count-row">
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->inventoryItem->store_display_name ?? 'Item #' . $item->inventory_item_id }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
                                            {{ __('messages.cat_' . Str::snake(str_replace(' ', '_', $item->inventoryItem->category ?? 'Others'))) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-semibold">{{ $item->system_store_qty }}</span>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" min="0" name="counts[{{ $item->id }}][counted_store_qty]" 
                                               class="form-control form-control-sm text-center mx-auto rounded-3 counted-store-input" 
                                               value="{{ $item->counted_store_qty !== null ? $item->counted_store_qty : $item->system_store_qty }}" 
                                               data-system="{{ $item->system_store_qty }}" style="max-width: 100px;">
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-semibold">{{ $item->system_lit_qty }}</span>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" min="0" name="counts[{{ $item->id }}][counted_lit_qty]" 
                                               class="form-control form-control-sm text-center mx-auto rounded-3 counted-lit-input" 
                                               value="{{ $item->counted_lit_qty !== null ? $item->counted_lit_qty : $item->system_lit_qty }}" 
                                               data-system="{{ $item->system_lit_qty }}" style="max-width: 100px;">
                                    </td>
                                    <td class="text-center variance-badge-cell">
                                        @php
                                            $totalSystem = $item->system_store_qty + $item->system_lit_qty;
                                            $totalCounted = ($item->counted_store_qty ?? $item->system_store_qty) + ($item->counted_lit_qty ?? $item->system_lit_qty);
                                            $diff = $totalCounted - $totalSystem;
                                        @endphp
                                        @if ($diff == 0)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">
                                                <i class="bi bi-check-lg me-1"></i>Match
                                            </span>
                                        @elseif ($diff > 0)
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 rounded-pill fw-bold">
                                                +{{ $diff }} Surplus
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-bold">
                                                {{ $diff }} Shortage
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Action Bar --}}
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap gap-2">
                    <button type="submit" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-save me-1"></i>{{ __('messages.save_draft_counts') }}
                    </button>
                    <button type="submit" name="complete_session" value="1" class="btn btn-warning rounded-pill px-4 shadow-sm text-dark fw-bold" onclick="return confirm('{{ __('messages.confirm_finalize_stocktaking_msg') }}')">
                        <i class="bi bi-check2-circle me-1"></i>{{ __('messages.finalize_counts_and_generate_report') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('countSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const q = this.value.toLowerCase().trim();
                    const rows = document.querySelectorAll('#countTable tbody tr.count-row');
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(q) ? '' : 'none';
                    });
                });
            }

            // Real-time variance calculation
            document.querySelectorAll('.counted-store-input, .counted-lit-input').forEach(input => {
                input.addEventListener('input', function() {
                    const row = this.closest('tr');
                    const storeInput = row.querySelector('.counted-store-input');
                    const litInput = row.querySelector('.counted-lit-input');
                    const badgeCell = row.querySelector('.variance-badge-cell');

                    const sysStore = parseInt(storeInput.getAttribute('data-system') || 0, 10);
                    const sysLit = parseInt(litInput.getAttribute('data-system') || 0, 10);

                    const cntStore = parseInt(storeInput.value || 0, 10);
                    const cntLit = parseInt(litInput.value || 0, 10);

                    const diff = (cntStore + cntLit) - (sysStore + sysLit);

                    if (diff === 0) {
                        badgeCell.innerHTML = `<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold"><i class="bi bi-check-lg me-1"></i>Match</span>`;
                    } else if (diff > 0) {
                        badgeCell.innerHTML = `<span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 rounded-pill fw-bold">+${diff} Surplus</span>`;
                    } else {
                        badgeCell.innerHTML = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-bold">${diff} Shortage</span>`;
                    }
                });
            });
        });
    </script>
</x-layout>
