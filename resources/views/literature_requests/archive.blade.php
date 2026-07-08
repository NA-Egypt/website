<x-layout>
    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="mb-4">
            <h1 class="h2 text-gradient font-bold mb-0">
                {{ __('messages.literature_requests_archive') }}
            </h1>
            <p class="text-secondary mb-0">
                Browse literature requests by Year and Month.
            </p>
        </div>

        {{-- Accordion Tree --}}
        @if(count($archiveData) > 0)
            <div class="accordion" id="archiveAccordion">
                @foreach($archiveData as $year => $months)
                    <div class="accordion-item border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
                        <h2 class="accordion-header" id="heading{{ $year }}">
                            <button class="accordion-button fw-bold text-primary collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $year }}" aria-expanded="false" aria-controls="collapse{{ $year }}">
                                <i class="bi bi-calendar-check me-2"></i> {{ $year }}
                            </button>
                        </h2>
                        <div id="collapse{{ $year }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $year }}" data-bs-parent="#archiveAccordion">
                            <div class="accordion-body bg-light-subtle">
                                <div class="accordion" id="monthsAccordion{{ $year }}">
                                    @foreach($months as $monthNum => $data)
                                        <div class="accordion-item border-0 rounded-3 mb-2 overflow-hidden shadow-sm">
                                            <h3 class="accordion-header" id="heading{{ $year }}{{ $monthNum }}">
                                                <button class="accordion-button collapsed py-2 text-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $year }}{{ $monthNum }}" aria-expanded="false" aria-controls="collapse{{ $year }}{{ $monthNum }}">
                                                    <i class="bi bi-folder2 me-2"></i> {{ $data['name'] }}
                                                </button>
                                            </h3>
                                            <div id="collapse{{ $year }}{{ $monthNum }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $year }}{{ $monthNum }}" data-bs-parent="#monthsAccordion{{ $year }}">
                                                <div class="accordion-body">
                                                    <div class="table-responsive">
                                                        <table class="table align-middle">
                                                            <thead>
                                                                <tr>
                                                                    <th>Type</th>
                                                                    <th>Group / Service Body</th>
                                                                    <th class="text-center">Total Items</th>
                                                                    <th class="text-end">Total Price</th>
                                                                    <th class="text-center">Status</th>
                                                                    <th class="text-center">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($data['requests'] as $req)
                                                                    <tr>
                                                                        <td>
                                                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2">
                                                                                {{ strtoupper($req->type) }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="fw-bold">
                                                                            @if($req->type === 'group')
                                                                                {{ $req->group->{app()->getLocale() . '_name'} ?? $req->group->en_name ?? 'Unknown Group' }}
                                                                            @else
                                                                                {{ $req->serviceBody->{app()->getLocale() . '_name'} ?? $req->serviceBody->en_name ?? 'Unknown Service Body' }}
                                                                            @endif
                                                                        </td>
                                                                        <td class="text-center">{{ $req->total_items_count }}</td>
                                                                        <td class="text-end fw-bold text-primary">{{ $req->total_price }} {{ __('messages.EGP') }}</td>
                                                                        <td class="text-center">
                                                                            <span class="badge bg-success rounded-pill px-2">
                                                                                {{ strtoupper($req->status) }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <a href="{{ route('literature-requests.pdf', $req->id) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                                                <i class="bi bi-file-earmark-pdf me-1"></i>
                                                                                {{ __('messages.Download PDF') }}
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card p-5 border-0 shadow-sm text-center text-muted">
                <i class="bi bi-folder-x fs-1 mb-2"></i>
                <p class="mb-0">No archived literature requests found.</p>
            </div>
        @endif
    </div>
</x-layout>
