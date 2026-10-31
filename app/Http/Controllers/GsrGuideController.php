<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GsrGuideController extends Controller
{
    /**
     * Display the GSR Meetings and Service Guide.
     */
    public function index(Request $request)
    {
        return view('guides.gsr-meetings');
    }
}

