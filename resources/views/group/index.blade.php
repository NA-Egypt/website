<x-layout>
    <x-backhead>{{ __('messages.Manage') . ' ' . __('messages.Group') }}</x-backhead>

    @php
    $labels = [
        'totalGroups'        => __('messages.Total Groups'),
        'linkedMeetings'     => __('messages.Linked Meetings'),
        'serviceBodies'      => __('messages.Service Bodies'),
        'activeGsrs'         => __('messages.Active GSRs'),
        'searchPlaceholder'  => __('messages.Search groups, GSR, email...'),
        'allServiceBodies'   => __('messages.All Service Bodies'),
        'hasMeetings'        => __('messages.Has Meetings'),
        'withMeetings'       => __('messages.With Meetings'),
        'withoutMeetings'    => __('messages.Without Meetings'),
        'groupName'          => __('messages.Group Name'),
        'serviceBody'        => __('messages.Service Body Name'),
        'neighborhood'       => __('messages.Neighborhood Name'),
        'gsrContact'         => __('messages.GSR Information'),
        'capacity'           => __('messages.Capacity'),
        'control'            => __('messages.Control'),
        'createLabel'        => __('messages.Add') . ' ' . __('messages.Group'),
        'quickView'          => __('messages.Quick View'),
        'viewDetails'        => __('messages.View Details'),
        'edit'               => __('messages.Edit'),
        'delete'             => __('messages.Delete'),
        'meetings'           => __('messages.Meetings'),
        'viewGroupMeetings'  => __('messages.View Group Meetings'),
        'clearFilters'       => __('messages.Clear Filters'),
        'groupDetails'       => __('messages.Group Details'),
        'gsrInformation'     => __('messages.GSR Information'),
        'addressLocation'    => __('messages.Address & Location'),
        'copyLink'           => __('messages.Copy Link'),
        'viewOnMap'          => __('messages.View on Google Maps'),
        'close'              => __('messages.Close'),
        'confirmDelete'      => __('messages.Confirm Delete Group'),
        'deletedSuccess'     => __('messages.group_deleted_success'),
        'noData'             => __('messages.No matching records found'),
        'adjustFilters'      => __('messages.Try adjusting your search or filters'),
        'first'              => __('messages.First') ?: 'First',
        'last'               => __('messages.Last') ?: 'Last',
        'prev'               => __('messages.previous') ?: 'Prev',
        'next'               => __('messages.next') ?: 'Next',
    ];
    @endphp

    <div class="container py-3">
        <div data-vue-app="GroupsDataTable"
             data-fetch-url="{{ route('group.index') }}"
             data-create-route="{{ route('group.create') }}"
             data-create-label="{{ __('messages.Add') . ' ' . __('messages.Group') }}"
             data-edit-route-template="{{ str_replace('1', '{id}', route('group.edit', ['group' => 1])) }}"
             data-show-route-template="{{ str_replace('1', '{id}', route('group.show', ['group' => 1])) }}"
             data-meetings-route-template="{{ str_replace('1', '{id}', route('searches.meeting', ['id' => 1])) }}"
             data-delete-route-template="{{ str_replace('1', '{id}', route('group.destroy', ['group' => 1])) }}"
             data-service-bodies="{{ json_encode($serviceBodies ?? []) }}"
             data-kpi-stats="{{ json_encode($kpiStats ?? ['total_groups' => 0, 'total_meetings' => 0, 'service_bodies_count' => 0, 'active_gsrs_count' => 0]) }}"
             data-labels="{{ json_encode($labels) }}"
             data-csrf-token="{{ csrf_token() }}">
        </div>
    </div>
</x-layout>