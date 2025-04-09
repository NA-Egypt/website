<x-layout>

    <x-section-head>{{ __('messages.Logs')}}</x-section-head>

    <div class="container">

        <div class="table-responsive">
            <table class="main-table manage-member text-center table table-bordered">
               
                <tr>
                    <td>#{{ __('messages.ID')}}</td>
                    <td>{{  __('messages.Operation') }}</td>
                    <td>{{  __('messages.Model') }}</td>
                    <td>{{  __('messages.User') }}</td>
                    <td>{{  __('messages.Date') }}</td>
                    <td>{{  __('messages.Time') }}</td>
                    <td>{{  __('messages.Name') }}</td>
                </tr>
                
                
                @foreach ($transactions as $trans)                    
                    <tr>
                        <td>{{ $trans->id }}</td>
                        <td>{{ ucfirst($trans->operation) }}</td>
                        <td>{{ $trans->model }}</td>
                        <td>{{ $trans->user->name ?? 'System' }}</td>
                        <td>{{ $trans->created_at->format('Y-m-d') }}</td>
                        <td>{{ $trans->created_at->format('H:i:s') }}</td>
                        <td>
                            {{-- <x-forms.normal-button name='Show' color='outline-info' class="transaction-row" data-transaction-id="{{ $trans->id }}" /> --}}

                            @if ($trans->model === 'Meeting')
                                {{ $trans->group_name }}
                            @else
                                {{ $trans->details['name'] }}
                            @endif
                        </td>
                    </tr>
                    {{-- Show All Rows Of Transaction Details Using Button --}}
                    {{-- <tr class="transaction-row" id="transactions-{{ $trans->id }}" style="display: none;">
                        <td colspan="7">
                            <div class="transaction-details w-50">
                                <small>{{ __('messages.Logs Details')}}</small>
                                <table class="sub-row-table manage-member text-center table table-bordered">
                                   
                                    
                                    @foreach ($trans->details as $key => $value)
                                        <tr>
                                            <td>{{ ucfirst($key) }}</td>
                                            <td>
                                                @if (is_array($value))
                                                    {{ json_encode($value, JSON_PRETTY_PRINT) }}
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        @if (isset($trans->details['name']))
                                            <tr>
                                                <td>Name</td>
                                                <td>{{ $trans->details['name'] }}</td>
                                            </tr>
                                        @endif
                                </table>
                            </div>
                        </td>
                    </tr> --}}
                @endforeach
                
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $transactions->links() }}
        </div>
    </div>

</x-layout>