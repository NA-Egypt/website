<x-layout>

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
                                            {{ $val }}
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

</x-layout>
