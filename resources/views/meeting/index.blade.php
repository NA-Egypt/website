<x-layout>

    <x-section-head>{{__('messages.Manage') . ' ' . __('messages.Meetings')}}</x-section-head>

    <div class="container">

        <div class="mt-3 mb-3">
            <x-button-a href="{{ route('meeting.create') }}" color='outline-primary' name="{{__('messages.Add') . ' ' . __('messages.Meeting')}}" />
        </div>

        <div class="table-responsive">
            <table class="main-tables manage-member text-center table table-bordered display" id="example">
                <thead>
                    <tr>
                        <th>#{{ __('messages.ID')}}</th>
                        <th>{{  __('messages.Group Name') }}</th>
                        <th>{{  __('messages.Meeting Topic') }}</th>
                        <th>{{  __('messages.Day') }}</th>
                        <th>{{  __('messages.From') }}</th>
                        <th>{{  __('messages.To') }}</th>
                        <th>{{  __('messages.Description') }}</th>
                        <th>{{  __('messages.Control') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($meetings as $meeting)                    
                        <tr>
                            <td>{{ $meeting->id }}</td>
                            <td>{{ $meeting->group->ar_name }}</td>
                            <td>{{ $meeting->topic->title }}</td>
                            <td>{{ $meeting->day->name }}</td>
                            <td>{{ $meeting->formatted_start_time }}</td>
                            <td>{{ $meeting->formatted_end_time }}</td>
                            <td>{{ $meeting->description }}</td>
                            <td>
                                <x-button-a href="{{ route('meeting.edit', $meeting->id) }}" color='outline-info' name="{{  __('messages.Edit') }}" />
                                <x-forms.delete-button name="{{  __('messages.Delete') }}" formName='delete-item' id="{{$meeting->id}}" routeName="meeting.destroy" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{-- {{$meetings->links()}} --}}
    </div>

</x-layout>