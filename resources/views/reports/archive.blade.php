<x-layout>
    <x-backhead>{{ __('messages.Reports Archive') ?? 'Reports Archive' }}</x-backhead>

    <div class="container mt-4">
        <!-- Advanced Filter & Search Card -->
        <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-funnel-fill me-2"></i>{{ __('messages.Filter Options') ?? 'Filter Options' }}
                    </h5>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="{{ request()->anyFilled(['search', 'committee_id', 'start_date', 'end_date', 'exceptional']) ? 'true' : 'false' }}" aria-controls="filterCollapse">
                        <i class="bi bi-filter"></i> {{ __('messages.Toggle Filters') ?? 'Toggle Filters' }}
                    </button>
                </div>
            </div>
            <div class="collapse {{ request()->anyFilled(['search', 'committee_id', 'start_date', 'end_date', 'exceptional']) ? 'show' : '' }}" id="filterCollapse">
                <div class="card-body p-4 border-top">
                    <form action="{{ route('committee-reports.archive') }}" method="GET" id="searchFilterForm">
                        <div class="row g-3">
                            <div class="col-12 col-md-6 col-lg-3">
                                <label for="search" class="form-label fw-semibold text-muted">{{ __('messages.Search') ?? 'Search' }}</label>
                                <input type="text" name="search" id="search" class="form-control rounded-3" value="{{ request('search') }}" placeholder="{{ __('messages.Search by day or body') ?? 'Search by day or body...' }}">
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <label for="committee_id" class="form-label fw-semibold text-muted">{{ __('messages.Committee') ?? 'Committee' }}</label>
                                <select name="committee_id" id="committee_id" class="form-select rounded-3">
                                    <option value="">{{ __('messages.All Committees') ?? 'All Committees' }}</option>
                                    @foreach($committees as $committee)
                                        <option value="{{ $committee->id }}" {{ request('committee_id') == $committee->id ? 'selected' : '' }}>
                                            {{ $committee->ar_name ?? $committee->en_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 col-lg-2">
                                <label for="start_date" class="form-label fw-semibold text-muted">{{ __('messages.Start Date') ?? 'Start Date' }}</label>
                                <input type="date" name="start_date" id="start_date" class="form-control rounded-3" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-12 col-md-6 col-lg-2">
                                <label for="end_date" class="form-label fw-semibold text-muted">{{ __('messages.End Date') ?? 'End Date' }}</label>
                                <input type="date" name="end_date" id="end_date" class="form-control rounded-3" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-12 col-md-6 col-lg-2 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="exceptional" id="exceptional" value="1" {{ request('exceptional') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-muted" for="exceptional">
                                        {{ __('messages.Exceptional') ?? 'Exceptional' }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('committee-reports.archive') }}" class="btn btn-light px-4 rounded-pill">
                               <i class="bi bi-x-circle me-1"></i> {{ __('messages.Reset') ?? 'Reset' }}
                            </a>
                            <button type="submit" class="btn btn-primary px-4 rounded-pill">
                                <i class="bi bi-search me-1"></i> {{ __('messages.Search') ?? 'Search' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Folder Explorer Interface -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-light border-bottom p-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <nav aria-label="breadcrumb" class="mb-0">
                        <ol class="breadcrumb mb-0" id="archive-breadcrumbs">
                            <li class="breadcrumb-item"><a href="#" onclick="navigateToPath('Archives'); return false;" class="text-primary fw-semibold"><i class="bi bi-folder-fill me-1"></i></a></li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-muted small" id="items-count-badge"></div>
                        <a href="?refresh=1" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Sync files and reload storage box archives">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="folder-contents-list">
                    <!-- Dynamic explorer files and folders will load here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Offline Preview Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.1.15/dist/docx-preview.min.js"></script>

    <!-- JS to handle interactive client-side navigation within the tree -->
    <script>
        const archiveTree = @json($archiveTree);
        let currentPath = 'Archives';

        function getNestedNodeByPath(path) {
            if (path === 'Archives') {
                return { is_dir: true, children: archiveTree, name: 'Archives', path: 'Archives' };
            }
            
            // Traverse down
            let parts = path.replace(/^Archives\/?/, '').split('/');
            if (parts.length === 1 && parts[0] === '') {
                return { is_dir: true, children: archiveTree, name: 'Archives', path: 'Archives' };
            }

            let currentNode = { children: archiveTree };
            for (let part of parts) {
                if (currentNode && currentNode.children) {
                    let found = currentNode.children.find(child => child.name === part && child.is_dir);
                    if (found) {
                        currentNode = found;
                    } else {
                        return null;
                    }
                } else {
                    return null;
                }
            }
            return currentNode;
        }

        function formatBytes(bytes, decimals = 1) {
            if (!bytes || bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        function getFileIcon(name) {
            const ext = name.split('.').pop().toLowerCase();
            switch (ext) {
                case 'pdf':
                    return '<i class="bi bi-file-earmark-pdf-fill text-danger fs-4"></i>';
                case 'xlsx':
                case 'xls':
                case 'csv':
                    return '<i class="bi bi-file-earmark-excel-fill text-success fs-4"></i>';
                case 'docx':
                case 'doc':
                    return '<i class="bi bi-file-earmark-word-fill text-primary fs-4"></i>';
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    return '<i class="bi bi-file-earmark-image text-warning fs-4"></i>';
                case 'mov':
                case 'mp4':
                    return '<i class="bi bi-file-earmark-play-fill text-info fs-4"></i>';
                default:
                    return '<i class="bi bi-file-earmark text-secondary fs-4"></i>';
            }
        }

        function renderExplorer() {
            const node = getNestedNodeByPath(currentPath);
            const container = document.getElementById('folder-contents-list');
            const breadcrumbs = document.getElementById('archive-breadcrumbs');
            const countBadge = document.getElementById('items-count-badge');
            
            if (!node || !node.is_dir) {
                container.innerHTML = `<div class="p-4 text-center text-muted"><i class="bi bi-exclamation-circle fs-3 d-block mb-2 text-warning"></i>Folder not found.</div>`;
                return;
            }

            // Render Breadcrumbs
            let breadcrumbsHtml = `<li class="breadcrumb-item"><a href="#" onclick="navigateToPath('Archives'); return false;" class="text-primary fw-semibold"><i class="bi bi-folder-fill me-1"></i></a></li>`;
            let relativePathParts = currentPath.replace(/^Archives\/?/, '').split('/').filter(p => p !== '');
            let runningPath = 'Archives';
            
            relativePathParts.forEach((part, index) => {
                runningPath += '/' + part;
                if (index === relativePathParts.length - 1) {
                    breadcrumbsHtml += `<li class="breadcrumb-item active" aria-current="page">${part}</li>`;
                } else {
                    breadcrumbsHtml += `<li class="breadcrumb-item"><a href="#" onclick="navigateToPath('${runningPath.replace(/'/g, "\\'") }'); return false;" class="text-primary fw-semibold">${part}</a></li>`;
                }
            });
            breadcrumbs.innerHTML = breadcrumbsHtml;

            // Render List Items
            let itemsHtml = '';
            if (node.children && node.children.length > 0) {
                countBadge.textContent = `${node.children.length} items`;
                node.children.forEach(item => {
                    if (item.is_dir) {
                        itemsHtml += `
                            <a href="#" onclick="navigateToPath('${item.path.replace(/'/g, "\\'") }'); return false;" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3 border-bottom border-light">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="bi bi-folder-fill text-warning fs-4"></i>
                                    <div>
                                        <span class="fw-semibold text-dark d-block">${item.name}</span>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </a>
                        `;
                    } else {
                        let downloadUrl = '';
                        if (item.db_report_id) {
                            downloadUrl = `{{ url('committee-reports') }}/${item.db_report_id}/pdf`;
                        } else if (item.db_attachment_id) {
                            downloadUrl = `{{ url('committee-reports/attachments') }}/${item.db_attachment_id}`;
                        } else if (item.encrypted_path) {
                            downloadUrl = `{{ route('committee-reports.downloadStorageboxFile') }}?file=${encodeURIComponent(item.encrypted_path)}`;
                        }

                        let lowercaseName = item.name.toLowerCase();
                        let isPdf = lowercaseName.endsWith('.pdf');
                        let isOffice = lowercaseName.endsWith('.xlsx') || lowercaseName.endsWith('.xls') || lowercaseName.endsWith('.docx') || lowercaseName.endsWith('.doc');

                        itemsHtml += `
                            <div class="list-group-item d-flex align-items-center justify-content-between p-3 border-bottom border-light">
                                <div class="d-flex align-items-center gap-3 overflow-hidden me-2">
                                    ${getFileIcon(item.name)}
                                    <div class="text-truncate">
                                        <span class="fw-medium text-dark d-block text-truncate" title="${item.name}">${item.name}</span>
                                        ${item.size ? `<span class="text-muted small">${formatBytes(item.size)}</span>` : ''}
                                    </div>
                                </div>
                                <div class="d-flex gap-2 flex-shrink-0">
                                    ${(isPdf || isOffice) ? `
                                        <button type="button" onclick="previewDocument('${downloadUrl}', '${item.name.replace(/'/g, "\\'")}')" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                            <i class="bi bi-eye-fill me-1"></i> Preview
                                        </button>
                                    ` : ''}
                                    <a href="${downloadUrl}" class="btn btn-sm btn-outline-primary rounded-pill px-3" target="_blank">
                                        <i class="bi bi-download me-1"></i> {{ __('messages.Download') ?? 'Download' }}
                                    </a>
                                </div>
                            </div>
                        `;
                    }
                });
            } else {
                    countBadge.textContent = '0 items';
                    itemsHtml = `
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-folder-open fs-2 d-block mb-2 text-secondary"></i>
                            {{ __('messages.No reports archived yet.') ?? 'This folder is empty.' }}
                        </div>
                    `;
                }

                container.innerHTML = itemsHtml;
            }

            window.navigateToPath = function(path) {
                currentPath = path;
                renderExplorer();
            };

            window.previewDocument = function(url, filename) {
                let lowercaseName = filename.toLowerCase();
                const iframeContainer = document.getElementById('documentPreviewIframeContainer');
                const docxContainer = document.getElementById('docxPreviewContainer');
                const excelContainer = document.getElementById('excelPreviewContainer');
                const iframe = document.getElementById('documentPreviewIframe');

                // Hide all containers initially
                iframeContainer.classList.add('d-none');
                docxContainer.classList.add('d-none');
                excelContainer.classList.add('d-none');
                docxContainer.innerHTML = '';
                excelContainer.innerHTML = '';

                document.getElementById('documentPreviewModalLabel').textContent = filename;
                const myModal = new bootstrap.Modal(document.getElementById('documentPreviewModal'));
                myModal.show();

                // Load based on extension offline
                if (lowercaseName.endsWith('.pdf')) {
                    iframeContainer.classList.remove('d-none');
                    iframe.src = url + (url.includes('?') ? '&' : '?') + 'disposition=inline';
                } else if (lowercaseName.endsWith('.docx') || lowercaseName.endsWith('.doc')) {
                    docxContainer.classList.remove('d-none');
                    docxContainer.innerHTML = '<div class="p-4 text-center text-muted"><div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>Parsing document...</div>';
                    
                    fetch(url + (url.includes('?') ? '&' : '?') + 'disposition=inline')
                        .then(res => res.blob())
                        .then(blob => {
                            docxContainer.innerHTML = '';
                            docx.renderAsync(blob, docxContainer)
                                .catch(err => {
                                    docxContainer.innerHTML = `<div class="p-4 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Failed to render Word document body: ${err}</div>`;
                                });
                        })
                        .catch(err => {
                            docxContainer.innerHTML = `<div class="p-4 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Error loading document: ${err}</div>`;
                        });
                } else if (lowercaseName.endsWith('.xlsx') || lowercaseName.endsWith('.xls')) {
                    excelContainer.classList.remove('d-none');
                    excelContainer.innerHTML = '<div class="p-4 text-center text-muted"><div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>Loading workbook sheets...</div>';

                    fetch(url + (url.includes('?') ? '&' : '?') + 'disposition=inline')
                        .then(res => res.arrayBuffer())
                        .then(ab => {
                            const workbook = XLSX.read(new Uint8Array(ab), {type: 'array'});
                            excelContainer.innerHTML = '';

                            // Create sheet tabs
                            let tabsHtml = '<ul class="nav nav-tabs px-3 bg-light border-bottom" role="tablist">';
                            let contentHtml = '<div class="tab-content p-3">';

                            workbook.SheetNames.forEach((sheetName, index) => {
                                const id = `sheet-${index}`;
                                const activeClass = index === 0 ? 'active' : '';
                                const activeBtn = index === 0 ? 'true' : 'false';

                                tabsHtml += `
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link ${activeClass} fw-bold py-2 border-0" id="${id}-tab" data-bs-toggle="tab" data-bs-target="#${id}" type="button" role="tab" aria-controls="${id}" aria-selected="${activeBtn}">${sheetName}</button>
                                    </li>
                                `;

                                const sheetHtml = XLSX.utils.sheet_to_html(workbook.Sheets[sheetName], {
                                    editable: false
                                });

                                contentHtml += `
                                    <div class="tab-pane fade show ${activeClass}" id="${id}" role="tabpanel" aria-labelledby="${id}-tab" style="overflow-x: auto;">
                                        <div class="table-responsive bg-white rounded-3 shadow-sm border border-light p-2">
                                            ${sheetHtml}
                                        </div>
                                    </div>
                                `;
                            });

                            tabsHtml += '</ul>';
                            contentHtml += '</div>';

                            excelContainer.innerHTML = tabsHtml + contentHtml;

                            // Style table conversion output to make look like premium bootstrap table
                            excelContainer.querySelectorAll('table').forEach(table => {
                                table.classList.add('table', 'table-bordered', 'table-striped', 'table-hover', 'mb-0');
                            });
                        })
                        .catch(err => {
                            excelContainer.innerHTML = `<div class="p-4 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Error parsing Excel workbook: ${err}</div>`;
                        });
                }
            };

            // Clear iframe when modal is hidden
            document.addEventListener('DOMContentLoaded', function() {
                renderExplorer();
                const modalEl = document.getElementById('documentPreviewModal');
                if (modalEl) {
                    modalEl.addEventListener('hidden.bs.modal', function () {
                        document.getElementById('documentPreviewIframe').src = '';
                        document.getElementById('docxPreviewContainer').innerHTML = '';
                        document.getElementById('excelPreviewContainer').innerHTML = '';
                    });
                }
            });
    </script>

    <!-- Document Preview Modal -->
    <div class="modal fade" id="documentPreviewModal" tabindex="-1" aria-labelledby="documentPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content rounded-4 overflow-hidden shadow-lg border-0">
                <div class="modal-header bg-light border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark text-truncate" id="documentPreviewModalLabel">Document Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 d-flex flex-column" style="height: 80vh;">
                    <!-- Dynamic IFrame container for PDF -->
                    <div id="documentPreviewIframeContainer" class="w-100 h-100 d-none">
                        <iframe id="documentPreviewIframe" src="" class="w-100 h-100 border-0"></iframe>
                    </div>
                    <!-- Dynamic Word parser div container -->
                    <div id="docxPreviewContainer" class="w-100 h-100 overflow-auto bg-white p-4 d-none"></div>
                    <!-- Dynamic Excel parser tab container -->
                    <div id="excelPreviewContainer" class="w-100 h-100 overflow-auto bg-white d-none"></div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
