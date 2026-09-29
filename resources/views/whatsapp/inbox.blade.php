<x-layout>
    <x-backhead>{{ __('messages.whatsapp_inbox') }}</x-backhead>

    <div class="container-fluid px-4 py-3">
        {{-- Header & Subtitle --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h3 class="fw-bold mb-1" style="color: var(--text-primary);">
                    <i class="bi bi-chat-left-dots-fill text-primary me-2"></i>{{ __('messages.whatsapp_inbox') }}
                </h3>
                <p class="text-muted mb-0 small">
                    {{ __('messages.whatsapp_inbox_desc') }}
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('whatsapp.reports.index') }}" class="btn btn-outline-success rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-graph-up"></i>
                    <span>{{ __('messages.whatsapp_reports') }}</span>
                </a>
                <a href="{{ route('whatsapp.device.status') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-whatsapp"></i>
                    <span>{{ __('messages.whatsapp_device') }}</span>
                </a>
            </div>
        </div>

        {{-- Staging / Dev Mode Notice (egyptna.org) --}}
        @if($isDev)
            <div class="alert alert-warning rounded-4 border-0 shadow-sm p-3 mb-3" style="background: rgba(245, 158, 11, 0.1); border-left: 4px solid #f59e0b !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="small fw-semibold text-warning-emphasis">
                        <i class="bi bi-tools me-1"></i>{{ __('messages.whatsapp_dev_badge') }} — {{ __('messages.whatsapp_dev_notice') }}
                    </span>
                    <a href="{{ route('whatsapp.docs') }}#dev-testing" class="btn btn-warning btn-sm rounded-pill px-3 py-1 shadow-sm">{{ __('Developer Testing Guide') }}</a>
                </div>
            </div>
        @endif

        {{-- Two-Pane Chat Container --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--card-bg, #ffffff); height: calc(100vh - 210px); min-height: 580px;">
            <div class="row g-0 h-100">
                {{-- Left Pane: Conversation List --}}
                <div class="col-lg-4 col-md-5 border-end h-100 d-flex flex-column" style="background: #fafbfc;">
                    <div class="p-3 border-bottom bg-white">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="searchConversations" class="form-control bg-light border-0 small" placeholder="{{ __('messages.Search conversations...') }}">
                        </div>
                    </div>

                    <div class="overflow-y-auto flex-grow-1" id="conversationsList">
                        @forelse($conversations as $conv)
                            @php
                                $isSelected = $selectedConversation && $selectedConversation->id === $conv->id;
                                $isLive = $conv->isLiveAgentActive();
                            @endphp
                            <a href="{{ route('whatsapp.inbox', ['conversation_id' => $conv->id]) }}" 
                               class="d-flex align-items-center gap-3 p-3 border-bottom text-decoration-none text-reset conversation-item {{ $isSelected ? 'bg-primary-subtle border-start border-primary border-4' : 'hover-bg-light' }}"
                               data-phone="{{ $conv->phone }}" data-name="{{ $conv->name }}">
                                
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0"
                                     style="width: 44px; height: 44px; background: {{ $isLive ? 'linear-gradient(135deg, #f59e0b, #d97706)' : 'linear-gradient(135deg, #10b981, #059669)' }};">
                                    {{ mb_substr($conv->name ?: ($conv->phone ?: 'NA'), 0, 1) }}
                                </div>

                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                                        <h6 class="mb-0 fw-bold text-truncate" style="max-width: 140px;">
                                            {{ $conv->name ?: $conv->phone }}
                                        </h6>
                                        <small class="text-muted" style="font-size: 0.72rem;">
                                            {{ $conv->last_interaction_at ? $conv->last_interaction_at->diffForHumans(null, true) : '' }}
                                        </small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small text-truncate d-block" style="max-width: 160px; font-size: 0.8rem;">
                                            {{ $conv->phone }}
                                        </span>
                                        @if($isLive)
                                            <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.65rem;">
                                                <i class="bi bi-person-fill"></i> {{ __('messages.whatsapp_live_agent') }}
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.65rem;">
                                                <i class="bi bi-robot"></i> {{ __('messages.whatsapp_bot_active') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center p-5 text-muted">
                                <i class="bi bi-chat-square-dots display-4 mb-2 d-block text-secondary"></i>
                                <p class="small">{{ __('No active conversations yet.') }}</p>
                            </div>
                        @endforelse
                    </div>

                    @if($conversations->hasPages())
                        <div class="p-2 border-top bg-white small text-center">
                            {{ $conversations->links() }}
                        </div>
                    @endif
                </div>

                {{-- Right Pane: Chat Thread --}}
                <div class="col-lg-8 col-md-7 h-100 d-flex flex-column bg-white">
                    @if($selectedConversation)
                        {{-- Chat Top Header --}}
                        <div class="p-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2 bg-light">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                     style="width: 44px; height: 44px; background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                                    {{ mb_substr($selectedConversation->name ?: ($selectedConversation->phone ?: 'NA'), 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">
                                        {{ $selectedConversation->name ?: $selectedConversation->phone }}
                                    </h6>
                                    <div class="d-flex align-items-center gap-2 small text-muted">
                                        <span><i class="bi bi-phone"></i> {{ $selectedConversation->phone }}</span>
                                        @if($selectedConversation->isLiveAgentActive())
                                            <span class="text-warning fw-semibold">
                                                • {{ __('messages.whatsapp_live_agent') }} ({{ __('active until') }} {{ $selectedConversation->live_agent_until ? $selectedConversation->live_agent_until->format('H:i') : '' }})
                                            </span>
                                        @else
                                            <span class="text-success fw-semibold">• {{ __('messages.whatsapp_bot_active') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <a href="tel:{{ $selectedConversation->phone }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1" title="Call">
                                    <i class="bi bi-telephone-fill"></i>
                                    <span>{{ __('Call') }}</span>
                                </a>

                                <form method="POST" action="{{ route('whatsapp.inbox.toggle-live-agent', $selectedConversation) }}">
                                    @csrf
                                    @if($selectedConversation->isLiveAgentActive())
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                                            <i class="bi bi-robot"></i>
                                            <span>{{ __('messages.whatsapp_resume_bot') }}</span>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-warning btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                                            <i class="bi bi-person-fill-gear"></i>
                                            <span>{{ __('messages.whatsapp_pause_bot') }}</span>
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </div>

                        {{-- Message History Thread --}}
                        <div class="p-4 overflow-y-auto flex-grow-1 d-flex flex-column gap-3" id="messagesContainer" style="background: #f8fafc;">
                            @forelse($messages as $msg)
                                @php
                                    $isIncoming = $msg->direction === 'incoming';
                                    $isBot = $msg->sender_type === 'bot';
                                    $isAgent = $msg->sender_type === 'agent';
                                @endphp
                                <div class="d-flex {{ $isIncoming ? 'justify-content-start' : 'justify-content-end' }}">
                                    <div class="card border-0 shadow-sm rounded-4 p-3" 
                                         style="max-width: 75%; background: {{ $isIncoming ? '#ffffff' : ($isBot ? '#e6f4ea' : '#e0e7ff') }}; color: #1e293b;">
                                        
                                        {{-- Header info --}}
                                        <div class="d-flex justify-content-between align-items-center mb-1 gap-3" style="font-size: 0.72rem;">
                                            @if($isIncoming)
                                                <span class="fw-bold text-primary">{{ $selectedConversation->name ?: $selectedConversation->phone }}</span>
                                            @elseif($isBot)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                                    <i class="bi bi-robot"></i> NA Bot (الرد الآلي)
                                                </span>
                                            @else
                                                <span class="badge bg-primary text-white rounded-pill">
                                                    <i class="bi bi-person-fill"></i> {{ $msg->user ? $msg->user->name : 'Helpline Volunteer' }}
                                                </span>
                                            @endif
                                            <span class="text-muted">{{ $msg->created_at ? $msg->created_at->format('h:i A') : '' }}</span>
                                        </div>

                                        {{-- Body text --}}
                                        <div class="text-break" style="white-space: pre-wrap; font-size: 0.9rem;">{{ $msg->body }}</div>

                                        @if($msg->category)
                                            <div class="mt-1 text-end">
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.65rem;">
                                                    {{ $msg->category }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center p-5 text-muted">
                                    <i class="bi bi-chat-dots display-4 mb-2 d-block text-secondary"></i>
                                    <p class="small">{{ __('No message records found in this conversation.') }}</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- Quick Responses / Templates Bar --}}
                        <div class="px-3 py-2 bg-light border-top d-flex gap-2 overflow-x-auto flex-nowrap" id="quickChips">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill text-nowrap py-1 px-3 quick-chip" data-text="أهلاً بك يا صديقي في خط مساعدة زمالة المدمنين المجهولين بمصر. كيف يمكنني مساعدتك؟">
                                👋 ترحيب خط المساعدة
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill text-nowrap py-1 px-3 quick-chip" data-text="يمكنك حضور أحد اجتماعات التعافي اليوم عبر الرابط: https://naegypt.org/meetings">
                                📍 رابط الاجتماعات
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill text-nowrap py-1 px-3 quick-chip" data-text="هل تفضل أن يتصل بك أحد متطوعي خط المساعدة هاتفياً؟">
                                📞 عرض اتصال هاتفي
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill text-nowrap py-1 px-3 quick-chip" data-text="شكراً لتواصلك مع الزمالة. نأمل لك يوماً نظيفاً وموفقاً.">
                                ✨ ختام المحادثة
                            </button>
                        </div>

                        {{-- Input & Send Message Form --}}
                        @can('reply whatsapp messages')
                            <div class="p-3 border-top bg-white">
                                <form method="POST" action="{{ route('whatsapp.inbox.send', $selectedConversation) }}" id="replyForm">
                                    @csrf
                                    <div class="input-group">
                                        <textarea name="message" id="messageInput" rows="2" class="form-control rounded-start-4 border shadow-sm" placeholder="{{ __('messages.whatsapp_type_message') }}" required></textarea>
                                        <button type="submit" class="btn btn-primary rounded-end-4 px-4 shadow-sm d-flex flex-column align-items-center justify-content-center">
                                            <i class="bi bi-send-fill fs-5"></i>
                                            <span class="small" style="font-size: 0.72rem;">{{ __('Send') }}</span>
                                        </button>
                                    </div>
                                    <div class="form-text small mt-1">
                                        <i class="bi bi-info-circle me-1"></i>
                                        {{ __('Sending a reply will automatically activate Live Volunteer Mode and pause automated bot replies for 30 minutes.') }}
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="p-3 border-top bg-light text-center text-muted small">
                                <i class="bi bi-lock-fill me-1"></i>{{ __('You do not have permission to reply as a volunteer (reply whatsapp messages).') }}
                            </div>
                        @endcan
                    @else
                        <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-5 text-muted">
                            <i class="bi bi-chat-square-text display-3 mb-3 text-secondary opacity-50"></i>
                            <h5 class="fw-bold">{{ __('Select a Conversation') }}</h5>
                            <p class="small text-muted" style="max-width: 320px;">
                                {{ __('Choose a conversation from the left panel to inspect message history, take over as live agent, or respond.') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto scroll message thread to bottom
            const container = document.getElementById('messagesContainer');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }

            // Quick response chip click handler
            const chips = document.querySelectorAll('.quick-chip');
            const messageInput = document.getElementById('messageInput');
            chips.forEach(chip => {
                chip.addEventListener('click', function () {
                    if (messageInput) {
                        messageInput.value = this.getAttribute('data-text');
                        messageInput.focus();
                    }
                });
            });

            // Live filter for conversations list
            const searchInput = document.getElementById('searchConversations');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const term = this.value.toLowerCase().trim();
                    const items = document.querySelectorAll('.conversation-item');
                    items.forEach(item => {
                        const name = (item.getAttribute('data-name') || '').toLowerCase();
                        const phone = (item.getAttribute('data-phone') || '').toLowerCase();
                        if (name.includes(term) || phone.includes(term)) {
                            item.style.display = 'flex';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
    @endpush
</x-layout>
