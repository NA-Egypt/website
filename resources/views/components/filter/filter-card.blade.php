@props(['meetings'])
@php
$direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp
<style>
    .meeting-item.open-border {
        border: 4px solid #f43f5e !important; /* Rose 500 */
    }

    .meeting-item.closed-border {
        border: 4px solid #3b82f6 !important; /* Blue 500 */
    }

    .meeting-item.online-border {
        border: 4px solid #10b981 !important; /* Emerald 500 */
    }

    .meeting-item-suspended, .meeting-item {
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        height: 100% !important;
        width: 100% !important;
        min-height: 360px !important;
        padding: 20px !important; /* Premium inside margins */
        border-radius: 20px !important;
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1) !important;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        overflow-wrap: break-word !important; /* Prevent text overflow */
        word-wrap: break-word !important;
        hyphens: auto;
    }
    
    .meeting-item:hover {
        transform: translateY(-8px) !important;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
    }
    
    .meeting-time-row {
        margin-bottom: 15px;
        width: 100%;
    }
    
    .meeting-day {
        font-weight: 700 !important;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #f43f5e !important;
    }

    .meeting-start-time {
        font-weight: 600 !important;
        color: #475569 !important;
    }
    
    .meeting-group-name {
        margin: 15px 0 !important;
        text-align: center;
        font-weight: 800 !important;
        font-size: 1.4rem !important; /* Slightly smaller to prevent overflow */
        color: #0f172a !important;
        line-height: 1.3;
        overflow-wrap: break-word;
    }

    .meeting-type-topic {
        margin: 15px 0 !important;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
    }

    .meeting-type-badge, .meeting-topic-badge, .meeting-open-badge {
        padding: 6px 12px !important; /* More compact badges */
        font-weight: 600 !important;
        border-radius: 10px !important;
        font-size: 0.85rem !important;
    }

    .meeting-item-suspended {
        border: 4px dashed #cbd5e1 !important;
        background-color: #f8fafc !important;
        opacity: 0.8;
    }
