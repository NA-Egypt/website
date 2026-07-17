<x-layout>

    <x-backhead>{{__('messages.Manage') . ' ' . __('messages.Neighborhood')}}</x-backhead>

    <div class="container">

        @php
        $columns = [
            ['field' => 'ar_name', 'title' => __('messages.Neighborhood Arabic Name'), 'sort' => true],
            ['field' => 'en_name', 'title' => __('messages.Neighborhood English Name'), 'sort' => true],
            ['field' => 'city.ar_name', 'title' => __('messages.City Arabic Name'), 'sort' => true, 'renderType' => 'nested', 'fieldPath' => 'city.ar_name'],
            ['field' => 'city.en_name', 'title' => __('messages.City English Name'), 'sort' => true, 'renderType' => 'nested', 'fieldPath' => 'city.en_name'],
            ['field' => 'actions', 'title' => __('messages.Control'), 'sort' => false]
        ];
        @endphp

        <div data-vue-app="GenericDataTable"
             data-fetch-url="{{ route('neighborhood.index') }}"
             data-columns="{{ json_encode($columns) }}"
             data-create-route="{{ route('neighborhood.create') }}"
             data-create-label="{{ __('messages.Add') . ' ' . __('messages.Neighborhood') }}"
             data-edit-route-template="{{ str_replace('1', '{id}', route('neighborhood.edit', ['neighborhood' => 1])) }}"
             data-delete-route-name="neighborhood.destroy"
             data-delete-route-template="{{ str_replace('1', '{id}', route('neighborhood.destroy', ['neighborhood' => 1])) }}">
        </div>
    </div>

</x-layout>