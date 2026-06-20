<x-layout>
    <x-backhead>{{ __('messages.Create Service Body Agenda') ?? 'Create Service Body Agenda' }}</x-backhead>

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

        <form action="{{ route('service-body-agendas.store') }}" method="POST" id="agendaForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="status" id="statusInput" value="draft">

            <div class="card mb-4 shadow-sm border-0" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 15px;">
                <div class="card-header bg-primary text-white" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                    <h5 class="mb-0" id="agendaDynamicHeader">
                        @if($isRsc)
                            {{ __('messages.Create Service Body Agenda') ?? 'Create Service Body Agenda' }}
                        @else
                            {{ $sb->ar_name }} - {{ now()->format('Y') }}
                        @endif
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-3 align-items-end g-3">
                        @if($isRsc)
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('messages.Select Service Body') ?? 'Select Service Body' }}</label>
                                <select name="service_body_id" id="serviceBodySelect" class="form-select" required>
                                    <option value="">{{ __('messages.Choose a Service Body...') ?? 'Choose a Service Body...' }}</option>
                                    @foreach($serviceBodies as $body)
                                        <option value="{{ $body->id }}" data-ar-name="{{ $body->ar_name }}" data-groups='@json($body->groups->pluck("ar_name"))'>{{ $body->ar_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="service_body_id" id="serviceBodyIdHidden" value="{{ $sb->id }}" data-ar-name="{{ $sb->ar_name }}">
                        @endif

                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('messages.Agenda Writing Date') ?? 'Agenda Writing Date' }}</label>
                            <input type="text" class="form-control bg-light" readonly value="{{ now()->toDateString() }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('messages.Meeting Date') ?? 'Meeting Date' }}</label>
                            <input type="date" name="meeting_date" id="meetingDateInput" class="form-control" required value="{{ old('meeting_date', now()->toDateString()) }}">
                        </div>

                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_exceptional" id="is_exceptional" value="1" {{ old('is_exceptional') ? 'checked' : '' }}>
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
                            @if(!$isRsc && $sb)
                                @forelse($sb->groups as $group)
                                    <span class="badge bg-secondary p-2 fs-6"><i class="bi bi-tag-fill me-1"></i> {{ $group->ar_name }}</span>
                                @empty
                                    <span class="text-muted">{{ __('messages.No groups registered yet.') ?? 'No groups registered yet.' }}</span>
                                @endforelse
                            @else
                                <span class="text-muted">{{ __('messages.Please select a service body first') ?? 'Please select a service body first' }}</span>
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
                    <div id="attachmentsContainer" class="d-flex flex-column gap-2 mb-2">
                        <!-- Repeatable file inputs will be added here -->
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addAttachmentBtn">+ {{ __('messages.Add Attachment') ?? 'Add Attachment' }}</button>
                    <small class="text-muted d-block mt-2">{{ __('messages.Max 5 attachments. Allowed types: pdf, png, jpg, jpeg, docx, xlsx. Max size: 5MB per file.') ?? 'Max 5 attachments. Allowed types: pdf, png, jpg, jpeg, docx, xlsx. Max size: 5MB per file.' }}</small>
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
                        // Groups are rendered on backend for RCM
                    }
                }

                // If groups are dynamically changing (RSC case)
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
            
            // Initial call
            updateHeaderAndGroups();

            // Repeatable New Groups Joined
            const newGroupsContainer = document.getElementById('newGroupsContainer');
            const addNewGroupBtn = document.getElementById('addNewGroupBtn');
            let newGroupIndex = 0;

            function addNewGroupRow(value = '') {
                const div = document.createElement('div');
                div.className = 'd-flex align-items-center gap-2';
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

            // Add initial sections
            const defaultSections = [
                { headline: 'تقرير السكرتير', content: '' },
                { headline: 'تقرير أمين الصندوق', content: '' },
                { headline: 'تقارير اللجان الفرعية', content: '' },
                { headline: 'مشاريع وخطط مستقبلية', content: '' }
            ];
            
            defaultSections.forEach(sec => addSectionRow(sec.headline, sec.content));

            addSectionBtn.addEventListener('click', () => addSectionRow('', ''));

            // Repeatable Attachments (Max 5)
            const attachmentsContainer = document.getElementById('attachmentsContainer');
            const addAttachmentBtn = document.getElementById('addAttachmentBtn');
            const maxAttachments = 5;

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
                // Remove required attribute from empty rows if any (or they are already required, but we should make sure we don't send empty files)
                document.querySelectorAll('.section-row').forEach(row => {
                    const idx = row.dataset.index;
                    const contentInput = document.getElementById(`section-content-${idx}`);
                    if (quillEditors[idx] && contentInput) {
                        contentInput.value = quillEditors[idx].root.innerHTML;
                    }
                });
            };
        });
    </script>
</x-layout>
