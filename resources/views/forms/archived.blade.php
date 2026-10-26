<x-frontend.layout>
    <style>
        body {
            background-color: #f8fafc !important;
            background-image: radial-gradient(circle at 10% 20%, rgba(100, 116, 139, 0.04) 0%, transparent 40%), radial-gradient(circle at 90% 80%, rgba(245, 158, 11, 0.03) 0%, transparent 40%) !important;
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
            background: rgba(255, 255, 255, 0.92) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(255, 255, 255, 0.6) !important;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.07), 0 0 1px 1px rgba(226, 232, 240, 0.8) !important;
            border-radius: 24px !important;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .archive-icon-wrapper {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(100, 116, 139, 0.15) 0%, rgba(100, 116, 139, 0.05) 70%, transparent 100%);
            border: 2px solid rgba(100, 116, 139, 0.25);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .notice-card {
            background: rgba(248, 250, 252, 0.9);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.5rem;
            max-width: 620px;
            margin: 0 auto;
        }
    </style>

    <div class="form-public-container py-5 my-md-4">
        <div class="glass-form-card text-center p-4 p-md-5">
            <div class="archive-icon-wrapper">
                <i class="bi bi-archive-fill" style="font-size: 2.75rem;"></i>
            </div>

            <h3 class="fw-bold mb-2 text-dark">{{ $form->title }}</h3>

            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-4" style="background: rgba(100, 116, 139, 0.1); color: #475569; font-size: 0.85rem; font-weight: 600;">
                <i class="bi bi-lock-fill"></i>
                <span>{{ __('messages.form_archived_notice_title') }}</span>
            </div>

            <div class="notice-card mb-4 text-start">
                <p class="text-secondary mb-0 text-center" style="font-size: 1.05rem; line-height: 1.6;">
                    {{ __('messages.form_archived_notice_desc') }}
                </p>
            </div>

            <div class="d-flex justify-content-center gap-3">
                <a href="{{ url('/') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2">
                    <i class="bi bi-house"></i> {{ __('messages.home') ?? 'Home' }}
                </a>
            </div>
        </div>
    </div>
</x-frontend.layout>
