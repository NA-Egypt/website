<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use App\Services\MeetingFilterService;
use App\Models\Day;
use App\Models\ServiceBody;
use App\Models\Group;
use App\Models\Neighborhood;

class MeetingFilterController extends Controller
{
    protected $meetingFilterService;

    public function __construct(MeetingFilterService $meetingFilterService)
    {
        $this->meetingFilterService = $meetingFilterService;
    }

    public function filterMeetings(Request $request)
    {

        // Fetch available filter options
        $days = Day::all();
        $serviceBodies = ServiceBody::all();
        $groups = Group::all();
        $neighborhoods = Neighborhood::all();
        $cities = City::all();

        // Apply filters
//        $filters = $request->only(['day', 'serviceBody', 'group', 'neighborhood', 'type', 'city']);
//        $meetings = $this->meetingFilterService->filterMeetings($filters);

        $filters = $request->only(['day', 'serviceBody', 'group', 'neighborhood', 'type', 'city']);
        // Add debug logging
        logger('Received filters:', [
            'raw' => $filters,
            'group_length' => isset($filters['group']) ? strlen($filters['group']) : null,
            'group_hex' => isset($filters['group']) ? bin2hex($filters['group']) : null
        ]);
        $meetings = $this->meetingFilterService->filterMeetings($filters);

        return view('frontend.meetings', compact('meetings', 'days', 'serviceBodies', 'groups', 'neighborhoods', 'cities'));
    }
}
