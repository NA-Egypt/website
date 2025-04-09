<x-layout>
    
    <x-section-head>{{__('messages.Add') . ' ' . __('messages.Topic')}}</x-section-head>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('topic.store') }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf

            <x-forms.input name="title" label="{{ __('messages.Topic Title')}}"/>
            <x-forms.textarea name="description" label="{{ __('messages.Topic Description')}}"/>
            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Save') }}" />

        </form>
    </div>

</x-layout>