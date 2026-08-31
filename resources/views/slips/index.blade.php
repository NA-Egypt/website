<x-layout>
    <div class="container-fluid py-4">
        {{-- Header Section --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h2 text-gradient font-bold mb-1">
                    <i class="bi bi-receipt me-2"></i>{{ __('messages.inventory_slips_title') }}
                </h1>
                <p class="text-secondary mb-0">
                    {{ __('messages.inventory_slips_desc') }}
                </p>
            </div>
            <div class="d-flex gap-2">
                @if(auth()->user()->can('view lit inventory') || auth()->user()->hasRole('Lit User') || auth()->user()->hasRole('super admin'))
                <a href="{{ route('lit.reconciliation') }}" class="btn btn-outline-primary rounded-pill shadow-sm px-3">
                    <i class="bi bi-arrow-left-right me-1"></i>{{ __('messages.reconciliation_and_return') }}
                </a>
                @endif
                <a href="{{ route('lit.ledger') }}" class="btn btn-primary rounded-pill shadow-sm px-3">
                    <i class="bi bi-journal-text me-1"></i>{{ __('messages.monthly_ledger') }}
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Filter Section --}}
        <div class="card p-3 shadow-sm border-0 mb-4 rounded-4">
            <form action="{{ route('slips.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control rounded-pill border-secondary-subtle px-3" placeholder="{{ __('messages.search_slips_placeholder') }}">
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
                <div class="col-6 col-md-2">
                    <button type="submit" class="btn btn-secondary rounded-pill w-100 shadow-sm">
                        <i class="bi bi-filter me-1"></i>{{ __('messages.Filter') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Slips Table Card --}}
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.slip_number') }}</th>
                            <th>{{ __('messages.Type') }}</th>
                            <th>{{ __('messages.Date') }}</th>
                            <th>{{ __('messages.issued_by') }}</th>
                            <th>{{ __('messages.received_by') }}</th>
                            <th class="text-center">{{ __('messages.total_items_count') }}</th>
                            <th class="text-end">{{ __('messages.total_valuation') }}</th>
                            <th class="text-center">{{ __('messages.Status') }}</th>
                            <th class="text-center">{{ __('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($slips as $slip)
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark font-monospace">{{ $slip->slip_number }}</span>
                                </td>
                                <td>
                                    @if($slip->type === 'transfer_to_lit')
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1">
                                            <i class="bi bi-box-arrow-right me-1"></i>{{ __('messages.slip_type_transfer') }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-dark rounded-pill px-3 py-1">
                                            <i class="bi bi-box-arrow-in-left me-1"></i>{{ __('messages.slip_type_return') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="small text-secondary">{{ $slip->created_at->format('Y-m-d H:i') }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $slip->issuer->name ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($slip->receiver)
                                        <span class="fw-semibold text-success">{{ $slip->receiver->name }}</span>
                                        <div class="text-muted small">{{ $slip->received_at ? $slip->received_at->format('Y-m-d H:i') : '' }}</div>
                                    @else
                                        <span class="text-muted small italic">{{ __('messages.not_yet_acknowledged') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill fs-6">
                                        {{ $slip->total_items_count }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    EGP {{ number_format($slip->total_value, 2) }}
                                </td>
                                <td class="text-center">
                                    @if($slip->status === 'received' || $slip->status === 'completed')
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">
                                            <i class="bi bi-check-circle me-1"></i>{{ __('messages.status_received') }}
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1">
                                            <i class="bi bi-clock-history me-1"></i>{{ __('messages.status_transferred') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        {{-- View Modal Trigger --}}
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#slipModal-{{ $slip->id }}" title="{{ __('messages.view_details') }}">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        {{-- PDF Download --}}
                                        <a href="{{ route('slips.pdf', $slip->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2" title="{{ __('messages.print_pdf') }}">
                                            <i class="bi bi-printer"></i>
                                        </a>

                                        {{-- Acknowledge Receipt Button (for Lit Committee on transfers) --}}
                                        @if($slip->type === 'transfer_to_lit' && $slip->status === 'transferred' && $canAcknowledge)
                                            <form action="{{ route('slips.acknowledge', $slip->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_acknowledge_receipt_prompt') }}')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-2" title="{{ __('messages.acknowledge_receipt') }}">
                                                    <i class="bi bi-check-lg"></i> {{ __('messages.acknowledge_btn') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- Modal for Slip Details --}}
                                    <div class="modal fade text-start" id="slipModal-{{ $slip->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0 shadow">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title font-bold">
                                                        <i class="bi bi-receipt me-2 text-primary"></i>{{ $slip->slip_number }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body pt-3">
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-6 col-md-3">
                                                            <div class="text-secondary small">{{ __('messages.Type') }}</div>
                                                            <div class="fw-bold">{{ $slip->type === 'transfer_to_lit' ? __('messages.slip_type_transfer') : __('messages.slip_type_return') }}</div>
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <div class="text-secondary small">{{ __('messages.Date') }}</div>
                                                            <div class="fw-bold">{{ $slip->created_at->format('Y-m-d H:i') }}</div>
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <div class="text-secondary small">{{ __('messages.issued_by') }}</div>
                                                            <div class="fw-bold">{{ $slip->issuer->name ?? '-' }}</div>
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <div class="text-secondary small">{{ __('messages.Status') }}</div>
                                                            <div class="fw-bold">{{ $slip->status === 'received' ? __('messages.status_received') : __('messages.status_transferred') }}</div>
                                                        </div>
                                                    </div>

                                                    @if($slip->notes)
                                                        <div class="alert alert-light border rounded-3 p-2 small mb-3">
                                                            <strong>{{ __('messages.Notes') }}:</strong> {{ $slip->notes }}
                                                        </div>
                                                    @endif

                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-sm align-middle">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>{{ __('messages.item_name') }}</th>
                                                                    <th class="text-center">{{ __('messages.Quantity') }}</th>
                                                                    <th class="text-end">{{ __('messages.unit_price') }}</th>
                                                                    <th class="text-end">{{ __('messages.Total') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($slip->items as $idx => $sItem)
                                                                    <tr>
                                                                        <td>{{ $idx + 1 }}</td>
                                                                        <td>{{ $sItem->item->store_display_name ?? 'Deleted Item' }}</td>
                                                                        <td class="text-center fw-bold">{{ $sItem->quantity }}</td>
                                                                        <td class="text-end">EGP {{ number_format($sItem->unit_price, 2) }}</td>
                                                                        <td class="text-end fw-bold">EGP {{ number_format($sItem->total_price, 2) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            <tfoot class="table-light fw-bold">
                                                                <tr>
                                                                    <td colspan="2" class="text-end">{{ __('messages.Total') }}:</td>
                                                                    <td class="text-center">{{ $slip->total_items_count }}</td>
                                                                    <td></td>
                                                                    <td class="text-end text-success">EGP {{ number_format($slip->total_value, 2) }}</td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <a href="{{ route('slips.pdf', $slip->id) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3">
                                                        <i class="bi bi-printer me-1"></i>{{ __('messages.print_pdf') }}
                                                    </a>
                                                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">{{ __('messages.Close') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-secondary py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    {{ __('messages.no_slips_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($slips->hasPages())
                <div class="mt-4">
                    {{ $slips->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layout>
