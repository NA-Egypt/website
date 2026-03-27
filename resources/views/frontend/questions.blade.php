<x-frontend.layout>
    <x-section-head>{{ __('messages.test_page.qa.title') }}</x-section-head>

    @php
        $isArabic = app()->getLocale() === 'ar';
        $pageDir = $isArabic ? 'rtl' : 'ltr';
        $pageAlign = $isArabic ? 'right' : 'left';
        $qaItems = __('messages.test_page.qa.items');
    @endphp

    <div class="questions-page" dir="{{ $pageDir }}" style="text-align: {{ $pageAlign }};">
        <section class="questions-hero">
            <div class="container">
                <div class="hero-shell">
                    <span class="hero-kicker">{{ __('messages.test_page.nav.questions.kicker') }}</span>
                    <h1>{{ __('messages.test_page.qa.title') }}</h1>
                    <p>{{ __('messages.test_page.nav.questions.desc') }}</p>
                </div>
            </div>
        </section>

        <section class="questions-content">
            <div class="container">
                <div class="faq-section qa-section">
                    @foreach ($qaItems as $index => $item)
                        <article class="faq-item fade-in fade-in-delay-{{ min($index + 1, 5) }}">
                            <h2 class="faq-question">
                                <i class="bi bi-question-circle-fill"></i>
                                {{ $item['q'] }}
                            </h2>
                            <div class="faq-answer">
                                @foreach ($item['a'] as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    <style>
        .questions-page {
            background:
                radial-gradient(circle at top right, rgba(59, 130, 246, 0.10), transparent 28%),
                linear-gradient(180deg, #f8fbff 0%, #f1f7ff 100%);
            min-height: 100vh;
            padding-bottom: 60px;
        }

        .questions-hero {
            padding: 52px 20px 28px;
        }

        .hero-shell {
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, 0.14), transparent 24%),
                linear-gradient(135deg, #ffffff, #eef6ff);
            color: #0f172a;
            border-radius: 28px;
            padding: 36px 32px;
            border: 1px solid rgba(59, 130, 246, 0.16);
            box-shadow: 0 24px 60px -42px rgba(37, 99, 235, 0.22);
        }

        .hero-kicker {
            display: inline-flex;
            align-items: center;
            padding: 7px 14px;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.10);
            color: #2563eb;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            margin-bottom: 16px;
        }

        .hero-shell h1 {
            font-size: clamp(1.9rem, 4vw, 3rem);
            font-weight: 800;
            margin-bottom: 12px;
        }

        .hero-shell p {
            max-width: 780px;
            margin-bottom: 0;
            font-size: 1rem;
            line-height: 1.9;
            color: #475569;
        }

        .questions-content {
            padding: 10px 20px 0;
        }

        .faq-section {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .faq-item {
            background: linear-gradient(180deg, #ffffff, #f8fbff);
            border-radius: 22px;
            overflow: hidden;
            border: 1px solid rgba(14, 165, 233, 0.12);
            box-shadow: 0 18px 44px -34px rgba(15, 23, 42, 0.26);
        }

        .faq-question {
            /*background: #ffffff;*/
            color: #020617;
            padding: 20px 24px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 1.08rem;
            font-weight: 800;
            line-height: 1.85;
            border-bottom: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow: inset 6px 0 0 #0284c7;
        }

        .faq-answer {
            padding: 22px 24px;
        }

        .faq-question i {
            color: #38bdf8;
            flex-shrink: 0;
            font-size: 1.1rem;
        }

        .faq-answer p {
            color: #1e293b;
            font-size: 1rem;
            line-height: 1.95;
            margin-bottom: 14px;
        }

        .faq-answer p:last-child {
            margin-bottom: 0;
        }

        .fade-in {
            animation: fadeInUp 0.8s ease forwards;
            opacity: 0;
        }

        .fade-in-delay-1 { animation-delay: 0.1s; }
        .fade-in-delay-2 { animation-delay: 0.2s; }
        .fade-in-delay-3 { animation-delay: 0.3s; }
        .fade-in-delay-4 { animation-delay: 0.4s; }
        .fade-in-delay-5 { animation-delay: 0.5s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .questions-hero {
                padding: 30px 16px 22px;
            }

            .hero-shell {
                padding: 26px 20px;
                border-radius: 22px;
            }

            .questions-content {
                padding: 6px 16px 0;
            }

            .faq-question {
                padding: 18px 18px;
                font-size: 1rem;
                align-items: flex-start;
            }

            .faq-answer {
                padding: 18px;
            }
        }

        @media (max-width: 480px) {
            .hero-shell h1 {
                font-size: 1.55rem;
            }

            .hero-shell p,
            .faq-answer p {
                font-size: 0.95rem;
            }
        }
    </style>
</x-frontend.layout>
