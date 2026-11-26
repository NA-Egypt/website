<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait PaginatesApi
{
    protected function resolvePerPage(Request $request, int $default = 50, int $max = 200): int
    {
        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:'.$max],
        ]);

        return (int) ($validated['per_page'] ?? $default);
    }

    protected function paginateOrAll(Request $request, Builder $query, int $default = 50, int $max = 200)
    {
        if ($request->boolean('all')) {
            return $query->get();
        }

        return $query->paginate($this->resolvePerPage($request, $default, $max));
    }
}
