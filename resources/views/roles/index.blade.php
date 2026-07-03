<x-layout>
    <x-backhead>{{__('messages.Manage Roles')}}</x-backhead>

    <div class="container py-3">
        <div class="card border-0 shadow-lg" style="background: var(--glass-bg); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid var(--glass-border); border-radius: 16px;">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center p-4">
                <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">{{__('messages.Roles List') ?? 'Roles List'}}</h5>
                <x-button-a href="{{ route('roles.create') }}" color='outline-primary' name='{{__("messages.New Role")}}'/>
            </div>
            
            <div class="card-body p-4 pt-0">
                <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
                    <table class="main-tables manage-member text-center table table-hover align-middle" id="example" style="border-collapse: separate; border-spacing: 0 8px;">
                        <thead>
                            <tr class="table-light-header" style="border-radius: 8px;">
                                <th style="border-bottom: 2px solid var(--glass-border); padding: 16px; color: var(--text-primary); font-weight: 600;">{{__('messages.Role Name')}}</th>
                                <th style="border-bottom: 2px solid var(--glass-border); padding: 16px; color: var(--text-primary); font-weight: 600;">{{__('messages.Permission Name')}}</th>
                                <th style="border-bottom: 2px solid var(--glass-border); padding: 16px; color: var(--text-primary); font-weight: 600;">{{__('messages.Control')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($roles as $role)
                            <tr style="background: rgba(255, 255, 255, 0.02); transition: all 0.2s ease;">
                                <td class="fw-bold" style="padding: 16px; color: var(--text-primary);">{{ $role->name }}</td>
                                <td style="padding: 16px; text-align: start;">
                                    <div class="d-flex flex-wrap gap-1 justify-content-start">
                                        @forelse($role->permissions as $permission)
                                            <span class="badge" style="background: rgba(52, 97, 255, 0.08); color: #3461ff; border: 1px solid rgba(52, 97, 255, 0.15); padding: 6px 12px; border-radius: 6px; font-weight: 500; font-size: 12px; transition: all 0.2s;" onmouseover="this.style.background='rgba(52, 97, 255, 0.15)'" onmouseout="this.style.background='rgba(52, 97, 255, 0.08)'">
                                                {{ $permission->name }}
                                            </span>
                                        @empty
                                            <span class="text-muted fs-7">{{ __('messages.No permissions assigned') ?? 'No permissions assigned' }}</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td style="padding: 16px; min-width: 180px;">
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <x-button-a href="{{ route('roles.assign-permissions', $role->id) }}"
                                                  color='outline-warning'
                                                  name='{{__("messages.Edit Permissions")}}'/>

                                        <x-forms.delete-button
                                                formName='delete-role' id="{{ $role->id }}"
                                                routeName="roles.destroy"
                                                name="{{__('messages.Delete')}}"/>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layout>