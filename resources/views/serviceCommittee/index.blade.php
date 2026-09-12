<x-layout>
    <x-backhead>{{ __('messages.Manage') . ' ' . __('messages.Service Committees') }}</x-backhead>

    <div class="container-fluid py-4 px-md-5">
        @php
            $labels = [
                'totalCommittees' => __('messages.Total Committees'),
                'childWorkgroups' => __('messages.Child Workgroups'),
                'activeCommittees' => __('messages.Active Committees'),
                'committeeNameAr' => __('messages.Committee Name (AR)'),
                'committeeNameEn' => __('messages.Committee Name (EN)'),
                'assignedOfficer' => __('messages.Assigned Officer'),
                'searchPlaceholder' => __('messages.Search committees...'),
                'clearFilters' => __('messages.Clear Filters'),
                'hasWorkgroups' => __('messages.Has Workgroups'),
                'withWorkgroups' => __('messages.With Workgroups'),
                'withoutWorkgroups' => __('messages.Without Workgroups'),
                'actions' => __('messages.actions'),
                'quickView' => __('messages.Quick View'),
                'committeeDetails' => __('messages.Committee Details'),
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
                'confirmDelete' => __('messages.Confirm Delete Committee'),
                'deletedSuccess' => __('messages.committee_deleted_success'),
            ];
        @endphp

        <div data-vue-app="ServiceCommitteesDataTable"
             data-fetch-url="{{ route('serviceCommittee.index') }}"
             data-create-route="{{ route('serviceCommittee.create') }}"
             data-create-label="{{ __('messages.Add') . ' ' . __('messages.Service Committees') }}"
             data-edit-route-template="{{ route('serviceCommittee.edit', ['serviceCommittee' => '__ID__']) }}"
             data-delete-route-template="{{ route('serviceCommittee.destroy', ['serviceCommittee' => '__ID__']) }}"
             data-kpi-stats="{{ json_encode($kpiStats ?? []) }}"
             data-labels="{{ json_encode($labels) }}"
             data-csrf-token="{{ csrf_token() }}">
        </div>
    </div>
</x-layout>