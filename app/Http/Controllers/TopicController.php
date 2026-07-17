<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;

use App\Traits\PaginatesDataTables;

class TopicController extends Controller
{
    use PaginatesDataTables;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $query = Topic::query();
            $topics = $this->paginateDataTable($query, $request, ['ar_name', 'en_name', 'description']);
            return response()->json($topics);
        }

        $topics = collect();
        return view('topic.index', ['topics'=>$topics]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('topic.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = request()->validate([
            'ar_name'       => 'required|min:2',
            'en_name'       => 'required|min:2',
            'description'   => 'nullable'
        ]);

        Topic::create($fields);

        return redirect()->route('topic.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Topic $topic)
    {
        return view('topic.edit', ['topic'=>$topic]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Topic $topic)
    {
        $fields = request()->validate([
            'ar_name'       => 'required|min:2',
            'en_name'       => 'required|min:2',
            'description'   => 'nullable'
        ]);

        $topic->update($fields);

        return redirect()->route('topic.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topic $topic)
    {
        $topic->delete();

        return redirect()->route('topic.index');
    }
}
