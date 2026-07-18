<x-layout>

    <x-backhead>{{__('messages.Manage') . ' ' . __('messages.Group')}}</x-backhead>

    <div class="container glass-card p-4 mt-4 mb-5">

        @php
        $columns = [
            ['field' => 'ar_name', 'title' => __('messages.Arabic Group Name'), 'sort' => true],
            ['field' => 'en_name', 'title' => __('messages.English Group Name'), 'sort' => true],
            ['field' => 'email', 'title' => __('messages.Email'), 'sort' => true, 'renderType' => 'nested', 'fieldPath' => 'user.email'],
            ['field' => 'service_body', 'title' => __('messages.Service Body Name'), 'sort' => true, 'renderType' => 'nested', 'fieldPath' => 'service_body.' . app()->getLocale() . '_name'],
            ['field' => 'neighborhood', 'title' => __('messages.Neighborhood Name'), 'sort' => true, 'renderType' => 'nested', 'fieldPath' => 'neighborhood.' . app()->getLocale() . '_name'],
            ['field' => 'actions', 'title' => __('messages.Control'), 'sort' => false]
        ];
        @endphp

        <div data-vue-app="GenericDataTable"
             data-fetch-url="{{ route('group.index') }}"
             data-columns="{{ json_encode($columns) }}"
             data-create-route="{{ route('group.create') }}"
             data-create-label="{{ __('messages.Add') . ' ' . __('messages.Group') }}"
             data-edit-route-template="{{ str_replace('1', '{id}', route('group.edit', ['group' => 1])) }}"
             data-show-route-template="{{ str_replace('1', '{id}', route('group.show', ['group' => 1])) }}"
             data-delete-route-name="group.destroy"
             data-delete-route-template="{{ str_replace('1', '{id}', route('group.destroy', ['group' => 1])) }}">
        </div>
    </div>

</x-layout>