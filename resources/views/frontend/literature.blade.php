@php
    $literatureItems = [
        [
            'type' => 'audio',
            'title' => __('messages.AudioSponsorship'),
            'file' => 'literature/_IP11_Sponsorship_Arabic.aac'
        ],
        [
            'type' => 'audio',
            'title' => __('messages.AudioAmIanAddict'),
            'file' => 'literature/_IP7_Am_I_an_addict_Arabic.aac'
        ],
                [
            'type' => 'audio',
            'title' => __('messages.AudioForthenewcomer'),
            'file' => 'literature/_IP16_For_the_new_comer_Arabic.aac'
        ],
        [
            'type' => 'audio',
            'title' => __('messages.AudioWelcometoNA'),
            'file' => 'literature/_IP22_Welcome_to_NA_Arabic.aac'
        ],
        [
            'type' => 'pdf',
            'title' => __('messages.groupreadings'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR_GRC_2015-Group-Reading-Cards-Arabic.pdf'
        ],
        [
            'type' => 'pdf',
            'title' => __('messages.introductoryGuide'),
            'file' => 'https://na.org/wp-content/uploads/2024/12/An-Introductory-Guide-to-NA-Arabic.pdf'
        ],
        [
            'type' => 'pdf',
            'title' => __('messages.WhiteBooklet'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR1500_LWB-White-Booklet-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP1'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR3101_2015-IP-1-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP2'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR3102-IP-2-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP5'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR3105-IP-5-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP6'),
            'file' => 'https://na.org/wp-content/uploads/2024/09/AR3106-IP-6-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP7'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR3107-IP-7-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP8'),
            'file' => 'https://na.org/wp-content/uploads/2024/09/AR3108-IP-8-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP9'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR3109-IP-9-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP11'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR3111-IP-11-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP12'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR3112-IP-12-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP13'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR3113-IP-13-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP14'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR3114-IP-14-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP16'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR3116-IP-16-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP19'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR3119-IP-19-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP21'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR3121-IP-21-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP22'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR3122-IP-22-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP23'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR3123-IP-23-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP24'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR3124-IP-24-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP26'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR3126-IP-26-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP27'),
            'file' => 'https://na.org/wp-content/uploads/2024/05/AR3127-IP-27-Arabic.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP28'),
            'file' => 'https://na.org/wp-content/uploads/2024/11/AR3128_2024.pdf'
        ],
                [
            'type' => 'pdf',
            'title' => __('messages.IP29'),
            'file' => 'https://na.org/wp-content/uploads/2024/11/AR3129_2024.pdf%20'
        ],
                
        
        // Add more items here
    ];
@endphp
<x-frontend.layout>
    <x-section-head>{{ __('messages.Literature') }}</x-section-head>

 <div class="literature-columns">
    <div class="literature-column">
        <h2>{{__('messages.BasicText')}}</h2>
        <div class="literature-item">
            <a href="https://na.org/wp-content/uploads/2025/04/BT-Audio-Arabic.zip" target="_blank" class="btn btn-primary">
                📄 {{__('messages.BasicTextdownload')}}
            </a>
            <a href="https://soundcloud.com/user-197598456/sets/8161e314-b4a0-417f-97a2-092b3005efb3" target="_blank" class="btn btn-info">
                🎵 {{__('messages.BasicTextlisten')}}
            </a>
        </div>
        <h2>{{ __('messages.Audio') }}</h2>
        @foreach($literatureItems as $item)
            @if($item['type'] === 'audio')
                <div class="literature-item">
                    <div class="literature-audio">
                        <p>🎵 {{ $item['title'] }}</p>
                        <audio controls>
                            <source src="{{ asset('/' . $item['file']) }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    <div class="literature-column">
        <h2>{{ __('messages.PDF') }}</h2>
        @foreach($literatureItems as $item)
            @if($item['type'] === 'pdf')
                <div class="literature-item">
                    <div class="literature-pdf">
                        <a href="{{ $item['file'] }}" target="_blank">
                            📄 {{ $item['title'] }}
                        </a>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>

   <style>
    .literature-columns {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-top: 1rem;
    }

    @media (min-width: 768px) {
        .literature-columns {
            flex-direction: row;
            justify-content: space-between;
        }

        .literature-column {
            width: 48%;
        }
    }

    .literature-column h2 {
        margin-bottom: 0.5rem;
        font-size: 1.25rem;
        color: #333;
    }

    .literature-item {
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .literature-pdf a {
        text-decoration: none;
        color: #007bff;
        font-weight: bold;
        text-align: right;
    }

    .literature-audio p {
        margin: 0 0 0.5rem 0;
        font-weight: bold;
        text-align: right;
    }
    audio {
        width: 100%;
    }
</style>
</x-frontend.layout>