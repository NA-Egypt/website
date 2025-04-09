<x-layout>

    <x-section-head>{{__('messages.Manage') . ' ' . __('messages.Neighborhood')}}</x-section-head>

    <div class="container">

        <div class="m-3">
            <x-button-a href="{{ route('neighborhood.create') }}" color='outline-primary' name="{{__('messages.Add') . ' ' . __('messages.Neighborhood')}}" />
        </div>

        <div class="table-responsive">
            <table class="main-table manage-member text-center table table-bordered">

                <tr>
                    <td>#{{ __('messages.ID')}}</td>
                    <td>{{  __('messages.Neighborhood Arabic Name') }}</td>
                    <td>{{  __('messages.Neighborhood English Name') }}</td>
                    <td>{{  __('messages.City Arabic Name') }}</td>
                    <td>{{  __('messages.City English Name') }}</td>
                    <td>{{  __('messages.Control') }}</td>
                </tr>

                @foreach ($neighborhoods as $nh)
                    <tr>
                        <td>{{ $nh->id }}</td>
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
            </table>
        </div>
        {{-- {{$neighborhoods->links()}} --}}
    </div>

</x-layout>