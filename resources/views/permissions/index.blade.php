<x-layout>
    <x-backhead>{{ __('messages.Manage Permissions') }}</x-backhead>

    <div class="container-fluid py-4 px-md-5">
        @php
            $labels = [
                'totalPermissions' => __('messages.Total Permissions'),
                'totalRoles' => __('messages.Total Roles'),
                'assignedPermissions' => __('messages.Assigned Permissions'),
                'permissionCategories' => __('messages.Permission Categories'),
                'permission' => __('messages.Permission Details'),
                'technicalKey' => __('messages.Technical Key'),
                'assignedRoles' => __('messages.Assigned Roles'),
                'allCategories' => __('messages.All Categories'),
                'searchPlaceholder' => __('messages.Search permissions...'),
                'clearFilters' => __('messages.Clear Filters'),
                'actions' => __('messages.actions'),
                'quickView' => __('messages.Quick View'),
                'permissionDetails' => __('messages.Permission Details'),
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
                'confirmDelete' => __('messages.Confirm Delete Permission'),
                'deletedSuccess' => __('messages.permission_deleted_success'),
            ];
        @endphp

        <div data-vue-app="PermissionsDataTable"
             data-fetch-url="{{ route('permissions.index') }}"
             data-create-route="{{ route('permissions.create') }}"
             data-create-label="{{ __('messages.New Permission') }}"
             data-edit-route-template="{{ route('permissions.edit', ['permission' => '__ID__']) }}"
             data-delete-route-template="{{ route('permissions.destroy', ['permission' => '__ID__']) }}"
             data-categories="{{ json_encode($categories ?? []) }}"
             data-kpi-stats="{{ json_encode($kpiStats ?? []) }}"
             data-labels="{{ json_encode($labels) }}"
             data-csrf-token="{{ csrf_token() }}">
        </div>
    </div>
</x-layout>
