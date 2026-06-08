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
    <form id="bulk-action-form" method="POST" action="{{ route('users.bulk_action') }}">
        @csrf
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <select name="action" class="form-select w-auto">
                    <option value="">{{ __('messages.Select Action') ?? 'Select Action' }}</option>
                    <option value="delete">{{ __('messages.Delete Selected') ?? 'Delete Selected' }}</option>
                </select>
                <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('messages.Are you sure?') ?? 'Are you sure?' }}')">
                    {{ __('messages.Apply') ?? 'Apply' }}
                </button>
            </div>
            <x-button-a href="{{ route('users.create') }}" color="primary" name="{{ __('messages.Add User') ?? 'Add User' }}" />
        </div>
    <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
        <table class="main-tables text-center table table-bordered display" id="example">
            <thead>

            <tr>
                <th style="width: 40px;"><input type="checkbox" id="select-all"></th>
                {{-- <th>{{ __('messages.#ID')}}</th> --}}
                <th>{{  __('messages.Display Name') }}</th>
                {{-- <th>{{  __('messages.Name') }}</th> --}}
                <th>{{  __('messages.Email') }}</th>
                {{-- <th>{{  __('messages.Type') }}</th> --}}
                <th>{{  __('messages.Roles') }}</th>
                <th>{{  __('messages.Service Body') }}</th>
                <th>{{  __('messages.Control') }}</th>
            </tr>

            </thead>

            <tbody>
            @foreach ($users as $user)

                <tr>
                    <td><input type="checkbox" name="user_ids[]" class="user-checkbox" value="{{$user->id}}"></td>
                    {{-- <td>{{ $user->id }}</td> --}}
                    <td>{{ $user->display_name }}</td>
                    {{-- <td>{{ $user->name }}</td> --}}
                    <td>{{ $user->email }}</td>
                    {{-- <td>{{ $user->type }}</td> --}}
                    <td>{{ $user->roles->pluck('name')->implode(', ') }}</td>
                    <td>{{ $user->serviceBody ? $user->serviceBody->{app()->getLocale() . '_name'} : '-' }}</td>
                    <td>
                        <x-button-a href="{{ route('users.edit', $user) }}" color='outline-info' name="{{  __('messages.Edit') }}" />
                        <x-forms.delete-button name="{{  __('messages.Delete') }}" formName='delete-user' id="{{$user->id}}" routeName="users.destroy" />
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    </form>

    <script>
        document.getElementById('select-all').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.user-checkbox');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });
    </script>
</x-layout>