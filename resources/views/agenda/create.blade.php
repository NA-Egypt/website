<x-layout>
<div class="container-fluid" style="background-color: var(--bs-body-bg); padding: 2rem;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg" style="border: none; border-radius: 15px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                <div class="card-header bg-primary text-white" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                    <h4 class="mb-0">{{ __('messages.create_agenda') }} - {{ $group->{app()->getLocale() . '_name'} }}</h4>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('agenda.store') }}" method="POST" id="agendaForm">
                        @csrf
                        <input type="hidden" name="group_id" value="{{ $group->id }}">

                        <!-- SECTION 1: Group's Data -->
                        <div id="section1" class="form-section">
                            <h5 class="mb-3 text-primary border-bottom pb-2">{{ __('messages.group_data') }}</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.meetings_per_week') }}</label>
                                <input type="number" name="meetings_per_week" class="form-control" value="{{ $group->meetings->filter(fn($m) => empty($m->recurrence) || in_array('weekly', $m->recurrence))->count() }}" readonly style="background-color: #e9ecef;">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.agenda_date') }}</label>
                                <input type="date" name="agenda_date" class="form-control" value="{{ date('Y-m-d') }}" required readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block">{{ __('messages.service_position') }} *</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="service_position" id="pos_open" value="Open Position" required onchange="handlePositionChange()">
                                    <label class="form-check-label" for="pos_open">{{ __('messages.open_position') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="service_position" id="pos_alt_gsr" value="Alt. GSR" required onchange="handlePositionChange()">
                                    <label class="form-check-label" for="pos_alt_gsr">{{ __('messages.alt_gsr') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="service_position" id="pos_gsr" value="GSR" required onchange="handlePositionChange()">
                                    <label class="form-check-label" for="pos_gsr">{{ __('messages.gsr') }}</label>
                                </div>
                            </div>

                            <div class="mb-3" id="submitterNameWrapper" style="display: none;">
                                <label class="form-label">{{ __('messages.submitter_name') }} ({{ __('messages.gsr') }} / {{ __('messages.alt_gsr') }} / {{ __('messages.delegate') ?? 'Delegate' }})</label>
                                <input type="text" name="submitter_name" id="submitter_name" class="form-control" placeholder="{{ __('messages.submitter_name_placeholder') }}">
                            </div>

                            <div class="row" id="gsrExtraWrapper" style="display: none;">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('messages.alt_gsr_position') }}</label>
                                    <select name="alt_gsr_position" id="alt_gsr_position" class="form-select" onchange="handleAltGsrChange()">
                                        <option value="">{{ __('messages.select_option') }}</option>
                                        <option value="Open Position">{{ __('messages.open_position') }}</option>
                                        <option value="Alt. GSR">{{ __('messages.alt_gsr') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3" id="altGsrNameWrapper" style="display: none;">
                                    <label class="form-label">{{ __('messages.alt_gsr_name') }}</label>
                                    <input type="text" name="alt_gsr_name" id="alt_gsr_name" class="form-control" placeholder="{{ __('messages.submitter_name_placeholder') }}">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-primary" onclick="nextSection(1)">{{ __('messages.next') }} <i class="bi bi-arrow-right"></i></button>
                            </div>
                        </div>

                        <!-- SECTION 2: Group News -->
                        <div id="section2" class="form-section" style="display: none;">
                            <h5 class="mb-3 text-primary border-bottom pb-2">{{ __('messages.group_news') }}</h5>

                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.new_comers') }}</label>
                                <input type="number" name="new_comers" class="form-control" min="0">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.open_positions') }}</label>
                                <textarea name="open_positions" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.next_business_meeting') }}</label>
                                <input type="datetime-local" name="next_business_meeting" class="form-control" value="{{ old('next_business_meeting', $nextBusinessMeeting ? $nextBusinessMeeting->format('Y-m-d\TH:i') : '') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block">{{ __('messages.recovery_meetings_changes') }} *</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="recovery_meetings_changes" id="changes_yes" value="1">
                                    <label class="form-check-label" for="changes_yes">{{ __('messages.yes') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="recovery_meetings_changes" id="changes_no" value="0" checked required>
                                    <label class="form-check-label" for="changes_no">{{ __('messages.no') }}</label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary" onclick="prevSection(2)"><i class="bi bi-arrow-left"></i> {{ __('messages.back') ?? 'Back' }}</button>
                                <button type="button" class="btn btn-primary" onclick="nextSection(2)">{{ __('messages.next') }} <i class="bi bi-arrow-right"></i></button>
                            </div>
                        </div>

                        <!-- SECTION 3: The Agenda -->
                        <div id="section3" class="form-section" style="display: none;">
                            <h5 class="mb-3 text-primary border-bottom pb-2">{{ __('messages.the_agenda') }}</h5>

                            <!-- Recovery Atmosphere -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ __('messages.recovery_atmosphere') }} *</label>
                                <textarea name="recovery_atmosphere" class="form-control" rows="3" required placeholder="{{ __('messages.recovery_atmosphere') }}"></textarea>
                            </div>

                            <!-- Financial Issues -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ __('messages.financial_issues') }} *</label>
                                <textarea name="financial_issues" class="form-control" rows="3" required placeholder="{{ __('messages.financial_issues') }}"></textarea>
                            </div>

                            <!-- Other Repeatable Topics -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ __('messages.other_topics') }}</label>
                                <div id="other_topics_container">
                                    {{-- JavaScript will inject repeatable items here --}}
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-1" onclick="addOtherTopicItem()"><i class="bi bi-plus-circle me-1"></i> {{ __('messages.Add Section') ?? 'Add More' }}</button>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary" onclick="prevSection(3)"><i class="bi bi-arrow-left"></i> {{ __('messages.back') ?? 'Back' }}</button>
                                <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> {{ __('messages.save') ?? 'Submit' }}</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function handlePositionChange() {
        const val = document.querySelector('input[name="service_position"]:checked').value;
        const nameWrapper = document.getElementById('submitterNameWrapper');
        const gsrWrapper = document.getElementById('gsrExtraWrapper');
        
        nameWrapper.style.display = 'block';

        if (val === 'GSR') {
            gsrWrapper.style.display = 'flex';
        } else {
            gsrWrapper.style.display = 'none';
            document.getElementById('alt_gsr_position').value = "";
            handleAltGsrChange();
        }
    }

    function handleAltGsrChange() {
        const val = document.getElementById('alt_gsr_position').value;
        const wrapper = document.getElementById('altGsrNameWrapper');
        
        if (val === 'Alt. GSR') {
            wrapper.style.display = 'block';
        } else {
            wrapper.style.display = 'none';
            document.getElementById('alt_gsr_name').value = "";
        }
    }

    function nextSection(current) {
        // Only validate fields within the current section
        const section = document.getElementById('section' + current);
        const inputs = section.querySelectorAll('input, select, textarea');
        let isValid = true;
        
        for (const input of inputs) {
            if (!input.checkValidity()) {
                input.reportValidity();
                isValid = false;
                break;
            }
        }
        
        if (!isValid) {
            return;
        }

        document.getElementById('section' + current).style.display = 'none';
        document.getElementById('section' + (current + 1)).style.display = 'block';
    }

    function prevSection(current) {
        document.getElementById('section' + current).style.display = 'none';
        document.getElementById('section' + (current - 1)).style.display = 'block';
    }

    document.getElementById('agendaForm').addEventListener('submit', function(e) {
        @if($hasExistingAgenda)
            if (!confirm("{{ __('messages.agenda_exists_warning') }}")) {
                e.preventDefault();
            }
        @endif
    });

    let otherTopicIndex = 0;
    function addOtherTopicItem(title = '', content = '') {
        const container = document.getElementById('other_topics_container');
        const newRow = document.createElement('div');
        newRow.className = 'card p-3 mb-3 other-topic-row position-relative border shadow-sm';
        newRow.style.background = 'rgba(0, 0, 0, 0.01)';
        
        newRow.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" onclick="this.closest('.other-topic-row').remove()"><i class="bi bi-trash"></i></button>
            <div class="mb-2 pe-4">
                <label class="form-label fw-bold small">${"{{ __('messages.Topic Title') }}"}</label>
                <input type="text" name="other_topics[${otherTopicIndex}][title]" class="form-control form-control-sm" required value="${title}" placeholder="${"{{ __('messages.Topic Title') }}"}">
            </div>
            <div>
                <label class="form-label fw-bold small">${"{{ __('messages.Topic') }}"}</label>
                <textarea name="other_topics[${otherTopicIndex}][content]" class="form-control form-control-sm" rows="2" required placeholder="${"{{ __('messages.Topic') }}"}">${content}</textarea>
            </div>
        `;
        
        container.appendChild(newRow);
        otherTopicIndex++;
    }

    // Add one initial empty item on load
    document.addEventListener('DOMContentLoaded', function() {
        addOtherTopicItem();
    });
</script>
</x-layout>
