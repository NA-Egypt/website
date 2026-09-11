<x-layout>
    <div class="container-fluid py-4 px-lg-4">
        {{-- Header Section --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 p-4 rounded-4 shadow-sm bg-white border border-light-subtle">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <a href="{{ route('committee-literature.index', ['committee_id' => $litRequest->service_committee_id]) }}" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left me-1"></i>{{ __('messages.back_to_requests') ?? 'Back to Requests' }}
                    </a>
                </div>
                <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-journal-check text-teal" style="color: #0d9488;"></i>
                    {{ __('messages.request_details') ?? 'Literature Request' }} #{{ $litRequest->id }}
                </h1>
                <p class="text-secondary small mb-0">
                    {{ $litRequest->serviceCommittee?->ar_name }} | {{ $litRequest->created_at->format('Y-m-d H:i') }}
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                @if($deliverySlip)
                    <a href="{{ route('committee-literature.slip-pdf', $deliverySlip->id) }}" class="btn btn-outline-primary rounded-pill px-3 py-2 shadow-xs d-inline-flex align-items-center gap-1.5" target="_blank">
                        <i class="bi bi-file-pdf"></i>
                        <span>{{ __('messages.delivery_slip_pdf') ?? 'Delivery Slip PDF' }}</span>
                    </a>
                @endif

                @if($deliverySlip && in_array($litRequest->status, ['dispatched', 'received']))
                    <a href="{{ route('committee-literature.return', $litRequest->id) }}" class="btn btn-danger rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center gap-1.5">
                        <i class="bi bi-arrow-return-left"></i>
                        <span>{{ __('messages.return_remains') ?? 'Return Remains' }}</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4 d-flex align-items-center gap-2 p-3" role="alert">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4 d-flex align-items-center gap-2 p-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Status and Info Row --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <span class="text-muted small mb-1">{{ __('messages.request_status') ?? 'Status' }}</span>
                    <div class="d-flex align-items-center gap-2">
                        @if($litRequest->status === 'submitted')
                            <span class="badge bg-warning-subtle text-warning-emphasis fs-6 px-3 py-1.5 rounded-pill">
                                <i class="bi bi-hourglass-split me-1"></i>{{ __('messages.submitted') ?? 'Awaiting Fulfillment' }}
                            </span>
                        @elseif($litRequest->status === 'dispatched')
                            <span class="badge bg-info-subtle text-info-emphasis fs-6 px-3 py-1.5 rounded-pill">
                                <i class="bi bi-truck me-1"></i>{{ __('messages.dispatched') ?? 'Dispatched with Slip' }}
                            </span>
                        @elseif($litRequest->status === 'received')
                            <span class="badge bg-success-subtle text-success-emphasis fs-6 px-3 py-1.5 rounded-pill">
                                <i class="bi bi-check-all me-1"></i>{{ __('messages.received') ?? 'Received by Committee' }}
                            </span>
                        @elseif($litRequest->status === 'completed')
                            <span class="badge bg-primary-subtle text-primary-emphasis fs-6 px-3 py-1.5 rounded-pill">
                                <i class="bi bi-check-circle-fill me-1"></i>{{ __('messages.completed') ?? 'Completed' }}
                            </span>
                        @else
                            <span class="badge bg-secondary fs-6 px-3 py-1.5 rounded-pill">{{ $litRequest->status }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <span class="text-muted small mb-1">{{ __('messages.service_committee') ?? 'Service Committee' }}</span>
                    <div class="fw-bold text-dark fs-6">{{ $litRequest->serviceCommittee?->ar_name }}</div>
                    <small class="text-muted">{{ $litRequest->serviceCommittee?->en_name }}</small>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <span class="text-muted small mb-1">{{ __('messages.billing_notice') ?? 'Financial Classification' }}</span>
                    <div class="fw-bold text-dark fs-6">
                        <i class="bi bi-tag text-teal me-1" style="color: #0d9488;"></i>
                        {{ __('messages.non_billable_slip') ?? 'Non-Billable (Slip without Invoice)' }}
                    </div>
                    <small class="text-muted">{{ __('messages.direct_from_lit_comm') ?? 'Direct Literature Committee distribution' }}</small>
                </div>
            </div>
        </div>

        {{-- Pending Acknowledgment Alert --}}
        @if($canAcknowledge)
            <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4 p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-box-seam fs-3 text-info"></i>
                    <div>
                        <div class="fw-bold">{{ __('messages.slip_awaiting_ack') ?? 'Literature has been dispatched with slip #' . $deliverySlip->slip_number }}</div>
                        <small>{{ __('messages.please_confirm_receipt') ?? 'Please confirm that your committee has received the physical items.' }}</small>
                    </div>
                </div>
                <form method="POST" action="{{ route('committee-literature.acknowledge', $deliverySlip->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-info text-white rounded-pill px-4 py-2 shadow-sm fw-bold">
                        <i class="bi bi-check-lg me-1"></i>{{ __('messages.acknowledge_receipt') ?? 'Acknowledge Receipt' }}
                    </button>
                </form>
            </div>
        @endif

        {{-- Literature Committee Fulfillment Form (Only when request is submitted) --}}
        @if($canManageFulfillment && $litRequest->status === 'submitted')
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden border-start border-4 border-warning">
                <div class="card-header bg-warning-subtle p-3.5 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-clipboard2-check text-warning-emphasis"></i>
                        {{ __('messages.fulfill_committee_request') ?? 'Literature Committee Fulfillment (Issue Slip)' }}
                    </h5>
                    <span class="badge bg-warning text-dark">{{ __('messages.action_required') ?? 'Action Required' }}</span>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-3">
                        {{ __('messages.fulfillment_instructions') ?? 'Review requested quantities, adjust if stock is limited, and issue the delivery slip. Items will be deducted directly from Literature Committee stock (lit_quantity).' }}
                    </p>

                    <form method="POST" action="{{ route('committee-literature.issue-slip', $litRequest->id) }}">
                        @csrf
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>{{ __('messages.item_name') }}</th>
                                        <th class="text-center" style="width: 140px;">{{ __('messages.requested_qty') ?? 'Requested' }}</th>
                                        <th class="text-center" style="width: 140px;">{{ __('messages.lit_stock') ?? 'Available Stock' }}</th>
                                        <th class="text-center" style="width: 160px;">{{ __('messages.issue_qty') ?? 'Quantity to Issue' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($litRequest->items as $reqItem)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $reqItem->item->name }}</div>
                                                @if($reqItem->item->name_en)
                                                    <small class="text-muted">{{ $reqItem->item->name_en }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center fw-bold">{{ $reqItem->quantity }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary-subtle text-secondary font-monospace">
                                                    {{ $reqItem->item->lit_quantity }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <input type="number" 
                                                       name="quantities[{{ $reqItem->inventory_item_id }}]" 
                                                       class="form-control text-center mx-auto" 
                                                       style="max-width: 90px;" 
                                                       min="0" 
                                                       max="{{ $reqItem->item->lit_quantity }}" 
                                                       value="{{ min($reqItem->quantity, $reqItem->item->lit_quantity) }}" 
                                                       required>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted">{{ __('messages.delivery_notes') ?? 'Delivery Notes / Handover Details' }}:</label>
                            <input type="text" name="notes" class="form-control rounded-3" placeholder="{{ __('messages.delivery_notes_placeholder') ?? 'e.g., Handed over to committee chair' }}">
                        </div>

                        <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5">
                            <i class="bi bi-file-earmark-plus"></i>
                            <span>{{ __('messages.issue_delivery_slip_btn') ?? 'Issue Delivery Slip (IC Slip)' }}</span>
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Items Status Breakdown Table --}}
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom p-3.5">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-list-check text-primary"></i>
                    {{ __('messages.items_breakdown') ?? 'Literature Items Breakdown' }}
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>{{ __('messages.item_name') }}</th>
                            <th class="text-center" style="width: 130px;">{{ __('messages.requested_qty') ?? 'Requested' }}</th>
                            <th class="text-center" style="width: 130px;">{{ __('messages.delivered_qty') ?? 'Delivered' }}</th>
                            <th class="text-center" style="width: 130px;">{{ __('messages.returned_qty') ?? 'Returned' }}</th>
                            <th class="text-center pe-4" style="width: 130px;">{{ __('messages.consumed_qty') ?? 'Net Consumed' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summaryItems as $index => $row)
                            <tr>
                                <td class="ps-4 text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $row['item']->name }}</div>
                                    @if($row['item']->name_en)
                                        <small class="text-muted">{{ $row['item']->name_en }}</small>
                                    @endif
                                </td>
                                <td class="text-center fw-medium">{{ $row['requested'] }}</td>
                                <td class="text-center">
                                    <span class="badge bg-teal-subtle text-teal px-2.5 py-1.5 font-monospace fw-bold" style="background-color: #ccfbf1; color: #0f766e;">
                                        {{ $row['delivered'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger-subtle text-danger px-2.5 py-1.5 font-monospace fw-bold">
                                        {{ $row['returned'] }}
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <span class="badge bg-primary-subtle text-primary px-2.5 py-1.5 font-monospace fw-bold">
                                        {{ $row['consumed'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Slips History Section --}}
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom p-3.5 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-receipt text-teal" style="color: #0d9488;"></i>
                    {{ __('messages.associated_slips') ?? 'Associated Inventory Slips' }}
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">{{ __('messages.slip_number') }}</th>
                            <th>{{ __('messages.Type') }}</th>
                            <th>{{ __('messages.Date') }}</th>
                            <th class="text-center">{{ __('messages.total_items_count') }}</th>
                            <th>{{ __('messages.Status') }}</th>
                            <th class="text-end pe-4">{{ __('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($litRequest->slips as $slip)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-dark">{{ $slip->slip_number }}</td>
                                <td>
                                    @if($slip->type === 'issue_to_committee')
                                        <span class="badge bg-teal-subtle text-teal px-2.5 py-1" style="background-color: #ccfbf1; color: #0f766e;">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('messages.issue_to_committee') ?? 'Delivery Slip' }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-2.5 py-1">
                                            <i class="bi bi-box-arrow-in-down-left me-1"></i>{{ __('messages.return_from_committee') ?? 'Return Slip' }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $slip->created_at->format('Y-m-d H:i') }}</td>
                                <td class="text-center font-monospace fw-bold">{{ $slip->total_items_count }}</td>
                                <td>
                                    @if($slip->status === 'transferred')
                                        <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2.5 py-1">
                                            {{ __('messages.dispatched') ?? 'Dispatched' }}
                                        </span>
                                    @elseif($slip->status === 'received')
                                        <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-2.5 py-1">
                                            {{ __('messages.received') ?? 'Received' }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-2.5 py-1">{{ $slip->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('committee-literature.slip-pdf', $slip->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" target="_blank">
                                        <i class="bi bi-file-pdf text-danger me-1"></i>{{ __('messages.pdf_slip') ?? 'PDF Slip' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    {{ __('messages.no_slips_yet') ?? 'No slips generated yet.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout>
