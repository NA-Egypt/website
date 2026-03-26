<x-layout>
    
    <x-backhead>{{__('messages.Add') . ' ' . __('messages.Neighborhood')}}</x-backhead>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('neighborhood.store') }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf

            <x-forms.input name="ar_name" label="{{ __('messages.Neighborhood Arabic Name')}}"/>
            <x-forms.input name="en_name" label="{{ __('messages.Neighborhood English Name')}}"/>

            <x-forms.select :$cities name="city_id" label="{{ __('messages.City')}}"/>
            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Save') }}" />

        </form>
    </div>

</x-layout>