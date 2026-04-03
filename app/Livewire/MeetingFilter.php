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

    public function updatedCity()
    {
        // When City changes, clear the neighborhood since the available list changes
        $this->neighborhood = '';
        $this->group = '';
    }

    public function updatedNeighborhood()
    {
        $this->group = '';
    }

    public function render(MeetingFilterService $filterService)
    {
        $days = Day::all();
        $serviceBodies = ServiceBody::all();
        
        // Base Groups Query
        $groupsQuery = Group::query();
        
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
        
        $neighborhoodsQuery = Neighborhood::query();
        if ($this->city) {
            $neighborhoodsQuery->whereHas('city', fn($q) => $q->where($field, $this->city));
        }
        $neighborhoods = $neighborhoodsQuery->get();

        $cities = City::all();

        $filters = [
            'day' => $this->day,
            'serviceBody' => $this->serviceBody,
            'group' => $this->group,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'type' => $this->type,
            'search' => $this->search,
        ];

        $meetings = $filterService->filterMeetings($filters);

        return view('livewire.meeting-filter', [
            'meetings' => $meetings,
            'days' => $days,
            'serviceBodies' => $serviceBodies,
            'groups' => $groups,
            'cities' => $cities,
            'neighborhoods' => $neighborhoods,
        ]);
    }
}
