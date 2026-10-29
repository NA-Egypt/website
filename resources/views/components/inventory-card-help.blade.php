@props([
    'title' => __('messages.what_this_number_means'),
    'description' => '',
    'formula' => '',
    'placement' => 'top',
    'class' => '',
])

@php
    $popoverBody = e($description);
    if (!empty($formula)) {
        $popoverBody .= '<div class="mt-2 pt-2 border-top border-primary-subtle font-monospace small text-primary fw-semibold"><i class="bi bi-calculator me-1"></i>' . e($formula) . '</div>';
    }
@endphp

<span class="inventory-card-help-wrapper d-inline-flex align-items-center {{ $class }}" onclick="event.stopPropagation(); event.preventDefault();">
    <button type="button"
            class="btn btn-link p-0 border-0 text-secondary opacity-75 hover-opacity-100 inventory-card-help-btn inv-card-help-btn d-inline-flex align-items-center justify-content-center rounded-circle"
            data-bs-toggle="popover"
            data-bs-trigger="hover focus click"
            data-bs-placement="{{ $placement }}"
            data-bs-html="true"
            data-bs-title="{{ $title }}"
            data-bs-content="{{ $popoverBody }}"
            aria-label="{{ $title }}"
            style="width: 22px; height: 22px; line-height: 1; text-decoration: none;">
        <i class="bi bi-question-circle-fill" style="font-size: 0.9rem;"></i>
    </button>
</span>

@once
    <style>
        .inventory-card-help-btn {
            transition: transform 0.15s ease, color 0.15s ease, opacity 0.15s ease;
        }
        .inventory-card-help-btn:hover,
        .inventory-card-help-btn:focus {
            opacity: 1 !important;
            color: #2563eb !important;
            transform: scale(1.12);
        }
        .popover {
            border: 1px solid rgba(37, 99, 235, 0.18) !important;
            border-radius: 0.85rem !important;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.15) !important;
            max-width: 310px !important;
            z-index: 1090 !important;
        }
        .popover-header {
            background-color: #eff6ff !important;
            color: #1e40af !important;
            font-weight: 700 !important;
            font-size: 0.85rem !important;
            border-bottom: 1px solid rgba(37, 99, 235, 0.12) !important;
            border-top-left-radius: 0.85rem !important;
            border-top-right-radius: 0.85rem !important;
            padding: 0.6rem 0.85rem !important;
        }
        .popover-body {
            color: #334155 !important;
            font-size: 0.82rem !important;
            line-height: 1.5 !important;
            padding: 0.75rem 0.85rem !important;
        }
        @keyframes inventoryLivePulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.55); }
            50% { transform: scale(1.08); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        .live-updated-flash {
            animation: inventoryLivePulse 0.85s ease-out;
        }
        .keyboard-focused-row {
            background-color: rgba(37, 99, 235, 0.07) !important;
            outline: 2px solid #2563eb !important;
            outline-offset: -2px;
            border-left: 4px solid #2563eb !important;
        }
        .kbd-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 22px;
            padding: 0 6px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.75rem;
            font-weight: 700;
            color: #1e293b;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-bottom-width: 2px;
            border-radius: 5px;
            box-shadow: 0 1px 1px rgba(0,0,0,0.05);
        }
    </style>
    <script>
        window.initInventoryPopovers = function() {
            if (typeof bootstrap === 'undefined' || !bootstrap.Popover) return;
            document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
                if (!bootstrap.Popover.getInstance(el)) {
                    new bootstrap.Popover(el, {
                        container: 'body',
                        html: true,
                        sanitize: false
                    });
                }
            });
        };
        document.addEventListener('DOMContentLoaded', function () {
            window.initInventoryPopovers();
        });
    </script>
@endonce
