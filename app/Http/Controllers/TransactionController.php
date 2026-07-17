<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('pagesize', 15);
        $sortColumn = $request->input('sort_column', 'created_at');
        
        $allowedSorts = ['operation', 'model', 'created_at', 'id'];
        if (!in_array($sortColumn, $allowedSorts)) {
            $sortColumn = 'created_at';
        }
        $sortDirection = $request->input('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $query = Transaction::with('user');

        if ($request->filled('filter_model')) {
            $query->where('model', $request->input('filter_model'));
        }

        if ($request->filled('filter_operation')) {
            $query->where('operation', $request->input('filter_operation'));
        }

        if ($request->filled('search_user')) {
            $searchUser = $request->input('search_user');
            $query->whereHas('user', function ($q) use ($searchUser) {
                $q->where('name', 'like', "%{$searchUser}%")
                  ->orWhere('email', 'like', "%{$searchUser}%");
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('model', 'like', "%{$search}%")
                  ->orWhere('operation', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        if ($sortColumn === 'created_at') {
            $query->orderBy('created_at', $sortDirection);
        } else {
            $query->orderBy($sortColumn, $sortDirection)->orderBy('created_at', 'desc');
        }

        $transactions = $query->paginate($perPage)->withQueryString();

        // Augment the paginated collection with `group_name`
        $transactions->getCollection()->transform(function ($transaction) {
            if ($transaction->model === 'Meeting' && isset($transaction->details['group_id'])) {
                $group = Group::find($transaction->details['group_id']);
                $transaction->group_name = $group ? $group->name : 'N/A';
            }
            return $transaction;
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($transactions);
        }

        $availableModels = Transaction::distinct()->pluck('model')->toArray();
        $availableOperations = Transaction::distinct()->pluck('operation')->toArray();
        
        return view('transactions.index', compact('transactions', 'availableModels', 'availableOperations'));
    }

}
