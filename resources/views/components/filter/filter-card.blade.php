@props(['meetings'])
@php
$direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';

// Group meetings by city
$groupedMeetings = $meetings->groupBy(function($meeting) use ($direction) {
    $group = $meeting->groupOrDirect;
    $cityObj = $group?->neighborhood?->city;
    if ($cityObj) {
        return $cityObj->id;
    }
    return 'online';
});

// Build details for each section and sort
$sections = collect();
foreach ($groupedMeetings as $cityId => $cityMeetings) {
    if ($cityId === 'online') {
        $name = __('messages.Online / Other');
        $isCairo = false;
        $isOnline = true;
    } else {
        $firstMeeting = $cityMeetings->first();
        $cityObj = $firstMeeting->groupOrDirect?->neighborhood?->city;
        $name = $direction === 'rtl' ? ($cityObj->ar_name ?? $cityObj->en_name) : ($cityObj->en_name ?? $cityObj->ar_name);
        $enNameLower = strtolower($cityObj->en_name ?? '');
        $arName = $cityObj->ar_name ?? '';
        $isCairo = ($enNameLower === 'cairo' || $arName === 'القاهرة');
        $isOnline = false;
    }

    $sections->push((object)[
        'id' => $cityId,
        'name' => $name,
        'isCairo' => $isCairo,
        'isOnline' => $isOnline,
        'count' => $cityMeetings->count(),
        'meetings' => $cityMeetings,
    ]);
}

// Sort sections: Cairo first, then remaining non-online cities by meeting count descending, then online/other last
$sortedSections = $sections->sort(function($a, $b) {
    if ($a->isCairo) return -1;
    if ($b->isCairo) return 1;
    if ($a->isOnline && !$b->isOnline) return 1;
    if (!$a->isOnline && $b->isOnline) return -1;
    if ($a->count !== $b->count) {
        return $b->count <=> $a->count;
    }
    return strcmp($a->name, $b->name);
});

