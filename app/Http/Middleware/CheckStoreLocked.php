<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\StocktakingSession;
use Symfony\Component\HttpFoundation\Response;

class CheckStoreLocked
{
    public function handle(Request $request, Closure $next): Response
    {
        $activeSession = StocktakingSession::getActiveSession();

        if ($activeSession) {
            $msg = __('messages.store_locked_stocktaking_active', [
                'id' => $activeSession->id,
                'user' => $activeSession->user->name ?? 'System'
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                    'store_locked' => true,
                ], 423); // 423 Locked
            }

            return redirect()->route('store.stocktaking.show', $activeSession->id)
                ->with('error', $msg);
        }

        return $next($request);
    }
}
