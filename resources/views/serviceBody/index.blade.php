<x-layout>

    <x-backhead>{{__('messages.Manage') . ' ' . __('messages.Service Body')}}</x-backhead>

    <div class="container">

        <div class="m-3">
            <x-button-a href="{{ route('serviceBody.create') }}" color='outline-primary' name="{{__('messages.Add') . ' ' . __('messages.Service Body')}}" />
        </div>

        <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
            <table class="main-tables manage-member text-center table table-bordered display" id="example">
                <thead>
                    <tr>
                        {{-- <td>#{{ __('messages.ID')}}</td> --}}
                        <th>{{  __('messages.Service Body Arabic Name') }}</th>
                        {{-- <td>{{  __('messages.Service Body English Name') }}</td>
                        <td>{{  __('messages.Description') }}</td> --}}
                        <th>{{  __('messages.Day') }}</th>
                        {{-- <td>{{  __('messages.Date') }}</td> --}}
                        <th>{{  __('messages.From') }}</th>
                        <th>{{  __('messages.To') }}</th>
                        <th>{{  __('messages.Location') }}</th>
                        <th>{{  __('messages.Control') }}</th>
                    </tr>
                </thead>
                <tbody>
                    
                @foreach ($sb as $service)                    
                    <tr>
                        {{-- <td>{{ $service->id }}</td> --}}
                        <td>{{ $service->ar_name }}</td>
                        {{-- <td>{{ $service->en_name }}</td>
                        <td>{{ $service->description }}</td> --}}
                        @if(app()->getLocale() === 'ar')
                            <td>{{ $service->day->ar_name }}</td>
                        @else
                            <td>{{ $service->day->en_name }}</td>
                        @endif
                        {{-- <td>{{ $service->formatted_date }}</td> --}}
                        <td>{{ $service->formatted_start_time }}</td>
                        <td>{{ $service->formatted_end_time}}</td>
                        <td>{{ $service->location }}</td>
                        <td>
                            <x-button-a href="{{ route('serviceBody.edit', $service->id) }}" color='outline-info' name="{{  __('messages.Edit') }}" />
                            <x-forms.delete-button name="{{  __('messages.Delete') }}" formName='delete-item' id="{{$service->id}}" routeName="serviceBody.destroy" />
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{-- {{$serviceBody->links()}} --}}
    </div>

</x-layout>