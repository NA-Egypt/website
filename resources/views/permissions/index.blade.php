<x-layout>

    <x-backhead>{{__('messages.Manage Permissions')}}</x-backhead>

    <div class="container">

        <div class="m-3 text-start">
            <x-button-a href="{{ route('permissions.create') }}" color='outline-dark' name='{{__("messages.New Permission")}}' />
        </div>

        <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
            <table class="main-tables manage-member text-center table table-bordered display" id="example">
               <thead>
                <tr>
                    {{-- <td>{{__('messages.#ID')}}</td> --}}
                    <th>{{__('messages.Permission Name')}}</th>
                    <th>{{__('messages.Permission Description')}}</th>
                    <th>{{__('messages.Control')}}</th>
                </tr>
                </thead>
                <tbody>

                @forelse ($permissions as $permission)

                    <tr>
                        {{-- <td>{{ $permission->id }}</td> --}}
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
                </tbody>
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
