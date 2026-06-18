<x-layout>
<div class="container-fluid" style="background-color: var(--bs-body-bg); padding: 2rem;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg" style="border: none; border-radius: 15px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                    <h4 class="mb-0">{{ __('messages.month_year_agenda', ['month' => \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'F'), 'year' => \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'Y')]) }} - {{ $agenda->group->{app()->getLocale() . '_name'} }}</h4>
                    <div>
                        <a href="{{ route('agenda.exportPdf', $agenda->id) }}" class="btn btn-sm btn-light rounded-pill me-2"><i class="bi bi-file-earmark-pdf text-danger"></i> {{ __('messages.download_pdf') ?? 'Download PDF' }}</a>
                        <a href="{{ route('group.show', $agenda->group_id) }}" class="btn btn-sm btn-light rounded-pill"><i class="bi bi-arrow-left"></i> {{ __('messages.back') ?? 'Back' }}</a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- SECTION 1: Group's Data -->
                    <h5 class="mb-3 text-primary border-bottom pb-2">{{ __('messages.group_data') }}</h5>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-2">
                            <strong>{{ __('messages.meetings_per_week') }}:</strong> {{ $agenda->meetings_per_week }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>{{ __('messages.agenda_date') }}:</strong> {{ \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'd M Y') }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>{{ __('messages.service_position') }}:</strong> {{ $agenda->translated_service_position }}
                        </div>
                        @if($agenda->submitter_name)
                        <div class="col-md-6 mb-2">
                            <strong>{{ __('messages.submitter_name') }}:</strong> {{ $agenda->submitter_name }}
                        </div>
                        @endif
                        @if($agenda->alt_gsr_position)
                        <div class="col-md-6 mb-2">
                            <strong>Group Alt. GSR:</strong> {{ $agenda->translated_alt_gsr_position }}
                        </div>
                        @endif
                        @if($agenda->alt_gsr_name)
                        <div class="col-md-6 mb-2">
                            <strong>{{ __('messages.alt_gsr_name') }}:</strong> {{ $agenda->alt_gsr_name }}
                        </div>
                        @endif
                    </div>

                    <!-- SECTION 2: Group News -->
                    <h5 class="mb-3 text-primary border-bottom pb-2">{{ __('messages.group_news') }}</h5>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-2">
                            <strong>{{ __('messages.new_comers') }}:</strong> {{ $agenda->new_comers }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>{{ __('messages.next_business_meeting') }}:</strong> {{ $agenda->next_business_meeting ? \App\Services\DateNumberHelper::translatedFormat($agenda->next_business_meeting, 'd M Y h:i A') : '' }}
                        </div>
                        <div class="col-12 mb-2">
                            <strong>{{ __('messages.recovery_meetings_changes') }}:</strong> {{ $agenda->recovery_meetings_changes ? __('messages.yes') : __('messages.no') }}
                        </div>
                        <div class="col-12 mb-2">
                            <strong>{{ __('messages.open_positions') }}:</strong>
                            <p class="text-secondary">{{ $agenda->open_positions ?: 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- SECTION 3: The Agenda -->
                    <h5 class="mb-3 text-primary border-bottom pb-2">{{ __('messages.the_agenda') }}</h5>
                    <div class="row mb-4">
                        <div class="col-12 mb-3">
                            <strong>{{ __('messages.recovery_atmosphere') }}:</strong>
                            <div class="p-3 bg-light rounded">{{ $agenda->recovery_atmosphere ?: 'N/A' }}</div>
                        </div>
                        <div class="col-12 mb-3">
                            <strong>{{ __('messages.trusted_servants') }}:</strong>
                            <div class="p-3 bg-light rounded">{{ $agenda->trusted_servants ?: 'N/A' }}</div>
                        </div>
                        <div class="col-12 mb-3">
                            <strong>{{ __('messages.financial_issues') }}:</strong>
                            <div class="p-3 bg-light rounded">{{ $agenda->financial_issues ?: 'N/A' }}</div>
                        </div>
                        <div class="col-12 mb-3">
                            <strong>{{ __('messages.other_topics') }}:</strong>
                            <div class="p-3 bg-light rounded">{{ $agenda->other_topics ?: 'N/A' }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</x-layout>
