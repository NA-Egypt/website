<x-layout>
    
    <x-section-head>{{__('messages.Edit') . ' ' . __('messages.Topic')}}</x-section-head>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('topic.update', $topic->id) }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf
            @method('PUT')
            <x-forms.input name="title" label="{{ __('messages.Topic Title')}}" value="{{ $topic->title }}" />
            <x-forms.textarea name="description" label="{{ __('messages.Topic Description')}}" value="{{ $topic->description }}" />
            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Update') }}" />

        </form>
    </div>

</x-layout>