$isFirstCardAcrossSections = true;
@endphp
<style>
    [x-cloak] {
        display: none !important;
    }

    .meeting-item.open-border {
        border: 4px solid #f43f5e !important; /* Rose 500 */
    }

    .meeting-item.closed-border {
        border: 4px solid #3b82f6 !important; /* Blue 500 */
    }

    .meeting-item.online-border {
        border: 4px solid #10b981 !important; /* Emerald 500 */
    }

    .meeting-item.business-border {
        border: 4px solid #ffc107 !important; /* Yellow / Warning */
    }

    .meeting-item-suspended, .meeting-item {
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        height: 100% !important;
        width: 100% !important;
        min-height: 360px !important;
        padding: 14px !important; /* Sane default padding for mobile */
        border-radius: 20px !important;
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1) !important;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        overflow-wrap: break-word !important; /* Prevent text overflow */
        word-wrap: break-word !important;
        hyphens: auto;
    }

    @media (min-width: 576px) {
        .meeting-item-suspended, .meeting-item {
            padding: 24px !important; /* Premium larger padding on desktop */
        }
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

    .meeting-recurring-subrow {
        width: 100%;
        margin-top: -6px;
        margin-bottom: 12px;
        padding: 8px 12px;
        border-radius: 12px;
        background: linear-gradient(135deg, #fff1f2 0%, #f8fafc 100%);
        border: 1px solid rgba(244, 63, 94, 0.22);
        font-size: 0.82rem;
        line-height: 1.4;
        color: #1e293b;
        transition: all 0.25s ease;
    }

    .meeting-recurring-subrow.filter-matched {
        background: linear-gradient(135deg, #ffe4e6 0%, #eff6ff 100%);
        border: 2px solid #f43f5e;
        box-shadow: 0 2px 8px rgba(244, 63, 94, 0.16);
    }

    .recurring-week-label {
        font-weight: 700;
        color: #be123c;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .recurring-mini-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.76rem;
    }

    .recurring-mini-badge.type-open {
        background-color: rgba(244, 63, 94, 0.12);
        color: #e11d48;
        border: 1px solid rgba(244, 63, 94, 0.3);
    }

    .recurring-mini-badge.type-closed {
        background-color: rgba(59, 130, 246, 0.12);
        color: #2563eb;
        border: 1px solid rgba(59, 130, 246, 0.25);
    }

    .recurring-mini-badge.topic-pill {
        background-color: rgba(15, 23, 42, 0.06);
        color: #334155;
    }

    .recurring-mini-badge.time-pill {
        background-color: rgba(255, 255, 255, 0.85);
        color: #475569;
        border: 1px solid rgba(148, 163, 184, 0.35);
    }
</style>
<div class="container-fluid px-1 px-sm-3 justify-content-center" style="max-width: 1140px; width: 100%;">
    @foreach($sortedSections as $section)
        <div class="city-section mb-5" wire:key="city-section-{{ $section->id }}">
            <!-- Glassmorphic Section Header -->
            <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden" @if($loop->first) id="tour-city-section" @endif style="background: rgba(255, 255, 255, 0.85) !important; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(13, 110, 253, 0.15) !important;">
                <div class="card-body py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h4 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                        @if($section->isOnline)
                            <x-fas-video style="width:20px; height:20px;" class="text-success" />
                        @else
                            <x-fas-map-marker-alt style="width:20px; height:20px;" class="text-danger" />
                        @endif
                        <span>{{ $section->name }}</span>
                    </h4>
                    <span class="badge bg-primary rounded-pill px-3 py-2 fs-6 shadow-sm d-inline-flex align-items-center gap-1">
                        <x-fas-calendar-alt style="width:14px; height:14px;" />
                        <span>{{ $section->count }} {{ __('messages.Meetings') }}</span>
                    </span>
                </div>
            </div>

            <!-- Cards Grid for Section -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-3 g-4 {{ $section->meetings->count() === 1 ? 'justify-content-center' : '' }}">
                @foreach($section->meetings as $meeting)
                    @php
                        $group = $meeting->groupOrDirect;
                        $isTargetCard = $isFirstCardAcrossSections;
                        if ($isFirstCardAcrossSections) {
                            $isFirstCardAcrossSections = false;
                        }
                    @endphp
                    <div class="col mb-3 d-flex align-items-stretch"
                         wire:key="meeting-card-{{ $meeting->id }}"
                         dir="{{ $direction }}"
                         @if($isTargetCard) id="tour-meeting-card" @endif
                         @if(!auth()->check() && $group && $group->phone)
                             data-contact-url="{{ route('meetings.contact', ['meeting' => $meeting->id]) }}"
                         @endif
                         x-data="{
                             revealed: {{ auth()->check() ? 'true' : 'false' }},
                             gsrName: '',
                             gsrPhone: '',
                             loadingContact: false,
                             clicks: 0,
                             timer: null,
                             handleNameClick() {
                                 if (this.revealed || this.loadingContact) return;
                                 this.clicks++;
                                 clearTimeout(this.timer);
                                 if (this.clicks >= 5) {
                                     this.clicks = 0;
                                     this.fetchContact();
                                 } else {
                                     this.timer = setTimeout(() => {
                                         this.clicks = 0;
                                     }, 1000);
                                 }
                             },
                             fetchContact() {
                                 const rootEl = this.$root || (this.$el && this.$el.closest('[data-contact-url]')) || this.$el;
                                 const contactUrl = rootEl?.dataset?.contactUrl;
                                 if (!contactUrl) return;
                                 this.loadingContact = true;
                                 fetch(contactUrl, {
                                     method: 'POST',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'Accept': 'application/json',
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                         'X-Requested-With': 'XMLHttpRequest'
                                     }
                                 })
                                 .then(r => r.ok ? r.json() : Promise.reject())
                                 .then(data => {
                                     if (data && data.phone) {
                                         this.gsrName = data.gsr_name || '';
                                         this.gsrPhone = data.phone || '';
                                         this.revealed = true;
                                     }
                                 })
                                 .catch(() => {})
                                 .finally(() => {
                                     this.loadingContact = false;
                                 });
                             }
                         }">
                        @php
                            $isOnline = $meeting->direct_online_group_id !== null || ($group && in_array($group->group_type, ['اونلاين', 'اون لاين', 'online']));
                            $isBusiness = $meeting->topics && $meeting->topics->contains('en_name', 'Group Business Meeting');
                        @endphp
                        @if($meeting->status=="suspended")
                            <div class="meeting-item-suspended">
                                <div style="text-align: center;font-size: x-large;color: crimson;">
                                    {{ __('messages.meeting_suspended_notice') }}
                                </div>
                        @elseif($isBusiness)
                            <div class="meeting-item business-border">
                        @elseif($isOnline)
                            <div class="meeting-item online-border">
                        @elseif($meeting->type=="open")
                            <div class="meeting-item open-border">
                        @else
                            <div class="meeting-item closed-border">
                        @endif

                        <!-- Day and Time Row -->
                        <div class="meeting-time-row d-flex justify-content-between align-items-center flex-wrap gap-2" @if($isTargetCard) id="tour-card-time" @endif>
                            <div class="meeting-day text-danger mb-0">
                                <x-fas-calendar-day style="width:16px; height:16px;"/>&NonBreakingSpace;
                                @if(empty($meeting->recurrence) || in_array('weekly', $meeting->recurrence) || ($meeting->recurring_variations && $meeting->recurring_variations->isNotEmpty()))
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

                        <!-- Embedded Recurring Schedule Sub-Header Row -->
                        @if($meeting->recurring_variations && $meeting->recurring_variations->isNotEmpty())
                            @php
                                $parentStartNorm = \Carbon\Carbon::parse($meeting->start_time)->format('H:i');
                                $parentEndNorm = \Carbon\Carbon::parse($meeting->end_time)->format('H:i');
                            @endphp
                            @foreach($meeting->recurring_variations as $variation)
                                @php
                                    $varStartNorm = \Carbon\Carbon::parse($variation->start_time)->format('H:i');
                                    $varEndNorm = \Carbon\Carbon::parse($variation->end_time)->format('H:i');
                                    $hasDifferentTime = ($parentStartNorm !== $varStartNorm || $parentEndNorm !== $varEndNorm);
                                @endphp
                                <div class="meeting-recurring-subrow d-flex align-items-center justify-content-between flex-wrap gap-2 {{ !empty($variation->is_filter_match) ? 'filter-matched' : '' }}">
                                    <span class="recurring-week-label">
                                        <i class="bi bi-arrow-repeat"></i>
                                        <span>{{ $variation->formatted_recurrence }}:</span>
                                    </span>
                                    <div class="d-inline-flex align-items-center flex-wrap gap-1">
                                        @if($hasDifferentTime)
                                            <span class="recurring-mini-badge time-pill" dir="ltr">
                                                <x-fas-clock style="width:11px; height:11px;"/>
                                                <span>{{ \Carbon\Carbon::parse($variation->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($variation->end_time)->format('h:i A') }}</span>
                                            </span>
                                        @endif
                                        @if($variation->type)
                                            <span class="recurring-mini-badge {{ $variation->type === 'open' ? 'type-open' : 'type-closed' }}">
                                                @if($variation->type === 'open')
                                                    <x-fas-circle-notch style="width:11px; height:11px;"/>
                                                @else
                                                    <x-fas-user-alt-slash style="width:11px; height:11px;"/>
                                                @endif
                                                <span>{{ __("messages." . $variation->type) }}</span>
                                            </span>
                                        @endif
                                        @if($variation->topics && $variation->topics->count() > 0)
                                            @foreach($variation->topics as $varTopic)
                                                <span class="recurring-mini-badge topic-pill">
                                                    <x-fas-book-open style="width:11px; height:11px;"/>
                                                    <span>{{ $direction === 'rtl' ? $varTopic->ar_name : $varTopic->en_name }}</span>
                                                </span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        <div class="meeting-group-name" @if($isTargetCard) id="tour-card-group" @endif
                             @click="handleNameClick()"
                             style="user-select: none; -webkit-user-select: none;">
                            {{ $direction === 'rtl' ? $group->ar_name : $group->en_name }}
                        </div>

                        <!-- Type and Topic in a single row -->
                        <div class="meeting-type-topic" @if($isTargetCard) id="tour-card-badges" @endif>
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
                            @if($group && isset($group->capacity) && $group->capacity)
                                <div class="meeting-topic-badge">
                                    <x-fas-users style="width:16px; height:16px;"/>
                                    {{$group->capacity }}
                                </div>
                            @endif
                        </div>
                        <!-- GSR DATA -->
                        @php
                            $isBot = \App\Services\BotDetectionService::isBotOrAi();
                        @endphp
                        @if($group && $group->phone && !$isBot)
                        <div data-nosnippet
                             @if($isTargetCard) id="tour-card-contact" @endif
                             x-show="revealed"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-cloak
                             @if(!auth()->check()) style="display: none;" @endif>
                            @auth
                                <x-fas-user-circle style="width:16px; height:16px;"/>
                                {{ $direction === 'rtl' ? $group->ar_gsr_name : $group->en_gsr_name }}
                                <br />
                                <x-fas-mobile-alt style="width:16px; height:16px;"/>
                                <a href="tel:{{ $group->phone }}" rel="nofollow" dir="ltr" style="direction: ltr !important; unicode-bidi: embed;">
                                    {{ $group->phone }}
                                </a>
                            @else
                                <x-fas-user-circle style="width:16px; height:16px;"/>
                                <span x-text="gsrName"></span>
                                <br />
                                <x-fas-mobile-alt style="width:16px; height:16px;"/>
                                <a :href="'tel:' + gsrPhone" x-text="gsrPhone" rel="nofollow" dir="ltr" style="direction: ltr !important; unicode-bidi: embed;"></a>
                            @endauth
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

                        @if($group && $group->ar_address)
                            <div class="meeting-options">
                                <x-fas-map-pin style="width:16px; height:16px;"/>
                                {{
                                    $direction === 'rtl'
                                        ? $group->ar_address
                                        : ($group->en_address ?: $group->ar_address)
                                }}
                            </div>
                        @endif

                        @if($group && ($group->location || $group->ar_address))
                            @php
                                $shareGroupName = $direction === 'rtl' ? $group->ar_name : $group->en_name;
                                $hasVariations = $meeting->recurring_variations && $meeting->recurring_variations->isNotEmpty();
                                $shareDay = (empty($meeting->recurrence) || in_array('weekly', $meeting->recurrence) || $hasVariations)
                                    ? ($direction === 'rtl' ? $meeting->day->ar_name : $meeting->day->en_name)
                                    : ($meeting->formatted_recurrence . ' - ' . ($direction === 'rtl' ? $meeting->day->ar_name : $meeting->day->en_name));
                                $shareTime = \Carbon\Carbon::parse($meeting->start_time)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($meeting->end_time)->format('h:i A');
                                $shareAddress = $group->ar_address ? ($direction === 'rtl' ? $group->ar_address : ($group->en_address ?: $group->ar_address)) : null;
                                $shareLocationUrl = $group->location;

                                $shareRecurringLines = [];
                                if ($hasVariations) {
                                    $pStart = \Carbon\Carbon::parse($meeting->start_time)->format('H:i');
                                    $pEnd = \Carbon\Carbon::parse($meeting->end_time)->format('H:i');
                                    foreach ($meeting->recurring_variations as $variation) {
                                        $vStart = \Carbon\Carbon::parse($variation->start_time)->format('H:i');
                                        $vEnd = \Carbon\Carbon::parse($variation->end_time)->format('H:i');
                                        $vTimeDiff = ($pStart !== $vStart || $pEnd !== $vEnd);
                                        $vTimeStr = $vTimeDiff
                                            ? (' (' . \Carbon\Carbon::parse($variation->start_time)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($variation->end_time)->format('h:i A') . ')')
                                            : '';
                                        $vParts = [];
                                        if ($variation->type) {
                                            $vParts[] = __("messages." . $variation->type);
                                        }
                                        if ($variation->topics && $variation->topics->count() > 0) {
                                            $vTopicNames = $variation->topics->map(function ($t) use ($direction) {
                                                return $direction === 'rtl' ? $t->ar_name : $t->en_name;
                                            })->implode($direction === 'rtl' ? '، ' : ', ');
                                            if ($vTopicNames) {
                                                $vParts[] = $vTopicNames;
                                            }
                                        }
                                        $vDetails = implode(' - ', $vParts);
                                        $shareRecurringLines[] = "🔄 {$variation->formatted_recurrence}{$vTimeStr}" . ($vDetails !== '' ? ": {$vDetails}" : '');
                                    }
                                }

                                if ($direction === 'rtl') {
                                    $shareText = "اجتماع: {$shareGroupName}\nاليوم: {$shareDay} ({$shareTime})";
                                    if (!empty($shareRecurringLines)) {
                                        $shareText .= "\n" . implode("\n", $shareRecurringLines);
                                    }
                                    if ($shareAddress) {
                                        $shareText .= "\nالعنوان: {$shareAddress}";
                                    }
                                    if ($shareLocationUrl) {
                                        $shareText .= "\nالموقع: {$shareLocationUrl}";
                                    }
                                } else {
                                    $shareText = "Meeting: {$shareGroupName}\nDay: {$shareDay} ({$shareTime})";
                                    if (!empty($shareRecurringLines)) {
                                        $shareText .= "\n" . implode("\n", $shareRecurringLines);
                                    }
                                    if ($shareAddress) {
                                        $shareText .= "\nAddress: {$shareAddress}";
                                    }
                                    if ($shareLocationUrl) {
                                        $shareText .= "\nLocation: {$shareLocationUrl}";
                                    }
                                }
                            @endphp
                            <div class="meeting-options d-flex align-items-center gap-2 flex-wrap mt-2" @if($isTargetCard) id="tour-card-actions" @endif>
                                @if($group->location)
                                    @php
                                        $isZoomLocation = \Illuminate\Support\Str::contains(strtolower($group->location), ['zoom', 'meet', 'teams']) ||
                                                          ($group instanceof \App\Models\DirectOnlineGroup) ||
                                                          ($group->group_type && $group->group_type !== 'فعلي');
                                    @endphp
                                    <a href="{{ $group->location }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 d-inline-flex align-items-center" style="font-size: 0.8rem; font-weight: 600;">
                                    @if(\Illuminate\Support\Str::contains(strtolower($group->location), ['map', 'goo.gl']))
                                        <x-fas-map-marker-alt style="width:12px; height:12px;" class="me-1"/> {{__('messages.Map')}}
                                    @elseif($isZoomLocation)
                                        <x-fas-video style="width:12px; height:12px;" class="me-1"/> {{__('messages.zoomlink')}}
                                    @else
                                        <x-fas-map-marker-alt style="width:12px; height:12px;" class="me-1"/> {{__('messages.Map')}}
                                    @endif
                                    </a>

                                    @if($isZoomLocation)
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 d-inline-flex align-items-center gap-1 zoom-card-qr-btn" onclick="window.openZoomQrModal && window.openZoomQrModal('{{ e($group->location) }}', '{{ e(addslashes($shareGroupName)) }}')" style="font-size: 0.8rem; font-weight: 600;" title="{{ __('messages.zoom_meeting_qr') }}">
                                            <i class="bi bi-qr-code"></i>
                                            <span>QR</span>
                                        </button>
                                    @endif
                                @endif

                                <a href="https://api.whatsapp.com/send?text={{ rawurlencode($shareText) }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 d-inline-flex align-items-center" style="font-size: 0.8rem; font-weight: 600;" title="{{ __('messages.ShareLocation') }}">
                                    <x-fab-whatsapp style="width:12px; height:12px;" class="me-1"/>
                                    <span>{{ __('messages.ShareLocation') }}</span>
                                </a>

                                @if($group instanceof \App\Models\DirectOnlineGroup || \Illuminate\Support\Str::contains(strtolower((string) $group->location), ['zoom', 'meet', 'teams']) || ($group && $group->group_type !== 'فعلي'))
                                    <a href="https://wa.me/201060933888" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3 d-inline-flex align-items-center" style="font-size: 0.8rem; font-weight: 600;">
                                        <x-fab-whatsapp style="width:12px; height:12px;" class="me-1"/>
                                        <span>{{ __('messages.Helpline') }}</span>
                                    </a>
                                @endif
                            </div>
                        @endif
                        </div>
                        @php
                            if ($isTargetCard) {
                                $isFirstCardAcrossSections = false;
                            }
                        @endphp
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
