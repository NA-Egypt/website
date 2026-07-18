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
    @php
    $columns = [
        ['field' => 'id', 'title' => 'ID', 'sort' => true, 'isUnique' => true, 'hide' => true],
        ['field' => 'display_name', 'title' => __('messages.Display Name'), 'sort' => true],
        ['field' => 'email', 'title' => __('messages.Email'), 'sort' => true],
        ['field' => 'roles', 'title' => __('messages.Roles'), 'sort' => false, 'renderType' => 'array', 'fieldPath' => 'roles', 'arrayKey' => 'name'],
        ['field' => 'service_body', 'title' => __('messages.Service Body'), 'sort' => true, 'renderType' => 'nested', 'fieldPath' => 'service_body.' . app()->getLocale() . '_name'],
        ['field' => 'actions', 'title' => __('messages.Control'), 'sort' => false]
    ];
    $bulkActions = [
        ['value' => 'delete', 'label' => __('messages.Delete Selected') ?? 'Delete Selected']
    ];
    @endphp

    <div data-vue-app="GenericDataTable"
         data-fetch-url="{{ route('users.index') }}"
         data-columns="{{ json_encode($columns) }}"
         data-create-route="{{ route('users.create') }}"
         data-create-label="{{ __('messages.Add User') ?? 'Add User' }}"
         data-bulk-action-route="{{ route('users.bulk_action') }}"
         data-bulk-actions="{{ json_encode($bulkActions) }}"
         data-bulk-ids-name="user_ids[]"
         data-edit-route-template="{{ str_replace('1', '{id}', route('users.edit', ['user' => 1])) }}"
         data-delete-route-name="users.destroy"
         data-delete-route-template="{{ str_replace('1', '{id}', route('users.destroy', ['user' => 1])) }}">
    </div>
</x-layout>