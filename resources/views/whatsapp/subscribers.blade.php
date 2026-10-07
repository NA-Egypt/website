<x-layout>
    <x-backhead>{{ __('messages.whatsapp_subscribers') }}</x-backhead>

    <div class="container-fluid px-3 px-md-4 py-3">
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
            <div class="d-flex flex-wrap align-items-center gap-2">
                {{-- Convention Attendees CSV (Option 9) Button --}}
                <button type="button" class="btn btn-warning text-dark rounded-pill px-3 shadow-sm d-flex align-items-center gap-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#conventionAttendeesModal">
                    <i class="bi bi-ticket-perforated-fill fs-6"></i>
                    <span>{{ __('messages.whatsapp_convention_attendees_title') }}</span>
                    @if(!empty($conventionAttendeesLog))
                        <span class="badge bg-dark rounded-pill">{{ count($conventionAttendeesLog->metadata['recipients'] ?? []) }}</span>
                    @endif
                </button>
                {{-- Bulk CSV Button --}}
                <button type="button" class="btn btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#bulkCsvModal">
                    <i class="bi bi-filetype-csv fs-6"></i>
                    <span>{{ __('messages.whatsapp_bulk_csv_title') }}</span>
                </button>
                {{-- Download Sample CSV --}}
                <a href="{{ route('whatsapp.subscribers.sample-csv') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2" title="{{ __('messages.whatsapp_download_sample_csv') }}">
                    <i class="bi bi-download"></i>
                    <span class="d-none d-sm-inline">{{ __('messages.whatsapp_download_sample_csv') }}</span>
                </a>
                {{-- Quick Broadcast Button --}}
                <button type="button" class="btn btn-outline-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#broadcastModal">
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Staging / Dev Mode Notice (egyptna.org) --}}
        @if($isDev)
            <div class="alert alert-warning rounded-4 border-0 shadow-sm p-3 mb-4" style="background: rgba(245, 158, 11, 0.1); border-left: 4px solid #f59e0b !important;">
                <span class="small fw-semibold text-warning-emphasis">
                    <i class="bi bi-tools me-1"></i>{{ __('messages.whatsapp_dev_badge') }} — {{ __('messages.whatsapp_dev_notice') }}
                </span>
            </div>
        @endif

        {{-- Real-Time Live Campaign Progress Tracker Card --}}
        @php
            $activeBroadcast = $broadcastLogs->first(fn($b) => in_array($b->status, ['processing', 'paused']));
        @endphp
        <div id="liveCampaignTracker" class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" 
             style="background: var(--card-bg, #ffffff); {{ $activeBroadcast ? '' : 'display: none;' }}"
             data-active-id="{{ $activeBroadcast->id ?? '' }}">
            <div class="card-header border-0 bg-primary-subtle py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="spinner-grow spinner-grow-sm text-primary" id="liveTrackerSpinner" role="status" style="{{ ($activeBroadcast && $activeBroadcast->status === 'paused') ? 'display: none;' : '' }}"></span>
                    <h6 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-broadcast me-1"></i>{{ __('messages.whatsapp_campaign_progress') }}
                        <span class="badge {{ ($activeBroadcast && $activeBroadcast->status === 'paused') ? 'bg-warning text-dark' : 'bg-primary' }} rounded-pill ms-2" id="liveTrackerBadge">
                            {{ ($activeBroadcast && $activeBroadcast->status === 'paused') ? __('messages.whatsapp_paused') : __('messages.whatsapp_realtime_badge') }}
                        </span>
                    </h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm" id="btnReturnToLiveCampaign" style="display: none;">
                        <i class="bi bi-arrow-return-left me-1"></i><span>{{ __('messages.whatsapp_return_to_live') }}</span>
                    </button>
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm" id="btnResumeCampaign" style="{{ ($activeBroadcast && $activeBroadcast->status === 'paused') ? '' : 'display: none;' }}">
                        <i class="bi bi-play-circle me-1"></i>{{ __('messages.whatsapp_resume_broadcast') }}
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-sm" id="btnCancelCampaign" style="{{ ($activeBroadcast && in_array($activeBroadcast->status, ['processing', 'paused'])) ? '' : 'display: none;' }}">
                        <i class="bi bi-stop-circle me-1"></i>{{ __('messages.whatsapp_cancel_broadcast') }}
                    </button>
                </div>
            </div>
            <div class="card-body p-4">
                @php
                    $initTotal = $activeBroadcast->total_recipients ?? 0;
                    $initSent = $activeBroadcast->successful_count ?? 0;
                    $initFailed = $activeBroadcast->failed_count ?? 0;
                    $initSkipped = $activeBroadcast->metadata['skipped_count'] ?? 0;
                    $initRemaining = max(0, $initTotal - ($initSent + $initFailed + $initSkipped));
                    $initPercent = ($initTotal > 0) ? min(100, round((($initSent + $initFailed) / $initTotal) * 100)) : 0;
                @endphp
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0" id="liveCampaignTitle">{{ $activeBroadcast->title ?? 'Bulk Campaign' }}</h6>
                    <span class="fw-bold font-monospace fs-5 text-primary" id="livePercentText">{{ $initPercent }}%</span>
                </div>

                {{-- Animated Progress Bar --}}
                <div class="progress rounded-pill mb-3" style="height: 10px;">
                    <div id="liveProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: {{ $initPercent }}%;"></div>
                </div>

                {{-- Counters Row --}}
                <div class="row g-2 text-center mb-3">
                    <div class="col">
                        <div class="p-2 rounded-3 bg-light">
                            <span class="text-muted d-block small" style="font-size: 0.72rem;">{{ __('Total') }}</span>
                            <strong class="font-monospace fs-6" id="liveTotalCount">{{ $initTotal }}</strong>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-2 rounded-3 bg-success-subtle text-success">
                            <span class="d-block small" style="font-size: 0.72rem;">{{ __('Sent') }}</span>
                            <strong class="font-monospace fs-6" id="liveSentCount">{{ $initSent }}</strong>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-2 rounded-3 bg-danger-subtle text-danger">
                            <span class="d-block small" style="font-size: 0.72rem;">{{ __('Failed') }}</span>
                            <strong class="font-monospace fs-6" id="liveFailedCount">{{ $initFailed }}</strong>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-2 rounded-3 bg-info-subtle text-info">
                            <span class="d-block small" style="font-size: 0.72rem;">{{ __('messages.whatsapp_skipped') }}</span>
                            <strong class="font-monospace fs-6" id="liveSkippedCount">{{ $initSkipped }}</strong>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-2 rounded-3 bg-light text-muted">
                            <span class="d-block small" style="font-size: 0.72rem;">{{ __('Remaining') }}</span>
                            <strong class="font-monospace fs-6" id="liveRemainingCount">{{ $initRemaining }}</strong>
                        </div>
                    </div>
                </div>

                {{-- Streaming Activity Log Window with Controls --}}
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h6 class="fw-bold small text-muted mb-0">
                            <i class="bi bi-terminal me-1"></i>{{ __('messages.whatsapp_live_progress_logs') }}
                            <span class="badge bg-secondary font-monospace ms-1" id="liveLogCountBadge">{{ count($activeBroadcast->metadata['logs'] ?? []) }}</span>
                        </h6>
                        {{-- Quick Filter Pills --}}
                        <div class="btn-group btn-group-sm" role="group" id="logFilterPills">
                            <button type="button" class="btn btn-outline-secondary btn-xs py-0 px-2 active" data-filter="all">{{ __('messages.all') }}</button>
                            <button type="button" class="btn btn-outline-danger btn-xs py-0 px-2" data-filter="failed">❌ {{ __('Failed') }}</button>
                            <button type="button" class="btn btn-outline-success btn-xs py-0 px-2" data-filter="sent">✅ {{ __('Sent') }}</button>
                            <button type="button" class="btn btn-outline-warning btn-xs py-0 px-2" data-filter="cooldown">☕ {{ __('messages.whatsapp_cooldown') }}</button>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        {{-- Search Input --}}
                        <div class="input-group input-group-sm" style="max-width: 220px;">
                            <span class="input-group-text bg-light border-end-0 py-0 text-muted"><i class="bi bi-search" style="font-size: 0.72rem;"></i></span>
                            <input type="text" id="logSearchInput" class="form-control form-control-sm border-start-0 py-0" placeholder="{{ __('messages.whatsapp_filter_logs') }}" style="font-size: 0.75rem;">
                        </div>
                        {{-- Copy Button --}}
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 rounded-pill shadow-xs" id="btnCopyLogs" title="{{ __('messages.whatsapp_copy_logs') }}" style="font-size: 0.72rem;">
                            <i class="bi bi-clipboard me-1"></i><span id="copyBtnText">{{ __('messages.whatsapp_copy_logs') }}</span>
                        </button>
                        {{-- Auto-scroll Toggle Button --}}
                        <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2 rounded-pill active" id="btnToggleAutoScroll" title="{{ __('messages.whatsapp_auto_scroll') }}" style="font-size: 0.72rem;">
                            <i class="bi bi-arrow-repeat me-1"></i>{{ __('messages.whatsapp_auto_scroll') }}
                        </button>
                        {{-- Scroll to Bottom Button --}}
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 rounded-pill" id="btnScrollLogBottom" title="Scroll to bottom" style="font-size: 0.72rem;">
                            <i class="bi bi-arrow-down"></i>
                        </button>
                    </div>
                </div>

                {{-- Interactive Terminal Black Box Styles --}}
                <style>
                    #liveLogWindow {
                        overflow-y: auto !important;
                        overflow-x: hidden !important;
                        overscroll-behavior: contain;
                        scrollbar-width: thin;
                        scrollbar-color: #334155 #0b1120;
                    }
                    #liveLogWindow::-webkit-scrollbar {
                        width: 6px;
                        height: 6px;
                    }
                    #liveLogWindow::-webkit-scrollbar-track {
                        background: #0b1120;
                        border-radius: 4px;
                    }
                    #liveLogWindow::-webkit-scrollbar-thumb {
                        background: #334155;
                        border-radius: 4px;
                    }
                    #liveLogWindow::-webkit-scrollbar-thumb:hover {
                        background: #475569;
                    }
                    #liveLogWindow .log-line {
                        white-space: pre-wrap;
                        word-break: break-word;
                        overflow-wrap: anywhere;
                        padding-left: 1.25rem;
                        text-indent: -1.25rem;
                        line-height: 1.6;
                    }
                </style>

                {{-- Interactive Terminal Black Box --}}
                <div id="liveLogWindow" class="p-3 rounded-4 font-monospace small overflow-y-auto shadow-inner border border-secondary-subtle" 
                     dir="ltr"
                     style="height: 280px; max-height: 520px; resize: vertical; font-size: 0.8rem; line-height: 1.7; text-align: left; background-color: #0b1120 !important; color: #e2e8f0 !important; overflow-x: hidden !important; overscroll-behavior: contain; word-break: break-word; overflow-wrap: anywhere;">
                    @php
                        $initialLogs = $activeBroadcast->metadata['logs'] ?? [];
                    @endphp
                    @forelse($initialLogs as $logLine)
                        <div class="log-line mb-1" style="{{ str_contains($logLine, '✅') ? 'color: #4ade80 !important;' : (str_contains($logLine, '❌') ? 'color: #f87171 !important; font-weight: 600;' : (str_contains($logLine, '☕') || str_contains($logLine, '⚠️') ? 'color: #facc15 !important;' : (str_contains($logLine, '⏭️') ? 'color: #38bdf8 !important;' : (str_contains($logLine, '🛑') ? 'color: #fbbf24 !important; font-weight: bold;' : 'color: #cbd5e1 !important;')))) }}">
                            {{ $logLine }}
                        </div>
                    @empty
                        <div class="text-secondary text-center py-4" id="logEmptyPlaceholder">
                            <i class="bi bi-terminal fs-3 d-block mb-1 opacity-50"></i>
                            {{ __('messages.whatsapp_no_logs') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

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

        {{-- Subscribers Table Card --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--card-bg, #ffffff);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
                        <i class="bi bi-journal-text text-primary me-2"></i>{{ __('messages.whatsapp_subscribers') }}
                    </h5>
                </div>

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
                            <i class="bi bi-filter me-1"></i>{{ __('Filter') }}
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.Phone Number') }}</th>
                                <th>{{ __('messages.Name') }}</th>
                                <th>{{ __('messages.Channel') }}</th>
                                <th>{{ __('messages.Status') }}</th>
                                <th>{{ __('messages.Subscribed At') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscribers as $sub)
                                <tr>
                                    <td class="text-muted small font-monospace">{{ $sub->id }}</td>
                                    <td class="fw-bold font-monospace text-primary">{{ $sub->phone ?: $sub->jid }}</td>
                                    <td>{{ $sub->name ?: '-' }}</td>
                                    <td><span class="badge bg-light text-dark border rounded-pill">{{ strtoupper($sub->channel) }}</span></td>
                                    <td>
                                        @if($sub->is_active)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                                <i class="bi bi-check-circle me-1"></i>{{ __('Active') }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">
                                                <i class="bi bi-x-circle me-1"></i>{{ __('Inactive') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ $sub->subscribed_at ? $sub->subscribed_at->format('Y-m-d H:i') : $sub->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox display-6 d-block mb-2 text-secondary opacity-50"></i>
                                        {{ __('No subscribers found matching your criteria.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($subscribers->hasPages())
                    <div class="pt-3 border-top d-flex justify-content-center">
                        {{ $subscribers->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent Broadcast History --}}
        <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg, #ffffff);">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: var(--text-primary);">
                    <i class="bi bi-clock-history text-secondary me-2"></i>{{ __('messages.Recent Broadcast Transmission History') }}
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.Title') }}</th>
                                <th>{{ __('messages.Channel') }}</th>
                                <th>{{ __('Device') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Sent / Failed') }}</th>
                                <th>{{ __('Anti-Ban') }}</th>
                                <th>{{ __('Dispatched By') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th class="text-end">{{ __('messages.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($broadcastLogs as $log)
                                <tr>
                                    <td class="text-muted small font-monospace">{{ $log->id }}</td>
                                    <td class="fw-semibold">{{ $log->title }}</td>
                                    <td><span class="badge bg-light text-dark border rounded-pill">{{ strtoupper($log->channel) }}</span></td>
                                    <td><code class="small">{{ $log->device_id ?: 'default' }}</code></td>
                                    <td>
                                        @if($log->status === 'completed')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Completed</span>
                                        @elseif($log->status === 'processing')
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>Running
                                            </span>
                                        @elseif($log->status === 'cancelled')
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Cancelled</span>
                                        @elseif($log->status === 'paused')
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Paused</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill">{{ ucfirst($log->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-success fw-bold font-monospace">{{ $log->successful_count }}</span>
                                        /
                                        <span class="text-danger font-monospace">{{ $log->failed_count }}</span>
                                        <small class="text-muted">({{ $log->total_recipients }} total)</small>
                                    </td>
                                    <td>
                                        @if($log->anti_ban_profile)
                                            <span class="badge bg-light text-secondary border rounded-pill small">{{ ucfirst(str_replace('_', ' ', $log->anti_ban_profile)) }}</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="small">{{ $log->dispatcher ? $log->dispatcher->name : 'System Scheduler' }}</td>
                                    <td class="small text-muted">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 btn-inspect-broadcast" 
                                                data-id="{{ $log->id }}" style="font-size: 0.75rem;" title="{{ __('messages.whatsapp_inspect_logs') }}">
                                            <i class="bi bi-terminal me-1"></i>{{ __('messages.whatsapp_logs') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">{{ __('No broadcasts dispatched yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Interactive Bulk CSV Campaign Modal --}}
    <div class="modal fade" id="bulkCsvModal" tabindex="-1" aria-labelledby="bulkCsvModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <form method="POST" action="{{ route('whatsapp.subscribers.bulk-csv') }}" enctype="multipart/form-data" id="bulkCsvForm">
                    @csrf
                    <div class="modal-header border-0 bg-light p-4">
                        <div>
                            <h5 class="modal-title fw-bold" id="bulkCsvModalLabel">
                                <i class="bi bi-filetype-csv text-primary me-2"></i>{{ __('messages.whatsapp_bulk_csv_title') }}
                            </h5>
                            <p class="text-muted small mb-0">{{ __('messages.whatsapp_bulk_csv_desc') }}</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        {{-- Educational Help Box --}}
                        <div class="alert alert-info rounded-4 border-0 p-3 mb-4 d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle-fill fs-4 text-info"></i>
                                <span class="small">
                                    {{ __('Columns supported:') }} <code>phone</code>, <code>name</code>, <code>message</code>.
                                    {{ __('Need the template?') }}
                                </span>
                            </div>
                            <a href="{{ route('whatsapp.subscribers.sample-csv') }}" class="btn btn-outline-info btn-sm rounded-pill text-nowrap px-3 shadow-sm bg-white">
                                <i class="bi bi-download me-1"></i>{{ __('Download Sample CSV') }}
                            </a>
                        </div>

                        <div class="row g-3">
                            {{-- Sending Device Selector --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">{{ __('messages.whatsapp_choose_device') }} <span class="text-danger">*</span></label>
                                <select name="device_id" class="form-select rounded-3" required>
                                    @foreach($devices as $dev)
                                        <option value="{{ $dev['id'] }}" {{ $dev['id'] === $activeDeviceId ? 'selected' : '' }}>
                                            {{ $dev['id'] }} {{ !empty($dev['display_name']) ? '('.$dev['display_name'].')' : '' }} [{{ $dev['state'] }}]
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- CSV File Upload --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">{{ __('messages.whatsapp_csv_file') }} <span class="text-danger">*</span></label>
                                <input type="file" name="csv_file" id="csvFileInput" class="form-control rounded-3" accept=".csv,text/csv,text/plain" required>
                            </div>

                            {{-- Send Mode Selection --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold small">{{ __('messages.whatsapp_send_mode') }} <span class="text-danger">*</span></label>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="form-check p-3 border rounded-3 bg-light h-100">
                                            <input class="form-check-input" type="radio" name="send_mode" id="modeTemplate" value="template_to_all" checked>
                                            <label class="form-check-label fw-bold small d-block mb-1" for="modeTemplate">
                                                {{ __('messages.whatsapp_mode_template_all') }}
                                            </label>
                                            <span class="text-muted d-block" style="font-size: 0.75rem;">
                                                {{ __('Uses one shared message template for everyone with auto {name} replacement.') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check p-3 border rounded-3 bg-light h-100">
                                            <input class="form-check-input" type="radio" name="send_mode" id="modeCustomRow" value="custom_per_row">
                                            <label class="form-check-label fw-bold small d-block mb-1" for="modeCustomRow">
                                                {{ __('messages.whatsapp_mode_custom_row') }}
                                            </label>
                                            <span class="text-muted d-block" style="font-size: 0.75rem;">
                                                {{ __('Sends the custom message specified in the "message" column of each CSV row.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Message Template Textarea --}}
                            <div class="col-12" id="templateEditorWrapper">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-semibold small mb-0">{{ __('messages.whatsapp_default_message') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-1 align-items-center">
                                        <span class="text-muted" style="font-size: 0.72rem;">{{ __('Click to insert:') }}</span>
                                        <button type="button" class="btn btn-light btn-xs border rounded-pill px-2 py-0 font-monospace btn-insert-chip" data-token="{name}">{name}</button>
                                        <button type="button" class="btn btn-light btn-xs border rounded-pill px-2 py-0 font-monospace btn-insert-chip" data-token="{phone}">{phone}</button>
                                        <button type="button" class="btn btn-light btn-xs border rounded-pill px-2 py-0 font-monospace btn-insert-chip text-primary" data-token="{مرحباً|أهلاً بكم|السلام عليكم}">{Spintax}</button>
                                    </div>
                                </div>
                                <textarea name="default_message" id="defaultMessageInput" rows="3" class="form-control rounded-3" placeholder="مرحباً {name}، نود تذكيرك بموعد الاجتماع القادم..."></textarea>
                                <div class="mt-1 d-flex align-items-center gap-1 text-muted" style="font-size: 0.74rem;">
                                    <i class="bi bi-magic text-primary"></i>
                                    <span>{{ __('messages.whatsapp_spintax_tip') }}</span>
                                </div>
                            </div>

                            {{-- Anti-Ban Protection Suite --}}
                            <div class="col-12">
                                <div class="card border rounded-4 p-3 bg-light">
                                    <h6 class="fw-bold mb-2 text-primary small">
                                        <i class="bi bi-shield-check me-1"></i>{{ __('messages.whatsapp_antiban_suite') }}
                                    </h6>
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold mb-1">{{ __('messages.whatsapp_antiban_profile') }}</label>
                                            <select name="anti_ban_profile" id="antiBanProfileSelect" class="form-select form-select-sm rounded-3">
                                                <option value="ultra_safe" selected>{{ __('messages.whatsapp_antiban_ultra_safe') }}</option>
                                                <option value="warmup">{{ __('messages.whatsapp_antiban_warmup') }}</option>
                                                <option value="safe">{{ __('messages.whatsapp_antiban_safe') }}</option>
                                                <option value="fast">{{ __('messages.whatsapp_antiban_fast') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold mb-1" for="sessionCapInput">
                                                <i class="bi bi-shield-lock text-warning me-1"></i>{{ __('messages.whatsapp_session_cap') }}
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" name="session_cap" id="sessionCapInput" class="form-control form-control-sm rounded-start-3" value="35" min="5" max="500">
                                                <span class="input-group-text small text-muted rounded-end-3">{{ __('messages.messages') }}</span>
                                            </div>
                                            <div class="form-text text-muted" style="font-size: 0.72rem;">
                                                {{ __('messages.whatsapp_session_cap_help') }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" name="enable_cooldown" value="1" id="checkCooldown" checked>
                                                <label class="form-check-label small" for="checkCooldown">
                                                    {{ __('messages.whatsapp_antiban_cooldown') }}
                                                </label>
                                            </div>
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" name="append_optout" value="1" id="checkOptout">
                                                <label class="form-check-label small" for="checkOptout">
                                                    {{ __('messages.whatsapp_append_optout') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <div class="d-flex align-items-center gap-2 p-2 rounded-3 bg-white border border-success-subtle small text-success-emphasis" style="font-size: 0.76rem;">
                                                <i class="bi bi-shield-fill-check text-success flex-shrink-0"></i>
                                                <span>{{ __('messages.whatsapp_precheck_notice') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Exclusion of Recently Messaged Contacts (Prevents Duplicates) --}}
                            <div class="col-12">
                                <div class="card border rounded-4 p-3 bg-light-subtle">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="exclude_recent" value="1" id="excludeRecentSwitch" checked>
                                        <label class="form-check-label fw-bold small text-dark" for="excludeRecentSwitch">
                                            <i class="bi bi-shield-check text-success me-1"></i>{{ __('messages.whatsapp_exclude_recent') }}
                                        </label>
                                        <span class="d-block text-muted small mt-1" style="font-size: 0.78rem;">
                                            {{ __('messages.whatsapp_exclude_recent_desc') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Client-Side CSV Preview (First 5 Rows) --}}
                            <div class="col-12" id="csvPreviewSection" style="display: none;">
                                <h6 class="fw-bold small text-muted mb-2">
                                    <i class="bi bi-eye me-1"></i>{{ __('messages.whatsapp_csv_preview') }}
                                </h6>
                                <div class="table-responsive border rounded-3 bg-white">
                                    <table class="table table-sm table-bordered mb-0 small" style="font-size: 0.75rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Phone</th>
                                                <th>Name</th>
                                                <th>Custom Message Excerpt</th>
                                            </tr>
                                        </thead>
                                        <tbody id="csvPreviewTableBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 bg-light p-3">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" id="btnSubmitBulkCsv">
                            <i class="bi bi-send-check me-1"></i>{{ __('Launch Bulk Campaign') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Dedicated Modal: Upload Convention Attendees CSV (Option 9 On-Demand) --}}
    <div class="modal fade" id="conventionAttendeesModal" tabindex="-1" aria-labelledby="conventionAttendeesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow">
                <form action="{{ route('whatsapp.convention-attendees.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-0 bg-warning bg-opacity-10 p-4">
                        <div>
                            <h5 class="modal-title fw-bold text-dark" id="conventionAttendeesModalLabel">
                                <i class="bi bi-ticket-perforated-fill text-warning me-2"></i>{{ __('messages.whatsapp_convention_attendees_title') }}
                            </h5>
                            <p class="text-muted small mb-0">{{ __('messages.whatsapp_convention_attendees_desc') }}</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        {{-- Current Status Banner --}}
                        <div class="card border border-warning-subtle bg-warning bg-opacity-10 rounded-4 p-3 mb-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <div class="fw-bold small text-dark">
                                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                                        {{ __('messages.whatsapp_convention_attendees_active_count', ['count' => !empty($conventionAttendeesLog) ? count($conventionAttendeesLog->metadata['recipients'] ?? []) : 0]) }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ __('messages.whatsapp_convention_attendees_last_updated', ['date' => !empty($conventionAttendeesLog->updated_at) ? $conventionAttendeesLog->updated_at->diffForHumans() : __('Never')]) }}
                                        @if(!empty($conventionAttendeesLog->metadata['file_name']))
                                            ({{ $conventionAttendeesLog->metadata['file_name'] }})
                                        @endif
                                    </div>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                    <i class="bi bi-robot me-1"></i>{{ __('Option 9 Active in Bot') }}
                                </span>
                            </div>
                        </div>

                        {{-- Instructions Notice --}}
                        <div class="alert alert-light border rounded-3 p-3 mb-3 small">
                            <div class="fw-semibold mb-1 text-primary">
                                <i class="bi bi-info-circle me-1"></i>{{ __('How this works:') }}
                            </div>
                            <ul class="mb-0 ps-3">
                                <li>{{ __('Uploading this file DOES NOT blast messages to attendees (zero outbound restrictions).') }}</li>
                                <li>{{ __('When any registered attendee sends 9 (or ٩) to the bot, they receive their invitation with their custom link and code instantly.') }}</li>
                                <li>{{ __('Supports CSV columns:') }} <code>phone</code> ({{ __('required') }}), <code>name</code>, <code>message</code>.</li>
                            </ul>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold small">{{ __('messages.whatsapp_csv_file') }} <span class="text-danger">*</span></label>
                                <input type="file" name="csv_file" class="form-control rounded-3" accept=".csv,text/csv" required>
                                <div class="form-text small">{{ __('Max size: 10MB. UTF-8 encoded CSV file.') }}</div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold small">{{ __('Campaign Title / Dataset Note (Optional)') }}</label>
                                <input type="text" name="title" class="form-control rounded-3" placeholder="كشف حضور مؤتمر مسار يجمعنا 2026">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 bg-light p-3">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm">
                            <i class="bi bi-upload me-1"></i>{{ __('messages.whatsapp_convention_attendees_upload_btn') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Standard Quick Broadcast Modal --}}
    <div class="modal fade" id="broadcastModal" tabindex="-1" aria-labelledby="broadcastModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <form method="POST" action="{{ route('whatsapp.subscribers.broadcast') }}">
                    @csrf
                    <div class="modal-header border-0 bg-light p-4">
                        <h5 class="modal-title fw-bold" id="broadcastModalLabel">
                            <i class="bi bi-broadcast text-primary me-2"></i>{{ __('messages.whatsapp_trigger_broadcast') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">{{ __('messages.whatsapp_broadcast_channel') }} <span class="text-danger">*</span></label>
                            <select name="channel" class="form-select rounded-3">
                                <option value="jft">Just For Today (JFT)</option>
                                <option value="announcements">General Announcements</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">{{ __('messages.whatsapp_broadcast_title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. Regional Assembly Reminder" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">{{ __('messages.Message') }} <span class="text-danger">*</span></label>
                            <textarea name="message" rows="4" class="form-control rounded-3" placeholder="Message content..." required></textarea>
                            <div class="form-text small">{{ __('Broadcast is rate-limited with anti-ban jitter delays (2-5s per subscriber).') }}</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light p-3">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-send me-1"></i>{{ __('Dispatch Broadcast') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Chip insertion into template textarea
            document.querySelectorAll('.btn-insert-chip').forEach(btn => {
                btn.addEventListener('click', function () {
                    const token = this.getAttribute('data-token');
                    const textarea = document.getElementById('defaultMessageInput');
                    if (textarea) {
                        const start = textarea.selectionStart;
                        const end = textarea.selectionEnd;
                        textarea.value = textarea.value.substring(0, start) + token + textarea.value.substring(end);
                        textarea.focus();
                        textarea.selectionEnd = start + token.length;
                    }
                });
            });

            // Auto-adjust default session safety cap based on selected anti-ban profile
            const profileSelect = document.getElementById('antiBanProfileSelect');
            const sessionCapInput = document.getElementById('sessionCapInput');
            if (profileSelect && sessionCapInput) {
                profileSelect.addEventListener('change', function () {
                    const defaults = {
                        'ultra_safe': 35,
                        'warmup': 20,
                        'safe': 60,
                        'fast': 100
                    };
                    if (defaults[this.value] !== undefined) {
                        sessionCapInput.value = defaults[this.value];
                    }
                });
            }

            // Client-side CSV Preview on file select
            const csvInput = document.getElementById('csvFileInput');
            const previewSection = document.getElementById('csvPreviewSection');
            const previewBody = document.getElementById('csvPreviewTableBody');

            if (csvInput) {
                csvInput.addEventListener('change', function () {
                    const file = this.files[0];
                    if (!file) {
                        previewSection.style.display = 'none';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const text = e.target.result;
                        const lines = text.split(/\r?\n/).filter(line => line.trim().length > 0);
                        if (lines.length < 2) return;

                        previewBody.innerHTML = '';
                        // Preview first 5 rows
                        const rowsToPreview = lines.slice(1, 6);
                        rowsToPreview.forEach((line, idx) => {
                            // Basic CSV parse
                            const parts = line.split(',');
                            const phone = (parts[0] || '').replace(/["']/g, '').trim();
                            const name = (parts[1] || '').replace(/["']/g, '').trim();
                            const msg = parts.slice(2).join(',').replace(/["']/g, '').trim();

                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td class="text-muted font-monospace">${idx + 1}</td>
                                <td class="fw-bold font-monospace text-primary">${phone || '-'}</td>
                                <td>${name || '-'}</td>
                                <td class="text-truncate text-muted" style="max-width: 200px;">${msg || '<em>(Uses default template)</em>'}</td>
                            `;
                            previewBody.appendChild(tr);
                        });

                        previewSection.style.display = 'block';
                    };
                    reader.readAsText(file, 'UTF-8');
                });
            }

            // Real-Time Live Campaign Tracker & Terminal Controller
            const liveTracker = document.getElementById('liveCampaignTracker');
            const liveActiveBroadcastId = "{{ $activeBroadcast->id ?? '' }}";
            let currentBroadcastId = liveActiveBroadcastId;
            let trackerInterval = null;

            // Named route templates with current locale included
            const progressRouteTemplate = "{{ route('whatsapp.broadcasts.progress', ['broadcast' => '__ID__']) }}";
            const resumeRouteTemplate = "{{ route('whatsapp.broadcasts.resume', ['broadcast' => '__ID__']) }}";
            const cancelRouteTemplate = "{{ route('whatsapp.broadcasts.cancel', ['broadcast' => '__ID__']) }}";

            // Terminal State
            let currentLogs = @json($activeBroadcast->metadata['logs'] ?? []);
            let currentFilter = 'all'; // 'all', 'failed', 'sent', 'cooldown'
            let searchQuery = '';
            let autoScroll = true;

            function escapeLog(str) {
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            function formatLogLine(rawText) {
                const escaped = escapeLog(rawText);
                let colorStyle = 'color: #cbd5e1 !important;';

                if (rawText.includes('✅')) {
                    colorStyle = 'color: #4ade80 !important;';
                } else if (rawText.includes('❌')) {
                    colorStyle = 'color: #f87171 !important; font-weight: 600;';
                } else if (rawText.includes('☕') || rawText.includes('⚠️')) {
                    colorStyle = 'color: #facc15 !important;';
                } else if (rawText.includes('🛑')) {
                    colorStyle = 'color: #fbbf24 !important; font-weight: bold;';
                } else if (rawText.includes('⏭️')) {
                    colorStyle = 'color: #38bdf8 !important;';
                } else if (rawText.includes('▶️')) {
                    colorStyle = 'color: #a78bfa !important; font-weight: 600;';
                }

                return `<div class="log-line mb-1" style="${colorStyle}">${escaped}</div>`;
            }

            function matchesFilter(logText, filter) {
                if (filter === 'failed') return logText.includes('❌');
                if (filter === 'sent') return logText.includes('✅');
                if (filter === 'cooldown') return logText.includes('☕') || logText.includes('🛑') || logText.includes('⚠️');
                return true;
            }

            function matchesSearch(logText, query) {
                if (!query) return true;
                return logText.toLowerCase().includes(query.toLowerCase());
            }

            function renderTerminalLogs() {
                const logWin = document.getElementById('liveLogWindow');
                if (!logWin) return;

                const filteredLogs = currentLogs.filter(l => matchesFilter(l, currentFilter) && matchesSearch(l, searchQuery));

                const countBadge = document.getElementById('liveLogCountBadge');
                if (countBadge) {
                    if (filteredLogs.length !== currentLogs.length) {
                        countBadge.textContent = `${filteredLogs.length} / ${currentLogs.length}`;
                    } else {
                        countBadge.textContent = currentLogs.length;
                    }
                }

                if (filteredLogs.length === 0) {
                    if (currentLogs.length === 0) {
                        logWin.innerHTML = `
                            <div class="text-secondary text-center py-4" id="logEmptyPlaceholder">
                                <i class="bi bi-terminal fs-3 d-block mb-1 opacity-50"></i>
                                {{ __('messages.whatsapp_no_logs') }}
                            </div>`;
                    } else {
                        logWin.innerHTML = `
                            <div class="text-secondary text-center py-4">
                                <i class="bi bi-funnel fs-3 d-block mb-1 opacity-50"></i>
                                <div>{{ __('messages.whatsapp_clear_filter') }}</div>
                                <small class="opacity-75">No logs matched the current filter or search criteria.</small>
                            </div>`;
                    }
                    return;
                }

                logWin.innerHTML = filteredLogs.map(l => formatLogLine(l)).join('');

                if (autoScroll) {
                    logWin.scrollTop = logWin.scrollHeight;
                }
            }

            // Sync Tracker Progress and State
            function applyBroadcastProgress(data) {
                if (!data) return;

                const percentText = document.getElementById('livePercentText');
                const progressBar = document.getElementById('liveProgressBar');
                const totalCount = document.getElementById('liveTotalCount');
                const sentCount = document.getElementById('liveSentCount');
                const failedCount = document.getElementById('liveFailedCount');
                const skippedCount = document.getElementById('liveSkippedCount');
                const remainingCount = document.getElementById('liveRemainingCount');
                const campaignTitle = document.getElementById('liveCampaignTitle');

                if (campaignTitle && data.title) campaignTitle.textContent = data.title;
                if (percentText) percentText.textContent = data.percent + '%';
                if (progressBar) progressBar.style.width = data.percent + '%';
                if (totalCount) totalCount.textContent = data.total;
                if (sentCount) sentCount.textContent = data.successful;
                if (failedCount) failedCount.textContent = data.failed;
                if (skippedCount) skippedCount.textContent = data.skipped || 0;
                if (remainingCount) {
                    remainingCount.textContent = Math.max(0, data.total - (data.successful + data.failed + (data.skipped || 0)));
                }

                const spinner = document.getElementById('liveTrackerSpinner');
                const badge = document.getElementById('liveTrackerBadge');
                const btnResume = document.getElementById('btnResumeCampaign');
                const btnCancel = document.getElementById('btnCancelCampaign');
                const btnReturn = document.getElementById('btnReturnToLiveCampaign');

                // Return to Live button visibility
                if (btnReturn) {
                    if (liveActiveBroadcastId && String(liveActiveBroadcastId) !== String(data.id)) {
                        btnReturn.style.display = 'inline-flex';
                    } else {
                        btnReturn.style.display = 'none';
                    }
                }

                if (data.status === 'paused') {
                    if (spinner) spinner.style.display = 'none';
                    if (btnResume) btnResume.style.display = 'inline-flex';
                    if (btnCancel) btnCancel.style.display = 'inline-flex';
                    if (badge) {
                        badge.className = 'badge bg-warning text-dark rounded-pill ms-2';
                        badge.textContent = '{{ __("messages.whatsapp_paused") }}';
                    }
                } else if (data.status === 'processing') {
                    if (spinner) spinner.style.display = 'inline-block';
                    if (btnResume) btnResume.style.display = 'none';
                    if (btnCancel) btnCancel.style.display = 'inline-flex';
                    if (badge) {
                        badge.className = 'badge bg-primary rounded-pill ms-2';
                        badge.textContent = '{{ __("messages.whatsapp_realtime_badge") }}';
                    }
                } else {
                    if (spinner) spinner.style.display = 'none';
                    if (btnResume) btnResume.style.display = 'none';
                    if (btnCancel) btnCancel.style.display = 'none';

                    if (data.status === 'completed') {
                        if (progressBar) progressBar.className = 'progress-bar bg-success';
                        if (badge) {
                            badge.className = 'badge bg-success rounded-pill ms-2';
                            badge.textContent = 'Completed';
                        }
                    } else if (data.status === 'cancelled') {
                        if (progressBar) progressBar.className = 'progress-bar bg-secondary';
                        if (badge) {
                            badge.className = 'badge bg-secondary rounded-pill ms-2';
                            badge.textContent = 'Cancelled';
                        }
                    } else {
                        if (progressBar) progressBar.className = 'progress-bar bg-danger';
                        if (badge) {
                            badge.className = 'badge bg-danger rounded-pill ms-2';
                            badge.textContent = 'Failed';
                        }
                    }
                }

                // Update logs
                if (Array.isArray(data.logs)) {
                    currentLogs = data.logs;
                    renderTerminalLogs();
                }
            }

            function fetchBroadcastProgress(id) {
                return fetch(progressRouteTemplate.replace('__ID__', id), {
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    applyBroadcastProgress(data);
                    return data;
                })
                .catch(err => {
                    console.error('Error fetching broadcast progress:', err);
                });
            }

            function startTrackerPolling(id) {
                currentBroadcastId = id;
                liveTracker.style.display = 'block';
                clearInterval(trackerInterval);

                // Fetch once immediately
                fetchBroadcastProgress(id).then(data => {
                    if (data && !data.is_finished && (data.status === 'processing' || data.status === 'paused')) {
                        trackerInterval = setInterval(() => {
                            fetchBroadcastProgress(id).then(d => {
                                if (d && d.is_finished) {
                                    clearInterval(trackerInterval);
                                }
                            });
                        }, 2500);
                    }
                });
            }

            // Quick Filter Pills Event Listeners
            const filterPills = document.querySelectorAll('#logFilterPills button');
            filterPills.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterPills.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = this.getAttribute('data-filter') || 'all';
                    renderTerminalLogs();
                });
            });

            // Live Search Input Event Listener
            const searchInput = document.getElementById('logSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    searchQuery = this.value.trim();
                    renderTerminalLogs();
                });
            }

            // 1-Click Clipboard Copy with Feedback
            const btnCopyLogs = document.getElementById('btnCopyLogs');
            if (btnCopyLogs) {
                btnCopyLogs.addEventListener('click', function() {
                    const filteredLogs = currentLogs.filter(l => matchesFilter(l, currentFilter) && matchesSearch(l, searchQuery));
                    const textToCopy = (filteredLogs.length > 0 ? filteredLogs : currentLogs).join('\n');
                    if (!textToCopy) return;

                    navigator.clipboard.writeText(textToCopy).then(() => {
                        const copyBtnText = document.getElementById('copyBtnText');
                        const originalText = copyBtnText ? copyBtnText.textContent : '';
                        if (copyBtnText) copyBtnText.textContent = '{{ __("messages.whatsapp_logs_copied") }}';
                        btnCopyLogs.classList.add('btn-success');
                        btnCopyLogs.classList.remove('btn-outline-secondary');
                        setTimeout(() => {
                            if (copyBtnText) copyBtnText.textContent = originalText;
                            btnCopyLogs.classList.remove('btn-success');
                            btnCopyLogs.classList.add('btn-outline-secondary');
                        }, 2000);
                    }).catch(() => {
                        alert('Could not copy to clipboard.');
                    });
                });
            }

            // Auto-scroll Toggle Button
            const btnToggleAutoScroll = document.getElementById('btnToggleAutoScroll');
            if (btnToggleAutoScroll) {
                btnToggleAutoScroll.addEventListener('click', function() {
                    autoScroll = !autoScroll;
                    if (autoScroll) {
                        btnToggleAutoScroll.classList.add('active', 'btn-outline-primary');
                        btnToggleAutoScroll.classList.remove('btn-outline-secondary');
                        const logWin = document.getElementById('liveLogWindow');
                        if (logWin) logWin.scrollTop = logWin.scrollHeight;
                    } else {
                        btnToggleAutoScroll.classList.remove('active', 'btn-outline-primary');
                        btnToggleAutoScroll.classList.add('btn-outline-secondary');
                    }
                });
            }

            // Log Window Manual Scroll Detection
            const logWin = document.getElementById('liveLogWindow');
            if (logWin) {
                logWin.addEventListener('scroll', function() {
                    const distFromBottom = logWin.scrollHeight - logWin.scrollTop - logWin.clientHeight;
                    if (distFromBottom > 80 && autoScroll) {
                        // User manually scrolled up: pause auto-scroll without breaking user focus
                        autoScroll = false;
                        if (btnToggleAutoScroll) {
                            btnToggleAutoScroll.classList.remove('active', 'btn-outline-primary');
                            btnToggleAutoScroll.classList.add('btn-outline-secondary');
                        }
                    }
                });
            }

            // Scroll to Bottom Button
            const btnScroll = document.getElementById('btnScrollLogBottom');
            if (btnScroll) {
                btnScroll.addEventListener('click', function () {
                    const logWin = document.getElementById('liveLogWindow');
                    if (logWin) {
                        logWin.scrollTop = logWin.scrollHeight;
                    }
                    autoScroll = true;
                    if (btnToggleAutoScroll) {
                        btnToggleAutoScroll.classList.add('active', 'btn-outline-primary');
                        btnToggleAutoScroll.classList.remove('btn-outline-secondary');
                    }
                });
            }

            // Inspect Campaign Handler (History Table Action)
            function inspectCampaign(id) {
                if (!id) return;
                startTrackerPolling(id);
                liveTracker.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            document.querySelectorAll('.btn-inspect-broadcast').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-id');
                    inspectCampaign(targetId);
                });
            });

            // Return to Live Campaign Handler
            const btnReturn = document.getElementById('btnReturnToLiveCampaign');
            if (btnReturn) {
                btnReturn.addEventListener('click', function() {
                    if (liveActiveBroadcastId) {
                        inspectCampaign(liveActiveBroadcastId);
                    }
                });
            }

            // Initial startup
            renderTerminalLogs();
            if (currentBroadcastId) {
                startTrackerPolling(currentBroadcastId);
            }

            // Resume Campaign handler
            const btnResume = document.getElementById('btnResumeCampaign');
            if (btnResume) {
                btnResume.addEventListener('click', function () {
                    if (!currentBroadcastId) return;
                    btnResume.disabled = true;

                    fetch(resumeRouteTemplate.replace('__ID__', currentBroadcastId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(async res => {
                        let data = null;
                        try {
                            data = await res.json();
                        } catch (_) {}

                        if (res.ok && data && data.success) {
                            btnResume.style.display = 'none';
                            startTrackerPolling(currentBroadcastId);
                        } else {
                            const errMsg = (data && (data.error || data.message))
                                || 'Failed to resume broadcast. Please verify that a WhatsApp device is active.';
                            alert(errMsg);
                        }
                    })
                    .catch(err => {
                        alert('Error communicating with server: ' + (err.message || 'Network error'));
                    })
                    .finally(() => {
                        btnResume.disabled = false;
                    });
                });
            }

            // Cancel Campaign handler
            const btnCancel = document.getElementById('btnCancelCampaign');
            if (btnCancel) {
                btnCancel.addEventListener('click', function () {
                    if (!currentBroadcastId) return;
                    if (!confirm('{{ __("messages.whatsapp_cancel_broadcast") }}?')) return;

                    fetch(cancelRouteTemplate.replace('__ID__', currentBroadcastId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(async res => {
                        let data = null;
                        try {
                            data = await res.json();
                        } catch (_) {}

                        clearInterval(trackerInterval);
                        alert('{{ __("messages.whatsapp_broadcast_cancelled") }}');
                        window.location.reload();
                    })
                    .catch(err => {
                        alert('Error communicating with server: ' + (err.message || 'Network error'));
                    });
                });
            }
        });
    </script>
    @endpush
</x-layout>
