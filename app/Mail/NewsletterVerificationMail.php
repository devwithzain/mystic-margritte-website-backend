<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscriber;
    public $redirectUrl;

    public function __construct($subscriber, $redirectUrl = null)
    {
        $this->subscriber = $subscriber;
        $this->redirectUrl = $redirectUrl ?? env('FRONTEND_WEBSITE_URL');
    }

    public function build()
    {
        $verificationUrl = env('APP_URL') . "/api/newsletter/verify/{$this->subscriber->token}?redirect=" . urlencode($this->redirectUrl);

        return $this->subject('Confirm Your Subscription')
            ->view('email.newsletter_verification')
            ->with(['verificationUrl' => $verificationUrl]);
    }
}