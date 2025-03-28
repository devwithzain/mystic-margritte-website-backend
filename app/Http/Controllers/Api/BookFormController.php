<?php

namespace App\Http\Controllers\Api;

use App\Mail\BookFormMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\BookFormRequest;

class BookFormController extends Controller
{
   public function sendBookForm(BookFormRequest $request)
   {
      $data = $request->validated();
      $subject = "Booking from" . " " . $data['first_name'] . " " . $data['last_name'];
      $userEmail = $data['email'];

      try {
         Mail::to(config('mail.from.address'))->send((new BookFormMail($subject, data: $data))->from($userEmail, $userEmail));
         Mail::to($userEmail)->send((new BookFormMail($subject, data: $data))->from(config('mail.from.address'), config('mail.from.name')));
      } catch (\Exception $e) {
         return response()->json(['error' => 'Failed to send booking. Please try again later.'], 500);
      }
      return response()->json(['success' => "Your Booking has been submitted successfully."], 200);
   }
}