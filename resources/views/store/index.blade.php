<x-layout>
    <style>
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
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createItemModal">
                    <i class="bi bi-plus-lg me-1"></i>
                    {{ __('messages.add_new_item') }}
                </button>
            </div>
        </div>

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
            <div class="col-12 col-xl-8">
                <div class="card p-3 shadow-sm border-0 h-100 justify-content-center">
                    <form action="{{ route('store.index') }}" method="GET" class="row g-2 align-items-center">
                        <input type="hidden" name="category" value="{{ request('category') }}">
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
            
            <div class="col-12 col-md-6 col-xl-2">
                <div class="card text-center p-3 border-0 bg-success-subtle text-success h-100">
                    <div class="fs-4 mb-1"><i class="bi bi-box-seam"></i></div>
                    <div class="fw-semibold">{{ __('messages.total_store_stock') }}</div>
                    <div class="h3 mb-0 mt-2 font-bold">{{ $items->sum('store_quantity') }}</div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-2">
                <div class="card text-center p-3 border-0 bg-info-subtle text-info h-100">
                    <div class="fs-4 mb-1"><i class="bi bi-book"></i></div>
                    <div class="fw-semibold">{{ __('messages.total_lit_stock') }}</div>
                    <div class="h3 mb-0 mt-2 font-bold">{{ $items->sum('lit_quantity') }}</div>
                </div>
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

        {{-- Inventory Items Table --}}
        <div class="card border-0 shadow-sm p-4">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>{{ __('messages.item_name') }}</th>
                            <th>{{ __('messages.Category') }}</th>
                            <th>{{ __('messages.Description') }}</th>
                            <th class="text-end">{{ __('messages.selling_price') }}</th>
                            <th class="text-center">{{ __('messages.store_qty') }}</th>
                            <th class="text-center">{{ __('messages.lit_qty') }}</th>
                            <th class="text-center">{{ __('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr class="inventory-row">
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
                                <td class="text-end fw-bold text-dark">
                                    {{ __('messages.EGP') }} {{ number_format($item->selling_price, 2) }}
                                </td>
                                <td class="text-center">
                                    @if ($item->store_quantity == 0)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-bold">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>0
                                        </span>
                                    @elseif ($item->store_quantity < 10)
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fw-bold">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $item->store_quantity }}
                                        </span>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                                            {{ $item->store_quantity }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->lit_quantity == 0)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-bold">
                                            0
                                        </span>
                                    @elseif ($item->lit_quantity < 10)
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
                                    <div class="d-flex justify-content-center">
                                        <div class="dropdown">
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
                                                        <i class="bi bi-arrow-right-circle-fill"></i>{{ __('messages.transfer_to_lit') }}
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-warning-emphasis py-2" 
                                                            data-bs-toggle="modal" data-bs-target="#returnModal"
                                                            data-id="{{ $item->id }}" data-name="{{ $item->store_display_name }}" data-lit-qty="{{ $item->lit_quantity }}">
                                                        <i class="bi bi-arrow-left-circle-fill"></i>{{ __('messages.return_from_lit') }}
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
        });
    </script>
</x-layout>
