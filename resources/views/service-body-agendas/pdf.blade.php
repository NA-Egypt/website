@php
$direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp
<!DOCTYPE html>
<html dir="{{ $direction }}" lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Language" content="{{ app()->getLocale() }}" />
    <style>
        body { 
            font-family: 'xbriyaz', sans-serif; 
            direction: {{ $direction }};
            unicode-bidi: embed;
            font-size: 14px;
        }
        .page-break { page-break-after: always; }
        .header-table { width: 100%; border: none; margin-bottom: 15px; }
        .header-table td { border: none; padding: 5px; vertical-align: middle; }
        .title { font-size: 20px; font-weight: bold; text-align: center; }
        .meta { font-size: 14px; margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 8px; text-align: {{ $direction === 'rtl' ? 'right' : 'left' }}; font-size: 12px; }
        table.data-table th { background-color: #f0f0f0; }
        .content-box { border: 1px solid #aaa; padding: 12px; margin-bottom: 15px; border-radius: 6px; background-color: #fafafa; }
        .section-title { font-size: 15px; font-weight: bold; background-color: #e8e8e8; padding: 6px; margin-top: 15px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; }
    </style>
</head>
<body>
    @foreach($agendas as $agenda)
        <table class="header-table">
            <tr>
                <td style="width: 30%; text-align: left;">
                    @if(file_exists(public_path('assets/images/na.png')))
                        <img src="{{ public_path('assets/images/na.png') }}" style="max-height: 60px; width: auto;" alt="NA Logo">
                    @else
                        <!-- fallback if na.png does not exist but logo.png exists -->
                        @if(file_exists(public_path('assets/images/logo.png')))
                            <img src="{{ public_path('assets/images/logo.png') }}" style="max-height: 60px; width: auto;" alt="NA Logo">
                        @endif
                    @endif
                </td>
                <td style="width: 40%; text-align: center;">
                    <div style="font-size: 18px; font-weight: bold;">
                        {{ $agenda->serviceBody->ar_name }}
                    </div>
                    <div style="font-size: 14px; color: #555; margin-top: 5px;">
                        {{ __('messages.Service Body Agenda') ?? 'Service Body Agenda' }}
                    </div>
                </td>
                <td style="width: 30%; text-align: right;">
                    @if($agenda->serviceBody && $agenda->serviceBody->logo && file_exists(public_path('storage/' . $agenda->serviceBody->logo)))
                        <img src="{{ public_path('storage/' . $agenda->serviceBody->logo) }}" style="max-height: 60px; width: auto;" alt="Service Body Logo">
                    @endif
                </td>
            </tr>
        </table>
        <div style="border-bottom: 2px solid #000; margin-bottom: 15px; margin-top: 5px;"></div>

        <div class="meta">
            @php
                $arabicMonths = [
                    1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو',
                    7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                ];
                $year = $agenda->meeting_date->format('Y');
                $monthNum = (int)$agenda->meeting_date->format('m');
                $monthName = $arabicMonths[$monthNum] ?? $agenda->meeting_date->format('m');
            @endphp
            <strong>{{ __('messages.Header') ?? 'Header' }}:</strong> {{ $agenda->serviceBody->ar_name }} {{ $monthName }} {{ $year }} <br>
            <strong>{{ __('messages.Agenda Writing Date') ?? 'Agenda Writing Date' }}:</strong> {{ $agenda->agenda_date->format('Y-m-d') }} <br>
            <strong>{{ __('messages.Meeting Date') ?? 'Meeting Date' }}:</strong> {{ $agenda->meeting_date->format('Y-m-d') }}
            @if($agenda->is_exceptional)
                <br><strong>{{ __('messages.Exceptional Meeting') ?? 'Exceptional Meeting' }}:</strong> <span style="color: red; font-weight: bold;">{{ __('messages.Yes') ?? 'Yes' }}</span>
            @endif
        </div>

        <div class="section-title">{{ __('messages.Registered Groups') ?? 'Registered Groups' }}</div>
        <div class="content-box">
            @forelse($agenda->serviceBody->groups as $group)
                <span style="display: inline-block; background-color: #eee; padding: 4px 8px; margin: 3px; border-radius: 4px; border: 1px solid #ddd;">
                    {{ $group->ar_name }}
                </span>
            @empty
                <span style="color: #666;">{{ __('messages.No registered groups') ?? 'No registered groups' }}</span>
            @endforelse
        </div>

        @if(!empty($agenda->groups_joined))
            <div class="section-title">{{ __('messages.New Groups Joined') ?? 'New Groups Joined' }}</div>
            <div class="content-box">
                @foreach($agenda->groups_joined as $g)
                    <span style="display: inline-block; background-color: #d4edda; color: #155724; padding: 4px 8px; margin: 3px; border-radius: 4px; border: 1px solid #c3e6cb;">
                        {{ $g }}
                    </span>
                @endforeach
            </div>
        @endif

        <div class="section-title">{{ __('messages.Agenda Body') ?? 'Agenda Body' }}</div>
        @foreach($agenda->body as $index => $section)
            <div class="content-box" style="margin-bottom: 15px;">
                @if(!empty($section['headline']))
                    <div style="font-weight: bold; font-size: 13px; margin-bottom: 5px; border-bottom: 1px solid #ddd; padding-bottom: 2px; color: #333;">
                        {{ $section['headline'] }}
                    </div>
                @endif
                <div>
                    {!! $section['content'] !!}
                </div>
            </div>
        @endforeach

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
