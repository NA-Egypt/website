<x-frontend.layout>
    <x-section-head>{{ __('messages.forpublicheader') }}</x-section-head>

    @php
        $isArabic = app()->getLocale() === 'ar';
        $pageDir = $isArabic ? 'rtl' : 'ltr';
        $pageAlign = $isArabic ? 'right' : 'left';
        $cardArrowIcon = $isArabic ? 'bi-arrow-left' : 'bi-arrow-right';
        $definitionParagraphs = __('messages.test_page.definition.paragraphs');
        $statsItems = __('messages.test_page.stats.items');
        $benefitParagraphs = __('messages.test_page.benefits.paragraphs');
        $statHighlights = [
            ['label' => __('messages.test_page.stats.respondents'), 'value' => 679, 'accent' => 'purple'],
            ['label' => __('messages.test_page.stats.egypt_residents'), 'value' => 654, 'accent' => 'blue'],
            ['label' => __('messages.test_page.stats.cairo_residents'), 'value' => 366, 'accent' => 'green'],
            ['label' => __('messages.test_page.stats.weekly_attendance'), 'value' => 415, 'accent' => 'amber'],
        ];
        $statsCharts = [
            [
                'title' => __('messages.test_page.stats.chart_titles.gender'),
                'desc' => __('messages.test_page.stats.chart_desc.gender'),
                'items' => [
                    ['label' => __('messages.test_page.stats.labels.male'), 'count' => 577, 'percent' => 84.98, 'color' => '#6366f1'],
                    ['label' => __('messages.test_page.stats.labels.female'), 'count' => 102, 'percent' => 15.02, 'color' => '#ec4899'],
                ],
            ],
            [
                'title' => __('messages.test_page.stats.chart_titles.age'),
                'desc' => __('messages.test_page.stats.chart_desc.age'),
                'items' => [
                    ['label' => __('messages.test_page.stats.labels.under_18'), 'count' => 1, 'percent' => 0.15, 'color' => '#94a3b8'],
                    ['label' => __('messages.test_page.stats.labels.age_18_25'), 'count' => 66, 'percent' => 9.72, 'color' => '#0ea5e9'],
                    ['label' => __('messages.test_page.stats.labels.age_26_32'), 'count' => 158, 'percent' => 23.27, 'color' => '#14b8a6'],
                    ['label' => __('messages.test_page.stats.labels.age_33_39'), 'count' => 225, 'percent' => 33.14, 'color' => '#8b5cf6'],
                    ['label' => __('messages.test_page.stats.labels.age_40_46'), 'count' => 149, 'percent' => 21.94, 'color' => '#f97316'],
                    ['label' => __('messages.test_page.stats.labels.age_47_53'), 'count' => 60, 'percent' => 8.84, 'color' => '#ef4444'],
                    ['label' => __('messages.test_page.stats.labels.age_54_60'), 'count' => 16, 'percent' => 2.36, 'color' => '#64748b'],
                    ['label' => __('messages.test_page.stats.labels.over_60'), 'count' => 4, 'percent' => 0.59, 'color' => '#334155'],
                ],
            ],
            [
                'title' => __('messages.test_page.stats.chart_titles.employment'),
                'desc' => __('messages.test_page.stats.chart_desc.employment'),
                'items' => [
                    ['label' => __('messages.test_page.stats.labels.student'), 'count' => 37, 'percent' => 5.45, 'color' => '#06b6d4'],
                    ['label' => __('messages.test_page.stats.labels.full_time'), 'count' => 477, 'percent' => 70.25, 'color' => '#10b981'],
                    ['label' => __('messages.test_page.stats.labels.part_time'), 'count' => 78, 'percent' => 11.49, 'color' => '#f59e0b'],
                    ['label' => __('messages.test_page.stats.labels.retired'), 'count' => 12, 'percent' => 1.77, 'color' => '#64748b'],
                    ['label' => __('messages.test_page.stats.labels.home_care'), 'count' => 23, 'percent' => 3.39, 'color' => '#ec4899'],
                    ['label' => __('messages.test_page.stats.labels.not_working'), 'count' => 73, 'percent' => 10.75, 'color' => '#ef4444'],
                ],
            ],
            [
                'title' => __('messages.test_page.stats.chart_titles.quality_of_life'),
                'desc' => __('messages.test_page.stats.chart_desc.quality_of_life'),
                'items' => [
                    ['label' => __('messages.test_page.stats.labels.stable_housing'), 'count' => 393, 'percent' => 57.88, 'color' => '#0ea5e9'],
                    ['label' => __('messages.test_page.stats.labels.work'), 'count' => 493, 'percent' => 72.61, 'color' => '#10b981'],
                    ['label' => __('messages.test_page.stats.labels.education_progress'), 'count' => 271, 'percent' => 39.91, 'color' => '#f59e0b'],
                    ['label' => __('messages.test_page.stats.labels.social_relations'), 'count' => 584, 'percent' => 86.01, 'color' => '#8b5cf6'],
                    ['label' => __('messages.test_page.stats.labels.family_relations'), 'count' => 585, 'percent' => 86.16, 'color' => '#ec4899'],
                    ['label' => __('messages.test_page.stats.labels.hobbies'), 'count' => 444, 'percent' => 65.39, 'color' => '#f97316'],
                ],
            ],
        ];
        $buildDonut = function (array $items) {
            $parts = [];
            $current = 0;
            foreach ($items as $item) {
                $next = min($current + $item['percent'], 100);
                $parts[] = "{$item['color']} {$current}% {$next}%";
                $current = $next;
            }
            if ($current < 100) {
                $parts[] = "#e2e8f0 {$current}% 100%";
            }
            return 'conic-gradient(' . implode(', ', $parts) . ')';
        };
        $statsDonuts = [
            [
                'title' => $statsCharts[0]['title'],
                'desc' => $statsCharts[0]['desc'],
                'gradient' => $buildDonut($statsCharts[0]['items']),
                'center' => '679',
                'center_label' => __('messages.test_page.stats.respondents'),
                'items' => $statsCharts[0]['items'],
            ],
            [
                'title' => $statsCharts[1]['title'],
                'desc' => $statsCharts[1]['desc'],
                'gradient' => $buildDonut($statsCharts[1]['items']),
                'center' => '33-39',
                'center_label' => __('messages.test_page.stats.labels.age_33_39'),
                'items' => $statsCharts[1]['items'],
            ],
        ];
        $statsDonuts = array_merge($statsDonuts, array_map(function ($chart) use ($buildDonut) {
            $maxItem = collect($chart['items'])->sortByDesc('percent')->first();
            return [
                'title' => $chart['title'],
                'desc' => $chart['desc'],
                'gradient' => $buildDonut($chart['items']),
                'center' => rtrim(rtrim(number_format($maxItem['percent'], 2), '0'), '.') . '%',
                'center_label' => $maxItem['label'],
                'items' => $chart['items'],
            ];
        }, array_slice($statsCharts, 2)));
    @endphp

    <div id="top" class="modern-page" dir="{{ $pageDir }}" style="text-align: {{ $pageAlign }};">

        {{-- Hero Section --}}
        <div class="hero-section">
            <div class="container">
                <h1 class="hero-title fade-in">{{__('messages.General information about Narcotics Anonymous')}}</h1>
                <p class="hero-subtitle fade-in fade-in-delay-1">
                    {{__('messages.Discover the journey of recovery and hope through our community')}}</p>
                <div class="hero-links fade-in fade-in-delay-2 mt-4 d-flex justify-content-center flex-wrap gap-2">
                    <a href="#definition" class="btn btn-outline-light rounded-pill px-4">{{ __('messages.test_page.nav.definition.title') }}</a>
                    <a href="#local-fellowship" class="btn btn-outline-light rounded-pill px-4">{{ __('messages.test_page.nav.local.title') }}</a>
                    <a href="#local-statistics" class="btn btn-outline-light rounded-pill px-4">{{ __('messages.test_page.nav.stats.title') }}</a>
                    <a href="#arabic-brochures" class="btn btn-outline-light rounded-pill px-4">{{ __('messages.test_page.nav.brochures.title') }}</a>
                    <a href="#community-benefits" class="btn btn-outline-light rounded-pill px-4">{{ __('messages.test_page.nav.benefits.title') }}</a>
                </div>
                <div class="mt-4 text-center">
                    <livewire:cooperation-form />
                </div>
            </div>
        </div>

        {{-- Navigation Cards --}}
        <div class="container py-5">
            <div class="nav-grid">
                <a href="#definition" class="nav-card card-1 fade-in">
                    <div class="card-icon">
                        <i class="bi bi-globe2"></i>
                    </div>
                    <span class="card-kicker">{{ __('messages.test_page.nav.definition.kicker') }}</span>
                    <h5>{{ __('messages.test_page.nav.definition.title') }}</h5>
                    <p>{{ __('messages.test_page.nav.definition.desc') }}</p>
                    <span class="card-arrow"><i class="bi {{ $cardArrowIcon }}"></i></span>
                </a>
                <a href="#local-fellowship" class="nav-card card-2 fade-in fade-in-delay-1">
                    <div class="card-icon">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <span class="card-kicker">{{ __('messages.test_page.nav.local.kicker') }}</span>
                    <h5>{{ __('messages.test_page.nav.local.title') }}</h5>
                    <p>{{ __('messages.test_page.nav.local.desc') }}</p>
                    <span class="card-arrow"><i class="bi {{ $cardArrowIcon }}"></i></span>
                </a>
                <a href="#local-statistics" class="nav-card card-3 fade-in fade-in-delay-2">
                    <div class="card-icon">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                    <span class="card-kicker">{{ __('messages.test_page.nav.stats.kicker') }}</span>
                    <h5>{{ __('messages.test_page.nav.stats.title') }}</h5>
                    <p>{{ __('messages.test_page.nav.stats.desc') }}</p>
                    <span class="card-arrow"><i class="bi {{ $cardArrowIcon }}"></i></span>
                </a>
                <a href="#arabic-brochures" class="nav-card card-4 fade-in fade-in-delay-3">
                    <div class="card-icon">
                        <i class="bi bi-journals"></i>
                    </div>
                    <span class="card-kicker">{{ __('messages.test_page.nav.brochures.kicker') }}</span>
                    <h5>{{ __('messages.test_page.nav.brochures.title') }}</h5>
                    <p>{{ __('messages.test_page.nav.brochures.desc') }}</p>
                    <span class="card-arrow"><i class="bi {{ $cardArrowIcon }}"></i></span>
                </a>
                <a href="#community-benefits" class="nav-card card-5 fade-in fade-in-delay-4">
                    <div class="card-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <span class="card-kicker">{{ __('messages.test_page.nav.benefits.kicker') }}</span>
                    <h5>{{ __('messages.test_page.nav.benefits.title') }}</h5>
                    <p>{{ __('messages.test_page.nav.benefits.desc') }}</p>
                    <span class="card-arrow"><i class="bi {{ $cardArrowIcon }}"></i></span>
                </a>
            </div>
        </div>

        {{-- Definition of Narcotics Anonymous--}}
        <div id="definition" class="content-section flip-in" style="animation-delay: 0.1s;">
            <div class="container">
                <div class="section-header">
                    <div class="header-icon icon-green">
                        <i class="bi bi-globe2"></i>
                    </div>
                    <h2>{{__('messages.Definition of Narcotics Anonymous')}}</h2>
                </div>
                <div class="content-box">
                    @foreach ($definitionParagraphs as $index => $paragraph)
                        <p class="fade-in fade-in-delay-{{ min($index + 1, 5) }}">{{ $paragraph }}</p>
                    @endforeach

                    <div class="definition-box fade-in fade-in-delay-2">
                        <h5 class="definition-title">{{ __('messages.test_page.definition.box_title') }}</h5>
                        <div class="definition-items">
                            <div class="definition-item">
                                <span
                                    class="term">{{ __('messages.test_page.definition.items.fellowship.term') }}</span>
                                <p>{{ __('messages.test_page.definition.items.fellowship.text') }}</p>
                            </div>
                            <div class="definition-item">
                                <span class="term">{{ __('messages.test_page.definition.items.addicts.term') }}</span>
                                <p>{{ __('messages.test_page.definition.items.addicts.text') }}</p>
                            </div>
                            <div class="definition-item">
                                <span class="term">{{ __('messages.test_page.definition.items.anonymous.term') }}</span>
                                <p>{{ __('messages.test_page.definition.items.anonymous.text') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Information about the local fellowship--}}
        <div id="local-fellowship" class="content-section flip-in" style="animation-delay: 0.2s;">
            <div class="container">
                <div class="section-header">
                    <div class="header-icon icon-blue">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <h2>{{__('messages.Information about the local fellowship')}}</h2>
                </div>
                <div class="content-box">
                    <div class="timeline">
                        <div class="timeline-item fade-in fade-in-delay-1">
                            <div class="timeline-marker">
                                <i class="bi bi-calendar-event-fill"></i>
                            </div>
                            <div class="timeline-content">
                                <h5>{{ __('messages.test_page.local.timeline.first.title') }}</h5>
                                <p>{{ __('messages.test_page.local.timeline.first.text') }}</p>
                            </div>
                        </div>
                        <div class="timeline-item fade-in fade-in-delay-2">
                            <div class="timeline-marker">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="timeline-content">
                                <h5>{{ __('messages.test_page.local.timeline.members.title') }}</h5>
                                <p>{{ __('messages.test_page.local.timeline.members.text') }}</p>
                            </div>
                        </div>
                        <div class="timeline-item fade-in fade-in-delay-3">
                            <div class="timeline-marker">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <div class="timeline-content">
                                <h5>{{ __('messages.test_page.local.timeline.growth.title') }}</h5>
                                <p>{{ __('messages.test_page.local.timeline.growth.text', ['groups' => \App\Models\Group::count(), 'cities' => \App\Models\City::count(), 'meetings' => \App\Models\Meeting::notMonthlyRecurrent()->count()]) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Local statistics (general characteristics of members) --}}
        <div id="local-statistics" class="content-section flip-in" style="animation-delay: 0.3s;">
            <div class="container">
                <div class="section-header">
                    <div class="header-icon icon-purple">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                    <h2>{{__('messages.Local statistics (general characteristics of members)')}}</h2>
                </div>
                <div class="content-box">
                    <p class="fade-in fade-in-delay-1">{{ __('messages.test_page.stats.intro_1') }}</p>
                    <p class="fade-in fade-in-delay-2">{{ __('messages.test_page.stats.intro_2') }}</p>

                    {{-- <div class="stats-highlights fade-in fade-in-delay-3">
                        @foreach ($statHighlights as $highlight)
                            <div class="highlight-card {{ $highlight['accent'] }}">
                                <span class="highlight-label">{{ $highlight['label'] }}</span>
                                <strong class="highlight-value">{{ number_format($highlight['value']) }}</strong>
                            </div>
                        @endforeach
                    </div>--}}

                    <div class="stats-legend fade-in fade-in-delay-3">
                        @foreach ($statsItems as $item)
                            <span class="stats-pill">{{ $item }}</span>
                        @endforeach
                    </div>

                    <div class="donut-grid fade-in fade-in-delay-4">
                        @foreach ($statsDonuts as $donut)
                            <article class="donut-card">
                                <div class="chart-card-head">
                                    <h3>{{ $donut['title'] }}</h3>
                                    <p>{{ $donut['desc'] }}</p>
                                </div>

                                <div class="donut-layout">
                                    <div class="donut-visual" style="--donut-gradient: {{ $donut['gradient'] }};">
                                        <div class="donut-hole">
                                            <strong>{{ $donut['center'] }}</strong>
                                            <span>{{ $donut['center_label'] }}</span>
                                        </div>
                                    </div>

                                    <div class="donut-legend">
                                        @foreach ($donut['items'] as $item)
                                            <div class="legend-row">
                                                <span class="legend-dot" style="--legend-color: {{ $item['color'] }};"></span>
                                                <span class="legend-label">{{ $item['label'] }}</span>
                                                <span class="legend-value">{{ $item['percent'] }}%</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>

        {{-- Informational brochures (Arabic) --}}
        <div id="arabic-brochures" class="content-section flip-in" style="animation-delay: 0.4s;">
            <div class="container">
                <div class="section-header">
                    <div class="header-icon icon-red">
                        <i class="bi bi-journals"></i>
                    </div>
                    <h2>{{__('messages.Informational brochures (Arabic)')}}</h2>
                </div>
                <div class="content-box">
                    <div class="brochure-list">

                        {{-- MembershipSurvey --}}
                        <div class="brochure-item featured fade-in fade-in-delay-1">
                            <div class="brochure-header">
                                <i class="bi bi-star-fill"></i>
                                <h5><a href="/literature/membership_survey.pdf"
                                       class="brochure-link">{{__('messages.membershipSurvey')}}</a></h5>
                            </div>
                            <p>{{ app()->getLocale() === 'ar' ? 'تم جمع بيانات هذا الاستبيان ورقيا خلال فعاليات المؤتمر السنوي في مايو ٢٠٢٥ بهدف تقديم مؤشرات عمليه و مفيدة حول بعض ملامح واقع التعافي و العضويه في مصر' : 'The data for this survey was collected in paper form during the annual conference in May 2025 with the aim of providing practical and useful indicators on some aspects of the recovery and membership reality in Egypt.' }}
                            </p>
                        </div>

                        {{-- Who, what, how and why --}}
                        <div class="brochure-item featured fade-in fade-in-delay-1">
                            <div class="brochure-header">
                                <i class="bi bi-star-fill"></i>
                                <h5><a href="https://na.org/wp-content/uploads/2024/05/AR3101_2015-IP-1-Arabic.pdf"
                                        class="brochure-link">{{__('messages.Who, what, how and why')}}</a></h5>
                            </div>
                            <p>{{ app()->getLocale() === 'ar' ? 'هذه النشرة توضح أن المدمن هو شخص تسيطر المخدرات على حياته، وأن زمالة المدمنين المجهولين هي تجمّع للمدمنين المتعافين الذين يساعدون بعضهم البعض في البقاء ممتنعين' : 'This pamphlet explains that an addict is a person whose life is controlled by drugs, and that Narcotics Anonymous is a fellowship of recovering addicts who help one another stay abstinent.' }}
                            </p>
                        </div>

                        {{-- Welcome to Narcotics Anonymous --}}
                        <div class="brochure-item featured fade-in fade-in-delay-1">
                            <div class="brochure-header">
                                <i class="bi bi-star-fill"></i>
                                <h5><a href="https://na.org/wp-content/uploads/2024/05/AR3122-IP-22-Arabic.pdf"
                                        class="brochure-link">{{__('messages.Welcome to Narcotics Anonymous')}}</a></h5>
                            </div>
                            <p>{{ app()->getLocale() === 'ar' ? 'هي نشرة ترحيبية توضح أساسيات زمالة المدمنين المجهولين وتبين أن الزمالة تتكون من مدمنين يتعافون من الإدمان من خلال دعم بعضهم البعض في اجتماعات منتظمة' : 'A welcoming pamphlet that explains the basics of Narcotics Anonymous and shows that the fellowship consists of addicts recovering from addiction by supporting one another in regular meetings.' }}
                            </p>
                        </div>

                        {{-- Introduction to Narcotics Anonymous Meetings --}}
                        <div class="brochure-item featured fade-in fade-in-delay-1">
                            <div class="brochure-header">
                                <i class="bi bi-star-fill"></i>
                                <h5><a href="https://na.org/wp-content/uploads/2024/11/AR3129_2024.pdf"
                                        class="brochure-link">{{__('messages.Introduction to Narcotics Anonymous Meetings')}}</a>
                                </h5>
                            </div>
                            <p>{{ app()->getLocale() === 'ar' ? 'القصد من المعلومات المتوفرة في هذا المنشور هي إعطائك مفهوم عن ما نقوم به عندما نجتمع لمشاركة التعافي' : 'The purpose of the information in this pamphlet is to give you an idea of what we do when we gather to share recovery.' }}
                            </p>
                        </div>

                        {{-- Another Look --}}
                        <div class="brochure-item featured fade-in fade-in-delay-2">
                            <div class="brochure-header">
                                <i class="bi bi-star-fill"></i>
                                <h5><a href="https://na.org/wp-content/uploads/2024/05/AR3105-IP-5-Arabic.pdf"
                                        class="brochure-link">{{ __('messages.test_page.brochures.another_look.title') }}</a>
                                </h5>
                            </div>
                            <p>{{ __('messages.test_page.brochures.another_look.text') }}</p>
                        </div>

                        {{-- By Young Addicts, For Young Addicts --}}
                        <div class="brochure-item featured fade-in fade-in-delay-2">
                            <div class="brochure-header">
                                <i class="bi bi-star-fill"></i>
                                <h5><a href="https://na.org/wp-content/uploads/2024/05/AR3113-IP-13-Arabic.pdf"
                                        class="brochure-link">{{ __('messages.test_page.brochures.young_addicts.title') }}</a>
                                </h5>
                            </div>
                            <p>{{ __('messages.test_page.brochures.young_addicts.text') }}</p>
                        </div>

                        {{-- Staying Clean on the Outside --}}
                        <div class="brochure-item featured fade-in fade-in-delay-3">
                            <div class="brochure-header">
                                <i class="bi bi-star-fill"></i>
                                <h5><a href="https://na.org/wp-content/uploads/2024/05/AR3123-IP-23-Arabic.pdf"
                                        class="brochure-link">{{ __('messages.test_page.brochures.staying_clean.title') }}</a>
                                </h5>
                            </div>
                            <p>{{ __('messages.test_page.brochures.staying_clean.text') }}</p>
                        </div>

                        {{-- For the Parents or Guardians of Young People in NA --}}
                        <div class="brochure-item featured fade-in fade-in-delay-3">
                            <div class="brochure-header">
                                <i class="bi bi-star-fill"></i>
                                <h5><a href="https://na.org/wp-content/uploads/2024/05/AR3127-IP-27-Arabic.pdf"
                                        class="brochure-link">{{ __('messages.test_page.brochures.parents.title') }}</a>
                                </h5>
                            </div>
                            <p>{{ __('messages.test_page.brochures.parents.text') }}</p>
                        </div>

                        {{-- Accessibility for Those with Additional Needs --}}
                        <div class="brochure-item featured fade-in fade-in-delay-4">
                            <div class="brochure-header">
                                <i class="bi bi-star-fill"></i>
                                <h5><a href="https://na.org/wp-content/uploads/2024/05/AR3126-IP-26-Arabic.pdf"
                                        class="brochure-link">{{ __('messages.test_page.brochures.accessibility.title') }}</a>
                                </h5>
                            </div>
                            <p>{{ __('messages.test_page.brochures.accessibility.text') }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Society Benefits Section --}}
        <div id="community-benefits" class="content-section flip-in" style="animation-delay: 0.5s;">
            <div class="container">
                <div class="section-header">
                    <div class="header-icon icon-indigo">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h2>{{ __('messages.test_page.benefits.title') }}</h2>
                </div>
                <div class="content-box">
                    <div class="faq-section">
                        <div class="faq-item fade-in fade-in-delay-1">
                            <h5 class="faq-question">
                                <i class="bi bi-question-circle-fill"></i>
                                {{ __('messages.test_page.benefits.card_title') }}
                            </h5>
                            <div class="faq-answer">
                                @foreach ($benefitParagraphs as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Back to Top Button --}}
        <a href="#top" class="back-to-top-btn">
            <i class="bi bi-arrow-up"></i> {{ __('messages.test_page.back_to_top') }}
        </a>
    </div>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .modern-page {
            min-height: 100vh;
            padding-top: 0;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: #ffffff;
            padding: 80px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -30%;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }

        .hero-section .container {
            position: relative;
            z-index: 1;
        }

        .hero-title {
            color: #ffffff;
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            font-weight: 700;
            margin-bottom: 15px;
            letter-spacing: -1px;
            text-shadow: 0 0 1px rgba(255, 255, 255, 1);
        }

        .hero-subtitle {
            font-size: 1.25rem;
            opacity: 0.95;
            font-weight: 300;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) translateX(0px);
            }

            50% {
                transform: translateY(30px) translateX(20px);
            }
        }

        /* Navigation Grid */
        .nav-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 22px;
            padding: 40px 0;
        }

        .nav-card {
            flex: 1 1 280px;
            max-width: 380px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            border-radius: 24px;
            padding: 26px 22px 22px;
            text-align: start;
            text-decoration: none;
            color: #1e293b;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            box-shadow: 0 18px 40px -28px rgba(15, 23, 42, 0.45);
            border: 1px solid rgba(148, 163, 184, 0.2);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 12px;
            min-height: 220px;
        }

        .nav-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.72), rgba(255, 255, 255, 0));
            pointer-events: none;
        }

        .nav-card::after {
            content: '';
            position: absolute;
            inset-inline-start: 0;
            top: 0;
            width: 6px;
            height: 100%;
            background: currentColor;
            opacity: 0.12;
        }

        .nav-card:hover,
        .nav-card:focus-visible {
            transform: translateY(-8px);
            box-shadow: 0 26px 50px -30px rgba(15, 23, 42, 0.5);
            border-color: currentColor;
        }

        .nav-card.card-1 {
            color: #059669;
        }

        .nav-card.card-2 {
            color: #2563eb;
        }

        .nav-card.card-3 {
            color: #7c3aed;
        }

        .nav-card.card-4 {
            color: #dc2626;
        }

        .nav-card.card-5 {
            color: #4f46e5;
        }

        .nav-card.card-6 {
            color: #0284c7;
        }

        .nav-card:hover h5,
        .nav-card:focus-visible h5 {
            color: inherit;
        }

        .card-icon {
            font-size: 2rem;
            background: rgba(255, 255, 255, 0.82);
            width: 72px;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 22px;
            margin-inline-start: 0;
            box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.18);
            position: relative;
            z-index: 1;
        }

        .nav-card.card-1 .card-icon {
            color: #059669;
            background: rgba(236, 253, 245, 0.95);
        }

        .nav-card.card-2 .card-icon {
            color: #2563eb;
            background: rgba(239, 246, 255, 0.95);
        }

        .nav-card.card-3 .card-icon {
            color: #7c3aed;
            background: rgba(245, 243, 255, 0.95);
        }

        .nav-card.card-4 .card-icon {
            color: #dc2626;
            background: rgba(254, 242, 242, 0.95);
        }

        .nav-card.card-5 .card-icon {
            color: #4f46e5;
            background: rgba(238, 242, 255, 0.95);
        }

        .nav-card.card-6 .card-icon {
            color: #0284c7;
            background: rgba(240, 249, 255, 0.96);
        }

        .card-kicker {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: currentColor;
            opacity: 0.85;
            position: relative;
            z-index: 1;
        }

        .nav-card h5 {
            font-size: 1.12rem;
            font-weight: 700;
            margin-bottom: 0;
            transition: color 0.3s ease;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        .nav-card p {
            font-size: 0.95rem;
            color: #475569;
            line-height: 1.8;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }

        .card-arrow {
            margin-top: auto;
            width: 42px;
            height: 42px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.9);
            color: currentColor;
            box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.22);
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .nav-card:hover .card-arrow,
        .nav-card:focus-visible .card-arrow {
            transform: translateX(var(--arrow-shift, -4px));
            background: rgba(255, 255, 255, 1);
        }

        /* Content Sections */
        .content-section {
            padding: 60px 20px;
            animation-fill-mode: forwards;
            scroll-margin-top: 90px;
        }

        .content-section:nth-child(odd) {
            /* background: #f8fafc; */
        }

        .content-section:nth-child(even) {
            /* background: #ffffff; */
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 40px;
        }

        .header-icon {
            font-size: 3rem;
            width: 90px;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: #ffffff;
            flex-shrink: 0;
        }

        .header-icon.icon-green {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .header-icon.icon-blue {
            background: linear-gradient(135deg, #3b82f6, #1e40af);
        }

        .header-icon.icon-purple {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }

        .header-icon.icon-red {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .header-icon.icon-indigo {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
        }

        .header-icon.icon-sky {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
        }

        .section-header h2 {
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            color: #1e293b;
            font-weight: 700;
        }



        .content-box {
            background: #ffffff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .content-box p {
            font-size: 1rem;
            line-height: 1.8;
            color: #1e293b;
            margin-bottom: 20px;
        }

        /* Definition Box */
        .definition-box {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border-inline-start: 4px solid #10b981;
            padding: 30px;
            border-radius: 12px;
            margin-top: 30px;
        }

        .definition-title {
            color: #10b981;
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.2rem;
        }

        .definition-items {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .definition-item {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
        }

        .definition-item .term {
            font-weight: 700;
            color: #10b981;
            font-size: 1.1rem;
            display: block;
            margin-bottom: 10px;
        }

        .definition-item p {
            font-size: 0.95rem;
            color: #1e293b;
            margin-bottom: 0;
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            inset-inline-start: 30px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        }

        .timeline-item {
            margin-bottom: 30px;
            padding-inline-start: 120px;
            position: relative;
        }

        .timeline-marker {
            position: absolute;
            inset-inline-start: 0;
            top: 0;
            width: 65px;
            height: 65px;
            background: #ffffff;
            border: 4px solid #3b82f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #3b82f6;
        }

        .timeline-content {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border-inline-start: 3px solid #3b82f6;
        }

        .timeline-content h5 {
            color: #1e293b;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .timeline-content p {
            margin-bottom: 0;
            font-size: 0.95rem;
        }

        /* Stats Grid */
        .stats-highlights {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-top: 28px;
        }

        .highlight-card {
            padding: 22px 18px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid rgba(148, 163, 184, 0.18);
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .highlight-card.purple {
            background: linear-gradient(135deg, #f5f3ff, #ede9fe);
        }

        .highlight-card.blue {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
        }

        .highlight-card.green {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        }

        .highlight-card.amber {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
        }

        .highlight-label {
            font-size: 0.88rem;
            color: #475569;
            line-height: 1.6;
        }

        .highlight-value {
            font-size: clamp(1.6rem, 3vw, 2.3rem);
            color: #0f172a;
            font-weight: 800;
        }

        .stats-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 22px;
        }

        .stats-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 9px 14px;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .donut-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 22px;
            margin-top: 28px;
        }

        .donut-card,
        .chart-card {
            flex: 1 1 450px;
            max-width: 580px;
            background: linear-gradient(180deg, #f8fbff, #eef6ff);
            border-radius: 24px;
            border: 1px solid rgba(96, 165, 250, 0.16);
            padding: 24px;
            box-shadow: 0 20px 50px -34px rgba(30, 64, 175, 0.28);
        }

        .chart-card-head {
            margin-bottom: 18px;
        }

        .chart-card-head h3 {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .chart-card-head p {
            margin-bottom: 0;
            color: #64748b;
            font-size: 0.92rem;
            line-height: 1.7;
        }

        .donut-layout {
            display: grid;
            grid-template-columns: minmax(180px, 220px) minmax(0, 1fr);
            gap: 22px;
            align-items: center;
        }

        .donut-visual {
            width: min(220px, 100%);
            aspect-ratio: 1;
            margin-inline: auto;
            border-radius: 50%;
            background: var(--donut-gradient);
            display: grid;
            place-items: center;
            position: relative;
            box-shadow: inset 0 0 0 10px rgba(255, 255, 255, 0.22);
        }

        .donut-visual::after {
            content: '';
            position: absolute;
            inset: 18px;
            background: linear-gradient(180deg, #ffffff, #f8fbff);
            border-radius: 50%;
        }

        .donut-hole {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 6px;
            width: 55%;
        }

        .donut-hole strong {
            font-size: clamp(1.5rem, 3vw, 2.3rem);
            color: #0f172a;
            line-height: 1;
        }

        .donut-hole span {
            font-size: 0.82rem;
            color: #64748b;
            line-height: 1.5;
        }

        .donut-legend {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .legend-row {
            display: grid;
            grid-template-columns: 14px minmax(0, 1fr) auto;
            gap: 10px;
            align-items: center;
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(148, 163, 184, 0.14);
        }

        .legend-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--legend-color);
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--legend-color) 16%, white);
        }

        .legend-label {
            font-size: 0.92rem;
            color: #1e293b;
            font-weight: 600;
            line-height: 1.6;
        }

        .legend-value {
            font-size: 0.9rem;
            color: #0f172a;
            font-weight: 700;
        }

        /* Brochure Section */
        .brochure-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .brochure-item {
            padding: 25px;
            border-radius: 12px;
            background: #f8fafc;
            transition: all 0.3s ease;
        }

        .brochure-item.featured {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-inline-start: 4px solid #f97316;
        }

        .brochure-item:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateX(var(--brochure-shift, -5px));
        }

        .brochure-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .brochure-header i {
            font-size: 1.5rem;
            color: #f97316;
        }

        .brochure-header h5 {
            margin: 0;
            color: #1e293b;
            font-size: 1.1rem;
        }

        .brochure-link {
            text-decoration: none;
            color: #f97316;
            font-weight: 700;
            transition: color 0.3s ease;
        }

        .brochure-link:hover {
            color: #ef4444;
            text-decoration: underline;
        }

        .brochure-item p {
            margin-bottom: 0;
            color: #1e293b;
        }

        .list-items {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .list-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .item-icon {
            flex-shrink: 0;
            font-size: 1.2rem;
            color: #ef4444;
            margin-top: 2px;
        }

        .list-item span:last-child {
            color: #1e293b;
            line-height: 1.6;
        }

        /* FAQ Section */
        .faq-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .qa-section .faq-item {
            background: linear-gradient(180deg, #f8fbff, #ffffff);
            border: 1px solid rgba(14, 165, 233, 0.12);
        }

        .faq-item {
            background: #f8fafc;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .faq-question {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #ffffff;
            padding: 20px 25px;
            margin: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            user-select: none;
        }

        .faq-question i {
            font-size: 1.3rem;
        }

        .faq-answer {
            padding: 25px;
            animation: slideDown 0.3s ease;
        }

        .faq-answer p {
            margin-bottom: 15px;
        }

        .faq-answer p:last-child {
            margin-bottom: 0;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Back to Top Button */
        .back-to-top-btn {
            display: none;
            position: fixed;
            bottom: 30px;
            inset-inline-end: 30px;
            z-index: 999;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: #ffffff;
            padding: 15px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .back-to-top-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
        }

        /* Animations */
        @keyframes flipIn {
            0% {
                transform: perspective(800px) rotateY(90deg);
                opacity: 0;
            }

            100% {
                transform: perspective(800px) rotateY(0);
                opacity: 1;
            }
        }

        .flip-in {
            animation: flipIn 0.8s ease forwards;
            transform-origin: var(--flip-origin, right) center;
            opacity: 0;
            backface-visibility: hidden;
        }

        .modern-page[dir="ltr"] {
            --arrow-shift: 4px;
            --brochure-shift: 5px;
            --flip-origin: left;
        }

        .modern-page[dir="rtl"] {
            --arrow-shift: -4px;
            --brochure-shift: -5px;
            --flip-origin: right;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeInUp 1s ease forwards;
            opacity: 0;
        }

        .fade-in-delay-1 {
            animation-delay: 0.3s;
        }

        .fade-in-delay-2 {
            animation-delay: 0.5s;
        }

        .fade-in-delay-3 {
            animation-delay: 0.7s;
        }

        .fade-in-delay-4 {
            animation-delay: 0.9s;
        }

        .fade-in-delay-5 {
            animation-delay: 1.1s;
        }

        html {
            scroll-behavior: smooth;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 1.75rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .section-header {
                flex-direction: column;
                text-align: center;
            }

            .header-icon {
                width: 80px;
                height: 80px;
            }

            .content-box {
                padding: 25px;
            }

            .nav-grid {
                flex-direction: column;
                padding: 20px 0;
            }

            .nav-card {
                max-width: 100%;
                min-height: unset;
                padding: 22px 18px 18px;
                gap: 10px;
            }

            .card-icon {
                width: 64px;
                height: 64px;
                font-size: 1.7rem;
            }

            .timeline::before {
                inset-inline-start: 20px;
            }

            .timeline-item {
                padding-inline-start: 100px;
            }

            .timeline-marker {
                width: 55px;
                height: 55px;
                font-size: 1.2rem;
            }

            .definition-items {
                grid-template-columns: 1fr;
            }

            .stats-highlights {
                grid-template-columns: 1fr;
            }

            .donut-grid {
                flex-direction: column;
                align-items: center;
            }

            .donut-card,
            .chart-card {
                max-width: 100%;
                width: 100%;
            }

            .donut-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .hero-section {
                padding: 50px 15px;
            }

            .hero-title {
                font-size: 1.25rem;
            }

            .content-section {
                padding: 30px 15px;
            }

            .content-box {
                padding: 20px;
            }

            .section-header h2 {
                font-size: 1.4rem;
            }

            .legend-row {
                grid-template-columns: 12px minmax(0, 1fr) auto;
                padding: 9px 10px;
            }

            .donut-hole strong {
                font-size: 1.35rem;
            }

            .nav-card h5 {
                font-size: 1rem;
            }

            .nav-card p {
                font-size: 0.88rem;
            }

            .timeline-item {
                padding-inline-start: 80px;
            }

            .faq-question {
                padding: 15px 20px;
                font-size: 1rem;
            }
        }
    </style>

    <script>
        // Smooth scroll for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // Scroll to top visibility
        const scrollToTopBtn = document.querySelector('.back-to-top-btn');
        if (scrollToTopBtn) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    scrollToTopBtn.style.display = 'inline-flex';
                } else {
                    scrollToTopBtn.style.display = 'none';
                }
            });
        }

        // Animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });
    </script>
</x-frontend.layout>