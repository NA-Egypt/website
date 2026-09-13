<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChangeRequest;
use App\Http\Resources\ChangeRequestResource;
use App\Mail\ChangeRequestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ChangeRequestController extends Controller
{
    /**
     * Display a listing of change requests.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = ChangeRequest::with('user')->latest();

        if (!$user->hasRole('super admin')) {
            $query->where('user_id', $user->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = min((int) $request->get('per_page', 15), 100);
        return ChangeRequestResource::collection($query->paginate($perPage));
    }

    /**
     * Store a newly created change request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_type' => 'required|string|in:meetings_groups,committee_info,general,other',
            'subject'      => 'required|string|max:255',
            'description'  => 'required|string',
            'attachment'   => 'nullable|file|mimes:pdf,png,jpg,jpeg,docx,xlsx|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('change_requests_attachments');
        }

        $changeRequest = ChangeRequest::create([
            'user_id'         => Auth::id(),
            'request_type'    => $validated['request_type'],
            'subject'         => $validated['subject'],
            'description'     => $validated['description'],
            'attachment_path' => $attachmentPath,
            'status'          => 'pending',
        ]);

        try {
            Mail::to('web@naegypt.org')->send(new ChangeRequestMail($changeRequest));
        } catch (\Exception $e) {
            Log::error("Failed to send IT change request email: " . $e->getMessage());
        }

        return (new ChangeRequestResource($changeRequest->load('user')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified change request.
     */
    public function show(ChangeRequest $changeRequest)
    {
        $user = Auth::user();
        if (!$user->hasRole('super admin') && $changeRequest->user_id !== $user->id) {
            abort(403, 'Unauthorized to view this change request.');
        }

        return new ChangeRequestResource($changeRequest->load('user'));
    }

    /**
     * Update the status of a change request (admin action).
     */
    public function updateStatus(Request $request, ChangeRequest $changeRequest)
    {
        $user = Auth::user();
        if (!$user->hasRole('super admin')) {
            abort(403, 'Unauthorized to modify change request status.');
        }

        $validated = $request->validate([
            'status' => 'required|string|in:pending,in_progress,completed,rejected',
        ]);

        $changeRequest->update(['status' => $validated['status']]);

        return new ChangeRequestResource($changeRequest->load('user'));
    }

    /**
     * Remove the specified change request.
     */
    public function destroy(ChangeRequest $changeRequest)
    {
        $user = Auth::user();
        if (!$user->hasRole('super admin') && ($changeRequest->user_id !== $user->id || $changeRequest->status !== 'pending')) {
            abort(403, 'Unauthorized to delete this change request.');
        }

        if ($changeRequest->attachment_path && Storage::exists($changeRequest->attachment_path)) {
            Storage::delete($changeRequest->attachment_path);
        }

        $changeRequest->delete();

        return response()->noContent();
    }
}
