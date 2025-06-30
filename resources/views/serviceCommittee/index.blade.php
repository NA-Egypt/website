<x-layout>

    <x-backhead>{{__('messages.Manage') . ' ' . __('messages.Service Committees')}}</x-backhead>

    <div class="container">

        {{-- Add Button --}}
        <div class="mt-3 mb-3">
            <x-button-a
                    href="{{ route('serviceCommittee.create') }}"
                    color='outline-primary'
                    name="{{__('messages.Add') . ' ' . __('messages.Service Committees')}}"
            />
        </div>
        {{-- / Add Button --}}

                <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
            <table class="main-tables text-center table table-bordered display" id="example">
                <thead>

                <tr>
                    {{-- <th>#{{ __('messages.ID')}}</th> --}}
                    <th>{{  __('messages.Arabic Service Committee Name') }}</th>
                    {{-- <th>{{  __('messages.English Service Committee Name') }}</th> --}}
                    <th>{{  __('messages.Email') }}</th>
                    {{-- <th>{{  __('messages.Chairman Name') }}</th>
                    <th>{{  __('messages.Chairman Phone') }}</th>
                    <th>{{  __('messages.Arabic Address') }}</th>
                    <th>{{  __('messages.English Address') }}</th>
                    <th>{{  __('messages.Locations') }}</th> --}}
                    <th>{{  __('messages.Committee Meetings') }}</th>
                    <th>{{  __('messages.Control') }}</th>
                </tr>

                </thead>

                <tbody>
                @foreach ($ServiceCommittee as $sc)

                    <tr>
                        {{-- <td>{{ $sc->id }}</td> --}}
                        <td>{{ $sc->ar_name }}</td>
                        {{-- <td>{{ $sc->en_name }}</td> --}}
                        <td>{{ $sc->user->email }}</td>
                        {{-- <td>{{ $sc->chairman_name }}</td>
                        <td>{{ $sc->chairman_phone }}</td>
                        <td>{{ $sc->ar_address }}</td>
                        <td>{{ $sc->en_address }}</td>
                        <td>{{ $sc->location }}</td> --}}
                        <td>{{ $sc->notes }}</td>
                        <td>
                            <x-button-a href="{{ route('serviceCommittee.edit', $sc->id) }}" color='outline-info' name="{{  __('messages.Edit') }}" />
                            <x-button-a href="{{ route('serviceCommittee.show', $sc->id) }}" color='outline-info' name="{{  __('messages.Show') }}" />
                            <x-forms.delete-button name="{{  __('messages.Delete') }}" formName='delete-item' id="{{$sc->id}}" routeName="serviceCommittee.destroy" />
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{-- {{$group->links()}} --}}
    </div>

</x-layout>