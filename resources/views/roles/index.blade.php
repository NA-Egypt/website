<x-layout>

    <x-backhead>{{__('messages.Manage Roles')}}</x-backhead>

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
        <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
            <table class="main-tables manage-member text-center table table-bordered display" id="example">
                <thead>
                    <tr>
                        <th>{{__('messages.Role Name')}}</th>
                        <th>{{__('messages.Permission Name')}}</th>
                        <th>{{__('messages.Control')}}</th>
                    </tr>
                </thead>
                <tbody>

                @foreach ($roles as $role)
                    <tr>
                        {{-- <td>{{ $role->id }}</td> --}}
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
                </tbody>
            </table>
        </div>
    </div>
</x-layout>