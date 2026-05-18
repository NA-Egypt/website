<x-layout>
<div class="container-fluid" style="background-color: var(--bs-body-bg); padding: 2rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: var(--text-primary);">{{ $serviceBody->{app()->getLocale() . '_name'} }} - {{ __('messages.agendas') ?? 'Agendas' }}</h2>
        @hasrole('super admin')
            <a href="{{ route('serviceBody.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> {{ __('messages.back') ?? 'Back' }}</a>
        @else
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> {{ __('messages.back') ?? 'Back' }}</a>
        @endhasrole
    </div>

    <div class="glass-card p-4 rounded-4 shadow-sm">
        @if($agendas && $agendas->count() > 0)
            <form action="{{ route('serviceBody.agendas.exportPdf', $serviceBody->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <button type="submit" class="btn btn-outline-danger rounded-pill">
                        <i class="bi bi-file-earmark-pdf"></i> {{ __('messages.export_selected_to_pdf') ?? 'Export Selected to PDF' }}
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="agendasTable">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAllAgendas">
                                    </div>
                                </th>
                                <th>{{ __('messages.Group') ?? 'Group' }}</th>
                                <th>{{ __('messages.Month/Year') ?? 'Month/Year' }}</th>
                                <th>{{ __('messages.Date Submitted') ?? 'Date Submitted' }}</th>
                                <th>{{ __('messages.Submitter') ?? 'Submitter' }}</th>
                                <th>{{ __('messages.Position') ?? 'Position' }}</th>
                                <th>{{ __('messages.Actions') ?? 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($agendas as $agenda)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input agenda-checkbox" type="checkbox" name="agenda_ids[]" value="{{ $agenda->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('group.show', $agenda->group_id) }}" class="text-decoration-none fw-bold">
                                            {{ $agenda->group->{app()->getLocale() . '_name'} }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary rounded-pill px-3 py-2">
                                            {{ \Carbon\Carbon::parse($agenda->agenda_date)->format('F Y') }}
                                        </span>
                                    </td>
                                    <td class="text-secondary">
                                        {{ \Carbon\Carbon::parse($agenda->created_at)->format('d M Y, h:i A') }}
                                    </td>
                                    <td>
                                        @if($agenda->submitter_name)
                                            {{ $agenda->submitter_name }}
                                        @else
                                            <span class="text-muted fst-italic">{{ __('messages.Not provided') ?? 'Not provided' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark rounded-pill">{{ $agenda->service_position }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('agenda.show', $agenda->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="bi bi-eye"></i> {{ __('messages.View') ?? 'View' }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const selectAll = document.getElementById('selectAllAgendas');
                    const checkboxes = document.querySelectorAll('.agenda-checkbox');
                    
                    if (selectAll) {
                        selectAll.addEventListener('change', function() {
                            checkboxes.forEach(cb => cb.checked = selectAll.checked);
                        });
                    }
                });
            </script>
        @else
            <div class="text-center p-5">
                <i class="bi bi-inbox text-secondary" style="font-size: 3rem; opacity: 0.5;"></i>
                <h5 class="text-secondary mt-3">{{ __('messages.No agendas found') ?? 'No agendas found' }}</h5>
            </div>
        @endif
    </div>
</div>
</x-layout>
