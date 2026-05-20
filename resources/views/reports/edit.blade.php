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
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.Meeting Date') }}</label>
                            <input type="date" name="meeting_date" class="form-control" required value="{{ old('meeting_date', $report->meeting_date->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.Meeting Day Description') }}</label>
                            <input type="text" name="meeting_day_description" class="form-control" placeholder="e.g. Second Sunday" required value="{{ old('meeting_day_description', $report->meeting_day_description) }}">
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

            <!-- Report Body Section -->
            <div class="card mb-4">
                <div class="card-header">{{ __('messages.Report Body') }}</div>
                <div class="card-body">
                    <div id="editor" style="height: 300px;"></div>
                    <input type="hidden" name="body" id="bodyInput" value="{{ old('body', $report->body) }}">
                </div>
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
                    @if($report->attachments->count() < 3)
                        <div class="mb-3">
                            <label for="attachments" class="form-label fw-bold">{{ __('messages.Upload New Attachments') ?? 'Upload New Attachments' }}</label>
                            <input class="form-control" type="file" id="attachments" name="attachments[]" multiple accept=".pdf,.png,.jpg,.jpeg,.docx,.xlsx">
                            <div class="form-text text-muted">
                                {{ __('messages.Max total 3 files') ?? 'Maximum total of 3 files' }} ({{ $report->attachments->count() }} {{ __('messages.currently uploaded') ?? 'currently uploaded' }}). <br>
                                {{ __('messages.Max size') ?? 'Maximum 5MB per file' }}. <br>
                                {{ __('messages.Allowed types') ?? 'Allowed file types' }}: PDF, PNG, JPG, JPEG, DOCX, XLSX
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning py-2 mb-0">
                            <i class="bi bi-exclamation-triangle"></i> {{ __('messages.Max attachments limit reached') ?? 'You have reached the maximum limit of 3 attachments. Delete one to upload a new file.' }}
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
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        function deleteAttachment(id) {
            if (confirm("{{ __('messages.Are you sure you want to delete this attachment?') ?? 'Are you sure you want to delete this attachment?' }}")) {
                const form = document.getElementById('deleteAttachmentForm');
                form.action = `/committee-reports/attachments/${id}`;
                form.submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Quill Init
            var quill = new Quill('#editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, false] }],
                        ['bold', 'italic', 'underline'],
                        ['image', 'code-block'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }]
                    ]
                }
            });

            // Set Initial Quill Content
            const initialBody = {!! json_encode(old('body', $report->body)) !!};
            if (initialBody) {
                quill.root.innerHTML = initialBody;
            }

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
                var body = document.querySelector('#bodyInput');
                body.value = quill.root.innerHTML;
            };

            // Dynamic Positions
            const positionsTableBody = document.getElementById('positionsTableBody');
            const addPositionBtn = document.getElementById('addPositionBtn');
            let positionIndex = 0;

            function addPositionRow(name = '', status = '', election = false) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <input type="text" name="positions[${positionIndex}][name]" class="form-control" placeholder="Position Name" value="${name}" required>
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
                    addPositionRow(pos.name, pos.status, pos.election);
                });
            } else {
                const defaultPositions = ['Chairman', 'Vice Chairman', 'Secretary', 'Treasurer', 'RCM', 'RCM Alternate', 'Literature', 'Activities'];
                defaultPositions.forEach(pos => addPositionRow(pos));
            }
        });
    </script>
</x-layout>
