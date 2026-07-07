<x-layout>
    <x-backhead>{{__('messages.Add') . ' ' . __('messages.Group')}} ({{__('messages.legend_online')}})</x-backhead>

    <div class="container d-flex justify-content-center align-items-center mb-5 mt-4">
        <form action="{{ route('direct-online-group.store') }}" method="post" class="row g-3 col-md-12 col-lg-8 mt-1 glass-card p-4">
            @csrf
            
            <div class="row mx-0 px-0 g-3">
                <div class="col-md-6">
                    <x-forms.input name="ar_name" label="{{ __('messages.Arabic Group Name') }}"/>
                </div>
                <div class="col-md-6">
                    <x-forms.input name="en_name" label="{{ __('messages.English Group Name') }}"/>
                </div>
            </div>

            <div class="row mx-0 px-0 g-3">
                <div class="col-md-6">
                    <x-forms.input name="ar_gsr_name" label="{!! __('messages.Arabic GSR Name') . ' <small class=\'text-success\' style=\'font-size: 0.75rem;\'>(' . __('messages.optional') . ')</small>' !!}"/>
                </div>
                <div class="col-md-6">
                    <x-forms.input name="en_gsr_name" label="{!! __('messages.English GSR Name') . ' <small class=\'text-success\' style=\'font-size: 0.75rem;\'>(' . __('messages.optional') . ')</small>' !!}"/>
                </div>
            </div>

            <div class="row mx-0 px-0 g-3">
                <div class="col-md-12">
                    <x-forms.input name="phone" label="{!! __('messages.Phone') . ' <small class=\'text-success\' style=\'font-size: 0.75rem;\'>(' . __('messages.optional') . ')</small>' !!}"/>
                </div>
            </div>

            <div class="row mx-0 px-0 g-3 align-items-end">
                <div class="col-md-12">
                    <x-forms.input name="location" label="Zoom Link"/>
                </div>
            </div>

            <div class="col-12 mt-4">
                <x-forms.normal-button color='outline-dark' name="{{ __('messages.Save') }}" />
            </div>
        </form>
    </div>
</x-layout>
