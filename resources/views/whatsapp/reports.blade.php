<x-layout>
    <x-backhead>{{ __('messages.whatsapp_reports') }}</x-backhead>

    <div class="container-fluid px-4 py-3">
        {{-- Header & Subtitle --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: var(--text-primary);">
                    <i class="bi bi-graph-up text-success me-2"></i>{{ __('messages.whatsapp_reports') }}
                </h3>
                <p class="text-muted mb-0 small">
                    {{ __('messages.whatsapp_reports_desc') }}
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('whatsapp.reports.export', request()->all()) }}" class="btn btn-outline-success rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                    <span>{{ __('messages.Export CSV') }}</span>
                </a>
                <a href="{{ route('whatsapp.inbox') }}" class="btn btn-outline-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-chat-dots-fill"></i>
                    <span>{{ __('messages.whatsapp_inbox') }}</span>
                </a>
            </div>
        </div>

        {{-- Filters & Controls Bar --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--card-bg, #ffffff);">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('whatsapp.reports.index') }}" class="row g-3 align-items-end" id="filterForm">
                    <div class="col-lg-6 col-md-8">
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

                    <div class="col-lg-3 col-md-4" id="customDateContainer" style="{{ $preset === 'custom' ? '' : 'display: none;' }}">
                        <div class="row g-2">
                            <div class="col-12 col-sm-6">
                                <label class="form-label small text-muted fw-semibold mb-1">{{ __('messages.From') }}</label>
                                <input type="date" name="start_date" class="form-control form-control-sm rounded-3" value="{{ $start->format('Y-m-d') }}">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label small text-muted fw-semibold mb-1">{{ __('messages.To') }}</label>
                                <input type="date" name="end_date" class="form-control form-control-sm rounded-3" value="{{ $end->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    @if($isDev)
                        <div class="col-lg-3 col-md-4">
                            <div class="form-check form-switch mb-1">
                                <input class="form-check-input" type="checkbox" name="include_dev" id="includeDevSwitch" value="1" {{ $includeDev ? 'checked' : '' }} onchange="this.form.submit()">
                                <label class="form-check-label small text-muted" for="includeDevSwitch">
                                    {{ __('Include egyptna.org Dev Logs') }}
                                </label>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Executive KPI Metrics Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-3">
                        <span class="text-muted small d-block mb-1">{{ __('messages.whatsapp_total_conversations') }}</span>
                        <h4 class="fw-bold mb-0 text-primary">{{ number_format($kpis['total_conversations']) }}</h4>
                        <span class="text-muted" style="font-size: 0.72rem;">{{ __('messages.whatsapp_unique_contacts_engaged') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-3">
                        <span class="text-muted small d-block mb-1">{{ __('messages.whatsapp_total_messages') }}</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($kpis['total_messages']) }}</h4>
                        <span class="text-muted" style="font-size: 0.72rem;">
                            📥 {{ $kpis['inbound_messages'] }} | 📤 {{ $kpis['outbound_messages'] }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-3">
                        <span class="text-muted small d-block mb-1">{{ __('messages.whatsapp_bot_automation_rate') }}</span>
                        <h4 class="fw-bold mb-0 text-success">{{ $kpis['automation_rate'] }}%</h4>
                        <span class="text-muted" style="font-size: 0.72rem;">{{ __('messages.whatsapp_handled_without_agent') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-3">
                        <span class="text-muted small d-block mb-1">{{ __('messages.whatsapp_live_volunteer_chats') }}</span>
                        <h4 class="fw-bold mb-0 text-warning">{{ number_format($kpis['live_agent_conversations']) }}</h4>
                        <span class="text-muted" style="font-size: 0.72rem;">
                            {{ $kpis['agent_replies'] }} {{ __('messages.whatsapp_manual_replies') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-3">
                        <span class="text-muted small d-block mb-1">{{ __('messages.whatsapp_active_jft_subscribers') }}</span>
                        <h4 class="fw-bold mb-0 text-info">{{ number_format($kpis['active_subscribers']) }}</h4>
                        <span class="text-success small" style="font-size: 0.72rem;">
                            +{{ $kpis['new_subscribers_period'] }} {{ __('messages.whatsapp_this_period') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-3">
                        <span class="text-muted small d-block mb-1">{{ __('messages.whatsapp_avg_volunteer_response') }}</span>
                        <h4 class="fw-bold mb-0 text-secondary">{{ $kpis['avg_response_minutes'] }} <small class="fs-6">min</small></h4>
                        <span class="text-muted" style="font-size: 0.72rem;">{{ __('messages.whatsapp_turnaround_speed') }}</span>
                    </div>
                </div>
            </div>
        </div>

        @php
            $hasVolumeData = (array_sum($volumeTrends['inbound'] ?? []) + array_sum($volumeTrends['bot'] ?? []) + array_sum($volumeTrends['agent'] ?? [])) > 0;
            $hasCategoryData = array_sum($categoryBreakdown['data'] ?? []) > 0;
            $totalCategoryInquiries = array_sum($categoryBreakdown['data'] ?? []);
        @endphp

        {{-- Visual Charts Row --}}
        <div class="row g-4 mb-4">
            {{-- Message Volume Trend Chart --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-bar-chart-fill text-primary me-2"></i>{{ __('messages.whatsapp_daily_volume_trend') }}
                            </h5>
                        </div>
                        @if($hasVolumeData)
                            <div style="position: relative; height: 320px; width: 100%;">
                                <canvas id="volumeChart"></canvas>
                            </div>
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center text-center p-4 rounded-3 bg-light-subtle border border-dashed" style="height: 320px;">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3 text-secondary" style="width: 56px; height: 56px;">
                                    <i class="bi bi-bar-chart fs-3"></i>
                                </div>
                                <h6 class="fw-semibold text-secondary mb-1">{{ __('messages.whatsapp_no_activity_period') }}</h6>
                                <p class="text-muted small mb-0">{{ __('messages.whatsapp_reports_desc') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Service Breakdown Donut Chart --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-pie-chart-fill text-success me-2"></i>{{ __('messages.whatsapp_service_breakdown') }}
                            </h5>
                            @if($hasCategoryData)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                                    {{ $totalCategoryInquiries }} {{ __('messages.whatsapp_total_inquiries') }}
                                </span>
                            @endif
                        </div>
                        @if($hasCategoryData)
                            <div style="position: relative; height: 320px; width: 100%;">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center text-center p-4 rounded-3 bg-light-subtle border border-dashed" style="height: 320px;">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3 text-secondary" style="width: 56px; height: 56px;">
                                    <i class="bi bi-pie-chart fs-3"></i>
                                </div>
                                <h6 class="fw-semibold text-secondary mb-1">{{ __('messages.whatsapp_no_category_period') }}</h6>
                                <p class="text-muted small mb-0">{{ __('messages.whatsapp_reports_desc') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Volunteer Performance & Helpline Activity --}}
        <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg, #ffffff);">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-person-check-fill text-primary me-2"></i>{{ __('messages.whatsapp_helpline_summary') }}
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>{{ __('messages.Name') }}</th>
                                <th>{{ __('messages.Email') }}</th>
                                <th>{{ __('messages.whatsapp_roles_permissions') }}</th>
                                <th>{{ __('messages.whatsapp_manual_replies_sent') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($volunteerActivity as $user)
                                <tr>
                                    <td class="fw-bold">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                {{ mb_substr($user->name, 0, 1) }}
                                            </div>
                                            <span>{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted small">{{ $user->email }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-light text-dark border me-1">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <span class="badge bg-success text-white rounded-pill px-3 py-1">
                                            {{ $user->whatsapp_replies_count }} {{ __('messages.whatsapp_msg_count') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        {{ __('messages.whatsapp_no_volunteer_activity') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('assets/js/chart.js') }}"></script>
    <script>
        function initWhatsAppCharts() {
            if (typeof Chart === 'undefined') {
                console.warn('Chart.js library is not loaded.');
                return;
            }

            // Volume Chart
            const volumeEl = document.getElementById('volumeChart');
            if (volumeEl) {
                const volumeCtx = volumeEl.getContext('2d');
                new Chart(volumeCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($volumeTrends['labels']),
                        datasets: [
                            {
                                label: "{{ __('messages.whatsapp_inbound_messages') }}",
                                data: @json($volumeTrends['inbound']),
                                backgroundColor: 'rgba(59, 130, 246, 0.75)',
                                borderColor: '#3b82f6',
                                borderWidth: 1,
                                borderRadius: 4
                            },
                            {
                                label: "{{ __('messages.whatsapp_bot_automated_replies') }}",
                                data: @json($volumeTrends['bot']),
                                backgroundColor: 'rgba(16, 185, 129, 0.75)',
                                borderColor: '#10b981',
                                borderWidth: 1,
                                borderRadius: 4
                            },
                            {
                                label: "{{ __('messages.whatsapp_volunteer_live_replies') }}",
                                data: @json($volumeTrends['agent']),
                                backgroundColor: 'rgba(245, 158, 11, 0.75)',
                                borderColor: '#f59e0b',
                                borderWidth: 1,
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        scales: {
                            x: { stacked: true },
                            y: { stacked: true, beginAtZero: true }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: { boxWidth: 12, font: { size: 11 } }
                            }
                        }
                    }
                });
            }

            // Category Donut Chart
            const catEl = document.getElementById('categoryChart');
            if (catEl) {
                const catCtx = catEl.getContext('2d');
                new Chart(catCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($categoryBreakdown['labels']),
                        datasets: [{
                            data: @json($categoryBreakdown['data']),
                            backgroundColor: [
                                '#10b981', // JFT
                                '#3b82f6', // Meetings
                                '#ef4444', // Helpline
                                '#f59e0b', // Events
                                '#8b5cf6', // Forms
                                '#06b6d4', // Social
                                '#6366f1', // Subscription
                                '#94a3b8'  // Menu
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    font: { size: 11 },
                                    padding: 12
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initWhatsAppCharts);
        } else {
            initWhatsAppCharts();
        }

        function toggleCustomDates(show) {
            const container = document.getElementById('customDateContainer');
            if (container) {
                container.style.display = show ? 'block' : 'none';
            }
        }
    </script>
    @endpush
</x-layout>
