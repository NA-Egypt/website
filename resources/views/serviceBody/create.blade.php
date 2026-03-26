<x-layout>
    
    <x-backhead>{{__('messages.Add') . ' ' . __('messages.Service Body')}}</x-backhead>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('serviceBody.store') }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf

            <x-forms.input name="ar_name" label="{{ __('messages.Service Body Arabic Name')}}"/>
            <x-forms.input name="en_name" label="{{ __('messages.Service Body English Name')}}"/>
            <x-forms.textarea name="description" label="{{ __('messages.Description')}}"/>
            <x-forms.select :$days name="day_id" label="{{ __('messages.Day')}}"/>
            <div class="row align-items-end">
                <div class="col-md-4">
                    <x-forms.input name="date" label="{{ __('messages.Date')}}" type="date" />
                </div>
                <div class="col-md-4">
                    <x-forms.input name="start_time" label="{{ __('messages.From')}}" type="time" />
                </div>
                <div class="col-md-4">
                    <x-forms.input name="end_time" label="{{ __('messages.To')}}" type="time" />
                </div>
            </div>
            <x-forms.input name="location" label="{{ __('messages.Location')}}"/>
            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Save') }}" />

        </form>
    </div>

</x-layout>