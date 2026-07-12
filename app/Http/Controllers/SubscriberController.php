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

        if (!$this->verifyEmailExists($request->email)) {
            return redirect()->back()
                ->withErrors(['email' => __('messages.invalid_email_domain')])
                ->withInput();
        }

        Subscriber::create([
            'email' => $request->email,
        ]);

        return redirect()->route('subscribers.index')
            ->with('success', __('messages.Subscriber added successfully'));
    }

    public function storeFrontend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:subscribers,email',
        ]);

        if (!$this->verifyEmailExists($request->email)) {
            return redirect()->back()
                ->withErrors(['email' => __('messages.invalid_email_domain')])
                ->withInput();
        }

        $subscriber = Subscriber::create([
            'email' => $request->email,
        ]);

        if (config('laravel-subscribers.verify')) {
            $subscriber->sendEmailVerificationNotification();
            return redirect()->route(config('laravel-subscribers.redirect_url'))
                ->with('subscribed', __('Please verify your email address!'));
        }

        return redirect()->route(config('laravel-subscribers.redirect_url'))
            ->with('subscribed', __('You are successfully subscribed to our list!'));
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

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $subscriberIds = $request->input('subscriber_ids', []);

        if (empty($subscriberIds)) {
            return redirect()->route('subscribers.index')->with('success', __('messages.no_subscribers_selected') ?? 'No subscribers selected.');
        }

        $subscribers = Subscriber::whereIn('id', $subscriberIds)->get();

        if ($action === 'delete') {
            foreach ($subscribers as $subscriber) {
                $subscriber->delete();
            }
            return redirect()->route('subscribers.index')->with('success', __('messages.selected_subscribers_deleted'));
        }

        if ($action === 'verify') {
            foreach ($subscribers as $subscriber) {
                $subscriber->markEmailAsVerified();
            }
            return redirect()->route('subscribers.index')->with('success', __('messages.selected_subscribers_verified'));
        }

        if ($action === 'unverify') {
            foreach ($subscribers as $subscriber) {
                $subscriber->email_verified_at = null;
                $subscriber->save();
            }
            return redirect()->route('subscribers.index')->with('success', __('messages.selected_subscribers_unverified'));
        }

        return redirect()->route('subscribers.index');
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

    public function getSubscriberIds()
    {
        $ids = Subscriber::whereNull('email_verified_at')->pluck('id');
        return response()->json($ids);
    }

    public function verifyBatch(Request $request)
    {
        $request->validate([
            'subscriber_ids' => 'required|array',
            'subscriber_ids.*' => 'integer|exists:subscribers,id'
        ]);

        $ids = $request->input('subscriber_ids');
        $subscribers = Subscriber::whereIn('id', $ids)->get();
        $deletedCount = 0;

        foreach ($subscribers as $subscriber) {
            if (!$this->verifyEmailExists($subscriber->email)) {
                $subscriber->delete();
                $deletedCount++;
            } else {
                $subscriber->markEmailAsVerified();
            }
        }

        return response()->json([
            'processed' => count($ids),
            'deleted' => $deletedCount
        ]);
    }

    private function verifyEmailExists($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = substr(strrchr($email, "@"), 1);

        // Get MX records
        $mxHosts = [];
        $mxWeights = [];
        if (!getmxrr($domain, $mxHosts, $mxWeights)) {
            // Fallback: check A records
            if (checkdnsrr($domain, 'A') || checkdnsrr($domain, 'AAAA')) {
                $mxHosts[] = $domain;
            } else {
                return false;
            }
        } else {
            // Sort MX hosts by priority weight
            array_multisort($mxWeights, $mxHosts);
        }

        // Try to connect to one of the MX hosts
        $mxHost = $mxHosts[0];
        $port = 25;
        $timeout = 5;

        $socket = @fsockopen($mxHost, $port, $errno, $errstr, $timeout);
        if (!$socket) {
            // If connection fails, assume it exists to prevent false negatives (server blocked us, etc)
            return true;
        }

        // Set stream timeout
        stream_set_timeout($socket, $timeout);

        // Read greeting
        $response = fgets($socket, 1024);
        if (strpos($response, '220') !== 0) {
            fclose($socket);
            return true; 
        }

        // HELO/EHLO
        $host = request()->getHost() ?: 'localhost';
        fputs($socket, "EHLO " . $host . "\r\n");
        $response = fgets($socket, 1024);
        
        // MAIL FROM
        $from = "verify@" . $host;
        fputs($socket, "MAIL FROM:<" . $from . ">\r\n");
        $response = fgets($socket, 1024);

        // RCPT TO
        fputs($socket, "RCPT TO:<" . $email . ">\r\n");
        $response = fgets($socket, 1024);

        // QUIT
        fputs($socket, "QUIT\r\n");
        fclose($socket);

        // Check if mailbox exists: 250 or 251 codes mean it exists
        // 550, 551, 552, 554 mean mailbox explicitly doesn't exist
        if (strpos($response, '250') === 0 || strpos($response, '251') === 0) {
            return true;
        }

        if (strpos($response, '550') === 0 || strpos($response, '551') === 0 || strpos($response, '552') === 0 || strpos($response, '554') === 0) {
            return false;
        }

        // For other response codes (e.g. 4xx greylisting, rate limits), assume true to prevent false negatives
        return true;
    }
}
