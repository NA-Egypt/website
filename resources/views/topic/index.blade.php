<x-layout>

    <x-section-head>{{__('messages.Manage') . ' ' . __('messages.Topics')}}</x-section-head>

    <div class="container">

        <div class="m-3">
            <x-button-a href="{{ route('topic.create') }}" color='outline-primary' name="{{__('messages.Add') . ' ' . __('messages.Topic')}}" />
        </div>

        <div class="table-responsive">
            <table class="main-table manage-member text-center table table-bordered">
                <tr>
                    <td>#{{ __('messages.ID')}}</td>
                    <td>{{  __('messages.Topic Title') }}</td>
                    <td>{{  __('messages.Topic Description') }}</td>
                    <td>{{  __('messages.Control') }}</td>
                </tr>
                
                @foreach ($topics as $topic)                 
                    <tr>
                        <td>{{ $topic->id }}</td>
                        <td>{{ $topic->title }}</td>
                        <td>{{ $topic->description }}</td>
                        <td>
                            <x-button-a href="{{ route('topic.edit', $topic->id) }}" color='outline-info' name="{{  __('messages.Edit') }}" />
                            <x-forms.delete-button name="{{  __('messages.Delete') }}" formName='delete-item' id="{{$topic->id}}" routeName="topic.destroy" />
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        {{-- {{$neighborhoods->links()}} --}}
    </div>

</x-layout>