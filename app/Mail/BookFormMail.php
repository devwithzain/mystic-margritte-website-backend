<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class BookFormMail extends Mailable
{
   use Queueable, SerializesModels;

   public $subject;
   public $data;

   public function __construct($subject, $data)
   {
      $this->subject = $subject;
      $this->data = $data;
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
         view: 'email.book',
         with: ['data' => $this->data],
      );
   }

   public function attachments(): array
   {
      return [];
   }
}