<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsAppBroadcast;
use App\Models\WhatsAppBroadcastLog;
use App\Models\WhatsAppSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppSubscriberController extends Controller
{
    /**
     * Display broadcast subscribers and transmission history.
     */
    public function index(Request $request): View
    {
        $query = WhatsAppSubscriber::query();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('phone', 'like', "%{$s}%")
                    ->orWhere('name', 'like', "%{$s}%")
                    ->orWhere('jid', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $subscribers = $query->orderBy('id', 'desc')->paginate(25);

        $broadcastLogs = WhatsAppBroadcastLog::with('dispatcher')
            ->orderBy('id', 'desc')
            ->take(15)
            ->get();

        $stats = [
            'total' => WhatsAppSubscriber::count(),
            'active' => WhatsAppSubscriber::where('is_active', true)->count(),
            'inactive' => WhatsAppSubscriber::where('is_active', false)->count(),
        ];

        $isDev = config('whatsapp.dev_mode', false) || (request()->getHost() === 'egyptna.org' || str_ends_with(request()->getHost(), '.egyptna.org'));

        return view('whatsapp.subscribers', compact('subscribers', 'broadcastLogs', 'stats', 'isDev'));
    }

    /**
     * Dispatch manual broadcast to subscribers.
     */
    public function triggerBroadcast(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'channel' => 'required|string',
            'title' => 'required|string|max:150',
            'message' => 'required|string|max:4000',
        ]);

        SendWhatsAppBroadcast::dispatch(
            $validated['channel'],
            $validated['title'],
            $validated['message'],
            auth()->id()
        );

        return redirect()->route('whatsapp.subscribers.index')
            ->with('success', __('messages.whatsapp_broadcast_queued'));
    }
}
