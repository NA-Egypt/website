<x-layout>

    <x-backhead>{{__('messages.Manage') . ' ' . __('messages.Meetings')}}</x-backhead>

    <div class="container glass-card p-4 mt-4 mb-5">

        @php
        $columns = [
            ['field' => 'group_name', 'title' => __('messages.Group Name'), 'sort' => true],
            ['field' => 'topic_name', 'title' => __('messages.Meeting Topic'), 'sort' => true],
            ['field' => 'day_name', 'title' => __('messages.Day'), 'sort' => true],
            ['field' => 'from_time', 'title' => __('messages.From'), 'sort' => true],
            ['field' => 'to_time', 'title' => __('messages.To'), 'sort' => true],
            ['field' => 'status_label', 'title' => __('messages.Status'), 'sort' => true],
            ['field' => 'actions', 'title' => __('messages.Control'), 'sort' => false]
        ];
        @endphp

        <div data-vue-app="GenericDataTable"
             data-fetch-url="{{ route('meeting.index') }}"
             data-columns="{{ json_encode($columns) }}"
             data-create-route="{{ route('meeting.create') }}"
             data-create-label="{{ __('messages.Add') . ' ' . __('messages.Meeting') }}"
             data-edit-route-template="{{ str_replace('1', '{id}', route('meeting.edit', ['meeting' => 1])) }}"
             data-delete-route-name="meeting.destroy"
             data-delete-route-template="{{ str_replace('1', '{id}', route('meeting.destroy', ['meeting' => 1])) }}">
        </div>
    </div>

</x-layout>