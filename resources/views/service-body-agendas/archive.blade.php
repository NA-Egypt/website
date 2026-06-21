<x-layout>
    <x-backhead>{{ __('messages.Service Body Agendas Archive') ?? 'Service Body Agendas Archive' }}</x-backhead>

    <div class="container mt-4">
        <!-- Toggle Filters Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold text-gradient"><i class="bi bi-archive-fill me-2"></i>{{ __('messages.Archive Files') ?? 'Archive Explorer' }}</h5>
            <button class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm border" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters" aria-expanded="false" aria-controls="collapseFilters">
                <i class="bi bi-funnel-fill me-1"></i> {{ __('messages.Toggle Filters') ?? 'Toggle Filters' }}
            </button>
        </div>

        <!-- Advanced Filter & Search Collapsible Block -->
        <div class="collapse {{ request('search') || request('service_body_id') || request('start_date') || request('end_date') ? 'show' : '' }}" id="collapseFilters">
            <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden" style="background: rgba(255, 255, 255, 0.45) !important; backdrop-filter: blur(12px) !important; -webkit-backdrop-filter: blur(12px) !important; border: 1px solid rgba(255, 255, 255, 0.25) !important;">
                <div class="card-body p-4">
                    <form action="{{ route('service-body-agendas.archive') }}" method="GET" id="searchFilterForm">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-4 col-lg-3">
                                <label for="search" class="form-label fw-semibold text-muted small"><i class="bi bi-search me-1"></i>{{ __('messages.Search') ?? 'Search' }}</label>
                                <input type="text" name="search" id="search" class="form-control rounded-3" value="{{ request('search') }}" placeholder="{{ __('messages.Search agendas...') ?? 'Search agendas...' }}">
                            </div>
                            <div class="col-12 col-md-4 col-lg-3">
                                <label for="service_body_id" class="form-label fw-semibold text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ __('messages.Service Body') ?? 'Service Body' }}</label>
                                <select name="service_body_id" id="service_body_id" class="form-select rounded-3 select2">
                                    <option value="">{{ __('messages.All Service Bodies') ?? 'All Service Bodies' }}</option>
                                    @foreach($serviceBodies as $sb)
                                        <option value="{{ $sb->id }}" {{ request('service_body_id') == $sb->id ? 'selected' : '' }}>
                                            {{ $sb->ar_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4 col-lg-2">
                                <label for="start_date" class="form-label fw-semibold text-muted small"><i class="bi bi-calendar-event me-1"></i>{{ __('messages.Start Date') ?? 'Start Date' }}</label>
                                <input type="date" name="start_date" id="start_date" class="form-control rounded-3" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-12 col-md-4 col-lg-2">
                                <label for="end_date" class="form-label fw-semibold text-muted small"><i class="bi bi-calendar-event me-1"></i>{{ __('messages.End Date') ?? 'End Date' }}</label>
                                <input type="date" name="end_date" id="end_date" class="form-control rounded-3" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-12 col-md-4 col-lg-2 d-flex gap-2">
                                <a href="{{ route('service-body-agendas.archive') }}" class="btn btn-light rounded-pill flex-fill py-2 text-center text-secondary small border">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </a>
                                <button type="submit" class="btn btn-primary rounded-pill flex-fill py-2 small">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
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
            return '<i class="bi bi-file-earmark-pdf-fill text-danger fs-4"></i>';
        }

        const isSearchActive = @json(request('search') != '' || request('service_body_id') != '');

        function getFlattenedMatches(nodes) {
            let matches = [];
            nodes.forEach(node => {
                if (node.is_dir) {
                    matches = matches.concat(getFlattenedMatches(node.children));
                } else {
                    matches.push(node);
                }
            });
            return matches;
        }

        function renderExplorer() {
            let node = getNestedNodeByPath(currentPath);
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
            let displayItems = node.children || [];

            if (isSearchActive && currentPath === 'Archives') {
                displayItems = getFlattenedMatches(archiveTree);
            }

            if (displayItems && displayItems.length > 0) {
                countBadge.textContent = `${displayItems.length} items`;
                displayItems.forEach(item => {
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
                        if (item.db_agenda_id) {
                            downloadUrl = `{{ url('service-body-agendas') }}/${item.db_agenda_id}/pdf`;
                        } else if (item.encrypted_path) {
                            downloadUrl = `{{ route('committee-reports.downloadStorageboxFile') }}?file=${encodeURIComponent(item.encrypted_path)}`;
                        }

                        let itemFolderPath = '';
                        if (isSearchActive) {
                            let pathParts = item.path.split('/');
                            pathParts.pop();
                            if (pathParts[0] === 'Archives') pathParts.shift();
                            if (pathParts.length > 0) {
                                itemFolderPath = `<span class="badge bg-light text-secondary border me-2">${pathParts.join(' &raquo; ')}</span>`;
                            }
                        }

                        itemsHtml += `
                            <div class="list-group-item d-flex align-items-center justify-content-between p-3 border-bottom border-light">
                                <div class="d-flex align-items-center gap-3 overflow-hidden me-2">
                                    ${getFileIcon(item.name)}
                                    <div class="text-truncate">
                                        <span class="fw-medium text-dark d-block text-truncate mb-1" title="${item.name}">${item.name}</span>
                                        <div class="d-flex align-items-center flex-wrap gap-1">
                                            ${itemFolderPath}
                                            ${item.size ? `<span class="text-muted small">${formatBytes(item.size)}</span>` : ''}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 flex-shrink-0">
                                    <button type="button" onclick="previewDocument('${downloadUrl}', '${item.name.replace(/'/g, "\\'")}')" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                        <i class="bi bi-eye-fill me-1"></i> Preview
                                    </button>
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
                        {{ __('messages.No agendas archived yet.') ?? 'This folder is empty.' }}
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
            const iframeContainer = document.getElementById('documentPreviewIframeContainer');
            const iframe = document.getElementById('documentPreviewIframe');

            document.getElementById('documentPreviewModalLabel').textContent = filename;
            const myModal = new bootstrap.Modal(document.getElementById('documentPreviewModal'));
            myModal.show();

            iframeContainer.classList.remove('d-none');
            iframe.src = url + (url.includes('?') ? '&' : '?') + 'disposition=inline';
        };

        document.addEventListener('DOMContentLoaded', function() {
            renderExplorer();
            const modalEl = document.getElementById('documentPreviewModal');
            if (modalEl) {
                modalEl.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('documentPreviewIframe').src = '';
                });
            }
        });
    </script>

    <!-- Document Preview Modal -->
    <div class="modal fade" id="documentPreviewModal" tabindex="-1" aria-labelledby="documentPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content rounded-4 overflow-hidden shadow-lg border-0">
                <div class="modal-header bg-light border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-dark text-truncate" id="documentPreviewModalLabel">Document Preview</h5>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 d-flex flex-column" style="height: 80vh;">
                    <div id="documentPreviewIframeContainer" class="w-100 h-100">
                        <iframe id="documentPreviewIframe" src="" class="w-100 h-100 border-0"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
