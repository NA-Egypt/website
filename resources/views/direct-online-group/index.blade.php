<x-layout>

    <x-backhead>{{__('messages.Manage') . ' ' . __('messages.Group')}} ({{__('messages.legend_online')}})</x-backhead>

    <div class="container glass-card p-4 mt-4 mb-5">

        @php
        $columns = [
            ['field' => 'ar_name', 'title' => __('messages.Arabic Group Name'), 'sort' => true],
            ['field' => 'en_name', 'title' => __('messages.English Group Name'), 'sort' => true],
            ['field' => 'email', 'title' => __('messages.Email'), 'sort' => false, 'renderType' => 'nested', 'fieldPath' => 'user.email'],
            ['field' => 'location', 'title' => __('messages.Locations') . ' (Zoom)', 'sort' => false],
            ['field' => 'actions', 'title' => __('messages.Control'), 'sort' => false]
        ];
        @endphp

        <div data-vue-app="GenericDataTable"
             data-fetch-url="{{ route('direct-online-group.index') }}"
             data-columns="{{ json_encode($columns) }}"
             data-create-route="{{ route('direct-online-group.create') }}"
             data-create-label="{{ __('messages.Add') . ' ' . __('messages.Group') }}"
             data-edit-route-template="{{ str_replace('1', '{id}', route('direct-online-group.edit', ['directOnlineGroup' => 1])) }}"
             data-show-route-template="{{ str_replace('1', '{id}', route('direct-online-group.show', ['directOnlineGroup' => 1])) }}"
             data-delete-route-name="direct-online-group.destroy"
             data-delete-route-template="{{ str_replace('1', '{id}', route('direct-online-group.destroy', ['directOnlineGroup' => 1])) }}">
        </div>
    </div>

</x-layout>
