<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Http\Resources\CalendarEventResource;
use App\Services\JftService;
use App\Services\StatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    protected JftService $jftService;
    protected StatsService $statsService;

    public function __construct(JftService $jftService, StatsService $statsService)
    {
        $this->jftService = $jftService;
        $this->statsService = $statsService;
    }

    /**
     * Get consolidated data for the Frontpage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $date = $request->query('date');
        $cacheKey = 'api_v1_home_data_' . ($date ?: now()->format('Y_m_d'));

        $data = Cache::remember($cacheKey, 1800, function () use ($date) {
            $stats = $this->statsService->getStats();
            $jft = $this->jftService->getReading($date);

            // Helplines matching the official frontpage
            $helplineNumbers = [
                [
                    'region' => 'Egypt Region (General)',
                    'region_ar' => 'إقليم مصر (عام)',
                    'phones' => ['+201006979198', '+201060933888'],
                    'whatsapp' => 'https://wa.me/201060933888',
                    'hours' => '10 AM - 12 Midnight',
                    'hours_ar' => '١٠ ص - ١٢ منتصف الليل',
                ],
                [
                    'region' => 'Alexandria',
                    'region_ar' => 'الإسكندرية',
                    'phones' => ['+201503884411'],
                    'whatsapp' => 'https://wa.me/201503884411',
                    'hours' => '12 PM - 10 PM',
                    'hours_ar' => '١٢ م - ١٠ م',
                ],
            ];

            // Official Social links
            $socialLinks = [
                'facebook' => 'https://www.facebook.com/OfficialNAEgyPage',
                'instagram' => 'https://www.instagram.com/narcoticsanonymousegy',
                'tiktok' => 'https://www.tiktok.com/@narcoticsanonymousegypt',
                'whatsapp' => 'https://wa.me/201060933888',
                'email' => 'pr@naegypt.org',
            ];

            // Upcoming events (up to 5 upcoming calendar events)
            $upcomingEvents = [];
            try {
                if (class_exists(CalendarEvent::class)) {
                    $events = CalendarEvent::where('start', '>=', now())
                        ->orderBy('start', 'asc')
                        ->limit(5)
                        ->get();
                    $upcomingEvents = CalendarEventResource::collection($events)->resolve();
                }
            } catch (\Exception $e) {
                $upcomingEvents = [];
            }

            return [
                'stats' => $stats,
                'jft' => $jft,
                'helplines' => $helplineNumbers,
                'social_links' => $socialLinks,
                'upcoming_events' => $upcomingEvents,
            ];
        });

        return response()->json([
            'data' => $data,
        ], 200);
    }
}
