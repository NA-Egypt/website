<x-layout>
    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h1 class="h2 text-gradient font-bold mb-0">
                    {{ __('messages.Literature Requests') }}
                </h1>
                <p class="text-secondary mb-0">
                    {{__('messages.Received Service Body Accumulated Invoices')}}
                </p>
            </div>
            <div>
                <a href="{{ route('literature-requests.archive') }}" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-archive me-1"></i>
                    {{ __('messages.literature_requests_archive') }}
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Invoices Table --}}
        <div class="card border-0 shadow-sm p-4">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.Service Committee') ?? 'Service Body' }}</th>
                            <th>{{ __('messages.Month/Year') ?? 'Month' }}</th>
                            <th class="text-center">{{ __('messages.total_unique_items') ?? 'Total Items' }}</th>
                            <th class="text-end">{{ __('messages.total_stock_value') ?? 'Total Price' }}</th>
                            <th class="text-center">{{ __('messages.status') ?? 'Status' }}</th>
                            <th class="text-center">{{ __('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            <tr>
                                <td class="fw-bold">
                                    {{ $req->serviceBody->{app()->getLocale() . '_name'} ?? $req->serviceBody->en_name }}
                                </td>
                                <td>
                                    {{ \App\Services\DateNumberHelper::translatedFormat($req->month, 'F Y') }}
                                </td>
                                <td class="text-center">{{ $req->total_items_count }}</td>
                                <td class="text-end fw-bold text-primary">{{ $req->total_price }} {{ __('messages.EGP') }}</td>
                                <td class="text-center">
                                    @php
                                        $badgeColor = 'bg-secondary';
                                        if ($req->status === 'sent_to_committee') $badgeColor = 'bg-primary';
                                        elseif ($req->status === 'returned_by_committee') $badgeColor = 'bg-success';
                                    @endphp
                                    <span class="badge {{ $badgeColor }} rounded-pill px-2 py-1">
                                        {{ __('messages.' . $req->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('literature-requests.committee.edit', $req->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                            <i class="bi bi-pencil-square me-1"></i>
                                            {{ __('messages.Edit') }}
                                        </a>
                                        <a href="{{ route('literature-requests.pdf', $req->id) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm">
                                            <i class="bi bi-file-earmark-pdf me-1"></i>
                                            {{ __('messages.Download PDF') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    {{__('messages.No service body requests received.')}}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout>
