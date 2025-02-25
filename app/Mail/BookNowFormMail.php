<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class BookNowFormMail extends Mailable
{
   use Queueable, SerializesModels;

   public $name;
   public $lastName;
   public $services;
   public $healingTopics;
   public $preferredTime;
   public $cityAndState;
   public $specialMessage;
   public $subject;

   public function __construct($subject, $data)
   {
      $this->name = $data['name'];
      $this->lastName = $data['lastName'];
      $this->services = $data['services'];
      $this->healingTopics = $data['healingTopics'];
      $this->preferredTime = $data['preferredTime'];
      $this->cityAndState = $data['cityAndState'];
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
         view: 'email.book',
         with: [
            'name' => $this->name,
            'lastName' => $this->lastName,
            'services' => $this->services,
            'healingTopics' => $this->healingTopics,
            'preferredTime' => $this->preferredTime,
            'cityAndState' => $this->cityAndState,
            'specialMessage' => $this->specialMessage,
         ],
      );
   }

   public function attachments(): array
   {
      return [];
   }
}