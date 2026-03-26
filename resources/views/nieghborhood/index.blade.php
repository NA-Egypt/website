<x-layout>

    <x-backhead>{{__('messages.Manage') . ' ' . __('messages.Neighborhood')}}</x-backhead>

    <div class="container">

        <div class="m-3">
            <x-button-a href="{{ route('neighborhood.create') }}" color='outline-primary' name="{{__('messages.Add') . ' ' . __('messages.Neighborhood')}}" />
        </div>

        <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
            <table class="main-tables manage-member text-center table table-bordered display" id="example">
                <thead>
                <tr>
                    {{-- <td>#{{ __('messages.ID')}}</td> --}}
                    <th>{{  __('messages.Neighborhood Arabic Name') }}</th>
                    <th>{{  __('messages.Neighborhood English Name') }}</th>
                    <th>{{  __('messages.City Arabic Name') }}</th>
                    <th>{{  __('messages.City English Name') }}</th>
                    <th>{{  __('messages.Control') }}</th>
                </tr>
                </thead>
                <tbody>

                @foreach ($neighborhoods as $nh)
                    <tr>
                        {{-- <td>{{ $nh->id }}</td> --}}
                        <td>{{ $nh->ar_name }}</td>
                        <td>{{ $nh->en_name }}</td>
                        <td>{{ $nh->city->ar_name }}</td>
                        <td>{{ $nh->city->en_name }}</td>
                        <td>
                            <x-button-a href="{{ route('neighborhood.edit', $nh->id) }}" color='outline-info' name="{{  __('messages.Edit') }}" />
                            <x-forms.delete-button name="{{  __('messages.Delete') }}" formName='delete-item' id="{{$nh->id}}" routeName="neighborhood.destroy" />
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{-- {{$neighborhoods->links()}} --}}
    </div>

</x-layout>