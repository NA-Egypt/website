<x-layout>
    <div class="container-fluid py-4">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h1 class="h2 text-gradient font-bold mb-0">
                        {{ __('messages.lit_inventory_title') }}
                    </h1>
                    <span id="litLiveSyncBadge" class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1 small fw-semibold">
                        <span class="spinner-grow spinner-grow-sm text-success" style="width: 7px; height: 7px;" role="status"></span>
                        <span>{{ __('messages.live_sync_active') }}</span>
                        <x-inventory-card-help
                            :title="__('messages.live_sync_active')"
                            :description="__('messages.card_help_live_sync')"
                        />
                    </span>
                </div>
                <p class="text-secondary mb-0">
                    {{ __('messages.lit_inventory_desc') }}
                </p>
            </div>
            <div>
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm fw-semibold d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#keyboardShortcutsModal" title="{{ __('messages.keyboard_shortcuts_desc') }}">
                    <i class="bi bi-keyboard"></i>
                    <span>{{ __('messages.keyboard_shortcuts') }}</span>
                    <span class="kbd-badge ms-1">?</span>
                </button>
            </div>
        </div>

        {{-- Overview Stock Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm d-flex align-items-center justify-content-between flex-row h-100">
                    <div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="text-secondary small fw-semibold">{{ __('messages.total_unique_items') }}</span>
                            <x-inventory-card-help
                                :title="__('messages.total_unique_items')"
                                :description="__('messages.card_help_lit_unique_items')"
                                :formula="__('messages.card_formula_lit_unique_items')"
                            />
                        </div>
                        <h3 class="mt-1 mb-0 font-bold text-dark" id="litTotalUniqueCardVal">{{ $items->count() }}</h3>
                    </div>
                    <div class="fs-2 text-primary bg-primary-subtle rounded-3 p-2 px-3"><i class="bi bi-tags"></i></div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm d-flex align-items-center justify-content-between flex-row h-100">
                    <div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="text-secondary small fw-semibold">{{ __('messages.total_lit_qty') }}</span>
                            <x-inventory-card-help
                                :title="__('messages.total_lit_qty')"
                                :description="__('messages.card_help_total_lit_stock')"
                                :formula="__('messages.card_formula_total_lit_stock')"
                            />
                        </div>
                        <h3 class="mt-1 mb-0 font-bold text-info" id="litTotalStockCardVal">{{ $items->sum('lit_quantity') }}</h3>
                    </div>
                    <div class="fs-2 text-info bg-info-subtle rounded-3 p-2 px-3"><i class="bi bi-book"></i></div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm d-flex align-items-center justify-content-between flex-row h-100">
                    <div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="text-secondary small fw-semibold">{{ __('messages.total_estimated_value') }}</span>
                            <x-inventory-card-help
                                :title="__('messages.total_estimated_value')"
                                :description="__('messages.card_help_lit_estimated_value')"
                                :formula="__('messages.card_formula_lit_estimated_value')"
                            />
                        </div>
                        <h3 class="mt-1 mb-0 font-bold text-success" id="litTotalValueCardVal">
                            EGP {{ number_format($items->sum(fn($i) => $i->lit_quantity * $i->selling_price), 2) }}
                        </h3>
                    </div>
                    <div class="fs-2 text-success bg-success-subtle rounded-3 p-2 px-3"><i class="bi bi-currency-dollar"></i></div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card p-3 border-0 shadow-sm d-flex align-items-center justify-content-between flex-row h-100">
                    <div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="text-secondary small fw-semibold">{{ __('messages.total_damaged_stock') }}</span>
                            <x-inventory-card-help
                                :title="__('messages.total_damaged_stock')"
                                :description="__('messages.card_help_total_damaged_stock')"
                                :formula="__('messages.card_formula_total_damaged_stock')"
                            />
                        </div>
                        <h3 class="mt-1 mb-0 font-bold text-danger" id="litTotalDamagedCardVal">{{ $items->sum('damaged_quantity') }}</h3>
                    </div>
                    <div class="fs-2 text-danger bg-danger-subtle rounded-3 p-2 px-3"><i class="bi bi-heartbreak-fill"></i></div>
                </div>
            </div>
        </div>

        {{-- Search & Category filter --}}
        <div class="card p-3 shadow-sm border-0 mb-4 justify-content-center">
            <form action="{{ route('lit.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-5 position-relative">
                    <input type="search" id="litSearchInput" name="search" value="{{ request('search') }}" class="form-control rounded-pill bg-transparent border-secondary-subtle px-4 pe-5" placeholder="{{ __('messages.search_literature') }} ( / )">
                    <span class="kbd-badge position-absolute top-50 translate-middle-y end-0 me-3 d-none d-md-inline-flex" style="pointer-events: none;">/</span>
                </div>
                <div class="col-12 col-md-4">
                    <select name="category" class="form-select rounded-pill border-secondary-subtle px-3">
                        <option value="">{{ __('messages.Category') }} ({{ __('messages.all') }})</option>
                        @foreach(\App\Models\InventoryItem::CATEGORIES as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                {{ __('messages.cat_' . Str::snake(str_replace(' ', '_', $cat))) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-secondary rounded-pill w-100 shadow-sm">
                        <i class="bi bi-search me-1"></i>
                        {{ __('messages.Search') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Inventory Table --}}
        <div class="card border-0 shadow-sm p-4">
            {{-- Keyboard Navigation Hint Bar --}}
            <div class="d-none d-lg-flex align-items-center justify-content-between bg-light rounded-3 px-3 py-2 mb-3 border border-secondary-subtle small text-secondary">
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <span><i class="bi bi-keyboard text-primary me-1"></i><strong>{{ __('messages.keyboard_shortcuts') }}:</strong></span>
                    <span><span class="kbd-badge">↑</span> <span class="kbd-badge">↓</span> {{ __('messages.shortcut_nav_rows') }}</span>
                    <span><span class="kbd-badge">D</span> {{ __('messages.mark_damaged') }}</span>
                    <span><span class="kbd-badge">/</span> {{ __('messages.shortcut_focus_search') }}</span>
                </div>
                <button type="button" class="btn btn-link btn-sm text-primary text-decoration-none p-0 fw-semibold" data-bs-toggle="modal" data-bs-target="#keyboardShortcutsModal">
                    {{ __('messages.shortcut_open_help') }} <span class="kbd-badge ms-1">?</span>
                </button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0" id="litInventoryTable">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.literature_title') }}</th>
                            <th>{{ __('messages.Category') }}</th>
                            <th>{{ __('messages.Description') }}</th>
                            <th class="text-end">{{ __('messages.selling_price') }}</th>
                            <th class="text-center">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <span>{{ __('messages.available_stock_lit') }}</span>
                                    <x-inventory-card-help
                                        :title="__('messages.available_stock_lit')"
                                        :description="__('messages.card_help_total_lit_stock')"
                                        :formula="__('messages.card_formula_total_lit_stock')"
                                    />
                                </div>
                            </th>
                            <th class="text-center">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <span>{{ __('messages.damaged_qty') }}</span>
                                    <x-inventory-card-help
                                        :title="__('messages.damaged_qty')"
                                        :description="__('messages.card_help_total_damaged_stock')"
                                        :formula="__('messages.card_formula_total_damaged_stock')"
                                    />
                                </div>
                            </th>
                            <th class="text-center">{{ __('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            @php
                                $damageNotesHtml = '';
                                if ($item->relationLoaded('damagedTransactions') && $item->damagedTransactions->isNotEmpty()) {
                                    $damageNotesHtml = '<div class="inv-help-popover-body"><div class="inv-help-popover-title"><i class="bi bi-heartbreak-fill text-danger"></i>' . e(__('messages.damage_notes_history')) . '</div><ul class="list-unstyled mb-0 small">';
                                    foreach ($item->damagedTransactions->take(5) as $dTx) {
                                        $srcLabel = ($dTx->source_location === 'lit') ? __('messages.damage_source_lit') : __('messages.damage_source_store');
                                        $damageNotesHtml .= '<li class="mb-2 pb-1 border-bottom border-light"><div class="d-flex justify-content-between fw-bold text-danger"><span>-' . (int) $dTx->quantity . ' (' . e($srcLabel) . ')</span><span class="text-muted fw-normal" style="font-size:0.7rem;">' . e($dTx->created_at ? $dTx->created_at->format('Y-m-d H:i') : '') . '</span></div><div class="text-dark mt-1">' . e($dTx->notes) . '</div><div class="text-muted" style="font-size:0.7rem;">' . e($dTx->user->name ?? 'System') . '</div></li>';
                                    }
                                    $damageNotesHtml .= '</ul></div>';
                                }
                            @endphp
                            <tr class="inventory-row"
                                data-row-id="{{ $item->id }}"
                                data-lit-qty="{{ $item->lit_quantity }}"
                                data-damaged-qty="{{ (int) ($item->damaged_quantity ?? 0) }}"
                                data-price="{{ (float) $item->selling_price }}"
                                tabindex="0">
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->name }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
                                        {{ __('messages.cat_' . Str::snake(str_replace(' ', '_', $item->category))) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-secondary small">{{ Str::limit($item->description, 100) ?: '-' }}</span>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    EGP <span class="lit-price-val">{{ number_format($item->selling_price, 2) }}</span>
                                </td>
                                <td class="text-center lit-qty-container" id="lit-qty-container-{{ $item->id }}">
                                    <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill fs-6 lit-qty-badge">
                                        {{ $item->lit_quantity }}
                                    </span>
                                </td>
                                <td class="text-center damaged-qty-container" id="lit-damaged-container-{{ $item->id }}">
                                    @if ((int) ($item->damaged_quantity ?? 0) > 0)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-bold d-inline-flex align-items-center gap-1"
                                              @if($damageNotesHtml)
                                              tabindex="0"
                                              data-bs-toggle="popover"
                                              data-bs-trigger="hover focus click"
                                              data-bs-placement="top"
                                              data-bs-html="true"
                                              data-bs-custom-class="inv-help-popover"
                                              data-bs-content="{{ $damageNotesHtml }}"
                                              style="cursor: pointer;"
                                              @endif>
                                            <i class="bi bi-heartbreak-fill"></i>
                                            <span class="damaged-qty-val">{{ (int) $item->damaged_quantity }}</span>
                                            @if($damageNotesHtml)
                                                <i class="bi bi-chat-left-text-fill ms-1 small opacity-75"></i>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted small damaged-qty-val">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-semibold btn-lit-damage"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->name }}"
                                            data-lit-qty="{{ $item->lit_quantity }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#litDamageModal"
                                            title="{{ __('messages.mark_damaged') }} (D)">
                                        <i class="bi bi-heartbreak-fill me-1"></i>{{ __('messages.mark_damaged') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-secondary py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    {{ __('messages.no_literature_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Mark Literature Item Damaged Modal --}}
    <div class="modal fade" id="litDamageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="" method="POST" class="modal-content border-0 shadow-lg rounded-4" id="litDamageForm">
                @csrf
                <div class="modal-header border-0 bg-danger-subtle text-danger">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-heartbreak-fill me-2"></i>{{ __('messages.mark_damaged_title', ['name' => '']) }}<span class="modal-title-name"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3 text-secondary">
                        {{ __('messages.current_lit_balance') }} <span class="badge bg-info text-dark fw-bold" id="litDamageCurrentBalanceBadge">0</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.damaged_quantity_input') }}</label>
                        <input type="number" name="quantity" id="litDamageQuantityInput" min="1" required class="form-control rounded-3" placeholder="e.g. 1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.damage_note_label') }} <span class="text-danger">*</span></label>
                        <textarea name="notes" id="litDamageNotesInput" required class="form-control rounded-3" rows="3" placeholder="{{ __('messages.damage_note_placeholder') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">
                        <i class="bi bi-heartbreak-fill me-1"></i>{{ __('messages.confirm_damaged') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Keyboard Shortcuts Modal --}}
    <x-inventory-shortcuts-modal mode="lit" />

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = '{{ csrf_token() }}';
            const liveStateUrl = '{{ route("inventory.live_state") }}';
            let currentStateHash = null;
            let focusedRowIndex = -1;

            const inventoryChannel = ('BroadcastChannel' in window) ? new BroadcastChannel('na_egypt_inventory_live') : null;
            if (inventoryChannel) {
                inventoryChannel.addEventListener('message', function (event) {
                    if (event.data && event.data.live_state) {
                        applyLitLiveState(event.data.live_state, true);
                    }
                });
            }

            const litDamageModal = document.getElementById('litDamageModal');
            const litDamageForm = document.getElementById('litDamageForm');
            const litDamageQtyInput = document.getElementById('litDamageQuantityInput');

            if (litDamageModal) {
                litDamageModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    if (!button) return;
                    const id = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    const row = document.querySelector(`#litInventoryTable tr[data-row-id="${id}"]`);
                    const litQty = parseInt(row ? row.getAttribute('data-lit-qty') : (button.getAttribute('data-lit-qty') || 0), 10);

                    litDamageForm.action = "{{ route('lit.damage', ':id') }}".replace(':id', id);
                    litDamageForm.reset();

                    const titleName = litDamageModal.querySelector('.modal-title-name');
                    if (titleName) titleName.textContent = name;

                    const balBadge = document.getElementById('litDamageCurrentBalanceBadge');
                    if (balBadge) balBadge.textContent = litQty;

                    if (litDamageQtyInput) litDamageQtyInput.max = litQty;
                });
                litDamageModal.addEventListener('shown.bs.modal', function () {
                    if (litDamageQtyInput) litDamageQtyInput.focus();
                });
            }

            if (litDamageForm) {
                litDamageForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const submitBtn = litDamageForm.querySelector('button[type="submit"]');
                    const origHtml = submitBtn ? submitBtn.innerHTML : '';
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';
                    }

                    fetch(litDamageForm.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: new FormData(litDamageForm)
                    })
                    .then(res => res.json().then(data => ({ status: res.status, body: data })))
                    .then(({ status, body }) => {
                        if (status >= 200 && status < 300 && body.success) {
                            const inst = bootstrap.Modal.getInstance(litDamageModal);
                            if (inst) inst.hide();
                            if (body.live_state) {
                                applyLitLiveState(body.live_state, true);
                                if (inventoryChannel) {
                                    try { inventoryChannel.postMessage({ live_state: body.live_state }); } catch (err) {}
                                }
                            }
                            showToast(body.message, 'success');
                        } else {
                            let errMsg = body.message || 'Validation error occurred.';
                            if (body.errors) {
                                const firstErr = Object.values(body.errors)[0];
                                if (Array.isArray(firstErr) && firstErr.length > 0) errMsg = firstErr[0];
                            }
                            showToast(errMsg, 'danger');
                        }
                    })
                    .catch(() => showToast('Failed to record damaged stock.', 'danger'))
                    .finally(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = origHtml;
                        }
                    });
                });
            }

            function escapeHtml(str) {
                return String(str || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function buildDamagePopoverHtml(damageNotes) {
                if (!Array.isArray(damageNotes) || damageNotes.length === 0) return '';
                let html = `<div class="inv-help-popover-body"><div class="inv-help-popover-title"><i class="bi bi-heartbreak-fill text-danger"></i>{{ __('messages.damage_notes_history') }}</div><ul class="list-unstyled mb-0 small">`;
                damageNotes.slice(0, 5).forEach(dTx => {
                    const srcLabel = dTx.source_location === 'lit' ? "{{ __('messages.damage_source_lit') }}" : "{{ __('messages.damage_source_store') }}";
                    html += `<li class="mb-2 pb-1 border-bottom border-light"><div class="d-flex justify-content-between fw-bold text-danger"><span>-${parseInt(dTx.quantity || 0, 10)} (${escapeHtml(srcLabel)})</span><span class="text-muted fw-normal" style="font-size:0.7rem;">${escapeHtml(dTx.created_at || '')}</span></div><div class="text-dark mt-1">${escapeHtml(dTx.notes || '')}</div><div class="text-muted" style="font-size:0.7rem;">${escapeHtml(dTx.user_name || 'System')}</div></li>`;
                });
                html += `</ul></div>`;
                return html;
            }

            function flashElement(el) {
                if (!el) return;
                el.classList.remove('live-updated-flash');
                void el.offsetWidth;
                el.classList.add('live-updated-flash');
            }

            function applyLitLiveState(liveState, highlightChanges = false) {
                if (!liveState) return;
                const isFirstLoad = currentStateHash === null;
                currentStateHash = liveState.state_hash || null;

                let visibleLitSum = 0;
                let visibleDamagedSum = 0;
                let visibleValueSum = 0;

                if (Array.isArray(liveState.items)) {
                    liveState.items.forEach(itemData => {
                        const row = document.querySelector(`#litInventoryTable tr[data-row-id="${itemData.id}"]`);
                        if (!row) return;

                        const prevLit = parseInt(row.getAttribute('data-lit-qty') || 0, 10);
                        const prevDamaged = parseInt(row.getAttribute('data-damaged-qty') || 0, 10);
                        const lq = parseInt(itemData.lit_quantity ?? prevLit, 10);
                        const dq = parseInt(itemData.damaged_quantity ?? prevDamaged, 10);
                        const price = parseFloat(itemData.selling_price_raw ?? row.getAttribute('data-price') ?? 0);

                        row.setAttribute('data-lit-qty', lq);
                        row.setAttribute('data-damaged-qty', dq);
                        row.setAttribute('data-price', price);

                        const litContainer = row.querySelector('.lit-qty-container');
                        if (litContainer) {
                            litContainer.innerHTML = `<span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill fs-6 lit-qty-badge">${lq}</span>`;
                            if (!isFirstLoad && highlightChanges && lq !== prevLit) flashElement(litContainer);
                        }

                        const damagedContainer = row.querySelector('.damaged-qty-container');
                        if (damagedContainer) {
                            if (dq > 0) {
                                const popHtml = buildDamagePopoverHtml(itemData.damage_notes || []);
                                const popAttrs = popHtml
                                    ? `tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus click" data-bs-placement="top" data-bs-html="true" data-bs-custom-class="inv-help-popover" data-bs-content="${escapeHtml(popHtml)}" style="cursor: pointer;"`
                                    : '';
                                const chatIcon = popHtml ? `<i class="bi bi-chat-left-text-fill ms-1 small opacity-75"></i>` : '';
                                damagedContainer.innerHTML = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-bold d-inline-flex align-items-center gap-1" ${popAttrs}><i class="bi bi-heartbreak-fill"></i><span class="damaged-qty-val">${dq}</span>${chatIcon}</span>`;
                                if (typeof window.initInventoryPopovers === 'function') {
                                    window.initInventoryPopovers(damagedContainer);
                                }
                            } else {
                                damagedContainer.innerHTML = `<span class="text-muted small damaged-qty-val">0</span>`;
                            }
                            if (!isFirstLoad && highlightChanges && dq !== prevDamaged) flashElement(damagedContainer);
                        }

                        const dmgBtn = row.querySelector('.btn-lit-damage');
                        if (dmgBtn) dmgBtn.setAttribute('data-lit-qty', lq);
                    });
                }

                // Recalculate KPI cards from current visible rows
                document.querySelectorAll('#litInventoryTable tbody tr.inventory-row').forEach(row => {
                    const lq = parseInt(row.getAttribute('data-lit-qty') || 0, 10);
                    const dq = parseInt(row.getAttribute('data-damaged-qty') || 0, 10);
                    const pr = parseFloat(row.getAttribute('data-price') || 0);
                    visibleLitSum += lq;
                    visibleDamagedSum += dq;
                    visibleValueSum += (lq * pr);
                });

                const elStock = document.getElementById('litTotalStockCardVal');
                const elDamaged = document.getElementById('litTotalDamagedCardVal');
                const elValue = document.getElementById('litTotalValueCardVal');

                if (elStock && String(elStock.textContent).trim() !== String(visibleLitSum)) {
                    elStock.textContent = visibleLitSum;
                    if (!isFirstLoad) flashElement(elStock.closest('.card'));
                }
                if (elDamaged && String(elDamaged.textContent).trim() !== String(visibleDamagedSum)) {
                    elDamaged.textContent = visibleDamagedSum;
                    if (!isFirstLoad) flashElement(elDamaged.closest('.card'));
                }
                if (elValue) {
                    const formattedVal = 'EGP ' + visibleValueSum.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    if (String(elValue.textContent).trim() !== formattedVal) {
                        elValue.textContent = formattedVal;
                        if (!isFirstLoad) flashElement(elValue.closest('.card'));
                    }
                }
            }

            function pollLitLiveState() {
                if (document.hidden) return;
                fetch(liveStateUrl, { headers: { 'Accept': 'application/json' } })
                    .then(res => res.ok ? res.json() : null)
                    .then(data => {
                        if (data && data.success && data.live_state) {
                            if (currentStateHash && currentStateHash !== data.live_state.state_hash) {
                                applyLitLiveState(data.live_state, true);
                            } else if (!currentStateHash) {
                                currentStateHash = data.live_state.state_hash;
                            }
                        }
                    })
                    .catch(() => {});
            }

            setInterval(pollLitLiveState, 4000);
            pollLitLiveState();

            // Keyboard Navigation on Literature Inventory Table
            function getRows() {
                return Array.from(document.querySelectorAll('#litInventoryTable tbody tr.inventory-row'));
            }

            function setFocusedRow(index) {
                const rows = getRows();
                rows.forEach(r => r.classList.remove('keyboard-focused-row'));
                if (rows.length === 0) return null;
                if (index < 0) index = 0;
                if (index >= rows.length) index = rows.length - 1;
                focusedRowIndex = index;
                const row = rows[focusedRowIndex];
                row.classList.add('keyboard-focused-row');
                row.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                return row;
            }

            document.addEventListener('click', function (e) {
                const row = e.target.closest('#litInventoryTable tbody tr.inventory-row');
                if (row) {
                    const rows = getRows();
                    const idx = rows.indexOf(row);
                    if (idx !== -1) {
                        rows.forEach(r => r.classList.remove('keyboard-focused-row'));
                        focusedRowIndex = idx;
                        row.classList.add('keyboard-focused-row');
                    }
                }
            });

            document.addEventListener('keydown', function (e) {
                const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
                const isInput = (activeTag === 'input' || activeTag === 'textarea' || activeTag === 'select');
                const anyModal = document.querySelector('.modal.show') !== null;

                if (isInput && e.key === 'Escape' && document.activeElement.id === 'litSearchInput') {
                    document.activeElement.blur();
                    return;
                }
                if (isInput || anyModal || e.ctrlKey || e.metaKey || e.altKey) return;

                const key = e.key;
                const lowerKey = key.toLowerCase();

                if (key === '?' || (e.shiftKey && key === '/')) {
                    e.preventDefault();
                    const helpEl = document.getElementById('keyboardShortcutsModal');
                    if (helpEl) bootstrap.Modal.getOrCreateInstance(helpEl).show();
                    return;
                }
                if (key === '/') {
                    e.preventDefault();
                    const s = document.getElementById('litSearchInput');
                    if (s) { s.focus(); s.select(); }
                    return;
                }
                if (key === 'ArrowDown' || lowerKey === 'j') {
                    e.preventDefault();
                    setFocusedRow(focusedRowIndex + 1);
                    return;
                }
                if (key === 'ArrowUp' || lowerKey === 'k') {
                    e.preventDefault();
                    setFocusedRow(focusedRowIndex <= 0 ? 0 : focusedRowIndex - 1);
                    return;
                }
                if (lowerKey === 'd') {
                    const rows = getRows();
                    if (rows.length === 0) return;
                    const row = (focusedRowIndex >= 0 && focusedRowIndex < rows.length) ? rows[focusedRowIndex] : setFocusedRow(0);
                    if (row) {
                        e.preventDefault();
                        const btn = row.querySelector('.btn-lit-damage');
                        if (btn) btn.click();
                    }
                }
            });

            function showToast(message, type) {
                let toastContainer = document.getElementById('litToastContainer');
                if (!toastContainer) {
                    toastContainer = document.createElement('div');
                    toastContainer.id = 'litToastContainer';
                    toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
                    toastContainer.style.zIndex = '1100';
                    document.body.appendChild(toastContainer);
                }
                const toastEl = document.createElement('div');
                toastEl.className = `toast align-items-center text-bg-${type} border-0 show mb-2`;
                toastEl.setAttribute('role', 'alert');
                toastEl.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body fw-semibold">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                `;
                toastContainer.appendChild(toastEl);
                setTimeout(() => toastEl.remove(), 3500);
            }
        });
    </script>
</x-layout>
