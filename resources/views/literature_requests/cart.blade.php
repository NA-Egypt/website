<x-layout>
    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h1 class="h2 text-gradient font-bold mb-0">
                    {{ __('messages.Literature Request') }}
                </h1>
                <p class="text-secondary mb-0">
                    {{ __('messages.Literature Request for :month', ['month' => \App\Services\DateNumberHelper::translatedFormat($month, 'F Y')]) }}
                </p>
            </div>
            <div>
                <a href="{{ route('literature-requests.archive') }}" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-archive me-1"></i>
                    {{ __('messages.literature_requests_archive') }}
                </a>
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

        @if($isLocked)
            <div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4" role="alert">
                <i class="bi bi-lock-fill me-2"></i>
                {{ __('messages.locked_after_19th') }}
            </div>
        @endif

        {{-- Current Request Status --}}
        @if($request)
            <div class="card mb-4 border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-3" style="color: var(--text-primary);">
                    {{ __('messages.status') ?? 'Status' }}: 
                    @php
                        $badgeColor = 'bg-secondary';
                        if ($request->status === 'submitted') $badgeColor = 'bg-info';
                        elseif ($request->status === 'sent_to_committee') $badgeColor = 'bg-primary';
                        elseif ($request->status === 'returned_by_committee') $badgeColor = 'bg-success';
                    @endphp
                    <span class="badge {{ $badgeColor }} rounded-pill px-3 py-2">
                        {{ strtoupper($request->status) }}
                    </span>
                </h5>
                <p class="mb-1 text-secondary">
                    {{ __('messages.total_unique_items') ?? 'Total Items' }}: <strong>{{ $request->total_items_count }}</strong>
                </p>
                <p class="mb-0 text-secondary">
                    {{ __('messages.total_stock_value') ?? 'Total Price' }}: <strong>{{ $request->total_price }} {{ __('messages.EGP') }}</strong>
                </p>
            </div>
        @endif

        {{-- Cart Form --}}
        <div class="card border-0 shadow-sm p-4">
            <form action="{{ route('literature-requests.cart.update') }}" method="POST" id="cart-form">
                @csrf
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.item_name') }}</th>
                                <th>{{ __('messages.Category') }}</th>
                                <th class="text-end">{{ __('messages.selling_price') }}</th>
                                <th class="text-center" style="width: 150px;">{{ __('messages.quantity') }}</th>
                                <th class="text-end">{{ __('messages.total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                @php
                                    $existingItem = $request ? $request->items->where('inventory_item_id', $item->id)->first() : null;
                                    $existingQty = $existingItem ? $existingItem->quantity : 0;
                                @endphp
                                <tr class="item-row" data-price="{{ $item->selling_price }}">
                                    <td>
                                        <div class="fw-bold">{{ $item->name }}</div>
                                        <small class="text-muted">{{ $item->description }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ __('messages.cat_' . Str::snake(str_replace(' ', '_', $item->category))) }}
                                        </span>
                                    </td>
                                    <td class="text-end price-value">{{ $item->selling_price }} {{ __('messages.EGP') }}</td>
                                    <td class="text-center">
                                        <input type="number" 
                                               name="quantities[{{ $item->id }}]" 
                                               value="{{ $existingQty }}" 
                                               min="0" 
                                               class="form-control text-center rounded-3 quantity-input"
                                               {{ $isLocked ? 'disabled' : '' }}
                                               style="max-width: 100px; margin: 0 auto;">
                                    </td>
                                    <td class="text-end fw-bold row-total">
                                        {{ number_format($existingQty * $item->selling_price, 2) }} {{ __('messages.EGP') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-basket3 fs-2 d-block mb-2"></i>
                                        {{ __('messages.no_available_items') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Summary Footer --}}
                <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-4 flex-wrap gap-3">
                    <div>
                        <h4 class="mb-0 text-secondary">
                            {{ __('messages.total') }}: <span id="cart-total-price" class="text-primary fw-bold">0.00</span> {{ __('messages.EGP') }}
                        </h4>
                    </div>
                    @if(!$isLocked)
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-secondary rounded-pill px-4 shadow-sm">
                                <i class="bi bi-save me-1"></i>
                                {{ __('messages.save') ?? 'Save Draft' }}
                            </button>
                        </form>
                        
                        @if($request && $request->items->count() > 0)
                            <form action="{{ route('literature-requests.submit') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-send me-1"></i>
                                    {{ __('messages.submit_request') }}
                                </button>
                            </form>
                        @endif
                        </div>
                    @endif
                </div>
        </div>
    </div>

    {{-- Simple Reactive calculation script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qtyInputs = document.querySelectorAll('.quantity-input');
            const currencyText = "{{ __('messages.EGP') }}";

            function recalculateCart() {
                let overallTotal = 0;
                document.querySelectorAll('.item-row').forEach(row => {
                    const price = parseFloat(row.getAttribute('data-price'));
                    const qty = parseInt(row.querySelector('.quantity-input').value) || 0;
                    const rowTotal = price * qty;
                    row.querySelector('.row-total').innerText = rowTotal.toFixed(2) + ' ' + currencyText;
                    overallTotal += rowTotal;
                });
                document.getElementById('cart-total-price').innerText = overallTotal.toFixed(2);
            }

            qtyInputs.forEach(input => {
                input.addEventListener('input', recalculateCart);
                input.addEventListener('change', recalculateCart);
            });

            recalculateCart();
        });
    </script>
</x-layout>
