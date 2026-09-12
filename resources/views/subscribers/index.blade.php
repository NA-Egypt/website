<x-layout>
    <x-backhead>{{ __('messages.Manage Subscribers') }}</x-backhead>

    <div class="container-fluid py-4 px-md-5">
        @if (session('success'))
            <div class="alert alert-success shadow-sm d-flex align-items-center mb-3">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @php
            $labels = [
                'totalSubscribers' => __('messages.Total Subscribers'),
                'verifiedSubscribers' => __('messages.Verified Subscribers'),
                'unverifiedSubscribers' => __('messages.Unverified Subscribers'),
                'verificationRate' => __('messages.Verification Rate'),
                'email' => __('messages.Email'),
                'status' => __('messages.Status'),
                'createdAt' => __('messages.Created At'),
                'exportCsv' => __('messages.Export CSV'),
                'batchVerifySmtp' => __('messages.Batch Verify SMTP'),
                'verifySelected' => __('messages.Verify Selected'),
                'unverifySelected' => __('messages.Unverify Selected'),
                'searchPlaceholder' => __('messages.Search subscribers by email...'),
                'allVerificationStatuses' => __('messages.All Verification Statuses'),
                'clearFilters' => __('messages.Clear Filters'),
                'actions' => __('messages.actions'),
                'quickView' => __('messages.Quick View'),
                'subscriberDetails' => __('messages.Subscriber Details'),
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
                'confirmDelete' => __('messages.Confirm Delete Subscriber'),
                'deletedSuccess' => __('messages.subscriber_deleted_success'),
                'batchVerificationProgress' => __('messages.Batch Verification Progress'),
                'verifyingViaSmtp' => __('messages.Verifying unverified subscribers via SMTP...'),
                'batchComplete' => __('messages.Batch Verification Complete'),
                'noUnverifiedSubscribers' => __('messages.No unverified subscribers found'),
            ];
        @endphp

        <div data-vue-app="SubscribersDataTable"
             data-fetch-url="{{ route('subscribers.index') }}"
             data-create-route="{{ route('subscribers.create') }}"
             data-create-label="{{ __('messages.Add Subscriber') }}"
             data-export-route="{{ route('subscribers.export') }}"
             data-toggle-route-template="{{ route('subscribers.toggle-verification', ['subscriber' => '__ID__']) }}"
             data-delete-route-template="{{ route('subscribers.destroy_admin', ['subscriber' => '__ID__']) }}"
             data-get-ids-url="{{ route('subscribers.ids') }}"
             data-verify-batch-url="{{ route('subscribers.verify_batch') }}"
             data-kpi-stats="{{ json_encode($kpiStats ?? []) }}"
             data-labels="{{ json_encode($labels) }}"
             data-csrf-token="{{ csrf_token() }}">
        </div>
    </div>
</x-layout>
