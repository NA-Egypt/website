<x-layout>
    <x-backhead>{{ __('messages.Manage') . ' ' . __('messages.Meetings') }}</x-backhead>

    @php
    $labels = [
        'totalMeetings'      => __('messages.Total Meetings'),
        'inPersonMeetings'   => __('messages.In-Person Meetings'),
        'onlineMeetings'     => __('messages.Online Meetings'),
        'activeMeetings'     => __('messages.Active Meetings'),
        'searchPlaceholder'  => __('messages.Search by name, topic, location...'),
        'allDays'            => __('messages.All Days'),
        'allTypes'           => __('messages.All Types'),
        'allStatuses'        => __('messages.All Statuses'),
        'inPersonGroup'      => __('messages.In-Person Group'),
        'directOnlineGroup'  => __('messages.Direct Online Group'),
        'available'          => __('messages.Available'),
        'suspended'          => __('messages.Suspended'),
        'groupName'          => __('messages.Group / Online Group'),
        'meetingTopic'       => __('messages.Meeting Topic'),
        'dayAndTime'         => __('messages.Day & Time'),
        'status'             => __('messages.Status'),
        'control'            => __('messages.Control'),
        'createLabel'        => __('messages.Add') . ' ' . __('messages.Meeting'),
        'quickView'          => __('messages.Quick View'),
        'edit'               => __('messages.Edit'),
        'delete'             => __('messages.Delete'),
        'clearFilters'       => __('messages.Clear Filters'),
        'meetingDetails'     => __('messages.Meeting Details'),
        'meetingOptions'     => __('messages.Meeting Options'),
        'platformLink'       => __('messages.Platform / Link'),
        'addressLocation'    => __('messages.Address & Location'),
        'joinMeeting'        => __('messages.Join Meeting'),
        'copyLink'           => __('messages.Copy Link'),
        'linkCopied'         => __('messages.Link Copied'),
        'viewOnMap'          => __('messages.View on Google Maps'),
        'noLink'             => __('messages.No Link Available'),
        'notes'              => __('messages.Notes'),
        'close'              => __('messages.Close'),
        'confirmDelete'      => __('messages.Confirm Delete Meeting'),
        'deletedSuccess'     => __('messages.meeting_deleted_success'),
        'noData'             => __('messages.No matching records found'),
        'adjustFilters'      => __('messages.Try adjusting your search or filters'),
        'first'              => __('messages.First') ?: 'First',
        'last'               => __('messages.Last') ?: 'Last',
        'prev'               => __('messages.previous') ?: 'Prev',
        'next'               => __('messages.next') ?: 'Next',
    ];
    @endphp

    <div class="container py-3">
        <div data-vue-app="MeetingsDataTable"
             data-fetch-url="{{ route('meeting.index') }}"
             data-create-route="{{ route('meeting.create') }}"
             data-create-label="{{ __('messages.Add') . ' ' . __('messages.Meeting') }}"
             data-edit-route-template="{{ str_replace('1', '{id}', route('meeting.edit', ['meeting' => 1])) }}"
             data-delete-route-template="{{ str_replace('1', '{id}', route('meeting.destroy', ['meeting' => 1])) }}"
             data-days="{{ json_encode($days ?? []) }}"
             data-kpi-stats="{{ json_encode($kpiStats ?? ['total_meetings' => 0, 'in_person_meetings' => 0, 'online_meetings' => 0, 'active_meetings' => 0]) }}"
             data-labels="{{ json_encode($labels) }}"
             data-csrf-token="{{ csrf_token() }}">
        </div>
    </div>
</x-layout>