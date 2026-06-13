<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Day;
use App\Models\ServiceBody;
use App\Models\Group;
use App\Models\City;
use App\Models\Neighborhood;
use App\Services\MeetingFilterService;

class MeetingFilter extends Component
{
    #[Url] public $day = '';
    #[Url] public $serviceBody = '';
    #[Url] public $group = '';
    #[Url] public $city = '';
    #[Url] public $neighborhood = '';
    #[Url] public $type = '';
    #[Url(except: '')] public $search = '';
    #[Url] public $virtualOnly = false;
    #[Url] public $englishOnly = false;
    #[Url] public $businessMeetingsOnly = false;

    public function updatedVirtualOnly($value)
    {
        if ($value) {
            $this->englishOnly = false;
            $this->businessMeetingsOnly = false;
        }
    }

    public function updatedEnglishOnly($value)
    {
        if ($value) {
            $this->virtualOnly = false;
            $this->businessMeetingsOnly = false;
        }
    }

    public function updatedBusinessMeetingsOnly($value)
    {
        if ($value) {
            $this->virtualOnly = false;
            $this->englishOnly = false;
            // Clear other search criteria if necessary or let them filter
        }
    }

    public function toggleVirtualOnly()
    {
        $this->virtualOnly = !$this->virtualOnly;
        if ($this->virtualOnly) {
            $this->englishOnly = false;
            $this->businessMeetingsOnly = false;
        }
    }

    public function toggleEnglishOnly()
    {
        $this->englishOnly = !$this->englishOnly;
        if ($this->englishOnly) {
            $this->virtualOnly = false;
            $this->businessMeetingsOnly = false;
        }
    }

    public function toggleBusinessMeetingsOnly()
    {
        $this->businessMeetingsOnly = !$this->businessMeetingsOnly;
        if ($this->businessMeetingsOnly) {
            $this->virtualOnly = false;
            $this->englishOnly = false;
        }
    }

    public function updatedCity($value)
    {
        if ($value !== '') {
            $this->day = '';
            $this->serviceBody = '';
            $this->neighborhood = '';
            $this->group = '';
            $this->type = '';
            $this->search = '';
            $this->virtualOnly = false;
            $this->englishOnly = false;
            $this->businessMeetingsOnly = false;
        } else {
            // When City changes, clear the neighborhood since the available list changes
            $this->neighborhood = '';
            $this->group = '';
        }
    }

    public function updatedNeighborhood($value)
    {
        if ($value !== '') {
            $this->day = '';
            $this->serviceBody = '';
            $this->city = '';
            $this->group = '';
            $this->type = '';
            $this->search = '';
            $this->virtualOnly = false;
            $this->englishOnly = false;
            $this->businessMeetingsOnly = false;
        } else {
            $this->group = '';
        }
    }

    public function updatedGroup($value)
    {
        if ($value !== '') {
            $this->day = '';
            $this->serviceBody = '';
            $this->city = '';
            $this->neighborhood = '';
            $this->type = '';
            $this->search = '';
            $this->virtualOnly = false;
            $this->englishOnly = false;
            $this->businessMeetingsOnly = false;
        }
    }

    public function updatedServiceBody($value)
    {
        if ($value !== '') {
            $this->day = '';
            $this->group = '';
            $this->city = '';
            $this->neighborhood = '';
            $this->type = '';
            $this->search = '';
            $this->virtualOnly = false;
            $this->englishOnly = false;
            $this->businessMeetingsOnly = false;
        }
    }

    public function render(MeetingFilterService $filterService)
    {
        $days = Day::withCount(['meetings' => fn($q) => $q->notMonthlyRecurrent()])->get();
        $serviceBodies = ServiceBody::withCount(['meetings' => fn($q) => $q->notMonthlyRecurrent()])->get();
        
        // Base Groups Query
        $groupsQuery = Group::withCount(['meetings' => fn($q) => $q->notMonthlyRecurrent()]);
        
        $field = app()->getLocale() === 'ar' ? 'ar_name' : 'en_name';

        // Filter groups available based on selected city or neighborhood
        if ($this->city || $this->neighborhood) {
            
            if ($this->neighborhood) {
                $groupsQuery->whereHas('neighborhood', fn($q) => $q->where($field, $this->neighborhood));
            } elseif ($this->city) {
                $groupsQuery->whereHas('neighborhood.city', fn($q) => $q->where($field, $this->city));
            }
        }
        $groups = $groupsQuery->get();
        
        $neighborhoodsQuery = Neighborhood::withCount(['meetings' => fn($q) => $q->notMonthlyRecurrent()]);
        if ($this->city) {
            $neighborhoodsQuery->whereHas('city', fn($q) => $q->where($field, $this->city));
        }
        $neighborhoods = $neighborhoodsQuery->get();

        $cities = City::leftJoin('neighborhoods', 'cities.id', '=', 'neighborhoods.city_id')
            ->leftJoin('groups', 'neighborhoods.id', '=', 'groups.neighborhood_id')
            ->leftJoin('meetings', function($join) {
                $join->on('groups.id', '=', 'meetings.group_id')
                     ->where(function($q) {
                         $q->whereNull('meetings.recurrence')
                           ->orWhere(function($sub) {
                               foreach (['1st', '2nd', '3rd', '4th', '5th', 'last'] as $item) {
                                   $sub->where('meetings.recurrence', 'not like', '%"' . $item . '"%');
                               }
                           });
                     })
                     ->whereNotExists(function($sub) {
                         $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                             ->from('meeting_topic')
                             ->join('topics', 'meeting_topic.topic_id', '=', 'topics.id')
                             ->whereRaw('meeting_topic.meeting_id = meetings.id')
                             ->where('topics.en_name', 'Group Business Meeting');
                     });
            })
            ->select('cities.id', 'cities.ar_name', 'cities.en_name', \Illuminate\Support\Facades\DB::raw('COUNT(meetings.id) as meetings_count'))
            ->groupBy('cities.id', 'cities.ar_name', 'cities.en_name')
            ->get();
            
        $openCount = \App\Models\Meeting::where('type', 'open')->notMonthlyRecurrent()->count();
        $closedCount = \App\Models\Meeting::where('type', 'closed')->notMonthlyRecurrent()->count();

        $filters = [
            'day' => $this->day,
            'serviceBody' => $this->serviceBody,
            'group' => $this->group,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'type' => $this->type,
            'search' => $this->search,
            'virtualOnly' => $this->virtualOnly,
            'englishOnly' => $this->englishOnly,
            'businessMeetingsOnly' => $this->businessMeetingsOnly,
        ];

        $meetings = $filterService->filterMeetings($filters);

        return view('livewire.meeting-filter', [
            'meetings' => $meetings,
            'days' => $days,
            'serviceBodies' => $serviceBodies,
            'groups' => $groups,
            'cities' => $cities,
            'neighborhoods' => $neighborhoods,
            'openCount' => $openCount,
            'closedCount' => $closedCount,
            'businessMeetingsOnly' => $this->businessMeetingsOnly,
        ]);
    }
}
