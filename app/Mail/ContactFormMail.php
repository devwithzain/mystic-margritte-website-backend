<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Log;

class ContactFormMail extends Mailable
{
   use Queueable, SerializesModels;

   public $name;
   public $email;
   public $specialMessage;
   public $subject;

   public function __construct($subject, $data)
   {
      Log::error('Failed to send contact form email: ', $data);

      $this->name = $data['name'];
      $this->email = $data['email'];
      $this->specialMessage = $data['specialMessage'];
      $this->subject = $subject;
   }

   public function envelope(): Envelope
   {
      return new Envelope(
         subject: $this->subject,
      );
   }

   public function content(): Content
   {
      return new Content(
         view: 'email.contact',
         with: [
            'name' => $this->name,
            'email' => $this->email,
            'specialMessage' => $this->specialMessage,
         ],
      );
   }

   public function attachments(): array
   {
      return [];
   }
}