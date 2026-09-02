<x-layout>
    <div class="container-fluid py-4 px-lg-4">
        {{-- Header Section with Modern Glassmorphic Feel --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 p-4 rounded-4 shadow-sm bg-white border border-light-subtle">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 font-monospace fw-semibold">
                        <i class="bi bi-shield-check me-1"></i>{{ __('messages.authorized_access') ?? 'Authorized Access' }}
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 small">
                        {{ $slips->total() }} {{ __('messages.slips_recorded') ?? 'Slips' }}
                    </span>
                </div>
                <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-receipt-cutoff text-primary"></i>
                    {{ __('messages.inventory_slips_title') }}
                </h1>
                <p class="text-secondary small mb-0">
                    {{ __('messages.inventory_slips_desc') }}
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if(auth()->check() && (auth()->user()->can('view lit inventory') || auth()->user()->hasRole('Lit User') || auth()->user()->hasRole('super admin')))
                <a href="{{ route('lit.reconciliation') }}" class="btn btn-outline-primary rounded-pill px-3 py-2 shadow-xs d-inline-flex align-items-center gap-1 transition-all">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>{{ __('messages.reconciliation_and_return') }}</span>
                </a>
                @endif

                @if(auth()->check() && (auth()->user()->can('view lit ledger') || auth()->user()->hasRole('Lit User') || auth()->user()->hasRole('Store Manager') || auth()->user()->hasRole('rsc') || auth()->user()->hasRole('super admin')))
                <a href="{{ route('lit.ledger') }}" class="btn btn-primary rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center gap-1 transition-all">
                    <i class="bi bi-journal-text"></i>
                    <span>{{ __('messages.monthly_ledger') }}</span>
                </a>
                @endif
            </div>
        </div>

        {{-- Alerts --}}
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

        {{-- Summary KPI Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white border-start border-4 border-primary">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-medium d-block mb-1">{{ __('messages.total_slips') ?? 'Total Slips' }}</span>
                            <h3 class="fw-bold mb-0 text-dark">{{ $stats['total_slips'] ?? $slips->total() }}</h3>
                        </div>
                        <div class="avatar-box bg-primary-subtle text-primary rounded-4 p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-receipt fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white border-start border-4 border-info">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-medium d-block mb-1">{{ __('messages.slip_type_transfer') }}</span>
                            <h3 class="fw-bold mb-0 text-info">{{ $stats['transferred'] ?? 0 }}</h3>
                        </div>
                        <div class="avatar-box bg-info-subtle text-info rounded-4 p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-box-arrow-right fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white border-start border-4 border-warning">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-medium d-block mb-1">{{ __('messages.slip_type_return') }}</span>
                            <h3 class="fw-bold mb-0 text-warning">{{ $stats['returned'] ?? 0 }}</h3>
                        </div>
                        <div class="avatar-box bg-warning-subtle text-warning rounded-4 p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-box-arrow-in-left fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white border-start border-4 {{ ($stats['pending_acknowledgment'] ?? 0) > 0 ? 'border-danger' : 'border-success' }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-medium d-block mb-1">{{ __('messages.pending_acknowledgment') ?? 'Pending Acknowledgment' }}</span>
                            <h3 class="fw-bold mb-0 {{ ($stats['pending_acknowledgment'] ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $stats['pending_acknowledgment'] ?? 0 }}
                            </h3>
                        </div>
                        <div class="avatar-box {{ ($stats['pending_acknowledgment'] ?? 0) > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} rounded-4 p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Interactive Filter Bar --}}
        <div class="card p-3 p-md-4 shadow-sm border-0 mb-4 rounded-4 bg-white">
            <form action="{{ route('slips.index') }}" method="GET" class="row g-2.5 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary-subtle border-end-0 rounded-start-pill px-3">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="search" name="search" value="{{ request('search') }}" class="form-control border-secondary-subtle border-start-0 rounded-end-pill px-2" placeholder="{{ __('messages.search_slips_placeholder') }}">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <select name="type" class="form-select rounded-pill border-secondary-subtle">
                        <option value="">{{ __('messages.all_types') }}</option>
                        <option value="transfer_to_lit" {{ request('type') === 'transfer_to_lit' ? 'selected' : '' }}>{{ __('messages.slip_type_transfer') }}</option>
                        <option value="return_to_store" {{ request('type') === 'return_to_store' ? 'selected' : '' }}>{{ __('messages.slip_type_return') }}</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select rounded-pill border-secondary-subtle">
                        <option value="">{{ __('messages.all_statuses') }}</option>
                        <option value="transferred" {{ request('status') === 'transferred' ? 'selected' : '' }}>{{ __('messages.status_transferred') }}</option>
                        <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>{{ __('messages.status_received') }}</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('messages.status_completed') }}</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <input type="month" name="month" value="{{ request('month') }}" class="form-control rounded-pill border-secondary-subtle">
                </div>
                <div class="col-6 col-md-2 d-flex gap-1.5">
                    <button type="submit" class="btn btn-primary rounded-pill w-100 shadow-xs">
                        <i class="bi bi-funnel me-1"></i>{{ __('messages.Filter') }}
                    </button>
                    @if(request()->anyFilled(['search', 'type', 'status', 'month']))
                        <a href="{{ route('slips.index') }}" class="btn btn-outline-secondary rounded-pill px-2.5" title="{{ __('messages.clear_filters') ?? 'Clear' }}">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Slips Table Card --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light text-secondary border-bottom">
                        <tr>
                            <th class="ps-4 py-3">{{ __('messages.slip_number') }}</th>
                            <th class="py-3">{{ __('messages.Type') }}</th>
                            <th class="py-3">{{ __('messages.Date') }}</th>
                            <th class="py-3">{{ __('messages.issued_by') }}</th>
                            <th class="py-3">{{ __('messages.received_by') }}</th>
                            <th class="text-center py-3">{{ __('messages.total_items_count') }}</th>
                            <th class="text-end py-3">{{ __('messages.total_valuation') }}</th>
                            <th class="text-center py-3">{{ __('messages.Status') }}</th>
                            <th class="text-center pe-4 py-3">{{ __('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($slips as $slip)
                            <tr class="transition-all">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="badge-icon bg-light text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </div>
                                        <span class="fw-bold text-dark font-monospace">{{ $slip->slip_number }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($slip->type === 'transfer_to_lit')
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fw-medium">
                                            <i class="bi bi-box-arrow-right me-1"></i>{{ __('messages.slip_type_transfer') }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-dark rounded-pill px-3 py-1.5 fw-medium">
                                            <i class="bi bi-box-arrow-in-left me-1"></i>{{ __('messages.slip_type_return') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="small text-secondary fw-medium">{{ $slip->created_at->format('Y-m-d H:i') }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <i class="bi bi-person text-secondary"></i>
                                        <span class="fw-semibold text-dark">{{ $slip->issuer->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($slip->receiver)
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold text-success d-flex align-items-center gap-1">
                                                <i class="bi bi-check-circle-fill text-success" style="font-size: 0.85rem;"></i>
                                                {{ $slip->receiver->name }}
                                            </span>
                                            <span class="text-muted small" style="font-size: 0.75rem;">
                                                {{ $slip->received_at ? $slip->received_at->format('Y-m-d H:i') : '' }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary-subtle text-muted rounded-pill px-2.5 py-1 small fw-normal">
                                            <i class="bi bi-clock me-1"></i>{{ __('messages.not_yet_acknowledged') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border border-secondary-subtle px-3 py-1.5 rounded-pill font-monospace fw-bold">
                                        {{ $slip->total_items_count }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-dark font-monospace">
                                    EGP {{ number_format($slip->total_value, 2) }}
                                </td>
                                <td class="text-center">
                                    @if($slip->status === 'received' || $slip->status === 'completed')
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-check2-circle"></i>
                                            {{ __('messages.status_received') }}
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-arrow-repeat"></i>
                                            {{ __('messages.status_transferred') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        {{-- View Modal Trigger --}}
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" data-bs-toggle="modal" data-bs-target="#slipModal-{{ $slip->id }}" title="{{ __('messages.view_details') }}">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        {{-- PDF Download --}}
                                        <a href="{{ route('slips.pdf', $slip->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1" title="{{ __('messages.print_pdf') }}">
                                            <i class="bi bi-printer"></i>
                                        </a>

                                        {{-- Acknowledge Receipt Button (for Lit Committee on transfers) --}}
                                        @if($slip->type === 'transfer_to_lit' && $slip->status === 'transferred' && $canAcknowledge)
                                            <form action="{{ route('slips.acknowledge', $slip->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_acknowledge_receipt_prompt') }}')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1 shadow-xs fw-medium d-inline-flex align-items-center gap-1" title="{{ __('messages.acknowledge_receipt') }}">
                                                    <i class="bi bi-check-lg"></i>
                                                    <span>{{ __('messages.acknowledge_btn') }}</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- Modal for Slip Details --}}
                                    <div class="modal fade text-start" id="slipModal-{{ $slip->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                                                <div class="modal-header border-bottom bg-light px-4 py-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="badge bg-primary rounded-pill p-2">
                                                            <i class="bi bi-receipt fs-6 text-white"></i>
                                                        </div>
                                                        <div>
                                                            <h5 class="modal-title fw-bold mb-0">{{ $slip->slip_number }}</h5>
                                                            <span class="text-secondary small">{{ $slip->created_at->format('Y-m-d H:i') }}</span>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="row g-3 mb-4 bg-light rounded-4 p-3 border border-light-subtle">
                                                        <div class="col-6 col-md-3">
                                                            <div class="text-secondary small mb-1">{{ __('messages.Type') }}</div>
                                                            <div class="fw-bold">{{ $slip->type === 'transfer_to_lit' ? __('messages.slip_type_transfer') : __('messages.slip_type_return') }}</div>
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <div class="text-secondary small mb-1">{{ __('messages.Status') }}</div>
                                                            <div>
                                                                @if($slip->status === 'received' || $slip->status === 'completed')
                                                                    <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">{{ __('messages.status_received') }}</span>
                                                                @else
                                                                    <span class="badge bg-info-subtle text-info rounded-pill px-2.5 py-1">{{ __('messages.status_transferred') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <div class="text-secondary small mb-1">{{ __('messages.issued_by') }}</div>
                                                            <div class="fw-bold text-dark">{{ $slip->issuer->name ?? '-' }}</div>
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <div class="text-secondary small mb-1">{{ __('messages.received_by') }}</div>
                                                            <div class="fw-bold text-success">{{ $slip->receiver->name ?? __('messages.not_yet_acknowledged') }}</div>
                                                        </div>
                                                    </div>

                                                    @if($slip->notes)
                                                        <div class="alert alert-light border rounded-3 p-3 small mb-4">
                                                            <strong class="text-secondary">{{ __('messages.Notes') }}:</strong>
                                                            <span class="text-dark">{{ $slip->notes }}</span>
                                                        </div>
                                                    @endif

                                                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                                        <i class="bi bi-box-seam text-primary"></i>
                                                        {{ __('messages.items_in_slip') ?? 'Items in Slip' }}
                                                    </h6>

                                                    <div class="table-responsive rounded-3 border">
                                                        <table class="table table-sm align-middle mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="ps-3">#</th>
                                                                    <th>{{ __('messages.item_name') }}</th>
                                                                    <th class="text-center">{{ __('messages.Quantity') }}</th>
                                                                    <th class="text-end">{{ __('messages.unit_price') }}</th>
                                                                    <th class="text-end pe-3">{{ __('messages.Total') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($slip->items as $idx => $sItem)
                                                                    <tr>
                                                                        <td class="ps-3 text-secondary">{{ $idx + 1 }}</td>
                                                                        <td class="fw-medium text-dark">{{ $sItem->item->store_display_name ?? 'Deleted Item' }}</td>
                                                                        <td class="text-center font-monospace fw-bold">{{ $sItem->quantity }}</td>
                                                                        <td class="text-end font-monospace">EGP {{ number_format($sItem->unit_price, 2) }}</td>
                                                                        <td class="text-end pe-3 font-monospace fw-bold text-dark">EGP {{ number_format($sItem->total_price, 2) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            <tfoot class="table-light fw-bold">
                                                                <tr>
                                                                    <td colspan="2" class="ps-3 text-end">{{ __('messages.Total') }}:</td>
                                                                    <td class="text-center font-monospace">{{ $slip->total_items_count }}</td>
                                                                    <td></td>
                                                                    <td class="text-end pe-3 font-monospace text-success fs-6">EGP {{ number_format($slip->total_value, 2) }}</td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top bg-light px-4 py-3 d-flex justify-content-between">
                                                    <a href="{{ route('slips.pdf', $slip->id) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-printer"></i>
                                                        <span>{{ __('messages.print_pdf') }}</span>
                                                    </a>
                                                    <div class="d-flex gap-2">
                                                        @if($slip->type === 'transfer_to_lit' && $slip->status === 'transferred' && $canAcknowledge)
                                                            <form action="{{ route('slips.acknowledge', $slip->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_acknowledge_receipt_prompt') }}')">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success rounded-pill px-3 py-1.5 fw-medium d-inline-flex align-items-center gap-1">
                                                                    <i class="bi bi-check-lg"></i>
                                                                    <span>{{ __('messages.acknowledge_receipt') }}</span>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <button type="button" class="btn btn-secondary rounded-pill px-3 py-1.5" data-bs-dismiss="modal">{{ __('messages.Close') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-secondary py-5">
                                    <div class="py-4">
                                        <i class="bi bi-receipt-cutoff fs-1 d-block mb-3 text-muted"></i>
                                        <h5 class="fw-semibold text-dark mb-1">{{ __('messages.no_slips_found') }}</h5>
                                        <p class="text-secondary small mb-0">{{ __('messages.try_adjusting_filters') ?? 'Try adjusting search or filters.' }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($slips->hasPages())
                <div class="p-3 border-top d-flex justify-content-center">
                    {{ $slips->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layout>
