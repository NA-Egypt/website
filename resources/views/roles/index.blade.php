<x-layout>
    <x-backhead>{{__('messages.Manage Roles')}}</x-backhead>

    <div class="container py-3">
        <div class="card border-0 shadow-lg" style="background: var(--glass-bg); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid var(--glass-border); border-radius: 16px;">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">{{__('messages.Roles List') ?? 'Roles List'}}</h5>
            </div>
            
            <div class="card-body p-4 pt-0">
                @php
                $columns = [
                    ['field' => 'name', 'title' => __('messages.Role Name'), 'sort' => true],
                    ['field' => 'permissions', 'title' => __('messages.Permission Name'), 'sort' => false, 'renderType' => 'array', 'fieldPath' => 'permissions', 'arrayKey' => 'name'],
                    ['field' => 'actions', 'title' => __('messages.Control'), 'sort' => false]
                ];
                @endphp

                <div data-vue-app="GenericDataTable"
                     data-fetch-url="{{ route('roles.index') }}"
                     data-columns="{{ json_encode($columns) }}"
                     data-create-route="{{ route('roles.create') }}"
                     data-create-label="{{ __('messages.New Role') }}"
                     data-edit-route-template="{{ str_replace('1', '{id}', route('roles.assign-permissions', ['role' => 1])) }}"
                     data-delete-route-name="roles.destroy"
                     data-delete-route-template="{{ str_replace('1', '{id}', route('roles.destroy', ['role' => 1])) }}">
                </div>
            </div>
        </div>
    </div>
</x-layout>