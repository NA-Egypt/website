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
    #[Url] public $recurrence = 'weekly';

    public function mount()
    {
        if (empty($this->day)) {
            $englishDay = now()->format('l');
            $today = Day::where('en_name', $englishDay)->first();
            if ($today) {
                $this->day = app()->getLocale() === 'ar' ? $today->ar_name : $today->en_name;
            } else {
                $this->day = $englishDay;
            }
        } else {
            $this->normalizeFilters();
        }
    }

    public function clearFilters()
    {
        $englishDay = now()->format('l');
        $today = Day::where('en_name', $englishDay)->first();
        if ($today) {
            $this->day = app()->getLocale() === 'ar' ? $today->ar_name : $today->en_name;
        } else {
            $this->day = $englishDay;
        }

        $this->serviceBody = '';
        $this->group = '';
        $this->city = '';
        $this->neighborhood = '';
        $this->type = '';
        $this->search = '';
        $this->virtualOnly = false;
        $this->englishOnly = false;
        $this->businessMeetingsOnly = false;
        $this->recurrence = 'weekly';
    }

    private function normalizeFilters()
    {
        $locale = app()->getLocale();
        $targetField = $locale === 'ar' ? 'ar_name' : 'en_name';

        if (!empty($this->day) && $this->day !== 'all') {
            $dayObj = Day::where('ar_name', $this->day)->orWhere('en_name', $this->day)->first();
            if ($dayObj) {
                $this->day = $dayObj->$targetField;
            }
        }

        if (!empty($this->city)) {
            $cityObj = City::where('ar_name', $this->city)->orWhere('en_name', $this->city)->first();
            if ($cityObj) {
                $this->city = $cityObj->$targetField;
            }
        }

        if (!empty($this->group)) {
            $groupObj = Group::where('ar_name', $this->group)->orWhere('en_name', $this->group)->first();
            if ($groupObj) {
                $this->group = $groupObj->$targetField;
            }
        }

        if (!empty($this->serviceBody)) {
            $sbObj = ServiceBody::where('ar_name', $this->serviceBody)->orWhere('en_name', $this->serviceBody)->first();
            if ($sbObj) {
                $this->serviceBody = $sbObj->$targetField;
            }
        }

        if (!empty($this->neighborhood)) {
            $nObj = Neighborhood::where('ar_name', $this->neighborhood)->orWhere('en_name', $this->neighborhood)->first();
            if ($nObj) {
                $this->neighborhood = $nObj->$targetField;
            }
        }
    }

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
            $this->day = '';
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
            $this->day = '';
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
            $this->recurrence = 'weekly';
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
            $this->recurrence = 'weekly';
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
            $this->recurrence = 'weekly';
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
            $this->recurrence = 'weekly';
        }
    }

    public function render(MeetingFilterService $filterService)
    {
        $this->normalizeFilters();
        
        $days = \Illuminate\Support\Facades\Cache::remember('meetings_filter_days', 3600, function () {
            return Day::withCount(['meetings' => fn($q) => $q->notMonthlyRecurrent()])->get();
        });

        $serviceBodies = \Illuminate\Support\Facades\Cache::remember('meetings_filter_service_bodies', 3600, function () {
            return ServiceBody::withCount(['meetings' => fn($q) => $q->notMonthlyRecurrent()])->get();
        });

        $field = app()->getLocale() === 'ar' ? 'ar_name' : 'en_name';

        // Base Groups Query
        $groupsQuery = Group::withCount(['meetings' => fn($q) => $q->notMonthlyRecurrent()]);
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

        $cities = \Illuminate\Support\Facades\Cache::remember('meetings_filter_cities', 3600, function () {
            return City::leftJoin('neighborhoods', 'cities.id', '=', 'neighborhoods.city_id')
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
        });

        $openCount = \Illuminate\Support\Facades\Cache::remember('meetings_filter_open_count', 3600, function () {
            return \App\Models\Meeting::where('type', 'open')->notMonthlyRecurrent()->count();
        });

        $closedCount = \Illuminate\Support\Facades\Cache::remember('meetings_filter_closed_count', 3600, function () {
            return \App\Models\Meeting::where('type', 'closed')->notMonthlyRecurrent()->count();
        });

        $onlineCount = \Illuminate\Support\Facades\Cache::remember('meetings_filter_online_count', 3600, function () {
            return \App\Models\Meeting::where(function ($bigQ) {
                $bigQ->whereHas('group', function ($q) {
                    $q->whereIn('group_type', ['اونلاين', 'اون لاين', 'online'])
                      ->where(function ($sub) {
                          $sub->whereNull('location')
                              ->orWhere(function ($sub2) {
                                  $sub2->where('location', 'not like', '%map%')
                                       ->where('location', 'not like', '%goo.gl%');
                              });
                      });
                })->orWhereNotNull('direct_online_group_id');
            })->notMonthlyRecurrent()->count();
        });

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
            'recurrence' => $this->recurrence,
        ];

        $meetings = $filterService->filterMeetings($filters);

        $recurrences = collect([
            (object) ['id' => 'weekly', 'en_name' => 'Weekly Meetings', 'ar_name' => 'اجتماعات أسبوعية'],
            (object) ['id' => 'monthly', 'en_name' => 'Monthly Meetings', 'ar_name' => 'اجتماعات شهرية'],
            (object) ['id' => '1st', 'en_name' => '1st Week', 'ar_name' => 'الأسبوع الأول'],
            (object) ['id' => '2nd', 'en_name' => '2nd Week', 'ar_name' => 'الأسبوع الثاني'],
            (object) ['id' => '3rd', 'en_name' => '3rd Week', 'ar_name' => 'الأسبوع الثالث'],
            (object) ['id' => '4th', 'en_name' => '4th Week', 'ar_name' => 'الأسبوع الرابع'],
            (object) ['id' => '5th', 'en_name' => '5th Week', 'ar_name' => 'الأسبوع الخامس'],
            (object) ['id' => 'last', 'en_name' => 'Last Week', 'ar_name' => 'الأسبوع الأخير'],
        ]);

        return view('livewire.meeting-filter', [
            'day' => $this->day,
            'serviceBody' => $this->serviceBody,
            'group' => $this->group,
            'city' => $this->city,
            'neighborhood' => $this->neighborhood,
            'type' => $this->type,
            'search' => $this->search,
            'virtualOnly' => $this->virtualOnly,
            'englishOnly' => $this->englishOnly,
            'businessMeetingsOnly' => $this->businessMeetingsOnly,
            'recurrence' => $this->recurrence,
            'meetings' => $meetings,
            'days' => $days,
            'serviceBodies' => $serviceBodies,
            'groups' => $groups,
            'cities' => $cities,
            'neighborhoods' => $neighborhoods,
            'openCount' => $openCount,
            'closedCount' => $closedCount,
            'onlineCount' => $onlineCount,
            'recurrences' => $recurrences,
        ]);
    }
}
