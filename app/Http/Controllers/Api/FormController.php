<?php

namespace App\Http\Controllers\Api;

use App\Mail\ContactFormMail;
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
         Mail::to(config('mail.from.address'))->send((new ContactFormMail($subject, data: $data))->from($userEmail, $userEmail));
      } catch (\Exception $e) {
         return response()->json(['error' => 'Failed to send email. Please try again later.'], 500);
      }
      return response()->json(['success' => "Your message has been submitted successfully."], 200);
   }
}