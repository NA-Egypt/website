<x-layout>

    <x-backhead>{{ __('messages.Form Reports') ?? 'Form Reports' }}</x-backhead>

    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0 fw-bold" style="color: var(--text-primary);">{{ $form->title }} - {{ __('messages.Submissions Report') ?? 'Submissions Report' }}</h4>
                <p class="text-secondary mb-0">Total Views: <strong>{{ $form->views }}</strong> | Submissions: <strong>{{ $form->submissions->count() }}</strong> | Conversion: <strong>{{ $form->conversion_rate }}%</strong></p>
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

        <div class="glass-card p-4">
            <div class="table-responsive">
                <table class="table neo-table align-middle text-center display" id="submissions-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.Submission ID') ?? 'ID' }}</th>
                            <th>{{ __('messages.Date') ?? 'Submitted At' }}</th>
                            <th>{{ __('messages.Submitted By') ?? 'Submitted By' }}</th>
                            @foreach ($form->fields as $field)
                                <th>{{ $field->label }}</th>
                            @endforeach
                            <th>{{ __('messages.Actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($form->submissions as $submission)
                            <tr>
                                <td class="fw-bold">#{{ $submission->id }}</td>
                                <td class="text-secondary">{{ $submission->created_at->format('Y-m-d H:i') }}</td>
                                <td class="fw-semibold">
                                    {{ $submission->user ? $submission->user->name : (__('messages.Guest') ?? 'Guest') }}
                                </td>
                                @foreach ($form->fields as $field)
                                    <td>
                                        @php
                                            $val = $submission->data[$field->id] ?? '-';
                                        @endphp
                                        @if (is_array($val))
                                            {{ implode(', ', $val) }}
                                        @else
                                            {{ $val }}
                                        @endif
                                    </td>
                                @endforeach
                                <td>
                                    <a href="{{ route('forms.submissionPdf', [$form->id, $submission->id]) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 d-flex align-items-center justify-content-center gap-1">
                                        <i class="bi bi-file-earmark-pdf"></i> PDF
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-layout>
