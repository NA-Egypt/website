<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use App\Rules\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    public function create()
    {
        return view('frontend.contactus');
    }

    public function store(Request $request)
    {
        // Validate form fields including Cloudflare Turnstile
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'cf-turnstile-response' => ['required', new Turnstile],
        ], [
            'cf-turnstile-response.required' => __('messages.turnstile_required'),
        ]);

        // Remove Turnstile response token from validated data before storing
        unset($validated['cf-turnstile-response']);

        // Store the contact us message in the database
        ContactUs::create($validated);

        // Send email to a specific address
        $emailSubject = !empty($validated['subject']) ? "Contact Us: {$validated['subject']}" : 'New Contact Us Message';
        $phoneLine = !empty($validated['phone']) ? "Phone: {$validated['phone']}\n" : '';
        $subjectLine = !empty($validated['subject']) ? "Subject: {$validated['subject']}\n" : '';

        Mail::raw(
            "Name: {$validated['name']}\nEmail: {$validated['email']}\n{$phoneLine}{$subjectLine}Message: {$validated['message']}",
            function ($message) use ($emailSubject) {
                $message->to('info@naegypt.org')
                        ->cc('web@naegypt.org')
                        ->subject($emailSubject);
            }
        );

        return redirect()->back()->with('status', 'mail-sent');
    }
}
