<x-layout>
    <x-backhead>{{__('messages.Assign Permissions to')}} <span style="color: red">{{ $role->name }}</span>
    </x-backhead>
    <form action="{{ route('roles.update-permissions', $role->id) }}" method="POST">
        @csrf
        <div class="mb-2">
            <div class="d-flex align-items-center mb-3">
                <span
                    style="width: 0.5rem; height: 0.5rem; background-color: white; display: inline-block; margin-inline-end: 0.5rem;"></span>
                <label class="text-primary" for="permissions">{{__('messages.Permissions')}}</label>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($permissions as $permission)
                    <input type="checkbox" class="btn-check perm-checkbox" id="perm_{{ $permission->id }}"
                           name="permissions[]" value="{{ $permission->id }}"
                           {{ $role->permissions->contains($permission->id) ? 'checked' : '' }} autocomplete="off">
                    <label class="btn btn-outline-primary" for="perm_{{ $permission->id }}">
                        {{ $permission->name }}
                    </label>
                @endforeach
            </div>
        </div>
        <x-forms.normal-button color='outline-dark' name='{{__("messages.Update")}}'/>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".perm-checkbox").forEach(function (checkbox) {
                checkbox.addEventListener("click", function () {
                    this.blur(); // Force UI to refresh state immediately
                });
            });
        });
    </script>
</x-layout>
