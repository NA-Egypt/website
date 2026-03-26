@php
$direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp
<x-frontend.layout>
    <x-section-head>{{ __('messages.commsmeetings') }}</x-section-head>
        <div class="card" dir="{{ $direction }}">
            <div class="card-body" dir="{{ $direction }}">
            <div id="top" class="container py-4" dir="{{ $direction }}" style="text-align: center; background: linear-gradient(to top, #ffffff, #f0f4f8); border-radius: 12px;">
                <div class="mb-2 mt-2">
                    <h2 class="mb-3" style="font-size: 1.75rem; color: #1a73e8;">تعرف على اللجان الخدمية ومواعيد اجتماعاتها</h2>
                    <style>
                        [dir="rtl"] .meeting-card {
                            background: #ffffff;
                            border: 1px solid #e0e0e0;
                            border-radius: 10px;
                            padding: 1rem;
                            margin-bottom: 1rem;
                            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
                            transition: all 0.3s ease-in-out;
                            text-align: right;
                            width: 90%;
                        }
                        [dir="ltr"] .meeting-card {
                            background: #ffffff;
                            border: 1px solid #e0e0e0;
                            border-radius: 10px;
                            padding: 1rem;
                            margin-bottom: 1rem;
                            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
                            transition: all 0.3s ease-in-out;
                            text-align: left;
                            width: 90%;
                        }
                        .meeting-card:hover {
                            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                        }
                        .meeting-title {
                            font-weight: bold;
                            font-size: 1.25rem;
                            color: #1a73e8;
                        }
                        .meeting-notes {
                            color: #555;
                            font-size: 1rem;
                        }
                    </style>
                </div>
                @foreach ($serviceCommittees as $comm)
                    <div class="meeting-card">
                        <div class="meeting-title">{{ $direction === 'rtl' ? $comm->ar_name : $comm->en_name }}</div>
                        <div class="meeting-notes mb-2">{{ $comm->notes }}</div>
                        <div class="meeting-notes mb-2">{{ $direction === 'rtl' ? $comm->ar_address : $comm->en_address }}</div>
                        <div class="text-muted">
                            <x-fas-map style="width:16px; height:16px;"/>&NonBreakingSpace;
                            <a class="text-decoration-none" href="{{ $comm->location }}" target="_blank">
                                {{ __('messages.Map') }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
</x-frontend.layout>

