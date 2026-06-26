<x-layout>
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

        {{-- Search & Overview Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-12 col-xl-8">
                <div class="card p-3 shadow-sm border-0 h-100 justify-content-center">
                    <form action="{{ route('store.index') }}" method="GET" class="row g-2 align-items-center">
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

        {{-- Inventory Items Table --}}
        <div class="card border-0 shadow-sm p-4">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.item_name') }}</th>
                            <th>{{ __('messages.Description') }}</th>
                            <th class="text-end">{{ __('messages.selling_price') }}</th>
                            <th class="text-center">{{ __('messages.store_qty') }}</th>
                            <th class="text-center">{{ __('messages.lit_qty') }}</th>
                            <th class="text-center">{{ __('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->name }}</div>
                                </td>
                                <td>
                                    <span class="text-secondary small">{{ Str::limit($item->description, 60) ?: '-' }}</span>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    EGP {{ number_format($item->selling_price, 2) }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fs-6">
                                        {{ $item->store_quantity }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill fs-6">
                                        {{ $item->lit_quantity }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1 flex-wrap">
                                        {{-- Receive --}}
                                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill" 
                                                data-bs-toggle="modal" data-bs-target="#receiveModal{{ $item->id }}">
                                            <i class="bi bi-plus-circle me-1"></i>{{ __('messages.receive') }}
                                        </button>

                                        {{-- Transfer --}}
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" 
                                                data-bs-toggle="modal" data-bs-target="#transferModal{{ $item->id }}">
                                            <i class="bi bi-arrow-right-circle me-1"></i>{{ __('messages.transfer_to_lit') }}
                                        </button>

                                        {{-- Return --}}
                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill text-dark" 
                                                data-bs-toggle="modal" data-bs-target="#returnModal{{ $item->id }}">
                                            <i class="bi bi-arrow-left-circle me-1"></i>{{ __('messages.return_from_lit') }}
                                        </button>

                                        {{-- Edit --}}
                                        <button type="button" class="btn btn-sm btn-light border rounded-pill" 
                                                data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        {{-- Delete --}}
                                        <form action="{{ route('store.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete_item') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- Modals for current item --}}
                            {{-- Receive Modal --}}
                            <div class="modal fade" id="receiveModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="{{ route('store.receive', $item->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                                        @csrf
                                        <div class="modal-header border-0 bg-success-subtle text-success">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>{{ __('messages.receive_stock_for', ['name' => $item->name]) }}</h5>
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
                            <div class="modal fade" id="transferModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="{{ route('store.transfer', $item->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                                        @csrf
                                        <div class="modal-header border-0 bg-primary-subtle text-primary">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-arrow-right-circle me-2"></i>{{ __('messages.transfer_to_lit_title', ['name' => $item->name]) }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3 text-secondary">
                                                {{ __('messages.current_store_balance') }} <span class="badge bg-secondary">{{ $item->store_quantity }}</span>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">{{ __('messages.qty_to_transfer') }}</label>
                                                <input type="number" name="quantity" min="1" max="{{ $item->store_quantity }}" required class="form-control rounded-3" placeholder="e.g. 10">
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
                            <div class="modal fade" id="returnModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="{{ route('store.return', $item->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                                        @csrf
                                        <div class="modal-header border-0 bg-warning-subtle text-warning-emphasis">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-arrow-left-circle me-2"></i>{{ __('messages.return_from_lit_title', ['name' => $item->name]) }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3 text-secondary">
                                                {{ __('messages.current_lit_balance') }} <span class="badge bg-secondary">{{ $item->lit_quantity }}</span>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">{{ __('messages.qty_to_return') }}</label>
                                                <input type="number" name="quantity" min="1" max="{{ $item->lit_quantity }}" required class="form-control rounded-3" placeholder="e.g. 5">
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
                            <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="{{ route('store.update', $item->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-0 bg-light">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>{{ __('messages.edit_item_details') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">{{ __('messages.item_name') }}</label>
                                                <input type="text" name="name" value="{{ $item->name }}" required class="form-control rounded-3">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">{{ __('messages.selling_price') }} (EGP)</label>
                                                <input type="number" step="0.01" name="selling_price" value="{{ $item->selling_price }}" required class="form-control rounded-3">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">{{ __('messages.Description') }}</label>
                                                <textarea name="description" class="form-control rounded-3" rows="3">{{ $item->description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                                            <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('messages.save_changes') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    {{ __('messages.no_items_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                        <label class="form-label fw-semibold">{{ __('messages.item_name') }}</label>
                        <input type="text" name="name" required class="form-control rounded-3" placeholder="e.g. Basic Text Book">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.selling_price') }} (EGP)</label>
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
</x-layout>
