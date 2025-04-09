<x-layout>

    <x-section-head>{{__('messages.Manage') . ' ' . __('messages.Service Body')}}</x-section-head>

    <div class="container">

        <div class="m-3">
            <x-button-a href="{{ route('serviceBody.create') }}" color='outline-primary' name="{{__('messages.Add') . ' ' . __('messages.Service Body')}}" />
        </div>

        <div class="table-responsive">
            <table class="main-table manage-member text-center table table-bordered ">
                <tr>
                    <td>#{{ __('messages.ID')}}</td>
                    <td>{{  __('messages.Service Body Arabic Name') }}</td>
                    <td>{{  __('messages.Service Body English Name') }}</td>
                    <td>{{  __('messages.Description') }}</td>
                    <td>{{  __('messages.Day') }}</td>
                    <td>{{  __('messages.Date') }}</td>
                    <td>{{  __('messages.From') }}</td>
                    <td>{{  __('messages.To') }}</td>
                    <td>{{  __('messages.Location') }}</td>
                    <td>{{  __('messages.Control') }}</td>
                </tr>
                
                @foreach ($sb as $service)                    
                    <tr>
                        <td>{{ $service->id }}</td>
                        <td>{{ $service->ar_name }}</td>
                        <td>{{ $service->en_name }}</td>
                        <td>{{ $service->description }}</td>
                        @if(app()->getLocale() === 'ar')
                            <td>{{ $service->day->ar_name }}</td>
                        @else
                            <td>{{ $service->day->en_name }}</td>
                        @endif
                        <td>{{ $service->formatted_date }}</td>
                        <td>{{ $service->formatted_start_time }}</td>
                        <td>{{ $service->formatted_end_time}}</td>
                        <td>{{ $service->location }}</td>
                        <td>
                            <x-button-a href="{{ route('serviceBody.edit', $service->id) }}" color='outline-info' name="{{  __('messages.Edit') }}" />
                            <x-forms.delete-button name="{{  __('messages.Delete') }}" formName='delete-item' id="{{$service->id}}" routeName="serviceBody.destroy" />
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        {{-- {{$serviceBody->links()}} --}}
    </div>

</x-layout>