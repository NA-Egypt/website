<x-layout>
    <x-backhead>{{ __('messages.Manage') . ' ' . __('messages.Service Body') }}</x-backhead>

    <div class="container-fluid py-4 px-md-5">
        @php
            $labels = [
                'totalServiceBodies' => __('messages.Total Service Bodies'),
                'hostedGroups' => __('messages.Hosted Groups'),
                'serviceBodyName' => __('messages.Service Body'),
                'meetingDayTime' => __('messages.Meeting Day') . ' & ' . __('messages.Meeting Time'),
                'meetingAddress' => __('messages.Meeting Address'),
                'allDays' => __('messages.All Days'),
                'searchPlaceholder' => __('messages.Search service bodies...'),
                'clearFilters' => __('messages.Clear Filters'),
                'viewOnMap' => __('messages.View on Google Maps'),
                'actions' => __('messages.actions'),
                'quickView' => __('messages.Quick View'),
                'viewDetails' => __('messages.View Details'),
                'serviceBodyDetails' => __('messages.Service Body Details'),
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
                'confirmDelete' => __('messages.Confirm Delete Service Body'),
                'deletedSuccess' => __('messages.service_body_deleted_success'),
            ];

            $dayOptions = collect($days ?? [])->map(function($d) {
                return [
                    'id' => $d->id,
                    'name' => app()->getLocale() === 'ar' ? ($d->ar_name ?: $d->en_name) : ($d->en_name ?: $d->ar_name),
                ];
            })->values();
        @endphp

        <div data-vue-app="ServiceBodiesDataTable"
             data-fetch-url="{{ route('serviceBody.index') }}"
             data-create-route="{{ route('serviceBody.create') }}"
             data-create-label="{{ __('messages.Add') . ' ' . __('messages.Service Body') }}"
             data-edit-route-template="{{ route('serviceBody.edit', ['serviceBody' => '__ID__']) }}"
             data-show-route-template="{{ route('serviceBody.agendas', ['serviceBody' => '__ID__']) }}"
             data-delete-route-template="{{ route('serviceBody.destroy', ['serviceBody' => '__ID__']) }}"
             data-days="{{ json_encode($dayOptions) }}"
             data-kpi-stats="{{ json_encode($kpiStats ?? []) }}"
             data-labels="{{ json_encode($labels) }}"
             data-csrf-token="{{ csrf_token() }}">
        </div>
    </div>
</x-layout>