</style>
<div class="container px-4 justify-content-center" style="max-width: 1140px;">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-3 g-4 {{ $meetings->count() === 1 ? 'justify-content-center' : '' }}">
@foreach($meetings as $meeting)
@if(!$meeting->group || !$meeting->day) @continue @endif
<div class="col mb-3 d-flex align-items-stretch" dir="{{ $direction }}" @if($loop->first) id="tour-meeting-card" @endif>
    @php
        $isOnline = $meeting->group && in_array($meeting->group->group_type, ['اونلاين', 'اون لاين', 'online']);
    @endphp
    @if($meeting->status=="suspended")
        <div class="meeting-item-suspended">
            <div style="text-align: center;font-size: x-large;color: crimson;">
                {{ __('messages.suspended') }}
            </div>
    @elseif($isOnline)
        <div class="meeting-item online-border">
    @elseif($meeting->type=="open")
        <div class="meeting-item open-border">
    @else
        <div class="meeting-item closed-border">
    @endif


    <!-- Day and Time Row -->
    <div class="meeting-time-row d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="meeting-day text-danger mb-0">
            <x-fas-calendar-day style="width:16px; height:16px;"/>&NonBreakingSpace;
            @if(empty($meeting->recurrence) || in_array('weekly', $meeting->recurrence))
                {{ $direction === 'rtl' ? $meeting->day->ar_name : $meeting->day->en_name }}
            @else
                {{ $meeting->formatted_recurrence }} - {{ $direction === 'rtl' ? $meeting->day->ar_name : $meeting->day->en_name }}
            @endif
        </div>
        <span class="meeting-start-time d-flex align-items-center gap-1" dir="ltr">
            <x-fas-clock style="width:16px; height:16px;"/>&NonBreakingSpace;
            {{ \Carbon\Carbon::parse($meeting->start_time)->format('h:i A') }} -
            {{ \Carbon\Carbon::parse($meeting->end_time)->format('h:i A') }}
        </span>
    </div>
    <div class="meeting-group-name">
        {{ $direction === 'rtl' ? $meeting->group->ar_name : $meeting->group->en_name }}
    </div>

    <!-- Type and Topic in a single row -->
    <div class="meeting-type-topic">
        @if($meeting->topics && $meeting->topics->count() > 0)
            @foreach($meeting->topics as $topic)
                <div class="meeting-type-badge">
                    <x-fas-book-open style="width:16px; height:16px;"/>
                    {{ $direction === 'rtl' ? $topic->ar_name : $topic->en_name }}
                </div>
            @endforeach
        @endif
        @if($meeting->lang)
        <div class="meeting-topic-badge">
            <x-fas-language style="width:16px; height:16px;"/>
            {{ __("messages." . $meeting->lang) }}
        </div>
        @endif
        @if($meeting->type)
            @if($meeting->type=="open")
                <div class="meeting-open-badge" style="border: 2px solid pink;">
                    <x-fas-circle-notch style="width:16px; height:16px;"/>
            @else
                <div class="meeting-type-badge">
                    <x-fas-user-alt-slash style="width:16px; height:16px;"/>
            @endif
                {{ __("messages." . $meeting->type) }}
            </div>
        @endif
        @if($meeting->group && $meeting->group->capacity)
            <div class="meeting-topic-badge">
                <x-fas-users style="width:16px; height:16px;"/>
                {{$meeting->group->capacity }}
            </div>
        @endif
    </div>
    <!-- GSR DATA -->
    @php
        $userAgent = request()->header('User-Agent');
        $isBot = false;
        if ($userAgent) {
            $bots = [
                'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
                'yandexbot', 'sogou', 'exabot', 'facebot', 'ia_archiver',
                'crawler', 'spider', 'bot'
            ];
            foreach ($bots as $bot) {
                if (stripos($userAgent, $bot) !== false) {
                    $isBot = true;
                    break;
                }
            }
        }
    @endphp
    @if($meeting->group->phone && !$isBot)
    <div data-nosnippet>
        <x-fas-user-circle style="width:16px; height:16px;"/>
        {{ $direction === 'rtl' ? $meeting->group->ar_gsr_name : $meeting->group->en_gsr_name }}
        <br />
        <x-fas-mobile-alt style="width:16px; height:16px;"/>
        <a href="tel:{{ $meeting->group->phone }}" itemprop="telephone">
            {{ $meeting->group->phone }}
        </a>
    </div>
    @endif
    <!-- Meeting Options -->
    @if($meeting->options->count() > 0)
    <div class="meeting-options">
        <div class="options-title">
            <x-fas-circle style="width:12px; height:12px;"/>&NonBreakingSpace;{{ __('messages.Options') }}
        </div>
        <div class="options-list">
            @foreach($meeting->options as $option)
                <div class="option-item">
                <span class="option-name">
                    {{ $direction === 'rtl' ? $option->ar_name : $option->en_name }}
                </span>
                <span class="option-value">
                    @if($option->id == 15)
                    &NonBreakingSpace;<x-fas-smoking style="width:16px; height:16px;"/>
                    @elseif($option->id == 14)
                        &NonBreakingSpace;<x-fas-parking style="width:16px; height:16px;"/>
                    @elseif($option->id == 13)
                    &NonBreakingSpace;<x-fas-wheelchair style="width:16px; height:16px;"/>
                    @elseif($option->id == 16)
                    &NonBreakingSpace;<x-fas-fire style="width:16px; height:16px;"/>
                    @endif
                </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    @if($meeting->notes)
    <div class="meeting-options">
        <x-fas-asterisk style="width:16px; height:16px;"/>
        {{ $meeting->notes }}
    </div>
    @endif

    @if($meeting->group->ar_address)
        <div class="meeting-options">
            <x-fas-map-pin style="width:16px; height:16px;"/>
            {{
                $direction === 'rtl'
                    ? $meeting->group->ar_address
                    : ($meeting->group->en_address ?: $meeting->group->ar_address)
            }}
            @if($meeting->group->location)
            <br />
            <a href="{{ $meeting->group->location }}" target="_blank">
            @if(\Illuminate\Support\Str::contains(strtolower($meeting->group->location), ['map', 'goo.gl']))
                <x-fas-map-marker-alt style="width:16px; height:16px;"/> {{__('messages.Map')}}
            @elseif(\Illuminate\Support\Str::contains(strtolower($meeting->group->location), ['zoom', 'meet', 'teams']))
                <x-fas-video style="width:16px; height:16px;"/> {{__('messages.zoomlink')}}
            @elseif($meeting->group->group_type !== 'فعلي')
                <x-fas-video style="width:16px; height:16px;"/> {{__('messages.zoomlink')}}
            @else
                <x-fas-map-marker-alt style="width:16px; height:16px;"/> {{__('messages.Map')}}
            @endif
            </a>
            @endif
        </div>
    @endif
    </div>
</div>
@endforeach
            </div>
        </div>
    </div>
</div>
