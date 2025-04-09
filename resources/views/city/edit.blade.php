<x-layout>
    
    <x-section-head>{{__('messages.Edit') . ' ' . __('messages.City')}}</x-section-head>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('city.update', $city->id) }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf
            @method('PUT')
{{--            <x-forms.input name="name" label="{{ __('messages.Name')}}" value="{{ $city->name }}"/>--}}
            <x-forms.input name="ar_name" label="{{ __('messages.City Arabic Name')}}" value="{{ $city->ar_name }}"/>
            <x-forms.input name="en_name" label="{{ __('messages.City English Name')}}" value="{{ $city->en_name }}"/>
            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Update') }}" />

        </form>
    </div>

</x-layout>