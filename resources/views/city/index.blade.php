<x-layout>
    <x-backhead>{{ __('messages.Manage') . ' ' . __('messages.City') }}</x-backhead>

    <div class="container-fluid py-4 px-md-5">
        @php
            $labels = [
                'totalCities' => __('messages.Total Cities'),
                'linkedNeighborhoods' => __('messages.Linked Neighborhoods'),
                'hostedGroups' => __('messages.Hosted Groups'),
                'cityNameAr' => __('messages.City Name (AR)'),
                'cityNameEn' => __('messages.City Name (EN)'),
                'searchPlaceholder' => __('messages.Search cities by name...'),
                'clearFilters' => __('messages.Clear Filters'),
                'hasNeighborhoods' => __('messages.Has Neighborhoods'),
                'withNeighborhoods' => __('messages.With Neighborhoods'),
                'withoutNeighborhoods' => __('messages.Without Neighborhoods'),
                'actions' => __('messages.actions'),
                'quickView' => __('messages.Quick View'),
                'cityDetails' => __('messages.City Details'),
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
                'confirmDelete' => __('messages.Confirm Delete City'),
                'deletedSuccess' => __('messages.city_deleted_success'),
            ];
        @endphp

        <div data-vue-app="CitiesDataTable"
             data-fetch-url="{{ route('city.index') }}"
             data-create-route="{{ route('city.create') }}"
             data-create-label="{{ __('messages.Add') . ' ' . __('messages.City') }}"
             data-edit-route-template="{{ route('city.edit', ['city' => '__ID__']) }}"
             data-delete-route-template="{{ route('city.destroy', ['city' => '__ID__']) }}"
             data-kpi-stats="{{ json_encode($kpiStats ?? []) }}"
             data-labels="{{ json_encode($labels) }}"
             data-csrf-token="{{ csrf_token() }}">
        </div>
    </div>
</x-layout>