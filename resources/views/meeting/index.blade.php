<x-layout>

    <x-backhead>{{__('messages.Manage') . ' ' . __('messages.Meetings')}}</x-backhead>

    <div class="container">

        <div class="mt-3 mb-3">
            <x-button-a href="{{ route('meeting.create') }}" color='outline-primary' name="{{__('messages.Add') . ' ' . __('messages.Meeting')}}" />
        </div>

        <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
            <table class="main-tables manage-member text-center table table-bordered display" id="example">
                <thead>
                    <tr>
                        {{-- <th>#{{ __('messages.ID')}}</th> --}}
                        <th>{{  __('messages.Group Name') }}</th>
                        <th>{{  __('messages.Meeting Topic') }}</th>
                        <th>{{  __('messages.Day') }}</th>
                        <th>{{  __('messages.From') }}</th>
                        <th>{{  __('messages.To') }}</th>
                    {{-- <th>{{  __('messages.Language') }}</th> --}}
                    <th>{{  __('messages.Status') }}</th>
                    {{-- <th>{{  __('messages.Capacity') }}</th>
                        <th>{{  __('messages.Notes') }}</th> --}}
                        <th>{{  __('messages.Control') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($meetings as $meeting)                    
                        <tr>
                            {{-- <td>{{ $meeting->id }}</td> --}}
                            <td>{{ $meeting->group->ar_name }}</td>
                            <td>
                                @if(app()->getLocale() === 'ar')
                                    {{$meeting->topic?->ar_name}}
                                @else
                                    {{$meeting->topic?->en_name}}
                                @endif
                            </td>
                            <td>
                                @if(app()->getLocale() === 'ar')
                                    {{$meeting->day->ar_name}}
                                @else
                                    {{$meeting->day->en_name}}
                                @endif
                            </td>
                            <td>{{ $meeting->formatted_start_time }}</td>
                            <td>{{ $meeting->formatted_end_time }}</td>
                            {{-- <td>
                            @if(app()->getLocale() === 'ar')
                                {{__('messages.'. $meeting->lang)}}
                            @else
                                {{$meeting->lang}}
                            @endif
                        </td> --}}
                        <td>
                            @if(app()->getLocale() === 'ar')
                                {{__('messages.'. $meeting->status)}}
                            @else
                                {{$meeting->status}}
                            @endif
                        </td>
                        {{-- <td>{{ $meeting->capacity }}</td>
                        <td>{{ $meeting->notes }}</td> --}}
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