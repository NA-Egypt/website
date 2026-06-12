<?php

namespace App\Observers;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;

class GenericObserver
{
    private static $originalValues = [];

    public function created(Model $model)
    {
        $this->logTransaction($model, 'create');
    }

    public function updating(Model $model)
    {
        $hash = spl_object_hash($model);
        self::$originalValues[$hash] = $model->getRawOriginal();
    }

    public function updated(Model $model)
    {
        $this->logTransaction($model, 'update');
    }

    public function deleted(Model $model)
    {
        $this->logTransaction($model, 'delete');
    }

    private function logTransaction(Model $model, string $operation)
    {
        $oldValues = null;
        $newValues = null;
        $details = [];

        if ($operation === 'create') {
            $newValues = $this->sanitize($model->getAttributes());
            $details = $newValues;
        } elseif ($operation === 'update') {
            $hash = spl_object_hash($model);
            $changes = $model->getChanges();
            $originals = self::$originalValues[$hash] ?? [];
            unset(self::$originalValues[$hash]); // Clean up static store

            $oldValues = [];
            $newValues = [];

            foreach ($changes as $key => $newValue) {
                if (in_array($key, ['updated_at'])) {
                    continue;
                }
                $oldValues[$key] = $originals[$key] ?? null;
                $newValues[$key] = $newValue;
            }

            // Sanitize sensitive fields from change tracking
            $oldValues = $this->sanitize($oldValues);
            $newValues = $this->sanitize($newValues);

            if (empty($oldValues) && empty($newValues)) {
                return; // No auditable changes detected (e.g. only updated_at changed)
            }

            $details = $newValues;
        } elseif ($operation === 'delete') {
            $oldValues = $this->sanitize($model->getRawOriginal());
            $details = $oldValues;
        }

        Transaction::create([
            'model' => class_basename($model),
            'operation' => $operation,
            'details' => $details,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'user_id' => auth()->id(),
        ]);
    }

    private function sanitize(array $data): array
    {
        $hidden = ['password', 'remember_token', 'token', 'secret', 'key', 'api_token'];
        return array_filter($data, function ($key) use ($hidden) {
            foreach ($hidden as $field) {
                if (stripos($key, $field) !== false) {
                    return false;
                }
            }
            return true;
        }, ARRAY_FILTER_USE_KEY);
    }
}
