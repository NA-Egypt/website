<x-layout>
    <x-backhead>{{ __('messages.cloud_archive_title') ?? __('messages.Reports Archive') }}</x-backhead>

    @php
        $hasActiveFilters = request()->filled('search')
            || request()->filled('committee_id')
            || request()->filled('start_date')
            || request()->filled('end_date')
            || request('exceptional') == '1';

        $syncStatus = $syncState['status'] ?? 'idle';
        $diagnostics = $syncState['diagnostics'] ?? [];
        $canManage = $canManageArchive ?? false;
    @endphp

    <div class="container-fluid px-3 px-lg-4 mt-3 mb-5">

        <!-- Live Cloud Sync Status & Action Header Bar -->
        <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden archive-sync-banner">
            <div class="card-body py-3 px-3 px-md-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <!-- Left/Start: Cloud Sync Identity & Status Badge -->
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="sync-icon-orb d-flex align-items-center justify-content-center rounded-3">
                            <i class="bi bi-cloud-check-fill fs-4 text-primary" id="sync-banner-icon"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h6 class="mb-0 fw-bold text-dark">{{ __('messages.onedrive_sync_status') }}</h6>
                                <span id="sync-status-badge" class="badge rounded-pill
                                    @if($syncStatus === 'completed' || $syncStatus === 'idle') bg-success-subtle text-success border border-success-subtle
                                    @elseif($syncStatus === 'syncing' || $syncStatus === 'indexing') bg-primary-subtle text-primary border border-primary-subtle
                                    @elseif($syncStatus === 'warning') bg-warning-subtle text-warning-emphasis border border-warning-subtle
                                    @else bg-danger-subtle text-danger border border-danger-subtle @endif">
                                    @if($syncStatus === 'syncing' || $syncStatus === 'indexing')
                                        <span class="spinner-border spinner-border-sm me-1" role="status" style="width: 0.75rem; height: 0.75rem;"></span>
                                    @else
                                        <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                    @endif
                                    <span id="sync-status-text">
                                        @if($syncStatus === 'syncing')
                                            {{ __('messages.sync_status_syncing') }}
                                        @elseif($syncStatus === 'indexing')
                                            {{ __('messages.sync_status_indexing') }}
                                        @elseif($syncStatus === 'warning')
                                            {{ __('messages.sync_status_warning') }}
                                        @elseif($syncStatus === 'failed')
                                            {{ __('messages.sync_status_failed') }}
                                        @else
                                            {{ __('messages.sync_status_completed') }}
                                        @endif
                                    </span>
                                </span>
                            </div>
                            <div class="d-flex align-items-center flex-wrap gap-3 mt-1 small text-muted">
                                <span>
                                    <i class="bi bi-file-earmark-text me-1"></i>{{ __('messages.total_indexed_files') }}
                                    <strong class="text-dark" id="sync-stat-files">{{ number_format($syncState['total_files'] ?? 0) }}</strong>
                                </span>
                                <span>
                                    <i class="bi bi-folder2 me-1"></i>{{ __('messages.total_indexed_folders') }}
                                    <strong class="text-dark" id="sync-stat-dirs">{{ number_format($syncState['total_dirs'] ?? 0) }}</strong>
                                </span>
                                @if(!empty($syncState['last_synced_at']))
                                    <span>
                                        <i class="bi bi-clock-history me-1"></i>{{ __('messages.last_synced_label') }}
                                        <strong class="text-dark" id="sync-stat-time">{{ \Carbon\Carbon::parse($syncState['last_synced_at'])->diffForHumans() }}</strong>
                                    </span>
                                @endif
                                @if(!empty($syncState['message']))
                                    <span class="text-secondary d-none d-xl-inline" id="sync-stat-message">— {{ $syncState['message'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right/End: Filter Toggle, Diagnostics & Sync Actions -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button class="btn btn-sm {{ $hasActiveFilters ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#archiveFilterCollapse"
                                aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}"
                                aria-controls="archiveFilterCollapse">
                            <i class="bi bi-funnel-fill me-1"></i>{{ __('messages.advanced_filters_btn') }}
                            @if($hasActiveFilters)
                                <span class="badge bg-white text-primary ms-1">!</span>
                            @endif
                        </button>

                        <button type="button"
                                class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                data-bs-toggle="modal"
                                data-bs-target="#syncDiagnosticsModal">
                            <i class="bi bi-activity me-1"></i>{{ __('messages.sync_diagnostics_btn') }}
                        </button>

                        @if($canManage)
                            <button type="button"
                                    id="btn-trigger-sync"
                                    onclick="triggerCloudSync(false)"
                                    class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                <i class="bi bi-arrow-repeat me-1" id="btn-sync-icon"></i>
                                <span id="btn-sync-label">{{ __('messages.sync_now_btn') }}</span>
                            </button>
                        @else
                            <a href="?refresh=1" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-arrow-clockwise me-1"></i>{{ __('messages.sync_now_btn') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Collapsible Advanced Filter & Search Panel -->
        <div class="collapse {{ $hasActiveFilters ? 'show' : '' }} mb-3" id="archiveFilterCollapse">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('committee-reports.archive') }}" method="GET" id="searchFilterForm">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-6 col-lg-3">
                                <label for="search" class="form-label small fw-semibold text-muted mb-1">{{ __('messages.Search') ?? 'Search' }}</label>
                                <input type="text" name="search" id="search" class="form-control form-control-sm rounded-3" value="{{ request('search') }}" placeholder="{{ __('messages.Search by day or body') ?? 'Search by day or body...' }}">
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <label for="committee_id" class="form-label small fw-semibold text-muted mb-1">{{ __('messages.Committee') ?? 'Committee' }}</label>
                                <select name="committee_id" id="committee_id" class="form-select form-select-sm rounded-3">
                                    <option value="">{{ __('messages.All Committees') ?? 'All Committees' }}</option>
                                    @foreach($committees as $committee)
                                        <option value="{{ $committee->id }}" {{ request('committee_id') == $committee->id ? 'selected' : '' }}>
                                            {{ $committee->ar_name ?? $committee->en_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-4 col-lg-2">
                                <label for="start_date" class="form-label small fw-semibold text-muted mb-1">{{ __('messages.Start Date') ?? 'Start Date' }}</label>
                                <input type="date" name="start_date" id="start_date" class="form-control form-control-sm rounded-3" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-6 col-md-4 col-lg-2">
                                <label for="end_date" class="form-label small fw-semibold text-muted mb-1">{{ __('messages.End Date') ?? 'End Date' }}</label>
                                <input type="date" name="end_date" id="end_date" class="form-control form-control-sm rounded-3" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-12 col-md-4 col-lg-2 d-flex align-items-center justify-content-between gap-2">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" name="exceptional" id="exceptional" value="1" {{ request('exceptional') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold text-muted" for="exceptional">
                                        {{ __('messages.Exceptional') ?? 'Exceptional' }}
                                    </label>
                                </div>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('committee-reports.archive') }}" class="btn btn-sm btn-light rounded-pill px-3" title="{{ __('messages.Reset') ?? 'Reset' }}">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">
                                        <i class="bi bi-search me-1"></i>{{ __('messages.Search') ?? 'Search' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Dual-Pane Cloud Drive Explorer -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <!-- Explorer Top Action & Breadcrumb Toolbar -->
            <div class="card-header bg-light border-bottom p-2 px-md-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <!-- Start: Sidebar Toggle + Up Button + Breadcrumbs -->
                    <div class="d-flex align-items-center gap-2 flex-grow-1 overflow-hidden">
                        <button type="button"
                                id="btn-toggle-sidebar"
                                class="btn btn-sm btn-white border rounded-3 shadow-xs"
                                title="{{ __('messages.toggle_folder_tree') }}">
                            <i class="bi bi-layout-sidebar-inset"></i>
                        </button>

                        <button type="button"
                                id="btn-up-level"
                                onclick="navigateUpOneLevel()"
                                class="btn btn-sm btn-white border rounded-3 shadow-xs"
                                title="{{ __('messages.up_one_level') }}"
                                disabled>
                            <i class="bi bi-arrow-90deg-up"></i>
                        </button>

                        <nav aria-label="breadcrumb" class="mb-0 overflow-auto py-1 flex-grow-1">
                            <ol class="breadcrumb mb-0 flex-nowrap" id="archive-breadcrumbs">
                                <li class="breadcrumb-item">
                                    <a href="#" onclick="navigateToPath('Archives'); return false;" class="text-primary fw-semibold text-decoration-none">
                                        <i class="bi bi-hdd-network-fill me-1"></i>Archives
                                    </a>
                                </li>
                            </ol>
                        </nav>
                    </div>

                    <!-- End: Instant Filter, Sort, Layout Toggle & Folder Actions -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <!-- Instant filter in current folder -->
                        <div class="position-relative" style="width: 190px;">
                            <span class="position-absolute top-50 start-0 translate-middle-y ps-2 text-muted" style="pointer-events: none;">
                                <i class="bi bi-search small"></i>
                            </span>
                            <input type="text"
                                   id="explorer-filter"
                                   class="form-control form-control-sm rounded-pill"
                                   placeholder="{{ __('messages.Filter view...') ?? 'Filter view...' }}"
                                   style="padding-inline-start: 1.85rem !important;">
                        </div>

                        <!-- Sort Dropdown -->
                        <select id="explorer-sort" class="form-select form-select-sm rounded-pill" style="width: auto; min-width: 155px;" title="{{ __('messages.sort_by_label') }}">
                            <option value="name_asc">{{ __('messages.sort_name_asc') }}</option>
                            <option value="name_desc">{{ __('messages.sort_name_desc') }}</option>
                            <option value="date_desc">{{ __('messages.sort_date_desc') }}</option>
                            <option value="date_asc">{{ __('messages.sort_date_asc') }}</option>
                            <option value="size_desc">{{ __('messages.sort_size_desc') }}</option>
                            <option value="size_asc">{{ __('messages.sort_size_asc') }}</option>
                            <option value="type_asc">{{ __('messages.sort_type_asc') }}</option>
                        </select>

                        <!-- Grid/List layout toggle buttons -->
                        <div class="btn-group btn-group-sm rounded-pill overflow-hidden border bg-white" role="group">
                            <button type="button" class="btn btn-light border-0 px-2.5" id="btn-layout-grid" title="{{ __('messages.Grid View') ?? 'Grid View' }}">
                                <i class="bi bi-grid-fill"></i>
                            </button>
                            <button type="button" class="btn btn-light border-0 px-2.5" id="btn-layout-list" title="{{ __('messages.List View') ?? 'List View' }}">
                                <i class="bi bi-list-ul"></i>
                            </button>
                        </div>

                        <!-- Download current folder as ZIP -->
                        <button type="button"
                                onclick="downloadCurrentFolderZip()"
                                class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                title="{{ __('messages.download_folder_zip_btn') }}">
                            <i class="bi bi-file-earmark-zip me-1"></i><span class="d-none d-md-inline">{{ __('messages.download_folder_zip_btn') }}</span>
                        </button>

                        @if($canManage)
                            <button type="button"
                                    onclick="openUploadModal()"
                                    class="btn btn-sm btn-success rounded-pill px-3"
                                    title="{{ __('messages.upload_files_btn') }}">
                                <i class="bi bi-cloud-arrow-up-fill me-1"></i><span class="d-none d-sm-inline">{{ __('messages.upload_files_btn') }}</span>
                            </button>
                            <button type="button"
                                    onclick="openCreateFolderModal()"
                                    class="btn btn-sm btn-outline-dark rounded-pill px-3"
                                    title="{{ __('messages.new_folder_btn') }}">
                                <i class="bi bi-folder-plus me-1"></i><span class="d-none d-sm-inline">{{ __('messages.new_folder_btn') }}</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Sub-bar: Select All, Item Count & Bulk Selection Actions -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-2 pt-2 border-top border-secondary-subtle small">
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="chk-select-all" onchange="toggleSelectAllInView(this.checked)">
                            <label class="form-check-label text-muted user-select-none" for="chk-select-all">
                                {{ __('messages.select_all_in_folder') }}
                            </label>
                        </div>
                        <span class="text-muted fw-semibold" id="items-count-badge">0 items</span>
                        <span class="text-muted d-none d-md-inline" id="folder-total-size-badge"></span>
                    </div>

                    <!-- Bulk Selection Toolbar (visible when items selected) -->
                    <div id="bulk-selection-bar" class="d-none align-items-center gap-2 bg-primary-subtle text-primary px-3 py-1 rounded-pill border border-primary-subtle">
                        <span class="fw-bold" id="bulk-selected-count">0</span>
                        <button type="button" onclick="downloadSelectedItemsZip()" class="btn btn-xs btn-primary rounded-pill px-2 py-0">
                            <i class="bi bi-file-earmark-zip me-1"></i>{{ __('messages.download_selected_zip_btn') }}
                        </button>
                        <button type="button" onclick="clearSelection()" class="btn btn-xs btn-link text-secondary p-0 text-decoration-none">
                            {{ __('messages.clear_selection_btn') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Dual-Pane Body: Sidebar Tree + Main Folder Contents -->
            <div class="d-flex archive-dual-pane">
                <!-- Collapsible Folder Tree Sidebar -->
                <aside id="archive-tree-sidebar" class="archive-tree-sidebar border-end bg-light-subtle">
                    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                        <span class="fw-bold small text-uppercase text-muted">
                            <i class="bi bi-diagram-3-fill me-1 text-primary"></i>{{ __('messages.folder_tree_heading') }}
                        </span>
                        <button type="button" class="btn btn-sm btn-link text-muted p-0 d-lg-none" onclick="toggleSidebar(false)">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div id="folder-tree-nav" class="p-2 overflow-auto" style="max-height: 68vh;">
                        <!-- Populated dynamically from archiveTree -->
                    </div>
                </aside>

                <!-- Main Explorer Content Area -->
                <main class="flex-grow-1 bg-white" style="min-height: 480px; min-width: 0;">
                    <div id="folder-contents-list">
                        <!-- Dynamic explorer files and folders render here -->
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Hidden form for ZIP download requests -->
    <form id="downloadZipForm" action="{{ route('committee-reports.archive.downloadZip') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="folder_path" id="zip-folder-path-input">
        <div id="zip-paths-container"></div>
    </form>

    <!-- Backdrop Overlay for Details Drawer -->
    <div id="drawer-backdrop" class="drawer-backdrop" onclick="closeDetailsDrawer()"></div>

    <!-- Slideout Info Details Drawer -->
    <div id="details-drawer-panel" class="details-drawer">
        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-3">
            <h5 class="fw-bold mb-0 text-primary">
                <i class="bi bi-info-circle-fill me-2"></i>{{ __('messages.Details') ?? 'Details' }}
            </h5>
            <button type="button" class="btn-close" onclick="closeDetailsDrawer()" aria-label="Close"></button>
        </div>
        <div class="flex-grow-1 overflow-auto pe-1">
            <!-- Icon/Visual preview placeholder -->
            <div class="text-center py-4 bg-light rounded-4 mb-3 border border-light">
                <div id="drawer-file-icon-container" class="fs-1 mb-2"></div>
                <span id="drawer-source-badge" class="badge rounded-pill"></span>
            </div>

            <!-- Metadata info -->
            <div class="mb-3">
                <span class="text-muted small d-block">{{ __('messages.Name') ?? 'Name' }}</span>
                <span id="drawer-file-name" class="fw-bold text-dark text-break fs-6"></span>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <span class="text-muted small d-block">{{ __('messages.Type') ?? 'Type' }}</span>
                    <span id="drawer-file-type" class="badge bg-secondary"></span>
                </div>
                <div class="col-6">
                    <span class="text-muted small d-block">{{ __('messages.Size') ?? 'Size' }}</span>
                    <span id="drawer-file-size" class="fw-semibold text-dark"></span>
                </div>
            </div>

            <div class="mb-3" id="drawer-mtime-row">
                <span class="text-muted small d-block">{{ __('messages.modified_date_label') }}</span>
                <span id="drawer-file-mtime" class="small fw-medium text-dark"></span>
            </div>

            <!-- Linked DB Committee Report Metadata Card -->
            <div id="drawer-db-report-card" class="card bg-primary-subtle border-primary-subtle rounded-3 p-3 mb-3 d-none">
                <div class="fw-bold text-primary small mb-2">
                    <i class="bi bi-file-earmark-check-fill me-1"></i>{{ __('messages.source_synced_badge') }}
                </div>
                <div class="small mb-1">
                    <span class="text-muted">{{ __('messages.Committee') ?? 'Committee' }}:</span>
                    <strong class="text-dark" id="drawer-report-committee"></strong>
                </div>
                <div class="small mb-1">
                    <span class="text-muted">{{ __('messages.Meeting Date') ?? 'Meeting Date' }}:</span>
                    <strong class="text-dark" id="drawer-report-date"></strong>
                </div>
                <div class="small mb-2">
                    <span class="text-muted">{{ __('messages.Description') ?? 'Description' }}:</span>
                    <span class="text-dark" id="drawer-report-desc"></span>
                </div>
                <a href="#" id="drawer-open-report-link" class="btn btn-xs btn-primary rounded-pill w-100">
                    <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('messages.open_linked_report_btn') }}
                </a>
            </div>

            <div class="mb-3">
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
            @if($canManage)
                <div class="d-flex gap-2 mt-1" id="drawer-admin-actions">
                    <button type="button" id="drawer-rename-btn" class="btn btn-sm btn-outline-secondary rounded-pill flex-grow-1">
                        <i class="bi bi-pencil-square me-1"></i>{{ __('messages.rename_item_btn') }}
                    </button>
                    <button type="button" id="drawer-delete-btn" class="btn btn-sm btn-outline-danger rounded-pill flex-grow-1">
                        <i class="bi bi-trash3 me-1"></i>{{ __('messages.delete_item_btn') }}
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Cloud Sync Diagnostics Modal -->
    <div class="modal fade" id="syncDiagnosticsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i>{{ __('messages.sync_diagnostics_title') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <div class="p-3 rounded-3 border bg-light-subtle h-100">
                                <div class="text-muted small mb-1">{{ __('messages.storagebox_mount_label') }}</div>
                                @if(!empty($diagnostics['storagebox_mounted']))
                                    <div class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i>{{ __('messages.connected_ready') }}</div>
                                @else
                                    <div class="fw-bold text-danger"><i class="bi bi-x-circle-fill me-1"></i>{{ __('messages.sync_storagebox_unmounted') }}</div>
                                @endif
                                <div class="small text-muted font-monospace text-truncate mt-1" title="{{ $diagnostics['storagebox_root'] ?? '' }}">
                                    {{ $diagnostics['storagebox_root'] ?? '/mnt/storagebox-archives' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 rounded-3 border bg-light-subtle h-100">
                                <div class="text-muted small mb-1">{{ __('messages.rclone_status_label') }}</div>
                                @if(!empty($diagnostics['rclone_installed']))
                                    <div class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i>{{ $diagnostics['rclone_version'] ?? 'rclone installed' }}</div>
                                @else
                                    <div class="fw-bold text-warning"><i class="bi bi-exclamation-triangle-fill me-1"></i>{{ __('messages.not_configured_yet') }}</div>
                                @endif
                                <div class="small text-muted font-monospace mt-1">{{ $diagnostics['rclone_binary'] ?? '/usr/bin/rclone' }}</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 rounded-3 border bg-light-subtle h-100">
                                <div class="text-muted small mb-1">{{ __('messages.onedrive_remote_label') }}</div>
                                @if(!empty($diagnostics['remote_configured']))
                                    <div class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i>{{ $diagnostics['remote_target'] ?? 'onedrive:Archives' }}</div>
                                @else
                                    <div class="fw-bold text-warning-emphasis"><i class="bi bi-clock-fill me-1"></i>{{ __('messages.not_configured_yet') }}</div>
                                @endif
                                <div class="small text-muted font-monospace text-truncate mt-1">
                                    Mode: {{ $diagnostics['sync_mode'] ?? 'bisync' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(empty($diagnostics['remote_configured']))
                        <div class="alert alert-info rounded-3 border-0 small mb-4">
                            <div class="fw-bold mb-2"><i class="bi bi-terminal-fill me-1"></i>{{ __('messages.onedrive_setup_guide_title') }}</div>
                            <ol class="mb-0 ps-3">
                                <li class="mb-1">Run <code>rclone config --config={{ $diagnostics['rclone_config'] ?? storage_path('app/private/rclone.conf') }}</code> on the server and create a remote named <code>{{ $diagnostics['remote_name'] ?? 'onedrive' }}</code> of type <code>onedrive</code>.</li>
                                <li class="mb-1">Ensure <code>chown www-data:www-data {{ $diagnostics['rclone_config'] ?? storage_path('app/private/rclone.conf') }}</code> so the web server and scheduler can read/refresh the OAuth token.</li>
                                <li>Run <code>php artisan onedrive:sync-archives --force-resync</code> or click <strong>{{ __('messages.sync_now_btn') }}</strong>.</li>
                            </ol>
                        </div>
                    @endif

                    @if(!empty($syncState['error']))
                        <div class="alert alert-danger rounded-3 small mb-4">
                            <strong>Error:</strong> <span class="font-monospace">{{ $syncState['error'] }}</span>
                        </div>
                    @endif

                    <h6 class="fw-bold small text-uppercase text-muted mb-2">{{ __('messages.sync_history_title') }}</h6>
                    @if(!empty($syncState['history']))
                        <div class="table-responsive border rounded-3">
                            <table class="table table-sm table-hover mb-0 small align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('messages.Timestamp') ?? 'Timestamp' }}</th>
                                        <th>{{ __('messages.Status') ?? 'Status' }}</th>
                                        <th>{{ __('messages.total_indexed_files') }}</th>
                                        <th>{{ __('messages.Summary') ?? 'Summary' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($syncState['history'] as $h)
                                        <tr>
                                            <td class="text-nowrap">{{ isset($h['timestamp']) ? \Carbon\Carbon::parse($h['timestamp'])->format('Y-m-d H:i:s') : '-' }}</td>
                                            <td>
                                                <span class="badge {{ ($h['status'] ?? '') === 'completed' ? 'bg-success' : (($h['status'] ?? '') === 'warning' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                    {{ $h['status'] ?? 'unknown' }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($h['total_files'] ?? 0) }}</td>
                                            <td class="text-muted">{{ $h['message'] ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted small mb-0">No sync runs recorded yet.</p>
                    @endif
                </div>
                <div class="modal-footer bg-light border-top">
                    @if($canManage)
                        <button type="button" onclick="triggerCloudSync(true)" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-dismiss="modal">
                            <i class="bi bi-arrow-repeat me-1"></i>{{ __('messages.force_resync_btn') }}
                        </button>
                    @endif
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.close_modal') ?? 'Close' }}</button>
                </div>
            </div>
        </div>
    </div>

    @if($canManage)
        <!-- Upload Files Modal -->
        <div class="modal fade" id="uploadArchiveModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('committee-reports.archive.upload') }}" method="POST" enctype="multipart/form-data" class="modal-content rounded-4 border-0 shadow-lg">
                    @csrf
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-cloud-arrow-up-fill text-success me-2"></i>{{ __('messages.upload_modal_title') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="target_path" id="upload-target-path-input" value="Archives">
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">{{ __('messages.upload_target_folder') }}</label>
                            <div id="upload-target-path-display" class="font-monospace small bg-light p-2 rounded border text-break">Archives</div>
                        </div>
                        <div class="mb-2">
                            <label for="archive-upload-files" class="form-label fw-semibold small">{{ __('messages.select_files_to_upload') }}</label>
                            <input type="file" name="files[]" id="archive-upload-files" class="form-control" multiple required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">{{ __('messages.close_modal') ?? 'Cancel' }}</button>
                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-4">
                            <i class="bi bi-upload me-1"></i>{{ __('messages.upload_files_btn') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Create New Folder Modal -->
        <div class="modal fade" id="createFolderModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('committee-reports.archive.createFolder') }}" method="POST" class="modal-content rounded-4 border-0 shadow-lg">
                    @csrf
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-folder-plus text-primary me-2"></i>{{ __('messages.new_folder_modal_title') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="parent_path" id="create-folder-parent-input" value="Archives">
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">{{ __('messages.parent_folder_label') }}</label>
                            <div id="create-folder-parent-display" class="font-monospace small bg-light p-2 rounded border text-break">Archives</div>
                        </div>
                        <div class="mb-2">
                            <label for="new-folder-name-input" class="form-label fw-semibold small">{{ __('messages.folder_name_label') }}</label>
                            <input type="text" name="folder_name" id="new-folder-name-input" class="form-control" placeholder="{{ __('messages.folder_name_placeholder') }}" required maxlength="255">
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">{{ __('messages.close_modal') ?? 'Cancel' }}</button>
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4">
                            <i class="bi bi-check2 me-1"></i>{{ __('messages.new_folder_btn') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Rename Archive Item Modal -->
        <div class="modal fade" id="renameItemModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('committee-reports.archive.rename') }}" method="POST" class="modal-content rounded-4 border-0 shadow-lg">
                    @csrf
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-pencil-square text-primary me-2"></i>{{ __('messages.rename_modal_title') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="path" id="rename-item-path-input">
                        <div class="mb-3">
                            <label for="rename-item-new-name" class="form-label fw-semibold small">{{ __('messages.new_name_label') }}</label>
                            <input type="text" name="new_name" id="rename-item-new-name" class="form-control" required maxlength="255">
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">{{ __('messages.close_modal') ?? 'Cancel' }}</button>
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4">
                            <i class="bi bi-check2 me-1"></i>{{ __('messages.rename_item_btn') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Archive Item Modal -->
        <div class="modal fade" id="deleteItemModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('committee-reports.archive.delete') }}" method="POST" class="modal-content rounded-4 border-0 shadow-lg">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title fw-bold text-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ __('messages.delete_modal_title') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="path" id="delete-item-path-input">
                        <input type="hidden" name="is_dir" id="delete-item-isdir-input" value="0">
                        <p class="mb-2">{{ __('messages.delete_confirm_message') }}</p>
                        <div id="delete-item-name-display" class="fw-bold text-dark bg-light p-2 rounded border text-break font-monospace small"></div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">{{ __('messages.close_modal') ?? 'Cancel' }}</button>
                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4">
                            <i class="bi bi-trash3 me-1"></i>{{ __('messages.delete_item_btn') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Styles for Dual-Pane Cloud Explorer, Cards & Slideout Drawer -->
    <style>
        .archive-sync-banner {
            background: linear-gradient(135deg, rgba(248, 250, 252, 0.98), rgba(239, 246, 255, 0.9));
            border: 1px solid rgba(59, 130, 246, 0.14) !important;
        }
        .sync-icon-orb {
            width: 44px;
            height: 44px;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        .archive-dual-pane {
            position: relative;
        }
        .archive-tree-sidebar {
            width: 280px;
            min-width: 280px;
            max-width: 320px;
            transition: all 0.25s ease;
        }
        .archive-tree-sidebar.collapsed {
            width: 0 !important;
            min-width: 0 !important;
            overflow: hidden !important;
            border: 0 !important;
        }
        .tree-node-row {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 8px;
            border-radius: 8px;
            cursor: pointer;
            color: #334155;
            font-size: 0.85rem;
            transition: background 0.15s ease;
            user-select: none;
        }
        .tree-node-row:hover {
            background: rgba(59, 130, 246, 0.08);
        }
        .tree-node-row.active-tree-node {
            background: rgba(59, 130, 246, 0.15);
            color: #1d4ed8;
            font-weight: 700;
        }
        .tree-children-group {
            padding-inline-start: 14px;
            border-inline-start: 1px dashed rgba(148, 163, 184, 0.4);
            margin-inline-start: 10px;
        }

        /* Details Slideout Panel CSS */
        .details-drawer {
            position: fixed;
            top: 0;
            width: 390px;
            height: 100vh;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border-inline-start: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 0 30px rgba(0,0,0,0.14);
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
            backdrop-filter: blur(3px);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
        }
        .drawer-backdrop.show {
            opacity: 1;
            visibility: visible;
        }

        /* Folder themes */
        .folder-indigo { --folder-accent: #6366f1; --folder-bg: rgba(99, 102, 241, 0.06); }
        .folder-teal { --folder-accent: #14b8a6; --folder-bg: rgba(20, 184, 166, 0.06); }
        .folder-violet { --folder-accent: #8b5cf6; --folder-bg: rgba(139, 92, 246, 0.06); }
        .folder-amber { --folder-accent: #f59e0b; --folder-bg: rgba(245, 158, 11, 0.06); }
        .folder-slate { --folder-accent: #64748b; --folder-bg: rgba(100, 116, 139, 0.06); }

        .folder-card {
            background: var(--folder-bg) !important;
            border: 1px solid rgba(0, 0, 0, 0.07) !important;
            border-inline-start: 4px solid var(--folder-accent) !important;
            border-radius: 14px;
            padding: 16px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
        }
        .folder-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            border-color: var(--folder-accent) !important;
        }

        .file-card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 14px;
            padding: 16px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
        }
        .file-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            border-color: rgba(59, 130, 246, 0.45);
        }
        .file-card .quick-card-actions,
        .folder-card .quick-card-actions {
            opacity: 0;
            transition: opacity 0.15s ease;
        }
        .file-card:hover .quick-card-actions,
        .folder-card:hover .quick-card-actions {
            opacity: 1;
        }

        .active-item {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2) !important;
        }

        @keyframes spinIcon {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .spin-anim {
            display: inline-block;
            animation: spinIcon 1.1s linear infinite;
        }

        @media (max-width: 991.98px) {
            .archive-tree-sidebar {
                position: absolute;
                top: 0;
                bottom: 0;
                z-index: 30;
                background: #fff;
                box-shadow: 0 10px 25px rgba(0,0,0,0.12);
            }
        }
        @media (max-width: 768px) {
            .details-drawer {
                width: 100% !important;
                height: 68vh !important;
                bottom: 0 !important;
                top: auto !important;
                border-inline-start: none !important;
                border-top: 1px solid rgba(0,0,0,0.1) !important;
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

    <!-- Interactive Cloud Explorer Script -->
    <script>
        const archiveTree = @json($archiveTree);
        const canManageArchive = @json($canManage);
        const isSearchActive = @json($hasActiveFilters);

        let currentPath = 'Archives';
        let currentLayout = localStorage.getItem('archive_layout_pref') || 'grid';
        let currentSort = 'name_asc';
        let selectedPaths = new Set();
        let expandedTreePaths = new Set(['Archives']);

        const i18n = {
            folder: @json(__('messages.Folder') ?? 'Folder'),
            filesCount: @json(__('messages.files_count_badge')),
            foldersCount: @json(__('messages.subfolders_count_badge')),
            emptyFolder: @json(__('messages.This folder is empty.') ?? 'This folder is empty.'),
            folderNotFound: @json(__('messages.Folder not found.') ?? 'Folder not found.'),
            sourceSynced: @json(__('messages.source_synced_badge')),
            sourceCloud: @json(__('messages.source_storagebox_badge')),
            sourceDb: @json(__('messages.source_db_badge')),
            preview: @json(__('messages.Preview File') ?? 'Preview'),
            download: @json(__('messages.Download') ?? 'Download'),
            rename: @json(__('messages.rename_item_btn')),
            deleteItem: @json(__('messages.delete_item_btn')),
            syncingNow: @json(__('messages.sync_status_syncing')),
            syncNowBtn: @json(__('messages.sync_now_btn')),
            doubleClickHint: @json(__('messages.double_click_preview_hint')),
        };

        function getNestedNodeByPath(path) {
            if (!path || path === 'Archives') {
                return { is_dir: true, children: archiveTree, name: 'Archives', path: 'Archives' };
            }

            let parts = path.replace(/^Archives\/?/, '').split('/').filter(Boolean);
            if (parts.length === 0) {
                return { is_dir: true, children: archiveTree, name: 'Archives', path: 'Archives' };
            }

            let currentNode = { is_dir: true, children: archiveTree, name: 'Archives', path: 'Archives' };
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
            if (!bytes || bytes === 0) return '0 B';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        function formatTimestamp(ts) {
            if (!ts) return '';
            const d = new Date(ts * 1000);
            if (isNaN(d.getTime())) return '';
            return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
        }

        function getFileDownloadUrl(item) {
            if (item.encrypted_path) {
                return `{{ route('committee-reports.downloadStorageboxFile') }}?file=${encodeURIComponent(item.encrypted_path)}`;
            }
            if (item.db_report_id) {
                return `{{ url('committee-reports') }}/${item.db_report_id}/pdf`;
            }
            if (item.db_attachment_id) {
                return `{{ url('committee-reports/attachments') }}/${item.db_attachment_id}`;
            }
            return '#';
        }

        function isPreviewableFile(filename) {
            const lower = (filename || '').toLowerCase();
            return lower.endsWith('.pdf')
                || lower.endsWith('.xlsx')
                || lower.endsWith('.xls')
                || lower.endsWith('.docx')
                || lower.endsWith('.doc')
                || lower.endsWith('.png')
                || lower.endsWith('.jpg')
                || lower.endsWith('.jpeg')
                || lower.endsWith('.gif');
        }

        function getFileIconElement(name) {
            const ext = (name || '').split('.').pop().toLowerCase();
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
                case 'webp':
                    icon.classList.add('bi-file-earmark-image-fill', 'text-warning');
                    break;
                case 'zip':
                case 'rar':
                    icon.classList.add('bi-file-earmark-zip-fill', 'text-secondary');
                    break;
                default:
                    icon.classList.add('bi-file-earmark-text-fill', 'text-secondary');
            }
            return icon;
        }

        function getFolderTheme(name) {
            const lower = (name || '').toLowerCase();
            if (lower.includes('أجندة') || lower.includes('agenda')) {
                return 'folder-indigo';
            } else if (lower.includes('التقارير') || lower.includes('report')) {
                return 'folder-teal';
            } else if (lower.includes('المرفقات') || lower.includes('attachment')) {
                return 'folder-violet';
            } else if (/\b(19|20)\d{2}\b/.test(lower)) {
                return 'folder-amber';
            }
            return 'folder-slate';
        }

        function getFlattenedMatches(nodes) {
            let matches = [];
            (nodes || []).forEach(node => {
                if (node.is_dir) {
                    matches = matches.concat(getFlattenedMatches(node.children));
                } else {
                    matches.push(node);
                }
            });
            return matches;
        }

        function sortItems(items) {
            const copy = [...items];
            copy.sort((a, b) => {
                if (a.is_dir && !b.is_dir) return -1;
                if (!a.is_dir && b.is_dir) return 1;

                switch (currentSort) {
                    case 'name_desc':
                        return b.name.localeCompare(a.name, undefined, { numeric: true, sensitivity: 'base' });
                    case 'date_desc':
                        return (b.mtime || 0) - (a.mtime || 0);
                    case 'date_asc':
                        return (a.mtime || 0) - (b.mtime || 0);
                    case 'size_desc':
                        return (b.size || 0) - (a.size || 0);
                    case 'size_asc':
                        return (a.size || 0) - (b.size || 0);
                    case 'type_asc': {
                        const extA = a.is_dir ? '00_dir' : (a.name.split('.').pop() || '').toLowerCase();
                        const extB = b.is_dir ? '00_dir' : (b.name.split('.').pop() || '').toLowerCase();
                        return extA.localeCompare(extB) || a.name.localeCompare(b.name, undefined, { numeric: true });
                    }
                    case 'name_asc':
                    default:
                        if (a.is_dir && b.is_dir && /^\d{4}$/.test(a.name) && /^\d{4}$/.test(b.name)) {
                            return b.name.localeCompare(a.name);
                        }
                        return a.name.localeCompare(b.name, undefined, { numeric: true, sensitivity: 'base' });
                }
            });
            return copy;
        }

        // Render Sidebar Folder Tree
        function renderSidebarTree() {
            const nav = document.getElementById('folder-tree-nav');
            if (!nav) return;
            nav.replaceChildren();

            // Root node row
            const rootRow = document.createElement('div');
            rootRow.className = 'tree-node-row ' + (currentPath === 'Archives' ? 'active-tree-node' : '');
            const rootIcon = document.createElement('i');
            rootIcon.className = 'bi bi-hdd-network-fill text-primary';
            rootRow.appendChild(rootIcon);
            const rootLabel = document.createElement('span');
            rootLabel.className = 'text-truncate flex-grow-1';
            rootLabel.textContent = 'Archives';
            rootRow.appendChild(rootLabel);
            rootRow.addEventListener('click', () => navigateToPath('Archives'));
            nav.appendChild(rootRow);

            function buildSubtreeDom(nodes) {
                const container = document.createElement('div');
                container.className = 'tree-children-group';
                (nodes || []).filter(n => n.is_dir).forEach(dirNode => {
                    const isExpanded = expandedTreePaths.has(dirNode.path) || currentPath === dirNode.path || currentPath.startsWith(dirNode.path + '/');
                    if (isExpanded) {
                        expandedTreePaths.add(dirNode.path);
                    }

                    const subDirs = (dirNode.children || []).filter(c => c.is_dir);

                    const row = document.createElement('div');
                    row.className = 'tree-node-row ' + (currentPath === dirNode.path ? 'active-tree-node' : '');

                    const chevron = document.createElement('i');
                    if (subDirs.length > 0) {
                        chevron.className = 'bi ' + (isExpanded ? 'bi-chevron-down' : 'bi-chevron-right') + ' small text-muted';
                        chevron.addEventListener('click', (e) => {
                            e.stopPropagation();
                            if (expandedTreePaths.has(dirNode.path)) {
                                expandedTreePaths.delete(dirNode.path);
                            } else {
                                expandedTreePaths.add(dirNode.path);
                            }
                            renderSidebarTree();
                        });
                    } else {
                        chevron.className = 'bi bi-dot text-muted';
                    }
                    row.appendChild(chevron);

                    const folderIcon = document.createElement('i');
                    folderIcon.className = 'bi ' + (currentPath === dirNode.path ? 'bi-folder2-open text-primary' : 'bi-folder-fill text-warning');
                    row.appendChild(folderIcon);

                    const nameSpan = document.createElement('span');
                    nameSpan.className = 'text-truncate flex-grow-1';
                    nameSpan.textContent = dirNode.name;
                    nameSpan.title = dirNode.name;
                    row.appendChild(nameSpan);

                    if (dirNode.recursive_files_count) {
                        const countBadge = document.createElement('span');
                        countBadge.className = 'badge bg-light text-secondary border rounded-pill';
                        countBadge.style.fontSize = '0.68rem';
                        countBadge.textContent = dirNode.recursive_files_count;
                        row.appendChild(countBadge);
                    }

                    row.addEventListener('click', () => {
                        expandedTreePaths.add(dirNode.path);
                        navigateToPath(dirNode.path);
                    });

                    container.appendChild(row);

                    if (subDirs.length > 0 && isExpanded) {
                        container.appendChild(buildSubtreeDom(dirNode.children));
                    }
                });
                return container;
            }

            nav.appendChild(buildSubtreeDom(archiveTree));
        }

        function createFolderGridCard(item) {
            const card = document.createElement('div');
            const theme = getFolderTheme(item.name);
            card.className = `folder-card ${theme} h-100 d-flex flex-column justify-content-between`;
            if (selectedPaths.has(item.path)) {
                card.classList.add('active-item');
            }

            const topDiv = document.createElement('div');
            topDiv.className = 'd-flex align-items-start justify-content-between mb-2';

            const leftTop = document.createElement('div');
            leftTop.className = 'd-flex align-items-center gap-2';

            const chk = document.createElement('input');
            chk.type = 'checkbox';
            chk.className = 'form-check-input mt-0';
            chk.checked = selectedPaths.has(item.path);
            chk.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleSelectItem(item.path, chk.checked);
            });
            leftTop.appendChild(chk);

            const icon = document.createElement('i');
            icon.className = 'bi bi-folder-fill fs-2';
            icon.style.color = 'var(--folder-accent)';
            leftTop.appendChild(icon);
            topDiv.appendChild(leftTop);

            const actionsDiv = document.createElement('div');
            actionsDiv.className = 'd-flex align-items-center gap-1 quick-card-actions';

            const zipBtn = document.createElement('button');
            zipBtn.type = 'button';
            zipBtn.className = 'btn btn-xs btn-light border rounded-pill px-2 py-0';
            zipBtn.title = @json(__('messages.download_folder_zip_btn'));
            const zipIcon = document.createElement('i');
            zipIcon.className = 'bi bi-file-earmark-zip text-primary small';
            zipBtn.appendChild(zipIcon);
            zipBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                downloadFolderZip(item.path);
            });
            actionsDiv.appendChild(zipBtn);

            if (canManageArchive) {
                const renBtn = document.createElement('button');
                renBtn.type = 'button';
                renBtn.className = 'btn btn-xs btn-light border rounded-pill px-2 py-0';
                renBtn.title = i18n.rename;
                const renIcon = document.createElement('i');
                renIcon.className = 'bi bi-pencil small text-secondary';
                renBtn.appendChild(renIcon);
                renBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    openRenameModal(item);
                });
                actionsDiv.appendChild(renBtn);
            }

            topDiv.appendChild(actionsDiv);
            card.appendChild(topDiv);

            const bodyDiv = document.createElement('div');
            const nameDiv = document.createElement('div');
            nameDiv.className = 'fw-bold text-dark text-truncate mb-1';
            nameDiv.textContent = item.name;
            nameDiv.title = item.name;
            bodyDiv.appendChild(nameDiv);

            const statsDiv = document.createElement('div');
            statsDiv.className = 'd-flex align-items-center justify-content-between text-muted small';

            const countSpan = document.createElement('span');
            const filesCount = item.recursive_files_count ?? 0;
            countSpan.textContent = `${filesCount} ${i18n.filesCount}`;
            statsDiv.appendChild(countSpan);

            if (item.size > 0) {
                const sizeSpan = document.createElement('span');
                sizeSpan.textContent = formatBytes(item.size);
                statsDiv.appendChild(sizeSpan);
            }
            bodyDiv.appendChild(statsDiv);
            card.appendChild(bodyDiv);

            card.addEventListener('click', () => navigateToPath(item.path));
            return card;
        }

        function createFileGridCard(item) {
            const card = document.createElement('div');
            card.className = 'file-card h-100 d-flex flex-column justify-content-between';
            card.dataset.path = item.path;
            card.title = i18n.doubleClickHint;
            if (selectedPaths.has(item.path)) {
                card.classList.add('active-item');
            }

            const topDiv = document.createElement('div');
            topDiv.className = 'd-flex align-items-start justify-content-between mb-2';

            const leftTop = document.createElement('div');
            leftTop.className = 'd-flex align-items-center gap-2';

            const chk = document.createElement('input');
            chk.type = 'checkbox';
            chk.className = 'form-check-input mt-0';
            chk.checked = selectedPaths.has(item.path);
            chk.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleSelectItem(item.path, chk.checked);
            });
            leftTop.appendChild(chk);
            leftTop.appendChild(getFileIconElement(item.name));
            topDiv.appendChild(leftTop);

            // Quick hover actions (Preview / Download)
            const quickActions = document.createElement('div');
            quickActions.className = 'd-flex align-items-center gap-1 quick-card-actions';

            const downloadUrl = getFileDownloadUrl(item);
            if (isPreviewableFile(item.name)) {
                const prevBtn = document.createElement('button');
                prevBtn.type = 'button';
                prevBtn.className = 'btn btn-xs btn-outline-primary rounded-pill px-2 py-0';
                prevBtn.title = i18n.preview;
                const prevIcon = document.createElement('i');
                prevIcon.className = 'bi bi-eye-fill small';
                prevBtn.appendChild(prevIcon);
                prevBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    previewDocument(downloadUrl, item.name);
                });
                quickActions.appendChild(prevBtn);
            }

            const dlLink = document.createElement('a');
            dlLink.href = downloadUrl;
            dlLink.target = '_blank';
            dlLink.className = 'btn btn-xs btn-outline-secondary rounded-pill px-2 py-0';
            dlLink.title = i18n.download;
            const dlIcon = document.createElement('i');
            dlIcon.className = 'bi bi-download small';
            dlLink.appendChild(dlIcon);
            dlLink.addEventListener('click', (e) => e.stopPropagation());
            quickActions.appendChild(dlLink);

            topDiv.appendChild(quickActions);
            card.appendChild(topDiv);

            const infoDiv = document.createElement('div');
            infoDiv.className = 'mt-1';

            const nameDiv = document.createElement('div');
            nameDiv.className = 'fw-semibold text-dark text-truncate mb-1';
            nameDiv.textContent = item.name;
            nameDiv.title = item.name;
            infoDiv.appendChild(nameDiv);

            if (item.meeting_day_description) {
                const descBadge = document.createElement('div');
                descBadge.className = 'text-primary small text-truncate mb-1';
                descBadge.style.fontSize = '0.75rem';
                descBadge.textContent = `${item.meeting_date || ''} • ${item.meeting_day_description}`;
                infoDiv.appendChild(descBadge);
            }

            const metaDiv = document.createElement('div');
            metaDiv.className = 'd-flex align-items-center justify-content-between text-muted small mt-1';

            const sizeSpan = document.createElement('span');
            sizeSpan.textContent = item.size ? formatBytes(item.size) : (item.source === 'db' ? 'PDF' : '0 B');
            metaDiv.appendChild(sizeSpan);

            const srcDot = document.createElement('span');
            if (item.source === 'synced') {
                srcDot.className = 'badge bg-success-subtle text-success border border-success-subtle';
                srcDot.textContent = 'Synced';
            } else if (item.source === 'db') {
                srcDot.className = 'badge bg-info-subtle text-info-emphasis border border-info-subtle';
                srcDot.textContent = 'DB';
            } else {
                srcDot.className = 'text-muted';
                srcDot.textContent = formatTimestamp(item.mtime);
            }
            metaDiv.appendChild(srcDot);

            infoDiv.appendChild(metaDiv);
            card.appendChild(infoDiv);

            card.addEventListener('click', () => {
                document.querySelectorAll('.file-card, .list-group-item').forEach(el => el.classList.remove('active-item'));
                card.classList.add('active-item');
                showDetailsDrawer(item);
            });

            card.addEventListener('dblclick', (e) => {
                e.stopPropagation();
                if (isPreviewableFile(item.name)) {
                    previewDocument(downloadUrl, item.name);
                } else {
                    window.open(downloadUrl, '_blank');
                }
            });

            return card;
        }

        function createFolderListRow(item) {
            const row = document.createElement('div');
            row.className = 'list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2 px-3 border-bottom border-light';
            row.style.cursor = 'pointer';

            const leftDiv = document.createElement('div');
            leftDiv.className = 'd-flex align-items-center gap-3 overflow-hidden';

            const chk = document.createElement('input');
            chk.type = 'checkbox';
            chk.className = 'form-check-input mt-0';
            chk.checked = selectedPaths.has(item.path);
            chk.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleSelectItem(item.path, chk.checked);
            });
            leftDiv.appendChild(chk);

            const icon = document.createElement('i');
            icon.className = 'bi bi-folder-fill text-warning fs-4';
            leftDiv.appendChild(icon);

            const nameWrap = document.createElement('div');
            nameWrap.className = 'text-truncate';
            const nameSpan = document.createElement('span');
            nameSpan.className = 'fw-semibold text-dark d-block text-truncate';
            nameSpan.textContent = item.name;
            nameWrap.appendChild(nameSpan);

            const subMeta = document.createElement('span');
            subMeta.className = 'text-muted small';
            subMeta.textContent = `${item.recursive_files_count ?? 0} ${i18n.filesCount}` + (item.size ? ` • ${formatBytes(item.size)}` : '');
            nameWrap.appendChild(subMeta);

            leftDiv.appendChild(nameWrap);
            row.appendChild(leftDiv);

            const rightDiv = document.createElement('div');
            rightDiv.className = 'd-flex align-items-center gap-2';

            if (item.mtime) {
                const dateSpan = document.createElement('span');
                dateSpan.className = 'text-muted small d-none d-md-inline';
                dateSpan.textContent = formatTimestamp(item.mtime);
                rightDiv.appendChild(dateSpan);
            }

            const zipBtn = document.createElement('button');
            zipBtn.type = 'button';
            zipBtn.className = 'btn btn-sm btn-light border rounded-pill px-2 py-0';
            zipBtn.title = @json(__('messages.download_folder_zip_btn'));
            const zipIcon = document.createElement('i');
            zipIcon.className = 'bi bi-file-earmark-zip text-primary';
            zipBtn.appendChild(zipIcon);
            zipBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                downloadFolderZip(item.path);
            });
            rightDiv.appendChild(zipBtn);

            row.appendChild(rightDiv);

            row.addEventListener('click', () => navigateToPath(item.path));
            return row;
        }

        function createFileListRow(item) {
            const row = document.createElement('div');
            row.className = 'list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2 px-3 border-bottom border-light';
            row.style.cursor = 'pointer';
            row.dataset.path = item.path;

            const leftDiv = document.createElement('div');
            leftDiv.className = 'd-flex align-items-center gap-3 overflow-hidden me-2';

            const chk = document.createElement('input');
            chk.type = 'checkbox';
            chk.className = 'form-check-input mt-0';
            chk.checked = selectedPaths.has(item.path);
            chk.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleSelectItem(item.path, chk.checked);
            });
            leftDiv.appendChild(chk);
            leftDiv.appendChild(getFileIconElement(item.name));

            const textDiv = document.createElement('div');
            textDiv.className = 'text-truncate';

            const nameSpan = document.createElement('span');
            nameSpan.className = 'fw-medium text-dark d-block text-truncate';
            nameSpan.textContent = item.name;
            nameSpan.title = item.name;
            textDiv.appendChild(nameSpan);

            const metaDiv = document.createElement('div');
            metaDiv.className = 'd-flex align-items-center flex-wrap gap-2 small text-muted';

            if (isSearchActive) {
                let pathParts = item.path.split('/');
                pathParts.pop();
                if (pathParts[0] === 'Archives') pathParts.shift();
                if (pathParts.length > 0) {
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-light text-secondary border';
                    badge.textContent = pathParts.join(' » ');
                    metaDiv.appendChild(badge);
                }
            }

            if (item.size) {
                const sizeSpan = document.createElement('span');
                sizeSpan.textContent = formatBytes(item.size);
                metaDiv.appendChild(sizeSpan);
            }

            if (item.mtime) {
                const dateSpan = document.createElement('span');
                dateSpan.textContent = formatTimestamp(item.mtime);
                metaDiv.appendChild(dateSpan);
            }

            textDiv.appendChild(metaDiv);
            leftDiv.appendChild(textDiv);
            row.appendChild(leftDiv);

            const rightActions = document.createElement('div');
            rightActions.className = 'd-flex align-items-center gap-1 flex-shrink-0';

            const downloadUrl = getFileDownloadUrl(item);
            if (isPreviewableFile(item.name)) {
                const prevBtn = document.createElement('button');
                prevBtn.type = 'button';
                prevBtn.className = 'btn btn-sm btn-outline-primary rounded-pill px-2 py-0';
                prevBtn.title = i18n.preview;
                const prevIcon = document.createElement('i');
                prevIcon.className = 'bi bi-eye-fill';
                prevBtn.appendChild(prevIcon);
                prevBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    previewDocument(downloadUrl, item.name);
                });
                rightActions.appendChild(prevBtn);
            }

            const dlLink = document.createElement('a');
            dlLink.href = downloadUrl;
            dlLink.target = '_blank';
            dlLink.className = 'btn btn-sm btn-outline-secondary rounded-pill px-2 py-0';
            dlLink.title = i18n.download;
            const dlIcon = document.createElement('i');
            dlIcon.className = 'bi bi-download';
            dlLink.appendChild(dlIcon);
            dlLink.addEventListener('click', (e) => e.stopPropagation());
            rightActions.appendChild(dlLink);

            row.appendChild(rightActions);

            row.addEventListener('click', () => {
                document.querySelectorAll('.file-card, .list-group-item').forEach(el => el.classList.remove('active-item'));
                row.classList.add('active-item');
                showDetailsDrawer(item);
            });

            row.addEventListener('dblclick', (e) => {
                e.stopPropagation();
                if (isPreviewableFile(item.name)) {
                    previewDocument(downloadUrl, item.name);
                } else {
                    window.open(downloadUrl, '_blank');
                }
            });

            return row;
        }

        function renderExplorer() {
            let node = getNestedNodeByPath(currentPath);
            const container = document.getElementById('folder-contents-list');
            const breadcrumbs = document.getElementById('archive-breadcrumbs');
            const countBadge = document.getElementById('items-count-badge');
            const sizeBadge = document.getElementById('folder-total-size-badge');
            const upBtn = document.getElementById('btn-up-level');

            if (upBtn) {
                upBtn.disabled = (currentPath === 'Archives' || !currentPath.includes('/'));
            }

            renderSidebarTree();

            if (!node || !node.is_dir) {
                container.replaceChildren();
                const errDiv = document.createElement('div');
                errDiv.className = 'p-5 text-center text-muted';
                const errIcon = document.createElement('i');
                errIcon.className = 'bi bi-exclamation-circle fs-3 d-block mb-2 text-warning';
                errDiv.appendChild(errIcon);
                errDiv.appendChild(document.createTextNode(i18n.folderNotFound));
                container.appendChild(errDiv);
                return;
            }

            // Render Breadcrumbs
            breadcrumbs.replaceChildren();
            const rootLi = document.createElement('li');
            rootLi.className = 'breadcrumb-item';
            const rootLink = document.createElement('a');
            rootLink.href = '#';
            rootLink.className = 'text-primary fw-semibold text-decoration-none';
            rootLink.addEventListener('click', (e) => {
                e.preventDefault();
                navigateToPath('Archives');
            });
            const rootIcon = document.createElement('i');
            rootIcon.className = 'bi bi-hdd-network-fill me-1';
            rootLink.appendChild(rootIcon);
            rootLink.appendChild(document.createTextNode('Archives'));
            rootLi.appendChild(rootLink);
            breadcrumbs.appendChild(rootLi);

            let relativePathParts = currentPath.replace(/^Archives\/?/, '').split('/').filter(Boolean);
            let runningPath = 'Archives';

            relativePathParts.forEach((part, index) => {
                runningPath += '/' + part;
                const li = document.createElement('li');
                if (index === relativePathParts.length - 1) {
                    li.className = 'breadcrumb-item active fw-bold text-dark text-nowrap';
                    li.setAttribute('aria-current', 'page');
                    li.textContent = part;
                } else {
                    li.className = 'breadcrumb-item text-nowrap';
                    const link = document.createElement('a');
                    link.href = '#';
                    link.className = 'text-primary fw-semibold text-decoration-none';
                    const targetPath = runningPath;
                    link.addEventListener('click', (e) => {
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
                displayItems = displayItems.filter(item =>
                    (item.name || '').toLowerCase().includes(filterVal) ||
                    (item.meeting_day_description || '').toLowerCase().includes(filterVal) ||
                    (item.committee_name || '').toLowerCase().includes(filterVal)
                );
            }

            displayItems = sortItems(displayItems);

            countBadge.textContent = `${displayItems.length} items`;
            if (sizeBadge) {
                sizeBadge.textContent = node.size ? `• ${formatBytes(node.size)}` : '';
            }

            container.replaceChildren();

            if (displayItems.length > 0) {
                if (currentLayout === 'grid') {
                    const gridRow = document.createElement('div');
                    gridRow.className = 'row g-3 p-3';
                    displayItems.forEach(item => {
                        const col = document.createElement('div');
                        col.className = 'col-12 col-sm-6 col-md-4 col-xl-3';
                        col.appendChild(item.is_dir ? createFolderGridCard(item) : createFileGridCard(item));
                        gridRow.appendChild(col);
                    });
                    container.appendChild(gridRow);
                } else {
                    const listContainer = document.createElement('div');
                    listContainer.className = 'list-group list-group-flush';
                    displayItems.forEach(item => {
                        listContainer.appendChild(item.is_dir ? createFolderListRow(item) : createFileListRow(item));
                    });
                    container.appendChild(listContainer);
                }
            } else {
                const emptyDiv = document.createElement('div');
                emptyDiv.className = 'p-5 text-center text-muted';
                const emptyIcon = document.createElement('i');
                emptyIcon.className = 'bi bi-folder2-open fs-1 d-block mb-2 text-secondary';
                emptyDiv.appendChild(emptyIcon);
                const textSpan = document.createElement('span');
                textSpan.textContent = i18n.emptyFolder;
                emptyDiv.appendChild(textSpan);
                container.appendChild(emptyDiv);
            }
        }

        window.navigateToPath = function(path, pushHistory = true) {
            currentPath = path || 'Archives';
            const chkAll = document.getElementById('chk-select-all');
            if (chkAll) chkAll.checked = false;

            if (pushHistory && window.history && window.history.pushState) {
                const url = new URL(window.location.href);
                if (currentPath === 'Archives') {
                    url.searchParams.delete('path');
                } else {
                    url.searchParams.set('path', currentPath);
                }
                window.history.pushState({ path: currentPath }, '', url.toString());
            }

            renderExplorer();
            closeDetailsDrawer();
        };

        window.navigateUpOneLevel = function() {
            if (!currentPath || currentPath === 'Archives') return;
            const parts = currentPath.split('/');
            parts.pop();
            const parent = parts.join('/') || 'Archives';
            navigateToPath(parent);
        };

        window.toggleSidebar = function(forceState) {
            const sb = document.getElementById('archive-tree-sidebar');
            if (!sb) return;
            if (typeof forceState === 'boolean') {
                sb.classList.toggle('collapsed', !forceState);
            } else {
                sb.classList.toggle('collapsed');
            }
        };

        // Bulk selection handlers
        function toggleSelectItem(path, checked) {
            if (checked) {
                selectedPaths.add(path);
            } else {
                selectedPaths.delete(path);
            }
            updateBulkSelectionBar();
        }

        window.toggleSelectAllInView = function(checked) {
            const node = getNestedNodeByPath(currentPath);
            const items = (node && node.children) ? node.children : [];
            items.forEach(it => {
                if (checked) {
                    selectedPaths.add(it.path);
                } else {
                    selectedPaths.delete(it.path);
                }
            });
            updateBulkSelectionBar();
            renderExplorer();
        };

        window.clearSelection = function() {
            selectedPaths.clear();
            const chkAll = document.getElementById('chk-select-all');
            if (chkAll) chkAll.checked = false;
            updateBulkSelectionBar();
            renderExplorer();
        };

        function updateBulkSelectionBar() {
            const bar = document.getElementById('bulk-selection-bar');
            const countEl = document.getElementById('bulk-selected-count');
            if (!bar || !countEl) return;
            if (selectedPaths.size > 0) {
                bar.classList.remove('d-none');
                bar.classList.add('d-flex');
                countEl.textContent = `${selectedPaths.size} selected`;
            } else {
                bar.classList.add('d-none');
                bar.classList.remove('d-flex');
            }
        }

        window.downloadFolderZip = function(folderPath) {
            const form = document.getElementById('downloadZipForm');
            const folderInput = document.getElementById('zip-folder-path-input');
            const pathsContainer = document.getElementById('zip-paths-container');
            folderInput.value = folderPath || currentPath;
            pathsContainer.replaceChildren();
            form.submit();
        };

        window.downloadCurrentFolderZip = function() {
            downloadFolderZip(currentPath);
        };

        window.downloadSelectedItemsZip = function() {
            if (selectedPaths.size === 0) return;
            const form = document.getElementById('downloadZipForm');
            const folderInput = document.getElementById('zip-folder-path-input');
            const pathsContainer = document.getElementById('zip-paths-container');
            folderInput.value = '';
            pathsContainer.replaceChildren();
            selectedPaths.forEach(p => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'paths[]';
                inp.value = p;
                pathsContainer.appendChild(inp);
            });
            form.submit();
        };

        // Modals for Upload, Create Folder, Rename, Delete
        window.openUploadModal = function() {
            const input = document.getElementById('upload-target-path-input');
            const disp = document.getElementById('upload-target-path-display');
            if (input) input.value = currentPath;
            if (disp) disp.textContent = currentPath;
            new bootstrap.Modal(document.getElementById('uploadArchiveModal')).show();
        };

        window.openCreateFolderModal = function() {
            const input = document.getElementById('create-folder-parent-input');
            const disp = document.getElementById('create-folder-parent-display');
            if (input) input.value = currentPath;
            if (disp) disp.textContent = currentPath;
            new bootstrap.Modal(document.getElementById('createFolderModal')).show();
        };

        window.openRenameModal = function(item) {
            const pathInp = document.getElementById('rename-item-path-input');
            const nameInp = document.getElementById('rename-item-new-name');
            if (pathInp) pathInp.value = item.path;
            if (nameInp) nameInp.value = item.name;
            new bootstrap.Modal(document.getElementById('renameItemModal')).show();
        };

        window.openDeleteModal = function(item) {
            const pathInp = document.getElementById('delete-item-path-input');
            const dirInp = document.getElementById('delete-item-isdir-input');
            const disp = document.getElementById('delete-item-name-display');
            if (pathInp) pathInp.value = item.path;
            if (dirInp) dirInp.value = item.is_dir ? '1' : '0';
            if (disp) disp.textContent = item.path;
            new bootstrap.Modal(document.getElementById('deleteItemModal')).show();
        };

        // Live OneDrive & StorageBox Sync Trigger + Polling
        window.triggerCloudSync = function(forceResync = false) {
            const btn = document.getElementById('btn-trigger-sync');
            const icon = document.getElementById('btn-sync-icon');
            const label = document.getElementById('btn-sync-label');
            const statusText = document.getElementById('sync-status-text');

            if (btn) btn.disabled = true;
            if (icon) icon.classList.add('spin-anim');
            if (label) label.textContent = i18n.syncingNow;
            if (statusText) statusText.textContent = i18n.syncingNow;

            fetch(`{{ route('committee-reports.archive.sync') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ force_resync: forceResync })
            })
            .then(r => r.json())
            .then(() => {
                pollSyncStatus();
            })
            .catch(() => {
                if (btn) btn.disabled = false;
                if (icon) icon.classList.remove('spin-anim');
                if (label) label.textContent = i18n.syncNowBtn;
            });
        };

        function pollSyncStatus() {
            const interval = setInterval(() => {
                fetch(`{{ route('committee-reports.archive.syncStatus') }}`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(state => {
                    const filesEl = document.getElementById('sync-stat-files');
                    const dirsEl = document.getElementById('sync-stat-dirs');
                    const statusText = document.getElementById('sync-status-text');
                    if (filesEl && state.total_files !== undefined) {
                        filesEl.textContent = Number(state.total_files).toLocaleString();
                    }
                    if (dirsEl && state.total_dirs !== undefined) {
                        dirsEl.textContent = Number(state.total_dirs).toLocaleString();
                    }
                    if (statusText && state.message) {
                        statusText.textContent = state.message;
                    }
                    if (state.status !== 'syncing' && state.status !== 'indexing') {
                        clearInterval(interval);
                        window.location.reload();
                    }
                })
                .catch(() => clearInterval(interval));
            }, 2000);
        }

        // Slideout Details Drawer
        window.showDetailsDrawer = function(item) {
            const drawer = document.getElementById('details-drawer-panel');
            const backdrop = document.getElementById('drawer-backdrop');

            const iconContainer = document.getElementById('drawer-file-icon-container');
            iconContainer.replaceChildren(getFileIconElement(item.name).cloneNode(true));

            const srcBadge = document.getElementById('drawer-source-badge');
            if (item.source === 'synced') {
                srcBadge.className = 'badge rounded-pill bg-success-subtle text-success border border-success-subtle';
                srcBadge.textContent = i18n.sourceSynced;
            } else if (item.source === 'db') {
                srcBadge.className = 'badge rounded-pill bg-info-subtle text-info-emphasis border border-info-subtle';
                srcBadge.textContent = i18n.sourceDb;
            } else {
                srcBadge.className = 'badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle';
                srcBadge.textContent = i18n.sourceCloud;
            }

            document.getElementById('drawer-file-name').textContent = item.name;

            const ext = (item.name.split('.').pop() || '').toUpperCase();
            const typeBadge = document.getElementById('drawer-file-type');
            typeBadge.textContent = ext || 'FILE';
            typeBadge.className = 'badge text-white ' + (ext === 'PDF' ? 'bg-danger' : (['XLSX', 'XLS', 'CSV'].includes(ext) ? 'bg-success' : 'bg-primary'));

            document.getElementById('drawer-file-size').textContent = item.size ? formatBytes(item.size) : (item.source === 'db' ? 'Dynamic PDF' : 'N/A');
            document.getElementById('drawer-file-path').textContent = item.path;

            const mtimeRow = document.getElementById('drawer-mtime-row');
            const mtimeVal = document.getElementById('drawer-file-mtime');
            if (item.mtime) {
                mtimeRow.classList.remove('d-none');
                mtimeVal.textContent = formatTimestamp(item.mtime);
            } else {
                mtimeRow.classList.add('d-none');
            }

            // Linked DB report card
            const dbCard = document.getElementById('drawer-db-report-card');
            if (item.db_report_id) {
                dbCard.classList.remove('d-none');
                document.getElementById('drawer-report-committee').textContent = item.committee_name || '-';
                document.getElementById('drawer-report-date').textContent = item.meeting_date || '-';
                document.getElementById('drawer-report-desc').textContent = item.meeting_day_description || '-';
                document.getElementById('drawer-open-report-link').href = `{{ url('committee-reports') }}/${item.db_report_id}`;
            } else {
                dbCard.classList.add('d-none');
            }

            const downloadUrl = getFileDownloadUrl(item);
            const previewBtn = document.getElementById('drawer-preview-btn');
            const downloadBtn = document.getElementById('drawer-download-btn');
            downloadBtn.href = downloadUrl;

            if (isPreviewableFile(item.name)) {
                previewBtn.style.display = 'block';
                previewBtn.onclick = function() {
                    previewDocument(downloadUrl, item.name);
                };
            } else {
                previewBtn.style.display = 'none';
            }

            if (canManageArchive) {
                const adminActions = document.getElementById('drawer-admin-actions');
                const renBtn = document.getElementById('drawer-rename-btn');
                const delBtn = document.getElementById('drawer-delete-btn');
                if (item.source === 'db' && !item.encrypted_path) {
                    if (adminActions) adminActions.classList.add('d-none');
                } else {
                    if (adminActions) adminActions.classList.remove('d-none');
                    if (renBtn) renBtn.onclick = () => openRenameModal(item);
                    if (delBtn) delBtn.onclick = () => openDeleteModal(item);
                }
            }

            drawer.classList.add('open');
            backdrop.classList.add('show');
        };

        window.closeDetailsDrawer = function() {
            const drawer = document.getElementById('details-drawer-panel');
            const backdrop = document.getElementById('drawer-backdrop');
            if (drawer) drawer.classList.remove('open');
            if (backdrop) backdrop.classList.remove('show');
        };

        // Offline Document Preview Modal Handler
        window.previewDocument = function(url, filename) {
            let lowercaseName = (filename || '').toLowerCase();
            const inlineUrl = url + (url.includes('?') ? '&' : '?') + 'disposition=inline';

            if (lowercaseName.endsWith('.pdf') && window.innerWidth < 992) {
                window.open(inlineUrl, '_blank');
                return;
            }

            const objectContainer = document.getElementById('documentPreviewObjectContainer');
            const objectEl = document.getElementById('documentPreviewObject');
            const fallbackLink = document.getElementById('documentPreviewFallbackLink');
            const openNewTabBtn = document.getElementById('pdfOpenNewTabBtn');
            const docxContainer = document.getElementById('docxPreviewContainer');
            const excelContainer = document.getElementById('excelPreviewContainer');
            const imageContainer = document.getElementById('imagePreviewContainer');
            const img = document.getElementById('documentPreviewImg');

            objectContainer.classList.add('d-none');
            objectContainer.classList.remove('d-flex');
            docxContainer.classList.add('d-none');
            excelContainer.classList.add('d-none');
            imageContainer.classList.add('d-none');
            if (openNewTabBtn) openNewTabBtn.classList.add('d-none');
            docxContainer.replaceChildren();
            excelContainer.replaceChildren();
            img.src = '';

            document.getElementById('documentPreviewModalLabel').textContent = filename;
            const myModal = new bootstrap.Modal(document.getElementById('documentPreviewModal'));
            myModal.show();

            if (lowercaseName.endsWith('.pdf')) {
                objectContainer.classList.remove('d-none');
                objectContainer.classList.add('d-flex');
                objectEl.data = inlineUrl;
                fallbackLink.href = inlineUrl;
                if (openNewTabBtn) {
                    openNewTabBtn.href = inlineUrl;
                    openNewTabBtn.classList.remove('d-none');
                }
            } else if (lowercaseName.endsWith('.png') || lowercaseName.endsWith('.jpg') || lowercaseName.endsWith('.jpeg') || lowercaseName.endsWith('.gif')) {
                imageContainer.classList.remove('d-none');
                img.src = inlineUrl;
            } else if (lowercaseName.endsWith('.docx') || lowercaseName.endsWith('.doc')) {
                docxContainer.classList.remove('d-none');
                const spinnerDiv = document.createElement('div');
                spinnerDiv.className = 'p-4 text-center text-muted';
                spinnerDiv.textContent = @json(__('messages.Parsing document...') ?? 'Parsing document...');
                docxContainer.appendChild(spinnerDiv);

                fetch(inlineUrl)
                    .then(res => res.blob())
                    .then(blob => {
                        docxContainer.replaceChildren();
                        docx.renderAsync(blob, docxContainer);
                    })
                    .catch(err => {
                        docxContainer.replaceChildren();
                        const errDiv = document.createElement('div');
                        errDiv.className = 'p-4 text-danger';
                        errDiv.textContent = `Error loading document: ${err}`;
                        docxContainer.appendChild(errDiv);
                    });
            } else if (lowercaseName.endsWith('.xlsx') || lowercaseName.endsWith('.xls')) {
                excelContainer.classList.remove('d-none');
                const spinnerDiv = document.createElement('div');
                spinnerDiv.className = 'p-4 text-center text-muted';
                spinnerDiv.textContent = @json(__('messages.Loading workbook sheets...') ?? 'Loading workbook sheets...');
                excelContainer.appendChild(spinnerDiv);

                fetch(inlineUrl)
                    .then(res => res.arrayBuffer())
                    .then(ab => {
                        const workbook = XLSX.read(new Uint8Array(ab), { type: 'array' });
                        excelContainer.replaceChildren();

                        const tabsContainer = document.createElement('ul');
                        tabsContainer.className = 'nav nav-tabs px-3 bg-light border-bottom';
                        tabsContainer.setAttribute('role', 'tablist');

                        const contentContainer = document.createElement('div');
                        contentContainer.className = 'tab-content p-3';

                        workbook.SheetNames.forEach((sheetName, index) => {
                            const id = `sheet-${index}`;
                            const activeClass = index === 0 ? 'active' : '';

                            const li = document.createElement('li');
                            li.className = 'nav-item';
                            const tabBtn = document.createElement('button');
                            tabBtn.className = `nav-link ${activeClass} fw-bold py-2 border-0`;
                            tabBtn.setAttribute('data-bs-toggle', 'tab');
                            tabBtn.setAttribute('data-bs-target', `#${id}`);
                            tabBtn.type = 'button';
                            tabBtn.textContent = sheetName;
                            li.appendChild(tabBtn);
                            tabsContainer.appendChild(li);

                            const pane = document.createElement('div');
                            pane.className = `tab-pane fade show ${activeClass}`;
                            pane.id = id;
                            pane.style.overflowX = 'auto';

                            const wrapper = document.createElement('div');
                            wrapper.className = 'table-responsive bg-white rounded-3 shadow-sm border border-light p-2';
                            const sheetHtml = XLSX.utils.sheet_to_html(workbook.Sheets[sheetName], { editable: false });
                            const parsedDoc = new DOMParser().parseFromString(sheetHtml, 'text/html');
                            const parsedTable = parsedDoc.querySelector('table');
                            if (parsedTable) {
                                parsedTable.className = 'table table-bordered table-striped table-hover mb-0';
                                wrapper.appendChild(parsedTable);
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
                        errDiv.textContent = `Error parsing Excel workbook: ${err}`;
                        excelContainer.appendChild(errDiv);
                    });
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize path from URL ?path= query parameter if present
            const urlParams = new URLSearchParams(window.location.search);
            const initPath = urlParams.get('path');
            if (initPath && getNestedNodeByPath(initPath)) {
                currentPath = initPath;
            }

            // Collapse sidebar by default on small screens
            if (window.innerWidth < 992) {
                toggleSidebar(false);
            }

            const btnSidebar = document.getElementById('btn-toggle-sidebar');
            if (btnSidebar) {
                btnSidebar.addEventListener('click', () => toggleSidebar());
            }

            const btnGrid = document.getElementById('btn-layout-grid');
            const btnList = document.getElementById('btn-layout-list');
            const filterInput = document.getElementById('explorer-filter');
            const sortSelect = document.getElementById('explorer-sort');

            function updateLayoutButtons() {
                if (!btnGrid || !btnList) return;
                if (currentLayout === 'grid') {
                    btnGrid.classList.add('active', 'btn-secondary', 'text-white');
                    btnGrid.classList.remove('btn-light');
                    btnList.classList.remove('active', 'btn-secondary', 'text-white');
                    btnList.classList.add('btn-light');
                } else {
                    btnList.classList.add('active', 'btn-secondary', 'text-white');
                    btnList.classList.remove('btn-light');
                    btnGrid.classList.remove('active', 'btn-secondary', 'text-white');
                    btnGrid.classList.add('btn-light');
                }
            }

            if (btnGrid && btnList) {
                btnGrid.addEventListener('click', function() {
                    currentLayout = 'grid';
                    localStorage.setItem('archive_layout_pref', 'grid');
                    updateLayoutButtons();
                    renderExplorer();
                });
                btnList.addEventListener('click', function() {
                    currentLayout = 'list';
                    localStorage.setItem('archive_layout_pref', 'list');
                    updateLayoutButtons();
                    renderExplorer();
                });
                updateLayoutButtons();
            }

            if (filterInput) {
                filterInput.addEventListener('input', () => renderExplorer());
            }

            if (sortSelect) {
                sortSelect.addEventListener('change', function() {
                    currentSort = this.value;
                    renderExplorer();
                });
            }

            window.addEventListener('popstate', function(e) {
                const targetPath = (e.state && e.state.path) ? e.state.path : (new URLSearchParams(window.location.search).get('path') || 'Archives');
                navigateToPath(targetPath, false);
            });

            renderExplorer();

            const modalEl = document.getElementById('documentPreviewModal');
            if (modalEl) {
                modalEl.addEventListener('hidden.bs.modal', function () {
                    const objectEl = document.getElementById('documentPreviewObject');
                    if (objectEl) objectEl.data = '';
                    const img = document.getElementById('documentPreviewImg');
                    if (img) img.src = '';
                    document.getElementById('docxPreviewContainer').replaceChildren();
                    document.getElementById('excelPreviewContainer').replaceChildren();
                });
            }
        });
    </script>

    <!-- Document Preview Modal -->
    <div class="modal fade" id="documentPreviewModal" tabindex="-1" aria-labelledby="documentPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen-lg-down modal-dialog-centered">
            <div class="modal-content rounded-4 overflow-hidden shadow-lg border-0 h-100">
                <div class="modal-header bg-light border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-dark text-truncate" id="documentPreviewModalLabel">Document Preview</h5>
                    <div class="d-flex align-items-center gap-2 ms-auto">
                        <a id="pdfOpenNewTabBtn" href="#" target="_blank" class="btn btn-sm btn-outline-primary d-none me-2">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Fullscreen
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-0 d-flex flex-column" style="height: 80vh; background: #e9ecef;">
                    <div id="documentPreviewObjectContainer" class="w-100 h-100 d-none flex-column flex-grow-1" style="height: 100%;">
                        <object id="documentPreviewObject" data="" type="application/pdf" class="w-100 h-100 border-0 flex-grow-1 d-block" style="width: 100%; height: 100%; min-height: 100%;">
                            <div class="p-5 text-center text-muted">
                                <p>Your browser does not support inline PDFs.</p>
                                <a id="documentPreviewFallbackLink" href="#" target="_blank" class="btn btn-primary">Download PDF</a>
                            </div>
                        </object>
                    </div>
                    <div id="imagePreviewContainer" class="w-100 h-100 overflow-auto bg-white p-3 text-center d-none">
                        <img id="documentPreviewImg" src="" alt="Image preview" class="img-fluid rounded shadow-sm border border-light" style="max-height: 100%;">
                    </div>
                    <div id="docxPreviewContainer" class="w-100 h-100 overflow-auto bg-white p-4 d-none"></div>
                    <div id="excelPreviewContainer" class="w-100 h-100 overflow-auto bg-white d-none"></div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
