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
            'locale' => 'nullable|string|in:ar,en',
            'lang' => 'nullable|string|in:ar,en',
        ]);

        $dateString = $request->query('date');
        $locale = $request->query('locale', $request->query('lang', $request->header('X-Locale', app()->getLocale())));

        $reading = $this->jftService->getReading($dateString, $locale);

        return response()->json([
            'data' => $reading,
        ], 200);
    }
}
