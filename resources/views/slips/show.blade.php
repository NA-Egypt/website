<x-layout>
    <div class="container-fluid py-4 px-lg-5">
        {{-- Top Navigation & Actions Bar --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
            <div>
                <a href="{{ route('slips.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 shadow-xs mb-2 d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i>
                    <span>{{ __('messages.back_to_slips') ?? 'Back to Slips' }}</span>
                </a>
                <h1 class="h3 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-receipt text-primary"></i>
                    {{ __('messages.slip_number') }}: <span class="font-monospace text-primary">{{ $slip->slip_number }}</span>
                </h1>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('slips.pdf', $slip->id) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3 py-2 shadow-xs d-inline-flex align-items-center gap-1.5">
                    <i class="bi bi-printer"></i>
                    <span>{{ __('messages.print_pdf') }}</span>
                </a>

                @if($slip->type === 'transfer_to_lit' && $slip->status === 'transferred' && $canAcknowledge)
                    <form action="{{ route('slips.acknowledge', $slip->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_acknowledge_receipt_prompt') }}')">
                        @csrf
                        <button type="submit" class="btn btn-success rounded-pill px-4 py-2 shadow-sm fw-medium d-inline-flex align-items-center gap-1.5">
                            <i class="bi bi-check-lg fs-5"></i>
                            <span>{{ __('messages.acknowledge_receipt') }}</span>
                        </button>
                    </form>
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

        {{-- Main Slip Document Card --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
            {{-- Document Header --}}
            <div class="p-4 p-md-5 border-bottom bg-light bg-opacity-50">
                <div class="row g-4 align-items-center">
                    <div class="col-12 col-md-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="badge bg-primary rounded-4 p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                <i class="bi bi-box-seam fs-3 text-white"></i>
                            </div>
                            <div>
                                <span class="badge {{ $slip->type === 'transfer_to_lit' ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-dark' }} rounded-pill px-3 py-1.5 fw-semibold mb-1">
                                    {{ $slip->type === 'transfer_to_lit' ? __('messages.slip_type_transfer') : __('messages.slip_type_return') }}
                                </span>
                                <h2 class="h4 fw-bold text-dark font-monospace mb-0">{{ $slip->slip_number }}</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 text-md-end">
                        <div class="d-inline-flex flex-column align-items-md-end">
                            <span class="text-secondary small">{{ __('messages.Status') }}</span>
                            @if($slip->status === 'received' || $slip->status === 'completed')
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1.5 fw-semibold fs-6 mt-1">
                                    <i class="bi bi-check-circle-fill me-1"></i>{{ __('messages.status_received') }}
                                </span>
                            @else
                                <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1.5 fw-semibold fs-6 mt-1">
                                    <i class="bi bi-clock-history me-1"></i>{{ __('messages.status_transferred') }}
                                </span>
                            @endif
                            <span class="text-secondary small mt-1">{{ $slip->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Metadata Cards Row --}}
            <div class="p-4 p-md-5 border-bottom">
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="p-3 rounded-4 bg-light border border-light-subtle h-100">
                            <span class="text-secondary small d-block mb-1">{{ __('messages.issued_by') }}</span>
                            <div class="fw-bold text-dark d-flex align-items-center gap-1.5">
                                <i class="bi bi-person-circle text-primary"></i>
                                <span>{{ $slip->issuer->name ?? '-' }}</span>
                            </div>
                            <span class="text-muted small" style="font-size: 0.75rem;">{{ $slip->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="p-3 rounded-4 bg-light border border-light-subtle h-100">
                            <span class="text-secondary small d-block mb-1">{{ __('messages.received_by') }}</span>
                            @if($slip->receiver)
                                <div class="fw-bold text-success d-flex align-items-center gap-1.5">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span>{{ $slip->receiver->name }}</span>
                                </div>
                                <span class="text-muted small" style="font-size: 0.75rem;">{{ $slip->received_at ? $slip->received_at->format('Y-m-d H:i') : '' }}</span>
                            @else
                                <div class="text-muted small fw-medium">
                                    <i class="bi bi-hourglass text-warning me-1"></i>
                                    {{ __('messages.not_yet_acknowledged') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="p-3 rounded-4 bg-light border border-light-subtle h-100">
                            <span class="text-secondary small d-block mb-1">{{ __('messages.total_items_count') }}</span>
                            <div class="fw-bold text-dark font-monospace fs-5">
                                {{ $slip->total_items_count }} {{ __('messages.units') ?? 'units' }}
                            </div>
                            <span class="text-muted small" style="font-size: 0.75rem;">{{ $slip->items->count() }} {{ __('messages.line_items') ?? 'distinct items' }}</span>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="p-3 rounded-4 bg-light border border-light-subtle h-100">
                            <span class="text-secondary small d-block mb-1">{{ __('messages.total_valuation') }}</span>
                            <div class="fw-bold text-success font-monospace fs-5">
                                EGP {{ number_format($slip->total_value, 2) }}
                            </div>
                        </div>
                    </div>
                </div>

                @if($slip->notes)
                    <div class="mt-4 p-3 rounded-4 bg-light border border-light-subtle">
                        <strong class="text-secondary small d-block mb-1">{{ __('messages.Notes') }}:</strong>
                        <p class="text-dark mb-0 small">{{ $slip->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Items Table --}}
            <div class="p-4 p-md-5">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2 text-dark">
                    <i class="bi bi-list-columns-reverse text-primary"></i>
                    {{ __('messages.items_in_slip') ?? 'Itemized Slip Breakdown' }}
                </h5>

                <div class="table-responsive rounded-4 border overflow-hidden">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">#</th>
                                <th class="py-3">{{ __('messages.item_name') }}</th>
                                <th class="text-center py-3">{{ __('messages.Quantity') }}</th>
                                <th class="text-end py-3">{{ __('messages.unit_price') }}</th>
                                <th class="text-end pe-4 py-3">{{ __('messages.Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($slip->items as $idx => $sItem)
                                <tr>
                                    <td class="ps-4 text-secondary">{{ $idx + 1 }}</td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $sItem->item->store_display_name ?? 'Deleted Item' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill font-monospace fw-bold fs-6">
                                            {{ $sItem->quantity }}
                                        </span>
                                    </td>
                                    <td class="text-end font-monospace text-secondary">
                                        EGP {{ number_format($sItem->unit_price, 2) }}
                                    </td>
                                    <td class="text-end pe-4 font-monospace fw-bold text-dark fs-6">
                                        EGP {{ number_format($sItem->total_price, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="2" class="ps-4 text-end">{{ __('messages.Total') }}:</td>
                                <td class="text-center font-monospace fs-6">{{ $slip->total_items_count }}</td>
                                <td></td>
                                <td class="text-end pe-4 font-monospace text-success fs-5">
                                    EGP {{ number_format($slip->total_value, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layout>
