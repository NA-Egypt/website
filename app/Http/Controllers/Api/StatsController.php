<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    protected StatsService $statsService;

    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * Get platform statistics.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $refresh = $request->boolean('refresh', false) && ($request->user() !== null);
        $stats = $this->statsService->getStats($refresh);

        return response()->json([
            'data' => $stats,
        ], 200);
    }
}
