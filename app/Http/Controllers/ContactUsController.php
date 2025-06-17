<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactUs;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
        public function create()
    {
        return view('frontend.contactus');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        // Store the contact us message in the database
        ContactUs::create($validated);

        // Send email to a specific address
        Mail::raw(
            "Name: {$validated['name']}\nEmail: {$validated['email']}\nMessage: {$validated['message']}",
            function ($message) {
                $message->to('mr.ahmedsaleh.86@gmail.com') // <-- Replace with your email
                        ->subject('New Contact Us Message');
            }
        );

        return redirect()->back()->with('status', 'mail-sent');
    }
}
