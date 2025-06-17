<x-layout>
    <x-section-head>{{__("messages.Service Committee information") }}

        @if(app()->getLocale() === 'ar')
            {{$serviceCommittee->ar_name}}
        @else
            {{$serviceCommittee->en_name}}
        @endif

    </x-section-head>

    <div class="group-info-container">

        {{-- Group Section  --}}

        {{-- Button of edit service committee details  --}}
        <div class="mb-3">
            <x-button-a href="{{ route('serviceCommittee.edit', $serviceCommittee->id) }}" color='outline-secondary' name="{{  __('messages.Edit Service Committee') }}" />
        </div>
        <!-- Arabic Service Committee Name -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.Arabic Service Committee Name') }}:</div>
            <div class="info-value">{{$serviceCommittee->ar_name}}</div>
        </div>

        <!-- English Service Committee Name -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.English Service Committee Name') }}:</div>
            <div class="info-value">{{$serviceCommittee->en_name}}</div>
        </div>

        <!-- chairman_name -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.Chairman Name')}}:</div>
            <div class="info-value">{{$serviceCommittee->chairman_name}}</div>
        </div>

        <!-- English Chairman Phone -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.Chairman Phone')}}:</div>
            <div class="info-value">{{$serviceCommittee->chairman_phone}}</div>
        </div>

        <!-- Email -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.Email')}}:</div>
            <div class="info-value">{{$serviceCommittee->user->email}}</div>
        </div>

        <!-- Location -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.Locations')}}:</div>
            <div class="info-value">{{$serviceCommittee->location}}</div>
        </div>

        <!-- Arabic Address -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.Arabic Address')}}:</div>
            <div class="info-value">{{$serviceCommittee->ar_address}}</div>
        </div>

        <!-- English Address -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.English Address')}}:</div>
            <div class="info-value">{{$serviceCommittee->en_address}}</div>
        </div>


        <!-- notes -->
        <div class="info-block">
            <div class="info-label">{{ __('messages.Committee Meeting')}}:</div>
            <div class="info-value">{{$serviceCommittee->notes}}</div>
        </div>

        {{-- / Group Section  --}}

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