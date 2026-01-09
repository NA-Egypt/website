<?php

namespace App\Http\Controllers;

use App\Models\CommitteeReport;
use App\Models\ServiceCommittee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail; // For sending later
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommitteeReportController extends Controller
{
    use AuthorizesRequests;

    protected function getServiceCommittee()
    {
        $user = Auth::user();
        if (!$user) return null;
        
        // Find committee by email
        return ServiceCommittee::where('email', $user->email)->first();
    }

    protected function isRsc()
    {
        return Auth::check() && (Auth::user()->email === 'rsc@naegypt.org' || Auth::user()->hasRole('super admin'));
    }

    public function index(Request $request)
    {
        $isRsc = $this->isRsc();
        $query = CommitteeReport::with('serviceCommittee');

        if (!$isRsc) {
            $committee = $this->getServiceCommittee();
            if (!$committee) {
                abort(403, 'You are not assigned to any Service Committee.');
            }
            $query->where('service_committee_id', $committee->id);
        } else {
            // RSC Filters
            if ($request->has('committee_id') && $request->committee_id) {
                $query->where('service_committee_id', $request->committee_id);
            }
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('meeting_day_description', 'like', "%$search%")
                  ->orWhereDate('meeting_date', $search); // exact date match
            });
        }

        $reports = $query->latest('meeting_date')->paginate(10);
        
        $committees = $isRsc ? ServiceCommittee::all() : [];

        return view('reports.index', compact('reports', 'isRsc', 'committees'));
    }

    public function create()
    {
        $isRsc = $this->isRsc();
        $committee = $this->getServiceCommittee();
        $committees = [];

        if ($isRsc) {
            $committees = ServiceCommittee::all();
        } elseif (!$committee) {
            abort(403, 'Only Committee members can create reports.');
        }

        return view('reports.create', compact('committee', 'committees', 'isRsc'));
    }

    public function store(Request $request)
    {
        $isRsc = $this->isRsc();
        $committee = $this->getServiceCommittee();

        if (!$isRsc && !$committee) {
             abort(403, 'Unauthorized');
        }

        $request->validate([
            'service_committee_id' => $isRsc ? 'required|exists:service_committees,id' : 'nullable',
            'meeting_date' => 'required|date',
            'meeting_day_description' => 'required|string|max:255',
            'body' => 'nullable|string',
            'positions' => 'nullable|array',
            'positions.*.name' => 'required|string',
            'positions.*.status' => 'required|string',
            'positions.*.election' => 'nullable', // boolean or string
        ]);

        $committeeId = $isRsc ? $request->service_committee_id : $committee->id;

        // Process positions to match schema
        // The View will send 'positions' array.
        // We'll store it as 'positions_status' JSON.
        
        $positionsStatus = $request->positions;

        CommitteeReport::create([
            'service_committee_id' => $committeeId,
            'meeting_date' => $request->meeting_date,
            'meeting_day_description' => $request->meeting_day_description,
            'body' => $request->body,
            'positions_status' => $positionsStatus,
        ]);

        return redirect()->route('committee-reports.index')->with('success', 'Report created successfully.');
    }

    public function show($id)
    {
        $report = CommitteeReport::with('serviceCommittee')->findOrFail($id);
        
        if (!$this->isRsc() && $this->getServiceCommittee()?->id !== $report->service_committee_id) {
            abort(403, 'Unauthorized');
        }

        return view('reports.show', compact('report'));
    }

    public function pdf($id)
    {
        $report = CommitteeReport::with('serviceCommittee')->findOrFail($id);

        if (!$this->isRsc() && $this->getServiceCommittee()?->id !== $report->service_committee_id) {
            abort(403, 'Unauthorized');
        }

        // Generate PDF
        $pdf = Pdf::loadView('reports.pdf', compact('report'));
        return $pdf->download('committee_report_' . $report->meeting_date->format('Y-m-d') . '.pdf');
    }

    public function send($id)
    {
        $report = CommitteeReport::findOrFail($id);

        if (!$this->isRsc() && $this->getServiceCommittee()?->id !== $report->service_committee_id) {
            abort(403, 'Unauthorized');
        }

        // TODO: Implement actual mailing logic
        // For now, simple success notification simulating handling
        
        return redirect()->back()->with('success', 'Report sent to Region Secretary.');
    }
}
