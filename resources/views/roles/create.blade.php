<x-layout>

    <x-backhead>{{__('messages.Create New Roles')}}</x-backhead>
{{--    <form method="POST" action="{{ route('roles.store') }}">--}}
{{--        @csrf--}}
{{--        <div>--}}
{{--            <label>Name:</label>--}}
{{--            <input type="text" name="name" required>--}}
{{--        </div>--}}
{{--        <div>--}}
{{--            <label>Description:</label>--}}
{{--            <textarea name="description"></textarea>--}}
{{--        </div>--}}
{{--        <div>--}}
{{--            <label>Permissions:</label>--}}
{{--            @foreach ($permissions as $permission)--}}
{{--                <div>--}}
{{--                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}">--}}
{{--                    <label>{{ $permission->name }}</label>--}}
{{--                </div>--}}
{{--            @endforeach--}}
{{--        </div>--}}
{{--        <button type="submit">Create</button>--}}
{{--    </form>--}}
    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('roles.store') }}" method="post" class="row g-2 col-md-12 col-lg-8">
            @csrf

            <x-forms.input name="name" label="{{__('messages.Role Name')}}" id="name"/>
            <x-forms.input name="description" label="{{__('messages.Role Description')}}" id="name"/>

            <div class="mb-3">
                <div class="d-flex align-items-center mb-2">
                    <span
                            style="width: 0.5rem; height: 0.5rem; background-color: white; display: inline-block; margin-inline-end: 0.5rem;"></span>
                    <label class="fw-bold " for="permissions">{{__('messages.Permissions')}}</label>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($permissions as $permission)
                        <input type="checkbox" class="btn-check perm-checkbox" id="perm_{{ $permission->id }}"
                               name="permissions[]"
                               value="{{ $permission->id }}">
                        <label class="btn btn-outline-primary" for="perm_{{ $permission->id }}">
                            {{ $permission->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <x-forms.normal-button color='outline-dark' name='{{__("messages.Save")}}'/>

        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".perm-checkbox").forEach(function (checkbox) {
                checkbox.addEventListener("change", function () {
                    this.blur(); // Fix delayed state update
                });
            });
        });
    </script>
</x-layout>