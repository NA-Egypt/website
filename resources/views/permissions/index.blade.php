<x-layout>
    <x-backhead>{{__('messages.Manage Permissions')}}</x-backhead>

    <div class="container py-3">
        <div class="card border-0 shadow-lg" style="background: var(--glass-bg); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid var(--glass-border); border-radius: 16px;">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center p-4">
                <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">{{__('messages.Permissions List') ?? 'Permissions List'}}</h5>
                <x-button-a href="{{ route('permissions.create') }}" color='outline-primary' name='{{__("messages.New Permission")}}' />
            </div>

            <div class="card-body p-4 pt-0">
                <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
                    <table class="main-tables manage-member text-center table table-hover align-middle" id="example" style="border-collapse: separate; border-spacing: 0 8px;">
                        <thead>
                            <tr class="table-light-header" style="border-radius: 8px;">
                                <th style="border-bottom: 2px solid var(--glass-border); padding: 16px; color: var(--text-primary); font-weight: 600;">{{__('messages.Permission Name')}}</th>
                                <th style="border-bottom: 2px solid var(--glass-border); padding: 16px; color: var(--text-primary); font-weight: 600;">{{__('messages.Permission Description')}}</th>
                                <th style="border-bottom: 2px solid var(--glass-border); padding: 16px; color: var(--text-primary); font-weight: 600;">{{__('messages.Control')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($permissions as $permission)
                            <tr style="background: rgba(255, 255, 255, 0.02); transition: all 0.2s ease;">
                                <td class="fw-bold" style="padding: 16px; color: var(--text-primary);">
                                    <span class="d-flex align-items-center gap-2 justify-content-center">
                                        <i class="bi bi-shield-lock text-primary"></i>
                                        {{ $permission->name }}
                                    </span>
                                </td>
                                <td style="padding: 16px; color: var(--text-secondary);">{{ $permission->description ?? '-' }}</td>
                                <td style="padding: 16px; min-width: 150px;">
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <x-button-a href="{{ route('permissions.edit', $permission->id) }}" color='outline-warning'
                                                  name='{{__("messages.Edit")}}'/>
                                        <x-forms.delete-button formName='delete-permission' id="{{$permission->id}}"
                                                               routeName="permissions.destroy" name="{{__('messages.Delete')}}"/>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-muted py-4">{{ __('messages.No permissions found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Sweet Alert --}}
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
