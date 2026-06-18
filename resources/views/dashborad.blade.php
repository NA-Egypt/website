<x-layout>

  <!--start content-->

    
    {{-- Cards --}}
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-4">
      
      {{-- total meetings --}}
      <div class="col">
        <a href="{{ route('meeting.index') }}" class="text-decoration-none">
          <x-dashboard.card-statics name="{{ __('messages.Total') . ' ' . __('messages.Meetings') }}" qty="{{ $meetings->count() }}" color-theme="success" icon="calendar-week" />
        </a>
      </div>
      {{-- / total meetings --}}
    
      {{-- total Groups --}}
      <div class="col">
        <a href="{{ route('group.index') }}" class="text-decoration-none">
          <x-dashboard.card-statics name="{{ __('messages.Total') . ' ' . __('messages.Groups') }}" qty="{{ $groups->count() }}" color-theme="primary" icon="people-fill" />
        </a>
      </div>
      {{-- / total Groups --}}

      {{-- Total cities --}}
      <div class="col">
        <a href="{{ route('city.index') }}" class="text-decoration-none">
          <x-dashboard.card-statics name="{{ __('messages.Total') . ' ' . __('messages.Cities') }}" qty="{{ $cities->count() }}" color-theme="warning" icon="geo-alt-fill" />
        </a>
      </div>
      {{-- / Total cities --}}

      @if (!auth()->user()->hasRole('ServiceBody'))
      {{-- Total Users --}}
      <div class="col">
          <x-dashboard.card-statics name="{{ __('messages.Total Users') }}" qty="{{ $usersCount }}" color-theme="info" icon="person-badge" />
      </div>
      {{-- / Total Users --}}
      @endif

      {{-- Committee Reports --}}
      <div class="col">
        <a href="{{ route('committee-reports.index') }}" class="text-decoration-none">
          <x-dashboard.card-statics name="{{ __('messages.Committee Reports') }}" qty="{{ $reportsCount }}" color-theme="danger" icon="file-earmark-text" />
        </a>
      </div>
      {{-- / Committee Reports --}}

      {{-- Custom Forms --}}
      <div class="col">
        <a href="{{ route('forms.index') }}" class="text-decoration-none">
          <x-dashboard.card-statics name="{{ app()->getLocale() === 'ar' ? 'النماذج المخصصة' : 'Custom Forms' }}" qty="{{ $customFormsCount }}" color-theme="info" icon="file-earmark-spreadsheet" />
        </a>
      </div>
      {{-- / Custom Forms --}}

      {{-- Export Meetings for Print --}}
      <div class="col">
        <a href="{{ route('meetings.export.wizard') }}" class="text-decoration-none">
          <x-dashboard.card-statics name="{{ app()->getLocale() === 'ar' ? 'تصدير للطباعة' : 'Export for Print' }}" qty="PDF" color-theme="primary" icon="printer" />
        </a>
      </div>
      {{-- / Export Meetings for Print --}}
    </div>
    {{-- / Cards --}}
    {{-- / Cards --}}

    <div class="row g-3 mb-4">

      {{-- Groups --}}
      <div class="col-12 col-lg-6 d-flex">
        <div class="glass-card w-100 h-100 p-0" style="display: flex; flex-direction: column;">
          <x-dashboard.card-header>{{ __('messages.Groups') }}</x-dashboard.card-header>
          <div class="px-3 pt-3">
            <div class="position-relative">
               <input type="search" id="search-input" class="form-control rounded-pill bg-transparent border shadow-none glass-search-input" placeholder="{{ __('messages.Search') }}...">
               <i class="bi bi-search position-absolute top-50 translate-middle-y end-0 me-3" style="color: var(--text-secondary); pointer-events: none;"></i>
            </div>
          </div>
          <div class="top-sellers-list p-3 mb-3 neo-scrollbar" style="max-height: 400px; overflow-y: auto; overflow-x: hidden; flex-grow: 1;">
            <x-dashboard.card-group :$groups />
          </div>
        </div>
      </div>
      {{-- Groups --}}

      <style>
          .glass-search-input {
              color: var(--text-primary);
              border-color: var(--glass-border) !important;
              transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
              padding-inline-end: 2.5rem !important;
          }
          .glass-search-input:focus {
              background: rgba(255, 255, 255, 0.05) !important;
              border-color: rgba(59, 130, 246, 0.6) !important;
              box-shadow: 0 0 15px rgba(59, 130, 246, 0.2) !important;
          }
          /* Custom scrollbar adjustments for matching glass aesthetic */
          .neo-scrollbar::-webkit-scrollbar-thumb {
              background: rgba(59, 130, 246, 0.2) !important;
              border-radius: 10px;
              transition: background 0.3s;
          }
          .neo-scrollbar::-webkit-scrollbar-thumb:hover {
              background: rgba(59, 130, 246, 0.4) !important;
          }
      </style>

      {{-- List Of Meetings & Groups in Specific City --}}
      <div class="col-12 col-lg-6 d-flex">
        <div class="glass-card w-100 h-100 p-0" style="display: flex; flex-direction: column;">
          <x-dashboard.card-header>{{ app()->getLocale() === 'ar' ? 'الاجتماعات والمجموعات في المدينة' : 'Meetings & Groups in City' }}</x-dashboard.card-header>
          <div class="card-body p-3 neo-scrollbar" style="max-height: 400px; overflow-y: auto; overflow-x: hidden; flex-grow: 1;">
            @foreach ($cities as $city)
              @php
                $meetingsCount = $city->neighborhoods->sum(fn($neighborhood) => 
                    $neighborhood->groups->sum(fn($group) => $group->meetings->count()));
                $groupsCount = $city->neighborhoods->sum(fn($neighborhood) => $neighborhood->groups->count());
              @endphp
              <x-dashboard.card-city-stats :$city :$groupsCount :$meetingsCount />
            @endforeach
          </div>
        </div>
      </div>
      {{-- List Of Meetings & Groups in Specific City --}}

    </div>

    @if (auth()->user()->hasRole('ServiceBody'))
    {{-- ServiceBody Agendas --}}
    <div class="row mb-4">
      <div class="col-12">
        <div class="neo-table-wrapper w-100">
          <div class="bg-transparent border-0 border-bottom py-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid var(--glass-border) !important;">
            <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">{{ __('messages.Agendas') ?? 'Agendas' }}</h5>
            <a href="{{ route('serviceBody.agendas', auth()->user()->service_body_id ?? 0) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.View All') ?? 'View All' }}</a>
          </div>
          <div class="p-0">
            <div class="table-responsive w-100">
              <table class="table neo-table align-middle">
                <thead class="table-light">
                  <tr>
                    <th>{{ __('messages.Group') ?? 'Group' }}</th>
                    <th>{{ __('messages.Month/Year') ?? 'Month/Year' }}</th>
                    <th>{{ __('messages.Date Submitted') ?? 'Date Submitted' }}</th>
                    <th>{{ __('messages.Submitter') ?? 'Submitter' }}</th>
                    <th>{{ __('messages.Position') ?? 'Position' }}</th>
                    <th>{{ __('messages.Actions') ?? 'Actions' }}</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($agendas->take(5) as $agenda)
                    <tr>
                      <td>
                        <a href="{{ route('group.show', $agenda->group_id) }}" class="text-decoration-none fw-bold">
                            {{ $agenda->group->{app()->getLocale() . '_name'} }}
                        </a>
                      </td>
                      <td>
                        <span class="badge bg-primary rounded-pill px-3 py-2">
                            {{ \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'F Y') }}
                        </span>
                      </td>
                      <td class="text-secondary">
                        {{ \App\Services\DateNumberHelper::translatedFormat($agenda->created_at, 'd M Y, h:i A') }}
                      </td>
                      <td>
                        @if($agenda->submitter_name)
                            {{ $agenda->submitter_name }}
                        @else
                            <span class="text-muted fst-italic">{{ __('messages.Not provided') ?? 'Not provided' }}</span>
                        @endif
                      </td>
                      <td>
                        <span class="badge bg-info text-dark rounded-pill">{{ $agenda->translated_service_position }}</span>
                      </td>
                      <td>
                        <a href="{{ route('agenda.show', $agenda->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="bi bi-eye"></i> {{ __('messages.View') ?? 'View' }}
                        </a>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center" style="color: var(--text-secondary);">
                        <i class="bi bi-inbox text-secondary" style="font-size: 2rem; opacity: 0.5;"></i><br>
                        {{ __('messages.No agendas found') ?? 'No agendas found' }}
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    {{-- / ServiceBody Agendas --}}
    @endif
  
    @if (!auth()->user()->hasRole('ServiceBody'))
    {{-- Recent Transactions --}}
    <div class="row">
      <div class="col-12">
        <div class="neo-table-wrapper w-100">
          <div class="bg-transparent border-0 border-bottom py-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid var(--glass-border) !important;">
            <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">{{ __('messages.Recent Logs')}}</h5>
          </div>
          <div class="p-0">
            <div class="table-responsive w-100">
              <table class="table neo-table align-middle">
                <thead>
                  <tr>
                    {{-- <th>#{{ __('messages.ID')}}</th> --}}
                    <th>{{  __('messages.Operation') }}</th>
                    <th>{{  __('messages.Model') }}</th>
                    <th>{{  __('messages.User') }}</th>
                    <th>{{  __('messages.Date') }}</th>
                    <th>{{  __('messages.Time') }}</th>
                    <th>{{  __('messages.Email') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($transactions as $trans)                    
                    <tr>
                      @php
                        $badgeClass = 'neo-badge-primary';
                        if (strtolower($trans->operation) === 'create') $badgeClass = 'neo-badge-success';
                        elseif (strtolower($trans->operation) === 'delete') $badgeClass = 'neo-badge-danger';
                        elseif (strtolower($trans->operation) === 'info') $badgeClass = 'neo-badge-info';
                      @endphp
                      <td><span class="neo-badge {{ $badgeClass }}">{{ ucfirst($trans->operation) }}</span></td>
                      <td style="color: var(--text-secondary);">{{ $trans->model }}</td>
                      <td class="fw-bold" style="color: #3b82f6;">{{ $trans->user->name ?? 'System' }}</td>
                      <td style="color: var(--text-secondary);">{{ $trans->created_at->format('Y-m-d') }}</td>
                      <td style="color: var(--text-secondary);">{{ $trans->created_at->format('H:i:s') }}</td>
                      <td style="color: var(--text-secondary);">
                          @if ($trans->model === 'Meeting')
                              {{ $trans->user->email }}

                          @else
                          {{  $trans->user->email ?? $trans->details['en_name'] ?? '' }}
                          @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    {{-- / Recent Transactions --}}
    @endif

  <!--end page main-->
  
</x-layout>





 