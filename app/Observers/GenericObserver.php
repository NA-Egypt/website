<?php

namespace App\Observers;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;

class GenericObserver
{
    public function created(Model $model)
    {
        $this->logTransaction($model, 'create');
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
        Transaction::create([
            'model' => class_basename($model),
            'operation' => $operation,
            'details' => $model->toArray(),
            'user_id' => auth()->id(),
        ]);
    }
}
