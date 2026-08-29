<x-frontend.layout>
    <style>
        body {
            background-color: #f8fafc !important;
            background-image: radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.03) 0%, transparent 40%), radial-gradient(circle at 90% 80%, rgba(16, 185, 129, 0.03) 0%, transparent 40%) !important;
            background-attachment: fixed !important;
        }

        .form-public-container {
            width: 100% !important;
            max-width: 860px !important;
            margin: 0 auto !important;
            box-sizing: border-box !important;
            padding: 0 16px !important;
        }

        .glass-form-card {
            background: rgba(255, 255, 255, 0.88) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(255, 255, 255, 0.6) !important;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.07), 0 0 1px 1px rgba(226, 232, 240, 0.8) !important;
            border-radius: 24px !important;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .success-icon-wrapper {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(16, 185, 129, 0.05) 70%, transparent 100%);
            border: 2px solid rgba(16, 185, 129, 0.25);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #10b981;
            margin-bottom: 1.5rem;
            position: relative;
            animation: pulseGlow 2.5s infinite ease-in-out;
        }

        @keyframes pulseGlow {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.2);
            }
            50% {
                transform: scale(1.04);
                box-shadow: 0 0 0 14px rgba(16, 185, 129, 0);
            }
        }

        .summary-card {
            background: rgba(248, 250, 252, 0.9);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            max-width: 620px;
            margin: 0 auto;
        }

        .btn-action-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            border: none !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            padding: 0.75rem 1.75rem !important;
            border-radius: 9999px !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important;
            transition: all 0.2s ease !important;
        }

        .btn-action-primary:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35) !important;
            color: #ffffff !important;
        }

        .btn-action-secondary {
            background: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #334155 !important;
            font-weight: 600 !important;
            padding: 0.75rem 1.75rem !important;
            border-radius: 9999px !important;
            transition: all 0.2s ease !important;
        }

        .btn-action-secondary:hover {
            background: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
            transform: translateY(-1px) !important;
        }

        @media (max-width: 575.98px) {
            .glass-form-card {
                border-radius: 18px !important;
                padding: 2rem 1.25rem !important;
            }
            .success-icon-wrapper {
                width: 76px;
                height: 76px;
            }
            .success-icon-wrapper i {
                font-size: 2.75rem !important;
            }
            .summary-card {
                padding: 1rem !important;
            }
        }
    </style>

    <div class="form-public-container py-4 py-md-5 my-md-3">
        <div class="glass-form-card p-4 p-sm-5 text-center">
            <div class="success-icon-wrapper">
                <i class="bi bi-check2-circle" style="font-size: 3.5rem;"></i>
            </div>

            <h1 class="fw-bold mb-2" style="color: #0f172a; font-size: clamp(1.6rem, 4vw, 2.25rem); letter-spacing: -0.5px;">
                {{ __('messages.Thank You!') }}
            </h1>

            <p class="text-secondary fs-6 mb-4 mx-auto" style="max-width: 580px; line-height: 1.6;">
                {{ __('messages.Your response for :title has been successfully submitted.', ['title' => $form->title]) }}
            </p>

            <!-- Form Summary Info Box -->
            <div class="summary-card mb-4 text-start">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pb-2 border-bottom mb-2.5" style="border-color: #e2e8f0 !important;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi {{ $form->settings['icon'] ?? 'bi-clipboard2-data' }} text-primary fs-5"></i>
                        <span class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $form->title }}</span>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                        <i class="bi bi-check2"></i> {{ __('messages.Received') ?? 'Received' }}
                    </span>
                </div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 text-muted small">
                    <div>
                        <i class="bi bi-clock me-1"></i> {{ now()->format('Y-m-d H:i') }}
                    </div>
                    <div>
                        @if ($form->type === 'survey')
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5" style="font-size: 0.75rem;">
                                {{ __('messages.Survey') ?? 'Survey' }}
                            </span>
                        @elseif ($form->type === 'event_registration')
                            <span class="badge bg-indigo-subtle text-indigo border rounded-pill px-2.5 py-0.5" style="font-size: 0.75rem; background-color: rgba(99, 102, 241, 0.1); color: #4f46e5; border-color: rgba(99, 102, 241, 0.2);">
                                {{ __('messages.Event Registration') ?? 'Event Registration' }}
                            </span>
                        @elseif ($form->type === 'service_position_application')
                            <span class="badge rounded-pill px-2.5 py-0.5" style="font-size: 0.75rem; background-color: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2);">
                                {{ __('messages.Service Application') ?? 'Service Application' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contextual Action Buttons -->
            <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-3 pt-2">
                @if ($form->type === 'event_registration')
                    <a href="{{ route('forms.show.public', $form->slug) }}" class="btn btn-action-primary w-100 w-sm-auto d-inline-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-plus-circle"></i>
                        {{ __('messages.New Registration') ?? 'New Registration' }}
                    </a>
                @else
                    <a href="{{ route('forms.show.public', $form->slug) }}" class="btn btn-action-secondary w-100 w-sm-auto d-inline-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-arrow-repeat"></i>
                        {{ __('messages.Submit Another Response') ?? 'Submit Another Response' }}
                    </a>
                @endif

                <a href="{{ route('frontend.home') }}" class="btn btn-action-primary w-100 w-sm-auto d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-house-door-fill"></i>
                    {{ __('messages.Back to Homepage') }}
                </a>
            </div>
        </div>
    </div>
</x-frontend.layout>
