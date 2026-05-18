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

    /**
     * Display the specified resource.
     */
    public function show(Agenda $agenda)
    {
        Gate::authorize('view', $agenda->group);

        return view('agenda.show', compact('agenda'));
    }
    
    /**
     * Export the specified agenda to PDF.
     */
    public function exportPdf(Agenda $agenda)
    {
        Gate::authorize('view', $agenda->group);

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
}
