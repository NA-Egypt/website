<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ChangeRequest;
use App\Mail\ChangeRequestMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ChangeRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->hasRole('super admin')) {
            $changeRequests = ChangeRequest::with('user')->latest()->paginate(10);
        } else {
            $changeRequests = ChangeRequest::where('user_id', $user->id)->latest()->paginate(10);
        }

        return view('change-requests.index', compact('changeRequests'));
    }

    public function create()
    {
        return view('change-requests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'request_type' => 'required|string|in:meetings_groups,committee_info,general,other',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,png,jpg,jpeg,docx,xlsx|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('change_requests_attachments');
        }

        $changeRequest = ChangeRequest::create([
            'user_id' => Auth::id(),
            'request_type' => $request->request_type,
            'subject' => $request->subject,
            'description' => $request->description,
            'attachment_path' => $attachmentPath,
            'status' => 'pending',
        ]);

        try {
            Mail::to('web@naegypt.org')->send(new ChangeRequestMail($changeRequest));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send IT change request email: " . $e->getMessage());
        }

        return redirect()->route('change-requests.index')->with('success', __('messages.change_request_submitted') ?? 'Change request submitted successfully.');
    }

    public function show($id)
    {
        $changeRequest = ChangeRequest::with('user')->findOrFail($id);

        if (!Auth::user()->hasRole('super admin') && $changeRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('change-requests.show', compact('changeRequest'));
    }

    public function updateStatus(Request $request, $id)
    {
        if (!Auth::user()->hasRole('super admin')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'status' => 'required|string|in:pending,in_progress,completed,rejected',
        ]);

        $changeRequest = ChangeRequest::findOrFail($id);
        $changeRequest->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', __('messages.change_request_status_updated') ?? 'Status updated successfully.');
    }

    public function downloadAttachment($id)
    {
        $changeRequest = ChangeRequest::findOrFail($id);

        if (!Auth::user()->hasRole('super admin') && $changeRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (!$changeRequest->attachment_path || !Storage::exists($changeRequest->attachment_path)) {
            abort(404, 'File not found');
        }

        return Storage::download($changeRequest->attachment_path, basename($changeRequest->attachment_path));
    }
}
