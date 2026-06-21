<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceBodyAgenda;
use App\Http\Resources\ServiceBodyAgendaResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ServiceBodyAgendaController extends Controller
{
    protected function isRsc($user)
    {
        if (!$user) {
            return false;
        }
        return $user->hasRole('super admin') || $user->hasRole('rsc');
    }

    protected function isRestrictedConsumer($user)
    {
        if (!$user) {
            return true;
        }
        if ($user->hasRole('super admin') || $user->hasRole('rsc') || $user->hasRole('ServiceBody')) {
            return false;
        }
        return true;
    }

    protected function isAgendaVisibleToUser(ServiceBodyAgenda $agenda, $user)
    {
        if ($this->isRsc($user)) {
            return true;
        }

        if ($user && $user->hasRole('ServiceBody') && $user->service_body_id === $agenda->service_body_id) {
            return true;
        }

        if ($agenda->status !== 'approved') {
            return false;
        }

        if ($agenda->is_exceptional) {
            return true;
        }

        $mDate = Carbon::parse($agenda->meeting_date);
        if ($mDate->day <= 10) {
            $releaseDate = Carbon::create($mDate->year, $mDate->month, 10);
        } else {
            $releaseDate = Carbon::create($mDate->year, $mDate->month, 10)->addMonth();
        }

        return now()->greaterThanOrEqualTo($releaseDate);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::guard('sanctum')->user() ?? auth()->user();
        $isRsc = $this->isRsc($user);
        $query = ServiceBodyAgenda::with('serviceBody');

        $daySql = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite'
            ? "cast(strftime('%d', meeting_date) as integer)"
            : "DAY(meeting_date)";

        if ($this->isRestrictedConsumer($user)) {
            // Only approved and released
            $query->where('status', 'approved');
            $query->where(function($q) use ($daySql) {
                $q->where('is_exceptional', true)
                  ->orWhere(function($subQ) use ($daySql) {
                      $now = now();
                      $subQ->where(function($inner) use ($now, $daySql) {
                          $inner->whereRaw("{$daySql} <= 10")
                                ->where(function($dateQ) use ($now) {
                                    $dateQ->whereYear('meeting_date', '<', $now->year)
                                          ->orWhere(function($yQ) use ($now) {
                                              $yQ->whereYear('meeting_date', $now->year)
                                                 ->where(function($mQ) use ($now) {
                                                     $mQ->whereMonth('meeting_date', '<', $now->month)
                                                        ->orWhere(function($dQ) use ($now) {
                                                            $dQ->whereMonth('meeting_date', $now->month)
                                                               ->whereDay('meeting_date', '<=', $now->day);
                                                        });
                                                 });
                                          });
                                  });
                      })->orWhere(function($inner) use ($now, $daySql) {
                          $inner->whereRaw("{$daySql} > 10")
                                ->where(function($dateQ) use ($now) {
                                    $dateQ->whereYear('meeting_date', '<', $now->year)
                                          ->orWhere(function($yQ) use ($now) {
                                              $yQ->whereYear('meeting_date', $now->year)
                                                 ->where(function($mQ) use ($now) {
                                                     $mQ->where('meeting_date', '<', Carbon::now()->startOfMonth()->subMonth()->day(10));
                                                 });
                                          });
                                  });
                      });
                  });
            });
        } elseif (!$isRsc) {
            $sbId = $user ? $user->service_body_id : 0;
            // ServiceBody role: sees all of their own, but others' only approved + released
            $query->where(function($q) use ($sbId, $daySql) {
                if ($sbId) {
                    $q->where('service_body_id', $sbId);
                }
                $q->orWhere(function($otherQ) use ($sbId, $daySql) {
                    if ($sbId) {
                        $otherQ->where('service_body_id', '!=', $sbId);
                    }
                    $otherQ->where('status', 'approved');
                    $otherQ->where(function($releaseQ) use ($daySql) {
                        $releaseQ->where('is_exceptional', true)
                                 ->orWhere(function($subQ) use ($daySql) {
                                     $now = now();
                                     $subQ->where(function($inner) use ($now, $daySql) {
                                         $inner->whereRaw("{$daySql} <= 10")
                                               ->where(function($dateQ) use ($now) {
                                                   $dateQ->whereYear('meeting_date', '<', $now->year)
                                                         ->orWhere(function($yQ) use ($now) {
                                                             $yQ->whereYear('meeting_date', $now->year)
                                                                ->where(function($mQ) use ($now) {
                                                                    $mQ->whereMonth('meeting_date', '<', $now->month)
                                                                       ->orWhere(function($dQ) use ($now) {
                                                                           $dQ->whereMonth('meeting_date', $now->month)
                                                                              ->whereDay('meeting_date', '<=', $now->day);
                                                                       });
                                                                });
                                                         });
                                                 });
                                     })->orWhere(function($inner) use ($now, $daySql) {
                                         $inner->whereRaw("{$daySql} > 10")
                                               ->where(function($dateQ) use ($now) {
                                                   $dateQ->whereYear('meeting_date', '<', $now->year)
                                                         ->orWhere(function($yQ) use ($now) {
                                                             $yQ->where('meeting_date', '<', Carbon::now()->startOfMonth()->subMonth()->day(10));
                                                         });
                                               });
                                     });
                                 });
                    });
                });
            });
        }

        return ServiceBodyAgendaResource::collection($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'service_body_id' => 'required|exists:service_bodies,id',
            'meeting_date' => 'required|date',
            'sections' => 'required|array|min:1',
            'sections.*.headline' => 'nullable|string|max:255',
            'sections.*.content' => 'required|string',
            'groups_joined' => 'nullable|array',
            'groups_joined.*' => 'nullable|string|max:255',
            'status' => 'required|in:draft,submitted,approved',
            'is_exceptional' => 'nullable|boolean',
        ]);

        $agenda = ServiceBodyAgenda::create([
            'service_body_id' => $validatedData['service_body_id'],
            'agenda_date' => now()->toDateString(),
            'meeting_date' => $validatedData['meeting_date'],
            'groups_joined' => isset($validatedData['groups_joined']) ? array_filter($validatedData['groups_joined']) : [],
            'body' => $validatedData['sections'],
            'status' => $validatedData['status'],
            'is_exceptional' => $request->boolean('is_exceptional'),
        ]);

        return new ServiceBodyAgendaResource($agenda);
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceBodyAgenda $serviceBodyAgenda)
    {
        $user = Auth::guard('sanctum')->user() ?? auth()->user();
        if (!$this->isAgendaVisibleToUser($serviceBodyAgenda, $user)) {
            abort(403, 'Unauthorized');
        }

        return new ServiceBodyAgendaResource($serviceBodyAgenda);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceBodyAgenda $serviceBodyAgenda)
    {
        $validatedData = $request->validate([
            'service_body_id' => 'required|exists:service_bodies,id',
            'meeting_date' => 'required|date',
            'sections' => 'required|array|min:1',
            'sections.*.headline' => 'nullable|string|max:255',
            'sections.*.content' => 'required|string',
            'groups_joined' => 'nullable|array',
            'groups_joined.*' => 'nullable|string|max:255',
            'status' => 'required|in:draft,submitted,approved',
            'is_exceptional' => 'nullable|boolean',
        ]);

        $serviceBodyAgenda->update([
            'service_body_id' => $validatedData['service_body_id'],
            'meeting_date' => $validatedData['meeting_date'],
            'groups_joined' => isset($validatedData['groups_joined']) ? array_filter($validatedData['groups_joined']) : [],
            'body' => $validatedData['sections'],
            'status' => $validatedData['status'],
            'is_exceptional' => $request->boolean('is_exceptional'),
        ]);

        return new ServiceBodyAgendaResource($serviceBodyAgenda);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceBodyAgenda $serviceBodyAgenda)
    {
        $serviceBodyAgenda->delete();
        return response()->json(null, 204);
    }
}
