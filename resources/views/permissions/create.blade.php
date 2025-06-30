<x-layout>

    <x-backhead>{{__("messages.Create New Permission")}}</x-backhead>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('permissions.store') }}" method="post" class="row g-2 col-md-12 col-lg-8">
            @csrf

            <x-forms.input name="name" label="{{__('messages.Permission Name')}}"/>
            <x-forms.input name="description" label="{{__('messages.Permission Description')}}"/>
            <x-forms.normal-button color='outline-dark' name='{{__("messages.Save")}}'/>

        </form>
    </div>


</x-layout>
