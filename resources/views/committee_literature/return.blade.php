<x-layout>
    <div class="container-fluid py-4 px-lg-4">
        {{-- Header Section --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 p-4 rounded-4 shadow-sm bg-white border border-light-subtle">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <a href="{{ route('committee-literature.show', $litRequest->id) }}" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left me-1"></i>{{ __('messages.back_to_request_details') ?? 'Back to Request #' . $litRequest->id }}
                    </a>
                </div>
                <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-return-left text-danger"></i>
                    {{ __('messages.return_committee_remains') ?? 'Return Remaining Literature' }}
                </h1>
                <p class="text-secondary small mb-0">
                    {{ __('messages.return_remains_desc') ?? 'Return unused literature back to the Literature Committee. Items will be restored to Literature Committee stock.' }}
                </p>
            </div>
            <div>
                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2 fs-6">
                    {{ $litRequest->serviceCommittee?->ar_name }}
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

        @if(empty($returnableItems))
            <div class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center">
                <i class="bi bi-check2-circle text-success fs-1 mb-2"></i>
                <h4 class="fw-bold text-dark">{{ __('messages.all_items_accounted') ?? 'No Returnable Items Remaining' }}</h4>
                <p class="text-muted">{{ __('messages.all_delivered_returned') ?? 'All literature issued under this request has either been returned or consumed.' }}</p>
                <div class="mt-3">
                    <a href="{{ route('committee-literature.show', $litRequest->id) }}" class="btn btn-outline-primary rounded-pill px-4">
                        {{ __('messages.back_to_request') ?? 'Back to Request' }}
                    </a>
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('committee-literature.process-return', $litRequest->id) }}">
                @csrf
                <div class="row g-4">
                    <div class="col-12 col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
                            <div class="card-header bg-white border-bottom p-3.5">
                                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <i class="bi bi-box-arrow-in-down-left text-danger"></i>
                                    {{ __('messages.select_quantities_to_return') ?? 'Select Quantities to Return' }}
                                </h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3">{{ __('messages.item_name') }}</th>
                                            <th class="text-center" style="width: 120px;">{{ __('messages.delivered') ?? 'Delivered' }}</th>
                                            <th class="text-center" style="width: 130px;">{{ __('messages.already_returned') ?? 'Returned So Far' }}</th>
                                            <th class="text-center" style="width: 130px;">{{ __('messages.remaining') ?? 'Remaining' }}</th>
                                            <th class="text-center pe-3" style="width: 150px;">{{ __('messages.qty_returning_now') ?? 'Return Now' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($returnableItems as $rItem)
                                            <tr>
                                                <td class="ps-3">
                                                    <div class="fw-semibold text-dark">{{ $rItem['item']->name }}</div>
                                                    @if($rItem['item']->name_en)
                                                        <small class="text-muted">{{ $rItem['item']->name_en }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center font-monospace">{{ $rItem['delivered'] }}</td>
                                                <td class="text-center font-monospace text-muted">{{ $rItem['already_returned'] }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning-subtle text-warning-emphasis font-monospace fw-bold fs-6">
                                                        {{ $rItem['max_returnable'] }}
                                                    </span>
                                                </td>
                                                <td class="text-center pe-3">
                                                    <input type="number" 
                                                           name="quantities[{{ $rItem['item']->id }}]" 
                                                           class="form-control form-control-sm text-center mx-auto return-input" 
                                                           style="max-width: 90px;" 
                                                           min="0" 
                                                           max="{{ $rItem['max_returnable'] }}" 
                                                           value="0"
                                                           onchange="updateReturnTotal()">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 sticky-top" style="top: 20px;">
                            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-check text-danger"></i>
                                {{ __('messages.return_summary') ?? 'Return Summary' }}
                            </h5>

                            <div class="p-3 rounded-3 bg-light border border-light-subtle mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">{{ __('messages.total_items_to_return') ?? 'Total Returning' }}:</span>
                                    <span class="fw-bold fs-5 text-danger" id="returnCounter">0</span>
                                </div>
                                <div class="small text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    {{ __('messages.stock_restored_to_lit') ?? 'These quantities will be added directly back to Literature Committee stock.' }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">{{ __('messages.return_notes') ?? 'Return Notes (Reason / Handover details)' }}:</label>
                                <textarea name="notes" rows="3" class="form-control rounded-3" placeholder="{{ __('messages.return_notes_placeholder') ?? 'e.g., Unused materials after regional workshop' }}"></textarea>
                            </div>

                            <button type="submit" class="btn btn-danger w-100 py-2.5 rounded-pill shadow-sm fw-bold d-flex align-items-center justify-content-center gap-2" id="submitReturnBtn" disabled>
                                <i class="bi bi-arrow-return-left"></i>
                                <span>{{ __('messages.confirm_return_generate_slip') ?? 'Confirm Return (Generate RC Slip)' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>

    <script>
        function updateReturnTotal() {
            let total = 0;
            document.querySelectorAll('.return-input').forEach(inp => {
                total += parseInt(inp.value) || 0;
            });
            document.getElementById('returnCounter').innerText = total;
            const btn = document.getElementById('submitReturnBtn');
            if (total > 0) {
                btn.removeAttribute('disabled');
            } else {
                btn.setAttribute('disabled', 'disabled');
            }
        }
    </script>
</x-layout>
