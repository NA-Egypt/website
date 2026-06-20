<x-layout>
    <x-backhead>{{ __('messages.Edit Service Body Agenda') ?? 'Edit Service Body Agenda' }}</x-backhead>

    <div class="container mt-4">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('service-body-agendas.update', $agenda->id) }}" method="POST" id="agendaForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" id="statusInput" value="{{ $agenda->status }}">

            <div class="card mb-4 shadow-sm border-0" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 15px;">
                <div class="card-header bg-primary text-white" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                    <h5 class="mb-0" id="agendaDynamicHeader">
                        {{ $agenda->serviceBody->ar_name }} - {{ $agenda->meeting_date->format('Y') }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-3 align-items-end g-3">
                        @if($isRsc)
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('messages.Select Service Body') ?? 'Select Service Body' }}</label>
                                <select name="service_body_id" id="serviceBodySelect" class="form-select" required>
                                    @foreach($serviceBodies as $body)
                                        <option value="{{ $body->id }}" data-ar-name="{{ $body->ar_name }}" data-groups='@json($body->groups->pluck("ar_name"))' {{ $agenda->service_body_id == $body->id ? 'selected' : '' }}>{{ $body->ar_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="service_body_id" id="serviceBodyIdHidden" value="{{ $agenda->service_body_id }}" data-ar-name="{{ $agenda->serviceBody->ar_name }}">
                        @endif

                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('messages.Agenda Writing Date') ?? 'Agenda Writing Date' }}</label>
                            <input type="text" class="form-control bg-light" readonly value="{{ $agenda->agenda_date->toDateString() }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('messages.Meeting Date') ?? 'Meeting Date' }}</label>
                            <input type="date" name="meeting_date" id="meetingDateInput" class="form-control" required value="{{ old('meeting_date', $agenda->meeting_date->toDateString()) }}">
                        </div>

                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_exceptional" id="is_exceptional" value="1" {{ old('is_exceptional', $agenda->is_exceptional) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-danger" for="is_exceptional">
                                    {{ __('messages.Exceptional Meeting') ?? 'Exceptional Meeting' }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Groups Section -->
            <div class="card mb-4 shadow-sm border-0" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 15px;">
                <div class="card-header bg-light fw-bold" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                    <i class="bi bi-people me-2"></i>{{ __('messages.Service Body Groups') ?? 'Service Body Groups' }}
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary">{{ __('messages.Current Groups') ?? 'Current Groups' }}</label>
                        <div id="currentGroupsContainer" class="p-3 border rounded bg-light d-flex flex-wrap gap-2">
                            @if(!$isRsc && $agenda->serviceBody)
                                @forelse($agenda->serviceBody->groups as $group)
                                    <span class="badge bg-secondary p-2 fs-6"><i class="bi bi-tag-fill me-1"></i> {{ $group->ar_name }}</span>
                                @empty
                                    <span class="text-muted">{{ __('messages.No groups registered yet.') ?? 'No groups registered yet.' }}</span>
                                @endforelse
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">{{ __('messages.New Groups Joined') ?? 'New Groups Joined' }}</label>
                        <div id="newGroupsContainer" class="d-flex flex-column gap-2 mb-2">
                            <!-- Repeatable joined groups will be here -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addNewGroupBtn">+ {{ __('messages.Add Group') ?? 'Add Group' }}</button>
                    </div>
                </div>
            </div>

            <!-- Agenda Body Sections (Repeatable) -->
            <div class="mb-4">
                <div id="agendaSectionsContainer">
                    <!-- Dynamic sections will be added here -->
                </div>
                <button type="button" class="btn btn-outline-primary" id="addSectionBtn">+ {{ __('messages.Add Section') ?? 'Add Section' }}</button>
            </div>

            <!-- Attachments Section (Repeatable, Max 5) -->
            <div class="card mb-4 shadow-sm border-0" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 15px;">
                <div class="card-header bg-light fw-bold" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                    <i class="bi bi-paperclip me-2"></i>{{ __('messages.Attachments') ?? 'Attachments' }}
                </div>
                <div class="card-body p-4">
                    @if($agenda->attachments->count() > 0)
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">{{ __('messages.Current Attachments') ?? 'Current Attachments' }}</label>
                            <div class="list-group">
                                @foreach($agenda->attachments as $attachment)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="{{ route('service-body-agendas.downloadAttachment', $attachment->id) }}" target="_blank">
                                            <i class="bi bi-file-earmark-arrow-down me-2"></i>{{ $attachment->original_name }}
                                        </a>
                                        @if($agenda->status === 'draft')
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Are you sure?')) { document.getElementById('delete-attachment-form-{{ $attachment->id }}').submit(); }">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="delete-attachment-form-{{ $attachment->id }}" action="{{ route('service-body-agendas.deleteAttachment', $attachment->id) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">{{ __('messages.Add New Attachments') ?? 'Add New Attachments' }}</label>
                        <div id="attachmentsContainer" class="d-flex flex-column gap-2 mb-2">
                            <!-- Repeatable file inputs will be added here -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addAttachmentBtn">+ {{ __('messages.Add Attachment') ?? 'Add Attachment' }}</button>
                        <small class="text-muted d-block mt-2">{{ __('messages.Max 5 attachments total. Allowed types: pdf, png, jpg, jpeg, docx, xlsx. Max size: 5MB per file.') ?? 'Max 5 attachments total. Allowed types: pdf, png, jpg, jpeg, docx, xlsx. Max size: 5MB per file.' }}</small>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mb-5">
                <button type="submit" id="saveDraftBtn" class="btn btn-outline-secondary btn-lg flex-fill">
                    <i class="bi bi-file-earmark"></i> {{ __('messages.Save Draft') ?? 'Save Draft' }}
                </button>
                <button type="submit" id="approveSendBtn" class="btn btn-primary btn-lg flex-fill">
                    <i class="bi bi-check-circle"></i> {{ __('messages.Submit & Send to RSC') ?? 'Submit & Send to RSC' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Quill Styles -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isRsc = @json($isRsc);
            const serviceBodySelect = document.getElementById('serviceBodySelect');
            const hiddenSbInput = document.getElementById('serviceBodyIdHidden');
            const meetingDateInput = document.getElementById('meetingDateInput');
            const agendaDynamicHeader = document.getElementById('agendaDynamicHeader');
            const currentGroupsContainer = document.getElementById('currentGroupsContainer');
            
            const arabicMonths = {
                1: 'يناير', 2: 'فبراير', 3: 'مارس', 4: 'أبريل', 5: 'مايو', 6: 'يونيو',
                7: 'يوليو', 8: 'أغسطس', 9: 'سبتمبر', 10: 'أكتوبر', 11: 'نوفمبر', 12: 'ديسمبر'
            };

            function updateHeaderAndGroups() {
                let sbName = '';
                let groups = [];
                
                if (isRsc) {
                    const selectedOption = serviceBodySelect.options[serviceBodySelect.selectedIndex];
                    if (selectedOption && selectedOption.value) {
                        sbName = selectedOption.getAttribute('data-ar-name');
                        groups = JSON.parse(selectedOption.getAttribute('data-groups') || '[]');
                    }
                } else {
                    if (hiddenSbInput) {
                        sbName = hiddenSbInput.getAttribute('data-ar-name');
                    }
                }

                if (isRsc && groups) {
                    currentGroupsContainer.innerHTML = '';
                    if (groups.length > 0) {
                        groups.forEach(g => {
                            const span = document.createElement('span');
                            span.className = 'badge bg-secondary p-2 fs-6';
                            span.innerHTML = `<i class="bi bi-tag-fill me-1"></i> ${g}`;
                            currentGroupsContainer.appendChild(span);
                        });
                    } else {
                        currentGroupsContainer.innerHTML = `<span class="text-muted">{{ __('messages.No groups registered yet.') ?? 'No groups registered yet.' }}</span>`;
                    }
                }

                const dateVal = meetingDateInput.value;
                if (dateVal && sbName) {
                    const dateObj = new Date(dateVal);
                    const year = dateObj.getFullYear();
                    const monthNum = dateObj.getMonth() + 1;
                    const monthName = arabicMonths[monthNum] || monthNum;
                    agendaDynamicHeader.textContent = `${sbName} ${monthName} ${year}`;
                } else if (sbName) {
                    agendaDynamicHeader.textContent = sbName;
                }
            }

            if (isRsc && serviceBodySelect) {
                serviceBodySelect.addEventListener('change', updateHeaderAndGroups);
            }
            meetingDateInput.addEventListener('change', updateHeaderAndGroups);

            // Repeatable New Groups Joined
            const newGroupsContainer = document.getElementById('newGroupsContainer');
            const addNewGroupBtn = document.getElementById('addNewGroupBtn');
            let newGroupIndex = 0;

            function addNewGroupRow(value = '') {
                const div = document.createElement('div');
                div.className = 'd-flex align-items-center gap-2 mt-2';
                div.innerHTML = `
                    <input type="text" name="groups_joined[${newGroupIndex}]" class="form-control" placeholder="{{ __('messages.Enter group name...') ?? 'Enter group name...' }}" value="${value}">
                    <button type="button" class="btn btn-outline-danger remove-group-btn">X</button>
                `;
                newGroupsContainer.appendChild(div);
                newGroupIndex++;

                div.querySelector('.remove-group-btn').addEventListener('click', function() {
                    div.remove();
                });
            }

            addNewGroupBtn.addEventListener('click', () => addNewGroupRow());

            // Populate existing groups joined
            const oldGroupsJoined = @json($agenda->groups_joined ?? []);
            if (oldGroupsJoined && oldGroupsJoined.length > 0) {
                oldGroupsJoined.forEach(g => addNewGroupRow(g));
            }

            // Repeatable Agenda Sections (Quill)
            const agendaSectionsContainer = document.getElementById('agendaSectionsContainer');
            const addSectionBtn = document.getElementById('addSectionBtn');
            let sectionIndex = 0;
            const quillEditors = {};

            function addSectionRow(headline = '', content = '') {
                const card = document.createElement('div');
                card.className = 'card mb-3 section-row shadow-sm border-0';
                card.dataset.index = sectionIndex;
                card.style.borderRadius = '15px';
                card.innerHTML = `
                    <div class="card-header d-flex justify-content-between align-items-center bg-light" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                        <span class="fw-bold text-secondary">{{ __('messages.Section') ?? 'Section' }} #${sectionIndex + 1}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-section-btn">{{ __('messages.Remove Section') ?? 'Remove' }}</button>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('messages.Section Headline (Optional)') ?? 'Section Headline (Optional)' }}</label>
                            <input type="text" name="sections[${sectionIndex}][headline]" class="form-control" placeholder="{{ __('messages.Enter optional headline...') ?? 'Enter optional headline...' }}" value="${headline}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('messages.Section Content') ?? 'Section Content' }}</label>
                            <div class="quill-editor mb-2" id="quill-editor-${sectionIndex}" style="height: 250px;"></div>
                            <input type="hidden" name="sections[${sectionIndex}][content]" class="section-content-input" id="section-content-${sectionIndex}">
                            
                            <!-- Dynamic Table Action Controls -->
                            <div class="table-controls p-2 border rounded bg-light d-none align-items-center gap-2 flex-wrap" id="table-controls-${sectionIndex}">
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
                    </div>
                `;
                agendaSectionsContainer.appendChild(card);
                
                const editorId = `#quill-editor-${sectionIndex}`;
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
                
                quillEditors[sectionIndex] = quill;
                
                const currentIndex = sectionIndex;
                const tableControls = card.querySelector('.table-controls');
                const tableModule = quill.getModule('table');

                card.querySelector('.insert-row-above-btn').addEventListener('click', () => tableModule.insertRowAbove());
                card.querySelector('.insert-row-below-btn').addEventListener('click', () => tableModule.insertRowBelow());
                card.querySelector('.insert-column-left-btn').addEventListener('click', () => tableModule.insertColumnLeft());
                card.querySelector('.insert-column-right-btn').addEventListener('click', () => tableModule.insertColumnRight());
                card.querySelector('.delete-row-btn').addEventListener('click', () => tableModule.deleteRow());
                card.querySelector('.delete-column-btn').addEventListener('click', () => tableModule.deleteColumn());
                card.querySelector('.delete-table-btn').addEventListener('click', function() {
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
                
                card.querySelector('.remove-section-btn').addEventListener('click', function() {
                    if (document.querySelectorAll('.section-row').length > 1) {
                        card.remove();
                        delete quillEditors[currentIndex];
                        document.querySelectorAll('.section-row').forEach((row, idx) => {
                            row.querySelector('.card-header span').textContent = `{{ __('messages.Section') ?? 'Section' }} #${idx + 1}`;
                        });
                    } else {
                        alert("{{ __('messages.An agenda must have at least one section.') ?? 'An agenda must have at least one section.' }}");
                    }
                });
                
                sectionIndex++;
            }

            // Populate existing sections
            const oldSections = @json($agenda->body ?? []);
            if (oldSections && oldSections.length > 0) {
                oldSections.forEach(sec => addSectionRow(sec.headline, sec.content));
            } else {
                addSectionRow('', '');
            }

            addSectionBtn.addEventListener('click', () => addSectionRow('', ''));

            // Repeatable Attachments (Max 5 total)
            const attachmentsContainer = document.getElementById('attachmentsContainer');
            const addAttachmentBtn = document.getElementById('addAttachmentBtn');
            const existingCount = @json($agenda->attachments->count());
            const maxAttachments = 5 - existingCount;

            function updateAttachmentBtn() {
                const currentCount = attachmentsContainer.querySelectorAll('.attachment-row').length;
                if (currentCount >= maxAttachments) {
                    addAttachmentBtn.disabled = true;
                } else {
                    addAttachmentBtn.disabled = false;
                }
            }

            function addAttachmentRow() {
                const currentCount = attachmentsContainer.querySelectorAll('.attachment-row').length;
                if (currentCount >= maxAttachments) {
                    alert("{{ __('messages.Maximum 5 attachments allowed.') ?? 'Maximum 5 attachments allowed.' }}");
                    return;
                }

                const div = document.createElement('div');
                div.className = 'd-flex align-items-center gap-2 attachment-row';
                div.innerHTML = `
                    <input type="file" name="attachments[]" class="form-control" required>
                    <button type="button" class="btn btn-outline-danger remove-attachment-btn">X</button>
                `;
                attachmentsContainer.appendChild(div);

                div.querySelector('.remove-attachment-btn').addEventListener('click', function() {
                    div.remove();
                    updateAttachmentBtn();
                });

                updateAttachmentBtn();
            }

            addAttachmentBtn.addEventListener('click', addAttachmentRow);
            updateAttachmentBtn();

            // Form Submit
            const form = document.querySelector('#agendaForm');
            const statusInput = document.getElementById('statusInput');
            const saveDraftBtn = document.getElementById('saveDraftBtn');
            const approveSendBtn = document.getElementById('approveSendBtn');

            saveDraftBtn.addEventListener('click', function() {
                statusInput.value = 'draft';
            });

            approveSendBtn.addEventListener('click', function() {
                statusInput.value = 'submitted';
            });

            form.onsubmit = function() {
                document.querySelectorAll('.section-row').forEach(row => {
                    const idx = row.dataset.index;
                    const contentInput = document.getElementById(`section-content-${idx}`);
                    if (quillEditors[idx] && contentInput) {
                        contentInput.value = quillEditors[idx].root.innerHTML;
                    }
                });
            };

            // Run initial update
            updateHeaderAndGroups();
        });
    </script>
</x-layout>
