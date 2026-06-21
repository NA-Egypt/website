<x-layout>
<div class="container-fluid px-0 px-sm-3 mt-4 mb-5 mx-auto" style="max-width: 900px; width: 100%;">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 px-0 px-sm-2">
            <div class="card shadow-lg" style="border: none; border-radius: 15px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); border-top-left-radius: 15px; border-top-right-radius: 15px;">
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
                                <button type="button" class="btn btn-primary" onclick="nextSection(1)">{{ __('messages.next') }} <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i></button>
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
                                <button type="button" class="btn btn-secondary" onclick="prevSection(2)"><i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i> {{ __('messages.back') ?? 'Back' }}</button>
                                <button type="button" class="btn btn-primary" onclick="nextSection(2)">{{ __('messages.next') }} <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i></button>
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

                            <!-- Trusted Servants -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ __('messages.trusted_servants') }} *</label>
                                <textarea name="trusted_servants" class="form-control" rows="3" required placeholder="{{ __('messages.trusted_servants') }}"></textarea>
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
                                <button type="button" class="btn btn-secondary" onclick="prevSection(3)"><i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i> {{ __('messages.back') ?? 'Back' }}</button>
                                <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> {{ __('messages.save') ?? 'Submit' }}</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quill Styles -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<script>
    const quillEditors = {};

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
                return;
            }
        @endif

        // Populate Quill editors to hidden inputs
        document.querySelectorAll('.other-topic-row').forEach(row => {
            const idx = row.dataset.index;
            const contentInput = document.getElementById(`other-topic-content-${idx}`);
            if (quillEditors[idx] && contentInput) {
                contentInput.value = quillEditors[idx].root.innerHTML;
            }
        });
    });

    let otherTopicIndex = 0;
    function addOtherTopicItem(title = '', content = '') {
        const container = document.getElementById('other_topics_container');
        const newRow = document.createElement('div');
        newRow.className = 'card p-3 mb-3 other-topic-row position-relative border shadow-sm';
        newRow.style.background = 'rgba(0, 0, 0, 0.01)';
        newRow.dataset.index = otherTopicIndex;
        
        newRow.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" onclick="deleteOtherTopicItem(this, ${otherTopicIndex})"><i class="bi bi-trash"></i></button>
            <div class="mb-2 pe-4">
                <label class="form-label fw-bold small">${"{{ __('messages.Topic Title') }}"}</label>
                <input type="text" name="other_topics[${otherTopicIndex}][title]" class="form-control form-control-sm" required value="${title}" placeholder="${"{{ __('messages.Topic Title') }}"}">
            </div>
            <div>
                <label class="form-label fw-bold small">${"{{ __('messages.Topic') }}"}</label>
                <div class="quill-editor mb-2" id="quill-editor-${otherTopicIndex}" style="height: 200px;"></div>
                <input type="hidden" name="other_topics[${otherTopicIndex}][content]" class="other-topic-content-input" id="other-topic-content-${otherTopicIndex}">
                
                <!-- Dynamic Table Action Controls -->
                <div class="table-controls p-2 border rounded bg-light d-none align-items-center gap-2 flex-wrap mb-2" id="table-controls-${otherTopicIndex}">
                    <span class="badge bg-secondary py-2"><i class="bi bi-table"></i> {{ __('messages.Table Actions') ?? 'Table Actions' }}</span>
                    <button type="button" class="btn btn-sm btn-outline-primary insert-row-above-btn"><i class="bi bi-arrow-bar-up"></i> Row Above</button>
                    <button type="button" class="btn btn-sm btn-outline-primary insert-row-below-btn"><i class="bi bi-arrow-bar-down"></i> Row Below</button>
                    <button type="button" class="btn btn-sm btn-outline-primary insert-column-left-btn"><i class="bi bi-arrow-bar-left"></i> Column Left</button>
                    <button type="button" class="btn btn-sm btn-outline-primary insert-column-right-btn"><i class="bi bi-arrow-bar-right"></i> Column Right</button>
                    <button type="button" class="btn btn-sm btn-outline-danger delete-row-btn ms-md-auto"><i class="bi bi-trash"></i> Delete Row</button>
                    <button type="button" class="btn btn-sm btn-outline-danger delete-column-btn"><i class="bi bi-trash"></i> Delete Col</button>
                    <button type="button" class="btn btn-danger btn-sm delete-table-btn"><i class="bi bi-x-circle"></i> Delete Table</button>
                </div>
            </div>
        `;
        
        container.appendChild(newRow);
        
        const editorId = `#quill-editor-${otherTopicIndex}`;
        const quill = new Quill(editorId, {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, false] }],
                    ['bold', 'italic', 'underline'],
                    ['image', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['table']
                ],
                table: true
            }
        });
        
        if (content) {
            quill.root.innerHTML = content;
        }
        
        quillEditors[otherTopicIndex] = quill;
        
        const currentIndex = otherTopicIndex;
        const tableControls = newRow.querySelector('.table-controls');
        const tableModule = quill.getModule('table');

        newRow.querySelector('.insert-row-above-btn').addEventListener('click', () => tableModule.insertRowAbove());
        newRow.querySelector('.insert-row-below-btn').addEventListener('click', () => tableModule.insertRowBelow());
        newRow.querySelector('.insert-column-left-btn').addEventListener('click', () => tableModule.insertColumnLeft());
        newRow.querySelector('.insert-column-right-btn').addEventListener('click', () => tableModule.insertColumnRight());
        newRow.querySelector('.delete-row-btn').addEventListener('click', () => tableModule.deleteRow());
        newRow.querySelector('.delete-column-btn').addEventListener('click', () => tableModule.deleteColumn());
        newRow.querySelector('.delete-table-btn').addEventListener('click', function() {
            if (confirm('Are you sure you want to delete the entire table?')) {
                tableModule.deleteTable();
            }
        });

        function checkTableFocus(range) {
            if (range) {
                const formats = quill.getFormat(range);
                if (formats.table || formats['table-cell']) {
                    tableControls.classList.remove('d-none');
                    tableControls.classList.add('d-flex');
                } else {
                    const [leaf] = quill.getLeaf(range.index);
                    if (leaf && leaf.domNode && leaf.domNode.parentElement && leaf.domNode.parentElement.closest('table')) {
                        tableControls.classList.remove('d-none');
                        tableControls.classList.add('d-flex');
                    } else {
                        tableControls.classList.add('d-none');
                        tableControls.classList.remove('d-flex');
                    }
                }
            } else {
                tableControls.classList.add('d-none');
                tableControls.classList.remove('d-flex');
            }
        }

        quill.on('selection-change', function(range) {
            checkTableFocus(range);
        });

        quill.on('text-change', function() {
            const range = quill.getSelection();
            checkTableFocus(range);
        });

        otherTopicIndex++;
    }

    function deleteOtherTopicItem(btn, index) {
        btn.closest('.other-topic-row').remove();
        delete quillEditors[index];
    }

    // Add one initial empty item on load
    document.addEventListener('DOMContentLoaded', function() {
        addOtherTopicItem();
    });
</script>
</x-layout>
