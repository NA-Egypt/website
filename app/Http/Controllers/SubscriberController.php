<?php

namespace App\Http\Controllers;

use Mydnic\Subscribers\Subscriber;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriberController extends Controller
{
    public function index()
    {
        $subscribers = Subscriber::latest()->get();
        return view('subscribers.index', compact('subscribers'));
    }

    public function create()
    {
        return view('subscribers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:subscribers,email',
        ]);

        Subscriber::create([
            'email' => $request->email,
        ]);

        return redirect()->route('subscribers.index')
            ->with('success', __('messages.Subscriber added successfully'));
    }

    public function toggleVerification(Subscriber $subscriber)
    {
        if ($subscriber->hasVerifiedEmail()) {
            $subscriber->email_verified_at = null;
            $subscriber->save();
            $message = __('messages.Subscriber unverified successfully');
        } else {
            $subscriber->markEmailAsVerified();
            $message = __('messages.Subscriber verified successfully');
        }

        return redirect()->route('subscribers.index')->with('success', $message);
    }

    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();

        return redirect()->route('subscribers.index')
            ->with('success', __('messages.Subscriber deleted successfully'));
    }

    public function export()
    {
        $subscribers = Subscriber::all();

        $response = new StreamedResponse(function () use ($subscribers) {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM for proper excel alignment / encoding:
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($handle, [
                __('messages.ID'),
                __('messages.Email'),
                __('messages.Status'),
                __('messages.Created At')
            ]);

            foreach ($subscribers as $subscriber) {
                fputcsv($handle, [
                    $subscriber->id,
                    $subscriber->email,
                    $subscriber->hasVerifiedEmail() ? __('messages.Verified') : __('messages.Unverified'),
                    $subscriber->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="subscribers_' . date('Y-m-d_H-i-s') . '.csv"',
        ]);

        return $response;
    }
}
