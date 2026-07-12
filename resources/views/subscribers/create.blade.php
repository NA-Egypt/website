<x-layout>
    
    <x-backhead>{{ __('messages.Add Subscriber') ?? 'Add Subscriber' }}</x-backhead>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('subscribers.store_admin') }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf

            <x-forms.input name="email" type="email" label="{{ __('messages.Email') ?? 'Email' }}"/>
            
            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Save') }}" />

        </form>
    </div>

</x-layout>
