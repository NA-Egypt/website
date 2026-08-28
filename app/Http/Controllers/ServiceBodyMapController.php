<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Group;
use App\Models\Neighborhood;
use App\Models\ServiceBody;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ServiceBodyMapController extends Controller
{
    private $cacheFile;

    private $serviceBodyColors = [
        1 => ['primary' => '#2563eb', 'stroke' => '#1d4ed8', 'fill' => '#3b82f6'], // North East Cairo - Blue
        3 => ['primary' => '#059669', 'stroke' => '#047857', 'fill' => '#10b981'], // Lower Egypt - Green
        6 => ['primary' => '#d97706', 'stroke' => '#b45309', 'fill' => '#f59e0b'], // Al Wehda - Amber / Orange
        7 => ['primary' => '#7c3aed', 'stroke' => '#6d28d9', 'fill' => '#8b5cf6'], // West Cairo - Purple
        8 => ['primary' => '#db2777', 'stroke' => '#be185d', 'fill' => '#ec4899'], // South Cairo - Rose / Pink
        9 => ['primary' => '#b45309', 'stroke' => '#92400e', 'fill' => '#d97706'], // Upper Egypt - Warm Ochre
        10 => ['primary' => '#0891b2', 'stroke' => '#0e7490', 'fill' => '#06b6d4'], // Giza - Cyan / Teal
        11 => ['primary' => '#e11d48', 'stroke' => '#be123c', 'fill' => '#f43f5e'], // Canal - Crimson Red
    ];

    private $fallbackColors = [
        ['primary' => '#4f46e5', 'stroke' => '#4338ca', 'fill' => '#6366f1'],
        ['primary' => '#0d9488', 'stroke' => '#0f766e', 'fill' => '#14b8a6'],
        ['primary' => '#ea580c', 'stroke' => '#c2410c', 'fill' => '#f97316'],
        ['primary' => '#9333ea', 'stroke' => '#7e22ce', 'fill' => '#a855f7'],
    ];

    private $cityCoordinates = [
        'Cairo' => ['lat' => 30.0444, 'lng' => 31.2357],
        'القاهرة' => ['lat' => 30.0444, 'lng' => 31.2357],
        'Alexandria' => ['lat' => 31.2001, 'lng' => 29.9187],
        'الإسكندرية' => ['lat' => 31.2001, 'lng' => 29.9187],
        'Giza' => ['lat' => 30.0131, 'lng' => 31.2089],
        'الجيزة' => ['lat' => 30.0131, 'lng' => 31.2089],
        'El-Sharqeya' => ['lat' => 30.5877, 'lng' => 31.5020],
        'الشرقية' => ['lat' => 30.5877, 'lng' => 31.5020],
        'Daqahliya' => ['lat' => 31.0409, 'lng' => 31.3785],
        'الدقهلية' => ['lat' => 31.0409, 'lng' => 31.3785],
        'El-Gharbeiya' => ['lat' => 30.7865, 'lng' => 31.0004],
        'الغربية' => ['lat' => 30.7865, 'lng' => 31.0004],
        'Domiat' => ['lat' => 31.4175, 'lng' => 31.8144],
        'دمياط' => ['lat' => 31.4175, 'lng' => 31.8144],
        'Port Said' => ['lat' => 31.2653, 'lng' => 32.3019],
        'بورسعيد' => ['lat' => 31.2653, 'lng' => 32.3019],
        'Red Sea' => ['lat' => 27.2579, 'lng' => 33.8116],
        'البحر الأحمر' => ['lat' => 27.2579, 'lng' => 33.8116],
        'Al-Minya' => ['lat' => 28.0991, 'lng' => 30.7636],
        'المنيا' => ['lat' => 28.0991, 'lng' => 30.7636],
        'Asyout' => ['lat' => 27.1783, 'lng' => 31.1859],
        'أسيوط' => ['lat' => 27.1783, 'lng' => 31.1859],
        'Sohag' => ['lat' => 26.5591, 'lng' => 31.6948],
        'سوهاج' => ['lat' => 26.5591, 'lng' => 31.6948],
        'Ismailia' => ['lat' => 30.5965, 'lng' => 32.2715],
        'الاسماعيلية' => ['lat' => 30.5965, 'lng' => 32.2715],
        'Al-Beheira' => ['lat' => 31.0379, 'lng' => 30.4704],
        'البحيرة' => ['lat' => 31.0379, 'lng' => 30.4704],
        'Kafr Elsheikh' => ['lat' => 31.1107, 'lng' => 30.9388],
        'كفر الشيخ' => ['lat' => 31.1107, 'lng' => 30.9388],
        'North-Sinai' => ['lat' => 31.1321, 'lng' => 33.7984],
        'شمال سيناء' => ['lat' => 31.1321, 'lng' => 33.7984],
        'South-Sinai' => ['lat' => 27.9158, 'lng' => 34.3299],
        'جنوب سيناء' => ['lat' => 27.9158, 'lng' => 34.3299],
        'El-Monofeya' => ['lat' => 30.5574, 'lng' => 31.0097],
        'المنوفية' => ['lat' => 30.5574, 'lng' => 31.0097],
        'Beni Suef' => ['lat' => 29.0744, 'lng' => 31.0978],
        'بني سويف' => ['lat' => 29.0744, 'lng' => 31.0978],
        'Aswan' => ['lat' => 24.0889, 'lng' => 32.8998],
        'أسوان' => ['lat' => 24.0889, 'lng' => 32.8998],
        'Luxor' => ['lat' => 25.6872, 'lng' => 32.6396],
        'الأقصر' => ['lat' => 25.6872, 'lng' => 32.6396],
        'Qena' => ['lat' => 26.1551, 'lng' => 32.7160],
        'قنا' => ['lat' => 26.1551, 'lng' => 32.7160],
        'Fayoum' => ['lat' => 29.3084, 'lng' => 30.8428],
        'الفيوم' => ['lat' => 29.3084, 'lng' => 30.8428],
        'Suez' => ['lat' => 29.9668, 'lng' => 32.5498],
        'السويس' => ['lat' => 29.9668, 'lng' => 32.5498],
    ];

    public function __construct()
    {
        $this->cacheFile = storage_path('app/facebook_targeting_coordinates.json');
    }

    /**
     * Public page view for Service Bodies Area Map
     */
    public function index()
    {
        $mapData = $this->getServiceBodiesMapData();
        return view('frontend.service_body_map', compact('mapData'));
    }

    /**
     * Dashboard authenticated view for Service Bodies Area Map
     */
    public function dashboardMap()
    {
        $mapData = $this->getServiceBodiesMapData();
        return view('serviceBody.map', compact('mapData'));
    }

    /**
     * JSON API endpoint for Service Bodies Map data (Live database reading)
     */
    public function apiData()
    {
        return response()->json($this->getServiceBodiesMapData());
    }

    /**
     * Compile Service Bodies, Groups, and resolved live coordinates
     */
    public function getServiceBodiesMapData()
    {
        $cached = $this->loadCache();
        $isAr = app()->getLocale() === 'ar';

        // Load all physical groups (exclude online) with fresh relationships
        $allPhysicalGroups = Group::with([
            'neighborhood.city',
            'meetings' => function ($q) {
                $q->with(['day', 'topic']);
            }
        ])
            ->whereNotIn('group_type', ['online', 'اونلاين', 'اون لاين'])
            ->get()
            ->filter(function ($g) {
                if ($g->location && preg_match('/zoom/i', $g->location)) {
                    return false;
                }
                return true;
            });

        // 1. Resolve coordinates for all groups directly from live location URL or cache
        $directParsedCoords = [];
        foreach ($allPhysicalGroups as $g) {
            if ($g->location) {
                $parsed = $this->parseCoordinatesDirectly($g->location);
                if ($parsed) {
                    $directParsedCoords[$g->id] = $parsed;
                }
            }
        }

        // 2. Pre-calculate neighborhood centers from live coordinates
        $neighborhoodCoords = [];
        foreach ($allPhysicalGroups as $g) {
            $coords = $directParsedCoords[$g->id] ?? ($cached[$g->id] ?? null);
            if ($coords) {
                $nid = $g->neighborhood_id;
                if ($nid) {
                    $neighborhoodCoords[$nid][] = $coords;
                }
            }
        }

        $neighborhoodCenters = [];
        foreach ($neighborhoodCoords as $nid => $coordsList) {
            $lats = array_column($coordsList, 'lat');
            $lngs = array_column($coordsList, 'lng');
            if (count($lats) > 0 && count($lngs) > 0) {
                $neighborhoodCenters[$nid] = [
                    'lat' => array_sum($lats) / count($lats),
                    'lng' => array_sum($lngs) / count($lngs),
                ];
            }
        }

        // Fetch Service Bodies (excluding ID 2: Al Ahram GSF as requested)
        $serviceBodies = ServiceBody::where('id', '!=', 2)
            ->where(function ($q) {
                $q->whereNull('location')
                    ->orWhere('location', 'not like', '%أونلاين%')
                    ->orWhere('id', '!=', 2);
            })
            ->orderBy('id')
            ->get();

        $serviceBodyList = [];
        $totalMappedGroups = 0;
        $totalMeetings = 0;

        foreach ($serviceBodies as $index => $sb) {
            // Get physical groups belonging to this service body
            $sbGroups = $allPhysicalGroups->where('service_body_id', $sb->id);

            // Determine color palette
            $color = $this->serviceBodyColors[$sb->id] ?? $this->fallbackColors[$index % count($this->fallbackColors)];

            $mappedGroups = [];
            $lats = [];
            $lngs = [];

            foreach ($sbGroups as $g) {
                $lat = null;
                $lng = null;
                $source = 'unresolved';

                // Hierarchy 1: Direct location URL parse
                if (isset($directParsedCoords[$g->id])) {
                    $lat = (float)$directParsedCoords[$g->id]['lat'];
                    $lng = (float)$directParsedCoords[$g->id]['lng'];
                    $source = 'live_url';
                }
                // Hierarchy 2: Cached coordinate
                elseif (isset($cached[$g->id])) {
                    $lat = (float)$cached[$g->id]['lat'];
                    $lng = (float)$cached[$g->id]['lng'];
                    $source = 'cached';
                }
                // Hierarchy 3: Neighborhood DB coordinates
                elseif ($g->neighborhood && !is_null($g->neighborhood->latitude) && !is_null($g->neighborhood->longitude)) {
                    $lat = (float)$g->neighborhood->latitude;
                    $lng = (float)$g->neighborhood->longitude;
                    $source = 'neighborhood_db';
                }
                // Hierarchy 4: Neighborhood live average
                elseif ($g->neighborhood_id && isset($neighborhoodCenters[$g->neighborhood_id])) {
                    $lat = (float)$neighborhoodCenters[$g->neighborhood_id]['lat'];
                    $lng = (float)$neighborhoodCenters[$g->neighborhood_id]['lng'];
                    $source = 'neighborhood_avg';
                }
                // Hierarchy 5: City DB coordinates
                elseif ($g->neighborhood && $g->neighborhood->city && !is_null($g->neighborhood->city->latitude) && !is_null($g->neighborhood->city->longitude)) {
                    $lat = (float)$g->neighborhood->city->latitude;
                    $lng = (float)$g->neighborhood->city->longitude;
                    $source = 'city_db';
                }
                // Hierarchy 6: City preset
                else {
                    $cityName = $g->neighborhood && $g->neighborhood->city ? $g->neighborhood->city->en_name : null;
                    $cityArName = $g->neighborhood && $g->neighborhood->city ? $g->neighborhood->city->ar_name : null;

                    if ($cityName && isset($this->cityCoordinates[$cityName])) {
                        $lat = (float)$this->cityCoordinates[$cityName]['lat'];
                        $lng = (float)$this->cityCoordinates[$cityName]['lng'];
                        $source = 'city_preset';
                    } elseif ($cityArName && isset($this->cityCoordinates[$cityArName])) {
                        $lat = (float)$this->cityCoordinates[$cityArName]['lat'];
                        $lng = (float)$this->cityCoordinates[$cityArName]['lng'];
                        $source = 'city_preset';
                    } else {
                        // Default Cairo
                        $lat = (float)$this->cityCoordinates['Cairo']['lat'];
                        $lng = (float)$this->cityCoordinates['Cairo']['lng'];
                        $source = 'default';
                    }
                }

                if ($lat && $lng) {
                    $lats[] = $lat;
                    $lngs[] = $lng;
                }

                // Format meeting times
                $formattedMeetings = $g->meetings->map(function ($m) use ($isAr) {
                    return [
                        'id' => $m->id,
                        'day' => $m->day ? ($isAr ? $m->day->ar_name : $m->day->en_name) : '',
                        'start_time' => $m->formatted_start_time ?? substr($m->start_time, 0, 5),
                        'end_time' => $m->formatted_end_time ?? substr($m->end_time, 0, 5),
                        'topic' => $m->topic ? ($isAr ? $m->topic->ar_name : $m->topic->en_name) : '',
                        'type' => $m->type,
                    ];
                })->values()->toArray();

                $totalMeetings += count($formattedMeetings);

                $mappedGroups[] = [
                    'id' => $g->id,
                    'service_body_id' => $sb->id,
                    'name' => $isAr ? ($g->ar_name ?: $g->en_name) : ($g->en_name ?: $g->ar_name),
                    'ar_name' => $g->ar_name,
                    'en_name' => $g->en_name,
                    'gsr_name' => $isAr ? ($g->ar_gsr_name ?: $g->en_gsr_name) : ($g->en_gsr_name ?: $g->ar_gsr_name),
                    'phone' => $g->phone,
                    'location_url' => $g->location,
                    'address' => $isAr ? ($g->ar_address ?: $g->en_address) : ($g->en_address ?: $g->ar_address),
                    'city' => $g->neighborhood && $g->neighborhood->city ? ($isAr ? $g->neighborhood->city->ar_name : $g->neighborhood->city->en_name) : '',
                    'neighborhood' => $g->neighborhood ? ($isAr ? $g->neighborhood->ar_name : $g->neighborhood->en_name) : '',
                    'lat' => $lat,
                    'lng' => $lng,
                    'source' => $source,
                    'meetings' => $formattedMeetings,
                    'meetings_count' => count($formattedMeetings),
                ];
            }

            $totalMappedGroups += count($mappedGroups);

            // Calculate center and bounding box for this service body
            $center = null;
            $bounds = null;
            if (count($lats) > 0 && count($lngs) > 0) {
                $minLat = min($lats);
                $maxLat = max($lats);
                $minLng = min($lngs);
                $maxLng = max($lngs);

                $center = [
                    'lat' => array_sum($lats) / count($lats),
                    'lng' => array_sum($lngs) / count($lngs),
                ];

                $bounds = [
                    'south' => $minLat,
                    'north' => $maxLat,
                    'west' => $minLng,
                    'east' => $maxLng,
                ];
            }

            $serviceBodyList[] = [
                'id' => $sb->id,
                'name' => $isAr ? ($sb->ar_name ?: $sb->en_name) : ($sb->en_name ?: $sb->ar_name),
                'ar_name' => $sb->ar_name,
                'en_name' => $sb->en_name,
                'description' => $sb->description,
                'location' => $sb->location,
                'color' => $color['primary'],
                'stroke_color' => $color['stroke'],
                'fill_color' => $color['fill'],
                'groups_count' => count($mappedGroups),
                'meetings_count' => array_sum(array_column($mappedGroups, 'meetings_count')),
                'center' => $center,
                'bounds' => $bounds,
                'groups' => $mappedGroups,
            ];
        }

        return [
            'service_bodies' => $serviceBodyList,
            'total_service_bodies' => count($serviceBodyList),
            'total_groups' => $totalMappedGroups,
            'total_meetings' => $totalMeetings,
            'locale' => app()->getLocale(),
        ];
    }

    /**
     * Direct parser for coordinates embedded in URL strings
     */
    private function parseCoordinatesDirectly($url): ?array
    {
        $url = urldecode($url);
        if (preg_match('/(?:@|search\/|q=)(-?\d+\.\d+)\s*,\s*\+?(-?\d+\.\d+)/i', $url, $matches)) {
            return ['lat' => (float)$matches[1], 'lng' => (float)$matches[2]];
        }

        if (preg_match('/(-?\d+\.\d+)\s*,\s*\+?(-?\d+\.\d+)/', $url, $matches)) {
            $lat = (float)$matches[1];
            $lng = (float)$matches[2];
            if ($lat >= 20 && $lat <= 34 && $lng >= 24 && $lng <= 37) {
                return ['lat' => $lat, 'lng' => $lng];
            }
        }

        return null;
    }

    /**
     * Load coordinate cache
     */
    private function loadCache(): array
    {
        if (File::exists($this->cacheFile)) {
            $data = json_decode(File::get($this->cacheFile), true);
            return is_array($data) ? $data : [];
        }
        return [];
    }
}
