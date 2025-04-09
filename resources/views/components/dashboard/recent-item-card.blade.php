{{-- Recent transactions in dashboard --}}

@props(['recentTransactions'])

@foreach ($recentTransactions as $transaction)
  @if ($transaction['type'] === 'withdrawal')
    @foreach ($transaction['withdrawalTransactions'] as $wTransaction)        
      <tr>
          
        <td>{{ $transaction['id'] }}</td>
        <td>
          <div class="d-flex align-items-center gap-3">
            <div class="product-info">
              <h6 class="product-name mb-1">{{ $wTransaction->item->name }}</h6>
            </div>
          </div>
        </td>
        <td>{{ $wTransaction->withdrawal_qty }}</td>
        <td>{{ $transaction['location']}}</td>
        <td>{{ $transaction['created_at']->format('d-m-Y')}}</td>
        <td>
          <div class="d-flex align-items-center gap-3 fs-6 ">
            <i class="bi bi-arrow-down text-danger"></i>Withdrawal
          </div>
        </td>
      </tr>
    @endforeach

  @elseif ($transaction['type'] === 'purchase')
    @foreach ($transaction['purchaseTransaction'] as $pTransaction)
    <tr>
                    
      <td>{{ $transaction['id'] }}</td>
      <td>
        <div class="d-flex align-items-center gap-3">
          <div class="product-info">
            <h6 class="product-name mb-1">{{ $pTransaction->item->name }}</h6>
          </div>
        </div>
      </td>
      <td>{{ $pTransaction->purchase_qty }}</td>
      <td>{{ $transaction['supplier_name'] }}</td>
      <td>{{ $transaction['created_at']->format('d-m-Y')}}</td>
      <td>
        <div class="d-flex align-items-center gap-3 fs-6 ">
          <i class="bi bi-arrow-up text-success"></i>Purchase
        </div>
      </td>
    </tr>
    @endforeach
  @endif

@endforeach