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
                            <input type="text" name="meeting_day_description" class="form-control" placeholder="e.g. first sunday" required value="{{ old('meeting_day_description') }}">
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
                    <input type="hidden" name="body" id="bodyInput">
                </div>
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
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
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

            // Add default positions
            const defaultPositions = ['Chairman', 'Vice Chairman', 'Secretary', 'Treasurer', 'RCM', 'RCM Alternate', 'Literature', 'Activities'];
            defaultPositions.forEach(pos => addPositionRow(pos));
        });
    </script>
</x-layout>
