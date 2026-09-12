<x-layout>
    <x-backhead>{{ __('messages.Manage Workgroups') }}</x-backhead>

    <div class="container-fluid py-4 px-md-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(!$isSuperAdmin && $myCommittee)
            <div class="card border-0 shadow-sm mb-4 glass-card">
                <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 46px; height: 46px;">
                            <i class="bi bi-people-fill fs-5"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ app()->getLocale() === 'ar' ? $myCommittee->ar_name : $myCommittee->en_name }}</h5>
                            <small class="text-muted">{{ __('messages.Sub-Workgroups') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @php
            $labels = [
                'totalWorkgroups' => __('messages.Total Workgroups'),
                'parentCommittees' => __('messages.Parent Committees'),
                'activeWorkgroups' => __('messages.Active Workgroups'),
                'temporaryWorkgroups' => __('messages.Temporary Workgroups'),
                'permanentWorkgroups' => __('messages.Permanent Workgroups'),
                'workgroupName' => __('messages.Workgroup Details'),
                'parentCommittee' => __('messages.Parent Committee'),
                'workgroupType' => __('messages.Workgroup Type'),
                'assignedOfficer' => __('messages.Assigned Officer'),
                'linkedMeetings' => __('messages.Linked Meetings'),
                'searchPlaceholder' => __('messages.Search workgroups...'),
                'allCommittees' => __('messages.All Committees'),
                'allWorkgroupTypes' => __('messages.All Workgroup Types'),
                'clearFilters' => __('messages.Clear Filters'),
                'actions' => __('messages.actions'),
                'quickView' => __('messages.Quick View'),
                'workgroupDetails' => __('messages.Workgroup Details'),
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
                'confirmDelete' => __('messages.Confirm Delete Workgroup'),
                'deletedSuccess' => __('messages.workgroup_deleted_success'),
            ];

            $committeeOptions = collect($committees ?? [])->map(function($c) {
                return [
                    'id' => $c->id,
                    'name' => app()->getLocale() === 'ar' ? ($c->ar_name ?: $c->en_name) : ($c->en_name ?: $c->ar_name),
                ];
            })->values();
        @endphp

        <div data-vue-app="WorkgroupsDataTable"
             data-fetch-url="{{ route('workgroup.index') }}"
             data-create-route="{{ route('workgroup.create') }}"
             data-create-label="{{ __('messages.Add Workgroup') }}"
             data-edit-route-template="{{ route('workgroup.edit', ['workgroup' => '__ID__']) }}"
             data-delete-route-template="{{ route('workgroup.destroy', ['workgroup' => '__ID__']) }}"
             data-committees="{{ json_encode($committeeOptions) }}"
             data-kpi-stats="{{ json_encode($kpiStats ?? []) }}"
             data-labels="{{ json_encode($labels) }}"
             data-csrf-token="{{ csrf_token() }}">
        </div>
    </div>
</x-layout>
