<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactUs;
use Illuminate\Support\Facades\Mail;
use ReCaptcha\ReCaptcha;

class ContactUsController extends Controller
{
        public function create()
    {
        return view('frontend.contactus');
    }

    public function store(Request $request)
    {
        // Validate form fields including reCAPTCHA
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'g-recaptcha-response' => 'required',
        ], [
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification.',
        ]);

        // Verify reCAPTCHA
        $recaptcha = new ReCaptcha(env('RECAPTCHA_SECRET_KEY'));
        $response = $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip());

        if (!$response->isSuccess()) {
            return redirect()->back()
                ->withErrors(['g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.'])
                ->withInput();
        }

        // Remove reCAPTCHA response from validated data before storing
        unset($validated['g-recaptcha-response']);

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
