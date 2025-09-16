<?php

namespace App\Http\Controllers\Api;

use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ContactFormRequest;

class FormController extends Controller
{
   public function sendContactForm(ContactFormRequest $request)
   {
      $data = $request->validated();
      $subject = "Message from" . $data['name'];
      $userEmail = $data['email'];

      try {
         Mail::to(config('mail.from.address'))->send((new ContactFormMail($subject,  $data))->from($userEmail, $userEmail));
      } catch (\Exception $e) {
         Log::error('Failed to send contact form email: ' . $e->getMessage());
         return response()->json(['error' => 'Failed to send email. Please try again later.'. $e->getMessage()], 500);
      }
      return response()->json(['success' => "Your message has been submitted successfully."], 200);
   }
}