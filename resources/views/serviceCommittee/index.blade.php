<x-layout>

    <x-backhead>{{__('messages.Manage') . ' ' . __('messages.Service Committees')}}</x-backhead>

    <div class="container">

        @php
        $columns = [
            ['field' => 'ar_name', 'title' => __('messages.Arabic Service Committee Name'), 'sort' => true],
            ['field' => 'email', 'title' => __('messages.Email'), 'sort' => true],
            ['field' => 'notes', 'title' => __('messages.Committee Meetings'), 'sort' => false],
            ['field' => 'actions', 'title' => __('messages.Control'), 'sort' => false]
        ];
        @endphp

        <div data-vue-app="GenericDataTable"
             data-fetch-url="{{ route('serviceCommittee.index') }}"
             data-columns="{{ json_encode($columns) }}"
             data-create-route="{{ route('serviceCommittee.create') }}"
             data-create-label="{{ __('messages.Add') . ' ' . __('messages.Service Committees') }}"
             data-edit-route-template="{{ str_replace('1', '{id}', route('serviceCommittee.edit', ['serviceCommittee' => 1])) }}"
             data-show-route-template="{{ str_replace('1', '{id}', route('serviceCommittee.show', ['serviceCommittee' => 1])) }}"
             data-delete-route-name="serviceCommittee.destroy"
             data-delete-route-template="{{ str_replace('1', '{id}', route('serviceCommittee.destroy', ['serviceCommittee' => 1])) }}">
        </div>
        {{-- {{$group->links()}} --}}
    </div>

</x-layout>