<?php

namespace App\Http\Controllers;
use App\Http\Requests\ServiceBodyRequest;

use App\Models\Day;
use App\Models\ServiceBody;
use Illuminate\Http\Request;

use App\Models\Agenda;
use App\Services\MpdfService;

use App\Traits\PaginatesDataTables;

class ServiceBodyController extends Controller
{
    use PaginatesDataTables;
    
    public function index(Request $request) {
        if ($request->wantsJson() || $request->ajax()) {
            $query = ServiceBody::with('day')->withCount('groups');

            if ($request->filled('day_id')) {
                $query->where('day_id', $request->input('day_id'));
            }

            $sb = $this->paginateDataTable($query, $request, ['ar_name', 'en_name', 'email', 'helpline']);
            
            $locale = app()->getLocale();
            $sb->getCollection()->transform(function($s) use ($locale) {
                $s->primary_name = $locale === 'ar' ? ($s->ar_name ?: $s->en_name) : ($s->en_name ?: $s->ar_name);
                $s->secondary_name = $locale === 'ar' ? $s->en_name : $s->ar_name;
                $s->day_name = $s->day ? ($locale === 'ar' ? $s->day->ar_name : $s->day->en_name) : '-';
                $s->from_time = $s->formatted_start_time;
                $s->to_time = $s->formatted_end_time;
                $s->groups_count = $s->groups_count ?? 0;
                $s->location_url = $s->location;
                return $s;
            });

            return response()->json($sb);
        }

        $kpiStats = [
            'total_service_bodies' => ServiceBody::count(),
            'total_groups'         => \App\Models\Group::whereNotNull('service_body_id')->count(),
            'total_meetings'       => \App\Models\Meeting::whereNotNull('group_id')->count(),
        ];

        $days = Day::all(['id', 'ar_name', 'en_name']);

        return view('serviceBody.index', [
            'sb'       => collect(),
            'kpiStats' => $kpiStats,
            'days'     => $days
        ]);
    }

    public function create() {

        $days = Day::all();
        return view('serviceBody.create', ['days'=>$days]);
    }

    public function store(ServiceBodyRequest $request) {

        $fields = $request->except('logo');

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $fields['logo'] = $path;
        }

        ServiceBody::create($fields);

        return redirect()->route('serviceBody.index');

    }

    public function edit(ServiceBody $serviceBody) {

        $days = Day::all();
        return view('serviceBody.edit', ['serviceBody'=>$serviceBody, 'days'=>$days]);
    }

    public function update(ServiceBodyRequest $request, ServiceBody $serviceBody) {

        $fields = $request->except('logo');

        if ($request->hasFile('logo')) {
            if ($serviceBody->logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($serviceBody->logo);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $fields['logo'] = $path;
        }

        $serviceBody->update($fields);

        return redirect()->route('serviceBody.index');
        
    }

    public function destroy(ServiceBody $serviceBody, Request $request) {
        $serviceBody->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('messages.service_body_deleted_success')]);
        }

        return redirect()->route('serviceBody.index')->with('success', __('messages.service_body_deleted_success'));
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

        $mpdf = MpdfService::create();

        $html = view('pdf.agenda', compact('agendas'))->render();
        $mpdf->WriteHTML($html);

        $filename = 'agendas_export_' . date('Y-m-d') . '.pdf';
        
        return response($mpdf->Output($filename, 'S'), 200)
               ->header('Content-Type', 'application/pdf')
               ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
