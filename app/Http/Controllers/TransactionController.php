<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user');

        if ($request->filled('filter_model')) {
            $query->where('model', $request->input('filter_model'));
        }

        if ($request->filled('filter_operation')) {
            $query->where('operation', $request->input('filter_operation'));
        }

        if ($request->filled('search_user')) {
            $search = $request->input('search_user');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();

        // Augment the paginated collection with `group_name`
        $transactions->getCollection()->transform(function ($transaction) {
            if ($transaction->model === 'Meeting' && isset($transaction->details['group_id'])) {
                $group = Group::find($transaction->details['group_id']);
                $transaction->group_name = $group ? $group->name : 'N/A';
            }
            return $transaction;
        });

        $availableModels = Transaction::distinct()->pluck('model')->toArray();
        $availableOperations = Transaction::distinct()->pluck('operation')->toArray();
        
        return view('transactions.index', compact('transactions', 'availableModels', 'availableOperations'));
    }

}
