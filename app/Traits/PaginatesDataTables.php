<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait PaginatesDataTables
{
    /**
     * Paginate queries according to Vue3 Datatable parameters.
     */
    protected function paginateDataTable(Builder $query, Request $request, array $searchableColumns = [])
    {
        $perPage = (int) $request->input('pagesize', 15);
        if ($perPage < 1 || $perPage > 100) {
            $perPage = 15;
        }

        $sortColumn = $request->input('sort_column');
        $sortDirection = $request->input('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $search = $request->input('search');

        // Apply global search across specified columns (and relation columns)
        if (!empty($search) && !empty($searchableColumns)) {
            $query->where(function ($q) use ($search, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    if (str_contains($column, '.')) {
                        [$relation, $relColumn] = explode('.', $column, 2);
                        $q->orWhereHas($relation, function ($rq) use ($search, $relColumn) {
                            $rq->where($relColumn, 'like', "%{$search}%");
                        });
                    } else {
                        $q->orWhere($column, 'like', "%{$search}%");
                    }
                }
            });
        }

        // Apply sorting
        if (!empty($sortColumn) && !str_contains($sortColumn, '.')) {
            // Check if column exists in table to prevent errors
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('id', 'desc');
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
