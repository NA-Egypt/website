<x-layout>
    <div class="container-fluid py-4">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h1 class="h2 text-gradient font-bold mb-0">
                    <i class="bi bi-clipboard-check me-2"></i>{{ __('messages.stocktaking_management') }}
                </h1>
                <p class="text-secondary mb-0">
                    {{ __('messages.stocktaking_desc') }}
                </p>
            </div>
            <div>
                @if ($activeSession)
                    <a href="{{ route('store.stocktaking.count', $activeSession->id) }}" class="btn btn-warning rounded-pill px-4 shadow-sm text-dark fw-bold">
                        <i class="bi bi-pencil-square me-1"></i>{{ __('messages.continue_active_session') }} (#{{ $activeSession->id }})
                    </a>
                @else
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#startStocktakingModal">
                        <i class="bi bi-play-circle me-1"></i>{{ __('messages.start_new_stocktaking') }}
                    </button>
                @endif
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Active Session Status Banner --}}
        @if ($activeSession)
            <div class="card border-0 shadow-sm p-4 mb-4 bg-warning-subtle text-warning-emphasis">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="fs-1 text-warning"><i class="bi bi-lock-fill"></i></div>
                        <div>
                            <h4 class="fw-bold mb-1 text-dark">{{ __('messages.store_locked_active_session_title') }}</h4>
                            <p class="mb-0 text-secondary">
                                {{ __('messages.stocktaking_session_in_progress_msg', ['id' => $activeSession->id, 'user' => $activeSession->user->name ?? 'System']) }}
                                &bull; <span class="fw-semibold">{{ __('messages.started_at') }}:</span> {{ $activeSession->started_at->format('Y-m-d H:i') }}
                            </p>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('store.stocktaking.count', $activeSession->id) }}" class="btn btn-warning rounded-pill px-4 font-bold text-dark shadow-sm">
                            <i class="bi bi-calculator me-1"></i>{{ __('messages.enter_physical_counts') }}
                        </a>
                        <a href="{{ route('store.stocktaking.show', $activeSession->id) }}" class="btn btn-outline-dark rounded-pill px-3">
                            <i class="bi bi-file-earmark-bar-graph me-1"></i>{{ __('messages.view_report') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Sessions History Table --}}
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-primary"></i>
                {{ __('messages.stocktaking_sessions_history') }}
            </h5>
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.session_id') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.started_by') }}</th>
                            <th>{{ __('messages.started_at') }}</th>
                            <th>{{ __('messages.items_count') }}</th>
                            <th>{{ __('messages.adjusted_by') }}</th>
                            <th class="text-end">{{ __('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sessions as $session)
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark font-monospace">#{{ $session->id }}</span>
                                </td>
                                <td>
                                    @if ($session->isInProgress())
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill fw-bold">
                                            <i class="bi bi-arrow-repeat me-1"></i>{{ __('messages.in_progress') }}
                                        </span>
                                    @elseif ($session->isCompleted())
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 rounded-pill fw-bold">
                                            <i class="bi bi-clock-history me-1"></i>{{ __('messages.completed_pending_adj') }}
                                        </span>
                                    @elseif ($session->isAdjusted())
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">
                                            <i class="bi bi-check-circle-fill me-1"></i>{{ __('messages.adjusted_and_unlocked') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $session->user->name ?? 'System' }}</span>
                                </td>
                                <td>
                                    <span class="text-secondary small">{{ $session->started_at->format('Y-m-d H:i') }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">{{ $session->items_count }}</span>
                                </td>
                                <td>
                                    @if ($session->isAdjusted())
                                        <span class="text-dark small fw-semibold">{{ $session->adjustedByUser->name ?? 'System' }}</span>
                                        <div class="text-secondary fs-7">{{ $session->adjusted_at ? $session->adjusted_at->format('Y-m-d H:i') : '' }}</div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        @if ($session->isInProgress())
                                            <a href="{{ route('store.stocktaking.count', $session->id) }}" class="btn btn-sm btn-warning rounded-pill px-3 text-dark fw-bold shadow-sm">
                                                <i class="bi bi-pencil-square me-1"></i>{{ __('messages.count') }}
                                            </a>
                                        @endif
                                        <a href="{{ route('store.stocktaking.show', $session->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                            <i class="bi bi-file-earmark-bar-graph me-1"></i>{{ __('messages.report') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-secondary py-5">
                                    <i class="bi bi-clipboard-x fs-2 d-block mb-2"></i>
                                    {{ __('messages.no_stocktaking_sessions') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $sessions->links() }}
            </div>
        </div>
    </div>

    {{-- Start New Stocktaking Modal --}}
    <div class="modal fade" id="startStocktakingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('store.stocktaking.start') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 bg-primary-subtle text-primary">
                    <h5 class="modal-title fw-bold"><i class="bi bi-play-circle me-2"></i>{{ __('messages.start_new_stocktaking') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 rounded-3 small mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ __('messages.start_stocktaking_warning_notice') }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.Notes') }} ({{ __('messages.optional') }})</label>
                        <textarea name="notes" class="form-control rounded-3" rows="3" placeholder="e.g. End of Quarter 3 Inventory Audit"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('messages.confirm_start_stocktaking') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
