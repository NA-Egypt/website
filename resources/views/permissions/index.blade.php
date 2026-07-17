<x-layout>
    <x-backhead>{{__('messages.Manage Permissions')}}</x-backhead>

    <div class="container py-3">
        <div class="card border-0 shadow-lg" style="background: var(--glass-bg); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid var(--glass-border); border-radius: 16px;">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">{{__('messages.Permissions List') ?? 'Permissions List'}}</h5>
            </div>

            <div class="card-body p-4 pt-0">
                @php
                $columns = [
                    ['field' => 'name', 'title' => __('messages.Permission Name'), 'sort' => true],
                    ['field' => 'description', 'title' => __('messages.Permission Description'), 'sort' => true],
                    ['field' => 'actions', 'title' => __('messages.Control'), 'sort' => false]
                ];
                @endphp

                <div data-vue-app="GenericDataTable"
                     data-fetch-url="{{ route('permissions.index') }}"
                     data-columns="{{ json_encode($columns) }}"
                     data-create-route="{{ route('permissions.create') }}"
                     data-create-label="{{ __('messages.New Permission') }}"
                     data-edit-route-template="{{ str_replace('1', '{id}', route('permissions.edit', ['permission' => 1])) }}"
                     data-delete-route-name="permissions.destroy"
                     data-delete-route-template="{{ str_replace('1', '{id}', route('permissions.destroy', ['permission' => 1])) }}">
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
