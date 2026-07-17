<x-layout>

    <x-backhead>{{__('messages.Manage') . ' ' . __('messages.City')}}</x-backhead>

    <div class="container">

        <div class="m-3">
            <x-button-a href="{{ route('city.create') }}" color='outline-primary' name="{{__('messages.Add') . ' ' . __('messages.City')}}" />
        </div>

        <div data-vue-app="GenericDataTable"
             data-fetch-url="{{ route('city.index') }}"
             data-columns="{{ json_encode($columns) }}"
             data-create-route="{{ route('city.create') }}"
             data-create-label="{{ __('messages.Add') . ' ' . __('messages.City') }}"
             data-edit-route-template="{{ str_replace('1', '{id}', route('city.edit', ['city' => 1])) }}"
             data-delete-route-name="city.destroy"
             data-delete-route-template="{{ str_replace('1', '{id}', route('city.destroy', ['city' => 1])) }}">
        </div>
        {{-- {{$items->links()}} --}}
    </div>

</x-layout>