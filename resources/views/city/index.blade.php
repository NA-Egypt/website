<x-layout>

    <x-backhead>{{__('messages.Manage') . ' ' . __('messages.City')}}</x-backhead>

    <div class="container">

        <div class="m-3">
            <x-button-a href="{{ route('city.create') }}" color='outline-primary' name="{{__('messages.Add') . ' ' . __('messages.City')}}" />
        </div>

        <div class="table-responsive">
            <table class="main-tables manage-member text-center table table-bordered display" id="example">
                <thead>
                <tr>
                    {{-- <td>#{{ __('messages.ID')}}</td> --}}
                    <th>{{  __('messages.City Arabic Name') }}</th>
                    <th>{{  __('messages.City English Name') }}</th>
                    <th>{{  __('messages.Control') }}</th>
                </tr>
                </thead>
                <tbody>
                
                @foreach ($cities as $city)                 
                    <tr>
                        {{-- <td>{{ $city->id }}</td> --}}
                        <td>{{ $city->ar_name }}</td>
                        <td>{{ $city->en_name }}</td>
                        <td>
                            <x-button-a href="{{ route('city.edit', $city->id) }}" color='outline-info' name="{{  __('messages.Edit') }}" />
                            <x-forms.delete-button name="{{  __('messages.Delete') }}" formName='delete-item' id="{{$city->id}}" routeName="city.destroy" />
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{-- {{$items->links()}} --}}
    </div>

</x-layout>