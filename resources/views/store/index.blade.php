<x-layout>
    <style>
        #inventoryTable thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #f8fafc !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        }
        .sortable-header {
            cursor: pointer;
            user-select: none;
            transition: color 0.15s ease;
        }
        .sortable-header:hover {
            color: #2563eb !important;
        }
        .inventory-row {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent !important;
        }
        .inventory-row:hover {
            background-color: rgba(59, 130, 246, 0.03) !important;
            transform: scale(1.001) translateX(2px);
            border-left: 4px solid #3b82f6 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03) !important;
        }
        .action-btn-group .btn {
            transition: all 0.2s ease-in-out;
            opacity: 0.85;
        }
        .action-btn-group .btn:hover {
            opacity: 1;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08) !important;
        }
        .inventory-row .dropdown-menu {
            background-color: #ffffff !important;
            background: #ffffff !important;
            border: 1px solid rgba(0, 0, 0, 0.12) !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
            z-index: 1050 !important;
            opacity: 1 !important;
        }
        .inventory-row .dropdown-item {
            transition: background-color 0.15s ease;
        }
        .inventory-row .dropdown-item:hover {
            background-color: rgba(59, 130, 246, 0.08) !important;
        }
    </style>
    <div class="container-fluid py-4">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h1 class="h2 text-gradient font-bold mb-0">
                    {{ __('messages.store_lit_dashboard') }}
                </h1>
                <p class="text-secondary mb-0">
                    {{ __('messages.store_lit_desc') }}
                </p>
            </div>
            <div>
                <button type="button" class="btn btn-warning rounded-pill px-4 shadow-sm fw-bold text-dark me-2" data-bs-toggle="modal" data-bs-target="#quickBatchMovementModal" @if(isset($activeStocktaking) && $activeStocktaking) disabled @endif>
                    <i class="bi bi-arrow-left-right me-1"></i>
                    {{ __('messages.quick_stock_movement') }}
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createItemModal">
                    <i class="bi bi-plus-lg me-1"></i>
                    {{ __('messages.add_new_item') }}
                </button>
            </div>
        </div>

        {{-- Active Stocktaking Lock Banner --}}
        @if (isset($activeStocktaking) && $activeStocktaking)
            <div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2 p-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-lock-fill text-warning-emphasis fs-4 me-1"></i>
                    <div>
                        <div class="fw-bold text-dark">{{ __('messages.store_currently_locked_title') }}</div>
                        <div class="small text-secondary">
                            {{ __('messages.stocktaking_session_in_progress_msg', ['id' => $activeStocktaking->id, 'user' => $activeStocktaking->user->name ?? 'System']) }}
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('store.stocktaking.count', $activeStocktaking->id) }}" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold text-dark shadow-sm">
                        <i class="bi bi-pencil-square me-1"></i>{{ __('messages.continue_counting') }}
                    </a>
                    <a href="{{ route('store.stocktaking.show', $activeStocktaking->id) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3 shadow-sm">
                        <i class="bi bi-eye me-1"></i>{{ __('messages.view_session') }}
                    </a>
                </div>
            </div>
        @endif

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Search & Category Filter --}}
        <div class="row g-4 mb-4">
            <div class="col-12 col-xl-6">
                <div class="card p-3 shadow-sm border-0 h-100 justify-content-center">
                    <form action="{{ route('store.index') }}" method="GET" class="row g-2 align-items-center">
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        @if(request('low_stock'))
                            <input type="hidden" name="low_stock" value="1">
                        @endif
                        <div class="col-12 col-md-9 position-relative">
                            <input type="search" name="search" value="{{ request('search') }}" class="form-control rounded-pill bg-transparent border-secondary-subtle px-4" placeholder="{{ __('messages.Search') }}...">
                        </div>
                        <div class="col-12 col-md-3">
                            <button type="submit" class="btn btn-secondary rounded-pill w-100 shadow-sm">
                                <i class="bi bi-search me-1"></i>
                                {{ __('messages.Search') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-12 col-md-4 col-xl-2">
                <div class="card text-center p-3 border-0 bg-success-subtle text-success h-100">
                    <div class="fs-4 mb-1"><i class="bi bi-box-seam"></i></div>
                    <div class="fw-semibold">{{ __('messages.total_store_stock') }}</div>
                    <div class="h3 mb-0 mt-2 font-bold">{{ $items->sum('store_quantity') }}</div>
                </div>
            </div>

            <div class="col-12 col-md-4 col-xl-2">
                <div class="card text-center p-3 border-0 bg-info-subtle text-info h-100">
                    <div class="fs-4 mb-1"><i class="bi bi-book"></i></div>
                    <div class="fw-semibold">{{ __('messages.total_lit_stock') }}</div>
                    <div class="h3 mb-0 mt-2 font-bold">{{ $items->sum('lit_quantity') }}</div>
                </div>
            </div>

            <div class="col-12 col-md-4 col-xl-2">
                <a href="{{ route('store.index', array_filter(array_merge(request()->query(), ['low_stock' => request('low_stock') ? null : 1]))) }}" class="text-decoration-none">
                    <div class="card text-center p-3 border-0 {{ request('low_stock') ? 'bg-warning text-dark shadow' : 'bg-warning-subtle text-warning-emphasis' }} h-100 position-relative" style="cursor: pointer;">
                        <div class="fs-4 mb-1"><i class="bi bi-exclamation-triangle-fill"></i></div>
                        <div class="fw-semibold">{{ __('messages.low_stock_filter') }}</div>
                        <div class="h3 mb-0 mt-2 font-bold">{{ $lowStockCount ?? 0 }}</div>
                        @if(request('low_stock'))
                            <span class="position-absolute top-0 end-0 m-2 badge rounded-pill bg-danger" title="Clear filter">
                                <i class="bi bi-x-lg"></i>
                            </span>
                        @endif
                    </div>
                </a>
            </div>
        </div>

        {{-- Category Tabs --}}
        <div class="mb-4">
            <ul class="nav nav-pills flex-nowrap overflow-x-auto p-1 rounded-3 bg-light" id="categoryTabList" style="border: 1px solid var(--glass-border); -webkit-overflow-scrolling: touch; scrollbar-width: none; gap: 4px;">
                <li class="nav-item flex-shrink-0" role="presentation">
                    <a href="{{ route('store.index', ['search' => request('search')]) }}" class="nav-link py-2 rounded-2 fw-semibold {{ !request('category') ? 'active bg-primary text-white' : 'text-secondary' }}" style="font-size: 0.9rem;">
                        {{ __('messages.all') }}
                    </a>
                </li>
                @foreach(\App\Models\InventoryItem::CATEGORIES as $cat)
                    <li class="nav-item flex-shrink-0" role="presentation">
                        <a href="{{ route('store.index', ['category' => $cat, 'search' => request('search')]) }}" class="nav-link py-2 rounded-2 fw-semibold {{ request('category') === $cat ? 'active bg-primary text-white' : 'text-secondary' }}" style="font-size: 0.9rem;">
                            {{ __('messages.cat_' . Str::snake(str_replace(' ', '_', $cat))) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Bulk Actions Toolbar --}}
        <div id="bulkActionsToolbar" class="d-none mb-4 p-3 bg-light rounded-4 border d-flex align-items-center justify-content-between flex-wrap gap-2 animate__animated animate__fadeIn">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-semibold text-secondary">
                    <i class="bi bi-check2-square me-1 text-primary"></i>
                    <span id="selectedCount">0</span> {{ __('messages.selected_items') }}
                </span>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" id="btnBulkQuickEdit">
                    <i class="bi bi-pencil-square me-1"></i>{{ __('messages.quick_edit_selected') ?? 'Quick Edit Selected' }}
                </button>
                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#bulkReceiveModal">
                    <i class="bi bi-plus-circle me-1"></i>{{ __('messages.bulk_receive') }}
                </button>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#bulkTransferModal">
                    <i class="bi bi-arrow-right-circle me-1"></i>{{ __('messages.bulk_transfer') }}
                </button>
                <button type="button" class="btn btn-sm btn-warning rounded-pill px-3 text-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#bulkReturnModal">
                    <i class="bi bi-arrow-left-circle me-1"></i>{{ __('messages.bulk_return') }}
                </button>
                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm" id="btnBulkDelete">
                    <i class="bi bi-trash me-1"></i>{{ __('messages.bulk_delete') }}
                </button>
            </div>
        </div>

        {{-- Floating Bulk Edit Save Bar --}}
        <div id="bulkEditBar" class="position-fixed bottom-0 start-50 translate-middle-x mb-4 p-3 bg-dark text-white rounded-pill shadow-lg d-none" style="z-index: 1050; border: 1px solid rgba(255,255,255,0.2);">
            <div class="d-flex align-items-center gap-3 px-2">
                <span class="fw-semibold"><i class="bi bi-pencil-fill text-warning me-2"></i>{{ __('messages.editing') ?? 'Editing' }} <span id="bulkEditCount" class="badge bg-warning text-dark me-1">0</span> {{ __('messages.selected_items') }}</span>
                <button type="button" class="btn btn-sm btn-success rounded-pill px-4 shadow-sm" id="btnSaveBulkEdit">
                    <i class="bi bi-check-circle me-1"></i>{{ __('messages.save_all_selected') ?? 'Save All Selected' }}
                </button>
                <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3" id="btnCancelBulkEdit">
                    <i class="bi bi-x-circle me-1"></i>{{ __('messages.Cancel') ?? 'Cancel' }}
                </button>
            </div>
        </div>

        {{-- Inventory Items Table --}}
        <div class="card border-0 shadow-sm p-4">
            <div class="table-responsive">
                @php 
                    $canEditPrice = auth()->check() && (auth()->user()->hasRole('super admin') || auth()->user()->hasRole('Lit User') || auth()->user()->hasRole('Committees') || auth()->user()->can('view lit inventory')); 
                    
                    $isRtl = app()->getLocale() === 'ar';
                    $transferIcon = $isRtl ? 'bi-arrow-left-circle-fill' : 'bi-arrow-right-circle-fill';
                    $returnIcon = $isRtl ? 'bi-arrow-right-circle-fill' : 'bi-arrow-left-circle-fill';
                    $transferArrow = $isRtl ? '←' : '→';
                    $returnArrow = $isRtl ? '→' : '←';

                    $currentSort = request('sort', 'name');
                    $currentDir = strtolower(request('direction', 'asc'));
                    
                    $sortUrl = function($col) use ($currentSort, $currentDir) {
                        $nextDir = ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc';
                        return route('store.index', array_merge(request()->query(), ['sort' => $col, 'direction' => $nextDir]));
                    };
                @endphp
                <table class="table align-middle table-hover mb-0" id="inventoryTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th class="sortable-header">
                                <a href="{{ $sortUrl('name') }}" class="text-decoration-none text-dark d-inline-flex align-items-center fw-bold">
                                    {{ __('messages.item_name') }}
                                    @if($currentSort === 'name')
                                        <i class="bi bi-sort-alpha-{{ $currentDir === 'asc' ? 'down' : 'down-alt' }} text-primary ms-1"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted opacity-50 ms-1 small"></i>
                                    @endif
                                </a>
                            </th>
                            <th>{{ __('messages.Category') }}</th>
                            <th>{{ __('messages.Description') }}</th>
                            <th class="text-end sortable-header" style="min-width: 120px;">
                                <a href="{{ $sortUrl('selling_price') }}" class="text-decoration-none text-dark d-inline-flex align-items-center justify-content-end w-100 fw-bold">
                                    {{ __('messages.selling_price') }}
                                    @if($currentSort === 'selling_price')
                                        <i class="bi bi-sort-numeric-{{ $currentDir === 'asc' ? 'down' : 'down-alt' }} text-primary ms-1"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted opacity-50 ms-1 small"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center" style="min-width: 95px;">+ {{ __('messages.received_quantity') ?? 'Rec. Qty' }}</th>
                            <th class="text-center" style="min-width: 95px;">{{ $transferArrow }} {{ __('messages.transfer_to_lit') ?? 'Trans. Qty' }}</th>
                            <th class="text-center sortable-header">
                                <a href="{{ $sortUrl('store_quantity') }}" class="text-decoration-none text-dark d-inline-flex align-items-center justify-content-center fw-bold">
                                    {{ __('messages.store_qty') }}
                                    @if($currentSort === 'store_quantity')
                                        <i class="bi bi-sort-numeric-{{ $currentDir === 'asc' ? 'down' : 'down-alt' }} text-primary ms-1"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted opacity-50 ms-1 small"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center sortable-header">
                                <a href="{{ $sortUrl('lit_quantity') }}" class="text-decoration-none text-dark d-inline-flex align-items-center justify-content-center fw-bold">
                                    {{ __('messages.lit_qty') }}
                                    @if($currentSort === 'lit_quantity')
                                        <i class="bi bi-sort-numeric-{{ $currentDir === 'asc' ? 'down' : 'down-alt' }} text-primary ms-1"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted opacity-50 ms-1 small"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center" style="min-width: 140px;">{{ __('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr class="inventory-row" data-row-id="{{ $item->id }}">
                                <td>
                                    <input type="checkbox" class="form-check-input item-checkbox" 
                                           data-id="{{ $item->id }}" 
                                           data-name="{{ $item->store_display_name }}"
                                           data-store-qty="{{ $item->store_quantity }}"
                                           data-lit-qty="{{ $item->lit_quantity }}">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->store_display_name }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
                                        {{ __('messages.cat_' . Str::snake(str_replace(' ', '_', $item->category))) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-secondary small">{{ Str::limit($item->description, 60) ?: '-' }}</span>
                                </td>
                                <td class="text-end">
                                    {{-- Read mode price --}}
                                    <div class="read-mode-field">
                                        <span class="fw-bold text-dark me-1">{{ __('messages.EGP') }} <span class="price-val">{{ number_format($item->selling_price, 2) }}</span></span>
                                    </div>
                                    {{-- Edit mode price --}}
                                    <div class="edit-mode-field d-none">
                                        @if ($canEditPrice)
                                            <div class="input-group input-group-sm ms-auto" style="max-width: 110px;">
                                                <span class="input-group-text px-1 bg-light border-secondary-subtle text-muted small">EGP</span>
                                                <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end fw-bold inline-price-input" 
                                                       data-id="{{ $item->id }}" data-original="{{ number_format($item->selling_price, 2, '.', '') }}" value="{{ number_format($item->selling_price, 2, '.', '') }}">
                                            </div>
                                        @else
                                            <span class="fw-bold text-dark me-1">{{ __('messages.EGP') }} {{ number_format($item->selling_price, 2) }}</span>
                                            <input type="hidden" class="inline-price-input" data-id="{{ $item->id }}" data-original="{{ number_format($item->selling_price, 2, '.', '') }}" value="{{ number_format($item->selling_price, 2, '.', '') }}">
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="read-mode-field">
                                        <span class="text-muted small">-</span>
                                    </div>
                                    <div class="edit-mode-field d-none">
                                        <div class="input-group input-group-sm mx-auto shadow-sm" style="max-width: 105px;" dir="ltr">
                                            <button class="btn btn-outline-success btn-stepper-minus px-2 py-0 border-success-subtle" type="button" data-target="inline-receive-input" data-id="{{ $item->id }}">-</button>
                                            <input type="number" min="0" class="form-control form-control-sm text-center px-1 inline-receive-input border-success-subtle fw-semibold" 
                                                   data-id="{{ $item->id }}" placeholder="0" style="max-width: 50px;">
                                            <button class="btn btn-outline-success btn-stepper-plus px-2 py-0 border-success-subtle" type="button" data-target="inline-receive-input" data-id="{{ $item->id }}">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center position-relative">
                                    <div class="read-mode-field">
                                        <span class="text-muted small">-</span>
                                    </div>
                                    <div class="edit-mode-field d-none">
                                        <div class="input-group input-group-sm mx-auto shadow-sm" style="max-width: 105px;" dir="ltr">
                                            <button class="btn btn-outline-primary btn-stepper-minus px-2 py-0 border-primary-subtle" type="button" data-target="inline-transfer-input" data-id="{{ $item->id }}">-</button>
                                            <input type="number" min="0" class="form-control form-control-sm text-center px-1 inline-transfer-input border-primary-subtle fw-semibold" 
                                                   data-id="{{ $item->id }}" data-store-qty="{{ $item->store_quantity }}" placeholder="0" style="max-width: 50px;">
                                            <button class="btn btn-outline-primary btn-stepper-plus px-2 py-0 border-primary-subtle" type="button" data-target="inline-transfer-input" data-id="{{ $item->id }}">+</button>
                                        </div>
                                        <div class="transfer-warning-msg text-danger fw-semibold mt-1 d-none" style="font-size: 0.7rem;">Exceeds stock</div>
                                    </div>
                                </td>
                                <td class="text-center store-qty-container" id="store-qty-container-{{ $item->id }}">
                                    @if ($item->store_quantity == 0)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-bold">
                                            <i class="bi bi-exclamation-octagon-fill me-1"></i>0
                                        </span>
                                    @elseif ($item->store_quantity <= 5)
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fw-bold">
                                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $item->store_quantity }}
                                        </span>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                                            {{ $item->store_quantity }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center lit-qty-container" id="lit-qty-container-{{ $item->id }}">
                                    @if ($item->lit_quantity == 0)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-bold">
                                            0
                                        </span>
                                    @elseif ($item->lit_quantity <= 5)
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fw-bold">
                                            {{ $item->lit_quantity }}
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 rounded-pill fw-bold">
                                            {{ $item->lit_quantity }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        {{-- Read mode actions --}}
                                        <div class="read-mode-actions d-flex align-items-center gap-1">
                                             <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-2 py-0 fw-bold btn-row-quick-receive" 
                                                     data-id="{{ $item->id }}" data-name="{{ $item->store_display_name }}"
                                                     data-bs-toggle="modal" data-bs-target="#receiveModal"
                                                     title="{{ __('messages.receive') }}" style="font-size: 0.75rem;">
                                                 {{ __('messages.btn_rec') }}
                                             </button>
                                             <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 fw-bold btn-row-quick-transfer" 
                                                     data-id="{{ $item->id }}" data-name="{{ $item->store_display_name }}" data-store-qty="{{ $item->store_quantity }}"
                                                     data-bs-toggle="modal" data-bs-target="#transferModal"
                                                     title="{{ __('messages.transfer_to_lit') }}" style="font-size: 0.75rem;">
                                                 {{ __('messages.btn_to_lit') }}
                                             </button>
                                             <button type="button" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-2 py-0 fw-bold btn-row-quick-return" 
                                                     data-id="{{ $item->id }}" data-name="{{ $item->store_display_name }}" data-lit-qty="{{ $item->lit_quantity }}"
                                                     data-bs-toggle="modal" data-bs-target="#returnModal"
                                                     title="{{ __('messages.return_from_lit') }}" style="font-size: 0.75rem;">
                                                 {{ __('messages.btn_from_lit') }}
                                             </button>
                                             <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle btn-quick-edit shadow-sm ms-1" 
                                                     data-id="{{ $item->id }}" title="{{ __('messages.Edit') }}" 
                                                     style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                 <i class="bi bi-pencil-fill" style="font-size: 0.75rem;"></i>
                                             </button>
                                        </div>

                                        {{-- Edit mode actions --}}
                                        <div class="edit-mode-actions d-none align-items-center gap-1">
                                            <button type="button" class="btn btn-sm btn-success rounded-circle shadow-sm inline-save-btn" 
                                                    data-id="{{ $item->id }}" title="{{ __('messages.Save') ?? 'Save' }}" 
                                                    style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-check-lg fs-6"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle inline-cancel-btn" 
                                                    data-id="{{ $item->id }}" title="{{ __('messages.Cancel') ?? 'Cancel' }}" 
                                                    style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>

                                        {{-- Actions Dropdown --}}
                                        <div class="dropdown ms-1">
                                            <button class="btn btn-link text-secondary p-0 border-0 dropdown-toggle-nocaret" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical fs-5"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow rounded-3">
                                                <li>
                                                    <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-success py-2" 
                                                            data-bs-toggle="modal" data-bs-target="#receiveModal"
                                                            data-id="{{ $item->id }}" data-name="{{ $item->store_display_name }}">
                                                        <i class="bi bi-plus-circle-fill"></i>{{ __('messages.receive') }}
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-primary py-2" 
                                                            data-bs-toggle="modal" data-bs-target="#transferModal"
                                                            data-id="{{ $item->id }}" data-name="{{ $item->store_display_name }}" data-store-qty="{{ $item->store_quantity }}">
                                                        <i class="bi {{ $transferIcon }}"></i>{{ __('messages.transfer_to_lit') }}
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-warning-emphasis py-2" 
                                                            data-bs-toggle="modal" data-bs-target="#returnModal"
                                                            data-id="{{ $item->id }}" data-name="{{ $item->store_display_name }}" data-lit-qty="{{ $item->lit_quantity }}">
                                                        <i class="bi {{ $returnIcon }}"></i>{{ __('messages.return_from_lit') }}
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-dark py-2" 
                                                            data-bs-toggle="modal" data-bs-target="#editModal"
                                                            data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-name-en="{{ $item->name_en }}"
                                                            data-category="{{ $item->category }}" data-price="{{ $item->selling_price }}"
                                                            data-description="{{ $item->description }}">
                                                        <i class="bi bi-pencil-fill text-secondary"></i>{{ __('messages.Edit') ?? 'Edit' }}
                                                    </button>
                                                </li>
                                                <li>
                                                    <form action="{{ route('store.destroy', $item->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete_item') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger py-2">
                                                            <i class="bi bi-trash-fill"></i>{{ __('messages.Delete') ?? 'Delete' }}
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-secondary py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    {{ __('messages.no_items_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination Links --}}
            <div class="mt-4 d-flex justify-content-center">
                {{ $items->links() }}
            </div>
        </div>
    </div>

    {{-- Reusable Modals --}}
    {{-- Quick Stock Movement Modal --}}
    <div class="modal fade" id="quickBatchMovementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 bg-warning-subtle text-warning-emphasis p-4">
                    <div>
                        <h4 class="modal-title fw-bold mb-1">
                            <i class="bi bi-arrow-left-right text-primary me-2"></i>{{ __('messages.quick_stock_movement') }}
                        </h4>
                        <p class="text-secondary small mb-0">{{ __('messages.quick_stock_movement_desc') }}</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light-subtle">
                    {{-- Search & Controls Section --}}
                    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold text-dark small mb-1">
                                    <i class="bi bi-sliders me-1 text-primary"></i>{{ __('messages.select_default_movement') }}
                                </label>
                                <select id="globalMovementType" class="form-select rounded-pill fw-semibold border-secondary-subtle">
                                    <option value="receive" selected>➕ {{ __('messages.receive') }}</option>
                                    <option value="transfer_to_lit">{{ $transferArrow }} {{ __('messages.transfer_to_lit') }}</option>
                                    <option value="return_from_lit">{{ $returnArrow }} {{ __('messages.return_from_lit') }}</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-8 position-relative">
                                <label class="form-label fw-bold text-dark small mb-1">
                                    <i class="bi bi-search me-1 text-primary"></i>{{ __('messages.Search') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-start-pill bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                    <input type="text" id="quickBatchSearch" class="form-control rounded-end-pill border-start-0 ps-0" placeholder="{{ __('messages.search_inventory_placeholder') }}" autocomplete="off">
                                </div>
                                <div id="quickBatchSearchResults" class="dropdown-menu w-100 shadow-lg border-0 rounded-4 mt-1 p-2 overflow-y-auto" style="max-height: 280px; display: none; z-index: 1060;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Draft Batch Items Table --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                        <div class="card-header bg-white border-0 pt-3 pb-0 px-4 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-list-check me-2 text-primary"></i>{{ __('messages.batch_movement_items') }}
                                <span class="badge bg-primary rounded-pill ms-2" id="batchItemBadge">0</span>
                            </h6>
                            <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-pill" id="btnClearBatch" style="display: none;">
                                <i class="bi bi-trash me-1"></i>{{ __('messages.clear_all') }}
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0" id="quickBatchTable">
                                    <thead class="table-light">
                                        <tr class="small text-uppercase text-secondary">
                                            <th class="ps-4">{{ __('messages.item_name') }}</th>
                                            <th class="text-center">{{ __('messages.stock_levels') }}</th>
                                            <th style="min-width: 170px;">{{ __('messages.default_type') }}</th>
                                            <th class="text-center" style="min-width: 240px;">{{ __('messages.Quantity') }}</th>
                                            <th class="text-end pe-4" style="min-width: 120px;">{{ __('messages.estimated_valuation') }}</th>
                                            <th class="text-center" style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="quickBatchTbody">
                                        <tr id="emptyBatchRow">
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="bi bi-basket3 fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                                <span>{{ __('messages.no_items_in_batch') }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Batch Summary & Notes --}}
                    <div class="row g-3">
                        <div class="col-12 col-lg-7">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                                <label class="form-label fw-bold text-dark small mb-1">
                                    <i class="bi bi-journal-text me-1 text-primary"></i>{{ __('messages.batch_notes') }}
                                </label>
                                <input type="text" id="batchNotesInput" class="form-control rounded-3 border-secondary-subtle" placeholder="{{ __('messages.batch_notes_placeholder') }}">
                            </div>
                        </div>
                        <div class="col-12 col-lg-5">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-primary-subtle text-primary-emphasis h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-semibold small">{{ __('messages.total_batch_qty') }}:</span>
                                    <span class="fs-5 fw-bold text-primary" id="summaryTotalQty">0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold small">{{ __('messages.estimated_valuation') }}:</span>
                                    <span class="fs-5 fw-bold text-success me-1">{{ __('messages.EGP') }} <span id="summaryTotalValuation">0.00</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-white">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                    <button type="button" class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm" id="btnSubmitBatchMovement" disabled>
                        <i class="bi bi-check2-circle me-1"></i>{{ __('messages.submit_batch_movement') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Receive Modal --}}
    <div class="modal fade" id="receiveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 bg-success-subtle text-success">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>{{ __('messages.receive_stock_for', ['name' => '']) }}<span class="modal-title-name"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.received_quantity') }}</label>
                        <input type="number" name="quantity" min="1" required class="form-control rounded-3" placeholder="e.g. 50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.Notes') }}</label>
                        <textarea name="notes" class="form-control rounded-3" rows="3" placeholder="e.g. Received from printer / main supplier"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">{{ __('messages.confirm_receipt') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Transfer Modal --}}
    <div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 bg-primary-subtle text-primary">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-arrow-right-circle me-2"></i>{{ __('messages.transfer_to_lit_title', ['name' => '']) }}<span class="modal-title-name"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3 text-secondary">
                        {{ __('messages.current_store_balance') }} <span class="badge bg-secondary current-balance-badge"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.qty_to_transfer') }}</label>
                        <input type="number" name="quantity" min="1" required class="form-control rounded-3" placeholder="e.g. 10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.Notes') }}</label>
                        <textarea name="notes" class="form-control rounded-3" rows="3" placeholder="e.g. Sent to Literature committee"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('messages.confirm_transfer') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Return Modal --}}
    <div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 bg-warning-subtle text-warning-emphasis">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-arrow-left-circle me-2"></i>{{ __('messages.return_from_lit_title', ['name' => '']) }}<span class="modal-title-name"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3 text-secondary">
                        {{ __('messages.current_lit_balance') }} <span class="badge bg-secondary current-balance-badge"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.qty_to_return') }}</label>
                        <input type="number" name="quantity" min="1" required class="form-control rounded-3" placeholder="e.g. 5">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.Notes') }}</label>
                        <textarea name="notes" class="form-control rounded-3" rows="3" placeholder="e.g. Excess stock returned to main store"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 text-dark">{{ __('messages.confirm_return') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>{{ __('messages.edit_item_details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.item_name_ar') }}</label>
                        <input type="text" name="name" required class="form-control rounded-3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.item_name_en') }}</label>
                        <input type="text" name="name_en" class="form-control rounded-3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.Category') }}</label>
                        <select name="category" required class="form-select rounded-3">
                            @foreach(\App\Models\InventoryItem::CATEGORIES as $cat)
                                <option value="{{ $cat }}">
                                    {{ __('messages.cat_' . Str::snake(str_replace(' ', '_', $cat))) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.selling_price') }} ({{ __('messages.EGP') }})</label>
                        <input type="number" step="0.01" name="selling_price" required class="form-control rounded-3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.Description') }}</label>
                        <textarea name="description" class="form-control rounded-3" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('messages.save_changes') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Create Item Modal --}}
    <div class="modal fade" id="createItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('store.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 bg-primary-subtle text-primary">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-lg me-2"></i>{{ __('messages.create_new_inventory_item') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.item_name_ar') }}</label>
                        <input type="text" name="name" required class="form-control rounded-3" placeholder="e.g. كتاب أساسي">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.item_name_en') }}</label>
                        <input type="text" name="name_en" class="form-control rounded-3" placeholder="e.g. Basic Text Book">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.Category') }}</label>
                        <select name="category" required class="form-select rounded-3">
                            @foreach(\App\Models\InventoryItem::CATEGORIES as $cat)
                                <option value="{{ $cat }}">
                                    {{ __('messages.cat_' . Str::snake(str_replace(' ', '_', $cat))) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.selling_price') }} ({{ __('messages.EGP') }})</label>
                        <input type="number" step="0.01" name="selling_price" required class="form-control rounded-3" placeholder="e.g. 150.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.initial_store_qty') }}</label>
                        <input type="number" name="initial_store_quantity" min="0" value="0" class="form-control rounded-3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.Description') }}</label>
                        <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Details about this item..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('messages.create_item') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Bulk Receive Modal --}}
    <div class="modal fade" id="bulkReceiveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('store.bulk_receive') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 bg-success-subtle text-success">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>{{ __('messages.bulk_receive') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.item_name') }}</th>
                                    <th style="width: 150px;">{{ __('messages.qty') }}</th>
                                </tr>
                            </thead>
                            <tbody id="bulkReceiveList">
                                {{-- Loaded via JS --}}
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.Notes') }}</label>
                        <textarea name="notes" class="form-control rounded-3" rows="3" placeholder="e.g. Bulk stock received"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">{{ __('messages.confirm_receipt') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Bulk Transfer Modal --}}
    <div class="modal fade" id="bulkTransferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('store.bulk_transfer') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 bg-primary-subtle text-primary">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-arrow-right-circle me-2"></i>{{ __('messages.bulk_transfer') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.item_name') }}</th>
                                    <th>{{ __('messages.store_qty') }}</th>
                                    <th style="width: 150px;">{{ __('messages.qty_to_transfer') }}</th>
                                </tr>
                            </thead>
                            <tbody id="bulkTransferList">
                                {{-- Loaded via JS --}}
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.Notes') }}</label>
                        <textarea name="notes" class="form-control rounded-3" rows="3" placeholder="e.g. Bulk transfer to Lit"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('messages.confirm_transfer') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Bulk Return Modal --}}
    <div class="modal fade" id="bulkReturnModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('store.bulk_return') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 bg-warning-subtle text-warning-emphasis">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-arrow-left-circle me-2"></i>{{ __('messages.bulk_return') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.item_name') }}</th>
                                    <th>{{ __('messages.lit_qty') }}</th>
                                    <th style="width: 150px;">{{ __('messages.qty_to_return') }}</th>
                                </tr>
                            </thead>
                            <tbody id="bulkReturnList">
                                {{-- Loaded via JS --}}
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.Notes') }}</label>
                        <textarea name="notes" class="form-control rounded-3" rows="3" placeholder="e.g. Bulk return from Lit"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 text-dark">{{ __('messages.confirm_return') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Hidden form for Bulk Delete --}}
    <form id="bulkDeleteForm" action="{{ route('store.bulk_delete') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Reusable Receive Modal Setup
            const receiveModal = document.getElementById('receiveModal');
            if (receiveModal) {
                receiveModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    
                    const form = receiveModal.querySelector('form');
                    form.action = "{{ route('store.receive', ':id') }}".replace(':id', id);
                    
                    const titleName = receiveModal.querySelector('.modal-title-name');
                    if (titleName) titleName.textContent = name;
                });
            }

            // Reusable Transfer Modal Setup
            const transferModal = document.getElementById('transferModal');
            if (transferModal) {
                transferModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    const storeQty = button.getAttribute('data-store-qty');
                    
                    const form = transferModal.querySelector('form');
                    form.action = "{{ route('store.transfer', ':id') }}".replace(':id', id);
                    
                    const titleName = transferModal.querySelector('.modal-title-name');
                    if (titleName) titleName.textContent = name;
                    
                    const balanceSpan = transferModal.querySelector('.current-balance-badge');
                    if (balanceSpan) balanceSpan.textContent = storeQty;
                    
                    const qtyInput = transferModal.querySelector('input[name="quantity"]');
                    if (qtyInput) {
                        qtyInput.max = storeQty;
                    }
                });
            }

            // Reusable Return Modal Setup
            const returnModal = document.getElementById('returnModal');
            if (returnModal) {
                returnModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    const litQty = button.getAttribute('data-lit-qty');
                    
                    const form = returnModal.querySelector('form');
                    form.action = "{{ route('store.return', ':id') }}".replace(':id', id);
                    
                    const titleName = returnModal.querySelector('.modal-title-name');
                    if (titleName) titleName.textContent = name;
                    
                    const balanceSpan = returnModal.querySelector('.current-balance-badge');
                    if (balanceSpan) balanceSpan.textContent = litQty;
                    
                    const qtyInput = returnModal.querySelector('input[name="quantity"]');
                    if (qtyInput) {
                        qtyInput.max = litQty;
                    }
                });
            }

            // Reusable Edit Modal Setup
            const editModal = document.getElementById('editModal');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    const nameEn = button.getAttribute('data-name-en');
                    const category = button.getAttribute('data-category');
                    const price = button.getAttribute('data-price');
                    const description = button.getAttribute('data-description');
                    
                    const form = editModal.querySelector('form');
                    form.action = "{{ route('store.update', ':id') }}".replace(':id', id);
                    
                    editModal.querySelector('input[name="name"]').value = name;
                    editModal.querySelector('input[name="name_en"]').value = nameEn || '';
                    editModal.querySelector('select[name="category"]').value = category;
                    editModal.querySelector('input[name="selling_price"]').value = price;
                    editModal.querySelector('textarea[name="description"]').value = description || '';
                });
            }

            // Bulk Actions logic
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.item-checkbox');
            const bulkToolbar = document.getElementById('bulkActionsToolbar');
            const selectedCountSpan = document.getElementById('selectedCount');

            function getSelectedCheckboxes() {
                return Array.from(checkboxes).filter(cb => cb.checked);
            }

            function updateBulkToolbar() {
                const selected = getSelectedCheckboxes();
                if (selected.length > 0) {
                    bulkToolbar.classList.remove('d-none');
                    selectedCountSpan.textContent = selected.length;
                } else {
                    bulkToolbar.classList.add('d-none');
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => {
                        cb.checked = selectAll.checked;
                    });
                    updateBulkToolbar();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (!this.checked) {
                        selectAll.checked = false;
                    } else if (getSelectedCheckboxes().length === checkboxes.length) {
                        selectAll.checked = true;
                    }
                    updateBulkToolbar();
                });
            });

            // Populate Bulk Receive Modal
            const bulkReceiveModal = document.getElementById('bulkReceiveModal');
            if (bulkReceiveModal) {
                bulkReceiveModal.addEventListener('show.bs.modal', function () {
                    const selected = getSelectedCheckboxes();
                    const listContainer = document.getElementById('bulkReceiveList');
                    listContainer.innerHTML = '';
                    
                    selected.forEach(cb => {
                        const id = cb.getAttribute('data-id');
                        const name = cb.getAttribute('data-name');
                        
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><div class="fw-bold">${name}</div></td>
                            <td>
                                <input type="number" name="quantities[${id}]" min="0" class="form-control form-control-sm rounded-3" placeholder="0">
                            </td>
                        `;
                        listContainer.appendChild(tr);
                    });
                });
            }

            // Populate Bulk Transfer Modal
            const bulkTransferModal = document.getElementById('bulkTransferModal');
            if (bulkTransferModal) {
                bulkTransferModal.addEventListener('show.bs.modal', function () {
                    const selected = getSelectedCheckboxes();
                    const listContainer = document.getElementById('bulkTransferList');
                    listContainer.innerHTML = '';
                    
                    selected.forEach(cb => {
                        const id = cb.getAttribute('data-id');
                        const name = cb.getAttribute('data-name');
                        const storeQty = cb.getAttribute('data-store-qty');
                        
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><div class="fw-bold">${name}</div></td>
                            <td><span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill">${storeQty}</span></td>
                            <td>
                                <input type="number" name="quantities[${id}]" min="0" max="${storeQty}" class="form-control form-control-sm rounded-3" placeholder="0">
                            </td>
                        `;
                        listContainer.appendChild(tr);
                    });
                });
            }

            // Populate Bulk Return Modal
            const bulkReturnModal = document.getElementById('bulkReturnModal');
            if (bulkReturnModal) {
                bulkReturnModal.addEventListener('show.bs.modal', function () {
                    const selected = getSelectedCheckboxes();
                    const listContainer = document.getElementById('bulkReturnList');
                    listContainer.innerHTML = '';
                    
                    selected.forEach(cb => {
                        const id = cb.getAttribute('data-id');
                        const name = cb.getAttribute('data-name');
                        const litQty = cb.getAttribute('data-lit-qty');
                        
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><div class="fw-bold">${name}</div></td>
                            <td><span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill">${litQty}</span></td>
                            <td>
                                <input type="number" name="quantities[${id}]" min="0" max="${litQty}" class="form-control form-control-sm rounded-3" placeholder="0">
                            </td>
                        `;
                        listContainer.appendChild(tr);
                    });
                });
            }

            // Bulk Delete handler
            const btnBulkDelete = document.getElementById('btnBulkDelete');
            if (btnBulkDelete) {
                btnBulkDelete.addEventListener('click', function() {
                    const selected = getSelectedCheckboxes();
                    if (selected.length === 0) return;
                    
                    if (confirm("{{ __('messages.confirm_bulk_delete') }}")) {
                        const form = document.getElementById('bulkDeleteForm');
                        // Remove old inputs if any
                        form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                        
                        selected.forEach(cb => {
                            const id = cb.getAttribute('data-id');
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
                            input.value = id;
                            form.appendChild(input);
                        });
                        form.submit();
                    }
                });
            }

            // --- Inline Edit Toggle & Real-time Validation System ---

            function toggleRowEditMode(row, enableEdit) {
                const readFields = row.querySelectorAll('.read-mode-field, .read-mode-actions');
                const editFields = row.querySelectorAll('.edit-mode-field, .edit-mode-actions');

                if (enableEdit) {
                    row.classList.add('is-editing', 'table-warning');
                    readFields.forEach(el => el.classList.add('d-none'));
                    editFields.forEach(el => el.classList.remove('d-none'));
                    validateRowInputs(row);
                } else {
                    row.classList.remove('is-editing', 'table-warning');
                    readFields.forEach(el => el.classList.remove('d-none'));
                    editFields.forEach(el => el.classList.add('d-none'));
                    // Reset inputs to original state
                    const priceInput = row.querySelector('.inline-price-input');
                    const receiveInput = row.querySelector('.inline-receive-input');
                    const transferInput = row.querySelector('.inline-transfer-input');
                    if (priceInput && priceInput.hasAttribute('data-original')) {
                        priceInput.value = priceInput.getAttribute('data-original');
                    }
                    if (receiveInput) receiveInput.value = '';
                    if (transferInput) {
                        transferInput.value = '';
                        transferInput.classList.remove('is-invalid');
                    }
                    const warningMsg = row.querySelector('.transfer-warning-msg');
                    if (warningMsg) warningMsg.classList.add('d-none');

                    const saveBtn = row.querySelector('.inline-save-btn');
                    if (saveBtn) saveBtn.disabled = false;
                }
            }

            function validateRowInputs(row) {
                const transferInput = row.querySelector('.inline-transfer-input');
                const saveBtn = row.querySelector('.inline-save-btn');
                const warningMsg = row.querySelector('.transfer-warning-msg');
                if (!transferInput) return true;

                const storeQty = parseInt(transferInput.getAttribute('data-store-qty') || 0, 10);
                const transferVal = parseInt(transferInput.value || 0, 10);

                if (transferVal > storeQty) {
                    transferInput.classList.add('is-invalid');
                    if (warningMsg) warningMsg.classList.remove('d-none');
                    if (saveBtn) saveBtn.disabled = true;
                    return false;
                } else {
                    transferInput.classList.remove('is-invalid');
                    if (warningMsg) warningMsg.classList.add('d-none');
                    if (saveBtn) saveBtn.disabled = false;
                    return true;
                }
            }

            // Real-time input listeners for validation
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('inline-transfer-input')) {
                    const row = e.target.closest('tr');
                    if (row) validateRowInputs(row);
                    updateBulkEditSaveState();
                }
            });

            // Stepper buttons click handlers (+ / -)
            document.addEventListener('click', function(e) {
                const plusBtn = e.target.closest('.btn-stepper-plus');
                const minusBtn = e.target.closest('.btn-stepper-minus');

                if (plusBtn) {
                    const targetClass = plusBtn.getAttribute('data-target');
                    const row = plusBtn.closest('tr');
                    const input = row ? row.querySelector('.' + targetClass) : null;
                    if (input) {
                        input.value = (parseInt(input.value || 0, 10) + 1);
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }

                if (minusBtn) {
                    const targetClass = minusBtn.getAttribute('data-target');
                    const row = minusBtn.closest('tr');
                    const input = row ? row.querySelector('.' + targetClass) : null;
                    if (input) {
                        const current = parseInt(input.value || 0, 10);
                        if (current > 0) {
                            input.value = current - 1;
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    }
                }
            });

            // Keyboard navigation handler (Enter to Save, Esc to Cancel)
            document.addEventListener('keydown', function(e) {
                if (e.target.matches('.inline-price-input, .inline-receive-input, .inline-transfer-input')) {
                    const row = e.target.closest('tr');
                    if (!row) return;

                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const saveBtn = row.querySelector('.inline-save-btn');
                        if (saveBtn && !saveBtn.disabled) saveBtn.click();
                    } else if (e.key === 'Escape') {
                        e.preventDefault();
                        toggleRowEditMode(row, false);
                    }
                }
            });

            // Quick Edit pencil button click
            document.addEventListener('click', function(e) {
                const editBtn = e.target.closest('.btn-quick-edit');
                if (editBtn) {
                    const row = editBtn.closest('tr');
                    if (row) toggleRowEditMode(row, true);
                }

                const cancelBtn = e.target.closest('.inline-cancel-btn');
                if (cancelBtn) {
                    const row = cancelBtn.closest('tr');
                    if (row) toggleRowEditMode(row, false);
                }
            });

            // Single Row Inline Save Handler
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.inline-save-btn');
                if (!btn) return;

                const id = btn.getAttribute('data-id');
                const row = btn.closest('tr');
                if (!row) return;

                if (!validateRowInputs(row)) return;

                const priceInput = row.querySelector('.inline-price-input');
                const receiveInput = row.querySelector('.inline-receive-input');
                const transferInput = row.querySelector('.inline-transfer-input');

                const payload = {
                    selling_price: priceInput ? priceInput.value : null,
                    receive_qty: receiveInput ? receiveInput.value : 0,
                    transfer_qty: transferInput ? transferInput.value : 0,
                };

                const originalBtnHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

                fetch(`/store/${id}/inline-update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => response.json().then(data => ({ status: response.status, body: data })))
                .then(({ status, body }) => {
                    if (status >= 200 && status < 300 && body.success) {
                        updateRowDisplayData(row, body.item);
                        toggleRowEditMode(row, false);
                        showToast(body.message || "{{ __('messages.item_updated_success') }}", 'success');
                    } else {
                        showToast(body.message || 'An error occurred.', 'danger');
                    }
                })
                .catch(err => {
                    showToast('Failed to save changes.', 'danger');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnHtml;
                });
            });

            // Bulk Quick Edit button click
            const btnBulkQuickEdit = document.getElementById('btnBulkQuickEdit');
            const bulkEditBar = document.getElementById('bulkEditBar');
            const bulkEditCount = document.getElementById('bulkEditCount');

            if (btnBulkQuickEdit) {
                btnBulkQuickEdit.addEventListener('click', function() {
                    const selected = getSelectedCheckboxes();
                    if (selected.length === 0) return;

                    selected.forEach(cb => {
                        const row = cb.closest('tr');
                        if (row) toggleRowEditMode(row, true);
                    });

                    if (bulkEditBar) {
                        bulkEditCount.textContent = selected.length;
                        bulkEditBar.classList.remove('d-none');
                    }
                });
            }

            // Bulk Edit Cancel button click
            const btnCancelBulkEdit = document.getElementById('btnCancelBulkEdit');
            if (btnCancelBulkEdit) {
                btnCancelBulkEdit.addEventListener('click', function() {
                    document.querySelectorAll('.inventory-row.is-editing').forEach(row => {
                        toggleRowEditMode(row, false);
                    });
                    if (bulkEditBar) bulkEditBar.classList.add('d-none');
                });
            }

            // Bulk Save button click
            const btnSaveBulkEdit = document.getElementById('btnSaveBulkEdit');
            if (btnSaveBulkEdit) {
                btnSaveBulkEdit.addEventListener('click', function() {
                    const editingRows = document.querySelectorAll('.inventory-row.is-editing');
                    if (editingRows.length === 0) return;

                    let hasErrors = false;
                    const itemsPayload = [];

                    editingRows.forEach(row => {
                        if (!validateRowInputs(row)) hasErrors = true;
                        const id = row.getAttribute('data-row-id');
                        const priceInput = row.querySelector('.inline-price-input');
                        const receiveInput = row.querySelector('.inline-receive-input');
                        const transferInput = row.querySelector('.inline-transfer-input');

                        itemsPayload.push({
                            id: id,
                            selling_price: priceInput ? priceInput.value : null,
                            receive_qty: receiveInput ? receiveInput.value : 0,
                            transfer_qty: transferInput ? transferInput.value : 0,
                        });
                    });

                    if (hasErrors) {
                        showToast('Please resolve validation errors before saving.', 'danger');
                        return;
                    }

                    const originalHtml = btnSaveBulkEdit.innerHTML;
                    btnSaveBulkEdit.disabled = true;
                    btnSaveBulkEdit.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving...';

                    fetch('/store/bulk-inline-update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ items: itemsPayload })
                    })
                    .then(response => response.json().then(data => ({ status: response.status, body: data })))
                    .then(({ status, body }) => {
                        if (status >= 200 && status < 300 && body.success) {
                            (body.updated_items || []).forEach(itemData => {
                                const row = document.querySelector(`.inventory-row[data-row-id="${itemData.id}"]`);
                                if (row) {
                                    updateRowDisplayData(row, itemData);
                                    toggleRowEditMode(row, false);
                                }
                            });
                            if (bulkEditBar) bulkEditBar.classList.add('d-none');
                            showToast(body.message || 'Bulk update completed successfully', 'success');
                        } else {
                            showToast(body.message || 'An error occurred during bulk update.', 'danger');
                        }
                    })
                    .catch(err => {
                        showToast('Failed to save bulk changes.', 'danger');
                    })
                    .finally(() => {
                        btnSaveBulkEdit.disabled = false;
                        btnSaveBulkEdit.innerHTML = originalHtml;
                    });
                });
            }

            function updateRowDisplayData(row, itemData) {
                // Update price read display
                const priceValEl = row.querySelector('.price-val');
                if (priceValEl) priceValEl.textContent = itemData.selling_price;

                const priceInput = row.querySelector('.inline-price-input');
                if (priceInput) priceInput.setAttribute('data-original', itemData.selling_price);

                // Update store qty badge
                const storeContainer = row.querySelector('.store-qty-container');
                if (storeContainer) {
                    const sq = itemData.store_quantity;
                    let badgeClass = sq == 0 ? 'bg-danger-subtle text-danger border-danger-subtle' : (sq < 10 ? 'bg-warning-subtle text-warning-emphasis border-warning-subtle' : 'bg-success-subtle text-success border-success-subtle');
                    let icon = sq == 0 ? '<i class="bi bi-exclamation-triangle-fill me-1"></i>' : (sq < 10 ? '<i class="bi bi-exclamation-circle me-1"></i>' : '');
                    storeContainer.innerHTML = `<span class="badge ${badgeClass} border px-3 py-2 rounded-pill fw-bold">${icon}${sq}</span>`;
                }

                // Update literature stock badge consistently
                const litContainer = row.querySelector('.lit-qty-container');
                if (litContainer) {
                    const lq = itemData.lit_quantity;
                    let badgeClass = lq == 0 ? 'bg-danger-subtle text-danger border-danger-subtle' : (lq < 10 ? 'bg-warning-subtle text-warning-emphasis border-warning-subtle' : 'bg-info-subtle text-info border-info-subtle');
                    litContainer.innerHTML = `<span class="badge ${badgeClass} border px-3 py-2 rounded-pill fw-bold">${lq}</span>`;
                }

                // Update input dataset store-qty limit
                const transferInput = row.querySelector('.inline-transfer-input');
                if (transferInput) transferInput.setAttribute('data-store-qty', itemData.store_quantity);

                // Update checkbox datasets
                const checkbox = row.querySelector('.item-checkbox');
                if (checkbox) {
                    checkbox.setAttribute('data-store-qty', itemData.store_quantity);
                    checkbox.setAttribute('data-lit-qty', itemData.lit_quantity);
                }
            }

            function updateBulkEditSaveState() {
                const editingRows = document.querySelectorAll('.inventory-row.is-editing');
                let anyErrors = false;
                editingRows.forEach(row => {
                    if (!validateRowInputs(row)) anyErrors = true;
                });
                if (btnSaveBulkEdit) btnSaveBulkEdit.disabled = anyErrors;
            }

            // Enter Key press handler in inline inputs
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && (e.target.classList.contains('inline-price-input') || e.target.classList.contains('inline-receive-input') || e.target.classList.contains('inline-transfer-input'))) {
                    e.preventDefault();
                    const row = e.target.closest('tr');
                    if (row) {
                        const saveBtn = row.querySelector('.inline-save-btn');
                        if (saveBtn && !saveBtn.disabled) saveBtn.click();
                    }
                }
            });

            // Quick Batch Movement Modal Management
            const i18n = {
                btnRec: "{{ __('messages.receive') }}",
                btnToLit: "{{ __('messages.transfer_to_lit') }}",
                btnFromLit: "{{ __('messages.return_from_lit') }}",
                transferArrow: "{{ $transferArrow }}",
                returnArrow: "{{ $returnArrow }}",
                presetMax: "{{ __('messages.preset_max') }}",
                noMatchingItems: "{{ __('messages.no_matching_items') }}",
                exceedsStoreStock: "{{ __('messages.exceeds_store_stock_val') }}",
                exceedsLitStock: "{{ __('messages.exceeds_lit_stock_val') }}",
                processing: "{{ __('messages.processing') }}",
                submitBatchMovement: "{{ __('messages.submit_batch_movement') }}"
            };

            const allItemsData = @json($allItems ?? []);
            let batchState = [];

            const quickBatchSearch = document.getElementById('quickBatchSearch');
            const quickBatchSearchResults = document.getElementById('quickBatchSearchResults');
            const globalMovementType = document.getElementById('globalMovementType');
            const quickBatchTbody = document.getElementById('quickBatchTbody');
            const batchItemBadge = document.getElementById('batchItemBadge');
            const btnClearBatch = document.getElementById('btnClearBatch');
            const summaryTotalQty = document.getElementById('summaryTotalQty');
            const summaryTotalValuation = document.getElementById('summaryTotalValuation');
            const btnSubmitBatchMovement = document.getElementById('btnSubmitBatchMovement');

            if (quickBatchSearch) {
                quickBatchSearch.addEventListener('input', function() {
                    const query = this.value.trim().toLowerCase();
                    if (!query) {
                        quickBatchSearchResults.style.display = 'none';
                        quickBatchSearchResults.innerHTML = '';
                        return;
                    }

                    const filtered = allItemsData.filter(item => {
                        const nameAr = (item.name || '').toLowerCase();
                        const nameEn = (item.name_en || '').toLowerCase();
                        const cat = (item.category || '').toLowerCase();
                        return nameAr.includes(query) || nameEn.includes(query) || cat.includes(query);
                    }).slice(0, 10);

                    if (filtered.length === 0) {
                        quickBatchSearchResults.innerHTML = `<div class="p-3 text-muted text-center small"><i class="bi bi-search me-1"></i>${i18n.noMatchingItems}</div>`;
                    } else {
                        quickBatchSearchResults.innerHTML = filtered.map(item => {
                            const isAlreadyAdded = batchState.some(b => b.id === item.id);
                            return `
                                <button type="button" class="dropdown-item p-2 rounded-3 d-flex justify-content-between align-items-center ${isAlreadyAdded ? 'disabled opacity-50 bg-light' : ''}" data-item-id="${item.id}">
                                    <div>
                                        <div class="fw-bold text-dark mb-0">${item.display_name}</div>
                                        <small class="text-secondary">${item.category}</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Store: ${item.store_quantity}</span>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">Lit: ${item.lit_quantity}</span>
                                        ${isAlreadyAdded ? '<span class="badge bg-secondary rounded-pill">Added</span>' : '<i class="bi bi-plus-circle-fill text-primary fs-5"></i>'}
                                    </div>
                                </button>
                            `;
                        }).join('');
                    }
                    quickBatchSearchResults.style.display = 'block';
                });

                quickBatchSearchResults.addEventListener('click', function(e) {
                    const btn = e.target.closest('[data-item-id]');
                    if (!btn || btn.classList.contains('disabled')) return;
                    const itemId = parseInt(btn.getAttribute('data-item-id'));
                    const targetItem = allItemsData.find(i => i.id === itemId);
                    if (targetItem) {
                        addItemToBatch(targetItem);
                        quickBatchSearch.value = '';
                        quickBatchSearchResults.style.display = 'none';
                        quickBatchSearch.focus();
                    }
                });

                document.addEventListener('click', function(e) {
                    if (!quickBatchSearch.contains(e.target) && !quickBatchSearchResults.contains(e.target)) {
                        quickBatchSearchResults.style.display = 'none';
                    }
                });
            }

            if (globalMovementType) {
                globalMovementType.addEventListener('change', function() {
                    const newType = this.value;
                    batchState.forEach(item => {
                        item.type = newType;
                    });
                    renderBatchTable();
                });
            }

            if (btnClearBatch) {
                btnClearBatch.addEventListener('click', function() {
                    batchState = [];
                    renderBatchTable();
                });
            }

            function addItemToBatch(item) {
                if (batchState.some(b => b.id === item.id)) return;
                const defaultType = globalMovementType ? globalMovementType.value : 'receive';
                batchState.push({
                    id: item.id,
                    display_name: item.display_name,
                    category: item.category,
                    selling_price: item.selling_price,
                    store_quantity: item.store_quantity,
                    lit_quantity: item.lit_quantity,
                    type: defaultType,
                    qty: 1
                });
                renderBatchTable();
            }

            function renderBatchTable() {
                if (!quickBatchTbody) return;

                if (batchState.length === 0) {
                    quickBatchTbody.innerHTML = `
                        <tr id="emptyBatchRow">
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-basket3 fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                <span>{{ __('messages.no_items_in_batch') }}</span>
                            </td>
                        </tr>
                    `;
                    batchItemBadge.textContent = '0';
                    if (btnClearBatch) btnClearBatch.style.display = 'none';
                    summaryTotalQty.textContent = '0';
                    summaryTotalValuation.textContent = '0.00';
                    if (btnSubmitBatchMovement) btnSubmitBatchMovement.disabled = true;
                    return;
                }

                if (btnClearBatch) btnClearBatch.style.display = 'inline-block';
                batchItemBadge.textContent = batchState.length;

                let hasError = false;
                let totalQty = 0;
                let totalValuation = 0;

                quickBatchTbody.innerHTML = batchState.map((item, index) => {
                    totalQty += item.qty;
                    const lineValue = item.qty * item.selling_price;
                    totalValuation += lineValue;

                    let isError = false;
                    let errorMsg = '';
                    if (item.type === 'transfer_to_lit' && item.qty > item.store_quantity) {
                        isError = true;
                        errorMsg = i18n.exceedsStoreStock + ' (' + item.store_quantity + ')';
                    } else if (item.type === 'return_from_lit' && item.qty > item.lit_quantity) {
                        isError = true;
                        errorMsg = i18n.exceedsLitStock + ' (' + item.lit_quantity + ')';
                    }

                    if (isError) hasError = true;

                    return `
                        <tr data-batch-index="${index}">
                            <td class="ps-4">
                                <div class="fw-bold text-dark">${item.display_name}</div>
                                <small class="text-secondary">${item.category}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill me-1" title="Store Qty">Store: ${item.store_quantity}</span>
                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill" title="Lit Qty">Lit: ${item.lit_quantity}</span>
                            </td>
                            <td>
                                <select class="form-select form-select-sm rounded-pill font-semibold batch-item-type" data-index="${index}">
                                    <option value="receive" ${item.type === 'receive' ? 'selected' : ''}>➕ ${i18n.btnRec}</option>
                                    <option value="transfer_to_lit" ${item.type === 'transfer_to_lit' ? 'selected' : ''}>${i18n.transferArrow} ${i18n.btnToLit}</option>
                                    <option value="return_from_lit" ${item.type === 'return_from_lit' ? 'selected' : ''}>${i18n.returnArrow} ${i18n.btnFromLit}</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <input type="number" min="1" dir="ltr" class="form-control form-control-sm text-center font-bold border-secondary-subtle batch-item-qty ${isError ? 'is-invalid' : ''}" data-index="${index}" value="${item.qty}" style="max-width: 70px;">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary px-1 py-0 btn-preset-add" data-index="${index}" data-add="1">+1</button>
                                        <button type="button" class="btn btn-outline-secondary px-1 py-0 btn-preset-add" data-index="${index}" data-add="5">+5</button>
                                        <button type="button" class="btn btn-outline-secondary px-1 py-0 btn-preset-add" data-index="${index}" data-add="10">+10</button>
                                        <button type="button" class="btn btn-outline-primary px-1 py-0 btn-preset-max" data-index="${index}">${i18n.presetMax}</button>
                                    </div>
                                </div>
                                ${isError ? `<div class="invalid-feedback d-block fw-semibold" style="font-size:0.75rem;">${errorMsg}</div>` : ''}
                            </td>
                            <td class="text-end pe-4 font-bold text-dark" dir="ltr">
                                EGP ${lineValue.toFixed(2)}
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-link text-danger p-0 border-0 btn-remove-batch-item" data-index="${index}">
                                    <i class="bi bi-trash fs-5"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');

                summaryTotalQty.textContent = totalQty;
                summaryTotalValuation.textContent = totalValuation.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                if (btnSubmitBatchMovement) {
                    btnSubmitBatchMovement.disabled = hasError || batchState.length === 0;
                }
            }

            if (quickBatchTbody) {
                quickBatchTbody.addEventListener('change', function(e) {
                    const typeSelect = e.target.closest('.batch-item-type');
                    if (typeSelect) {
                        const idx = parseInt(typeSelect.getAttribute('data-index'));
                        if (batchState[idx]) {
                            batchState[idx].type = typeSelect.value;
                            renderBatchTable();
                        }
                        return;
                    }

                    const qtyInput = e.target.closest('.batch-item-qty');
                    if (qtyInput) {
                        const idx = parseInt(qtyInput.getAttribute('data-index'));
                        let val = parseInt(qtyInput.value) || 1;
                        if (val < 1) val = 1;
                        if (batchState[idx]) {
                            batchState[idx].qty = val;
                            renderBatchTable();
                        }
                    }
                });

                quickBatchTbody.addEventListener('input', function(e) {
                    const qtyInput = e.target.closest('.batch-item-qty');
                    if (qtyInput) {
                        const idx = parseInt(qtyInput.getAttribute('data-index'));
                        let val = parseInt(qtyInput.value) || 1;
                        if (val < 1) val = 1;
                        if (batchState[idx]) {
                            batchState[idx].qty = val;
                            renderBatchTable();
                        }
                    }
                });

                quickBatchTbody.addEventListener('click', function(e) {
                    const addBtn = e.target.closest('.btn-preset-add');
                    if (addBtn) {
                        const idx = parseInt(addBtn.getAttribute('data-index'));
                        const addVal = parseInt(addBtn.getAttribute('data-add')) || 1;
                        if (batchState[idx]) {
                            batchState[idx].qty += addVal;
                            renderBatchTable();
                        }
                        return;
                    }

                    const maxBtn = e.target.closest('.btn-preset-max');
                    if (maxBtn) {
                        const idx = parseInt(maxBtn.getAttribute('data-index'));
                        const item = batchState[idx];
                        if (item) {
                            if (item.type === 'transfer_to_lit') {
                                item.qty = Math.max(1, item.store_quantity);
                            } else if (item.type === 'return_from_lit') {
                                item.qty = Math.max(1, item.lit_quantity);
                            } else {
                                item.qty += 10;
                            }
                            renderBatchTable();
                        }
                        return;
                    }

                    const removeBtn = e.target.closest('.btn-remove-batch-item');
                    if (removeBtn) {
                        const idx = parseInt(removeBtn.getAttribute('data-index'));
                        batchState.splice(idx, 1);
                        renderBatchTable();
                    }
                });
            }

            if (btnSubmitBatchMovement) {
                btnSubmitBatchMovement.addEventListener('click', function() {
                    if (batchState.length === 0 || btnSubmitBatchMovement.disabled) return;

                    btnSubmitBatchMovement.disabled = true;
                    btnSubmitBatchMovement.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> ' + i18n.processing;

                    const batchNotes = document.getElementById('batchNotesInput') ? document.getElementById('batchNotesInput').value : '';

                    fetch('{{ route("store.batch_movement") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            items: batchState.map(i => ({ id: i.id, type: i.type, quantity: i.qty })),
                            notes: batchNotes
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message, 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 800);
                        } else {
                            showToast(data.message || 'Error executing batch movement', 'danger');
                            btnSubmitBatchMovement.disabled = false;
                            btnSubmitBatchMovement.innerHTML = '<i class="bi bi-check2-circle me-1"></i> ' + i18n.submitBatchMovement;
                        }
                    })
                    .catch(err => {
                        showToast(err.message || 'Network error occurred', 'danger');
                        btnSubmitBatchMovement.disabled = false;
                        btnSubmitBatchMovement.innerHTML = '<i class="bi bi-check2-circle me-1"></i> ' + i18n.submitBatchMovement;
                    });
                });
            }

            // Helper toast function
            function showToast(message, type) {
                let toastContainer = document.getElementById('storeToastContainer');
                if (!toastContainer) {
                    toastContainer = document.createElement('div');
                    toastContainer.id = 'storeToastContainer';
                    toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
                    toastContainer.style.zIndex = '1100';
                    document.body.appendChild(toastContainer);
                }
                const toastEl = document.createElement('div');
                toastEl.className = `toast align-items-center text-bg-${type} border-0 show`;
                toastEl.setAttribute('role', 'alert');
                toastEl.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body fw-semibold">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                `;
                toastContainer.appendChild(toastEl);
                setTimeout(() => {
                    toastEl.remove();
                }, 3500);
            }
        });
    </script>
</x-layout>
