<x-layout>

    <x-backhead>{{__('messages.Manage') . ' ' . __('messages.Group')}} ({{__('messages.legend_online')}})</x-backhead>

    <div class="container glass-card p-4 mt-4 mb-5">

        {{-- Add Button --}}
        <div class="mt-3 mb-3">
            <x-button-a 
                href="{{ route('direct-online-group.create') }}" 
                color='outline-primary' 
                name="{{__('messages.Add') . ' ' . __('messages.Group')}}" 
            />
        </div>

        <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
            <table class="main-tables text-center table table-bordered display" id="example">
                <thead>
                    <tr>
                        <th>{{  __('messages.Arabic Group Name') }}</th>
                        <th>{{  __('messages.English Group Name') }}</th>
                        <th>{{  __('messages.Email') }}</th>
                        <th>{{  __('messages.Locations') }} (Zoom)</th>
                        <th>{{  __('messages.Control') }}</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach ($directOnlineGroups as $group)                    
                        <tr>
                            <td>{{ $group->ar_name }}</td>
                            <td>{{ $group->en_name }}</td>
                            <td>{{ $group->user ? $group->user->email : '-' }}</td>
                            <td>
                                <a href="{{ $group->location }}" target="_blank" class="text-primary text-decoration-underline">{{ $group->location }}</a>
                            </td>
                            <td>
                                <x-button-a href="{{ route('direct-online-group.edit', $group->id) }}" color='outline-info' name="{{  __('messages.Edit') }}" />
                                <x-button-a href="{{ route('direct-online-group.show', $group->id) }}" color='outline-info' name="{{  __('messages.Show') }}" />
                                <x-forms.delete-button name="{{  __('messages.Delete') }}" formName='delete-item' id="{{$group->id}}" routeName="direct-online-group.destroy" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-layout>
