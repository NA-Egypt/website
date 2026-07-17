<x-layout>

    <x-backhead>{{__('messages.Manage') . ' ' . __('messages.Service Body')}}</x-backhead>

    <div class="container">

        @php
        $columns = [
            ['field' => 'ar_name', 'title' => __('messages.Service Body Arabic Name'), 'sort' => true],
            ['field' => 'day_name', 'title' => __('messages.Day'), 'sort' => false],
            ['field' => 'from_time', 'title' => __('messages.From'), 'sort' => false],
            ['field' => 'to_time', 'title' => __('messages.To'), 'sort' => false],
            ['field' => 'location', 'title' => __('messages.Location'), 'sort' => true],
            ['field' => 'actions', 'title' => __('messages.Control'), 'sort' => false]
        ];
        @endphp

        <div data-vue-app="GenericDataTable"
             data-fetch-url="{{ route('serviceBody.index') }}"
             data-columns="{{ json_encode($columns) }}"
             data-create-route="{{ route('serviceBody.create') }}"
             data-create-label="{{ __('messages.Add') . ' ' . __('messages.Service Body') }}"
             data-edit-route-template="{{ str_replace('1', '{id}', route('serviceBody.edit', ['serviceBody' => 1])) }}"
             data-has-agendas-button
             data-delete-route-name="serviceBody.destroy"
             data-delete-route-template="{{ str_replace('1', '{id}', route('serviceBody.destroy', ['serviceBody' => 1])) }}">
        </div>
        {{-- {{$serviceBody->links()}} --}}
    </div>

</x-layout>