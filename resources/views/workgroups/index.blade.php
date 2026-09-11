<x-layout>

    <x-backhead>{{ __('messages.Manage Workgroups') }}</x-backhead>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Top Info Header --}}
        @if(!$isSuperAdmin && $myCommittee)
            <div class="glass-card p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 46px; height: 46px;">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ app()->getLocale() === 'ar' ? $myCommittee->ar_name : $myCommittee->en_name }}</h5>
                        <small class="text-muted">{{ __('messages.Sub-Workgroups') }}</small>
                    </div>
                </div>
                <a href="{{ route('workgroup.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle"></i>
                    <span>{{ __('messages.Add Workgroup') }}</span>
                </a>
            </div>
        @endif

        @php
        $columns = [
            ['field' => 'ar_name', 'title' => __('messages.Arabic Service Committee Name'), 'sort' => true],
            ['field' => 'en_name', 'title' => __('messages.English Service Committee Name'), 'sort' => true],
            ['field' => 'parent_name', 'title' => __('messages.Parent Committee'), 'sort' => false],
            ['field' => 'workgroup_type', 'title' => __('messages.Workgroup Type'), 'sort' => true],
            ['field' => 'status', 'title' => __('messages.Workgroup Status'), 'sort' => true],
            ['field' => 'notes', 'title' => __('messages.Workgroup Meetings'), 'sort' => false],
            ['field' => 'actions', 'title' => __('messages.Control'), 'sort' => false]
        ];
        @endphp

        <div data-vue-app="GenericDataTable"
             data-fetch-url="{{ route('workgroup.index') }}"
             data-columns="{{ json_encode($columns) }}"
             data-create-route="{{ route('workgroup.create') }}"
             data-create-label="{{ __('messages.Add Workgroup') }}"
             data-edit-route-template="{{ str_replace('1', '{id}', route('workgroup.edit', ['workgroup' => 1])) }}"
             data-show-route-template="{{ str_replace('1', '{id}', route('workgroup.show', ['workgroup' => 1])) }}"
             data-delete-route-name="workgroup.destroy"
             data-delete-route-template="{{ str_replace('1', '{id}', route('workgroup.destroy', ['workgroup' => 1])) }}">
        </div>
    </div>

</x-layout>
