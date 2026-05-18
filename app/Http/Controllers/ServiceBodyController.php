<?php

namespace App\Http\Controllers;
use App\Http\Requests\ServiceBodyRequest;

use App\Models\Day;
use App\Models\ServiceBody;
use Illuminate\Http\Request;

use App\Models\Agenda;
use Mpdf\Mpdf;

class ServiceBodyController extends Controller
{

    
    public function index() {

        $sb = ServiceBody::all();
        return view('serviceBody.index', ['sb' => $sb]);
    }

    public function create() {

        $days = Day::all();
        return view('serviceBody.create', ['days'=>$days]);
    }

    public function store(ServiceBodyRequest $request) {

        $validatedData = $request->validated();

        ServiceBody::create($validatedData);

        return redirect()->route('serviceBody.index');

    }

    public function edit(ServiceBody $serviceBody) {

        $days = Day::all();
        return view('serviceBody.edit', ['serviceBody'=>$serviceBody, 'days'=>$days]);
    }

    public function update(ServiceBodyRequest $request, ServiceBody $serviceBody) {

        $fields = $request->validated();

        $serviceBody->update($fields);

        return redirect()->route('serviceBody.index');
        
    }

    public function destroy(ServiceBody $serviceBody) {
        
        $serviceBody->delete();

        return redirect()->route('serviceBody.index');
    }

    public function agendas(ServiceBody $serviceBody) {
        // Eager load groups and agendas to avoid N+1 problem
        $serviceBody->load('agendas.group');
        
        return view('serviceBody.agendas', [
            'serviceBody' => $serviceBody,
            'agendas' => $serviceBody->agendas()->orderBy('agenda_date', 'desc')->get()
        ]);
    }

    public function exportAgendasPdf(Request $request, ServiceBody $serviceBody) {
        $agendaIds = $request->input('agenda_ids', []);
        
        if (empty($agendaIds)) {
            return back()->with('error', __('messages.no_agendas_selected') ?? 'No agendas selected for export.');
        }

        $agendas = Agenda::whereIn('id', $agendaIds)->whereHas('group', function ($query) use ($serviceBody) {
            $query->where('service_body_id', $serviceBody->id);
        })->with('group')->orderBy('agenda_date', 'desc')->get();

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
