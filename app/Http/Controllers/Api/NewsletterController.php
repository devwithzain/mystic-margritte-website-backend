<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\NewsletterSubscriber;
use App\Http\Controllers\Controller;
use App\Mail\NewsletterVerificationMail;

class NewsletterController extends Controller
{
    public function index()
    {
        try {
            $subscribers = NewsletterSubscriber::all();
            return response()->json(['data' => $subscribers], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch subscribers.'], 500);
        }
    }

    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email'
        ]);

        try {
            // Generate token
            $data['token'] = Str::random(32);

            // Save to database
            $subscriber = NewsletterSubscriber::create($data);

            // Send verification email
            Mail::to($subscriber->email)->send(new NewsletterVerificationMail($subscriber));

            return response()->json(['success' => 'A confirmation email has been sent. Please check your inbox.'], 200);
        } catch (\Exception $e) {
            Log::error('Newsletter subscription failed: ' . $e->getMessage());
            return response()->json(['error' => 'Subscription failed. Please try again.'], 500);
        }
    }

    public function verify($token)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->first();

        if (!$subscriber) {
            return redirect('http://127.0.0.1:8000')->with('error', 'Invalid or expired token.');
        }

        $subscriber->update(['is_verified' => true]);

        return redirect('http://127.0.0.1:8000')->with('success', 'Your subscription has been confirmed!');
    }
}