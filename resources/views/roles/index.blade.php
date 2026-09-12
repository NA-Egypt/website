<x-layout>
    <x-backhead>{{ __('messages.Manage Roles') }}</x-backhead>

    @php
    $labels = [
        'totalRoles' => __('messages.Total Roles'),
        'systemRoles' => __('messages.System Roles'),
        'totalPermissions' => __('messages.Total Permissions'),
        'activeAssignments' => __('messages.Active Assignments'),
        'roleInfo' => __('messages.Role Information'),
        'assignedUsers' => __('messages.Assigned Users'),
        'permissionsBreakdown' => __('messages.Permissions Breakdown'),
        'actions' => __('messages.Control'),
        'systemRole' => __('messages.System Role'),
        'customRole' => __('messages.Custom Role'),
        'view' => __('messages.View Details'),
        'edit' => __('messages.Edit') ?? 'Edit',
        'editRoleInfo' => __('messages.Edit Role Info'),
        'assignPermissions' => __('messages.Assign Permissions'),
        'delete' => __('messages.Delete') ?: (__('messages.delete') ?: 'Delete'),
        'cancel' => __('messages.Cancel') ?: (__('messages.cancel') ?: 'Cancel'),
        'newRole' => __('messages.New Role'),
        'searchPlaceholder' => __('messages.Search roles or permissions...'),
        'allTypes' => __('messages.All Types'),
        'rolesList' => __('messages.Roles List'),
        'showing' => __('messages.Showing') ?? 'Showing',
        'prev' => __('messages.previous') ?? 'Prev',
        'next' => __('messages.next') ?? 'Next',
        'noDescription' => __('messages.No description provided'),
        'loading' => __('messages.Loading...') ?? 'Loading...',
        'noRolesFound' => __('messages.No results found') ?? 'No roles found',
        'noRolesMatchCriteria' => __('messages.No permissions match your search') ?? 'Try adjusting your search query.',
        'permissionsLabel' => __('messages.Permissions') ?? 'Permissions',
        'clickToViewUsers' => __('messages.Click to view users') ?? 'Click to view assigned users',
        'systemRoleCannotDelete' => __('messages.Cannot Delete System Role'),
        'systemRoleCannotRename' => __('messages.System roles cannot be renamed'),
        'systemRoleProtectedDesc' => __('messages.Cannot Delete System Role'),
        'roleName' => __('messages.Role Name'),
        'roleDescription' => __('messages.Role Description'),
        'roleDescriptionPlaceholder' => __('messages.Role Description'),
        'saveChanges' => __('messages.Save Changes'),
        'close' => __('messages.Close'),
        'permissionsTab' => __('messages.Permissions Tab'),
        'assignedUsersTab' => __('messages.Assigned Users Tab'),
        'searchInPermissions' => __('messages.Search in permissions...'),
        'searchInUsers' => __('messages.Search in users...'),
        'noPermissionsAssigned' => __('messages.No permissions assigned to this role'),
        'noUsersAssigned' => __('messages.No users assigned to this role'),
        'loadingDetails' => __('messages.Loading...') ?? 'Loading details...',
        'roleUpdatedSuccess' => __('messages.role_updated_success'),
        'roleDeletedSuccess' => __('messages.role_deleted'),
        'confirmDeleteRole' => __('messages.Confirm Delete Role'),
        'catAgenda' => __('messages.Service Body Agendas') ?? 'Service Body Agendas',
        'catStore' => __('messages.Store & Lit') ?? 'Store & Literature',
        'catCalendar' => __('messages.Calendar') ?? 'Calendar',
        'catForms' => __('messages.Forms Builder') ?? 'Custom Forms',
        'catGeneral' => __('messages.General & Others') ?? 'General & Others',
    ];
    @endphp

    <div class="container py-3">
        <div data-vue-app="RolesDataTable"
             data-fetch-url="{{ route('roles.index') }}"
             data-create-route="{{ route('roles.create') }}"
             data-assign-permissions-template="{{ str_replace('1', '{id}', route('roles.assign-permissions', ['role' => 1])) }}"
             data-details-route-template="{{ str_replace('1', '{id}', route('roles.details', ['role' => 1])) }}"
             data-update-route-template="{{ str_replace('1', '{id}', route('roles.update', ['role' => 1])) }}"
             data-delete-route-template="{{ str_replace('1', '{id}', route('roles.destroy', ['role' => 1])) }}"
             data-kpi-stats="{{ json_encode($kpiStats ?? ['total_roles' => 0, 'system_roles' => 0, 'total_permissions' => 0, 'total_assignments' => 0]) }}"
             data-labels="{{ json_encode($labels) }}"
             data-csrf-token="{{ csrf_token() }}">
        </div>
    </div>
</x-layout>