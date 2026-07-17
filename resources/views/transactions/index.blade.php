<x-layout>

    <x-backhead>{{ __('messages.Logs')}}</x-backhead>

    <div class="container my-4" 
         data-vue-app="TransactionsTable" 
         data-fetch-url="{{ route('transactions.index') }}" 
         data-available-models="{{ json_encode($availableModels) }}" 
         data-available-operations="{{ json_encode($availableOperations) }}">
    </div>

</x-layout>