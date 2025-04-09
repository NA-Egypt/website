<x-layout>

    <x-section-head>{{__('messages.Edit') . ' ' . __('messages.Service Body')}}</x-section-head>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('serviceBody.update', $serviceBody->id) }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf
            @method('PUT')
            <x-forms.input name="ar_name" label="{{ __('messages.Service Body Arabic Name')}}" value="{{ $serviceBody->ar_name }}"/>
            <x-forms.input name="en_name" label="{{ __('messages.Service Body English Name')}}" value="{{ $serviceBody->en_name }}"/>
            <x-forms.textarea name="description" label="{{ __('messages.Description')}}" value="{{ $serviceBody->description }}"/>
            <x-forms.select :$days name="day_id" label="{{ __('messages.Day')}}" value="{{ $serviceBody->day_id }}"/>
            <div class="row align-items-end">
                <div class="col-md-4">
                    <x-forms.input name="date" label="{{ __('messages.Date')}}" type="date" value="{{ $serviceBody->date }}" />
                </div>
                <div class="col-md-4">
                    <x-forms.input name="start_time" label="{{ __('messages.From')}}" type="time" value="{{ $serviceBody->start_time }}"/>
                </div>
                <div class="col-md-4">
                    <x-forms.input name="end_time" label="{{ __('messages.To')}}" type="time" value="{{ $serviceBody->end_time }}"/>
                </div>
            </div>
            <x-forms.input name="location" label="{{ __('messages.Location')}}" value="{{ $serviceBody->location }}"/>
            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Update') }}" />

        </form>
    </div>

</x-layout>