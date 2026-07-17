<x-layout>

    <x-backhead>{{__('messages.Manage') . ' ' . __('messages.Topics')}}</x-backhead>

    <div class="container">

        @php
        $columns = [
            ['field' => 'ar_name', 'title' => __('messages.Topic Arabic Name'), 'sort' => true],
            ['field' => 'en_name', 'title' => __('messages.Topic English Name'), 'sort' => true],
            ['field' => 'actions', 'title' => __('messages.Control'), 'sort' => false]
        ];
        @endphp

        <div data-vue-app="GenericDataTable"
             data-fetch-url="{{ route('topic.index') }}"
             data-columns="{{ json_encode($columns) }}"
             data-create-route="{{ route('topic.create') }}"
             data-create-label="{{ __('messages.Add') . ' ' . __('messages.Topic') }}"
             data-edit-route-template="{{ str_replace('1', '{id}', route('topic.edit', ['topic' => 1])) }}"
             data-delete-route-name="topic.destroy"
             data-delete-route-template="{{ str_replace('1', '{id}', route('topic.destroy', ['topic' => 1])) }}">
        </div>
    </div>

</x-layout>