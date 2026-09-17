<x-layout>
    <x-backhead>{{ __('messages.Users') }}</x-backhead>

    <div class="container-fluid py-4 px-md-5">
        @php
            $labels = [
                'totalUsers' => __('messages.Total Users'),
                'activeRecently' => __('messages.Active Recently'),
                'neverLoggedIn' => __('messages.Never Logged In'),
                'serviceBodyOfficers' => __('messages.Service Body Officers'),
                'user' => __('messages.User Details'),
                'assignedRoles' => __('messages.Assigned Roles'),
                'associatedServiceBody' => __('messages.Associated Service Body'),
                'lastLogin' => __('messages.Last Login'),
                'never' => __('messages.Never'),
                'impersonateUser' => __('messages.Impersonate User'),
                'confirmImpersonate' => __('messages.confirm_impersonate') ?? 'Are you sure you want to impersonate this user?',
                'searchPlaceholder' => __('messages.Search users by name, email...'),
                'allRoles' => __('messages.All Roles'),
                'allVerificationStatuses' => __('messages.All Verification Statuses'),
                'clearFilters' => __('messages.Clear Filters'),
                'actions' => __('messages.actions'),
                'quickView' => __('messages.Quick View'),
                'userDetails' => __('messages.User Details'),
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
                'confirmDelete' => __('messages.Confirm Delete User'),
                'deletedSuccess' => __('messages.user_deleted_success'),
            ];

            $canImpersonate = auth()->user() && auth()->user()->hasRole('super admin');
        @endphp

        <div data-vue-app="UsersDataTable"
             data-fetch-url="{{ route('users.index') }}"
             data-create-route="{{ route('users.create') }}"
             data-create-label="{{ __('messages.Add User') }}"
             data-edit-route-template="{{ route('users.edit', ['user' => '__ID__']) }}"
             data-delete-route-template="{{ route('users.destroy', ['user' => '__ID__']) }}"
             data-impersonate-route-template="{{ route('users.impersonate', ['user' => '__ID__']) }}"
             data-can-impersonate="{{ $canImpersonate ? 'true' : 'false' }}"
             data-roles="{{ json_encode($roles ?? []) }}"
             data-kpi-stats="{{ json_encode($kpiStats ?? []) }}"
             data-labels="{{ json_encode($labels) }}"
             data-csrf-token="{{ csrf_token() }}">
        </div>
    </div>
</x-layout>