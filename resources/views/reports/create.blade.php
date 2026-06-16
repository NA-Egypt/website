<x-layout>
    <x-backhead>{{ __('messages.Create Committee Report') }}</x-backhead>

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

        <form action="{{ route('committee-reports.store') }}" method="POST" id="reportForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="status" id="statusInput" value="draft">

            <div class="card mb-4">
                <div class="card-header bg-light">
                    @if($isRsc)
                        <label class="form-label fw-bold">{{ __('messages.Select Committee') }}</label>
                        <select name="service_committee_id" class="form-select" required>
                            <option value="">{{ __('messages.Choose a Committee...') }}</option>
                            @foreach($committees as $c)
                                <option value="{{ $c->id }}">{{ $c->ar_name }}</option>
                            @endforeach
                        </select>
                    @else
                        <h5 class="mb-0">{{ $committee->ar_name }}</h5>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row mb-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('messages.Report Date') }}</label>
                            <input type="text" class="form-control bg-light" readonly value="{{ now()->toDateString() }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('messages.Meeting Date') }}</label>
                            <input type="date" name="meeting_date" class="form-control" required value="{{ old('meeting_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('messages.Meeting Day Description') }}</label>
                            <input type="text" name="meeting_day_description" class="form-control" placeholder="{{__('messages.meeting_day_desc')}}" required value="{{ old('meeting_day_description') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_exceptional" id="is_exceptional" value="1" {{ old('is_exceptional') ? 'checked' : '' }}>
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
                    <textarea name="attended_members" class="form-control" rows="4" placeholder="{{ __('messages.Enter names of members who attended...') }}">{{ old('attended_members') }}</textarea>
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
                    <div class="mb-3">
                        <label for="attachments" class="form-label fw-bold">{{ __('messages.Upload Attachments') ?? 'Upload Attachments' }}</label>
                        <input class="form-control" type="file" id="attachments" name="attachments[]" multiple accept=".pdf,.png,.jpg,.jpeg,.docx,.xlsx">
                        <div class="form-text text-muted">
                            {{ __('messages.Max 3 files, 5MB each') ?? 'Maximum 3 files, 5MB per file' }}. <br>
                            {{ __('messages.Allowed types') ?? 'Allowed file types' }}: PDF, PNG, JPG, JPEG, DOCX, XLSX
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Section -->
            <div class="card mb-4">
                <div class="card-header">{{ __('messages.Report Footer') ?? 'Report Footer' }}</div>
                <div class="card-body">
                    <textarea name="footer" class="form-control" rows="3" placeholder="{{ __('messages.Enter report-specific footer text (overrides default committee footer)...') ?? 'Enter report-specific footer text (overrides default committee footer)...' }}">{{ old('footer') }}</textarea>
                    @if(isset($committee) && $committee && $committee->default_footer)
                        <div class="form-text text-muted mt-2">
                            <strong>{{ __('messages.Default Committee Footer') ?? 'Default Committee Footer' }}:</strong> {{ $committee->default_footer }}
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

    <!-- Quill Styles -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
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

            // Add initial section
            addSectionRow();

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

            // Add default positions
            const defaultPositions = [
                "{{ __('messages.Chairman') }}",
                "{{ __('messages.Vice Chairman') }}",
                "{{ __('messages.Secretary') }}",
                "{{ __('messages.Treasurer') }}"
            ];
            defaultPositions.forEach(pos => addPositionRow(pos));
        });
    </script>
</x-layout>
