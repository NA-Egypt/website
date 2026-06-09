<x-layout>
    
    <x-backhead>{{__('messages.Add') . ' ' . __('messages.Group')}}</x-backhead>

    <div class="container d-flex justify-content-center align-items-center mb-5 mt-4">
        <form action="{{ route('group.store') }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1 glass-card p-4">
            @csrf
            <div class="row mx-0 px-0 g-3">
                <div class="col-md-6 pe-1">
                    <x-forms.input name="ar_name" label="{{ __('messages.Arabic Group Name') }}"/>
                </div>
                <div class="col-md-6 ps-1">
                    <x-forms.input name="en_name" label="{{ __('messages.English Group Name') }}"/>
                </div>
            </div>
            <div class="row mx-0 px-0 g-3">
                <div class="col-md-6 pe-1">
                    <x-forms.input name="ar_gsr_name" label="{{ __('messages.Arabic GSR Name')}}"/>
                </div>
                <div class="col-md-6 ps-1">
                    <x-forms.input name="en_gsr_name" label="{{ __('messages.English GSR Name')}}"/>
                </div>
            </div>

{{--            <x-forms.input name="email" label="{{ __('messages.Email')}}"/>--}}
            <x-forms.select :$users name="user_id" label="{{ __('messages.Email')}}"/>
            <div class="row mx-0 px-0 g-3">
                <div class="col-md-6 pe-1">
                    <x-forms.input name="phone" label="{{ __('messages.Phone')}}"/>
                </div>
                <div class="col-md-6 ps-1">
                    <x-forms.input name="capacity" label="{{ __('messages.Capacity')}}" type="number"/>
                </div>
            </div>
            <div class="row align-items-end mx-1">
                <div class="form-check form-switch col-md-2">
                    <input type="hidden" name="group_type" value="فعلي"> <!-- Sends "فعلي" when unchecked -->
                    <input
                            name="group_type"
                            class="form-check-input"
                            type="checkbox"
                            id="group-type"
                            value="اونلاين"
                    {{ old('group_type', $group->type ?? 'فعلي') === 'اونلاين' ? 'checked' : '' }}
                    >

                    <label class="form-check-label" for="meeting-type" id="switcGrouphLabel">
                        {{ old('group_type', $group->type ?? 'فعلي') === 'اونلاين' ? 'اونلاين' : 'فعلي' }}
                    </label>
                </div>
            </div>
            <x-forms.input id="location" name="location" label="{{ __('messages.Locations')}}"/>
            <x-forms.input id="ar_address" name="ar_address" label="{{ __('messages.Arabic Address')}}"/>
            <x-forms.input id="en_address" name="en_address" label="{{ __('messages.English Address')}}"/>
            <x-forms.select :$serviceBodies name="service_body_id" label="{{ __('messages.Service Body')}}"/>
            <x-forms.select :$neighborhoods name="neighborhood_id" label="{{ __('messages.Neighborhood')}}"/>
            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Save') }}" />

        </form>
    </div>

</x-layout>