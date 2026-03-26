<x-layout>

    <x-backhead>{{__('messages.Manage') . ' ' . __('messages.Topics')}}</x-backhead>

    <div class="container">

        <div class="m-3">
            <x-button-a href="{{ route('topic.create') }}" color='outline-primary' name="{{__('messages.Add') . ' ' . __('messages.Topic')}}" />
        </div>

        <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
            <table class="main-tables manage-member text-center table table-bordered display" id="example">
                <thead>
                    <tr>
                        {{-- <td>#{{ __('messages.ID')}}</td> --}}
                        <th>{{  __('messages.Topic Arabic Name') }}</th>
                        <th>{{  __('messages.Topic English Name') }}</th>
                        {{-- <th>{{  __('messages.Notes') }}</th> --}}
                        <th>{{  __('messages.Control') }}</th>
                    </tr>
                </thead>
                <tbody>
                
                @foreach ($topics as $topic)                 
                    <tr>
                        {{-- <td>{{ $topic->id }}</td> --}}
                        <td>{{ $topic->ar_name }}</td>
                        <td>{{ $topic->en_name }}</td>
                        {{-- <td>{{ $topic->description }}</td> --}}
                        <td>
                            <x-button-a href="{{ route('topic.edit', $topic->id) }}" color='outline-info' name="{{  __('messages.Edit') }}" />
                            <x-forms.delete-button name="{{  __('messages.Delete') }}" formName='delete-item' id="{{$topic->id}}" routeName="topic.destroy" />
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{-- {{$neighborhoods->links()}} --}}
    </div>

</x-layout>