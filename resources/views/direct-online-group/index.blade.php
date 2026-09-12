<x-layout>
    <x-backhead>{{ __('messages.Manage') . ' ' . __('messages.Group') }} ({{ __('messages.legend_online') }})</x-backhead>

    @php
    $labels = [
        'totalOnlineGroups'          => __('messages.Total Online Groups'),
        'onlineMeetingsCount'        => __('messages.Online Meetings Count'),
        'activeOnlineLinks'          => __('messages.Active Online Links'),
        'searchPlaceholder'          => __('messages.Search online groups, links, email...'),
        'hasMeetings'                => __('messages.Has Meetings'),
        'withMeetings'               => __('messages.With Meetings'),
        'withoutMeetings'            => __('messages.Without Meetings'),
        'platformLink'               => __('messages.Platform / Link'),
        'noLinkAvailable'            => __('messages.No Link Available'),
        'groupName'                  => __('messages.Group Name'),
        'directOnlineGroup'          => __('messages.Direct Online Group'),
        'gsrContact'                 => __('messages.GSR Information'),
        'control'                    => __('messages.Control'),
        'createLabel'                => __('messages.Add') . ' ' . __('messages.Group'),
        'quickView'                  => __('messages.Quick View'),
        'viewDetails'                => __('messages.View Details'),
        'edit'                       => __('messages.Edit'),
        'delete'                     => __('messages.Delete'),
        'meetings'                   => __('messages.Meetings'),
        'clearFilters'               => __('messages.Clear Filters'),
        'directOnlineGroupDetails'   => __('messages.Direct Online Group Details'),
        'gsrInformation'             => __('messages.GSR Information'),
        'joinMeeting'                => __('messages.Join Meeting'),
        'copyLink'                   => __('messages.Copy Link'),
        'linkCopied'                 => __('messages.Link Copied'),
        'close'                      => __('messages.Close'),
        'confirmDelete'              => __('messages.Confirm Delete Direct Online Group'),
        'deletedSuccess'             => __('messages.direct_online_group_deleted_success'),
        'noData'                     => __('messages.No matching records found'),
        'adjustFilters'              => __('messages.Try adjusting your search or filters'),
        'first'                      => __('messages.First') ?: 'First',
        'last'                       => __('messages.Last') ?: 'Last',
        'prev'                       => __('messages.previous') ?: 'Prev',
        'next'                       => __('messages.next') ?: 'Next',
    ];
    @endphp

    <div class="container py-3">
        <div data-vue-app="DirectOnlineGroupsDataTable"
             data-fetch-url="{{ route('direct-online-group.index') }}"
             data-create-route="{{ route('direct-online-group.create') }}"
             data-create-label="{{ __('messages.Add') . ' ' . __('messages.Group') }}"
             data-edit-route-template="{{ str_replace('1', '{id}', route('direct-online-group.edit', ['directOnlineGroup' => 1])) }}"
             data-show-route-template="{{ str_replace('1', '{id}', route('direct-online-group.show', ['directOnlineGroup' => 1])) }}"
             data-delete-route-template="{{ str_replace('1', '{id}', route('direct-online-group.destroy', ['directOnlineGroup' => 1])) }}"
             data-kpi-stats="{{ json_encode($kpiStats ?? ['total_online_groups' => 0, 'total_online_meetings' => 0, 'active_links_count' => 0]) }}"
             data-labels="{{ json_encode($labels) }}"
             data-csrf-token="{{ csrf_token() }}">
        </div>
    </div>
</x-layout>
