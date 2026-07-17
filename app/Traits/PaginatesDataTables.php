<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

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

        $sortColumn    = $request->input('sort_column');
        $sortDirection = $request->input('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $search        = $request->input('search');

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
        if (!empty($sortColumn)) {
            $model = $query->getModel();
            $table = $model->getTable();

            // Map common virtual/formatted columns to real DB columns
            $columnMap = [
                'created_at_formatted' => 'created_at',
                'updated_at_formatted' => 'updated_at',
                'status'               => ($table === 'subscribers') ? 'email_verified_at' : 'status',
            ];

            if (isset($columnMap[$sortColumn])) {
                $sortColumn = $columnMap[$sortColumn];
            }

            if (str_contains($sortColumn, '.')) {
                // Relationship sort: e.g. "city.ar_name" → JOIN cities ON ... ORDER BY cities.ar_name
                [$relation, $relColumn] = explode('.', $sortColumn, 2);

                // Attempt to resolve the relation table automatically
                if (method_exists($model, $relation)) {
                    try {
                        $relationInstance = $model->$relation();
                        $relatedTable     = $relationInstance->getRelated()->getTable();
                        $foreignKey       = $relationInstance->getForeignKeyName();
                        $ownerKey         = $relationInstance->getOwnerKeyName();

                        $query
                            ->join($relatedTable, "$table.$foreignKey", '=', "$relatedTable.$ownerKey")
                            ->select("$table.*")
                            ->orderBy("$relatedTable.$relColumn", $sortDirection);
                    } catch (\Throwable $e) {
                        $query->orderBy($model->getKeyName(), $sortDirection);
                    }
                } else {
                    $query->orderBy($model->getKeyName(), $sortDirection);
                }
            } elseif (Schema::hasColumn($table, $sortColumn)) {
                $query->orderBy("$table.$sortColumn", $sortDirection);
            } else {
                $query->orderBy($model->getKeyName(), $sortDirection);
            }
        } else {
            $query->orderBy($query->getModel()->getKeyName(), 'desc');
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
