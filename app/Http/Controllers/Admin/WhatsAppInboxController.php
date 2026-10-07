<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\WhatsApp\WhatsAppClient;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppInboxController extends Controller
{
    /**
     * Display the WhatsApp Live Chat Inbox.
     */
    public function index(Request $request): View
    {
        $query = WhatsAppConversation::with(['lastAssignedUser', 'latestMessage', 'subscriber'])
            ->withCount(['messages as unread_count' => function ($q) {
                $q->where('direction', 'incoming')->where('status', 'received');
            }]);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('jid', 'like', "%{$search}%");
            });
        }

        $filter = $request->input('filter', 'all');
        if ($filter === 'live') {
            $query->where('is_live_agent_mode', true)
                  ->where('live_agent_until', '>', Carbon::now());
        } elseif ($filter === 'bot') {
            $query->where(function ($q) {
                $q->where('is_live_agent_mode', false)
                  ->orWhereNull('live_agent_until')
                  ->orWhere('live_agent_until', '<=', Carbon::now());
            });
        } elseif ($filter === 'unread') {
            $query->has('messages', '>=', 1, 'and', function ($q) {
                $q->where('direction', 'incoming')->where('status', 'received');
            });
        }

        $conversations = $query->orderBy('last_interaction_at', 'desc')->paginate(30);

        $selectedId = $request->query('conversation_id');
        $selectedConversation = null;
        $messages = collect();

        if ($selectedId) {
            $selectedConversation = WhatsAppConversation::with(['lastAssignedUser', 'subscriber'])->find($selectedId);
        } elseif ($conversations->isNotEmpty() && !$request->has('conversation_id')) {
            $selectedConversation = $conversations->first();
        }

        if ($selectedConversation) {
            // Automatically mark unread incoming messages as read upon viewing
            $selectedConversation->messages()
                ->where('direction', 'incoming')
                ->where('status', 'received')
                ->update(['status' => 'read']);

            $messages = $selectedConversation->messages()
                ->with('user')
                ->orderBy('created_at', 'asc')
                ->take(150)
                ->get();
        }

        $isDev = config('whatsapp.dev_mode', false) || (request()->getHost() === 'egyptna.org' || str_ends_with(request()->getHost(), '.egyptna.org'));

        return view('whatsapp.inbox', compact('conversations', 'selectedConversation', 'messages', 'isDev'));
    }

    /**
     * Return JSON list of conversations for real-time list synchronization and search.
     */
    public function conversations(Request $request): JsonResponse
    {
        $query = WhatsAppConversation::with(['lastAssignedUser', 'latestMessage', 'subscriber'])
            ->withCount(['messages as unread_count' => function ($q) {
                $q->where('direction', 'incoming')->where('status', 'received');
            }]);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('jid', 'like', "%{$search}%");
            });
        }

        $filter = $request->input('filter', 'all');
        if ($filter === 'live') {
            $query->where('is_live_agent_mode', true)
                  ->where('live_agent_until', '>', Carbon::now());
        } elseif ($filter === 'bot') {
            $query->where(function ($q) {
                $q->where('is_live_agent_mode', false)
                  ->orWhereNull('live_agent_until')
                  ->orWhere('live_agent_until', '<=', Carbon::now());
            });
        } elseif ($filter === 'unread') {
            $query->has('messages', '>=', 1, 'and', function ($q) {
                $q->where('direction', 'incoming')->where('status', 'received');
            });
        }

        $conversations = $query->orderBy('last_interaction_at', 'desc')->paginate(30);

        return response()->json([
            'conversations' => $conversations->items(),
            'total' => $conversations->total(),
            'current_page' => $conversations->currentPage(),
            'last_page' => $conversations->lastPage(),
        ]);
    }

    /**
     * Fetch conversation messages via JSON.
     */
    public function messages(WhatsAppConversation $conversation): JsonResponse
    {
        // Mark unread incoming messages as read upon fetching
        $conversation->messages()
            ->where('direction', 'incoming')
            ->where('status', 'received')
            ->update(['status' => 'read']);

        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->take(150)
            ->get();

        return response()->json([
            'conversation' => $conversation->load(['lastAssignedUser', 'subscriber']),
            'messages' => $messages,
            'is_live_agent' => $conversation->isLiveAgentActive(),
            'live_agent_until' => $conversation->live_agent_until ? $conversation->live_agent_until->toIso8601String() : null,
            'live_agent_until_formatted' => $conversation->live_agent_until ? $conversation->live_agent_until->format('H:i') : null,
        ]);
    }

    /**
     * Mark all incoming messages in a conversation as read.
     */
    public function markRead(WhatsAppConversation $conversation): JsonResponse
    {
        $updated = $conversation->messages()
            ->where('direction', 'incoming')
            ->where('status', 'received')
            ->update(['status' => 'read']);

        return response()->json(['success' => true, 'updated' => $updated]);
    }

    /**
     * Update internal notes for a conversation.
     */
    public function updateNotes(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        abort_unless(auth()->user()->can('reply whatsapp messages'), 403);

        $validated = $request->validate([
            'notes' => 'nullable|string|max:5000',
        ]);

        $conversation->update(['notes' => $validated['notes'] ?? null]);

        return response()->json([
            'success' => true,
            'notes' => $conversation->notes,
            'message' => __('messages.whatsapp_notes_saved'),
        ]);
    }

    /**
     * Toggle JFT broadcast subscription for this contact.
     */
    public function toggleSubscription(WhatsAppConversation $conversation): JsonResponse
    {
        abort_unless(auth()->user()->can('reply whatsapp messages'), 403);

        $subscriber = \App\Models\WhatsAppSubscriber::where('jid', $conversation->jid)
            ->orWhere('phone', $conversation->phone)
            ->first();

        if ($subscriber && $subscriber->is_active) {
            $subscriber->update([
                'is_active' => false,
                'unsubscribed_at' => Carbon::now(),
            ]);
            $isSubscribed = false;
        } else {
            \App\Models\WhatsAppSubscriber::updateOrCreate(
                ['jid' => $conversation->jid],
                [
                    'phone' => $conversation->phone,
                    'name' => $conversation->name,
                    'channel' => 'jft',
                    'is_active' => true,
                    'subscribed_at' => Carbon::now(),
                    'unsubscribed_at' => null,
                ]
            );
            $isSubscribed = true;
        }

        return response()->json([
            'success' => true,
            'is_subscribed' => $isSubscribed,
        ]);
    }

    /**
     * Send manual reply as volunteer.
     */
    public function send(Request $request, WhatsAppConversation $conversation, WhatsAppClient $client): JsonResponse|RedirectResponse
    {
        // Enforce volunteer reply permission
        abort_unless(auth()->user()->can('reply whatsapp messages'), 403);

        $validated = $request->validate([
            'message' => 'required|string|max:4000',
        ]);

        $body = trim($validated['message']);

        // Send via WhatsApp microservice
        $response = $client->sendTextMessage($conversation->jid, $body);

        // Record outgoing message
        $msg = WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'message_id' => $response['data']['results']['message_id'] ?? null,
            'direction' => 'outgoing',
            'sender_type' => 'agent',
            'category' => 'volunteer_reply',
            'message_type' => 'text',
            'body' => $body,
            'status' => ($response['success'] ?? false) ? 'sent' : 'failed',
            'user_id' => auth()->id(),
            'raw_payload' => $response,
        ]);

        // Automatically activate Live Agent mode to suppress the bot for 30 minutes
        $conversation->enableLiveAgent(null, auth()->id());
        $conversation->update(['last_interaction_at' => Carbon::now()]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg->load('user'),
                'conversation' => $conversation,
            ]);
        }

        return redirect()->route('whatsapp.inbox', ['conversation_id' => $conversation->id]);
    }

    /**
     * Toggle Live Agent takeover state.
     */
    public function toggleLiveAgent(WhatsAppConversation $conversation): RedirectResponse|JsonResponse
    {
        abort_unless(auth()->user()->can('reply whatsapp messages'), 403);

        if ($conversation->isLiveAgentActive()) {
            $conversation->disableLiveAgent();
            $status = 'bot_resumed';
        } else {
            $conversation->enableLiveAgent(null, auth()->id());
            $status = 'live_agent_enabled';
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $status,
                'is_live_agent' => $conversation->isLiveAgentActive(),
                'live_agent_until' => $conversation->live_agent_until ? $conversation->live_agent_until->toIso8601String() : null,
                'live_agent_until_formatted' => $conversation->live_agent_until ? $conversation->live_agent_until->format('H:i') : null,
            ]);
        }

        return redirect()->route('whatsapp.inbox', ['conversation_id' => $conversation->id]);
    }
}
