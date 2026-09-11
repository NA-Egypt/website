<x-layout>
    <div class="container-fluid py-4 px-lg-4">
        {{-- Header Section --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 p-4 rounded-4 shadow-sm bg-white border border-light-subtle">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-teal-subtle text-teal rounded-pill px-3 py-1 font-monospace fw-semibold" style="background-color: #ccfbf1; color: #0f766e;">
                        <i class="bi bi-diagram-3 me-1"></i>{{ $committee ? $committee->ar_name : __('messages.committees') }}
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 small">
                        {{ $requests->total() }} {{ __('messages.requests_count') ?? 'Requests' }}
                    </span>
                </div>
                <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-journal-bookmark text-teal" style="color: #0d9488;"></i>
                    {{ __('messages.committee_literature_title') ?? 'Committees Literature Requests' }}
                </h1>
                <p class="text-secondary small mb-0">
                    {{ __('messages.committee_literature_desc') ?? 'On-demand literature requests and quantity slips issued directly by the Literature Committee (No invoices).' }}
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if($committee)
                <a href="{{ route('committee-literature.create', ['committee_id' => $committee->id]) }}" class="btn btn-teal text-white rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center gap-1.5" style="background-color: #0d9488;">
                    <i class="bi bi-plus-circle"></i>
                    <span>{{ __('messages.new_literature_request') ?? 'New Literature Request' }}</span>
                </a>
                @endif

                @if($isLitUser)
                <a href="{{ route('literature-requests.committee') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 shadow-xs d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i>
                    <span>{{ __('messages.back_to_lit_dashboard') ?? 'Literature Dashboard' }}</span>
                </a>
                @endif
            </div>
        </div>

        {{-- Session Flash Alerts --}}
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

        {{-- Filter selector for Admins / Lit Users --}}
        @if($isLitUser && $allCommittees->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('committee-literature.index') }}" class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <label class="form-label small text-muted mb-1">{{ __('messages.filter_by_committee') ?? 'Filter by Service Committee' }}:</label>
                        <select name="committee_id" class="form-select rounded-3" onchange="this.form.submit()">
                            <option value="">-- {{ __('messages.all_committees') ?? 'All Committees' }} --</option>
                            @foreach($allCommittees as $comm)
                                <option value="{{ $comm->id }}" {{ ($committee && $committee->id == $comm->id) ? 'selected' : '' }}>
                                    {{ $comm->ar_name }} ({{ $comm->en_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Requests Table --}}
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="ps-4" style="width: 80px;">#</th>
                            <th>{{ __('messages.service_committee') ?? 'Committee' }}</th>
                            <th>{{ __('messages.Date') }}</th>
                            <th class="text-center">{{ __('messages.total_items_count') }}</th>
                            <th>{{ __('messages.delivery_slip') ?? 'Delivery Slip' }}</th>
                            <th>{{ __('messages.Status') }}</th>
                            <th class="text-end pe-4">{{ __('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            @php
                                $dSlip = $req->deliverySlip();
                                $rSlips = $req->returnSlips()->get();
                            @endphp
                            <tr>
                                <td class="ps-4 fw-bold text-muted">#{{ $req->id }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $req->serviceCommittee?->ar_name }}</div>
                                    <small class="text-muted">{{ $req->serviceCommittee?->en_name }}</small>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $req->created_at->format('Y-m-d') }}</div>
                                    <small class="text-muted">{{ $req->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 fs-6 fw-bold">
                                        {{ $req->total_items_count }}
                                    </span>
                                </td>
                                <td>
                                    @if($dSlip)
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <span class="badge bg-teal-subtle text-teal px-2 py-1 font-monospace" style="background-color: #ccfbf1; color: #0f766e;">
                                                <i class="bi bi-file-earmark-text me-1"></i>{{ $dSlip->slip_number }}
                                            </span>
                                            <a href="{{ route('committee-literature.slip-pdf', $dSlip->id) }}" class="btn btn-sm btn-outline-secondary p-1 rounded" title="Download PDF Slip">
                                                <i class="bi bi-file-pdf text-danger"></i>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted small italic">{{ __('messages.awaiting_fulfillment') ?? 'Pending Fulfillment' }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($req->status === 'submitted')
                                        <span class="badge bg-warning-subtle text-warning-emphasis px-2.5 py-1.5 rounded-pill">
                                            <i class="bi bi-hourglass-split me-1"></i>{{ __('messages.submitted') ?? 'Submitted' }}
                                        </span>
                                    @elseif($req->status === 'dispatched')
                                        <span class="badge bg-info-subtle text-info-emphasis px-2.5 py-1.5 rounded-pill">
                                            <i class="bi bi-truck me-1"></i>{{ __('messages.dispatched') ?? 'Dispatched' }}
                                        </span>
                                    @elseif($req->status === 'received')
                                        <span class="badge bg-success-subtle text-success-emphasis px-2.5 py-1.5 rounded-pill">
                                            <i class="bi bi-check-all me-1"></i>{{ __('messages.received') ?? 'Received' }}
                                        </span>
                                    @elseif($req->status === 'completed')
                                        <span class="badge bg-primary-subtle text-primary-emphasis px-2.5 py-1.5 rounded-pill">
                                            <i class="bi bi-check-circle-fill me-1"></i>{{ __('messages.completed') ?? 'Completed' }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary px-2 py-1 rounded-pill">{{ $req->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1.5">
                                        <a href="{{ route('committee-literature.show', $req->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i>{{ __('messages.view_details') ?? 'View' }}
                                        </a>

                                        @if($dSlip && in_array($req->status, ['dispatched', 'received']))
                                        <a href="{{ route('committee-literature.return', $req->id) }}" class="btn btn-sm btn-outline-danger rounded-pill px-2.5" title="Return Remains">
                                            <i class="bi bi-arrow-return-left me-1"></i>{{ __('messages.return_remains') ?? 'Return Remains' }}
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    <div class="fw-semibold">{{ __('messages.no_committee_requests') ?? 'No literature requests found for this committee.' }}</div>
                                    <small class="text-muted">{{ __('messages.create_request_to_start') ?? 'Click "New Literature Request" above to request materials.' }}</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($requests->hasPages())
                <div class="card-footer bg-white border-top p-3">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layout>
