<x-layout>

  <!--start content-->

    
    {{-- Cards --}}
    {{-- Cards --}}
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-4">
      
      {{-- total meetings --}}
      <div class="col">
        <a href="{{ route('meeting.index') }}" class="text-decoration-none">
          <x-dashboard.card-statics name="{{ __('messages.Total') . ' ' . __('messages.Meetings') }}" qty="{{ $meetings->count() }}" class="bg-primary text-white" icon="calendar-week" />
        </a>
      </div>
      {{-- / total meetings --}}
    
      {{-- total Groups --}}
      <div class="col">
        <a href="{{ route('group.index') }}" class="text-decoration-none">
          <x-dashboard.card-statics name="{{ __('messages.Total') . ' ' . __('messages.Groups') }}" qty="{{ $groups->count() }}" class="bg-success text-white" icon="people-fill" />
        </a>
      </div>
      {{-- / total Groups --}}

      {{-- Total cities --}}
      <div class="col">
        <a href="{{ route('city.index') }}" class="text-decoration-none">
          <x-dashboard.card-statics name="{{ __('messages.Total') . ' ' . __('messages.Cities') }}" qty="{{ $cities->count() }}" class="bg-warning text-dark" icon="geo-alt-fill" />
        </a>
      </div>
      {{-- / Total cities --}}

      {{-- Total Users --}}
      <div class="col">
          <x-dashboard.card-statics name="{{ __('messages.Total Users') }}" qty="5" class="bg-info text-dark" icon="person-badge" />
      </div>
      {{-- / Total Users --}}

      {{-- Committee Reports --}}
      <div class="col">
        <a href="{{ route('committee-reports.index') }}" class="text-decoration-none">
          <x-dashboard.card-statics name="{{ __('messages.Committee Reports') }}" qty="-" class="bg-danger text-white" icon="file-earmark-text" />
        </a>
      </div>
      {{-- / Committee Reports --}}
    </div>
    {{-- / Cards --}}

    <div class="row g-3 mb-4">

      {{-- Groups --}}
      <div class="col-12 col-lg-4 d-flex">
        <div class="card radius-10 w-100 border-0 shadow-sm h-100">
          <x-dashboard.card-header>{{ __('messages.Groups') }}</x-dashboard.card-header>
          <div class="px-3 pt-3">
            <input type="search" id="search-input" class="form-control" placeholder="{{ __('messages.Search') }}...">
          </div>
          <div class="top-sellers-list p-3 mb-3" style="max-height: 400px; overflow-y: auto;">
            <x-dashboard.card-group :$groups />
          </div>
        </div>
      </div>
      {{-- Groups --}}

      {{-- List Of Meetings in Spacific City --}}
      <div class="col-12 col-lg-4 d-flex">
        <div class="card radius-10 w-100 border-0 shadow-sm h-100">
          <x-dashboard.card-header>{{ __('messages.Meetings') . ' ' . __('messages.in') . ' ' . __('messages.City') }}</x-dashboard.card-header>
          <div class="card-body p-0">
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
        <div class="card radius-10 w-100 border-0 shadow-sm h-100">
          <x-dashboard.card-header>{{ __('messages.Groups') . ' ' . __('messages.in') . ' ' . __('messages.City') }}</x-dashboard.card-header>
          <div class="card-body p-0">
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
        <div class="card radius-10 w-100 border-0 shadow-sm">
          <div class="card-header bg-transparent border-0 border-bottom py-3">
            <h5 class="mb-0 text-dark fw-bold">{{ __('messages.Recent Logs')}}</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive w-100">
              <table class="table align-middle mb-0 table-hover">
                <thead class="table-light">
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
                      {{-- <td>{{ $trans->id }}</td> --}}
                      <td><span class="badge bg-light text-dark border">{{ ucfirst($trans->operation) }}</span></td>
                      <td>{{ $trans->model }}</td>
                      <td class="fw-bold text-primary">{{ $trans->user->name ?? 'System' }}</td>
                      <td>{{ $trans->created_at->format('Y-m-d') }}</td>
                      <td>{{ $trans->created_at->format('H:i:s') }}</td>
                      <td>
                          @if ($trans->model === 'Meeting')
                              {{ $trans->user->email }}

                          @else
                          {{  $trans->user->email ?? $trans->details['en_name'] }}
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





 