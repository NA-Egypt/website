<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JftController extends Controller
{
    protected JftService $jftService;

    public function __construct(JftService $jftService)
    {
        $this->jftService = $jftService;
    }

    /**
     * Get the Just For Today spiritual reading.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        $dateString = $request->query('date');
        $reading = $this->jftService->getReading($dateString);

        return response()->json([
            'data' => $reading,
        ], 200);
    }
}
