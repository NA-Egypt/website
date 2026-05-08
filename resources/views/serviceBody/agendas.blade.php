<x-layout>
<div class="container-fluid" style="background-color: var(--bs-body-bg); padding: 2rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: var(--text-primary);">{{ $serviceBody->{app()->getLocale() . '_name'} }} - {{ __('messages.agendas') ?? 'Agendas' }}</h2>
        <a href="{{ route('serviceBody.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> {{ __('messages.back') ?? 'Back' }}</a>
    </div>

    <div class="glass-card p-4 rounded-4 shadow-sm">
        @if($agendas && $agendas->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Group</th>
                            <th>Month/Year</th>
                            <th>Date Submitted</th>
                            <th>Submitter</th>
                            <th>Position</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agendas as $agenda)
                            <tr>
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
                                        <span class="text-muted fst-italic">Not provided</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark rounded-pill">{{ $agenda->service_position }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('agenda.show', $agenda->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center p-5">
                <i class="bi bi-inbox text-secondary" style="font-size: 3rem; opacity: 0.5;"></i>
                <h5 class="text-secondary mt-3">No agendas found for this Service Body</h5>
            </div>
        @endif
    </div>
</div>
</x-layout>
