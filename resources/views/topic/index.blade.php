<x-layout>
    <x-backhead>{{ __('messages.Manage') . ' ' . __('messages.Topics') }}</x-backhead>

    <div class="container-fluid py-4 px-md-5">
        @php
            $labels = [
                'totalTopics' => __('messages.Total Topics'),
                'linkedMeetings' => __('messages.Linked Meetings'),
                'standardTopics' => __('messages.Standard Topics'),
                'topicNameAr' => __('messages.Topic Name (AR)'),
                'topicNameEn' => __('messages.Topic Name (EN)'),
                'searchPlaceholder' => __('messages.Search topics...'),
                'clearFilters' => __('messages.Clear Filters'),
                'hasMeetings' => __('messages.Has Meetings'),
                'withMeetings' => __('messages.With Meetings'),
                'withoutMeetings' => __('messages.Without Meetings'),
                'actions' => __('messages.actions'),
                'quickView' => __('messages.Quick View'),
                'topicDetails' => __('messages.Topic Details'),
                'edit' => __('messages.edit'),
                'delete' => __('messages.delete'),
                'close' => __('messages.Close'),
                'showing' => __('messages.showing'),
                'to' => __('messages.to'),
                'of' => __('messages.of'),
                'entries' => __('messages.entries'),
                'first' => __('messages.first'),
                'prev' => __('messages.prev'),
                'next' => __('messages.next'),
                'last' => __('messages.Last'),
                'noRecords' => __('messages.No matching records found'),
                'tryAdjusting' => __('messages.Try adjusting your search or filters'),
                'confirmDelete' => __('messages.Confirm Delete Topic'),
                'deletedSuccess' => __('messages.topic_deleted_success'),
            ];
        @endphp

        <div data-vue-app="TopicsDataTable"
             data-fetch-url="{{ route('topic.index') }}"
             data-create-route="{{ route('topic.create') }}"
             data-create-label="{{ __('messages.Add') . ' ' . __('messages.Topic') }}"
             data-edit-route-template="{{ route('topic.edit', ['topic' => '__ID__']) }}"
             data-delete-route-template="{{ route('topic.destroy', ['topic' => '__ID__']) }}"
             data-kpi-stats="{{ json_encode($kpiStats ?? []) }}"
             data-labels="{{ json_encode($labels) }}"
             data-csrf-token="{{ csrf_token() }}">
        </div>
    </div>
</x-layout>