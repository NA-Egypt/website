<x-layout>
    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h1 class="h2 text-gradient font-bold mb-0">
                    {{ __('messages.Edit') }} {{ __('messages.Literature Request') }}
                </h1>
                <p class="text-secondary mb-0">
                    Editing Accumulated Request of {{ $litRequest->serviceBody->{app()->getLocale() . '_name'} ?? $litRequest->serviceBody->en_name }} for {{ \App\Services\DateNumberHelper::translatedFormat($litRequest->month, 'F Y') }}
                </p>
            </div>
            <div>
                <a href="{{ route('literature-requests.committee') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i>
                    {{ __('messages.Back to List') }}
                </a>
            </div>
        </div>

        {{-- Form --}}
        <div class="card border-0 shadow-sm p-4">
            <form action="{{ route('literature-requests.committee.update', $litRequest->id) }}" method="POST">
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
                            @foreach($allItems as $item)
                                @php
                                    $existingItem = $litRequest->items->where('inventory_item_id', $item->id)->first();
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
                                               style="max-width: 100px; margin: 0 auto;">
                                    </td>
                                    <td class="text-end fw-bold row-total">
                                        {{ number_format($existingQty * $item->selling_price, 2) }} {{ __('messages.EGP') }}
                                    </td>
                                </tr>
                            @endforeach
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
                    <div>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm py-2">
                            <i class="bi bi-reply-all me-1"></i>
                            {{ __('messages.save_and_return_to_servicebody') }}
                        </button>
                    </div>
                </div>
            </form>
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
