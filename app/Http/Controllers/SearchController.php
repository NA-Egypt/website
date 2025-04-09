<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Group;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function city($cityId)
    {
        // Fetch the city with its neighborhoods and groups
        $city = City::with('neighborhoods.groups')->findOrFail($cityId);

        // Flatten the groups from all neighborhoods
        $groups = $city->neighborhoods->flatMap(function ($neighborhood) {
            return $neighborhood->groups;
        });

        // Return a view or JSON response with the groups
        return view('searches.city', [
            'city' => $city,
            'groups' => $groups
        ]);
    }

    public function groupMeetings($groupId)
    {
        // Fetch the group with its meetings
        $group = Group::with('meetings')->findOrFail($groupId);

        // Get the meetings associated with the group
        $meetings = $group->meetings;

        // Return a view with the meetings
        return view('searches.meeting', [
            'group' => $group,
            'meetings' => $meetings
        ]);
    }

}
