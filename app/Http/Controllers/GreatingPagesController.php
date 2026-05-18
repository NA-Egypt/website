<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\ServiceBody;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class GreatingPagesController extends Controller
{
    public function dashboard() {
        
        $user = auth()->user();
        
        $meetings = Meeting::all();
        $serviceBodies = ServiceBody::all();
        $cities = City::with('neighborhoods.groups')->get();
        $groups = Group::all();
        $usersCount = User::count();
        $agendas = collect();

        if ($user && $user->hasRole('ServiceBody') && $user->service_body_id) {
            $sbId = $user->service_body_id;
            
            $groups = Group::where('service_body_id', $sbId)->get();
            $groupIds = $groups->pluck('id')->toArray();
            
            $meetings = Meeting::whereHas('group', function($q) use ($sbId) {
                $q->where('service_body_id', $sbId);
            })->get();
            
            $cities = City::with(['neighborhoods.groups' => function($q) use ($sbId) {
                $q->where('service_body_id', $sbId);
            }])->whereHas('neighborhoods.groups', function($q) use ($sbId) {
                $q->where('service_body_id', $sbId);
            })->get();

            if ($user->serviceBody) {
                $agendas = $user->serviceBody->agendas()->orderBy('agenda_date', 'desc')->get();
            }
        }

        // transactions:
        /** @var \Illuminate\Pagination\LengthAwarePaginator $transactions */
        $transactions = Transaction::with('user')->latest()->paginate(4);

        // Augment the paginated collection with `group_name`
        $transactions->through(function ($transaction) {
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
            'usersCount'    => $usersCount,
            'transactions'  => $transactions,
            'agendas'       => $agendas,
        ]);
    }
}
