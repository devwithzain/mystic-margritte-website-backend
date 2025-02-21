<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscriber;

    public function __construct($subscriber)
    {
        $this->subscriber = $subscriber;
    }
    public function build()
    {
        return $this->subject('Confirm Your Subscription')
            ->view('email.newsletter_verification')
            ->with([
                'verificationUrl' => url('http://127.0.0.1:8000/api/newsletter/verify/' . $this->subscriber->token)
            ]);
    }
}