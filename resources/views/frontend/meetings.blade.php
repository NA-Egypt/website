<x-frontend.layout>
<x-section-head>{{__('messages.Recovery Meetings')}}</x-section-head>

<livewire:meeting-filter />

<button id="scrollToTopBtn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" title="Go to top">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 24px; height: 24px;">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
    </svg>
</button>

<style>
    #scrollToTopBtn {
        position: fixed;
        bottom: 80px;
        z-index: 99999;
        background-color: rgba(0, 105, 143, 0.8);
        color: white;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 12px;
        border-radius: 50%;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s, background-color 0.3s;
    }
    html[dir="rtl"] #scrollToTopBtn {
        left: 20px;
    }
    html[dir="ltr"] #scrollToTopBtn,
    html:not([dir]) #scrollToTopBtn {
        right: 20px;
    }
    #scrollToTopBtn.show {
        opacity: 1;
        visibility: visible;
    }
    #scrollToTopBtn:hover {
        background-color: rgba(0, 80, 112, 1);
    }
    @media (min-width: 768px) {
        #scrollToTopBtn {
            bottom: 40px;
        }
        html[dir="rtl"] #scrollToTopBtn {
            left: 30px;
        }
        html[dir="ltr"] #scrollToTopBtn,
        html:not([dir]) #scrollToTopBtn {
            right: 30px;
        }
    }
</style>

<script>
    if (!window.setupScrollToTop) {
        window.setupScrollToTop = true;
        window.addEventListener('scroll', function() {
            var btn = document.getElementById('scrollToTopBtn');
            if (!btn) return;
            
            var scrollPos = window.scrollY || window.pageYOffset || document.documentElement.scrollTop || 0;
            if (scrollPos > 100) {
                btn.classList.add('show');
            } else {
                btn.classList.remove('show');
            }
        }, { passive: true });
    }
    
    // Trigger once on initialization to handle case where page is already scrolled
    setTimeout(function() {
        window.dispatchEvent(new Event('scroll'));
    }, 100);
</script>

</x-frontend.layout>