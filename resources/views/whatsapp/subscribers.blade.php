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
            $activeBroadcast = $broadcastLogs->firstWhere('status', 'processing');
        @endphp
        <div id="liveCampaignTracker" class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" 
             style="background: var(--card-bg, #ffffff); {{ $activeBroadcast ? '' : 'display: none;' }}"
             data-active-id="{{ $activeBroadcast->id ?? '' }}">
            <div class="card-header border-0 bg-primary-subtle py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="spinner-grow spinner-grow-sm text-primary" role="status"></span>
                    <h6 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-broadcast me-1"></i>{{ __('messages.whatsapp_campaign_progress') }}
                        <span class="badge bg-primary rounded-pill ms-2" id="liveTrackerBadge">{{ __('messages.whatsapp_realtime_badge') }}</span>
                    </h6>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-sm" id="btnCancelCampaign">
                    <i class="bi bi-stop-circle me-1"></i>{{ __('messages.whatsapp_cancel_broadcast') }}
                </button>
            </div>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0" id="liveCampaignTitle">{{ $activeBroadcast->title ?? 'Bulk Campaign' }}</h6>
                    <span class="fw-bold font-monospace fs-5 text-primary" id="livePercentText">0%</span>
                </div>

                {{-- Animated Progress Bar --}}
                <div class="progress rounded-pill mb-3" style="height: 10px;">
                    <div id="liveProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%;"></div>
                </div>

                {{-- Counters Row --}}
                <div class="row g-2 text-center mb-3">
                    <div class="col-3">
                        <div class="p-2 rounded-3 bg-light">
                            <span class="text-muted d-block small" style="font-size: 0.75rem;">{{ __('Total') }}</span>
                            <strong class="font-monospace fs-6" id="liveTotalCount">0</strong>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 rounded-3 bg-success-subtle text-success">
                            <span class="d-block small" style="font-size: 0.75rem;">{{ __('Sent') }}</span>
                            <strong class="font-monospace fs-6" id="liveSentCount">0</strong>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 rounded-3 bg-danger-subtle text-danger">
                            <span class="d-block small" style="font-size: 0.75rem;">{{ __('Failed') }}</span>
                            <strong class="font-monospace fs-6" id="liveFailedCount">0</strong>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 rounded-3 bg-light text-muted">
                            <span class="d-block small" style="font-size: 0.75rem;">{{ __('Remaining') }}</span>
                            <strong class="font-monospace fs-6" id="liveRemainingCount">0</strong>
                        </div>
                    </div>
                </div>

                {{-- Streaming Activity Log Window --}}
                <h6 class="fw-bold small text-muted mb-2">
                    <i class="bi bi-terminal me-1"></i>{{ __('messages.whatsapp_live_progress_logs') }}
                </h6>
                <div id="liveLogWindow" class="p-3 rounded-4 font-monospace bg-dark text-light small overflow-y-auto" style="height: 140px; font-size: 0.78rem; line-height: 1.5;">
                    <div class="text-secondary">Waiting for activity logs...</div>
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">{{ __('No broadcasts dispatched yet.') }}</td>
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
                                    </div>
                                </div>
                                <textarea name="default_message" id="defaultMessageInput" rows="3" class="form-control rounded-3" placeholder="مرحباً {name}، نود تذكيرك بموعد الاجتماع القادم..."></textarea>
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
                                            <select name="anti_ban_profile" class="form-select form-select-sm rounded-3">
                                                <option value="safe" selected>{{ __('messages.whatsapp_antiban_safe') }}</option>
                                                <option value="ultra_safe">{{ __('messages.whatsapp_antiban_ultra_safe') }}</option>
                                                <option value="fast">{{ __('messages.whatsapp_antiban_fast') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check mt-3">
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

            // Real-Time Live Campaign Tracker Polling
            const liveTracker = document.getElementById('liveCampaignTracker');
            let trackerInterval = null;
            let currentBroadcastId = liveTracker.getAttribute('data-active-id');

            function startTrackerPolling(id) {
                currentBroadcastId = id;
                liveTracker.style.display = 'block';
                clearInterval(trackerInterval);

                trackerInterval = setInterval(() => {
                    fetch(`{{ url('/whatsapp/broadcasts') }}/${id}/progress`)
                        .then(res => res.json())
                        .then(data => {
                            document.getElementById('livePercentText').textContent = data.percent + '%';
                            document.getElementById('liveProgressBar').style.width = data.percent + '%';
                            document.getElementById('liveTotalCount').textContent = data.total;
                            document.getElementById('liveSentCount').textContent = data.successful;
                            document.getElementById('liveFailedCount').textContent = data.failed;
                            document.getElementById('liveRemainingCount').textContent = Math.max(0, data.total - (data.successful + data.failed));

                            // Update logs
                            if (data.logs && data.logs.length > 0) {
                                const logWin = document.getElementById('liveLogWindow');
                                logWin.innerHTML = data.logs.map(l => `<div>${l}</div>`).join('');
                                logWin.scrollTop = logWin.scrollHeight;
                            }

                            if (data.is_finished) {
                                clearInterval(trackerInterval);
                                document.getElementById('liveProgressBar').className = 'progress-bar bg-success';
                                document.getElementById('liveTrackerBadge').className = 'badge bg-success rounded-pill ms-2';
                                document.getElementById('liveTrackerBadge').textContent = 'Completed';
                            }
                        })
                        .catch(() => {});
                }, 2500);
            }

            if (currentBroadcastId) {
                startTrackerPolling(currentBroadcastId);
            }

            // Cancel Campaign handler
            const btnCancel = document.getElementById('btnCancelCampaign');
            if (btnCancel) {
                btnCancel.addEventListener('click', function () {
                    if (!currentBroadcastId) return;
                    if (!confirm('{{ __("messages.whatsapp_cancel_broadcast") }}?')) return;

                    fetch(`{{ url('/whatsapp/broadcasts') }}/${currentBroadcastId}/cancel`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(() => {
                        clearInterval(trackerInterval);
                        alert('{{ __("messages.whatsapp_broadcast_cancelled") }}');
                        window.location.reload();
                    });
                });
            }
        });
    </script>
    @endpush
</x-layout>
