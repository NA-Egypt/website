<x-layout>

  <!--start content-->

    
    {{-- Cards --}}
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-2 row-cols-xl-4">
      
      {{-- total meetings --}}
      <a href="{{ route('meeting.index') }}">
        <x-dashboard.card-statics name="{{ __('messages.Total') . ' ' . __('messages.Meetings') }}" qty="{{ $meetings->count() }}" class="bg-gradient-info " icon="calendar-week" />
      </a>
      {{-- / total meetings --}}
    
      {{-- total Groups --}}
      <a href="{{ route('group.index') }}">
        <x-dashboard.card-statics name="{{ __('messages.Total') . ' ' . __('messages.Groups') }}" qty="{{ $groups->count() }}" class="bg-gradient-info " icon="house" />
      </a>
      {{-- / total Groups --}}

      {{-- Total cities --}}
      <a href="{{ route('city.index') }}">
        <x-dashboard.card-statics name="{{ __('messages.Total') . ' ' . __('messages.Cities') }}" qty="{{ $cities->count() }}" class="bg-gradient-info " icon="globe" />
      </a>
      {{-- / Total cities --}}

      {{-- Total Users --}}
      <div class="col">
        <div class="card radius-10">
          <div class="card-body">
              <div class="d-flex align-items-center">
                  <div>
                      <p class="mb-0 text-secondary">{{__('messages.Total Users')}}</p>
                      <h4 class="my-1">5</h4>
                  </div>
                  <div class="widget-icon-large bg-gradient-info text-white ms-auto"><i class="bi bi-people"></i>
                  </div>
              </div>
          </div>
        </div>
      </div>
      {{-- / Total Users --}}
    </div>
    {{-- / Cards --}}

    <div class="row">

      {{-- Groups --}}
      <div class="col-12 col-lg-12 col-xl-4 d-flex">
        <div class="card radius-10 w-100">
          <x-dashboard.card-header>{{ __('messages.Groups') }}</x-dashboard.card-header>
          <div class="d-flex justify-content-end">
            <input type="search" id="search-input" class="form-control form-control-sm" placeholder="Search">
          </div>
          <div class="top-sellers-list p-2 mb-3">
            <x-dashboard.card-group :$groups />
          </div>
        </div>
      </div>
      {{-- Groups --}}

      {{-- List Of Meetings in Spacific City --}}
      <div class="col-12 col-lg-12 col-xl-4 d-flex">
        <div class="card radius-10 w-100">
          <x-dashboard.card-header>{{ __('messages.Meetings') . ' ' . __('messages.in') . ' ' . __('messages.City') }}</x-dashboard.card-header>
          <div class="card-body">
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
      <div class="col-12 col-lg-12 col-xl-4 d-flex">
        <div class="card radius-10 w-100">
          <x-dashboard.card-header>{{ __('messages.Groups') . ' ' . __('messages.in') . ' ' . __('messages.City') }}</x-dashboard.card-header>
          <div class="card-body">
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
    <div class="card radius-10">
      <div class="card-header bg-secondary bg-gradient">
        <div class="row g-3 align-items-center ">
          <div class="col ">
            <p class="mb-0 text-black">{{ __('messages.Recent Logs')}}</p>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>#{{ __('messages.ID')}}</th>
                <th>{{  __('messages.Operation') }}</th>
                <th>{{  __('messages.Model') }}</th>
                <th>{{  __('messages.User') }}</th>
                <th>{{  __('messages.Date') }}</th>
                <th>{{  __('messages.Time') }}</th>
                <th>{{  __('messages.Name') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($transactions as $trans)                    
                <tr>
                  <td>{{ $trans->id }}</td>
                  <td>{{ ucfirst($trans->operation) }}</td>
                  <td>{{ $trans->model }}</td>
                  <td>{{ $trans->user->name ?? 'System' }}</td>
                  <td>{{ $trans->created_at->format('Y-m-d') }}</td>
                  <td>{{ $trans->created_at->format('H:i:s') }}</td>
                  <td>
                      @if ($trans->model === 'Meeting')
                          {{ $trans->group_name }}

                      @else
                      {{ $trans->details['en_name'] ?? '' }}
                      @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    {{-- / Recent Transactions --}}

  <!--end page main-->
  
</x-layout>





 