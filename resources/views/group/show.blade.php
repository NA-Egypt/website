<x-layout>
    <x-backhead>{{__("messages.Group information for") }}

        @if(app()->getLocale() === 'ar')
            {{$group->ar_name}}
        @else
            {{$group->en_name}}
        @endif

    </x-backhead>

    <div class="group-info-container">

        {{-- Group Section  --}}

        {{-- Button of edit group details  --}}
        <div class="mb-3">
            <x-button-a href="{{ route('group.edit', $group->id) }}" color='outline-secondary' name="{{  __('messages.Edit Group') }}" />
        </div>
        <!-- Arabic Group Name -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.Arabic Group Name') }}:</div>
            <div class="info-value">{{$group->ar_name}}</div>
        </div>

        <!-- English Group Name -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.English Group Name') }}:</div>
            <div class="info-value">{{$group->en_name}}</div>
        </div>

        <!-- Arabic GSR Name -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.Arabic GSR Name')}}:</div>
            <div class="info-value">{{$group->ar_gsr_name}}</div>
        </div>

        <!-- English GSR Name -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.English GSR Name')}}:</div>
            <div class="info-value">{{$group->en_gsr_name}}</div>
        </div>

        <!-- Email -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.Email')}}:</div>
            <div class="info-value">{{$group->user->email}}</div>
        </div>

        <!-- Phone -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.Phone')}}:</div>
            <div class="info-value">{{$group->phone}}</div>
        </div>

        <!-- Location -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.Locations')}}:</div>
            <div class="info-value">{{$group->location}}</div>
        </div>

        <!-- Arabic Address -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.Arabic Address')}}:</div>
            <div class="info-value">{{$group->ar_address}}</div>
        </div>

        <!-- English Address -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.English Address')}}:</div>
            <div class="info-value">{{$group->en_address}}</div>
        </div>

        <!-- Service Body -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.Service Body')}}:</div>
            <div class="info-value">{{$group->serviceBody->ar_name}}</div>
        </div>

        <!-- Neighborhoods -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.Neighborhood')}}:</div>
            <div class="info-value">{{$group->neighborhood->ar_name}}</div>
        </div>

        {{-- / Group Section  --}}

        {{--  Meetings Section  --}}
        <div class="meetings-section mt-5">
            <h4 class="section-title mb-3 text-center">{{ __('messages.Meetings') }}</h4>
            <div class="mb-3">
                <x-button-a href="{{ route('meeting.create') }}" color='outline-primary' name="{{__('messages.Add') . ' ' . __('messages.Meeting')}}" />
            </div>
                @if($meetings->count() > 0)
                <div class="meetings-list">
                    @foreach($meetings as $meeting)
                        <div class="meeting-item">
                            <!-- Day and Time Row -->
                            <div class="meeting-time-row">
                                <div class="meeting-day text-warning mb-2">
                                    @if(app()->getLocale() === 'ar')
                                        {{ $meeting->day->ar_name }}
                                    @else
                                        {{ $meeting->day->en_name }}
                                    @endif

                                </div>
                                <div class="meeting-time">
                                    {{ \Carbon\Carbon::parse($meeting->start_time)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse($meeting->end_time)->format('h:i A') }}
                                </div>
                            </div>

                            <!-- Title -->
                            @if($meeting->title)
                                <div class="meeting-title">
                                    {{ $meeting->title }}
a
                                </div>
                            @endif

                            <!-- Type and Topic in a single row -->
                            <div class="meeting-type-topic">
                                @if($meeting->type)
                                    <div class="meeting-type-badge">
                                        <i class="fas fa-calendar-alt"></i>
                                        @if(app()->getLocale() === 'ar')
                                            {{ __("messages." . $meeting->type) }}
                                        @else
                                            {{ $meeting->type }}
                                        @endif
                                    </div>
                                @endif

                                @if($meeting->topic)
                                    <div class="meeting-topic-badge">
                                        <i class="fas fa-comment-dots"></i>

                                        @if(app()->getLocale() === 'ar')
                                            {{$meeting->topic->ar_name }}
                                        @else
                                            {{ $meeting->topic->en_name }}
                                        @endif

                                    </div>
                                @endif
                            </div>

                            <!-- Description -->
                            @if($meeting->description)
                                <div class="meeting-description">
                                    <i class="fas fa-align-left description-icon"></i>
                                    {{ $meeting->description }}
                                </div>
                            @endif

                            <!-- Meeting Options -->
                            @if($meeting->options->count() > 0)
                                <div class="meeting-options">
                                    <div class="options-title">
                                        <i class="fas fa-cog"></i> {{ __('messages.Options') }}
                                    </div>
                                    <div class="options-list">
                                        @foreach($meeting->options as $option)
                                            <div class="option-item">
                                                <span class="option-name">
                                                    @if(app()->getLocale() === 'ar')
                                                        {{ $option->ar_name }}
                                                    @else
                                                        {{ $option->en_name }}
                                                    @endif
                                                </span>
                                                <span class="option-value">
                                                @if($option->pivot->value)
                                                        {{ $option->pivot->value }}
                                                    @else
                                                        <i class="fas fa-check-circle"></i>
                                                    @endif
                                            </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="meeting-actions">
                                <x-button-a href="{{ route('meeting.edit', $meeting->id) }}"
                                            color='outline-primary'
                                            name="{{ __('messages.Edit Meeting') }}"
                                            class="action-btn" />

                                <x-forms.delete-button
                                        name="{{ __('messages.Delete') }}"
                                        formName='delete-item'
                                        id="{{$meeting->id}}"
                                        routeName="meeting.destroy"
                                        class="action-btn" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-meetings">
                    <i class="far fa-calendar-times"></i> {{ __('messages.No meetings scheduled') }}
                </div>
            @endif
        </div>
        {{--  / Meetings Section  --}}
    </div>
</x-layout>

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
        border-left: 4px ;
    }
    [dir="rtl"] .meeting-item {
        border-right: 4px solid #4f46e5;
        border-left: 4px ;
    }

    .meeting-item {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.05);
        border-left: 4px solid #4f46e5;
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

    .meeting-type-badge, .meeting-topic-badge {
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
        padding: 12px;
        background-color: #f9fafb;
        border-radius: 8px;
        border: 1px dashed #e2e8f0;
    }

    .options-title {
        font-weight: 600;
        color: #4b5563;
        font-size: 0.95rem;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .options-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .option-item {
        display: flex;
        align-items: center;
        font-size: 0.85rem;
        background-color: white;
        padding: 8px 12px;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
    }

    .option-name {
        color: #374151;
        margin-right: 6px;
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
            padding: 15px;
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