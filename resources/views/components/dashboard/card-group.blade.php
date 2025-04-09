@props(['groups'])

<div class="groups-container">
    @foreach ($groups as $group)
    <div class="group-item">

        <div class="sellers-list-item group-item">
            <div class="d-flex justify-content-between align-items-center">
            <p class="mb-1 text-primary group-name fs-md-1 ">
                <a href="{{ route('searches.meeting', ['id' => $group->id]) }}">
                    @if(app()->getLocale() === 'ar')
                        {{ $group->ar_name }}
                    @else
                        {{ $group->en_name }}
                    @endif
                </a>
            </p>
            <p class="fixed-size small mb-0 ">
                <a href="{{ route('searches.city', $group->neighborhood->city->id) }}" class="text-danger" >
                    {{ $group->neighborhood->city->name }}</p>
                </a>
            <p class="fixed-size small mb-0 group-neighborhood bg-gradient-warning rounded-pill p-1 text-black">
                @if(app()->getLocale() === 'ar')
                    {{ $group->neighborhood->ar_name }}
                @else
                    {{ $group->neighborhood->en_name }}
                @endif

            </p>
            </div>
            <div class="d-flex justify-content-between align-items-center p-1">
            <p class="small mt-1 mb-0 group-service-body">
                @if(app()->getLocale() === 'ar')
                    {{ $group->serviceBody->ar_name }}
                @else
                    {{ $group->serviceBody->en_name }}
                @endif

            </p>
            <p class="fixed-size small mb-0 ">{{ $group->meetings->count() }} per week</p>
            </div>
            <x-divider class="group-divider"/>
        </div>
    </div>

    @endforeach
</div>