<x-layout>
    <div class="container-fluid py-4 px-lg-4">
        {{-- Header Section --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 p-4 rounded-4 shadow-sm bg-white border border-light-subtle">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <a href="{{ route('committee-literature.index', ['committee_id' => $committee->id]) }}" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left me-1"></i>{{ __('messages.back_to_list') ?? 'Back to Requests' }}
                    </a>
                </div>
                <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-cart-plus text-teal" style="color: #0d9488;"></i>
                    {{ __('messages.request_committee_literature') ?? 'New Literature Request' }}
                </h1>
                <p class="text-secondary small mb-0">
                    {{ __('messages.committee_literature_notice') ?? 'Submit an on-demand request for service materials. Received directly via slip from Literature Committee without invoice.' }}
                </p>
            </div>
            <div>
                <span class="badge bg-teal-subtle text-teal rounded-pill px-3 py-2 fs-6 font-monospace" style="background-color: #ccfbf1; color: #0f766e;">
                    <i class="bi bi-building me-1"></i>{{ $committee->ar_name }}
                </span>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4 d-flex align-items-center gap-2 p-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('committee-literature.store') }}" id="literatureRequestForm">
            @csrf
            <input type="hidden" name="committee_id" value="{{ $committee->id }}">

            <div class="row g-4">
                {{-- Items Table --}}
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
                        <div class="card-header bg-white border-bottom p-3.5 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-box-seam text-primary"></i>
                                {{ __('messages.available_literature_items') ?? 'Available Literature (Literature Committee Stock)' }}
                            </h5>
                            <div class="w-100 w-md-auto" style="max-width: 260px;">
                                <input type="text" id="itemSearch" class="form-control form-control-sm rounded-pill" placeholder="{{ __('messages.search_items') ?? 'Search items...' }}">
                            </div>
                        </div>
                        <div class="table-responsive" style="max-height: 520px;">
                            <table class="table table-hover align-middle mb-0" id="itemsTable">
                                <thead class="bg-light sticky-top">
                                    <tr>
                                        <th class="ps-3">{{ __('messages.item_name') }}</th>
                                        <th>{{ __('messages.category') }}</th>
                                        <th class="text-center" style="width: 140px;">{{ __('messages.lit_stock') ?? 'In Lit Stock' }}</th>
                                        <th class="text-center pe-3" style="width: 150px;">{{ __('messages.quantity_needed') ?? 'Request Qty' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($items as $item)
                                        <tr class="item-row" data-name="{{ strtolower($item->name . ' ' . $item->name_en) }}">
                                            <td class="ps-3">
                                                <div class="fw-semibold text-dark">{{ $item->name }}</div>
                                                @if($item->name_en)
                                                    <small class="text-muted">{{ $item->name_en }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 small">
                                                    {{ $item->category ?: 'General' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success-subtle text-success px-2.5 py-1.5 font-monospace fw-bold">
                                                    {{ $item->lit_quantity }}
                                                </span>
                                            </td>
                                            <td class="text-center pe-3">
                                                <div class="input-group input-group-sm justify-content-center">
                                                    <button type="button" class="btn btn-outline-secondary btn-step-down" onclick="adjustQty('{{ $item->id }}', -1)">-</button>
                                                    <input type="number" 
                                                           name="quantities[{{ $item->id }}]" 
                                                           id="qty_{{ $item->id }}" 
                                                           class="form-control text-center qty-input" 
                                                           style="max-width: 65px;" 
                                                           min="0" 
                                                           max="{{ $item->lit_quantity }}" 
                                                           value="0"
                                                           onchange="updateTotalCounter()">
                                                    <button type="button" class="btn btn-outline-secondary btn-step-up" onclick="adjustQty('{{ $item->id }}', 1, {{ $item->lit_quantity }})">+</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                {{ __('messages.no_items_available_in_lit') ?? 'No items currently in stock in Literature Committee inventory.' }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Sidebar / Summary Card --}}
                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 sticky-top" style="top: 20px;">
                        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-card-checklist text-teal" style="color: #0d9488;"></i>
                            {{ __('messages.request_summary') ?? 'Request Summary' }}
                        </h5>

                        <div class="mb-3 p-3 rounded-3 bg-light border border-light-subtle">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">{{ __('messages.service_committee') ?? 'Committee' }}:</span>
                                <span class="fw-bold text-dark">{{ $committee->ar_name }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">{{ __('messages.total_items_count') }}:</span>
                                <span class="fw-bold fs-5 text-teal" id="totalQtyCount" style="color: #0d9488;">0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">{{ __('messages.billing_status') ?? 'Invoice Status' }}:</span>
                                <span class="badge bg-secondary-subtle text-secondary">{{ __('messages.non_billable_slip') ?? 'No Invoice (Slip only)' }}</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">{{ __('messages.purpose_or_notes') ?? 'Purpose of Literature (Event / Outreach / Facility)' }}:</label>
                            <textarea name="notes" rows="3" class="form-control rounded-3" placeholder="{{ __('messages.purpose_placeholder') ?? 'Specify event, institution, or outreach details...' }}"></textarea>
                        </div>

                        <button type="submit" class="btn btn-teal text-white w-100 py-2.5 rounded-pill shadow-sm fw-bold d-flex align-items-center justify-content-center gap-2" style="background-color: #0d9488;" id="submitBtn" disabled>
                            <i class="bi bi-send-check"></i>
                            <span>{{ __('messages.submit_request_to_lit') ?? 'Submit Literature Request' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function adjustQty(itemId, delta, maxVal = 9999) {
            const input = document.getElementById('qty_' + itemId);
            if (!input) return;
            let current = parseInt(input.value) || 0;
            let next = current + delta;
            if (next < 0) next = 0;
            if (next > maxVal) next = maxVal;
            input.value = next;
            updateTotalCounter();
        }

        function updateTotalCounter() {
            let total = 0;
            document.querySelectorAll('.qty-input').forEach(inp => {
                total += parseInt(inp.value) || 0;
            });
            document.getElementById('totalQtyCount').innerText = total;
            const submitBtn = document.getElementById('submitBtn');
            if (total > 0) {
                submitBtn.removeAttribute('disabled');
            } else {
                submitBtn.setAttribute('disabled', 'disabled');
            }
        }

        // Real-time search filter
        document.getElementById('itemSearch')?.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            document.querySelectorAll('.item-row').forEach(row => {
                const name = row.getAttribute('data-name');
                if (!query || name.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</x-layout>
