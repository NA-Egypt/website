<x-layout>
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm 10mm;
            }
            body * {
                visibility: hidden;
            }
            #charts-pane, #charts-pane * {
                visibility: visible;
            }
            html, body, .container-fluid, .page-content, .wrapper, .page-wrapper {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                box-shadow: none !important;
                border: none !important;
                background: transparent !important;
            }
            #charts-pane {
                position: absolute;
                left: 0;
                top: 0;
                width: 100% !important;
                background: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            #analytics-export-wrapper {
                display: none !important;
            }
            .glass-card {
                page-break-inside: avoid;
                break-inside: avoid;
                background: #ffffff !important;
                border: 1px solid #cbd5e1 !important;
                box-shadow: none !important;
                margin-bottom: 25px !important;
                padding: 20px !important;
                width: 100% !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .chart-container {
                height: 250px !important;
                max-height: 250px !important;
                width: 100% !important;
            }
            .row {
                display: flex !important;
                flex-wrap: wrap !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            .col-md-6 {
                width: 100% !important;
                flex: 0 0 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin-bottom: 20px !important;
            }
            .col-12 {
                width: 100% !important;
                flex: 0 0 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
            }
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            th, td {
                padding: 6px 8px !important;
                font-size: 11px !important;
                border: 1px solid #cbd5e1 !important;
            }
            th {
                background-color: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

    <x-backhead>{{ __('messages.Form Reports') ?? 'Form Reports' }}</x-backhead>

    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0 fw-bold" style="color: var(--text-primary);">{{ $form->title }} - {{ __('messages.Submissions Report') ?? 'Submissions Report' }}</h4>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('forms.csv', $form->id) }}" class="btn btn-outline-success rounded-pill px-4 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i> {{ __('messages.Export CSV') ?? 'Export CSV' }}
                </a>
                <a href="{{ route('forms.reportPdf', $form->id) }}" class="btn btn-outline-danger rounded-pill px-4 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-pdf-fill"></i> {{ __('messages.Download Report PDF') ?? 'Download PDF' }}
                </a>
                <a href="{{ route('forms.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left"></i> {{ __('messages.Back to Dashboard') ?? 'Back' }}
                </a>
            </div>
        </div>

        <!-- Premium Glassmorphic Stats Grid -->
        <div class="row g-3 mb-4">
            <div class="col-md-4 col-sm-6">
                <div class="glass-card p-3 d-flex align-items-center gap-3" style="background: rgba(255,255,255,0.4) !important; border: 1px solid var(--glass-border) !important;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary shadow-sm" style="width: 48px; height: 48px; background-color: rgba(37, 99, 235, 0.1) !important;">
                        <i class="bi bi-eye-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-semibold" style="font-size: 0.8rem;">{{ __('messages.Total Views') ?? 'Total Views' }}</div>
                        <h5 class="mb-0 fw-bold text-dark mt-1">{{ $form->views }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="glass-card p-3 d-flex align-items-center gap-3" style="background: rgba(255,255,255,0.4) !important; border: 1px solid var(--glass-border) !important;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success shadow-sm" style="width: 48px; height: 48px; background-color: rgba(16, 185, 129, 0.1) !important;">
                        <i class="bi bi-inbox-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-semibold" style="font-size: 0.8rem;">{{ __('messages.Submissions') ?? 'Submissions' }}</div>
                        <h5 class="mb-0 fw-bold text-dark mt-1">{{ $form->submissions->count() }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="glass-card p-3 d-flex align-items-center gap-3" style="background: rgba(255,255,255,0.4) !important; border: 1px solid var(--glass-border) !important;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning-subtle text-warning shadow-sm" style="width: 48px; height: 48px; background-color: rgba(245, 158, 11, 0.1) !important;">
                        <i class="bi bi-graph-up-arrow fs-5"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-semibold" style="font-size: 0.8rem;">{{ __('messages.Conversion') ?? 'Conversion' }}</div>
                        <h5 class="mb-0 fw-bold text-dark mt-1">{{ $form->conversion_rate }}%</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-pills mb-4 gap-2" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-4 fw-semibold shadow-sm d-flex align-items-center gap-2" id="submissions-tab" data-bs-toggle="pill" data-bs-target="#submissions-pane" type="button" role="tab" aria-controls="submissions-pane" aria-selected="true" style="transition: all 0.2s ease;">
                    <i class="bi bi-list-task"></i> {{ __('messages.Submissions List') ?? 'Submissions List' }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 fw-semibold shadow-sm d-flex align-items-center gap-2" id="charts-tab" data-bs-toggle="pill" data-bs-target="#charts-pane" type="button" role="tab" aria-controls="charts-pane" aria-selected="false" style="transition: all 0.2s ease;">
                    <i class="bi bi-bar-chart-line-fill"></i> {{ __('messages.Visual Analytics') ?? 'Visual Analytics' }}
                </button>
            </li>
        </ul>

        <div class="tab-content" id="reportTabsContent">
            <!-- Submissions List Tab Pane -->
            <div class="tab-pane fade show active" id="submissions-pane" role="tabpanel" aria-labelledby="submissions-tab" tabindex="0">
                <div class="glass-card p-4">
                    <div class="table-responsive" style="overflow: visible !important;">
                        <table class="table neo-table align-middle text-center display" id="submissions-table" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.Submission ID') ?? 'ID' }}</th>
                                    <th>{{ __('messages.Date') ?? 'Submitted At' }}</th>
                                    <th>{{ __('messages.Submitted By') ?? 'Submitted By' }}</th>
                                    <th>{{ __('messages.Actions') ?? 'Actions' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($submissions as $submission)
                                    <tr>
                                        <td class="fw-bold">#{{ $submission->id }}</td>
                                        <td class="text-secondary">{{ $submission->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="fw-semibold">
                                            {{ $submission->user ? $submission->user->name : (__('messages.Guest') ?? 'Guest') }}
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#submissionModal-{{ $submission->id }}">
                                                <i class="bi bi-eye-fill"></i> {{ __('messages.View Details') ?? 'View Details' }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Charts Tab Pane -->
            <div class="tab-pane fade" id="charts-pane" role="tabpanel" aria-labelledby="charts-tab" tabindex="0">
                @if (empty($chartData))
                    <div class="glass-card p-5 text-center text-secondary" style="background: rgba(255,255,255,0.4) !important; border: 1px solid var(--glass-border) !important;">
                        <i class="bi bi-bar-chart display-4 opacity-50 mb-3 text-primary"></i>
                        <h5 class="fw-bold text-dark">{{ __('messages.No Chartable Data') ?? 'No Chartable Data Available' }}</h5>
                        <p class="small mb-0 text-secondary">{{ __('messages.Charts require choice-based questions (Dropdown, Checkboxes, etc.) and at least one submission.') ?? 'Charts require choice-based questions (Dropdown, Checkboxes, etc.) and at least one submission.' }}</p>
                    </div>
                @else
                    <div class="d-flex justify-content-end mb-3" id="analytics-export-wrapper">
                        <button type="button" class="btn btn-outline-danger rounded-pill px-4 d-flex align-items-center gap-2" onclick="exportAnalyticsToPDF()">
                            <i class="bi bi-file-earmark-pdf-fill"></i> {{ __('messages.Download Analytics PDF') ?? 'Download Analytics PDF' }}
                        </button>
                    </div>
                    <div class="row g-4">
                        @foreach ($chartData as $cId => $chart)
                            @if ($chart['type'] === 'date')
                                <div class="col-12">
                                    <div class="glass-card p-4 shadow-sm border border-opacity-10" style="background: rgba(255,255,255,0.6) !important; border: 1px solid var(--glass-border) !important;">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.15rem; letter-spacing: -0.2px;">{{ $chart['label'] }}</h5>
                                            <span class="badge rounded-pill px-2.5 py-1 text-uppercase fw-semibold" style="font-size: 0.65rem; border: 1px solid rgba(139, 92, 246, 0.2); background-color: rgba(139, 92, 246, 0.08); color: rgb(139, 92, 246);">
                                                {{ __('messages.Date Field') ?? 'Date Field' }}
                                            </span>
                                        </div>

                                        <!-- Date Stats Grid -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-3 col-sm-6">
                                                <div class="p-3 rounded-3" style="background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.1);">
                                                    <div class="text-secondary small fw-semibold">{{ __('messages.Total Entries') ?? 'Total Entries' }}</div>
                                                    <h5 class="mb-0 fw-bold text-dark mt-1">{{ $chart['total_entries'] }}</h5>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6">
                                                <div class="p-3 rounded-3" style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.1);">
                                                    <div class="text-secondary small fw-semibold">{{ __('messages.Exact Total Elapsed') ?? 'Exact Total Elapsed' }}</div>
                                                    <h5 class="mb-0 fw-bold text-dark mt-1" style="font-size: 0.95rem;">{{ $chart['exact_total'] }}</h5>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6">
                                                <div class="p-3 rounded-3" style="background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.1);">
                                                    <div class="text-secondary small fw-semibold">{{ __('messages.Oldest Entry') ?? 'Biggest Elapsed (Oldest)' }}</div>
                                                    <h5 class="mb-0 fw-bold text-dark mt-1" style="font-size: 0.95rem;">{{ $chart['oldest_elapsed'] }}</h5>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6">
                                                <div class="p-3 rounded-3" style="background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.1);">
                                                    <div class="text-secondary small fw-semibold">{{ __('messages.Newest Entry') ?? 'Smallest Elapsed (Newest)' }}</div>
                                                    <h5 class="mb-0 fw-bold text-dark mt-1" style="font-size: 0.95rem;">{{ $chart['newest_elapsed'] }}</h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="fw-semibold text-secondary small mb-2">{{ __('messages.Elapsed Duration Distribution') ?? 'Elapsed Duration Distribution' }}</div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height: 260px; width: 100%;">
                                                    <canvas id="chart-{{ $chart['field_id'] }}"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="table-responsive">
                                                    <table class="table align-middle table-sm text-center">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ __('messages.Bracket') ?? 'Bracket' }}</th>
                                                                <th>{{ __('messages.Entries') ?? 'Entries' }}</th>
                                                                <th>{{ __('messages.Percentage') ?? 'Percentage' }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $totalEntries = array_sum($chart['data']);
                                                            @endphp
                                                            @foreach ($chart['labels'] as $idx => $label)
                                                                @php
                                                                    $val = $chart['data'][$idx];
                                                                    $percentage = $totalEntries > 0 ? round(($val / $totalEntries) * 100, 1) : 0;
                                                                @endphp
                                                                <tr>
                                                                    <td class="text-start fw-semibold text-secondary">{{ __('messages.' . $label) ?? $label }}</td>
                                                                    <td class="fw-bold">{{ $val }}</td>
                                                                    <td class="text-primary fw-semibold">{{ $percentage }}%</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-12">
                                    <div class="glass-card p-4 shadow-sm border border-opacity-10 d-flex flex-column" style="background: rgba(255,255,255,0.6) !important; border: 1px solid var(--glass-border) !important;">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem; letter-spacing: -0.2px;">{{ $chart['label'] }}</h5>
                                            <span class="badge rounded-pill px-2.5 py-1 text-uppercase fw-semibold" style="font-size: 0.65rem; border: 1px solid rgba(59, 130, 246, 0.2); background-color: rgba(59, 130, 246, 0.08); color: var(--text-primary);">
                                                {{ str_replace('_', ' ', $chart['type']) }}
                                            </span>
                                        </div>
                                        <div class="row g-3 flex-grow-1">
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height: 260px; width: 100%;">
                                                    <canvas id="chart-{{ $chart['field_id'] }}"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="table-responsive">
                                                    <table class="table align-middle table-sm text-center">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ __('messages.Option') ?? 'Option' }}</th>
                                                                <th>{{ __('messages.Submissions') ?? 'Submissions' }}</th>
                                                                <th>{{ __('messages.Percentage') ?? 'Percentage' }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $totalEntries = array_sum($chart['data']);
                                                            @endphp
                                                            @foreach ($chart['labels'] as $idx => $label)
                                                                @php
                                                                    $val = $chart['data'][$idx];
                                                                    $percentage = $totalEntries > 0 ? round(($val / $totalEntries) * 100, 1) : 0;
                                                                @endphp
                                                                <tr>
                                                                    <td class="text-start fw-semibold text-secondary">{{ $label }}</td>
                                                                    <td class="fw-bold">{{ $val }}</td>
                                                                    <td class="text-primary fw-semibold">{{ $percentage }}%</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Submission Details Modals rendered outside the container box -->
    @foreach ($submissions as $submission)
        <div class="modal fade" id="submissionModal-{{ $submission->id }}" tabindex="-1" aria-labelledby="submissionModalLabel-{{ $submission->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 rounded-4 shadow-lg" style="background: #ffffff;">
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="submissionModalLabel-{{ $submission->id }}" style="color: var(--text-primary);">
                            <i class="bi bi-file-earmark-text text-primary"></i> {{ __('messages.Submission Details') ?? 'Submission Details' }} #{{ $submission->id }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-start" style="text-align: initial !important;">
                        <div class="mb-4 pb-3 border-bottom d-flex flex-wrap gap-4 text-secondary small">
                            <div>
                                <i class="bi bi-calendar-event me-1"></i>
                                <strong>{{ __('messages.Submitted At') ?? 'Submitted At' }}:</strong> {{ $submission->created_at->format('Y-m-d H:i:s') }}
                            </div>
                            <div>
                                <i class="bi bi-person me-1"></i>
                                <strong>{{ __('messages.Submitted By') ?? 'Submitted By' }}:</strong> {{ $submission->user ? $submission->user->name : (__('messages.Guest') ?? 'Guest') }}
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            @foreach ($form->fields as $field)
                                <div class="p-3 rounded-3" style="background: rgba(0, 0, 0, 0.02); border: 1px solid rgba(0, 0, 0, 0.05);">
                                    <div class="fw-bold text-secondary small mb-1">{{ $field->label }}</div>
                                    <div class="fw-semibold text-dark">
                                        @php
                                            $val = $submission->data[$field->id] ?? '-';
                                        @endphp
                                        @if (is_array($val))
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @foreach($val as $item)
                                                    <span class="badge bg-secondary rounded-pill px-2.5 py-1 small">{{ $item }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            @if ($field->type === 'date' && !empty($val) && strtotime($val))
                                                @php
                                                    $submittedDate = new \DateTime($val);
                                                    $now = new \DateTime();
                                                    $interval = $submittedDate->diff($now);
                                                    $locale = app()->getLocale();
                                                    if ($submittedDate > $now) {
                                                        $elapsedStr = $locale === 'ar' ? 'في المستقبل' : 'in the future';
                                                    } else {
                                                        $elapsedStr = sprintf(
                                                            $locale === 'ar' ? '(%d سنة، %d شهر، %d يوم)' : '(%d years, %d months, %d days ago)',
                                                            $interval->y,
                                                            $interval->m,
                                                            $interval->d
                                                        );
                                                    }
                                                @endphp
                                                {{ $val }} <span class="text-primary small fw-semibold">{{ $elapsedStr }}</span>
                                            @else
                                                {{ $val }}
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 py-2 fw-semibold" data-bs-dismiss="modal">{{ __('messages.Close') ?? 'Close' }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    @if (!empty($chartData))
        <script src="{{ asset('assets/js/chart.js') }}"></script>
        <script>
            function exportAnalyticsToPDF() {
                window.print();
            }

            document.addEventListener('DOMContentLoaded', function() {
                const chartData = @json($chartData);
                const colorPalette = [
                    'rgba(59, 130, 246, 0.75)',   // Blue
                    'rgba(16, 185, 129, 0.75)',  // Green
                    'rgba(245, 158, 11, 0.75)',   // Amber
                    'rgba(239, 68, 68, 0.75)',    // Red
                    'rgba(139, 92, 246, 0.75)',   // Purple
                    'rgba(236, 72, 153, 0.75)',   // Pink
                    'rgba(6, 182, 212, 0.75)',    // Cyan
                    'rgba(107, 114, 128, 0.75)'   // Gray
                ];
                const borderPalette = [
                    'rgba(59, 130, 246, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(245, 158, 11, 1)',
                    'rgba(239, 68, 68, 1)',
                    'rgba(139, 92, 246, 1)',
                    'rgba(236, 72, 153, 1)',
                    'rgba(6, 182, 212, 1)',
                    'rgba(107, 114, 128, 1)'
                ];

                Object.keys(chartData).forEach(function(fieldId) {
                    const canvasElement = document.getElementById('chart-' + fieldId);
                    if (!canvasElement) return;

                    const ctx = canvasElement.getContext('2d');
                    const cInfo = chartData[fieldId];
                    const isMulti = cInfo.type === 'checkbox';
                    const isDate = cInfo.type === 'date';
                    
                    const chartType = (isMulti || isDate) ? 'bar' : 'doughnut';

                    const config = {
                        type: chartType,
                        data: {
                            labels: cInfo.labels,
                            datasets: [{
                                label: "{{ __('messages.Submissions') ?? 'Submissions' }}",
                                data: cInfo.data,
                                backgroundColor: colorPalette.slice(0, cInfo.labels.length),
                                borderColor: borderPalette.slice(0, cInfo.labels.length),
                                borderWidth: 1.5,
                                borderRadius: chartType === 'bar' ? 6 : 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: chartType !== 'bar',
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12,
                                        font: {
                                            family: 'Outfit, Inter, system-ui',
                                            size: 11
                                        },
                                        padding: 15
                                    }
                                }
                            }
                        }
                    };

                    if (chartType === 'bar') {
                        config.options.scales = {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        family: 'Outfit, Inter, system-ui'
                                    }
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        family: 'Outfit, Inter, system-ui'
                                    }
                                }
                            }
                        };
                        config.options.plugins.legend.display = false;
                    }

                    new Chart(ctx, config);
                });
            });
        </script>
    @endif

</x-layout>
