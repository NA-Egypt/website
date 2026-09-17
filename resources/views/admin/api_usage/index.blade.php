<x-layout>
    <x-backhead>{{ __('messages.api_analytics_title') }}</x-backhead>

    <div class="container-fluid px-4 py-3">
        {{-- Header & Subtitle --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: var(--text-primary);">
                    <i class="bi bi-cpu-fill text-primary me-2"></i>{{ __('messages.api_analytics_title') }}
                </h3>
                <p class="text-muted mb-0 small">
                    {{ __('messages.api_analytics_subtitle') }}
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.api_usage.export', request()->all()) }}" class="btn btn-outline-success rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                    <span>{{ __('messages.Export CSV') }}</span>
                </a>
                <a href="{{ route('admin.api_usage.index') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2" title="{{ __('messages.Reset') }}">
                    <i class="bi bi-arrow-clockwise"></i>
                    <span>{{ __('messages.Reset') }}</span>
                </a>
            </div>
        </div>

        {{-- Filters & Controls Bar --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--card-bg, #ffffff);">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.api_usage.index') }}" class="row g-3 align-items-end" id="filterForm">
                    {{-- Date Presets --}}
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label small text-muted fw-semibold mb-1">{{ __('messages.Reporting Cycle') ?? 'Period' }}</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="preset" id="presetToday" value="today" {{ $preset === 'today' ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="btn btn-outline-primary btn-sm py-2" for="presetToday">{{ __('messages.Today') }}</label>

                            <input type="radio" class="btn-check" name="preset" id="preset7d" value="7d" {{ $preset === '7d' ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="btn btn-outline-primary btn-sm py-2" for="preset7d">{{ __('messages.Last 7 Days') }}</label>

                            <input type="radio" class="btn-check" name="preset" id="preset30d" value="30d" {{ $preset === '30d' ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="btn btn-outline-primary btn-sm py-2" for="preset30d">{{ __('messages.Last 30 Days') }}</label>

                            <input type="radio" class="btn-check" name="preset" id="presetCustom" value="custom" {{ $preset === 'custom' ? 'checked' : '' }} onclick="toggleCustomDates(true)">
                            <label class="btn btn-outline-primary btn-sm py-2" for="presetCustom">{{ __('messages.Custom Range') }}</label>
                        </div>
                    </div>

                    {{-- Platform Selector --}}
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <label class="form-label small text-muted fw-semibold mb-1">{{ __('messages.Platform') }}</label>
                        <select name="platform" class="form-select form-select-sm py-2 rounded-3" onchange="this.form.submit()">
                            <option value="all" {{ $platform === 'all' ? 'selected' : '' }}>{{ __('messages.All Platforms') }}</option>
                            <option value="android" {{ $platform === 'android' ? 'selected' : '' }}>{{ __('messages.Android App') }}</option>
                            <option value="ios" {{ $platform === 'ios' ? 'selected' : '' }}>{{ __('messages.iOS App') }}</option>
                            <option value="web" {{ $platform === 'web' ? 'selected' : '' }}>{{ __('messages.Web & Other') }}</option>
                        </select>
                    </div>

                    {{-- Status Selector --}}
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <label class="form-label small text-muted fw-semibold mb-1">{{ __('messages.Status') }}</label>
                        <select name="status" class="form-select form-select-sm py-2 rounded-3" onchange="this.form.submit()">
                            <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>{{ __('messages.All Statuses') }}</option>
                            <option value="success" {{ $statusFilter === 'success' ? 'selected' : '' }}>{{ __('messages.Success') }} (2xx)</option>
                            <option value="errors" {{ $statusFilter === 'errors' ? 'selected' : '' }}>{{ __('messages.Errors Only') }} (4xx/5xx)</option>
                        </select>
                    </div>

                    {{-- Search Input --}}
                    <div class="col-lg-4 col-md-12">
                        <label class="form-label small text-muted fw-semibold mb-1">{{ __('messages.Search') ?? 'Search' }}</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" value="{{ $search }}" class="form-control py-2 rounded-start-3" placeholder="{{ __('messages.Search endpoint, IP, version...') }}">
                            <button type="submit" class="btn btn-primary px-3 rounded-end-3">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Custom Date Inputs Row (Collapsible) --}}
                    <div class="col-12 {{ $preset === 'custom' ? '' : 'd-none' }}" id="customDateRow">
                        <div class="p-3 bg-light rounded-3 d-flex flex-wrap align-items-center gap-3 border">
                            <div class="d-flex align-items-center gap-2">
                                <label class="small text-muted fw-bold mb-0">{{ __('messages.From Date') }}:</label>
                                <input type="date" name="start_date" value="{{ $startDate->toDateString() }}" class="form-control form-control-sm">
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <label class="small text-muted fw-bold mb-0">{{ __('messages.To Date') }}:</label>
                                <input type="date" name="end_date" value="{{ $endDate->toDateString() }}" class="form-control form-control-sm">
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">
                                {{ __('messages.Apply') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- KPI Cards Grid --}}
        <div class="row g-3 mb-4">
            {{-- Total Calls --}}
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, rgba(50,85,127,0.08) 0%, rgba(50,85,127,0.02) 100%);">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">{{ __('messages.Total API Calls') }}</div>
                            <h2 class="fw-bold my-1 text-dark">{{ number_format($totalRequests) }}</h2>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2">
                                <i class="bi bi-broadcast me-1"></i>{{ $preset === 'today' ? __('messages.Today') : ($preset === '7d' ? '7 Days' : 'Period') }}
                            </span>
                        </div>
                        <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(50,85,127,0.15); color: #32557f;">
                            <i class="bi bi-hdd-network fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile Traffic --}}
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, rgba(16,179,207,0.10) 0%, rgba(16,179,207,0.02) 100%);">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">{{ __('messages.Mobile Traffic') }}</div>
                            <div class="d-flex align-items-baseline gap-2 my-1">
                                <h2 class="fw-bold mb-0 text-dark">{{ number_format($mobileCount) }}</h2>
                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">{{ $mobilePercentage }}%</span>
                            </div>
                            <div class="d-flex gap-2 small">
                                <span class="text-muted"><i class="bi bi-android2 text-success"></i> {{ number_format($androidCount) }}</span>
                                <span class="text-muted"><i class="bi bi-apple text-dark"></i> {{ number_format($iosCount) }}</span>
                            </div>
                        </div>
                        <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(16,179,207,0.15); color: #0891b2;">
                            <i class="bi bi-phone-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Avg Latency --}}
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, rgba(16,185,129,0.08) 0%, rgba(16,185,129,0.02) 100%);">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">{{ __('messages.Avg Latency') }}</div>
                            <h2 class="fw-bold my-1 text-dark">{{ $avgLatency }} <span class="fs-6 fw-normal text-muted">ms</span></h2>
                            @if($avgLatency <= 200)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">
                                    <i class="bi bi-lightning-charge-fill me-1"></i>Optimal Fast
                                </span>
                            @elseif($avgLatency <= 500)
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2">
                                    <i class="bi bi-clock-history me-1"></i>Moderate
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2">
                                    <i class="bi bi-exclamation-octagon-fill me-1"></i>High Latency
                                </span>
                            @endif
                        </div>
                        <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(16,185,129,0.15); color: #059669;">
                            <i class="bi bi-speedometer2 fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Error Rate & Success --}}
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, {{ $totalErrors > 0 ? 'rgba(239,68,68,0.08)' : 'rgba(16,185,129,0.08)' }} 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">{{ __('messages.Error Rate') }}</div>
                            <div class="d-flex align-items-baseline gap-2 my-1">
                                <h2 class="fw-bold mb-0 {{ $totalErrors > 0 ? 'text-danger' : 'text-success' }}">{{ $errorRate }}%</h2>
                                <span class="small text-muted">({{ number_format($totalErrors) }} err)</span>
                            </div>
                            <div class="small text-muted">
                                <i class="bi bi-people me-1"></i>{{ number_format($uniqueIps) }} {{ __('messages.Unique Clients') }}
                            </div>
                        </div>
                        <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: {{ $totalErrors > 0 ? 'rgba(239,68,68,0.15)' : 'rgba(16,185,129,0.15)' }}; color: {{ $totalErrors > 0 ? '#dc2626' : '#059669' }};">
                            <i class="bi bi-shield-check fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Visual Charts Grid --}}
        <div class="row g-4 mb-4">
            {{-- Timeline Chart --}}
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-graph-up-arrow text-primary me-2"></i>{{ __('messages.Traffic Over Time') }}
                        </h5>
                        <div class="d-flex gap-3 small">
                            <span class="d-flex align-items-center gap-1"><span class="badge rounded-circle p-1" style="background: #32557f;"> </span> {{ __('messages.Total API Calls') }}</span>
                            <span class="d-flex align-items-center gap-1"><span class="badge rounded-circle p-1" style="background: #10b3cf;"> </span> {{ __('messages.Mobile Traffic') }}</span>
                            <span class="d-flex align-items-center gap-1"><span class="badge rounded-circle p-1" style="background: #94a3b8;"> </span> {{ __('messages.Web & Other') }}</span>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div style="height: 300px;">
                            <canvas id="timelineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Platform Distribution Doughnut --}}
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-0 pt-3 px-4">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-pie-chart-fill text-info me-2"></i>{{ __('messages.Platform Distribution') }}
                        </h5>
                    </div>
                    <div class="card-body px-4 pb-4 d-flex flex-column align-items-center justify-content-center">
                        <div style="width: 220px; height: 220px;">
                            <canvas id="platformChart"></canvas>
                        </div>
                        <div class="w-100 mt-3">
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <span class="small"><i class="bi bi-android2 text-success me-1"></i> {{ __('messages.Android App') }}</span>
                                <span class="fw-bold small">{{ number_format($androidCount) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <span class="small"><i class="bi bi-apple text-dark me-1"></i> {{ __('messages.iOS App') }}</span>
                                <span class="fw-bold small">{{ number_format($iosCount) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-1">
                                <span class="small"><i class="bi bi-globe2 text-secondary me-1"></i> {{ __('messages.Web & Other') }}</span>
                                <span class="fw-bold small">{{ number_format($webCount + $otherCount) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Endpoints & Status Distribution --}}
        <div class="row g-4 mb-4">
            {{-- Top Endpoints --}}
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-0 pt-3 px-4">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-fire text-danger me-2"></i>{{ __('messages.Top API Endpoints') }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="small text-muted text-uppercase">
                                        <th class="ps-4">{{ __('messages.Method') }}</th>
                                        <th>{{ __('messages.Endpoint') }}</th>
                                        <th class="text-center">{{ __('messages.Total API Calls') }}</th>
                                        <th class="pe-4 text-end">{{ __('messages.Latency') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topEndpoints as $ep)
                                        <tr>
                                            <td class="ps-4">
                                                <span class="badge {{ match(strtoupper($ep->method)) {
                                                    'GET' => 'bg-info-subtle text-info border border-info-subtle',
                                                    'POST' => 'bg-success-subtle text-success border border-success-subtle',
                                                    'PUT', 'PATCH' => 'bg-warning-subtle text-warning border border-warning-subtle',
                                                    'DELETE' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                                    default => 'bg-secondary-subtle text-secondary',
                                                } }} px-2 py-1">
                                                    {{ $ep->method }}
                                                </span>
                                            </td>
                                            <td class="font-monospace small fw-semibold text-dark">{{ $ep->endpoint }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border px-3 py-1 fw-bold rounded-pill">
                                                    {{ number_format($ep->count) }}
                                                </span>
                                            </td>
                                            <td class="pe-4 text-end small text-muted font-monospace">
                                                {{ round($ep->avg_latency, 0) }} ms
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                {{ __('messages.No API logs found') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Breakdown Card --}}
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-0 pt-3 px-4">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-activity text-success me-2"></i>{{ __('messages.Status Codes') }}
                        </h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small fw-semibold mb-1">
                                <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> 2xx Success</span>
                                <span>{{ number_format($statusDistribution['2xx']) }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalRequests > 0 ? ($statusDistribution['2xx'] / $totalRequests * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between small fw-semibold mb-1">
                                <span class="text-info"><i class="bi bi-arrow-repeat me-1"></i> 3xx Redirection</span>
                                <span>{{ number_format($statusDistribution['3xx']) }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $totalRequests > 0 ? ($statusDistribution['3xx'] / $totalRequests * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between small fw-semibold mb-1">
                                <span class="text-warning"><i class="bi bi-exclamation-triangle-fill me-1"></i> 4xx Client Errors</span>
                                <span>{{ number_format($statusDistribution['4xx']) }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $totalRequests > 0 ? ($statusDistribution['4xx'] / $totalRequests * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="d-flex justify-content-between small fw-semibold mb-1">
                                <span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i> 5xx Server Errors</span>
                                <span>{{ number_format($statusDistribution['5xx']) }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $totalRequests > 0 ? ($statusDistribution['5xx'] / $totalRequests * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Live Request Logs Table --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-transparent border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-list-columns text-primary me-2"></i>{{ __('messages.API Request Logs') }}
                </h5>
                <span class="small text-muted">{{ __('messages.Showing') }} {{ $logs->firstItem() ?? 0 }} {{ __('messages.to') }} {{ $logs->lastItem() ?? 0 }} {{ __('messages.of') }} {{ $logs->total() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted text-uppercase">
                                <th class="ps-4">{{ __('messages.Timestamp') }}</th>
                                <th>{{ __('messages.Method') }}</th>
                                <th>{{ __('messages.Endpoint') }}</th>
                                <th>{{ __('messages.Status') }}</th>
                                <th>{{ __('messages.Platform') }}</th>
                                <th>{{ __('messages.App Version') }}</th>
                                <th>{{ __('messages.Latency') }}</th>
                                <th>{{ __('messages.Client IP') }}</th>
                                <th>{{ __('messages.User') }}</th>
                                <th class="pe-4 text-center">{{ __('messages.Actions') ?? 'Details' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td class="ps-4 small text-muted font-monospace">
                                        {{ $log->created_at->format('M d, H:i:s') }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $log->method_badge_class }} px-2 py-1">
                                            {{ $log->method }}
                                        </span>
                                    </td>
                                    <td class="font-monospace small text-dark fw-semibold" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->endpoint }}">
                                        {{ $log->endpoint }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $log->status_badge_class }} rounded-pill px-2">
                                            {{ $log->status_code }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1">
                                            <i class="bi {{ $log->platform_icon }}"></i>
                                            <span class="text-capitalize">{{ $log->platform }}</span>
                                        </span>
                                    </td>
                                    <td class="small font-monospace text-muted">
                                        {{ $log->app_version ?: '-' }}
                                    </td>
                                    <td class="small font-monospace {{ $log->response_time_ms > 500 ? 'text-danger fw-bold' : 'text-muted' }}">
                                        {{ $log->response_time_ms }} ms
                                    </td>
                                    <td class="small font-monospace text-muted">
                                        {{ $log->ip_address ?: '-' }}
                                    </td>
                                    <td class="small">
                                        @if($log->user)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" title="{{ $log->user->email }}">
                                                <i class="bi bi-person-fill me-1"></i>{{ $log->user->name }}
                                            </span>
                                        @else
                                            <span class="text-muted small">Guest</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-center">
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}" title="Inspect Request">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        {{-- Inspect Modal --}}
                                        <div class="modal fade text-start" id="logModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 border-0 shadow">
                                                    <div class="modal-header border-0 pb-0">
                                                        <h5 class="modal-title fw-bold">
                                                            <i class="bi bi-info-circle-fill text-primary me-2"></i>{{ __('messages.API Request Logs') }} #{{ $log->id }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <dl class="row mb-0 small">
                                                            <dt class="col-sm-4 text-muted">{{ __('messages.Timestamp') }}:</dt>
                                                            <dd class="col-sm-8 font-monospace">{{ $log->created_at->toDateTimeString() }}</dd>

                                                            <dt class="col-sm-4 text-muted">{{ __('messages.Method') }} & {{ __('messages.Status') }}:</dt>
                                                            <dd class="col-sm-8">
                                                                <span class="badge {{ $log->method_badge_class }}">{{ $log->method }}</span>
                                                                <span class="badge {{ $log->status_badge_class }}">{{ $log->status_code }}</span>
                                                            </dd>

                                                            <dt class="col-sm-4 text-muted">{{ __('messages.Endpoint') }}:</dt>
                                                            <dd class="col-sm-8 font-monospace text-break">{{ $log->endpoint }}</dd>

                                                            @if($log->route_name)
                                                                <dt class="col-sm-4 text-muted">Route Name:</dt>
                                                                <dd class="col-sm-8 font-monospace text-muted">{{ $log->route_name }}</dd>
                                                            @endif

                                                            <dt class="col-sm-4 text-muted">{{ __('messages.Latency') }}:</dt>
                                                            <dd class="col-sm-8 font-monospace">{{ $log->response_time_ms }} ms</dd>

                                                            <dt class="col-sm-4 text-muted">{{ __('messages.Platform') }}:</dt>
                                                            <dd class="col-sm-8 text-capitalize"><i class="bi {{ $log->platform_icon }} me-1"></i> {{ $log->platform }}</dd>

                                                            <dt class="col-sm-4 text-muted">{{ __('messages.App Version') }}:</dt>
                                                            <dd class="col-sm-8 font-monospace">{{ $log->app_version ?: 'N/A' }}</dd>

                                                            @if($log->device_id)
                                                                <dt class="col-sm-4 text-muted">Device ID:</dt>
                                                                <dd class="col-sm-8 font-monospace text-break">{{ $log->device_id }}</dd>
                                                            @endif

                                                            <dt class="col-sm-4 text-muted">{{ __('messages.Client IP') }}:</dt>
                                                            <dd class="col-sm-8 font-monospace">{{ $log->ip_address ?: 'N/A' }}</dd>

                                                            <dt class="col-sm-4 text-muted">{{ __('messages.User') }}:</dt>
                                                            <dd class="col-sm-8">{{ $log->user ? $log->user->name . ' (' . $log->user->email . ')' : 'Guest (Unauthenticated)' }}</dd>

                                                            <dt class="col-sm-4 text-muted">User-Agent:</dt>
                                                            <dd class="col-sm-8 font-monospace text-break text-muted">{{ $log->user_agent ?: 'N/A' }}</dd>
                                                        </dl>
                                                    </div>
                                                    <div class="modal-footer border-0 pt-0">
                                                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">{{ __('messages.Close') ?? 'Close' }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                        {{ __('messages.No API logs found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($logs->hasPages())
                <div class="card-footer bg-transparent border-0 py-3 px-4 d-flex justify-content-center">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Chart.js Script --}}
    <script src="{{ asset('assets/js/chart.js') }}"></script>
    <script>
        function toggleCustomDates(show) {
            var row = document.getElementById('customDateRow');
            if (row) {
                if (show) {
                    row.classList.remove('d-none');
                } else {
                    row.classList.add('d-none');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // 1. Timeline Chart
            var timelineCtx = document.getElementById('timelineChart');
            if (timelineCtx) {
                new Chart(timelineCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: @json($timelineLabels),
                        datasets: [
                            {
                                label: '{{ __('messages.Total API Calls') }}',
                                data: @json($timelineTotalData),
                                borderColor: '#32557f',
                                backgroundColor: 'rgba(50, 85, 127, 0.08)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 6,
                            },
                            {
                                label: '{{ __('messages.Mobile Traffic') }}',
                                data: @json($timelineMobileData),
                                borderColor: '#10b3cf',
                                backgroundColor: 'rgba(16, 179, 207, 0.08)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 6,
                            },
                            {
                                label: '{{ __('messages.Web & Other') }}',
                                data: @json($timelineWebData),
                                borderColor: '#94a3b8',
                                backgroundColor: 'transparent',
                                borderDash: [4, 4],
                                tension: 0.35,
                                borderWidth: 1.5,
                                pointRadius: 2,
                                pointHoverRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                padding: 10,
                                cornerRadius: 8,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // 2. Platform Distribution Doughnut Chart
            var platformCtx = document.getElementById('platformChart');
            if (platformCtx) {
                new Chart(platformCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: [
                            '{{ __('messages.Android App') }}',
                            '{{ __('messages.iOS App') }}',
                            '{{ __('messages.Web & Other') }}'
                        ],
                        datasets: [{
                            data: [
                                {{ $androidCount }},
                                {{ $iosCount }},
                                {{ $webCount + $otherCount }}
                            ],
                            backgroundColor: [
                                '#10b981', // Emerald for Android
                                '#32557f', // Navy for iOS
                                '#94a3b8'  // Slate for Web
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                cornerRadius: 8
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-layout>
