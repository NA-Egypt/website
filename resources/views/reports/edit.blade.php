<x-layout>
    <x-backhead>{{ __('messages.Edit Committee Report') ?? 'Edit Committee Report' }}</x-backhead>

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

        @if($report->review_notes && auth()->check() && !auth()->user()->hasRole('ServiceBody') && !auth()->user()->hasRole('gsr'))
            <div class="alert alert-warning border-start border-warning border-4 mb-4 shadow-sm">
                <h5 class="alert-heading fw-bold text-dark"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>{{ __('messages.Review Notes') ?? 'RSC Review Notes / Comments' }}</h5>
                <p class="mb-0 text-dark">{{ $report->review_notes }}</p>
            </div>
        @endif

        <form action="{{ route('committee-reports.update', $report->id) }}" method="POST" id="reportForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" id="statusInput" value="{{ $report->status }}">

            <div class="card mb-4">
                <div class="card-header bg-light">
                    @if($isRsc)
                        <label class="form-label fw-bold">{{ __('messages.Select Committee') }}</label>
                        <select name="service_committee_id" class="form-select" required>
                            <option value="">{{ __('messages.Choose a Committee...') }}</option>
                            @foreach($committees as $c)
                                <option value="{{ $c->id }}" {{ $report->service_committee_id == $c->id ? 'selected' : '' }}>{{ $c->ar_name }}</option>
                            @endforeach
                        </select>
                    @else
                        <h5 class="mb-0">{{ $report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name }}</h5>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row mb-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('messages.Report Date') }}</label>
                            <input type="text" class="form-control bg-light" readonly value="{{ optional($report->report_date)->format('Y-m-d') ?? $report->created_at->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('messages.Meeting Date') }}</label>
                            <input type="date" name="meeting_date" class="form-control" required value="{{ old('meeting_date', $report->meeting_date->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('messages.Meeting Day Description') }}</label>
                            <input type="text" name="meeting_day_description" class="form-control" placeholder="e.g. first sunday" required value="{{ old('meeting_day_description', $report->meeting_day_description) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_exceptional" id="is_exceptional" value="1" {{ old('is_exceptional', $report->is_exceptional) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-danger" for="is_exceptional">
                                    {{ __('messages.Exceptional Meeting') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Positions Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('messages.Positions Status') }}</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addPositionBtn">+ {{ __('messages.Add Position') }}</button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('messages.Position Name') }}</th>
                                <th>{{ __('messages.Member Name') }}</th>
                                <th>{{ __('messages.Status') }}</th>
                                <th>{{ __('messages.Open for Election') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="positionsTableBody">
                            <!-- Rows will be added here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Attended Members Section -->
            <div class="card mb-4">
                <div class="card-header fw-bold">{{ __('messages.Attended Members') }}</div>
                <div class="card-body">
                    <textarea name="attended_members" class="form-control" rows="4" placeholder="{{ __('messages.Enter names of members who attended...') }}">{{ old('attended_members', $report->attended_members) }}</textarea>
                </div>
            </div>

            <!-- Report Body Sections (Repeatable) -->
            <div class="mb-4">
                <div id="reportSectionsContainer">
                    <!-- Dynamic sections will be added here -->
                </div>
                <button type="button" class="btn btn-outline-primary" id="addSectionBtn">+ {{ __('messages.Add Section') ?? 'Add Section' }}</button>
            </div>

            <!-- Attachments Section -->
            <div class="card mb-4">
                <div class="card-header">{{ __('messages.Attachments') ?? 'Attachments' }}</div>
                <div class="card-body">
                    <!-- Existing Attachments -->
                    @if($report->attachments->isNotEmpty())
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">{{ __('messages.Existing Attachments') ?? 'Existing Attachments' }}</label>
                            <ul class="list-group">
                                @foreach($report->attachments as $attachment)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-file-earmark-arrow-down text-primary me-2"></i>
                                            <a href="{{ route('committee-reports.downloadAttachment', $attachment->id) }}" class="text-decoration-none" target="_blank">
                                                {{ $attachment->original_name }}
                                            </a>
                                            <span class="text-muted ms-2 small">({{ number_format($attachment->file_size / 1024, 1) }} KB)</span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteAttachment({{ $attachment->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Upload New Attachments -->
                    @if($report->attachments->count() < 5)
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('messages.Upload New Attachments') ?? 'Upload New Attachments' }}</label>
                            <div id="attachmentsContainer">
                                <div class="attachment-row mb-2">
                                    <div class="input-group">
                                        <input class="form-control" type="file" name="attachments[]" accept=".pdf,.png,.jpg,.jpeg,.docx,.xlsx">
                                        <button type="button" class="btn btn-outline-danger remove-attachment-btn d-none">X</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addAttachmentBtn">+ {{ __('messages.Add Attachment') ?? 'Add Attachment' }}</button>
                            <div class="form-text text-muted mt-2">
                                {{ __('messages.Max total 5 files') ?? 'Maximum total of 5 files' }} ({{ $report->attachments->count() }} {{ __('messages.currently uploaded') ?? 'currently uploaded' }}). <br>
                                {{ __('messages.Max size') ?? 'Maximum 5MB per file' }}. <br>
                                {{ __('messages.Allowed types') ?? 'Allowed file types' }}: PDF, PNG, JPG, JPEG, DOCX, XLSX
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning py-2 mb-0">
                            <i class="bi bi-exclamation-triangle"></i> {{ __('messages.Max attachments limit reached') ?? 'You have reached the maximum limit of 5 attachments. Delete one to upload a new file.' }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer Section -->
            <div class="card mb-4">
                <div class="card-header">{{ __('messages.Report Footer') ?? 'Report Footer' }}</div>
                <div class="card-body">
                    <textarea name="footer" class="form-control" rows="3" placeholder="{{ __('messages.Enter report-specific footer text (overrides default committee footer)...') ?? 'Enter report-specific footer text (overrides default committee footer)...' }}">{{ old('footer', $report->footer) }}</textarea>
                    @if($report->serviceCommittee && $report->serviceCommittee->default_footer)
                        <div class="form-text text-muted mt-2">
                            <strong>{{ __('messages.Default Committee Footer') ?? 'Default Committee Footer' }}:</strong> {{ $report->serviceCommittee->default_footer }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="d-flex gap-3 mb-5">
                <button type="submit" id="saveDraftBtn" class="btn btn-outline-secondary btn-lg flex-fill">
                    <i class="bi bi-file-earmark"></i> {{ __('messages.Save Draft') ?? 'Save Draft' }}
                </button>
                <button type="submit" id="approveSendBtn" class="btn btn-primary btn-lg flex-fill">
                    <i class="bi bi-check-circle"></i> {{ __('messages.Approve & Send to RSC') ?? 'Approve & Send to RSC' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Hidden form for deleting attachments -->
    <form id="deleteAttachmentForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Quill Styles -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        function deleteAttachment(id) {
            if (confirm("{{ __('messages.Are you sure you want to delete this attachment?') ?? 'Are you sure you want to delete this attachment?' }}")) {
                const form = document.getElementById('deleteAttachmentForm');
                form.action = `/committee-reports/attachments/${id}`;
                form.submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const reportSectionsContainer = document.getElementById('reportSectionsContainer');
            const addSectionBtn = document.getElementById('addSectionBtn');
            let sectionIndex = 0;
            const quillEditors = {};

            function addSectionRow(headline = '', content = '') {
                const card = document.createElement('div');
                card.className = 'card mb-3 section-row';
                card.dataset.index = sectionIndex;
                card.innerHTML = `
                    <div class="card-header d-flex justify-content-between align-items-center bg-light">
                        <span class="fw-bold text-secondary">{{ __('messages.Section') ?? 'Section' }} #${sectionIndex + 1}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-section-btn">{{ __('messages.Remove Section') ?? 'Remove' }}</button>
                    </div>
                    <div class="card-body">
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
                reportSectionsContainer.appendChild(card);
                
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
                        alert("{{ __('messages.A report must have at least one section.') ?? 'A report must have at least one section.' }}");
                    }
                });
                
                sectionIndex++;
            }

            // Populate existing sections
            const existingSections = {!! json_encode(old('sections', $report->body_sections)) !!};
            if (existingSections && existingSections.length > 0) {
                existingSections.forEach(section => {
                    addSectionRow(section.headline || '', section.content || '');
                });
            } else {
                addSectionRow();
            }

            addSectionBtn.addEventListener('click', () => addSectionRow());

            // Form Submit
            var form = document.querySelector('#reportForm');
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

            // Dynamic Positions
            const positionsTableBody = document.getElementById('positionsTableBody');
            const addPositionBtn = document.getElementById('addPositionBtn');
            let positionIndex = 0;

            function addPositionRow(name = '', status = '', election = false, memberName = '') {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <input type="text" name="positions[${positionIndex}][name]" class="form-control" value="${name}" required>
                    </td>
                    <td>
                        <input type="text" name="positions[${positionIndex}][member_name]" class="form-control" value="${memberName}">
                    </td>
                    <td>
                        <select name="positions[${positionIndex}][status]" class="form-select" required>
                            <option value="Present" ${status === 'Present' ? 'selected' : ''}>{{__('messages.Present')}}</option>
                            <option value="Absent" ${status === 'Absent' ? 'selected' : ''}>{{__('messages.Absent')}}</option>
                            <option value="Excused" ${status === 'Excused' ? 'selected' : ''}>{{__('messages.Excused')}}</option>
                            <option value="Vacant" ${status === 'Vacant' ? 'selected' : ''}>{{__('messages.Vacant')}}</option>
                        </select>
                    </td>
                    <td class="text-center">
                        <input type="checkbox" name="positions[${positionIndex}][election]" value="1" ${election ? 'checked' : ''} class="form-check-input">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                    </td>
                `;
                positionsTableBody.appendChild(tr);
                positionIndex++;

                tr.querySelector('.remove-row').addEventListener('click', function() {
                    tr.remove();
                });
            }

            addPositionBtn.addEventListener('click', () => addPositionRow());

            // Populate existing positions if they exist, otherwise defaults
            const existingPositions = {!! json_encode($report->positions_status) !!};
            if (existingPositions && existingPositions.length > 0) {
                existingPositions.forEach(pos => {
                    addPositionRow(pos.name, pos.status, pos.election, pos.member_name || '');
                });
            } else {
                const defaultPositions = [
                    "{{ __('messages.Chairman') }}",
                    "{{ __('messages.Vice Chairman') }}",
                    "{{ __('messages.Secretary') }}",
                    "{{ __('messages.Treasurer') }}"
                ];
                defaultPositions.forEach(pos => addPositionRow(pos));
            }

            // Dynamic Attachments
            const attachmentsContainer = document.getElementById('attachmentsContainer');
            const addAttachmentBtn = document.getElementById('addAttachmentBtn');
            const existingCount = {{ $report->attachments->count() }};
            const maxAttachments = 5;

            function updateRemoveButtons() {
                if (!attachmentsContainer) return;
                const rows = attachmentsContainer.querySelectorAll('.attachment-row');
                rows.forEach((row, index) => {
                    const removeBtn = row.querySelector('.remove-attachment-btn');
                    removeBtn.classList.remove('d-none');
                });
                
                if (existingCount + rows.length >= maxAttachments) {
                    addAttachmentBtn.style.display = 'none';
                } else {
                    addAttachmentBtn.style.display = 'inline-block';
                }
            }

            if (addAttachmentBtn) {
                addAttachmentBtn.addEventListener('click', function() {
                    const rows = attachmentsContainer.querySelectorAll('.attachment-row');
                    if (existingCount + rows.length < maxAttachments) {
                        const newRow = document.createElement('div');
                        newRow.className = 'attachment-row mb-2';
                        newRow.innerHTML = `
                            <div class="input-group">
                                <input class="form-control" type="file" name="attachments[]" accept=".pdf,.png,.jpg,.jpeg,.docx,.xlsx">
                                <button type="button" class="btn btn-outline-danger remove-attachment-btn">X</button>
                            </div>
                        `;
                        attachmentsContainer.appendChild(newRow);
                        
                        newRow.querySelector('.remove-attachment-btn').addEventListener('click', function() {
                            newRow.remove();
                            updateRemoveButtons();
                        });
                        
                        updateRemoveButtons();
                    }
                });

                // Handle remove for initial row
                const initialRemoveBtn = attachmentsContainer.querySelector('.remove-attachment-btn');
                if (initialRemoveBtn) {
                    initialRemoveBtn.addEventListener('click', function(e) {
                        const rows = attachmentsContainer.querySelectorAll('.attachment-row');
                        if (rows.length > 1) {
                            e.currentTarget.closest('.attachment-row').remove();
                            updateRemoveButtons();
                        } else {
                            const fileInput = e.currentTarget.closest('.attachment-row').querySelector('input[type="file"]');
                            if (fileInput) fileInput.value = '';
                        }
                    });
                }
            }

            // Disable empty file inputs before submitting
            form.addEventListener('submit', function() {
                if (attachmentsContainer) {
                    attachmentsContainer.querySelectorAll('input[type="file"]').forEach(input => {
                        if (!input.value) {
                            input.disabled = true;
                        }
                    });
                }
            });
        });
    </script>
</x-layout>
