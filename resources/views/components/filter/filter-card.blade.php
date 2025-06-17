@props(['meetings'])
@php
$direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp

<div class="container" style="">
    <div class="row justify-content-center" style="">
        <div class="col-11 col-md-6">

            <!-- Search Input -->
            <input type="search" id="search-input" class="form-control mb-3" placeholder="{{__('messages.Search meetings')}}...">

            @foreach($meetings as $meeting)
                <div class="meetings-section mt-4">
                    <div class="meetings-list" dir="{{ $direction }}">

                        @if($meeting->type=="open")
                            <div class="meeting-item" style="border: 4px solid crimson;">
                        @elseif($meeting->status=="suspended")
                            <div class="meeting-item-suspended" style="border: 4px dashed #444;">
                                <div style="text-align: center;font-size: x-large;color: crimson;">
                                    {{ __('messages.suspended') }}
                                </div>
                        @else
                            <div class="meeting-item">
                        @endif
                        <div style="text-align: center;font-size: x-large;color: blue;">
                            {{ $direction === 'rtl' ? $meeting->group->ar_name : $meeting->group->en_name }}
                        </div>
                        <!-- Day and Time Row -->
                        <div class="meeting-time-row">
                            <div class="meeting-day text-danger mb-2 {{ $direction === 'rtl' ? 'text-end' : 'text-end' }}">
                                <x-fas-calendar-day style="width:16px; height:16px;"/>&NonBreakingSpace;
                                {{ $direction === 'rtl' ? $meeting->day->ar_name : $meeting->day->en_name }}
                                
                                <span class="meeting-start-time" dir="ltr" style="float: left;">
                                    <x-fas-clock style="width:16px; height:16px;"/>&NonBreakingSpace;
                                    {{ \Carbon\Carbon::parse($meeting->start_time)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse($meeting->end_time)->format('h:i A') }}
                                </span>
                            </div>
                        </div>
                    
                        <!-- Type and Topic in a single row -->
                        <div class="meeting-type-topic">
                            @if($meeting->topic)
                                <div class="meeting-type-badge">
                                    <x-fas-book-open style="width:16px; height:16px;"/>
                                    {{ $direction === 'rtl' ? $meeting->topic->ar_name : $meeting->topic->en_name }}
                                </div>
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
                            @if($meeting->capacity)
                                <div class="meeting-topic-badge">
                                    <x-fas-users style="width:16px; height:16px;"/>
                                    {{$meeting->capacity }}
                                </div>
                            @endif
                        </div>
                        <!-- GSR DATA -->
                        @if($meeting->group->phone)
                        <div>
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
                                <x-fas-circle style="width:16px; height:16px;"/>&NonBreakingSpace;{{ __('messages.Options') }}
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
                                <x-fas-map-marker style="width:16px; height:16px;"/>
                                <a href="{{ $meeting->group->location }}" target="_blank">الخريطة</a>
                                @endif
                            </div>
                        @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .group-info-container {
        background-color: #f8fafc;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        max-width: 800px;
        margin: 0 auto;
        border: 1px solid #e2e8f0;
    }

    .info-block {
        display: flex;
        align-items: center;
        padding: 14px 18px;
        margin-bottom: 12px;
        background-color: white;
        border-radius: 8px;
        border-left: 4px solid #4f46e5;
        border-right: 4px solid #4f46e5;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        transition: all 0.2s ease;
    }

    .info-block:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.08);
    }

    .info-label {
        font-weight: 600;
        color: #4b5563; /* Cool gray for labels */
        min-width: 200px;
        font-size: 0.95rem;
        letter-spacing: 0.3px;
    }

    .info-value {
        font-weight: 500;
        color: #1e40af; /* Deep blue for values */
        font-size: 1rem;
        padding-left: 12px;
        margin-left: 12px;
        border-left: 1px solid #e2e8f0;
    }

    /* Alternative color scheme option */
    /* .info-label {
        color: #6b7280;
    }
    .info-value {
        color: #065f46;
    } */

    @media (max-width: 768px) {
        .group-info-container {
            padding: 15px;
        }

        .info-block {
            flex-direction: column;
            align-items: flex-start;
            padding: 12px 15px;
        }

        .info-value {
            border-left: none;
            margin-left: 0;
            padding-left: 0;
            padding-top: 6px;
            color: #1e3a8a; /* Slightly darker blue on mobile */
        }
    }

    [dir="rtl"] .info-block {
        border-right: 4px solid #4f46e5;
        border-right: 4px solid #4f46e5;
    }
    [dir="rtl"] .meeting-item {
        border-right: 4px solid #4f46e5;
        border-left: 4px solid #4f46e5;
    }

    .meeting-item {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.05);
        border-left: 4px solid #4f46e5;
        border-right: 4px solid #4f46e5;
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 20px; /* Added space between cards */
        transition: all 0.3s ease;
    }

    .meeting-item-suspended {
        background-color: #aaaaaa;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.05);
        /*border-left: 4px dashed #444;
        border-right: 4px dashed #444;*/
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 20px; /* Added space between cards */
        transition: all 0.3s ease;
    }

    .meeting-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .meeting-type-topic {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin: 8px 0;
    }

    .meeting-type-badge, .meeting-topic-badge, .meeting-open-badge {
        background-color: #f0f7ff;
        color: #1e40af;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .meeting-topic-badge {
        background-color: #f0fff4;
        color: #065f46;
    }
    
    .meeting-open-badge {
        background-color: whitesmoke;
        color: #ff1717;
    }

    .meeting-type-badge i, .meeting-topic-badge i {
        font-size: 0.8rem;
    }

    .meeting-description {
        color: #4b5563;
        font-size: 0.95rem;
        line-height: 1.6;
        padding: 12px;
        background-color: #f8fafc;
        border-radius: 8px;
        position: relative;
    }

    .description-icon {
        color: #6b7280;
        margin-right: 8px;
    }

    .meeting-options {
        margin-top: 10px;
        padding: 10px;
        background-color: #f9fafb;
        border-radius: 8px;
        border: 1px dashed #e2e8f0;
    }

    .options-title {
        font-weight: 600;
        color: #4b5563;
        font-size: 0.95rem;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .options-list {
        display: flex;
        flex-wrap: wrap;
        gap: 3px;
    }

    .option-item {
        display: flex;
        align-items: center;
        font-size: 0.85rem;
        background-color: white;
        padding: 4px 4px;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
        direction: ltr;
    }

    .option-name {
        color: #374151;
        /* margin-right: 6px; */
        font-weight: 500;
    }

    .option-value {
        color: #1e40af;
        font-weight: 600;
    }

    .option-item i.fa-check-circle {
        color: #10b981;
        font-size: 0.9rem;
    }

    .meeting-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
    }

    .action-btn {
        padding: 8px 16px;
        font-size: 0.9rem;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        transform: translateY(-1px);
    }

    .no-meetings {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        color: #6b7280;
        font-size: 1rem;
    }

    .no-meetings i {
        font-size: 1.2rem;
        margin-right: 8px;
        color: #9ca3af;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .meeting-item {
            padding: 10px;
        }

        .meeting-type-topic {
            flex-direction: column;
            gap: 8px;
        }

        .meeting-actions {
            flex-direction: column;
        }

        .action-btn {
            width: 100%;
            text-align: center;
        }
    }

</style>

