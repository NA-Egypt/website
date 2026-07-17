<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class FacebookTargetingController extends Controller
{
    private $cacheFile;

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
        'Virtual' => ['lat' => 26.8206, 'lng' => 30.8025],
        'افتراضي' => ['lat' => 26.8206, 'lng' => 30.8025],
    ];

    public function __construct()
    {
        $this->cacheFile = storage_path('app/facebook_targeting_coordinates.json');
    }

    public function index()
    {
        $cached = $this->loadCache();
        
        // Exclude online groups by group_type AND location Zoom URLs
        $groups = Group::with(['neighborhood.city'])
            ->whereNotIn('group_type', ['online', 'اونلاين', 'اون لاين'])
            ->get()
            ->filter(function ($g) {
                if ($g->location && preg_match('/zoom/i', $g->location)) {
                    return false;
                }
                return true;
            });

        // 1. Pre-calculate neighborhood centers (average of parsed coordinates in each neighborhood)
        $neighborhoodCoords = [];
        foreach ($groups as $g) {
            if (isset($cached[$g->id])) {
                $nid = $g->neighborhood_id;
                if ($nid) {
                    $neighborhoodCoords[$nid][] = $cached[$g->id];
                }
            }
        }

        $neighborhoodCenters = [];
        foreach ($neighborhoodCoords as $nid => $coordsList) {
            $lats = array_column($coordsList, 'lat');
            $lngs = array_column($coordsList, 'lng');
            $neighborhoodCenters[$nid] = [
                'lat' => array_sum($lats) / count($lats),
                'lng' => array_sum($lngs) / count($lngs),
            ];
        }

        // 2. Map all groups to coordinate values & source
        $mappedGroups = [];
        foreach ($groups as $g) {
            $lat = null;
            $lng = null;
            $source = 'unresolved';
            $defaultRadius = 5;

            if (isset($cached[$g->id])) {
                $lat = $cached[$g->id]['lat'];
                $lng = $cached[$g->id]['lng'];
                $source = 'parsed';
                $defaultRadius = 5;
            } else {
                // Fallback 1: Database Neighborhood Coordinates
                if ($g->neighborhood && !is_null($g->neighborhood->latitude) && !is_null($g->neighborhood->longitude)) {
                    $lat = (float)$g->neighborhood->latitude;
                    $lng = (float)$g->neighborhood->longitude;
                    $source = 'neighborhood_db';
                    $defaultRadius = 10;
                }
                // Fallback 2: Neighborhood average coordinates
                elseif ($g->neighborhood_id && isset($neighborhoodCenters[$g->neighborhood_id])) {
                    $lat = $neighborhoodCenters[$g->neighborhood_id]['lat'];
                    $lng = $neighborhoodCenters[$g->neighborhood_id]['lng'];
                    $source = 'neighborhood_avg';
                    $defaultRadius = 10;
                }
                // Fallback 3: Database City Coordinates
                elseif ($g->neighborhood && $g->neighborhood->city && !is_null($g->neighborhood->city->latitude) && !is_null($g->neighborhood->city->longitude)) {
                    $lat = (float)$g->neighborhood->city->latitude;
                    $lng = (float)$g->neighborhood->city->longitude;
                    $source = 'city_db';
                    $defaultRadius = 20;
                }
                // Fallback 4: Hardcoded City presets
                else {
                    $cityName = $g->neighborhood && $g->neighborhood->city ? $g->neighborhood->city->en_name : null;
                    $cityArName = $g->neighborhood && $g->neighborhood->city ? $g->neighborhood->city->ar_name : null;

                    if ($cityName && isset($this->cityCoordinates[$cityName])) {
                        $lat = $this->cityCoordinates[$cityName]['lat'];
                        $lng = $this->cityCoordinates[$cityName]['lng'];
                        $source = 'city_preset';
                        $defaultRadius = 20;
                    } elseif ($cityArName && isset($this->cityCoordinates[$cityArName])) {
                        $lat = $this->cityCoordinates[$cityArName]['lat'];
                        $lng = $this->cityCoordinates[$cityArName]['lng'];
                        $source = 'city_preset';
                        $defaultRadius = 20;
                    } else {
                        // Fallback 5: Cairo default
                        $lat = $this->cityCoordinates['Cairo']['lat'];
                        $lng = $this->cityCoordinates['Cairo']['lng'];
                        $source = 'default';
                        $defaultRadius = 20;
                    }
                }
            }

            $mappedGroups[] = [
                'id' => $g->id,
                'name' => app()->getLocale() === 'ar' ? ($g->ar_name ?: $g->en_name) : ($g->en_name ?: $g->ar_name),
                'location_url' => $g->location,
                'address' => app()->getLocale() === 'ar' ? ($g->ar_address ?: $g->en_address) : ($g->en_address ?: $g->ar_address),
                'city' => $g->neighborhood && $g->neighborhood->city ? (app()->getLocale() === 'ar' ? $g->neighborhood->city->ar_name : $g->neighborhood->city->en_name) : 'N/A',
                'neighborhood' => $g->neighborhood ? (app()->getLocale() === 'ar' ? $g->neighborhood->ar_name : $g->neighborhood->en_name) : 'N/A',
                'lat' => $lat,
                'lng' => $lng,
                'source' => $source,
                'radius' => $defaultRadius,
            ];
        }

        return view('facebook_targeting.index', [
            'groups' => $mappedGroups,
        ]);
    }

    public function sync()
    {
        if (!auth()->user() || !auth()->user()->hasRole('super admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Exclude online groups by group_type AND location Zoom URLs
        $groups = Group::whereNotIn('group_type', ['online', 'اونلاين', 'اون لاين'])
            ->get()
            ->filter(function ($g) {
                if ($g->location && preg_match('/zoom/i', $g->location)) {
                    return false;
                }
                return true;
            });
            
        $cached = $this->loadCache();

        foreach ($groups as $g) {
            $url = $g->location;
            if (empty($url)) {
                continue;
            }

            if (!preg_match('/^https?:\/\//i', $url)) {
                $url = 'https://' . $url;
            }

            // Resolve redirect if shortened URL
            $resolvedUrl = $url;
            if (preg_match('/(maps\.app\.goo\.gl|bit\.ly|share\.google)/i', $url)) {
                try {
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, true);
                    curl_setopt($ch, CURLOPT_NOBODY, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
                    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
                    curl_exec($ch);
                    $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                    curl_close($ch);
                    if ($effectiveUrl) {
                        $resolvedUrl = $effectiveUrl;
                    }
                } catch (\Exception $e) {
                    // Ignore redirect failures, use original
                }
            }

            $coords = $this->parseCoordinates($resolvedUrl);
            if ($coords) {
                $cached[$g->id] = $coords;
            }
        }

        $this->saveCache($cached);

        return redirect()->route('facebook-targeting.index')->with('success', __('Coordinates synchronized successfully.'));
    }

    public function download(Request $request)
    {
        $radii = $request->input('radii', []);
        $selectedIds = $request->input('selected_groups', []);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="facebook_targeting_areas.csv"',
        ];

        $callback = function() use ($selectedIds, $radii) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for correct Excel opening
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Facebook bulk location import headers
            fputcsv($file, ['Latitude', 'Longitude', 'Radius', 'Name', 'Country Code']);

            foreach ($selectedIds as $item) {
                $parts = explode(':', $item, 4);
                if (count($parts) >= 4) {
                    $id = $parts[0];
                    $lat = $parts[1];
                    $lng = $parts[2];
                    $name = $parts[3];

                    $radius = isset($radii[$id]) ? (float)$radii[$id] : 5;

                    fputcsv($file, [
                        $lat,
                        $lng,
                        $radius,
                        $name,
                        'EG'
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function parseCoordinates($url)
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

    private function loadCache()
    {
        if (File::exists($this->cacheFile)) {
            $content = File::get($this->cacheFile);
            return json_decode($content, true) ?: [];
        }
        return [];
    }

    private function saveCache(array $data)
    {
        $dir = dirname($this->cacheFile);
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        File::put($this->cacheFile, json_encode($data, JSON_PRETTY_PRINT));
    }
}
