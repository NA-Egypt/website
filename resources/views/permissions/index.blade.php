<x-layout>

    <x-section-head>{{__('messages.Manage Permissions')}}</x-section-head>

    <div class="container">

        <div class="m-3 text-start">
            <x-button-a href="{{ route('permissions.create') }}" color='outline-dark' name='{{__("messages.New Permission")}}' />
        </div>

        <div class="table-responsive">
            <table class="main-table manage-member text-center table table-bordered">
                <tr>
                    <td>{{__('messages.#ID')}}</td>
                    <td>{{__('messages.Permission Name')}}</td>
                    <td>{{__('messages.Permission Description')}}</td>
                    <td>{{__('messages.Control')}}</td>
                </tr>

                @forelse ($permissions as $permission)

                    <tr>
                        <td>{{ $permission->id }}</td>
                        <td>{{ $permission->name }}</td>
                        <td>{{ $permission->description }}</td>
                        <td>
                            <x-button-a href="{{ route('permissions.edit', $permission->id) }}" color='outline-warning'
                                      name='{{__("messages.Edit")}}'/>
{{--                            <x-button-a href="{{ route('permissions.create') }}" color='outline-warning' name='{{__("messages.Edit")}}' />--}}
                            <x-forms.delete-button formName='delete-permission' id="{{$permission->id}}"
                                                   routeName="permissions.destroy" name="{{__('messages.Delete')}}"/>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="4">{{ __('messages.No permissions found') }}</td>
                        </tr>
                    @endforelse
            </table>
        </div>

    </div>

    {{--    Sweet Alert --}}
    @if(session('success'))
        <script>
            window.successMessage = @json(session('success'));
        </script>
    @endif
    @if(session('error'))
        <script>
            window.errorMessage = @json(session('error'));
        </script>
    @endif
</x-layout>
