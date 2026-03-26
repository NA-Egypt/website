<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\ServiceBody;
use App\Models\Transaction;
use Illuminate\Http\Request;

class GreatingPagesController extends Controller
{
    public function dashboard() {
        
        $meetings = Meeting::all();

        $serviceBodies = ServiceBody::all();

        // $cities = City::all();
        $cities = City::with('neighborhoods.groups')->get();

        $groups = Group::all();

        // transactions:
        $transactions = Transaction::with('user')->latest()->paginate(4);

        // Augment the paginated collection with `group_name`
        $transactions->getCollection()->transform(function ($transaction) {
            if ($transaction->model === 'Meeting' && isset($transaction->details['group_id'])) {
                $group = Group::find($transaction->details['group_id']);
                $transaction->group_name = $group ? $group->name : 'N/A';
            }
            return $transaction;
        });

        return view('dashborad', [

            'meetings'      => $meetings,
            'serviceBodies' => $serviceBodies,
            'cities'        => $cities,
            'groups'        => $groups,
            'transactions'  => $transactions,
        ]);
    }
}
