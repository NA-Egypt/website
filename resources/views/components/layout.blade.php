
<!doctype html>
@php
$direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp
<html lang="{{ app()->getLocale() }}"  dir="{{ $direction }}" class="minimal-theme">
<head>
  <meta charset="utf-8">
  <meta charset="utf-8">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">




  <script>
    window.dataTablesLanguage = {
        "sEmptyTable":     "{{ __('messages.datatables.sEmptyTable') }}",
        "sInfo":           "{{ __('messages.datatables.sInfo') }}",
        "sInfoEmpty":      "{{ __('messages.datatables.sInfoEmpty') }}",
        "sInfoFiltered":   "{{ __('messages.datatables.sInfoFiltered') }}",
        "sInfoPostFix":    "{{ __('messages.datatables.sInfoPostFix') }}",
        "sInfoThousands":  "{{ __('messages.datatables.sInfoThousands') }}",
        "sLengthMenu":     "{{ __('messages.datatables.sLengthMenu') }}",
        "sLoadingRecords": "{{ __('messages.datatables.sLoadingRecords') }}",
        "sProcessing":     "{{ __('messages.datatables.sProcessing') }}",
        "sSearch":         "{{ __('messages.datatables.sSearch') }}",
        "sZeroRecords":    "{{ __('messages.datatables.sZeroRecords') }}",
        "oPaginate": {
            "sFirst":    "{{ __('messages.datatables.sFirst') }}",
            "sLast":     "{{ __('messages.datatables.sLast') }}",
            "sNext":     "{{ __('messages.datatables.sNext') }}",
            "sPrevious": "{{ __('messages.datatables.sPrevious') }}"
        },
        "oAria": {
            "sSortAscending":  "{{ __('messages.datatables.sSortAscending') }}",
            "sSortDescending": "{{ __('messages.datatables.sSortDescending') }}"
        }
    };
  </script>
  <style>
  @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');
  
  body, .hanken-grotesk {
    font-family: 'Cairo', sans-serif !important;
  }

  .text-gradient {
      background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
      -webkit-background-clip: text !important;
      -webkit-text-fill-color: transparent !important;
      background-clip: text !important;
      color: transparent !important;
      display: inline-block;
  }

  /* Contrast fallback for dark/colored backgrounds */
  .bg-dark .text-gradient,
  .bg-primary .text-gradient,
  .card-header .text-gradient,
  .bg-gradient .text-gradient,
  .text-contrast .text-gradient,
  .text-contrast {
      background: none !important;
      -webkit-background-clip: unset !important;
      -webkit-text-fill-color: initial !important;
      background-clip: unset !important;
      color: #ffffff !important;
  }
  
  /* Force Neo-Glassmorphism Light Theme globally */
  body, html {
      --glass-bg: rgba(255, 255, 255, 0.85);
      --glass-border: rgba(0, 0, 0, 0.08);
      --neon-glow: 0 5px 20px rgba(0, 0, 0, 0.05);
      --text-primary: #1a202c !important;
      --text-secondary: #4a5568 !important;
      
      background-color: #f8fafc !important;
      background-image: radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.05) 0%, transparent 40%), radial-gradient(circle at 90% 80%, rgba(16, 185, 129, 0.05) 0%, transparent 40%) !important;
      background-attachment: fixed !important;
      color: var(--text-primary) !important;
  }
  
  .wrapper { background: transparent !important; }

  /* Generic Card Overrides */
  .card, .glass-card {
      background: var(--glass-bg) !important;
      backdrop-filter: blur(16px) !important;
      -webkit-backdrop-filter: blur(16px) !important;
      border: 1px solid var(--glass-border) !important;
      box-shadow: var(--neon-glow) !important;
      border-radius: 16px !important;
      color: var(--text-primary) !important;
  }
  .card:hover, .glass-card:hover {
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3) !important;
  }
  
  /* Generic Table Overrides to fix ALL pages */
  .table, .table-bordered, .dataTable {
      color: var(--text-primary) !important;
      border-collapse: separate !important;
      border-spacing: 0 !important;
      border: none !important;
  }
  .table-bordered td, .table-bordered th { border: none !important; border-bottom: 1px solid var(--glass-border) !important; }
  table.dataTable tbody tr { background-color: transparent !important; }
  .table thead th, table.dataTable thead th {
      background: rgba(0,0,0,0.03) !important;
      border-bottom: 2px solid var(--glass-border) !important;
      color: var(--text-secondary) !important;
      font-weight: 600 !important; border-top: none !important;
  }
  .table tbody tr, table.dataTable tbody tr { border-bottom: 1px solid var(--glass-border) !important; transition: background-color 0.2s ease; }
  .table tbody tr:hover, table.dataTable tbody tr:hover { background-color: rgba(0, 0, 0, 0.02) !important; }

  /* Form & Inputs Overrides */
  .form-control, .form-select, input[type="search"] {
      background: rgba(0, 0, 0, 0.02) !important;
      border: 1px solid var(--glass-border) !important;
      color: var(--text-primary) !important;
      border-radius: 8px !important;
  }
  .form-control:focus, .form-select:focus {
      background: rgba(0, 0, 0, 0.0) !important;
      box-shadow: 0 0 10px rgba(59, 130, 246, 0.2) !important;
      border-color: #3b82f6 !important;
  }

  /* Utility Overrides */
  .bg-light, .bg-white, .table-responsive { background: transparent !important; color: var(--text-primary) !important; }
  .text-dark { color: var(--text-primary) !important; }
  .text-muted, .text-secondary { color: var(--text-secondary) !important; }
  h1, h2, h3, h4, h5, h6, .menu-title { color: var(--text-primary) !important; }
  
  /* Labels & Selects (DataTables) */
  label, .dataTables_info, .dataTables_length { color: var(--text-secondary) !important; }
  .dataTables_wrapper .dataTables_paginate .paginate_button { color: var(--text-primary) !important; }
  
  /* Badges & Scrolls */
  .neo-badge { padding: 0.4rem 0.8rem; border-radius: 50rem; font-weight: 500; font-size: 0.85rem; box-shadow: 0 0 10px currentColor; }
  .neo-badge-success { color: #10b981; background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; }
  .neo-badge-primary { color: #3b82f6; background: rgba(59, 130, 246, 0.1); border: 1px solid #3b82f6; }
  .neo-badge-danger { color: #ef4444; background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; }
  .neo-badge-info { color: #0ea5e9; background: rgba(14, 165, 233, 0.1); border: 1px solid #0ea5e9; }

  .neo-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
  .neo-scrollbar::-webkit-scrollbar-track { background: transparent; }
  .neo-scrollbar::-webkit-scrollbar-thumb { background: var(--glass-border); border-radius: 10px; }
  
  .neo-list-item { transition: all 0.2s; border-radius: 8px; padding: 8px 12px; }
  .neo-list-item:hover { background: rgba(150, 150, 150, 0.1); transform: scale(1.01); }

  
  /* Mobile Overlay Fixes */
  .overlay { background: rgba(0,0,0,0.5); backdrop-filter: blur(5px); }
  
  /* Sidebar Neo-Glassmorphism */
  .sidebar-wrapper {
      background: rgba(255, 255, 255, 0.75) !important;
      backdrop-filter: blur(20px) !important;
      -webkit-backdrop-filter: blur(20px) !important;
      border-inline-end: 1px solid var(--glass-border) !important;
      z-index: 1030;
  }
  .sidebar-wrapper .navigation { background: transparent !important; }
  .sidebar-wrapper .navigation li a { transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1); }
  .sidebar-wrapper .navigation .menu-label { color: var(--text-secondary) !important; }
  .sidebar-wrapper .navigation ul { background: transparent !important; }

  /* RTL Form Check adjustments */
  [dir="rtl"] .form-check {
      padding-right: 1.5em !important;
      padding-left: 0 !important;
      text-align: right;
  }
  [dir="rtl"] .form-check .form-check-input {
      float: right !important;
      margin-right: -1.5em !important;
      margin-left: 0 !important;
  }
  [dir="rtl"] .form-check-inline {
      margin-right: 0 !important;
      margin-left: 1rem !important;
  }
  [dir="rtl"] .form-switch {
      padding-right: 2.5em !important;
      padding-left: 0 !important;
  }
  [dir="rtl"] .form-switch .form-check-input {
      margin-right: -2.5em !important;
      margin-left: 0 !important;
      background-position: right center !important;
  }
  </style>

  <title>{{__('messages.NA')}}</title>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-TX958298Y6"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-TX958298Y6');
</script>
</head>
@php
$hasSidebar = auth()->check() && (
    auth()->user()->hasRole('super admin') ||
    auth()->user()->hasRole('Committees') ||
    auth()->user()->hasRole('ServiceBody') ||
    auth()->user()->hasRole('rsc') ||
    auth()->user()->can('manage own forms')
);
@endphp
<body class="hanken-grotesk {{ $hasSidebar ? 'has-sidebar' : '' }}">
{{--     wrapper--}}
    <div class="wrapper">

        <!-- Nav Bar-->
        <x-nav-bar />
        <!-- / Nav Bar-->
         @if($hasSidebar)
        <!-- sidebar -->
        <x-side-bar />
        <!-- / sidebar -->
         @endif
        <main class="page-content hanken-grotesk">
          <div class="container-fluid">
            {{ $slot }}
          </div>
        </main>

        <!--start overlay-->
        <div class="overlay nav-toggle-icon"></div>
        <!--end overlay-->

        <!--Start Back To Top Button-->
        <x-back-to-top />
        <!--End Back To Top Button-->

        <!--start switcher-->
        {{-- <x-switch-themes /> --}}
        <!--end switcher-->

    </div>
    <!-- / wrapper-->

    <script type="module">
        document.addEventListener("DOMContentLoaded", function() {
            // Wait slightly for custom.js to initialize first, since it is also waiting for DOMContentLoaded
            setTimeout(function() {
                if (window.jQuery && $.fn.dataTable) {
                    // Set defaults for any future tables
                    $.extend(true, $.fn.dataTable.defaults, { language: window.dataTablesLanguage });

                    // Re-initialize already initialized tables to apply translations
                    var $foundTables = $('table.display, table.data-table, .main-tables');
                    if ($foundTables.length) {
                        $foundTables.each(function() {
                            var $table = $(this);
                            if ($.fn.DataTable.isDataTable(this)) {
                                var isServerSide = $table.data('server-pagination') === true;
                                $table.DataTable({
                                    paging: !isServerSide,
                                    info: !isServerSide,
                                    ordering: true,
                                    destroy: true,
                                    language: window.dataTablesLanguage,
                                    initComplete: function () {
                                        this.api().columns().every(function () {
                                            var column = this;
                                            var footer = column.footer();
                                            if (footer) {
                                                var input = footer.querySelector('input');
                                                if (input) return; // already added

                                                var title = footer.textContent;
                                                var newInput = document.createElement('input');
                                                newInput.placeholder = title;
                                                newInput.className = 'form-control form-control-sm';
                                                footer.replaceChildren(newInput);

                                                newInput.addEventListener('keyup', () => {
                                                    if (column.search() !== newInput.value) {
                                                        column.search(newInput.value).draw();
                                                    }
                                                });
                                                newInput.addEventListener('click', function (e) {
                                                    e.stopPropagation();
                                                });
                                            }
                                        });
                                    }
                                });
                            }
                        });
                    }
                }
            }, 300);
        });
    </script>
    <!-- Archive Loading Overlay -->
    <div id="page-loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 9999; align-items: center; justify-content: center;">
        <div class="text-center p-4 rounded-4 shadow-lg border bg-white" style="max-width: 280px; width: 90%;">
            <div class="spinner-border text-primary" role="status" style="width: 3.5rem; height: 3.5rem; border-width: 0.25em;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h5 class="mt-4 fw-bold text-dark mb-1">
                {{ app()->getLocale() === 'ar' ? 'جاري التحميل...' : 'Loading Archive...' }}
            </h5>
            <p class="text-muted small mb-0">
                {{ app()->getLocale() === 'ar' ? 'يرجى الانتظار قليلاً' : 'Please wait a moment' }}
            </p>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var archiveLinks = document.querySelectorAll('a[href*="committee-reports/archive"], a[href*="groups-agendas/archive"]');
            archiveLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    if (e.button === 0 && !e.ctrlKey && !e.shiftKey && !e.metaKey && !e.altKey) {
                        var overlay = document.getElementById('page-loading-overlay');
                        if (overlay) {
                            overlay.style.display = 'flex';
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>
