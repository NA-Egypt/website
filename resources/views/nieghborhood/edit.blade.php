<x-layout>
    
    <x-backhead>{{__('messages.Edit') . ' ' . __('messages.Neighborhood')}}</x-backhead>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('neighborhood.update', $neighborhood->id) }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf
            @method('PUT')
            <x-forms.input name="en_name" label="{{ __('messages.Neighborhood English Name')}}"  value="{{ $neighborhood->en_name }}"/>
            <x-forms.input name="ar_name" label="{{ __('messages.Neighborhood Arabic Name')}}"  value="{{ $neighborhood->ar_name }}"/>
            <x-forms.input name="latitude" label="{{ __('Latitude') }}" value="{{ $neighborhood->latitude }}"/>
            <x-forms.input name="longitude" label="{{ __('Longitude') }}" value="{{ $neighborhood->longitude }}"/>
            <x-forms.select :$cities name="city_id" label="{{ __('messages.City')}}" value="{{ $neighborhood->city_id }}"/>
            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Update') }}" />

        </form>
    </div>

</x-layout>