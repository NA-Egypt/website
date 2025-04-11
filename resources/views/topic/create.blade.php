<x-layout>
    
    <x-section-head>{{__('messages.Add') . ' ' . __('messages.Topic')}}</x-section-head>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('topic.store') }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf

            <x-forms.input name="ar_name" label="{{ __('messages.Topic Arabic Name')}}"/>
            <x-forms.input name="en_name" label="{{ __('messages.Topic English Name')}}"/>
            <x-forms.textarea name="description" label="{{ __('messages.Notes')}}"/>
            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Save') }}" />

        </form>
    </div>

</x-layout>