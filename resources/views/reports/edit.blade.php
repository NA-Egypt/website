<x-layout>
    <x-backhead>{{ (isset($isWorkgroup) && $isWorkgroup) ? __('messages.Edit Workgroup Report') : __('messages.Edit Committee Report') }}</x-backhead>

    <div class="container mt-4">
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm rounded-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(isset($isWorkgroup) && $isWorkgroup)
            <!-- Workgroup Report Guidance Banner -->
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-4 rounded-4 bg-info bg-opacity-10 text-dark">
                <div class="rounded-circle bg-info bg-opacity-25 p-2 d-flex align-items-center justify-content-center text-info" style="width: 48px; height: 48px; min-width: 48px;">
                    <i class="bi bi-diagram-3-fill fs-4"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1 text-primary d-flex align-items-center gap-2">
                        {{ __('messages.Workgroup Report Guidance') }}
                        @if(isset($committee) && $committee && $committee->parent)
                            <span class="badge bg-secondary bg-opacity-75 text-white rounded-pill fw-normal small">
                                {{ __('messages.Parent Committee') }}: {{ app()->getLocale() === 'ar' ? ($committee->parent->ar_name ?: $committee->parent->en_name) : ($committee->parent->en_name ?: $committee->parent->ar_name) }}
                            </span>
                        @endif
                    </h6>
                    <p class="mb-0 small text-muted">{{ __('messages.workgroup_report_guidance_text') }}</p>
                </div>
            </div>
        @endif

        @if($report->review_notes && auth()->check() && !auth()->user()->hasRole('ServiceBody') && !auth()->user()->hasRole('gsr'))
            <div class="alert alert-warning border-start border-warning border-4 mb-4 shadow-sm rounded-3">
                <h5 class="alert-heading fw-bold text-dark"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>{{ __('messages.Review Notes') ?? 'RSC Review Notes / Comments' }}</h5>
                <p class="mb-0 text-dark">{{ $report->review_notes }}</p>
            </div>
        @endif

        <form action="{{ route('committee-reports.update', $report->id) }}" method="POST" id="reportForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" id="statusInput" value="{{ $report->status }}">

            <!-- Container for embedded workgroup report IDs -->
            <div id="embeddedWorkgroupReportsInputs">
                @if($report->embeddedWorkgroupReports && $report->embeddedWorkgroupReports->isNotEmpty())
                    @foreach($report->embeddedWorkgroupReports as $ewr)
                        <input type="hidden" name="embedded_workgroup_report_ids[]" value="{{ $ewr->id }}" id="input-wg-{{ $ewr->id }}">
                    @endforeach
                @endif
            </div>

            <div class="card mb-4 shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-light py-3 border-bottom">
                    @if($isRsc)
                        <label class="form-label fw-bold">{{ __('messages.Select Committee') }}</label>
                        <select name="service_committee_id" class="form-select form-select-lg" required>
                            <option value="">{{ __('messages.Choose a Committee...') }}</option>
                            @foreach($committees as $c)
                                <option value="{{ $c->id }}" {{ $report->service_committee_id == $c->id ? 'selected' : '' }}>
                                    {{ app()->getLocale() === 'ar' ? ($c->ar_name ?: $c->en_name) : ($c->en_name ?: $c->ar_name) }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi {{ (isset($isWorkgroup) && $isWorkgroup) ? 'bi-diagram-3-fill text-success' : 'bi-building text-primary' }} fs-4"></i>
                                <h5 class="mb-0 fw-bold">{{ app()->getLocale() === 'ar' ? ($report->serviceCommittee->ar_name ?: $report->serviceCommittee->en_name) : ($report->serviceCommittee->en_name ?: $report->serviceCommittee->ar_name) }}</h5>
                            </div>
                            <span class="badge {{ (isset($isWorkgroup) && $isWorkgroup) ? 'bg-success' : 'bg-primary' }} bg-opacity-10 {{ (isset($isWorkgroup) && $isWorkgroup) ? 'text-success' : 'text-primary' }} px-3 py-2 rounded-pill fw-semibold">
                                {{ (isset($isWorkgroup) && $isWorkgroup) ? __('messages.Workgroup Report') : __('messages.Committee Reports') }}
                            </span>
                        </div>
                    @endif
                </div>
                <div class="card-body p-4">
                    <div class="row mb-3 align-items-end g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('messages.Report Date') }}</label>
                            <input type="text" class="form-control bg-light" readonly value="{{ optional($report->report_date)->format('Y-m-d') ?? $report->created_at->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('messages.Meeting Date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="meeting_date" class="form-control" required value="{{ old('meeting_date', $report->meeting_date ? $report->meeting_date->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('messages.Meeting Day Description') }} <span class="text-danger">*</span></label>
                            <input type="text" name="meeting_day_description" class="form-control" placeholder="{{__('messages.meeting_day_desc')}}" required value="{{ old('meeting_day_description', $report->meeting_day_description) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_exceptional" id="is_exceptional" value="1" {{ old('is_exceptional', $report->is_exceptional) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-danger ms-2" for="is_exceptional">
                                    {{ __('messages.Exceptional Meeting') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Positions Section -->
            <div class="card mb-4 shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-people-fill text-primary"></i>
                        {{ __('messages.Positions Status') }}
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="addPositionBtn">
                        <i class="bi bi-plus-lg me-1"></i> {{ __('messages.Add Position') }}
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('messages.Position Name') }}</th>
                                    <th>{{ __('messages.Member Name') }}</th>
                                    <th>{{ __('messages.Status') }}</th>
                                    <th>{{ __('messages.Open for Election') }}</th>
                                    <th style="width: 60px;"></th>
                                </tr>
                            </thead>
                            <tbody id="positionsTableBody">
                                <!-- Dynamic Rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Attended Members Section -->
            <div class="card mb-4 shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-light py-3 border-bottom fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-person-lines-fill text-success"></i>
                    {{ __('messages.Attended Members') }}
                </div>
                <div class="card-body p-4">
                    <textarea name="attended_members" class="form-control" rows="4" placeholder="{{ __('messages.Enter names of members who attended...') }}">{{ old('attended_members', $report->attended_members) }}</textarea>
                </div>
            </div>

            <!-- Report Body Sections (Repeatable + Embedded Workgroups) -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-richtext-fill text-primary"></i>
                        {{ __('messages.Report Sections') }}
                    </h5>
                    <div class="d-flex gap-2">
                        @if(isset($availableWorkgroupDrafts) && $availableWorkgroupDrafts->isNotEmpty())
                            <button type="button" class="btn btn-success shadow-sm rounded-pill px-3" data-bs-toggle="offcanvas" data-bs-target="#embedWorkgroupDrawer">
                                <i class="bi bi-box-arrow-in-down me-1"></i> {{ __('messages.Embed Workgroup Report') }}
                                <span class="badge bg-white text-success rounded-pill ms-1" id="availableDraftsCountBadge">{{ $availableWorkgroupDrafts->count() }}</span>
                            </button>
                        @endif
                        <button type="button" class="btn btn-outline-primary rounded-pill px-3" id="addSectionBtn">
                            <i class="bi bi-plus-lg me-1"></i> {{ __('messages.Add Section') }}
                        </button>
                    </div>
                </div>

                <div id="reportSectionsContainer">
                    <!-- Dynamic standard sections and embedded workgroup cards will appear here -->
                </div>
            </div>

            <!-- Attachments Section -->
            <div class="card mb-4 shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-light py-3 border-bottom fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-paperclip text-info"></i>
                    {{ __('messages.Attachments') }}
                </div>
                <div class="card-body p-4">
                    <!-- Existing Attachments -->
                    @if($report->attachments->isNotEmpty())
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">{{ __('messages.Existing Attachments') ?? 'Existing Attachments' }}</label>
                            <ul class="list-group">
                                @foreach($report->attachments as $attachment)
                                    <li class="list-group-item d-flex justify-content-between align-items-center rounded-3 mb-2 border shadow-sm">
                                        <div>
                                            <i class="bi bi-file-earmark-arrow-down text-primary me-2 fs-5"></i>
                                            <a href="{{ route('committee-reports.downloadAttachment', $attachment->id) }}" class="text-decoration-none fw-semibold" target="_blank">
                                                {{ $attachment->original_name }}
                                            </a>
                                            <span class="text-muted ms-2 small">({{ number_format($attachment->file_size / 1024, 1) }} KB)</span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" onclick="deleteAttachment({{ $attachment->id }})">
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
                                        <button type="button" class="btn btn-outline-danger remove-attachment-btn d-none">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 mt-2" id="addAttachmentBtn">
                                <i class="bi bi-plus-lg me-1"></i> {{ __('messages.Add Attachment') }}
                            </button>
                            <div class="form-text text-muted mt-2">
                                {{ __('messages.Max total 5 files') ?? 'Maximum total of 5 files' }} ({{ $report->attachments->count() }} {{ __('messages.currently uploaded') ?? 'currently uploaded' }}). <br>
                                {{ __('messages.Max size') ?? 'Maximum 5MB per file' }}. <br>
                                {{ __('messages.Allowed types') ?? 'Allowed file types' }}: PDF, PNG, JPG, JPEG, DOCX, XLSX
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning py-2 mb-0 rounded-3">
                            <i class="bi bi-info-circle me-1"></i> {{ __('messages.Max 5 attachments reached') ?? 'You have reached the maximum allowed number of attachments (5).' }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer Section -->
            <div class="card mb-4 shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-light py-3 border-bottom fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-layout-text-window-reverse text-secondary"></i>
                    {{ __('messages.Report Footer') }}
                </div>
                <div class="card-body p-4">
                    <textarea name="footer" class="form-control" rows="3" placeholder="{{ __('messages.Enter report-specific footer text (overrides default committee footer)...') ?? 'Enter report-specific footer text...' }}">{{ old('footer', $report->footer) }}</textarea>
                    @if(isset($committee) && $committee && $committee->default_footer)
                        <div class="form-text text-muted mt-2">
                            <strong>{{ __('messages.Default Committee Footer') ?? 'Default Committee Footer' }}:</strong> {{ $committee->default_footer }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex gap-3 mb-5">
                <button type="submit" id="saveDraftBtn" class="btn btn-outline-secondary btn-lg flex-fill rounded-pill py-3 fw-semibold">
                    <i class="bi bi-file-earmark me-1"></i> {{ (isset($isWorkgroup) && $isWorkgroup) ? __('messages.Save Draft for Committee') : __('messages.Save Draft') }}
                </button>
                @if(isset($isWorkgroup) && $isWorkgroup)
                    <button type="submit" id="submitToCommitteeBtn" class="btn btn-success btn-lg flex-fill rounded-pill py-3 fw-bold shadow-sm">
                        <i class="bi bi-send-check me-1"></i> {{ __('messages.Submit to Parent Committee') }}
                    </button>
                @else
                    <button type="submit" id="approveSendBtn" class="btn btn-primary btn-lg flex-fill rounded-pill py-3 fw-bold shadow-sm">
                        <i class="bi bi-check-circle me-1"></i> {{ __('messages.Approve & Send to RSC') }}
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Offcanvas Visual Drawer for Embedding Workgroup Reports -->
    @if(isset($availableWorkgroupDrafts) && $availableWorkgroupDrafts->isNotEmpty())
        @php
            $isRtl = app()->getLocale() === 'ar';
            $embeddedIdsArray = $report->embeddedWorkgroupReports ? $report->embeddedWorkgroupReports->pluck('id')->toArray() : [];
            $draftsData = $availableWorkgroupDrafts->map(function($r) use ($embeddedIdsArray) {
                return [
                    'id' => $r->id,
                    'wg_name' => $r->serviceCommittee ? (app()->getLocale() === 'ar' ? ($r->serviceCommittee->ar_name ?: $r->serviceCommittee->en_name) : ($r->serviceCommittee->en_name ?: $r->serviceCommittee->ar_name)) : 'Workgroup',
                    'meeting_date' => $r->meeting_date ? \App\Services\DateNumberHelper::translatedFormat($r->meeting_date, 'Y-m-d') : '',
                    'meeting_day_description' => $r->meeting_day_description,
                    'sections' => $r->body_sections,
                    'positions' => $r->positions_status,
                    'attended_members' => $r->attended_members,
                    'is_embedded_initially' => in_array($r->id, $embeddedIdsArray),
                    'attachments' => $r->attachments->map(function($att) {
                        return [
                            'id' => $att->id,
                            'name' => $att->original_name,
                            'size' => number_format($att->file_size / 1024, 1) . ' KB',
                        ];
                    })->values(),
                ];
            })->values();
        @endphp

        <div class="offcanvas {{ $isRtl ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg border-0" tabindex="-1" id="embedWorkgroupDrawer" style="max-width: 100%; width: 880px;">
            <div class="offcanvas-header bg-primary bg-gradient text-white p-3 p-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-25 p-2 d-flex align-items-center justify-content-center text-white" style="width: 44px; height: 44px;">
                        <i class="bi bi-box-arrow-in-down fs-4"></i>
                    </div>
                    <div>
                        <h5 class="offcanvas-title fw-bold text-white mb-0">{{ __('messages.Embed Workgroup Report') }}</h5>
                        <small class="text-white-50">{{ __('messages.Select a workgroup draft to preview and embed') }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body p-0">
                <div class="row g-0 h-100">
                    <!-- Left List Pane -->
                    <div class="col-md-5 border-end bg-light p-3 d-flex flex-column" style="max-height: calc(100vh - 85px); overflow-y: auto;">
                        <div class="mb-3">
                            <input type="text" id="drawerSearchInput" class="form-control rounded-pill" placeholder="{{ __('messages.Search...') }}">
                        </div>

                        <div class="list-group list-group-flush gap-2" id="drawerDraftsList">
                            @foreach($draftsData as $index => $wg)
                                <a href="javascript:void(0)" class="list-group-item list-group-item-action border rounded-3 p-3 shadow-sm draft-item {{ $index === 0 ? 'active' : '' }}" 
                                   id="drawer-item-{{ $wg['id'] }}" 
                                   data-report-id="{{ $wg['id'] }}" 
                                   onclick="selectDrawerReport({{ $wg['id'] }})">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-bold mb-0 text-truncate" style="max-width: 200px;">{{ $wg['wg_name'] }}</h6>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill small">{{ count($wg['sections'] ?? []) }} {{ __('messages.sections') ?? 'sections' }}</span>
                                    </div>
                                    <div class="text-muted small mb-2">
                                        <i class="bi bi-calendar3 me-1"></i> {{ $wg['meeting_date'] }}
                                        @if($wg['meeting_day_description'])
                                            &bull; {{ $wg['meeting_day_description'] }}
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill small">
                                            <i class="bi bi-paperclip me-1"></i> {{ count($wg['attachments'] ?? []) }}
                                        </span>
                                        <span class="badge {{ $wg['is_embedded_initially'] ? 'bg-success bg-opacity-25 text-success' : 'bg-primary bg-opacity-25 text-primary' }} rounded-pill small px-2 py-1 embed-status-badge">
                                            <i class="bi {{ $wg['is_embedded_initially'] ? 'bi-check2' : 'bi-eye' }} me-1"></i> 
                                            {{ $wg['is_embedded_initially'] ? __('messages.Already Embedded') : __('messages.Draft Preview') }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Right Preview Pane -->
                    <div class="col-md-7 p-3 p-md-4 d-flex flex-column justify-content-between" style="max-height: calc(100vh - 85px); overflow-y: auto;">
                        <div id="drawerPreviewContent">
                            <!-- Populated dynamically via JS -->
                        </div>

                        <!-- Sticky Embed Action -->
                        <div class="pt-3 border-top mt-3 d-flex justify-content-end gap-2 bg-white sticky-bottom">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="offcanvas">
                                {{ __('messages.Close') }}
                            </button>
                            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" id="drawerEmbedBtn" onclick="embedCurrentDrawerReport()">
                                <i class="bi bi-box-arrow-in-down me-1"></i> {{ __('messages.Embed Full Report') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Quill Styles & Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reportSectionsContainer = document.getElementById('reportSectionsContainer');
            const addSectionBtn = document.getElementById('addSectionBtn');
            let sectionIndex = 0;
            const quillEditors = {};

            // Add standard section row
            function addSectionRow(headline = '', content = '', isEmbedded = false, workgroupBadge = '', wgReportId = null) {
                const card = document.createElement('div');
                card.className = `card mb-3 section-row shadow-sm border-0 rounded-4 overflow-hidden ${isEmbedded ? 'border-start border-success border-4' : ''}`;
                card.dataset.index = sectionIndex;
                if (wgReportId) {
                    card.dataset.wgReportId = wgReportId;
                }

                card.innerHTML = `
                    <div class="card-header d-flex justify-content-between align-items-center bg-light py-3 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            ${workgroupBadge ? `<span class="badge bg-success rounded-pill px-3 py-1"><i class="bi bi-link-45deg me-1"></i> ${workgroupBadge}</span>` : ''}
                            <span class="fw-bold text-secondary">${workgroupBadge ? '' : `{{ __('messages.Section') }} #${sectionIndex + 1}`}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-section-btn rounded-pill px-3">
                            <i class="bi bi-trash me-1"></i> ${isEmbedded ? '{{ __("messages.Un-embed") }}' : '{{ __("messages.Remove Section") }}'}
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('messages.Section Headline (Optional)') }}</label>
                            <input type="text" name="sections[${sectionIndex}][headline]" class="form-control" placeholder="{{ __('messages.Enter optional headline...') }}" value="${headline}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('messages.Section Content') }}</label>
                            <div class="quill-editor mb-2" id="quill-editor-${sectionIndex}" style="height: 250px;"></div>
                            <input type="hidden" name="sections[${sectionIndex}][content]" class="section-content-input" id="section-content-${sectionIndex}">
                        </div>
                    </div>
                `;
                reportSectionsContainer.appendChild(card);

                const editorId = `#quill-editor-${sectionIndex}`;
                const quill = new Quill(editorId, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'align': [] }],
                            ['clean']
                        ]
                    }
                });

                if (content) {
                    quill.root.innerHTML = content;
                }

                quillEditors[sectionIndex] = quill;
                const currentIndex = sectionIndex;

                card.querySelector('.remove-section-btn').addEventListener('click', function() {
                    if (isEmbedded && wgReportId) {
                        if (!confirm('{{ __("messages.Are you sure you want to un-embed this workgroup report? It will return to available drafts.") }}')) {
                            return;
                        }
                        unEmbedWorkgroupReport(wgReportId);
                        return;
                    }

                    if (document.querySelectorAll('.section-row').length > 1) {
                        card.remove();
                        delete quillEditors[currentIndex];
                        document.querySelectorAll('.section-row:not([data-wg-report-id])').forEach((row, idx) => {
                            const span = row.querySelector('.card-header span.fw-bold');
                            if (span) span.textContent = `{{ __('messages.Section') }} #${idx + 1}`;
                        });
                    } else {
                        alert("{{ __('messages.A report must have at least one section.') }}");
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

            // Form Submit handler
            const form = document.querySelector('#reportForm');
            const statusInput = document.getElementById('statusInput');
            const saveDraftBtn = document.getElementById('saveDraftBtn');
            const approveSendBtn = document.getElementById('approveSendBtn');
            const submitToCommitteeBtn = document.getElementById('submitToCommitteeBtn');

            if (saveDraftBtn) {
                saveDraftBtn.addEventListener('click', () => { statusInput.value = 'draft'; });
            }
            if (submitToCommitteeBtn) {
                submitToCommitteeBtn.addEventListener('click', () => { statusInput.value = 'draft'; });
            }
            if (approveSendBtn) {
                approveSendBtn.addEventListener('click', () => { statusInput.value = 'submitted'; });
            }

            form.onsubmit = function() {
                document.querySelectorAll('.section-row').forEach(row => {
                    const idx = row.dataset.index;
                    const contentInput = document.getElementById(`section-content-${idx}`);
                    if (quillEditors[idx] && contentInput) {
                        contentInput.value = quillEditors[idx].root.innerHTML;
                    }
                });
            };

            // Dynamic Positions Table
            const positionsTableBody = document.getElementById('positionsTableBody');
            const addPositionBtn = document.getElementById('addPositionBtn');
            let positionIndex = 0;

            function addPositionRow(name = '', status = 'Present', election = false, memberName = '') {
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
                        <button type="button" class="btn btn-outline-danger btn-sm remove-row rounded-circle">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </td>
                `;
                positionsTableBody.appendChild(tr);
                positionIndex++;

                tr.querySelector('.remove-row').addEventListener('click', function() {
                    tr.remove();
                });
            }

            addPositionBtn.addEventListener('click', () => addPositionRow());

            // Populate existing positions or defaults
            const existingPositions = {!! json_encode(old('positions', $report->positions_status)) !!};
            if (existingPositions && existingPositions.length > 0) {
                existingPositions.forEach(pos => {
                    addPositionRow(pos.name || '', pos.status || 'Present', pos.election || false, pos.member_name || '');
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

            // Attachments Handling
            const attachmentsContainer = document.getElementById('attachmentsContainer');
            const addAttachmentBtn = document.getElementById('addAttachmentBtn');
            const existingCount = {{ $report->attachments->count() }};
            const maxAttachments = 5;

            function updateRemoveButtons() {
                if (!attachmentsContainer) return;
                const rows = attachmentsContainer.querySelectorAll('.attachment-row');
                rows.forEach(row => {
                    const removeBtn = row.querySelector('.remove-attachment-btn');
                    removeBtn.classList.remove('d-none');
                });
                if (addAttachmentBtn) {
                    addAttachmentBtn.style.display = (existingCount + rows.length >= maxAttachments) ? 'none' : 'inline-block';
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
                                <button type="button" class="btn btn-outline-danger remove-attachment-btn">
                                    <i class="bi bi-trash"></i>
                                </button>
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

            form.addEventListener('submit', function() {
                if (attachmentsContainer) {
                    attachmentsContainer.querySelectorAll('input[type="file"]').forEach(input => {
                        if (!input.value) input.disabled = true;
                    });
                }
            });

            // Delete existing attachment via AJAX
            window.deleteAttachment = function(id) {
                if (!confirm("{{ __('messages.Are you sure you want to delete this attachment?') ?? 'Are you sure you want to delete this attachment?' }}")) {
                    return;
                }

                fetch(`/committee-reports/attachments/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Error deleting attachment');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting attachment');
                });
            };

            // Offcanvas Workgroup Drawer & Embedding Logic
            @if(isset($availableWorkgroupDrafts) && $availableWorkgroupDrafts->isNotEmpty())
                const availableDrafts = @json($draftsData);
                const embeddedReportIds = new Set(@json($embeddedIdsArray));
                let activeSelectedReportId = availableDrafts.length > 0 ? availableDrafts[0].id : null;

                window.selectDrawerReport = function(reportId) {
                    activeSelectedReportId = reportId;
                    document.querySelectorAll('.draft-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    const activeItem = document.getElementById(`drawer-item-${reportId}`);
                    if (activeItem) activeItem.classList.add('active');

                    renderDrawerPreview(reportId);
                };

                function renderDrawerPreview(reportId) {
                    const r = availableDrafts.find(d => d.id === reportId);
                    const previewContainer = document.getElementById('drawerPreviewContent');
                    const embedBtn = document.getElementById('drawerEmbedBtn');
                    if (!r || !previewContainer) return;

                    const isAlreadyEmbedded = embeddedReportIds.has(reportId);

                    let sectionsHtml = '';
                    if (r.sections && r.sections.length > 0) {
                        sectionsHtml = r.sections.map((sec, idx) => `
                            <div class="card mb-3 border-0 bg-light rounded-3 p-3">
                                <h6 class="fw-bold text-primary mb-2">${sec.headline || `{{ __('messages.Section') }} #${idx + 1}`}</h6>
                                <div class="small text-muted">${sec.content || '<em>{{ __("messages.No content") }}</em>'}</div>
                            </div>
                        `).join('');
                    } else {
                        sectionsHtml = `<p class="text-muted small"><em>{{ __('messages.No sections in this draft report') }}</em></p>`;
                    }

                    let positionsHtml = '';
                    if (r.positions && r.positions.length > 0) {
                        positionsHtml = `
                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-bordered text-center small mb-0">
                                    <thead class="table-light"><tr><th>{{ __('messages.Position') }}</th><th>{{ __('messages.Member Name') }}</th><th>{{ __('messages.Status') }}</th></tr></thead>
                                    <tbody>
                                        ${r.positions.map(p => `<tr><td>${p.name || '-'}</td><td>${p.member_name || '-'}</td><td><span class="badge bg-secondary">${p.status || '-'}</span></td></tr>`).join('')}
                                    </tbody>
                                </table>
                            </div>
                        `;
                    }

                    let attachmentsHtml = '';
                    if (r.attachments && r.attachments.length > 0) {
                        attachmentsHtml = `
                            <div class="mb-3">
                                <strong class="small d-block mb-1">{{ __('messages.Attachments') }}:</strong>
                                <div class="d-flex flex-wrap gap-2">
                                    ${r.attachments.map(att => `<span class="badge bg-white text-dark border p-2"><i class="bi bi-file-earmark me-1 text-primary"></i> ${att.name} (${att.size})</span>`).join('')}
                                </div>
                            </div>
                        `;
                    }

                    previewContainer.innerHTML = `
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                            <div>
                                <h5 class="fw-bold text-primary mb-1">${r.wg_name}</h5>
                                <div class="text-muted small">
                                    <i class="bi bi-calendar3 me-1"></i> ${r.meeting_date}
                                    ${r.meeting_day_description ? `&bull; ${r.meeting_day_description}` : ''}
                                </div>
                            </div>
                            ${isAlreadyEmbedded ? '<span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check2-circle me-1"></i> {{ __("messages.Already Embedded") }}</span>' : '<span class="badge bg-warning bg-opacity-75 text-dark rounded-pill px-3 py-2">{{ __("messages.Draft for Committee") }}</span>'}
                        </div>

                        ${r.attended_members ? `
                            <div class="alert alert-light border rounded-3 p-3 mb-3">
                                <strong class="small text-secondary d-block mb-1"><i class="bi bi-people me-1"></i> {{ __('messages.Attendees') }}:</strong>
                                <span class="small text-dark">${r.attended_members}</span>
                            </div>
                        ` : ''}

                        ${positionsHtml}

                        <h6 class="fw-bold text-secondary mb-2">{{ __('messages.Report Sections') }} (${r.sections ? r.sections.length : 0})</h6>
                        <div class="mb-3">${sectionsHtml}</div>

                        ${attachmentsHtml}
                    `;

                    if (embedBtn) {
                        if (isAlreadyEmbedded) {
                            embedBtn.disabled = true;
                            embedBtn.className = 'btn btn-secondary rounded-pill px-4';
                            embedBtn.innerHTML = '<i class="bi bi-check2 me-1"></i> {{ __("messages.Already Embedded") }}';
                        } else {
                            embedBtn.disabled = false;
                            embedBtn.className = 'btn btn-success rounded-pill px-4 fw-bold shadow-sm';
                            embedBtn.innerHTML = '<i class="bi bi-box-arrow-in-down me-1"></i> {{ __("messages.Embed Full Report") }}';
                        }
                    }
                }

                window.embedCurrentDrawerReport = function() {
                    if (!activeSelectedReportId) return;
                    embedWorkgroupReport(activeSelectedReportId);
                };

                window.embedWorkgroupReport = function(reportId) {
                    const r = availableDrafts.find(d => d.id === reportId);
                    if (!r || embeddedReportIds.has(reportId)) return;

                    embeddedReportIds.add(reportId);

                    // Add hidden input for form submission
                    const hiddenContainer = document.getElementById('embeddedWorkgroupReportsInputs');
                    let input = document.getElementById(`input-wg-${reportId}`);
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'embedded_workgroup_report_ids[]';
                        input.value = reportId;
                        input.id = `input-wg-${reportId}`;
                        hiddenContainer.appendChild(input);
                    }

                    // Add each section as an intact embedded row
                    if (r.sections && r.sections.length > 0) {
                        r.sections.forEach((sec, idx) => {
                            const badge = `${r.wg_name} (${r.meeting_date})`;
                            const headline = sec.headline ? `[${r.wg_name}] ${sec.headline}` : `[${r.wg_name}] {{ __('messages.Workgroup Report') }}`;
                            addSectionRow(headline, sec.content || '', true, badge, reportId);
                        });
                    } else {
                        const badge = `${r.wg_name} (${r.meeting_date})`;
                        addSectionRow(`[${r.wg_name}] {{ __('messages.Workgroup Report') }}`, '', true, badge, reportId);
                    }

                    // Append attendees to attended_members textarea
                    if (r.attended_members) {
                        const attendedTextarea = document.querySelector('textarea[name="attended_members"]');
                        if (attendedTextarea) {
                            const prefix = attendedTextarea.value.trim() ? '\n\n' : '';
                            attendedTextarea.value += prefix + `[${r.wg_name}]: ` + r.attended_members;
                        }
                    }

                    // Update drawer item visual state
                    const drawerItem = document.getElementById(`drawer-item-${reportId}`);
                    if (drawerItem) {
                        const badge = drawerItem.querySelector('.embed-status-badge');
                        if (badge) {
                            badge.className = 'badge bg-success bg-opacity-25 text-success rounded-pill small px-2 py-1 embed-status-badge';
                            badge.innerHTML = '<i class="bi bi-check2 me-1"></i> {{ __("messages.Already Embedded") }}';
                        }
                    }

                    renderDrawerPreview(reportId);

                    // Close offcanvas drawer
                    const drawerEl = document.getElementById('embedWorkgroupDrawer');
                    const offcanvasInstance = bootstrap.Offcanvas.getInstance(drawerEl);
                    if (offcanvasInstance) offcanvasInstance.hide();
                };

                window.unEmbedWorkgroupReport = function(reportId) {
                    embeddedReportIds.delete(reportId);

                    // Remove hidden input
                    const input = document.getElementById(`input-wg-${reportId}`);
                    if (input) input.remove();

                    // Remove all section cards corresponding to this workgroup
                    document.querySelectorAll(`.section-row[data-wg-report-id="${reportId}"]`).forEach(card => {
                        const idx = card.dataset.index;
                        delete quillEditors[idx];
                        card.remove();
                    });

                    // Update drawer item state
                    const drawerItem = document.getElementById(`drawer-item-${reportId}`);
                    if (drawerItem) {
                        const badge = drawerItem.querySelector('.embed-status-badge');
                        if (badge) {
                            badge.className = 'badge bg-primary bg-opacity-25 text-primary rounded-pill small px-2 py-1 embed-status-badge';
                            badge.innerHTML = '<i class="bi bi-eye me-1"></i> {{ __("messages.Draft Preview") }}';
                        }
                    }

                    renderDrawerPreview(reportId);
                };

                // Search in Drawer list
                const drawerSearchInput = document.getElementById('drawerSearchInput');
                if (drawerSearchInput) {
                    drawerSearchInput.addEventListener('input', function() {
                        const q = this.value.trim().toLowerCase();
                        document.querySelectorAll('.draft-item').forEach(item => {
                            const text = item.textContent.toLowerCase();
                            item.style.display = text.includes(q) ? 'block' : 'none';
                        });
                    });
                }

                // Initial render of first draft in drawer
                if (availableDrafts.length > 0) {
                    renderDrawerPreview(availableDrafts[0].id);
                }
            @endif
        });
    </script>
</x-layout>
