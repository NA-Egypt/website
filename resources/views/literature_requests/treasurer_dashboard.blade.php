<x-layout>
    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h1 class="h2 text-gradient font-bold mb-0">
                    {{ __('messages.Treasurer Dashboard') }}
                </h1>
                <p class="text-secondary mb-0">
                    {{ __('messages.Literature Request of :servicebody for :month', [
                        'servicebody' => $serviceBody->{app()->getLocale() . '_name'} ?? $serviceBody->en_name,
                        'month' => \App\Services\DateNumberHelper::translatedFormat($month, 'F Y')
                    ]) }}
                </p>
            </div>
            <div>
                <a href="{{ route('literature-requests.archive') }}" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-archive me-1"></i>
                    {{ __('messages.literature_requests_archive') }}
                </a>
            </div>
        </div>

        {{-- Super Admin Service Body Switcher --}}
        @if(auth()->user()->hasRole('super admin') && count($allServiceBodies) > 0)
            <div class="card mb-4 border-0 shadow-sm p-3">
                <form action="{{ route('literature-requests.treasurer') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-12 col-md-4">
                        <select name="service_body_id" class="form-select rounded-pill" onchange="this.form.submit()">
                            @foreach($allServiceBodies as $sb)
                                <option value="{{ $sb->id }}" {{ $serviceBodyId == $sb->id ? 'selected' : '' }}>
                                    {{ $sb->{app()->getLocale() . '_name'} ?? $sb->en_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        @endif

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            {{-- Group Requests List --}}
            <div class="col-12 col-xl-6">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <h4 class="fw-bold mb-3 text-secondary">
                        {{ __('messages.groups_invoices') }}
                    </h4>
                    <div class="table-responsive">
                        <table class="table align-middle table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('messages.Group') ?? 'Group' }}</th>
                                    <th class="text-center">{{ __('messages.quantity') }}</th>
                                    <th class="text-end">{{ __('messages.total') }}</th>
                                    <th class="text-center">{{ __('messages.status') ?? 'Status' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupRequests as $gReq)
                                    <tr>
                                        <td class="fw-bold">
                                            {{ $gReq->group->{app()->getLocale() . '_name'} ?? $gReq->group->en_name }}
                                        </td>
                                        <td class="text-center">{{ $gReq->total_items_count }}</td>
                                        <td class="text-end fw-bold text-primary">{{ $gReq->total_price }} {{ __('messages.EGP') }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-success rounded-pill px-2 py-1">
                                                {{ __('messages.' . $gReq->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="bi bi-folder2-open fs-3 d-block mb-2"></i>
                                            {{ __('messages.no_agendas_submitted_yet') ?? 'No submitted requests yet' }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Accumulated Request / Invoice --}}
            <div class="col-12 col-xl-6">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold mb-0 text-secondary">
                            {{ __('messages.accumulated_invoice') }}
                        </h4>
                        @if($accumulatedRequest)
                            <a href="{{ route('literature-requests.pdf', $accumulatedRequest->id) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm">
                                <i class="bi bi-file-earmark-pdf me-1"></i>
                                {{ __('messages.Download PDF') }}
                            </a>
                        @endif
                    </div>

                    @if($accumulatedRequest)
                        <div class="alert bg-light border rounded-4 mb-4">
                            <h5 class="fw-bold mb-2">
                                {{ __('messages.status') ?? 'Status' }}: 
                                @php
                                    $badgeColor = 'bg-secondary';
                                    if ($accumulatedRequest->status === 'sent_to_committee') $badgeColor = 'bg-primary';
                                    elseif ($accumulatedRequest->status === 'returned_by_committee') $badgeColor = 'bg-success';
                                @endphp
                                <span class="badge {{ $badgeColor }} rounded-pill px-2 py-1">
                                    {{ __('messages.' . $accumulatedRequest->status) }}
                                </span>
                            </h5>
                            <p class="mb-1 text-secondary">
                                {{ __('messages.total_unique_items') ?? 'Total Items' }}: <strong>{{ $accumulatedRequest->total_items_count }}</strong>
                            </p>
                            <p class="mb-0 text-secondary">
                                {{ __('messages.total_stock_value') ?? 'Total Price' }}: <strong>{{ $accumulatedRequest->total_price }} {{ __('messages.EGP') }}</strong>
                            </p>
                        </div>

                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.item_name') }}</th>
                                        <th class="text-center">{{ __('messages.quantity') }}</th>
                                        <th class="text-end">{{ __('messages.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($accumulatedRequest->items as $item)
                                        <tr>
                                            <td>{{ $item->item->name }}</td>
                                            <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                            <td class="text-end">{{ $item->total }} {{ __('messages.EGP') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($accumulatedRequest->status === 'draft' || $accumulatedRequest->status === 'returned_by_committee')
                            <div class="mt-4 text-end">
                                <form action="{{ route('literature-requests.approve-send', $accumulatedRequest->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm w-100 py-2">
                                        <i class="bi bi-check-all me-1"></i>
                                        {{ __('messages.approve_and_send_to_committee') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5 text-muted h-100 d-flex flex-column justify-content-center align-items-center">
                            <i class="bi bi-clock-history fs-1 mb-2"></i>
                            <p class="mb-0">
                                {{ now()->day < 19 ? 'Accumulated invoice will be automatically compiled on the 19th of the month.' : 'No requests submitted from groups to accumulate.' }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout>
