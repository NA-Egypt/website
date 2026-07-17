<x-layout>
    
    <x-backhead>{{__('messages.Add') . ' ' . __('messages.City')}}</x-backhead>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('city.store') }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf

            <x-forms.input name="ar_name" label="{{ __('messages.City Arabic Name')}}"/>
            <x-forms.input name="en_name" label="{{ __('messages.City English Name')}}"/>
            <x-forms.input name="latitude" label="{{ __('Latitude') }}"/>
            <x-forms.input name="longitude" label="{{ __('Longitude') }}"/>
            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Save') }}" />

        </form>
    </div>

</x-layout>