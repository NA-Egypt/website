<x-layout>

    <x-section-head>{{__('messages.Manage Roles')}}</x-section-head>

    <div class="container">

        <div class="d-flex justify-content-between align-items-center m-3">
            <x-button-a href="{{ route('roles.create') }}" color='outline-dark' name='{{__("messages.New Role")}}'/>
        </div>

{{--        @if (session('success'))--}}
{{--            <div>{{ session('success') }}</div>--}}
{{--        @endif--}}
{{--        <ul>--}}
{{--            @foreach ($roles as $role)--}}
{{--                <li>{{ $role->name }} - Permissions: {{ $role->permissions->pluck('name')->implode(', ') }}</li>--}}
{{--            @endforeach--}}
{{--        </ul>--}}
        <div class="table-responsive">
            <table class="main-table manage-member text-center table table-bordered">
                <tr>
                    <td>{{__("messages.#ID")}}</td>
                    <td>{{__('messages.Role Name')}}</td>
                    <td>{{__('messages.Permission Name')}}</td>
                    <td>{{__('messages.Control')}}</td>
                </tr>

                @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>{{ $role->name }}</td>
                        <td>
                            @foreach($role->permissions as $permission)
                                <span class="badge bg-primary">{{ $permission->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <span style="display: inline-block;">
                                <x-button-a href="{{ route('roles.assign-permissions', $role->id) }}"
                                          color='outline-warning'
                                          name='{{__("messages.Edit Permissions")}}'/>
                            </span>

                            <span style="display: inline-block;">
                                <x-forms.delete-button
                                        formName='delete-role' id="{{ $role->id }}"
                                        routeName="roles.destroy"
                                        name="{{__('messages.Delete')}}"/>
                            </span>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</x-layout>