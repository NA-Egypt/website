<x-layout>
    
    <x-backhead>{{__('messages.Edit') . ' ' . __('messages.Topic')}}</x-backhead>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('topic.update', $topic->id) }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf
            @method('PUT')
            <x-forms.input name="ar_name" label="{{ __('messages.Topic Arabic Name')}}" value="{{ $topic->ar_name }}"/>
            <x-forms.input name="en_name" label="{{ __('messages.Topic English Name')}}" value="{{ $topic->en_name }}"/>
            <x-forms.textarea name="description" label="{{ __('messages.Notes')}}" value="{{ $topic->description }}" />
            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Update') }}" />

        </form>
    </div>

</x-layout>