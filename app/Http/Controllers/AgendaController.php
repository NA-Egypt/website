<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use Mpdf\Mpdf;

class AgendaController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $groupId = $request->query('group_id');
        $group = Group::findOrFail($groupId);

        // Ensure user can view the group to create an agenda for it.
        Gate::authorize('view', $group);

        return view('agenda.create', compact('group'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $group = Group::findOrFail($request->input('group_id'));
        Gate::authorize('view', $group);

        $validatedData = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'meetings_per_week' => 'nullable|integer|min:0',
            'agenda_date' => 'required|date',
            'service_position' => 'required|string|in:Open Position,Alt. GSR,GSR',
            'submitter_name' => 'nullable|string|max:255',
            'alt_gsr_position' => 'nullable|string|in:Open Position,Alt. GSR',
            'alt_gsr_name' => 'nullable|string|max:255',
            'new_comers' => 'nullable|integer|min:0',
            'open_positions' => 'nullable|string',
            'next_business_meeting' => 'nullable|date',
            'recovery_meetings_changes' => 'nullable|boolean',
            'recovery_atmosphere' => 'nullable|string',
            'trusted_servants' => 'nullable|string',
            'financial_issues' => 'nullable|string',
            'other_topics' => 'nullable|string',
        ]);

        if (!isset($validatedData['recovery_meetings_changes'])) {
            $validatedData['recovery_meetings_changes'] = false;
        }

        Agenda::create($validatedData);

        return redirect()->route('group.show', $group->id)->with('success', __('messages.agenda_created_successfully'));
    }

    protected function isAuthorized($user)
    {
        if (!$user) {
            return false;
        }
        return $user->hasRole('super admin') || 
               in_array(strtolower($user->email), ['rsc@naegypt.org', 'rcp@naegypt.org', 'rvcp@naegypt.org']);
    }

    protected function canAccessArchive($user)
    {
        if (!$user) {
            return false;
        }
        return $this->isAuthorized($user) || $user->hasRole('ServiceBody');
    }

    /**
     * Display the specified resource.
     */
    public function show(Agenda $agenda)
    {
        if (!$this->isAuthorized(auth()->user())) {
            Gate::authorize('view', $agenda->group);
        }

        return view('agenda.show', compact('agenda'));
    }
    
    /**
     * Export the specified agenda to PDF.
     */
    public function exportPdf(Agenda $agenda)
    {
        if (!$this->isAuthorized(auth()->user())) {
            Gate::authorize('view', $agenda->group);
        }

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'directionality' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
            'fontDir' => array_merge($fontDirs, [resource_path('fonts')]),
            'fontdata' => $fontData + [
                'amiri' => [
                    'R' => 'Amiri-Regular.ttf',
                ],
                'cairo' => [
                    'R' => 'Cairo-Regular.ttf',
                ],
            ],
            'default_font' => 'xbriyaz',
        ]);
        
        $mpdf->autoArabic = true;
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $agendas = collect([$agenda]);
        $html = view('pdf.agenda', compact('agendas'))->render();
        $mpdf->WriteHTML($html);

        $filename = 'agenda_' . $agenda->id . '.pdf';
        
        return response($mpdf->Output($filename, 'S'), 200)
               ->header('Content-Type', 'application/pdf')
               ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
     }

    /**
     * Display the archive of groups' agendas.
     */
    public function archive(Request $request)
    {
        $user = auth()->user();
        if (!$this->canAccessArchive($user)) {
            abort(403, 'Unauthorized');
        }

        $query = Agenda::with('group');

        if ($user->hasRole('ServiceBody') && $user->service_body_id) {
            $query->whereHas('group', function ($q) use ($user) {
                $q->where('service_body_id', $user->service_body_id);
            });
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('submitter_name', 'like', "%$search%")
                  ->orWhere('recovery_atmosphere', 'like', "%$search%")
                  ->orWhere('other_topics', 'like', "%$search%")
                  ->orWhereHas('group', function ($gQ) use ($search) {
                      $gQ->where('ar_name', 'like', "%$search%")
                         ->orWhere('en_name', 'like', "%$search%");
                  });
            });
        }

        if ($request->has('group_id') && $request->group_id != '') {
            $query->where('group_id', $request->group_id);
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $query->where('agenda_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->where('agenda_date', '<=', $request->end_date);
        }

        $agendas = $query->orderBy('agenda_date', 'desc')->get();

        // Group by Year, then by Month
        $archive = $agendas->groupBy(function ($agenda) {
            return \Carbon\Carbon::parse($agenda->agenda_date)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy(function ($agenda) {
                return \Carbon\Carbon::parse($agenda->agenda_date)->format('m');
            });
        });

        if ($user->hasRole('ServiceBody') && $user->service_body_id) {
            $groups = Group::where('service_body_id', $user->service_body_id)->get();
        } else {
            $groups = Group::all();
        }

        return view('agenda.archive', compact('archive', 'groups'));
    }

    /**
     * Export multiple agendas to PDF.
     */
    public function exportMultipleAgendasPdf(Request $request)
    {
        $user = auth()->user();
        if (!$this->canAccessArchive($user)) {
            abort(403, 'Unauthorized');
        }

        $agendaIds = $request->input('agenda_ids', []);
        
        if (empty($agendaIds)) {
            return back()->with('error', __('messages.no_agendas_selected') ?? 'No agendas selected for export.');
        }

        $query = Agenda::whereIn('id', $agendaIds);
        if ($user->hasRole('ServiceBody') && $user->service_body_id) {
            $query->whereHas('group', function ($q) use ($user) {
                $q->where('service_body_id', $user->service_body_id);
            });
        }

        $agendas = $query->with('group')->orderBy('agenda_date', 'desc')->get();

        if ($agendas->isEmpty()) {
            return back()->with('error', __('messages.no_agendas_selected') ?? 'No valid agendas found for export.');
        }

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'directionality' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
            'fontDir' => array_merge($fontDirs, [resource_path('fonts')]),
            'fontdata' => $fontData + [
                'amiri' => [
                    'R' => 'Amiri-Regular.ttf',
                ],
                'cairo' => [
                    'R' => 'Cairo-Regular.ttf',
                ],
            ],
            'default_font' => 'xbriyaz',
        ]);
        
        $mpdf->autoArabic = true;
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $html = view('pdf.agenda', compact('agendas'))->render();
        $mpdf->WriteHTML($html);

        $filename = 'agendas_export_' . date('Y-m-d') . '.pdf';
        
        return response($mpdf->Output($filename, 'S'), 200)
               ->header('Content-Type', 'application/pdf')
               ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
