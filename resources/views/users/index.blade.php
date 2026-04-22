<x-layout>
    <x-backhead>{{__('messages.Users')}}</x-backhead>
{{--    @if (session('success'))--}}
{{--        <div>{{ session('success') }}</div>--}}
{{--    @endif--}}
{{--    <ul>--}}
{{--        @foreach ($users as $user)--}}
{{--            <li>--}}
{{--                {{ $user->name }} - Roles: {{ $user->roles->pluck('name')->implode(', ') }}--}}
{{--                <a href="{{ route('users.edit', $user) }}">Edit Roles</a>--}}
{{--            </li>--}}
{{--        @endforeach--}}
{{--    </ul>--}}
    <div class="mb-3 text-end">
        <x-button-a href="{{ route('users.create') }}" color="primary" name="{{ __('messages.Add User') ?? 'Add User' }}" />
    </div>
    <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
        <table class="main-tables text-center table table-bordered display" id="example">
            <thead>

            <tr>
                {{-- <th>{{ __('messages.#ID')}}</th> --}}
                <th>{{  __('messages.Display Name') }}</th>
                {{-- <th>{{  __('messages.Name') }}</th> --}}
                <th>{{  __('messages.Email') }}</th>
                {{-- <th>{{  __('messages.Type') }}</th> --}}
                <th>{{  __('messages.Roles') }}</th>
                <th>{{  __('messages.Control') }}</th>
            </tr>

            </thead>

            <tbody>
            @foreach ($users as $user)

                <tr>
                    {{-- <td>{{ $user->id }}</td> --}}
                    <td>{{ $user->display_name }}</td>
                    {{-- <td>{{ $user->name }}</td> --}}
                    <td>{{ $user->email }}</td>
                    {{-- <td>{{ $user->type }}</td> --}}
                    <td>{{ $user->roles->pluck('name')->implode(', ') }}</td>
                    <td>
                        <x-button-a href="{{ route('users.edit', $user) }}" color='outline-info' name="{{  __('messages.Edit') }}" />
                        <x-forms.delete-button name="{{  __('messages.Delete') }}" formName='delete-user' id="{{$user->id}}" routeName="users.destroy" />
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-layout>