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
        $conversations = WhatsAppConversation::with(['lastAssignedUser'])
            ->withCount(['messages as unread_count' => function ($q) {
                $q->where('direction', 'incoming')->where('status', 'received');
            }])
            ->orderBy('last_interaction_at', 'desc')
            ->paginate(25);

        $selectedId = $request->query('conversation_id');
        $selectedConversation = null;
        $messages = collect();

        if ($selectedId) {
            $selectedConversation = WhatsAppConversation::with(['lastAssignedUser'])->find($selectedId);
        } elseif ($conversations->isNotEmpty()) {
            $selectedConversation = $conversations->first();
        }

        if ($selectedConversation) {
            $messages = $selectedConversation->messages()
                ->with('user')
                ->orderBy('created_at', 'asc')
                ->take(100)
                ->get();
        }

        $isDev = config('whatsapp.dev_mode', false) || (request()->getHost() === 'egyptna.org' || str_ends_with(request()->getHost(), '.egyptna.org'));

        return view('whatsapp.inbox', compact('conversations', 'selectedConversation', 'messages', 'isDev'));
    }

    /**
     * Fetch conversation messages via JSON.
     */
    public function messages(WhatsAppConversation $conversation): JsonResponse
    {
        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->take(150)
            ->get();

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages,
            'is_live_agent' => $conversation->isLiveAgentActive(),
            'live_agent_until' => $conversation->live_agent_until ? $conversation->live_agent_until->toIso8601String() : null,
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
            ]);
        }

        return redirect()->route('whatsapp.inbox', ['conversation_id' => $conversation->id]);
    }
}
