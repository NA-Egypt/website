<x-layout>
    {{-- Dedicated Viewport-Locked WhatsApp Inbox Container --}}
    <div id="whatsappInboxApp" class="wa-inbox-root">
        {{-- Toast Alerts --}}
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 3000;">
            <div id="inboxToast" class="toast align-items-center text-white bg-dark border-0 rounded-4 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2 small" id="inboxToastMessage">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>Notification</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        {{-- MAIN 3-COLUMN WHATSAPP APP SHELL --}}
        <div class="wa-shell">
            
            {{-- 1. CONVERSATIONS LIST PANE (Left / Right in RTL) --}}
            <aside id="leftPane" class="wa-list-pane {{ request()->has('conversation_id') ? 'd-none d-md-flex' : 'd-flex' }}">
                {{-- Pane Top Header Bar --}}
                <div class="wa-list-header">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 36px; height: 36px;">
                                <i class="bi bi-whatsapp fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">
                                    {{ __('messages.whatsapp_inbox') }}
                                </h6>
                                <span class="badge bg-secondary-subtle text-secondary border rounded-pill font-monospace" style="font-size: 0.65rem;" id="totalConvsCount">
                                    {{ $conversations->total() }} {{ __('محادثة') }}
                                </span>
                            </div>
                        </div>

                        {{-- Utility Actions Hub --}}
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" id="btnToggleAudio" class="btn btn-sm btn-light rounded-circle p-1" style="width: 32px; height: 32px;" title="{{ __('Sound Alert') }}">
                                <i class="bi bi-bell-fill text-warning fs-6" id="audioBellIcon"></i>
                            </button>

                            <a href="{{ route('whatsapp.reports.index') }}" class="btn btn-sm btn-light rounded-circle p-1" style="width: 32px; height: 32px;" title="{{ __('messages.whatsapp_reports') }}">
                                <i class="bi bi-graph-up text-success fs-6"></i>
                            </a>

                            <a href="{{ route('whatsapp.subscribers.index') }}" class="btn btn-sm btn-light rounded-circle p-1" style="width: 32px; height: 32px;" title="{{ __('messages.whatsapp_bulk_csv_title') }}">
                                <i class="bi bi-megaphone-fill text-primary fs-6"></i>
                            </a>

                            <a href="{{ route('whatsapp.device.status') }}" class="btn btn-sm btn-light rounded-circle p-1" style="width: 32px; height: 32px;" title="{{ __('messages.whatsapp_device') }}">
                                <i class="bi bi-qr-code text-secondary fs-6"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Search Input Bar --}}
                    <div class="wa-search-wrapper mb-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="searchConversations" class="form-control bg-light border-0 small" 
                                   placeholder="{{ __('messages.Search conversations...') }}" 
                                   value="{{ request('search') }}" autocomplete="off">
                            <button type="button" id="btnClearSearch" class="btn btn-light border-0 text-muted" style="display: none;">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Category Filter Pills --}}
                    <div class="d-flex gap-1 overflow-x-auto flex-nowrap pb-1 wa-filter-pills" id="filterPills">
                        <button type="button" class="btn btn-xs rounded-pill px-2 py-1 filter-pill active" data-filter="all">
                            {{ __('messages.whatsapp_filter_all') }}
                        </button>
                        <button type="button" class="btn btn-xs rounded-pill px-2 py-1 filter-pill" data-filter="live">
                            <i class="bi bi-person-fill text-warning me-1"></i>{{ __('messages.whatsapp_filter_live') }}
                        </button>
                        <button type="button" class="btn btn-xs rounded-pill px-2 py-1 filter-pill" data-filter="bot">
                            <i class="bi bi-robot text-success me-1"></i>{{ __('messages.whatsapp_filter_bot') }}
                        </button>
                        <button type="button" class="btn btn-xs rounded-pill px-2 py-1 filter-pill" data-filter="unread">
                            <i class="bi bi-envelope-fill text-primary me-1"></i>{{ __('messages.whatsapp_filter_unread') }}
                        </button>
                    </div>

                    @if($isDev)
                        <div class="mt-2 py-1 px-2 rounded-2 bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-between font-monospace" style="font-size: 0.68rem;">
                            <span><i class="bi bi-tools me-1"></i>{{ __('messages.whatsapp_dev_badge') }}</span>
                            <a href="{{ route('whatsapp.docs') }}#dev-testing" class="text-warning-emphasis text-decoration-underline">{{ __('Guide') }}</a>
                        </div>
                    @endif
                </div>

                {{-- Scrollable List of Contacts (Infinite Scroll & Instant Click Fallback) --}}
                <div class="wa-scroll-viewport" id="conversationsList" data-current-page="{{ $conversations->currentPage() }}" data-last-page="{{ $conversations->lastPage() }}">
                    @forelse($conversations as $conv)
                        @php
                            $isSelected = $selectedConversation && $selectedConversation->id === $conv->id;
                            $isLive = $conv->isLiveAgentActive();
                            $unreadCount = $conv->unread_count ?? 0;
                            $hue = crc32($conv->phone ?? $conv->jid) % 360;
                            $latestMsg = $conv->latestMessage;
                        @endphp
                        <a href="{{ route('whatsapp.inbox', ['conversation_id' => $conv->id]) }}"
                           class="conversation-item {{ $isSelected ? 'active-chat' : '' }}"
                           data-id="{{ $conv->id }}"
                           data-phone="{{ $conv->phone }}" 
                           data-name="{{ $conv->name }}"
                           data-jid="{{ $conv->jid }}"
                           data-is-live="{{ $isLive ? '1' : '0' }}"
                           data-unread="{{ $unreadCount }}">
                            
                            {{-- Avatar Initial --}}
                            <div class="conversation-avatar flex-shrink-0" style="background: hsl({{ $hue }}, 65%, 45%);">
                                {{ mb_substr($conv->name ?: ($conv->phone ?: 'NA'), 0, 1) }}
                            </div>

                            {{-- Contact Information & Message Preview --}}
                            <div class="conversation-info flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-baseline mb-1">
                                    <span class="fw-bold text-truncate text-dark item-title">
                                        {{ $conv->name ?: $conv->phone }}
                                    </span>
                                    <small class="text-muted item-time flex-shrink-0">
                                        {{ $conv->last_interaction_at ? $conv->last_interaction_at->diffForHumans(null, true) : '' }}
                                    </small>
                                </div>
                                
                                {{-- Last Message Snippet --}}
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <p class="text-muted small text-truncate mb-0 item-snippet">
                                        @if($latestMsg)
                                            @if($latestMsg->direction === 'outgoing')
                                                <i class="bi bi-check2-all text-primary me-1" title="Outgoing"></i>
                                            @endif
                                            {{ mb_substr($latestMsg->body ?? '', 0, 40) }}
                                        @else
                                            <span class="font-monospace text-secondary">{{ $conv->phone }}</span>
                                        @endif
                                    </p>

                                    {{-- Badges Hub --}}
                                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                        <span class="badge bg-success rounded-pill font-monospace unread-badge {{ $unreadCount > 0 ? '' : 'd-none' }}" style="font-size: 0.65rem;">
                                            {{ $unreadCount }}
                                        </span>
                                        @if($isLive)
                                            <span class="badge bg-warning text-dark rounded-pill py-1 px-2 live-badge" style="font-size: 0.65rem;" title="{{ __('messages.whatsapp_filter_live') }}">
                                                <i class="bi bi-person-fill"></i>
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-1 px-2 bot-badge" style="font-size: 0.65rem;" title="{{ __('messages.whatsapp_filter_bot') }}">
                                                <i class="bi bi-robot"></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center p-5 text-muted" id="emptyConversationsNotice">
                            <i class="bi bi-chat-square-dots display-4 mb-2 d-block text-secondary opacity-50"></i>
                            <p class="small mb-0">{{ __('No active conversations yet.') }}</p>
                        </div>
                    @endforelse

                    {{-- Infinite scroll loader indicator --}}
                    <div id="infiniteScrollLoader" class="text-center py-2 d-none">
                        <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                    </div>
                </div>
            </aside>

            {{-- 2. CHAT THREAD PANE (Center) --}}
            <main id="rightPane" class="wa-chat-pane {{ request()->has('conversation_id') ? 'd-flex' : 'd-none d-md-flex' }}">
                
                {{-- Active Chat Wrapper --}}
                <div id="activeChatWrapper" class="{{ $selectedConversation ? 'd-flex' : 'd-none' }}">
                    
                    {{-- Fixed Opaque Header Bar --}}
                    <header class="wa-chat-header" id="chatHeaderBar">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            {{-- Mobile Back Button --}}
                            <button type="button" id="btnMobileBack" class="btn btn-sm btn-light rounded-circle d-md-none p-1 flex-shrink-0" style="width: 34px; height: 34px;" title="{{ __('messages.whatsapp_back_to_conversations') }}">
                                <i class="bi bi-arrow-right fs-5"></i>
                            </button>

                            @php
                                $selectedHue = $selectedConversation ? (crc32($selectedConversation->phone ?? $selectedConversation->jid) % 360) : 180;
                            @endphp
                            <div id="headerAvatar" class="conversation-avatar flex-shrink-0 cursor-pointer"
                                 style="background: hsl({{ $selectedHue }}, 65%, 45%);"
                                 title="{{ __('messages.whatsapp_open_drawer') }}">
                                <span id="headerAvatarInitial">{{ $selectedConversation ? mb_substr($selectedConversation->name ?: ($selectedConversation->phone ?: 'NA'), 0, 1) : 'NA' }}</span>
                            </div>

                            <div class="cursor-pointer min-w-0" id="headerTitleArea" title="{{ __('messages.whatsapp_open_drawer') }}">
                                <h6 class="fw-bold mb-0 text-dark text-truncate" id="headerContactName" style="font-size: 0.95rem;">
                                    {{ $selectedConversation ? ($selectedConversation->name ?: $selectedConversation->phone) : '' }}
                                </h6>
                                <div class="d-flex align-items-center gap-2 small text-muted flex-wrap">
                                    <span class="font-monospace" id="headerContactPhone" style="font-size: 0.76rem;"><i class="bi bi-phone"></i> {{ $selectedConversation ? $selectedConversation->phone : '' }}</span>
                                    <span id="liveAgentStatusBadge">
                                        @if($selectedConversation && $selectedConversation->isLiveAgentActive())
                                            <span class="text-warning fw-semibold" style="font-size: 0.74rem;">
                                                • {{ __('messages.whatsapp_live_agent') }} ({{ __('حتى') }} {{ $selectedConversation->live_agent_until ? $selectedConversation->live_agent_until->format('H:i') : '' }})
                                            </span>
                                        @else
                                            <span class="text-success fw-semibold" style="font-size: 0.74rem;">• {{ __('messages.whatsapp_bot_active') }}</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Header Action Buttons --}}
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill small d-none d-lg-inline-flex align-items-center gap-1 py-1 px-2" title="{{ __('messages.whatsapp_realtime_badge') }}">
                                <span class="spinner-grow spinner-grow-sm text-success" style="width: 6px; height: 6px;" role="status"></span>
                                <span style="font-size: 0.68rem;">{{ __('messages.whatsapp_realtime_badge') }}</span>
                            </span>

                            <a id="btnCallPhone" href="tel:{{ $selectedConversation ? $selectedConversation->phone : '' }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-xs d-flex align-items-center gap-1" title="Call">
                                <i class="bi bi-telephone-fill"></i>
                                <span class="d-none d-sm-inline">{{ __('اتصال') }}</span>
                            </a>

                            <button type="button" id="btnToggleLiveAgent" class="btn {{ ($selectedConversation && $selectedConversation->isLiveAgentActive()) ? 'btn-success' : 'btn-warning' }} btn-sm rounded-pill px-3 shadow-xs d-flex align-items-center gap-1">
                                <i class="bi {{ ($selectedConversation && $selectedConversation->isLiveAgentActive()) ? 'bi-robot' : 'bi-person-fill-gear' }}" id="toggleLiveAgentIcon"></i>
                                <span id="toggleLiveAgentText">{{ ($selectedConversation && $selectedConversation->isLiveAgentActive()) ? __('messages.whatsapp_resume_bot') : __('messages.whatsapp_pause_bot') }}</span>
                            </button>

                            <button type="button" id="btnToggleDrawer" class="btn btn-outline-secondary btn-sm rounded-circle p-1 shadow-xs d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;" title="{{ __('messages.whatsapp_open_drawer') }}">
                                <i class="bi bi-layout-sidebar-inset-reverse fs-6"></i>
                            </button>
                        </div>
                    </header>

                    {{-- STRICT SCROLLABLE MESSAGES VIEWPORT --}}
                    <div class="wa-messages-viewport" id="messagesContainer">
                        @php
                            $lastDate = null;
                        @endphp
                        @forelse($messages as $msg)
                            @php
                                $msgDate = $msg->created_at ? $msg->created_at->format('Y-m-d') : null;
                                $isIncoming = $msg->direction === 'incoming';
                                $isBot = $msg->sender_type === 'bot';
                                $bubbleClass = $isIncoming ? 'wa-bubble-incoming' : ($isBot ? 'wa-bubble-bot' : 'wa-bubble-outgoing');
                                $rowClass = $isIncoming ? 'incoming-row' : 'outgoing-row';
                            @endphp

                            {{-- Date Divider Pill --}}
                            @if($msgDate && $msgDate !== $lastDate)
                                @php
                                    $lastDate = $msgDate;
                                    $isToday = $msg->created_at->isToday();
                                    $isYesterday = $msg->created_at->isYesterday();
                                    $dateLabel = $isToday ? __('messages.whatsapp_today') : ($isYesterday ? __('messages.whatsapp_yesterday') : $msg->created_at->translatedFormat('j F Y'));
                                @endphp
                                <div class="wa-date-divider">
                                    <span class="badge wa-date-pill">
                                        {{ $dateLabel }}
                                    </span>
                                </div>
                            @endif

                            {{-- Message Bubble Row --}}
                            <div class="message-row {{ $rowClass }}" data-id="{{ $msg->id }}">
                                <div class="wa-bubble {{ $bubbleClass }}">
                                    
                                    {{-- Bubble Header Sender Tag --}}
                                    <div class="wa-bubble-header">
                                        @if($isIncoming)
                                            <span class="fw-bold text-primary">{{ $selectedConversation ? ($selectedConversation->name ?: $selectedConversation->phone) : '' }}</span>
                                        @elseif($isBot)
                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill font-monospace" style="font-size: 0.65rem;">
                                                <i class="bi bi-robot"></i> الرد الآلي
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.65rem;">
                                                <i class="bi bi-person-fill"></i> {{ $msg->user ? $msg->user->name : 'متطوع خط المساعدة' }}
                                            </span>
                                        @endif
                                        
                                        <button type="button" class="btn btn-link p-0 text-muted btn-copy-msg" data-body="{{ $msg->body }}" title="{{ __('messages.whatsapp_copy_message') }}">
                                            <i class="bi bi-clipboard" style="font-size: 0.72rem;"></i>
                                        </button>
                                    </div>

                                    {{-- Bubble Body Text --}}
                                    <div class="message-body-text">{{ $msg->body }}</div>

                                    {{-- Bubble Footer Meta (Timestamp + Double Check) --}}
                                    <div class="wa-bubble-meta">
                                        <span class="wa-msg-time">{{ $msg->created_at ? $msg->created_at->format('h:i A') : '' }}</span>
                                        @if(!$isIncoming)
                                            <i class="bi bi-check2-all text-primary wa-msg-check" title="Delivered"></i>
                                        @endif
                                    </div>

                                    @if($msg->category)
                                        <div class="wa-msg-category-tag">
                                            <span class="badge bg-light text-secondary border rounded-pill" style="font-size: 0.62rem;">
                                                {{ $msg->category }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center p-5 text-muted empty-state-box m-auto">
                                <i class="bi bi-chat-dots display-4 mb-2 d-block text-secondary opacity-50"></i>
                                <p class="small">{{ __('No message records found in this conversation.') }}</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Floating Scroll-to-Bottom Button --}}
                    <button type="button" id="btnScrollDown" class="btn btn-light shadow rounded-circle wa-scroll-down-btn" title="{{ __('messages.whatsapp_scroll_latest') }}">
                        <i class="bi bi-chevron-down fs-5 text-dark"></i>
                        <span id="unreadScrollBadge" class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="display: none;"></span>
                    </button>

                    {{-- FIXED OPAQUE BOTTOM REPLY DOCK --}}
                    <footer class="wa-chat-footer">
                        {{-- Collapsible Quick Chips Toolbar --}}
                        <div class="d-flex align-items-center gap-1 overflow-x-auto flex-nowrap pb-2 wa-quick-chips" id="quickChipsBar">
                            <button type="button" class="btn btn-outline-primary btn-xs rounded-pill text-nowrap py-1 px-2 shadow-xs d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#cannedModal">
                                <i class="bi bi-lightning-charge-fill text-warning"></i>
                                <span>{{ __('messages.whatsapp_quick_responses') }}</span>
                            </button>

                            <button type="button" class="btn btn-light btn-xs rounded-pill text-nowrap py-1 px-2 quick-chip shadow-xs border" data-text="أهلاً بك يا صديقي في خط مساعدة زمالة المدمنين المجهولين بمصر 🇪🇬 كيف يمكنني مساعدتك؟">
                                👋 ترحيب
                            </button>
                            <button type="button" class="btn btn-light btn-xs rounded-pill text-nowrap py-1 px-2 quick-chip shadow-xs border" data-text="يمكنك حضور أحد اجتماعات التعافي اليوم عبر الرابط التالي: https://naegypt.org/meetings">
                                📍 الاجتماعات
                            </button>
                            <button type="button" class="btn btn-light btn-xs rounded-pill text-nowrap py-1 px-2 quick-chip shadow-xs border" data-text="أرقام هواتف خط المساعدة المباشر لزمالة NA مصر:&#10;📞 01119565544&#10;📞 01221444490&#10;📞 01004445585">
                                📞 الهواتف
                            </button>
                            <button type="button" class="btn btn-light btn-xs rounded-pill text-nowrap py-1 px-2 quick-chip shadow-xs border" data-text="تأمل اليوم من كتاب فقط لليوم متاح على الرابط: https://naegypt.org/jft">
                                🌅 فقط لليوم
                            </button>
                            <button type="button" class="btn btn-light btn-xs rounded-pill text-nowrap py-1 px-2 quick-chip shadow-xs border" data-text="شكراً لتواصلك مع الزمالة. نأمل لك يوماً نظيفاً وموفقاً. ✨">
                                ✨ ختام
                            </button>
                        </div>

                        {{-- Input & Send Form --}}
                        @can('reply whatsapp messages')
                            <form id="replyForm" class="wa-composer-form">
                                @csrf
                                <div class="d-flex align-items-end gap-2">
                                    <textarea name="message" id="messageInput" rows="1" class="form-control rounded-4 border shadow-xs p-2 wa-auto-textarea" 
                                              placeholder="{{ __('messages.whatsapp_type_message') }}..." required></textarea>
                                    <button type="submit" id="sendBtn" class="btn btn-success rounded-circle shadow-xs flex-shrink-0 d-flex align-items-center justify-content-center wa-send-btn" title="{{ __('Send') }}">
                                        <i class="bi bi-send-fill fs-5" id="sendBtnIcon"></i>
                                    </button>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1 px-1">
                                    <span class="text-muted" style="font-size: 0.7rem;">
                                        <i class="bi bi-keyboard me-1"></i>{{ __('messages.whatsapp_enter_to_send') }}
                                    </span>
                                    <span class="text-muted" style="font-size: 0.7rem;">
                                        <i class="bi bi-slash-square me-1"></i>{{ __('messages.whatsapp_quick_canned_hint') }}
                                    </span>
                                </div>
                            </form>
                        @else
                            <div class="p-2 border rounded-3 bg-light text-center text-muted small">
                                <i class="bi bi-lock-fill me-1"></i>{{ __('You do not have permission to reply as a volunteer (reply whatsapp messages).') }}
                            </div>
                        @endcan
                    </footer>
                </div>

                {{-- Empty State (No conversation selected) --}}
                <div id="noChatSelectedWrapper" class="h-100 flex-column align-items-center justify-content-center text-center p-5 text-muted {{ $selectedConversation ? 'd-none' : 'd-flex' }}" style="background: #f8fafc;">
                    <div class="rounded-circle bg-white p-4 shadow-xs mb-3 border">
                        <i class="bi bi-chat-square-text-fill display-3 text-success opacity-75"></i>
                    </div>
                    <h5 class="fw-bold text-dark">{{ __('Select a Conversation') }}</h5>
                    <p class="small text-muted" style="max-width: 320px;">
                        {{ __('Choose a conversation from the list to inspect message history, take over as live agent, or respond.') }}
                    </p>
                </div>
            </main>

            {{-- 3. CONTACT INFO DRAWER (3rd Column Beside Chat) --}}
            <aside id="contactDrawer" class="wa-drawer-pane d-none flex-column">
                {{-- Drawer Header --}}
                <div class="wa-drawer-header p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i>{{ __('messages.whatsapp_contact_info') }}
                    </h6>
                    <button type="button" id="btnCloseDrawer" class="btn btn-sm btn-light rounded-circle p-1" title="{{ __('messages.whatsapp_close_drawer') }}">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                {{-- Drawer Scrollable Body --}}
                <div class="p-3 wa-scroll-viewport">
                    {{-- Contact Profile Card --}}
                    <div class="text-center mb-4">
                        <div id="drawerAvatar" class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs mx-auto mb-2"
                             style="width: 72px; height: 72px; background: hsl({{ $selectedHue }}, 65%, 45%); font-size: 1.8rem;">
                            <span id="drawerAvatarInitial">{{ $selectedConversation ? mb_substr($selectedConversation->name ?: ($selectedConversation->phone ?: 'NA'), 0, 1) : 'NA' }}</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-0" id="drawerContactName">
                            {{ $selectedConversation ? ($selectedConversation->name ?: $selectedConversation->phone) : '-' }}
                        </h6>
                        <span class="font-monospace text-muted small d-block mb-2" id="drawerContactPhone">
                            {{ $selectedConversation ? $selectedConversation->phone : '-' }}
                        </span>
                        
                        <button type="button" id="btnCopyPhone" class="btn btn-outline-secondary btn-xs rounded-pill px-3 shadow-xs">
                            <i class="bi bi-clipboard me-1"></i><span id="copyPhoneText">{{ __('messages.whatsapp_copy_phone') }}</span>
                        </button>
                    </div>

                    {{-- JFT Daily Broadcast Subscription Status Card --}}
                    <div class="card border rounded-3 mb-3 shadow-xs">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold small text-dark"><i class="bi bi-book-half text-primary me-1"></i>{{ __('اشتراك فقط لليوم (JFT)') }}</span>
                                <span id="drawerSubBadge">
                                    @if($selectedConversation && $selectedConversation->subscriber && $selectedConversation->subscriber->is_active)
                                        <span class="badge bg-success rounded-pill" style="font-size: 0.68rem;">{{ __('نشط') }}</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill" style="font-size: 0.68rem;">{{ __('غير نشط') }}</span>
                                    @endif
                                </span>
                            </div>
                            <p class="text-muted small mb-2" style="font-size: 0.75rem;" id="drawerSubDesc">
                                @if($selectedConversation && $selectedConversation->subscriber && $selectedConversation->subscriber->is_active)
                                    {{ __('messages.whatsapp_jft_subscribed') }}
                                @else
                                    {{ __('messages.whatsapp_jft_not_subscribed') }}
                                @endif
                            </p>
                            @can('reply whatsapp messages')
                                <button type="button" id="btnToggleSubscription" class="btn btn-outline-primary btn-xs w-100 rounded-pill">
                                    <i class="bi bi-arrow-repeat me-1"></i>{{ __('تبديل حالة الاشتراك') }}
                                </button>
                            @endcan
                        </div>
                    </div>

                    {{-- Volunteer Internal Notes --}}
                    <div class="card border rounded-3 mb-3 shadow-xs">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold small text-dark"><i class="bi bi-journal-text text-warning me-1"></i>{{ __('messages.whatsapp_notes') }}</span>
                                <small class="text-muted" style="font-size: 0.7rem;">{{ __('سري للمتطوعين') }}</small>
                            </div>
                            <textarea id="drawerNotesInput" class="form-control small mb-2 rounded-3 border" rows="4" 
                                      placeholder="{{ __('اكتب ملاحظات حول هذه الحالة...') }}">{{ $selectedConversation ? $selectedConversation->notes : '' }}</textarea>
                            @can('reply whatsapp messages')
                                <button type="button" id="btnSaveNotes" class="btn btn-dark btn-xs w-100 rounded-pill d-flex align-items-center justify-content-center gap-1">
                                    <i class="bi bi-check2-circle" id="saveNotesIcon"></i>
                                    <span>{{ __('حفظ الملاحظات') }}</span>
                                </button>
                            @endcan
                        </div>
                    </div>

                    {{-- Conversation Diagnostics --}}
                    <div class="p-2 rounded-3 bg-light border small text-muted font-monospace" style="font-size: 0.7rem;">
                        <div class="d-flex justify-content-between mb-1">
                            <span>ID:</span>
                            <span id="diagConvId">{{ $selectedConversation ? $selectedConversation->id : '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>JID:</span>
                            <span class="text-truncate" style="max-width: 160px;" id="diagConvJid">{{ $selectedConversation ? $selectedConversation->jid : '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Created:</span>
                            <span>{{ $selectedConversation && $selectedConversation->created_at ? $selectedConversation->created_at->format('Y-m-d') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        {{-- CANNED RESPONSES MODAL --}}
        <div class="modal fade" id="cannedModal" tabindex="-1" aria-labelledby="cannedModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header bg-light border-bottom p-3">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="cannedModalLabel">
                            <i class="bi bi-lightning-charge-fill text-warning"></i>
                            <span>{{ __('messages.whatsapp_quick_responses') }}</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white border"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="searchCanned" class="form-control border" placeholder="{{ __('ابحث في الردود السريعة...') }}">
                        </div>

                        <div class="row g-2" id="cannedList">
                            <div class="col-md-6 canned-card" data-title="ترحيب رسمي">
                                <div class="p-3 border rounded-3 h-100 bg-white shadow-xs cursor-pointer btn-select-canned" 
                                     data-text="أهلاً بك يا صديقي في خط مساعدة زمالة المدمنين المجهولين بمصر 🇪🇬 كيف يمكنني مساعدتك؟">
                                    <h6 class="fw-bold mb-1 text-primary">👋 ترحيب رسمي</h6>
                                    <p class="text-muted small mb-0 text-truncate">أهلاً بك يا صديقي في خط مساعدة زمالة المدمنين المجهولين بمصر...</p>
                                </div>
                            </div>

                            <div class="col-md-6 canned-card" data-title="جدول الاجتماعات">
                                <div class="p-3 border rounded-3 h-100 bg-white shadow-xs cursor-pointer btn-select-canned"
                                     data-text="يمكنك العثور على أقرب اجتماع تعافي لك في مصر وجدول المواعيد المحدث عبر الرابط:&#10;https://naegypt.org/meetings">
                                    <h6 class="fw-bold mb-1 text-primary">📍 جدول الاجتماعات</h6>
                                    <p class="text-muted small mb-0 text-truncate">يمكنك العثور على أقرب اجتماع تعافي لك في مصر...</p>
                                </div>
                            </div>

                            <div class="col-md-6 canned-card" data-title="خط المساعدة الهاتفي">
                                <div class="p-3 border rounded-3 h-100 bg-white shadow-xs cursor-pointer btn-select-canned"
                                     data-text="أرقام هواتف خط المساعدة المباشر لزمالة المدمنين المجهولين بمصر:&#10;📞 01119565544&#10;📞 01221444490&#10;📞 01004445585&#10;المتطوعون متاحون للإجابة على استفساراتك بسرية تامة.">
                                    <h6 class="fw-bold mb-1 text-primary">📞 أرقام خط المساعدة</h6>
                                    <p class="text-muted small mb-0 text-truncate">أرقام هواتف خط المساعدة المباشر لزمالة المدمنين المجهولين بمصر...</p>
                                </div>
                            </div>

                            <div class="col-md-6 canned-card" data-title="قراءة فقط لليوم">
                                <div class="p-3 border rounded-3 h-100 bg-white shadow-xs cursor-pointer btn-select-canned"
                                     data-text="تأمل اليوم من كتاب 'فقط لليوم' متاح يومياً عبر موقع الزمالة:&#10;https://naegypt.org/jft&#10;كما يمكنك الاشتراك في الرسائل اليومية عبر الواتساب.">
                                    <h6 class="fw-bold mb-1 text-primary">🌅 فقط لليوم</h6>
                                    <p class="text-muted small mb-0 text-truncate">تأمل اليوم من كتاب 'فقط لليوم' متاح يومياً عبر موقع الزمالة...</p>
                                </div>
                            </div>

                            <div class="col-md-6 canned-card" data-title="ختام تشجيعي">
                                <div class="p-3 border rounded-3 h-100 bg-white shadow-xs cursor-pointer btn-select-canned"
                                     data-text="شكراً لتواصلك معنا يا صديقي. تذكر أنك لست وحدك أبداً، والتعافي ممكن يوماً بيوم. نتمنى لك دوام الصحة والسلام الداخلي. ✨">
                                    <h6 class="fw-bold mb-1 text-primary">✨ ختام تشجيعي</h6>
                                    <p class="text-muted small mb-0 text-truncate">شكراً لتواصلك معنا يا صديقي. تذكر أنك لست وحدك أبداً...</p>
                                </div>
                            </div>

                            <div class="col-md-6 canned-card" data-title="تأكيد تاريخ التبطيل">
                                <div class="p-3 border rounded-3 h-100 bg-white shadow-xs cursor-pointer btn-select-canned"
                                     data-text="شكراً لتوضيحك يا صديقي! تم تدوين تاريخ تبطيلك بنجاح في سجلاتنا. نتمنى لك دوام التعافي والحرية يوماً بيوم. 💪">
                                    <h6 class="fw-bold mb-1 text-primary">🗓️ تأكيد تاريخ التبطيل</h6>
                                    <p class="text-muted small mb-0 text-truncate">شكراً لتوضيحك يا صديقي! تم تدوين تاريخ تبطيلك بنجاح...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCOPED CSS OVERRIDES: ISOLATES INBOX INTO A 100% LOCKED VIEWPORT SHELL --}}
    <style>
        /* 1. Global Viewport-Lock Rules (Directly under 70px admin navbar) */
        html:has(#whatsappInboxApp),
        body:has(#whatsappInboxApp) {
            overflow: hidden !important;
            height: 100vh !important;
            max-height: 100vh !important;
        }

        body:has(#whatsappInboxApp) .page-content {
            padding: 0 !important;
            margin-top: 70px !important;
            padding-top: 0 !important;
            height: calc(100vh - 70px) !important;
            max-height: calc(100vh - 70px) !important;
            overflow: hidden !important;
        }

        body:has(#whatsappInboxApp) .page-content > .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
            height: 100% !important;
            max-height: 100% !important;
            max-width: 100% !important;
        }

        .wa-inbox-root {
            height: 100% !important;
            max-height: 100% !important;
            width: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden !important;
            background: #ffffff !important;
        }

        /* 2. Main 3-Column Shell */
        .wa-shell {
            display: flex !important;
            flex-direction: row !important;
            height: 100% !important;
            max-height: 100% !important;
            width: 100% !important;
            background: #ffffff !important;
            position: relative !important;
            overflow: hidden !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }

        /* 3. Conversations Sidebar (Left / Right in RTL) */
        .wa-list-pane {
            width: 360px !important;
            min-width: 300px !important;
            max-width: 420px !important;
            height: 100% !important;
            min-height: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            border-inline-end: 1px solid #e9edef !important;
            background: #ffffff !important;
            flex-shrink: 0 !important;
            overflow: hidden !important;
        }

        .wa-list-header {
            flex-shrink: 0 !important;
            background: #f0f2f5 !important;
            border-bottom: 1px solid #e9edef !important;
            padding: 10px 14px !important;
            z-index: 10 !important;
        }

        .conversation-avatar {
            width: 42px !important;
            height: 42px !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 1.05rem !important;
            user-select: none !important;
        }

        .conversation-item {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 10px 14px !important;
            border-bottom: 1px solid #f0f2f5 !important;
            cursor: pointer !important;
            transition: background-color 0.15s ease !important;
            background: #ffffff !important;
            flex-shrink: 0 !important;
            width: 100% !important;
            text-decoration: none !important;
            color: inherit !important;
        }

        .conversation-item:hover {
            background-color: #f5f6f6 !important;
        }

        .conversation-item.active-chat {
            background-color: #eef2f6 !important;
            border-inline-start: 4px solid #00a884 !important;
        }

        .conversation-item .item-title {
            font-size: 0.92rem !important;
            max-width: 175px !important;
        }

        .conversation-item .item-time {
            font-size: 0.7rem !important;
        }

        .conversation-item .item-snippet {
            font-size: 0.78rem !important;
            max-width: 180px !important;
        }

        /* 4. Chat Thread Center Pane */
        .wa-chat-pane {
            flex: 1 1 0 !important;
            min-width: 0 !important;
            height: 100% !important;
            min-height: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            background-color: #efeae2 !important;
            background-image: radial-gradient(#d1d7db 0.8px, transparent 0.8px) !important;
            background-size: 20px 20px !important;
            position: relative !important;
            overflow: hidden !important;
        }

        #activeChatWrapper {
            display: flex !important;
            flex-direction: column !important;
            height: 100% !important;
            width: 100% !important;
            min-height: 0 !important;
            overflow: hidden !important;
        }

        .wa-chat-header {
            flex-shrink: 0 !important;
            height: 60px !important;
            min-height: 60px !important;
            max-height: 60px !important;
            background: #f0f2f5 !important;
            border-bottom: 1px solid #d1d7db !important;
            padding: 8px 16px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            z-index: 20 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
        }

        .wa-chat-footer {
            flex-shrink: 0 !important;
            background: #f0f2f5 !important;
            border-top: 1px solid #d1d7db !important;
            padding: 8px 16px !important;
            z-index: 20 !important;
            box-shadow: 0 -1px 2px rgba(0, 0, 0, 0.04) !important;
        }

        /* 5. Strict Independent Scroll Viewports */
        .wa-scroll-viewport {
            flex: 1 1 0 !important;
            min-height: 0 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            -webkit-overflow-scrolling: touch;
        }

        .wa-messages-viewport {
            flex: 1 1 0 !important;
            min-height: 0 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding: 16px 24px !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 4px !important;
            -webkit-overflow-scrolling: touch;
        }

        /* Custom Slim Scrollbars */
        .wa-scroll-viewport::-webkit-scrollbar,
        .wa-messages-viewport::-webkit-scrollbar {
            width: 6px;
        }

        .wa-scroll-viewport::-webkit-scrollbar-track,
        .wa-messages-viewport::-webkit-scrollbar-track {
            background: transparent;
        }

        .wa-scroll-viewport::-webkit-scrollbar-thumb,
        .wa-messages-viewport::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
        }

        /* 6. Message Rows & WhatsApp Web Bubbles */
        .message-row {
            display: flex !important;
            width: 100% !important;
            flex-shrink: 0 !important;
            margin-bottom: 4px !important;
            clear: both !important;
        }

        .message-row.incoming-row {
            justify-content: flex-start !important;
        }

        .message-row.outgoing-row {
            justify-content: flex-end !important;
        }

        .wa-bubble {
            position: relative !important;
            max-width: 75% !important;
            min-width: 130px !important;
            padding: 6px 10px 8px 10px !important;
            border-radius: 7.5px !important;
            box-shadow: 0 1px 0.5px rgba(11, 20, 26, 0.13) !important;
            border: none !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            word-break: break-word !important;
        }

        /* Incoming Bubbles */
        .wa-bubble-incoming {
            background: #ffffff !important;
            color: #111b21 !important;
            border-top-left-radius: 0px !important;
        }
        [dir="rtl"] .wa-bubble-incoming {
            border-top-left-radius: 7.5px !important;
            border-top-right-radius: 0px !important;
        }

        /* Outgoing Bubbles */
        .wa-bubble-outgoing {
            background: #d9fdd3 !important;
            color: #111b21 !important;
            border-top-right-radius: 0px !important;
        }
        [dir="rtl"] .wa-bubble-outgoing {
            border-top-right-radius: 7.5px !important;
            border-top-left-radius: 0px !important;
        }

        /* Bot Bubbles */
        .wa-bubble-bot {
            background: #e0f2fe !important;
            color: #0c4a6e !important;
            border: 1px solid #bae6fd !important;
        }

        .wa-bubble-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            gap: 12px !important;
            margin-bottom: 2px !important;
            font-size: 0.72rem !important;
        }

        .message-body-text {
            white-space: pre-wrap !important;
            font-size: 0.92rem !important;
            line-height: 1.45 !important;
        }

        .wa-bubble-meta {
            display: inline-flex !important;
            align-items: center !important;
            gap: 3px !important;
            float: inline-end !important;
            margin-inline-start: 12px !important;
            margin-top: 4px !important;
            font-size: 0.68rem !important;
            color: #667781 !important;
            user-select: none !important;
        }

        .wa-date-divider {
            text-align: center !important;
            margin: 12px 0 !important;
            width: 100% !important;
            flex-shrink: 0 !important;
        }

        .wa-date-pill {
            background: #ffffff !important;
            color: #54656f !important;
            border: 1px solid #e9edef !important;
            border-radius: 8px !important;
            padding: 5px 12px !important;
            font-size: 0.72rem !important;
            box-shadow: 0 1px 0.5px rgba(11, 20, 26, 0.13) !important;
        }

        /* 7. Composer and Auto-resizing Textarea */
        .wa-auto-textarea {
            resize: none !important;
            min-height: 40px !important;
            max-height: 120px !important;
            font-size: 0.92rem !important;
            line-height: 1.4 !important;
            background: #ffffff !important;
            border-color: #d1d7db !important;
        }

        .wa-send-btn {
            width: 42px !important;
            height: 42px !important;
            background-color: #00a884 !important;
            border: none !important;
        }
        .wa-send-btn:hover {
            background-color: #008f6f !important;
        }

        /* Floating Scroll to Bottom Button */
        .wa-scroll-down-btn {
            position: absolute !important;
            bottom: 85px !important;
            width: 42px !important;
            height: 42px !important;
            display: none;
            align-items: center !important;
            justify-content: center !important;
            z-index: 15 !important;
            border: 1px solid #d1d7db !important;
        }
        [dir="rtl"] .wa-scroll-down-btn {
            left: 24px !important;
        }
        [dir="ltr"] .wa-scroll-down-btn {
            right: 24px !important;
        }

        /* 8. Contact Info Drawer (3rd Column) */
        .wa-drawer-pane {
            width: 320px !important;
            height: 100% !important;
            min-height: 0 !important;
            border-inline-start: 1px solid #e9edef !important;
            background: #ffffff !important;
            flex-shrink: 0 !important;
            z-index: 25 !important;
            overflow: hidden !important;
        }

        .filter-pill {
            background: #f0f2f5;
            color: #54656f;
            border: 1px solid transparent;
            font-size: 0.72rem;
            transition: all 0.15s ease;
        }
        .filter-pill.active {
            background: #00a884 !important;
            color: #ffffff !important;
        }

        .btn-xs {
            padding: 0.2rem 0.5rem;
            font-size: 0.72rem;
        }

        /* Mobile Adjustments */
        @media (max-width: 767.98px) {
            .wa-list-pane {
                width: 100% !important;
                max-width: 100% !important;
            }
            .wa-drawer-pane {
                position: absolute !important;
                top: 0 !important;
                bottom: 0 !important;
                width: 300px !important;
                max-width: 85vw !important;
                box-shadow: 0 0 25px rgba(0, 0, 0, 0.25) !important;
            }
            [dir="rtl"] .wa-drawer-pane {
                left: 0 !important;
            }
            [dir="ltr"] .wa-drawer-pane {
                right: 0 !important;
            }
        }
    </style>

    {{-- DIRECT EMBEDDED JAVASCRIPT (GUARANTEED TO EXECUTE) --}}
    <script>
        (function() {
            const WA_CONFIG = {
                activeConvId: {{ $selectedConversation ? $selectedConversation->id : 'null' }},
                urls: {
                    messages: "{{ route('whatsapp.inbox.messages', ['conversation' => ':id']) }}",
                    send: "{{ route('whatsapp.inbox.send', ['conversation' => ':id']) }}",
                    toggleLiveAgent: "{{ route('whatsapp.inbox.toggle-live-agent', ['conversation' => ':id']) }}",
                    notes: "{{ route('whatsapp.inbox.notes', ['conversation' => ':id']) }}",
                    toggleSubscription: "{{ route('whatsapp.inbox.toggle-subscription', ['conversation' => ':id']) }}",
                    conversations: "{{ route('whatsapp.inbox.conversations') }}",
                    inbox: "{{ route('whatsapp.inbox') }}"
                },
                csrfToken: "{{ csrf_token() }}"
            };

            function initWhatsAppInbox() {
                let activeConvId = WA_CONFIG.activeConvId;
                let isPollingActive = false;
                let audioEnabled = true;
                let currentFilter = 'all';
                let currentPage = parseInt(document.getElementById('conversationsList')?.getAttribute('data-current-page') || '1');
                let lastPage = parseInt(document.getElementById('conversationsList')?.getAttribute('data-last-page') || '1');
                let isLoadingMore = false;
                const knownMsgIds = new Set();

                const conversationsList = document.getElementById('conversationsList');
                const messagesContainer = document.getElementById('messagesContainer');
                const btnScrollDown = document.getElementById('btnScrollDown');
                const unreadScrollBadge = document.getElementById('unreadScrollBadge');
                const messageInput = document.getElementById('messageInput');
                const replyForm = document.getElementById('replyForm');
                const sendBtn = document.getElementById('sendBtn');
                const sendBtnIcon = document.getElementById('sendBtnIcon');
                const contactDrawer = document.getElementById('contactDrawer');
                const searchInput = document.getElementById('searchConversations');
                const btnClearSearch = document.getElementById('btnClearSearch');
                const btnToggleLiveAgent = document.getElementById('btnToggleLiveAgent');
                const toggleLiveAgentText = document.getElementById('toggleLiveAgentText');
                const toggleLiveAgentIcon = document.getElementById('toggleLiveAgentIcon');
                const cannedModalEl = document.getElementById('cannedModal');
                const cannedModal = cannedModalEl && window.bootstrap ? new bootstrap.Modal(cannedModalEl) : null;
                const toastEl = document.getElementById('inboxToast');
                const toast = toastEl && window.bootstrap ? new bootstrap.Toast(toastEl, { delay: 2500 }) : null;
                const infiniteScrollLoader = document.getElementById('infiniteScrollLoader');

                // Web Audio Synthesis Chime
                function playIncomingChime() {
                    if (!audioEnabled) return;
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        const now = ctx.currentTime;
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();

                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(587.33, now);
                        osc.frequency.exponentialRampToValueAtTime(880.00, now + 0.12);

                        gain.gain.setValueAtTime(0, now);
                        gain.gain.linearRampToValueAtTime(0.25, now + 0.04);
                        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);

                        osc.connect(gain);
                        gain.connect(ctx.destination);

                        osc.start(now);
                        osc.stop(now + 0.36);
                    } catch (e) {}
                }

                // Audio Toggle
                const btnToggleAudio = document.getElementById('btnToggleAudio');
                const audioBellIcon = document.getElementById('audioBellIcon');
                if (btnToggleAudio) {
                    btnToggleAudio.addEventListener('click', function () {
                        audioEnabled = !audioEnabled;
                        if (audioEnabled) {
                            if (audioBellIcon) audioBellIcon.className = 'bi bi-bell-fill text-warning fs-6';
                            showToast('{{ __("تم تفعيل التنبيهات الصوتية") }}');
                            playIncomingChime();
                        } else {
                            if (audioBellIcon) audioBellIcon.className = 'bi bi-bell-slash text-muted fs-6';
                            showToast('{{ __("تم كتم التنبيهات الصوتية") }}');
                        }
                    });
                }

                function showToast(msg) {
                    const toastMsgEl = document.getElementById('inboxToastMessage');
                    if (toastMsgEl) toastMsgEl.querySelector('span').innerText = msg;
                    if (toast) toast.show();
                }

                // Auto-scroll chat thread to bottom
                function scrollToBottom(smooth = false) {
                    if (messagesContainer) {
                        messagesContainer.scrollTo({
                            top: messagesContainer.scrollHeight,
                            behavior: smooth ? 'smooth' : 'auto'
                        });
                    }
                }
                scrollToBottom();

                // Track scroll distance for floating button
                if (messagesContainer && btnScrollDown) {
                    messagesContainer.addEventListener('scroll', function () {
                        const distFromBottom = messagesContainer.scrollHeight - messagesContainer.scrollTop - messagesContainer.clientHeight;
                        if (distFromBottom > 150) {
                            btnScrollDown.style.display = 'flex';
                        } else {
                            btnScrollDown.style.display = 'none';
                            if (unreadScrollBadge) unreadScrollBadge.style.display = 'none';
                        }
                    });

                    btnScrollDown.addEventListener('click', function () {
                        scrollToBottom(true);
                    });
                }

                // Initialize known message IDs
                document.querySelectorAll('.message-row').forEach(el => {
                    const id = el.getAttribute('data-id');
                    if (id) knownMsgIds.add(String(id));
                });

                function escapeHtml(str) {
                    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                }

                // Create WhatsApp Web Authentic Message Node
                function createMessageNode(msg, contactName) {
                    const isIncoming = msg.direction === 'incoming';
                    const isBot = msg.sender_type === 'bot';
                    const timeStr = msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';

                    let headerBadge = '';
                    if (isIncoming) {
                        headerBadge = `<span class="fw-bold text-primary">${escapeHtml(contactName)}</span>`;
                    } else if (isBot) {
                        headerBadge = `<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill font-monospace" style="font-size: 0.65rem;"><i class="bi bi-robot"></i> الرد الآلي</span>`;
                    } else {
                        const userName = (msg.user && msg.user.name) ? msg.user.name : 'متطوع خط المساعدة';
                        headerBadge = `<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.65rem;"><i class="bi bi-person-fill"></i> ${escapeHtml(userName)}</span>`;
                    }

                    const bubbleClass = isIncoming ? 'wa-bubble-incoming' : (isBot ? 'wa-bubble-bot' : 'wa-bubble-outgoing');
                    const rowClass = isIncoming ? 'incoming-row' : 'outgoing-row';
                    const doubleCheck = !isIncoming ? '<i class="bi bi-check2-all text-primary wa-msg-check" title="Delivered"></i>' : '';

                    const div = document.createElement('div');
                    div.className = `message-row ${rowClass}`;
                    div.setAttribute('data-id', msg.id);

                    div.innerHTML = `
                        <div class="wa-bubble ${bubbleClass}">
                            <div class="wa-bubble-header">
                                ${headerBadge}
                                <button type="button" class="btn btn-link p-0 text-muted btn-copy-msg" data-body="${escapeHtml(msg.body || '')}" title="{{ __('messages.whatsapp_copy_message') }}">
                                    <i class="bi bi-clipboard" style="font-size: 0.72rem;"></i>
                                </button>
                            </div>
                            <div class="message-body-text">${escapeHtml(msg.body || '')}</div>
                            <div class="wa-bubble-meta">
                                <span class="wa-msg-time">${timeStr}</span>
                                ${doubleCheck}
                            </div>
                            ${msg.category ? `<div class="wa-msg-category-tag"><span class="badge bg-light text-secondary border rounded-pill" style="font-size: 0.62rem;">${escapeHtml(msg.category)}</span></div>` : ''}
                        </div>
                    `;
                    return div;
                }

                // Clipboard Copy
                document.addEventListener('click', function (e) {
                    const btn = e.target.closest('.btn-copy-msg');
                    if (btn) {
                        const text = btn.getAttribute('data-body') || '';
                        navigator.clipboard.writeText(text).then(() => {
                            showToast('{{ __("messages.whatsapp_text_copied") }}');
                        });
                    }
                });

                // Contact Info Drawer Toggle
                const btnToggleDrawer = document.getElementById('btnToggleDrawer');
                const btnCloseDrawer = document.getElementById('btnCloseDrawer');
                const headerAvatar = document.getElementById('headerAvatar');
                const headerTitleArea = document.getElementById('headerTitleArea');

                function toggleDrawer() {
                    if (!contactDrawer) return;
                    if (contactDrawer.classList.contains('d-none')) {
                        contactDrawer.classList.remove('d-none');
                        contactDrawer.classList.add('d-flex');
                    } else {
                        contactDrawer.classList.remove('d-flex');
                        contactDrawer.classList.add('d-none');
                    }
                }

                if (btnToggleDrawer) btnToggleDrawer.addEventListener('click', toggleDrawer);
                if (btnCloseDrawer) btnCloseDrawer.addEventListener('click', toggleDrawer);
                if (headerAvatar) headerAvatar.addEventListener('click', toggleDrawer);
                if (headerTitleArea) headerTitleArea.addEventListener('click', toggleDrawer);

                // Copy Phone Button in Drawer
                const btnCopyPhone = document.getElementById('btnCopyPhone');
                if (btnCopyPhone) {
                    btnCopyPhone.addEventListener('click', function () {
                        const phone = document.getElementById('drawerContactPhone')?.innerText || '';
                        if (phone && phone !== '-') {
                            navigator.clipboard.writeText(phone).then(() => {
                                showToast('{{ __("messages.whatsapp_phone_copied") }}');
                                const copyText = document.getElementById('copyPhoneText');
                                if (copyText) {
                                    copyText.innerText = '{{ __("messages.whatsapp_phone_copied") }}';
                                    setTimeout(() => copyText.innerText = '{{ __("messages.whatsapp_copy_phone") }}', 2000);
                                }
                            });
                        }
                    });
                }

                // Save Notes Button in Drawer
                const btnSaveNotes = document.getElementById('btnSaveNotes');
                const drawerNotesInput = document.getElementById('drawerNotesInput');
                if (btnSaveNotes && drawerNotesInput) {
                    btnSaveNotes.addEventListener('click', function () {
                        if (!activeConvId) return;
                        btnSaveNotes.disabled = true;
                        const icon = document.getElementById('saveNotesIcon');
                        if (icon) icon.className = 'spinner-border spinner-border-sm text-white';

                        const notesUrl = WA_CONFIG.urls.notes.replace(':id', activeConvId);
                        fetch(notesUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': WA_CONFIG.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ notes: drawerNotesInput.value })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.success) {
                                showToast('{{ __("messages.whatsapp_notes_saved") }}');
                            }
                        })
                        .catch(() => alert('Failed to save notes.'))
                        .finally(() => {
                            btnSaveNotes.disabled = false;
                            if (icon) icon.className = 'bi bi-check2-circle';
                        });
                    });
                }

                // Toggle Subscription Button in Drawer
                const btnToggleSubscription = document.getElementById('btnToggleSubscription');
                if (btnToggleSubscription) {
                    btnToggleSubscription.addEventListener('click', function () {
                        if (!activeConvId) return;
                        btnToggleSubscription.disabled = true;

                        const subUrl = WA_CONFIG.urls.toggleSubscription.replace(':id', activeConvId);
                        fetch(subUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': WA_CONFIG.csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.success) {
                                const badge = document.getElementById('drawerSubBadge');
                                const desc = document.getElementById('drawerSubDesc');
                                if (data.is_subscribed) {
                                    if (badge) badge.innerHTML = '<span class="badge bg-success rounded-pill" style="font-size: 0.68rem;">{{ __("نشط") }}</span>';
                                    if (desc) desc.innerText = '{{ __("messages.whatsapp_jft_subscribed") }}';
                                    showToast('{{ __("messages.whatsapp_jft_subscribed") }}');
                                } else {
                                    if (badge) badge.innerHTML = '<span class="badge bg-secondary rounded-pill" style="font-size: 0.68rem;">{{ __("غير نشط") }}</span>';
                                    if (desc) desc.innerText = '{{ __("messages.whatsapp_jft_not_subscribed") }}';
                                    showToast('{{ __("messages.whatsapp_jft_not_subscribed") }}');
                                }
                            }
                        })
                        .catch(() => alert('Failed to toggle subscription.'))
                        .finally(() => btnToggleSubscription.disabled = false);
                    });
                }

                // SWITCH CONVERSATION VIA CLIENT-SIDE AJAX (WITH FAILSAFE SSR FALLBACK)
                function loadConversation(convId) {
                    if (!convId) return;

                    // Immediately visually select the conversation
                    document.querySelectorAll('.conversation-item').forEach(el => {
                        if (el.getAttribute('data-id') === String(convId)) {
                            el.classList.add('active-chat');
                            const badge = el.querySelector('.unread-badge');
                            if (badge) {
                                badge.innerText = '0';
                                badge.classList.add('d-none');
                            }
                            el.setAttribute('data-unread', '0');
                        } else {
                            el.classList.remove('active-chat');
                        }
                    });

                    // Update browser history
                    const newUrl = new URL(window.location);
                    newUrl.searchParams.set('conversation_id', convId);
                    window.history.pushState({ conversation_id: convId }, '', newUrl);

                    // Show active chat wrapper
                    document.getElementById('activeChatWrapper')?.classList.remove('d-none');
                    document.getElementById('activeChatWrapper')?.classList.add('d-flex');
                    document.getElementById('noChatSelectedWrapper')?.classList.add('d-none');
                    document.getElementById('noChatSelectedWrapper')?.classList.remove('d-flex');

                    // Mobile view toggle
                    if (window.innerWidth < 768) {
                        document.getElementById('leftPane')?.classList.add('d-none');
                        document.getElementById('leftPane')?.classList.remove('d-flex');
                        document.getElementById('rightPane')?.classList.remove('d-none');
                        document.getElementById('rightPane')?.classList.add('d-flex');
                    }

                    // Show loader in message thread
                    if (messagesContainer) {
                        messagesContainer.innerHTML = '<div class="text-center p-5 text-muted m-auto"><div class="spinner-border text-success" role="status"></div></div>';
                    }

                    const messagesUrl = WA_CONFIG.urls.messages.replace(':id', convId);
                    fetch(messagesUrl, {
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        return res.json();
                    })
                    .then(data => {
                        if (!data || !data.conversation) throw new Error('Invalid conversation payload');
                        activeConvId = convId;
                        const conv = data.conversation;

                        // Update header fields
                        const contactName = conv.name || conv.phone || 'NA';
                        const headerNameEl = document.getElementById('headerContactName');
                        if (headerNameEl) headerNameEl.innerText = contactName;

                        const headerPhoneEl = document.getElementById('headerContactPhone');
                        if (headerPhoneEl) headerPhoneEl.innerHTML = `<i class="bi bi-phone"></i> ${conv.phone || ''}`;

                        const btnCall = document.getElementById('btnCallPhone');
                        if (btnCall) btnCall.setAttribute('href', `tel:${conv.phone || ''}`);

                        const initial = contactName.charAt(0).toUpperCase();
                        const hue = crc32(conv.phone || conv.jid || '') % 360;

                        const headerInitialEl = document.getElementById('headerAvatarInitial');
                        if (headerInitialEl) headerInitialEl.innerText = initial;

                        const headerAvatarEl = document.getElementById('headerAvatar');
                        if (headerAvatarEl) headerAvatarEl.style.background = `hsl(${hue}, 65%, 45%)`;

                        // Update Drawer fields
                        const drawerInitEl = document.getElementById('drawerAvatarInitial');
                        if (drawerInitEl) drawerInitEl.innerText = initial;

                        const drawerAvEl = document.getElementById('drawerAvatar');
                        if (drawerAvEl) drawerAvEl.style.background = `hsl(${hue}, 65%, 45%)`;

                        const drawerNameEl = document.getElementById('drawerContactName');
                        if (drawerNameEl) drawerNameEl.innerText = contactName;

                        const drawerPhoneEl = document.getElementById('drawerContactPhone');
                        if (drawerPhoneEl) drawerPhoneEl.innerText = conv.phone || '-';

                        const diagIdEl = document.getElementById('diagConvId');
                        if (diagIdEl) diagIdEl.innerText = conv.id;

                        const diagJidEl = document.getElementById('diagConvJid');
                        if (diagJidEl) diagJidEl.innerText = conv.jid;

                        if (drawerNotesInput) drawerNotesInput.value = conv.notes || '';

                        // Subscription state
                        const isSub = conv.subscriber && conv.subscriber.is_active;
                        const subBadge = document.getElementById('drawerSubBadge');
                        const subDesc = document.getElementById('drawerSubDesc');
                        if (subBadge) subBadge.innerHTML = `<span class="badge ${isSub ? 'bg-success' : 'bg-secondary'} rounded-pill" style="font-size: 0.68rem;">${isSub ? '{{ __("نشط") }}' : '{{ __("غير نشط") }}'}</span>`;
                        if (subDesc) subDesc.innerText = isSub ? '{{ __("messages.whatsapp_jft_subscribed") }}' : '{{ __("messages.whatsapp_jft_not_subscribed") }}';

                        // Live Agent header button state
                        updateLiveAgentState(data.is_live_agent, data.live_agent_until_formatted);

                        // Render Messages
                        knownMsgIds.clear();
                        if (messagesContainer) messagesContainer.innerHTML = '';

                        let lastDateStr = null;
                        if (Array.isArray(data.messages) && data.messages.length > 0) {
                            data.messages.forEach(msg => {
                                knownMsgIds.add(String(msg.id));

                                if (msg.created_at) {
                                    const d = new Date(msg.created_at);
                                    const dateKey = d.toISOString().split('T')[0];
                                    if (dateKey !== lastDateStr) {
                                        lastDateStr = dateKey;
                                        const sep = document.createElement('div');
                                        sep.className = 'wa-date-divider';
                                        sep.innerHTML = `<span class="badge wa-date-pill">${d.toLocaleDateString([], { month: 'short', day: 'numeric', year: 'numeric' })}</span>`;
                                        messagesContainer.appendChild(sep);
                                    }
                                }

                                const node = createMessageNode(msg, contactName);
                                messagesContainer.appendChild(node);
                            });
                        } else {
                            messagesContainer.innerHTML = '<div class="text-center p-5 text-muted empty-state-box m-auto"><i class="bi bi-chat-dots display-4 mb-2 d-block text-secondary opacity-50"></i><p class="small">{{ __("No message records found in this conversation.") }}</p></div>';
                        }

                        scrollToBottom();
                    })
                    .catch(err => {
                        console.warn('AJAX load encountered an error, falling back to seamless navigation:', err);
                        // Failsafe SSR fallback
                        window.location.href = `${WA_CONFIG.urls.inbox}?conversation_id=${convId}`;
                    });
                }

                // CRC32 Helper
                function crc32(str) {
                    let crc = 0 ^ (-1);
                    for (let i = 0; i < str.length; i++) {
                        crc = (crc >>> 8) ^ table[(crc ^ str.charCodeAt(i)) & 0xFF];
                    }
                    return Math.abs((crc ^ (-1)) >>> 0);
                }
                const table = (function () {
                    let c, t = [];
                    for (let n = 0; n < 256; n++) {
                        c = n;
                        for (let k = 0; k < 8; k++) {
                            c = ((c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1));
                        }
                        t[n] = c;
                    }
                    return t;
                })();

                // Mobile Back Button
                const btnMobileBack = document.getElementById('btnMobileBack');
                if (btnMobileBack) {
                    btnMobileBack.addEventListener('click', function () {
                        document.getElementById('rightPane')?.classList.add('d-none');
                        document.getElementById('rightPane')?.classList.remove('d-flex');
                        document.getElementById('leftPane')?.classList.remove('d-none');
                        document.getElementById('leftPane')?.classList.add('d-flex');
                    });
                }

                // Click delegation on conversation list items (Seamless AJAX with Failsafe)
                document.addEventListener('click', function (e) {
                    const item = e.target.closest('.conversation-item');
                    if (item) {
                        if (e.button === 0 && !e.ctrlKey && !e.metaKey && !e.shiftKey) {
                            e.preventDefault();
                            const id = item.getAttribute('data-id');
                            if (id) {
                                loadConversation(id);
                            }
                        }
                    }
                });

                // Auto-resizing Textarea with Enter-to-send
                if (messageInput) {
                    messageInput.addEventListener('input', function () {
                        this.style.height = 'auto';
                        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                    });

                    messageInput.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            if (this.value.trim() !== '') {
                                replyForm.dispatchEvent(new Event('submit', { cancelable: true }));
                            }
                        } else if (e.key === '/' && this.value === '') {
                            e.preventDefault();
                            if (cannedModal) cannedModal.show();
                        }
                    });
                }

                // Quick Chips insertion
                document.querySelectorAll('.quick-chip').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const text = this.getAttribute('data-text');
                        if (messageInput && text) {
                            messageInput.value = text;
                            messageInput.dispatchEvent(new Event('input'));
                            messageInput.focus();
                        }
                    });
                });

                // Canned Modal Cards selection
                document.querySelectorAll('.btn-select-canned').forEach(el => {
                    el.addEventListener('click', function () {
                        const text = this.getAttribute('data-text');
                        if (messageInput && text) {
                            messageInput.value = text;
                            messageInput.dispatchEvent(new Event('input'));
                            if (cannedModal) cannedModal.hide();
                            messageInput.focus();
                        }
                    });
                });

                // Live filter inside Canned Modal
                const searchCanned = document.getElementById('searchCanned');
                if (searchCanned) {
                    searchCanned.addEventListener('input', function () {
                        const query = this.value.toLowerCase().trim();
                        document.querySelectorAll('.canned-card').forEach(card => {
                            const title = card.getAttribute('data-title')?.toLowerCase() || '';
                            const text = card.querySelector('.btn-select-canned')?.getAttribute('data-text')?.toLowerCase() || '';
                            if (title.includes(query) || text.includes(query)) {
                                card.style.display = 'block';
                            } else {
                                card.style.display = 'none';
                            }
                        });
                    });
                }

                // AJAX Send Reply Form
                if (replyForm) {
                    replyForm.addEventListener('submit', function (e) {
                        e.preventDefault();
                        if (!activeConvId) {
                            alert('{{ __("الرجاء اختيار محادثة أولاً.") }}');
                            return;
                        }
                        const text = messageInput.value.trim();
                        if (!text) return;

                        sendBtn.disabled = true;
                        sendBtnIcon.className = 'spinner-border spinner-border-sm text-white';

                        const sendUrl = WA_CONFIG.urls.send.replace(':id', activeConvId);
                        fetch(sendUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': WA_CONFIG.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ message: text })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.success && data.message) {
                                messageInput.value = '';
                                messageInput.style.height = 'auto';

                                // Append outgoing bubble
                                knownMsgIds.add(String(data.message.id));
                                const contactName = document.getElementById('headerContactName')?.innerText || '';
                                const node = createMessageNode(data.message, contactName);
                                messagesContainer.appendChild(node);
                                scrollToBottom(true);

                                // Update live agent state in header
                                updateLiveAgentState(true);

                                // Update snippet in left conversation list
                                updateConversationSnippet(activeConvId, text, 'outgoing');
                            } else {
                                alert('Failed to send message.');
                            }
                        })
                        .catch(err => {
                            console.error('Error sending message:', err);
                            alert('Error communicating with WhatsApp server.');
                        })
                        .finally(() => {
                            sendBtn.disabled = false;
                            sendBtnIcon.className = 'bi bi-send-fill fs-5';
                            messageInput.focus();
                        });
                    });
                }

                // Toggle Live Agent Takeover
                if (btnToggleLiveAgent) {
                    btnToggleLiveAgent.addEventListener('click', function () {
                        if (!activeConvId) return;
                        btnToggleLiveAgent.disabled = true;

                        const toggleUrl = WA_CONFIG.urls.toggleLiveAgent.replace(':id', activeConvId);
                        fetch(toggleUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': WA_CONFIG.csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.success) {
                                updateLiveAgentState(data.is_live_agent, data.live_agent_until_formatted);
                                showToast(data.is_live_agent ? '{{ __("messages.whatsapp_pause_bot") }}' : '{{ __("messages.whatsapp_resume_bot") }}');

                                // Update list badge
                                const item = document.querySelector(`.conversation-item[data-id="${activeConvId}"]`);
                                if (item) {
                                    item.setAttribute('data-is-live', data.is_live_agent ? '1' : '0');
                                    const badgeContainer = item.querySelector('.live-badge, .bot-badge')?.parentElement;
                                    if (badgeContainer) {
                                        if (data.is_live_agent) {
                                            badgeContainer.innerHTML = `
                                                <span class="badge bg-warning text-dark rounded-pill py-1 px-2 live-badge" style="font-size: 0.65rem;" title="{{ __('messages.whatsapp_filter_live') }}">
                                                    <i class="bi bi-person-fill"></i>
                                                </span>
                                            `;
                                        } else {
                                            badgeContainer.innerHTML = `
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-1 px-2 bot-badge" style="font-size: 0.65rem;" title="{{ __('messages.whatsapp_filter_bot') }}">
                                                    <i class="bi bi-robot"></i>
                                                </span>
                                            `;
                                        }
                                    }
                                }
                            }
                        })
                        .catch(err => console.error('Error toggling live agent:', err))
                        .finally(() => btnToggleLiveAgent.disabled = false);
                    });
                }

                function updateLiveAgentState(isLive, formattedUntil = '') {
                    const liveStatusBadge = document.getElementById('liveAgentStatusBadge');
                    if (isLive) {
                        btnToggleLiveAgent.className = 'btn btn-success btn-sm rounded-pill px-3 shadow-xs d-flex align-items-center gap-1';
                        toggleLiveAgentIcon.className = 'bi bi-robot';
                        toggleLiveAgentText.innerText = '{{ __("messages.whatsapp_resume_bot") }}';
                        if (liveStatusBadge) {
                            liveStatusBadge.innerHTML = `<span class="text-warning fw-semibold" style="font-size: 0.74rem;">• {{ __('messages.whatsapp_live_agent') }} ${formattedUntil ? '({{ __("حتى") }} ' + formattedUntil + ')' : ''}</span>`;
                        }
                    } else {
                        btnToggleLiveAgent.className = 'btn btn-warning btn-sm rounded-pill px-3 shadow-xs d-flex align-items-center gap-1';
                        toggleLiveAgentIcon.className = 'bi bi-person-fill-gear';
                        toggleLiveAgentText.innerText = '{{ __("messages.whatsapp_pause_bot") }}';
                        if (liveStatusBadge) {
                            liveStatusBadge.innerHTML = `<span class="text-success fw-semibold" style="font-size: 0.74rem;">• {{ __('messages.whatsapp_bot_active') }}</span>`;
                        }
                    }
                }

                function updateConversationSnippet(convId, text, direction) {
                    const item = document.querySelector(`.conversation-item[data-id="${convId}"]`);
                    if (item) {
                        const snippet = item.querySelector('.item-snippet');
                        if (snippet) {
                            const prefix = direction === 'outgoing' ? '<i class="bi bi-check2-all text-primary me-1"></i>' : '';
                            snippet.innerHTML = `${prefix}${escapeHtml(text.substring(0, 40))}`;
                        }
                        const timeEl = item.querySelector('.item-time');
                        if (timeEl) timeEl.innerText = '{{ __("الآن") }}';

                        // Move to top of conversations list
                        if (conversationsList && item !== conversationsList.firstElementChild) {
                            conversationsList.prepend(item);
                        }
                    }
                }

                // Real-Time Polling for Current Conversation Messages
                function pollMessages() {
                    if (!activeConvId || isPollingActive) return;
                    isPollingActive = true;

                    const pollUrl = WA_CONFIG.urls.messages.replace(':id', activeConvId);
                    fetch(pollUrl, {
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Poll HTTP ' + res.status);
                        return res.json();
                    })
                    .then(data => {
                        if (data && Array.isArray(data.messages)) {
                            let hasNew = false;
                            const contactName = document.getElementById('headerContactName')?.innerText || '';

                            data.messages.forEach(msg => {
                                if (!knownMsgIds.has(String(msg.id))) {
                                    knownMsgIds.add(String(msg.id));
                                    hasNew = true;

                                    const node = createMessageNode(msg, contactName);
                                    messagesContainer.appendChild(node);

                                    if (msg.direction === 'incoming') {
                                        playIncomingChime();
                                        updateConversationSnippet(activeConvId, msg.body, 'incoming');
                                    }
                                }
                            });

                            if (hasNew) {
                                const distFromBottom = messagesContainer.scrollHeight - messagesContainer.scrollTop - messagesContainer.clientHeight;
                                if (distFromBottom < 150) {
                                    scrollToBottom(true);
                                } else if (unreadScrollBadge) {
                                    unreadScrollBadge.style.display = 'block';
                                }
                            }
                        }
                    })
                    .catch(() => {})
                    .finally(() => isPollingActive = false);
                }

                // Polling interval: every 4 seconds
                setInterval(pollMessages, 4000);

                // Filter Pills in List
                document.querySelectorAll('.filter-pill').forEach(btn => {
                    btn.addEventListener('click', function () {
                        document.querySelectorAll('.filter-pill').forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        currentFilter = this.getAttribute('data-filter') || 'all';
                        currentPage = 1;
                        fetchConversations(true);
                    });
                });

                // Search with debounce
                let searchTimeout = null;
                if (searchInput) {
                    searchInput.addEventListener('input', function () {
                        const q = this.value.trim();
                        if (btnClearSearch) btnClearSearch.style.display = q ? 'block' : 'none';

                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => {
                            currentPage = 1;
                            fetchConversations(true);
                        }, 350);
                    });
                }

                if (btnClearSearch) {
                    btnClearSearch.addEventListener('click', function () {
                        if (searchInput) {
                            searchInput.value = '';
                            this.style.display = 'none';
                            currentPage = 1;
                            fetchConversations(true);
                        }
                    });
                }

                // Infinite Scroll in Conversation Sidebar
                if (conversationsList) {
                    conversationsList.addEventListener('scroll', function () {
                        if (isLoadingMore || currentPage >= lastPage) return;
                        const distFromBottom = this.scrollHeight - this.scrollTop - this.clientHeight;
                        if (distFromBottom < 80) {
                            currentPage++;
                            fetchConversations(false);
                        }
                    });
                }

                // Fetch conversations via JSON
                function fetchConversations(reset = false) {
                    if (isLoadingMore) return;
                    isLoadingMore = true;
                    if (infiniteScrollLoader) infiniteScrollLoader.classList.remove('d-none');

                    const search = searchInput ? searchInput.value.trim() : '';
                    const url = `${WA_CONFIG.urls.conversations}?filter=${encodeURIComponent(currentFilter)}&search=${encodeURIComponent(search)}&page=${currentPage}`;

                    fetch(url, { headers: { 'Accept': 'application/json' } })
                    .then(res => {
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        return res.json();
                    })
                    .then(data => {
                        if (!data) return;
                        lastPage = data.last_page || 1;

                        const totalBadge = document.getElementById('totalConvsCount');
                        if (totalBadge && data.total !== undefined) {
                            totalBadge.innerText = `${data.total} {{ __('محادثة') }}`;
                        }

                        if (reset) {
                            conversationsList.innerHTML = '';
                        }

                        if (Array.isArray(data.conversations) && data.conversations.length > 0) {
                            data.conversations.forEach(conv => {
                                if (conversationsList.querySelector(`.conversation-item[data-id="${conv.id}"]`)) return;

                                const item = renderConversationItem(conv);
                                conversationsList.appendChild(item);
                            });
                        } else if (reset) {
                            conversationsList.innerHTML = `
                                <div class="text-center p-5 text-muted" id="emptyConversationsNotice">
                                    <i class="bi bi-chat-square-dots display-4 mb-2 d-block text-secondary opacity-50"></i>
                                    <p class="small mb-0">{{ __('No active conversations yet.') }}</p>
                                </div>
                            `;
                        }
                    })
                    .catch(err => console.error('Error fetching conversations:', err))
                    .finally(() => {
                        isLoadingMore = false;
                        if (infiniteScrollLoader) infiniteScrollLoader.classList.add('d-none');
                    });
                }

                function renderConversationItem(conv) {
                    const isSelected = activeConvId && String(activeConvId) === String(conv.id);
                    const isLive = conv.is_live_agent_mode && conv.live_agent_until && new Date(conv.live_agent_until) > new Date();
                    const unreadCount = conv.unread_count || 0;
                    const hue = crc32(conv.phone || conv.jid || '') % 360;
                    const latestMsg = conv.latest_message;
                    const contactName = conv.name || conv.phone || 'NA';
                    const initial = contactName.charAt(0).toUpperCase();

                    let snippetText = conv.phone || '';
                    let checkIcon = '';
                    if (latestMsg) {
                        if (latestMsg.direction === 'outgoing') checkIcon = '<i class="bi bi-check2-all text-primary me-1"></i>';
                        snippetText = (latestMsg.body || '').substring(0, 40);
                    }

                    const a = document.createElement('a');
                    a.href = `${WA_CONFIG.urls.inbox}?conversation_id=${conv.id}`;
                    a.className = `conversation-item ${isSelected ? 'active-chat' : ''}`;
                    a.setAttribute('data-id', conv.id);
                    a.setAttribute('data-phone', conv.phone || '');
                    a.setAttribute('data-name', conv.name || '');
                    a.setAttribute('data-jid', conv.jid || '');
                    a.setAttribute('data-is-live', isLive ? '1' : '0');
                    a.setAttribute('data-unread', unreadCount);

                    a.innerHTML = `
                        <div class="conversation-avatar flex-shrink-0" style="background: hsl(${hue}, 65%, 45%);">
                            ${initial}
                        </div>
                        <div class="conversation-info flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between align-items-baseline mb-1">
                                <span class="fw-bold text-truncate text-dark item-title">
                                    ${escapeHtml(contactName)}
                                </span>
                                <small class="text-muted item-time flex-shrink-0">
                                    ${conv.last_interaction_at ? new Date(conv.last_interaction_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : ''}
                                </small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <p class="text-muted small text-truncate mb-0 item-snippet">
                                    ${checkIcon}${escapeHtml(snippetText)}
                                </p>
                                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                    <span class="badge bg-success rounded-pill font-monospace unread-badge ${unreadCount > 0 ? '' : 'd-none'}" style="font-size: 0.65rem;">
                                        ${unreadCount}
                                    </span>
                                    ${isLive ? `
                                        <span class="badge bg-warning text-dark rounded-pill py-1 px-2 live-badge" style="font-size: 0.65rem;" title="{{ __('messages.whatsapp_filter_live') }}">
                                            <i class="bi bi-person-fill"></i>
                                        </span>
                                    ` : `
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-1 px-2 bot-badge" style="font-size: 0.65rem;" title="{{ __('messages.whatsapp_filter_bot') }}">
                                            <i class="bi bi-robot"></i>
                                        </span>
                                    `}
                                </div>
                            </div>
                        </div>
                    `;
                    return a;
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initWhatsAppInbox);
            } else {
                initWhatsAppInbox();
            }
        })();
    </script>
</x-layout>
