<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommitteeReportsGuideController extends Controller
{
    /**
     * Display the Committee Reports and Workgroups Guide.
     */
    public function index(Request $request)
    {
        return view('guides.reports-workgroups');
    }
}

