<x-layout>

    <x-section-head>{{__('messages.Edit Permission')}}</x-section-head>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('permissions.update', $permission->id) }}" method="post"
              class="row g-2 col-md-12 col-lg-8">
            @csrf
            @method('PUT')
            <x-forms.input name="name" label="{{__('messages.Name')}}" value="{{ $permission->name }}"/>
            <x-forms.input name="description" label="{{__('messages.Description')}}" value="{{ $permission->description }}"/>
            <x-forms.normal-button color='outline-dark' name='{{__("messages.Update")}}'/>

        </form>
    </div>


</x-layout>
