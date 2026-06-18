<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\ServiceBody;
use App\Models\Transaction;
use App\Models\User;
use App\Models\CommitteeReport;
use App\Models\CustomForm;
use App\Models\ServiceCommittee;
use Illuminate\Http\Request;

class GreatingPagesController extends Controller
{
    public function dashboard() {
        
        $user = auth()->user();
        
        // Calculate reportsCount based on permissions
        $reportsQuery = CommitteeReport::query();
        if ($user) {
            if ($user->hasRole('super admin')) {
                $reportsQuery->whereIn('status', ['draft', 'submitted', 'approved']);
            } elseif ($user->hasRole('rsc')) {
                $reportsQuery->whereIn('status', ['submitted', 'approved']);
            } elseif ($user->hasRole('ServiceBody') || $user->hasRole('gsr')) {
                $reportsQuery->where('status', 'approved');
            } else {
                $committee = ServiceCommittee::where('user_id', $user->id)
                    ->orWhere('email', $user->email)
                    ->first();
                if ($committee) {
                    $reportsQuery->where('service_committee_id', $committee->id);
                } else {
                    $reportsQuery->where('status', 'approved');
                }
            }
        } else {
            $reportsQuery->where('status', 'approved');
        }
        $reportsCount = $reportsQuery->count();

        // Calculate customFormsCount based on permissions
        $customFormsQuery = CustomForm::query();
        if ($user && ($user->hasRole('super admin') || $user->hasRole('rsc'))) {
            // RSC/super admins can see all forms
        } elseif ($user) {
            $customFormsQuery->where('user_id', $user->id);
        } else {
            $customFormsQuery->where('id', 0);
        }
        $customFormsCount = $customFormsQuery->count();

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

            'meetings'          => $meetings,
            'serviceBodies'     => $serviceBodies,
            'cities'            => $cities,
            'groups'            => $groups,
            'usersCount'        => $usersCount,
            'transactions'      => $transactions,
            'agendas'           => $agendas,
            'reportsCount'      => $reportsCount,
            'customFormsCount'  => $customFormsCount,
        ]);
    }
}
