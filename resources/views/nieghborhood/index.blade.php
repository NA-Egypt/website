<x-layout>
    <x-backhead>{{ __('messages.Manage') . ' ' . __('messages.Neighborhood') }}</x-backhead>

    <div class="container-fluid py-4 px-md-5">
        @php
            $labels = [
                'totalNeighborhoods' => __('messages.Total Neighborhoods'),
                'citiesRepresented' => __('messages.Cities Represented'),
                'hostedGroups' => __('messages.Hosted Groups'),
                'neighborhoodNameAr' => __('messages.Neighborhood Name (AR)'),
                'neighborhoodNameEn' => __('messages.Neighborhood Name (EN)'),
                'city' => __('messages.City'),
                'allCities' => __('messages.All Cities'),
                'searchPlaceholder' => __('messages.Search neighborhoods...'),
                'clearFilters' => __('messages.Clear Filters'),
                'hasMeetings' => __('messages.Has Meetings'),
                'withMeetings' => __('messages.With Meetings'),
                'withoutMeetings' => __('messages.Without Meetings'),
                'actions' => __('messages.actions'),
                'quickView' => __('messages.Quick View'),
                'neighborhoodDetails' => __('messages.Neighborhood Details'),
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
                'confirmDelete' => __('messages.Confirm Delete Neighborhood'),
                'deletedSuccess' => __('messages.neighborhood_deleted_success'),
            ];

            $cityOptions = collect($cities ?? [])->map(function($c) {
                return [
                    'id' => $c->id,
                    'name' => app()->getLocale() === 'ar' ? ($c->ar_name ?: $c->en_name) : ($c->en_name ?: $c->ar_name),
                ];
            })->values();
        @endphp

        <div data-vue-app="NeighborhoodsDataTable"
             data-fetch-url="{{ route('neighborhood.index') }}"
             data-create-route="{{ route('neighborhood.create') }}"
             data-create-label="{{ __('messages.Add') . ' ' . __('messages.Neighborhood') }}"
             data-edit-route-template="{{ route('neighborhood.edit', ['neighborhood' => '__ID__']) }}"
             data-delete-route-template="{{ route('neighborhood.destroy', ['neighborhood' => '__ID__']) }}"
             data-cities="{{ json_encode($cityOptions) }}"
             data-kpi-stats="{{ json_encode($kpiStats ?? []) }}"
             data-labels="{{ json_encode($labels) }}"
             data-csrf-token="{{ csrf_token() }}">
        </div>
    </div>
</x-layout>