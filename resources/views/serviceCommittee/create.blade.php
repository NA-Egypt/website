<x-layout>

    <x-backhead>{{__('messages.Add') . ' ' . __('messages.Service Committees')}}</x-backhead>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('serviceCommittee.store') }}" method="post" enctype="multipart/form-data" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf
            <div class="row mx-0 px-0 g-3">
                <div class="col-md-6 pe-1">
                    <x-forms.input name="ar_name" label="{{ __('messages.Arabic Service Committee Name') }}"/>
                </div>
                <div class="col-md-6 ps-1">
                    <x-forms.input name="en_name" label="{{ __('messages.English Service Committee Name') }}"/>
                </div>
            </div>

            <x-forms.select :$users name="email" label="{{ __('messages.Email')}}"/>
            <x-forms.input name="chairman_name" label="{{ __('messages.Chairman Name')}}"/>
            <x-forms.input name="chairman_phone" label="{{ __('messages.Chairman Phone')}}"/>

            <x-forms.input id="location" name="location" label="{{ __('messages.Locations')}}"/>
            <x-forms.input id="ar_address" name="ar_address" label="{{ __('messages.Arabic Address')}}"/>
            <x-forms.input id="en_address" name="en_address" label="{{ __('messages.English Address')}}"/>
            <x-forms.input id="notes" name="notes" label="{{ __('messages.Committee Meetings')}}"/>

            <div class="mb-3">
                <label for="logo" class="form-label fw-bold">{{ __('messages.Committee Logo') ?? 'Committee Logo' }}</label>
                <input type="file" name="logo" id="logo" class="form-control" accept="image/png, image/jpeg, image/jpg">
                <div class="form-text text-muted">{{ __('messages.Allowed types: PNG, JPG, JPEG. Max size 2MB.') ?? 'Allowed types: PNG, JPG, JPEG. Max size 2MB.' }}</div>
            </div>

            <div class="mb-3">
                <x-forms.textarea name="default_footer" label="{{ __('messages.Default Report Footer') ?? 'Default Report Footer' }}"/>
            </div>

            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Save') }}" />

        </form>
    </div>

</x-layout>