<x-layout>
    <x-backhead>{{ __('messages.whatsapp_subscribers') }}</x-backhead>

    <div class="container-fluid px-4 py-3">
        {{-- Header & Subtitle --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: var(--text-primary);">
                    <i class="bi bi-people-fill text-primary me-2"></i>{{ __('messages.whatsapp_subscribers') }}
                </h3>
                <p class="text-muted mb-0 small">
                    {{ __('messages.whatsapp_subscribers_desc') }}
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#broadcastModal">
                    <i class="bi bi-broadcast"></i>
                    <span>{{ __('messages.whatsapp_trigger_broadcast') }}</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Staging / Dev Mode Notice (egyptna.org) --}}
        @if($isDev)
            <div class="alert alert-warning rounded-4 border-0 shadow-sm p-3 mb-4" style="background: rgba(245, 158, 11, 0.1); border-left: 4px solid #f59e0b !important;">
                <span class="small fw-semibold text-warning-emphasis">
                    <i class="bi bi-tools me-1"></i>{{ __('messages.whatsapp_dev_badge') }} — {{ __('Broadcasts are restricted to whitelisted developer test numbers.') }}
                </span>
            </div>
        @endif

        {{-- KPI Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <div class="rounded-circle p-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary" style="width: 48px; height: 48px;">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">{{ __('messages.Total Subscribers') }}</span>
                            <h4 class="fw-bold mb-0">{{ number_format($stats['total']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <div class="rounded-circle p-3 d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 48px; height: 48px;">
                            <i class="bi bi-check2-circle fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">{{ __('messages.Active (Opt-in)') }}</span>
                            <h4 class="fw-bold mb-0 text-success">{{ number_format($stats['active']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <div class="rounded-circle p-3 d-flex align-items-center justify-content-center bg-secondary-subtle text-secondary" style="width: 48px; height: 48px;">
                            <i class="bi bi-x-circle fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">{{ __('messages.Unsubscribed') }}</span>
                            <h4 class="fw-bold mb-0 text-muted">{{ number_format($stats['inactive']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Subscribers Table --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--card-bg, #ffffff);">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('whatsapp.subscribers.index') }}" class="row g-3 align-items-center mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-0" placeholder="{{ __('messages.Search by phone or name...') }}" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-select bg-light border-0" onchange="this.form.submit()">
                            <option value="">{{ __('messages.All Statuses') }}</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('messages.Active Only') }}</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('messages.Unsubscribed Only') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary rounded-pill w-100 shadow-sm">
                            {{ __('messages.Filter') }}
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.Phone') }}</th>
                                <th>{{ __('messages.Name') }}</th>
                                <th>{{ __('messages.whatsapp_broadcast_channel') }}</th>
                                <th>{{ __('messages.Status') }}</th>
                                <th>{{ __('messages.Subscribed At') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscribers as $sub)
                                <tr>
                                    <td>{{ $sub->id }}</td>
                                    <td class="fw-semibold">
                                        <a href="{{ route('whatsapp.inbox') }}?search={{ $sub->phone }}" class="text-decoration-none text-primary">
                                            {{ $sub->phone }}
                                        </a>
                                    </td>
                                    <td>{{ $sub->name ?: '—' }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ strtoupper($sub->channel) }}</span></td>
                                    <td>
                                        @if($sub->is_active)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                                <i class="bi bi-check-circle-fill me-1"></i>{{ __('messages.Active') }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                                <i class="bi bi-x-circle me-1"></i>{{ __('messages.Unsubscribed') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">
                                        {{ $sub->subscribed_at ? $sub->subscribed_at->format('Y-m-d H:i') : '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        {{ __('messages.No subscribers found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($subscribers->hasPages())
                    <div class="mt-4">
                        {{ $subscribers->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent Broadcast Logs Table --}}
        <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg, #ffffff);">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-clock-history text-primary me-2"></i>{{ __('messages.Recent Broadcast Transmissions') }}
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.whatsapp_broadcast_title') }}</th>
                                <th>{{ __('messages.whatsapp_broadcast_channel') }}</th>
                                <th>{{ __('messages.Recipients') }}</th>
                                <th>{{ __('messages.Successful') }}</th>
                                <th>{{ __('messages.Failed') }}</th>
                                <th>{{ __('messages.Completed At') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($broadcastLogs as $log)
                                <tr>
                                    <td>{{ $log->id }}</td>
                                    <td class="fw-semibold">{{ $log->title }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ strtoupper($log->channel) }}</span></td>
                                    <td>{{ $log->total_recipients }}</td>
                                    <td class="text-success fw-bold">{{ $log->successful_count }}</td>
                                    <td class="text-danger">{{ $log->failed_count }}</td>
                                    <td class="text-muted small">
                                        {{ $log->completed_at ? $log->completed_at->format('Y-m-d H:i') : __('messages.In Progress...') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-muted">
                                        {{ __('messages.No broadcast history recorded.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Broadcast Modal --}}
    <div class="modal fade" id="broadcastModal" tabindex="-1" aria-labelledby="broadcastModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form method="POST" action="{{ route('whatsapp.subscribers.broadcast') }}">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="broadcastModalLabel">
                            <i class="bi bi-broadcast text-primary me-2"></i>{{ __('messages.whatsapp_trigger_broadcast') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info border-0 rounded-4 small mb-3">
                            <i class="bi bi-shield-exclamation me-1"></i>
                            {{ __('Anti-Ban Throttling Active: Messages will be dispatched with randomized 2–5 second delays.') }}
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">{{ __('messages.whatsapp_broadcast_channel') }}</label>
                            <select name="channel" class="form-select rounded-3" required>
                                <option value="jft">Just For Today (JFT)</option>
                                <option value="announcements">General Announcements</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">{{ __('messages.whatsapp_broadcast_title') }}</label>
                            <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. Special Regional Announcement" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">{{ __('messages.Message Text') }}</label>
                            <textarea name="message" rows="5" class="form-control rounded-3" placeholder="Type your broadcast message text..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="return confirm('{{ __('Are you sure you want to dispatch this broadcast to all active subscribers?') }}')">
                            <i class="bi bi-send-fill me-1"></i>{{ __('Dispatch Broadcast') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
