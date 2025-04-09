<x-layout>

    <x-section-head>{{__('messages.Manage') . ' ' . __('messages.Group')}}</x-section-head>

    <div class="container">

        {{-- Add Button --}}
        <div class="mt-3 mb-3">
            <x-button-a 
            href="{{ route('group.create') }}" 
            color='outline-primary' 
            name="{{__('messages.Add') . ' ' . __('messages.Group')}}" 
            />
        </div>
        {{-- / Add Button --}}

        <div class="table-responsive">
            <table class="main-tables text-center table table-bordered display" id="example">
                <thead>

                    <tr>
                        <th>#{{ __('messages.ID')}}</th>
                        <th>{{  __('messages.Arabic Group Name') }}</th>
                        <th>{{  __('messages.English Group Name') }}</th>
                        <th>{{  __('messages.Arabic GSR Name') }}</th>
                        <th>{{  __('messages.English GSR Name') }}</th>
                        <th>{{  __('messages.Email') }}</th>
                        <th>{{  __('messages.Phone') }}</th>
                        <th>{{  __('messages.Address') }}</th>
                        <th>{{  __('messages.Group Type') }}</th>
                        <th>{{  __('messages.Service Body Name') }}</th>
                        <th>{{  __('messages.Neighborhood Name') }}</th>
                        <th>{{  __('messages.Control') }}</th>
                    </tr>

                </thead>
                
                <tbody>
                    @foreach ($groups as $group)                    

                        <tr>
                            <td>{{ $group->id }}</td>
                            <td>{{ $group->ar_name }}</td>
                            <td>{{ $group->en_name }}</td>
                            <td>{{ $group->ar_gsr_name }}</td>
                            <td>{{ $group->en_gsr_name }}</td>
                            <td>{{ $group->email }}</td>
                            <td>{{ $group->phone }}</td>
                            <td>{{ $group->location }}</td>
                            <td>{{ $group->group_type }}</td>
                            <td>{{ $group->serviceBody->name }}</td>
                            <td>{{ $group->neighborhood->name}}</td>
                            <td>
                                <x-button-a href="{{ route('group.edit', $group->id) }}" color='outline-info' name="{{  __('messages.Edit') }}" />
                                <x-forms.delete-button name="{{  __('messages.Delete') }}" formName='delete-item' id="{{$group->id}}" routeName="group.destroy" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{-- {{$group->links()}} --}}
    </div>

</x-layout>