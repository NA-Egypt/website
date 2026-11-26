<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Group;
use App\Models\Neighborhood;
use App\Models\ServiceBody;
use App\Models\ServiceCommittee;
use App\Services\Search\ArabicNormalizer;
use App\Traits\PaginatesApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    use PaginatesApi;

    public function search(Request $request, string $entity): JsonResponse
    {
        $rawQuery = trim((string) $request->query('q', ''));
        $normalized = ArabicNormalizer::normalize($rawQuery);
        $perPage = $this->resolvePerPage($request, 20, 100);

        $modelClass = match ($entity) {
            'groups' => Group::class,
            'cities' => City::class,
            'neighborhoods' => Neighborhood::class,
            'committees' => ServiceCommittee::class,
            'servicebodies' => ServiceBody::class,
            default => abort(404),
        };

        $query = $modelClass::query()->select(['id', 'ar_name', 'en_name']);

        if ($rawQuery !== '') {
            if (in_array($entity, ['groups', 'cities', 'neighborhoods'], true)) {
                $like = '%'.($normalized !== '' ? $normalized : $rawQuery).'%';
                $query->where('search_text', 'LIKE', $like);
            } else {
                $like = '%'.$rawQuery.'%';
                $query->where(function ($q) use ($like) {
                    $q->where('ar_name', 'LIKE', $like)
                        ->orWhere('en_name', 'LIKE', $like);
                });
            }
        }

        $results = $query->orderBy('ar_name')->paginate($perPage);

        return response()->json([
            'data' => $results->items(),
            'meta' => [
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
            ],
            'links' => [
                'first' => $results->url(1),
                'last' => $results->url($results->lastPage()),
                'prev' => $results->previousPageUrl(),
                'next' => $results->nextPageUrl(),
            ],
        ]);
    }
}
