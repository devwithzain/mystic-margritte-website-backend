<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
            'email' => 'required|email',
            'redirect_url' => 'nullable|url'
        ]);

        try {
            $existingSubscriber = NewsletterSubscriber::where('email', $data['email'])->first();

            if ($existingSubscriber) {
                if ($existingSubscriber->is_verified) {
                    return response()->json(['error' => 'This email is already subscribed.'], 400);
                } else {
                    return response()->json(['error' => 'Please check your inbox for email confirmation.'], 400);
                }
            }

            $data['token'] = Str::random(32);
            $subscriber = NewsletterSubscriber::create($data);

            Mail::to($subscriber->email)->send(new NewsletterVerificationMail($subscriber, $data['redirect_url']));

            return response()->json(['success' => 'A confirmation email has been sent. Please check your inbox.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Subscription failed. Please try again.'], 500);
        }
    }

    public function verify($token, Request $request)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->first();

        if (!$subscriber) {
            return redirect(env('FRONTEND_WEBSITE_URL'))->with('error', 'Invalid or expired token.');
        }

        $subscriber->update(['is_verified' => true]);

        $redirectUrl = $request->query('redirect', env('FRONTEND_WEBSITE_URL'));

        return redirect($redirectUrl . "?status=success&message=Your subscription has been confirmed!");
    }
}