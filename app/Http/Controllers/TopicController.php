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
            $query = Topic::withCount('meetings');

            if ($request->filled('has_meetings')) {
                if ($request->input('has_meetings') === 'yes') {
                    $query->has('meetings');
                } elseif ($request->input('has_meetings') === 'no') {
                    $query->doesntHave('meetings');
                }
            }

            $topics = $this->paginateDataTable($query, $request, ['ar_name', 'en_name', 'description']);

            $locale = app()->getLocale();
            $topics->getCollection()->transform(function($t) use ($locale) {
                $t->primary_name = $locale === 'ar' ? ($t->ar_name ?: $t->en_name) : ($t->en_name ?: $t->ar_name);
                $t->secondary_name = $locale === 'ar' ? $t->en_name : $t->ar_name;
                $t->meetings_count = $t->meetings_count ?? 0;
                $t->description_text = $t->description;
                $t->is_business = mb_stripos($t->en_name, 'Business') !== false;
                return $t;
            });

            return response()->json($topics);
        }

        $kpiStats = [
            'total_topics'    => Topic::count(),
            'total_meetings'  => \App\Models\Meeting::whereNotNull('topic_id')->count(),
            'business_topics' => Topic::where('en_name', 'like', '%Business%')->count(),
        ];

        return view('topic.index', [
            'topics'   => collect(),
            'kpiStats' => $kpiStats
        ]);
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
    public function destroy(Topic $topic, Request $request)
    {
        $topic->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('messages.topic_deleted_success')]);
        }

        return redirect()->route('topic.index')->with('success', __('messages.topic_deleted_success'));
    }
}
