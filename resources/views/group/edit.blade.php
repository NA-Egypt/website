<x-layout>
    
    <x-section-head>{{__('messages.Edit') . ' ' . __('messages.Group')}}</x-section-head>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('group.update', $group->id) }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf
            @method('PUT')
            <div class="row mx-0 px-0 g-3">
                <div class="col-md-6 pe-1">
                    <x-forms.input name="ar_name" label="{{ __('messages.Arabic Group Name') }}" value="{{ $group->ar_name }}"/>
                </div>
                <div class="col-md-6 ps-1">
                    <x-forms.input name="en_name" label="{{ __('messages.English Group Name') }}" value="{{ $group->en_name }}"/>
                </div>
            </div>
            <div class="row mx-0 px-0 g-3">
                <div class="col-md-6 pe-1">
                    <x-forms.input name="ar_gsr_name" label="{{ __('messages.Arabic GSR Name')}}" value="{{ $group->ar_gsr_name }}"/>
                </div>
                <div class="col-md-6 ps-1">
                    <x-forms.input name="en_gsr_name" label="{{ __('messages.English GSR Name')}}" value="{{ $group->en_gsr_name }}"/>
                </div>
            </div>

{{--            <x-forms.input name="email" label="{{ __('messages.Email')}}" value="{{ $group->email }}"/>--}}
            <x-forms.select :$users name="user_id" label="{{ __('messages.Email')}}" value="{{ $group->user_id }}"/>
            <x-forms.input name="phone" label="{{ __('messages.Phone')}}" value="{{ $group->phone }}"/>
            <div class="form-check form-switch col-md-2">
                <input type="hidden" name="group_type" value="فعلي"> <!-- Sends "فعلي" when unchecked -->
                <input
                        name="group_type"
                        class="form-check-input"
                        type="checkbox"
                        id="group-type"
                        value="اون لاين"
                {{ old('group_type', $group->group_type ?? 'فعلي') === 'اون لاين' ? 'checked' : '' }} <!-- Ensure the checkbox reflects the value -->
                >

                <label class="form-check-label" for="group-type" id="switcGrouphLabel">
                    {{ old('group_type', $group->group_type ?? 'فعلي') === 'اون لاين' ? 'اون لاين' : 'فعلي' }}
                </label>
            </div>

            @if($group->group_type === 'اون لاين')
                <x-forms.input id="location" name="location" label="URL" value="{{ $group->location }}"/>
            @else
                <x-forms.input id="location" name="location" label="{{ __('messages.Arabic Address') }}" value="{{ $group->location }}"/>
            @endif
            <x-forms.input id="ar_address" name="ar_address" label="{{ __('messages.Arabic Address')}}" value="{{ $group->ar_address }}"/>
            <x-forms.input id="en_address" name="en_address" label="{{ __('messages.English Address')}}" value="{{ $group->en_address }}"/>
            <x-forms.select :$serviceBodies name="service_body_id" label="{{ __('messages.Service Body')}}" value="{{ $group->service_body_id }}"/>
            <x-forms.select :$neighborhoods name="neighborhood_id" label="{{ __('messages.Neighborhood')}}" value="{{ $group->neighborhood_id }}"/>
            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Update') }}" />

        </form>
    </div>

</x-layout>