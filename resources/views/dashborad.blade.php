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

      {{-- Total Users --}}
      <div class="col">
          <x-dashboard.card-statics name="{{ __('messages.Total Users') }}" qty="5" color-theme="info" icon="person-badge" />
      </div>
      {{-- / Total Users --}}

      {{-- Committee Reports --}}
      <div class="col">
        <a href="{{ route('committee-reports.index') }}" class="text-decoration-none">
          <x-dashboard.card-statics name="{{ __('messages.Committee Reports') }}" qty="-" color-theme="danger" icon="file-earmark-text" />
        </a>
      </div>
      {{-- / Committee Reports --}}
    </div>
    {{-- / Cards --}}
    {{-- / Cards --}}

    <div class="row g-3 mb-4">

      {{-- Groups --}}
      <div class="col-12 col-lg-4 d-flex">
        <div class="glass-card w-100 h-100 p-0">
          <x-dashboard.card-header>{{ __('messages.Groups') }}</x-dashboard.card-header>
          <div class="px-3 pt-3">
            <div class="position-relative">
               <input type="search" id="search-input" class="form-control rounded-pill bg-transparent border shadow-none" style="color: var(--text-primary); border-color: var(--glass-border) !important;" placeholder="{{ __('messages.Search') }}...">
               <i class="bi bi-search position-absolute top-50 translate-middle-y end-0 me-3" style="color: var(--text-secondary);"></i>
            </div>
          </div>
          <div class="top-sellers-list p-3 mb-3 neo-scrollbar" style="max-height: 400px; overflow-y: auto;">
            <x-dashboard.card-group :$groups />
          </div>
        </div>
      </div>
      {{-- Groups --}}

      {{-- List Of Meetings in Spacific City --}}
      <div class="col-12 col-lg-4 d-flex">
        <div class="glass-card w-100 h-100 p-0">
          <x-dashboard.card-header>{{ __('messages.Meetings') . ' ' . __('messages.in') . ' ' . __('messages.City') }}</x-dashboard.card-header>
          <div class="card-body p-0 p-3 neo-scrollbar" style="max-height: 400px; overflow-y: auto;">
            @foreach ($cities as $city)
              <x-dashboard.card-meetings :$city>
                {{ $city->neighborhoods->sum(fn($neighborhood) => 
                $neighborhood->groups->sum(fn($group) => $group->meetings->count())) }} 
              </x-dashboard.card-meetings>
            @endforeach
          </div>
        </div>
      </div>
      {{-- List Of Meetings in Spacific City --}}


      {{-- List Of Groups in Spacific City --}}
      <div class="col-12 col-lg-4 d-flex">
        <div class="glass-card w-100 h-100 p-0">
          <x-dashboard.card-header>{{ __('messages.Groups') . ' ' . __('messages.in') . ' ' . __('messages.City') }}</x-dashboard.card-header>
          <div class="card-body p-0 p-3 neo-scrollbar" style="max-height: 400px; overflow-y: auto;">
            @foreach ($cities as $city)
              <x-dashboard.card-cities :$city>
                {{ $city->neighborhoods->sum(fn($neighborhood) => $neighborhood->groups->count()) }}
              </x-dashboard.card-cities>
            @endforeach
          </div>
        </div>
      </div>
      {{-- List Of Groups in Spacific City--}}

    </div>
  
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

  <!--end page main-->
  
</x-layout>





 