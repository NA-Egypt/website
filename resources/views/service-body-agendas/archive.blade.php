<x-layout>
    <x-backhead>{{ __('messages.Service Body Agendas Archive') ?? 'Service Body Agendas Archive' }}</x-backhead>

    <div class="container mt-4">
        <!-- Toggle Filters Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-funnel-fill me-2"></i>{{ __('messages.Filter Options') ?? 'Filter Options' }}</h5>
            <button class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm border" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters" aria-expanded="false" aria-controls="collapseFilters">
                <i class="bi bi-funnel-fill me-1"></i> {{ __('messages.Toggle Filters') ?? 'Toggle Filters' }}
            </button>
        </div>

        <!-- Advanced Filter & Search Collapsible Block -->
        <div class="collapse {{ request('search') || request('service_body_id') || request('start_date') || request('end_date') ? 'show' : '' }}" id="collapseFilters">
            <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 border-top">
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
                                    <i class="bi bi-arrow-counterclockwise"></i> {{ __('messages.Reset') ?? 'Reset' }}
                                </a>
                                <button type="submit" class="btn btn-primary rounded-pill flex-fill py-2 small">
                                    <i class="bi bi-search"></i> {{ __('messages.Search') ?? 'Search' }}
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
                        <!-- Instant text search filter -->
                        <div class="position-relative" style="width: 220px;">
                            <span class="position-absolute top-50 start-0 translate-middle-y ps-2 text-muted" style="pointer-events: none;">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="explorer-filter" class="form-control form-control-sm ps-4 rounded-pill" placeholder="{{ __('messages.Filter view...') ?? 'Filter view...' }}" style="padding-left: 1.8rem !important; padding-right: 1rem !important;">
                        </div>

                        <!-- Grid/List layout toggle buttons -->
                        <div class="btn-group btn-group-sm rounded-pill overflow-hidden border shadow-sm" role="group">
                            <button type="button" class="btn btn-light border-0" id="btn-layout-grid" title="{{ __('messages.Grid View') ?? 'Grid View' }}">
                                <i class="bi bi-grid-fill"></i>
                            </button>
                            <button type="button" class="btn btn-light border-0" id="btn-layout-list" title="{{ __('messages.List View') ?? 'List View' }}">
                                <i class="bi bi-list"></i>
                            </button>
                        </div>

                        <div class="text-muted small fw-semibold" id="items-count-badge"></div>
                        <a href="?refresh=1" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Sync files and reload storage box archives">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-0" style="min-height: 250px;">
                <div id="folder-contents-list">
                    <!-- Dynamic explorer files and folders will load here safely -->
                </div>
            </div>
        </div>
    </div>

    <!-- Backdrop Overlay for Details Drawer -->
    <div id="drawer-backdrop" class="drawer-backdrop" onclick="closeDetailsDrawer()"></div>

    <!-- Slideout Info Details Drawer -->
    <div id="details-drawer-panel" class="details-drawer">
        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-4">
            <h5 class="fw-bold mb-0 text-primary">
                <i class="bi bi-info-circle-fill me-2"></i>{{ __('messages.Details') ?? 'Details' }}
            </h5>
            <button type="button" class="btn-close" onclick="closeDetailsDrawer()" aria-label="Close"></button>
        </div>
        <div class="flex-grow-1 overflow-auto pe-2">
            <!-- Icon/Visual preview placeholder -->
            <div class="text-center py-4 bg-light rounded-4 mb-4 border border-light shadow-inner">
                <div id="drawer-file-icon-container" class="fs-1"></div>
            </div>

            <!-- Metadata info -->
            <div class="mb-3">
                <span class="text-muted small d-block">{{ __('messages.Name') ?? 'Name' }}</span>
                <span id="drawer-file-name" class="fw-semibold text-dark text-break fs-6"></span>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <span class="text-muted small d-block">{{ __('messages.Type') ?? 'Type' }}</span>
                    <span id="drawer-file-type" class="badge bg-secondary"></span>
                </div>
                <div class="col-6">
                    <span class="text-muted small d-block">{{ __('messages.Size') ?? 'Size' }}</span>
                    <span id="drawer-file-size" class="fw-medium text-dark"></span>
                </div>
            </div>
            <div class="mb-4">
                <span class="text-muted small d-block">{{ __('messages.Path') ?? 'Path' }}</span>
                <span id="drawer-file-path" class="small text-muted text-break font-monospace bg-light p-2 rounded d-block mt-1 border"></span>
            </div>
        </div>
        <div class="pt-3 border-top d-grid gap-2">
            <button type="button" id="drawer-preview-btn" class="btn btn-outline-primary rounded-pill py-2">
                <i class="bi bi-eye-fill me-1"></i> {{ __('messages.Preview File') ?? 'Preview File' }}
            </button>
            <a href="#" id="drawer-download-btn" class="btn btn-primary rounded-pill py-2" target="_blank">
                <i class="bi bi-download me-1"></i> {{ __('messages.Download') ?? 'Download' }}
            </a>
        </div>
    </div>

    <!-- Styles for Grid Cards & Slideout Drawer -->
    <style>
        /* Details Slideout Panel CSS */
        .details-drawer {
            position: fixed;
            top: 0;
            width: 380px;
            height: 100vh;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border-inline-start: 1px solid var(--glass-border);
            box-shadow: 0 0 30px rgba(0,0,0,0.12);
            z-index: 1050;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 24px;
            display: flex;
            flex-direction: column;
        }
        
        [dir="ltr"] .details-drawer {
            right: 0;
            transform: translateX(100%);
        }
        [dir="ltr"] .details-drawer.open {
            transform: translateX(0);
        }
        [dir="rtl"] .details-drawer {
            left: 0;
            transform: translateX(-100%);
        }
        [dir="rtl"] .details-drawer.open {
            transform: translateX(0);
        }

        .drawer-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .drawer-backdrop.show {
            opacity: 1;
            visibility: visible;
        }

        /* Folder themes */
        .folder-indigo { --folder-accent: #6366f1; --folder-bg: rgba(99, 102, 241, 0.07); }
        .folder-teal { --folder-accent: #14b8a6; --folder-bg: rgba(20, 184, 166, 0.07); }
        .folder-violet { --folder-accent: #8b5cf6; --folder-bg: rgba(139, 92, 246, 0.07); }
        .folder-amber { --folder-accent: #f59e0b; --folder-bg: rgba(245, 158, 11, 0.07); }
        .folder-slate { --folder-accent: #64748b; --folder-bg: rgba(100, 116, 139, 0.07); }

        .folder-card {
            background: var(--folder-bg) !important;
            border: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-left: 4px solid var(--folder-accent) !important;
            border-radius: 14px;
            padding: 18px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        [dir="rtl"] .folder-card {
            border-left: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-right: 4px solid var(--folder-accent) !important;
        }
        .folder-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            border-color: var(--folder-accent) !important;
        }

        .file-card {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid var(--glass-border);
            border-radius: 14px;
            padding: 18px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .file-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            border-color: rgba(59, 130, 246, 0.4);
        }

        .active-item {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
        }

        /* Mobile drawer adjustments */
        @media (max-width: 768px) {
            .details-drawer {
                width: 100% !important;
                height: 60vh !important;
                bottom: 0 !important;
                top: auto !important;
                border-inline-start: none !important;
                border-top: 1px solid var(--glass-border) !important;
                border-radius: 24px 24px 0 0 !important;
                transform: translateY(100%) !important;
            }
            [dir="ltr"] .details-drawer, [dir="rtl"] .details-drawer {
                right: 0 !important;
                left: 0 !important;
            }
            [dir="ltr"] .details-drawer.open, [dir="rtl"] .details-drawer.open {
                transform: translateY(0) !important;
            }
        }
    </style>

    <!-- Offline Preview Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.1.15/dist/docx-preview.min.js"></script>

    <!-- JS to handle interactive client-side navigation within the tree -->
    <script>
        const archiveTree = @json($archiveTree);
        let currentPath = 'Archives';
        let currentLayout = 'grid'; // grid or list

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

        function getFileIconElement(name) {
            const ext = name.split('.').pop().toLowerCase();
            const icon = document.createElement('i');
            icon.className = 'bi fs-4';
            switch (ext) {
                case 'pdf':
                    icon.classList.add('bi-file-earmark-pdf-fill', 'text-danger');
                    break;
                case 'xlsx':
                case 'xls':
                case 'csv':
                    icon.classList.add('bi-file-earmark-excel-fill', 'text-success');
                    break;
                case 'docx':
                case 'doc':
                    icon.classList.add('bi-file-earmark-word-fill', 'text-primary');
                    break;
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    icon.classList.add('bi-file-earmark-image-fill', 'text-warning');
                    break;
                case 'mov':
                case 'mp4':
                    icon.classList.add('bi-file-earmark-play-fill', 'text-info');
                    break;
                default:
                    icon.classList.add('bi-file-earmark-fill', 'text-secondary');
            }
            return icon;
        }

        function getFolderTheme(name) {
            const lowercaseName = name.toLowerCase();
            if (lowercaseName.includes('أجندة') || lowercaseName.includes('agenda')) {
                return 'folder-indigo';
            } else if (lowercaseName.includes('التقارير') || lowercaseName.includes('report')) {
                return 'folder-teal';
            } else if (lowercaseName.includes('المرفقات') || lowercaseName.includes('attachment')) {
                return 'folder-violet';
            } else if (/\b(19|20)\d{2}\b/.test(lowercaseName)) {
                return 'folder-amber';
            }
            return 'folder-slate';
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

        // Folder & File DOM creation helper functions
        function createFolderGridCard(item) {
            const card = document.createElement('div');
            const theme = getFolderTheme(item.name);
            card.className = `folder-card ${theme} h-100 d-flex flex-column justify-content-between`;
            
            const topDiv = document.createElement('div');
            topDiv.className = 'd-flex align-items-start justify-content-between mb-3';
            
            const icon = document.createElement('i');
            icon.className = 'bi bi-folder-fill fs-2';
            icon.style.color = 'var(--folder-accent)';
            topDiv.appendChild(icon);
            
            const badge = document.createElement('span');
            badge.className = 'badge bg-white text-secondary border small';
            badge.textContent = @json(__('messages.Folder') ?? 'Folder');
            topDiv.appendChild(badge);
            card.appendChild(topDiv);

            const nameDiv = document.createElement('div');
            nameDiv.className = 'fw-bold text-dark text-truncate';
            nameDiv.textContent = item.name;
            nameDiv.title = item.name;
            card.appendChild(nameDiv);
            
            card.addEventListener('click', function() {
                navigateToPath(item.path);
            });
            
            return card;
        }

        function createFileGridCard(item) {
            const card = document.createElement('div');
            card.className = 'file-card h-100 d-flex flex-column justify-content-between';
            card.dataset.path = item.path;
            
            const topDiv = document.createElement('div');
            topDiv.className = 'd-flex align-items-start justify-content-between mb-2';
            
            topDiv.appendChild(getFileIconElement(item.name));
            
            const infoBtn = document.createElement('button');
            infoBtn.className = 'btn btn-link p-0 text-muted border-0';
            infoBtn.type = 'button';
            const infoIcon = document.createElement('i');
            infoIcon.className = 'bi bi-info-circle-fill fs-5';
            infoBtn.appendChild(infoIcon);
            topDiv.appendChild(infoBtn);
            card.appendChild(topDiv);

            const infoDiv = document.createElement('div');
            infoDiv.className = 'mt-2';
            const nameDiv = document.createElement('div');
            nameDiv.className = 'fw-semibold text-dark text-truncate mb-1';
            nameDiv.textContent = item.name;
            nameDiv.title = item.name;
            infoDiv.appendChild(nameDiv);
            
            const metaDiv = document.createElement('div');
            metaDiv.className = 'd-flex align-items-center justify-content-between mt-1';
            
            if (item.size) {
                const sizeSpan = document.createElement('span');
                sizeSpan.className = 'text-muted small';
                sizeSpan.textContent = formatBytes(item.size);
                metaDiv.appendChild(sizeSpan);
            }
            
            infoDiv.appendChild(metaDiv);
            card.appendChild(infoDiv);
            
            card.addEventListener('click', function(e) {
                document.querySelectorAll('.file-card, .list-group-item').forEach(el => el.classList.remove('active-item'));
                card.classList.add('active-item');
                showDetailsDrawer(item);
            });
            
            return card;
        }

        function createFolderListRow(item) {
            const link = document.createElement('a');
            link.href = '#';
            link.className = 'list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3 border-bottom border-light';
            
            const leftDiv = document.createElement('div');
            leftDiv.className = 'd-flex align-items-center gap-3';
            
            const icon = document.createElement('i');
            icon.className = 'bi bi-folder-fill text-warning fs-4';
            leftDiv.appendChild(icon);
            
            const nameSpan = document.createElement('span');
            nameSpan.className = 'fw-semibold text-dark';
            nameSpan.textContent = item.name;
            leftDiv.appendChild(nameSpan);
            
            link.appendChild(leftDiv);
            
            const rightIcon = document.createElement('i');
            rightIcon.className = 'bi bi-chevron-right text-muted';
            link.appendChild(rightIcon);
            
            link.addEventListener('click', function(e) {
                e.preventDefault();
                navigateToPath(item.path);
            });
            
            return link;
        }

        function createFileListRow(item) {
            const row = document.createElement('div');
            row.className = 'list-group-item d-flex align-items-center justify-content-between p-3 border-bottom border-light';
            row.dataset.path = item.path;
            
            const leftDiv = document.createElement('div');
            leftDiv.className = 'd-flex align-items-center gap-3 overflow-hidden me-2';
            
            leftDiv.appendChild(getFileIconElement(item.name));
            
            const textDiv = document.createElement('div');
            textDiv.className = 'text-truncate';
            
            const nameSpan = document.createElement('span');
            nameSpan.className = 'fw-medium text-dark d-block text-truncate mb-1';
            nameSpan.textContent = item.name;
            nameSpan.title = item.name;
            textDiv.appendChild(nameSpan);
            
            const metaDiv = document.createElement('div');
            metaDiv.className = 'd-flex align-items-center flex-wrap gap-1';
            
            if (isSearchActive) {
                let pathParts = item.path.split('/');
                pathParts.pop();
                if (pathParts[0] === 'Archives') pathParts.shift();
                if (pathParts.length > 0) {
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-light text-secondary border me-2';
                    badge.textContent = pathParts.join(' » ');
                    metaDiv.appendChild(badge);
                }
            }
            
            if (item.size) {
                const sizeSpan = document.createElement('span');
                sizeSpan.className = 'text-muted small';
                sizeSpan.textContent = formatBytes(item.size);
                metaDiv.appendChild(sizeSpan);
            }
            
            textDiv.appendChild(metaDiv);
            leftDiv.appendChild(textDiv);
            row.appendChild(leftDiv);
            
            row.addEventListener('click', function(e) {
                document.querySelectorAll('.file-card, .list-group-item').forEach(el => el.classList.remove('active-item'));
                row.classList.add('active-item');
                showDetailsDrawer(item);
            });
            
            return row;
        }

        function renderExplorer() {
            let node = getNestedNodeByPath(currentPath);
            const container = document.getElementById('folder-contents-list');
            const breadcrumbs = document.getElementById('archive-breadcrumbs');
            const countBadge = document.getElementById('items-count-badge');
            
            if (!node || !node.is_dir) {
                container.replaceChildren();
                const errDiv = document.createElement('div');
                errDiv.className = 'p-4 text-center text-muted';
                const errIcon = document.createElement('i');
                errIcon.className = 'bi bi-exclamation-circle fs-3 d-block mb-2 text-warning';
                errDiv.appendChild(errIcon);
                errDiv.appendChild(document.createTextNode(@json(__('messages.Folder not found.') ?? 'Folder not found.')));
                container.appendChild(errDiv);
                return;
            }

            // Render Breadcrumbs
            breadcrumbs.replaceChildren();
            
            const rootLi = document.createElement('li');
            rootLi.className = 'breadcrumb-item';
            const rootLink = document.createElement('a');
            rootLink.href = '#';
            rootLink.className = 'text-primary fw-semibold';
            rootLink.addEventListener('click', function(e) {
                e.preventDefault();
                navigateToPath('Archives');
            });
            const rootIcon = document.createElement('i');
            rootIcon.className = 'bi bi-folder-fill me-1';
            rootLink.appendChild(rootIcon);
            rootLi.appendChild(rootLink);
            breadcrumbs.appendChild(rootLi);

            let relativePathParts = currentPath.replace(/^Archives\/?/, '').split('/').filter(p => p !== '');
            let runningPath = 'Archives';
            
            relativePathParts.forEach((part, index) => {
                runningPath += '/' + part;
                const li = document.createElement('li');
                if (index === relativePathParts.length - 1) {
                    li.className = 'breadcrumb-item active';
                    li.setAttribute('aria-current', 'page');
                    li.textContent = part;
                } else {
                    li.className = 'breadcrumb-item';
                    const link = document.createElement('a');
                    link.href = '#';
                    link.className = 'text-primary fw-semibold';
                    const targetPath = runningPath;
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        navigateToPath(targetPath);
                    });
                    link.textContent = part;
                    li.appendChild(link);
                }
                breadcrumbs.appendChild(li);
            });

            let displayItems = node.children || [];

            if (isSearchActive && currentPath === 'Archives') {
                displayItems = getFlattenedMatches(archiveTree);
            }

            // Local filter
            const filterInput = document.getElementById('explorer-filter');
            const filterVal = filterInput ? filterInput.value.toLowerCase().trim() : '';
            if (filterVal) {
                displayItems = displayItems.filter(item => item.name.toLowerCase().includes(filterVal));
            }

            countBadge.textContent = `${displayItems.length} items`;
            container.replaceChildren();

            if (displayItems && displayItems.length > 0) {
                if (currentLayout === 'grid') {
                    const gridRow = document.createElement('div');
                    gridRow.className = 'row g-3 p-3';
                    displayItems.forEach(item => {
                        const col = document.createElement('div');
                        col.className = 'col-12 col-sm-6 col-md-4 col-lg-3';
                        if (item.is_dir) {
                            col.appendChild(createFolderGridCard(item));
                        } else {
                            col.appendChild(createFileGridCard(item));
                        }
                        gridRow.appendChild(col);
                    });
                    container.appendChild(gridRow);
                } else {
                    const listContainer = document.createElement('div');
                    listContainer.className = 'list-group list-group-flush';
                    displayItems.forEach(item => {
                        if (item.is_dir) {
                            listContainer.appendChild(createFolderListRow(item));
                        } else {
                            listContainer.appendChild(createFileListRow(item));
                        }
                    });
                    container.appendChild(listContainer);
                }
            } else {
                const emptyDiv = document.createElement('div');
                emptyDiv.className = 'p-5 text-center text-muted';
                const emptyIcon = document.createElement('i');
                emptyIcon.className = 'bi bi-folder-open fs-2 d-block mb-2 text-secondary';
                emptyDiv.appendChild(emptyIcon);
                const textSpan = document.createElement('span');
                textSpan.textContent = @json(__('messages.This folder is empty.') ?? 'This folder is empty.');
                emptyDiv.appendChild(textSpan);
                container.appendChild(emptyDiv);
            }
        }

        window.navigateToPath = function(path) {
            currentPath = path;
            renderExplorer();
            closeDetailsDrawer();
        };

        window.showDetailsDrawer = function(item) {
            const drawer = document.getElementById('details-drawer-panel');
            const backdrop = document.getElementById('drawer-backdrop');
            
            const iconContainer = document.getElementById('drawer-file-icon-container');
            iconContainer.replaceChildren(getFileIconElement(item.name).cloneNode(true));
            
            document.getElementById('drawer-file-name').textContent = item.name;
            
            const ext = item.name.split('.').pop().toUpperCase();
            const typeBadge = document.getElementById('drawer-file-type');
            typeBadge.textContent = ext;
            typeBadge.className = 'badge ' + (ext === 'PDF' ? 'bg-danger' : (['XLSX', 'XLS', 'CSV'].includes(ext) ? 'bg-success' : 'bg-primary'));
            
            document.getElementById('drawer-file-size').textContent = item.size ? formatBytes(item.size) : 'N/A';
            document.getElementById('drawer-file-path').textContent = item.path;
            
            let downloadUrl = '';
            if (item.db_agenda_id) {
                downloadUrl = `{{ url('service-body-agendas') }}/${item.db_agenda_id}/pdf`;
            } else if (item.encrypted_path) {
                downloadUrl = `{{ route('committee-reports.downloadStorageboxFile') }}?file=${encodeURIComponent(item.encrypted_path)}`;
            }
            
            const previewBtn = document.getElementById('drawer-preview-btn');
            const downloadBtn = document.getElementById('drawer-download-btn');
            
            downloadBtn.href = downloadUrl;
            
            const lowercaseName = item.name.toLowerCase();
            const isPdf = lowercaseName.endsWith('.pdf');
            const isOffice = lowercaseName.endsWith('.xlsx') || lowercaseName.endsWith('.xls') || lowercaseName.endsWith('.docx') || lowercaseName.endsWith('.doc');
            const isImage = lowercaseName.endsWith('.png') || lowercaseName.endsWith('.jpg') || lowercaseName.endsWith('.jpeg') || lowercaseName.endsWith('.gif');
            
            if (isPdf || isOffice || isImage) {
                previewBtn.style.display = 'block';
                previewBtn.onclick = function() {
                    previewDocument(downloadUrl, item.name);
                };
            } else {
                previewBtn.style.display = 'none';
            }
            
            drawer.classList.add('open');
            backdrop.classList.add('show');
        };

        window.closeDetailsDrawer = function() {
            document.getElementById('details-drawer-panel').classList.remove('open');
            document.getElementById('drawer-backdrop').classList.remove('show');
            document.querySelectorAll('.file-card, .list-group-item').forEach(el => el.classList.remove('active-item'));
        };

        window.previewDocument = function(url, filename) {
            let lowercaseName = filename.toLowerCase();
            const iframeContainer = document.getElementById('documentPreviewIframeContainer');
            const docxContainer = document.getElementById('docxPreviewContainer');
            const excelContainer = document.getElementById('excelPreviewContainer');
            const imageContainer = document.getElementById('imagePreviewContainer');
            const iframe = document.getElementById('documentPreviewIframe');
            const img = document.getElementById('documentPreviewImg');

            iframeContainer.classList.add('d-none');
            docxContainer.classList.add('d-none');
            excelContainer.classList.add('d-none');
            imageContainer.classList.add('d-none');
            docxContainer.replaceChildren();
            excelContainer.replaceChildren();
            img.src = '';

            document.getElementById('documentPreviewModalLabel').textContent = filename;
            const myModal = new bootstrap.Modal(document.getElementById('documentPreviewModal'));
            myModal.show();

            const inlineUrl = url + (url.includes('?') ? '&' : '?') + 'disposition=inline';

            if (lowercaseName.endsWith('.pdf')) {
                iframeContainer.classList.remove('d-none');
                iframe.src = inlineUrl;
            } else if (lowercaseName.endsWith('.png') || lowercaseName.endsWith('.jpg') || lowercaseName.endsWith('.jpeg') || lowercaseName.endsWith('.gif')) {
                imageContainer.classList.remove('d-none');
                img.src = inlineUrl;
            } else if (lowercaseName.endsWith('.docx') || lowercaseName.endsWith('.doc')) {
                docxContainer.classList.remove('d-none');
                
                const spinnerDiv = document.createElement('div');
                spinnerDiv.className = 'p-4 text-center text-muted';
                const spinner = document.createElement('div');
                spinner.className = 'spinner-border spinner-border-sm text-primary me-2';
                spinner.setAttribute('role', 'status');
                spinnerDiv.appendChild(spinner);
                spinnerDiv.appendChild(document.createTextNode(@json(__('messages.Parsing document...') ?? 'Parsing document...')));
                docxContainer.appendChild(spinnerDiv);
                
                fetch(inlineUrl)
                    .then(res => res.blob())
                    .then(blob => {
                        docxContainer.replaceChildren();
                        docx.renderAsync(blob, docxContainer)
                            .catch(err => {
                                const errDiv = document.createElement('div');
                                errDiv.className = 'p-4 text-danger';
                                const icon = document.createElement('i');
                                icon.className = 'bi bi-exclamation-triangle-fill me-2';
                                errDiv.appendChild(icon);
                                errDiv.appendChild(document.createTextNode(`Failed to render Word document body: ${err}`));
                                docxContainer.appendChild(errDiv);
                            });
                    })
                    .catch(err => {
                        docxContainer.replaceChildren();
                        const errDiv = document.createElement('div');
                        errDiv.className = 'p-4 text-danger';
                        const icon = document.createElement('i');
                        icon.className = 'bi bi-exclamation-triangle-fill me-2';
                        errDiv.appendChild(icon);
                        errDiv.appendChild(document.createTextNode(`Error loading document: ${err}`));
                        docxContainer.appendChild(errDiv);
                    });
            } else if (lowercaseName.endsWith('.xlsx') || lowercaseName.endsWith('.xls')) {
                excelContainer.classList.remove('d-none');
                
                const spinnerDiv = document.createElement('div');
                spinnerDiv.className = 'p-4 text-center text-muted';
                const spinner = document.createElement('div');
                spinner.className = 'spinner-border spinner-border-sm text-success me-2';
                spinner.setAttribute('role', 'status');
                spinnerDiv.appendChild(spinner);
                spinnerDiv.appendChild(document.createTextNode(@json(__('messages.Loading workbook sheets...') ?? 'Loading workbook sheets...')));
                excelContainer.appendChild(spinnerDiv);

                fetch(inlineUrl)
                    .then(res => res.arrayBuffer())
                    .then(ab => {
                        const workbook = XLSX.read(new Uint8Array(ab), {type: 'array'});
                        excelContainer.replaceChildren();

                        const tabsContainer = document.createElement('ul');
                        tabsContainer.className = 'nav nav-tabs px-3 bg-light border-bottom';
                        tabsContainer.setAttribute('role', 'tablist');

                        const contentContainer = document.createElement('div');
                        contentContainer.className = 'tab-content p-3';

                        workbook.SheetNames.forEach((sheetName, index) => {
                            const id = `sheet-${index}`;
                            const activeClass = index === 0 ? 'active' : '';
                            const activeBtn = index === 0 ? 'true' : 'false';

                            const li = document.createElement('li');
                            li.className = 'nav-item';
                            li.setAttribute('role', 'presentation');

                            const tabBtn = document.createElement('button');
                            tabBtn.className = `nav-link ${activeClass} fw-bold py-2 border-0`;
                            tabBtn.id = `${id}-tab`;
                            tabBtn.setAttribute('data-bs-toggle', 'tab');
                            tabBtn.setAttribute('data-bs-target', `#${id}`);
                            tabBtn.type = 'button';
                            tabBtn.setAttribute('role', 'tab');
                            tabBtn.setAttribute('aria-controls', id);
                            tabBtn.setAttribute('aria-selected', activeBtn);
                            tabBtn.textContent = sheetName;
                            li.appendChild(tabBtn);
                            tabsContainer.appendChild(li);

                            const pane = document.createElement('div');
                            pane.className = `tab-pane fade show ${activeClass}`;
                            pane.id = id;
                            pane.setAttribute('role', 'tabpanel');
                            pane.setAttribute('aria-labelledby', `${id}-tab`);
                            pane.style.overflowX = 'auto';

                            const wrapper = document.createElement('div');
                            wrapper.className = 'table-responsive bg-white rounded-3 shadow-sm border border-light p-2';

                            const sheetHtml = XLSX.utils.sheet_to_html(workbook.Sheets[sheetName], {
                                editable: false
                            });

                            const parsedDoc = new DOMParser().parseFromString(sheetHtml, 'text/html');
                            const parsedTable = parsedDoc.querySelector('table');
                            if (parsedTable) {
                                parsedTable.className = 'table table-bordered table-striped table-hover mb-0';
                                wrapper.appendChild(parsedTable);
                            } else {
                                wrapper.textContent = @json(__('messages.No table found in sheet') ?? 'No table found in sheet');
                            }

                            pane.appendChild(wrapper);
                            contentContainer.appendChild(pane);
                        });

                        excelContainer.appendChild(tabsContainer);
                        excelContainer.appendChild(contentContainer);
                    })
                    .catch(err => {
                        excelContainer.replaceChildren();
                        const errDiv = document.createElement('div');
                        errDiv.className = 'p-4 text-danger';
                        const icon = document.createElement('i');
                        icon.className = 'bi bi-exclamation-triangle-fill me-2';
                        errDiv.appendChild(icon);
                        errDiv.appendChild(document.createTextNode(`Error parsing Excel workbook: ${err}`));
                        excelContainer.appendChild(errDiv);
                    });
            }
        };

        // Layout Toggle and Filter Event Handlers
        document.addEventListener('DOMContentLoaded', function() {
            renderExplorer();

            const btnGrid = document.getElementById('btn-layout-grid');
            const btnList = document.getElementById('btn-layout-list');
            const filterInput = document.getElementById('explorer-filter');

            if (btnGrid && btnList) {
                btnGrid.addEventListener('click', function() {
                    currentLayout = 'grid';
                    btnGrid.classList.add('active', 'btn-secondary');
                    btnGrid.classList.remove('btn-light');
                    btnList.classList.remove('active', 'btn-secondary');
                    btnList.classList.add('btn-light');
                    renderExplorer();
                });

                btnList.addEventListener('click', function() {
                    currentLayout = 'list';
                    btnList.classList.add('active', 'btn-secondary');
                    btnList.classList.remove('btn-light');
                    btnGrid.classList.remove('active', 'btn-secondary');
                    btnGrid.classList.add('btn-light');
                    renderExplorer();
                });

                // Default state styling
                btnGrid.classList.add('active', 'btn-secondary');
                btnGrid.classList.remove('btn-light');
            }

            if (filterInput) {
                filterInput.addEventListener('input', function() {
                    renderExplorer();
                });
            }

            const modalEl = document.getElementById('documentPreviewModal');
            if (modalEl) {
                modalEl.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('documentPreviewIframe').src = '';
                    document.getElementById('documentPreviewImg').src = '';
                    document.getElementById('docxPreviewContainer').replaceChildren();
                    document.getElementById('excelPreviewContainer').replaceChildren();
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
                    <!-- Dynamic IFrame container for PDF -->
                    <div id="documentPreviewIframeContainer" class="w-100 h-100 d-none">
                        <iframe id="documentPreviewIframe" src="" class="w-100 h-100 border-0"></iframe>
                    </div>
                    <!-- Dynamic Image container -->
                    <div id="imagePreviewContainer" class="w-100 h-100 overflow-auto bg-white p-3 text-center d-none">
                        <img id="documentPreviewImg" src="" alt="Image preview" class="img-fluid rounded shadow-sm border border-light" style="max-height: 100%;">
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
