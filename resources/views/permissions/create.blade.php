<x-layout>
    <x-backhead>{{__("messages.Create New Permission")}}</x-backhead>

    <div class="container py-3 d-flex justify-content-center align-items-center">
        <form action="{{ route('permissions.store') }}" method="post" class="row g-3 col-md-12 col-lg-8">
            @csrf

            <div class="card border-0 shadow-lg p-4" style="background: var(--glass-bg); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid var(--glass-border); border-radius: 16px; width: 100%;">
                <div class="mb-3">
                    <x-forms.input name="name" label="{{__('messages.Permission Name')}}"/>
                </div>
                <div class="mb-3">
                    <x-forms.input name="description" label="{{__('messages.Permission Description')}}"/>
                </div>
                <div class="mt-4">
                    <x-forms.normal-button color='outline-primary' name='{{__("messages.Save")}}'/>
                </div>
            </div>
        </form>
    </div>
</x-layout>
